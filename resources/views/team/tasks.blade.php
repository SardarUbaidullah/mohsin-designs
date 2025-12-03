@php
    $layout = match (Auth::user()->role) {
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
                            <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-tasks text-white text-lg"></i>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">My Tasks</h1>
                                <p class="text-gray-600">All tasks assigned to you across projects</p>
                            </div>
                        </div>
                    </div>

                    <!-- Status Filter -->
                    <div class="flex space-x-2">
                        <a href="{{ route('team.tasks.index') }}?status=all"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors
                              {{ request('status') == 'all' || !request('status')
                                  ? 'bg-blue-600 text-white'
                                  : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }}">
                            All Tasks ({{ $totalCount ?? 0 }})
                        </a>
                        <a href="{{ route('team.tasks.index') }}?status=todo"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors
                              {{ request('status') == 'todo'
                                  ? 'bg-yellow-600 text-white'
                                  : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }}">
                            To Do ({{ $todoCount ?? 0 }})
                        </a>

                        <a href="{{ route('team.tasks.index') }}?status=done"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors
                              {{ request('status') == 'done'
                                  ? 'bg-green-600 text-white'
                                  : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }}">
                            Done ({{ $doneCount ?? 0 }})
                        </a>
                    </div>
                </div>
            </div>

            <!-- Current Running Timer Alert -->
            <div id="runningTimerAlert" class="hidden mb-6">
                <!-- Will be populated by JavaScript -->
            </div>

            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Total Tasks</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $totalCount ?? 0 }}</p>
                        </div>
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-tasks text-blue-600"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">To Do</p>
                            <p class="text-2xl font-bold text-yellow-600">{{ $todoCount ?? 0 }}</p>
                        </div>
                        <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-clock text-yellow-600"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Done</p>
                            <p class="text-2xl font-bold text-green-600">{{ $doneCount ?? 0 }}</p>
                        </div>
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-check-circle text-green-600"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tasks List -->
            <div class="bg-white rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">Task List</h2>
                        <div class="text-sm text-gray-500">
                            {{ $tasks->total() }} tasks
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    @if ($tasks->count() > 0)
                        <div class="space-y-4">
                            @foreach ($tasks as $task)
                                @php
                                    $dueDate = $task->due_date ? \Carbon\Carbon::parse($task->due_date) : null;
                                    $isOverdue = $dueDate && $dueDate->isPast() && $task->status !== 'done';
                                    $isDueSoon = $dueDate && $dueDate->diffInDays(now()) <= 2 && !$isOverdue;

                                    // Calculate total time spent on this task - FIXED VERSION
                                    $totalTimeSpent = 0;
                                    $formattedTime = '0m';

                                    // Check if timeLogs relationship exists and is loaded
                                    if (method_exists($task, 'timeLogs') && $task->relationLoaded('timeLogs')) {
                                        $totalTimeSpent = $task->timeLogs
                                            ->where('is_running', false)
                                            ->sum('duration_minutes');
                                        $hours = floor($totalTimeSpent / 60);
                                        $minutes = $totalTimeSpent % 60;
                                        $formattedTime = $hours > 0 ? "{$hours}h {$minutes}m" : "{$minutes}m";
                                    }
                                @endphp
                                <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow
                                {{ $isOverdue ? 'border-l-4 border-l-red-500 bg-red-50' : '' }}
                                {{ $isDueSoon ? 'border-l-4 border-l-orange-500 bg-orange-50' : '' }}
                                {{ $task->status === 'done' ? 'bg-green-50 border-green-200' : '' }}"
                                    id="task-{{ $task->id }}">

                                    <div class="flex items-start justify-between">
                                        <!-- Left Section -->
                                        <div class="flex items-start space-x-4 flex-1">
                                            <!-- Task Status -->
                                            <div class="flex items-center space-x-3">
                                                @if ($task->status !== 'done')
                                                    <button
                                                        class="complete-task-btn w-8 h-8 border-2 border-gray-300 rounded flex items-center justify-center hover:border-green-500 hover:bg-green-50 transition-colors"
                                                        data-task-id="{{ $task->id }}" title="Mark as Done">
                                                        <i class="fas fa-check text-gray-400 hover:text-green-600"></i>
                                                    </button>
                                                @else
                                                    <div
                                                        class="w-8 h-8 bg-green-500 rounded flex items-center justify-center">
                                                        <i class="fas fa-check text-white text-sm"></i>
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- Task Details -->
                                            <div class="flex-1">
                                                <div class="flex items-start justify-between mb-2">
                                                    <div class="flex-1">
                                                        <h3 class="font-semibold text-gray-900 text-lg mb-1">
                                                            {{ $task->title }}</h3>
                                                        <p class="text-gray-600 text-sm mb-3">
                                                            {{ $task->description ?? 'No description provided' }}
                                                        </p>
                                                    </div>
                                                </div>

                                                <!-- Task Meta -->
                                                <div class="flex flex-wrap items-center gap-2 text-sm">
                                                    <!-- Project -->
                                                    <span
                                                        class="inline-flex items-center px-3 py-1 rounded-full bg-blue-100 text-blue-800">
                                                        <i class="fas fa-project-diagram mr-1 text-xs"></i>
                                                        {{ $task->project->name ?? 'No Project' }}
                                                    </span>

                                                    <!-- Priority -->
                                                    <span
                                                        class="inline-flex items-center px-3 py-1 rounded-full
                                            @if ($task->priority == 'high') bg-red-100 text-red-800
                                            @elseif($task->priority == 'medium') bg-yellow-100 text-yellow-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                                        <i class="fas fa-flag mr-1 text-xs"></i>
                                                        {{ ucfirst($task->priority) }}
                                                    </span>

                                                    <!-- Due Date -->
                                                    @if ($dueDate)
                                                        <span
                                                            class="inline-flex items-center px-3 py-1 rounded-full
                                            @if ($isOverdue) bg-red-100 text-red-800
                                            @elseif($isDueSoon) bg-orange-100 text-orange-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                                            <i class="fas fa-calendar-day mr-1 text-xs"></i>
                                                            {{ $dueDate->format('M d, Y') }}
                                                        </span>
                                                    @endif

                                                    <!-- Time Spent -->
                                                    @if ($totalTimeSpent > 0)
                                                        <span
                                                            class="inline-flex items-center px-3 py-1 rounded-full bg-purple-100 text-purple-800">
                                                            <i class="fas fa-clock mr-1 text-xs"></i>
                                                            {{ $formattedTime }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Right Section -->
                                        <div class="flex flex-col items-end space-y-2 ml-4">
                                            <!-- Status Badge -->
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                    @if ($task->status == 'done') bg-green-100 text-green-800
                                    @elseif($task->status == 'in_progress') bg-blue-100 text-blue-800
                                    @else bg-yellow-100 text-yellow-800 @endif">
                                                <i
                                                    class="fas
                                        @if ($task->status == 'done') fa-check-circle
                                        @elseif($task->status == 'in_progress') fa-spinner fa-spin
                                        @else fa-clock @endif mr-1 text-xs">
                                                </i>
                                                {{ str_replace('_', ' ', ucfirst($task->status)) }}
                                            </span>

                                            <!-- Action Buttons -->
                                            <div class="flex items-center space-x-2">
                                                <!-- Start/Stop Timer Button -->
                                                @if ($task->status !== 'done')
                                                    <button
                                                        class="timer-btn hidden w-8 h-8 rounded flex items-center justify-center transition-colors
                                                  {{ $task->active_timer ? 'bg-red-500 hover:bg-red-600 text-white' : 'bg-green-500 hover:bg-green-600 text-white' }}"
                                                        data-task-id="{{ $task->id }}"
                                                        data-task-title="{{ $task->title }}"
                                                        data-is-running="{{ $task->active_timer ? 'true' : 'false' }}"
                                                        data-time-log-id="{{ $task->active_timer?->id }}"
                                                        title="{{ $task->active_timer ? 'Stop Timer' : 'Start Timer' }}">
                                                        <i
                                                            class="fas {{ $task->active_timer ? 'fa-stop' : 'fa-play' }} text-xs"></i>
                                                    </button>
                                                @endif

                                                <a href="{{ route('team.tasks.show', $task->id) }}"
                                                    class="w-8 h-8 bg-gray-100 hover:bg-gray-200 rounded flex items-center justify-center text-gray-600 hover:text-gray-800 transition-colors"
                                                    title="View Details">
                                                    <i class="fas fa-eye text-xs"></i>
                                                </a>
                                            </div>

                                            <!-- Timer Display -->
                                            @if ($task->active_timer)
                                                <div class="text-xs text-orange-600 font-medium timer-display"
                                                    data-start-time="{{ $task->active_timer->start_time }}">
                                                    <i class="fas fa-clock mr-1"></i>
                                                    <span class="timer-value">00:00:00</span>
                                                </div>
                                            @endif

                                            <!-- Time Info -->
                                            @if ($task->created_at)
                                                <span class="text-xs text-gray-500">
                                                    Created {{ $task->created_at->diffForHumans() }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        @if ($tasks->hasPages())
                            <div class="mt-6 pt-6 border-t border-gray-200">
                                {{ $tasks->links() }}
                            </div>
                        @endif
                    @else
                        <!-- Empty State -->
                        <div class="text-center py-12">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-tasks text-gray-400"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">No tasks found</h3>
                            <p class="text-gray-500">
                                @if (request('status') == 'done')
                                    You haven't completed any tasks yet.
                                @else
                                    No tasks are assigned to you currently.
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Success Toast -->
    <div id="successToast" class="fixed top-4 right-4 bg-green-500 text-white px-4 py-3 rounded-lg shadow-lg z-50 hidden">
        <div class="flex items-center space-x-2">
            <i class="fas fa-check-circle"></i>
            <span id="toastMessage">Task completed successfully!</span>
        </div>
    </div>

    <!-- Timer Toast -->
    <div id="timerToast" class="fixed top-4 right-4 bg-blue-500 text-white px-4 py-3 rounded-lg shadow-lg z-50 hidden">
        <div class="flex items-center space-x-2">
            <i class="fas fa-clock"></i>
            <span id="timerToastMessage">Timer started successfully!</span>
        </div>
    </div>

    <script>
        // Task completion functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Load running timer on page load
            checkRunningTimer();

            // Complete button click handler
            document.querySelectorAll('.complete-task-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const taskId = this.getAttribute('data-task-id');
                    completeTask(taskId, this);
                });
            });

            // Timer button click handler
            document.querySelectorAll('.timer-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const taskId = this.getAttribute('data-task-id');
                    const taskTitle = this.getAttribute('data-task-title');
                    const isRunning = this.getAttribute('data-is-running') === 'true';
                    const timeLogId = this.getAttribute('data-time-log-id');

                    if (isRunning) {
                        stopTimer(timeLogId, this);
                    } else {
                        startTimer(taskId, taskTitle, this);
                    }
                });
            });

            // Update running timers every second
            setInterval(updateRunningTimers, 1000);

            // Complete task function
            async function completeTask(taskId, button) {
                // Show loading state
                const originalHTML = button.innerHTML;
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                button.disabled = true;

                try {
                    const response = await fetch(`/team/tasks/${taskId}/complete`, {
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
                        const taskElement = button.closest('.border');
                        const statusBadge = taskElement.querySelector('.inline-flex.items-center');

                        // Update status badge
                        statusBadge.className =
                            'inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800';
                        statusBadge.innerHTML = '<i class="fas fa-check-circle mr-1 text-xs"></i>Done';

                        // Update button to checked state
                        button.outerHTML = `
                    <div class="w-8 h-8 bg-green-500 rounded flex items-center justify-center">
                        <i class="fas fa-check text-white text-sm"></i>
                    </div>
                `;

                        // Remove timer button if exists
                        const timerBtn = taskElement.querySelector('.timer-btn');
                        if (timerBtn) {
                            timerBtn.remove();
                        }

                        // Update task background
                        taskElement.classList.add('bg-green-50', 'border-green-200');
                        taskElement.classList.remove('bg-red-50', 'bg-orange-50');

                        // Reload page after 2 seconds to update stats
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

            // Timer functions
            async function startTimer(taskId, taskTitle, button) {
                try {
                    const response = await fetch('/admin/time-tracking/start-timer', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            task_id: taskId,
                            description: `Working on: ${taskTitle}`
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        showTimerToast('Timer started for: ' + taskTitle);

                        // Update button to stop state
                        button.innerHTML = '<i class="fas fa-stop text-xs"></i>';
                        button.className = button.className.replace('bg-green-500', 'bg-red-500').replace(
                            'hover:bg-green-600', 'hover:bg-red-600');
                        button.setAttribute('data-is-running', 'true');
                        button.setAttribute('data-time-log-id', data.time_log.id);
                        button.title = 'Stop Timer';

                        // Add timer display
                        const timerDisplay = document.createElement('div');
                        timerDisplay.className = 'text-xs text-orange-600 font-medium timer-display';
                        timerDisplay.setAttribute('data-start-time', data.time_log.start_time);
                        timerDisplay.innerHTML =
                            '<i class="fas fa-clock mr-1"></i><span class="timer-value">00:00:00</span>';

                        button.parentNode.parentNode.appendChild(timerDisplay);

                        // Update running timer alert
                        checkRunningTimer();

                    } else {
                        showErrorToast(data.message);
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showErrorToast('Network error occurred');
                }
            }

            async function stopTimer(timeLogId, button) {
                try {
                    const response = await fetch('/admin/time-tracking/stop-timer', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            time_log_id: timeLogId
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        showTimerToast('Timer stopped. Time logged: ' + data.duration);

                        // Update button to start state
                        button.innerHTML = '<i class="fas fa-play text-xs"></i>';
                        button.className = button.className.replace('bg-red-500', 'bg-green-500').replace(
                            'hover:bg-red-600', 'hover:bg-green-600');
                        button.setAttribute('data-is-running', 'false');
                        button.removeAttribute('data-time-log-id');
                        button.title = 'Start Timer';

                        // Remove timer display
                        const timerDisplay = button.parentNode.parentNode.querySelector('.timer-display');
                        if (timerDisplay) {
                            timerDisplay.remove();
                        }

                        // Reload to update time spent display
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);

                    } else {
                        showErrorToast(data.message);
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showErrorToast('Network error occurred');
                }
            }

            // Update running timers display
            function updateRunningTimers() {
                document.querySelectorAll('.timer-display').forEach(display => {
                    const startTime = new Date(display.getAttribute('data-start-time'));
                    const now = new Date();
                    const diff = now - startTime;

                    const hours = Math.floor(diff / (1000 * 60 * 60));
                    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((diff % (1000 * 60)) / 1000);

                    const timerValue = display.querySelector('.timer-value');
                    timerValue.textContent =
                        `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                });
            }

            // Check for running timer
            async function checkRunningTimer() {
                try {
                    const response = await fetch('/admin/time-tracking/running-timer');
                    const data = await response.json();

                    const alertDiv = document.getElementById('runningTimerAlert');

                    if (data.has_running_timer && data.timer) {
                        alertDiv.innerHTML = `
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                                    <i class="fas fa-play text-white text-sm"></i>
                                </div>
                                <div>
                                    <div class="font-semibold text-yellow-800">Timer Running</div>
                                    <div class="text-sm text-yellow-600">${data.timer.task.title}</div>
                                </div>
                            </div>
                            <button onclick="stopRunningTimer(${data.timer.id})"
                                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm">
                                <i class="fas fa-stop mr-2"></i>Stop Timer
                            </button>
                        </div>
                    </div>
                `;
                        alertDiv.classList.remove('hidden');
                    } else {
                        alertDiv.classList.add('hidden');
                    }
                } catch (error) {
                    console.error('Error checking running timer:', error);
                }
            }

            // Stop running timer from alert
            window.stopRunningTimer = async function(timeLogId) {
                try {
                    const response = await fetch('/admin/time-tracking/stop-timer', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            time_log_id: timeLogId
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        showTimerToast('Timer stopped. Time logged: ' + data.duration);
                        window.location.reload();
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showErrorToast('Error stopping timer');
                }
            };

            // Toast functions
            function showSuccessToast(message) {
                const toast = document.getElementById('successToast');
                const toastMessage = document.getElementById('toastMessage');

                toastMessage.textContent = message;
                toast.classList.remove('hidden');

                setTimeout(() => {
                    toast.classList.add('hidden');
                }, 3000);
            }

            function showTimerToast(message) {
                const toast = document.getElementById('timerToast');
                const toastMessage = document.getElementById('timerToastMessage');

                toastMessage.textContent = message;
                toast.classList.remove('hidden');

                setTimeout(() => {
                    toast.classList.add('hidden');
                }, 3000);
            }

            function showErrorToast(message) {
                alert('Error: ' + message);
            }
        });
    </script>
@endsection
