@php
    $layout = match(Auth::user()->role) {
        'super_admin' => 'admin.layouts.app',
        'admin' => 'manager.layouts.app',
        'user' => 'team.app',
    };
@endphp

@extends($layout)
@section('content')

<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Files Management</h1>
            <p class="text-gray-600 mt-2">Manage and organize all uploaded files with version control</p>
        </div>
        <a href="{{ route('files.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-medium transition duration-200 flex items-center shadow-sm">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
            </svg>
            Upload New File
        </a>
    </div>

    <!-- Role-based Information -->
    @php
        $user = auth()->user();
    @endphp

    @if($user->role === 'admin')
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <p class="text-blue-800 font-medium">Manager Access</p>
                <p class="text-blue-600 text-sm">You can view and manage files from projects you manage.</p>
            </div>
        </div>
    </div>
    @elseif($user->role === 'user')
    <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <p class="text-green-800 font-medium">Team Member Access</p>
                <p class="text-green-600 text-sm">You can view files from projects where you have assigned tasks.</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Total Files</p>
                    <p class="text-3xl font-bold mt-1">{{ $files->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Project Files</p>
                    <p class="text-3xl font-bold mt-1">{{ $files->whereNotNull('project_id')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">General Files</p>
                    <p class="text-3xl font-bold mt-1">{{ $files->whereNull('project_id')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-medium">Total Versions</p>
                    <p class="text-3xl font-bold mt-1">{{ $totalVersions }}</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- View Toggle and Mobile Filter Tabs -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <!-- View Toggle -->
        <div class="flex items-center bg-white rounded-xl shadow-sm border border-gray-200 p-1">
            <button id="gridViewBtn" class="view-toggle-btn active flex items-center px-4 py-2 rounded-lg bg-blue-50 text-blue-600 font-medium transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
                Grid View
            </button>
            <button id="listViewBtn" class="view-toggle-btn flex items-center px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-100 font-medium transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                </svg>
                List View
            </button>
        </div>

        <!-- Professional Mobile Filter Tabs -->
        <div class="lg:hidden">
            <div class="flex space-x-1 bg-white p-1 rounded-xl shadow-sm border border-gray-200 overflow-x-auto">
                <button data-category="recent" class="file-filter-tab active flex-1 px-4 py-3 text-sm font-medium rounded-lg bg-blue-100 text-blue-800 whitespace-nowrap transition-all duration-200">
                    Recent ({{ $files->where('created_at', '>=', now()->subDays(7))->count() }})
                </button>
                <button data-category="project" class="file-filter-tab flex-1 px-4 py-3 text-sm font-medium rounded-lg text-gray-600 hover:bg-gray-100 whitespace-nowrap transition-all duration-200">
                    Project ({{ $files->whereNotNull('project_id')->count() }})
                </button>
                <button data-category="general" class="file-filter-tab flex-1 px-4 py-3 text-sm font-medium rounded-lg text-gray-600 hover:bg-gray-100 whitespace-nowrap transition-all duration-200">
                    General ({{ $files->whereNull('project_id')->count() }})
                </button>
            </div>
        </div>
    </div>

    <!-- Grid View -->
    <div id="gridView" class="file-view active">
        @if($files->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @foreach($files as $file)
                @if($file->canUserAccess(auth()->id()))
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-all duration-200 hover:border-blue-300">
                    @include('admin.files.partials.file-card', ['file' => $file])
                </div>
                @endif
            @endforeach
        </div>
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 text-center py-16">
                <div class="w-20 h-20 bg-gradient-to-br from-blue-100 to-blue-200 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-sm">
                    <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">No files found</h3>
                <p class="text-gray-600 mb-8 max-w-md mx-auto">Get started by uploading your first file to the system.</p>
                <a href="{{ route('files.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-xl font-medium transition duration-200 inline-flex items-center shadow-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    Upload Your First File
                </a>
            </div>
        @endif
    </div>

    <!-- List View -->
    <div id="listView" class="file-view hidden">
        @if($files->count() > 0)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">File</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project/Task</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Access</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Size & Type</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Uploaded</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($files as $file)
                                @if($file->canUserAccess(auth()->id()))
                                @php
                                    $latestVersion = $file->getAllVersions()->sortByDesc('version')->first();
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm mr-4
                                                {{ $file->is_image ? 'bg-green-100 text-green-600 border border-green-200' :
                                                   ($file->is_pdf ? 'bg-red-100 text-red-600 border border-red-200' : 'bg-blue-100 text-blue-600 border border-blue-200') }}">
                                                @if($file->is_image)
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                @elseif($file->is_pdf)
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                    </svg>
                                                @else
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                    </svg>
                                                @endif
                                            </div>
                                            <div class="min-w-0">
                                                <div class="text-sm font-medium text-gray-900 truncate max-w-xs">
                                                    {{ $latestVersion->file_name }}
                                                </div>
                                                <div class="flex items-center space-x-2 mt-1">
                                                    <span class="bg-blue-500 text-white px-2 py-1 rounded-lg text-xs font-medium">
                                                        v{{ $latestVersion->version }}
                                                    </span>
                                                    @if($file->child_versions_count > 0)
                                                    <span class="bg-orange-500 text-white px-2 py-1 rounded-lg text-xs font-medium">
                                                        +{{ $file->child_versions_count }}
                                                    </span>
                                                    @endif
                                                </div>
                                                @if($latestVersion->description)
                                                <div class="text-xs text-gray-500 mt-1 truncate max-w-xs">
                                                    {{ $latestVersion->description }}
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="space-y-2">
                                            @if($file->project)
                                            <span class="bg-green-500 text-white px-2 py-1 rounded-lg text-xs font-medium">
                                                {{ $file->project->name }}
                                            </span>
                                            @else
                                            <span class="bg-gray-500 text-white px-2 py-1 rounded-lg text-xs font-medium">
                                                General
                                            </span>
                                            @endif
                                            @if($file->task)
                                            <div class="text-xs text-purple-600 font-medium">
                                                {{ Str::limit($file->task->title, 25) }}
                                            </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if(!$file->project_id)
                                            @if($file->is_public)
                                            <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-green-100 text-green-800">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                Public
                                            </span>
                                            @elseif($file->accessible_users && count($file->accessible_users) > 0)
                                            <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-blue-100 text-blue-800">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                                </svg>
                                                Restricted ({{ count($file->accessible_users) }})
                                            </span>
                                            @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-gray-100 text-gray-800">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                                </svg>
                                                Private
                                            </span>
                                            @endif
                                        @else
                                        <span class="text-xs text-gray-500">Project File</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $latestVersion->readable_size }}</div>
                                        <div class="text-xs text-gray-500 uppercase">{{ $latestVersion->extension }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $file->created_at->diffForHumans() }}</div>
                                        <div class="text-xs text-gray-500">
                                            by {{ $file->user->name ?? 'Unknown' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ route('files.download', $latestVersion->id) }}"
                                               class="text-green-600 hover:text-green-800 p-2 rounded-lg hover:bg-green-50 transition-colors"
                                               title="Download">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                </svg>
                                            </a>
                                            <a href="{{ route('files.show', $file->id) }}"
                                               class="text-blue-600 hover:text-blue-800 p-2 rounded-lg hover:bg-blue-50 transition-colors"
                                               title="View">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </a>
                                            @if(auth()->user()->role === 'super_admin' && !$file->project_id)
                                            <a href="{{ route('files.manage-access', $file->id) }}"
                                               class="text-purple-600 hover:text-purple-800 p-2 rounded-lg hover:bg-purple-50 transition-colors"
                                               title="Manage Access">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                                </svg>
                                            </a>
                                            @endif
                                            <a href="{{ route('files.new-version-form', $file->id) }}"
                                               class="text-gray-600 hover:text-blue-600 p-2 rounded-lg hover:bg-blue-50 transition-colors"
                                               title="New Version">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                </svg>
                                            </a>
                                            <a href="{{ route('files.edit', $file->id) }}"
                                               class="text-gray-600 hover:text-yellow-600 p-2 rounded-lg hover:bg-yellow-50 transition-colors"
                                               title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>
                                            <form action="{{ route('files.destroy', $file->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        onclick="return confirm('Are you sure you want to delete this file and all its versions?')"
                                                        class="text-gray-600 hover:text-red-600 p-2 rounded-lg hover:bg-red-50 transition-colors"
                                                        title="Delete">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 text-center py-16">
                <div class="w-20 h-20 bg-gradient-to-br from-blue-100 to-blue-200 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-sm">
                    <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">No files found</h3>
                <p class="text-gray-600 mb-8 max-w-md mx-auto">Get started by uploading your first file to the system.</p>
                <a href="{{ route('files.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-xl font-medium transition duration-200 inline-flex items-center shadow-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    Upload Your First File
                </a>
            </div>
        @endif
    </div>
</div>

<script>
// Professional file filter and view toggle
document.addEventListener('DOMContentLoaded', function() {
    const filterTabs = document.querySelectorAll('.file-filter-tab');
    const fileColumns = document.querySelectorAll('.file-column');
    
    // View toggle elements
    const gridViewBtn = document.getElementById('gridViewBtn');
    const listViewBtn = document.getElementById('listViewBtn');
    const gridView = document.getElementById('gridView');
    const listView = document.getElementById('listView');
    const viewToggleBtns = document.querySelectorAll('.view-toggle-btn');

    // View toggle functionality
    gridViewBtn.addEventListener('click', function() {
        switchView('grid');
    });

    listViewBtn.addEventListener('click', function() {
        switchView('list');
    });

    function switchView(viewType) {
        // Update button states
        viewToggleBtns.forEach(btn => {
            btn.classList.remove('active', 'bg-blue-50', 'text-blue-600');
            btn.classList.add('text-gray-600', 'hover:bg-gray-100');
        });
        
        if (viewType === 'grid') {
            gridViewBtn.classList.add('active', 'bg-blue-50', 'text-blue-600');
            gridViewBtn.classList.remove('text-gray-600', 'hover:bg-gray-100');
            gridView.classList.remove('hidden');
            gridView.classList.add('active');
            listView.classList.add('hidden');
            listView.classList.remove('active');
        } else {
            listViewBtn.classList.add('active', 'bg-blue-50', 'text-blue-600');
            listViewBtn.classList.remove('text-gray-600', 'hover:bg-gray-100');
            listView.classList.remove('hidden');
            listView.classList.add('active');
            gridView.classList.add('hidden');
            gridView.classList.remove('active');
        }
    }

    // Initialize mobile view
    function initMobileView() {
        if (window.innerWidth < 1024) {
            fileColumns.forEach((col, index) => {
                if (index === 0) {
                    col.style.display = 'block';
                } else {
                    col.style.display = 'none';
                }
            });
        } else {
            fileColumns.forEach(col => {
                col.style.display = 'block';
            });
        }
    }

    // Filter tab click handler
    filterTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const category = this.getAttribute('data-category');

            // Update active tab
            filterTabs.forEach(t => {
                t.classList.remove('active', 'bg-blue-100', 'text-blue-800');
                t.classList.add('text-gray-600', 'hover:bg-gray-100');
            });
            this.classList.remove('text-gray-600', 'hover:bg-gray-100');
            this.classList.add('active', 'bg-blue-100', 'text-blue-800');

            // Show selected column, hide others on mobile
            if (window.innerWidth < 1024) {
                fileColumns.forEach(col => {
                    if (col.getAttribute('data-category') === category) {
                        col.style.display = 'block';
                    } else {
                        col.style.display = 'none';
                    }
                });
            }
        });
    });

    // Handle window resize
    window.addEventListener('resize', function() {
        initMobileView();
    });

    // Initial setup
    initMobileView();
});
</script>

<style>
@media (max-width: 1023px) {
    .file-column {
        display: none;
    }
    .file-column:first-child {
        display: block;
    }
}

.file-filter-tab.active {
    background-color: rgb(219, 234, 254);
    color: rgb(29, 78, 216);
}

.file-filter-tab {
    transition: all 0.2s ease-in-out;
}

.view-toggle-btn.active {
    background-color: rgb(239, 246, 255);
    color: rgb(37, 99, 235);
}

.view-toggle-btn {
    transition: all 0.2s ease-in-out;
}

.file-view {
    transition: opacity 0.3s ease-in-out;
}

.hover\:shadow-md {
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}
.shadow-sm {
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
}
.rounded-2xl {
    border-radius: 1rem;
}
.rounded-xl {
    border-radius: 0.75rem;
}

/* Smooth gradients */
.bg-gradient-to-br {
    background-image: linear-gradient(135deg, var(--tw-gradient-from), var(--tw-gradient-to));
}

/* Backdrop blur for modern look */
.backdrop-blur-sm {
    backdrop-filter: blur(4px);
}

/* Ensure text truncation works properly */
.truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endsection