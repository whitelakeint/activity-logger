<?php

namespace ActivityLogger\Console\Commands;

use Illuminate\Console\Command;
use ActivityLogger\Facades\ActivityLogger;
use Carbon\Carbon;

class AnalyzeLogsCommand extends Command
{
    protected $signature = 'activity-logger:analyze 
                            {--start= : Start date (Y-m-d)}
                            {--end= : End date (Y-m-d)}
                            {--user= : Filter by user ID}
                            {--detailed : Show detailed statistics}';

    protected $description = 'Analyze activity logs and show statistics';

    public function handle()
    {
        $filters = $this->buildFilters();
        
        $this->info('Analyzing activity logs...');
        
        $stats = ActivityLogger::getStatistics($filters);

        $this->displayBasicStats($stats);

        if ($this->option('detailed')) {
            $this->displayDetailedStats($stats);
        }

        return Command::SUCCESS;
    }

    protected function buildFilters(): array
    {
        $filters = [];

        if ($start = $this->option('start')) {
            $filters['start_date'] = Carbon::parse($start);
        }

        if ($end = $this->option('end')) {
            $filters['end_date'] = Carbon::parse($end);
        }

        if ($userId = $this->option('user')) {
            $filters['user_id'] = $userId;
        }

        return $filters;
    }

    protected function displayBasicStats(array $stats): void
    {
        $this->info("\n=== Activity Log Statistics ===\n");

        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Requests', number_format($stats['total_requests'])],
                ['Unique Users', number_format($stats['unique_users'])],
                ['Unique IPs', number_format($stats['unique_ips'])],
                ['Success Rate', $stats['success_rate'] . '%'],
                ['Avg Response Time', round($stats['average_response_time'], 2) . 'ms'],
                ['Avg Memory', round($stats['average_memory'] / (1024 * 1024), 2) . 'MB'],
                ['Avg Response Size', round($stats['average_response_size'] / 1024, 2) . 'KB'],
                ['Avg Query Count', round($stats['average_query_count'], 2)],
                ['Avg Query Time', round($stats['average_query_time'], 2) . 'ms'],
            ]
        );

        $this->info("\n=== HTTP Methods ===");
        $this->table(
            ['Method', 'Count'],
            collect($stats['methods'])->map(fn($count, $method) => [$method, number_format($count)])->toArray()
        );

        $this->info("\n=== Top Response Codes ===");
        $this->table(
            ['Code', 'Count'],
            collect($stats['response_codes'])->take(10)->map(fn($count, $code) => [$code, number_format($count)])->toArray()
        );
    }

    protected function displayDetailedStats(array $stats): void
    {
        $this->info("\n=== Top URLs ===");
        $this->table(
            ['URL', 'Requests'],
            collect($stats['top_urls'])->take(10)->map(fn($count, $url) => [
                str_limit($url, 80),
                number_format($count)
            ])->toArray()
        );

        $this->info("\n=== Top Users ===");
        $this->table(
            ['User ID', 'Requests'],
            collect($stats['top_users'])->take(10)->map(fn($count, $userId) => [$userId, number_format($count)])->toArray()
        );

        $this->info("\n=== Browsers ===");
        $this->table(
            ['Browser', 'Count'],
            collect($stats['browsers'])->map(fn($count, $browser) => [$browser, number_format($count)])->toArray()
        );

        $this->info("\n=== Platforms ===");
        $this->table(
            ['Platform', 'Count'],
            collect($stats['platforms'])->map(fn($count, $platform) => [$platform, number_format($count)])->toArray()
        );

        $this->info("\n=== Devices ===");
        $this->table(
            ['Device', 'Count'],
            collect($stats['devices'])->map(fn($count, $device) => [$device, number_format($count)])->toArray()
        );

        if (!empty($stats['hourly_distribution'])) {
            $this->info("\n=== Hourly Distribution ===");
            $hourlyData = [];
            $maxCount = max($stats['hourly_distribution']) ?: 1;
            for ($hour = 0; $hour < 24; $hour++) {
                $count = $stats['hourly_distribution'][$hour] ?? 0;
                $hourlyData[] = [
                    sprintf('%02d:00-%02d:59', $hour, $hour),
                    number_format($count),
                    str_repeat('█', (int)($count / $maxCount * 30))
                ];
            }
            $this->table(['Hour', 'Requests', 'Chart'], $hourlyData);
        }
    }
}