@extends("admin.layouts.app")

@section("content")
<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Tasks</h1>
            <p class="text-gray-600 mt-2">Manage all tasks across projects</p>
        </div>
        <a href="{{ url('/tasks/create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-medium transition duration-200 flex items-center shadow-sm">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Create New Task
        </a>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    <!-- Professional Filter Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <!-- Filter Title -->
            <div class="flex items-center">
                <svg class="w-5 h-5 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"/>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900">Filter Tasks</h3>
            </div>

            <!-- Filter Controls -->
            <div class="flex flex-col sm:flex-row gap-4 flex-1 lg:flex-initial lg:justify-end">
                <!-- Project Filter -->
                <div class="flex-1 sm:flex-initial">
                    <label for="project_id" class="block text-sm font-medium text-gray-700 mb-2">Project</label>
                    <select 
                        id="project_id" 
                        name="project_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white"
                    >
                        <option value="">All Projects</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                                {{ $project->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Assigned User Filter -->
                <div class="flex-1 sm:flex-initial">
                    <label for="assigned_user" class="block text-sm font-medium text-gray-700 mb-2">Assigned To</label>
                    <select 
                        id="assigned_user" 
                        name="assigned_user"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white"
                    >
                        <option value="">All Users</option>
                        @foreach($assignedUsers as $user)
                            <option value="{{ $user->id }}" {{ request('assigned_user') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                                @if($user->department)
                                    ({{ $user->department }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Priority Filter -->
                <div class="flex-1 sm:flex-initial">
                    <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                    <select 
                        id="priority" 
                        name="priority"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white"
                    >
                        <option value="">All Priorities</option>
                        <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                        <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-end gap-2">
                    <button 
                        type="button" 
                        id="applyFilters"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200 flex items-center"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"/>
                        </svg>
                        Apply
                    </button>
                    <a 
                        href="{{ route('tasks.index') }}" 
                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg font-medium transition duration-200 flex items-center"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Reset
                    </a>
                </div>
            </div>
        </div>

        <!-- Active Filters Display -->
        @if(request()->has('project_id') || request()->has('assigned_user') || request()->has('priority'))
        <div class="mt-4 pt-4 border-t border-gray-200">
            <div class="flex items-center flex-wrap gap-2">
                <span class="text-sm text-gray-600">Active filters:</span>
                @if(request('project_id'))
                    @php
                        $selectedProject = $projects->firstWhere('id', request('project_id'));
                    @endphp
                    @if($selectedProject)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                        Project: {{ $selectedProject->name }}
                        <a href="{{ request()->fullUrlWithQuery(['project_id' => null]) }}" class="ml-1 hover:text-purple-600">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </a>
                    </span>
                    @endif
                @endif
                @if(request('assigned_user'))
                    @php
                        $selectedUser = $assignedUsers->firstWhere('id', request('assigned_user'));
                    @endphp
                    @if($selectedUser)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        User: {{ $selectedUser->name }}
                        <a href="{{ request()->fullUrlWithQuery(['assigned_user' => null]) }}" class="ml-1 hover:text-blue-600">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </a>
                    </span>
                    @endif
                @endif
                @if(request('priority'))
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    Priority: {{ ucfirst(request('priority')) }}
                    <a href="{{ request()->fullUrlWithQuery(['priority' => null]) }}" class="ml-1 hover:text-green-600">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </a>
                </span>
                @endif
            </div>
        </div>
        @endif
    </div>

    <!-- Professional Mobile Filter Tabs -->
    <div class="lg:hidden mb-6">
        <div class="flex space-x-1 bg-white p-1 rounded-xl shadow-sm border border-gray-200 overflow-x-auto">
            <button data-status="todo" class="task-filter-tab active flex-1 px-4 py-3 text-sm font-medium rounded-lg bg-gray-100 text-gray-800 whitespace-nowrap transition-all duration-200">
                To Do ({{ $tasks->where('status', 'todo')->count() }})
            </button>
            <button data-status="in_progress" class="task-filter-tab flex-1 px-4 py-3 text-sm font-medium rounded-lg text-gray-600 hover:bg-gray-100 whitespace-nowrap transition-all duration-200">
                In Progress ({{ $tasks->where('status', 'in_progress')->count() }})
            </button>
            <button data-status="done" class="task-filter-tab flex-1 px-4 py-3 text-sm font-medium rounded-lg text-gray-600 hover:bg-gray-100 whitespace-nowrap transition-all duration-200">
                Done ({{ $tasks->where('status', 'done')->count() }})
            </button>
        </div>
    </div>

    <!-- Kanban Board -->
    @if($tasks->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- To Do Column -->
            <div class="task-column active bg-gray-50 rounded-2xl p-6" data-status="todo">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <div class="w-3 h-3 bg-gray-400 rounded-full mr-3"></div>
                        To Do
                    </h3>
                    <span class="bg-gray-200 text-gray-700 px-3 py-1 rounded-full text-sm font-medium">
                        {{ $tasks->where('status', 'todo')->count() }}
                    </span>
                </div>
                <div class="space-y-4">
                    @foreach($tasks->where('status', 'todo') as $task)
                    @include('admin.Tasks.partials.task-card', ['task' => $task, 'status' => 'todo'])
                    @endforeach
                </div>
            </div>

            <!-- In Progress Column -->
            <div class="task-column bg-gray-50 rounded-2xl p-6" data-status="in_progress">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                        In Progress
                    </h3>
                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                        {{ $tasks->where('status', 'in_progress')->count() }}
                    </span>
                </div>
                <div class="space-y-4">
                    @foreach($tasks->where('status', 'in_progress') as $task)
                    @include('admin.Tasks.partials.task-card', ['task' => $task, 'status' => 'in_progress'])
                    @endforeach
                </div>
            </div>

            <!-- Done Column -->
            <div class="task-column bg-gray-50 rounded-2xl p-6" data-status="done">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                        Done
                    </h3>
                    <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                        {{ $tasks->where('status', 'done')->count() }}
                    </span>
                </div>
                <div class="space-y-4">
                    @foreach($tasks->where('status', 'done') as $task)
                    @include('admin.Tasks.partials.task-card', ['task' => $task, 'status' => 'done'])
                    @endforeach
                </div>
            </div>
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 text-center py-16">
            <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-sm">
                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-3">No tasks found</h3>
            <p class="text-gray-600 mb-8 max-w-md mx-auto">
                @if(request()->has('project_id') || request()->has('assigned_user') || request()->has('priority'))
                    No tasks match your current filters. Try adjusting your criteria.
                @else
                    Get started by creating your first task
                @endif
            </p>
            @if(request()->has('project_id') || request()->has('assigned_user') || request()->has('priority'))
                <a href="{{ route('tasks.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-8 py-3 rounded-xl font-medium transition duration-200 inline-flex items-center shadow-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Clear Filters
                </a>
            @else
                <a href="{{ route('tasks.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-xl font-medium transition duration-200 inline-flex items-center shadow-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Create Your First Task
                </a>
            @endif
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterTabs = document.querySelectorAll('.task-filter-tab');
    const taskColumns = document.querySelectorAll('.task-column');
    const applyFiltersBtn = document.getElementById('applyFilters');
    const projectSelect = document.getElementById('project_id');
    const assignedUserSelect = document.getElementById('assigned_user');
    const prioritySelect = document.getElementById('priority');

    // Filter application
    applyFiltersBtn.addEventListener('click', function() {
        const projectId = projectSelect.value;
        const assignedUser = assignedUserSelect.value;
        const priority = prioritySelect.value;

        let url = new URL(window.location.href);
        let params = new URLSearchParams();

        if (projectId) {
            params.set('project_id', projectId);
        }

        if (assignedUser) {
            params.set('assigned_user', assignedUser);
        }

        if (priority) {
            params.set('priority', priority);
        }

        const queryString = params.toString();
        window.location.href = queryString ? `${url.pathname}?${queryString}` : url.pathname;
    });

    // Enter key support for filters
    [projectSelect, assignedUserSelect, prioritySelect].forEach(select => {
        select.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                applyFiltersBtn.click();
            }
        });
    });

    // Mobile view initialization
    function initMobileView() {
        if (window.innerWidth < 1024) {
            taskColumns.forEach((col, index) => {
                if (index === 0) {
                    col.style.display = 'block';
                } else {
                    col.style.display = 'none';
                }
            });
        } else {
            taskColumns.forEach(col => {
                col.style.display = 'block';
            });
        }
    }

    // Filter tab click handler
    filterTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const status = this.getAttribute('data-status');

            // Update active tab
            filterTabs.forEach(t => {
                t.classList.remove('active', 'bg-gray-100', 'text-gray-800');
                t.classList.add('text-gray-600', 'hover:bg-gray-100');
            });
            this.classList.remove('text-gray-600', 'hover:bg-gray-100');
            this.classList.add('active', 'bg-gray-100', 'text-gray-800');

            // Show selected column, hide others on mobile
            if (window.innerWidth < 1024) {
                taskColumns.forEach(col => {
                    if (col.getAttribute('data-status') === status) {
                        col.style.display = 'block';
                    } else {
                        col.style.display = 'none';
                    }
                });
            }
        });
    });

    // Handle window resize
    window.addEventListener('resize', initMobileView);

    // Initial setup
    initMobileView();
});
</script>

<style>
@media (max-width: 1023px) {
    .task-column {
        display: none;
    }
    .task-column:first-child {
        display: block;
    }
}

.task-filter-tab.active {
    background-color: rgb(243, 244, 246);
    color: rgb(31, 41, 55);
}

.task-filter-tab {
    transition: all 0.2s ease-in-out;
}

.hover\:shadow-md {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}
.shadow-sm {
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}
.rounded-2xl {
    border-radius: 1rem;
}
.rounded-xl {
    border-radius: 0.75rem;
}
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endsection