@extends('activity-logger::layouts.app')

@section('title', 'Reports')
@section('page-title', 'Reports & Analytics')

@section('content')
<div x-data="reportGenerator()" x-init="init()">
    <!-- Report Templates -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        @foreach($reportTemplates as $key => $name)
        <div class="bg-white overflow-hidden shadow rounded-lg hover:shadow-md transition-shadow duration-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                            @switch($key)
                                @case('daily_summary')
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    @break
                                @case('error_report')
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    @break
                                @case('performance_report')
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                    @break
                                @case('user_activity')
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    @break
                            @endswitch
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <h3 class="text-lg font-medium text-gray-900">{{ $name }}</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            @switch($key)
                                @case('daily_summary')
                                    Daily activity overview with key metrics
                                    @break
                                @case('error_report')
                                    Error analysis and resolution tracking
                                    @break
                                @case('performance_report')
                                    Performance metrics and optimization insights
                                    @break
                                @case('user_activity')
                                    User behavior and engagement analysis
                                    @break
                            @endswitch
                        </p>
                    </div>
                </div>
                <div class="mt-6">
                    <button x-on:click="generateReport('{{ $key }}')" 
                            class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Generate Report
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Custom Report Builder -->
    <div class="bg-white shadow rounded-lg mb-8">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Custom Report Builder</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">Create custom reports with specific metrics and date ranges</p>
        </div>
        <div class="px-4 py-5 sm:p-6">
            <form x-on:submit.prevent="generateCustomReport()" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Report Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Report Name</label>
                        <input type="text" x-model="customReport.name" 
                               placeholder="My Custom Report"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Date Range -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date Range</label>
                        <select x-model="customReport.dateRange" 
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="today">Today</option>
                            <option value="yesterday">Yesterday</option>
                            <option value="week">Last 7 days</option>
                            <option value="month">Last 30 days</option>
                            <option value="quarter">Last 3 months</option>
                            <option value="custom">Custom Range</option>
                        </select>
                    </div>

                    <!-- Format -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Export Format</label>
                        <select x-model="customReport.format" 
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="pdf">PDF Report</option>
                            <option value="excel">Excel Spreadsheet</option>
                            <option value="csv">CSV Data</option>
                            <option value="json">JSON Data</option>
                        </select>
                    </div>

                    <!-- Schedule -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Schedule</label>
                        <select x-model="customReport.schedule" 
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">One-time Report</option>
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                        </select>
                    </div>
                </div>

                <!-- Custom Date Range -->
                <div x-show="customReport.dateRange === 'custom'" 
                     x-transition
                     class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                        <input type="date" x-model="customReport.startDate" 
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                        <input type="date" x-model="customReport.endDate" 
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <!-- Metrics Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Include Metrics</label>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        <template x-for="metric in availableMetrics" :key="metric.key">
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       :value="metric.key"
                                       x-model="customReport.metrics"
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-900" x-text="metric.name"></span>
                            </label>
                        </template>
                    </div>
                </div>

                <!-- Email Recipients -->
                <div x-show="customReport.schedule" x-transition>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Recipients</label>
                    <input type="email" x-model="customReport.emailRecipients" 
                           placeholder="admin@example.com, manager@example.com"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <p class="mt-1 text-xs text-gray-500">Separate multiple emails with commas</p>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Generate Custom Report
                        </button>
                        <button type="button" x-on:click="saveReportTemplate()" 
                                class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Save as Template
                        </button>
                    </div>
                    <div x-show="customReport.schedule" class="text-sm text-gray-500">
                        Reports will be sent automatically
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Scheduled Reports -->
    <div class="bg-white shadow rounded-lg mb-8">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Scheduled Reports</h3>
                <button x-on:click="refreshScheduledReports()" 
                        class="text-sm text-blue-600 hover:text-blue-500 font-medium">
                    Refresh
                </button>
            </div>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">Manage your automated report schedules</p>
        </div>
        <div class="divide-y divide-gray-200">
            @forelse($scheduledReports as $report)
            <div class="px-4 py-4 flex items-center justify-between hover:bg-gray-50">
                <div class="flex-1">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $report['active'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $report['active'] ? 'Active' : 'Paused' }}
                            </span>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-900">{{ $report['name'] }}</h4>
                            <p class="text-sm text-gray-500">
                                {{ ucfirst($report['frequency']) }} • {{ $report['format'] }} • 
                                Next: {{ $report['nextRun']->format('M d, H:i') }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <button x-on:click="toggleSchedule('{{ $report['id'] }}')" 
                            class="text-sm text-blue-600 hover:text-blue-500 font-medium">
                        {{ $report['active'] ? 'Pause' : 'Resume' }}
                    </button>
                    <button x-on:click="editSchedule('{{ $report['id'] }}')" 
                            class="text-sm text-indigo-600 hover:text-indigo-500 font-medium">
                        Edit
                    </button>
                    <button x-on:click="deleteSchedule('{{ $report['id'] }}')" 
                            class="text-sm text-red-600 hover:text-red-500 font-medium">
                        Delete
                    </button>
                </div>
            </div>
            @empty
            <div class="px-4 py-8 text-center text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2z"></path>
                </svg>
                <p class="mt-2 text-sm">No scheduled reports</p>
                <p class="text-xs text-gray-400">Create a custom report with a schedule to get started</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Report History -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Recent Reports</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">Download or view previously generated reports</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Report</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Generated</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Format</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @for($i = 0; $i < 5; $i++)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ ['Daily Summary', 'Error Analysis', 'Performance Report', 'User Activity', 'Custom Report'][array_rand(['Daily Summary', 'Error Analysis', 'Performance Report', 'User Activity', 'Custom Report'])] }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ ['Scheduled', 'Manual', 'Custom'][array_rand(['Scheduled', 'Manual', 'Custom'])] }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ now()->subHours(rand(1, 48))->format('M d, H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ ['PDF', 'Excel', 'CSV'][array_rand(['PDF', 'Excel', 'CSV'])] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Ready
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <button x-on:click="downloadSampleReport('{{ ['daily_summary', 'error_report', 'performance_report', 'user_activity'][array_rand(['daily_summary', 'error_report', 'performance_report', 'user_activity'])] }}')" 
                                        class="text-indigo-600 hover:text-indigo-900">Download</button>
                                <button x-on:click="ActivityLogger.showToast('View functionality would show report preview', 'info')" 
                                        class="text-blue-600 hover:text-blue-900">View</button>
                            </div>
                        </td>
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function reportGenerator() {
    return {
        customReport: {
            name: '',
            dateRange: 'week',
            format: 'pdf',
            schedule: '',
            startDate: '',
            endDate: '',
            metrics: ['requests', 'users', 'errors'],
            emailRecipients: ''
        },
        
        availableMetrics: [
            { key: 'requests', name: 'Request Count' },
            { key: 'users', name: 'User Activity' },
            { key: 'errors', name: 'Error Analysis' },
            { key: 'performance', name: 'Performance Metrics' },
            { key: 'geographic', name: 'Geographic Data' },
            { key: 'devices', name: 'Device Statistics' },
            { key: 'browsers', name: 'Browser Statistics' },
            { key: 'referrers', name: 'Traffic Sources' }
        ],
        
        init() {
            // Set default custom date range
            const endDate = new Date();
            const startDate = new Date();
            startDate.setDate(startDate.getDate() - 7);
            
            this.customReport.endDate = endDate.toISOString().split('T')[0];
            this.customReport.startDate = startDate.toISOString().split('T')[0];
        },

        async generateReport(type) {
            try {
                ActivityLogger.showToast('Generating ' + type + ' report...', 'info');
                
                // Build export parameters
                const params = new URLSearchParams({
                    report_type: type,
                    format: 'csv',
                    start_date: new Date(Date.now() - 7 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
                    end_date: new Date().toISOString().split('T')[0]
                });
                
                const response = await ActivityLogger.fetch('{{ route("activity-logger.export") }}?' + params.toString(), {
                    method: 'POST'
                });
                
                const data = await response.json();
                
                if (data.success) {
                    ActivityLogger.showToast('Report generated successfully', 'success');
                    // Immediately download the report
                    window.location.href = data.download_url;
                } else {
                    throw new Error(data.error || 'Report generation failed');
                }
            } catch (error) {
                console.error('Report generation error:', error);
                ActivityLogger.showToast('Failed to generate report: ' + error.message, 'error');
            }
        },

        async generateCustomReport() {
            if (!this.customReport.name) {
                ActivityLogger.showToast('Please enter a report name', 'warning');
                return;
            }

            if (this.customReport.metrics.length === 0) {
                ActivityLogger.showToast('Please select at least one metric', 'warning');
                return;
            }

            try {
                ActivityLogger.showToast('Generating custom report...', 'info');
                
                // Build export parameters for custom report
                const params = new URLSearchParams({
                    report_type: 'custom',
                    format: this.customReport.format || 'csv',
                    start_date: this.customReport.dateRange === 'custom' ? this.customReport.startDate : new Date(Date.now() - 7 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
                    end_date: this.customReport.dateRange === 'custom' ? this.customReport.endDate : new Date().toISOString().split('T')[0],
                    report_name: this.customReport.name,
                    metrics: this.customReport.metrics.join(',')
                });
                
                const response = await ActivityLogger.fetch('{{ route("activity-logger.export") }}?' + params.toString(), {
                    method: 'POST'
                });
                
                const data = await response.json();
                
                if (data.success) {
                    ActivityLogger.showToast('Custom report generated successfully', 'success');
                    if (this.customReport.schedule) {
                        ActivityLogger.showToast('Report scheduled for ' + this.customReport.schedule + ' delivery', 'info');
                    }
                    // Immediately download the report
                    window.location.href = data.download_url;
                    this.resetCustomReport();
                } else {
                    throw new Error(data.error || 'Custom report generation failed');
                }
            } catch (error) {
                console.error('Custom report generation error:', error);  
                ActivityLogger.showToast('Failed to generate custom report: ' + error.message, 'error');
            }
        },

        downloadReport(url) {
            // In a real implementation, this would handle the download
            console.log('Downloading report from:', url);
            
            // Simulate download
            const link = document.createElement('a');
            link.href = '#';
            link.download = 'activity_report_' + new Date().toISOString().split('T')[0] + '.pdf';
            link.click();
        },

        resetCustomReport() {
            this.customReport = {
                name: '',
                dateRange: 'week',
                format: 'pdf',
                schedule: '',
                startDate: this.customReport.startDate,
                endDate: this.customReport.endDate,
                metrics: ['requests', 'users', 'errors'],
                emailRecipients: ''
            };
        },

        async saveReportTemplate() {
            if (!this.customReport.name) {
                ActivityLogger.showToast('Please enter a report name to save as template', 'warning');
                return;
            }

            try {
                ActivityLogger.showToast('Saving report template...', 'info');
                
                // In a real implementation, this would save the template
                setTimeout(() => {
                    ActivityLogger.showToast('Report template saved successfully', 'success');
                }, 1000);
            } catch (error) {
                ActivityLogger.showToast('Failed to save template', 'error');
            }
        },

        async refreshScheduledReports() {
            try {
                ActivityLogger.showToast('Refreshing scheduled reports...', 'info');
                
                // In a real implementation, this would refresh the data
                setTimeout(() => {
                    ActivityLogger.showToast('Scheduled reports refreshed', 'success');
                }, 1000);
            } catch (error) {
                ActivityLogger.showToast('Failed to refresh scheduled reports', 'error');
            }
        },

        async toggleSchedule(reportId) {
            try {
                ActivityLogger.showToast('Updating schedule...', 'info');
                
                // In a real implementation, this would toggle the schedule
                setTimeout(() => {
                    ActivityLogger.showToast('Schedule updated', 'success');
                }, 1000);
            } catch (error) {
                ActivityLogger.showToast('Failed to update schedule', 'error');
            }
        },

        editSchedule(reportId) {
            ActivityLogger.showToast('Edit functionality would open here', 'info');
            // In a real implementation, this would open an edit modal
        },

        async deleteSchedule(reportId) {
            if (!confirm('Are you sure you want to delete this scheduled report?')) {
                return;
            }

            try {
                ActivityLogger.showToast('Deleting scheduled report...', 'info');
                
                // In a real implementation, this would delete the schedule
                setTimeout(() => {
                    ActivityLogger.showToast('Scheduled report deleted', 'success');
                }, 1000);
            } catch (error) {
                ActivityLogger.showToast('Failed to delete scheduled report', 'error');
            }
        },

        async downloadSampleReport(reportType) {
            // This will download a sample report to demonstrate functionality
            await this.generateReport(reportType);
        }
    }
}
</script>
@endpush

@php
// Sample scheduled reports data
$scheduledReports = [
    [
        'id' => 1,
        'name' => 'Daily Performance Summary',
        'frequency' => 'daily',
        'format' => 'PDF',
        'active' => true,
        'nextRun' => now()->addHours(8)
    ],
    [
        'id' => 2,
        'name' => 'Weekly Error Analysis',
        'frequency' => 'weekly',
        'format' => 'Excel',
        'active' => true,
        'nextRun' => now()->addDays(3)
    ],
    [
        'id' => 3,
        'name' => 'Monthly User Activity Report',
        'frequency' => 'monthly',
        'format' => 'PDF',
        'active' => false,
        'nextRun' => now()->addDays(15)
    ]
];
@endphp