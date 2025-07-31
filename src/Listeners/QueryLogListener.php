<?php

namespace ActivityLogger\Listeners;

use Illuminate\Database\Events\QueryExecuted;

class QueryLogListener
{
    protected $queries = [];

    public function handle(QueryExecuted $event)
    {
        $this->queries[] = [
            'sql' => $event->sql,
            'bindings' => $event->bindings,
            'time' => $event->time,
        ];
    }

    public function getQueries(): array
    {
        return $this->queries;
    }

    public function getQueryCount(): int
    {
        return count($this->queries);
    }

    public function getTotalQueryTime(): float
    {
        return collect($this->queries)->sum('time');
    }

    public function reset(): void
    {
        $this->queries = [];
    }
}