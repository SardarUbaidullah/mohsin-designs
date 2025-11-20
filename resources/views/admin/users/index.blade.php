@extends('admin.layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-4 sm:py-6 lg:py-8">
    <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6 xl:px-8">
        <!-- Header -->
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6 lg:mb-8">
            <div class="mb-4 lg:mb-0">
                <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 truncate">Users Management</h1>
                <p class="text-gray-600 mt-1 sm:mt-2 text-xs sm:text-sm lg:text-base truncate">Manage all system users and their permissions</p>
            </div>
            <a href="{{ route('users.create') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white px-3 sm:px-4 lg:px-6 py-2 sm:py-3 rounded-lg lg:rounded-xl font-medium transition duration-200 flex items-center justify-center shadow-sm w-full lg:w-auto mb-4 lg:mb-0 text-sm sm:text-base">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1 sm:mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                <span class="truncate">Add New User</span>
            </a>
        </div>

        <!-- Professional Filters -->
        <div class="bg-white rounded-xl lg:rounded-2xl shadow-sm border border-gray-200 p-4 sm:p-5 lg:p-6 mb-6 lg:mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 sm:gap-4">
                <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                    <!-- Department Filter -->
                    <div class="min-w-0">
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2 truncate">Filter by Department</label>
                        <select id="department_filter" class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 truncate">
                            <option value="all">All Departments</option>
                            <option value="Not Assigned" {{ request('department') == 'Not Assigned' ? 'selected' : '' }}>Not Assigned</option>
                            @foreach($departments as $department)
                                <option value="{{ $department }}" {{ request('department') == $department ? 'selected' : '' }}>
                                    {{ $department }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Role Filter -->
                    <div class="min-w-0">
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2 truncate">Filter by Role</label>
                        <select id="role_filter" class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 truncate">
                            <option value="all">All Roles</option>
                            <option value="super_admin" {{ request('role') == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Manager</option>
                            <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>Team Member</option>
                            <option value="client" {{ request('role') == 'client' ? 'selected' : '' }}>Client</option>
                        </select>
                    </div>
                </div>

                <!-- Reset Filters -->
                <div class="lg:pt-6 mt-2 sm:mt-0">
                    <button id="reset_filters" class="w-full lg:w-auto px-4 sm:px-6 py-2 sm:py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition duration-200 font-medium flex items-center justify-center text-sm sm:text-base">
                        <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 sm:mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <span class="truncate">Reset Filters</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Professional Mobile Filter Tabs -->
        <div class="lg:hidden mb-4 sm:mb-6">
            <div class="flex space-x-1 bg-white p-1 rounded-lg shadow-sm border border-gray-200 overflow-x-auto">
                <button data-role="super_admin" class="filter-tab active flex-1 px-2 sm:px-4 py-2 sm:py-3 text-xs sm:text-sm font-medium rounded-lg bg-purple-100 text-purple-800 whitespace-nowrap transition-all duration-200 min-w-0 truncate">
                    <span class="truncate">Super Admins ({{ $users->where('role', 'super_admin')->count() }})</span>
                </button>
                <button data-role="admin" class="filter-tab flex-1 px-2 sm:px-4 py-2 sm:py-3 text-xs sm:text-sm font-medium rounded-lg text-gray-600 hover:bg-gray-100 whitespace-nowrap transition-all duration-200 min-w-0 truncate">
                    <span class="truncate">Managers ({{ $users->where('role', 'admin')->count() }})</span>
                </button>
                <button data-role="user" class="filter-tab flex-1 px-2 sm:px-4 py-2 sm:py-3 text-xs sm:text-sm font-medium rounded-lg text-gray-600 hover:bg-gray-100 whitespace-nowrap transition-all duration-200 min-w-0 truncate">
                    <span class="truncate">Team ({{ $users->where('role', 'user')->count() }})</span>
                </button>
                <button data-role="client" class="filter-tab flex-1 px-2 sm:px-4 py-2 sm:py-3 text-xs sm:text-sm font-medium rounded-lg text-gray-600 hover:bg-gray-100 whitespace-nowrap transition-all duration-200 min-w-0 truncate">
                    <span class="truncate">Clients ({{ $users->where('role', 'client')->count() }})</span>
                </button>
            </div>
        </div>

        <!-- Kanban Board -->
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-4 gap-3 sm:gap-4 lg:gap-6 mb-6 lg:mb-8">
            <!-- Super Admins Column -->
            <div class="user-column active bg-gray-50 rounded-xl lg:rounded-2xl p-3 sm:p-4 lg:p-6" data-role="super_admin">
                <div class="flex items-center justify-between mb-3 sm:mb-4 lg:mb-6">
                    <h3 class="text-sm sm:text-base lg:text-lg font-semibold text-gray-900 flex items-center min-w-0">
                        <div class="w-2 h-2 sm:w-2 sm:h-2 lg:w-3 lg:h-3 bg-purple-500 rounded-full mr-2 sm:mr-3 flex-shrink-0"></div>
                        <span class="truncate">Super Admins</span>
                    </h3>
                    <span class="bg-purple-100 text-purple-800 px-2 sm:px-3 py-1 rounded-full text-xs font-medium flex-shrink-0 ml-2">
                        {{ $users->where('role', 'super_admin')->count() }}
                    </span>
                </div>
                <div class="space-y-2 sm:space-y-3 lg:space-y-4">
                    @foreach($users->where('role', 'super_admin') as $user)
                    <div class="bg-white rounded-lg lg:rounded-xl shadow-sm border border-gray-200 p-3 sm:p-4 lg:p-5 hover:shadow-md transition-all duration-200">
                        <div class="flex items-center justify-between mb-2 sm:mb-3 lg:mb-4">
                            <div class="flex items-center space-x-2 sm:space-x-3 min-w-0 flex-1">
                                <div class="w-8 h-8 sm:w-10 sm:h-10 lg:w-12 lg:h-12 rounded-lg lg:rounded-xl bg-purple-500 flex items-center justify-center text-white font-semibold text-xs sm:text-sm lg:text-lg shadow-sm flex-shrink-0">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h4 class="font-semibold text-gray-900 text-xs sm:text-sm lg:text-base truncate">{{ $user->name }}</h4>
                                    <p class="text-xs text-gray-500 truncate">{{ $user->email }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-xs sm:text-sm mb-2 sm:mb-3 lg:mb-4">
                            <span class="bg-purple-100 text-purple-800 px-2 sm:px-3 py-1 rounded-lg text-xs font-medium truncate">
                                Super Admin
                            </span>
                            <span class="text-gray-400 text-xs flex-shrink-0 ml-2">
                                #{{ $user->id }}
                            </span>
                        </div>

                        <!-- Department Info -->
                        <div class="mb-2 sm:mb-3 lg:mb-4">
                            <div class="text-xs text-gray-500 flex items-center min-w-0">
                                <svg class="w-3 h-3 sm:w-3 sm:h-3 lg:w-4 lg:h-4 mr-1 sm:mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                <span class="truncate">{{ $user->department ?? 'Not Assigned' }}</span>
                            </div>
                        </div>
<!-- User Status Section -->
<div class="mb-2 sm:mb-3 lg:mb-4 p-2 sm:p-3 bg-gray-50 rounded-lg border border-gray-200">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-1 sm:space-x-2 min-w-0">
            <svg class="w-3 h-3 sm:w-4 sm:h-4 {{ $user->status === 'active' ? 'text-green-600' : 'text-red-600' }} flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                @if($user->status === 'active')
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                @else
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                @endif
            </svg>
            <span class="text-xs font-medium text-gray-700 truncate">Account Status</span>
        </div>
        <form action="{{ route('users.toggle-status', $user) }}" method="POST" class="inline toggle-status-form flex-shrink-0">
            @csrf
            @method('PATCH')
            <button type="submit"
                    class="relative inline-flex h-5 w-9 sm:h-6 sm:w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 {{ $user->status === 'active' ? 'bg-green-600' : 'bg-red-600' }}"
                    role="switch"
                    aria-checked="{{ $user->status === 'active' ? 'true' : 'false' }}"
                    {{ $user->id === auth()->id() ? 'disabled' : '' }}
                    title="{{ $user->id === auth()->id() ? 'Cannot change your own status' : ($user->status === 'active' ? 'Deactivate User' : 'Activate User') }}">
                <span class="sr-only">Toggle user status</span>
                <span aria-hidden="true"
                      class="pointer-events-none inline-block h-4 w-4 sm:h-5 sm:w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $user->status === 'active' ? 'translate-x-4 sm:translate-x-5' : 'translate-x-0' }}"></span>
            </button>
        </form>
    </div>
    <p class="text-xs text-gray-500 mt-1 sm:mt-2 truncate">
        @if($user->status === 'active')
        <span class="text-green-600 font-medium">✓ Active - Can login</span>
        @if($user->created_at)
        <span class="block text-gray-400">Active since: {{ $user->created_at->format('M d, Y g:i A') }}</span>
        @endif
        @else
        <span class="text-red-600 font-medium">✗ Inactive - Cannot login</span>
        @if($user->deactivated_at)
        <span class="block text-gray-400">Deactivated: {{ $user->deactivated_at->format('M d, Y g:i A') }}</span>
        @endif
        @endif
        @if($user->id === auth()->id())
        <span class="text-orange-600 font-medium block">(Your account)</span>
        @endif
    </p>
</div>
                        <div class="flex items-center justify-between pt-2 sm:pt-3 lg:pt-4 border-t border-gray-100">
                            <div class="text-xs text-gray-500 truncate max-w-[100px] sm:max-w-[120px] lg:max-w-none min-w-0">
                                @if($user->client)
                                <span class="flex items-center min-w-0">
                                    <svg class="w-3 h-3 sm:w-3 sm:h-3 lg:w-4 lg:h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    <span class="truncate">{{ $user->client->name }}</span>
                                </span>
                                @endif
                            </div>
                            <div class="flex space-x-1 sm:space-x-2 flex-shrink-0 ml-2">
                                <a href="{{ route('users.show', $user) }}"
                                   class="text-green-600 hover:text-green-800 p-1 sm:p-2 rounded-lg hover:bg-green-50 transition-colors"
                                   title="View User">
                                    <svg class="w-3 h-3 sm:w-3 sm:h-3 lg:w-4 lg:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                <a href="{{ route('users.edit', $user) }}"
                                   class="text-blue-600 hover:text-blue-800 p-1 sm:p-2 rounded-lg hover:bg-blue-50 transition-colors"
                                   title="Edit User">
                                    <svg class="w-3 h-3 sm:w-3 sm:h-3 lg:w-4 lg:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            onclick="return confirm('Are you sure you want to delete this user?')"
                                            class="text-red-600 hover:text-red-800 p-1 sm:p-2 rounded-lg hover:bg-red-50 transition-colors"
                                            title="Delete User">
                                        <svg class="w-3 h-3 sm:w-3 sm:h-3 lg:w-4 lg:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Managers Column -->
            <div class="user-column bg-gray-50 rounded-xl lg:rounded-2xl p-3 sm:p-4 lg:p-6" data-role="admin">
                <div class="flex items-center justify-between mb-3 sm:mb-4 lg:mb-6">
                    <h3 class="text-sm sm:text-base lg:text-lg font-semibold text-gray-900 flex items-center min-w-0">
                        <div class="w-2 h-2 sm:w-2 sm:h-2 lg:w-3 lg:h-3 bg-blue-500 rounded-full mr-2 sm:mr-3 flex-shrink-0"></div>
                        <span class="truncate">Managers</span>
                    </h3>
                    <span class="bg-blue-100 text-blue-800 px-2 sm:px-3 py-1 rounded-full text-xs font-medium flex-shrink-0 ml-2">
                        {{ $users->where('role', 'admin')->count() }}
                    </span>
                </div>
                <div class="space-y-2 sm:space-y-3 lg:space-y-4">
                    @foreach($users->where('role', 'admin') as $user)
                    <div class="bg-white rounded-lg lg:rounded-xl shadow-sm border border-gray-200 p-3 sm:p-4 lg:p-5 hover:shadow-md transition-all duration-200">
                        <div class="flex items-center justify-between mb-2 sm:mb-3 lg:mb-4">
                            <div class="flex items-center space-x-2 sm:space-x-3 min-w-0 flex-1">
                                <div class="w-8 h-8 sm:w-10 sm:h-10 lg:w-12 lg:h-12 rounded-lg lg:rounded-xl bg-blue-500 flex items-center justify-center text-white font-semibold text-xs sm:text-sm lg:text-lg shadow-sm flex-shrink-0">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h4 class="font-semibold text-gray-900 text-xs sm:text-sm lg:text-base truncate">{{ $user->name }}</h4>
                                    <p class="text-xs text-gray-500 truncate">{{ $user->email }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-xs sm:text-sm mb-2 sm:mb-3 lg:mb-4">
                            <span class="bg-blue-100 text-blue-800 px-2 sm:px-3 py-1 rounded-lg text-xs font-medium truncate">
                                Manager
                            </span>
                            <span class="text-gray-400 text-xs flex-shrink-0 ml-2">
                                #{{ $user->id }}
                            </span>
                        </div>

                        <!-- Project Creation Permission Section -->
                        <div class="mb-2 sm:mb-3 lg:mb-4 p-2 sm:p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-1 sm:space-x-2 min-w-0">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 text-gray-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="text-xs font-medium text-gray-700 truncate">Project Creation</span>
                                </div>
                                <form action="{{ route('admin.users.toggle-project-permission', $user) }}" method="POST" class="inline flex-shrink-0">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="relative inline-flex h-5 w-9 sm:h-6 sm:w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 {{ $user->can_create_project ? 'bg-blue-600' : 'bg-gray-200' }}"
                                            role="switch"
                                            aria-checked="{{ $user->can_create_project ? 'true' : 'false' }}">
                                        <span class="sr-only">Toggle project creation permission</span>
                                        <span aria-hidden="true"
                                              class="pointer-events-none inline-block h-4 w-4 sm:h-5 sm:w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $user->can_create_project ? 'translate-x-4 sm:translate-x-5' : 'translate-x-0' }}"></span>
                                    </button>
                                </form>
                            </div>
                            <p class="text-xs text-gray-500 mt-1 sm:mt-2 truncate">
                                @if($user->can_create_project)
                                <span class="text-green-600 font-medium">✓ Can create projects</span>
                                @else
                                <span class="text-red-600 font-medium">✗ Cannot create projects</span>
                                @endif
                            </p>
                        </div>

                        <!-- Department Info -->
                        <div class="mb-2 sm:mb-3 lg:mb-4">
                            <div class="text-xs text-gray-500 flex items-center min-w-0">
                                <svg class="w-3 h-3 sm:w-3 sm:h-3 lg:w-4 lg:h-4 mr-1 sm:mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                <span class="truncate">{{ $user->department ?? 'Not Assigned' }}</span>
                            </div>
                        </div>
<!-- User Status Section -->
<div class="mb-2 sm:mb-3 lg:mb-4 p-2 sm:p-3 bg-gray-50 rounded-lg border border-gray-200">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-1 sm:space-x-2 min-w-0">
            <svg class="w-3 h-3 sm:w-4 sm:h-4 {{ $user->status === 'active' ? 'text-green-600' : 'text-red-600' }} flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                @if($user->status === 'active')
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                @else
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                @endif
            </svg>
            <span class="text-xs font-medium text-gray-700 truncate">Account Status</span>
        </div>
        <form action="{{ route('users.toggle-status', $user) }}" method="POST" class="inline toggle-status-form flex-shrink-0">
            @csrf
            @method('PATCH')
            <button type="submit"
                    class="relative inline-flex h-5 w-9 sm:h-6 sm:w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 {{ $user->status === 'active' ? 'bg-green-600' : 'bg-red-600' }}"
                    role="switch"
                    aria-checked="{{ $user->status === 'active' ? 'true' : 'false' }}"
                    {{ $user->id === auth()->id() ? 'disabled' : '' }}
                    title="{{ $user->id === auth()->id() ? 'Cannot change your own status' : ($user->status === 'active' ? 'Deactivate User' : 'Activate User') }}">
                <span class="sr-only">Toggle user status</span>
                <span aria-hidden="true"
                      class="pointer-events-none inline-block h-4 w-4 sm:h-5 sm:w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $user->status === 'active' ? 'translate-x-4 sm:translate-x-5' : 'translate-x-0' }}"></span>
            </button>
        </form>
    </div>
    <p class="text-xs text-gray-500 mt-1 sm:mt-2 truncate">
        @if($user->status === 'active')
        <span class="text-green-600 font-medium">✓ Active - Can login</span>
        @if($user->created_at)
        <span class="block text-gray-400">Active since: {{ $user->created_at->format('M d, Y g:i A') }}</span>
        @endif
        @else
        <span class="text-red-600 font-medium">✗ Inactive - Cannot login</span>
        @if($user->deactivated_at)
        <span class="block text-gray-400">Deactivated: {{ $user->deactivated_at->format('M d, Y g:i A') }}</span>
        @endif
        @endif
        @if($user->id === auth()->id())
        <span class="text-orange-600 font-medium block">(Your account)</span>
        @endif
    </p>
</div>
                        <div class="flex items-center justify-between pt-2 sm:pt-3 lg:pt-4 border-t border-gray-100">
                            <div class="flex space-x-1 sm:space-x-2 flex-shrink-0 ml-2">
                                <a href="{{ route('users.show', $user) }}"
                                   class="text-green-600 hover:text-green-800 p-1 sm:p-2 rounded-lg hover:bg-green-50 transition-colors"
                                   title="View User">
                                    <svg class="w-3 h-3 sm:w-3 sm:h-3 lg:w-4 lg:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                <a href="{{ route('users.edit', $user) }}"
                                   class="text-blue-600 hover:text-blue-800 p-1 sm:p-2 rounded-lg hover:bg-blue-50 transition-colors"
                                   title="Edit User">
                                    <svg class="w-3 h-3 sm:w-3 sm:h-3 lg:w-4 lg:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            onclick="return confirm('Are you sure you want to delete this user?')"
                                            class="text-red-600 hover:text-red-800 p-1 sm:p-2 rounded-lg hover:bg-red-50 transition-colors"
                                            title="Delete User">
                                        <svg class="w-3 h-3 sm:w-3 sm:h-3 lg:w-4 lg:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Team Members Column -->
            <div class="user-column bg-gray-50 rounded-xl lg:rounded-2xl p-3 sm:p-4 lg:p-6" data-role="user">
                <div class="flex items-center justify-between mb-3 sm:mb-4 lg:mb-6">
                    <h3 class="text-sm sm:text-base lg:text-lg font-semibold text-gray-900 flex items-center min-w-0">
                        <div class="w-2 h-2 sm:w-2 sm:h-2 lg:w-3 lg:h-3 bg-green-500 rounded-full mr-2 sm:mr-3 flex-shrink-0"></div>
                        <span class="truncate">Team Members</span>
                    </h3>
                    <span class="bg-green-100 text-green-800 px-2 sm:px-3 py-1 rounded-full text-xs font-medium flex-shrink-0 ml-2">
                        {{ $users->where('role', 'user')->count() }}
                    </span>
                </div>
                <div class="space-y-2 sm:space-y-3 lg:space-y-4">
                    @foreach($users->where('role', 'user') as $user)
                    <div class="bg-white rounded-lg lg:rounded-xl shadow-sm border border-gray-200 p-3 sm:p-4 lg:p-5 hover:shadow-md transition-all duration-200">
                        <div class="flex items-center justify-between mb-2 sm:mb-3 lg:mb-4">
                            <div class="flex items-center space-x-2 sm:space-x-3 min-w-0 flex-1">
                                <div class="w-8 h-8 sm:w-10 sm:h-10 lg:w-12 lg:h-12 rounded-lg lg:rounded-xl bg-green-500 flex items-center justify-center text-white font-semibold text-xs sm:text-sm lg:text-lg shadow-sm flex-shrink-0">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h4 class="font-semibold text-gray-900 text-xs sm:text-sm lg:text-base truncate">{{ $user->name }}</h4>
                                    <p class="text-xs text-gray-500 truncate">{{ $user->email }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-xs sm:text-sm mb-2 sm:mb-3 lg:mb-4">
                            <span class="bg-green-100 text-green-800 px-2 sm:px-3 py-1 rounded-lg text-xs font-medium truncate">
                                Team Member
                            </span>
                            <span class="text-gray-400 text-xs flex-shrink-0 ml-2">
                                #{{ $user->id }}
                            </span>
                        </div>

                        <!-- Department Info -->
                        <div class="mb-2 sm:mb-3 lg:mb-4">
                            <div class="text-xs text-gray-500 flex items-center min-w-0">
                                <svg class="w-3 h-3 sm:w-3 sm:h-3 lg:w-4 lg:h-4 mr-1 sm:mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                <span class="truncate">{{ $user->department ?? 'Not Assigned' }}</span>
                            </div>
                        </div>
<!-- User Status Section -->
<div class="mb-2 sm:mb-3 lg:mb-4 p-2 sm:p-3 bg-gray-50 rounded-lg border border-gray-200">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-1 sm:space-x-2 min-w-0">
            <svg class="w-3 h-3 sm:w-4 sm:h-4 {{ $user->status === 'active' ? 'text-green-600' : 'text-red-600' }} flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                @if($user->status === 'active')
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                @else
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                @endif
            </svg>
            <span class="text-xs font-medium text-gray-700 truncate">Account Status</span>
        </div>
        <form action="{{ route('users.toggle-status', $user) }}" method="POST" class="inline toggle-status-form flex-shrink-0">
            @csrf
            @method('PATCH')
            <button type="submit"
                    class="relative inline-flex h-5 w-9 sm:h-6 sm:w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 {{ $user->status === 'active' ? 'bg-green-600' : 'bg-red-600' }}"
                    role="switch"
                    aria-checked="{{ $user->status === 'active' ? 'true' : 'false' }}"
                    {{ $user->id === auth()->id() ? 'disabled' : '' }}
                    title="{{ $user->id === auth()->id() ? 'Cannot change your own status' : ($user->status === 'active' ? 'Deactivate User' : 'Activate User') }}">
                <span class="sr-only">Toggle user status</span>
                <span aria-hidden="true"
                      class="pointer-events-none inline-block h-4 w-4 sm:h-5 sm:w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $user->status === 'active' ? 'translate-x-4 sm:translate-x-5' : 'translate-x-0' }}"></span>
            </button>
        </form>
    </div>
    <p class="text-xs text-gray-500 mt-1 sm:mt-2 truncate">
        @if($user->status === 'active')
        <span class="text-green-600 font-medium">✓ Active - Can login</span>
        @if($user->created_at)
        <span class="block text-gray-400">Active since: {{ $user->created_at->format('M d, Y g:i A') }}</span>
        @endif
        @else
        <span class="text-red-600 font-medium">✗ Inactive - Cannot login</span>
        @if($user->deactivated_at)
        <span class="block text-gray-400">Deactivated: {{ $user->deactivated_at->format('M d, Y g:i A') }}</span>
        @endif
        @endif
        @if($user->id === auth()->id())
        <span class="text-orange-600 font-medium block">(Your account)</span>
        @endif
    </p>
</div>
                        <div class="flex items-center justify-between pt-2 sm:pt-3 lg:pt-4 border-t border-gray-100">
                            <div class="flex space-x-1 sm:space-x-2 flex-shrink-0 ml-2">
                                <a href="{{ route('users.show', $user) }}"
                                   class="text-green-600 hover:text-green-800 p-1 sm:p-2 rounded-lg hover:bg-green-50 transition-colors"
                                   title="View User">
                                    <svg class="w-3 h-3 sm:w-3 sm:h-3 lg:w-4 lg:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                <a href="{{ route('users.edit', $user) }}"
                                   class="text-blue-600 hover:text-blue-800 p-1 sm:p-2 rounded-lg hover:bg-blue-50 transition-colors"
                                   title="Edit User">
                                    <svg class="w-3 h-3 sm:w-3 sm:h-3 lg:w-4 lg:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            onclick="return confirm('Are you sure you want to delete this user?')"
                                            class="text-red-600 hover:text-red-800 p-1 sm:p-2 rounded-lg hover:bg-red-50 transition-colors"
                                            title="Delete User">
                                        <svg class="w-3 h-3 sm:w-3 sm:h-3 lg:w-4 lg:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Clients Column -->
            <div class="user-column bg-gray-50 rounded-xl lg:rounded-2xl p-3 sm:p-4 lg:p-6" data-role="client">
                <div class="flex items-center justify-between mb-3 sm:mb-4 lg:mb-6">
                    <h3 class="text-sm sm:text-base lg:text-lg font-semibold text-gray-900 flex items-center min-w-0">
                        <div class="w-2 h-2 sm:w-2 sm:h-2 lg:w-3 lg:h-3 bg-orange-500 rounded-full mr-2 sm:mr-3 flex-shrink-0"></div>
                        <span class="truncate">Clients</span>
                    </h3>
                    <span class="bg-orange-100 text-orange-800 px-2 sm:px-3 py-1 rounded-full text-xs font-medium flex-shrink-0 ml-2">
                        {{ $users->where('role', 'client')->count() }}
                    </span>
                </div>
                <div class="space-y-2 sm:space-y-3 lg:space-y-4">
                    @foreach($users->where('role', 'client') as $user)
                    <div class="bg-white rounded-lg lg:rounded-xl shadow-sm border border-gray-200 p-3 sm:p-4 lg:p-5 hover:shadow-md transition-all duration-200">
                        <div class="flex items-center justify-between mb-2 sm:mb-3 lg:mb-4">
                            <div class="flex items-center space-x-2 sm:space-x-3 min-w-0 flex-1">
                                <div class="w-8 h-8 sm:w-10 sm:h-10 lg:w-12 lg:h-12 rounded-lg lg:rounded-xl bg-orange-500 flex items-center justify-center text-white font-semibold text-xs sm:text-sm lg:text-lg shadow-sm flex-shrink-0">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h4 class="font-semibold text-gray-900 text-xs sm:text-sm lg:text-base truncate">{{ $user->name }}</h4>
                                    <p class="text-xs text-gray-500 truncate">{{ $user->email }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-xs sm:text-sm mb-2 sm:mb-3 lg:mb-4">
                            <span class="bg-orange-100 text-orange-800 px-2 sm:px-3 py-1 rounded-lg text-xs font-medium truncate">
                                Client
                            </span>
                            <span class="text-gray-400 text-xs flex-shrink-0 ml-2">
                                #{{ $user->id }}
                            </span>
                        </div>

                        <!-- Department Info -->
                        <div class="mb-2 sm:mb-3 lg:mb-4">
                            <div class="text-xs text-gray-500 flex items-center min-w-0">
                                <svg class="w-3 h-3 sm:w-3 sm:h-3 lg:w-4 lg:h-4 mr-1 sm:mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                <span class="truncate">{{ $user->department ?? 'Not Assigned' }}</span>
                            </div>
                        </div>
<!-- User Status Section - Add this after department info and before action buttons -->
<!-- User Status Section -->
<div class="mb-2 sm:mb-3 lg:mb-4 p-2 sm:p-3 bg-gray-50 rounded-lg border border-gray-200">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-1 sm:space-x-2 min-w-0">
            <svg class="w-3 h-3 sm:w-4 sm:h-4 {{ $user->status === 'active' ? 'text-green-600' : 'text-red-600' }} flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                @if($user->status === 'active')
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                @else
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                @endif
            </svg>
            <span class="text-xs font-medium text-gray-700 truncate">Account Status</span>
        </div>
        <form action="{{ route('users.toggle-status', $user) }}" method="POST" class="inline toggle-status-form flex-shrink-0">
            @csrf
            @method('PATCH')
            <button type="submit"
                    class="relative inline-flex h-5 w-9 sm:h-6 sm:w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 {{ $user->status === 'active' ? 'bg-green-600' : 'bg-red-600' }}"
                    role="switch"
                    aria-checked="{{ $user->status === 'active' ? 'true' : 'false' }}"
                    {{ $user->id === auth()->id() ? 'disabled' : '' }}
                    title="{{ $user->id === auth()->id() ? 'Cannot change your own status' : ($user->status === 'active' ? 'Deactivate User' : 'Activate User') }}">
                <span class="sr-only">Toggle user status</span>
                <span aria-hidden="true"
                      class="pointer-events-none inline-block h-4 w-4 sm:h-5 sm:w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $user->status === 'active' ? 'translate-x-4 sm:translate-x-5' : 'translate-x-0' }}"></span>
            </button>
        </form>
    </div>
    <p class="text-xs text-gray-500 mt-1 sm:mt-2 truncate">
        @if($user->status === 'active')
        <span class="text-green-600 font-medium">✓ Active - Can login</span>
        @if($user->created_at)
        <span class="block text-gray-400">Active since: {{ $user->created_at->format('M d, Y g:i A') }}</span>
        @endif
        @else
        <span class="text-red-600 font-medium">✗ Inactive - Cannot login</span>
        @if($user->deactivated_at)
        <span class="block text-gray-400">Deactivated: {{ $user->deactivated_at->format('M d, Y g:i A') }}</span>
        @endif
        @endif
        @if($user->id === auth()->id())
        <span class="text-orange-600 font-medium block">(Your account)</span>
        @endif
    </p>
</div>
                        <div class="flex items-center justify-between pt-2 sm:pt-3 lg:pt-4 border-t border-gray-100">
                            <div class="flex space-x-1 sm:space-x-2 flex-shrink-0 ml-2">
                                <a href="{{ route('users.show', $user) }}"
                                   class="text-green-600 hover:text-green-800 p-1 sm:p-2 rounded-lg hover:bg-green-50 transition-colors"
                                   title="View User">
                                    <svg class="w-3 h-3 sm:w-3 sm:h-3 lg:w-4 lg:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                <a href="{{ route('users.edit', $user) }}"
                                   class="text-blue-600 hover:text-blue-800 p-1 sm:p-2 rounded-lg hover:bg-blue-50 transition-colors"
                                   title="Edit User">
                                    <svg class="w-3 h-3 sm:w-3 sm:h-3 lg:w-4 lg:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            onclick="return confirm('Are you sure you want to delete this user?')"
                                            class="text-red-600 hover:text-red-800 p-1 sm:p-2 rounded-lg hover:bg-red-50 transition-colors"
                                            title="Delete User">
                                        <svg class="w-3 h-3 sm:w-3 sm:h-3 lg:w-4 lg:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Empty State -->
        @if($users->count() == 0)
        <div class="bg-white rounded-xl lg:rounded-2xl shadow-sm border border-gray-200 text-center py-8 sm:py-12 lg:py-16 px-4">
            <div class="w-12 h-12 sm:w-16 sm:h-16 lg:w-20 lg:h-20 bg-gray-100 rounded-xl lg:rounded-2xl flex items-center justify-center mx-auto mb-3 sm:mb-4 lg:mb-6 shadow-sm">
                <svg class="w-6 h-6 sm:w-8 sm:h-8 lg:w-10 lg:h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                </svg>
            </div>
            <h3 class="text-base sm:text-lg lg:text-xl font-semibold text-gray-900 mb-1 sm:mb-2 lg:mb-3 truncate">No users found</h3>
            <p class="text-gray-600 mb-4 sm:mb-6 lg:mb-8 max-w-md mx-auto text-xs sm:text-sm lg:text-base">Get started by adding your first team member or client to the system</p>
            <a href="{{ route('users.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 sm:px-6 lg:px-8 py-2 sm:py-3 rounded-lg lg:rounded-xl font-medium transition duration-200 inline-flex items-center justify-center shadow-sm w-full sm:w-auto text-sm sm:text-base">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1 sm:mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                <span class="truncate">Add First User</span>
            </a>
        </div>
        @endif
    </div>
</div>

<script>
// Professional Filters
document.addEventListener('DOMContentLoaded', function() {
    const departmentFilter = document.getElementById('department_filter');
    const roleFilter = document.getElementById('role_filter');
    const resetFilters = document.getElementById('reset_filters');

    function applyFilters() {
        const department = departmentFilter.value;
        const role = roleFilter.value;

        let url = new URL(window.location.href);
        let params = new URLSearchParams();

        if (department !== 'all') {
            params.set('department', department);
        }

        if (role !== 'all') {
            params.set('role', role);
        }

        const queryString = params.toString();
        window.location.href = queryString ? `${url.pathname}?${queryString}` : url.pathname;
    }

    if (departmentFilter) {
        departmentFilter.addEventListener('change', applyFilters);
    }

    if (roleFilter) {
        roleFilter.addEventListener('change', applyFilters);
    }

    if (resetFilters) {
        resetFilters.addEventListener('click', function() {
            window.location.href = "{{ route('users.index') }}";
        });
    }

    // Mobile filter functionality
    const filterTabs = document.querySelectorAll('.filter-tab');
    const userColumns = document.querySelectorAll('.user-column');

    function initMobileView() {
        if (window.innerWidth < 1024) {
            userColumns.forEach((col, index) => {
                if (index === 0) {
                    col.style.display = 'block';
                } else {
                    col.style.display = 'none';
                }
            });
        } else {
            userColumns.forEach(col => {
                col.style.display = 'block';
            });
        }
    }

    if (filterTabs.length > 0) {
        filterTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const role = this.getAttribute('data-role');

                // Update active tab
                filterTabs.forEach(t => {
                    t.classList.remove('active', 'bg-purple-100', 'text-purple-800');
                    t.classList.add('text-gray-600', 'hover:bg-gray-100');
                });
                this.classList.remove('text-gray-600', 'hover:bg-gray-100');
                this.classList.add('active', 'bg-purple-100', 'text-purple-800');

                // Show selected column, hide others on mobile
                if (window.innerWidth < 1024) {
                    userColumns.forEach(col => {
                        if (col.getAttribute('data-role') === role) {
                            col.style.display = 'block';
                        } else {
                            col.style.display = 'none';
                        }
                    });
                }
            });
        });

        window.addEventListener('resize', initMobileView);
        initMobileView();
    }
});
</script>

<style>
@media (max-width: 1023px) {
    .user-column {
        display: none;
    }
    .user-column:first-child {
        display: block;
    }
}

.filter-tab.active {
    background-color: rgb(243, 232, 255);
    color: rgb(107, 33, 168);
}

.filter-tab {
    transition: all 0.2s ease-in-out;
}

/* Ensure text truncation works properly */
.truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Responsive breakpoints for better mobile experience */
@media (max-width: 640px) {
    .min-w-0 {
        min-width: 0;
    }
}
</style>
@endsection
