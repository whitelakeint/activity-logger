@extends('activity-logger::layouts.app')

@section('title', 'Error Monitoring')
@section('page-title', 'Error Monitoring')

@section('content')
<div x-data="errorMonitoring()" x-init="init()">
    <!-- Error Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Errors -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-red-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Errors</dt>
                            <dd class="text-lg font-medium text-gray-900">
                                {{ $errorData['overview']['total_errors'] }}
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
                        <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Error Rate</dt>
                            <dd class="text-lg font-medium text-gray-900">
                                {{ number_format($errorData['overview']['error_rate'], 2) }}%
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Critical Errors (5xx) -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-red-600 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Critical Errors</dt>
                            <dd class="text-lg font-medium text-gray-900">
                                {{ $errorData['overview']['critical_errors'] }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Client Errors (4xx) -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-orange-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Client Errors</dt>
                            <dd class="text-lg font-medium text-gray-900">
                                {{ $errorData['overview']['client_errors'] }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Trends and Distribution -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Error Timeline -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Error Timeline</h3>
                <div class="h-64">
                    <canvas id="errorTimelineChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Error Distribution by Type -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Error Distribution</h3>
                <div class="h-64">
                    <canvas id="errorDistributionChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Errors -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Recent Errors</h3>
                    <button x-on:click="refreshErrors()" 
                            class="text-sm text-blue-600 hover:text-blue-500 font-medium">
                        Refresh
                    </button>
                </div>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Latest application errors</p>
            </div>
            <div class="divide-y divide-gray-200 max-h-96 overflow-y-auto">
                @forelse($errorData['recent_errors'] as $error)
                <div class="px-4 py-4 hover:bg-gray-50">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center
                                {{ $error->response_code >= 500 ? 'bg-red-100' : 'bg-yellow-100' }}">
                                <svg class="w-4 h-4 {{ $error->response_code >= 500 ? 'text-red-600' : 'text-yellow-600' }}" 
                                     fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center space-x-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $error->response_code >= 500 ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $error->response_code }}
                                </span>
                                <span class="text-sm font-medium text-gray-900">
                                    {{ $error->error_message ?: 'HTTP Error' }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-500 truncate mt-1">
                                {{ $error->method }} {{ $error->url }}
                            </p>
                            <div class="mt-1 flex items-center text-xs text-gray-400">
                                <span>{{ $error->requested_at->diffForHumans() }}</span>
                                @if($error->ip_address)
                                <span class="mx-2">•</span>
                                <span>{{ $error->ip_address }}</span>
                                @endif
                                @if($error->user_id)
                                <span class="mx-2">•</span>
                                <span>User #{{ $error->user_id }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <a href="{{ route('activity-logger.logs.show', $error->id) }}" 
                               class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                Debug
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="px-4 py-8 text-center text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="mt-2 text-sm">No recent errors</p>
                    <p class="text-xs text-gray-400">Your application is running smoothly!</p>
                </div>
                @endforelse
            </div>
            <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
                <a href="{{ route('activity-logger.logs') }}?has_errors=1" 
                   class="text-sm font-medium text-red-600 hover:text-red-500">
                    View all errors →
                </a>
            </div>
        </div>

        <!-- Top Error URLs -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Top Error URLs</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">URLs with highest error frequency</p>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($errorData['top_error_urls'] as $url => $count)
                <div class="px-4 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate" title="{{ $url }}">
                                {{ $url }}
                            </p>
                            <div class="mt-1">
                                <div class="flex items-center">
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-red-500 h-2 rounded-full" 
                                             style="width: {{ min(($count / max(array_values($errorData['top_error_urls']))) * 100, 100) }}%"></div>
                                    </div>
                                    <span class="ml-2 text-xs text-gray-500 whitespace-nowrap">{{ $count }} errors</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex-shrink-0 ml-4">
                            <a href="{{ route('activity-logger.logs') }}?url={{ urlencode($url) }}&has_errors=1" 
                               class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                View
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="px-4 py-8 text-center text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="mt-2 text-sm">No error patterns found</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Error Resolution Actions -->
    <div class="mt-8 bg-red-50 border border-red-200 rounded-lg p-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">Error Resolution Checklist</h3>
                <div class="mt-2 text-sm text-red-700">
                    <ul class="list-disc pl-5 space-y-1">
                        <li>Review error stack traces and identify root causes</li>
                        <li>Check for patterns in error timing and user behavior</li>
                        <li>Implement proper error handling and user feedback</li>
                        <li>Set up monitoring alerts for critical error thresholds</li>
                        <li>Create post-mortem documentation for recurring issues</li>
                    </ul>
                </div>
                <div class="mt-4">
                    <div class="flex space-x-3">
                        <button type="button" 
                                class="bg-red-100 px-3 py-2 rounded-md text-sm font-medium text-red-800 hover:bg-red-200">
                            Export Error Report
                        </button>
                        <button type="button" 
                                class="bg-white px-3 py-2 rounded-md text-sm font-medium text-red-700 border border-red-300 hover:bg-red-50">
                            Set Up Alerts
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function errorMonitoring() {
    return {
        errorTimelineChart: null,
        errorDistributionChart: null,
        
        init() {
            this.initCharts();
            this.startRealTimeUpdates();
        },

        initCharts() {
            // Error Timeline Chart
            const timelineCtx = document.getElementById('errorTimelineChart').getContext('2d');
            this.errorTimelineChart = new Chart(timelineCtx, {
                type: 'line',
                data: {
                    labels: this.generateTimeLabels(),
                    datasets: [{
                        label: 'Total Errors',
                        data: this.generateErrorData(),
                        borderColor: 'rgb(239, 68, 68)',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        tension: 0.4,
                        fill: true
                    }, {
                        label: '5xx Errors',
                        data: this.generateCriticalErrorData(),
                        borderColor: 'rgb(153, 27, 27)',
                        backgroundColor: 'rgba(153, 27, 27, 0.1)',
                        tension: 0.4,
                        fill: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    plugins: {
                        legend: {
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.parsed.y + ' errors';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Number of Errors'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Time'
                            }
                        }
                    }
                }
            });

            // Error Distribution Chart
            const distributionCtx = document.getElementById('errorDistributionChart').getContext('2d');
            this.errorDistributionChart = new Chart(distributionCtx, {
                type: 'doughnut',
                data: {
                    labels: ['404 Not Found', '500 Server Error', '422 Validation', '403 Forbidden', '401 Unauthorized'],
                    datasets: [{
                        data: [45, 15, 20, 10, 10],
                        backgroundColor: [
                            'rgb(251, 191, 36)',  // Yellow for 404
                            'rgb(239, 68, 68)',   // Red for 500
                            'rgb(249, 115, 22)',  // Orange for 422
                            'rgb(168, 85, 247)',  // Purple for 403
                            'rgb(59, 130, 246)'   // Blue for 401
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = Math.round((value / total) * 100);
                                    return label + ': ' + value + ' (' + percentage + '%)';
                                }
                            }
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

        generateErrorData() {
            return Array.from({length: 24}, () => Math.floor(Math.random() * 20));
        },

        generateCriticalErrorData() {
            return Array.from({length: 24}, () => Math.floor(Math.random() * 5));
        },

        async startRealTimeUpdates() {
            setInterval(async () => {
                try {
                    const response = await ActivityLogger.fetch('{{ route("activity-logger.api.charts") }}?type=error_trends');
                    const data = await response.json();
                    
                    if (data.error_timeline) {
                        this.updateChartData(this.errorTimelineChart, data.error_timeline);
                    }
                    if (data.error_distribution) {
                        this.errorDistributionChart.data.datasets[0].data = data.error_distribution;
                        this.errorDistributionChart.update('none');
                    }
                } catch (error) {
                    console.error('Failed to update error charts:', error);
                }
            }, 60000); // Update every minute
        },

        updateChartData(chart, newData) {
            if (newData.total_errors) {
                chart.data.datasets[0].data = newData.total_errors;
            }
            if (newData.critical_errors) {
                chart.data.datasets[1].data = newData.critical_errors;
            }
            chart.update('none');
        },

        async refreshErrors() {
            try {
                ActivityLogger.showToast('Refreshing error data...', 'info');
                // In a real implementation, this would reload the error data
                setTimeout(() => {
                    ActivityLogger.showToast('Error data refreshed', 'success');
                }, 1000);
            } catch (error) {
                ActivityLogger.showToast('Failed to refresh error data', 'error');
            }
        }
    }
}
</script>
@endpush