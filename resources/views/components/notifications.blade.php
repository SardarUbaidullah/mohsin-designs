<div x-data="notificationSystem()" x-init="initNotifications()" class="relative">
    <!-- Notification Bell -->
    <button @click="toggleNotifications()"
            type="button"
            class="relative p-2 text-gray-600 hover:text-blue-600 transition-colors duration-200 focus:outline-none">
        <i class="fas fa-bell text-xl"></i>

        <!-- Notification Badge -->
        <span x-show="unreadCount > 0"
              x-text="unreadCount > 99 ? '99+' : unreadCount"
              class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center animate-pulse"
              :class="unreadCount > 0 ? 'block' : 'hidden'">
        </span>
    </button>

    <!-- Notifications Dropdown -->
    <div x-show="isOpen"
         x-cloak
         @click.away="closeNotifications()"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-95"
         class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-xl border border-gray-200 z-50"
         style="display: none;">

        <!-- Header -->
        <div class="bg-blue-600 px-4 py-3 rounded-t-lg">
            <div class="flex items-center justify-between">
                <h3 class="text-white font-semibold">Notifications</h3>
                <div class="flex items-center space-x-2">
                    <span x-show="unreadCount > 0"
                          class="bg-white bg-opacity-20 text-white text-xs px-2 py-1 rounded-full">
                        <span x-text="unreadCount"></span> unread
                    </span>
                    <button @click="markAllAsRead()"
                            x-show="unreadCount > 0"
                            type="button"
                            class="text-white text-xs hover:bg-white hover:bg-opacity-20 px-2 py-1 rounded transition-colors">
                        Mark all read
                    </button>
                </div>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="max-h-64 overflow-y-auto">
            <!-- Loading -->
            <div x-show="loading" class="p-4 text-center">
                <i class="fas fa-spinner fa-spin text-blue-500"></i>
                <p class="text-gray-500 text-sm mt-2">Loading...</p>
            </div>

            <!-- Empty State -->
            <div x-show="!loading && notifications.length === 0" class="p-6 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-bell-slash text-gray-400 text-xl"></i>
                </div>
                <p class="text-gray-500 text-sm">No notifications</p>
                <p class="text-gray-400 text-xs mt-1">New notifications will appear here</p>
            </div>

            <!-- Notifications -->
            <template x-for="notification in notifications" :key="notification.id">
                <div class="border-b border-gray-100 hover:bg-gray-50 transition-colors duration-150">
                    <a :href="notification.data?.action_url || '#'"
                       @click="markAsRead(notification)"
                       class="block p-4">
                        <div class="flex items-start space-x-3">
                            <!-- Icon -->
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white flex-shrink-0"
                                 :class="getNotificationColor(notification)">
                                <i :class="notification.data?.icon || 'fas fa-bell'"></i>
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between mb-1">
                                    <p class="text-sm font-semibold text-gray-900"
                                       x-text="notification.data?.title || 'Notification'"></p>
                                    <span class="text-xs text-gray-500"
                                          x-text="formatTime(notification.created_at)"></span>
                                </div>
                                <p class="text-xs text-gray-600"
                                   x-text="notification.data?.message || ''"></p>
                                <div class="flex items-center justify-between mt-2">
                                    <span class="text-xs px-2 py-1 rounded-full"
                                          :class="getBadgeColor(notification)"
                                          x-text="getNotificationType(notification.type)"></span>
                                    <span x-show="!notification.read_at"
                                          class="w-2 h-2 bg-blue-600 rounded-full"></span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </template>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 px-4 py-3 border-t border-gray-200 rounded-b-lg">
            <div class="flex justify-between items-center">
                <button @click="clearAll()" 
                        type="button"
                        class="text-xs text-gray-500 hover:text-gray-700">
                    Clear All
                </button>
            </div>
        </div>
    </div>
</div>

<style>
[x-cloak] {
    display: none !important;
}
</style>

