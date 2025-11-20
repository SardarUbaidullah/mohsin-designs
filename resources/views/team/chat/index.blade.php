@extends('team.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div class="flex items-center space-x-4 mb-4 md:mb-0">
            <div class="w-14 h-14 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                <i class="fas fa-comments text-xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Messages</h1>
                <p class="text-gray-600 flex items-center">
                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium mr-2">
                        <i class="fas fa-circle text-xs mr-1"></i>
                        Active
                    </span>
                    Team Communication
                </p>
            </div>
        </div>
        <div class="flex items-center space-x-4">
            <button id="new-chat-btn"
                    class="group bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-medium transition-all duration-200 flex items-center shadow-sm hover:shadow-lg">
                <i class="fas fa-plus mr-2 group-hover:scale-110 transition-transform duration-200"></i>
                New Message
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-4 gap-8">
        <!-- Chat Sidebar -->
        <div class="xl:col-span-1 space-y-6">
            <!-- Search Bar -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-4">
                <div class="relative">
                    <input type="text"
                           placeholder="Search conversations..."
                           class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>

            <!-- Chat List -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-600 to-purple-700 px-4 py-3">
                    <h3 class="text-lg font-semibold text-white flex items-center">
                        <i class="fas fa-comments mr-2"></i>
                        Conversations
                    </h3>
                </div>
                <div class="max-h-[600px] overflow-y-auto scrollbar-thin">
                    <!-- Project Chats -->
                    <div class="border-b border-gray-100">
                        <div class="px-4 py-3 bg-gray-50">
                            <h4 class="text-sm font-semibold text-gray-700 flex items-center">
                                <i class="fas fa-users mr-2 text-indigo-600"></i>
                                Project Chats
                            </h4>
                        </div>
                        <div id="project-chats-list">
                            @foreach($projectRooms as $room)
                            <div class="chat-item p-4 border-b border-gray-100 cursor-pointer transition-all duration-200 hover:bg-gray-50 {{ $activeChat && $activeChat->id == $room->id ? 'bg-indigo-50 border-l-4 border-l-indigo-500' : '' }}"
                                 data-chat-id="{{ $room->id }}"
                                 data-chat-type="project">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl flex items-center justify-center text-white">
                                        <i class="fas fa-project-diagram"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm font-semibold text-gray-900 truncate">{{ $room->project->name ?? 'Project Chat' }}</h4>
                                        @if($room->messages->count() > 0)
                                        <p class="text-xs text-gray-500 truncate">
                                            {{ $room->messages->first()->user->name }}:
                                            {{ Str::limit($room->messages->first()->message, 25) }}
                                        </p>
                                        @else
                                        <p class="text-xs text-gray-500">No messages yet</p>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        @if($room->messages->count() > 0)
                                        <span class="text-xs text-gray-500">{{ $room->messages->first()->created_at->format('h:i A') }}</span>
                                        @endif
                                        @php
                                            $unreadCount = $room->messages()->where('user_id', '!=', auth()->id())->whereNull('read_at')->count();
                                        @endphp
                                        @if($unreadCount > 0)
                                        <span class="unread-count bg-red-500 text-white text-xs font-bold w-5 h-5 rounded-full flex items-center justify-center ml-auto mt-1">
                                            {{ $unreadCount }}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Direct Messages -->
                    <div>
                        <div class="px-4 py-3 bg-gray-50">
                            <h4 class="text-sm font-semibold text-gray-700 flex items-center">
                                <i class="fas fa-user-friends mr-2 text-green-600"></i>
                                Direct Messages
                            </h4>
                        </div>
                        <div id="direct-chats-list">
                            @foreach($directRooms as $room)
                            @php
                                $otherUser = $room->participants->where('user_id', '!=', auth()->id())->first()->user ?? null;
                            @endphp
                            @if($otherUser)
                            <div class="chat-item p-4 border-b border-gray-100 cursor-pointer transition-all duration-200 hover:bg-gray-50 {{ $activeChat && $activeChat->id == $room->id ? 'bg-indigo-50 border-l-4 border-l-indigo-500' : '' }}"
                                 data-chat-id="{{ $room->id }}"
                                 data-chat-type="direct">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-green-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                        {{ strtoupper(substr($otherUser->name, 0, 1)) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm font-semibold text-gray-900">{{ $otherUser->name }}</h4>
                                        <p class="text-xs text-gray-500 capitalize">{{ $otherUser->role }}</p>
                                        @if($room->messages->count() > 0)
                                        <p class="text-xs text-gray-600 mt-1 truncate">
                                            {{ Str::limit($room->messages->first()->message, 25) }}
                                        </p>
                                        @else
                                        <p class="text-xs text-gray-500">No messages yet</p>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        @if($room->messages->count() > 0)
                                        <span class="text-xs text-gray-500">{{ $room->messages->first()->created_at->format('h:i A') }}</span>
                                        @endif
                                        @php
                                            $unreadCount = $room->messages()->where('user_id', '!=', auth()->id())->whereNull('read_at')->count();
                                        @endphp
                                        @if($unreadCount > 0)
                                        <span class="unread-count bg-red-500 text-white text-xs font-bold w-5 h-5 rounded-full flex items-center justify-center ml-auto mt-1">
                                            {{ $unreadCount }}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Start New Chat Panel -->
            <div id="new-chat-panel" class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6 hidden">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-gray-900 text-lg">New Message</h3>
                    <button id="close-new-chat" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Manager/Admin</label>
                        <div class="max-h-48 overflow-y-auto space-y-2">
                            @foreach($availableUsers  as $manager)
                            <div class="user-item flex items-center space-x-3 p-3 rounded-lg border border-gray-200 cursor-pointer hover:bg-gray-50 transition-all duration-200"
                                 data-user-id="{{ $manager->id }}"
                                 data-user-name="{{ $manager->name }}"
                                 data-user-role="{{ $manager->role }}">
                                <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-green-600 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                                    {{ strtoupper(substr($manager->name, 0, 1)) }}
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">{{ $manager->name }}</p>
                                    <p class="text-xs text-gray-500 capitalize">{{ $manager->role }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <button id="create-chat-btn" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2.5 rounded-lg font-medium transition-all duration-200 hidden">
                        Start Conversation
                    </button>
                </div>
            </div>
        </div>

        <!-- Chat Main Area -->
        <div class="xl:col-span-3">
            <!-- Welcome/Empty State -->
            <div id="welcome-state" class="bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden flex flex-col h-[700px] items-center justify-center p-8 text-center {{ $activeChat ? 'hidden' : '' }}">
                <div class="w-32 h-32 bg-gradient-to-br from-indigo-100 to-purple-200 rounded-full flex items-center justify-center mb-6">
                    <i class="fas fa-comments text-indigo-600 text-4xl"></i>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Welcome to Messages</h2>
                <p class="text-gray-600 max-w-md mb-6">
                    Select a conversation from the sidebar or start a new chat with your manager.
                </p>
                <button id="start-chatting-btn" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl font-medium transition-all duration-200 shadow-md hover:shadow-lg">
                    Start a New Chat
                </button>
            </div>

            <!-- Active Chat Area -->
            <div id="active-chat-area" class="{{ $activeChat ? '' : 'hidden' }}">
                @if($activeChat)
                <div class="bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden flex flex-col h-[700px] transform transition-all duration-200 hover:shadow-2xl">
                    <!-- Chat Header -->
                    <div class="bg-gradient-to-r from-indigo-600 to-purple-700 px-6 py-5 shadow-lg">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                @if($activeChat->type === 'project')
                                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center text-white shadow-lg">
                                    <i class="fas fa-users text-lg"></i>
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold text-white">{{ $activeChat->project->name ?? 'Project Chat' }}</h2>
                                    <p class="text-indigo-100 text-sm flex items-center">
                                        <i class="fas fa-users mr-1.5"></i>
                                        <span id="online-count">{{ $activeChat->participants->count() }}</span> participants
                                    </p>
                                </div>
                                @else
                                @php
                                    $otherUser = $activeChat->participants->where('user_id', '!=', auth()->id())->first()->user ?? null;
                                @endphp
                                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center text-white text-lg font-semibold shadow-lg">
                                    {{ $otherUser ? strtoupper(substr($otherUser->name, 0, 1)) : 'U' }}
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold text-white">{{ $otherUser->name ?? 'User' }}</h2>
                                    <p class="text-indigo-100 text-sm flex items-center">
                                        <i class="fas fa-user mr-1.5"></i>
                                        <span class="capitalize">{{ $otherUser->role ?? 'Manager' }}</span>
                                        <div class="flex items-center space-x-2 ml-4">
                                            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                                            <span class="text-indigo-100 text-xs font-medium">Online</span>
                                        </div>
                                    </p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Messages Area -->
                    <div class="flex-1 p-6 overflow-y-auto bg-gradient-to-b from-gray-50 to-blue-50 scrollbar-thin" id="messages-container">
                        <div class="space-y-4" id="messages-list">
                            @foreach($activeMessages as $message)
                            <div class="flex items-start space-x-3 animate-fade-in {{ $message->user_id === auth()->id() ? 'flex-row-reverse space-x-reverse' : '' }}">
                                <div class="w-10 h-10 {{ $message->user_id === auth()->id() ? 'bg-gradient-to-br from-green-500 to-emerald-600' : 'bg-gradient-to-br from-indigo-500 to-purple-600' }} rounded-full flex items-center justify-center text-white text-sm font-semibold flex-shrink-0 shadow-lg transition-transform duration-200 hover:scale-105 cursor-pointer"
                                     title="{{ $message->user->name }} ({{ $message->user->role }})">
                                    {{ strtoupper(substr($message->user->name, 0, 1)) }}
                                </div>
                                <div class="flex-1 max-w-md {{ $message->user_id === auth()->id() ? 'text-right' : '' }}">
                                    <div class="inline-block {{ $message->user_id === auth()->id() ? 'bg-green-100 border-green-200' : 'bg-white border-gray-200' }} rounded-2xl px-4 py-3 shadow-lg border hover:shadow-xl transition-all duration-200">
                                        <div class="flex items-center space-x-2 mb-2 {{ $message->user_id === auth()->id() ? 'flex-row-reverse space-x-reverse' : '' }}">
                                            <span class="text-sm font-bold {{ $message->user_id === auth()->id() ? 'text-green-800' : 'text-indigo-700' }}">{{ $message->user->name }}</span>
                                            <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">{{ $message->created_at->format('h:i A') }}</span>
                                        </div>
                                        <p class="text-gray-800 text-sm leading-relaxed">{{ $message->message }}</p>
                                        @if($message->attachment)
                                        <div class="mt-3">
                                            <a href="{{ Storage::url($message->attachment) }}"
                                               target="_blank"
                                               class="inline-flex items-center space-x-2 text-xs text-indigo-700 hover:text-indigo-800 bg-indigo-100 hover:bg-indigo-200 rounded-lg px-3 py-2 transition-all duration-200 hover:shadow-md border border-indigo-200">
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
                            <input type="hidden" id="active-chat-id" value="{{ $activeChat->id }}">
                            <input type="hidden" id="active-chat-type" value="{{ $activeChat->type }}">
                            <div class="flex-1 relative">
                                <textarea
                                    id="message-input"
                                    name="message"
                                    rows="1"
                                    placeholder="Type your message... (Press Enter to send, Shift+Enter for new line)"
                                    class="w-full px-5 py-4 border-2 border-gray-300 rounded-2xl resize-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 shadow-sm focus:shadow-md bg-gray-50 focus:bg-white"
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
                                    <span class="w-14 h-14 bg-gradient-to-br from-purple-500 to-pink-600 group-hover:from-purple-600 group-hover:to-pink-700 rounded-xl flex items-center justify-center text-white transition-all duration-200 shadow-lg group-hover:shadow-xl group-hover:scale-105">
                                        <i class="fas fa-paperclip text-lg"></i>
                                    </span>
                                </label>
                                <button
                                    type="submit"
                                    class="w-14 h-14 bg-gradient-to-br from-indigo-600 to-purple-700 hover:from-indigo-700 hover:to-purple-800 rounded-xl flex items-center justify-center text-white transition-all duration-200 shadow-lg hover:shadow-xl hover:scale-105 active:scale-95"
                                    id="send-button"
                                >
                                    <i class="fas fa-paper-plane text-lg"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Connection Status Indicator -->
<div id="connection-status" class="fixed bottom-4 left-4 bg-green-500 text-white px-3 py-2 rounded-lg shadow-lg z-40">
    <div class="flex items-center space-x-2">
        <i class="fas fa-wifi"></i>
        <span class="text-sm font-medium">Connected</span>
    </div>
</div>

<script src="https://js.pusher.com/7.0/pusher.min.js"></script>
<script src="https://js.pusher.com/7.0/pusher.min.js"></script>
<script>
    // Chat functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize variables
        let selectedUserId = null;
        let pusher = null;
        let channel = null;
let lastMessageId = {{ !empty($activeMessages) ? end($activeMessages)['id'] ?? 0 : 0 }};
        let isPolling = false;
        const currentUserId = {{ auth()->id() }};

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

        // Chat item clicks
        document.querySelectorAll('.chat-item').forEach(item => {
            item.addEventListener('click', function() {
                const chatId = this.dataset.chatId;
                const chatType = this.dataset.chatType;

                // Redirect to open this chat
                window.location.href = `/team/chat?active_chat=${chatId}&active_chat_type=${chatType}`;
            });
        });

        // New chat functionality
        document.getElementById('new-chat-btn').addEventListener('click', function() {
            document.getElementById('new-chat-panel').classList.remove('hidden');
        });

        document.getElementById('close-new-chat').addEventListener('click', function() {
            document.getElementById('new-chat-panel').classList.add('hidden');
            resetNewChatPanel();
        });

        document.getElementById('start-chatting-btn').addEventListener('click', function() {
            document.getElementById('new-chat-panel').classList.remove('hidden');
        });

        // User selection for new chat
        document.querySelectorAll('.user-item').forEach(item => {
            item.addEventListener('click', function() {
                document.querySelectorAll('.user-item').forEach(i => {
                    i.classList.remove('bg-indigo-50', 'border-indigo-200');
                });
                this.classList.add('bg-indigo-50', 'border-indigo-200');
                selectedUserId = this.dataset.userId;
                document.getElementById('create-chat-btn').classList.remove('hidden');
            });
        });

        // Create new chat
        document.getElementById('create-chat-btn').addEventListener('click', async function() {
            if (!selectedUserId) {
                alert('Please select a manager/admin to message');
                return;
            }

            const createBtn = this;
            const originalText = createBtn.innerHTML;
            createBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...';
            createBtn.disabled = true;

            try {
                const response = await fetch('/team/chat/create', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        type: 'direct',
                        user_id: selectedUserId
                    })
                });

                const data = await response.json();

                if (data.success) {
                    window.location.href = data.redirect_url;
                } else {
                    throw new Error(data.error || 'Failed to create chat');
                }
            } catch (error) {
                console.error('Error creating chat:', error);
                alert('Failed to create chat: ' + error.message);
                createBtn.innerHTML = originalText;
                createBtn.disabled = false;
            }
        });

        // Message form submission
        const messageForm = document.getElementById('message-form');
        if (messageForm) {
            messageForm.addEventListener('submit', function(e) {
                e.preventDefault();
                e.stopPropagation();
                sendMessage();
                return false;
            });

            // Enter key handler for immediate sending
            if (textarea) {
                textarea.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' && !e.shiftKey) {
                        e.preventDefault();
                        sendMessage();
                    }
                });
            }

            // Initialize real-time messaging if active chat exists
            initializeRealTime();
        }

        // Search functionality
        const searchInput = document.querySelector('input[placeholder="Search conversations..."]');
        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                filterChats(searchTerm);
            });
        }

        // File attachment preview
        const attachmentInput = document.getElementById('attachment');
        if (attachmentInput) {
            attachmentInput.addEventListener('change', function(e) {
                if (this.files[0]) {
                    const fileName = this.files[0].name;
                    console.log('File selected:', fileName);
                }
            });
        }

        // Functions
        function resetNewChatPanel() {
            selectedUserId = null;
            document.querySelectorAll('.user-item').forEach(i => {
                i.classList.remove('bg-indigo-50', 'border-indigo-200');
            });
            document.getElementById('create-chat-btn').classList.add('hidden');
        }

        function filterChats(searchTerm) {
            const chatItems = document.querySelectorAll('.chat-item');

            chatItems.forEach(item => {
                const chatName = item.querySelector('h4').textContent.toLowerCase();
                const lastMessage = item.querySelector('p.text-xs.text-gray-600, p.text-xs.text-gray-500');
                const lastMessageText = lastMessage ? lastMessage.textContent.toLowerCase() : '';

                if (chatName.includes(searchTerm) || lastMessageText.includes(searchTerm)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        // Send message function - USING YOUR WORKING LOGIC
        async function sendMessage() {
            const messageInput = document.getElementById('message-input');
            const attachmentInput = document.getElementById('attachment');
            const sendButton = document.getElementById('send-button');
            const chatId = document.getElementById('active-chat-id').value;

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

                const response = await fetch(`/team/chat/${chatId}/send`, {
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
                    // ⭐⭐ KEY CHANGE: IMMEDIATELY add message to UI ⭐⭐
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

        // Add message to chat - USING YOUR EXACT ORIGINAL LOGIC
        function addMessageToChat(message) {
            const isOwnMessage = message.user_id === currentUserId;

            const messageHtml = `
                <div class="flex items-start space-x-3 animate-fade-in ${isOwnMessage ? 'flex-row-reverse space-x-reverse' : ''}">
                    <div class="w-10 h-10 ${isOwnMessage ? 'bg-gradient-to-br from-green-500 to-emerald-600' : 'bg-gradient-to-br from-indigo-500 to-purple-600'} rounded-full flex items-center justify-center text-white text-sm font-semibold flex-shrink-0 shadow-lg transition-transform duration-200 hover:scale-105 cursor-pointer"
                         title="${message.user.name} (${message.user.role})">
                        ${message.user.name.charAt(0).toUpperCase()}
                    </div>
                    <div class="flex-1 max-w-md ${isOwnMessage ? 'text-right' : ''}">
                        <div class="inline-block ${isOwnMessage ? 'bg-green-100 border-green-200' : 'bg-white border-gray-200'} rounded-2xl px-4 py-3 shadow-lg border border-gray-200 hover:shadow-xl transition-all duration-200">
                            <div class="flex items-center space-x-2 mb-2 ${isOwnMessage ? 'flex-row-reverse space-x-reverse' : ''}">
                                <span class="text-sm font-bold ${isOwnMessage ? 'text-green-800' : 'text-indigo-700'}">${message.user.name}</span>
                                <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">${formatTime(message.created_at)}</span>
                            </div>
                            <p class="text-gray-800 text-sm leading-relaxed">${message.message}</p>
                            ${message.attachment ? `
                                <div class="mt-3">
                                    <a href="/storage/${message.attachment}" target="_blank"
                                       class="inline-flex items-center space-x-2 text-xs text-indigo-700 hover:text-indigo-800 bg-indigo-100 hover:bg-indigo-200 rounded-lg px-3 py-2 transition-all duration-200 hover:shadow-md border border-indigo-200">
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

            document.getElementById('messages-list').insertAdjacentHTML('beforeend', messageHtml);

            // Scroll to bottom
            const messagesContainer = document.getElementById('messages-container');
            if (messagesContainer) {
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }

            if (!isOwnMessage) {
                playNotificationSound();
            }
        }

        // REAL-TIME WITH PUSHER - USING YOUR WORKING LOGIC
        function initializeRealTime() {
            const activeChatId = document.getElementById('active-chat-id');
            if (!activeChatId || !activeChatId.value) {
                console.log('No active chat found');
                return;
            }

            console.log('🚀 Initializing real-time chat...');

            if (typeof Pusher !== 'undefined') {
                console.log('✅ Pusher is available, setting up real-time...');

                // Enable Pusher logging
                Pusher.logToConsole = true;

                pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
                    cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
                    encrypted: true,
                    forceTLS: true
                });

                // Subscribe to channel
                channel = pusher.subscribe(`chat.room.${activeChatId.value}`);

                // Listen for new messages
                channel.bind('message.sent', function(data) {
                    console.log('💬 New message received via Pusher:', data);

                    // ⭐⭐ KEY CHANGE: Only add messages from other users via Pusher ⭐⭐
                    if (data.message && data.message.user_id !== currentUserId) {
                        addMessageToChat(data.message);
                        playNotificationSound();
                        showNotification(`New message from ${data.message.user.name}`);
                    }
                });

                // Connection events
                channel.bind('pusher:subscription_succeeded', function() {
                    console.log('✅ Successfully subscribed to Pusher channel');
                    updateConnectionStatus('connected');
                });

                channel.bind('pusher:subscription_error', function(status) {
                    console.error('❌ Pusher subscription error:', status);
                    updateConnectionStatus('error');
                    setupPollingFallback();
                });

                console.log('📡 Pusher setup complete');

            } else {
                console.error('❌ Pusher not available');
                setupPollingFallback();
            }
        }

        // Fallback polling
        function setupPollingFallback() {
            console.log('🔄 Using polling fallback');
            updateConnectionStatus('polling');
            isPolling = true;

            setInterval(pollForNewMessages, 2000);
            setTimeout(pollForNewMessages, 1000);
        }

        async function pollForNewMessages() {
            if (!isPolling) return;

            const activeChatId = document.getElementById('active-chat-id');
            if (!activeChatId || !activeChatId.value) return;

            try {
                const response = await fetch(`/team/chat/${activeChatId.value}/messages?last_id=${lastMessageId}`);
                const data = await response.json();

                if (data.data && data.data.length > 0) {
                    const newMessages = data.data.filter(msg => msg.id > lastMessageId);

                    if (newMessages.length > 0) {
                        newMessages.forEach(message => {
                            // ⭐⭐ KEY CHANGE: Only add messages from other users via polling ⭐⭐
                            if (message.user_id !== currentUserId) {
                                addMessageToChat(message);
                            }
                            lastMessageId = Math.max(lastMessageId, message.id);
                        });

                        const othersMessages = newMessages.filter(msg => msg.user_id !== currentUserId);
                        if (othersMessages.length > 0) {
                            showNotification(`New message from ${othersMessages[0].user.name}`);
                        }
                    }
                }
            } catch (error) {
                console.error('Polling error:', error);
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
                audio.volume = 0.2;
                audio.play();
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

            if (statusElement) {
                statusElement.className = `fixed bottom-4 left-4 ${config.color} text-white px-3 py-2 rounded-lg shadow-lg z-40`;
                statusElement.innerHTML = `
                    <div class="flex items-center space-x-2">
                        <i class="fas ${config.icon}"></i>
                        <span class="text-sm font-medium">${config.text}</span>
                    </div>
                `;
            }
        }

        // Mark messages as read when chat is opened
        const activeChatId = document.getElementById('active-chat-id');
        if (activeChatId && activeChatId.value) {
            markMessagesAsRead(activeChatId.value);
        }

        async function markMessagesAsRead(chatId) {
            try {
                await fetch(`/team/chat/${chatId}/mark-read`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
            } catch (error) {
                console.error('Error marking messages as read:', error);
            }
        }

        // Stop polling when tab is hidden
        document.addEventListener('visibilitychange', function() {
            isPolling = !document.hidden;
            if (isPolling) pollForNewMessages();
        });

        // Initialize when page loads
        console.log('🚀 Chat page loaded');
        if (textarea) textarea.focus();
    });
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
    .animate-fade-in {
        animation: fadeIn 0.3s ease-in-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection
