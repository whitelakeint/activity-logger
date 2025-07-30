<!-- Real-time Notifications Component -->
<div x-data="notificationSystem()" x-init="init()" class="fixed top-0 right-0 z-50 p-4 space-y-2" style="pointer-events: none;">
    <!-- Notification Container -->
    <template x-for="notification in notifications" :key="notification.id">
        <div x-show="notification.visible"
             x-transition:enter="transform ease-out duration-300 transition"
             x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
             x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             :class="{
                'bg-green-50 border-green-200': notification.type === 'success',
                'bg-red-50 border-red-200': notification.type === 'error',
                'bg-yellow-50 border-yellow-200': notification.type === 'warning',
                'bg-blue-50 border-blue-200': notification.type === 'info',
                'bg-purple-50 border-purple-200': notification.type === 'critical'
             }"
             class="max-w-sm w-full shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden border">
            
            <div class="p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <!-- Success Icon -->
                        <svg x-show="notification.type === 'success'" class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        
                        <!-- Error Icon -->
                        <svg x-show="notification.type === 'error'" class="h-6 w-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        
                        <!-- Warning Icon -->
                        <svg x-show="notification.type === 'warning'" class="h-6 w-6 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        
                        <!-- Info Icon -->
                        <svg x-show="notification.type === 'info'" class="h-6 w-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        
                        <!-- Critical Icon -->
                        <svg x-show="notification.type === 'critical'" class="h-6 w-6 text-purple-400 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    
                    <div class="ml-3 w-0 flex-1 pt-0.5">
                        <p class="text-sm font-medium" 
                           :class="{
                               'text-green-900': notification.type === 'success',
                               'text-red-900': notification.type === 'error',
                               'text-yellow-900': notification.type === 'warning',
                               'text-blue-900': notification.type === 'info',
                               'text-purple-900': notification.type === 'critical'
                           }"
                           x-text="notification.title">
                        </p>
                        <p class="mt-1 text-sm"
                           :class="{
                               'text-green-700': notification.type === 'success',
                               'text-red-700': notification.type === 'error',
                               'text-yellow-700': notification.type === 'warning',
                               'text-blue-700': notification.type === 'info',
                               'text-purple-700': notification.type === 'critical'
                           }"
                           x-text="notification.message">
                        </p>
                        
                        <!-- Action Buttons -->
                        <div x-show="notification.actions && notification.actions.length > 0" class="mt-3 flex space-x-2">
                            <template x-for="action in notification.actions" :key="action.label">
                                <button x-on:click="handleAction(notification.id, action)" 
                                        class="text-sm font-medium rounded-md px-2 py-1 hover:bg-white hover:bg-opacity-50 transition-colors"
                                        :class="{
                                            'text-green-800 hover:bg-green-100': notification.type === 'success',
                                            'text-red-800 hover:bg-red-100': notification.type === 'error',
                                            'text-yellow-800 hover:bg-yellow-100': notification.type === 'warning',
                                            'text-blue-800 hover:bg-blue-100': notification.type === 'info',
                                            'text-purple-800 hover:bg-purple-100': notification.type === 'critical'
                                        }"
                                        x-text="action.label">
                                </button>
                            </template>
                        </div>
                        
                        <!-- Timestamp -->
                        <p class="mt-2 text-xs opacity-75"
                           :class="{
                               'text-green-600': notification.type === 'success',
                               'text-red-600': notification.type === 'error',
                               'text-yellow-600': notification.type === 'warning',
                               'text-blue-600': notification.type === 'info',
                               'text-purple-600': notification.type === 'critical'
                           }"
                           x-text="formatTime(notification.timestamp)">
                        </p>
                    </div>
                    
                    <div class="ml-4 flex-shrink-0 flex">
                        <button x-on:click="dismissNotification(notification.id)"
                                class="rounded-md inline-flex focus:outline-none focus:ring-2 focus:ring-offset-2"
                                :class="{
                                    'text-green-400 hover:text-green-500 focus:ring-green-500': notification.type === 'success',
                                    'text-red-400 hover:text-red-500 focus:ring-red-500': notification.type === 'error',
                                    'text-yellow-400 hover:text-yellow-500 focus:ring-yellow-500': notification.type === 'warning',
                                    'text-blue-400 hover:text-blue-500 focus:ring-blue-500': notification.type === 'info',
                                    'text-purple-400 hover:text-purple-500 focus:ring-purple-500': notification.type === 'critical'
                                }">
                            <span class="sr-only">Close</span>
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <!-- Progress Bar for Auto-dismiss -->
                <div x-show="notification.autoDismiss" class="mt-3">
                    <div class="w-full bg-gray-200 rounded-full h-1">
                        <div class="h-1 rounded-full transition-all duration-100 ease-linear"
                             :class="{
                                 'bg-green-400': notification.type === 'success',
                                 'bg-red-400': notification.type === 'error',
                                 'bg-yellow-400': notification.type === 'warning',
                                 'bg-blue-400': notification.type === 'info',
                                 'bg-purple-400': notification.type === 'critical'
                             }"
                             :style="'width: ' + notification.progress + '%'">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
    
    <!-- Notification Center Toggle -->
    <div class="fixed top-4 right-4 pointer-events-auto">
        <button x-on:click="toggleNotificationCenter()" 
                class="relative p-2 bg-white rounded-full shadow-lg border border-gray-200 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
            </svg>
            
            <!-- Notification Badge -->
            <span x-show="unreadCount > 0" 
                  x-text="unreadCount" 
                  class="absolute -top-1 -right-1 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full min-w-[1.25rem] h-5">
            </span>
        </button>
    </div>
