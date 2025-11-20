@php
    $layout = match(Auth::user()->role) {
        'super_admin' => 'admin.layouts.app',
        'admin' => 'manager.layouts.app',
        'user' => 'team.app',

    };
@endphp

@extends($layout)

@section('content')

<div class="max-w-6xl mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">File Details</h1>
            <p class="text-gray-600 mt-2">View and manage file information and versions</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('files.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg font-medium transition duration-200 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Files
            </a>
            <a href="{{ route('files.edit', $file->id) }}" class="bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-3 rounded-lg font-medium transition duration-200 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
            </a>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- File Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- File Preview Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">File Preview</h2>
                </div>
                <div class="p-6">
                    @if($file->is_image)
                        <div class="text-center">
                            <img src="{{ Storage::disk('public')->url($file->file_path) }}"
                                 alt="{{ $file->file_name }}"
                                 class="max-w-full h-auto max-h-96 mx-auto rounded-lg shadow-sm">
                        </div>
                    @elseif($file->is_pdf)
                        <div class="text-center bg-gray-50 rounded-lg p-8">
                            <div class="w-20 h-20 bg-red-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <p class="text-gray-600 mb-4">PDF Document</p>
                            <a href="{{ route('files.preview', $file->id) }}" target="_blank"
                               class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-medium transition duration-200 inline-flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Preview PDF
                            </a>
                        </div>
                    @else
                        <div class="text-center bg-gray-50 rounded-lg p-8">
                            <div class="w-20 h-20 bg-blue-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <p class="text-gray-600 mb-2">File Type: {{ $file->mime_type }}</p>
                            <p class="text-gray-600 mb-4">Size: {{ $file->readable_size }}</p>
                            <a href="{{ route('files.download', $file->id) }}"
                               class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition duration-200 inline-flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                Download File
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- File Details Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">File Information</h2>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">File Name</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $file->file_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Version</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-medium">
                                    v{{ $file->version }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">File Size</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $file->readable_size }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">File Type</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $file->mime_type }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Uploaded By</dt>
                            <dd class="mt-1 text-sm text-gray-900 flex items-center">
                                <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs font-medium mr-2">
                                    {{ strtoupper(substr($file->user->name, 0, 1)) }}
                                </div>
                                {{ $file->user->name }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Upload Date</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $file->created_at->format('M d, Y \a\t h:i A') }}</dd>
                        </div>
                        @if($file->project)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Project</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded-lg text-xs font-medium">
                                    {{ $file->project->name }}
                                </span>
                            </dd>
                        </div>
                        @endif
                        @if($file->task)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Task</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded-lg text-xs font-medium">
                                    {{ $file->task->title }}
                                </span>
                            </dd>
                        </div>
                        @endif
                    </dl>

                @if($file->description)
<div class="mt-6">
    <dt class="text-sm font-medium text-gray-500">Description</dt>
    <dd class="mt-1 text-sm text-gray-900 bg-gray-50 p-4 rounded-lg overflow-x-auto">
        <div class="whitespace-pre-line break-words min-w-0">
            {!! preg_replace('/(https?:\/\/[^\s]+)/', '<a href="$1" target="_blank" class="text-blue-600 hover:text-blue-800 hover:underline transition-colors duration-200 break-all">$1</a>', e($file->description)) !!}
        </div>
    </dd>
</div>
@endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Actions Card -->
          <div class="p-6 space-y-3">
    <a href="{{ route('files.download', $file->id) }}"
       class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg font-medium transition duration-200 flex items-center justify-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
        </svg>
        Download File
    </a>

    <!-- Updated: Link to the new version form page -->
    <a href="{{ route('files.new-version-form', $file->id) }}"
       class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg font-medium transition duration-200 flex items-center justify-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
        </svg>
        Upload New Version
    </a>


    @if(auth()->user()->role === 'super_admin' && !$file->project_id)
<!-- Manage Access Button (Only for General Files and Super Admin) -->
<a href="{{ route('files.manage-access', $file->id) }}"
   class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-3 rounded-lg font-medium transition duration-200 flex items-center justify-center">
    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
    </svg>
    Manage Access
</a>
@endif


    <form action="{{ route('files.destroy', $file->id) }}" method="POST" class="inline w-full">
        @csrf
        @method('DELETE')
        <button type="submit"
                onclick="return confirm('Are you sure you want to delete this file and all its versions?')"
                class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-3 rounded-lg font-medium transition duration-200 flex items-center justify-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            Delete File
        </button>
    </form>
</div>
            <!-- New Version Form -->
            <div id="newVersionForm" class="hidden bg-white rounded-2xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 bg-blue-50 border-b border-blue-200">
                    <h2 class="text-lg font-medium text-blue-900">Upload New Version</h2>
                </div>
                <div class="p-6">
                    <form action="{{ route('files.new-version', $file->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label for="file_path" class="block text-sm font-medium text-gray-700 mb-2">New File *</label>
                                <input type="file" name="file_path" required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                            </div>
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Version Notes</label>
                                <textarea name="description" rows="3"
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                          placeholder="What's changed in this version?"></textarea>
                            </div>
                            <button type="submit"
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg font-medium transition duration-200">
                                Upload New Version
                            </button>
                        </div>
                    </form>
                </div>
            </div>

          <!-- Version History -->
