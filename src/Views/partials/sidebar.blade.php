<!-- Sidebar -->
<div class="bg-gray-800 text-white w-64 space-y-6 py-7 px-2 absolute inset-y-0 left-0 transform -translate-x-full md:relative md:translate-x-0 transition duration-200 ease-in-out" x-data="{ open: false }">
    <!-- Logo -->
    <div class="flex items-center space-x-2 px-4">
        <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
        </div>
        <span class="text-xl font-semibold">Activity Logger</span>
    </div>

    <!-- Navigation -->
    <nav class="mt-10">
        <div class="px-4 space-y-2">
            <!-- Dashboard -->
            <a href="{{ route('activity-logger.dashboard') }}" 
               class="flex items-center space-x-2 text-gray-300 hover:text-white hover:bg-gray-700 group rounded-md px-2 py-2 text-sm font-medium {{ request()->routeIs('activity-logger.dashboard') ? 'bg-gray-700 text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                </svg>
                <span>Dashboard</span>
            </a>

            <!-- Activity Logs -->
            <a href="{{ route('activity-logger.logs') }}" 
               class="flex items-center space-x-2 text-gray-300 hover:text-white hover:bg-gray-700 group rounded-md px-2 py-2 text-sm font-medium {{ request()->routeIs('activity-logger.logs*') ? 'bg-gray-700 text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span>Activity Logs</span>
                @if(isset($errorCount) && $errorCount > 0)
                    <span class="bg-red-600 text-white text-xs rounded-full px-2 py-1">{{ $errorCount }}</span>
                @endif
            </a>

            <!-- Performance -->
            <a href="{{ route('activity-logger.performance') }}" 
               class="flex items-center space-x-2 text-gray-300 hover:text-white hover:bg-gray-700 group rounded-md px-2 py-2 text-sm font-medium {{ request()->routeIs('activity-logger.performance') ? 'bg-gray-700 text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                <span>Performance</span>
            </a>

            <!-- Errors -->
            <a href="{{ route('activity-logger.errors') }}" 
               class="flex items-center space-x-2 text-gray-300 hover:text-white hover:bg-gray-700 group rounded-md px-2 py-2 text-sm font-medium {{ request()->routeIs('activity-logger.errors') ? 'bg-gray-700 text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Errors</span>
            </a>

            <!-- Traffic -->
            <a href="{{ route('activity-logger.traffic') }}" 
               class="flex items-center space-x-2 text-gray-300 hover:text-white hover:bg-gray-700 group rounded-md px-2 py-2 text-sm font-medium {{ request()->routeIs('activity-logger.traffic') ? 'bg-gray-700 text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                </svg>
                <span>Traffic Analysis</span>
            </a>

            <!-- Users -->
            <a href="{{ route('activity-logger.users') }}" 
               class="flex items-center space-x-2 text-gray-300 hover:text-white hover:bg-gray-700 group rounded-md px-2 py-2 text-sm font-medium {{ request()->routeIs('activity-logger.users*') ? 'bg-gray-700 text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                </svg>
                <span>Users</span>
            </a>

            <!-- Reports -->
            <a href="{{ route('activity-logger.reports') }}" 
               class="flex items-center space-x-2 text-gray-300 hover:text-white hover:bg-gray-700 group rounded-md px-2 py-2 text-sm font-medium {{ request()->routeIs('activity-logger.reports') ? 'bg-gray-700 text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span>Reports</span>
            </a>
        </div>

        <!-- System Status -->
        <div class="mt-8 px-4">
            <div class="bg-gray-700 rounded-lg p-4">
                <h3 class="text-sm font-medium text-gray-300 mb-2">System Status</h3>
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-400">API</span>
                        <span class="flex items-center text-xs text-green-400">
                            <div class="w-2 h-2 bg-green-400 rounded-full mr-1"></div>
                            Online
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-400">Database</span>
                        <span class="flex items-center text-xs text-green-400">
                            <div class="w-2 h-2 bg-green-400 rounded-full mr-1"></div>
                            Connected
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</div>