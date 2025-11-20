<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unified Chat - CRM</title>
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

        /* Chat list hover effect */
        .chat-item:hover {
            background-color: #f8fafc;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        /* Active chat indicator */
        .active-chat {
            background-color: #eff6ff;
            border-left: 4px solid #0ea5e9;
        }
    </style>
</head>
<body class="whatsapp-bg">
    <div class="max-w-7xl mx-auto px-4 py-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
            <div class="flex items-center space-x-4 mb-4 md:mb-0">
                <a href="{{ route('dashboard') }}"
                   class="group bg-white hover:bg-primary-50 border border-gray-200 rounded-xl p-3 transition-all duration-200 shadow-sm hover:shadow-md">
                    <i class="fas fa-arrow-left text-gray-600 group-hover:text-primary-600 text-lg transition-colors duration-200"></i>
                </a>
                <div class="flex items-center space-x-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-primary-500 to-blue-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-comments text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Messages</h1>
                        <p class="text-gray-600 flex items-center">
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium mr-2">
                                <i class="fas fa-circle text-xs mr-1"></i>
                                Active
                            </span>
                            Unified Chat Interface
                        </p>
                    </div>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <button id="new-chat-btn"
                        class="group bg-primary-600 hover:bg-primary-700 text-white px-5 py-2.5 rounded-xl font-medium transition-all duration-200 flex items-center shadow-sm hover:shadow-lg">
                    <i class="fas fa-plus mr-2 group-hover:scale-110 transition-transform duration-200"></i>
                    New Chat
                </button>
                <div class="flex items-center space-x-3 bg-white rounded-xl px-4 py-2 shadow-sm border border-gray-200">
                    <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-blue-600 rounded-full flex items-center justify-center text-white text-sm font-semibold shadow-lg">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="text-sm text-gray-700 font-medium">
                        {{ auth()->user()->name }}
                    </div>
                </div>
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
                               class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                </div>

                <!-- Chat List -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-primary-600 to-blue-700 px-4 py-3">
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
                                    <i class="fas fa-users mr-2 text-primary-600"></i>
                                    Project Chats
                                </h4>
                            </div>
                            <div id="project-chats-list">
                                <!-- Project chats will be loaded here -->
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
                                <!-- Direct messages will be loaded here -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Start New Chat Panel -->
                <div id="new-chat-panel" class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6 hidden">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-gray-900 text-lg">Start New Chat</h3>
                        <button id="close-new-chat" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Chat Type</label>
                            <div class="grid grid-cols-2 gap-3">
                                <button class="chat-type-btn bg-primary-50 border border-primary-200 text-primary-700 py-2 rounded-lg font-medium transition-all duration-200" data-type="project">
                                    <i class="fas fa-users mr-2"></i>Project
                                </button>
                                <button class="chat-type-btn bg-gray-50 border border-gray-200 text-gray-700 py-2 rounded-lg font-medium transition-all duration-200" data-type="direct">
                                    <i class="fas fa-user mr-2"></i>Direct
                                </button>
                            </div>
                        </div>

                        <div id="project-select" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Select Project</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <option>Project Alpha</option>
                                <option>Project Beta</option>
                                <option>Project Gamma</option>
                            </select>
                        </div>

                        <div id="user-select" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Select User</label>
                            <div class="max-h-48 overflow-y-auto space-y-2">
                                <!-- Users will be listed here -->
                            </div>
                        </div>

                        <button id="create-chat-btn" class="w-full bg-primary-600 hover:bg-primary-700 text-white py-2.5 rounded-lg font-medium transition-all duration-200 hidden">
                            Start Chat
                        </button>
                    </div>
                </div>
            </div>

            <!-- Chat Main Area -->
            <div class="xl:col-span-3">
                <!-- Welcome/Empty State -->
                <div id="welcome-state" class="bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden flex flex-col h-[700px] items-center justify-center p-8 text-center">
                    <div class="w-32 h-32 bg-gradient-to-br from-primary-100 to-blue-200 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-comments text-primary-600 text-4xl"></i>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">Welcome to Messages</h2>
                    <p class="text-gray-600 max-w-md mb-6">
                        Select a conversation from the sidebar or start a new chat to begin messaging.
                    </p>
                    <button id="start-chatting-btn" class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-3 rounded-xl font-medium transition-all duration-200 shadow-md hover:shadow-lg">
                        Start a New Chat
                    </button>
                </div>

                <!-- Active Chat Area -->
                <div id="active-chat-area" class="hidden">
                    <div class="bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden flex flex-col h-[700px] transform transition-all duration-200 hover:shadow-2xl">
                        <!-- Chat Header -->
                        <div class="bg-gradient-to-r from-primary-600 to-blue-700 px-6 py-5 shadow-lg">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <div id="chat-avatar" class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center text-white shadow-lg">
                                        <i class="fas fa-comments text-lg"></i>
                                    </div>
                                    <div>
                                        <h2 id="chat-title" class="text-xl font-bold text-white">Chat Title</h2>
                                        <p id="chat-subtitle" class="text-blue-100 text-sm flex items-center">
                                            <i class="fas fa-users mr-1.5"></i>
                                            <span id="online-count">0</span> participants
                                            <div class="flex items-center space-x-2 ml-4">
                                                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                                                <span class="text-blue-100 text-xs font-medium" id="connection-text">Live Connected</span>
                                            </div>
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3">
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
                                <!-- Messages will be loaded here -->
                            </div>
                        </div>

                        <!-- Message Input -->
                        <div class="border-t border-gray-200 bg-white p-6 shadow-lg">
                            <form id="message-form" class="flex space-x-4 items-end">
                                @csrf
                                <input type="hidden" id="active-chat-id" value="">
                                <input type="hidden" id="active-chat-type" value="">
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
                                        <span class="w-14 h-14 bg-gradient-to-br from-purple-500 to-pink-600 group-hover:from-purple-600 group-hover:to-pink-700 rounded-xl flex items-center justify-center text-white transition-all duration-200 shadow-lg group-hover:shadow-xl group-hover:scale-105">
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

    <!-- Chat Templates (Hidden) -->
    <template id="project-chat-template">
        <div class="chat-item p-4 border-b border-gray-100 cursor-pointer transition-all duration-200" data-chat-id="" data-chat-type="project">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl flex items-center justify-center text-white">
                    <i class="fas fa-project-diagram"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="text-sm font-semibold text-gray-900 truncate">Project Name</h4>
                    <p class="text-xs text-gray-500 truncate">Last message preview...</p>
                </div>
                <div class="text-right">
                    <span class="text-xs text-gray-500">12:30</span>
                    <span class="unread-count hidden bg-red-500 text-white text-xs font-bold w-5 h-5 rounded-full flex items-center justify-center ml-auto mt-1">2</span>
                </div>
            </div>
        </div>
    </template>

    <template id="direct-chat-template">
        <div class="chat-item p-4 border-b border-gray-100 cursor-pointer transition-all duration-200" data-chat-id="" data-chat-type="direct">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-green-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                    U
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="text-sm font-semibold text-gray-900">User Name</h4>
                    <p class="text-xs text-gray-500">Role</p>
                    <p class="text-xs text-gray-600 mt-1 truncate">Last message preview...</p>
                </div>
                <div class="text-right">
                    <span class="text-xs text-gray-500">12:30</span>
                    <span class="unread-count hidden bg-red-500 text-white text-xs font-bold w-5 h-5 rounded-full flex items-center justify-center ml-auto mt-1">2</span>
                </div>
            </div>
        </div>
    </template>

    <template id="message-template">
        <div class="flex items-start space-x-3 animate-fade-in">
            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-semibold flex-shrink-0 shadow-lg transition-transform duration-200 hover:scale-105 cursor-pointer"
                 title="User Name (Role)">
                U
            </div>
            <div class="flex-1 max-w-md">
                <div class="inline-block rounded-2xl px-4 py-3 shadow-lg border border-gray-200 hover:shadow-xl transition-all duration-200">
                    <div class="flex items-center space-x-2 mb-2">
                        <span class="text-sm font-bold text-primary-700">User Name</span>
                        <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">12:30 PM</span>
                    </div>
                    <p class="text-gray-800 text-sm leading-relaxed">Message content</p>
                    <div class="attachment-container hidden mt-3">
                        <a href="#"
                           target="_blank"
                           class="inline-flex items-center space-x-2 text-xs text-primary-700 hover:text-primary-800 bg-primary-100 hover:bg-primary-200 rounded-lg px-3 py-2 transition-all duration-200 hover:shadow-md border border-primary-200">
                            <i class="fas fa-paperclip"></i>
                            <span class="font-medium">attachment_name</span>
                            <i class="fas fa-external-link-alt text-xs"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <script>
        // Chat Manager
        class ChatManager {
            constructor() {
                this.currentChatId = null;
                this.currentChatType = null;
                this.lastMessageId = 0;
                this.isPolling = false;
                this.currentUserId = {{ auth()->id() }};
                this.pusher = null;
                this.channel = null;

                this.initializeEventListeners();
                this.loadChatList();
                this.initializeRealTime();
            }

            initializeEventListeners() {
                // New chat button
                document.getElementById('new-chat-btn').addEventListener('click', () => {
                    this.toggleNewChatPanel(true);
                });

                document.getElementById('close-new-chat').addEventListener('click', () => {
                    this.toggleNewChatPanel(false);
                });

                document.getElementById('start-chatting-btn').addEventListener('click', () => {
                    this.toggleNewChatPanel(true);
                });

                // Chat type selection
                document.querySelectorAll('.chat-type-btn').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        document.querySelectorAll('.chat-type-btn').forEach(b => {
                            b.classList.remove('bg-primary-50', 'border-primary-200', 'text-primary-700');
                            b.classList.add('bg-gray-50', 'border-gray-200', 'text-gray-700');
                        });

                        e.target.classList.remove('bg-gray-50', 'border-gray-200', 'text-gray-700');
                        e.target.classList.add('bg-primary-50', 'border-primary-200', 'text-primary-700');

                        const type = e.target.dataset.type;
                        document.getElementById('project-select').classList.toggle('hidden', type !== 'project');
                        document.getElementById('user-select').classList.toggle('hidden', type !== 'direct');
                        document.getElementById('create-chat-btn').classList.remove('hidden');
                    });
                });

                // Message form submission
                document.getElementById('message-form').addEventListener('submit', (e) => {
                    e.preventDefault();
                    this.sendMessage();
                });

                // Auto-resize textarea
                const textarea = document.getElementById('message-input');
                textarea.addEventListener('input', function() {
                    this.style.height = 'auto';
                    this.style.height = Math.min(this.scrollHeight, 120) + 'px';
                });

                // Enter key handler
                textarea.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter' && !e.shiftKey) {
                        e.preventDefault();
                        this.sendMessage();
                    }
                });
            }

            toggleNewChatPanel(show) {
                document.getElementById('new-chat-panel').classList.toggle('hidden', !show);
            }

            loadChatList() {
                // In a real app, this would fetch from your API
                // For demo, we'll create some mock data

                // Project chats
                const projectChats = [
                    { id: 1, name: 'Project Alpha', lastMessage: 'Let\'s discuss the timeline', time: '12:30', unread: 2 },
                    { id: 2, name: 'Project Beta', lastMessage: 'The design is ready for review', time: '11:45', unread: 0 },
                    { id: 3, name: 'Project Gamma', lastMessage: 'Meeting scheduled for tomorrow', time: '10:15', unread: 5 }
                ];

                // Direct messages
                const directChats = [
                    { id: 4, name: 'John Doe', role: 'Developer', lastMessage: 'Can you review my PR?', time: '13:20', unread: 1 },
                    { id: 5, name: 'Jane Smith', role: 'Designer', lastMessage: 'I sent the design files', time: '09:45', unread: 0 },
                    { id: 6, name: 'Mike Johnson', role: 'Manager', lastMessage: 'Good job on the presentation', time: 'Yesterday', unread: 0 }
                ];

                this.renderChatList('project', projectChats);
                this.renderChatList('direct', directChats);

                // Add click handlers to chat items
                document.querySelectorAll('.chat-item').forEach(item => {
                    item.addEventListener('click', () => {
                        const chatId = item.dataset.chatId;
                        const chatType = item.dataset.chatType;
                        this.openChat(chatId, chatType);
                    });
                });
            }

            renderChatList(type, chats) {
                const container = document.getElementById(`${type}-chats-list`);
                const template = document.getElementById(`${type}-chat-template`);

                container.innerHTML = '';

                chats.forEach(chat => {
                    const clone = template.content.cloneNode(true);
                    const chatElement = clone.querySelector('.chat-item');

                    chatElement.dataset.chatId = chat.id;
                    chatElement.dataset.chatType = type;

                    if (type === 'project') {
                        chatElement.querySelector('h4').textContent = chat.name;
                        chatElement.querySelector('p').textContent = chat.lastMessage;
                    } else {
                        chatElement.querySelector('h4').textContent = chat.name;
                        chatElement.querySelector('p').textContent = chat.role;
                        chatElement.querySelector('.text-xs.text-gray-600').textContent = chat.lastMessage;
                        chatElement.querySelector('.w-12.h-12').textContent = chat.name.charAt(0).toUpperCase();
                    }

                    chatElement.querySelector('.text-xs.text-gray-500').textContent = chat.time;

                    if (chat.unread > 0) {
                        const unreadElement = chatElement.querySelector('.unread-count');
                        unreadElement.textContent = chat.unread;
                        unreadElement.classList.remove('hidden');
                    }

                    container.appendChild(chatElement);
                });
            }

            openChat(chatId, chatType) {
                this.currentChatId = chatId;
                this.currentChatType = chatType;

                // Update UI to show active chat
                document.getElementById('welcome-state').classList.add('hidden');
                document.getElementById('active-chat-area').classList.remove('hidden');

                // Update active chat indicators
                document.querySelectorAll('.chat-item').forEach(item => {
                    item.classList.remove('active-chat');
                    if (item.dataset.chatId === chatId && item.dataset.chatType === chatType) {
                        item.classList.add('active-chat');
                    }
                });

                // Update chat header based on type
                this.updateChatHeader(chatId, chatType);

                // Load messages for this chat
                this.loadMessages(chatId, chatType);

                // Update real-time subscription
                this.updateRealTimeSubscription(chatId);
            }

            updateChatHeader(chatId, chatType) {
                // In a real app, this would fetch chat details from your API
                // For demo, we'll use mock data

                if (chatType === 'project') {
                    document.getElementById('chat-title').textContent = `Project ${chatId} Discussion`;
                    document.getElementById('chat-subtitle').innerHTML = `
                        <i class="fas fa-users mr-1.5"></i>
                        <span id="online-count">5</span> participants
                        <div class="flex items-center space-x-2 ml-4">
                            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                            <span class="text-blue-100 text-xs font-medium" id="connection-text">Live Connected</span>
                        </div>
                    `;
                    document.getElementById('chat-avatar').innerHTML = '<i class="fas fa-users text-lg"></i>';
                } else {
                    document.getElementById('chat-title').textContent = `User ${chatId}`;
                    document.getElementById('chat-subtitle').innerHTML = `
                        <i class="fas fa-user mr-1.5"></i>
                        Direct message
                        <div class="flex items-center space-x-2 ml-4">
                            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                            <span class="text-blue-100 text-xs font-medium" id="connection-text">Live Connected</span>
                        </div>
                    `;
                    document.getElementById('chat-avatar').innerHTML = '<i class="fas fa-user text-lg"></i>';
                }

                // Update hidden inputs
                document.getElementById('active-chat-id').value = chatId;
                document.getElementById('active-chat-type').value = chatType;
            }

            loadMessages(chatId, chatType) {
                // In a real app, this would fetch messages from your API
                // For demo, we'll create some mock messages

                const messages = [
                    {
                        id: 1,
                        user_id: 2,
                        user: { name: 'John Doe', role: 'Developer' },
                        message: 'Hello everyone! How is the project going?',
                        created_at: new Date(Date.now() - 3600000).toISOString(),
                        attachment: null,
                        attachment_name: null
                    },
                    {
                        id: 2,
                        user_id: this.currentUserId,
                        user: { name: 'You', role: 'Manager' },
                        message: 'We\'re making good progress. The design phase is almost complete.',
                        created_at: new Date(Date.now() - 1800000).toISOString(),
                        attachment: null,
                        attachment_name: null
                    },
                    {
                        id: 3,
                        user_id: 3,
                        user: { name: 'Jane Smith', role: 'Designer' },
                        message: 'I\'ve uploaded the final design files. Please take a look when you have time.',
                        created_at: new Date(Date.now() - 600000).toISOString(),
                        attachment: 'designs.zip',
                        attachment_name: 'project_designs.zip'
                    }
                ];

                this.renderMessages(messages);
                this.lastMessageId = Math.max(...messages.map(m => m.id));
            }

            renderMessages(messages) {
                const container = document.getElementById('messages-list');
                const template = document.getElementById('message-template');

                container.innerHTML = '';

                messages.forEach(message => {
                    const clone = template.content.cloneNode(true);
                    const messageElement = clone.querySelector('.flex');

                    const isOwnMessage = message.user_id === this.currentUserId;

                    if (isOwnMessage) {
                        messageElement.classList.add('flex-row-reverse', 'space-x-reverse');
                        messageElement.querySelector('.w-10.h-10').classList.remove('bg-gradient-to-br', 'from-primary-500', 'to-blue-600');
                        messageElement.querySelector('.w-10.h-10').classList.add('bg-gradient-to-br', 'from-green-500', 'to-emerald-600');
                        messageElement.querySelector('.flex-1').classList.add('text-right');
                        messageElement.querySelector('.inline-block').classList.remove('bg-chat-received');
                        messageElement.querySelector('.inline-block').classList.add('bg-chat-sent');
                        messageElement.querySelector('.flex.items-center.space-x-2').classList.add('flex-row-reverse', 'space-x-reverse');
                        messageElement.querySelector('.text-sm.font-bold').classList.remove('text-primary-700');
                        messageElement.querySelector('.text-sm.font-bold').classList.add('text-green-800');
                    }

                    messageElement.querySelector('.w-10.h-10').textContent = message.user.name.charAt(0).toUpperCase();
                    messageElement.querySelector('.w-10.h-10').title = `${message.user.name} (${message.user.role})`;
                    messageElement.querySelector('.text-sm.font-bold').textContent = message.user.name;
                    messageElement.querySelector('.text-xs.text-gray-500').textContent = this.formatTime(message.created_at);
                    messageElement.querySelector('.text-gray-800').textContent = message.message;

                    if (message.attachment) {
                        const attachmentContainer = messageElement.querySelector('.attachment-container');
                        attachmentContainer.classList.remove('hidden');
                        const link = attachmentContainer.querySelector('a');
                        link.href = '#';
                        link.querySelector('span').textContent = message.attachment_name;
                    }

                    container.appendChild(messageElement);
                });

                // Scroll to bottom
                const messagesContainer = document.getElementById('messages-container');
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }

            async sendMessage() {
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

                // In a real app, this would send to your API
                // For demo, we'll simulate the process

                try {
                    // Simulate API call delay
                    await new Promise(resolve => setTimeout(resolve, 500));

                    // Create mock message data
                    const messageData = {
                        id: Date.now(),
                        user_id: this.currentUserId,
                        user: { name: 'You', role: 'Manager' },
                        message: messageText,
                        created_at: new Date().toISOString(),
                        attachment: attachmentInput.files[0] ? 'file.pdf' : null,
                        attachment_name: attachmentInput.files[0] ? attachmentInput.files[0].name : null
                    };

                    // Add message to UI immediately
                    this.addMessageToChat(messageData);

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

                    // Update last message ID
                    this.lastMessageId = messageData.id;

                } catch (error) {
                    console.error('Error sending message:', error);
                    sendButton.innerHTML = '<i class="fas fa-exclamation-triangle"></i>';
                    setTimeout(() => {
                        sendButton.innerHTML = originalHtml;
                        sendButton.disabled = false;
                    }, 2000);
                    this.showNotification('Failed to send message', 'error');
                }
            }

            addMessageToChat(message) {
                const container = document.getElementById('messages-list');
                const template = document.getElementById('message-template');
                const clone = template.content.cloneNode(true);
                const messageElement = clone.querySelector('.flex');

                const isOwnMessage = message.user_id === this.currentUserId;

                if (isOwnMessage) {
                    messageElement.classList.add('flex-row-reverse', 'space-x-reverse');
                    messageElement.querySelector('.w-10.h-10').classList.remove('bg-gradient-to-br', 'from-primary-500', 'to-blue-600');
                    messageElement.querySelector('.w-10.h-10').classList.add('bg-gradient-to-br', 'from-green-500', 'to-emerald-600');
                    messageElement.querySelector('.flex-1').classList.add('text-right');
                    messageElement.querySelector('.inline-block').classList.remove('bg-chat-received');
                    messageElement.querySelector('.inline-block').classList.add('bg-chat-sent');
                    messageElement.querySelector('.flex.items-center.space-x-2').classList.add('flex-row-reverse', 'space-x-reverse');
                    messageElement.querySelector('.text-sm.font-bold').classList.remove('text-primary-700');
                    messageElement.querySelector('.text-sm.font-bold').classList.add('text-green-800');
                }

                messageElement.querySelector('.w-10.h-10').textContent = message.user.name.charAt(0).toUpperCase();
                messageElement.querySelector('.w-10.h-10').title = `${message.user.name} (${message.user.role})`;
                messageElement.querySelector('.text-sm.font-bold').textContent = message.user.name;
                messageElement.querySelector('.text-xs.text-gray-500').textContent = this.formatTime(message.created_at);
                messageElement.querySelector('.text-gray-800').textContent = message.message;

                if (message.attachment) {
                    const attachmentContainer = messageElement.querySelector('.attachment-container');
                    attachmentContainer.classList.remove('hidden');
                    const link = attachmentContainer.querySelector('a');
                    link.href = '#';
                    link.querySelector('span').textContent = message.attachment_name;
                }

                container.appendChild(messageElement);

                // Scroll to bottom
                const messagesContainer = document.getElementById('messages-container');
                messagesContainer.scrollTop = messagesContainer.scrollHeight;

                if (!isOwnMessage) {
                    this.playNotificationSound();
                }
            }

            initializeRealTime() {
                console.log('🚀 Initializing real-time chat...');

                if (typeof Pusher !== 'undefined') {
                    console.log('✅ Pusher is available, setting up real-time...');

                    // Enable Pusher logging (disable in production)
                    Pusher.logToConsole = true;

                    this.pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
                        cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
                        encrypted: true,
                        forceTLS: true
                    });

                    this.updateConnectionStatus('connected');

                } else {
                    console.error('❌ Pusher not available');
                    this.setupPollingFallback();
                }
            }

            updateRealTimeSubscription(chatId) {
                if (this.channel) {
                    this.pusher.unsubscribe(this.channel.name);
                }

                if (this.pusher) {
                    this.channel = this.pusher.subscribe(`chat.room.${chatId}`);

                    // Listen for new messages
                    this.channel.bind('message.sent', (data) => {
                        console.log('💬 New message received via Pusher:', data);

                        // Only add messages from other users
                        if (data.message && data.message.user_id !== this.currentUserId) {
                            this.addMessageToChat(data.message);
                            this.playNotificationSound();
                            this.showNotification(`New message from ${data.message.user.name}`);
                        }
                    });

                    // Connection events
                    this.channel.bind('pusher:subscription_succeeded', () => {
                        console.log('✅ Successfully subscribed to Pusher channel');
                        this.updateConnectionStatus('connected');
                    });

                    this.channel.bind('pusher:subscription_error', (status) => {
                        console.error('❌ Pusher subscription error:', status);
                        this.updateConnectionStatus('error');
                        this.setupPollingFallback();
                    });
                }
            }

            setupPollingFallback() {
                console.log('🔄 Using polling fallback');
                this.updateConnectionStatus('polling');
                this.isPolling = true;

                setInterval(() => this.pollForNewMessages(), 2000);
                setTimeout(() => this.pollForNewMessages(), 1000);
            }

            async pollForNewMessages() {
                if (!this.isPolling || !this.currentChatId) return;

                try {
                    // In a real app, this would fetch from your API
                    // For demo, we'll simulate the process

                    // Simulate API call delay
                    await new Promise(resolve => setTimeout(resolve, 100));

                    // Mock new messages (in a real app, this would come from the API)
                    const newMessages = [
                        {
                            id: this.lastMessageId + 1,
                            user_id: 2,
                            user: { name: 'John Doe', role: 'Developer' },
                            message: 'Just pushed the latest changes to the repository.',
                            created_at: new Date().toISOString(),
                            attachment: null,
                            attachment_name: null
                        }
                    ];

                    if (newMessages.length > 0) {
                        newMessages.forEach(message => {
                            // Only add messages from other users
                            if (message.user_id !== this.currentUserId) {
                                this.addMessageToChat(message);
                            }
                            this.lastMessageId = Math.max(this.lastMessageId, message.id);
                        });
                    }
                } catch (error) {
                    console.error('Polling error:', error);
                }
            }

            formatTime(dateString) {
                try {
                    const date = new Date(dateString);
                    return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                } catch (e) {
                    return 'Just now';
                }
            }

            playNotificationSound() {
                try {
                    const audio = new Audio('data:audio/wav;base64,UklGRigAAABXQVZFZm10IBAAAAABAAEARKwAAIhYAQACABAAZGF0YQQAAAAAAA==');
                    audio.volume = 0.2;
                    audio.play();
                } catch (e) {}
            }

            showNotification(message, type = 'info') {
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

            updateConnectionStatus(status) {
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
        }

        // Initialize the chat manager when the page loads
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 Unified Chat page loaded');
            window.chatManager = new ChatManager();
            document.getElementById('message-input').focus();
        });
    </script>
</body>
</html>