<script>
function notificationSystem() {
    return {
        isOpen: false,
        unreadCount: 0,
        notifications: [],
        loading: false,

        async initNotifications() {
            // Initialize with closed state
            this.isOpen = false;
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
                console.log('?? Loading notifications...');

                const response = await fetch('/notifications');
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                console.log('?? Notifications data:', data);

                if (data.success) {
                    this.notifications = data.notifications || [];
                    this.unreadCount = data.unread_count || 0;
                    this.updateTabTitle();
                } else {
                    console.error('API returned error:', data);
                }
            } catch (error) {
                console.error('? Failed to load notifications:', error);
                // Fallback to empty state
                this.notifications = [];
                this.unreadCount = 0;
            } finally {
                this.loading = false;
            }
        },

        toggleNotifications() {
            console.log('?? Toggle clicked, current state:', this.isOpen);
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                this.loadNotifications();
            }
        },

        closeNotifications() {
            console.log('?? Closing notifications');
            this.isOpen = false;
        },

        async markAsRead(notification) {
            try {
                console.log('?? Marking notification as read:', notification.id);
                
                const response = await fetch(`/notifications/${notification.id}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({})
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                console.log('? Mark as read response:', data);
                
                if (data.success) {
                    notification.read_at = new Date().toISOString();
                    this.unreadCount = Math.max(0, this.unreadCount - 1);
                    this.updateTabTitle();
                }
            } catch (error) {
                console.error('? Failed to mark as read:', error);
                // Fallback: update UI anyway
                notification.read_at = new Date().toISOString();
                this.unreadCount = Math.max(0, this.unreadCount - 1);
                this.updateTabTitle();
            }
        },

        async markAsRead(notification) {
            try {
                const response = await fetch(`/notifications/${notification.id}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                });

                const data = await response.json();
                if (data.success) {
                    notification.read_at = new Date().toISOString();
                    this.unreadCount = data.unread_count || this.unreadCount - 1;
                    this.updateTabTitle();
                }
            } catch (error) {
                console.error('Failed to mark as read:', error);
            }
        },

        async markAllAsRead() {
            try {
                const response = await fetch('/notifications/read-all', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                });

                const data = await response.json();
                if (data.success) {
                    this.unreadCount = 0;
                    this.notifications.forEach(notification => {
                        notification.read_at = new Date().toISOString();
                    });
                    this.updateTabTitle();
                }
            } catch (error) {
                console.error('Failed to mark all as read:', error);
            }
        },

        async clearAll() {
            try {
                const response = await fetch('/notifications/clear', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
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
                console.error('Failed to clear all:', error);
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
                pink: 'bg-pink-500',
                teal: 'bg-teal-500',
                orange: 'bg-orange-500'
            };
            return colors[color] || 'bg-gray-500';
        },

        getBadgeColor(notification) {
            const color = notification.data?.color || 'blue';
            const colors = {
                green: 'bg-green-100 text-green-800',
                blue: 'bg-blue-100 text-blue-800',
                yellow: 'bg-yellow-100 text-yellow-800',
                red: 'bg-red-100 text-red-800',
                purple: 'bg-purple-100 text-purple-800',
                pink: 'bg-pink-100 text-pink-800',
                teal: 'bg-teal-100 text-teal-800',
                orange: 'bg-orange-100 text-orange-800'
            };
            return colors[color] || 'bg-gray-100 text-gray-800';
        },

        getNotificationType(type) {
            const types = {
                'new_message': 'Message',
                'task_assigned': 'Task',
                'project_created': 'Project',
                'file_uploaded': 'File',
                'system_alert': 'System',
                'project_chat_message': 'Project Chat',
                'direct_message': 'Direct Message',
                'chat_message': 'Chat',
                'mentioned_in_chat': 'Mention',
                'message_read': 'Read Receipt'
            };
            return types[type] || 'Notification';
        },

        formatTime(timestamp) {
            if (!timestamp) return 'Just now';
            
            const date = new Date(timestamp);
            const now = new Date();
            const diffInSeconds = Math.floor((now - date) / 1000);
            
            if (diffInSeconds < 60) return 'Just now';
            if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)}m ago`;
            if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)}h ago`;
            if (diffInSeconds < 604800) return `${Math.floor(diffInSeconds / 86400)}d ago`;
            
            return date.toLocaleDateString();
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