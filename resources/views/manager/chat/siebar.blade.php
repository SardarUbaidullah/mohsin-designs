   <div class="lg:col-span-1 space-y-6">
                <!-- Project Chats -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-primary-500 to-primary-600 px-4 py-3">
                        <h3 class="text-lg font-semibold text-white flex items-center">
                            <i class="fas fa-users mr-2"></i>
                            Project Chats
                        </h3>
                    </div>
                    <div class="p-4 max-h-96 overflow-y-auto">
                        @forelse($projectRooms as $room)
                        <a href="{{ route('manager.chat.project', $room->project) }}"
                           class="flex items-center space-x-3 p-3 rounded-xl hover:bg-gray-50 transition duration-150 mb-2 border border-gray-100">
                            <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl flex items-center justify-center text-white">
                                <i class="fas fa-project-diagram"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-semibold text-gray-900 truncate">{{ $room->name }}</h4>
                                <p class="text-xs text-gray-500 truncate">{{ $room->project->name }}</p>
                                @if($room->messages->count() > 0)
                                <p class="text-xs text-gray-600 mt-1 truncate">
                                    {{ $room->messages->first()->user->name }}:
                                    {{ Str::limit($room->messages->first()->message, 30) }}
                                </p>
                                @endif
                            </div>
                            @if($room->unreadMessagesCount(auth()->id()) > 0)
                            <span class="bg-red-500 text-white text-xs font-bold w-5 h-5 rounded-full flex items-center justify-center">
                                {{ $room->unreadMessagesCount(auth()->id()) }}
                            </span>
                            @endif
                        </a>
                        @empty
                        <div class="text-center py-6">
                            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-users text-gray-400"></i>
                            </div>
                            <p class="text-gray-500 text-sm">No project chats yet</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Direct Messages -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-green-500 to-green-600 px-4 py-3">
                        <h3 class="text-lg font-semibold text-white flex items-center">
                            <i class="fas fa-comment-dots mr-2"></i>
                            Direct Messages
                        </h3>
                    </div>
                    <div class="p-4 max-h-96 overflow-y-auto">
                        @forelse($directRooms as $room)
                        @php
                            $otherUser = $room->participants->where('user_id', '!=', auth()->id())->first()->user;
                        @endphp
                        <a href="{{ route('manager.chat.direct', $otherUser) }}"
                           class="flex items-center space-x-3 p-3 rounded-xl hover:bg-gray-50 transition duration-150 mb-2 border border-gray-100">
                            <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-green-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                {{ strtoupper(substr($otherUser->name, 0, 1)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-semibold text-gray-900">{{ $otherUser->name }}</h4>
                                <p class="text-xs text-gray-500">{{ $otherUser->role }}</p>
                                @if($room->messages->count() > 0)
                                <p class="text-xs text-gray-600 mt-1 truncate">
                                    {{ Str::limit($room->messages->first()->message, 30) }}
                                </p>
                                @endif
                            </div>
                            @if($room->unreadMessagesCount(auth()->id()) > 0)
                            <span class="bg-red-500 text-white text-xs font-bold w-5 h-5 rounded-full flex items-center justify-center">
                                {{ $room->unreadMessagesCount(auth()->id()) }}
                            </span>
                            @endif
                        </a>
                        @empty
                        <div class="text-center py-6">
                            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-comment text-gray-400"></i>
                            </div>
                            <p class="text-gray-500 text-sm">No direct messages</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Start New Chat -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-orange-500 to-orange-600 px-4 py-3">
                        <h3 class="text-lg font-semibold text-white flex items-center">
                            <i class="fas fa-plus mr-2"></i>
                            Start New Chat
                        </h3>
                    </div>
                    <div class="p-4">
                        <p class="text-sm text-gray-600 mb-3">Start a conversation with:</p>
                        <div class="space-y-2 max-h-48 overflow-y-auto">
                            @foreach($availableUsers as $user)
                            <a href="{{ route('manager.chat.direct', $user) }}"
                               class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-50 transition duration-150">
                                <div class="w-8 h-8 bg-gradient-to-r from-orange-500 to-orange-600 rounded-full flex items-center justify-center text-white text-xs font-semibold">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-500 capitalize">{{ $user->role }}</p>
                                </div>
                            </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
