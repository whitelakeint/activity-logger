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
    public function index(Request $request): View
    {
        $filters = $this->buildFilters($request);
        $dateRange = !empty($filters) ? $filters : $this->getDefaultDateRange();
        
        // Get dashboard data with filters applied
        $overview = $this->getOverviewStats($dateRange);
        $recentActivity = ActivityLogger::search(array_merge($filters, ['limit' => 10]));
        $recentErrors = $this->searchService->searchErrors($filters)->take(5);
        $filterOptions = $this->getFilterOptions();
        
        return view('activity-logger::dashboard.index', compact(
            'overview', 
            'recentActivity', 
            'recentErrors',
            'filterOptions'
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
            'user_patterns' => $this->getAllUserPatterns($dateRange),
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
     * AJAX endpoint for real-time notifications
     */
    public function realtimeNotifications(): JsonResponse
    {
        $notifications = $this->getRealtimeNotifications();
        
        return response()->json([
            'notifications' => $notifications,
            'timestamp' => now()->toIso8601String()
        ]);
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
            
            // Store the exported data in session for download
            session(['export_data' => $data, 'export_format' => $format]);
            
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
    
    /**
     * Download exported file
     */
    public function download($filename)
    {
        $data = session('export_data');
        $format = session('export_format', 'json');
        
        if (!$data) {
            abort(404, 'Export data not found');
        }
        
        // Clear the session data
        session()->forget(['export_data', 'export_format']);
        
        $headers = [
            'Content-Type' => $format === 'json' ? 'application/json' : 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        if ($format === 'json') {
            $content = json_encode($data, JSON_PRETTY_PRINT);
        } else {
            // CSV format
            $content = $this->convertToCSV($data);
        }
        
        return response($content, 200, $headers);
    }
    
    /**
     * Convert data to CSV format
     */
    protected function convertToCSV($data): string
    {
        if (empty($data)) {
            return '';
        }
        
        $output = fopen('php://temp', 'r+');
        
        // Write headers
        if (is_array($data) && !empty($data)) {
            $firstRow = reset($data);
            if (is_array($firstRow) || is_object($firstRow)) {
                fputcsv($output, array_keys((array)$firstRow));
            }
        }
        
        // Write data
        foreach ($data as $row) {
            fputcsv($output, (array)$row);
        }
        
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);
        
        return $csv;
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
            'browser', 'platform', 'device', 'start_date', 'end_date',
            'min_response_time', 'errors_only'
        ];
        
        foreach ($filterFields as $field) {
            if ($request->filled($field)) {
                $filters[$field] = $request->get($field);
            }
        }
        
        // Handle special filters
        if ($request->filled('min_response_time')) {
            $filters['min_response_time'] = (int) $request->get('min_response_time');
        }
        
        if ($request->filled('errors_only') && $request->get('errors_only') == '1') {
            $filters['response_code_min'] = 400;
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
        try {
            // Get dynamic filter options from database
            $logs = ActivityLogger::search([
                'start_date' => Carbon::now()->subDays(30)->toDateString(),
                'end_date' => Carbon::now()->toDateString(),
            ]);

            return [
                'methods' => $logs->pluck('method')->unique()->filter()->sort()->values()->toArray(),
                'response_codes' => $logs->pluck('response_code')->unique()->filter()->sort()->values()->toArray(),
                'routes' => $logs->pluck('route_name')->unique()->filter()->sort()->values()->toArray(),
                'devices' => $logs->pluck('device')->unique()->filter()->sort()->values()->toArray(),
                'countries' => $logs->pluck('country')->unique()->filter()->sort()->values()->toArray(),
                'browsers' => $logs->pluck('browser')->unique()->filter()->sort()->values()->toArray(),
            ];
        } catch (\Exception $e) {
            // Fallback to static values if database query fails
            return [
                'methods' => ['GET', 'POST', 'PUT', 'DELETE', 'PATCH'],
                'response_codes' => [200, 201, 302, 400, 401, 403, 404, 422, 500, 503],
                'routes' => [],
                'devices' => ['Desktop', 'Mobile', 'Tablet'],
                'countries' => [],
                'browsers' => ['Chrome', 'Firefox', 'Safari', 'Edge'],
            ];
        }
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
        $topUrls = $stats['top_urls'] ?? [];
        
        // Convert Collection to array if needed
        if ($topUrls instanceof \Illuminate\Support\Collection) {
            $topUrls = $topUrls->toArray();
        }
        
        return is_array($topUrls) ? array_slice($topUrls, 0, 20, true) : [];
    }

    protected function getGeographicStats(array $dateRange): array
    {
        return ActivityLogger::search($dateRange)
            ->groupBy('country')
            ->map(function ($group) {
                return [
                    'requests' => $group->count(),
                    'cities' => $group->pluck('city')->unique()->values()->toArray(),
                ];
            })
            ->toArray();
    }

    protected function getDeviceStats(array $dateRange): array
    {
        $stats = ActivityLogger::getStatistics($dateRange);
        $devices = $stats['devices'] ?? [];
        
        // Convert Collection to array if needed
        if ($devices instanceof \Illuminate\Support\Collection) {
            $devices = $devices->toArray();
        }
        
        return is_array($devices) ? $devices : [];
    }

    protected function getBrowserStats(array $dateRange): array
    {
        $stats = ActivityLogger::getStatistics($dateRange);
        $browsers = $stats['browsers'] ?? [];
        
        // Convert Collection to array if needed
        if ($browsers instanceof \Illuminate\Support\Collection) {
            $browsers = $browsers->toArray();
        }
        
        return is_array($browsers) ? $browsers : [];
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
    
    protected function getAllUserPatterns(array $dateRange): array
    {
        // Implementation for all user patterns
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
    
    /**
     * Get real-time notifications data
     */
    protected function getRealtimeNotifications(): array
    {
        $notifications = [];
        
        try {
            // Check for recent errors (last 5 minutes only)
            $recentErrors = $this->searchService->searchErrors([
                'start_date' => now()->subMinutes(5)->toDateString(),
                'end_date' => now()->toDateString(),
            ])->take(3); // Limit to 3 to avoid overwhelming
            
            foreach ($recentErrors as $error) {
                $notifications[] = [
                    'type' => 'error',
                    'title' => 'Error Detected',
                    'message' => "Error {$error->response_code} on {$error->url}",
                    'timestamp' => $error->created_at->toIso8601String(),
                    'severity' => 'high',
                ];
            }
            
            // Check for performance issues (last 5 minutes only)
            $slowRequests = ActivityLogger::search([
                'start_date' => now()->subMinutes(5)->toDateString(),
                'end_date' => now()->toDateString(),
            ])->where('response_time', '>', 3000)->take(2); // Limit to 2
            
            foreach ($slowRequests as $request) {
                $notifications[] = [
                    'type' => 'performance',
                    'title' => 'Slow Request Detected',
                    'message' => "Response time: {$request->response_time}ms for {$request->url}",
                    'timestamp' => $request->created_at->toIso8601String(),
                    'severity' => 'medium',
                ];
            }
            
        } catch (\Exception $e) {
            // Log error but don't break the response
            \Log::error('Error fetching notifications: ' . $e->getMessage());
        }
        
        return $notifications;
    }
}