@extends('admin.layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">User Details</h1>
                    <p class="mt-2 text-sm text-gray-600">View user information and details</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('users.edit', $user->id) }}"
                       class="inline-flex items-center px-4 py-2 bg-primary border border-transparent rounded-md font-semibold text-white hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors duration-200">
                        <i class="fas fa-edit mr-2"></i>
                        Edit User
                    </a>
                    <a href="{{ route('users.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Users
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <div class="bg-white shadow-lg rounded-xl border border-gray-200 overflow-hidden">
                    <!-- Card Header -->
                    <div class="px-6 py-4 bg-white border-b border-gray-200">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-r from-primary to-primary/80 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-user text-white text-sm"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Personal Information</h3>
                                <p class="text-sm text-gray-500">Basic user details and contact information</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Full Name</label>
                                <p class="text-lg font-semibold text-gray-900">{{ $user->name }}</p>
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Email Address</label>
                                <p class="text-lg font-semibold text-gray-900">{{ $user->email }}</p>
                            </div>

                            <!-- Role -->
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Role</label>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    {{ $user->role == 'super_admin' ? 'bg-purple-100 text-purple-800' :
                                       ($user->role == 'admin' ? 'bg-red-100 text-red-800' :
                                       ($user->role == 'manager' ? 'bg-blue-100 text-blue-800' :
                                       ($user->role == 'client' ? 'bg-green-100 text-green-800' :
                                       'bg-gray-100 text-gray-800'))) }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </div>

                            <!-- Phone -->
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Phone Number</label>
                                <p class="text-lg font-semibold text-gray-900">{{ $user->phone ?? 'Not provided' }}</p>
                            </div>

                            <!-- Created At -->
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Member Since</label>
                                <p class="text-lg font-semibold text-gray-900">{{ $user->created_at->format('F d, Y') }}</p>
                            </div>

                            <!-- Last Updated -->
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Last Updated</label>
                                <p class="text-lg font-semibold text-gray-900">{{ $user->updated_at->format('F d, Y') }}</p>
                            </div>
                        </div>

                        <!-- Client Information -->
                        @if($user->role == 'client' && $client)
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <div class="flex items-center mb-4">
                                <div class="w-8 h-8 bg-gradient-to-r from-green-500 to-green-600 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-building text-white text-xs"></i>
                                </div>
                                <h4 class="text-lg font-semibold text-gray-900">Client Information</h4>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-green-50 rounded-lg p-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Client ID</label>
                                    <p class="text-lg font-semibold text-gray-900 font-mono">{{ $client->id }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                        {{ $client->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ucfirst($client->status) }}
                                    </span>
                                </div>
                                @if($client->company)
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Company</label>
                                    <p class="text-lg font-semibold text-gray-900">{{ $client->company }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- User Profile Card -->
                <div class="bg-white shadow-lg rounded-xl border border-gray-200 overflow-hidden mb-6">
                    <div class="p-6">
                        <div class="text-center">
                            <div class="w-24 h-24 bg-gradient-to-br from-primary to-green-600 rounded-full flex items-center justify-center text-white font-bold text-3xl mx-auto shadow-lg mb-4">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <h3 class="text-xl font-bold text-gray-900">{{ $user->name }}</h3>
                            <p class="text-sm text-gray-500 mt-1">{{ $user->email }}</p>

                            <div class="mt-4 flex justify-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    {{ $user->role == 'super_admin' ? 'bg-purple-100 text-purple-800' :
                                       ($user->role == 'admin' ? 'bg-red-100 text-red-800' :
                                       ($user->role == 'manager' ? 'bg-blue-100 text-blue-800' :
                                       ($user->role == 'client' ? 'bg-green-100 text-green-800' :
                                       'bg-gray-100 text-gray-800'))) }}">
                                    <i class="fas fa-user-shield mr-1"></i>
                                    {{ ucfirst($user->role) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white shadow-lg rounded-xl border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
                    </div>
                    <div class="p-4">
                        <div class="space-y-3">
                            <a href="{{ route('users.edit', $user->id) }}"
                               class="w-full flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:border-primary hover:bg-primary/5 transition-colors duration-200 group">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3 group-hover:bg-blue-500 transition-colors duration-200">
                                        <i class="fas fa-edit text-blue-600 group-hover:text-white text-sm transition-colors duration-200"></i>
                                    </div>
                                    <span class="font-medium text-gray-700">Edit User</span>
                                </div>
                                <i class="fas fa-chevron-right text-gray-400 group-hover:text-primary transition-colors duration-200"></i>
                            </a>

                            <a href="{{ route('users.index') }}"
                               class="w-full flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:border-gray-400 hover:bg-gray-50 transition-colors duration-200 group">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center mr-3 group-hover:bg-gray-600 transition-colors duration-200">
                                        <i class="fas fa-users text-gray-600 group-hover:text-white text-sm transition-colors duration-200"></i>
                                    </div>
                                    <span class="font-medium text-gray-700">All Users</span>
                                </div>
                                <i class="fas fa-chevron-right text-gray-400 group-hover:text-gray-600 transition-colors duration-200"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
