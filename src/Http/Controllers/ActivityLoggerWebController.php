<?php

namespace ActivityLogger\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use ActivityLogger\Services\ActivityLogSearchService;
use ActivityLogger\Facades\ActivityLogger;
use Carbon\Carbon;

class ActivityLoggerWebController extends Controller
{
    protected $searchService;
    
    public function __construct(ActivityLogSearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    /**
     * Main dashboard view
     */
    public function index(): View
    {
        $dateRange = $this->getDefaultDateRange();
        
        // Get dashboard data
        $overview = $this->getOverviewStats($dateRange);
        $recentActivity = $this->searchService->getRecentActivity(10);
        $recentErrors = ActivityLogger::getRecentErrors(60, 5);
        
        return view('activity-logger::dashboard.index', compact(
            'overview', 
            'recentActivity', 
            'recentErrors'
        ));
    }

    /**
     * Activity logs listing view
     */
    public function logs(Request $request): View
    {
        $filters = $this->buildFilters($request);
        $perPage = $request->get('per_page', 50);
        
        $logs = ActivityLogger::search($filters, $perPage);
        $filterOptions = $this->getFilterOptions();
        
        return view('activity-logger::logs.index', compact(
            'logs', 
            'filters', 
            'filterOptions'
        ));
    }

    /**
     * Individual log detail view
     */
    public function showLog($id): View
    {
        $log = ActivityLogger::find($id);
        
        if (!$log) {
            abort(404, 'Activity log not found');
        }

        return view('activity-logger::logs.show', compact('log'));
    }

    /**
     * Performance analytics view
     */
    public function performance(): View
    {
        $dateRange = $this->getDefaultDateRange();
        
        $performanceData = [
            'overview' => $this->getPerformanceOverview($dateRange),
            'slow_requests' => ActivityLogger::getSlowRequests(1000, 20),
            'high_memory_requests' => ActivityLogger::getHighMemoryRequests(52428800, 20),
            'performance_trends' => $this->getPerformanceTrends($dateRange),
        ];
        
        return view('activity-logger::performance.index', compact('performanceData'));
    }

    /**
     * Error monitoring view
     */
    public function errors(): View
    {
        $dateRange = $this->getDefaultDateRange();
        
        $errorData = [
            'overview' => $this->getErrorOverview($dateRange),
            'recent_errors' => $this->searchService->searchErrors($dateRange)->take(50),
            'error_trends' => $this->getErrorTrends($dateRange),
            'top_error_urls' => $this->getTopErrorUrls($dateRange),
        ];
        
        return view('activity-logger::errors.index', compact('errorData'));
    }

    /**
     * Traffic analysis view
     */
    public function traffic(): View
    {
        $dateRange = $this->getDefaultDateRange();
        
        $trafficData = [
            'overview' => $this->getTrafficOverview($dateRange),
            'top_pages' => $this->getTopPages($dateRange),
            'geographic_stats' => $this->getGeographicStats($dateRange),
            'device_stats' => $this->getDeviceStats($dateRange),
            'browser_stats' => $this->getBrowserStats($dateRange),
        ];
        
        return view('activity-logger::traffic.index', compact('trafficData'));
    }

    /**
     * User activity view
     */
    public function users(): View
    {
        $dateRange = $this->getDefaultDateRange();
        
        $userData = [
            'overview' => $this->getUserOverview($dateRange),
            'active_users' => $this->getActiveUsers($dateRange),
            'user_patterns' => $this->getUserPatterns($dateRange),
        ];
        
        return view('activity-logger::users.index', compact('userData'));
    }

    /**
     * Individual user activity view
     */
    public function showUser($id): View
    {
        $dateRange = $this->getDefaultDateRange();
        
        $userActivity = [
            'user_logs' => $this->searchService->searchByUser($id, $dateRange),
            'user_stats' => $this->getUserStats($id, $dateRange),
            'user_patterns' => $this->getUserPatterns($id, $dateRange),
        ];
        
        return view('activity-logger::users.show', compact('userActivity', 'id'));
    }

    /**
     * Reports view
     */
    public function reports(): View
    {
        $reportTemplates = $this->getReportTemplates();
        $scheduledReports = $this->getScheduledReports();
        
        return view('activity-logger::reports.index', compact(
            'reportTemplates', 
            'scheduledReports'
        ));
    }

    /**
     * AJAX: Real-time stats for dashboard
     */
    public function realtimeStats(): JsonResponse
    {
        $stats = [
            'current_active_users' => $this->getActiveUsersCount(),
            'requests_last_hour' => $this->getRequestsLastHour(),
            'errors_last_hour' => $this->getErrorsLastHour(), 
            'avg_response_time' => $this->getAvgResponseTimeLastHour(),
            'recent_activity' => $this->searchService->getRecentActivity(5),
        ];

        return response()->json($stats);
    }

    /**
     * AJAX: Chart data for various visualizations
     */
    public function chartData(Request $request): JsonResponse
    {
        $type = $request->get('type');
        $dateRange = $this->getDateRange($request);
        
        switch ($type) {
            case 'requests_timeline':
                return response()->json($this->getRequestsTimeline($dateRange));
            case 'response_codes':
                return response()->json($this->getResponseCodesChart($dateRange));
            case 'performance_trends':
                return response()->json($this->getPerformanceTrendsChart($dateRange));
            case 'error_trends':
                return response()->json($this->getErrorTrendsChart($dateRange));
            default:
                return response()->json(['error' => 'Unknown chart type'], 400);
        }
    }

    /**
     * AJAX: Export data
     */
    public function export(Request $request): JsonResponse
    {
        $filters = $this->buildFilters($request);
        $format = $request->get('format', 'json');
        
        try {
            $data = ActivityLogger::export($filters, $format);
            
            return response()->json([
                'success' => true,
                'download_url' => route('activity-logger.download', [
                    'filename' => 'activity_logs_' . now()->format('Y-m-d_H-i-s') . '.' . $format
                ])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Export failed: ' . $e->getMessage()
            ], 500);
        }
    }

    // Protected helper methods

    protected function getDefaultDateRange(): array
    {
        return [
            'start_date' => Carbon::today()->subDays(7)->toDateString(),
            'end_date' => Carbon::today()->toDateString(),
        ];
    }

    protected function getDateRange(Request $request): array
    {
        return [
            'start_date' => $request->get('start_date', Carbon::today()->subDays(7)->toDateString()),
            'end_date' => $request->get('end_date', Carbon::today()->toDateString()),
        ];
    }

    protected function buildFilters(Request $request): array
    {
        $filters = [];
        
        $filterFields = [
            'user_id', 'ip_address', 'method', 'url', 'route_name', 
            'controller_action', 'response_code', 'country', 'city',
            'browser', 'platform', 'device', 'start_date', 'end_date'
        ];
        
        foreach ($filterFields as $field) {
            if ($request->filled($field)) {
                $filters[$field] = $request->get($field);
            }
        }
        
        return $filters;
    }

    protected function getOverviewStats(array $dateRange): array
    {
        $stats = ActivityLogger::getStatistics($dateRange);
        
        return [
            'total_requests' => $stats['total_requests'],
            'unique_users' => $stats['unique_users'],
            'unique_ips' => $stats['unique_ips'],
            'success_rate' => $stats['success_rate'],
            'error_rate' => 100 - $stats['success_rate'],
            'avg_response_time' => round($stats['average_response_time'], 2),
        ];
    }

    protected function getFilterOptions(): array
    {
        // Get filter options from database
        return [
            'methods' => ['GET', 'POST', 'PUT', 'DELETE', 'PATCH'],
            'response_codes' => [200, 201, 302, 400, 401, 403, 404, 422, 500, 503],
            'browsers' => ['Chrome', 'Firefox', 'Safari', 'Edge'],
            'platforms' => ['Windows', 'Mac', 'Linux', 'Android', 'iOS'],
            'devices' => ['Desktop', 'Mobile', 'Tablet'],
        ];
    }

    protected function getPerformanceOverview(array $dateRange): array
    {
        $stats = ActivityLogger::getStatistics($dateRange);
        
        return [
            'avg_response_time' => round($stats['average_response_time'], 2),
            'avg_memory_usage' => round($stats['average_memory'] / (1024 * 1024), 2),
            'avg_query_count' => round($stats['average_query_count'], 2),
            'slow_requests_count' => count(ActivityLogger::getSlowRequests(1000, 1000)),
        ];
    }

    protected function getErrorOverview(array $dateRange): array
    {
        $errors = $this->searchService->searchErrors($dateRange);
        
        return [
            'total_errors' => $errors->count(),
            'error_rate' => $this->calculateErrorRate($dateRange),
            'critical_errors' => $errors->where('response_code', '>=', 500)->count(),
            'client_errors' => $errors->where('response_code', '>=', 400)->where('response_code', '<', 500)->count(),
        ];
    }

    protected function getTrafficOverview(array $dateRange): array
    {
        $stats = ActivityLogger::getStatistics($dateRange);
        
        return [
            'total_requests' => $stats['total_requests'],
            'unique_visitors' => $stats['unique_ips'],
            'page_views' => $stats['total_requests'],
            'top_referrers' => [], // Would need additional implementation
        ];
    }

    protected function getUserOverview(array $dateRange): array
    {
        $stats = ActivityLogger::getStatistics($dateRange);
        
        return [
            'total_users' => $stats['unique_users'],
            'active_users' => $this->getActiveUsersCount(),
            'new_users' => 0, // Would need user registration tracking
            'returning_users' => 0, // Would need session analysis
        ];
    }

    protected function getActiveUsersCount(): int
    {
        return ActivityLogger::search([
            'start_date' => Carbon::now()->subMinutes(30)->toDateString()
        ])->unique('user_id')->count();
    }

    protected function getRequestsLastHour(): int
    {
        return ActivityLogger::search([
            'start_date' => Carbon::now()->subHour()->toDateString()
        ])->count();
    }

    protected function getErrorsLastHour(): int
    {
        return $this->searchService->searchErrors([
            'start_date' => Carbon::now()->subHour()->toDateString()
        ])->count();
    }

    protected function getAvgResponseTimeLastHour(): float
    {
        $recentLogs = ActivityLogger::search([
            'start_date' => Carbon::now()->subHour()->toDateString()
        ]);
        
        return round($recentLogs->avg('response_time'), 2);
    }

    protected function calculateErrorRate(array $dateRange): float
    {
        $totalRequests = ActivityLogger::search($dateRange)->count();
        $errorRequests = $this->searchService->searchErrors($dateRange)->count();
        
        return $totalRequests > 0 ? round(($errorRequests / $totalRequests) * 100, 2) : 0;
    }

    protected function getPerformanceTrends(array $dateRange): array
    {
        // Implementation for performance trends
        return [];
    }

    protected function getErrorTrends(array $dateRange): array
    {
        // Implementation for error trends
        return [];
    }

    protected function getTopErrorUrls(array $dateRange): array
    {
        $errors = $this->searchService->searchErrors($dateRange);
        return $errors->groupBy('url')->map->count()->sortDesc()->take(10)->toArray();
    }

    protected function getTopPages(array $dateRange): array
    {
        $stats = ActivityLogger::getStatistics($dateRange);
        return array_slice($stats['top_urls'], 0, 20, true);
    }

    protected function getGeographicStats(array $dateRange): array
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

    protected function getBrowserStats(array $dateRange): array
    {
        $stats = ActivityLogger::getStatistics($dateRange);
        return $stats['browsers'];
    }

    protected function getActiveUsers(array $dateRange): array
    {
        // Implementation for active users list
        return [];
    }

    protected function getUserPatterns($userId, array $dateRange): array
    {
        // Implementation for user patterns
        return [];
    }

    protected function getUserStats($userId, array $dateRange): array
    {
        // Implementation for individual user stats
        return [];
    }

    protected function getReportTemplates(): array
    {
        return [
            'daily_summary' => 'Daily Activity Summary',
            'error_report' => 'Error Analysis Report',
            'performance_report' => 'Performance Analysis Report',
            'user_activity' => 'User Activity Report',
        ];
    }

    protected function getScheduledReports(): array
    {
        // Implementation for scheduled reports
        return [];
    }

    protected function getRequestsTimeline(array $dateRange): array
    {
        // Implementation for requests timeline chart
        return [];
    }

    protected function getResponseCodesChart(array $dateRange): array
    {
        // Implementation for response codes chart
        return [];
    }

    protected function getPerformanceTrendsChart(array $dateRange): array
    {
        // Implementation for performance trends chart
        return [];
    }

    protected function getErrorTrendsChart(array $dateRange): array
    {
        // Implementation for error trends chart
        return [];
    }
}