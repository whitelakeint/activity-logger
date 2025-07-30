@extends('activity-logger::layouts.app')

@section('title', 'Performance Analytics')
@section('page-title', 'Performance Analytics')

@section('content')
<div x-data="performanceAnalytics()" x-init="init()">
    <!-- Performance Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Average Response Time -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Avg Response Time</dt>
                            <dd class="text-lg font-medium text-gray-900">
                                {{ number_format($performanceData['overview']['avg_response_time'], 1) }}ms
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Average Memory Usage -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Avg Memory Usage</dt>
                            <dd class="text-lg font-medium text-gray-900">
                                {{ number_format($performanceData['overview']['avg_memory_usage'], 1) }}MB
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Average Query Count -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Avg DB Queries</dt>
                            <dd class="text-lg font-medium text-gray-900">
                                {{ number_format($performanceData['overview']['avg_query_count'], 1) }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Slow Requests -->
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
                            <dt class="text-sm font-medium text-gray-500 truncate">Slow Requests (>1s)</dt>
                            <dd class="text-lg font-medium text-gray-900">
                                {{ $performanceData['overview']['slow_requests_count'] }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Trends Chart -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Response Time Trends</h3>
                <div class="h-80">
                    <canvas id="responseTimeTrendChart"></canvas>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Memory Usage Trends</h3>
                <div class="h-80">
                    <canvas id="memoryUsageTrendChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Issues Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Slowest Requests -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Slowest Requests</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Requests with response time > 1 second</p>
            </div>
            <div class="divide-y divide-gray-200 max-h-96 overflow-y-auto">
                @forelse($performanceData['slow_requests'] as $request)
                <div class="px-4 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center space-x-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    {{ $request->method }}
                                </span>
                                <span class="text-sm font-medium text-gray-900 truncate">
                                    {{ $request->url }}
                                </span>
                            </div>
                            <div class="mt-1 flex items-center text-sm text-gray-500">
                                <span>{{ $request->requested_at->format('M d, H:i') }}</span>
                                <span class="mx-2">•</span>
                                <span>{{ number_format($request->response_time, 1) }}ms</span>
                                @if($request->memory_usage)
                                <span class="mx-2">•</span>
                                <span>{{ number_format($request->memory_usage / (1024 * 1024), 1) }}MB</span>
                                @endif
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <a href="{{ route('activity-logger.logs.show', $request->id) }}" 
                               class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                Analyze
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="px-4 py-8 text-center text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="mt-2 text-sm">No slow requests found</p>
                    <p class="text-xs text-gray-400">All requests are performing well!</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- High Memory Usage Requests -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">High Memory Usage</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Requests using > 50MB memory</p>
            </div>
            <div class="divide-y divide-gray-200 max-h-96 overflow-y-auto">
                @forelse($performanceData['high_memory_requests'] as $request)
                <div class="px-4 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center space-x-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    {{ $request->method }}
                                </span>
                                <span class="text-sm font-medium text-gray-900 truncate">
                                    {{ $request->url }}
                                </span>
                            </div>
                            <div class="mt-1 flex items-center text-sm text-gray-500">
                                <span>{{ $request->requested_at->format('M d, H:i') }}</span>
                                <span class="mx-2">•</span>
                                <span>{{ number_format($request->memory_usage / (1024 * 1024), 1) }}MB</span>
                                <span class="mx-2">•</span>
                                <span>{{ number_format($request->response_time, 1) }}ms</span>
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <a href="{{ route('activity-logger.logs.show', $request->id) }}" 
                               class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                Analyze
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="px-4 py-8 text-center text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="mt-2 text-sm">No high memory usage requests</p>
                    <p class="text-xs text-gray-400">Memory usage is optimal!</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Performance Recommendations -->
    <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Performance Recommendations</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <ul class="list-disc pl-5 space-y-1">
                        <li>Consider implementing database query optimization for requests with high query counts</li>
                        <li>Enable response caching for frequently accessed endpoints</li>
                        <li>Monitor memory usage patterns and implement garbage collection strategies</li>
                        <li>Set up alerts for requests exceeding performance thresholds</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function performanceAnalytics() {
    return {
        responseTimeTrendChart: null,
        memoryUsageTrendChart: null,
        
        init() {
            this.initCharts();
            this.startRealTimeUpdates();
        },

        initCharts() {
            // Response Time Trend Chart
            const responseTimeCtx = document.getElementById('responseTimeTrendChart').getContext('2d');
            this.responseTimeTrendChart = new Chart(responseTimeCtx, {
                type: 'line',
                data: {
                    labels: this.generateTimeLabels(),
                    datasets: [{
                        label: 'Response Time (ms)',
                        data: this.generateResponseTimeData(),
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4,
                        fill: true
                    }, {
                        label: 'Threshold (1000ms)',
                        data: Array(24).fill(1000),
                        borderColor: 'rgb(239, 68, 68)',
                        borderDash: [5, 5],
                        pointRadius: 0,
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
                                    return context.dataset.label + ': ' + context.parsed.y + 'ms';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Response Time (ms)'
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

            // Memory Usage Trend Chart
            const memoryCtx = document.getElementById('memoryUsageTrendChart').getContext('2d');
            this.memoryUsageTrendChart = new Chart(memoryCtx, {
                type: 'line',
                data: {
                    labels: this.generateTimeLabels(),
                    datasets: [{
                        label: 'Memory Usage (MB)',
                        data: this.generateMemoryData(),
                        borderColor: 'rgb(34, 197, 94)',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        tension: 0.4,
                        fill: true
                    }, {
                        label: 'Threshold (50MB)',
                        data: Array(24).fill(50),
                        borderColor: 'rgb(251, 191, 36)',
                        borderDash: [5, 5],
                        pointRadius: 0,
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
                                    return context.dataset.label + ': ' + context.parsed.y + 'MB';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Memory Usage (MB)'
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

        generateResponseTimeData() {
            return Array.from({length: 24}, () => Math.floor(Math.random() * 2000) + 200);
        },

        generateMemoryData() {
            return Array.from({length: 24}, () => Math.floor(Math.random() * 80) + 10);
        },

        async startRealTimeUpdates() {
            setInterval(async () => {
                try {
                    const response = await ActivityLogger.fetch('{{ route("activity-logger.api.charts") }}?type=performance_trends');
                    const data = await response.json();
                    
                    if (data.response_times && data.memory_usage) {
                        this.updateChartData(this.responseTimeTrendChart, data.response_times);
                        this.updateChartData(this.memoryUsageTrendChart, data.memory_usage);
                    }
                } catch (error) {
                    console.error('Failed to update performance charts:', error);
                }
            }, 60000); // Update every minute
        },

        updateChartData(chart, newData) {
            chart.data.datasets[0].data = newData;
            chart.update('none');
        }
    }
}
</script>
@endpush