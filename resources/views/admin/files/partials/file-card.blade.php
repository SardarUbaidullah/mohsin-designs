@php
    // Get the latest version for this file
    $latestVersion = $file->getAllVersions()->sortByDesc('version')->first();
@endphp

<!-- File Header -->
<div class="flex items-start justify-between mb-3">
    <div class="flex items-center space-x-3 flex-1 min-w-0">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm
            {{ $file->is_image ? 'bg-green-100 text-green-600 border border-green-200' :
               ($file->is_pdf ? 'bg-red-100 text-red-600 border border-red-200' : 'bg-blue-100 text-blue-600 border border-blue-200') }}">
            @if($file->is_image)
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            @elseif($file->is_pdf)
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            @else
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            @endif
        </div>
        <div class="flex-1 min-w-0">
            <!-- File Name with Smart Truncation -->
            <div class="relative group">
                @php
                    $fileName = $latestVersion->file_name;
                    $extension = pathinfo($fileName, PATHINFO_EXTENSION);
                    $nameWithoutExt = pathinfo($fileName, PATHINFO_FILENAME);

                    $displayName = strlen($nameWithoutExt) > 20
                        ? substr($nameWithoutExt, 0, 20) . '...'
                        : $nameWithoutExt;

                    $displayNameWithExt = $displayName . ($extension ? '.' . $extension : '');
                @endphp

                <h4 class="font-semibold text-gray-900 text-sm truncate" title="{{ $fileName }}">
                    {{ $displayNameWithExt }}
                </h4>

                @if(strlen($fileName) > 25)
                <div class="absolute bottom-full left-0 mb-2 hidden group-hover:block z-10">
                    <div class="bg-gray-900 text-white text-xs rounded py-1 px-2 whitespace-nowrap max-w-xs break-words">
                        {{ $fileName }}
                    </div>
                    <div class="w-3 h-3 bg-gray-900 transform rotate-45 absolute -bottom-1 left-3"></div>
                </div>
                @endif
            </div>

            <div class="flex items-center space-x-2 mt-1">
                @if($file->project)
                <span class="bg-green-500 text-white px-2 py-1 rounded-lg text-xs font-medium">
                    {{ $file->project->name }}
                </span>
                @else
                <span class="bg-gray-500 text-white px-2 py-1 rounded-lg text-xs font-medium">
                    General
                </span>
                @endif
                <span class="bg-blue-500 text-white px-2 py-1 rounded-lg text-xs font-medium">
                    v{{ $latestVersion->version }}
                </span>
            </div>

            <!-- Access Status for General Files -->
            @if(!$file->project_id)
            <div class="mt-2">
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
                    Restricted ({{ count($file->accessible_users) }} users)
                </span>
                @else
                <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-gray-100 text-gray-800">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Private
                </span>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>

<!-- File Meta -->
<div class="space-y-2 mb-4">
    <div class="flex items-center justify-between text-xs text-gray-500">
        <div class="flex items-center space-x-3">
            @if($file->user)
            <span class="flex items-center">
                <div class="w-6 h-6 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white text-xs font-medium mr-1 shadow-sm">
                    {{ strtoupper(substr($file->user->name, 0, 1)) }}
                </div>
                {{ $file->user->name }}
            </span>
            @endif
        </div>
        <span>{{ $file->created_at->diffForHumans() }}</span>
    </div>
    <div class="flex items-center justify-between text-xs text-gray-500">
        <span class="flex items-center">
            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
            </svg>
            {{ $latestVersion->readable_size }}
        </span>
        <span class="uppercase text-xs font-medium bg-gray-100 px-2 py-1 rounded">{{ $latestVersion->extension }}</span>
    </div>
</div>

@if($file->task)
<div class="mb-4">
    <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-purple-500 text-white">
        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        {{ Str::limit($file->task->title, 25) }}
    </span>
</div>
@endif

@if($latestVersion->description)
<div class="mb-4">
    <p class="text-xs text-gray-600 bg-white p-2 rounded-lg border border-gray-200">
        {{ Str::limit($latestVersion->description, 80) }}
    </p>
</div>
@endif

<!-- Version Info -->
@if($file->child_versions_count > 0)
<div class="mb-4">
    <div class="flex items-center justify-between text-xs">
        <span class="text-gray-600">Version History:</span>
        <span class="bg-orange-500 text-white px-2 py-1 rounded-lg font-medium">
            +{{ $file->child_versions_count }} versions
        </span>
    </div>
</div>
@endif

<!-- File Actions -->
<div class="flex items-center justify-between pt-3 border-t border-gray-200">
    <div class="flex space-x-3">
        <a href="{{ route('files.download', $latestVersion->id) }}"
           class="text-green-600 hover:text-green-800 text-xs font-medium flex items-center transition-colors"
           title="Download Latest Version">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Download
        </a>
        <a href="{{ route('files.show', $file->id) }}"
           class="text-blue-600 hover:text-blue-800 text-xs font-medium flex items-center transition-colors"
           title="View Details">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
            View
        </a>
    </div>
    <div class="flex space-x-1">
        <!-- Manage Access Button (Only for General Files and Super Admin) -->
        @if(auth()->user()->role === 'super_admin' && !$file->project_id)
        <a href="{{ route('files.manage-access', $file->id) }}"
           class="text-gray-400 hover:text-purple-600 p-1 rounded hover:bg-purple-50 transition-colors"
           title="Manage Access">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
        </a>
        @endif

        <a href="{{ route('files.new-version-form', $file->id) }}"
           class="text-gray-400 hover:text-blue-600 p-1 rounded hover:bg-blue-50 transition-colors"
           title="Upload New Version">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
        </a>
        <a href="{{ route('files.edit', $file->id) }}"
           class="text-gray-400 hover:text-yellow-600 p-1 rounded hover:bg-yellow-50 transition-colors"
           title="Edit File">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
        </a>
        <form action="{{ route('files.destroy', $file->id) }}" method="POST" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit"
                    onclick="return confirm('Are you sure you want to delete this file and all its versions?')"
                    class="text-gray-400 hover:text-red-600 p-1 rounded hover:bg-red-50 transition-colors"
                    title="Delete File">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </button>
        </form>
    </div>
</div>
