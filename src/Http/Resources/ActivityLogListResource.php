<?php

namespace ActivityLogger\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ActivityLogListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'user_name' => $this->user_name,
            'user_email' => $this->user_email,
            'ip_address' => $this->ip_address,
            'requested_at' => $this->requested_at ? $this->requested_at->toISOString() : null,
            'method' => $this->method,
            'url' => $this->url,
            'route_name' => $this->route_name,
            'controller_action' => $this->controller_action,
            'response_code' => $this->response_code,
            'response_time' => $this->response_time,
            'query_count' => $this->query_count,
            'error_type' => $this->error_type,
            'browser' => $this->browser,
            'platform' => $this->platform,
            'device' => $this->device,
            'is_ajax' => $this->is_ajax,
            'is_mobile' => $this->is_mobile,
            
            // Computed fields for list view
            'formatted_duration' => $this->formatted_duration,
            'is_successful' => $this->is_successful,
            'is_error' => $this->is_error,
            
            // Basic timestamps
            'created_at' => $this->created_at ? $this->created_at->toISOString() : null,
        ];
    }
}