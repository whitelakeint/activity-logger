<?php

namespace ActivityLogger\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ActivityLogResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'session_id' => $this->session_id,
            'ip_address' => $this->ip_address,
            'requested_at' => $this->requested_at?->toISOString(),
            'user_agent' => $this->user_agent,
            'method' => $this->method,
            'url' => $this->url,
            'referer' => $this->referer,
            'route_name' => $this->route_name,
            'controller_action' => $this->controller_action,
            'middleware' => $this->middleware,
            'request_id' => $this->request_id,
            'request_headers' => $this->when($this->shouldShowHeaders(), $this->request_headers),
            'request_params' => $this->request_params,
            'request_body' => $this->when($this->shouldShowBody(), $this->request_body),
            'response_code' => $this->response_code,
            'response_time' => $this->response_time,
            'response_size' => $this->response_size,
            'response_headers' => $this->when($this->shouldShowHeaders(), $this->response_headers),
            'response_body' => $this->when($this->shouldShowBody(), $this->response_body),
            'error_message' => $this->error_message,
            'error_trace' => $this->when($this->shouldShowTrace(), $this->error_trace),
            'error_type' => $this->error_type,
            'memory_usage' => $this->memory_usage,
            'query_count' => $this->query_count,
            'query_time' => $this->query_time,
            'country' => $this->country,
            'city' => $this->city,
            'timezone' => $this->timezone,
            'browser' => $this->browser,
            'platform' => $this->platform,
            'device' => $this->device,
            'is_ajax' => $this->is_ajax,
            'is_mobile' => $this->is_mobile,
            'custom_data' => $this->custom_data,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            
            // Computed fields
            'formatted_duration' => $this->formatted_duration,
            'formatted_memory_usage' => $this->formatted_memory_usage,
            'formatted_response_size' => $this->formatted_response_size,
            'is_successful' => $this->is_successful,
            'is_error' => $this->is_error,
        ];
    }

    protected function shouldShowHeaders(): bool
    {
        return request()->has('include_headers') && request()->get('include_headers') === 'true';
    }

    protected function shouldShowBody(): bool
    {
        return request()->has('include_body') && request()->get('include_body') === 'true';
    }

    protected function shouldShowTrace(): bool
    {
        return request()->has('include_trace') && request()->get('include_trace') === 'true';
    }
}