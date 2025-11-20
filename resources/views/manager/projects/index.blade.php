
@php
    $layout = match(Auth::user()->role) {
        'super_admin' => 'admin.layouts.app',
        'admin' => 'manager.layouts.app',
        'user' => 'team.app',
    };
@endphp
@extends($layout)

@section("content")
<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- Header Section -->
    <div class="mb-8">
        <h1 class="text-2xl font-semibold text-gray-900">My Projects</h1>
        <p class="text-gray-600 mt-1">Manage projects assigned to you as manager</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-lg p-4 border border-gray-200">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Projects</p>
                    <p class="text-2xl font-medium text-gray-900">{{ $projects->count() }}</p>
                </div>
                <div class="w-10 h-10 bg-gray-100 rounded-md flex items-center justify-center">
                    <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg p-4 border border-gray-200">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Active Projects</p>
                    <p class="text-2xl font-medium text-gray-900">{{ $projects->where('status', 'in_progress')->count() }}</p>
                </div>
                <div class="w-10 h-10 bg-gray-100 rounded-md flex items-center justify-center">
                    <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg p-4 border border-gray-200">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Tasks</p>
                    <p class="text-2xl font-medium text-gray-900">{{ $totalTasks ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 bg-gray-100 rounded-md flex items-center justify-center">
                    <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg p-4 border border-gray-200">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Team Members</p>
                    <p class="text-2xl font-medium text-gray-900">{{ $teamMembersCount ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 bg-gray-100 rounded-md flex items-center justify-center">
                    <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>
    {{-- //filter --}}
    @auth
       @if (auth::user()->role=='manager')
<div class="bg-white rounded-xl p-4 border border-gray-200 mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center gap-3">

        <!-- Assigned To Me -->
        <a href="{{ route('manager.projects.index') }}"
           class="flex-1 sm:flex-none text-center px-4 py-2 rounded-lg text-sm font-medium transition
           {{ !request('filter') 
                ? 'bg-blue-600 text-white shadow-md' 
                : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
             Assigned To Me
        </a>

        <!-- Created By Me -->
        <a href="{{ route('manager.projects.index', ['filter' => 'created_by_me']) }}"
           class="flex-1 sm:flex-none text-center px-4 py-2 rounded-lg text-sm font-medium transition
           {{ request('filter') == 'created_by_me' 
                ? 'bg-green-600 text-white shadow-md' 
                : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            Created By Me
        </a>
    </div>

    <!-- Active Filter Label -->
    <div class="mt-4 text-sm text-gray-600 border-t pt-3">
        @if(request('filter') == 'created_by_me')
            Showing: <span class="font-medium">Projects Created By You</span>
            <a href="{{ route('manager.projects.index') }}" 
               class="ml-2 text-blue-600 hover:text-blue-800 underline">Reset</a>
        @else
            Showing: <span class="font-medium">Projects Assigned To You</span>
        @endif
    </div>
</div>
@endif 
    @endauth




    <!-- Notifications -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md mb-6 flex items-center">
            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md mb-6 flex items-center">
            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif
    {{-- Add this where you want the create button to appear --}}
@if(auth()->user()->can_create_project)
 <div class="mb-4 flex justify-start">
    <a href="{{ route('manager.projects.create') }}"
       class="inline-flex items-center gap-2 bg-purple-600 hover:bg-purple-700 text-white 
              px-4 py-2 rounded-lg shadow-sm hover:shadow-md transition">
        <i class="fas fa-plus"></i>
        Create New Project
    </a>
</div>


@endif

    <!-- Kanban Board -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Pending Column -->
        <div class="bg-gray-50 rounded-lg p-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Pending</h3>
                <span class="bg-gray-200 text-gray-700 text-sm font-medium px-2 py-1 rounded-full">
                    {{ $projects->where('status', 'pending')->count() }}
                </span>
            </div>
            <div class="space-y-4">
                @foreach($projects->where('status', 'pending') as $project)
                    @php
                        $projectTasks = $project->tasks;
                        $completedTasks = $projectTasks->where('status', 'done')->count();
                        $totalProjectTasks = $projectTasks->count();
                        $progress = $totalProjectTasks > 0 ? round(($completedTasks / $totalProjectTasks) * 100) : 0;
                        $teamMembers = \App\Models\User::whereHas('assignedTasks', function($q) use ($project) {
                            $q->where('project_id', $project->id);
                        })->distinct()->count();
                    @endphp
                    <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-xs hover:shadow-sm transition-shadow">
                        <div class="flex justify-between items-start mb-3">
                            <h4 class="font-medium text-gray-900">{{ $project->name }}</h4>
                            <div class="flex space-x-1">
                                <form method="POST" action="{{ route('manager.projects.updateStatus', $project->id) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="in_progress">
                                    <button type="submit" class="text-gray-400 hover:text-green-600 transition-colors p-1 rounded hover:bg-gray-100" title="Mark as In Progress">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                        </svg>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('manager.projects.updateStatus', $project->id) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="completed">
                                    <button type="submit" class="text-gray-400 hover:text-blue-600 transition-colors p-1 rounded hover:bg-gray-100" title="Mark as Completed" onclick="return confirm('Are you sure you want to mark this project as completed?')">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $project->description ?: 'No description' }}</p>

                        <!-- Progress -->
                        <div class="mb-3">
                            <div class="flex justify-between text-xs text-gray-500 mb-1">
                                <span>Progress</span>
                                <span>{{ $progress }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                <div class="bg-gray-400 h-1.5 rounded-full" style="width: {{ $progress }}%"></div>
                            </div>
                        </div>

                        <!-- Meta Info -->
                        <div class="flex justify-between items-center text-xs text-gray-500">
                            <div class="flex items-center space-x-3">
                                <div class="flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    <span>{{ $completedTasks }}/{{ $totalProjectTasks }}</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                    </svg>
                                    <span>{{ $teamMembers }}</span>
                                </div>
                            </div>

                            @if($project->due_date)
                                <div class="flex items-center {{ \Carbon\Carbon::parse($project->due_date)->isPast() ? 'text-red-500' : 'text-gray-500' }}">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span>{{ \Carbon\Carbon::parse($project->due_date)->format('M d') }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex space-x-2 mt-3 pt-3 border-t border-gray-100">
                            <a href="{{ route('manager.projects.show', $project->id) }}" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium py-2 px-3 rounded-md text-center transition-colors">
                                View
                            </a>
                            <a href="{{ route('manager.tasks.index', ['project_id' => $project->id]) }}" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium py-2 px-3 rounded-md text-center transition-colors">
                                Tasks
                            </a>
                        </div>
                    </div>
                @endforeach

                @if($projects->where('status', 'pending')->count() === 0)
                    <div class="text-center py-8 text-gray-400">
                        <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-sm">No pending projects</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- In Progress Column -->
        <div class="bg-gray-50 rounded-lg p-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">In Progress</h3>
                <span class="bg-blue-100 text-blue-700 text-sm font-medium px-2 py-1 rounded-full">
                    {{ $projects->where('status', 'in_progress')->count() }}
                </span>
            </div>
            <div class="space-y-4">
                @foreach($projects->where('status', 'in_progress') as $project)
                    @php
                        $projectTasks = $project->tasks;
                        $completedTasks = $projectTasks->where('status', 'done')->count();
                        $totalProjectTasks = $projectTasks->count();
                        $progress = $totalProjectTasks > 0 ? round(($completedTasks / $totalProjectTasks) * 100) : 0;
                        $teamMembers = \App\Models\User::whereHas('assignedTasks', function($q) use ($project) {
                            $q->where('project_id', $project->id);
                        })->distinct()->count();
                    @endphp
                    <div class="bg-white rounded-lg border border-blue-100 p-4 shadow-xs hover:shadow-sm transition-shadow">
                        <div class="flex justify-between items-start mb-3">
                            <h4 class="font-medium text-gray-900">{{ $project->name }}</h4>
                            <div class="flex space-x-1">
                                <form method="POST" action="{{ route('manager.projects.updateStatus', $project->id) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="pending">
                                    <button type="submit" class="text-gray-400 hover:text-gray-600 transition-colors p-1 rounded hover:bg-gray-100" title="Mark as Pending">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('manager.projects.updateStatus', $project->id) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="completed">
                                    <button type="submit" class="text-gray-400 hover:text-blue-600 transition-colors p-1 rounded hover:bg-gray-100" title="Mark as Completed" onclick="return confirm('Are you sure you want to mark this project as completed?')">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $project->description ?: 'No description' }}</p>

                        <!-- Progress -->
                        <div class="mb-3">
                            <div class="flex justify-between text-xs text-gray-500 mb-1">
                                <span>Progress</span>
                                <span>{{ $progress }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                <div class="bg-blue-500 h-1.5 rounded-full" style="width: {{ $progress }}%"></div>
                            </div>
                        </div>

                        <!-- Meta Info -->
                        <div class="flex justify-between items-center text-xs text-gray-500">
                            <div class="flex items-center space-x-3">
                                <div class="flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    <span>{{ $completedTasks }}/{{ $totalProjectTasks }}</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                    </svg>
                                    <span>{{ $teamMembers }}</span>
                                </div>
                            </div>

                            @if($project->due_date)
                                <div class="flex items-center {{ \Carbon\Carbon::parse($project->due_date)->isPast() ? 'text-red-500' : 'text-gray-500' }}">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span>{{ \Carbon\Carbon::parse($project->due_date)->format('M d') }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex space-x-2 mt-3 pt-3 border-t border-gray-100">
                            <a href="{{ route('manager.projects.show', $project->id) }}" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium py-2 px-3 rounded-md text-center transition-colors">
                                View
                            </a>
                            <a href="{{ route('manager.tasks.index', ['project_id' => $project->id]) }}" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium py-2 px-3 rounded-md text-center transition-colors">
                                Tasks
                            </a>
                        </div>
                    </div>
                @endforeach

                @if($projects->where('status', 'in_progress')->count() === 0)
                    <div class="text-center py-8 text-gray-400">
                        <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        <p class="text-sm">No projects in progress</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Completed Column -->
        <div class="bg-gray-50 rounded-lg p-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Completed</h3>
                <span class="bg-green-100 text-green-700 text-sm font-medium px-2 py-1 rounded-full">
                    {{ $projects->where('status', 'completed')->count() }}
                </span>
            </div>
            <div class="space-y-4">
                @foreach($projects->where('status', 'completed') as $project)
                    @php
                        $projectTasks = $project->tasks;
                        $completedTasks = $projectTasks->where('status', 'done')->count();
                        $totalProjectTasks = $projectTasks->count();
                        $progress = $totalProjectTasks > 0 ? round(($completedTasks / $totalProjectTasks) * 100) : 0;
                        $teamMembers = \App\Models\User::whereHas('assignedTasks', function($q) use ($project) {
                            $q->where('project_id', $project->id);
                        })->distinct()->count();
                    @endphp
                    <div class="bg-white rounded-lg border border-green-100 p-4 shadow-xs hover:shadow-sm transition-shadow">
                        <div class="flex justify-between items-start mb-3">
                            <h4 class="font-medium text-gray-900">{{ $project->name }}</h4>
                            <div class="flex space-x-1">
                                <form method="POST" action="{{ route('manager.projects.updateStatus', $project->id) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="pending">
                                    <button type="submit" class="text-gray-400 hover:text-gray-600 transition-colors p-1 rounded hover:bg-gray-100" title="Mark as Pending">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('manager.projects.updateStatus', $project->id) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="in_progress">
                                    <button type="submit" class="text-gray-400 hover:text-green-600 transition-colors p-1 rounded hover:bg-gray-100" title="Mark as In Progress">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $project->description ?: 'No description' }}</p>

                        <!-- Progress -->
                        <div class="mb-3">
                            <div class="flex justify-between text-xs text-gray-500 mb-1">
                                <span>Progress</span>
                                <span>{{ $progress }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                <div class="bg-green-500 h-1.5 rounded-full" style="width: {{ $progress }}%"></div>
                            </div>
                        </div>

                        <!-- Meta Info -->
                        <div class="flex justify-between items-center text-xs text-gray-500">
                            <div class="flex items-center space-x-3">
                                <div class="flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    <span>{{ $completedTasks }}/{{ $totalProjectTasks }}</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                    </svg>
                                    <span>{{ $teamMembers }}</span>
                                </div>
                            </div>

                            @if($project->due_date)
                                <div class="flex items-center text-gray-500">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span>{{ \Carbon\Carbon::parse($project->due_date)->format('M d') }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex space-x-2 mt-3 pt-3 border-t border-gray-100">
                            <a href="{{ route('manager.projects.show', $project->id) }}" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium py-2 px-3 rounded-md text-center transition-colors">
                                View
                            </a>
                            <a href="{{ route('manager.tasks.index', ['project_id' => $project->id]) }}" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium py-2 px-3 rounded-md text-center transition-colors">
                                Tasks
                            </a>
                        </div>
                    </div>
                @endforeach

                @if($projects->where('status', 'completed')->count() === 0)
                    <div class="text-center py-8 text-gray-400">
                        <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm">No completed projects</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endsection
