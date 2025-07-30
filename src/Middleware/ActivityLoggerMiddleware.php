<?php

namespace ActivityLogger\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use ActivityLogger\Models\ActivityLog;
use Exception;
use Jenssegers\Agent\Agent;
use Illuminate\Auth\AuthManager;
use Throwable;

class ActivityLoggerMiddleware
{
    protected $startTime;
    protected $startMemory;

    protected AuthManager $auth;

    public function __construct(AuthManager $auth)
    {
        $this->auth = $auth;
    }

    public function handle(Request $request, Closure $next)
    {
        if ($this->shouldSkipLogging($request)) {
            return $next($request);
        }

        $this->startTime = microtime(true);
        $this->startMemory = memory_get_usage();

        try {
            $response = $next($request);
            
            if (config('activity-logger.enabled', true)) {
                $this->logActivity($request, $response);
            }

            return $response;
        } catch (Throwable $e) {
            if (config('activity-logger.enabled', true)) {
                $this->logActivity($request, null, $e);
            }
            
            throw $e;
        }
    }

    protected function logActivity(Request $request, $response = null, Throwable $exception = null)
    {
        $agent = new Agent();
        $agent->setUserAgent($request->userAgent());
        
        $responseTime = (microtime(true) - $this->startTime) * 1000;
        $memoryUsage = memory_get_usage() - $this->startMemory;
        $requestTime = now();
        $user_id = null;
        $user = null;
        try {
            // Get current user information
            $user = $request->user();

            if ($request->bearerToken()) {
                $user = Auth::guard('api')->user();
            }
        } catch (Exception $e) {
            // Nothing to do in here
        }
        
        $data = [
            'user_id' => $user ? $user->id : null,
            'user_name' => $user ? ($user->name ?? null) : null,
            'user_email' => $user ? ($user->email ?? null) : null,
            'session_id' => session()->getId(),
            'ip_address' => $request->ip(),
            'request_date' => $requestTime->toDateString(),
            'requested_at' => $requestTime,
            'user_agent' => $request->userAgent(),
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'referer' => $request->header('referer'),
            'route_name' => $request->route() ? $request->route()->getName() : null,
            'controller_action' => $this->getControllerAction($request),
            'middleware' => $this->getMiddleware($request),
            'request_id' => $request->header('X-Request-ID') ?? uniqid('req_'),
            'response_time' => $responseTime,
            'memory_usage' => $memoryUsage,
            'query_count' => $this->getQueryCount(),
            'query_time' => $this->getQueryTime(),
            'browser' => $agent->browser(),
            'platform' => $agent->platform(),
            'device' => $this->getDevice($agent),
            'is_ajax' => $request->ajax(),
            'is_mobile' => $agent->isMobile(),
        ];

        if (config('activity-logger.log_request_headers', true)) {
            $data['request_headers'] = $this->filterHeaders($request->headers->all());
        }

        if (config('activity-logger.log_request_params', true)) {
            $data['request_params'] = $request->query();
        }

        if (config('activity-logger.log_request_body', true)) {
            $data['request_body'] = $this->filterRequestBody($request);
        }

        if ($response) {
            $data['response_code'] = $response->getStatusCode();
            $data['response_size'] = strlen($response->getContent());
            
            if (config('activity-logger.log_response_headers', false)) {
                $data['response_headers'] = $this->filterHeaders($response->headers->all());
            }

            if (config('activity-logger.log_response_body', false) && $this->shouldLogResponseBody($response)) {
                $data['response_body'] = $this->getResponseBody($response);
            }
        }

        if ($exception) {
            $data['response_code'] = 500;
            $data['error_message'] = $exception->getMessage();
            $data['error_type'] = get_class($exception);
            
            if (config('activity-logger.log_error_trace', true)) {
                $data['error_trace'] = $exception->getTraceAsString();
            }
        }

        if (method_exists($this, 'getCustomData')) {
            $data['custom_data'] = $this->getCustomData($request, $response, $exception);
        }

        try {
            ActivityLog::create($data);
        } catch (Throwable $e) {
            if (config('activity-logger.log_errors', true)) {
                \Log::error('Failed to log activity: ' . $e->getMessage());
            }
        }
    }

    protected function shouldSkipLogging(Request $request): bool
    {
        $skipUrls = config('activity-logger.skip_urls', []);
        $skipMethods = config('activity-logger.skip_methods', []);
        $skipRoutes = config('activity-logger.skip_routes', []);

        foreach ($skipUrls as $pattern) {
            if ($request->is($pattern)) {
                return true;
            }
        }

        if (in_array($request->method(), $skipMethods)) {
            return true;
        }

        if ($request->route() && in_array($request->route()->getName(), $skipRoutes)) {
            return true;
        }

        return false;
    }

    protected function filterHeaders(array $headers): array
    {
        $sensitiveHeaders = config('activity-logger.sensitive_headers', [
            'authorization',
            'cookie',
            'x-csrf-token',
            'x-xsrf-token',
        ]);

        foreach ($sensitiveHeaders as $header) {
            unset($headers[strtolower($header)]);
        }

        return array_map(function ($value) {
            return is_array($value) ? implode(', ', $value) : $value;
        }, $headers);
    }

    protected function filterRequestBody(Request $request): array
    {
        $body = $request->except(config('activity-logger.sensitive_fields', [
            'password',
            'password_confirmation',
            'credit_card',
            'cvv',
            'ssn',
        ]));

        $maxSize = config('activity-logger.max_body_size', 10000);
        $bodyJson = json_encode($body);
        
        if (strlen($bodyJson) > $maxSize) {
            return [
                '_truncated' => true,
                '_original_size' => strlen($bodyJson),
                '_message' => 'Request body too large to log'
            ];
        }

        return $body;
    }

    protected function shouldLogResponseBody($response): bool
    {
        $contentType = $response->headers->get('Content-Type', '');
        
        if (strpos($contentType, 'application/json') !== false || strpos($contentType, 'text/') !== false) {
            return true;
        }

        return false;
    }

    protected function getResponseBody($response): array
    {
        $content = $response->getContent();
        $maxSize = config('activity-logger.max_body_size', 10000);

        if (strlen($content) > $maxSize) {
            return [
                '_truncated' => true,
                '_original_size' => strlen($content),
                '_message' => 'Response body too large to log'
            ];
        }

        $decoded = json_decode($content, true);
        
        return $decoded !== null ? $decoded : ['_raw' => substr($content, 0, 1000)];
    }

    protected function getDevice(Agent $agent): string
    {
        if ($agent->isDesktop()) {
            return 'Desktop';
        } elseif ($agent->isTablet()) {
            return 'Tablet';
        } elseif ($agent->isMobile()) {
            return 'Mobile';
        } elseif ($agent->isRobot()) {
            return 'Robot';
        }
        
        return 'Unknown';
    }

    protected function getControllerAction(Request $request): ?string
    {
        if (!$request->route()) {
            return null;
        }

        $action = $request->route()->getAction();
        
        if (isset($action['controller'])) {
            return $action['controller'];
        }

        return null;
    }

    protected function getMiddleware(Request $request): array
    {
        if (!$request->route()) {
            return [];
        }

        return $request->route()->gatherMiddleware();
    }

    protected function getQueryCount(): int
    {
        if (class_exists('\Illuminate\Database\Events\QueryExecuted')) {
            return count(\DB::getQueryLog());
        }

        return 0;
    }

    protected function getQueryTime(): float
    {
        if (class_exists('\Illuminate\Database\Events\QueryExecuted')) {
            $queries = \DB::getQueryLog();
            return collect($queries)->sum('time');
        }

        return 0;
    }
}