@extends('activity-logger::layouts.app')

@section('title', 'Log Details - #' . $log->id)

@section('content')
<div class="container-fluid">
    @if(isset($log))
        <!-- Header with Actions -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2>Log Details - #{{ $log->id }}</h2>
                        <p class="text-muted mb-0">{{ $log->requested_at->format('F j, Y \a\t g:i:s A') }} ({{ $log->requested_at->diffForHumans() }})</p>
                    </div>
                    <div>
                        <a href="{{ route('activity-logger.logs') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Logs
                        </a>
                        <button class="btn btn-primary" onclick="copyReproductionCommand()">
                            <i class="fas fa-copy"></i> Copy cURL Command
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Basic Request Information -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Request Information</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <th width="35%">Method</th>
                                <td><span class="badge badge-{{ $log->method === 'GET' ? 'info' : ($log->method === 'POST' ? 'success' : ($log->method === 'PUT' ? 'warning' : ($log->method === 'DELETE' ? 'danger' : 'secondary'))) }}">{{ $log->method }}</span></td>
                            </tr>
                            <tr>
                                <th>URL</th>
                                <td><code>{{ $log->url }}</code></td>
                            </tr>
                            <tr>
                                <th>Route Name</th>
                                <td>{{ $log->route_name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Controller</th>
                                <td><code>{{ $log->controller_action ?? 'N/A' }}</code></td>
                            </tr>
                            <tr>
                                <th>Request ID</th>
                                <td><code>{{ $log->request_id }}</code></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Response Information</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <th width="35%">Status Code</th>
                                <td><span class="badge badge-{{ $log->response_code >= 200 && $log->response_code < 300 ? 'success' : ($log->response_code >= 400 ? 'danger' : 'warning') }}">{{ $log->response_code }}</span></td>
                            </tr>
                            <tr>
                                <th>Response Time</th>
                                <td>{{ $log->formatted_duration }}</td>
                            </tr>
                            <tr>
                                <th>Memory Usage</th>
                                <td>{{ $log->formatted_memory_usage }}</td>
                            </tr>
                            <tr>
                                <th>Response Size</th>
                                <td>{{ $log->formatted_response_size ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Query Count</th>
                                <td>{{ $log->query_count ?? 0 }} queries</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- User and Client Information -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">User Information</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <th width="35%">User</th>
                                <td>
                                    @if($log->user_id)
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-user text-primary mr-2"></i>
                                            <div>
                                                <strong>
                                                    {{ $log->user->name ?? $log->user_name ?? $log->user->email ?? $log->user_email ?? 'User #' . $log->user_id }}
                                                </strong>
                                                <br><small class="text-muted">ID: {{ $log->user_id }}</small>
                                                @if($log->user->email ?? $log->user_email)
                                                    <br><small class="text-muted">{{ $log->user->email ?? $log->user_email }}</small>
                                                @endif
                                                @if($log->user && $log->user->trashed())
                                                    <br><small class="text-danger">User Deleted</small>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">Guest User</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Session ID</th>
                                <td><code>{{ $log->session_id ?? 'N/A' }}</code></td>
                            </tr>
                            <tr>
                                <th>IP Address</th>
                                <td><code>{{ $log->ip_address }}</code></td>
                            </tr>
                            <tr>
                                <th>Location</th>
                                <td>{{ $log->city && $log->country ? $log->city . ', ' . $log->country : ($log->country ?? 'Unknown') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Client Information</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <th width="35%">Device</th>
                                <td>
                                    <i class="fas fa-{{ $log->device === 'Mobile' ? 'mobile-alt' : ($log->device === 'Tablet' ? 'tablet-alt' : 'desktop') }} mr-2"></i>
                                    {{ $log->device ?? 'Unknown' }}
                                    @if($log->is_mobile)
                                        <span class="badge badge-info badge-sm ml-1">Mobile</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Browser</th>
                                <td>{{ $log->browser ?? 'Unknown' }}</td>
                            </tr>
                            <tr>
                                <th>Platform</th>
                                <td>{{ $log->platform ?? 'Unknown' }}</td>
                            </tr>
                            <tr>
                                <th>AJAX Request</th>
                                <td>
                                    @if($log->is_ajax)
                                        <span class="badge badge-success">Yes</span>
                                    @else
                                        <span class="badge badge-secondary">No</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Referer</th>
                                <td>{{ $log->referer ? Str::limit($log->referer, 50) : 'Direct' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Request Reproduction -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Request Reproduction</h5>
                        <small class="text-muted">Copy and paste to reproduce this request</small>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>cURL Command</h6>
                                <div class="bg-dark text-light p-3 rounded position-relative">
                                    <button class="btn btn-sm btn-outline-light position-absolute" style="top: 10px; right: 10px;" onclick="copyToClipboard('curl-command')">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                    <pre id="curl-command" class="mb-0 text-light" style="white-space: pre-wrap; word-wrap: break-word;">{{ generateCurlCommand($log) }}</pre>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6>HTTP Request</h6>
                                <div class="bg-light p-3 rounded">
                                    <pre class="mb-0">{{ $log->method }} {{ parse_url($log->url, PHP_URL_PATH) }}{{ parse_url($log->url, PHP_URL_QUERY) ? '?' . parse_url($log->url, PHP_URL_QUERY) : '' }} HTTP/1.1
Host: {{ parse_url($log->url, PHP_URL_HOST) }}
@if($log->request_headers)@foreach($log->request_headers as $key => $value){{ ucwords(str_replace(['-', '_'], ' ', $key), ' -') }}: {{ is_array($value) ? implode(', ', $value) : $value }}
@endforeach
@endif</pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Request Parameters -->
        @if($log->request_params && !empty($log->request_params))
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Query Parameters</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm table-striped">
                                    <thead>
                                        <tr>
                                            <th>Parameter</th>
                                            <th>Value</th>
                                            <th>Type</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($log->request_params as $key => $value)
                                            <tr>
                                                <td><code>{{ $key }}</code></td>
                                                <td>
                                                    @if(is_array($value))
                                                        <pre class="mb-0">{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                                                    @else
                                                        <span class="text-break">{{ Str::limit($value, 100) }}</span>
                                                    @endif
                                                </td>
                                                <td><span class="badge badge-info">{{ gettype($value) }}</span></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Request Body -->
        @if($log->request_body && !empty($log->request_body))
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Request Body</h5>
                            <button class="btn btn-sm btn-outline-secondary" onclick="copyToClipboard('request-body')">
                                <i class="fas fa-copy"></i> Copy
                            </button>
                        </div>
                        <div class="card-body">
                            @if(isset($log->request_body['_truncated']) && $log->request_body['_truncated'])
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i> 
                                    Request body was truncated. Original size: {{ number_format($log->request_body['_original_size']) }} bytes
                                </div>
                            @endif
                            <div class="table-responsive">
                                <table class="table table-sm table-striped">
                                    <thead>
                                        <tr>
                                            <th>Field</th>
                                            <th>Value</th>
                                            <th>Type</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($log->request_body as $key => $value)
                                            @if(!in_array($key, ['_truncated', '_original_size', '_message']))
                                                <tr>
                                                    <td><code>{{ $key }}</code></td>
                                                    <td>
                                                        @if(is_array($value))
                                                            <pre class="mb-0">{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                                                        @elseif(is_bool($value))
                                                            <span class="badge badge-{{ $value ? 'success' : 'secondary' }}">{{ $value ? 'true' : 'false' }}</span>
                                                        @elseif(is_null($value))
                                                            <span class="text-muted">null</span>
                                                        @else
                                                            <span class="text-break">{{ Str::limit($value, 100) }}</span>
                                                        @endif
                                                    </td>
                                                    <td><span class="badge badge-info">{{ gettype($value) }}</span></td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                <h6>JSON Format</h6>
                                <pre id="request-body" class="bg-light p-3">{{ json_encode($log->request_body, JSON_PRETTY_PRINT) }}</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Request Headers -->
        @if($log->request_headers && !empty($log->request_headers))
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Request Headers</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm table-striped">
                                    <thead>
                                        <tr>
                                            <th>Header</th>
                                            <th>Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($log->request_headers as $key => $value)
                                            <tr>
                                                <td><code>{{ ucwords(str_replace(['-', '_'], ' ', $key), ' -') }}</code></td>
                                                <td><span class="text-break">{{ is_array($value) ? implode(', ', $value) : $value }}</span></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Response Data -->
        @if($log->response_body && !empty($log->response_body))
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Response Body</h5>
                            <button class="btn btn-sm btn-outline-secondary" onclick="copyToClipboard('response-body')">
                                <i class="fas fa-copy"></i> Copy
                            </button>
                        </div>
                        <div class="card-body">
                            @if(isset($log->response_body['_truncated']) && $log->response_body['_truncated'])
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i> 
                                    Response body was truncated. Original size: {{ number_format($log->response_body['_original_size']) }} bytes
                                </div>
                            @endif
                            <pre id="response-body" class="bg-light p-3">{{ json_encode($log->response_body, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Error Information -->
        @if($log->error_message)
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white">
                            <h5 class="card-title mb-0">Error Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-sm">
                                        <tr>
                                            <th width="30%">Error Type</th>
                                            <td><code>{{ $log->error_type }}</code></td>
                                        </tr>
                                        <tr>
                                            <th>Error Message</th>
                                            <td class="text-danger">{{ $log->error_message }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            @if($log->error_trace)
                                <div class="mt-3">
                                    <h6>Stack Trace</h6>
                                    <pre class="bg-dark text-light p-3 small">{{ $log->error_trace }}</pre>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

    @else
        <div class="row">
            <div class="col-12">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> Log not found.
                </div>
            </div>
        </div>
    @endif
</div>

<script>
function copyToClipboard(elementId) {
    const element = document.getElementById(elementId);
    const text = element.textContent || element.innerText;
    
    navigator.clipboard.writeText(text).then(function() {
        // Show success message
        const btn = event.target.closest('button');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i> Copied!';
        btn.classList.remove('btn-outline-secondary', 'btn-outline-light');
        btn.classList.add('btn-success');
        
        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.classList.remove('btn-success');
            btn.classList.add('btn-outline-secondary');
        }, 2000);
    });
}

function copyReproductionCommand() {
    copyToClipboard('curl-command');
}
</script>
@endsection

@php
function generateCurlCommand($log) {
    $curl = "curl -X {$log->method}";
    
    // Add URL
    $curl .= " \\\n  '{$log->url}'";
    
    // Add headers
    if ($log->request_headers) {
        foreach ($log->request_headers as $key => $value) {
            // Skip some headers that curl adds automatically or are server-specific
            if (in_array(strtolower($key), ['host', 'connection', 'content-length', 'accept-encoding'])) {
                continue;
            }
            $headerValue = is_array($value) ? implode(', ', $value) : $value;
            $curl .= " \\\n  -H '{$key}: {$headerValue}'";
        }
    }
    
    // Add request body for POST/PUT/PATCH requests
    if (in_array($log->method, ['POST', 'PUT', 'PATCH']) && $log->request_body && !empty($log->request_body)) {
        $body = json_encode($log->request_body);
        $curl .= " \\\n  -d '{$body}'";
    }
    
    return $curl;
}
@endphp