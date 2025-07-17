<?php

namespace ActivityLogger\Console\Commands;

use Illuminate\Console\Command;
use ActivityLogger\Facades\ActivityLogger;

class ClearOldLogsCommand extends Command
{
    protected $signature = 'activity-logger:clear 
                            {--days=90 : Number of days to keep logs}
                            {--chunk=1000 : Number of records to delete per batch}';

    protected $description = 'Clear old activity logs from the database';

    public function handle()
    {
        $days = (int) $this->option('days');
        $chunk = (int) $this->option('chunk');

        $this->info("Clearing activity logs older than {$days} days...");

        $deleted = ActivityLogger::deleteOldLogs($days);

        $this->info("Successfully deleted {$deleted} log entries.");

        return Command::SUCCESS;
    }
}