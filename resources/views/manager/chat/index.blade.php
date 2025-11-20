
@php
    $layout = match(Auth::user()->role) {
        'super_admin' => 'admin.layouts.app',
        'admin' => 'manager.layouts.app',
        'user' => 'team.app',
    };
@endphp
@extends($layout)

@section('content')
<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    primary: {
                        50: '#f0f9ff',
                        100: '#e0f2fe',
                        500: '#0ea5e9',
                        600: '#0284c7',
                        700: '#0369a1',
                    }
                }
            }
        }
    }

    // Real-time notification functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize real-time notifications
        initializeNotifications();

        // Set up periodic checks for new messages
        setInterval(checkForNewMessages, 10000); // Check every 10 seconds

        // Mark messages as read when viewing a chat
        document.querySelectorAll('.chat-room-link').forEach(link => {
            link.addEventListener('click', function() {
                const roomId = this.dataset.roomId;
                if (roomId) {
                    markMessagesAsRead(roomId);
                }
            });
        });
    });

    function initializeNotifications() {
        // Set up WebSocket or polling for real-time notifications
        if (typeof Echo !== 'undefined') {
            // Laravel Echo setup for real-time
            Echo.private(`user.${authUserId}`)
                .listen('NewMessageNotification', (e) => {
                    displayNewNotification(e.message);
                    updateUnreadCounts();
                });
        }
    }

    function checkForNewMessages() {
        // AJAX call to check for new messages
        fetch('/api/chat/unread-counts')
            .then(response => response.json())
            .then(data => {
                updateUnreadCounts(data);
            });
    }

    function displayNewNotification(message) {
        // Create and display new notification at the top
        const notificationContainer = document.getElementById('notification-container');
        const notification = createNotificationElement(message);

        // Insert at top
        if (notificationContainer.firstChild) {
            notificationContainer.insertBefore(notification, notificationContainer.firstChild);
        } else {
            notificationContainer.appendChild(notification);
        }

        // Show toast notification
        showToastNotification(message);
    }

    function createNotificationElement(message) {
        const element = document.createElement('div');
        element.className = 'notification-item bg-white border-l-4 border-blue-500 p-4 mb-3 rounded-lg shadow-sm';
        element.innerHTML = `
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                        <span class="text-blue-600 font-semibold">${message.user_initials}</span>
                    </div>
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium text-gray-900">${message.user_name}</p>
                    <p class="text-sm text-gray-500 truncate">${message.content}</p>
                    <p class="text-xs text-gray-400 mt-1">Just now</p>
                </div>
            </div>
        `;
        return element;
    }

    function showToastNotification(message) {
        // Create toast notification
        const toast = document.createElement('div');
        toast.className = 'fixed top-4 right-4 bg-white border border-gray-200 rounded-lg shadow-lg p-4 max-w-sm z-50 transform transition-transform duration-300 translate-x-full';
        toast.innerHTML = `
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <span class="text-blue-600 text-xs font-semibold">${message.user_initials}</span>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900">${message.user_name}</p>
                    <p class="text-sm text-gray-500">${message.content}</p>
                </div>
                <button type="button" class="ml-auto flex-shrink-0 text-gray-400 hover:text-gray-500">
                    <span class="sr-only">Close</span>
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        `;

        document.body.appendChild(toast);

        // Animate in
        setTimeout(() => {
            toast.classList.remove('translate-x-full');
        }, 10);

        // Set up close button
        toast.querySelector('button').addEventListener('click', () => {
            toast.classList.add('translate-x-full');
            setTimeout(() => {
                document.body.removeChild(toast);
            }, 300);
        });

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (document.body.contains(toast)) {
                toast.classList.add('translate-x-full');
                setTimeout(() => {
                    if (document.body.contains(toast)) {
                        document.body.removeChild(toast);
                    }
                }, 300);
            }
        }, 5000);
    }

    function updateUnreadCounts(data = null) {
        // Update unread message counts throughout the UI
        if (data) {
            // Update counts from API response
            Object.keys(data).forEach(roomId => {
                const badge = document.querySelector(`.unread-badge[data-room-id="${roomId}"]`);
                if (badge) {
                    if (data[roomId] > 0) {
                        badge.textContent = data[roomId];
                        badge.classList.remove('hidden');
                    } else {
                        badge.classList.add('hidden');
                    }
                }
            });
        }

        // Update total unread count in header
        updateTotalUnreadCount();
    }

    function updateTotalUnreadCount() {
        // Calculate and update total unread count
        const badges = document.querySelectorAll('.unread-badge:not(.hidden)');
        let total = 0;

        badges.forEach(badge => {
            total += parseInt(badge.textContent);
        });

        const totalBadge = document.getElementById('total-unread-count');
        if (totalBadge) {
            if (total > 0) {
                totalBadge.textContent = total;
                totalBadge.classList.remove('hidden');
            } else {
                totalBadge.classList.add('hidden');
            }
        }
    }

    function markMessagesAsRead(roomId) {
        // Send request to mark messages as read
        fetch(`/api/chat/rooms/${roomId}/mark-read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            // Update UI
            const badge = document.querySelector(`.unread-badge[data-room-id="${roomId}"]`);
            if (badge) {
                badge.classList.add('hidden');
            }
            updateTotalUnreadCount();
        });
    }
</script>

<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold text-gray-900">Messages</h1>
                    <span id="total-unread-count" class="hidden ml-2 bg-red-500 text-white text-xs font-bold w-5 h-5 rounded-full flex items-center justify-center"></span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('manager.projects.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Projects
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Notification Toast Container -->
        <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Project Chats -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-primary-600 to-primary-700 px-6 py-4">
                    <h2 class="text-lg font-semibold text-white flex items-center">
                        <i class="fas fa-users mr-3"></i>
                        Project Chats
                        <span class="ml-2 bg-white/20 px-2 py-1 rounded-full text-xs font-medium">
                            {{ $projectRooms->count() }} projects
                        </span>
                    </h2>
                </div>
                <div class="h-[500px] overflow-y-auto">
                    @forelse($projectRooms as $room)
                    <a href="{{ route('manager.chat.project', $room->project) }}"
                       class="chat-room-link block border-b border-gray-100 hover:bg-gray-50 transition-colors duration-150 p-4"
                       data-room-id="{{ $room->id }}">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center text-white">
                                    <i class="fas fa-project-diagram"></i>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between">
                                    <h3 class="text-sm font-semibold text-gray-900 truncate">{{ $room->project->name }}</h3>
                                    @if($room->unreadMessagesCount(auth()->id()) > 0)
                                    <span class="unread-badge bg-red-500 text-white text-xs font-bold w-5 h-5 rounded-full flex items-center justify-center ml-2 flex-shrink-0"
                                          data-room-id="{{ $room->id }}">
                                        {{ $room->unreadMessagesCount(auth()->id()) }}
                                    </span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Project Discussion</p>

                                @if($room->messages->count() > 0)
                                <div class="mt-3 pt-3 border-t border-gray-100">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-6 h-6 bg-primary-100 rounded-full flex items-center justify-center text-primary-700 text-xs font-bold flex-shrink-0">
                                            {{ strtoupper(substr($room->messages->first()->user->name, 0, 1)) }}
                                        </div>
                                        <p class="text-sm text-gray-600 truncate flex-1">
                                            <span class="font-medium text-gray-900">{{ $room->messages->first()->user->name }}</span>:
                                            {{ Str::limit($room->messages->first()->message, 25) }}
                                        </p>
                                    </div>
                                    <p class="text-xs text-gray-400 mt-1">
                                        {{ $room->messages->first()->created_at->diffForHumans() }}
                                    </p>
                                </div>
                                @else
                                <div class="mt-3 pt-3 border-t border-gray-100">
                                    <p class="text-sm text-gray-400 italic">No messages yet</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </a>
                    @empty
                    <div class="text-center py-12">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-users text-gray-400 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">No Project Chats</h3>
                        <p class="text-gray-500 text-sm mb-4">
                            @if(auth()->user()->role === 'admin')
                            You don't have any projects assigned as manager yet.
                            @else
                            No project chats available.
                            @endif
                        </p>
                        <a href="{{ route('manager.projects.index') }}"
                           class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-md text-sm font-medium transition-colors">
                            <i class="fas fa-plus mr-2"></i>
                            Create Project
                        </a>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Direct Messages -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-green-600 to-emerald-700 px-6 py-4">
                    <h2 class="text-lg font-semibold text-white flex items-center">
                        <i class="fas fa-comment-dots mr-3"></i>
                        Direct Messages
                        <span class="ml-2 bg-white/20 px-2 py-1 rounded-full text-xs font-medium">
                            {{ $directRooms->count() }} chats
                        </span>
                    </h2>
                </div>
                <div class="h-[500px] overflow-y-auto">
                    @forelse($directRooms as $room)
                    @php
                        $otherUser = $room->participants->where('user_id', '!=', auth()->id())->first()->user;
                    @endphp
                    <a href="{{ route('manager.chat.direct', $otherUser) }}"
                       class="chat-room-link block border-b border-gray-100 hover:bg-gray-50 transition-colors duration-150 p-4"
                       data-room-id="{{ $room->id }}">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center text-white">
                                    <span class="font-semibold">{{ strtoupper(substr($otherUser->name, 0, 1)) }}</span>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h3 class="text-sm font-semibold text-gray-900">{{ $otherUser->name }}</h3>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 mt-1">
                                            {{ ucfirst($otherUser->role) }}
                                        </span>
                                    </div>
                                    @if($room->unreadMessagesCount(auth()->id()) > 0)
                                    <span class="unread-badge bg-red-500 text-white text-xs font-bold w-5 h-5 rounded-full flex items-center justify-center ml-2 flex-shrink-0"
                                          data-room-id="{{ $room->id }}">
                                        {{ $room->unreadMessagesCount(auth()->id()) }}
                                    </span>
                                    @endif
                                </div>

                                @if($room->messages->count() > 0)
                                <div class="mt-3 pt-3 border-t border-gray-100">
                                    <p class="text-sm text-gray-600 truncate">
                                        {{ Str::limit($room->messages->first()->message, 30) }}
                                    </p>
                                    <p class="text-xs text-gray-400 mt-1">
                                        {{ $room->messages->first()->created_at->diffForHumans() }}
                                    </p>
                                </div>
                                @else
                                <div class="mt-3 pt-3 border-t border-gray-100">
                                    <p class="text-sm text-gray-400 italic">No messages yet</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </a>
                    @empty
                    <div class="text-center py-12">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-comment text-gray-400 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">No Direct Messages</h3>
                        <p class="text-gray-500 text-sm">Start a conversation with team members</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Start New Chat -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-orange-600 to-amber-700 px-6 py-4">
                    <h2 class="text-lg font-semibold text-white flex items-center">
                        <i class="fas fa-plus mr-3"></i>
                        Start New Chat
                    </h2>
                </div>
                <div class="p-6 h-[500px] overflow-y-auto">
                    <p class="text-sm text-gray-600 mb-4 flex items-center">
                        <i class="fas fa-info-circle text-orange-500 mr-2"></i>
                        Start a conversation with team members
                    </p>
                    <div class="space-y-3">
                        @foreach($availableUsers as $user)
                        <a href="{{ route('manager.chat.direct', $user) }}"
                           class="flex items-center space-x-3 p-3 rounded-lg border border-gray-200 hover:border-orange-300 hover:bg-orange-50 transition-all duration-150">
                            <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-orange-600 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900">{{ $user->name }}</p>
                                <p class="text-xs text-gray-500 capitalize">{{ $user->role }}</p>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400"></i>
                        </a>
                        @endforeach
                    </div>

                    @if($availableUsers->count() === 0)
                    <div class="text-center py-12">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-user-plus text-gray-400 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">No Users Available</h3>
                        <p class="text-gray-500 text-sm">All team members are already in your chats</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Custom scrollbar */
    .overflow-y-auto {
        scrollbar-width: thin;
        scrollbar-color: #cbd5e1 #f1f5f9;
    }

    .overflow-y-auto::-webkit-scrollbar {
        width: 6px;
    }

    .overflow-y-auto::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 3px;
    }

    .overflow-y-auto::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }

    .overflow-y-auto::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* Smooth transitions */
    .transition-colors {
        transition-property: background-color, border-color, color, fill, stroke;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 150ms;
    }

    .transition-all {
        transition-property: all;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 150ms;
    }

    .transform {
        transition-property: transform;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 300ms;
    }
</style>
@endsection
