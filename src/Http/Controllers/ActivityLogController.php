<?php

namespace ActivityLogger\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use ActivityLogger\Http\Resources\ActivityLogResource;
use ActivityLogger\Http\Resources\ActivityLogCollection;
use ActivityLogger\Http\Resources\ActivityLogListResource;
use ActivityLogger\Facades\ActivityLogger;

class ActivityLogController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $filters = $this->buildFilters($request);
        $perPage = $request->get('per_page', 50);
        
        $logs = ActivityLogger::search($filters, $perPage);
        
        // Use list resource for listing to optimize response size
        return response()->json([
            'data' => ActivityLogListResource::collection($logs->items()),
            'links' => [
                'first' => $logs->url(1),
                'last' => $logs->url($logs->lastPage()),
                'prev' => $logs->previousPageUrl(),
                'next' => $logs->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $logs->currentPage(),
                'from' => $logs->firstItem(),
                'last_page' => $logs->lastPage(),
                'links' => $this->generatePaginationLinks($logs),
                'path' => $request->url(),
                'per_page' => $logs->perPage(),
                'to' => $logs->lastItem(),
                'total' => $logs->total(),
            ],
        ]);
    }

    public function show(Request $request, $id): JsonResponse
    {
        $log = ActivityLogger::find($id);
        
        if (!$log) {
            return response()->json(['error' => 'Activity log not found'], 404);
        }
        
        return (new ActivityLogResource($log))->response();
    }

    public function statistics(Request $request): JsonResponse
    {
        $filters = $this->buildFilters($request);
        $stats = ActivityLogger::getStatistics($filters);
        
        return response()->json([
            'statistics' => $this->formatStatistics($stats),
            'filters_applied' => $filters,
        ]);
    }

    public function export(Request $request): JsonResponse
    {
        $filters = $this->buildFilters($request);
        $format = $request->get('format', 'json');
        
        if (!in_array($format, ['json', 'csv', 'xml'])) {
            return response()->json(['error' => 'Invalid format'], 400);
        }
        
        $data = ActivityLogger::export($filters, $format);
        
        $headers = [
            'Content-Type' => $this->getContentType($format),
            'Content-Disposition' => 'attachment; filename="activity_logs_' . now()->format('Y-m-d_H-i-s') . '.' . $format . '"',
        ];
        
        return response($data, 200, $headers);
    }

    protected function buildFilters(Request $request): array
    {
        $filters = [];
        
        // User filters
        if ($request->filled('user_id')) {
            $filters['user_id'] = $request->get('user_id');
        }
        
        // IP filters
        if ($request->filled('ip_address')) {
            $filters['ip_address'] = $request->get('ip_address');
        }
        
        // Date filters - using request_date for efficient searching
        if ($request->filled('date')) {
            $filters['start_date'] = $request->get('date');
            $filters['end_date'] = $request->get('date');
        }
        
        if ($request->filled('start_date')) {
            $filters['start_date'] = $request->get('start_date');
        }
        
        if ($request->filled('end_date')) {
            $filters['end_date'] = $request->get('end_date');
        }
        
        // Method filters
        if ($request->filled('method')) {
            $filters['method'] = $request->get('method');
        }
        
        // URL filters
        if ($request->filled('url')) {
            $filters['url'] = $request->get('url');
        }
        
        // Route filters
        if ($request->filled('route_name')) {
            $filters['route_name'] = $request->get('route_name');
        }
        
        // Controller filters
        if ($request->filled('controller_action')) {
            $filters['controller_action'] = $request->get('controller_action');
        }
        
        // Request ID filters
        if ($request->filled('request_id')) {
            $filters['request_id'] = $request->get('request_id');
        }
        
        // Response code filters
        if ($request->filled('response_code')) {
            $filters['response_code'] = $request->get('response_code');
        }
        
        // Performance filters
        if ($request->filled('min_response_time')) {
            $filters['min_response_time'] = $request->get('min_response_time');
        }
        
        if ($request->filled('max_response_time')) {
            $filters['max_response_time'] = $request->get('max_response_time');
        }
        
        if ($request->filled('min_memory_usage')) {
            $filters['min_memory_usage'] = $request->get('min_memory_usage');
        }
        
        if ($request->filled('max_memory_usage')) {
            $filters['max_memory_usage'] = $request->get('max_memory_usage');
        }
        
        if ($request->filled('min_query_count')) {
            $filters['min_query_count'] = $request->get('min_query_count');
        }
        
        // Geographical filters
        if ($request->filled('country')) {
            $filters['country'] = $request->get('country');
        }
        
        if ($request->filled('city')) {
            $filters['city'] = $request->get('city');
        }
        
        // Device filters
        if ($request->filled('browser')) {
            $filters['browser'] = $request->get('browser');
        }
        
        if ($request->filled('platform')) {
            $filters['platform'] = $request->get('platform');
        }
        
        if ($request->filled('device')) {
            $filters['device'] = $request->get('device');
        }
        
        // Status filters
        if ($request->filled('has_errors')) {
            $filters['has_errors'] = $request->boolean('has_errors');
        }
        
        if ($request->filled('successful')) {
            $filters['successful'] = $request->boolean('successful');
        }
        
        if ($request->filled('failed')) {
            $filters['failed'] = $request->boolean('failed');
        }
        
        // Boolean filters
        if ($request->filled('is_mobile')) {
            $filters['is_mobile'] = $request->boolean('is_mobile');
        }
        
        if ($request->filled('is_ajax')) {
            $filters['is_ajax'] = $request->boolean('is_ajax');
        }
        
        return $filters;
    }

    protected function formatStatistics(array $stats): array
    {
        return [
            'overview' => [
                'total_requests' => $stats['total_requests'],
                'unique_users' => $stats['unique_users'],
                'unique_ips' => $stats['unique_ips'],
                'success_rate' => $stats['success_rate'] . '%',
            ],
            'performance' => [
                'average_response_time' => round($stats['average_response_time'], 2) . 'ms',
                'average_memory_usage' => round($stats['average_memory'] / (1024 * 1024), 2) . 'MB',
                'average_response_size' => round($stats['average_response_size'] / 1024, 2) . 'KB',
                'average_query_count' => round($stats['average_query_count'], 2),
                'average_query_time' => round($stats['average_query_time'], 2) . 'ms',
            ],
            'distribution' => [
                'methods' => $stats['methods'],
                'response_codes' => $stats['response_codes'],
                'browsers' => $stats['browsers'],
                'platforms' => $stats['platforms'],
                'devices' => $stats['devices'],
            ],
            'top_lists' => [
                'urls' => array_slice($stats['top_urls'], 0, 10, true),
                'users' => array_slice($stats['top_users'], 0, 10, true),
            ],
            'hourly_distribution' => $stats['hourly_distribution'],
        ];
    }

    protected function getContentType(string $format): string
    {
        switch ($format) {
            case 'csv':
                return 'text/csv';
            case 'xml':
                return 'application/xml';
            default:
                return 'application/json';
        }
    }

    protected function generatePaginationLinks($paginator): array
    {
        $links = [];
        
        // Previous link
        if ($paginator->currentPage() > 1) {
            $links[] = [
                'url' => $paginator->previousPageUrl(),
                'label' => '&laquo; Previous',
                'active' => false,
            ];
        } else {
            $links[] = [
                'url' => null,
                'label' => '&laquo; Previous',
                'active' => false,
            ];
        }
        
        // Page number links
        $elements = $paginator->linkCollection();
        foreach ($elements as $element) {
            if (is_string($element)) {
                $links[] = [
                    'url' => null,
                    'label' => '...',
                    'active' => false,
                ];
            } elseif (is_array($element)) {
                foreach ($element as $page => $url) {
                    $links[] = [
                        'url' => $url,
                        'label' => $page,
                        'active' => $paginator->currentPage() == $page,
                    ];
                }
            }
        }
        
        // Next link
        if ($paginator->hasMorePages()) {
            $links[] = [
                'url' => $paginator->nextPageUrl(),
                'label' => 'Next &raquo;',
                'active' => false,
            ];
        } else {
            $links[] = [
                'url' => null,
                'label' => 'Next &raquo;',
                'active' => false,
            ];
        }
        
        return $links;
    }
}