<!-- Mobile Optimization Component -->
<div x-data="mobileOptimization()" x-init="init()" class="mobile-optimized">
    <!-- Mobile Navigation Menu -->
    <div x-show="showMobileMenu" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 lg:hidden" 
         style="display: none;">
        
        <!-- Overlay -->
        <div x-on:click="showMobileMenu = false" class="fixed inset-0 bg-gray-600 bg-opacity-75"></div>
        
        <!-- Mobile Menu Panel -->
        <div class="fixed inset-y-0 left-0 flex flex-col w-full max-w-xs bg-white shadow-xl">
            <!-- Header -->
            <div class="flex items-center justify-between h-16 px-4 bg-blue-600">
                <div class="flex items-center">
                    <h2 class="text-lg font-semibold text-white">Activity Logger</h2>
                </div>
                <button x-on:click="showMobileMenu = false" 
                        class="text-white hover:text-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Navigation Links -->
            <nav class="flex-1 px-4 py-4 overflow-y-auto">
                <div class="space-y-2">
                    @php
                    $mobileNavItems = [
                        ['route' => 'activity-logger.dashboard', 'label' => 'Dashboard', 'icon' => 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z'],
                        ['route' => 'activity-logger.logs', 'label' => 'Activity Logs', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                        ['route' => 'activity-logger.performance', 'label' => 'Performance', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z'],
                        ['route' => 'activity-logger.errors', 'label' => 'Errors', 'icon' => 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                        ['route' => 'activity-logger.traffic', 'label' => 'Traffic', 'icon' => 'M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z'],
                        ['route' => 'activity-logger.users', 'label' => 'Users', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z'],
                        ['route' => 'activity-logger.reports', 'label' => 'Reports', 'icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z']
                    ];
                    @endphp
                    
                    @foreach($mobileNavItems as $item)
                    <a href="{{ route($item['route']) }}" 
                       class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs($item['route']) ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"></path>
                        </svg>
                        {{ $item['label'] }}
                    </a>
                    @endforeach
                </div>
                
                <!-- System Status (Mobile) -->
                <div class="mt-6 p-3 bg-gray-50 rounded-lg">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">System Status</h3>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-600">Server</span>
                            <span class="flex items-center text-green-600">
                                <div class="w-2 h-2 bg-green-500 rounded-full mr-1"></div>
                                Online
                            </span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-600">Last Update</span>
                            <span class="text-gray-500" x-text="formatTime(lastUpdate)"></span>
                        </div>
                    </div>
                </div>
            </nav>
        </div>
    </div>

    <!-- Mobile Header Bar -->
    <div class="lg:hidden bg-white shadow-sm border-b border-gray-200 sticky top-0 z-40">
        <div class="flex items-center justify-between h-16 px-4">
            <!-- Mobile Menu Button -->
            <button x-on:click="showMobileMenu = true" 
                    class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
            
            <!-- Page Title -->
            <h1 class="text-lg font-semibold text-gray-900 truncate">
                @yield('page-title', 'Activity Logger')
            </h1>
            
            <!-- Mobile Actions -->
            <div class="flex items-center space-x-2">
                <!-- Quick Stats Toggle -->
                <button x-on:click="showQuickStats = !showQuickStats" 
                        class="text-gray-500 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </button>
                
                <!-- Mobile Search -->
                <button x-on:click="showMobileSearch = !showMobileSearch" 
                        class="text-gray-500 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>
            </div>
        </div>
        
        <!-- Mobile Search Bar -->
        <div x-show="showMobileSearch" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 max-h-0"
             x-transition:enter-end="opacity-100 max-h-16"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 max-h-16"
             x-transition:leave-end="opacity-0 max-h-0"
             class="border-t border-gray-200 overflow-hidden"
             style="display: none;">
            <div class="p-3">
                <div class="relative">
                    <input type="search" 
                           x-model="mobileSearchQuery"
                           x-on:input="performMobileSearch()"
                           placeholder="Search logs, errors, users..." 
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Stats (Mobile) -->
        <div x-show="showQuickStats" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 max-h-0"
             x-transition:enter-end="opacity-100 max-h-32"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 max-h-32"
             x-transition:leave-end="opacity-0 max-h-0"
             class="border-t border-gray-200 bg-gray-50 overflow-hidden"
             style="display: none;">
            <div class="p-3">
                <div class="grid grid-cols-2 gap-3">
                    <div class="text-center p-2 bg-white rounded">
                        <div class="text-lg font-semibold text-blue-600" x-text="quickStats.requests"></div>
                        <div class="text-xs text-gray-500">Requests</div>
                    </div>
                    <div class="text-center p-2 bg-white rounded">
                        <div class="text-lg font-semibold text-red-600" x-text="quickStats.errors"></div>
                        <div class="text-xs text-gray-500">Errors</div>
                    </div>
                    <div class="text-center p-2 bg-white rounded">
                        <div class="text-lg font-semibold text-green-600" x-text="quickStats.avgResponse + 'ms'"></div>
                        <div class="text-xs text-gray-500">Avg Response</div>
                    </div>
                    <div class="text-center p-2 bg-white rounded">
                        <div class="text-lg font-semibold text-purple-600" x-text="quickStats.users"></div>
                        <div class="text-xs text-gray-500">Active Users</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile-Optimized Content Wrapper -->
    <div class="mobile-content-wrapper">
        <!-- Responsive Grid System -->
        <style>
        @media (max-width: 768px) {
            .mobile-optimized .grid {
                grid-template-columns: 1fr !important;
            }
            
            .mobile-optimized .md\\:grid-cols-2 {
                grid-template-columns: 1fr !important;
            }
            
            .mobile-optimized .lg\\:grid-cols-3,
            .mobile-optimized .lg\\:grid-cols-4 {
                grid-template-columns: 1fr !important;
            }
            
            .mobile-optimized .overflow-x-auto {
                font-size: 0.875rem;
            }
            
            .mobile-optimized table {
                min-width: auto !important;
            }
            
            .mobile-optimized th,
            .mobile-optimized td {
                padding: 0.5rem 0.25rem !important;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                max-width: 150px;
            }
            
            .mobile-optimized .chart-container {
                height: 200px !important;
            }
            
            .mobile-optimized .text-sm {
                font-size: 0.75rem !important;
            }
            
            .mobile-optimized .space-y-6 > * + * {
                margin-top: 1rem !important;
            }
        }
        
        @media (max-width: 640px) {
            .mobile-optimized .px-4 {
                padding-left: 0.75rem !important;
                padding-right: 0.75rem !important;
            }
            
            .mobile-optimized .py-5 {
                padding-top: 1rem !important;
                padding-bottom: 1rem !important;
            }
            
            .mobile-optimized .gap-6 {
                gap: 0.75rem !important;
            }
        }
        </style>

        <!-- Touch-Friendly Components -->
        <div class="mobile-touch-improvements">
            <style>
            @media (max-width: 768px) {
                .mobile-optimized button,
                .mobile-optimized .btn,
                .mobile-optimized input[type="button"],
                .mobile-optimized input[type="submit"] {
                    min-height: 44px !important;
                    min-width: 44px !important;
                    padding: 0.75rem 1rem !important;
                }
                
                .mobile-optimized input,
                .mobile-optimized select,
                .mobile-optimized textarea {
                    min-height: 44px !important;
                    font-size: 16px !important; /* Prevents zoom on iOS */
                }
                
                .mobile-optimized .clickable-row {
                    min-height: 60px !important;
                    display: flex !important;
                    align-items: center !important;
                }
                
                .mobile-optimized .touch-target {
                    padding: 0.75rem !important;
                }
            }
            </style>
        </div>

        <!-- Swipe Gestures for Tables -->
        <div x-data="{ 
            swipeStartX: 0, 
            swipeEndX: 0,
            handleSwipeStart(e) { 
                this.swipeStartX = e.touches[0].clientX; 
            },
            handleSwipeEnd(e) { 
                this.swipeEndX = e.changedTouches[0].clientX;
                this.handleSwipe();
            },
            handleSwipe() {
                const swipeDistance = this.swipeStartX - this.swipeEndX;
                const minSwipeDistance = 50;
                
                if (Math.abs(swipeDistance) > minSwipeDistance) {
                    const table = e.target.closest('table');
                    if (table) {
                        if (swipeDistance > 0) {
                            // Swipe left - scroll right
                            table.scrollLeft += 100;
                        } else {
                            // Swipe right - scroll left  
                            table.scrollLeft -= 100;
                        }
                    }
                }
            }
        }" class="mobile-swipe-handler lg:hidden">
            <div x-on:touchstart="handleSwipeStart($event)" 
                 x-on:touchend="handleSwipeEnd($event)"
                 class="swipe-area">
                <!-- Swipe indicator for tables -->
                <div class="text-center py-2 text-xs text-gray-500 border-b">
                    ← Swipe to scroll table →
                </div>
            </div>
        </div>

        <!-- Mobile-Optimized Filters -->
        <div class="mobile-filters lg:hidden">
            <button x-on:click="showMobileFilters = !showMobileFilters"
                    class="w-full flex items-center justify-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg mb-4">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"></path>
                </svg>
                <span x-text="showMobileFilters ? 'Hide Filters' : 'Show Filters'"></span>
            </button>

            <div x-show="showMobileFilters" 
                 x-transition
                 class="bg-white rounded-lg shadow p-4 mb-4"
                 style="display: none;">
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date Range</label>
                        <select class="w-full border-gray-300 rounded-md">
                            <option>Today</option>
                            <option>Last 7 days</option>
                            <option>Last 30 days</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select class="w-full border-gray-300 rounded-md">
                            <option>All</option>
                            <option>Success</option>
                            <option>Error</option>
                        </select>
                    </div>
                    <button class="w-full bg-blue-600 text-white py-2 rounded-md">Apply Filters</button>
                </div>
            </div>
        </div>

        <!-- Pull-to-Refresh -->
        <div x-data="pullToRefresh()" 
             x-on:touchstart="handleTouchStart($event)"
             x-on:touchmove="handleTouchMove($event)" 
             x-on:touchend="handleTouchEnd($event)"
             class="pull-to-refresh-container">
            
            <div x-show="pullDistance > 0" 
                 class="pull-to-refresh-indicator fixed top-16 left-1/2 transform -translate-x-1/2 z-30"
                 :style="'transform: translateX(-50%) translateY(' + Math.max(0, pullDistance - 50) + 'px)'">
                <div class="bg-blue-500 text-white px-3 py-1 rounded-full text-sm">
                    <span x-show="pullDistance < refreshThreshold">Pull to refresh</span>
                    <span x-show="pullDistance >= refreshThreshold">Release to refresh</span>
                </div>
            </div>
        </div>

        <!-- Mobile Floating Action Button -->
        <div class="fixed bottom-4 right-4 z-40 lg:hidden">
            <div x-data="{ showFab: false }" class="relative">
                <button x-on:click="showFab = !showFab"
                        class="w-14 h-14 bg-blue-600 text-white rounded-full shadow-lg hover:bg-blue-700 transition-colors flex items-center justify-center">
                    <svg x-show="!showFab" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <svg x-show="showFab" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>

                <div x-show="showFab" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute bottom-16 right-0 space-y-2"
                     style="display: none;">
                    
                    <button class="w-12 h-12 bg-green-500 text-white rounded-full shadow-lg flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </button>
                    
                    <button class="w-12 h-12 bg-purple-500 text-white rounded-full shadow-lg flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function mobileOptimization() {
    return {
        showMobileMenu: false,
        showMobileSearch: false,
        showQuickStats: false,
        showMobileFilters: false,
        mobileSearchQuery: '',
        lastUpdate: new Date(),
        quickStats: {
            requests: '1.2K',
            errors: '23',
            avgResponse: '145',
            users: '89'
        },
        
        init() {
            this.setupMobileOptimizations();
            this.updateQuickStats();
            
            // Close mobile menu on route change
            window.addEventListener('beforeunload', () => {
                this.showMobileMenu = false;
            });
            
            // Update viewport meta tag for better mobile experience
            this.updateViewport();
        },
        
        setupMobileOptimizations() {
            // Add mobile-specific CSS classes
            document.body.classList.add('mobile-optimized');
            
            // Prevent zoom on input focus (iOS)
            const inputs = document.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                if (input.style.fontSize === '') {
                    input.style.fontSize = '16px';
                }
            });
            
            // Add touch-friendly classes to interactive elements
            const buttons = document.querySelectorAll('button, .btn, a[role="button"]');
            buttons.forEach(button => {
                button.classList.add('touch-target');
            });
        },
        
        updateViewport() {
            let viewport = document.querySelector('meta[name="viewport"]');
            if (!viewport) {
                viewport = document.createElement('meta');
                viewport.name = 'viewport';
                document.head.appendChild(viewport);
            }
            viewport.content = 'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no';
        },
        
        async performMobileSearch() {
            if (this.mobileSearchQuery.length < 2) return;
            
            try {
                // Simulated search - in real implementation, this would search across all data
                console.log('Mobile search:', this.mobileSearchQuery);
                
                if (window.ActivityLogger) {
                    ActivityLogger.showToast('Searching...', 'info');
                }
                
                // Simulate search delay
                setTimeout(() => {
                    if (window.ActivityLogger) {
                        ActivityLogger.showToast('Search completed', 'success');
                    }
                }, 1000);
            } catch (error) {
                if (window.ActivityLogger) {
                    ActivityLogger.showToast('Search failed', 'error');
                }
            }
        },
        
        async updateQuickStats() {
            try {
                // In real implementation, fetch actual stats
                const stats = await this.fetchQuickStats();
                this.quickStats = stats;
                this.lastUpdate = new Date();
            } catch (error) {
                console.error('Failed to update quick stats:', error);
            }
        },
        
        async fetchQuickStats() {
            // Simulated API call
            return new Promise(resolve => {
                setTimeout(() => {
                    resolve({
                        requests: Math.floor(Math.random() * 2000) + 1000 + '',
                        errors: Math.floor(Math.random() * 50) + 10 + '',
                        avgResponse: Math.floor(Math.random() * 200) + 100 + '',
                        users: Math.floor(Math.random() * 100) + 50 + ''
                    });
                }, 500);
            });
        },
        
        formatTime(timestamp) {
            const now = new Date();
            const time = new Date(timestamp);
            const diff = now - time;
            
            if (diff < 60000) {
                return 'Just now';
            } else if (diff < 3600000) {
                return Math.floor(diff / 60000) + 'm ago';
            } else {
                return time.toLocaleTimeString([], {hour: '2-digit', minute: '2-digit'});
            }
        }
    }
}

function pullToRefresh() {
    return {
        pullDistance: 0,
        refreshThreshold: 80,
        startY: 0,
        currentY: 0,
        isRefreshing: false,
        
        handleTouchStart(e) {
            if (window.scrollY === 0) {
                this.startY = e.touches[0].clientY;
            }
        },
        
        handleTouchMove(e) {
            if (window.scrollY === 0 && !this.isRefreshing) {
                this.currentY = e.touches[0].clientY;
                this.pullDistance = Math.max(0, this.currentY - this.startY);
                
                if (this.pullDistance > 0) {
                    e.preventDefault();
                }
            }
        },
        
        async handleTouchEnd(e) {
            if (this.pullDistance >= this.refreshThreshold && !this.isRefreshing) {
                this.isRefreshing = true;
                
                try {
                    if (window.ActivityLogger) {
                        ActivityLogger.showToast('Refreshing...', 'info');
                    }
                    
                    // Simulate refresh
                    await new Promise(resolve => setTimeout(resolve, 1500));
                    
                    // Refresh the page data
                    window.location.reload();
                    
                } catch (error) {
                    if (window.ActivityLogger) {
                        ActivityLogger.showToast('Refresh failed', 'error');
                    }
                } finally {
                    this.isRefreshing = false;
                }
            }
            
            this.pullDistance = 0;
            this.startY = 0;
            this.currentY = 0;
        }
    }
}
</script>