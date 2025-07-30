<!-- Data Export Component -->
<div x-data="dataExport()" x-init="init()" class="bg-white shadow rounded-lg">
    <!-- Export Header -->
    <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <h3 class="text-lg font-medium text-gray-900">Data Export</h3>
            <span x-show="activeExports.length > 0" 
                  x-text="activeExports.length + ' active'"
                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
            </span>
        </div>
        <div class="flex items-center space-x-2">
            <button x-on:click="toggleExportPanel()" 
                    class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span x-text="showExportPanel ? 'Hide Export' : 'Export Data'"></span>
            </button>
            <button x-on:click="showExportHistory = !showExportHistory" 
                    class="text-sm text-gray-600 hover:text-gray-500 font-medium">
                <span x-text="showExportHistory ? 'Hide History' : 'View History'"></span>
            </button>
        </div>
    </div>

    <!-- Quick Export Actions -->
    <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
        <div class="flex flex-wrap gap-2">
            <template x-for="quickExport in quickExports" :key="quickExport.key">
                <button x-on:click="startQuickExport(quickExport)"
                        :disabled="isExporting"
                        class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span x-text="quickExport.label"></span>
                    <span class="ml-1 text-xs text-gray-500" x-text="'(' + quickExport.format + ')'"></span>
                </button>
            </template>
        </div>
    </div>

    <!-- Export Configuration Panel -->
    <div x-show="showExportPanel" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 max-h-0"
         x-transition:enter-end="opacity-100 max-h-screen"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 max-h-screen"
         x-transition:leave-end="opacity-0 max-h-0"
         class="overflow-hidden">
        
        <div class="p-4 space-y-6">
            <!-- Export Type Selection -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">Export Type</label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <template x-for="exportType in exportTypes" :key="exportType.key">
                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50"
                               :class="exportConfig.type === exportType.key ? 'border-blue-500 bg-blue-50' : 'border-gray-200'">
                            <input type="radio" 
                                   :value="exportType.key" 
                                   x-model="exportConfig.type"
                                   class="sr-only">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 rounded-md flex items-center justify-center"
                                     :class="exportType.iconClass">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-html="exportType.icon">
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-gray-900" x-text="exportType.name"></div>
                                    <div class="text-xs text-gray-500" x-text="exportType.description"></div>
                                </div>
                            </div>
                        </label>
                    </template>
                </div>
            </div>

            <!-- Format and Options -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Export Format -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Format</label>
                    <select x-model="exportConfig.format" 
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="csv">CSV</option>
                        <option value="excel">Excel (XLSX)</option>
                        <option value="json">JSON</option>
                        <option value="pdf">PDF Report</option>
                    </select>
                </div>

                <!-- Date Range -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date Range</label>
                    <select x-model="exportConfig.dateRange" x-on:change="handleDateRangeChange()"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="today">Today</option>
                        <option value="yesterday">Yesterday</option>
                        <option value="week">Last 7 Days</option>
                        <option value="month">Last 30 Days</option>
                        <option value="quarter">Last 3 Months</option>
                        <option value="custom">Custom Range</option>
                    </select>
                </div>

                <!-- Compression -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Compression</label>
                    <select x-model="exportConfig.compression" 
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="none">None</option>
                        <option value="zip">ZIP Archive</option>
                        <option value="gzip">GZIP</option>
                    </select>
                </div>

                <!-- Max Records -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Max Records</label>
                    <select x-model="exportConfig.maxRecords" 
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="1000">1,000</option>
                        <option value="5000">5,000</option>
                        <option value="10000">10,000</option>
                        <option value="50000">50,000</option>
                        <option value="unlimited">Unlimited</option>
                    </select>
                </div>
            </div>

            <!-- Custom Date Range -->
            <div x-show="exportConfig.dateRange === 'custom'" 
                 x-transition
                 class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                    <input type="datetime-local" x-model="exportConfig.startDate"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                    <input type="datetime-local" x-model="exportConfig.endDate"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <!-- Data Fields Selection -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">Include Fields</label>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <template x-for="field in availableFields" :key="field.key">
                        <label class="flex items-center">
                            <input type="checkbox" 
                                   :value="field.key"
                                   x-model="exportConfig.fields"
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-900" x-text="field.name"></span>
                        </label>
                    </template>
                </div>
            </div>

            <!-- Advanced Options -->
            <div class="border-t border-gray-200 pt-4">
                <h4 class="text-sm font-medium text-gray-700 mb-3">Advanced Options</h4>
                <div class="space-y-3">
                    <label class="flex items-center">
                        <input type="checkbox" x-model="exportConfig.includeMetadata"
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-900">Include metadata and system information</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" x-model="exportConfig.anonymizeData"
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-900">Anonymize sensitive data (IPs, user IDs)</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" x-model="exportConfig.splitByDate"
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-900">Split large exports by date</span>
                    </label>
                </div>
            </div>

            <!-- Email Delivery -->
            <div class="border-t border-gray-200 pt-4">
                <div class="flex items-center space-x-2 mb-3">
                    <input type="checkbox" x-model="exportConfig.emailDelivery" id="emailDelivery"
                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <label for="emailDelivery" class="text-sm font-medium text-gray-700">Email delivery</label>
                </div>
                <div x-show="exportConfig.emailDelivery" x-transition class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input type="email" x-model="exportConfig.emailAddress" 
                               placeholder="admin@example.com"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                        <input type="text" x-model="exportConfig.emailSubject" 
                               placeholder="Activity Logger Export"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            </div>

            <!-- Export Actions -->
            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                <div class="flex items-center space-x-4">
                    <button x-on:click="startExport()" 
                            :disabled="isExporting || !canExport()"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg x-show="!isExporting" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <svg x-show="isExporting" class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="isExporting ? 'Exporting...' : 'Start Export'"></span>
                    </button>
                    <button x-on:click="resetExportConfig()" 
                            class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Reset
                    </button>
                </div>
                <div class="text-sm text-gray-500">
                    <span x-show="estimatedSize" x-text="'Estimated size: ' + estimatedSize"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Exports -->
    <div x-show="activeExports.length > 0" class="border-t border-gray-200">
        <div class="px-4 py-3">
            <h4 class="text-sm font-medium text-gray-700 mb-3">Active Exports</h4>
            <div class="space-y-2">
                <template x-for="exportJob in activeExports" :key="exportJob.id">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2">
                                <span class="text-sm font-medium text-gray-900" x-text="exportJob.name"></span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                                      :class="{
                                          'bg-blue-100 text-blue-800': exportJob.status === 'processing',
                                          'bg-green-100 text-green-800': exportJob.status === 'completed',
                                          'bg-red-100 text-red-800': exportJob.status === 'failed',
                                          'bg-yellow-100 text-yellow-800': exportJob.status === 'queued'
                                      }"
                                      x-text="exportJob.status.charAt(0).toUpperCase() + exportJob.status.slice(1)">
                                </span>
                            </div>
                            <div class="mt-1 text-xs text-gray-500">
                                <span x-text="exportJob.format.toUpperCase()"></span> • 
                                <span x-text="exportJob.records + ' records'"></span> • 
                                <span x-text="formatTime(exportJob.startTime)"></span>
                            </div>
                            <!-- Progress Bar -->
                            <div x-show="exportJob.status === 'processing'" class="mt-2">
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
                                         :style="'width: ' + exportJob.progress + '%'"></div>
                                </div>
                                <div class="mt-1 flex justify-between text-xs text-gray-500">
                                    <span x-text="exportJob.progress + '% complete'"></span>
                                    <span x-show="exportJob.eta" x-text="'ETA: ' + exportJob.eta"></span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2 ml-4">
                            <button x-show="exportJob.status === 'completed'" 
                                    x-on:click="downloadExport(exportJob.id)"
                                    class="text-sm text-blue-600 hover:text-blue-500 font-medium">
                                Download
                            </button>
                            <button x-show="exportJob.status === 'processing'" 
                                    x-on:click="cancelExport(exportJob.id)"
                                    class="text-sm text-red-600 hover:text-red-500 font-medium">
                                Cancel
                            </button>
                            <button x-on:click="removeExport(exportJob.id)"
                                    class="text-sm text-gray-400 hover:text-gray-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Export History -->
    <div x-show="showExportHistory" 
         x-transition
         class="border-t border-gray-200">
        <div class="px-4 py-3">
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-sm font-medium text-gray-700">Export History</h4>
                <button x-on:click="clearExportHistory()" 
                        class="text-sm text-red-600 hover:text-red-500 font-medium">
                    Clear History
                </button>
            </div>
            <div class="max-h-64 overflow-y-auto space-y-2">
                <template x-for="historyItem in exportHistory" :key="historyItem.id">
                    <div class="flex items-center justify-between p-2 hover:bg-gray-50 rounded">
                        <div class="flex-1">
                            <div class="text-sm text-gray-900" x-text="historyItem.name"></div>
                            <div class="text-xs text-gray-500">
                                <span x-text="historyItem.format.toUpperCase()"></span> • 
                                <span x-text="historyItem.size"></span> • 
                                <span x-text="formatTime(historyItem.completedAt)"></span>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button x-show="historyItem.downloadUrl" 
                                    x-on:click="window.open(historyItem.downloadUrl)"
                                    class="text-xs text-blue-600 hover:text-blue-500">
                                Download
                            </button>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                                  :class="{
                                      'bg-green-100 text-green-800': historyItem.status === 'completed',
                                      'bg-red-100 text-red-800': historyItem.status === 'failed'
                                  }"
                                  x-text="historyItem.status">
                            </span>
                        </div>
                    </div>
                </template>
                <div x-show="exportHistory.length === 0" class="text-center py-4 text-gray-500">
                    <p class="text-sm">No export history</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function dataExport() {
    return {
        showExportPanel: false,
        showExportHistory: false,
        isExporting: false,
        estimatedSize: '',
        
        exportConfig: {
            type: 'activity_logs',
            format: 'csv',
            dateRange: 'week',
            startDate: '',
            endDate: '',
            maxRecords: '10000',
            compression: 'none',
            fields: ['timestamp', 'method', 'url', 'response_code', 'response_time'],
            includeMetadata: false,
            anonymizeData: false,
            splitByDate: false,
            emailDelivery: false,
            emailAddress: '',
            emailSubject: 'Activity Logger Export'
        },
        
        quickExports: [
            { key: 'today_csv', label: 'Today\'s Activity', format: 'CSV', type: 'activity_logs', dateRange: 'today' },
            { key: 'errors_json', label: 'Error Logs', format: 'JSON', type: 'error_logs', dateRange: 'week' },
            { key: 'performance_excel', label: 'Performance Data', format: 'Excel', type: 'performance', dateRange: 'month' },
            { key: 'users_csv', label: 'User Activity', format: 'CSV', type: 'user_activity', dateRange: 'week' }
        ],
        
        exportTypes: [
            {
                key: 'activity_logs',
                name: 'Activity Logs',
                description: 'Request logs and activity data',
                iconClass: 'bg-blue-500',
                icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>'
            },
            {
                key: 'error_logs',
                name: 'Error Logs',
                description: 'Error reports and debugging data',
                iconClass: 'bg-red-500',
                icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
            },
            {
                key: 'performance',
                name: 'Performance Data',
                description: 'Response times and metrics',
                iconClass: 'bg-green-500',
                icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>'
            },
            {
                key: 'user_activity',
                name: 'User Activity',
                description: 'User behavior and sessions',
                iconClass: 'bg-purple-500',
                icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>'
            }
        ],
        
        availableFields: [
            { key: 'timestamp', name: 'Timestamp' },
            { key: 'method', name: 'HTTP Method' },
            { key: 'url', name: 'URL' },
            { key: 'route', name: 'Route Name' },
            { key: 'response_code', name: 'Response Code' },
            { key: 'response_time', name: 'Response Time' },
            { key: 'memory_usage', name: 'Memory Usage' },
            { key: 'user_id', name: 'User ID' },
            { key: 'ip_address', name: 'IP Address' },
            { key: 'user_agent', name: 'User Agent' },
            { key: 'referer', name: 'Referer' },
            { key: 'session_id', name: 'Session ID' }
        ],
        
        activeExports: [],
        exportHistory: [],
        
        init() {
            this.loadExportHistory();
            this.startPolling();
            this.updateEstimatedSize();
        },
        
        toggleExportPanel() {
            this.showExportPanel = !this.showExportPanel;
        },
        
        handleDateRangeChange() {
            if (this.exportConfig.dateRange !== 'custom') {
                this.exportConfig.startDate = '';
                this.exportConfig.endDate = '';
            }
            this.updateEstimatedSize();
        },
        
        canExport() {
            return this.exportConfig.fields.length > 0 && !this.isExporting;
        },
        
        async startQuickExport(quickExport) {
            const config = {
                ...this.exportConfig,
                type: quickExport.type,
                format: quickExport.format.toLowerCase(),
                dateRange: quickExport.dateRange
            };
            
            await this.performExport(config, quickExport.label);
        },
        
        async startExport() {
            const exportName = `${this.exportConfig.type}_${this.exportConfig.dateRange}_${Date.now()}`;
            await this.performExport(this.exportConfig, exportName);
        },
        
        async performExport(config, name) {
            try {
                this.isExporting = true;
                
                if (window.ActivityLogger) {
                    ActivityLogger.showToast('Starting export...', 'info');
                }
                
                const response = await this.fetch('{{ route("activity-logger.api.export") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(config)
                });
                
                const data = await response.json();
                
                if (data.success) {
                    const exportJob = {
                        id: data.export_id || Date.now(),
                        name: name,
                        format: config.format,
                        records: data.estimated_records || 0,
                        status: 'processing',
                        progress: 0,
                        startTime: new Date(),
                        eta: null
                    };
                    
                    this.activeExports.push(exportJob);
                    
                    if (window.ActivityLogger) {
                        ActivityLogger.showToast('Export started successfully', 'success');
                    }
                    
                    if (data.immediate_download) {
                        this.downloadFile(data.download_url, data.filename);
                        this.completeExport(exportJob.id);
                    }
                } else {
                    throw new Error(data.error || 'Export failed');
                }
            } catch (error) {
                if (window.ActivityLogger) {
                    ActivityLogger.showToast('Export failed: ' + error.message, 'error');
                }
            } finally {
                this.isExporting = false;
            }
        },
        
        async fetch(url, options = {}) {
            const defaultOptions = {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    ...options.headers
                }
            };
            
            return fetch(url, { ...defaultOptions, ...options });
        },
        
        startPolling() {
            setInterval(() => {
                this.updateActiveExports();
            }, 2000);
        },
        
        async updateActiveExports() {
            if (this.activeExports.length === 0) return;
            
            try {
                const exportIds = this.activeExports.map(exp => exp.id);
                const response = await this.fetch('{{ route("activity-logger.api.export") }}/status', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ export_ids: exportIds })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    data.exports.forEach(exportStatus => {
                        const exportJob = this.activeExports.find(exp => exp.id === exportStatus.id);
                        if (exportJob) {
                            exportJob.status = exportStatus.status;
                            exportJob.progress = exportStatus.progress || 0;
                            exportJob.eta = exportStatus.eta;
                            
                            if (exportStatus.status === 'completed') {
                                this.completeExport(exportJob.id, exportStatus.download_url, exportStatus.file_size);
                            } else if (exportStatus.status === 'failed') {
                                this.failExport(exportJob.id, exportStatus.error);
                            }
                        }
                    });
                }
            } catch (error) {
                console.error('Failed to update export status:', error);
            }
        },
        
        completeExport(exportId, downloadUrl = null, fileSize = null) {
            const exportJob = this.activeExports.find(exp => exp.id === exportId);
            if (exportJob) {
                exportJob.status = 'completed';
                exportJob.progress = 100;
                exportJob.downloadUrl = downloadUrl;
                exportJob.fileSize = fileSize;
                exportJob.completedAt = new Date();
                
                // Add to history
                this.addToHistory(exportJob);
                
                if (window.ActivityLogger) {
                    ActivityLogger.showToast(`Export "${exportJob.name}" completed`, 'success');
                }
                
                // Auto-download if not email delivery
                if (downloadUrl && !this.exportConfig.emailDelivery) {
                    setTimeout(() => {
                        this.downloadExport(exportId);
                    }, 1000);
                }
            }
        },
        
        failExport(exportId, error) {
            const exportJob = this.activeExports.find(exp => exp.id === exportId);
            if (exportJob) {
                exportJob.status = 'failed';
                exportJob.error = error;
                
                if (window.ActivityLogger) {
                    ActivityLogger.showToast(`Export "${exportJob.name}" failed: ${error}`, 'error');
                }
            }
        },
        
        async cancelExport(exportId) {
            try {
                const response = await this.fetch(`{{ route("activity-logger.api.export") }}/${exportId}/cancel`, {
                    method: 'POST'
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.removeExport(exportId);
                    if (window.ActivityLogger) {
                        ActivityLogger.showToast('Export cancelled', 'info');
                    }
                }
            } catch (error) {
                if (window.ActivityLogger) {
                    ActivityLogger.showToast('Failed to cancel export', 'error');
                }
            }
        },
        
        downloadExport(exportId) {
            const exportJob = this.activeExports.find(exp => exp.id === exportId);
            if (exportJob && exportJob.downloadUrl) {
                this.downloadFile(exportJob.downloadUrl, `${exportJob.name}.${exportJob.format}`);
            }
        },
        
        downloadFile(url, filename) {
            const link = document.createElement('a');
            link.href = url;
            link.download = filename;
            link.style.display = 'none';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        },
        
        removeExport(exportId) {
            const index = this.activeExports.findIndex(exp => exp.id === exportId);
            if (index > -1) {
                this.activeExports.splice(index, 1);
            }
        },
        
        addToHistory(exportJob) {
            const historyItem = {
                id: exportJob.id,
                name: exportJob.name,
                format: exportJob.format,
                size: exportJob.fileSize || 'Unknown',
                status: exportJob.status,
                completedAt: exportJob.completedAt,
                downloadUrl: exportJob.downloadUrl
            };
            
            this.exportHistory.unshift(historyItem);
            
            // Keep only last 50 items
            if (this.exportHistory.length > 50) {
                this.exportHistory = this.exportHistory.slice(0, 50);
            }
            
            this.saveExportHistory();
        },
        
        saveExportHistory() {
            localStorage.setItem('activity-logger-export-history', JSON.stringify(this.exportHistory));
        },
        
        loadExportHistory() {
            const stored = localStorage.getItem('activity-logger-export-history');
            if (stored) {
                this.exportHistory = JSON.parse(stored);
            }
        },
        
        clearExportHistory() {
            if (confirm('Are you sure you want to clear the export history?')) {
                this.exportHistory = [];
                this.saveExportHistory();
                if (window.ActivityLogger) {
                    ActivityLogger.showToast('Export history cleared', 'info');
                }
            }
        },
        
        resetExportConfig() {
            this.exportConfig = {
                type: 'activity_logs',
                format: 'csv',
                dateRange: 'week',
                startDate: '',
                endDate: '',
                maxRecords: '10000',
                compression: 'none',
                fields: ['timestamp', 'method', 'url', 'response_code', 'response_time'],
                includeMetadata: false,
                anonymizeData: false,
                splitByDate: false,
                emailDelivery: false,
                emailAddress: '',
                emailSubject: 'Activity Logger Export'
            };
            this.updateEstimatedSize();
        },
        
        updateEstimatedSize() {
            // Simple estimation based on fields and date range
            const fieldCount = this.exportConfig.fields.length;
            const maxRecords = parseInt(this.exportConfig.maxRecords);
            
            let multiplier = 1;
            switch (this.exportConfig.dateRange) {
                case 'today': multiplier = 0.1; break;
                case 'yesterday': multiplier = 0.1; break;
                case 'week': multiplier = 0.7; break;
                case 'month': multiplier = 3; break;
                case 'quarter': multiplier = 9; break;
            }
            
            const estimatedRecords = Math.min(maxRecords * multiplier, maxRecords);
            const avgBytesPerRecord = fieldCount * 50; // Rough estimate
            const totalBytes = estimatedRecords * avgBytesPerRecord;
            
            this.estimatedSize = this.formatFileSize(totalBytes);
        },
        
        formatFileSize(bytes) {
            if (bytes === 0) return '0 B';
            const k = 1024;
            const sizes = ['B', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
        },
        
        formatTime(timestamp) {
            const now = new Date();
            const time = new Date(timestamp);
            const diff = now - time;
            
            if (diff < 60000) {
                return 'Just now';
            } else if (diff < 3600000) {
                return Math.floor(diff / 60000) + 'm ago';
            } else if (diff < 86400000) {
                return Math.floor(diff / 3600000) + 'h ago';
            } else {
                return time.toLocaleDateString();
            }
        }
    }
}
</script>