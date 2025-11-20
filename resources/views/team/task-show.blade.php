@php
    $layout = match(Auth::user()->role) {
        'super_admin' => 'admin.layouts.app',
        'admin' => 'Manager.layouts.app',
        'user' => 'team.app',
    };
@endphp
@extends($layout)
@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div class="mb-6 lg:mb-0">
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('team.tasks.index') }}"
                           class="w-10 h-10 bg-white border border-gray-300 rounded-lg flex items-center justify-center text-gray-600 hover:bg-blue-50 hover:border-blue-300 hover:text-blue-600 transition-colors">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">{{ $task->title }}</h1>
                            <p class="text-gray-600">Task details and management</p>
                        </div>
                    </div>
                </div>

                <!-- Status & Priority Badges -->
                <div class="flex flex-wrap gap-3">
                    <span class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium
                        @if($task->status == 'done') bg-green-100 text-green-800 border border-green-200
                        @elseif($task->status == 'in_progress') bg-blue-100 text-blue-800 border border-blue-200
                        @else bg-yellow-100 text-yellow-800 border border-yellow-200 @endif">
                        <i class="fas
                            @if($task->status == 'done') fa-check-circle
                            @elseif($task->status == 'in_progress') fa-spinner fa-spin
                            @else fa-clock @endif mr-2 text-xs">
                        </i>
                        {{ str_replace('_', ' ', ucfirst($task->status)) }}
                    </span>
                    <span class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium
                        @if($task->priority == 'high') bg-red-100 text-red-800 border border-red-200
                        @elseif($task->priority == 'medium') bg-yellow-100 text-yellow-800 border border-yellow-200
                        @else bg-gray-100 text-gray-800 border border-gray-200 @endif">
                        <i class="fas fa-flag mr-2 text-xs"></i>
                        {{ ucfirst($task->priority) }} Priority
                    </span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content - Task Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Task Information Card -->
                <div class="bg-white rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Task Information</h2>
                    </div>
                    <div class="p-6">
                        <!-- Description Section -->
                        @if($task->description)
                        <div class="mb-6">
                            <h3 class="text-sm font-medium text-gray-700 mb-3">Description</h3>
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $task->description }}</p>
                            </div>
                        </div>
                        @endif

                        <!-- Task Details Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Project Info -->
                            <div class="space-y-4">
                                <h3 class="text-sm font-medium text-gray-700">Project Details</h3>
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                                        <span class="text-sm text-gray-700">Project Name</span>
                                        <span class="text-sm font-semibold text-blue-700">{{ $task->project->name }}</span>
                                    </div>
                                    @if($task->project->manager)
                                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                        <span class="text-sm text-gray-700">Project Manager</span>
                                        <span class="text-sm text-gray-900">{{ $task->project->manager->name }}</span>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Timeline & Status -->
                            <div class="space-y-4">
                                <h3 class="text-sm font-medium text-gray-700">Timeline & Status</h3>
                                <div class="space-y-3">
                                    @if($task->due_date)
                                    @php
                                        $dueDate = \Carbon\Carbon::parse($task->due_date);
                                        $isOverdue = $dueDate->isPast() && $task->status != 'done';
                                        $isDueSoon = $dueDate->diffInDays(now()) <= 2 && !$isOverdue;
                                    @endphp
                                    <div class="flex justify-between items-center p-3 rounded-lg
                                        @if($isOverdue) bg-red-50 border border-red-200
                                        @elseif($isDueSoon) bg-orange-50 border border-orange-200
                                        @else bg-gray-50 border border-gray-200 @endif">
                                        <span class="text-sm text-gray-700">Due Date</span>
                                        <div class="text-right">
                                            <span class="text-sm font-semibold
                                                @if($isOverdue) text-red-700
                                                @elseif($isDueSoon) text-orange-700
                                                @else text-gray-900 @endif">
                                                {{ $dueDate->format('M d, Y') }}
                                            </span>
                                            @if($isOverdue)
                                            <div class="text-xs text-red-600 mt-1">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                                Overdue
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    @endif

                                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg border border-gray-200">
                                        <span class="text-sm text-gray-700">Created</span>
                                        <div class="text-right">
                                            <span class="text-sm text-gray-900">{{ $task->created_at->format('M d, Y') }}</span>
                                            <div class="text-xs text-gray-500">{{ $task->created_at->diffForHumans() }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Information -->
                        @if($task->estimated_hours || $task->actual_hours)
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <h3 class="text-sm font-medium text-gray-700 mb-4">Time Tracking</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @if($task->estimated_hours)
                                <div class="flex justify-between items-center p-4 bg-purple-50 rounded-lg border border-purple-200">
                                    <span class="text-sm text-gray-700">Estimated Hours</span>
                                    <span class="text-lg font-bold text-purple-700">{{ $task->estimated_hours }}h</span>
                                </div>
                                @endif
                                @if($task->actual_hours)
                                <div class="flex justify-between items-center p-4 bg-green-50 rounded-lg border border-green-200">
                                    <span class="text-sm text-gray-700">Actual Hours</span>
                                    <span class="text-lg font-bold text-green-700">{{ $task->actual_hours }}h</span>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Actions Card -->
                <div class="bg-white rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="font-semibold text-gray-900">Quick Actions</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            @if($task->status !== 'done')
                            <button id="markCompleteBtn"
                                    class="w-full flex items-center justify-between p-4 bg-green-50 hover:bg-green-100 border border-green-200 rounded-lg transition-colors group">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3 group-hover:bg-green-600 transition-colors">
                                        <i class="fas fa-check text-green-600 group-hover:text-white"></i>
                                    </div>
                                    <div class="text-left">
                                        <p class="font-semibold text-gray-900">Mark Complete</p>
                                        <p class="text-xs text-gray-500">Update status to done</p>
                                    </div>
                                </div>
                                <i class="fas fa-chevron-right text-gray-400 group-hover:text-green-600"></i>
                            </button>
                            @endif

                            <!-- Status Update Dropdown -->
                            <div class="relative">
                                <button id="statusDropdownBtn"
                                        class="w-full flex items-center justify-between p-4 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-lg transition-colors group">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3 group-hover:bg-blue-600 transition-colors">
                                            <i class="fas fa-edit text-blue-600 group-hover:text-white"></i>
                                        </div>
                                        <div class="text-left">
                                            <p class="font-semibold text-gray-900">Update Status</p>
                                            <p class="text-xs text-gray-500">Change task progress</p>
                                        </div>
                                    </div>
                                    <i class="fas fa-chevron-down text-gray-400 group-hover:text-blue-600"></i>
                                </button>

                                <!-- Status Dropdown Menu -->
                                <div id="statusDropdown" class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg hidden">
                                    <div class="p-2 space-y-1">
                                        <button class="status-option w-full text-left px-3 py-2 rounded hover:bg-gray-100 transition-colors
                                                    {{ $task->status == 'todo' ? 'bg-yellow-50 text-yellow-800' : 'text-gray-700' }}"
                                                data-status="todo">
                                            <i class="fas fa-clock mr-2 text-xs"></i>
                                            To Do
                                        </button>
                                        <button class="status-option w-full text-left px-3 py-2 rounded hover:bg-gray-100 transition-colors
                                                    {{ $task->status == 'in_progress' ? 'bg-blue-50 text-blue-800' : 'text-gray-700' }}"
                                                data-status="in_progress">
                                            <i class="fas fa-spinner mr-2 text-xs"></i>
                                            In Progress
                                        </button>
                                        <button class="status-option w-full text-left px-3 py-2 rounded hover:bg-gray-100 transition-colors
                                                    {{ $task->status == 'done' ? 'bg-green-50 text-green-800' : 'text-gray-700' }}"
                                                data-status="done">
                                            <i class="fas fa-check-circle mr-2 text-xs"></i>
                                            Done
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <a href="{{ route('team.projects') }}?project={{ $task->project->id }}"
                               class="w-full flex items-center justify-between p-4 bg-purple-50 hover:bg-purple-100 border border-purple-200 rounded-lg transition-colors group">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3 group-hover:bg-purple-600 transition-colors">
                                        <i class="fas fa-external-link-alt text-purple-600 group-hover:text-white"></i>
                                    </div>
                                    <div class="text-left">
                                        <p class="font-semibold text-gray-900">View Project</p>
                                        <p class="text-xs text-gray-500">Go to project details</p>
                                    </div>
                                </div>
                                <i class="fas fa-chevron-right text-gray-400 group-hover:text-purple-600"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Task Metadata Card -->
                <div class="bg-white rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="font-semibold text-gray-900">Task Information</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-gray-600">Task ID</span>
                                <span class="font-mono text-gray-900">#{{ $task->id }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-gray-600">Assigned To</span>
                                <span class="text-gray-900">{{ $task->assignedTo->name ?? 'You' }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2">
                                <span class="text-gray-600">Last Updated</span>
                                <span class="text-gray-900">{{ $task->updated_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success Toast -->
<div id="successToast" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform duration-300">
    <div class="flex items-center space-x-3">
        <i class="fas fa-check-circle text-lg"></i>
        <span class="font-medium" id="toastMessage">Task updated successfully!</span>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const markCompleteBtn = document.getElementById('markCompleteBtn');
    const statusDropdownBtn = document.getElementById('statusDropdownBtn');
    const statusDropdown = document.getElementById('statusDropdown');
    const statusOptions = document.querySelectorAll('.status-option');

    // Mark Complete Button
    if (markCompleteBtn) {
        markCompleteBtn.addEventListener('click', function() {
            completeTask();
        });
    }

    // Status Dropdown Toggle
    if (statusDropdownBtn) {
        statusDropdownBtn.addEventListener('click', function() {
            statusDropdown.classList.toggle('hidden');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            if (!statusDropdownBtn.contains(event.target) && !statusDropdown.contains(event.target)) {
                statusDropdown.classList.add('hidden');
            }
        });
    }

    // Status Option Selection
    statusOptions.forEach(option => {
        option.addEventListener('click', function() {
            const newStatus = this.getAttribute('data-status');
            updateTaskStatus(newStatus);
            statusDropdown.classList.add('hidden');
        });
    });

    // Complete Task Function
    async function completeTask() {
        const button = markCompleteBtn;
        const originalHTML = button.innerHTML;

        // Show loading state
        button.innerHTML = `
            <div class="flex items-center">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-spinner fa-spin text-green-600"></i>
                </div>
                <div class="text-left">
                    <p class="font-semibold text-gray-900">Processing...</p>
                    <p class="text-xs text-gray-500">Updating task status</p>
                </div>
            </div>
            <i class="fas fa-chevron-right text-gray-400"></i>
        `;
        button.disabled = true;

        try {
            const response = await fetch(`/team/tasks/${ {{ $task->id }} }/complete-task`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();

            if (data.success) {
                showSuccessToast(data.message);

                // Update UI
                updateTaskUI('done');

                // Remove complete button
                button.remove();

                // Reload page after 2 seconds
                setTimeout(() => {
                    window.location.reload();
                }, 2000);

            } else {
                showErrorToast(data.error);
                // Reset button
                button.innerHTML = originalHTML;
                button.disabled = false;
            }
        } catch (error) {
            console.error('Error:', error);
            showErrorToast('Network error occurred');
            // Reset button
            button.innerHTML = originalHTML;
            button.disabled = false;
        }
    }

    // Update Task Status Function
    async function updateTaskStatus(newStatus) {
        const button = statusDropdownBtn;
        const originalHTML = button.innerHTML;

        // Show loading state
        button.innerHTML = `
            <div class="flex items-center">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-spinner fa-spin text-blue-600"></i>
                </div>
                <div class="text-left">
                    <p class="font-semibold text-gray-900">Updating...</p>
                    <p class="text-xs text-gray-500">Changing status</p>
                </div>
            </div>
            <i class="fas fa-chevron-down text-gray-400"></i>
        `;
        button.disabled = true;

        try {
            const response = await fetch(`/team/tasks/${ {{ $task->id }} }/update-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    status: newStatus
                })
            });

            const data = await response.json();

            if (data.success) {
                showSuccessToast(data.message);
                updateTaskUI(newStatus);

                // Reload page after 1 second
                setTimeout(() => {
                    window.location.reload();
                }, 1000);

            } else {
                showErrorToast(data.error);
                // Reset button
                button.innerHTML = originalHTML;
                button.disabled = false;
            }
        } catch (error) {
            console.error('Error:', error);
            showErrorToast('Network error occurred');
            // Reset button
            button.innerHTML = originalHTML;
            button.disabled = false;
        }
    }

    // Update UI after status change
    function updateTaskUI(newStatus) {
        // Update status badge in header
        const statusBadge = document.querySelector('.inline-flex.items-center');
        if (statusBadge) {
            let bgClass, textClass, icon;

            switch(newStatus) {
                case 'done':
                    bgClass = 'bg-green-100 text-green-800 border-green-200';
                    icon = 'fa-check-circle';
                    break;
                case 'in_progress':
                    bgClass = 'bg-blue-100 text-blue-800 border-blue-200';
                    icon = 'fa-spinner fa-spin';
                    break;
                default:
                    bgClass = 'bg-yellow-100 text-yellow-800 border-yellow-200';
                    icon = 'fa-clock';
            }

            statusBadge.className = `inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium ${bgClass}`;
            statusBadge.innerHTML = `<i class="fas ${icon} mr-2 text-xs"></i>${newStatus.replace('_', ' ')}`;
        }
    }

    // Toast functions
    function showSuccessToast(message) {
        const toast = document.getElementById('successToast');
        const toastMessage = document.getElementById('toastMessage');

        toastMessage.textContent = message;
        toast.classList.remove('translate-x-full');

        setTimeout(() => {
            toast.classList.add('translate-x-full');
        }, 3000);
    }

    function showErrorToast(message) {
        alert('Error: ' + message);
    }
});
</script>
@endsection
