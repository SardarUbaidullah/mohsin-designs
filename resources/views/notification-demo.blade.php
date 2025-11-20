<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification System</title>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <style>
        .notification-badge {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        .fade-in {
            animation: fadeIn 0.3s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-gray-100 p-8">

    <!-- Notification Component -->
    <div x-data="notificationSystem()" x-init="init()" class="max-w-4xl mx-auto">

        <!-- Header -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Notification System</h1>
            <p class="text-gray-600">100% Working Notification System</p>
        </div>

        <!-- Notification Bell -->
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center space-x-4">
                <button @click="toggleNotifications"
                        class="relative p-3 bg-white rounded-full shadow-lg hover:shadow-xl transition-all duration-200">
                    <i class="fas fa-bell text-2xl text-gray-600"></i>

                    <!-- Badge -->
                    <span x-show="unreadCount > 0"
                          x-text="unreadCount > 99 ? '99+' : unreadCount"
                          class="notification-badge absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full h-6 w-6 flex items-center justify-center border-2 border-white">
                    </span>
                </button>

                <div class="text-sm text-gray-600">
                    <span x-text="unreadCount"></span> unread notifications
                </div>
            </div>

            <!-- Actions -->
            <div class="flex space-x-2">
                <button @click="markAllAsRead"
                        x-show="unreadCount > 0"
                        class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                    Mark All Read
                </button>
                <button @click="clearAll"
                        class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                    Clear All
                </button>
                <button @click="addTestNotification"
                        class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                    Test Notification
                </button>
            </div>
        </div>

        <!-- Notifications Panel -->
        <div x-show="isOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95"
             class="bg-white rounded-lg shadow-xl border border-gray-200 overflow-hidden fade-in">

            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-white font-bold text-lg">
                        <i class="fas fa-bell mr-2"></i>Notifications
                    </h3>
                    <span x-show="unreadCount > 0" class="bg-white bg-opacity-20 text-white px-3 py-1 rounded-full text-sm">
                        <span x-text="unreadCount"></span> unread
                    </span>
                </div>
            </div>

            <!-- Loading -->
            <div x-show="loading" class="p-8 text-center">
                <i class="fas fa-spinner fa-spin text-blue-500 text-2xl mb-3"></i>
                <p class="text-gray-600">Loading notifications...</p>
            </div>

            <!-- Empty State -->
            <div x-show="!loading && notifications.length === 0" class="p-8 text-center">
                <i class="fas fa-bell-slash text-gray-400 text-4xl mb-4"></i>
                <h4 class="text-gray-600 font-semibold mb-2">No notifications yet</h4>
                <p class="text-gray-500 text-sm">Notifications will appear here when you receive them</p>
            </div>

            <!-- Notifications List -->
            <div x-show="!loading && notifications.length > 0" class="max-h-96 overflow-y-auto">
                <template x-for="notification in notifications" :key="notification.id">
                    <div class="border-b border-gray-100 hover:bg-gray-50 transition-colors duration-150">
                        <div class="p-4">
                            <div class="flex items-start space-x-4">
                                <!-- Icon -->
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-white"
                                         :class="getNotificationColor(notification)">
                                        <i :class="notification.data?.icon || 'fas fa-bell'"></i>
                                    </div>
                                </div>

                                <!-- Content -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between mb-2">
                                        <div>
                                            <h4 class="font-semibold text-gray-900 text-sm mb-1"
                                                x-text="notification.data?.title || 'Notification'"></h4>
                                            <p class="text-gray-600 text-sm"
                                               x-text="notification.data?.message || 'You have a new notification'"></p>
                                        </div>
                                        <button @click="markAsRead(notification)"
                                                x-show="!notification.read_at"
                                                class="text-xs bg-blue-100 text-blue-600 px-2 py-1 rounded hover:bg-blue-200 transition-colors">
                                            Mark Read
                                        </button>
                                    </div>

                                    <div class="flex items-center justify-between mt-2">
                                        <span class="text-xs text-gray-500" x-text="notification.time_ago"></span>
                                        <div class="flex items-center space-x-2">
                                            <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-700"
                                                  x-text="getNotificationType(notification.type)"></span>
                                            <span x-show="!notification.read_at"
                                                  class="w-2 h-2 bg-blue-500 rounded-full"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">
                        Showing <span x-text="notifications.length"></span> notifications
                    </span>
                    <button @click="loadNotifications" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                        <i class="fas fa-refresh mr-1"></i>Refresh
                    </button>
                </div>
            </div>
        </div>

        <!-- Debug Info -->
        <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <h4 class="font-semibold text-yellow-800 mb-2">Debug Information:</h4>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div>
                    <span class="font-medium">Unread Count:</span>
                    <span x-text="unreadCount" class="ml-2 font-bold"></span>
                </div>
                <div>
                    <span class="font-medium">Total Notifications:</span>
                    <span x-text="notifications.length" class="ml-2 font-bold"></span>
                </div>
                <div>
                    <span class="font-medium">Panel Open:</span>
                    <span x-text="isOpen" class="ml-2 font-bold"></span>
                </div>
                <div>
                    <span class="font-medium">Loading:</span>
                    <span x-text="loading" class="ml-2 font-bold"></span>
                </div>
            </div>
        </div>
    </div>

    <script>
    function notificationSystem() {
        return {
            isOpen: false,
            unreadCount: 0,
            notifications: [],
            loading: false,

            async init() {
                await this.loadNotifications();
                // Auto-refresh every 30 seconds
                setInterval(() => {
                    if (!this.isOpen) {
                        this.loadNotifications();
                    }
                }, 30000);
            },

            async loadNotifications() {
                try {
                    this.loading = true;
                    console.log('🔄 Loading notifications...');

                    const response = await fetch('/notifications');
                    const data = await response.json();

                    console.log('📦 Response:', data);

                    if (data.success) {
                        this.notifications = data.notifications || [];
                        this.unreadCount = data.unread_count || 0;
                        this.updateTabTitle();
                    } else {
                        console.error('❌ API Error:', data.message);
                    }
                } catch (error) {
                    console.error('❌ Fetch Error:', error);
                } finally {
                    this.loading = false;
                }
            },

            toggleNotifications() {
                this.isOpen = !this.isOpen;
                if (this.isOpen) {
                    this.loadNotifications();
                }
            },

            async markAsRead(notification) {
                try {
                    const response = await fetch(`/notifications/${notification.id}/read`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        notification.read_at = new Date().toISOString();
                        this.unreadCount = data.unread_count;
                        this.updateTabTitle();
                    }
                } catch (error) {
                    console.error('Mark as read error:', error);
                }
            },

            async markAllAsRead() {
                try {
                    const response = await fetch('/notifications/read-all', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        this.notifications.forEach(notification => {
                            notification.read_at = new Date().toISOString();
                        });
                        this.unreadCount = 0;
                        this.updateTabTitle();
                    }
                } catch (error) {
                    console.error('Mark all as read error:', error);
                }
            },

            async clearAll() {
                if (confirm('Are you sure you want to clear all notifications?')) {
                    try {
                        const response = await fetch('/notifications/clear', {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                            }
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.notifications = [];
                            this.unreadCount = 0;
                            this.updateTabTitle();
                            this.isOpen = false;
                        }
                    } catch (error) {
                        console.error('Clear all error:', error);
                    }
                }
            },

            async addTestNotification() {
                try {
                    // This would typically be called from your backend
                    // For testing, we'll simulate by reloading
                    await this.loadNotifications();

                    // Show success message
                    alert('Check your notifications! If no test data exists, create some in your database.');
                } catch (error) {
                    console.error('Test notification error:', error);
                }
            },

            getNotificationColor(notification) {
                const color = notification.data?.color || 'blue';
                const colors = {
                    green: 'bg-green-500',
                    blue: 'bg-blue-500',
                    yellow: 'bg-yellow-500',
                    red: 'bg-red-500',
                    purple: 'bg-purple-500',
                    pink: 'bg-pink-500'
                };
                return colors[color] || 'bg-gray-500';
            },

            getNotificationType(type) {
                const types = {
                    'new_message': 'Message',
                    'task_assigned': 'Task',
                    'project_created': 'Project',
                    'file_uploaded': 'File',
                    'system_alert': 'System'
                };
                return types[type] || 'General';
            },

            updateTabTitle() {
                const baseTitle = document.title.replace(/^\(\d+\)\s*/, '');
                if (this.unreadCount > 0) {
                    document.title = `(${this.unreadCount}) ${baseTitle}`;
                } else {
                    document.title = baseTitle;
                }
            }
        }
    }
    </script>
</body>
</html>
