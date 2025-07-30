@extends('activity-logger::layouts.app')

@section('title', 'Log Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Log Details</h3>
                    <div class="card-tools">
                        <a href="{{ route('activity-logger.logs') }}" class="btn btn-sm btn-default">
                            <i class="fas fa-arrow-left"></i> Back to Logs
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(isset($log))
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="30%">ID</th>
                                        <td>{{ $log->id }}</td>
                                    </tr>
                                    <tr>
                                        <th>Date/Time</th>
                                        <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Method</th>
                                        <td><span class="badge badge-{{ $log->method === 'GET' ? 'info' : ($log->method === 'POST' ? 'success' : 'warning') }}">{{ $log->method }}</span></td>
                                    </tr>
                                    <tr>
                                        <th>URL</th>
                                        <td>{{ $log->url }}</td>
                                    </tr>
                                    <tr>
                                        <th>Status Code</th>
                                        <td><span class="badge badge-{{ $log->response_code >= 200 && $log->response_code < 300 ? 'success' : ($log->response_code >= 400 ? 'danger' : 'warning') }}">{{ $log->response_code }}</span></td>
                                    </tr>
                                    <tr>
                                        <th>Response Time</th>
                                        <td>{{ number_format($log->response_time, 2) }} ms</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="30%">IP Address</th>
                                        <td>{{ $log->ip }}</td>
                                    </tr>
                                    <tr>
                                        <th>User Agent</th>
                                        <td>{{ $log->user_agent }}</td>
                                    </tr>
                                    <tr>
                                        <th>Device</th>
                                        <td>{{ $log->device ?? 'Unknown' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Browser</th>
                                        <td>{{ $log->browser ?? 'Unknown' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Country</th>
                                        <td>{{ $log->country ?? 'Unknown' }}</td>
                                    </tr>
                                    <tr>
                                        <th>City</th>
                                        <td>{{ $log->city ?? 'Unknown' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        @if($log->headers)
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h4>Request Headers</h4>
                                    <pre class="bg-light p-3"><code>{{ json_encode(json_decode($log->headers), JSON_PRETTY_PRINT) }}</code></pre>
                                </div>
                            </div>
                        @endif

                        @if($log->data)
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h4>Request Data</h4>
                                    <pre class="bg-light p-3"><code>{{ json_encode(json_decode($log->data), JSON_PRETTY_PRINT) }}</code></pre>
                                </div>
                            </div>
                        @endif

                        @if($log->response)
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h4>Response</h4>
                                    <pre class="bg-light p-3"><code>{{ $log->response }}</code></pre>
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> Log not found.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection