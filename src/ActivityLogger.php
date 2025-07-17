<?php

namespace ActivityLogger;

use ActivityLogger\Repositories\ActivityLogRepository;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ActivityLogger
{
    protected $repository;

    public function __construct(ActivityLogRepository $repository)
    {
        $this->repository = $repository;
    }

    public function search(array $filters = [], int $perPage = 50): LengthAwarePaginator
    {
        return $this->repository->search($filters, $perPage);
    }

    public function find(int $id)
    {
        return $this->repository->find($id);
    }

    public function getByDateRange($startDate, $endDate, array $additionalFilters = []): Collection
    {
        return $this->repository->getByDateRange($startDate, $endDate, $additionalFilters);
    }

    public function getRecentErrors(int $minutes = 60, int $limit = 100): Collection
    {
        return $this->repository->getRecentErrors($minutes, $limit);
    }

    public function getSlowRequests(int $thresholdMs = 1000, int $limit = 100): Collection
    {
        return $this->repository->getSlowRequests($thresholdMs, $limit);
    }

    public function getHighMemoryRequests(int $thresholdBytes = 52428800, int $limit = 100): Collection
    {
        return $this->repository->getHighMemoryRequests($thresholdBytes, $limit);
    }

    public function getUserActivity(int $userId, array $filters = []): Collection
    {
        return $this->repository->getUserActivity($userId, $filters);
    }

    public function getStatistics(array $filters = []): array
    {
        return $this->repository->getStatistics($filters);
    }

    public function deleteOldLogs(int $days): int
    {
        return $this->repository->deleteOldLogs($days);
    }

    public function export(array $filters = [], string $format = 'json'): string
    {
        return $this->repository->export($filters, $format);
    }

    public function searchByUrl(string $url, array $additionalFilters = []): Collection
    {
        $filters = array_merge(['url' => $url], $additionalFilters);
        return $this->repository->search($filters)->items();
    }

    public function searchByMethod(string $method, array $additionalFilters = []): Collection
    {
        $filters = array_merge(['method' => $method], $additionalFilters);
        return $this->repository->search($filters)->items();
    }

    public function searchByIp(string $ipAddress, array $additionalFilters = []): Collection
    {
        $filters = array_merge(['ip_address' => $ipAddress], $additionalFilters);
        return $this->repository->search($filters)->items();
    }

    public function searchByResponseCode(int $code, array $additionalFilters = []): Collection
    {
        $filters = array_merge(['response_code' => $code], $additionalFilters);
        return $this->repository->search($filters)->items();
    }

    public function getFailedRequests(array $additionalFilters = []): Collection
    {
        $filters = array_merge(['failed' => true], $additionalFilters);
        return $this->repository->search($filters)->items();
    }

    public function getSuccessfulRequests(array $additionalFilters = []): Collection
    {
        $filters = array_merge(['successful' => true], $additionalFilters);
        return $this->repository->search($filters)->items();
    }

    public function searchByController(string $controller, array $additionalFilters = []): Collection
    {
        $filters = array_merge(['controller_action' => $controller], $additionalFilters);
        return $this->repository->search($filters)->items();
    }

    public function searchByRequestId(string $requestId, array $additionalFilters = []): Collection
    {
        $filters = array_merge(['request_id' => $requestId], $additionalFilters);
        return $this->repository->search($filters)->items();
    }

    public function searchByCountry(string $country, array $additionalFilters = []): Collection
    {
        $filters = array_merge(['country' => $country], $additionalFilters);
        return $this->repository->search($filters)->items();
    }

    public function searchByCity(string $city, array $additionalFilters = []): Collection
    {
        $filters = array_merge(['city' => $city], $additionalFilters);
        return $this->repository->search($filters)->items();
    }

    public function getHighQueryCountRequests(int $threshold = 50, int $limit = 100): Collection
    {
        $filters = ['min_query_count' => $threshold];
        return $this->repository->search($filters, $limit)->items();
    }

    public function getSlowQueryRequests(int $thresholdMs = 1000, int $limit = 100): Collection
    {
        $filters = ['min_query_time' => $thresholdMs];
        return $this->repository->search($filters, $limit)->items();
    }

    public function searchByDateOnly(string $date, array $additionalFilters = []): Collection
    {
        $filters = array_merge([
            'start_date' => $date,
            'end_date' => $date
        ], $additionalFilters);
        return $this->repository->search($filters)->items();
    }
}