@extends("admin.layouts.app")

@section("content")
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-6xl mx-auto px-4">
        <!-- Header - Basecamp Style -->
        <div class="mb-8 text-center">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Create New Task</h1>
            <p class="text-gray-600 text-lg">Add a task to your project workflow</p>
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

        <!-- Kanban Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column - Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <form action="{{ route('tasks.store') }}" method="POST" id="taskForm">
                        @csrf

                        <!-- Task Title -->
                        <div class="mb-6">
                            <label for="title" class="block text-sm font-semibold text-gray-900 mb-3">What needs to be done?</label>
                            <input type="text"
                                   name="title"
                                   id="title"
                                   value="{{ old('title') }}"
                                   class="w-full px-4 py-3 text-lg border-0 border-b-2 border-gray-200 focus:border-green-500 focus:ring-0 transition-colors duration-200 bg-gray-50 rounded-t-lg"
                                   placeholder="Write a clear task title..."
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
                                                    {{ (old('project_id') == $project->id || request('project_id') == $project->id) ? 'selected' : '' }}
                                                    data-manager="{{ $project->manager_id }}"
                                                    data-is-manager="true"
                                                    class="flex items-center">
                                                {{ $project->name }}
                                                <span class="ml-2 text-xs text-green-600">(Your Project)</span>
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">All projects are shown for super admin</p>
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
                                                    {{ (old('assigned_to') == $user->id || request('assigned_to') == $user->id) ? 'selected' : '' }}>
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
                                    <option value="">No milestone - assign later</option>
                                    <!-- Milestones will be loaded dynamically -->
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
                                    <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>💤 Low</option>
                                    <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>⚡ Medium</option>
                                    <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>🔥 High</option>
                                </select>
                            </div>

                            <!-- Status -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <select name="status"
                                        id="status"
                                        class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition duration-200">
                                    <option value="todo" {{ old('status', 'todo') == 'todo' ? 'selected' : '' }}>📝 To Do</option>
                                    <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>🔄 In Progress</option>
                                    <option value="done" {{ old('status') == 'done' ? 'selected' : '' }}>✅ Done</option>
                                </select>
                            </div>

                            <!-- Due Date -->
                            <div>
                                <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">Due Date</label>
                                <input type="date"
                                       name="due_date"
                                       id="due_date"
                                       value="{{ old('due_date') }}"
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
                                      placeholder="Add details, instructions, or context for this task...">{{ old('description') }}</textarea>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                            <a href="{{ route('tasks.index', ['project_id' => request('project_id')]) ?: route('tasks.index') }}"
                               class="px-6 py-3 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition duration-200 font-medium flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                                Back to Tasks
                            </a>
                            <div class="flex items-center space-x-3">
                                <button type="button"
                                        onclick="if(confirm('Clear all fields?')) { document.getElementById('taskForm').reset(); resetMilestones(); }"
                                        class="px-6 py-3 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition duration-200 font-medium">
                                    Reset
                                </button>
                                <button type="submit"
                                        class="px-8 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition duration-200 font-medium shadow-sm flex items-center group">
                                    <svg class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                    Create Task
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right Column - Preview & Tips -->
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
                            <span id="preview-status" class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">To Do</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Priority:</span>
                            <span id="preview-priority" class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Medium</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Due Date:</span>
                            <span id="preview-due" class="text-gray-600">Not set</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Assigned:</span>
                            <span id="preview-assigned" class="text-gray-600">Unassigned</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Milestone:</span>
                            <span id="preview-milestone" class="text-gray-600">None</span>
                        </div>
                    </div>
                </div>

                <!-- Project Info -->
                <div class="bg-blue-50 rounded-xl p-6 border border-blue-200">
                    <h3 class="text-lg font-semibold text-blue-900 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Project Details
                    </h3>
                    <div id="project-info" class="text-sm text-blue-800 space-y-2">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span>Select a project to view details</span>
                        </div>
                    </div>
                </div>

                <!-- Quick Tips -->
                <div class="bg-green-50 rounded-xl p-6 border border-green-200">
                    <h3 class="text-lg font-semibold text-green-900 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Task Tips
                    </h3>
                    <ul class="space-y-3 text-sm text-green-800">
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-green-400 rounded-full mt-1.5 mr-3 flex-shrink-0"></span>
                            <span>Assign to milestones for better progress tracking</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-green-400 rounded-full mt-1.5 mr-3 flex-shrink-0"></span>
                            <span>Use clear, action-oriented task titles</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-green-400 rounded-full mt-1.5 mr-3 flex-shrink-0"></span>
                            <span>Set due dates to maintain project momentum</span>
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
    const projectInfo = document.getElementById('project-info');

    // Function to reset milestones
    function resetMilestones() {
        milestoneSelect.innerHTML = '<option value="">No milestone - assign later</option>';
        updateMilestonePreview('None');
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
        
        // Super admin ke liye always allow milestones
        const isSuperAdmin = true; // Since this is admin layout
        const isManagerProject = true; // Super admin ke liye sab projects accessible

        // Reset milestones
        resetMilestones();

        // Update project info
        if (projectId && isManagerProject) {
            updateProjectInfo(selectedOption.text, true);

            // Fetch milestones for selected project
            fetch(`/tasks/projects/${projectId}/milestones`)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(milestones => {
                    if (milestones && milestones.length > 0) {
                        milestones.forEach(milestone => {
                            const option = document.createElement('option');
                            option.value = milestone.id;
                            option.textContent = milestone.display_text;
                            milestoneSelect.appendChild(option);
                        });
                    } else {
                        const option = document.createElement('option');
                        option.value = "";
                        option.textContent = "No milestones available";
                        option.disabled = true;
                        milestoneSelect.appendChild(option);
                    }
                })
                .catch(error => {
                    console.error('Error fetching milestones:', error);
                    const option = document.createElement('option');
                    option.value = "";
                    option.textContent = "Error loading milestones";
                    option.disabled = true;
                    milestoneSelect.appendChild(option);
                });
        } else if (projectId && !isManagerProject) {
            updateProjectInfo(selectedOption.text, false);
            const option = document.createElement('option');
            option.value = "";
            option.textContent = "Milestones not available for this project";
            option.disabled = true;
            milestoneSelect.appendChild(option);
        } else {
            updateProjectInfo('', false);
        }
    });

    // Update milestone preview
    milestoneSelect.addEventListener('change', function() {
        const milestoneText = this.options[this.selectedIndex].text;
        updateMilestonePreview(milestoneText.includes('No milestone') ? 'None' : milestoneText);
    });

    // Update project info display
    function updateProjectInfo(projectName, isManagerProject) {
        if (projectName && isManagerProject) {
            projectInfo.innerHTML = `
                <div class="flex items-center text-green-600 mb-2">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="font-medium">Your Project</span>
                </div>
                <div class="text-blue-800">
                    <strong>${projectName.replace('(Your Project)', '').trim()}</strong>
                </div>
                <div class="text-xs text-blue-600 mt-1">
                    ✓ You can assign milestones to tasks
                </div>
            `;
        } else if (projectName && !isManagerProject) {
            projectInfo.innerHTML = `
                <div class="flex items-center text-orange-600 mb-2">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span class="font-medium">Other Project</span>
                </div>
                <div class="text-blue-800">
                    <strong>${projectName.replace('(Your Project)', '').trim()}</strong>
                </div>
                <div class="text-xs text-orange-600 mt-1">
                    ⚠ Milestone assignment not available
                </div>
            `;
        } else {
            projectInfo.innerHTML = `
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span>Select a project to view details</span>
                </div>
            `;
        }
    }

    // Live preview updates (existing functionality)
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

    // Auto-focus on title field
    document.getElementById('title').focus();

    // Show loading state when form is submitted
    document.getElementById('taskForm').addEventListener('submit', function() {
        const submitButton = this.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.innerHTML = '<svg class="w-5 h-5 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2v4m0 12v4m8-10h-4M6 12H2m15.364-7.364l-2.828 2.828M7.464 17.536l-2.828 2.828m0-12.728l2.828 2.828m9.9 9.9l2.828 2.828"></path></svg> Creating...';
    });

    // Initialize previews
    statusSelect.dispatchEvent(new Event('change'));
    prioritySelect.dispatchEvent(new Event('change'));
    assignedSelect.dispatchEvent(new Event('change'));

    // Trigger project change if pre-selected
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