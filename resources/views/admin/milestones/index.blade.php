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
<div class="min-h-screen bg-gradient-to-br from-purple-50 to-indigo-50/30 py-4 sm:py-6">
    <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6">
        <!-- Header Section -->
        <div class="mb-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="flex-1">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3 mb-2">
                        <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Project Milestones</h1>
                    </div>
                    <p class="text-gray-600 text-sm">
                        @if($isSuperAdmin)
                            Viewing all milestones across all projects
                        @else
                            Track and manage your project milestones
                        @endif
                    </p>
                </div>
                <a href="{{ $milestoneCreateRoute }}"
                   class="bg-gradient-to-r from-purple-500 to-indigo-600 hover:from-purple-600 hover:to-indigo-700 text-white px-4 py-2.5 rounded-lg font-semibold transition-all duration-200 flex items-center justify-center shadow-lg hover:shadow-xl text-sm w-full sm:w-auto">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Create Milestone
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl flex items-center shadow-sm">
                <div class="w-5 h-5 bg-green-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                    <svg class="w-3 h-3 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <span class="font-medium text-sm">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
            @foreach([
                ['total', 'Total Milestones', 'purple', 'M13 10V3L4 14h7v7l9-11h-7z'],
                ['completed', 'Completed', 'green', 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['in_progress', 'In Progress', 'blue', 'M13 10V3L4 14h7v7l9-11h-7z'],
                ['pending', 'Pending', 'gray', 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z']
            ] as $stat)
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-200/60">
                <div class="flex items-center justify-between">
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-gray-600 truncate">{{ $stat[1] }}</p>
                        <p class="text-lg font-bold text-{{ $stat[2] }}-600 mt-1">{{ $milestoneStats[$stat[0]] }}</p>
                    </div>
                    <div class="w-8 h-8 bg-{{ $stat[2] }}-100 rounded-lg flex items-center justify-center flex-shrink-0 ml-2">
                        <svg class="w-4 h-4 text-{{ $stat[2] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $stat[3] }}"/>
                        </svg>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Filters and View Toggle -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200/60 p-4 mb-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <!-- View Toggle -->
                <div class="flex items-center bg-gray-50 rounded-lg p-1">
                    <button id="gridViewBtn" class="view-toggle-btn active flex items-center px-3 py-2 rounded-md bg-white text-gray-900 font-medium transition-all duration-200 text-sm shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                        </svg>
                        Grid
                    </button>
                    <button id="listViewBtn" class="view-toggle-btn flex items-center px-3 py-2 rounded-md text-gray-600 font-medium transition-all duration-200 text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                        List
                    </button>
                </div>

                <!-- Filters -->
                <div class="flex flex-col sm:flex-row gap-3 flex-1 lg:justify-end">
                    <!-- Project Filter -->
                    <select id="project_filter" onchange="filterByProject(this.value)"
                            class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm bg-white min-w-0 flex-1">
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

                    <!-- Status Filter -->
                    <select id="status_filter" onchange="filterByStatus(this.value)"
                            class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm bg-white min-w-0 flex-1">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>

                    <!-- Reset Filters -->
                    <button onclick="resetFilters()" 
                            class="px-3 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition duration-200 font-medium text-sm bg-white min-w-0 flex items-center justify-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Reset
                    </button>
                </div>
            </div>
        </div>

        <!-- Grid View -->
        <div id="gridView" class="milestone-view active">
            @if($milestones->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    @foreach($milestones as $milestone)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200/60 p-4 hover:shadow-md transition-all duration-300 group">
                            <!-- Milestone Header -->
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-semibold text-gray-900 text-sm mb-2 line-clamp-2 group-hover:text-purple-600 transition-colors duration-200">
                                        <a href="{{ str_replace(':id', $milestone->id, $milestoneShowRoute) }}" class="hover:underline">
                                            {{ $milestone->title }}
                                        </a>
                                    </h3>
                                    <div class="flex items-center space-x-2">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold
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
                            <div class="flex items-center justify-between mb-3 text-xs">
                                <span class="text-gray-600">Project</span>
                                <div class="text-right min-w-0 ml-2">
                                    <a href="{{ str_replace(':id', $milestone->project_id, $projectShowRoute) }}"
                                       class="font-medium text-blue-600 hover:text-blue-800 transition-colors duration-200 block truncate text-xs">
                                        {{ $milestone->project->name }}
                                    </a>
                                </div>
                            </div>

                            <!-- Progress Bar -->
                            @php
                                $completedTasks = $milestone->tasks->where('status', 'done')->count();
                                $totalTasks = $milestone->tasks->count();
                                $progress = $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 0;
                            @endphp
                            <div class="mb-3">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-xs font-medium text-gray-700">Progress</span>
                                    <span class="text-xs text-gray-600">{{ round($progress) }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-1.5">
                                    <div class="h-1.5 rounded-full bg-gradient-to-r from-purple-500 to-indigo-600 transition-all duration-500"
                                         style="width: {{ $progress }}%"></div>
                                </div>
                                <div class="flex justify-between mt-1">
                                    <span class="text-xs text-gray-500">{{ $completedTasks }}/{{ $totalTasks }} tasks</span>
                                </div>
                            </div>

                            <!-- Due Date -->
                            @if($milestone->due_date)
                            <div class="flex items-center justify-between mb-3 text-xs">
                                <span class="text-gray-600">Due Date</span>
                                <div class="text-right">
                                    <span class="font-medium {{ $milestone->due_date < now() && $milestone->status != 'completed' ? 'text-red-600' : 'text-gray-900' }} text-xs">
                                        {{ \Carbon\Carbon::parse($milestone->due_date)->format('M d, Y') }}
                                    </span>
                                    @if($milestone->due_date < now() && $milestone->status != 'completed')
                                    <span class="text-xs text-red-500 block">Overdue</span>
                                    @endif
                                </div>
                            </div>
                            @endif

                            <!-- Actions -->
                            <div class="flex items-center justify-between pt-3 border-t border-gray-200">
                                <div class="text-xs text-gray-500">
                                    {{ $milestone->updated_at->diffForHumans() }}
                                </div>
                                <div class="flex space-x-2">
                                    <a href="{{ str_replace(':id', $milestone->id, $milestoneShowRoute) }}"
                                       class="text-blue-600 hover:text-blue-800 transition-colors duration-200 p-1 rounded hover:bg-blue-50"
                                       title="View">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ Auth::user()->role === 'super_admin' ? route('milestones.edit', $milestone->id) : route('manager.milestones.edit', $milestone->id) }}"
                                       class="text-purple-600 hover:text-purple-800 transition-colors duration-200 p-1 rounded hover:bg-purple-50"
                                       title="Edit">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $milestones->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200/60 p-8 text-center">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No milestones found</h3>
                    <p class="text-gray-600 mb-4 text-sm">
                        @if($isSuperAdmin)
                            No milestones match your current filters.
                        @else
                            No milestones found for your projects.
                        @endif
                    </p>
                    <button onclick="resetFilters()" 
                            class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200 text-sm">
                        Reset Filters
                    </button>
                </div>
            @endif
        </div>

        <!-- List View -->
        <div id="listView" class="milestone-view hidden">
            @if($milestones->count() > 0)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200/60 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Milestone</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Project</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Progress</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Due Date</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($milestones as $milestone)
                                @php
                                    $completedTasks = $milestone->tasks->where('status', 'done')->count();
                                    $totalTasks = $milestone->tasks->count();
                                    $progress = $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 0;
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-3 py-3 whitespace-nowrap">
                                        <div class="min-w-0">
                                            <div class="font-medium text-gray-900 truncate max-w-[150px] text-sm">
                                                <a href="{{ str_replace(':id', $milestone->id, $milestoneShowRoute) }}" class="hover:text-purple-600 hover:underline">
                                                    {{ $milestone->title }}
                                                </a>
                                            </div>
                                            <div class="text-xs text-gray-500 mt-1">
                                                {{ $milestone->tasks->count() }} tasks
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap">
                                        <div class="text-gray-900 truncate max-w-[120px] text-sm">
                                            <a href="{{ str_replace(':id', $milestone->project_id, $projectShowRoute) }}" class="hover:text-blue-600 hover:underline">
                                                {{ $milestone->project->name }}
                                            </a>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold
                                            @if($milestone->status == 'completed') bg-green-100 text-green-800
                                            @elseif($milestone->status == 'in_progress') bg-blue-100 text-blue-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst(str_replace('_', ' ', $milestone->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap">
                                        <div class="w-20">
                                            <div class="flex justify-between text-xs text-gray-500 mb-1">
                                                <span>{{ round($progress) }}%</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                                <div class="h-1.5 rounded-full bg-gradient-to-r from-purple-500 to-indigo-600"
                                                     style="width: {{ $progress }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-900">
                                        @if($milestone->due_date)
                                            <div class="{{ $milestone->due_date < now() && $milestone->status != 'completed' ? 'text-red-600' : 'text-gray-900' }}">
                                                {{ \Carbon\Carbon::parse($milestone->due_date)->format('M d, Y') }}
                                            </div>
                                            @if($milestone->due_date < now() && $milestone->status != 'completed')
                                            <div class="text-xs text-red-500">Overdue</div>
                                            @endif
                                        @else
                                            <span class="text-gray-400 text-xs">No due date</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap">
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ str_replace(':id', $milestone->id, $milestoneShowRoute) }}"
                                               class="text-blue-600 hover:text-blue-800 p-1 rounded hover:bg-blue-50 transition-colors"
                                               title="View">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </a>
                                            <a href="{{ Auth::user()->role === 'super_admin' ? route('milestones.edit', $milestone->id) : route('manager.milestones.edit', $milestone->id) }}"
                                               class="text-purple-600 hover:text-purple-800 p-1 rounded hover:bg-purple-50 transition-colors"
                                               title="Edit">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $milestones->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200/60 p-8 text-center">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No milestones found</h3>
                    <p class="text-gray-600 mb-4 text-sm">
                        @if($isSuperAdmin)
                            No milestones match your current filters.
                        @else
                            No milestones found for your projects.
                        @endif
                    </p>
                    <button onclick="resetFilters()" 
                            class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200 text-sm">
                        Reset Filters
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const gridViewBtn = document.getElementById('gridViewBtn');
    const listViewBtn = document.getElementById('listViewBtn');
    const gridView = document.getElementById('gridView');
    const listView = document.getElementById('listView');

    // View toggle functionality
    gridViewBtn.addEventListener('click', function() {
        switchView('grid');
    });

    listViewBtn.addEventListener('click', function() {
        switchView('list');
    });

    function switchView(viewType) {
        // Update button states
        [gridViewBtn, listViewBtn].forEach(btn => {
            btn.classList.remove('active', 'bg-blue-50', 'text-blue-600');
            btn.classList.add('text-gray-600', 'hover:bg-gray-100');
        });
        
        if (viewType === 'grid') {
            gridViewBtn.classList.add('active', 'bg-blue-50', 'text-blue-600');
            gridViewBtn.classList.remove('text-gray-600', 'hover:bg-gray-100');
            gridView.classList.remove('hidden');
            gridView.classList.add('active');
            listView.classList.add('hidden');
            listView.classList.remove('active');
        } else {
            listViewBtn.classList.add('active', 'bg-blue-50', 'text-blue-600');
            listViewBtn.classList.remove('text-gray-600', 'hover:bg-gray-100');
            listView.classList.remove('hidden');
            listView.classList.add('active');
            gridView.classList.add('hidden');
            gridView.classList.remove('active');
        }
    }
});

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

function resetFilters() {
    window.location.href = "{{ url()->current() }}";
}
</script>

<style>
.view-toggle-btn.active {
    background-color: rgb(239, 246, 255);
    color: rgb(37, 99, 235);
}

.view-toggle-btn {
    transition: all 0.2s ease-in-out;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.min-w-0 {
    min-width: 0;
}

/* Responsive table */
@media (max-width: 640px) {
    .overflow-x-auto {
        -webkit-overflow-scrolling: touch;
    }
}
</style>


@endsection