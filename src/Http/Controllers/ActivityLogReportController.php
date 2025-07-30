<?php

namespace ActivityLogger\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use ActivityLogger\Services\ActivityLogSearchService;
use ActivityLogger\Facades\ActivityLogger;
use Carbon\Carbon;

class ActivityLogReportController extends Controller
{
    protected $searchService;
    
    public function __construct(ActivityLogSearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    public function dailyReport(Request $request): JsonResponse
    {
        $date = $request->get('date', Carbon::today()->toDateString());
        
        $report = [
            'date' => $date,
            'summary' => $this->getDailySummary($date),
            'hourly_breakdown' => $this->getHourlyBreakdown($date),
            'top_pages' => $this->getTopPagesForDate($date),
            'top_users' => $this->getTopUsersForDate($date),
            'errors' => $this->getErrorsForDate($date),
            'performance' => $this->getPerformanceForDate($date),
        ];

        return response()->json($report);
    }

    public function weeklyReport(Request $request): JsonResponse
    {
        $endDate = $request->get('end_date', Carbon::today()->toDateString());
        $startDate = Carbon::parse($endDate)->subDays(6)->toDateString();
        
        $report = [
            'period' => ['start' => $startDate, 'end' => $endDate],
            'summary' => $this->getWeeklySummary($startDate, $endDate),
            'daily_breakdown' => $this->getDailyBreakdown($startDate, $endDate),
            'trends' => $this->getWeeklyTrends($startDate, $endDate),
            'top_performers' => $this->getTopPerformers($startDate, $endDate),
        ];

        return response()->json($report);
    }

    public function monthlyReport(Request $request): JsonResponse
    {
        $month = $request->get('month', Carbon::now()->format('Y-m'));
        $startDate = Carbon::parse($month . '-01')->toDateString();
        $endDate = Carbon::parse($month . '-01')->endOfMonth()->toDateString();
        
        $report = [
            'period' => ['start' => $startDate, 'end' => $endDate],
            'summary' => $this->getMonthlySummary($startDate, $endDate),
            'weekly_breakdown' => $this->getWeeklyBreakdown($startDate, $endDate),
            'growth_metrics' => $this->getGrowthMetrics($startDate, $endDate),
            'insights' => $this->getMonthlyInsights($startDate, $endDate),
        ];

        return response()->json($report);
    }

    public function customReport(Request $request): JsonResponse
    {
        $filters = $this->buildFilters($request);
        
        $report = [
            'filters' => $filters,
            'summary' => $this->getCustomSummary($filters),
            'detailed_analysis' => $this->getDetailedAnalysis($filters),
            'recommendations' => $this->getRecommendations($filters),
        ];

        return response()->json($report);
    }

    public function exportReport(Request $request): JsonResponse
    {
        $type = $request->get('type', 'daily');
        $format = $request->get('format', 'json');
        
        switch ($type) {
            case 'daily':
                $reportData = $this->dailyReport($request)->getData();
                break;
            case 'weekly':
                $reportData = $this->weeklyReport($request)->getData();
                break;
            case 'monthly':
                $reportData = $this->monthlyReport($request)->getData();
                break;
            case 'custom':
                $reportData = $this->customReport($request)->getData();
                break;
            default:
                $reportData = ['error' => 'Invalid report type'];
                break;
        }

        if ($format === 'csv') {
            return $this->exportToCsv($reportData);
        }

        return response()->json($reportData);
    }

    protected function getDailySummary(string $date): array
    {
        $logs = $this->searchService->searchByDateRange($date);
        
        return [
            'total_requests' => $logs->count(),
            'unique_users' => $logs->unique('user_id')->count(),
            'unique_ips' => $logs->unique('ip_address')->count(),
            'success_rate' => $this->calculateSuccessRate($logs),
            'avg_response_time' => round($logs->avg('response_time'), 2),
            'total_errors' => $logs->whereNotNull('error_message')->count(),
            'peak_hour' => $this->getPeakHour($logs),
        ];
    }

    protected function getHourlyBreakdown(string $date): array
    {
        $logs = $this->searchService->searchByDateRange($date);
        
        $hourly = [];
        for ($hour = 0; $hour < 24; $hour++) {
            $hourLogs = $logs->filter(function ($log) use ($hour) {
                return $log->requested_at->hour === $hour;
            });
            
            $hourly[$hour] = [
                'hour' => $hour,
                'requests' => $hourLogs->count(),
                'errors' => $hourLogs->whereNotNull('error_message')->count(),
                'avg_response_time' => round($hourLogs->avg('response_time'), 2),
            ];
        }
        
        return $hourly;
    }

    protected function getTopPagesForDate(string $date): array
    {
        $logs = $this->searchService->searchByDateRange($date);
        
        return $logs->groupBy('url')
                   ->map(function ($group) {
                       return [
                           'requests' => $group->count(),
                           'avg_response_time' => round($group->avg('response_time'), 2),
                           'errors' => $group->whereNotNull('error_message')->count(),
                       ];
                   })
                   ->sortByDesc('requests')
                   ->take(20)
                   ->toArray();
    }

    protected function getTopUsersForDate(string $date): array
    {
        $logs = $this->searchService->searchByDateRange($date);
        
        return $logs->whereNotNull('user_id')
                   ->groupBy('user_id')
                   ->map(function ($group) {
                       return [
                           'requests' => $group->count(),
                           'unique_sessions' => $group->unique('session_id')->count(),
                           'errors' => $group->whereNotNull('error_message')->count(),
                       ];
                   })
                   ->sortByDesc('requests')
                   ->take(20)
                   ->toArray();
    }

    protected function getErrorsForDate(string $date): array
    {
        $errors = $this->searchService->searchErrors(['date' => $date]);
        
        return [
            'total_errors' => $errors->count(),
            'by_type' => $errors->groupBy('error_type')->map->count()->toArray(),
            'by_code' => $errors->groupBy('response_code')->map->count()->toArray(),
            'top_error_pages' => $errors->groupBy('url')->map->count()->sortDesc()->take(10)->toArray(),
        ];
    }

    protected function getPerformanceForDate(string $date): array
    {
        $logs = $this->searchService->searchByDateRange($date);
        
        return [
            'avg_response_time' => round($logs->avg('response_time'), 2),
            'avg_memory_usage' => round($logs->avg('memory_usage') / (1024 * 1024), 2),
            'avg_query_count' => round($logs->avg('query_count'), 2),
            'slow_requests' => $logs->where('response_time', '>', 1000)->count(),
            'high_memory_requests' => $logs->where('memory_usage', '>', 52428800)->count(),
        ];
    }

    protected function getWeeklySummary(string $startDate, string $endDate): array
    {
        $logs = $this->searchService->searchByDateRange($startDate, $endDate);
        
        return [
            'total_requests' => $logs->count(),
            'daily_average' => round($logs->count() / 7, 2),
            'unique_users' => $logs->unique('user_id')->count(),
            'unique_ips' => $logs->unique('ip_address')->count(),
            'success_rate' => $this->calculateSuccessRate($logs),
            'total_errors' => $logs->whereNotNull('error_message')->count(),
            'busiest_day' => $this->getBusiestDay($logs),
        ];
    }

    protected function getDailyBreakdown(string $startDate, string $endDate): array
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $breakdown = [];
        
        for ($date = $start; $date->lte($end); $date->addDay()) {
            $dayLogs = $this->searchService->searchByDateRange($date->toDateString());
            
            $breakdown[$date->toDateString()] = [
                'date' => $date->toDateString(),
                'requests' => $dayLogs->count(),
                'errors' => $dayLogs->whereNotNull('error_message')->count(),
                'avg_response_time' => round($dayLogs->avg('response_time'), 2),
            ];
        }
        
        return $breakdown;
    }

