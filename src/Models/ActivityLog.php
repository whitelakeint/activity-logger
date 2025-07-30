<?php

namespace ActivityLogger\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'user_name',
        'user_email',
        'session_id',
        'ip_address',
        'request_date',
        'requested_at',
        'user_agent',
        'method',
        'url',
        'referer',
        'route_name',
        'request_headers',
        'request_params',
        'request_body',
        'response_code',
        'response_headers',
        'response_time',
        'response_body',
        'response_size',
        'error_message',
        'error_trace',
        'error_type',
        'controller_action',
        'middleware',
        'request_id',
        'memory_usage',
        'query_count',
        'query_time',
        'country',
        'city',
        'timezone',
        'duration',
        'cpu_usage',
        'browser',
        'platform',
        'device',
        'is_ajax',
        'is_mobile',
        'custom_data',
    ];

    protected $casts = [
        'request_date' => 'date',
        'requested_at' => 'datetime',
        'request_headers' => 'array',
        'request_params' => 'array',
        'request_body' => 'array',
        'response_headers' => 'array',
        'response_body' => 'array',
        'middleware' => 'array',
        'custom_data' => 'array',
        'is_ajax' => 'boolean',
        'is_mobile' => 'boolean',
        'response_time' => 'float',
        'response_size' => 'integer',
        'memory_usage' => 'integer',
        'query_count' => 'integer',
        'query_time' => 'float',
        'duration' => 'float',
        'cpu_usage' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(config('activity-logger.user_model', 'App\Models\User'));
    }

    public function scopeForUser(Builder $query, $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForIp(Builder $query, $ipAddress): Builder
    {
        return $query->where('ip_address', $ipAddress);
    }

    public function scopeForMethod(Builder $query, $method): Builder
    {
        return $query->where('method', strtoupper($method));
    }

    public function scopeForUrl(Builder $query, $url): Builder
    {
        return $query->where('url', 'LIKE', '%' . $url . '%');
    }

    public function scopeForRoute(Builder $query, $routeName): Builder
    {
        return $query->where('route_name', $routeName);
    }

    public function scopeForResponseCode(Builder $query, $code): Builder
    {
        return $query->where('response_code', $code);
    }

    public function scopeWithErrors(Builder $query): Builder
    {
        return $query->whereNotNull('error_message');
    }

    public function scopeSuccessful(Builder $query): Builder
    {
        return $query->whereBetween('response_code', [200, 299]);
    }

    public function scopeFailed(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->where('response_code', '>=', 400)
              ->orWhereNotNull('error_message');
        });
    }

    public function scopeForDateRange(Builder $query, $startDate, $endDate = null): Builder
    {
        if ($endDate) {
            return $query->whereBetween('requested_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ]);
        }

        return $query->whereDate('requested_at', Carbon::parse($startDate));
    }

    public function scopeRecent(Builder $query, $minutes = 60): Builder
    {
        return $query->where('requested_at', '>=', now()->subMinutes($minutes));
    }

    public function scopeSlowRequests(Builder $query, $thresholdMs = 1000): Builder
    {
        return $query->where('response_time', '>=', $thresholdMs);
    }

    public function scopeHighMemoryUsage(Builder $query, $thresholdBytes = 52428800): Builder
    {
        return $query->where('memory_usage', '>=', $thresholdBytes);
    }

    public function getIsSuccessfulAttribute(): bool
    {
        return $this->response_code >= 200 && $this->response_code < 300;
    }

    public function getIsErrorAttribute(): bool
    {
        return $this->response_code >= 400 || !empty($this->error_message);
    }

    public function getDurationInSecondsAttribute(): float
    {
        return $this->response_time / 1000;
    }

    public function getFormattedDurationAttribute(): string
    {
        if ($this->response_time < 1000) {
            return round($this->response_time, 2) . 'ms';
        }
        
        return round($this->response_time / 1000, 2) . 's';
    }

    public function getFormattedMemoryUsageAttribute(): string
    {
        if ($this->memory_usage < 1024 * 1024) {
            return round($this->memory_usage / 1024, 2) . 'KB';
        }
        
        return round($this->memory_usage / (1024 * 1024), 2) . 'MB';
    }

    public function getFormattedResponseSizeAttribute(): string
    {
        if ($this->response_size < 1024) {
            return $this->response_size . 'B';
        } elseif ($this->response_size < 1024 * 1024) {
            return round($this->response_size / 1024, 2) . 'KB';
        }
        
        return round($this->response_size / (1024 * 1024), 2) . 'MB';
    }

    public function scopeForCountry(Builder $query, $country): Builder
    {
        return $query->where('country', $country);
    }

    public function scopeForCity(Builder $query, $city): Builder
    {
        return $query->where('city', $city);
    }

    public function scopeForRequestId(Builder $query, $requestId): Builder
    {
        return $query->where('request_id', $requestId);
    }

    public function scopeHighQueryCount(Builder $query, $threshold = 50): Builder
    {
        return $query->where('query_count', '>=', $threshold);
    }

    public function scopeSlowQueryTime(Builder $query, $thresholdMs = 1000): Builder
    {
        return $query->where('query_time', '>=', $thresholdMs);
    }
}