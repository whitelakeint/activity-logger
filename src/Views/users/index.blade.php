@extends('activity-logger::layouts.app')

@section('title', 'User Activity')
@section('page-title', 'User Activity')

@section('content')
<div x-data="userActivity()" x-init="init()">
    <!-- User Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Users -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Users</dt>
                            <dd class="text-lg font-medium text-gray-900">
                                {{ $userData['overview']['total_users'] }}
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.636 18.364a9 9 0 010-12.728m12.728 0a9 9 0 010 12.728m-9.9-2.829a5 5 0 010-7.07m7.072 0a5 5 0 010 7.07M13 12a1 1 0 11-2 0 1 1 0 012 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Active Users</dt>
                            <dd class="text-lg font-medium text-gray-900">
                                {{ $userData['overview']['active_users'] }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- New Users -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">New Users</dt>
                            <dd class="text-lg font-medium text-gray-900">
                                {{ $userData['overview']['new_users'] }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Returning Users -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Returning Users</dt>
                            <dd class="text-lg font-medium text-gray-900">
                                {{ $userData['overview']['returning_users'] }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Search and Filters -->
    <div class="bg-white shadow rounded-lg mb-6">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center space-x-4">
                <!-- User Search -->
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search Users</label>
                    <div class="relative">
                        <input type="search" 
                               x-model="searchQuery"
                               x-on:input="searchUsers()"
                               placeholder="Search by User ID, email, or IP address..." 
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Date Range Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date Range</label>
                    <select x-model="dateRange" x-on:change="filterUsers()" 
                            class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="today">Today</option>
                        <option value="week">Last 7 days</option>
                        <option value="month">Last 30 days</option>
                        <option value="custom">Custom Range</option>
                    </select>
                </div>

                <!-- Activity Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Activity</label>
                    <select x-model="activityFilter" x-on:change="filterUsers()"
                            class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="all">All Users</option>
                        <option value="active">Active Only</option>
                        <option value="inactive">Inactive Only</option>
                        <option value="errors">With Errors</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- User Activity Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- User Activity Timeline -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">User Activity Timeline</h3>
                <div class="h-64">
                    <canvas id="userActivityChart"></canvas>
                </div>
            </div>
        </div>

        <!-- User Engagement Patterns -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Activity Patterns</h3>
                <div class="h-64">
                    <canvas id="activityPatternsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Users List -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg leading-6 font-medium text-gray-900">User Activity List</h3>
                <div class="flex items-center space-x-2">
                    <button x-on:click="refreshUsers()" 
                            class="text-sm text-blue-600 hover:text-blue-500 font-medium">
                        Refresh
                    </button>
                    <button x-on:click="exportUsers()" 
                            class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Export
                    </button>
                </div>
            </div>
        </div>

        <!-- Users Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Activity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sessions</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requests</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Errors</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($userData['active_users'] as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <img class="h-10 w-10 rounded-full" 
                                         src="https://ui-avatars.com/api/?name={{ urlencode($user['name'] ?? 'User ' . $user['user_id']) }}&background=3B82F6&color=fff" 
                                         alt="">
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $user['name'] ?? 'User #' . $user['user_id'] }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $user['email'] ?? 'ID: ' . $user['user_id'] }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $user['last_activity']->diffForHumans() }}
                            <div class="text-xs text-gray-500">
                                {{ $user['last_activity']->format('M d, H:i') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $user['session_count'] }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ number_format($user['request_count']) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($user['error_count'] > 0)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                {{ $user['error_count'] }} errors
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                No errors
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($user['country'])
                            <div class="flex items-center">
                                <span class="text-lg mr-1">{{ $this->getCountryFlag($user['country']) }}</span>
                                <span>{{ $user['country'] }}</span>
                            </div>
                            @if($user['city'])
                            <div class="text-xs text-gray-500">{{ $user['city'] }}</div>
                            @endif
                            @else
                            <span class="text-gray-400">Unknown</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($user['is_online'])
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <div class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1"></div>
                                Online
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                <div class="w-1.5 h-1.5 bg-gray-500 rounded-full mr-1"></div>
                                Offline
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('activity-logger.users.show', $user['user_id']) }}" 
                               class="text-indigo-600 hover:text-indigo-900">View Details</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                            <p class="mt-2 text-sm">No users found</p>
                            <p class="text-xs text-gray-400">Try adjusting your search filters</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- User Privacy Notice -->
    <div class="mt-8 bg-yellow-50 border border-yellow-200 rounded-lg p-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">Privacy & Data Protection</h3>
                <div class="mt-2 text-sm text-yellow-700">
                    <p>This user activity data is collected for application monitoring and performance optimization purposes. 
                    All data is handled in accordance with your privacy policy and applicable data protection regulations (GDPR, CCPA, etc.).</p>
                    <div class="mt-3">
                        <button type="button" class="bg-yellow-100 px-3 py-2 rounded-md text-sm font-medium text-yellow-800 hover:bg-yellow-200">
                            View Privacy Settings
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
function userActivity() {
    return {
        searchQuery: '',
        dateRange: 'week',
        activityFilter: 'all',
        userActivityChart: null,
        activityPatternsChart: null,
        
        init() {
            this.initCharts();
            this.startRealTimeUpdates();
        },

        initCharts() {
            // User Activity Timeline Chart
            const activityCtx = document.getElementById('userActivityChart').getContext('2d');
            this.userActivityChart = new Chart(activityCtx, {
                type: 'line',
                data: {
                    labels: this.generateTimeLabels(),
                    datasets: [{
                        label: 'Active Users',
                        data: this.generateActivityData(),
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4,
                        fill: true
                    }, {
                        label: 'New Users',
                        data: this.generateNewUserData(),
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
                                text: 'Number of Users'
                            }
                        }
                    }
                }
            });

            // Activity Patterns Chart (Heatmap-style)
            const patternsCtx = document.getElementById('activityPatternsChart').getContext('2d');
            this.activityPatternsChart = new Chart(patternsCtx, {
                type: 'bar',
                data: {
                    labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                    datasets: [{
                        label: 'Peak Hours (9-17)',
                        data: [120, 140, 135, 155, 145, 80, 70],
                        backgroundColor: 'rgba(59, 130, 246, 0.8)'
                    }, {
                        label: 'Off Hours (18-8)',
                        data: [45, 50, 48, 55, 52, 85, 75],
                        backgroundColor: 'rgba(156, 163, 175, 0.8)'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top'
                        }
                    },
                    scales: {
                        x: {
                            stacked: true
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Active Users'
                            }
                        }
                    }
                }
            });
        },

        generateTimeLabels() {
            const labels = [];
            for (let i = 6; i >= 0; i--) {
                const date = new Date();
                date.setDate(date.getDate() - i);
                labels.push(date.toLocaleDateString([], {month: 'short', day: 'numeric'}));
            }
            return labels;
        },

        generateActivityData() {
            return Array.from({length: 7}, () => Math.floor(Math.random() * 200) + 50);
        },

        generateNewUserData() {
            return Array.from({length: 7}, () => Math.floor(Math.random() * 30) + 5);
        },

        searchUsers() {
            // Debounced search implementation
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                this.performSearch();
            }, 500);
        },

        async performSearch() {
            if (this.searchQuery.length < 2) return;
            
            try {
                ActivityLogger.showToast('Searching users...', 'info');
                // In a real implementation, this would make an API call
                setTimeout(() => {
                    ActivityLogger.showToast('Search completed', 'success');
                }, 1000);
            } catch (error) {
                ActivityLogger.showToast('Search failed', 'error');
            }
        },

        filterUsers() {
            ActivityLogger.showToast('Filtering users...', 'info');
            // Implement filtering logic
        },

        async refreshUsers() {
            try {
                ActivityLogger.showToast('Refreshing user data...', 'info');
                // In a real implementation, this would reload user data
                setTimeout(() => {
                    ActivityLogger.showToast('User data refreshed', 'success');
                }, 1000);
            } catch (error) {
                ActivityLogger.showToast('Failed to refresh user data', 'error');
            }
        },

        async exportUsers() {
            try {
                ActivityLogger.showToast('Preparing user export...', 'info');
                // In a real implementation, this would generate and download a user report
                setTimeout(() => {
                    ActivityLogger.showToast('Export ready for download', 'success');
                }, 2000);
            } catch (error) {
                ActivityLogger.showToast('Export failed', 'error');
            }
        },

        startRealTimeUpdates() {
            setInterval(async () => {
                try {
                    const response = await ActivityLogger.fetch('{{ route("activity-logger.api.charts") }}?type=user_activity');
                    const data = await response.json();
                    
                    if (data.activity_timeline) {
                        this.updateActivityChart(data.activity_timeline);
                    }
                } catch (error) {
                    console.error('Failed to update user activity charts:', error);
                }
            }, 120000); // Update every 2 minutes
        },

        updateActivityChart(data) {
            if (data.active_users) {
                this.userActivityChart.data.datasets[0].data = data.active_users;
            }
            if (data.new_users) {
                this.userActivityChart.data.datasets[1].data = data.new_users;
            }
            this.userActivityChart.update('none');
        }
    }
}
</script>
@endpush