<!-- Version History -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-200">
    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
        <h2 class="text-lg font-medium text-gray-900">
            Version History ({{ $file->getAllVersions()->count() }})
        </h2>
    </div>
    <div class="p-6">
        <div class="space-y-4">
            @php
                $allVersions = $file->getAllVersions();
                $currentVersionId = $file->id;
            @endphp

            @foreach($allVersions as $version)
            <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 rounded-xl border transition-all duration-200 hover:shadow-md
                {{ $version->id == $currentVersionId ? 'bg-blue-50 border-blue-300 shadow-sm' : 'bg-white border-gray-200 hover:border-gray-300' }}">

                <!-- Left Section: Version Info -->
                <div class="flex items-start space-x-4 flex-1 min-w-0 mb-3 sm:mb-0">
                    <!-- Version Badge -->
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow-sm
                            {{ $version->id == $currentVersionId ? 'bg-blue-500 shadow-blue-200' : 'bg-gray-100 border border-gray-200' }}">
                            <span class="text-sm font-bold {{ $version->id == $currentVersionId ? 'text-white' : 'text-gray-700' }}">
                                v{{ $version->version }}
                            </span>
                        </div>
                    </div>

                    <!-- File Details -->
                    <div class="flex-1 min-w-0">
                        <!-- File Name Row -->
                        <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-3 mb-2">
                            <div class="relative group flex-1 min-w-0">
                                @php
                                    $fileName = $version->file_name;
                                    $extension = pathinfo($fileName, PATHINFO_EXTENSION);
                                    $nameWithoutExt = pathinfo($fileName, PATHINFO_FILENAME);

                                    // Smart truncation for different screen sizes
                                    $maxLengthMobile = 20;
                                    $maxLengthTablet = 30;
                                    $maxLengthDesktop = 40;

                                    $displayName = strlen($nameWithoutExt) > $maxLengthMobile
                                        ? substr($nameWithoutExt, 0, $maxLengthMobile) . '...'
                                        : $nameWithoutExt;

                                    $displayNameWithExt = $displayName . ($extension ? '.' . $extension : '');
                                @endphp

                                <p class="text-base font-semibold text-gray-900 truncate md:break-all"
                                   title="{{ $fileName }}">
                                    {{ $displayNameWithExt }}
                                </p>

                                <!-- Tooltip -->
                                @if(strlen($fileName) > 25)
                                <div class="absolute bottom-full left-0 mb-2 hidden group-hover:block z-50">
                                    <div class="bg-gray-900 text-white text-sm rounded-lg py-2 px-3 max-w-xs break-words shadow-xl">
                                        <div class="font-medium">Full filename:</div>
                                        <div class="text-gray-200 mt-1">{{ $fileName }}</div>
                                    </div>
                                    <div class="w-3 h-3 bg-gray-900 transform rotate-45 absolute -bottom-1 left-4"></div>
                                </div>
                                @endif
                            </div>

                            @if($version->id == $currentVersionId)
                            <span class="bg-blue-500 text-white px-3 py-1 rounded-full text-xs font-bold shadow-sm mt-2 sm:mt-0 sm:ml-2 inline-flex items-center self-start sm:self-center">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                CURRENT
                            </span>
                            @endif
                        </div>

                        <!-- Meta Information -->
                        <div class="flex flex-wrap items-center gap-3 text-sm text-gray-600">
                            <!-- Upload Info -->
                            <div class="flex items-center space-x-1">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>{{ $version->created_at->format('M j, Y') }}</span>
                            </div>

                            <!-- User -->
                            <div class="flex items-center space-x-1">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span>{{ $version->user->name }}</span>
                            </div>

                            <!-- File Size -->
                            <div class="flex items-center space-x-1">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                                </svg>
                                <span>{{ $version->readable_size }}</span>
                            </div>

                            <!-- File Type -->
                            <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs font-medium">
                                {{ strtoupper($version->extension) }}
                            </span>
                        </div>

                        <!-- Description -->
                        @if($version->description)
                        <div class="mt-2">
                            <p class="text-sm text-gray-700 bg-gray-50 rounded-lg p-2 border border-gray-200">
                                {{ $version->description }}
                            </p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Right Section: Actions -->
                <div class="flex items-center justify-end space-x-2 sm:space-x-1 sm:pl-4 border-t pt-3 sm:pt-0 sm:border-t-0 sm:justify-start">
                    <!-- Download Button -->
                    <a href="{{ route('files.download', $version->id) }}"
                       class="flex items-center justify-center w-10 h-10 bg-green-500 hover:bg-green-600 text-white rounded-xl transition-all duration-200 shadow-sm hover:shadow-md"
                       title="Download this version">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                    </a>

                    <!-- View Button (only for non-current versions) -->
                    @if($version->id != $currentVersionId)
                    <a href="{{ route('files.show', $version->id) }}"
                       class="flex items-center justify-center w-10 h-10 bg-blue-500 hover:bg-blue-600 text-white rounded-xl transition-all duration-200 shadow-sm hover:shadow-md"
                       title="View this version">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </a>
                    @endif
                </div>
            </div>
            @endforeach

            @if($allVersions->isEmpty())
            <div class="text-center py-8">
                <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <p class="text-gray-500 text-lg font-medium">No version history yet</p>
                <p class="text-gray-400 text-sm mt-1">Upload a new version to see the history here</p>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
/* Smooth animations */
.transition-all {
    transition-property: all;
}

/* Better shadow effects */
.hover\:shadow-md:hover {
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

/* Tooltip animations */
.group:hover .group-hover\:block {
    display: block;
    animation: tooltipFade 0.2s ease-out;
}

@keyframes tooltipFade {
    from {
        opacity: 0;
        transform: translateY(8px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Mobile responsive improvements */
@media (max-width: 640px) {
    .filename-container {
        max-width: calc(100vw - 120px);
    }
}

/* Better text breaking */
.break-all {
    word-break: break-all;
}
</style>
        </div>
    </div>
</div>
@endsection
