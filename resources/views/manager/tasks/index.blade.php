
@php
    $layout = match(Auth::user()->role) {
        'super_admin' => 'admin.layouts.app',
        'admin' => 'manager.layouts.app',
        'user' => 'team.app',
    };
@endphp
@extends($layout)
@section("content")
<div class="h-screen flex flex-col bg-gradient-to-br from-green-50 to-emerald-50/30">
    <!-- Header Section -->
    <div class="flex-shrink-0 bg-white border-b border-gray-200 px-6 py-4">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="flex-1">
                <div class="flex items-center space-x-3 mb-3">
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Task Board</h1>
                </div>
                <p class="text-gray-600">Drag and drop tasks to update their status</p>
            </div>
            <div class="flex items-center space-x-3">
                <!-- Project Filter -->
                <select id="project_filter" onchange="filterByProject(this.value)"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm bg-white">
                    <option value="">All Projects</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                            {{ $project->name }}
                        </option>
                    @endforeach
                </select>

                <!-- Create Task Button -->
                <a href="{{ route('manager.tasks.create') }}"
                   class="group relative bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-200 flex items-center shadow-lg hover:shadow-xl hover:scale-105 active:scale-95">
                    <div class="w-5 h-5 bg-white/20 rounded-full flex items-center justify-center mr-2">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                    </div>
                    Create Task
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="flex-shrink-0 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 text-green-800 px-6 py-4 flex items-center animate-fade-in">
            <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center mr-3">
                <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            </div>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="flex-shrink-0 grid grid-cols-2 lg:grid-cols-4 gap-4 px-6 py-4 bg-white border-b border-gray-200">
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-200/60">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-600">Total Tasks</p>
                    <p class="text-xl font-bold text-gray-900">{{ $taskCounts['all'] }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-200/60">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-600">To Do</p>
                    <p class="text-xl font-bold text-gray-900">{{ $taskCounts['todo'] }}</p>
                </div>
                <div class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-200/60">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-600">In Progress</p>
                    <p class="text-xl font-bold text-yellow-600">{{ $taskCounts['in_progress'] }}</p>
                </div>
                <div class="w-10 h-10 bg-yellow-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-200/60">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-600">Completed</p>
                    <p class="text-xl font-bold text-green-600">{{ $taskCounts['done'] }}</p>
                </div>
                <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Kanban Board -->
    <div class="flex-1 min-h-0 px-6 py-4">
        @if($tasks->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 h-full">
                <!-- To Do Column -->
                <div class="bg-white rounded-2xl p-4 flex flex-col h-full shadow-sm border border-gray-200/60">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-gray-400 rounded-full mr-3"></div>
                            <h3 class="font-semibold text-gray-700 text-lg">To Do</h3>
                            <span class="ml-2 bg-gray-100 px-3 py-1 rounded-full text-sm font-medium text-gray-600">
                                {{ $taskCounts['todo'] }}
                            </span>
                        </div>
                    </div>
                    <div class="flex-1 overflow-y-auto space-y-4">
                        @foreach($tasks->where('status', 'todo') as $task)
                            @include('manager.tasks.partials.task-card', ['task' => $task])
                        @endforeach

                        @if($tasks->where('status', 'todo')->count() === 0)
                            <div class="text-center py-8 text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <p class="text-sm">No tasks to do</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- In Progress Column -->
                <div class="bg-white rounded-2xl p-4 flex flex-col h-full shadow-sm border border-gray-200/60">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-yellow-400 rounded-full mr-3"></div>
                            <h3 class="font-semibold text-gray-700 text-lg">In Progress</h3>
                            <span class="ml-2 bg-yellow-100 px-3 py-1 rounded-full text-sm font-medium text-yellow-700">
                                {{ $taskCounts['in_progress'] }}
                            </span>
                        </div>
                    </div>
                    <div class="flex-1 overflow-y-auto space-y-4">
                        @foreach($tasks->where('status', 'in_progress') as $task)
                            @include('manager.tasks.partials.task-card', ['task' => $task])
                        @endforeach

                        @if($tasks->where('status', 'in_progress')->count() === 0)
                            <div class="text-center py-8 text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                <p class="text-sm">No tasks in progress</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Done Column -->
                <div class="bg-white rounded-2xl p-4 flex flex-col h-full shadow-sm border border-gray-200/60">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-400 rounded-full mr-3"></div>
                            <h3 class="font-semibold text-gray-700 text-lg">Completed</h3>
                            <span class="ml-2 bg-green-100 px-3 py-1 rounded-full text-sm font-medium text-green-700">
                                {{ $taskCounts['done'] }}
                            </span>
                        </div>
                    </div>
                    <div class="flex-1 overflow-y-auto space-y-4">
                        @foreach($tasks->where('status', 'done') as $task)
                            @include('manager.tasks.partials.task-card', ['task' => $task])
                        @endforeach

                        @if($tasks->where('status', 'done')->count() === 0)
                            <div class="text-center py-8 text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-sm">No completed tasks</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @else
            <!-- Empty State -->
            <div class="h-full flex items-center justify-center">
                <div class="text-center bg-white rounded-2xl shadow-sm border border-gray-200/60 p-12 max-w-md mx-auto">
                    <div class="w-20 h-20 bg-green-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">No tasks found</h3>
                    <p class="text-gray-500 mb-6">Get started by creating your first task</p>
                    <a href="{{ route('manager.tasks.create') }}"
                       class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl hover:from-green-600 hover:to-emerald-700 transition-all duration-200 font-semibold shadow-lg hover:shadow-xl">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Create Your First Task
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
function filterByProject(projectId) {
    if (projectId) {
        window.location.href = '{{ route("manager.tasks.index") }}?project_id=' + projectId;
    } else {
        window.location.href = '{{ route("manager.tasks.index") }}';
    }
}

// Drag and drop functionality
document.addEventListener('DOMContentLoaded', function() {
    const taskCards = document.querySelectorAll('.task-card');

    taskCards.forEach(card => {
        card.setAttribute('draggable', 'true');

        card.addEventListener('dragstart', function(e) {
            e.dataTransfer.setData('text/plain', card.dataset.taskId);
            card.classList.add('opacity-50', 'scale-95');
        });

        card.addEventListener('dragend', function() {
            card.classList.remove('opacity-50', 'scale-95');
        });
    });

    const columns = document.querySelectorAll('.bg-white.rounded-2xl');

    columns.forEach(column => {
        column.addEventListener('dragover', function(e) {
            e.preventDefault();
            column.classList.add('ring-2', 'ring-green-300', 'bg-green-50/50');
        });

        column.addEventListener('dragleave', function() {
            column.classList.remove('ring-2', 'ring-green-300', 'bg-green-50/50');
        });

        column.addEventListener('drop', function(e) {
            e.preventDefault();
            column.classList.remove('ring-2', 'ring-green-300', 'bg-green-50/50');

            const taskId = e.dataTransfer.getData('text/plain');
            const columnTitle = column.querySelector('h3').textContent.trim();
            let newStatus = 'todo';

            if (columnTitle === 'In Progress') newStatus = 'in_progress';
            else if (columnTitle === 'Completed') newStatus = 'done';

            updateTaskStatus(taskId, newStatus);
        });
    });
});

function updateTaskStatus(taskId, newStatus) {
    fetch(`/manager/tasks/${taskId}/status`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            status: newStatus
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showNotification('Task status updated successfully!', 'success');
            setTimeout(() => {
                location.reload();
            }, 1000);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error updating task status', 'error');
        location.reload();
    });
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-6 py-3 rounded-xl shadow-lg text-white font-medium z-50 animate-fade-in ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    }`;
    notification.textContent = message;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>

<style>
.animate-fade-in {
    animation: fadeIn 0.5s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Custom scrollbar for columns */
.flex-1.overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}

.flex-1.overflow-y-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 10px;
}

.flex-1.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 10px;
}

.flex-1.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Smooth transitions */
.task-card {
    transition: all 0.2s ease-in-out;
}

.task-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}
</style>
@endsection
