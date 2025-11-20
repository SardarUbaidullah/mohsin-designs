@extends("admin.layouts.app")

@section("content")
<div class="max-w-6xl mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Create New Task</h1>
            <p class="text-gray-600 mt-2">Add a new task to the project management system</p>
        </div>
        <a href="{{ route('tasks.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg font-medium transition duration-200 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Tasks
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Task Information</h2>
        </div>
        <div class="p-6">
            <form action="{{ route('tasks.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Task Title *</label>
                        <input
                            type="text"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('title') border-red-500 @enderror"
                            id="title"
                            name="title"
                            value="{{ old('title') }}"
                            required
                            placeholder="Enter task title"
                        >
                        @error('title')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label for="project_id" class="block text-sm font-medium text-gray-700 mb-2">Project *</label>
                        <select
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('project_id') border-red-500 @enderror"
                            id="project_id"
                            name="project_id"
                            required
                        >
                            <option value="">Select Project</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                    {{ $project->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('project_id')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-2">Assigned To</label>
                        <select
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                            id="assigned_to"
                            name="assigned_to"
                        >
                            <option value="">Select Assignee</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="milestone_id" class="block text-sm font-medium text-gray-700 mb-2">Milestone</label>
                        <select
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                            id="milestone_id"
                            name="milestone_id"
                        >
                            <option value="">Select Milestone</option>
                            @if($selectedProject && $milestones->count() > 0)
                                @foreach($milestones as $milestone)
                                    <option value="{{ $milestone->id }}" {{ old('milestone_id') == $milestone->id ? 'selected' : '' }}>
                                        {{ $milestone->title }}
                                        @if($milestone->due_date)
                                            (Due: {{ \Carbon\Carbon::parse($milestone->due_date)->format('M d, Y') }})
                                        @endif
                                        @if($milestone->status === 'completed')
                                            ✅
                                        @endif
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        <p class="mt-1 text-sm text-gray-500" id="milestone-help">
                            Select a project first to see available milestones
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="created_by" class="block text-sm font-medium text-gray-700 mb-2">Created By</label>
                        <select
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                            id="created_by"
                            name="created_by"
                        >
                            <option value="">Select Creator</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('created_by') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                        id="description"
                        name="description"
                        rows="4"
                        placeholder="Enter task description"
                    >{{ old('description') }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                        <select
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                            id="priority"
                            name="priority"
                        >
                            <option value="">Select Priority</option>
                            <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                        </select>
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                            id="status"
                            name="status"
                        >
                            <option value="">Select Status</option>
                            <option value="todo" {{ old('status') == 'todo' ? 'selected' : '' }}>To Do</option>
                            <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="done" {{ old('status') == 'done' ? 'selected' : '' }}>Done</option>
                        </select>
                    </div>

                    <div>
                        <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">Due Date</label>
                        <input
                            type="date"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                            id="due_date"
                            name="due_date"
                            value="{{ old('due_date') }}"
                        >
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                        <input
                            type="date"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                            id="start_date"
                            name="start_date"
                            value="{{ old('start_date') }}"
                        >
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('tasks.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition duration-200 font-medium">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200 font-medium flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Create Task
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const projectSelect = document.getElementById('project_id');
    const milestoneSelect = document.getElementById('milestone_id');
    const milestoneHelp = document.getElementById('milestone-help');

    projectSelect.addEventListener('change', function() {
        const projectId = this.value;

        // Clear current milestones
        milestoneSelect.innerHTML = '<option value="">Select Milestone</option>';
        milestoneHelp.textContent = 'Loading milestones...';

        if (!projectId) {
            milestoneHelp.textContent = 'Select a project first to see available milestones';
            return;
        }

        // Fetch milestones for the selected project
        fetch(`/projects/${projectId}/milestones`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(milestones => {
                milestoneSelect.innerHTML = '<option value="">Select Milestone</option>';

                if (milestones.length === 0) {
                    milestoneSelect.innerHTML = '<option value="">No milestones found for this project</option>';
                    milestoneHelp.textContent = 'No milestones available for this project';
                    return;
                }

                milestones.forEach(milestone => {
                    const option = document.createElement('option');
                    option.value = milestone.id;
                    option.textContent = milestone.display_text;
                    milestoneSelect.appendChild(option);
                });

                milestoneHelp.textContent = `${milestones.length} milestone(s) found for this project`;
            })
            .catch(error => {
                console.error('Error fetching milestones:', error);
                milestoneSelect.innerHTML = '<option value="">Error loading milestones</option>';
                milestoneHelp.textContent = 'Error loading milestones. Please try again.';
            });
    });

    // Trigger change event if project is pre-selected (from query parameter)
    @if($selectedProject)
        projectSelect.dispatchEvent(new Event('change'));
    @endif
});
</script>
@endsection
