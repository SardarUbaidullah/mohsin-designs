@extends('admin.layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6 py-4 sm:py-6 lg:py-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6 sm:mb-8">
            <div class="flex-1 min-w-0">
                <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 truncate">Projects</h1>
                <p class="text-gray-600 mt-1 sm:mt-2 text-xs sm:text-sm lg:text-base truncate">Manage all projects in Kanban
                    view</p>
            </div>
            <a href="{{ route('projects.create') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 sm:px-5 lg:px-6 py-2 sm:py-2.5 lg:py-3 rounded-lg font-medium transition duration-200 flex items-center justify-center shadow-sm w-full sm:w-auto text-sm sm:text-base">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1 sm:mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6">
                    </path>
                </svg>
                Create New Project
            </a>
        </div>

        <!-- Success/Error Messages -->
        @if (session('success'))
            <div
                class="bg-green-50 border border-green-200 text-green-700 px-3 sm:px-4 py-2 sm:py-3 rounded-lg mb-4 sm:mb-6 flex items-center text-sm sm:text-base">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div
                class="bg-red-50 border border-red-200 text-red-700 px-3 sm:px-4 py-2 sm:py-3 rounded-lg mb-4 sm:mb-6 flex items-center text-sm sm:text-base">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                        clip-rule="evenodd" />
                </svg>
                {{ session('error') }}
            </div>
        @endif

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6 mb-6 sm:mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
                <div class="flex items-center">
                    <div
                        class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-100 rounded-xl flex items-center justify-center mr-3 sm:mr-4">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Projects</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $projects->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
                <div class="flex items-center">
                    <div
                        class="w-10 h-10 sm:w-12 sm:h-12 bg-green-100 rounded-xl flex items-center justify-center mr-3 sm:mr-4">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Tasks</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalTasks }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
                <div class="flex items-center">
                    <div
                        class="w-10 h-10 sm:w-12 sm:h-12 bg-purple-100 rounded-xl flex items-center justify-center mr-3 sm:mr-4">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-purple-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Team Members</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $teamMembersCount }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category Filter -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-5 lg:p-6 mb-4 sm:mb-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Category Filter -->
                    <div class="min-w-0">
                        <label for="category_filter"
                            class="block text-xs sm:text-sm font-medium text-gray-700 mb-2 truncate">Filter by
                            Category</label>
                        <select id="category_filter"
                            class="category-filter w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white shadow-sm">
                            <option value="all">All Categories</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div class="min-w-0">
                        <label for="status_filter"
                            class="block text-xs sm:text-sm font-medium text-gray-700 mb-2 truncate">Filter by
                            Status</label>
                        <select id="status_filter"
                            class="status-filter w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white shadow-sm">
                            <option value="all">All Statuses</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Planning</option>
                            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In
                                Progress</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed
                            </option>
                        </select>
                    </div>

                    <!-- Reset Filters -->
                    <div class="min-w-0 flex items-end">
                        <button id="reset_filters"
                            class="w-full lg:w-auto px-4 sm:px-6 py-2 sm:py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition duration-200 font-medium flex items-center justify-center text-sm sm:text-base bg-white shadow-sm">
                            <svg class="w-4 h-4 sm:w-4 sm:h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Reset Filters
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- View Toggle and Mobile Filter Tabs -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 sm:mb-6 gap-4">
            <!-- View Toggle -->
            <div class="flex items-center bg-white rounded-lg shadow-sm border border-gray-200 p-1">
                <button id="gridViewBtn"
                    class="view-toggle-btn active flex items-center px-3 sm:px-4 py-2 rounded-lg bg-blue-50 text-blue-600 font-medium transition-all duration-200 text-xs sm:text-sm">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1 sm:mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    <span class="truncate">Grid View</span>
                </button>
                <button id="listViewBtn"
                    class="view-toggle-btn flex items-center px-3 sm:px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-100 font-medium transition-all duration-200 text-xs sm:text-sm">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1 sm:mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                    <span class="truncate">List View</span>
                </button>
            </div>

            <!-- Professional Mobile Filter Tabs -->
            <div class="lg:hidden">
                <div
                    class="flex space-x-1 bg-white p-1 rounded-lg sm:rounded-xl shadow-sm border border-gray-200 overflow-x-auto">
                    <button type="button" data-category="planning"
                        class="file-filter-tab active flex-1 min-w-0 px-3 sm:px-4 py-2 sm:py-3 text-xs sm:text-sm font-medium rounded-lg bg-blue-100 text-blue-800 whitespace-nowrap transition-all duration-200">
                        Planning ({{ $projects->where('status', 'pending')->count() }})
                    </button>
                    <button type="button" data-category="progress"
                        class="file-filter-tab flex-1 min-w-0 px-3 sm:px-4 py-2 sm:py-3 text-xs sm:text-sm font-medium rounded-lg text-gray-600 hover:bg-gray-100 whitespace-nowrap transition-all duration-200">
                        In Progress ({{ $projects->where('status', 'in_progress')->count() }})
                    </button>
                    <button type="button" data-category="completed"
                        class="file-filter-tab flex-1 min-w-0 px-3 sm:px-4 py-2 sm:py-3 text-xs sm:text-sm font-medium rounded-lg text-gray-600 hover:bg-gray-100 whitespace-nowrap transition-all duration-200">
                        Completed ({{ $projects->where('status', 'completed')->count() }})
                    </button>
                </div>
            </div>
        </div>

        <!-- Grid View (Kanban Board) -->
        <div id="gridView" class="project-view active">
            @if ($projects->count() > 0)
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-5 lg:gap-6 mb-6 sm:mb-8">
                    <!-- Planning Column -->
                    <div data-category="planning" class="file-column bg-gray-50 rounded-xl p-4 sm:p-5 lg:p-6">
                        <div class="flex items-center justify-between mb-4 sm:mb-5 lg:mb-6">
                            <h3 class="text-base sm:text-lg font-semibold text-gray-900 flex items-center min-w-0">
                                <div
                                    class="w-2 h-2 sm:w-2.5 sm:h-2.5 lg:w-3 lg:h-3 bg-gray-400 rounded-full mr-2 sm:mr-3 flex-shrink-0">
                                </div>
                                <span class="truncate">Planning</span>
                            </h3>
                            <span
                                class="bg-gray-200 text-gray-700 px-2 sm:px-2.5 lg:px-3 py-1 rounded-full text-xs font-medium flex-shrink-0 ml-2">
                                {{ $projects->where('status', 'pending')->count() }}
                            </span>
                        </div>
                        <div class="space-y-3 sm:space-y-4">
                            @foreach ($projects->where('status', 'pending') as $project)
                                @include('projects.partials.project-card', [
                                    'project' => $project,
                                    'status' => 'pending',
                                ])
                            @endforeach
                        </div>
                    </div>

                    <!-- In Progress Column -->
                    <div data-category="progress" class="file-column bg-gray-50 rounded-xl p-4 sm:p-5 lg:p-6">
                        <div class="flex items-center justify-between mb-4 sm:mb-5 lg:mb-6">
                            <h3 class="text-base sm:text-lg font-semibold text-gray-900 flex items-center min-w-0">
                                <div
                                    class="w-2 h-2 sm:w-2.5 sm:h-2.5 lg:w-3 lg:h-3 bg-yellow-400 rounded-full mr-2 sm:mr-3 flex-shrink-0">
                                </div>
                                <span class="truncate">In Progress</span>
                            </h3>
                            <span
                                class="bg-yellow-100 text-yellow-800 px-2 sm:px-2.5 lg:px-3 py-1 rounded-full text-xs font-medium flex-shrink-0 ml-2">
                                {{ $projects->where('status', 'in_progress')->count() }}
                            </span>
                        </div>
                        <div class="space-y-3 sm:space-y-4">
                            @foreach ($projects->where('status', 'in_progress') as $project)
                                @include('projects.partials.project-card', [
                                    'project' => $project,
                                    'status' => 'in_progress',
                                ])
                            @endforeach
                        </div>
                    </div>

                    <!-- Completed Column -->
                    <div data-category="completed" class="file-column bg-gray-50 rounded-xl p-4 sm:p-5 lg:p-6">
                        <div class="flex items-center justify-between mb-4 sm:mb-5 lg:mb-6">
                            <h3 class="text-base sm:text-lg font-semibold text-gray-900 flex items-center min-w-0">
                                <div
                                    class="w-2 h-2 sm:w-2.5 sm:h-2.5 lg:w-3 lg:h-3 bg-green-400 rounded-full mr-2 sm:mr-3 flex-shrink-0">
                                </div>
                                <span class="truncate">Completed</span>
                            </h3>
                            <span
                                class="bg-green-100 text-green-800 px-2 sm:px-2.5 lg:px-3 py-1 rounded-full text-xs font-medium flex-shrink-0 ml-2">
                                {{ $projects->where('status', 'completed')->count() }}
                            </span>
                        </div>
                        <div class="space-y-3 sm:space-y-4">
                            @foreach ($projects->where('status', 'completed') as $project)
                                @include('projects.partials.project-card', [
                                    'project' => $project,
                                    'status' => 'completed',
                                ])
                            @endforeach
                        </div>
                    </div>
                </div>
            @else
                <!-- Empty State -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 text-center py-8 sm:py-10 lg:py-12 px-4">
                    <svg class="w-12 h-12 sm:w-14 sm:h-14 lg:w-16 lg:h-16 mx-auto text-gray-400 mb-3 sm:mb-4"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="text-lg sm:text-xl font-medium text-gray-900 mb-1 sm:mb-2">No projects found</p>
                    <p class="text-gray-600 mb-4 sm:mb-6 text-sm sm:text-base">
                        @if (request()->has('category_id') || request()->has('status'))
                            No projects match your current filters. Try adjusting your search criteria.
                        @else
                            Get started by creating your first project
                        @endif
                    </p>
                    <a href="{{ route('projects.create') }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 sm:px-5 lg:px-6 py-2 sm:py-2.5 lg:py-3 rounded-lg font-medium transition duration-200 inline-flex items-center justify-center text-sm sm:text-base">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1 sm:mr-2 flex-shrink-0" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Create Your First Project
                    </a>
                </div>
            @endif
        </div>

        <!-- List View -->
        <div id="listView" class="project-view hidden">
            @if ($projects->count() > 0)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="px-3 sm:px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Project</th>
                                    <th scope="col"
                                        class="px-3 sm:px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Description</th>
                                    <th scope="col"
                                        class="px-3 sm:px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                    <th scope="col"
                                        class="px-3 sm:px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date</th>
                                    <th scope="col"
                                        class="px-3 sm:px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Progress</th>
                                    <th scope="col"
                                        class="px-3 sm:px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($projects as $project)
                                    @php
                                        $totalTasks = $project->tasks->count();
                                        $completedTasks = $project->tasks->where('status', 'done')->count();
                                        $progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
                                        $progressWidth =
                                            $project->status === 'pending'
                                                ? 0
                                                : ($project->status === 'completed'
                                                    ? 100
                                                    : $progress);
                                        $progressColor =
                                            $project->status === 'pending'
                                                ? 'bg-gray-400'
                                                : ($project->status === 'completed'
                                                    ? 'bg-green-500'
                                                    : 'bg-yellow-500');
                                    @endphp
                                    <tr
                                        class="hover:bg-gray-50 transition-colors duration-150 
                            @if ($project->status === 'in_progress') border-l-4 border-l-yellow-200 @endif
                            @if ($project->status === 'completed') border-l-4 border-l-green-200 @endif">
                                        <td class="px-3 sm:px-4 lg:px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div
                                                    class="flex-shrink-0 h-8 w-8 sm:h-10 sm:w-10 rounded-lg flex items-center justify-center text-white font-semibold text-sm shadow-sm
                                        @if ($project->status === 'pending') bg-gray-500
                                        @elseif($project->status === 'in_progress') bg-yellow-500
                                        @else bg-green-500 @endif">
                                                    {{ strtoupper(substr($project->name, 0, 1)) }}
                                                </div>
                                                <div class="ml-3 min-w-0">
                                                    <div class="text-sm font-medium text-gray-900 truncate">
                                                        {{ $project->name }}</div>
                                                    <div class="text-xs text-gray-400">#{{ $project->id }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-3 sm:px-4 lg:px-6 py-4">
                                            <div class="text-xs text-gray-600 max-w-xs line-clamp-2">
                                                {{ $project->description ?: 'No description provided' }}
                                            </div>
                                        </td>
                                        <td class="px-3 sm:px-4 lg:px-6 py-4 whitespace-nowrap">
                                            @php
                                                $statusColors = [
                                                    'pending' => 'bg-gray-100 text-gray-800',
                                                    'in_progress' => 'bg-yellow-100 text-yellow-800',
                                                    'completed' => 'bg-green-100 text-green-800',
                                                ];
                                                $statusColor =
                                                    $statusColors[$project->status] ?? 'bg-gray-100 text-gray-800';
                                                $statusLabels = [
                                                    'pending' => 'Planning',
                                                    'in_progress' => 'In Progress',
                                                    'completed' => 'Completed',
                                                ];
                                                $statusLabel =
                                                    $statusLabels[$project->status] ?? ucfirst($project->status);
                                            @endphp
                                            <span
                                                class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColor }}">
                                                {{ $statusLabel }}
                                            </span>
                                        </td>
                                        <td class="px-3 sm:px-4 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div class="flex items-center">
                                                <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 flex-shrink-0 text-gray-400"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                @if ($project->status === 'pending')
                                                    {{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('M d, Y') : 'No date' }}
                                                @elseif($project->status === 'in_progress')
                                                    {{ $project->due_date ? \Carbon\Carbon::parse($project->due_date)->format('M d, Y') : 'No due date' }}
                                                @else
                                                    Completed
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-3 sm:px-4 lg:px-6 py-4 whitespace-nowrap">
                                            <div class="w-24 sm:w-32">
                                                <div class="flex justify-between text-xs text-gray-500 mb-1">
                                                    <span>Progress</span>
                                                    <span>{{ $progressWidth }}%</span>
                                                </div>
                                                <div class="w-full bg-gray-200 rounded-full h-1.5">
                                                    <div class="{{ $progressColor }} h-1.5 rounded-full"
                                                        style="width: {{ $progressWidth }}%"></div>
                                                </div>
                                                <div class="text-xs text-gray-400 mt-1">
                                                    {{ $completedTasks }}/{{ $totalTasks }} tasks
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-3 sm:px-4 lg:px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center space-x-1 sm:space-x-2">
                                                <a href="{{ route('projects.show', $project) }}"
                                                    class="text-blue-600 hover:text-blue-800 p-1 sm:p-2 rounded-lg hover:bg-blue-50 transition-colors flex items-center text-xs font-medium"
                                                    title="View Project">
                                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 flex-shrink-0" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    <span class="hidden sm:inline">View</span>
                                                </a>
                                                <a href="{{ route('projects.edit', $project) }}"
                                                    class="text-gray-600 hover:text-gray-800 p-1 sm:p-2 rounded-lg hover:bg-gray-50 transition-colors flex items-center text-xs font-medium"
                                                    title="Edit Project">
                                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 flex-shrink-0" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                    <span class="hidden sm:inline">Edit</span>
                                                </a>
                                                <form action="{{ route('projects.destroy', $project) }}" method="POST"
                                                    class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        onclick="return confirm('Are you sure you want to delete this project?')"
                                                        class="text-red-600 hover:text-red-800 p-1 sm:p-2 rounded-lg hover:bg-red-50 transition-colors flex items-center text-xs font-medium"
                                                        title="Delete Project">
                                                        <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 flex-shrink-0"
                                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                        <span class="hidden sm:inline">Delete</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <!-- Empty State -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 text-center py-8 sm:py-10 lg:py-12 px-4">
                    <svg class="w-12 h-12 sm:w-14 sm:h-14 lg:w-16 lg:h-16 mx-auto text-gray-400 mb-3 sm:mb-4"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="text-lg sm:text-xl font-medium text-gray-900 mb-1 sm:mb-2">No projects found</p>
                    <p class="text-gray-600 mb-4 sm:mb-6 text-sm sm:text-base">
                        @if (request()->has('category_id') || request()->has('status'))
                            No projects match your current filters. Try adjusting your search criteria.
                        @else
                            Get started by creating your first project
                        @endif
                    </p>
                    <a href="{{ route('projects.create') }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 sm:px-5 lg:px-6 py-2 sm:py-2.5 lg:py-3 rounded-lg font-medium transition duration-200 inline-flex items-center justify-center text-sm sm:text-base">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1 sm:mr-2 flex-shrink-0" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Create Your First Project
                    </a>
                </div>
            @endif
        </div>

    </div>

    <script>
        // Professional file filter - clean and working
        document.addEventListener('DOMContentLoaded', function() {
            const filterTabs = document.querySelectorAll('.file-filter-tab');
            const fileColumns = document.querySelectorAll('.file-column');
            const categoryFilter = document.getElementById('category_filter');
            const statusFilter = document.getElementById('status_filter');
            const resetFilters = document.getElementById('reset_filters');

            // View toggle elements
            const gridViewBtn = document.getElementById('gridViewBtn');
            const listViewBtn = document.getElementById('listViewBtn');
            const gridView = document.getElementById('gridView');
            const listView = document.getElementById('listView');
            const viewToggleBtns = document.querySelectorAll('.view-toggle-btn');

            // View toggle functionality
            gridViewBtn.addEventListener('click', function() {
                switchView('grid');
            });

            listViewBtn.addEventListener('click', function() {
                switchView('list');
            });

            function switchView(viewType) {
                // Update button states
                viewToggleBtns.forEach(btn => {
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

            // Filter function
            function applyFilters() {
                const category = categoryFilter.value;
                const status = statusFilter.value;

                let url = new URL(window.location.href);
                let params = new URLSearchParams();

                if (category !== 'all') {
                    params.set('category_id', category);
                }

                if (status !== 'all') {
                    params.set('status', status);
                }

                const queryString = params.toString();
                window.location.href = queryString ? `${url.pathname}?${queryString}` : url.pathname;
            }

            // Category filter change
            if (categoryFilter) {
                categoryFilter.addEventListener('change', applyFilters);
            }

            // Status filter change
            if (statusFilter) {
                statusFilter.addEventListener('change', applyFilters);
            }

            // Reset filters
            if (resetFilters) {
                resetFilters.addEventListener('click', function() {
                    window.location.href = "{{ route('projects.index') }}";
                });
            }

            // Initialize mobile view
            function initMobileView() {
                if (window.innerWidth < 1024) {
                    fileColumns.forEach((col, index) => {
                        if (index === 0) {
                            col.style.display = 'block';
                        } else {
                            col.style.display = 'none';
                        }
                    });
                } else {
                    fileColumns.forEach(col => {
                        col.style.display = 'block';
                    });
                }
            }

            // Filter tab click handler
            filterTabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const category = this.getAttribute('data-category');

                    // Update active tab
                    filterTabs.forEach(t => {
                        t.classList.remove('active', 'bg-blue-100', 'text-blue-800');
                        t.classList.add('text-gray-600', 'hover:bg-gray-100');
                    });
                    this.classList.remove('text-gray-600', 'hover:bg-gray-100');
                    this.classList.add('active', 'bg-blue-100', 'text-blue-800');

                    // Show selected column, hide others on mobile
                    if (window.innerWidth < 1024) {
                        fileColumns.forEach(col => {
                            if (col.getAttribute('data-category') === category) {
                                col.style.display = 'block';
                            } else {
                                col.style.display = 'none';
                            }
                        });
                    }
                });
            });

            // Handle window resize
            window.addEventListener('resize', function() {
                initMobileView();
            });

            // Initial setup
            initMobileView();
        });
    </script>

    <style>
        /* Mobile responsive behavior */
        @media (max-width: 1023px) {
            .file-column {
                display: none;
            }

            /* Only show the first column by default on mobile */
            .file-column:first-child {
                display: block;
            }
        }

        .view-toggle-btn.active {
            background-color: rgb(239, 246, 255);
            color: rgb(37, 99, 235);
        }

        .view-toggle-btn {
            transition: all 0.2s ease-in-out;
        }

        .project-view {
            transition: opacity 0.3s ease-in-out;
        }

        /* Utility classes */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .hover-shadow-md {
            transition: box-shadow 0.2s ease-in-out;
        }

        .truncate {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .min-w-0 {
            min-width: 0;
        }

        /* Filter styles */
        .category-filter,
        .status-filter {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.5rem center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
            padding-right: 2.5rem;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    </style>
@endsection
