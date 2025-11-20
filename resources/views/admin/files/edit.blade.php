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
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Edit File</h1>
            <p class="text-gray-600 mt-2">Update file information and metadata</p>
        </div>
        <a href="{{ route('files.show', $file->id) }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg font-medium transition duration-200 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to File
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">File Information</h2>
        </div>
        <div class="p-6">
            <form action="{{ route('files.update', $file->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="file_path" class="block text-sm font-medium text-gray-700 mb-2">Replace File</label>
                        <div class="relative">
                            <input
                                type="file"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('file_path') border-red-500 @enderror file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                id="file_path"
                                name="file_path"
                            >
                        </div>
                        <div class="mt-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <p class="text-sm font-medium text-gray-700 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Current file: <span class="font-semibold ml-1">{{ $file->file_name }}</span>
                            </p>
                        </div>
                        <p class="mt-2 text-sm text-gray-500 flex items-center">
                            <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Maximum file size: 10MB
                        </p>
                        @error('file_path')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label for="file_name" class="block text-sm font-medium text-gray-700 mb-2">File Name *</label>
                        <input
                            type="text"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                            id="file_name"
                            name="file_name"
                            value="{{ old('file_name', $file->file_name) }}"
                            placeholder="Enter file name"
                            required
                        >
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Project Selection -->
                    <div>
                        <label for="project_id" class="block text-sm font-medium text-gray-700 mb-2">Project</label>
                        <select
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                            id="project_id"
                            name="project_id"
                        >
                            <option value="">Select Project</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ old('project_id', $file->project_id) == $project->id ? 'selected' : '' }}>
                                    {{ $project->name }}
                                    @if(auth()->user()->role === 'admin')
                                    (Manager)
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @if(auth()->user()->role === 'user')
                        <p class="mt-2 text-sm text-gray-500">You can only assign to projects where you have assigned tasks.</p>
                        @endif
                    </div>

                    <!-- Task Selection -->
                    <div>
                        <label for="task_id" class="block text-sm font-medium text-gray-700 mb-2">Task</label>
                        <select
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                            id="task_id"
                            name="task_id"
                        >
                            <option value="">Select Task</option>
                            @foreach($tasks as $task)
                                <option value="{{ $task->id }}" {{ old('task_id', $file->task_id) == $task->id ? 'selected' : '' }}>
                                    {{ $task->title }} - {{ $task->project->name }}
                                </option>
                            @endforeach
                        </select>
                        @if(auth()->user()->role === 'user')
                        <p class="mt-2 text-sm text-gray-500">You can only assign to tasks assigned to you.</p>
                        @endif
                    </div>
                </div>

                <!-- Uploaded By (Display only, cannot change) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Uploaded By</label>
                    <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-medium text-sm">
                            {{ strtoupper(substr($file->user->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $file->user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $file->user->email }}</p>
                        </div>
                    </div>
                    <input type="hidden" name="user_id" value="{{ $file->user_id }}">
                    <p class="mt-2 text-sm text-gray-500">Uploader cannot be changed after file creation.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="version" class="block text-sm font-medium text-gray-700 mb-2">Version *</label>
                        <input
                            type="number"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                            id="version"
                            name="version"
                            value="{{ old('version', $file->version) }}"
                            min="1"
                            placeholder="Enter version number"
                            required
                        >
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea
                            name="description"
                            id="description"
                            rows="3"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                            placeholder="Enter file description..."
                        >{{ old('description', $file->description) }}</textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('files.show', $file->id) }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition duration-200 font-medium">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200 font-medium flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Update File
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const projectSelect = document.getElementById('project_id');
    const taskSelect = document.getElementById('task_id');

    if (projectSelect && taskSelect) {
        projectSelect.addEventListener('change', function() {
            const projectId = this.value;
            const currentTasks = @json($tasks->keyBy('id')->toArray());

            // Clear and disable task select
            taskSelect.innerHTML = '<option value="">Select Task</option>';

            if (projectId) {
                // Filter tasks by selected project
                Object.values(currentTasks).forEach(task => {
                    if (task.project_id == projectId) {
                        const option = new Option(
                            task.title + ' - ' + task.project.name,
                            task.id
                        );
                        taskSelect.add(option);
                    }
                });
            }
        });

        // Trigger change event on page load to set initial tasks
        const initialProjectId = projectSelect.value;
        if (initialProjectId) {
            projectSelect.dispatchEvent(new Event('change'));

            // Set the initial task value after populating tasks
            setTimeout(() => {
                const initialTaskId = '{{ old("task_id", $file->task_id) }}';
                if (initialTaskId) {
                    taskSelect.value = initialTaskId;
                }
            }, 100);
        }
    }
});
</script>
@endsection