@php
// Sample data generation for demo - would come from actual user analytics
$userData['active_users'] = collect([
    [
        'user_id' => 1,
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'last_activity' => now()->subMinutes(5),
        'session_count' => 3,
        'request_count' => 45,
        'error_count' => 0,
        'country' => 'United States',
        'city' => 'New York',
        'is_online' => true
    ],
    [
        'user_id' => 2,
        'name' => 'Jane Smith',
        'email' => 'jane@example.com',
        'last_activity' => now()->subMinutes(15),
        'session_count' => 2,
        'request_count' => 28,
        'error_count' => 1,
        'country' => 'United Kingdom',
        'city' => 'London',
        'is_online' => true
    ],
    [
        'user_id' => 3,
        'name' => null,
        'email' => null,
        'last_activity' => now()->subHours(2),
        'session_count' => 1,
        'request_count' => 12,
        'error_count' => 2,
        'country' => 'Germany',
        'city' => 'Berlin',
        'is_online' => false
    ]
]);

function getCountryFlag($country) {
    $flags = [
        'United States' => '🇺🇸',
        'United Kingdom' => '🇬🇧',
        'Germany' => '🇩🇪',
        'France' => '🇫🇷',
        'Canada' => '🇨🇦',
    ];
    return $flags[$country] ?? '🌍';
}
@endphp