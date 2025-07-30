<!-- Advanced Filtering Component -->
<div x-data="advancedFilters()" x-init="init()" class="bg-white shadow rounded-lg">
    <!-- Filter Header -->
    <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <h3 class="text-lg font-medium text-gray-900">Advanced Filters</h3>
            <span x-show="activeFiltersCount > 0" 
                  x-text="activeFiltersCount + ' active'"
                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
            </span>
        </div>
        <div class="flex items-center space-x-2">
            <button x-on:click="toggleFilterPanel()" 
                    class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"></path>
                </svg>
                <span x-text="showFilters ? 'Hide Filters' : 'Show Filters'"></span>
            </button>
            <button x-on:click="clearAllFilters()" 
                    class="text-sm text-red-600 hover:text-red-500 font-medium">
                Clear All
            </button>
        </div>
    </div>

    <!-- Active Filters Bar -->
    <div x-show="activeFiltersCount > 0" class="px-4 py-2 bg-gray-50 border-b border-gray-200">
        <div class="flex flex-wrap gap-2">
            <template x-for="filter in getActiveFilters()" :key="filter.key">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    <span x-text="filter.label + ': ' + filter.displayValue"></span>
                    <button x-on:click="removeFilter(filter.key)" class="ml-1.5 inline-flex items-center justify-center w-4 h-4 text-blue-400 hover:text-blue-600">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </span>
            </template>
        </div>
    </div>

    <!-- Filter Panel -->
    <div x-show="showFilters" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 max-h-0"
         x-transition:enter-end="opacity-100 max-h-screen"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 max-h-screen"
         x-transition:leave-end="opacity-0 max-h-0"
         class="overflow-hidden">
        
        <div class="p-4 space-y-6">
            <!-- Quick Filters -->
            <div>
                <h4 class="text-sm font-medium text-gray-900 mb-3">Quick Filters</h4>
                <div class="flex flex-wrap gap-2">
                    <template x-for="quickFilter in quickFilters" :key="quickFilter.key">
                        <button x-on:click="applyQuickFilter(quickFilter)"
                                :class="isQuickFilterActive(quickFilter) ? 
                                    'bg-blue-600 text-white border-blue-600' : 
                                    'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'"
                                class="inline-flex items-center px-3 py-1 border rounded-md text-sm font-medium focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <span x-text="quickFilter.label"></span>
                            <span x-show="quickFilter.count" 
                                  x-text="'(' + quickFilter.count + ')'"
                                  class="ml-1 opacity-75">
                            </span>
                        </button>
                    </template>
                </div>
            </div>

            <!-- Date Range Filters -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Date Range Presets -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date Range</label>
                    <select x-model="filters.dateRange" x-on:change="handleDateRangeChange()"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Time</option>
                        <option value="today">Today</option>
                        <option value="yesterday">Yesterday</option>
                        <option value="last_7_days">Last 7 Days</option>
                        <option value="last_30_days">Last 30 Days</option>
                        <option value="this_month">This Month</option>
                        <option value="last_month">Last Month</option>
                        <option value="custom">Custom Range</option>
                    </select>
                </div>

                <!-- Custom Date Range -->
                <div x-show="filters.dateRange === 'custom'" class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Custom Date Range</label>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="datetime-local" x-model="filters.startDate"
                               class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <input type="datetime-local" x-model="filters.endDate"
                               class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <!-- Time Range -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Time Range</label>
                    <select x-model="filters.timeRange"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Hours</option>
                        <option value="business_hours">Business Hours (9-17)</option>
                        <option value="after_hours">After Hours (18-8)</option>
                        <option value="peak_hours">Peak Hours (10-16)</option>
                        <option value="night_hours">Night Hours (22-6)</option>
                    </select>
                </div>
            </div>

            <!-- Request Filters -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- HTTP Method -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">HTTP Method</label>
                    <select x-model="filters.method" multiple
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="GET">GET</option>
                        <option value="POST">POST</option>
                        <option value="PUT">PUT</option>
                        <option value="DELETE">DELETE</option>
                        <option value="PATCH">PATCH</option>
                        <option value="OPTIONS">OPTIONS</option>
                    </select>
                </div>

                <!-- Response Code -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Response Code</label>
                    <div class="space-y-2">
                        <div class="flex flex-wrap gap-1">
                            <template x-for="codeGroup in responseCodeGroups" :key="codeGroup.key">
                                <button x-on:click="toggleCodeGroup(codeGroup)"
                                        :class="isCodeGroupActive(codeGroup) ? 
                                            'bg-blue-100 text-blue-800 border-blue-200' : 
                                            'bg-gray-50 text-gray-700 border-gray-200'"
                                        class="px-2 py-1 text-xs border rounded">
                                    <span x-text="codeGroup.label"></span>
                                </button>
                            </template>
                        </div>
                        <input type="text" x-model="filters.specificResponseCode" 
                               placeholder="Specific code (e.g., 404)"
                               class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <!-- URL Pattern -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">URL Pattern</label>
                    <input type="text" x-model="filters.urlPattern" 
                           placeholder="e.g., /api/*, *admin*"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <p class="mt-1 text-xs text-gray-500">Use * for wildcards</p>
                </div>

                <!-- Route Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Route Name</label>
                    <input type="text" x-model="filters.routeName" 
                           placeholder="e.g., users.index"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <!-- Performance Filters -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Response Time -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Response Time</label>
                    <div class="space-y-2">
                        <select x-model="filters.responseTimeRange"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Any Duration</option>
                            <option value="fast">Fast (< 100ms)</option>
                            <option value="normal">Normal (100ms - 1s)</option>
                            <option value="slow">Slow (1s - 5s)</option>
                            <option value="very_slow">Very Slow (> 5s)</option>
                            <option value="custom">Custom Range</option>
                        </select>
                        <div x-show="filters.responseTimeRange === 'custom'" class="grid grid-cols-2 gap-1">
                            <input type="number" x-model="filters.minResponseTime" placeholder="Min (ms)"
                                   class="text-sm border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <input type="number" x-model="filters.maxResponseTime" placeholder="Max (ms)"
                                   class="text-sm border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>

                <!-- Memory Usage -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Memory Usage</label>
                    <select x-model="filters.memoryUsage"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Any Usage</option>
                        <option value="low">Low (< 10MB)</option>
                        <option value="normal">Normal (10MB - 50MB)</option>
                        <option value="high">High (50MB - 100MB)</option>
                        <option value="very_high">Very High (> 100MB)</option>
                    </select>
                </div>

                <!-- Database Queries -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">DB Queries</label>
                    <select x-model="filters.queryCount"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Any Count</option>
                        <option value="few">Few (< 10)</option>
                        <option value="normal">Normal (10 - 50)</option>
                        <option value="many">Many (50 - 100)</option>
                        <option value="excessive">Excessive (> 100)</option>
                    </select>
                </div>

                <!-- Has Errors -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Error Status</label>
                    <select x-model="filters.errorStatus"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Requests</option>
                        <option value="with_errors">With Errors</option>
                        <option value="without_errors">No Errors</option>
                        <option value="critical_errors">Critical Errors</option>
                        <option value="client_errors">Client Errors</option>
                    </select>
                </div>
            </div>

            <!-- User & Geographic Filters -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- User Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">User</label>
                    <div class="space-y-2">
                        <select x-model="filters.userType"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Users</option>
                            <option value="authenticated">Authenticated</option>
                            <option value="guest">Guest</option>
                            <option value="admin">Admin</option>
                        </select>
                        <input type="text" x-model="filters.specificUserId" 
                               placeholder="Specific User ID"
                               class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <!-- IP Address -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">IP Address</label>
                    <input type="text" x-model="filters.ipAddress" 
                           placeholder="e.g., 192.168.1.*, 10.0.0.100"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Country -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                    <select x-model="filters.country"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Countries</option>
                        <template x-for="country in availableCountries" :key="country.code">
                            <option :value="country.code" x-text="country.name"></option>
                        </template>
                    </select>
                </div>

                <!-- Device Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Device Type</label>
                    <select x-model="filters.deviceType"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Devices</option>
                        <option value="desktop">Desktop</option>
                        <option value="mobile">Mobile</option>
                        <option value="tablet">Tablet</option>
                        <option value="bot">Bot/Crawler</option>
                    </select>
                </div>
            </div>

            <!-- Advanced Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Advanced Search</label>
                <div class="space-y-2">
                    <textarea x-model="filters.advancedQuery" 
                              placeholder="Enter advanced search query (supports field:value syntax, boolean operators)"
                              rows="2"
                              class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                    <div class="text-xs text-gray-500">
                        Examples: <code>method:POST AND response_code:>400</code>, <code>url:*api* OR route:*admin*</code>
                    </div>
                </div>
            </div>

            <!-- Filter Actions -->
            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                <div class="flex items-center space-x-4">
                    <button x-on:click="applyFilters()" 
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Apply Filters
                    </button>
                    <button x-on:click="resetFilters()" 
                            class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Reset
                    </button>
                </div>
                <div class="flex items-center space-x-2">
                    <button x-on:click="saveFilterPreset()" 
                            class="text-sm text-blue-600 hover:text-blue-500 font-medium">
                        Save as Preset
                    </button>
                    <div x-data="{ open: false }" class="relative">
                        <button x-on:click="open = !open" 
                                class="text-sm text-gray-600 hover:text-gray-500 font-medium">
                            Load Preset
                        </button>
                        <div x-show="open" x-on:click.away="open = false"
                             class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg border border-gray-200 z-10">
                            <template x-for="preset in filterPresets" :key="preset.id">
                                <button x-on:click="loadFilterPreset(preset); open = false"
                                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <span x-text="preset.name"></span>
                                    <span class="text-xs text-gray-500 block" x-text="preset.description"></span>
                                </button>
                            </template>
                            <div x-show="filterPresets.length === 0" class="px-4 py-2 text-sm text-gray-500">
                                No saved presets
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function advancedFilters() {
    return {
        showFilters: false,
        filters: {
            dateRange: '',
            startDate: '',
            endDate: '',
            timeRange: '',
            method: [],
            responseCode: [],
            specificResponseCode: '',
            urlPattern: '',
            routeName: '',
            responseTimeRange: '',
            minResponseTime: '',
            maxResponseTime: '',
            memoryUsage: '',
            queryCount: '',
            errorStatus: '',
            userType: '',
            specificUserId: '',
            ipAddress: '',
            country: '',
            deviceType: '',
            advancedQuery: ''
        },
        quickFilters: [
            { key: 'errors', label: 'Errors Only', filters: { errorStatus: 'with_errors' }, count: 23 },
            { key: 'slow', label: 'Slow Requests', filters: { responseTimeRange: 'slow' }, count: 45 },
            { key: 'today', label: 'Today', filters: { dateRange: 'today' }, count: 156 },
            { key: 'api', label: 'API Requests', filters: { urlPattern: '/api/*' }, count: 89 },
            { key: 'mobile', label: 'Mobile', filters: { deviceType: 'mobile' }, count: 67 },
            { key: 'critical', label: 'Critical Errors', filters: { errorStatus: 'critical_errors' }, count: 3 }
        ],
        responseCodeGroups: [
            { key: '2xx', label: '2xx', codes: [200, 201, 202, 204] },
            { key: '3xx', label: '3xx', codes: [301, 302, 304] },
            { key: '4xx', label: '4xx', codes: [400, 401, 403, 404, 422] },
            { key: '5xx', label: '5xx', codes: [500, 502, 503, 504] }
        ],
        availableCountries: [
            { code: 'US', name: 'United States' },
            { code: 'GB', name: 'United Kingdom' },
            { code: 'DE', name: 'Germany' },
            { code: 'FR', name: 'France' },
            { code: 'CA', name: 'Canada' },
            { code: 'AU', name: 'Australia' },
            { code: 'JP', name: 'Japan' },
            { code: 'BR', name: 'Brazil' }
        ],
        filterPresets: [],
        activeFiltersCount: 0,
        
        init() {
            this.loadFilterPresets();
            this.updateActiveFiltersCount();
            
            // Watch for filter changes
            this.$watch('filters', () => {
                this.updateActiveFiltersCount();
            }, { deep: true });
        },
        
        toggleFilterPanel() {
            this.showFilters = !this.showFilters;
        },
        
        applyQuickFilter(quickFilter) {
            if (this.isQuickFilterActive(quickFilter)) {
                // Remove quick filter
                Object.keys(quickFilter.filters).forEach(key => {
                    if (Array.isArray(this.filters[key])) {
                        this.filters[key] = [];
                    } else {
                        this.filters[key] = '';
                    }
                });
            } else {
                // Apply quick filter
                Object.assign(this.filters, quickFilter.filters);
            }
            this.applyFilters();
        },
        
        isQuickFilterActive(quickFilter) {
            return Object.keys(quickFilter.filters).every(key => {
                const filterValue = this.filters[key];
                const quickValue = quickFilter.filters[key];
                
                if (Array.isArray(filterValue)) {
                    return Array.isArray(quickValue) ? 
                        quickValue.every(v => filterValue.includes(v)) :
                        filterValue.includes(quickValue);
                }
                return filterValue === quickValue;
            });
        },
        
        toggleCodeGroup(codeGroup) {
            if (this.isCodeGroupActive(codeGroup)) {
                // Remove codes from filter
                this.filters.responseCode = this.filters.responseCode.filter(code => 
                    !codeGroup.codes.includes(parseInt(code))
                );
            } else {
                // Add codes to filter
                const newCodes = codeGroup.codes.filter(code => 
                    !this.filters.responseCode.includes(code.toString())
                );
                this.filters.responseCode = [...this.filters.responseCode, ...newCodes.map(String)];
            }
        },
        
        isCodeGroupActive(codeGroup) {
            return codeGroup.codes.some(code => 
                this.filters.responseCode.includes(code.toString())
            );
        },
        
        handleDateRangeChange() {
            if (this.filters.dateRange !== 'custom') {
                this.filters.startDate = '';
                this.filters.endDate = '';
            }
        },
        
        applyFilters() {
            const filterData = this.buildFilterQuery();
            
            // Emit event for parent components to handle
            this.$dispatch('filters-applied', { filters: filterData });
            
            // Update URL with filter parameters
            this.updateUrl(filterData);
            
            // Show toast notification
            if (window.ActivityLogger) {
                ActivityLogger.showToast('Filters applied', 'success');
            }
        },
        
        buildFilterQuery() {
            const query = {};
            
            // Process each filter type
            Object.keys(this.filters).forEach(key => {
                const value = this.filters[key];
                if (value !== '' && value !== null && (!Array.isArray(value) || value.length > 0)) {
                    query[key] = value;
                }
            });
            
            return query;
        },
        
        updateUrl(filterData) {
            const params = new URLSearchParams(window.location.search);
            
            // Clear existing filter params
            Array.from(params.keys()).forEach(key => {
                if (key.startsWith('filter_')) {
                    params.delete(key);
                }
            });
            
            // Add new filter params
            Object.keys(filterData).forEach(key => {
                const value = filterData[key];
                if (Array.isArray(value)) {
                    value.forEach(v => params.append(`filter_${key}[]`, v));
                } else {
                    params.set(`filter_${key}`, value);
                }
            });
            
            // Update URL without page refresh
            const newUrl = window.location.pathname + '?' + params.toString();
            window.history.pushState({}, '', newUrl);
        },
        
        resetFilters() {
            this.filters = {
                dateRange: '',
                startDate: '',
                endDate: '',
                timeRange: '',
                method: [],
                responseCode: [],
                specificResponseCode: '',
                urlPattern: '',
                routeName: '',
                responseTimeRange: '',
                minResponseTime: '',
                maxResponseTime: '',
                memoryUsage: '',
                queryCount: '',
                errorStatus: '',
                userType: '',
                specificUserId: '',
                ipAddress: '',
                country: '',
                deviceType: '',
                advancedQuery: ''
            };
            this.applyFilters();
        },
        
        clearAllFilters() {
            this.resetFilters();
        },
        
        removeFilter(filterKey) {
            if (Array.isArray(this.filters[filterKey])) {
                this.filters[filterKey] = [];
            } else {
                this.filters[filterKey] = '';
            }
            this.applyFilters();
        },
        
        getActiveFilters() {
            const active = [];
            
            Object.keys(this.filters).forEach(key => {
                const value = this.filters[key];
                if (value !== '' && value !== null && (!Array.isArray(value) || value.length > 0)) {
                    const label = this.getFilterLabel(key);
                    const displayValue = this.getFilterDisplayValue(key, value);
                    active.push({ key, label, displayValue, value });
                }
            });
            
            return active;
        },
        
        getFilterLabel(key) {
            const labels = {
                dateRange: 'Date Range',
                timeRange: 'Time Range',
                method: 'HTTP Method',
                responseCode: 'Response Code',
                specificResponseCode: 'Response Code',
                urlPattern: 'URL Pattern',
                routeName: 'Route Name',
                responseTimeRange: 'Response Time',
                memoryUsage: 'Memory Usage',
                queryCount: 'DB Queries',
                errorStatus: 'Error Status',
                userType: 'User Type',
                specificUserId: 'User ID',
                ipAddress: 'IP Address',
                country: 'Country',
                deviceType: 'Device Type',
                advancedQuery: 'Advanced Query'
            };
            return labels[key] || key;
        },
        
        getFilterDisplayValue(key, value) {
            if (Array.isArray(value)) {
                return value.join(', ');
            }
            
            // Convert coded values to readable text
            const displayMaps = {
                dateRange: {
                    today: 'Today',
                    yesterday: 'Yesterday',
                    last_7_days: 'Last 7 Days',
                    last_30_days: 'Last 30 Days',
                    this_month: 'This Month',
                    last_month: 'Last Month'
                },
                errorStatus: {
                    with_errors: 'With Errors',
                    without_errors: 'No Errors',
                    critical_errors: 'Critical Errors',
                    client_errors: 'Client Errors'
                }
            };
            
            if (displayMaps[key] && displayMaps[key][value]) {
                return displayMaps[key][value];
            }
            
            return value.toString();
        },
        
        updateActiveFiltersCount() {
            this.activeFiltersCount = this.getActiveFilters().length;
        },
        
        saveFilterPreset() {
            const name = prompt('Enter a name for this filter preset:');
            if (name) {
                const preset = {
                    id: Date.now(),
                    name: name,
                    description: this.getPresetDescription(),
                    filters: { ...this.filters }
                };
                
                this.filterPresets.push(preset);
                this.saveFilterPresets();
                
                if (window.ActivityLogger) {
                    ActivityLogger.showToast('Filter preset saved', 'success');
                }
            }
        },
        
        loadFilterPreset(preset) {
            this.filters = { ...preset.filters };
            this.applyFilters();
            
            if (window.ActivityLogger) {
                ActivityLogger.showToast(`Loaded preset: ${preset.name}`, 'success');
            }
        },
        
        getPresetDescription() {
            const active = this.getActiveFilters();
            if (active.length === 0) return 'No filters';
            if (active.length === 1) return active[0].label;
            return `${active.length} filters applied`;
        },
        
        saveFilterPresets() {
            localStorage.setItem('activity-logger-filter-presets', JSON.stringify(this.filterPresets));
        },
        
        loadFilterPresets() {
            const stored = localStorage.getItem('activity-logger-filter-presets');
            if (stored) {
                this.filterPresets = JSON.parse(stored);
            }
        }
    }
}
</script>