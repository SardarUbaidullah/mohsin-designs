@extends("admin.layouts.app")

@section("content")
<div class="max-w-6xl mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Edit Task</h1>
            <p class="text-gray-600 mt-2">Update task information and details</p>
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
            <form action="{{ route('tasks.update', $task->id) }}" method="POST" class="space-y-6" id="taskForm">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Task Title *</label>
                        <input
                            type="text"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('title') border-red-500 @enderror"
                            id="title"
                            name="title"
                            value="{{ old('title', $task->title) }}"
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
                                <option value="{{ $project->id }}" {{ old('project_id', $task->project_id) == $project->id ? 'selected' : '' }}>
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
                                <option value="{{ $user->id }}" {{ old('assigned_to', $task->assigned_to) == $user->id ? 'selected' : '' }}>
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
                            @if($milestones->count() > 0)
                                @foreach($milestones as $milestone)
                                    <option value="{{ $milestone->id }}" {{ old('milestone_id', $task->milestone_id) == $milestone->id ? 'selected' : '' }}>
                                        {{ $milestone->title }}
                                        @if($milestone->due_date)
                                            (Due: {{ \Carbon\Carbon::parse($milestone->due_date)->format('M d, Y') }})
                                        @endif
                                        @if($milestone->status === 'completed')
                                            ?
                                        @endif
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        <p class="mt-1 text-sm text-gray-500" id="milestone-help">
                            @if($milestones->count() > 0)
                                {{ $milestones->count() }} milestone(s) available for this project
                            @else
                                No milestones available for this project
                            @endif
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
                                <option value="{{ $user->id }}" {{ old('created_by', $task->created_by) == $user->id ? 'selected' : '' }}>
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
                    >{{ old('description', $task->description) }}</textarea>
                </div>

                <!-- Date Section - Edit Mode (No Date Restrictions) -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                        <select
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                            id="priority"
                            name="priority"
                        >
                            <option value="">Select Priority</option>
                            <option value="low" {{ old('priority', $task->priority) == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ old('priority', $task->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ old('priority', $task->priority) == 'high' ? 'selected' : '' }}>High</option>
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
                            <option value="todo" {{ old('status', $task->status) == 'todo' ? 'selected' : '' }}>To Do</option>
                            <option value="in_progress" {{ old('status', $task->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="done" {{ old('status', $task->status) == 'done' ? 'selected' : '' }}>Done</option>
                        </select>
                    </div>

                    <div>
                        <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Due Date
                            <span class="text-xs text-gray-500 font-normal">(Optional)</span>
                        </label>
                        <input
                            type="date"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('due_date') border-red-500 @enderror"
                            id="due_date"
                            name="due_date"
                            value="{{ old('due_date', $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('Y-m-d') : '') }}"
                        >
                        @error('due_date')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                        <div id="due_date_validation" class="mt-1 text-xs"></div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Start Date
                            <span class="text-xs text-gray-500 font-normal">(Optional)</span>
                        </label>
                        <input
                            type="date"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('start_date') border-red-500 @enderror"
                            id="start_date"
                            name="start_date"
                            value="{{ old('start_date', $task->start_date ? \Carbon\Carbon::parse($task->start_date)->format('Y-m-d') : '') }}"
                        >
                        @error('start_date')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                        <div id="start_date_validation" class="mt-1 text-xs"></div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Date Information
                        </label>
                        <div id="date_relationship_info" class="text-xs text-gray-500 p-3 bg-gray-50 rounded-lg">
                            <p>• Start date and due date are optional</p>
                            <p>• Due date should be after start date</p>
                            <p>• Existing dates are preserved</p>
                            <p>• Past dates are allowed for editing</p>
                        </div>
                    </div>
                </div>

                <!-- Date Validation Summary -->
                <div id="date_validation_summary" class="hidden bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-yellow-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <h4 class="text-sm font-medium text-yellow-800">Date Validation Issues</h4>
                    </div>
                    <ul id="date_validation_errors" class="text-sm text-yellow-700 mt-2 space-y-1"></ul>
                </div>

                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('tasks.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition duration-200 font-medium">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200 font-medium flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                        </svg>
                        Update Task
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
    const startDateInput = document.getElementById('start_date');
    const dueDateInput = document.getElementById('due_date');
    const form = document.getElementById('taskForm');

    // Project change event for dynamic milestone loading
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

                    // Preselect if it was the previously selected milestone
                    const currentMilestoneId = '{{ old("milestone_id", $task->milestone_id) }}';
                    if (currentMilestoneId == milestone.id) {
                        option.selected = true;
                    }

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

    // Add event listeners for real-time validation
    startDateInput.addEventListener('input', validateTaskDates);
    dueDateInput.addEventListener('input', validateTaskDates);
    startDateInput.addEventListener('change', validateTaskDates);
    dueDateInput.addEventListener('change', validateTaskDates);

    // Form submission validation
    form.addEventListener('submit', function(e) {
        if (!validateTaskDates()) {
            e.preventDefault();

            // Scroll to first error
            const firstErrorInput = document.querySelector('.border-red-500');
            if (firstErrorInput) {
                firstErrorInput.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                firstErrorInput.focus();
            }
        }
    });

    // Initial validation
    validateTaskDates();
});

function validateTaskDates() {
    const startDateInput = document.getElementById('start_date');
    const dueDateInput = document.getElementById('due_date');
    const startDateValidation = document.getElementById('start_date_validation');
    const dueDateValidation = document.getElementById('due_date_validation');
    const validationSummary = document.getElementById('date_validation_summary');
    const validationErrors = document.getElementById('date_validation_errors');

    const startDate = startDateInput.value ? new Date(startDateInput.value) : null;
    const dueDate = dueDateInput.value ? new Date(dueDateInput.value) : null;

    let errors = [];
    let hasErrors = false;

    // Clear previous validation states
    startDateInput.classList.remove('border-red-500', 'border-green-500', 'border-yellow-500');
    dueDateInput.classList.remove('border-red-500', 'border-green-500', 'border-yellow-500');
    startDateValidation.innerHTML = '';
    dueDateValidation.innerHTML = '';
    startDateValidation.className = 'mt-1 text-xs';
    dueDateValidation.className = 'mt-1 text-xs';

    // Validate Start Date (if provided)
    if (startDateInput.value) {
        startDateInput.classList.add('border-green-500');

        // Check if this is an existing date being preserved
        const originalStartDate = '{{ $task->start_date ? \Carbon\Carbon::parse($task->start_date)->format("Y-m-d") : "" }}';
        if (startDateInput.value === originalStartDate) {
            startDateValidation.innerHTML = '<span class="text-green-600">? Existing start date</span>';
        } else {
            startDateValidation.innerHTML = '<span class="text-green-600">? Start date set</span>';
        }
    } else {
        // Optional field - no validation needed if empty
        startDateValidation.innerHTML = '<span class="text-gray-500">No start date set</span>';
    }

    // Validate Due Date (if provided)
    if (dueDateInput.value) {
        // Check if due date is before start date
        if (startDateInput.value && dueDate <= startDate) {
            dueDateInput.classList.add('border-red-500');
            dueDateValidation.innerHTML = '<span class="text-red-600">Due date must be after start date</span>';
            errors.push('Due date must be after start date');
            hasErrors = true;
        } else {
            dueDateInput.classList.add('border-green-500');

            // Check if this is an existing date being preserved
            const originalDueDate = '{{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format("Y-m-d") : "" }}';

            // Calculate and show task duration if both dates are provided
            if (startDateInput.value && startDateInput.classList.contains('border-green-500')) {
                const timeDiff = dueDate - startDate;
                const daysDiff = Math.ceil(timeDiff / (1000 * 60 * 60 * 24));

                let durationText = '';
                if (dueDateInput.value === originalDueDate) {
                    durationText = '? Existing due date';
                } else {
                    durationText = '? Due date set';
                }

                if (daysDiff === 0) {
                    dueDateValidation.innerHTML = `<span class="text-green-600">${durationText} (same day)</span>`;
                } else if (daysDiff === 1) {
                    dueDateValidation.innerHTML = `<span class="text-green-600">${durationText} (1 day duration)</span>`;
                } else if (daysDiff < 7) {
                    dueDateValidation.innerHTML = `<span class="text-green-600">${durationText} (${daysDiff} days duration)</span>`;
                } else {
                    const weeks = Math.floor(daysDiff / 7);
                    const remainingDays = daysDiff % 7;
                    let durationDetail = ` (${weeks} week${weeks !== 1 ? 's' : ''}`;
                    if (remainingDays > 0) {
                        durationDetail += ` ${remainingDays} day${remainingDays !== 1 ? 's' : ''}`;
                    }
                    durationDetail += ' duration)';
                    dueDateValidation.innerHTML = `<span class="text-green-600">${durationText}${durationDetail}</span>`;
                }
            } else {
                if (dueDateInput.value === originalDueDate) {
                    dueDateValidation.innerHTML = '<span class="text-green-600">? Existing due date</span>';
                } else {
                    dueDateValidation.innerHTML = '<span class="text-green-600">? Due date set</span>';
                }
            }
        }
    } else {
        // Optional field - no validation needed if empty
        dueDateValidation.innerHTML = '<span class="text-gray-500">No due date set</span>';
    }

    // Show warning if only one date is provided
    if ((startDateInput.value && !dueDateInput.value) || (!startDateInput.value && dueDateInput.value)) {
        if (startDateInput.value && !dueDateInput.value) {
            startDateInput.classList.add('border-yellow-500');
            startDateValidation.innerHTML = '<span class="text-yellow-600">? Start date set but no due date</span>';
        }
        if (!startDateInput.value && dueDateInput.value) {
            dueDateInput.classList.add('border-yellow-500');
            dueDateValidation.innerHTML = '<span class="text-yellow-600">? Due date set but no start date</span>';
        }
    }

    // Show/hide validation summary
    if (errors.length > 0) {
        validationErrors.innerHTML = '';
        errors.forEach(error => {
            const li = document.createElement('li');
            li.className = 'flex items-center';
            li.innerHTML = `
                <svg class="w-4 h-4 mr-2 text-yellow-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                ${error}
            `;
            validationErrors.appendChild(li);
        });
        validationSummary.classList.remove('hidden');
    } else {
        validationSummary.classList.add('hidden');
    }

    return !hasErrors;
}
</script>

<style>
.border-green-500 {
    border-color: #10B981 !important;
}

.border-red-500 {
    border-color: #EF4444 !important;
}

.border-yellow-500 {
    border-color: #F59E0B !important;
}

input[type="date"] {
    transition: all 0.3s ease;
}

/* Custom styling for better visual feedback */
input:focus {
    outline: none;
    ring: 2px;
    ring-color: #3B82F6;
}
</style>
@endsection