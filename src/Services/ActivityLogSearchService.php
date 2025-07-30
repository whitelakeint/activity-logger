<?php

namespace ActivityLogger\Services;

use ActivityLogger\Models\ActivityLog;
use ActivityLogger\Repositories\ActivityLogRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class ActivityLogSearchService
{
    protected $repository;
    
    public function __construct(ActivityLogRepository $repository)
    {
        $this->repository = $repository;
    }

    public function quickSearch(string $query, array $options = []): Collection
    {
        $builder = ActivityLog::query();

        // Search in multiple fields
        $builder->where(function ($q) use ($query) {
            $q->where('url', 'LIKE', "%{$query}%")
              ->orWhere('method', 'LIKE', "%{$query}%")
              ->orWhere('ip_address', 'LIKE', "%{$query}%")
              ->orWhere('user_agent', 'LIKE', "%{$query}%")
              ->orWhere('controller_action', 'LIKE', "%{$query}%")
              ->orWhere('route_name', 'LIKE', "%{$query}%")
              ->orWhere('error_message', 'LIKE', "%{$query}%");
        });

        // Apply date filter using request_date if provided
        if (isset($options['date'])) {
            $builder->whereDate('request_date', Carbon::parse($options['date'])->toDateString());
        }

        // Apply user filter
        if (isset($options['user_id'])) {
            $builder->where('user_id', $options['user_id']);
        }

        // Apply response code filter
        if (isset($options['response_code'])) {
            $builder->where('response_code', $options['response_code']);
        }

        $limit = $options['limit'] ?? 100;
        
        return $builder->orderBy('requested_at', 'desc')
                      ->limit($limit)
                      ->get();
    }

    public function searchByDateRange(string $startDate, ?string $endDate = null, array $additionalFilters = []): Collection
    {
        $builder = ActivityLog::query();

        // Use request_date for efficient date filtering
        if ($endDate) {
            $builder->whereBetween('request_date', [
                Carbon::parse($startDate)->toDateString(),
                Carbon::parse($endDate)->toDateString()
            ]);
        } else {
            $builder->whereDate('request_date', Carbon::parse($startDate)->toDateString());
        }

        // Apply additional filters
        $this->applyFilters($builder, $additionalFilters);

        return $builder->orderBy('requested_at', 'desc')->get();
    }

    public function searchErrors(array $filters = []): Collection
    {
        $builder = ActivityLog::query()->where(function ($q) {
            // Include records with error messages OR HTTP error status codes (4xx, 5xx)
            $q->whereNotNull('error_message')
              ->orWhere('response_code', '>=', 400);
        });

        // Use request_date for date filtering if provided
        if (isset($filters['date'])) {
            $builder->whereDate('request_date', Carbon::parse($filters['date'])->toDateString());
        }

        if (isset($filters['start_date'])) {
            $endDate = $filters['end_date'] ?? $filters['start_date'];
            $builder->whereBetween('request_date', [
                Carbon::parse($filters['start_date'])->toDateString(),
                Carbon::parse($endDate)->toDateString()
            ]);
        }

        $this->applyFilters($builder, $filters);

        return $builder->orderBy('requested_at', 'desc')->get();
    }

    public function searchPerformanceIssues(array $filters = []): Collection
    {
        $builder = ActivityLog::query();

        // Performance thresholds
        $slowThreshold = $filters['slow_threshold'] ?? 1000; // ms
        $memoryThreshold = $filters['memory_threshold'] ?? 52428800; // 50MB in bytes
        $queryCountThreshold = $filters['query_count_threshold'] ?? 50;

        $builder->where(function ($q) use ($slowThreshold, $memoryThreshold, $queryCountThreshold) {
            $q->where('response_time', '>=', $slowThreshold)
              ->orWhere('memory_usage', '>=', $memoryThreshold)
              ->orWhere('query_count', '>=', $queryCountThreshold);
        });

        // Use request_date for date filtering if provided
        if (isset($filters['date'])) {
            $builder->whereDate('request_date', Carbon::parse($filters['date'])->toDateString());
        }

        if (isset($filters['start_date'])) {
            $endDate = $filters['end_date'] ?? $filters['start_date'];
            $builder->whereBetween('request_date', [
                Carbon::parse($filters['start_date'])->toDateString(),
                Carbon::parse($endDate)->toDateString()
            ]);
        }

        $this->applyFilters($builder, $filters);

        return $builder->orderBy('response_time', 'desc')->get();
    }

    public function searchByUser(int $userId, array $filters = []): Collection
    {
        $builder = ActivityLog::query()->where('user_id', $userId);

        // Use request_date for date filtering if provided
        if (isset($filters['date'])) {
            $builder->whereDate('request_date', Carbon::parse($filters['date'])->toDateString());
        }

        if (isset($filters['start_date'])) {
            $endDate = $filters['end_date'] ?? $filters['start_date'];
            $builder->whereBetween('request_date', [
                Carbon::parse($filters['start_date'])->toDateString(),
                Carbon::parse($endDate)->toDateString()
            ]);
        }

        $this->applyFilters($builder, $filters);

        return $builder->orderBy('requested_at', 'desc')->get();
    }

    public function searchByLocation(string $country, ?string $city = null, array $filters = []): Collection
    {
        $builder = ActivityLog::query()->where('country', $country);

        if ($city) {
            $builder->where('city', $city);
        }

        // Use request_date for date filtering if provided
        if (isset($filters['date'])) {
            $builder->whereDate('request_date', Carbon::parse($filters['date'])->toDateString());
        }

        if (isset($filters['start_date'])) {
            $endDate = $filters['end_date'] ?? $filters['start_date'];
            $builder->whereBetween('request_date', [
                Carbon::parse($filters['start_date'])->toDateString(),
                Carbon::parse($endDate)->toDateString()
            ]);
        }

        $this->applyFilters($builder, $filters);

        return $builder->orderBy('requested_at', 'desc')->get();
    }

    public function getTodaysActivity(): Collection
    {
        $today = Carbon::today()->toDateString();
        
        return ActivityLog::whereDate('request_date', $today)
                         ->orderBy('requested_at', 'desc')
                         ->get();
    }

    public function getRecentActivity(int $hours = 24): Collection
    {
        $since = Carbon::now()->subHours($hours);
        
        return ActivityLog::where('requested_at', '>=', $since)
                         ->orderBy('requested_at', 'desc')
                         ->get();
    }

    protected function applyFilters(Builder $builder, array $filters): void
    {
        if (isset($filters['method'])) {
            $builder->where('method', $filters['method']);
        }

        if (isset($filters['response_code'])) {
            $builder->where('response_code', $filters['response_code']);
        }

        if (isset($filters['controller_action'])) {
            $builder->where('controller_action', 'LIKE', "%{$filters['controller_action']}%");
        }

        if (isset($filters['route_name'])) {
            $builder->where('route_name', $filters['route_name']);
        }

        if (isset($filters['ip_address'])) {
            $builder->where('ip_address', $filters['ip_address']);
        }

        if (isset($filters['browser'])) {
            $builder->where('browser', $filters['browser']);
        }

        if (isset($filters['platform'])) {
            $builder->where('platform', $filters['platform']);
        }

        if (isset($filters['device'])) {
            $builder->where('device', $filters['device']);
        }

        if (isset($filters['is_mobile'])) {
            $builder->where('is_mobile', $filters['is_mobile']);
        }

        if (isset($filters['is_ajax'])) {
            $builder->where('is_ajax', $filters['is_ajax']);
        }
    }
}