@extends("Manager.layouts.app")

@section("content")
<div class="max-w-2xl mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <div class="flex items-center space-x-3 mb-2">
                <a href="{{ route('manager.subtasks.index', $task->id) }}" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <h1 class="text-3xl font-bold text-gray-900">Edit Subtask</h1>
            </div>
            <p class="text-gray-600">Update subtask details</p>
        </div>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
            <div class="flex items-center mb-2">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <span class="font-medium">Please fix the following errors:</span>
            </div>
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Edit Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form action="{{ route('manager.subtasks.update', ['taskId' => $task->id, 'subtaskId' => $subtask->id]) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Task Info -->
            <div class="bg-blue-50 rounded-lg p-4 mb-6">
                <h3 class="text-sm font-medium text-blue-800 mb-2">Parent Task</h3>
                <p class="text-blue-900 font-semibold">{{ $task->title }}</p>
                <p class="text-blue-700 text-sm">Project: {{ $task->project->name }}</p>
            </div>

            <div class="space-y-6">
                <!-- Subtask Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Subtask Title *</label>
                    <input type="text"
                           name="title"
                           id="title"
                           value="{{ old('title', $subtask->title) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                           placeholder="Enter subtask title"
                           required>
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                    <select name="status"
                            id="status"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                            required>
                        <option value="todo" {{ old('status', $subtask->status) == 'todo' ? 'selected' : '' }}>To Do</option>
                        <option value="in_progress" {{ old('status', $subtask->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="done" {{ old('status', $subtask->status) == 'done' ? 'selected' : '' }}>Done</option>
                    </select>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <a href="{{ route('manager.subtasks.index', $task->id) }}"
                   class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition duration-200 font-medium">
                    Cancel
                </a>
                <div class="flex items-center space-x-3">
                    <button type="button"
                            onclick="if(confirm('Are you sure you want to reset the form?')) { this.form.reset(); }"
                            class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition duration-200 font-medium">
                        Reset
                    </button>
                    <button type="submit"
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200 font-medium shadow-sm flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Update Subtask
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Subtask Information -->
    <div class="bg-gray-50 rounded-xl p-6 mt-8 border border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900 mb-3">Subtask Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <span class="font-medium text-gray-600">Created:</span>
                <span class="text-gray-900 ml-2">{{ $subtask->created_at->format('M d, Y H:i') }}</span>
            </div>
            <div>
                <span class="font-medium text-gray-600">Last Updated:</span>
                <span class="text-gray-900 ml-2">{{ $subtask->updated_at->format('M d, Y H:i') }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
