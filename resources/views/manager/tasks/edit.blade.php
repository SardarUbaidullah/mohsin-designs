
@php
    $layout = match(Auth::user()->role) {
        'super_admin' => 'admin.layouts.app',
        'admin' => 'manager.layouts.app',
        'user' => 'team.app',
    };
@endphp
@extends($layout)
@section("content")
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-6xl mx-auto px-4">
        <!-- Header - Basecamp Style -->
        <div class="mb-8 text-center">
            <div class="flex items-center justify-center space-x-3 mb-2">
                <a href="{{ route('manager.tasks.show', $task->id) }}" class="text-gray-500 hover:text-gray-700 transition duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <h1 class="text-3xl font-bold text-gray-900">Edit Task</h1>
            </div>
            <p class="text-gray-600 text-lg">Update task details and assignment</p>
        </div>

        @if($errors->any())
            <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-red-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <span class="font-medium text-red-800">Please fix the following errors:</span>
                </div>
                <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6 rounded-lg flex items-center">
                <svg class="w-5 h-5 text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span class="text-green-700">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Kanban Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column - Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <form action="{{ route('manager.tasks.update', $task->id) }}" method="POST" id="taskForm">
                        @csrf
                        @method('PUT')

                        <!-- Task Title -->
                        <div class="mb-6">
                            <label for="title" class="block text-sm font-semibold text-gray-900 mb-3">Task Title</label>
                            <input type="text"
                                   name="title"
                                   id="title"
                                   value="{{ old('title', $task->title) }}"
                                   class="w-full px-4 py-3 text-lg border-0 border-b-2 border-gray-200 focus:border-green-500 focus:ring-0 transition-colors duration-200 bg-gray-50 rounded-t-lg"
                                   placeholder="Update task title..."
                                   required
                                   autofocus>
                        </div>

                        <!-- Project & Assignment -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- Project Selection -->
                            <div>
                                <label for="project_id" class="block text-sm font-medium text-gray-700 mb-2">Project</label>
                                <div class="relative">
                                    <select name="project_id"
                                            id="project_id"
                                            class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition duration-200 appearance-none"
                                            required>
                                        <option value="">Choose a project...</option>
                                        @foreach($projects as $project)
                                            <option value="{{ $project->id }}"
                                                    {{ old('project_id', $task->project_id) == $project->id ? 'selected' : '' }}
                                                    data-manager="{{ $project->manager_id }}"
                                                    class="flex items-center">
                                                {{ $project->name }}
                                                @if($project->manager_id == auth()->id())
                                                    <span class="ml-2 text-xs text-green-600">(Your Project)</span>
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Only your assigned projects are shown</p>
                            </div>

                            <!-- Assign To -->
                            <div>
                                <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-2">Assign to</label>
                                <div class="relative">
                                    <select name="assigned_to"
                                            id="assigned_to"
                                            class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition duration-200 appearance-none">
                                        <option value="">Unassigned</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}"
                                                    {{ old('assigned_to', $task->assigned_to) == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                                <span class="text-xs text-gray-500">({{ ucfirst($user->role) }})</span>
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Milestone Selection -->
                        <div class="mb-6">
                            <label for="milestone_id" class="block text-sm font-medium text-gray-700 mb-2">Milestone (Optional)</label>
                            <div class="relative">
                                <select name="milestone_id"
                                        id="milestone_id"
                                        class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition duration-200 appearance-none">
                                    <option value="">No milestone</option>
                                    @foreach($milestones as $milestone)
                                        <option value="{{ $milestone->id }}"
                                                {{ old('milestone_id', $task->milestone_id) == $milestone->id ? 'selected' : '' }}>
                                            {{ $milestone->title }}
                                            @if($milestone->due_date)
                                                (Due: {{ \Carbon\Carbon::parse($milestone->due_date)->format('M d, Y') }})
                                            @endif
                                            @if($milestone->status === 'completed')
                                                ✅
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Assign this task to a milestone for better tracking</p>
                        </div>

                        <!-- Task Details Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <!-- Priority -->
                            <div>
                                <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                                <select name="priority"
                                        id="priority"
                                        class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition duration-200">
                                    <option value="low" {{ old('priority', $task->priority) == 'low' ? 'selected' : '' }}>💤 Low</option>
                                    <option value="medium" {{ old('priority', $task->priority) == 'medium' ? 'selected' : '' }}>⚡ Medium</option>
                                    <option value="high" {{ old('priority', $task->priority) == 'high' ? 'selected' : '' }}>🔥 High</option>
                                </select>
                            </div>

                            <!-- Status -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <select name="status"
                                        id="status"
                                        class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition duration-200">
                                    <option value="todo" {{ old('status', $task->status) == 'todo' ? 'selected' : '' }}>📝 To Do</option>
                                    <option value="in_progress" {{ old('status', $task->status) == 'in_progress' ? 'selected' : '' }}>🔄 In Progress</option>
                                    <option value="done" {{ old('status', $task->status) == 'done' ? 'selected' : '' }}>✅ Done</option>
                                </select>
                            </div>

                            <!-- Due Date -->
                            <div>
                                <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">Due Date</label>
                                <input type="date"
                                       name="due_date"
                                       id="due_date"
                                       value="{{ old('due_date', $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('Y-m-d') : '') }}"
                                       class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition duration-200"
                                       min="{{ date('Y-m-d') }}">
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-6">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea name="description"
                                      id="description"
                                      rows="5"
                                      class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition duration-200 resize-none"
                                      placeholder="Update task description, instructions, or context...">{{ old('description', $task->description) }}</textarea>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                            <a href="{{ route('manager.tasks.show', $task->id) }}"
                               class="px-6 py-3 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition duration-200 font-medium flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                                Back to Task
                            </a>
                            <div class="flex items-center space-x-3">
                                <button type="button"
                                        onclick="if(confirm('Reset all changes?')) { document.getElementById('taskForm').reset(); resetMilestones(); }"
                                        class="px-6 py-3 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition duration-200 font-medium">
                                    Reset
                                </button>
                                <button type="submit"
                                        class="px-8 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition duration-200 font-medium shadow-sm flex items-center group">
                                    <svg class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Update Task
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right Column - Preview & Info -->
            <div class="space-y-6">
                <!-- Task Preview -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Task Preview
                    </h3>
                    <div class="space-y-3 text-sm text-gray-600">
                        <div class="flex justify-between">
                            <span class="font-medium">Status:</span>
                            <span id="preview-status" class="px-2 py-1 rounded-full text-xs
                                {{ $task->status == 'todo' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $task->status == 'in_progress' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $task->status == 'done' ? 'bg-green-100 text-green-800' : '' }}">
                                @if($task->status == 'todo') To Do @endif
                                @if($task->status == 'in_progress') In Progress @endif
                                @if($task->status == 'done') Done @endif
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Priority:</span>
                            <span id="preview-priority" class="px-2 py-1 rounded-full text-xs
                                {{ $task->priority == 'low' ? 'bg-gray-100 text-gray-800' : '' }}
                                {{ $task->priority == 'medium' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $task->priority == 'high' ? 'bg-red-100 text-red-800' : '' }}">
                                @if($task->priority == 'low') Low @endif
                                @if($task->priority == 'medium') Medium @endif
                                @if($task->priority == 'high') High @endif
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Due Date:</span>
                            <span id="preview-due" class="text-gray-600">
                                {{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('M d, Y') : 'Not set' }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Assigned:</span>
                            <span id="preview-assigned" class="text-gray-600">
                                {{ $task->assignee->name ?? 'Unassigned' }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Milestone:</span>
                            <span id="preview-milestone" class="text-gray-600">
                                {{ $task->milestone->title ?? 'None' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Task Information -->
                <div class="bg-blue-50 rounded-xl p-6 border border-blue-200">
                    <h3 class="text-lg font-semibold text-blue-900 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Task Information
                    </h3>
                    <div class="space-y-2 text-sm text-blue-800">
                        <div class="flex justify-between">
                            <span class="font-medium">Created:</span>
                            <span>{{ $task->created_at->format('M d, Y H:i') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Last Updated:</span>
                            <span>{{ $task->updated_at->format('M d, Y H:i') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Created By:</span>
                            <span>{{ $task->user->name ?? 'System' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Project:</span>
                            <span class="font-semibold">{{ $task->project->name }}</span>
                        </div>
                    </div>
                </div>

                <!-- Quick Tips -->
                <div class="bg-green-50 rounded-xl p-6 border border-green-200">
                    <h3 class="text-lg font-semibold text-green-900 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Editing Tips
                    </h3>
                    <ul class="space-y-3 text-sm text-green-800">
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-green-400 rounded-full mt-1.5 mr-3 flex-shrink-0"></span>
                            <span>Update milestones to track progress across related tasks</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-green-400 rounded-full mt-1.5 mr-3 flex-shrink-0"></span>
                            <span>Changing status will update the task workflow</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-green-400 rounded-full mt-1.5 mr-3 flex-shrink-0"></span>
                            <span>Reassigning tasks will notify the new team member</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const projectSelect = document.getElementById('project_id');
    const milestoneSelect = document.getElementById('milestone_id');
    const statusSelect = document.getElementById('status');
    const prioritySelect = document.getElementById('priority');
    const dueDateInput = document.getElementById('due_date');
    const assignedSelect = document.getElementById('assigned_to');

    // Function to reset milestones
    function resetMilestones() {
        // This will be handled by the project change event
        projectSelect.dispatchEvent(new Event('change'));
    }

    // Function to update milestone preview
    function updateMilestonePreview(milestoneName) {
        const previewMilestone = document.getElementById('preview-milestone');
        previewMilestone.textContent = milestoneName;
    }

    // Load milestones when project changes
    projectSelect.addEventListener('change', function() {
        const projectId = this.value;
        const selectedOption = this.options[this.selectedIndex];
        const isManagerProject = selectedOption.getAttribute('data-manager') === '{{ auth()->id() }}';

        if (projectId && isManagerProject) {
            // Fetch milestones for selected project
            fetch(`/manager/tasks/projects/${projectId}/milestones`)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(milestones => {
                    // Store current milestone value
                    const currentMilestoneId = '{{ old("milestone_id", $task->milestone_id) }}';

                    milestoneSelect.innerHTML = '<option value="">No milestone</option>';

                    if (milestones.length > 0) {
                        milestones.forEach(milestone => {
                            const option = document.createElement('option');
                            option.value = milestone.id;
                            option.textContent = milestone.display_text;
                            if (milestone.id == currentMilestoneId) {
                                option.selected = true;
                            }
                            milestoneSelect.appendChild(option);
                        });
                    }

                    // Update preview
                    const selectedMilestone = milestoneSelect.options[milestoneSelect.selectedIndex];
                    updateMilestonePreview(selectedMilestone ? selectedMilestone.text : 'None');
                })
                .catch(error => {
                    console.error('Error fetching milestones:', error);
                    milestoneSelect.innerHTML = '<option value="">Error loading milestones</option>';
                });
        }
    });

    // Update milestone preview
    milestoneSelect.addEventListener('change', function() {
        const milestoneText = this.options[this.selectedIndex].text;
        updateMilestonePreview(milestoneText.includes('No milestone') ? 'None' : milestoneText);
    });

    // Live preview updates
    statusSelect.addEventListener('change', function() {
        const statusText = this.options[this.selectedIndex].text;
        const previewStatus = document.getElementById('preview-status');
        previewStatus.textContent = statusText.replace(/[📝🔄✅]/g, '').trim();
        previewStatus.className = 'px-2 py-1 rounded-full text-xs ';
        switch(this.value) {
            case 'todo': previewStatus.classList.add('bg-blue-100', 'text-blue-800'); break;
            case 'in_progress': previewStatus.classList.add('bg-yellow-100', 'text-yellow-800'); break;
            case 'done': previewStatus.classList.add('bg-green-100', 'text-green-800'); break;
        }
    });

    prioritySelect.addEventListener('change', function() {
        const priorityText = this.options[this.selectedIndex].text;
        const previewPriority = document.getElementById('preview-priority');
        previewPriority.textContent = priorityText.replace(/[💤⚡🔥]/g, '').trim();
        previewPriority.className = 'px-2 py-1 rounded-full text-xs ';
        switch(this.value) {
            case 'low': previewPriority.classList.add('bg-gray-100', 'text-gray-800'); break;
            case 'medium': previewPriority.classList.add('bg-yellow-100', 'text-yellow-800'); break;
            case 'high': previewPriority.classList.add('bg-red-100', 'text-red-800'); break;
        }
    });

    dueDateInput.addEventListener('change', function() {
        const previewDue = document.getElementById('preview-due');
        previewDue.textContent = this.value ? new Date(this.value).toLocaleDateString('en-US', {
            weekday: 'short', month: 'short', day: 'numeric'
        }) : 'Not set';
    });

    assignedSelect.addEventListener('change', function() {
        const previewAssigned = document.getElementById('preview-assigned');
        previewAssigned.textContent = this.value ? this.options[this.selectedIndex].text : 'Unassigned';
    });

    // Set minimum due date to today
    if (dueDateInput) {
        dueDateInput.min = new Date().toISOString().split('T')[0];
    }

    // Show loading state when form is submitted
    document.getElementById('taskForm').addEventListener('submit', function() {
        const submitButton = this.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.innerHTML = '<svg class="w-5 h-5 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2v4m0 12v4m8-10h-4M6 12H2m15.364-7.364l-2.828 2.828M7.464 17.536l-2.828 2.828m0-12.728l2.828 2.828m9.9 9.9l2.828 2.828"></path></svg> Updating...';
    });

    // Initialize previews with current values
    statusSelect.dispatchEvent(new Event('change'));
    prioritySelect.dispatchEvent(new Event('change'));
    assignedSelect.dispatchEvent(new Event('change'));

    // Trigger project change to load milestones
    if (projectSelect.value) {
        projectSelect.dispatchEvent(new Event('change'));
    }
});
</script>

<style>
/* Custom select styling */
select {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
    background-position: right 0.5rem center;
    background-repeat: no-repeat;
    background-size: 1.5em 1.5em;
    padding-right: 2.5rem;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
}

select::-ms-expand { display: none; }

/* Smooth transitions */
input, select, textarea {
    transition: all 0.2s ease-in-out;
}

input:focus, select:focus, textarea:focus {
    transform: translateY(-1px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}
</style>
@endsection
