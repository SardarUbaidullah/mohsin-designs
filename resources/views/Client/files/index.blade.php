<!-- resources/views/client/files/index.blade.php -->
@extends('Client.app')

@section('title', 'Project Files')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Project Files</h1>
        <p class="text-gray-600 mt-2">All files from your projects</p>
    </div>

    <!-- Files List -->
    <div class="bg-white rounded-lg shadow border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">Files ({{ $files->count() }})</h3>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-500">Sorted by: Newest First</span>
                </div>
            </div>
        </div>

        <div class="p-6">
            @if($files->count() > 0)
            <div class="space-y-4">
                @foreach($files as $file)
                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition duration-200">
                    <div class="flex items-center space-x-4 flex-1">
                        <!-- File Icon -->
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            @php
                                $extension = pathinfo($file->original_name, PATHINFO_EXTENSION);
                                $icon = match($extension) {
                                    'pdf' => 'fa-file-pdf',
                                    'doc', 'docx' => 'fa-file-word',
                                    'xls', 'xlsx' => 'fa-file-excel',
                                    'ppt', 'pptx' => 'fa-file-powerpoint',
                                    'zip', 'rar' => 'fa-file-archive',
                                    'jpg', 'jpeg', 'png', 'gif' => 'fa-file-image',
                                    default => 'fa-file'
                                };
                                $color = match($extension) {
                                    'pdf' => 'text-red-500',
                                    'doc', 'docx' => 'text-blue-500',
                                    'xls', 'xlsx' => 'text-green-500',
                                    'ppt', 'pptx' => 'text-orange-500',
                                    'zip', 'rar' => 'text-yellow-500',
                                    'jpg', 'jpeg', 'png', 'gif' => 'text-purple-500',
                                    default => 'text-gray-500'
                                };
                            @endphp
                            <i class="fas {{ $icon }} {{ $color }} text-xl"></i>
                        </div>

                        <!-- File Info -->
                        <div class="flex-1 min-w-0">
                            <h4 class="font-medium text-gray-900 truncate">{{ $file->original_name }}</h4>
                            <div class="flex items-center space-x-4 text-sm text-gray-500 mt-1">
                                <span class="flex items-center">
                                    <i class="fas fa-project-diagram mr-1"></i>
                                    {{ $file->project->name }}
                                </span>
                                <span class="flex items-center">
                                    <i class="fas fa-user mr-1"></i>
                                    {{ $file->uploadedBy->name }}
                                </span>
                                <span class="flex items-center">
                                    <i class="fas fa-calendar mr-1"></i>
                                    {{ $file->created_at->format('M d, Y') }}
                                </span>
                                <span class="flex items-center">
                                    <i class="fas fa-file mr-1"></i>
                                    {{ strtoupper($extension) }} file
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Download Button -->
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('client.files.download', $file) }}"
                           class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-200 flex items-center text-sm font-medium">
                            <i class="fas fa-download mr-2"></i>
                            Download
                        </a>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Empty State -->
            @else
            <div class="text-center py-12">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-folder-open text-gray-400 text-3xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No files yet</h3>
                <p class="text-gray-500 mb-6">Files will appear here once they are uploaded to your projects.</p>
                <a href="{{ route('client.projects') }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                    <i class="fas fa-project-diagram mr-2"></i>
                    View Your Projects
                </a>
            </div>
            @endif
        </div>
    </div>

    <!-- Quick Stats -->
    @if($files->count() > 0)
    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-lg shadow border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg mr-3">
                    <i class="fas fa-file text-blue-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Files</p>
                    <p class="text-xl font-bold text-gray-900">{{ $files->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg mr-3">
                    <i class="fas fa-project-diagram text-green-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Projects with Files</p>
                    <p class="text-xl font-bold text-gray-900">{{ $files->unique('project_id')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg mr-3">
                    <i class="fas fa-calendar text-purple-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Latest Upload</p>
                    <p class="text-sm font-medium text-gray-900">
                        {{ $files->first()->created_at->diffForHumans() }}
                    </p>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
