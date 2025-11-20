@extends("admin.layouts.app")

@section("content")
<div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6 py-4 sm:py-6 lg:py-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6 sm:mb-8">
        <div class="flex-1 min-w-0">
            <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 truncate">Projects</h1>
            <p class="text-gray-600 mt-1 sm:mt-2 text-xs sm:text-sm lg:text-base truncate">Manage all projects in Kanban view</p>
        </div>
        <a href="{{ route('projects.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 sm:px-5 lg:px-6 py-2 sm:py-2.5 lg:py-3 rounded-lg font-medium transition duration-200 flex items-center justify-center shadow-sm w-full sm:w-auto text-sm sm:text-base">
            <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1 sm:mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Create New Project
        </a>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-3 sm:px-4 py-2 sm:py-3 rounded-lg mb-4 sm:mb-6 flex items-center text-sm sm:text-base">
            <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-3 sm:px-4 py-2 sm:py-3 rounded-lg mb-4 sm:mb-6 flex items-center text-sm sm:text-base">
            <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6 mb-6 sm:mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
            <div class="flex items-center">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-100 rounded-xl flex items-center justify-center mr-3 sm:mr-4">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
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
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-100 rounded-xl flex items-center justify-center mr-3 sm:mr-4">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
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
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-purple-100 rounded-xl flex items-center justify-center mr-3 sm:mr-4">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
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
                    <label for="category_filter" class="block text-xs sm:text-sm font-medium text-gray-700 mb-2 truncate">Filter by Category</label>
                    <select id="category_filter" class="category-filter w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white shadow-sm">
                        <option value="all">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div class="min-w-0">
                    <label for="status_filter" class="block text-xs sm:text-sm font-medium text-gray-700 mb-2 truncate">Filter by Status</label>
                    <select id="status_filter" class="status-filter w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white shadow-sm">
                        <option value="all">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Planning</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>

                <!-- Reset Filters -->
                <div class="min-w-0 flex items-end">
                    <button id="reset_filters" class="w-full lg:w-auto px-4 sm:px-6 py-2 sm:py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition duration-200 font-medium flex items-center justify-center text-sm sm:text-base bg-white shadow-sm">
                        <svg class="w-4 h-4 sm:w-4 sm:h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Reset Filters
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Professional Mobile Filter Tabs -->
    <div class="lg:hidden mb-4 sm:mb-6">
        <div class="flex space-x-1 bg-white p-1 rounded-lg sm:rounded-xl shadow-sm border border-gray-200 overflow-x-auto">
            <button type="button" data-category="planning" class="file-filter-tab active flex-1 min-w-0 px-3 sm:px-4 py-2 sm:py-3 text-xs sm:text-sm font-medium rounded-lg bg-blue-100 text-blue-800 whitespace-nowrap transition-all duration-200">
                Planning ({{ $projects->where('status', 'pending')->count() }})
            </button>
            <button type="button" data-category="progress" class="file-filter-tab flex-1 min-w-0 px-3 sm:px-4 py-2 sm:py-3 text-xs sm:text-sm font-medium rounded-lg text-gray-600 hover:bg-gray-100 whitespace-nowrap transition-all duration-200">
                In Progress ({{ $projects->where('status', 'in_progress')->count() }})
            </button>
            <button type="button" data-category="completed" class="file-filter-tab flex-1 min-w-0 px-3 sm:px-4 py-2 sm:py-3 text-xs sm:text-sm font-medium rounded-lg text-gray-600 hover:bg-gray-100 whitespace-nowrap transition-all duration-200">
                Completed ({{ $projects->where('status', 'completed')->count() }})
            </button>
        </div>
    </div>

    <!-- Kanban Board -->
    @if($projects->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-5 lg:gap-6 mb-6 sm:mb-8">
            <!-- Planning Column -->
            <div data-category="planning" class="file-column bg-gray-50 rounded-xl p-4 sm:p-5 lg:p-6">
                <div class="flex items-center justify-between mb-4 sm:mb-5 lg:mb-6">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900 flex items-center min-w-0">
                        <div class="w-2 h-2 sm:w-2.5 sm:h-2.5 lg:w-3 lg:h-3 bg-gray-400 rounded-full mr-2 sm:mr-3 flex-shrink-0"></div>
                        <span class="truncate">Planning</span>
                    </h3>
                    <span class="bg-gray-200 text-gray-700 px-2 sm:px-2.5 lg:px-3 py-1 rounded-full text-xs font-medium flex-shrink-0 ml-2">
                        {{ $projects->where('status', 'pending')->count() }}
                    </span>
                </div>
                <div class="space-y-3 sm:space-y-4">
                    @foreach($projects->where('status', 'pending') as $project)
                        @include('projects.partials.project-card', ['project' => $project, 'status' => 'pending'])
                    @endforeach
                </div>
            </div>

            <!-- In Progress Column -->
            <div data-category="progress" class="file-column bg-gray-50 rounded-xl p-4 sm:p-5 lg:p-6">
                <div class="flex items-center justify-between mb-4 sm:mb-5 lg:mb-6">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900 flex items-center min-w-0">
                        <div class="w-2 h-2 sm:w-2.5 sm:h-2.5 lg:w-3 lg:h-3 bg-yellow-400 rounded-full mr-2 sm:mr-3 flex-shrink-0"></div>
                        <span class="truncate">In Progress</span>
                    </h3>
                    <span class="bg-yellow-100 text-yellow-800 px-2 sm:px-2.5 lg:px-3 py-1 rounded-full text-xs font-medium flex-shrink-0 ml-2">
                        {{ $projects->where('status', 'in_progress')->count() }}
                    </span>
                </div>
                <div class="space-y-3 sm:space-y-4">
                    @foreach($projects->where('status', 'in_progress') as $project)
                        @include('projects.partials.project-card', ['project' => $project, 'status' => 'in_progress'])
                    @endforeach
                </div>
            </div>

            <!-- Completed Column -->
            <div data-category="completed" class="file-column bg-gray-50 rounded-xl p-4 sm:p-5 lg:p-6">
                <div class="flex items-center justify-between mb-4 sm:mb-5 lg:mb-6">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900 flex items-center min-w-0">
                        <div class="w-2 h-2 sm:w-2.5 sm:h-2.5 lg:w-3 lg:h-3 bg-green-400 rounded-full mr-2 sm:mr-3 flex-shrink-0"></div>
                        <span class="truncate">Completed</span>
                    </h3>
                    <span class="bg-green-100 text-green-800 px-2 sm:px-2.5 lg:px-3 py-1 rounded-full text-xs font-medium flex-shrink-0 ml-2">
                        {{ $projects->where('status', 'completed')->count() }}
                    </span>
                </div>
                <div class="space-y-3 sm:space-y-4">
                    @foreach($projects->where('status', 'completed') as $project)
                        @include('projects.partials.project-card', ['project' => $project, 'status' => 'completed'])
                    @endforeach
                </div>
            </div>
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 text-center py-8 sm:py-10 lg:py-12 px-4">
            <svg class="w-12 h-12 sm:w-14 sm:h-14 lg:w-16 lg:h-16 mx-auto text-gray-400 mb-3 sm:mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="text-lg sm:text-xl font-medium text-gray-900 mb-1 sm:mb-2">No projects found</p>
            <p class="text-gray-600 mb-4 sm:mb-6 text-sm sm:text-base">
                @if(request()->has('category_id') || request()->has('status'))
                    No projects match your current filters. Try adjusting your search criteria.
                @else
                    Get started by creating your first project
                @endif
            </p>
            <a href="{{ route('projects.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 sm:px-5 lg:px-6 py-2 sm:py-2.5 lg:py-3 rounded-lg font-medium transition duration-200 inline-flex items-center justify-center text-sm sm:text-base">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1 sm:mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Create Your First Project
            </a>
        </div>
    @endif
</div>

<script>
// Professional file filter - clean and working
document.addEventListener('DOMContentLoaded', function() {
    const filterTabs = document.querySelectorAll('.file-filter-tab');
    const fileColumns = document.querySelectorAll('.file-column');
    const categoryFilter = document.getElementById('category_filter');
    const statusFilter = document.getElementById('status_filter');
    const resetFilters = document.getElementById('reset_filters');

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
.category-filter, .status-filter {
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
