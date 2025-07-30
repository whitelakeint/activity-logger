@extends('activity-logger::layouts.app')

@section('title', 'Activity Logger Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div x-data="dashboard()" x-init="init()" @filters-applied.window="handleFiltersApplied($event)">
    <!-- Filters Panel -->
    <div class="bg-white shadow rounded-lg mb-6">
        <form method="GET" action="{{ route('activity-logger.dashboard') }}">
        <div class="px-4 py-5 sm:p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <!-- Date Range -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                    <input type="date" name="start_date" value="{{ request('start_date', date('Y-m-d', strtotime('-7 days'))) }}" 
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                    <input type="date" name="end_date" value="{{ request('end_date', date('Y-m-d')) }}" 
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Method Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Method</label>
                    <select name="method" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Methods</option>
                        @foreach($filterOptions['methods'] as $method)
                        <option value="{{ $method }}" {{ request('method') == $method ? 'selected' : '' }}>
                            {{ $method }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Response Code Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Response Code</label>
                    <select name="response_code" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Codes</option>
                        @foreach($filterOptions['response_codes'] as $code)
                        <option value="{{ $code }}" {{ request('response_code') == $code ? 'selected' : '' }}>
                            {{ $code }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- URL Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">URL</label>
                    <input type="text" name="url" value="{{ request('url') }}" placeholder="Filter by URL"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Filter Actions -->
                <div class="flex items-end space-x-2">
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Filter
                    </button>
                    <a href="{{ route('activity-logger.dashboard') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Clear
                    </a>
                </div>
            </div>
        </div>
        </form>
    </div>
    
    <!-- Overview Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Requests -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Requests</dt>
                            <dd class="text-lg font-medium text-gray-900" x-text="stats.total_requests">
                                {{ number_format($overview['total_requests']) }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Users -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Active Users</dt>
                            <dd class="text-lg font-medium text-gray-900" x-text="stats.unique_users">
                                {{ number_format($overview['unique_users']) }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Error Rate -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-red-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.996-.833-2.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Error Rate</dt>
                            <dd class="text-lg font-medium text-gray-900" x-text="stats.error_rate + '%'">
                                {{ number_format($overview['error_rate'], 1) }}%
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Avg Response Time -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Avg Response Time</dt>
                            <dd class="text-lg font-medium text-gray-900" x-text="stats.avg_response_time + 'ms'">
                                {{ number_format($overview['avg_response_time'], 1) }}ms
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Requests Timeline -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Request Timeline</h3>
                <div class="h-64">
                    <canvas id="requestsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Response Codes Distribution -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Response Codes</h3>
                <div class="h-64">
                    <canvas id="responseCodesChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity and Errors -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Activity -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Recent Activity</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Latest requests to your application</p>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($recentActivity as $activity)
                <div class="px-4 py-4 flex items-center justify-between hover:bg-gray-50">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $activity->response_code >= 200 && $activity->response_code < 300 ? 'bg-green-100 text-green-800' : 
                                   ($activity->response_code >= 400 ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ $activity->response_code }}
                            </span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-gray-900 truncate">
                                {{ $activity->method }} {{ $activity->url }}
                            </p>
                            <p class="text-sm text-gray-500">
                                {{ $activity->requested_at->diffForHumans() }} • {{ number_format($activity->response_time, 1) }}ms
                            </p>
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        <a href="{{ route('activity-logger.logs.show', $activity->id) }}" 
                           class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                            View
                        </a>
                    </div>
                </div>
                @empty
                <div class="px-4 py-8 text-center text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="mt-2 text-sm">No recent activity</p>
                </div>
                @endforelse
            </div>
            <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
                <a href="{{ route('activity-logger.logs') }}" 
                   class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                    View all activity logs →
                </a>
            </div>
        </div>

        <!-- Recent Errors -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Recent Errors</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Latest errors in your application</p>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($recentErrors as $error)
                <div class="px-4 py-4 flex items-center justify-between hover:bg-gray-50">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-gray-900 truncate">
                                {{ $error->error_message ?: 'HTTP ' . $error->response_code . ' Error' }}
                            </p>
                            <p class="text-sm text-gray-500">
                                {{ $error->url }} • {{ $error->requested_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        <a href="{{ route('activity-logger.logs.show', $error->id) }}" 
                           class="text-red-600 hover:text-red-900 text-sm font-medium">
                            Debug
                        </a>
                    </div>
                </div>
                @empty
                <div class="px-4 py-8 text-center text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="mt-2 text-sm">No recent errors</p>
                </div>
                @endforelse
            </div>
            <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
                <a href="{{ route('activity-logger.errors') }}" 
                   class="text-sm font-medium text-red-600 hover:text-red-500">
                    View all errors →
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function dashboard() {
    return {
        stats: {
            total_requests: {{ $overview['total_requests'] }},
            unique_users: {{ $overview['unique_users'] }},
            error_rate: {{ $overview['error_rate'] }},
            avg_response_time: {{ $overview['avg_response_time'] }}
        },
        requestsChart: null,
        responseCodesChart: null,
        
        init() {
            this.initCharts();
            this.startRealTimeUpdates();
        },

        initCharts() {
            // Requests Timeline Chart
            const requestsCtx = document.getElementById('requestsChart').getContext('2d');
            this.requestsChart = new Chart(requestsCtx, {
                type: 'line',
                data: {
                    labels: this.generateTimeLabels(),
                    datasets: [{
                        label: 'Requests',
                        data: this.generateSampleData(),
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });

            // Response Codes Chart
            const responseCtx = document.getElementById('responseCodesChart').getContext('2d');
            this.responseCodesChart = new Chart(responseCtx, {
                type: 'doughnut',
                data: {
                    labels: ['2xx Success', '3xx Redirect', '4xx Client Error', '5xx Server Error'],
                    datasets: [{
                        data: [85, 10, 4, 1],
                        backgroundColor: [
                            'rgb(34, 197, 94)',
                            'rgb(59, 130, 246)',
                            'rgb(251, 191, 36)',
                            'rgb(239, 68, 68)'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        },

        generateTimeLabels() {
            const labels = [];
            for (let i = 23; i >= 0; i--) {
                const time = new Date();
                time.setHours(time.getHours() - i);
                labels.push(time.toLocaleTimeString([], {hour: '2-digit', minute: '2-digit'}));
            }
            return labels;
        },

        generateSampleData() {
            return Array.from({length: 24}, () => Math.floor(Math.random() * 100) + 10);
        },

        // Real-time updates disabled for performance
        startRealTimeUpdates() {
            // Disabled - was causing performance issues and accumulating notifications
            // Users can refresh the page to get updated data
        },

        async updateStats() {
            // Disabled - real-time updates removed for better performance
        },
        
        handleFiltersApplied(event) {
            const filters = event.detail.filters;
            
            // Reload the page with filter parameters
            const params = new URLSearchParams(filters);
            window.location.href = window.location.pathname + '?' + params.toString();
        }
    }
}
</script>
@endpush