    protected function getWeeklyTrends(string $startDate, string $endDate): array
    {
        $logs = $this->searchService->searchByDateRange($startDate, $endDate);
        
        return [
            'request_trend' => $this->calculateTrend($logs, 'count'),
            'error_trend' => $this->calculateTrend($logs->whereNotNull('error_message'), 'count'),
            'performance_trend' => $this->calculateTrend($logs, 'response_time'),
        ];
    }

    protected function calculateSuccessRate($logs): float
    {
        if ($logs->count() === 0) {
            return 0;
        }
        
        $successful = $logs->where('response_code', '>=', 200)->where('response_code', '<', 300)->count();
        return round(($successful / $logs->count()) * 100, 2);
    }

    protected function getPeakHour($logs): int
    {
        return $logs->groupBy(function ($log) {
            return $log->requested_at->hour;
        })->map->count()->sortDesc()->keys()->first() ?? 0;
    }

    protected function getBusiestDay($logs): string
    {
        return $logs->groupBy(function ($log) {
            return $log->requested_at->toDateString();
        })->map->count()->sortDesc()->keys()->first() ?? '';
    }

    protected function calculateTrend($logs, string $metric): string
    {
        // Simple trend calculation - would need more sophisticated implementation
        return 'stable';
    }

    protected function buildFilters(Request $request): array
    {
        $filters = [];
        
        if ($request->filled('start_date')) {
            $filters['start_date'] = $request->get('start_date');
        }
        
        if ($request->filled('end_date')) {
            $filters['end_date'] = $request->get('end_date');
        }
        
        if ($request->filled('user_id')) {
            $filters['user_id'] = $request->get('user_id');
        }
        
        if ($request->filled('method')) {
            $filters['method'] = $request->get('method');
        }
        
        if ($request->filled('response_code')) {
            $filters['response_code'] = $request->get('response_code');
        }
        
        return $filters;
    }

    protected function getCustomSummary(array $filters): array
    {
        $stats = ActivityLogger::getStatistics($filters);
        
        return [
            'total_requests' => $stats['total_requests'],
            'unique_users' => $stats['unique_users'],
            'unique_ips' => $stats['unique_ips'],
            'success_rate' => $stats['success_rate'],
            'avg_response_time' => round($stats['average_response_time'], 2),
            'avg_memory_usage' => round($stats['average_memory'] / (1024 * 1024), 2),
        ];
    }

    protected function getDetailedAnalysis(array $filters): array
    {
        $stats = ActivityLogger::getStatistics($filters);
        
        return [
            'method_distribution' => $stats['methods'],
            'response_code_distribution' => $stats['response_codes'],
            'browser_distribution' => $stats['browsers'],
            'device_distribution' => $stats['devices'],
            'top_urls' => array_slice($stats['top_urls'], 0, 20, true),
        ];
    }

    protected function getRecommendations(array $filters): array
    {
        // This would contain intelligent recommendations based on the data
        return [
            'performance' => [],
            'security' => [],
            'optimization' => [],
        ];
    }

    protected function exportToCsv($data): JsonResponse
    {
        // Implementation for CSV export
        return response()->json(['message' => 'CSV export not yet implemented']);
    }
}