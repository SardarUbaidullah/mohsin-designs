<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-5 hover:shadow-md transition-all duration-200">
    <div class="flex items-center justify-between mb-3 sm:mb-4">
        <div class="flex items-center space-x-2 sm:space-x-3">
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-{{ $color }}-500 flex items-center justify-center text-white font-semibold text-sm sm:text-lg shadow-sm">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div class="min-w-0 flex-1">
                <h4 class="font-semibold text-gray-900 text-sm sm:text-base truncate">{{ $user->name }}</h4>
                <p class="text-xs sm:text-sm text-gray-500 truncate">{{ $user->email }}</p>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-between text-xs sm:text-sm mb-3 sm:mb-4">
        <span class="bg-{{ $color }}-100 text-{{ $color }}-800 px-2 sm:px-3 py-1 rounded-lg text-xs font-medium">
            @if($user->role === 'super_admin') Super Admin
            @elseif($user->role === 'admin') Manager
            @elseif($user->role === 'user') Team Member
            @elseif($user->role === 'client') Client
            @endif
        </span>
        <span class="text-gray-400 text-xs">
            #{{ $user->id }}
        </span>
    </div>

    <div class="flex items-center justify-between pt-3 sm:pt-4 border-t border-gray-100">
        <div class="text-xs text-gray-500 truncate max-w-[120px] sm:max-w-none">
            @if($user->client)
            <span class="flex items-center">
                <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <span class="truncate">{{ $user->client->name }}</span>
            </span>
            @endif
        </div>
        <div class="flex space-x-1 sm:space-x-2">
            <!-- Show Button -->
            <a href="{{ route('users.show', $user) }}"
               class="text-green-600 hover:text-green-800 p-1 sm:p-2 rounded-lg hover:bg-green-50 transition-colors"
               title="View User">
                <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </a>
            <!-- Edit Button -->
            <a href="{{ route('users.edit', $user) }}"
               class="text-blue-600 hover:text-blue-800 p-1 sm:p-2 rounded-lg hover:bg-blue-50 transition-colors"
               title="Edit User">
                <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </a>
            <!-- Delete Button -->
            <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit"
                        onclick="return confirm('Are you sure you want to delete this user?')"
                        class="text-red-600 hover:text-red-800 p-1 sm:p-2 rounded-lg hover:bg-red-50 transition-colors"
                        title="Delete User">
                    <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</div>
