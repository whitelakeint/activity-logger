@extends('activity-logger::layouts.app')

@section('title', 'Activity Logs')
@section('page-title', 'Activity Logs')

@section('content')
<div x-data="logsTable()" x-init="init()">
    <!-- Filters Panel -->
    <div class="bg-white shadow rounded-lg mb-6">
        <form method="GET" action="{{ route('activity-logger.logs') }}">
        <div class="px-4 py-5 sm:p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <!-- Date Range -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                    <input type="date" name="start_date" value="{{ request('start_date', date('Y-m-d')) }}" 
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
                    <a href="{{ route('activity-logger.logs') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Clear
                    </a>
                    <button type="button" id="toggleAdvancedFilters"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                        </svg>
                        Advanced
                    </button>
                </div>
            </div>

            <!-- Advanced Filters Panel -->
            <div id="advancedFiltersPanel" class="hidden border-t border-gray-200 pt-4 mt-4">
                <h4 class="text-sm font-medium text-gray-900 mb-3">Advanced Filters</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- IP Address Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">IP Address</label>
                        <input type="text" name="ip_address" value="{{ request('ip_address') }}" placeholder="e.g., 192.168.1.1"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- User ID Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">User ID</label>
                        <input type="number" name="user_id" value="{{ request('user_id') }}" placeholder="User ID"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Route Name Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Route Name</label>
                        <select name="route_name" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Routes</option>
                            @foreach($filterOptions['routes'] as $route)
                            <option value="{{ $route }}" {{ request('route_name') == $route ? 'selected' : '' }}>
                                {{ $route }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Device Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Device</label>
                        <select name="device" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Devices</option>
                            @foreach($filterOptions['devices'] as $device)
                            <option value="{{ $device }}" {{ request('device') == $device ? 'selected' : '' }}>
                                {{ $device }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Country Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                        <select name="country" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Countries</option>
                            @foreach($filterOptions['countries'] as $country)
                            <option value="{{ $country }}" {{ request('country') == $country ? 'selected' : '' }}>
                                {{ $country }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Browser Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Browser</label>
                        <select name="browser" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Browsers</option>
                            @foreach($filterOptions['browsers'] as $browser)
                            <option value="{{ $browser }}" {{ request('browser') == $browser ? 'selected' : '' }}>
                                {{ $browser }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Response Time Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Min Response Time (ms)</label>
                        <input type="number" name="min_response_time" value="{{ request('min_response_time') }}" placeholder="e.g., 1000"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Error Only Filter -->
                    <div class="flex items-end">
                        <label class="flex items-center">
                            <input type="checkbox" name="errors_only" value="1" {{ request('errors_only') ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-700">Errors Only</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
        </form>
    </div>

    <!-- Results Summary -->
    <div class="bg-white shadow rounded-lg mb-6">
        <div class="px-4 py-3 flex items-center justify-between">
            <div class="text-sm text-gray-700">
                Showing {{ $logs->firstItem() ?: 0 }} to {{ $logs->lastItem() ?: 0 }} of {{ $logs->total() }} results
            </div>
            <div class="flex items-center space-x-2">
                <label class="text-sm text-gray-700">Per page:</label>
                <select name="per_page" onchange="window.location.href = updateUrlParameter(window.location.href, 'per_page', this.value)"
                        class="border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('per_page', 50) == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Timestamp
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Method & URL
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Response Time
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            User
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            IP Address
                        </th>
                        <th class="relative px-6 py-3">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($logs as $log)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $log->requested_at->format('M d, H:i:s') }}
                            <div class="text-xs text-gray-500">
                                {{ $log->requested_at->diffForHumans() }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <div class="flex items-center space-x-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $log->method === 'GET' ? 'bg-blue-100 text-blue-800' : 
                                       ($log->method === 'POST' ? 'bg-green-100 text-green-800' : 
                                        ($log->method === 'PUT' ? 'bg-yellow-100 text-yellow-800' : 
                                         ($log->method === 'DELETE' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800'))) }}">
                                    {{ $log->method }}
                                </span>
                                <span class="truncate max-w-xs" title="{{ $log->url }}">
                                    {{ $log->url }}
                                </span>
                            </div>
                            @if($log->route_name)
                            <div class="text-xs text-gray-500 mt-1">
                                Route: {{ $log->route_name }}
                            </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $log->response_code >= 200 && $log->response_code < 300 ? 'bg-green-100 text-green-800' : 
                                   ($log->response_code >= 300 && $log->response_code < 400 ? 'bg-blue-100 text-blue-800' : 
                                    ($log->response_code >= 400 && $log->response_code < 500 ? 'bg-yellow-100 text-yellow-800' : 
                                     'bg-red-100 text-red-800')) }}">
                                {{ $log->response_code }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div class="flex items-center">
                                {{ number_format($log->response_time, 1) }}ms
                                @if($log->response_time > 1000)
                                <svg class="w-4 h-4 text-red-500 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                @endif
                            </div>
                            @if($log->memory_usage)
                            <div class="text-xs text-gray-500">
                                {{ number_format($log->memory_usage / (1024 * 1024), 1) }}MB
                            </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($log->user_id)
                                User #{{ $log->user_id }}
                            @else
                                <span class="text-gray-400">Guest</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $log->ip_address }}
                            @if($log->country)
                            <div class="text-xs text-gray-500">
                                {{ $log->country }}{{ $log->city ? ', ' . $log->city : '' }}
                            </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('activity-logger.logs.show', $log->id) }}" 
                               class="text-indigo-600 hover:text-indigo-900">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="mt-2 text-sm">No activity logs found</p>
                            <p class="text-xs text-gray-400">Try adjusting your filters or check back later</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($logs->hasPages())
        <div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
            {{ $logs->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleButton = document.getElementById('toggleAdvancedFilters');
    const advancedPanel = document.getElementById('advancedFiltersPanel');
    
    toggleButton.addEventListener('click', function() {
        if (advancedPanel.classList.contains('hidden')) {
            advancedPanel.classList.remove('hidden');
            toggleButton.innerHTML = `
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                </svg>
                Hide Advanced
            `;
        } else {
            advancedPanel.classList.add('hidden');
            toggleButton.innerHTML = `
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                </svg>
                Advanced
            `;
        }
    });
    
    // Show advanced panel if any advanced filters are set
    const hasAdvancedFilters = {{ request()->hasAny(['ip_address', 'user_id', 'route_name', 'device', 'country', 'browser', 'min_response_time', 'errors_only']) ? 'true' : 'false' }};
    if (hasAdvancedFilters) {
        toggleButton.click();
    }
});
</script>
@endpush

@push('scripts')
<script>
function logsTable() {
    return {
        init() {
            // Initialize table functionality
        }
    }
}

function updateUrlParameter(url, param, paramVal) {
    var newAdditionalURL = "";
    var tempArray = url.split("?");
    var baseURL = tempArray[0];
    var additionalURL = tempArray[1];
    var temp = "";
    if (additionalURL) {
        tempArray = additionalURL.split("&");
        for (var i = 0; i < tempArray.length; i++) {
            if (tempArray[i].split('=')[0] != param) {
                newAdditionalURL += temp + tempArray[i];
                temp = "&";
            }
        }
    }
    var rows_txt = temp + "" + param + "=" + paramVal;
    return baseURL + "?" + newAdditionalURL + rows_txt;
}
</script>
@endpush