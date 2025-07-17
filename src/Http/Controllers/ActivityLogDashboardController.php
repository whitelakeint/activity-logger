<?php

namespace ActivityLogger\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use ActivityLogger\Services\ActivityLogSearchService;
use ActivityLogger\Facades\ActivityLogger;
use Carbon\Carbon;

class ActivityLogDashboardController extends Controller
{
    protected $searchService;
    
    public function __construct(ActivityLogSearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    public function dashboard(Request $request): JsonResponse
    {
        $dateRange = $this->getDateRange($request);
        
        return response()->json([
            'overview' => $this->getOverview($dateRange),
            'performance' => $this->getPerformanceMetrics($dateRange),
            'errors' => $this->getErrorMetrics($dateRange),
            'traffic' => $this->getTrafficMetrics($dateRange),
            'charts' => $this->getChartData($dateRange),
        ]);
    }

    public function realtimeStats(): JsonResponse
    {
        $stats = [
            'current_active_users' => $this->getActiveUsers(),
            'requests_last_hour' => $this->getRequestsLastHour(),
            'errors_last_hour' => $this->getErrorsLastHour(),
            'performance_last_hour' => $this->getPerformanceLastHour(),
            'recent_activity' => $this->searchService->getRecentActivity(1),
        ];

        return response()->json($stats);
    }

    public function performanceReport(Request $request): JsonResponse
    {
        $dateRange = $this->getDateRange($request);
        
        $report = [
            'slow_requests' => ActivityLogger::getSlowRequests(1000, 50),
            'high_memory_requests' => ActivityLogger::getHighMemoryRequests(52428800, 50),
            'high_query_requests' => ActivityLogger::getHighQueryCountRequests(50, 50),
            'performance_trends' => $this->getPerformanceTrends($dateRange),
            'bottlenecks' => $this->getBottlenecks($dateRange),
        ];

        return response()->json($report);
    }

    public function errorReport(Request $request): JsonResponse
    {
        $dateRange = $this->getDateRange($request);
        
        $report = [
            'recent_errors' => $this->searchService->searchErrors($dateRange),
            'error_trends' => $this->getErrorTrends($dateRange),
            'top_error_urls' => $this->getTopErrorUrls($dateRange),
            'error_distribution' => $this->getErrorDistribution($dateRange),
        ];

        return response()->json($report);
    }

    public function trafficReport(Request $request): JsonResponse
    {
        $dateRange = $this->getDateRange($request);
        
        $report = [
            'traffic_overview' => $this->getTrafficOverview($dateRange),
            'top_pages' => $this->getTopPages($dateRange),
            'user_agents' => $this->getUserAgentStats($dateRange),
            'geographical_distribution' => $this->getGeographicalStats($dateRange),
            'device_stats' => $this->getDeviceStats($dateRange),
        ];

        return response()->json($report);
    }

    public function userActivity(Request $request, int $userId): JsonResponse
    {
        $dateRange = $this->getDateRange($request);
        
        $activity = [
            'user_logs' => $this->searchService->searchByUser($userId, $dateRange),
            'user_stats' => $this->getUserStats($userId, $dateRange),
            'user_patterns' => $this->getUserPatterns($userId, $dateRange),
        ];

        return response()->json($activity);
    }

    protected function getDateRange(Request $request): array
    {
        $startDate = $request->get('start_date', Carbon::today()->subDays(7)->toDateString());
        $endDate = $request->get('end_date', Carbon::today()->toDateString());
        
        return [
            'start_date' => $startDate,
            'end_date' => $endDate,
        ];
    }

    protected function getOverview(array $dateRange): array
    {
        $stats = ActivityLogger::getStatistics($dateRange);
        
        return [
            'total_requests' => $stats['total_requests'],
            'unique_users' => $stats['unique_users'],
            'unique_ips' => $stats['unique_ips'],
            'success_rate' => $stats['success_rate'],
            'error_rate' => 100 - $stats['success_rate'],
            'avg_response_time' => round($stats['average_response_time'], 2),
            'total_errors' => $this->countErrors($dateRange),
        ];
    }

    protected function getPerformanceMetrics(array $dateRange): array
    {
        $stats = ActivityLogger::getStatistics($dateRange);
        
        return [
            'avg_response_time' => round($stats['average_response_time'], 2),
            'avg_memory_usage' => round($stats['average_memory'] / (1024 * 1024), 2),
            'avg_query_count' => round($stats['average_query_count'], 2),
            'avg_query_time' => round($stats['average_query_time'], 2),
            'slow_requests_count' => count(ActivityLogger::getSlowRequests(1000, 1000)),
            'high_memory_count' => count(ActivityLogger::getHighMemoryRequests(52428800, 1000)),
        ];
    }

    protected function getErrorMetrics(array $dateRange): array
    {
        $errors = $this->searchService->searchErrors($dateRange);
        
        $errorsByCode = $errors->groupBy('response_code');
        $errorsByType = $errors->groupBy('error_type');
        
        return [
            'total_errors' => $errors->count(),
            'errors_by_code' => $errorsByCode->map->count()->toArray(),
            'errors_by_type' => $errorsByType->map->count()->toArray(),
            'recent_errors' => $errors->take(10)->values(),
        ];
    }

    protected function getTrafficMetrics(array $dateRange): array
    {
        $stats = ActivityLogger::getStatistics($dateRange);
        
        return [
            'total_requests' => $stats['total_requests'],
            'methods' => $stats['methods'],
            'top_urls' => array_slice($stats['top_urls'], 0, 10, true),
            'browsers' => $stats['browsers'],
            'platforms' => $stats['platforms'],
            'devices' => $stats['devices'],
        ];
    }

    protected function getChartData(array $dateRange): array
    {
        $stats = ActivityLogger::getStatistics($dateRange);
        
        return [
            'hourly_requests' => $stats['hourly_distribution'],
            'response_codes' => $stats['response_codes'],
            'methods' => $stats['methods'],
            'devices' => $stats['devices'],
        ];
    }

    protected function getActiveUsers(): int
    {
        return ActivityLogger::search(['start_date' => Carbon::now()->subMinutes(30)->toDateString()])->unique('user_id')->count();
    }

    protected function getRequestsLastHour(): int
    {
        return ActivityLogger::search(['start_date' => Carbon::now()->subHour()->toDateString()])->count();
    }

    protected function getErrorsLastHour(): int
    {
        return $this->searchService->searchErrors(['start_date' => Carbon::now()->subHour()->toDateString()])->count();
    }

    protected function getPerformanceLastHour(): array
    {
        $recentLogs = ActivityLogger::search(['start_date' => Carbon::now()->subHour()->toDateString()]);
        
        return [
            'avg_response_time' => $recentLogs->avg('response_time'),
            'avg_memory_usage' => $recentLogs->avg('memory_usage'),
            'slow_requests' => $recentLogs->where('response_time', '>', 1000)->count(),
        ];
    }

    protected function countErrors(array $dateRange): int
    {
        return $this->searchService->searchErrors($dateRange)->count();
    }

    protected function getPerformanceTrends(array $dateRange): array
    {
        // Implementation for performance trends over time
        return [];
    }

    protected function getBottlenecks(array $dateRange): array
    {
        // Implementation for identifying bottlenecks
        return [];
    }

    protected function getErrorTrends(array $dateRange): array
    {
        // Implementation for error trends over time
        return [];
    }

    protected function getTopErrorUrls(array $dateRange): array
    {
        $errors = $this->searchService->searchErrors($dateRange);
        return $errors->groupBy('url')->map->count()->sortDesc()->take(10)->toArray();
    }

    protected function getErrorDistribution(array $dateRange): array
    {
        $errors = $this->searchService->searchErrors($dateRange);
        return $errors->groupBy('error_type')->map->count()->toArray();
    }

    protected function getTrafficOverview(array $dateRange): array
    {
        $stats = ActivityLogger::getStatistics($dateRange);
        return [
            'total_requests' => $stats['total_requests'],
            'unique_visitors' => $stats['unique_ips'],
            'page_views' => $stats['total_requests'],
            'bounce_rate' => 0, // Would need session tracking
        ];
    }

    protected function getTopPages(array $dateRange): array
    {
        $stats = ActivityLogger::getStatistics($dateRange);
        return array_slice($stats['top_urls'], 0, 20, true);
    }

    protected function getUserAgentStats(array $dateRange): array
    {
        $stats = ActivityLogger::getStatistics($dateRange);
        return [
            'browsers' => $stats['browsers'],
            'platforms' => $stats['platforms'],
        ];
    }

    protected function getGeographicalStats(array $dateRange): array
    {
        return ActivityLogger::search($dateRange)
            ->groupBy('country')
            ->map(function ($group) {
                return [
                    'requests' => $group->count(),
                    'cities' => $group->pluck('city')->unique()->values(),
                ];
            })
            ->toArray();
    }

    protected function getDeviceStats(array $dateRange): array
    {
        $stats = ActivityLogger::getStatistics($dateRange);
        return $stats['devices'];
    }

    protected function getUserStats(int $userId, array $dateRange): array
    {
        $userLogs = $this->searchService->searchByUser($userId, $dateRange);
        
        return [
            'total_requests' => $userLogs->count(),
            'unique_sessions' => $userLogs->unique('session_id')->count(),
            'avg_response_time' => $userLogs->avg('response_time'),
            'errors' => $userLogs->whereNotNull('error_message')->count(),
            'most_visited_pages' => $userLogs->groupBy('url')->map->count()->sortDesc()->take(10)->toArray(),
        ];
    }

    protected function getUserPatterns(int $userId, array $dateRange): array
    {
        $userLogs = $this->searchService->searchByUser($userId, $dateRange);
        
        return [
            'active_hours' => $userLogs->groupBy(function ($log) {
                return $log->requested_at->hour;
            })->map->count()->toArray(),
            'devices_used' => $userLogs->pluck('device')->unique()->values()->toArray(),
            'browsers_used' => $userLogs->pluck('browser')->unique()->values()->toArray(),
        ];
    }
}