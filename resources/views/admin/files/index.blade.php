@php
    $layout = match(Auth::user()->role) {
        'super_admin' => 'admin.layouts.app',
        'admin' => 'manager.layouts.app',
        'user' => 'team.app',
    };
@endphp

@extends($layout)
@section('content')

<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Files Management</h1>
            <p class="text-gray-600 mt-2">Manage and organize all uploaded files with version control</p>
        </div>
        <a href="{{ route('files.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-medium transition duration-200 flex items-center shadow-sm">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
            </svg>
            Upload New File
        </a>
    </div>

    <!-- Role-based Information -->
    @php
        $user = auth()->user();
    @endphp

    @if($user->role === 'admin')
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <p class="text-blue-800 font-medium">Manager Access</p>
                <p class="text-blue-600 text-sm">You can view and manage files from projects you manage.</p>
            </div>
        </div>
    </div>
    @elseif($user->role === 'user')
    <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <p class="text-green-800 font-medium">Team Member Access</p>
                <p class="text-green-600 text-sm">You can view files from projects where you have assigned tasks.</p>
            </div>
        </div>
    </div>
    @endif

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

    <!-- Professional Mobile Filter Tabs -->
    <div class="lg:hidden mb-6">
        <div class="flex space-x-1 bg-white p-1 rounded-xl shadow-sm border border-gray-200 overflow-x-auto">
            <button data-category="recent" class="file-filter-tab active flex-1 px-4 py-3 text-sm font-medium rounded-lg bg-blue-100 text-blue-800 whitespace-nowrap transition-all duration-200">
                Recent ({{ $files->where('created_at', '>=', now()->subDays(7))->count() }})
            </button>
            <button data-category="project" class="file-filter-tab flex-1 px-4 py-3 text-sm font-medium rounded-lg text-gray-600 hover:bg-gray-100 whitespace-nowrap transition-all duration-200">
                Project ({{ $files->whereNotNull('project_id')->count() }})
            </button>
            <button data-category="general" class="file-filter-tab flex-1 px-4 py-3 text-sm font-medium rounded-lg text-gray-600 hover:bg-gray-100 whitespace-nowrap transition-all duration-200">
                General ({{ $files->whereNull('project_id')->count() }})
            </button>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Total Files</p>
                    <p class="text-3xl font-bold mt-1">{{ $files->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Project Files</p>
                    <p class="text-3xl font-bold mt-1">{{ $files->whereNotNull('project_id')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">General Files</p>
                    <p class="text-3xl font-bold mt-1">{{ $files->whereNull('project_id')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-medium">Total Versions</p>
                    <p class="text-3xl font-bold mt-1">{{ $totalVersions }}</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Kanban Board -->
    @if($files->count() > 0)
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Recent Files Column -->
        <div class="file-column active bg-gradient-to-b from-blue-50 to-white rounded-2xl shadow-sm border border-blue-100 p-6" data-category="recent">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                    Recent Files
                </h3>
                <span class="bg-blue-500 text-white px-3 py-1 rounded-full text-sm font-medium">
                    {{ $files->where('created_at', '>=', now()->subDays(7))->count() }}
                </span>
            </div>
            <div class="space-y-4">
                @foreach($files->where('created_at', '>=', now()->subDays(7))->take(6) as $file)
                    @if($file->canUserAccess(auth()->id()))
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-all duration-200 hover:border-blue-300">
                        @include('admin.files.partials.file-card', ['file' => $file])
                    </div>
                    @endif
                @endforeach
            </div>
        </div>

        <!-- Project Files Column -->
        <div class="file-column bg-gradient-to-b from-green-50 to-white rounded-2xl shadow-sm border border-green-100 p-6" data-category="project">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                    Project Files
                </h3>
                <span class="bg-green-500 text-white px-3 py-1 rounded-full text-sm font-medium">
                    {{ $files->whereNotNull('project_id')->count() }}
                </span>
            </div>
            <div class="space-y-4">
                @foreach($files->whereNotNull('project_id')->take(6) as $file)
                    @if($file->canUserAccess(auth()->id()))
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-all duration-200 hover:border-green-300">
                        @include('admin.files.partials.file-card', ['file' => $file])
                    </div>
                    @endif
                @endforeach
            </div>
        </div>

        <!-- General Files Column -->
        <div class="file-column bg-gradient-to-b from-purple-50 to-white rounded-2xl shadow-sm border border-purple-100 p-6" data-category="general">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <div class="w-3 h-3 bg-purple-500 rounded-full mr-3"></div>
                    General Files
                </h3>
                <span class="bg-purple-500 text-white px-3 py-1 rounded-full text-sm font-medium">
                    {{ $files->whereNull('project_id')->count() }}
                </span>
            </div>
            <div class="space-y-4">
                @foreach($files->whereNull('project_id')->take(6) as $file)
                    @if($file->canUserAccess(auth()->id()))
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-all duration-200 hover:border-purple-300">
                        @include('admin.files.partials.file-card', ['file' => $file])
                    </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    <!-- All Files Grid -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 rounded-t-2xl">
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-medium text-gray-900">All Files ({{ $files->count() }})</h2>
                @if(auth()->user()->role === 'super_admin')
                <div class="text-sm text-gray-600">
                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Public: {{ $files->where('is_public', true)->count() }}</span>
                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs ml-2">Restricted: {{ $files->whereNotNull('accessible_users')->count() }}</span>
                </div>
                @endif
            </div>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($files as $file)
                    @if($file->canUserAccess(auth()->id()))
                    <div class="bg-gray-50 rounded-xl border border-gray-200 p-5 hover:shadow-md transition-all duration-200 hover:border-blue-300">
                        @include('admin.files.partials.file-card', ['file' => $file])
                    </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
    @else
        <!-- Empty State -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 text-center py-16">
            <div class="w-20 h-20 bg-gradient-to-br from-blue-100 to-blue-200 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-sm">
                <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-3">No files found</h3>
            <p class="text-gray-600 mb-8 max-w-md mx-auto">Get started by uploading your first file to the system.</p>
            <a href="{{ route('files.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-xl font-medium transition duration-200 inline-flex items-center shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                Upload Your First File
            </a>
        </div>
    @endif
</div>

<script>
// Professional file filter - clean and working
document.addEventListener('DOMContentLoaded', function() {
    const filterTabs = document.querySelectorAll('.file-filter-tab');
    const fileColumns = document.querySelectorAll('.file-column');

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
@media (max-width: 1023px) {
    .file-column {
        display: none;
    }
    .file-column:first-child {
        display: block;
    }
}

.file-filter-tab.active {
    background-color: rgb(219, 234, 254);
    color: rgb(29, 78, 216);
}

.file-filter-tab {
    transition: all 0.2s ease-in-out;
}

.hover\:shadow-md {
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}
.shadow-sm {
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
}
.rounded-2xl {
    border-radius: 1rem;
}
.rounded-xl {
    border-radius: 0.75rem;
}

/* Smooth gradients */
.bg-gradient-to-br {
    background-image: linear-gradient(135deg, var(--tw-gradient-from), var(--tw-gradient-to));
}

/* Backdrop blur for modern look */
.backdrop-blur-sm {
    backdrop-filter: blur(4px);
}
</style>
@endsection
