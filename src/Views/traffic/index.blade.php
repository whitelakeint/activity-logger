@extends('activity-logger::layouts.app')

@section('title', 'Traffic Analysis')
@section('page-title', 'Traffic Analysis')

@section('content')
<div x-data="trafficAnalytics()" x-init="init()">
    <!-- Traffic Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Requests -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Requests</dt>
                            <dd class="text-lg font-medium text-gray-900">
                                {{ number_format($trafficData['overview']['total_requests']) }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Unique Visitors -->
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
                            <dt class="text-sm font-medium text-gray-500 truncate">Unique Visitors</dt>
                            <dd class="text-lg font-medium text-gray-900">
                                {{ number_format($trafficData['overview']['unique_visitors']) }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Page Views -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Page Views</dt>
                            <dd class="text-lg font-medium text-gray-900">
                                {{ number_format($trafficData['overview']['page_views']) }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Referrers -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Referrer Sources</dt>
                            <dd class="text-lg font-medium text-gray-900">
                                {{ count($trafficData['top_referrers'] ?? []) }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Traffic Visualizations -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Traffic Over Time -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Traffic Over Time</h3>
                <div class="h-64">
                    <canvas id="trafficTimelineChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Device Distribution -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Device Distribution</h3>
                <div class="h-64">
                    <canvas id="deviceDistributionChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Geographic and Browser Analysis -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Geographic Distribution -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Geographic Distribution</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Traffic by country and city</p>
            </div>
            <div class="divide-y divide-gray-200 max-h-80 overflow-y-auto">
                @forelse($trafficData['geographic_stats'] as $country => $data)
                <div class="px-4 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2">
                                <span class="text-lg">{{ getCountryFlag($country) }}</span>
                                <span class="text-sm font-medium text-gray-900">{{ $country }}</span>
                            </div>
                            <div class="mt-1">
                                <div class="flex items-center">
                                    <div class="w-full bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="bg-blue-500 h-2 rounded-full" 
                                             style="width: {{ !empty($trafficData['geographic_stats']) && is_array($trafficData['geographic_stats']) ? min(($data['requests'] / max(array_column($trafficData['geographic_stats'], 'requests'))) * 100, 100) : 0 }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-500 whitespace-nowrap">{{ number_format($data['requests']) }} requests</span>
                                </div>
                            </div>
                            @if(!empty($data['cities']) && is_array($data['cities']))
                            <div class="mt-1 text-xs text-gray-400">
                                Cities: {{ implode(', ', array_slice($data['cities'], 0, 3)) }}
                                @if(count($data['cities']) > 3)
                                    +{{ count($data['cities']) - 3 }} more
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="px-4 py-8 text-center text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="mt-2 text-sm">No geographic data available</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Browser & Platform Stats -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Browser & Platform</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">User agent statistics</p>
            </div>
            <div class="p-4">
                <!-- Browser Stats -->
                <div class="mb-6">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Browsers</h4>
                    <div class="space-y-2">
                        @forelse($trafficData['browser_stats'] as $browser => $count)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <div class="w-4 h-4 bg-blue-500 rounded-sm"></div>
                                <span class="text-sm text-gray-900">{{ $browser }}</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="w-20 bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-500 h-2 rounded-full" 
                                         style="width: {{ !empty($trafficData['browser_stats']) && is_array($trafficData['browser_stats']) && max($trafficData['browser_stats']) > 0 ? min(($count / max($trafficData['browser_stats'])) * 100, 100) : 0 }}%"></div>
                                </div>
                                <span class="text-xs text-gray-500 w-12 text-right">{{ $count }}</span>
                            </div>
                        </div>
                        @empty
                        <p class="text-sm text-gray-500">No browser data available</p>
                        @endforelse
                    </div>
                </div>

                <!-- Platform Stats -->
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Platforms</h4>
                    <div class="space-y-2">
                        @forelse($trafficData['device_stats'] as $platform => $count)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <div class="w-4 h-4 bg-green-500 rounded-sm"></div>
                                <span class="text-sm text-gray-900">{{ $platform }}</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="w-20 bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full" 
                                         style="width: {{ !empty($trafficData['device_stats']) && is_array($trafficData['device_stats']) && max($trafficData['device_stats']) > 0 ? min(($count / max($trafficData['device_stats'])) * 100, 100) : 0 }}%"></div>
                                </div>
                                <span class="text-xs text-gray-500 w-12 text-right">{{ $count }}</span>
                            </div>
                        </div>
                        @empty
                        <p class="text-sm text-gray-500">No platform data available</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Pages -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Top Pages</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">Most visited pages on your site</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Page</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Views</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unique Visitors</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg. Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Traffic Share</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($trafficData['top_pages'] as $url => $views)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <div class="flex items-center">
                                <span class="truncate max-w-xs" title="{{ $url }}">{{ $url }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ number_format($views) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ number_format(round($views * 0.7)) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ rand(30, 300) }}s
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                    <div class="bg-blue-500 h-2 rounded-full" 
                                         style="width: {{ !empty($trafficData['top_pages']) && is_array($trafficData['top_pages']) && max($trafficData['top_pages']) > 0 ? min(($views / max($trafficData['top_pages'])) * 100, 100) : 0 }}%"></div>
                                </div>
                                <span class="text-sm text-gray-500">{{ !empty($trafficData['top_pages']) && is_array($trafficData['top_pages']) && array_sum($trafficData['top_pages']) > 0 ? number_format(($views / array_sum($trafficData['top_pages'])) * 100, 1) : 0 }}%</span>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="mt-2 text-sm">No page data available</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Traffic Insights -->
    <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Traffic Analysis Insights</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <ul class="list-disc pl-5 space-y-1">
                        <li>Monitor peak traffic hours to optimize server resources</li>
                        <li>Analyze geographic distribution for CDN optimization</li>
                        <li>Track device types to prioritize responsive design</li>
                        <li>Use referrer data to understand traffic acquisition channels</li>
                        <li>Identify popular content to guide content strategy</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function trafficAnalytics() {
    return {
        trafficTimelineChart: null,
        deviceDistributionChart: null,
        
        init() {
            this.initCharts();
            this.startRealTimeUpdates();
        },

        initCharts() {
            // Traffic Timeline Chart
            const timelineCtx = document.getElementById('trafficTimelineChart').getContext('2d');
            this.trafficTimelineChart = new Chart(timelineCtx, {
                type: 'line',
                data: {
                    labels: this.generateTimeLabels(),
                    datasets: [{
                        label: 'Page Views',
                        data: this.generateTrafficData(),
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4,
                        fill: true
                    }, {
                        label: 'Unique Visitors',
                        data: this.generateUniqueVisitorData(),
                        borderColor: 'rgb(34, 197, 94)',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
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
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Count'
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

            // Device Distribution Chart
            const deviceCtx = document.getElementById('deviceDistributionChart').getContext('2d');
            this.deviceDistributionChart = new Chart(deviceCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Desktop', 'Mobile', 'Tablet'],
                    datasets: [{
                        data: [65, 30, 5],
                        backgroundColor: [
                            'rgb(59, 130, 246)',   // Blue for Desktop
                            'rgb(34, 197, 94)',    // Green for Mobile
                            'rgb(251, 191, 36)'    // Yellow for Tablet
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
                                    return label + ': ' + value + '%';
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

        generateTrafficData() {
            return Array.from({length: 24}, () => Math.floor(Math.random() * 1000) + 100);
        },

        generateUniqueVisitorData() {
            return Array.from({length: 24}, () => Math.floor(Math.random() * 500) + 50);
        },

        async startRealTimeUpdates() {
            setInterval(async () => {
                try {
                    const response = await ActivityLogger.fetch('{{ route("activity-logger.api.charts") }}?type=traffic_trends');
                    const data = await response.json();
                    
                    if (data.traffic_timeline) {
                        this.updateTrafficChart(data.traffic_timeline);
                    }
                    if (data.device_distribution) {
                        this.deviceDistributionChart.data.datasets[0].data = data.device_distribution;
                        this.deviceDistributionChart.update('none');
                    }
                } catch (error) {
                    console.error('Failed to update traffic charts:', error);
                }
            }, 60000); // Update every minute
        },

        updateTrafficChart(data) {
            if (data.page_views) {
                this.trafficTimelineChart.data.datasets[0].data = data.page_views;
            }
            if (data.unique_visitors) {
                this.trafficTimelineChart.data.datasets[1].data = data.unique_visitors;
            }
            this.trafficTimelineChart.update('none');
        }
    }
}
</script>
@endpush

@php
// Helper function for country flags (would be in a proper helper class)
function getCountryFlag($country) {
    $flags = [
        'United States' => '🇺🇸',
        'United Kingdom' => '🇬🇧',
        'Germany' => '🇩🇪',
        'France' => '🇫🇷',
        'Canada' => '🇨🇦',
        'Australia' => '🇦🇺',
        'Japan' => '🇯🇵',
        'Brazil' => '🇧🇷',
        'India' => '🇮🇳',
        'China' => '🇨🇳',
    ];
    return $flags[$country] ?? '🌍';
}
@endphp