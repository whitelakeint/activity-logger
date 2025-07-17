<?php

namespace ActivityLogger\Console\Commands;

use Illuminate\Console\Command;
use ActivityLogger\Facades\ActivityLogger;
use Carbon\Carbon;

class ExportLogsCommand extends Command
{
    protected $signature = 'activity-logger:export 
                            {--format=json : Export format (json, csv, xml)}
                            {--start= : Start date (Y-m-d)}
                            {--end= : End date (Y-m-d)}
                            {--user= : Filter by user ID}
                            {--method= : Filter by HTTP method}
                            {--status= : Filter by response status code}
                            {--controller= : Filter by controller action}
                            {--request-id= : Filter by request ID}
                            {--country= : Filter by country}
                            {--city= : Filter by city}
                            {--output= : Output file path}';

    protected $description = 'Export activity logs to a file';

    public function handle()
    {
        $format = $this->option('format');
        $filters = $this->buildFilters();

        $this->info('Exporting activity logs...');

        $data = ActivityLogger::export($filters, $format);

        $outputPath = $this->option('output') ?: $this->getDefaultOutputPath($format);

        file_put_contents($outputPath, $data);

        $this->info("Logs exported successfully to: {$outputPath}");

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

        if ($method = $this->option('method')) {
            $filters['method'] = strtoupper($method);
        }

        if ($status = $this->option('status')) {
            $filters['response_code'] = $status;
        }

        if ($controller = $this->option('controller')) {
            $filters['controller_action'] = $controller;
        }

        if ($requestId = $this->option('request-id')) {
            $filters['request_id'] = $requestId;
        }

        if ($country = $this->option('country')) {
            $filters['country'] = $country;
        }

        if ($city = $this->option('city')) {
            $filters['city'] = $city;
        }

        return $filters;
    }

    protected function getDefaultOutputPath(string $format): string
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        return storage_path("logs/activity_logs_{$timestamp}.{$format}");
    }
}