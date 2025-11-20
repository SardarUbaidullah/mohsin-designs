<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with {{ $user->name }} - CRM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <style>
        .animate-fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .bg-chat-sent {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }

        .bg-chat-received {
            background: white;
        }
    </style>
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
    </script>
</head>
<body class="bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 py-6">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-4">
                <a href="{{ route('manager.chat.index') }}"
                   class="text-gray-500 hover:text-primary-600 transition-colors duration-200">
                    <i class="fas fa-arrow-left text-xl"></i>
                </a>
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-green-600 rounded-full flex items-center justify-center text-white font-semibold text-lg">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1>
                        <p class="text-gray-600 flex items-center">
                            <span class="capitalize">{{ $user->role }}</span>
                            <span class="w-2 h-2 bg-green-500 rounded-full ml-2 animate-pulse" title="Online"></span>
                        </p>
                    </div>
                </div>
            </div>

        </div>

        <!-- Connection Status Indicator -->
        <div id="connection-status" class="fixed bottom-4 left-4 bg-green-500 text-white px-3 py-2 rounded-lg shadow-lg z-40">
            <div class="flex items-center space-x-2">
                <i class="fas fa-wifi"></i>
                <span class="text-sm font-medium" id="connection-text">Live Connected</span>
            </div>
        </div>

        <!-- Chat Container -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col h-[600px]">
            <!-- Chat Header -->
            <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center text-white">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-white">Direct Message</h2>
                            <p class="text-green-100 text-sm">Private conversation with {{ $user->name }}</p>
                        </div>
                    </div>
                    <div class="text-green-100 text-sm">
                        <i class="fas fa-circle text-xs mr-1"></i>
                        <span id="connection-status-text">Active now</span>
                    </div>
                </div>
            </div>

            <!-- Messages Area -->
            <div class="flex-1 p-6 overflow-y-auto bg-gray-50" id="messages-container">
                <div class="space-y-4" id="messages-list">
                    @foreach($messages->reverse() as $message)
                    <div class="flex items-start space-x-3 {{ $message->user_id === auth()->id() ? 'flex-row-reverse space-x-reverse' : '' }}">
                        <div class="w-10 h-10 {{ $message->user_id === auth()->id() ? 'bg-gradient-to-br from-green-500 to-emerald-600' : 'bg-gradient-to-br from-primary-500 to-blue-600' }} rounded-full flex items-center justify-center text-white text-sm font-semibold flex-shrink-0 shadow-lg transition-transform duration-200 hover:scale-105 cursor-pointer"
                             title="{{ $message->user->name }}">
                            {{ strtoupper(substr($message->user->name, 0, 1)) }}
                        </div>
                        <div class="flex-1 max-w-md {{ $message->user_id === auth()->id() ? 'text-right' : '' }}">
                            <div class="inline-block {{ $message->user_id === auth()->id() ? 'bg-chat-sent text-white' : 'bg-white' }} rounded-2xl px-4 py-3 shadow-lg border border-gray-200 hover:shadow-xl transition-all duration-200">
                                <div class="flex items-center space-x-2 mb-2 {{ $message->user_id === auth()->id() ? 'flex-row-reverse space-x-reverse' : '' }}">
                                    <span class="text-sm font-bold {{ $message->user_id === auth()->id() ? 'text-green-100' : 'text-primary-700' }}">{{ $message->user->name }}</span>
                                    <span class="text-xs {{ $message->user_id === auth()->id() ? 'text-green-100 bg-green-600' : 'text-gray-500 bg-gray-100' }} px-2 py-1 rounded-full">{{ $message->formatted_time }}</span>
                                </div>
                                <p class="{{ $message->user_id === auth()->id() ? 'text-white' : 'text-gray-800' }} text-sm leading-relaxed">{{ $message->message }}</p>
                                @if($message->attachment)
                                <div class="mt-3">
                                    <a href="{{ Storage::url($message->attachment) }}"
                                       target="_blank"
                                       class="inline-flex items-center space-x-2 text-xs {{ $message->user_id === auth()->id() ? 'text-white bg-green-600 hover:bg-green-700 border-green-500' : 'text-primary-700 hover:text-primary-800 bg-primary-100 hover:bg-primary-200 border-primary-200' }} rounded-lg px-3 py-2 transition-all duration-200 hover:shadow-md border">
                                        <i class="fas fa-paperclip"></i>
                                        <span class="font-medium">{{ $message->attachment_name }}</span>
                                        <i class="fas fa-external-link-alt text-xs"></i>
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Message Input -->
            <div class="border-t border-gray-200 p-4 bg-white">
                <form id="message-form" class="flex space-x-3">
                    @csrf
                    <div class="flex-1">
                        <textarea
                            id="message-input"
                            name="message"
                            rows="1"
                            placeholder="Type your message..."
                            class="w-full px-4 py-3 border border-gray-300 rounded-2xl resize-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                            required
                        ></textarea>
                    </div>
                    <div class="flex space-x-2 " >
                        <label for="attachment" class="cursor-pointer">
                            <input type="file" id="attachment" name="attachment" class="hidden" >
                            <span class="w-12 h-12 bg-gray-100 hover:bg-gray-200 rounded-xl flex items-center justify-center text-gray-600 transition-colors duration-200" style="display: none;">
                                <i class="fas fa-paperclip"></i>
                            </span>
                        </label>
                        <button
                            type="submit"
                            id="send-button"
                            class="w-12 h-12 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 rounded-xl flex items-center justify-center text-white transition-all duration-200 shadow-md hover:shadow-lg"
                        >
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // ⭐⭐ CRITICAL FIX: Enhanced real-time chat that works in background/inactive windows

        // Auto-scroll to bottom
        const messagesContainer = document.getElementById('messages-container');
        if (messagesContainer) {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }

        // Auto-resize textarea
        const textarea = document.getElementById('message-input');
        if (textarea) {
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = Math.min(this.scrollHeight, 120) + 'px';
            });
        }

        // Global variables for real-time
        let lastMessageId = {{ $messages->last() ? $messages->last()->id : 0 }};
        let isPolling = false;
        const currentUserId = {{ auth()->id() }};
        let processedMessageIds = new Set();
        let pusherChannel = null;

        // Send message function - YOUR EXACT WORKING VERSION
        async function sendMessage() {
            const messageInput = document.getElementById('message-input');
            const attachmentInput = document.getElementById('attachment');
            const sendButton = document.getElementById('send-button');

            const messageText = messageInput.value.trim();

            if (!messageText && !attachmentInput.files[0]) {
                return;
            }

            // Show sending state
            const originalHtml = sendButton.innerHTML;
            sendButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            sendButton.disabled = true;

            const formData = new FormData();
            formData.append('message', messageText);
            formData.append('_token', '{{ csrf_token() }}');

            if (attachmentInput.files[0]) {
                formData.append('attachment', attachmentInput.files[0]);
            }

            try {
                console.log('📤 Sending message...');

                const response = await fetch('{{ route('manager.chat.send', $chatRoom) }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                });

                const data = await response.json();
                console.log('📥 Server response:', data);

                if (data.success) {
                    // ⭐⭐ IMMEDIATELY add message to UI
                    console.log('✅ Adding message to UI immediately:', data.message_data);
                    addMessageToChat(data.message_data);

                    // Clear input fields
                    messageInput.value = '';
                    attachmentInput.value = '';
                    messageInput.style.height = 'auto';

                    // Show success state
                    sendButton.innerHTML = '<i class="fas fa-check"></i>';
                    setTimeout(() => {
                        sendButton.innerHTML = '<i class="fas fa-paper-plane"></i>';
                        sendButton.disabled = false;
                    }, 1000);

                } else {
                    throw new Error(data.error || 'Failed to send message');
                }
            } catch (error) {
                console.error('❌ Error sending message:', error);
                sendButton.innerHTML = '<i class="fas fa-exclamation-triangle"></i>';
                setTimeout(() => {
                    sendButton.innerHTML = originalHtml;
                    sendButton.disabled = false;
                }, 2000);
                showNotification('Failed to send message: ' + error.message, 'error');
            }
        }

        // Event listeners for form submission
        const messageForm = document.getElementById('message-form');
        if (messageForm) {
            messageForm.addEventListener('submit', function(e) {
                e.preventDefault();
                e.stopPropagation();
                sendMessage();
                return false;
            });
        }

        // Enter key handler
        if (textarea) {
            textarea.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendMessage();
                }
            });
        }

        // ⭐⭐ ENHANCED REAL-TIME INITIALIZATION
        function initializeRealTime() {
            console.log('🚀 Initializing real-time direct chat...');

            if (typeof Pusher !== 'undefined') {
                console.log('✅ Pusher is available, setting up real-time...');

                // Enable Pusher logging
                Pusher.logToConsole = true;

                const pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
                    cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
                    encrypted: true,
                    forceTLS: true,
                    // ⭐⭐ CRITICAL: Force background connection
                    activityTimeout: 120000, // 2 minutes
                    pongTimeout: 30000, // 30 seconds
                });

                // Subscribe to channel
                pusherChannel = pusher.subscribe('chat.room.{{ $chatRoom->id }}');

                // ⭐⭐ CRITICAL FIX: Listen for messages even in background/inactive windows
                pusherChannel.bind('message.sent', function(data) {
                    console.log('💬 New message received via Pusher (background ok):', data);

                    // Process message regardless of window focus state
                    if (data.message && data.message.user_id !== currentUserId) {
                        const messageId = data.message.id;
                        if (!processedMessageIds.has(messageId)) {
                            processedMessageIds.add(messageId);
                            console.log('✅ Adding message from background:', data.message.user.name);
                            addMessageToChat(data.message);

                            // Play sound and show notification even in background
                            if (!document.hasFocus()) {
                                playNotificationSound();
                                showBackgroundNotification(data.message);
                            }
                        } else {
                            console.log('🔄 Skipping duplicate message:', messageId);
                        }
                    }
                });

                // Connection events
                pusherChannel.bind('pusher:subscription_succeeded', function() {
                    console.log('✅ Successfully subscribed to Pusher channel');
                    updateConnectionStatus('connected');
                });

                pusherChannel.bind('pusher:subscription_error', function(status) {
                    console.error('❌ Pusher subscription error:', status);
                    updateConnectionStatus('error');
                    setupPollingFallback();
                });

                // ⭐⭐ ADDED: Keep connection alive in background
                setInterval(() => {
                    if (pusherChannel && pusher.connection.state === 'connected') {
                        // This keeps the connection active
                        console.log('❤️ Keeping Pusher connection alive in background');
                    }
                }, 25000); // Every 25 seconds

                console.log('📡 Pusher setup complete - background messages enabled');

            } else {
                console.error('❌ Pusher not available');
                setupPollingFallback();
            }

            // ⭐⭐ CRITICAL: Start background polling as backup
            startBackgroundPolling();
        }

        // ⭐⭐ NEW: Background polling that works even when window is inactive
        function startBackgroundPolling() {
            console.log('🔄 Starting background polling...');

            // Poll every 3 seconds regardless of window state
            setInterval(() => {
                pollForNewMessages();
            }, 3000);

            // Immediate poll
            setTimeout(() => pollForNewMessages(), 1000);
        }

        async function pollForNewMessages() {
            try {
                const response = await fetch('{{ route('manager.chat.messages', $chatRoom) }}?last_id=' + lastMessageId + '&t=' + Date.now());
                const data = await response.json();

                if (data.data && data.data.length > 0) {
                    const newMessages = data.data.filter(msg => msg.id > lastMessageId);

                    if (newMessages.length > 0) {
                        console.log('🔄 Background polling found', newMessages.length, 'new messages');

                        newMessages.forEach(message => {
                            if (message.user_id !== currentUserId && !processedMessageIds.has(message.id)) {
                                console.log('✅ Adding message via background polling:', message.user.name);
                                addMessageToChat(message);
                                processedMessageIds.add(message.id);

                                // Notify even in background
                                if (!document.hasFocus()) {
                                    playNotificationSound();
                                    showBackgroundNotification(message);
                                }
                            }
                            lastMessageId = Math.max(lastMessageId, message.id);
                        });
                    }
                }
            } catch (error) {
                console.error('Background polling error:', error);
            }
        }

        // ⭐⭐ NEW: Show notification for background messages
        function showBackgroundNotification(message) {
            // Show desktop notification
            if ("Notification" in window && Notification.permission === "granted") {
                const notification = new Notification(`New message from ${message.user.name}`, {
                    body: message.message.length > 50 ? message.message.substring(0, 50) + '...' : message.message,
                    icon: '/favicon.ico',
                    tag: 'chat-message',
                    requireInteraction: false
                });

                notification.onclick = function() {
                    window.focus();
                    this.close();
                };
            }

            // Also show in-page notification
            showNotification(`New message from ${message.user.name}`, 'info');
        }

        // ⭐⭐ YOUR EXACT ORIGINAL addMessageToChat FUNCTION
        function addMessageToChat(message) {
            const isOwnMessage = message.user_id === currentUserId;

            const messageHtml = `
                <div class="flex items-start space-x-3 animate-fade-in ${isOwnMessage ? 'flex-row-reverse space-x-reverse' : ''}">
                    <div class="w-10 h-10 ${isOwnMessage ? 'bg-gradient-to-br from-green-500 to-emerald-600' : 'bg-gradient-to-br from-primary-500 to-blue-600'} rounded-full flex items-center justify-center text-white text-sm font-semibold flex-shrink-0 shadow-lg transition-transform duration-200 hover:scale-105 cursor-pointer"
                         title="${message.user.name}">
                        ${message.user.name.charAt(0).toUpperCase()}
                    </div>
                    <div class="flex-1 max-w-md ${isOwnMessage ? 'text-right' : ''}">
                        <div class="inline-block ${isOwnMessage ? 'bg-chat-sent text-white' : 'bg-white'} rounded-2xl px-4 py-3 shadow-lg border border-gray-200 hover:shadow-xl transition-all duration-200">
                            <div class="flex items-center space-x-2 mb-2 ${isOwnMessage ? 'flex-row-reverse space-x-reverse' : ''}">
                                <span class="text-sm font-bold ${isOwnMessage ? 'text-green-100' : 'text-primary-700'}">${message.user.name}</span>
                                <span class="text-xs ${isOwnMessage ? 'text-green-100 bg-green-600' : 'text-gray-500 bg-gray-100'} px-2 py-1 rounded-full">${formatTime(message.created_at)}</span>
                            </div>
                            <p class="${isOwnMessage ? 'text-white' : 'text-gray-800'} text-sm leading-relaxed">${message.message}</p>
                            ${message.attachment ? `
                                <div class="mt-3">
                                    <a href="/storage/${message.attachment}" target="_blank"
                                       class="inline-flex items-center space-x-2 text-xs ${isOwnMessage ? 'text-white bg-green-600 hover:bg-green-700 border-green-500' : 'text-primary-700 hover:text-primary-800 bg-primary-100 hover:bg-primary-200 border-primary-200'} rounded-lg px-3 py-2 transition-all duration-200 hover:shadow-md border">
                                        <i class="fas fa-paperclip"></i>
                                        <span class="font-medium">${message.attachment_name}</span>
                                        <i class="fas fa-external-link-alt text-xs"></i>
                                    </a>
                                </div>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `;

            const messagesList = document.getElementById('messages-list');
            if (messagesList) {
                messagesList.insertAdjacentHTML('beforeend', messageHtml);

                // Auto-scroll to bottom
                if (messagesContainer) {
                    messagesContainer.scrollTop = messagesContainer.scrollHeight;
                }
            }

            if (!isOwnMessage) {
                playNotificationSound();
            }
        }

        // Format time function
        function formatTime(dateString) {
            try {
                const date = new Date(dateString);
                return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            } catch (e) {
                return 'Just now';
            }
        }

        // Helper functions
        function playNotificationSound() {
            try {
                const audio = new Audio('data:audio/wav;base64,UklGRigAAABXQVZFZm10IBAAAAABAAEARKwAAIhYAQACABAAZGF0YQQAAAAAAA==');
                audio.volume = 0.3;
                audio.play().catch(e => console.log('Sound play failed'));
            } catch (e) {}
        }

        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            const bgColor = type === 'error' ? 'bg-red-500' : 'bg-green-500';

            notification.className = `fixed top-4 right-4 ${bgColor} text-white px-4 py-2 rounded-lg shadow-lg z-50 animate-fade-in`;
            notification.innerHTML = `
                <div class="flex items-center space-x-2">
                    <i class="fas ${type === 'error' ? 'fa-exclamation-triangle' : 'fa-bell'}"></i>
                    <span class="text-sm">${message}</span>
                </div>
            `;
            document.body.appendChild(notification);
            setTimeout(() => notification.remove(), 3000);
        }

        function updateConnectionStatus(status) {
            const statusConfig = {
                'connected': { text: 'Live Connected', color: 'bg-green-500', icon: 'fa-wifi' },
                'polling': { text: 'Polling Active', color: 'bg-blue-500', icon: 'fa-sync' },
                'error': { text: 'Connection Error', color: 'bg-red-500', icon: 'fa-exclamation-triangle' }
            };

            const config = statusConfig[status] || statusConfig.error;
            const statusElement = document.getElementById('connection-status');
            const connectionText = document.getElementById('connection-text');
            const statusText = document.getElementById('connection-status-text');

            if (statusElement) {
                statusElement.className = `fixed bottom-4 left-4 ${config.color} text-white px-3 py-2 rounded-lg shadow-lg z-40`;
                statusElement.innerHTML = `
                    <div class="flex items-center space-x-2">
                        <i class="fas ${config.icon}"></i>
                        <span class="text-sm font-medium">${config.text}</span>
                    </div>
                `;
            }

            if (connectionText) {
                connectionText.textContent = config.text;
            }

            if (statusText) {
                statusText.textContent = config.text === 'Live Connected' ? 'Active now' : config.text;
            }
        }

        function setupPollingFallback() {
            console.log('🔄 Using polling fallback');
            updateConnectionStatus('polling');
            isPolling = true;
        }

        // Request notification permission on page load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 Direct chat page loaded - initializing real-time...');

            // Request notification permission
            if ("Notification" in window && Notification.permission === "default") {
                Notification.requestPermission();
            }

            initializeRealTime();

            if (textarea) {
                textarea.focus();
            }

            console.log('✅ Real-time direct chat ready - messages will work in background/inactive windows');
        });

        // Keep polling active regardless of visibility
        document.addEventListener('visibilitychange', function() {
            console.log('👀 Visibility changed:', document.hidden ? 'hidden' : 'visible');
            // Background polling continues regardless
        });





        class ChatVirtualScroll {
    constructor(containerId, chatRoomId) {
        this.container = document.getElementById(containerId);
        this.chatRoomId = chatRoomId;
        this.messages = [];
        this.visibleMessages = [];
        this.pageSize = 50;
        this.currentPage = 1;
        this.isLoading = false;

        this.init();
    }

    init() {
        this.setupContainer();
        this.loadInitialMessages();
        this.setupScrollListener();
    }

    setupContainer() {
        this.container.innerHTML = `
            <div class="messages-viewport" style="height: 100%; overflow-y: auto;">
                <div class="messages-spacer" style="height: 0px;"></div>
                <div class="messages-content"></div>
                <div class="messages-spacer" style="height: 0px;"></div>
            </div>
        `;

        this.viewport = this.container.querySelector('.messages-viewport');
        this.content = this.container.querySelector('.messages-content');
        this.spacerTop = this.container.querySelector('.messages-spacer:first-child');
        this.spacerBottom = this.container.querySelector('.messages-spacer:last-child');
    }

    async loadInitialMessages() {
        await this.loadMessages(1);
        this.renderVisibleMessages();
    }

    async loadMessages(page) {
        if (this.isLoading) return;

        this.isLoading = true;

        try {
            const response = await fetch(`/manager/chat/${this.chatRoomId}/messages?page=${page}`);
            const data = await response.json();

            if (page === 1) {
                this.messages = data.data.reverse(); // Oldest first
            } else {
                this.messages = [...data.data.reverse(), ...this.messages];
            }

            this.currentPage = data.current_page;
            this.totalPages = data.last_page;

            this.updateSpacers();

        } catch (error) {
            console.error('Load messages error:', error);
        } finally {
            this.isLoading = false;
        }
    }

    renderVisibleMessages() {
        const viewportHeight = this.viewport.clientHeight;
        const scrollTop = this.viewport.scrollTop;

        // Calculate which messages are visible
        const startIndex = Math.floor(scrollTop / 100); // Approximate message height
        const endIndex = Math.min(startIndex + Math.ceil(viewportHeight / 100) + 5, this.messages.length);

        this.visibleMessages = this.messages.slice(startIndex, endIndex);

        // Render only visible messages
        this.content.innerHTML = this.visibleMessages.map(message =>
            this.createMessageHTML(message)
        ).join('');

        this.spacerTop.style.height = (startIndex * 100) + 'px';
        this.spacerBottom.style.height = ((this.messages.length - endIndex) * 100) + 'px';
    }

    createMessageHTML(message) {
        return `
            <div class="message ${message.user_id === currentUserId ? 'own-message' : 'other-message'}"
                 data-message-id="${message.id}"
                 style="height: 100px; margin: 5px 0;">
                <div class="message-header">
                    <strong>${message.user.name}</strong>
                    <span class="message-time">${this.formatTime(message.created_at)}</span>
                </div>
                <div class="message-content">
                    ${this.escapeHtml(message.message)}
                    ${message.attachment ? `
                        <div class="attachment">
                            <a href="/storage/${message.attachment}" target="_blank">
                                📎 ${message.attachment_name}
                            </a>
                        </div>
                    ` : ''}
                </div>
            </div>
        `;
    }

    setupScrollListener() {
        this.viewport.addEventListener('scroll', () => {
            this.renderVisibleMessages();

            // Load more messages when near top
            if (this.viewport.scrollTop < 500 && this.currentPage < this.totalPages && !this.isLoading) {
                this.loadMessages(this.currentPage + 1);
            }
        });
    }

    updateSpacers() {
        const totalHeight = this.messages.length * 100;
        this.spacerBottom.style.height = (totalHeight - (this.visibleMessages.length * 100)) + 'px';
    }

    addNewMessage(message) {
        this.messages.push(message);
        this.updateSpacers();
        this.renderVisibleMessages();
        this.viewport.scrollTop = this.viewport.scrollHeight;
    }

    formatTime(timestamp) {
        return new Date(timestamp).toLocaleTimeString();
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Initialize virtual scroll
let chatVirtualScroll;

function initializeChat() {
    chatVirtualScroll = new ChatVirtualScroll('messages-container', chatRoomId);
}
    </script>
</body>
</html>