</div>

<!-- Notification Center Modal -->
<div x-show="showNotificationCenter" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 overflow-y-auto pointer-events-auto"
     style="display: none;">
    
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div x-on:click="showNotificationCenter = false" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        
        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Notification Center</h3>
                <div class="flex items-center space-x-2">
                    <button x-on:click="markAllAsRead()" class="text-sm text-blue-600 hover:text-blue-500">
                        Mark all as read
                    </button>
                    <button x-on:click="clearAllNotifications()" class="text-sm text-red-600 hover:text-red-500">
                        Clear all
                    </button>
                </div>
            </div>
            
            <div class="max-h-96 overflow-y-auto space-y-2">
                <template x-for="notification in allNotifications.slice().reverse()" :key="notification.id">
                    <div class="p-3 border rounded-lg hover:bg-gray-50 cursor-pointer"
                         :class="{ 'bg-blue-50 border-blue-200': !notification.read, 'bg-white border-gray-200': notification.read }"
                         x-on:click="markAsRead(notification.id)">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 mt-1">
                                <div class="w-2 h-2 rounded-full" 
                                     :class="{
                                         'bg-green-400': notification.type === 'success',
                                         'bg-red-400': notification.type === 'error',
                                         'bg-yellow-400': notification.type === 'warning',
                                         'bg-blue-400': notification.type === 'info',
                                         'bg-purple-400': notification.type === 'critical'
                                     }"></div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900" x-text="notification.title"></p>
                                <p class="text-sm text-gray-500 truncate" x-text="notification.message"></p>
                                <p class="text-xs text-gray-400 mt-1" x-text="formatTime(notification.timestamp)"></p>
                            </div>
                        </div>
                    </div>
                </template>
                
                <div x-show="allNotifications.length === 0" class="text-center py-8 text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <p class="mt-2">No notifications</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function notificationSystem() {
    return {
        notifications: [],
        allNotifications: [],
        showNotificationCenter: false,
        unreadCount: 0,
        eventSource: null,
        
        init() {
            this.initializeEventSource();
            this.loadStoredNotifications();
            
            // Global notification methods
            window.showNotification = this.addNotification.bind(this);
            window.notificationSystem = this;
        },
        
        initializeEventSource() {
            // Use AJAX polling instead of Server-Sent Events for better compatibility
            this.startNotificationPolling();
        },
        
        startNotificationPolling() {
            setInterval(async () => {
                try {
                    const response = await fetch('{{ route("activity-logger.api.realtime.notifications") }}');
                    if (response.ok) {
                        const data = await response.json();
                        if (data.notifications && data.notifications.length > 0) {
                            data.notifications.forEach(notification => {
                                this.addNotification({
                                    type: notification.type,
                                    title: notification.title,
                                    message: notification.message,
                                    autoDismiss: notification.severity !== 'high'
                                });
                            });
                        }
                    }
                } catch (error) {
                    console.error('Failed to fetch notifications:', error);
                }
            }, 30000); // Poll every 30 seconds
        },
        
        addNotification(config) {
            const notification = {
                id: Date.now() + Math.random(),
                type: config.type || 'info',
                title: config.title || 'Notification',
                message: config.message || '',
                timestamp: new Date(),
                autoDismiss: config.autoDismiss !== false,
                duration: config.duration || (config.type === 'critical' ? 0 : 5000),
                actions: config.actions || [],
                read: false,
                visible: true,
                progress: 100
            };
            
            this.notifications.push(notification);
            this.allNotifications.push(notification);
            this.updateUnreadCount();
            this.saveToStorage();
            
            // Auto-dismiss timer
            if (notification.autoDismiss && notification.duration > 0) {
                this.startProgressTimer(notification);
                setTimeout(() => {
                    this.dismissNotification(notification.id);
                }, notification.duration);
            }
            
            // Play notification sound for critical alerts
            if (notification.type === 'critical') {
                this.playNotificationSound();
            }
            
            return notification.id;
        },
        
        startProgressTimer(notification) {
            const startTime = Date.now();
            const duration = notification.duration;
            
            const updateProgress = () => {
                const elapsed = Date.now() - startTime;
                const progress = Math.max(0, 100 - (elapsed / duration) * 100);
                
                const notif = this.notifications.find(n => n.id === notification.id);
                if (notif) {
                    notif.progress = progress;
                    if (progress > 0) {
                        requestAnimationFrame(updateProgress);
                    }
                }
            };
            
            requestAnimationFrame(updateProgress);
        },
        
        dismissNotification(id) {
            const index = this.notifications.findIndex(n => n.id === id);
            if (index > -1) {
                this.notifications[index].visible = false;
                setTimeout(() => {
                    this.notifications.splice(index, 1);
                }, 300);
            }
        },
        
        handleRealtimeNotification(data) {
            switch (data.type) {
                case 'error_spike':
                    this.addNotification({
                        type: 'critical',
                        title: 'Error Spike Detected',
                        message: `${data.count} errors in the last ${data.timeframe} minutes`,
                        actions: [
                            { label: 'View Errors', action: 'navigate', url: '{{ route("activity-logger.errors") }}' },
                            { label: 'Dismiss', action: 'dismiss' }
                        ]
                    });
                    break;
                    
                case 'performance_alert':
                    this.addNotification({
                        type: 'warning',
                        title: 'Performance Alert',
                        message: `Average response time: ${data.responseTime}ms (threshold: ${data.threshold}ms)`,
                        actions: [
                            { label: 'View Performance', action: 'navigate', url: '{{ route("activity-logger.performance") }}' }
                        ]
                    });
                    break;
                    
                case 'security_alert':
                    this.addNotification({
                        type: 'critical',
                        title: 'Security Alert',
                        message: data.message,
                        autoDismiss: false,
                        actions: [
                            { label: 'Investigate', action: 'navigate', url: '{{ route("activity-logger.logs") }}?security=1' }
                        ]
                    });
                    break;
                    
                case 'system_status':
                    this.addNotification({
                        type: data.status === 'up' ? 'success' : 'error',
                        title: 'System Status Update',
                        message: `System is now ${data.status}`,
                        duration: 3000
                    });
                    break;
            }
        },
        
        
        hasNotification(alertId) {
            return this.allNotifications.some(n => n.alertId === alertId);
        },
        
        handleAction(notificationId, action) {
            switch (action.action) {
                case 'navigate':
                    window.location.href = action.url;
                    break;
                case 'dismiss':
                    this.dismissNotification(notificationId);
                    break;
                case 'callback':
                    if (action.callback && typeof action.callback === 'function') {
                        action.callback();
                    }
                    break;
            }
        },
        
        toggleNotificationCenter() {
            this.showNotificationCenter = !this.showNotificationCenter;
        },
        
        markAsRead(id) {
            const notification = this.allNotifications.find(n => n.id === id);
            if (notification && !notification.read) {
                notification.read = true;
                this.updateUnreadCount();
                this.saveToStorage();
            }
        },
        
        markAllAsRead() {
            this.allNotifications.forEach(n => n.read = true);
            this.updateUnreadCount();
            this.saveToStorage();
        },
        
        clearAllNotifications() {
            this.notifications = [];
            this.allNotifications = [];
            this.updateUnreadCount();
            this.saveToStorage();
        },
        
        updateUnreadCount() {
            this.unreadCount = this.allNotifications.filter(n => !n.read).length;
        },
        
        formatTime(timestamp) {
            const now = new Date();
            const time = new Date(timestamp);
            const diff = now - time;
            
            if (diff < 60000) { // Less than 1 minute
                return 'Just now';
            } else if (diff < 3600000) { // Less than 1 hour
                return Math.floor(diff / 60000) + 'm ago';
            } else if (diff < 86400000) { // Less than 1 day
                return Math.floor(diff / 3600000) + 'h ago';
            } else {
                return time.toLocaleDateString();
            }
        },
        
        playNotificationSound() {
            // Create and play a notification sound
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            
            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);
            
            oscillator.frequency.setValueAtTime(800, audioContext.currentTime);
            oscillator.frequency.setValueAtTime(600, audioContext.currentTime + 0.1);
            
            gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.2);
            
            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.2);
        },
        
        saveToStorage() {
            const data = {
                notifications: this.allNotifications.slice(-50), // Keep last 50
                timestamp: Date.now()
            };
            localStorage.setItem('activity-logger-notifications', JSON.stringify(data));
        },
        
        loadStoredNotifications() {
            const stored = localStorage.getItem('activity-logger-notifications');
            if (stored) {
                const data = JSON.parse(stored);
                // Only load notifications from the last 24 hours
                const dayAgo = Date.now() - 86400000;
                this.allNotifications = data.notifications.filter(n => 
                    new Date(n.timestamp).getTime() > dayAgo
                );
                this.updateUnreadCount();
            }
        }
    }
}
</script>