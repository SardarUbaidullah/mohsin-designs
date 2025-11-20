<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $project->name }} Chat - CRM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>

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
                        },
                        chat: {
                            sent: '#dcf8c6',
                            received: '#ffffff'
                        }
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.3s ease-in-out',
                        'slide-in': 'slideIn 0.3s ease-out'
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0', transform: 'translateY(10px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' }
                        },
                        slideIn: {
                            '0%': { opacity: '0', transform: 'translateX(-10px)' },
                            '100%': { opacity: '1', transform: 'translateX(0)' }
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .scrollbar-thin {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 #f1f5f9;
        }
        .scrollbar-thin::-webkit-scrollbar {
            width: 6px;
        }
        .scrollbar-thin::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }
        .scrollbar-thin::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        .scrollbar-thin::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* WhatsApp style background */
        .whatsapp-bg {
            background-color: #e5ddd5;
            background-image: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%239C92AC' fill-opacity='0.1' fill-rule='evenodd'/%3E%3C/svg%3E");
        }
    </style>
</head>
<body class="whatsapp-bg">
    <div class="max-w-7xl mx-auto px-4 py-6">
         <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
            <div class="flex items-center space-x-4 mb-4 md:mb-0">
                <a href="{{ route('manager.chat.index') }}"
                   class="group bg-white hover:bg-primary-50 border border-gray-200 rounded-xl p-3 transition-all duration-200 shadow-sm hover:shadow-md">
                    <i class="fas fa-arrow-left text-gray-600 group-hover:text-primary-600 text-lg transition-colors duration-200"></i>
                </a>
                <div class="flex items-center space-x-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-primary-500 to-blue-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-comments text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $project->name }}</h1>
                        <p class="text-gray-600 flex items-center">
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium mr-2">
                                <i class="fas fa-circle text-xs mr-1"></i>
                                Active Chat
                            </span>
                            Project Discussion
                        </p>
                    </div>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('manager.projects.show', $project) }}"
                   class="group bg-white hover:bg-primary-600 border border-primary-200 text-primary-700 hover:text-white px-5 py-2.5 rounded-xl font-medium transition-all duration-200 flex items-center shadow-sm hover:shadow-lg">
                    <i class="fas fa-external-link-alt mr-2 group-hover:scale-110 transition-transform duration-200"></i>
                    View Project
                </a>
                <div class="flex items-center space-x-3 bg-white rounded-xl px-4 py-2 shadow-sm border border-gray-200">
                    <div class="flex -space-x-3">
                        @foreach($chatRoom->participants->take(4) as $participant)
                        <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-blue-600 rounded-full flex items-center justify-center text-white text-sm font-semibold border-2 border-white shadow-lg transition-transform duration-200 hover:scale-110 cursor-pointer"
                             title="{{ $participant->user->name }} ({{ $participant->user->role }})">
                            {{ strtoupper(substr($participant->user->name, 0, 1)) }}
                        </div>
                        @endforeach
                    </div>
                    @if($chatRoom->participants->count() > 4)
                    <div class="text-sm text-gray-500 font-medium bg-gray-100 px-3 py-1 rounded-full">
                        +{{ $chatRoom->participants->count() - 4 }} more
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-4 gap-8">
            <!-- Chat Sidebar -->
            <div class="xl:col-span-1 space-y-6">
                <!-- Project Info Card -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6 transition-all duration-200 hover:shadow-xl">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center text-white">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3 class="font-bold text-gray-900 text-lg">Project Overview</h3>
                    </div>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-flag text-blue-500"></i>
                                <span class="text-sm font-medium text-gray-700">Status</span>
                            </div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                @if($project->status == 'completed') bg-green-100 text-green-800
                                @elseif($project->status == 'in_progress') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800 @endif">
                                <i class="fas fa-circle text-xs mr-1.5"></i>
                                {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-calendar-day text-purple-500"></i>
                                <span class="text-sm font-medium text-gray-700">Due Date</span>
                            </div>
                            <span class="text-sm font-semibold text-gray-900">
                                {{ $project->due_date ? \Carbon\Carbon::parse($project->due_date)->format('M d, Y') : 'Not set' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Team Members Card -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6 transition-all duration-200 hover:shadow-xl">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center text-white">
                                <i class="fas fa-users"></i>
                            </div>
                            <h3 class="font-bold text-gray-900 text-lg">Team Members</h3>
                        </div>
                        <span class="bg-primary-100 text-primary-800 px-3 py-1 rounded-full text-sm font-semibold">
                            {{ $chatRoom->participants->count() }}
                        </span>
                    </div>
                    <div class="space-y-3 max-h-80 overflow-y-auto scrollbar-thin">
                        @foreach($chatRoom->participants as $participant)
                        <div class="flex items-center space-x-3 p-3 rounded-xl transition-all duration-200 hover:bg-primary-50 group border border-transparent hover:border-primary-200">
                            <div class="relative">
                                <div class="w-12 h-12 bg-gradient-to-br from-primary-500 to-blue-600 rounded-full flex items-center justify-center text-white text-sm font-semibold shadow-md group-hover:scale-105 transition-transform duration-200">
                                    {{ strtoupper(substr($participant->user->name, 0, 1)) }}
                                </div>
                                <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 border-2 border-white rounded-full"></div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center space-x-2">
                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $participant->user->name }}</p>
                                    @if($participant->user->id === auth()->id())
                                    <span class="bg-primary-600 text-white px-2 py-0.5 rounded-full text-xs font-medium">You</span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-500 capitalize bg-gray-100 px-2 py-1 rounded-full inline-block mt-1">
                                    {{ $participant->user->role }}
                                </p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Chat Main Area -->
            <div class="xl:col-span-3">
                <div class="bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden flex flex-col h-[700px] transform transition-all duration-200 hover:shadow-2xl">
                    <!-- Chat Header -->
                    <div class="bg-gradient-to-r from-primary-600 to-blue-700 px-6 py-5 shadow-lg">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center text-white shadow-lg">
                                    <i class="fas fa-comments text-lg"></i>
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold text-white">Project Discussion</h2>
                                    <p class="text-blue-100 text-sm flex items-center">
                                        <i class="fas fa-users mr-1.5"></i>
                                        <span id="online-count">{{ $chatRoom->participants->count() }}</span> participants in chat
                                        <div class="flex items-center space-x-2 ml-4">
                                            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                                            <span class="text-blue-100 text-xs font-medium" id="connection-text">Live Connected</span>
                                        </div>
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3" style="display:none;">
                                <button class="w-10 h-10 bg-white bg-opacity-20 hover:bg-opacity-30 rounded-xl flex items-center justify-center text-white transition-all duration-200 hover:scale-110 shadow-md">
                                    <i class="fas fa-phone-alt text-sm"></i>
                                </button>
                                <button class="w-10 h-10 bg-white bg-opacity-20 hover:bg-opacity-30 rounded-xl flex items-center justify-center text-white transition-all duration-200 hover:scale-110 shadow-md">
                                    <i class="fas fa-video text-sm"></i>
                                </button>
                                <button class="w-10 h-10 bg-white bg-opacity-20 hover:bg-opacity-30 rounded-xl flex items-center justify-center text-white transition-all duration-200 hover:scale-110 shadow-md">
                                    <i class="fas fa-ellipsis-v text-sm"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Messages Area -->
                    <div class="flex-1 p-6 overflow-y-auto bg-gradient-to-b from-gray-50 to-blue-50 scrollbar-thin" id="messages-container">
                        <div class="space-y-4" id="messages-list">
                            <!-- YOUR EXACT ORIGINAL MESSAGE DISPLAY LOGIC -->
                            @foreach($messages as $message)
                            <div class="flex items-start space-x-3 animate-fade-in {{ $message->user_id === auth()->id() ? 'flex-row-reverse space-x-reverse' : '' }}">
                                <div class="w-10 h-10 {{ $message->user_id === auth()->id() ? 'bg-gradient-to-br from-green-500 to-emerald-600' : 'bg-gradient-to-br from-primary-500 to-blue-600' }} rounded-full flex items-center justify-center text-white text-sm font-semibold flex-shrink-0 shadow-lg transition-transform duration-200 hover:scale-105 cursor-pointer"
                                     title="{{ $message->user->name }} ({{ $message->user->role }})">
                                    {{ strtoupper(substr($message->user->name, 0, 1)) }}
                                </div>
                                <div class="flex-1 max-w-md {{ $message->user_id === auth()->id() ? 'text-right' : '' }}">
                                    <div class="inline-block {{ $message->user_id === auth()->id() ? 'bg-chat-sent' : 'bg-chat-received' }} rounded-2xl px-4 py-3 shadow-lg border border-gray-200 hover:shadow-xl transition-all duration-200">
                                        <div class="flex items-center space-x-2 mb-2 {{ $message->user_id === auth()->id() ? 'flex-row-reverse space-x-reverse' : '' }}">
                                            <span class="text-sm font-bold {{ $message->user_id === auth()->id() ? 'text-green-800' : 'text-primary-700' }}">{{ $message->user->name }}</span>
                                            <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">{{ $message->formatted_time }}</span>
                                        </div>
                                        <p class="text-gray-800 text-sm leading-relaxed">{{ $message->message }}</p>
                                        @if($message->attachment)
                                        <div class="mt-3">
                                            <a href="{{ Storage::url($message->attachment) }}"
                                               target="_blank"
                                               class="inline-flex items-center space-x-2 text-xs text-primary-700 hover:text-primary-800 bg-primary-100 hover:bg-primary-200 rounded-lg px-3 py-2 transition-all duration-200 hover:shadow-md border border-primary-200">
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
                    <div class="border-t border-gray-200 bg-white p-6 shadow-lg">
                        <form id="message-form" class="flex space-x-4 items-end">
                            @csrf
                            <div class="flex-1 relative">
                                <textarea
                                    id="message-input"
                                    name="message"
                                    rows="1"
                                    placeholder="Type your message... (Press Enter to send, Shift+Enter for new line)"
                                    class="w-full px-5 py-4 border-2 border-gray-300 rounded-2xl resize-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 shadow-sm focus:shadow-md bg-gray-50 focus:bg-white"
                                    required
                                ></textarea>
                                <div class="absolute right-3 bottom-3 flex space-x-2">
                                    <span class="text-xs text-gray-400 bg-white px-2 py-1 rounded-full border">
                                        Enter ⏎
                                    </span>
                                </div>
                            </div>
                            <div class="flex space-x-3">
                                <label for="attachment" class="cursor-pointer group">
                                    <input type="file" id="attachment" name="attachment" class="hidden">
                                    <span style="display: none" class="w-14 h-14 bg-gradient-to-br from-purple-500 to-pink-600 group-hover:from-purple-600 group-hover:to-pink-700 rounded-xl flex items-center justify-center text-white transition-all duration-200 shadow-lg group-hover:shadow-xl group-hover:scale-105">
                                        <i class="fas fa-paperclip text-lg"></i>
                                    </span>
                                </label>
                                <button
                                    type="submit"
                                    class="w-14 h-14 bg-gradient-to-br from-primary-600 to-blue-700 hover:from-primary-700 hover:to-blue-800 rounded-xl flex items-center justify-center text-white transition-all duration-200 shadow-lg hover:shadow-xl hover:scale-105 active:scale-95"
                                    id="send-button"
                                >
                                    <i class="fas fa-paper-plane text-lg"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- Connection Status Indicator -->
    <div id="connection-status" class="fixed bottom-4 left-4 bg-green-500 text-white px-3 py-2 rounded-lg shadow-lg z-40">
        <div class="flex items-center space-x-2">
            <i class="fas fa-wifi"></i>
            <span class="text-sm font-medium">Live Connected</span>
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
        console.log('🚀 Initializing real-time chat...');

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
                     title="${message.user.name} (${message.user.role})">
                    ${message.user.name.charAt(0).toUpperCase()}
                </div>
                <div class="flex-1 max-w-md ${isOwnMessage ? 'text-right' : ''}">
                    <div class="inline-block ${isOwnMessage ? 'bg-chat-sent' : 'bg-chat-received'} rounded-2xl px-4 py-3 shadow-lg border border-gray-200 hover:shadow-xl transition-all duration-200">
                        <div class="flex items-center space-x-2 mb-2 ${isOwnMessage ? 'flex-row-reverse space-x-reverse' : ''}">
                            <span class="text-sm font-bold ${isOwnMessage ? 'text-green-800' : 'text-primary-700'}">${message.user.name}</span>
                            <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">${formatTime(message.created_at)}</span>
                        </div>
                        <p class="text-gray-800 text-sm leading-relaxed">${message.message}</p>
                        ${message.attachment ? `
                            <div class="mt-3">
                                <a href="/storage/${message.attachment}" target="_blank"
                                   class="inline-flex items-center space-x-2 text-xs text-primary-700 hover:text-primary-800 bg-primary-100 hover:bg-primary-200 rounded-lg px-3 py-2 transition-all duration-200 hover:shadow-md border border-primary-200">
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
    }

    function setupPollingFallback() {
        console.log('🔄 Using polling fallback');
        updateConnectionStatus('polling');
        isPolling = true;
    }

    // Request notification permission on page load
    document.addEventListener('DOMContentLoaded', function() {
        console.log('🚀 Chat page loaded - initializing real-time...');

        // Request notification permission
        if ("Notification" in window && Notification.permission === "default") {
            Notification.requestPermission();
        }

        initializeRealTime();

        if (textarea) {
            textarea.focus();
        }

        console.log('✅ Real-time chat ready - messages will work in background/inactive windows');
    });

    // Keep polling active regardless of visibility
    document.addEventListener('visibilitychange', function() {
        console.log('👀 Visibility changed:', document.hidden ? 'hidden' : 'visible');
        // Background polling continues regardless
    });



    // Virtual Scroll Implementation
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
