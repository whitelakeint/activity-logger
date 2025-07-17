<?php

namespace ActivityLogger\Repositories;

use ActivityLogger\Models\ActivityLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class ActivityLogRepository
{
    protected $model;

    public function __construct()
    {
        $this->model = new ActivityLog();
    }

    public function search(array $filters = [], int $perPage = 50): LengthAwarePaginator
    {
        $query = $this->buildSearchQuery($filters);
        
        return $query->orderBy('requested_at', 'desc')
                     ->paginate($perPage);
    }

    public function find(int $id): ?ActivityLog
    {
        return $this->model->find($id);
    }

    public function getByDateRange($startDate, $endDate, array $additionalFilters = []): Collection
    {
        $query = $this->model->newQuery();
        
        if ($endDate) {
            $query->whereBetween('request_date', [
                \Carbon\Carbon::parse($startDate)->toDateString(),
                \Carbon\Carbon::parse($endDate)->toDateString()
            ]);
        } else {
            $query->whereDate('request_date', \Carbon\Carbon::parse($startDate)->toDateString());
        }
        
        $query = $this->applyFilters($query, $additionalFilters);
        
        return $query->orderBy('requested_at', 'desc')->get();
    }

    public function getRecentErrors(int $minutes = 60, int $limit = 100): Collection
    {
        return $this->model->recent($minutes)
                          ->withErrors()
                          ->orderBy('requested_at', 'desc')
                          ->limit($limit)
                          ->get();
    }

    public function getSlowRequests(int $thresholdMs = 1000, int $limit = 100): Collection
    {
        return $this->model->slowRequests($thresholdMs)
                          ->orderBy('response_time', 'desc')
                          ->limit($limit)
                          ->get();
    }

    public function getHighMemoryRequests(int $thresholdMb = 50, int $limit = 100): Collection
    {
        return $this->model->highMemoryUsage($thresholdMb)
                          ->orderBy('memory_usage', 'desc')
                          ->limit($limit)
                          ->get();
    }

    public function getUserActivity(int $userId, array $filters = []): Collection
    {
        $query = $this->model->forUser($userId);
        
        $query = $this->applyFilters($query, $filters);
        
        return $query->orderBy('requested_at', 'desc')->get();
    }

    public function getStatistics(array $filters = []): array
    {
        $query = $this->buildSearchQuery($filters);
        
        return [
            'total_requests' => $query->count(),
            'unique_users' => $query->distinct('user_id')->count('user_id'),
            'unique_ips' => $query->distinct('ip_address')->count('ip_address'),
            'success_rate' => $this->calculateSuccessRate($query),
            'average_response_time' => $query->avg('response_time'),
            'average_memory' => $query->avg('memory_usage'),
            'average_response_size' => $query->avg('response_size'),
            'average_query_count' => $query->avg('query_count'),
            'average_query_time' => $query->avg('query_time'),
            'methods' => $this->getMethodStatistics($query),
            'response_codes' => $this->getResponseCodeStatistics($query),
            'top_urls' => $this->getTopUrls($query),
            'top_users' => $this->getTopUsers($query),
            'browsers' => $this->getBrowserStatistics($query),
            'platforms' => $this->getPlatformStatistics($query),
            'devices' => $this->getDeviceStatistics($query),
            'hourly_distribution' => $this->getHourlyDistribution($query),
        ];
    }

    public function deleteOldLogs(int $days): int
    {
        $cutoffDate = Carbon::now()->subDays($days)->toDateString();
        return $this->model->where('request_date', '<', $cutoffDate)
                          ->delete();
    }

    public function export(array $filters = [], string $format = 'json'): string
    {
        $query = $this->buildSearchQuery($filters);
        $data = $query->get();
        
        switch ($format) {
            case 'csv':
                return $this->exportToCsv($data);
            case 'xml':
                return $this->exportToXml($data);
            default:
                return $data->toJson();
        }
    }

    protected function buildSearchQuery(array $filters): Builder
    {
        $query = $this->model->newQuery();
        
        return $this->applyFilters($query, $filters);
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        if (!empty($filters['user_id'])) {
            $query->forUser($filters['user_id']);
        }
        
        if (!empty($filters['ip_address'])) {
            $query->forIp($filters['ip_address']);
        }
        
        if (!empty($filters['method'])) {
            $query->forMethod($filters['method']);
        }
        
        if (!empty($filters['url'])) {
            $query->forUrl($filters['url']);
        }
        
        if (!empty($filters['route_name'])) {
            $query->forRoute($filters['route_name']);
        }
        
        if (!empty($filters['response_code'])) {
            $query->forResponseCode($filters['response_code']);
        }
        
        if (!empty($filters['start_date'])) {
            if (!empty($filters['end_date'])) {
                // Use request_date for efficient date range filtering
                $query->whereBetween('request_date', [
                    \Carbon\Carbon::parse($filters['start_date'])->toDateString(),
                    \Carbon\Carbon::parse($filters['end_date'])->toDateString()
                ]);
            } else {
                // Single date filter using request_date
                $query->whereDate('request_date', \Carbon\Carbon::parse($filters['start_date'])->toDateString());
            }
        }
        
        if (isset($filters['has_errors']) && $filters['has_errors']) {
            $query->withErrors();
        }
        
        if (isset($filters['successful']) && $filters['successful']) {
            $query->successful();
        }
        
        if (isset($filters['failed']) && $filters['failed']) {
            $query->failed();
        }
        
        if (!empty($filters['min_response_time'])) {
            $query->where('response_time', '>=', $filters['min_response_time']);
        }
        
        if (!empty($filters['max_response_time'])) {
            $query->where('response_time', '<=', $filters['max_response_time']);
        }
        
        if (!empty($filters['min_memory_usage'])) {
            $query->where('memory_usage', '>=', $filters['min_memory_usage']);
        }
        
        if (!empty($filters['max_memory_usage'])) {
            $query->where('memory_usage', '<=', $filters['max_memory_usage']);
        }
        
        if (!empty($filters['min_query_count'])) {
            $query->where('query_count', '>=', $filters['min_query_count']);
        }
        
        if (!empty($filters['controller_action'])) {
            $query->where('controller_action', 'LIKE', '%' . $filters['controller_action'] . '%');
        }
        
        if (!empty($filters['request_id'])) {
            $query->where('request_id', $filters['request_id']);
        }
        
        if (!empty($filters['country'])) {
            $query->where('country', $filters['country']);
        }
        
        if (!empty($filters['city'])) {
            $query->where('city', $filters['city']);
        }
        
        if (!empty($filters['browser'])) {
            $query->where('browser', $filters['browser']);
        }
        
        if (!empty($filters['platform'])) {
            $query->where('platform', $filters['platform']);
        }
        
        if (!empty($filters['device'])) {
            $query->where('device', $filters['device']);
        }
        
        if (isset($filters['is_mobile'])) {
            $query->where('is_mobile', $filters['is_mobile']);
        }
        
        if (isset($filters['is_ajax'])) {
            $query->where('is_ajax', $filters['is_ajax']);
        }
        
        return $query;
    }

    protected function calculateSuccessRate(Builder $query): float
    {
        $total = $query->count();
        
        if ($total === 0) {
            return 0;
        }
        
        $successful = (clone $query)->successful()->count();
        
        return round(($successful / $total) * 100, 2);
    }

    protected function getMethodStatistics(Builder $query): array
    {
        return (clone $query)->select('method', \DB::raw('COUNT(*) as count'))
                            ->groupBy('method')
                            ->pluck('count', 'method')
                            ->toArray();
    }

    protected function getResponseCodeStatistics(Builder $query): array
    {
        return (clone $query)->select('response_code', \DB::raw('COUNT(*) as count'))
                            ->groupBy('response_code')
                            ->orderBy('count', 'desc')
                            ->pluck('count', 'response_code')
                            ->toArray();
    }

    protected function getTopUrls(Builder $query, int $limit = 20): array
    {
        return (clone $query)->select('url', \DB::raw('COUNT(*) as count'))
                            ->groupBy('url')
                            ->orderBy('count', 'desc')
                            ->limit($limit)
                            ->pluck('count', 'url')
                            ->toArray();
    }

    protected function getTopUsers(Builder $query, int $limit = 20): array
    {
        return (clone $query)->whereNotNull('user_id')
                            ->select('user_id', \DB::raw('COUNT(*) as count'))
                            ->groupBy('user_id')
                            ->orderBy('count', 'desc')
                            ->limit($limit)
                            ->pluck('count', 'user_id')
                            ->toArray();
    }

    protected function getBrowserStatistics(Builder $query): array
    {
        return (clone $query)->whereNotNull('browser')
                            ->select('browser', \DB::raw('COUNT(*) as count'))
                            ->groupBy('browser')
                            ->orderBy('count', 'desc')
                            ->pluck('count', 'browser')
                            ->toArray();
    }

    protected function getPlatformStatistics(Builder $query): array
    {
        return (clone $query)->whereNotNull('platform')
                            ->select('platform', \DB::raw('COUNT(*) as count'))
                            ->groupBy('platform')
                            ->orderBy('count', 'desc')
                            ->pluck('count', 'platform')
                            ->toArray();
    }

    protected function getDeviceStatistics(Builder $query): array
    {
        return (clone $query)->select('device', \DB::raw('COUNT(*) as count'))
                            ->groupBy('device')
                            ->orderBy('count', 'desc')
                            ->pluck('count', 'device')
                            ->toArray();
    }

    protected function getHourlyDistribution(Builder $query): array
    {
        return (clone $query)->select(\DB::raw('HOUR(requested_at) as hour'), \DB::raw('COUNT(*) as count'))
                            ->groupBy('hour')
                            ->orderBy('hour')
                            ->pluck('count', 'hour')
                            ->toArray();
    }

    protected function exportToCsv(Collection $data): string
    {
        $headers = [
            'ID', 'User ID', 'IP Address', 'Requested At', 'Method', 'URL', 'Response Code',
            'Response Time (ms)', 'Memory (bytes)', 'Query Count', 'Query Time (ms)', 'Browser', 'Platform', 'Device',
            'Controller Action', 'Country', 'City', 'Error Message', 'Created At'
        ];
        
        $csv = implode(',', $headers) . "\n";
        
        foreach ($data as $log) {
            $row = [
                $log->id,
                $log->user_id,
                $log->ip_address,
                $log->requested_at->toIso8601String(),
                $log->method,
                '"' . str_replace('"', '""', $log->url) . '"',
                $log->response_code,
                $log->response_time,
                $log->memory_usage,
                $log->query_count,
                $log->query_time,
                $log->browser,
                $log->platform,
                $log->device,
                '"' . str_replace('"', '""', $log->controller_action ?? '') . '"',
                $log->country ?? '',
                $log->city ?? '',
                '"' . str_replace('"', '""', $log->error_message ?? '') . '"',
                $log->created_at->toIso8601String()
            ];
            
            $csv .= implode(',', $row) . "\n";
        }
        
        return $csv;
    }

    protected function exportToXml(Collection $data): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<activity_logs>';
        
        foreach ($data as $log) {
            $xml .= '<log>';
            $xml .= '<id>' . $log->id . '</id>';
            $xml .= '<user_id>' . $log->user_id . '</user_id>';
            $xml .= '<ip_address>' . htmlspecialchars($log->ip_address) . '</ip_address>';
            $xml .= '<requested_at>' . $log->requested_at->toIso8601String() . '</requested_at>';
            $xml .= '<method>' . $log->method . '</method>';
            $xml .= '<url>' . htmlspecialchars($log->url) . '</url>';
            $xml .= '<response_code>' . $log->response_code . '</response_code>';
            $xml .= '<response_time>' . $log->response_time . '</response_time>';
            $xml .= '<memory_usage>' . $log->memory_usage . '</memory_usage>';
            $xml .= '<query_count>' . $log->query_count . '</query_count>';
            $xml .= '<query_time>' . $log->query_time . '</query_time>';
            $xml .= '<browser>' . htmlspecialchars($log->browser ?? '') . '</browser>';
            $xml .= '<platform>' . htmlspecialchars($log->platform ?? '') . '</platform>';
            $xml .= '<device>' . htmlspecialchars($log->device ?? '') . '</device>';
            $xml .= '<controller_action>' . htmlspecialchars($log->controller_action ?? '') . '</controller_action>';
            $xml .= '<country>' . htmlspecialchars($log->country ?? '') . '</country>';
            $xml .= '<city>' . htmlspecialchars($log->city ?? '') . '</city>';
            $xml .= '<error_message>' . htmlspecialchars($log->error_message ?? '') . '</error_message>';
            $xml .= '<created_at>' . $log->created_at->toIso8601String() . '</created_at>';
            $xml .= '</log>';
        }
        
        $xml .= '</activity_logs>';
        
        return $xml;
    }
}