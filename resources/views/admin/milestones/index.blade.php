@php
    $layout = match(Auth::user()->role) {
        'super_admin' => 'admin.layouts.app',
        'admin' => 'manager.layouts.app',
        'user' => 'team.app',
    };

    // Define routes based on role
    $milestoneCreateRoute = Auth::user()->role === 'super_admin' ? route('milestones.create') : route('manager.milestones.create');
    $milestoneShowRoute = Auth::user()->role === 'super_admin' ? route('milestones.show', ':id') : route('manager.milestones.show', ':id');
    $projectShowRoute = Auth::user()->role === 'super_admin' ? route('projects.show', ':id') : route('manager.projects.show', ':id');
@endphp

@extends($layout)

@section("content")
<div class="min-h-screen bg-gradient-to-br from-purple-50 to-indigo-50/30 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="flex-1">
                    <div class="flex items-center space-x-3 mb-3">
                        <h1 class="text-3xl font-bold text-gray-900">Project Milestones</h1>
                        @if($isSuperAdmin)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800 border border-red-200">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/>
                            </svg>
                            Super Admin View
                        </span>
                        @endif
                    </div>
                    <p class="text-gray-600">
                        @if($isSuperAdmin)
                            Viewing all milestones across all projects
                        @else
                            Track and manage your project milestones
                        @endif
                    </p>
                </div>
                <div class="flex items-center space-x-3">
                    <!-- Project Filter -->
                    <select id="project_filter" onchange="filterByProject(this.value)"
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm bg-white">
                        <option value="">All Projects</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                                {{ $project->name }}
                                @if($isSuperAdmin && $project->manager)
                                    ({{ $project->manager->name }})
                                @endif
                            </option>
                        @endforeach
                    </select>

                    <!-- Create Milestone Button -->
                    <a href="{{ $milestoneCreateRoute }}"
                       class="group relative bg-gradient-to-r from-purple-500 to-indigo-600 hover:from-purple-600 hover:to-indigo-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-200 flex items-center shadow-lg hover:shadow-xl hover:scale-105 active:scale-95">
                        <div class="w-5 h-5 bg-white/20 rounded-full flex items-center justify-center mr-2">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                        </div>
                        Create Milestone
                    </a>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-8 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 text-green-800 px-6 py-4 rounded-2xl flex items-center shadow-sm animate-fade-in">
                <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center mr-3">
                    <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200/60">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Milestones</p>
                        <p class="text-2xl font-bold text-purple-600 mt-1">{{ $milestoneStats['total'] }}</p>
                        @if($isSuperAdmin)
                        <p class="text-xs text-gray-500 mt-1">Across all projects</p>
                        @endif
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200/60">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Completed</p>
                        <p class="text-2xl font-bold text-green-600 mt-1">{{ $milestoneStats['completed'] }}</p>
                        @if($isSuperAdmin)
                        <p class="text-xs text-gray-500 mt-1">{{ $milestoneStats['total'] > 0 ? round(($milestoneStats['completed'] / $milestoneStats['total']) * 100) : 0 }}% completion</p>
                        @endif
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200/60">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">In Progress</p>
                        <p class="text-2xl font-bold text-blue-600 mt-1">{{ $milestoneStats['in_progress'] }}</p>
                        @if($isSuperAdmin)
                        <p class="text-xs text-gray-500 mt-1">Active milestones</p>
                        @endif
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200/60">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Pending</p>
                        <p class="text-2xl font-bold text-gray-600 mt-1">{{ $milestoneStats['pending'] }}</p>
                        @if($isSuperAdmin)
                        <p class="text-xs text-gray-500 mt-1">Not started yet</p>
                        @endif
                    </div>
                    <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Milestones Grid -->
        @if($milestones->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($milestones as $milestone)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200/60 p-6 hover:shadow-md transition-all duration-300 group">
                        <!-- Milestone Header -->
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900 text-lg mb-2 line-clamp-2 group-hover:text-purple-600 transition-colors duration-200">
                                    <a href="{{ str_replace(':id', $milestone->id, $milestoneShowRoute) }}" class="hover:underline">
                                        {{ $milestone->title }}
                                    </a>
                                </h3>
                                <div class="flex items-center space-x-2">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                        @if($milestone->status == 'completed') bg-green-100 text-green-800
                                        @elseif($milestone->status == 'in_progress') bg-blue-100 text-blue-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst(str_replace('_', ' ', $milestone->status)) }}
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        {{ $milestone->tasks->count() }} tasks
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Project Info -->
                        <div class="flex items-center justify-between mb-4 text-sm">
                            <span class="text-gray-600">Project</span>
                            <div class="text-right">
                                <a href="{{ str_replace(':id', $milestone->project_id, $projectShowRoute) }}"
                                   class="font-medium text-blue-600 hover:text-blue-800 transition-colors duration-200 block">
                                    {{ $milestone->project->name }}
                                </a>
                                @if($isSuperAdmin && $milestone->project->manager)
                                <span class="text-xs text-gray-500">by {{ $milestone->project->manager->name }}</span>
                                @endif
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        @php
                            $completedTasks = $milestone->tasks->where('status', 'done')->count();
                            $totalTasks = $milestone->tasks->count();
                            $progress = $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 0;
                        @endphp
                        <div class="mb-4">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-medium text-gray-700">Progress</span>
                                <span class="text-sm text-gray-600">{{ round($progress) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="h-2 rounded-full bg-gradient-to-r from-purple-500 to-indigo-600 transition-all duration-500"
                                     style="width: {{ $progress }}%"></div>
                            </div>
                            <div class="flex justify-between mt-1">
                                <span class="text-xs text-gray-500">{{ $completedTasks }} of {{ $totalTasks }} tasks</span>
                            </div>
                        </div>

                        <!-- Due Date -->
                        @if($milestone->due_date)
                        <div class="flex items-center justify-between mb-4 text-sm">
                            <span class="text-gray-600">Due Date</span>
                            <div class="text-right">
                                <span class="font-medium {{ $milestone->due_date < now() && $milestone->status != 'completed' ? 'text-red-600' : 'text-gray-900' }}">
                                    {{ \Carbon\Carbon::parse($milestone->due_date)->format('M d, Y') }}
                                </span>
                                @if($milestone->due_date < now() && $milestone->status != 'completed')
                                <span class="text-xs text-red-500 block">Overdue</span>
                                @endif
                            </div>
                        </div>
                        @endif

                        <!-- Actions -->
                        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                            <div class="text-xs text-gray-500">
                                Updated {{ $milestone->updated_at->diffForHumans() }}
                            </div>
                            <div class="flex space-x-2">
                                <a href="{{ str_replace(':id', $milestone->id, $milestoneShowRoute) }}"
                                   class="text-blue-600 hover:text-blue-800 transition-colors duration-200"
                                   title="View Milestone">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                <a href="{{ Auth::user()->role === 'super_admin' ? route('milestones.edit', $milestone->id) : route('manager.milestones.edit', $milestone->id) }}"
                                   class="text-purple-600 hover:text-purple-800 transition-colors duration-200"
                                   title="Edit Milestone">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $milestones->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200/60 p-12 text-center">
                <div class="w-24 h-24 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-12 h-12 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No milestones found</h3>
                <p class="text-gray-600 mb-6 max-w-md mx-auto">
                    @if($isSuperAdmin)
                        There are no milestones across all projects yet.
                    @else
                        You haven't created any milestones for your projects yet.
                    @endif
                </p>
                <a href="{{ $milestoneCreateRoute }}"
                   class="bg-gradient-to-r from-purple-500 to-indigo-600 hover:from-purple-600 hover:to-indigo-700 text-white px-8 py-3 rounded-xl font-semibold transition-all duration-200 inline-flex items-center shadow-lg hover:shadow-xl">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Create Your First Milestone
                </a>
            </div>
        @endif
    </div>
</div>

<script>
function filterByProject(projectId) {
    const url = new URL(window.location.href);
    if (projectId) {
        url.searchParams.set('project_id', projectId);
    } else {
        url.searchParams.delete('project_id');
    }
    window.location.href = url.toString();
}

function filterByStatus(status) {
    const url = new URL(window.location.href);
    if (status) {
        url.searchParams.set('status', status);
    } else {
        url.searchParams.delete('status');
    }
    window.location.href = url.toString();
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
</style>
@endsection
