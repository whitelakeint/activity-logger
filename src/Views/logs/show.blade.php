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
                                <td>
                                    {{ $log->query_count ?? 0 }} queries
                                    @if($log->query_count > 0 && $log->queries && count($log->queries) > 0)
                                        <button class="btn btn-sm btn-outline-primary ml-2" onclick="showQueriesModal()">
                                            <i class="fas fa-database"></i> View Queries
                                        </button>
                                    @endif
                                </td>
                            </tr>
                            @if($log->query_time)
                            <tr>
                                <th>Total Query Time</th>
                                <td>{{ number_format($log->query_time, 2) }}ms</td>
                            </tr>
                            @endif
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

<!-- Queries Modal -->
@if(isset($log) && $log->queries && count($log->queries) > 0)
<div id="queriesModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-0 transition-opacity duration-300" aria-hidden="true" onclick="hideQueriesModal()"></div>

        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all duration-300 opacity-0 translate-y-4 sm:my-8 sm:align-middle sm:max-w-6xl sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                            <svg class="inline w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4-3.582-4-8-4-8 1.79-8 4z"></path>
                            </svg>
                            Database Queries
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 ml-2">
                                {{ count($log->queries) }}
                            </span>
                        </h3>
                        
                        <!-- Stats Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-500">Total Queries</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ count($log->queries) }}</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-500">Total Time</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ number_format($log->query_time, 2) }}ms</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-500">Average Time</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ number_format($log->query_time / count($log->queries), 2) }}ms</p>
                            </div>
                        </div>
                        
                        <!-- Queries Table -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Query</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bindings</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($log->queries as $index => $query)
                                        <tr>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                                            <td class="px-3 py-4 text-sm text-gray-900">
                                                <code class="block bg-gray-100 p-2 rounded text-xs mb-2" style="white-space: pre-wrap; word-break: break-word;">{{ $query['sql'] }}</code>
                                                @if(!empty($query['bindings']))
                                                    <button class="text-blue-600 hover:text-blue-900 text-sm font-medium" onclick="toggleBindings({{ $index }})">
                                                        <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                                        </svg>
                                                        Show Bindings
                                                    </button>
                                                    <div id="bindings-{{ $index }}" class="hidden mt-2">
                                                        <div class="bg-blue-50 border border-blue-200 rounded p-3">
                                                            <strong class="text-sm">Bindings:</strong>
                                                            <pre class="mt-1 text-xs">{{ json_encode($query['bindings'], JSON_PRETTY_PRINT) }}</pre>
                                                        </div>
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $query['time'] > 100 ? 'bg-red-100 text-red-800' : ($query['time'] > 50 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                                    {{ number_format($query['time'], 2) }}ms
                                                </span>
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">
                                                @if(!empty($query['bindings']))
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        {{ count($query['bindings']) }}
                                                    </span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        @php
                            $slowQueries = collect($log->queries)->filter(function($query) {
                                return $query['time'] > 100;
                            });
                        @endphp
                        
                        @if($slowQueries->count() > 0)
                            <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-md p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-yellow-800">Performance Warning</h3>
                                        <p class="mt-1 text-sm text-yellow-700">
                                            {{ $slowQueries->count() }} slow {{ Str::plural('query', $slowQueries->count()) }} detected (>100ms)
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm" onclick="exportQueries()">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Export Queries
                </button>
                <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" onclick="hideQueriesModal()">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
@endif

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

function showQueriesModal() {
    const modal = document.getElementById('queriesModal');
    if (modal) {
        modal.classList.remove('hidden');
        // Add fade-in animation
        setTimeout(() => {
            modal.querySelector('.bg-gray-500').classList.add('opacity-75');
            modal.querySelector('.inline-block').classList.add('opacity-100', 'translate-y-0');
        }, 10);
    }
}

function hideQueriesModal() {
    const modal = document.getElementById('queriesModal');
    if (modal) {
        // Add fade-out animation
        modal.querySelector('.bg-gray-500').classList.remove('opacity-75');
        modal.querySelector('.inline-block').classList.remove('opacity-100', 'translate-y-0');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }
}

function toggleBindings(index) {
    const bindingsDiv = document.getElementById('bindings-' + index);
    if (bindingsDiv) {
        bindingsDiv.classList.toggle('hidden');
    }
}

function exportQueries() {
    @if(isset($log) && $log->queries)
        const queries = @json($log->queries);
        const logId = {{ $log->id }};
        const exportData = {
            log_id: logId,
            url: '{{ $log->url }}',
            timestamp: '{{ $log->requested_at }}',
            total_queries: queries.length,
            total_time: {{ $log->query_time ?? 0 }},
            queries: queries
        };
        
        const dataStr = JSON.stringify(exportData, null, 2);
        const dataUri = 'data:application/json;charset=utf-8,'+ encodeURIComponent(dataStr);
        
        const exportFileDefaultName = `activity_log_${logId}_queries.json`;
        
        const linkElement = document.createElement('a');
        linkElement.setAttribute('href', dataUri);
        linkElement.setAttribute('download', exportFileDefaultName);
        linkElement.click();
    @endif
}

// Close modal on ESC key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        hideQueriesModal();
    }
});
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