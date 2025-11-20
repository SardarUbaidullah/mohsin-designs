<div class="sidebar w-64 flex flex-col h-full bg-gradient-to-b from-sidebar-background to-sidebar-background/95 border-r border-sidebar-border/20 shadow-xl"
    x-data="{ openDropdown: null }">

    <!-- Scrollable content -->
    <div class="flex-1 overflow-y-auto custom-scrollbar">
        <div class="p-6">

            <!-- Logo -->
            <div class="flex items-center justify-between mb-8 pb-4 border-b border-gray-200">
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-800">Admin Dashboard</h3>
                    <p class="text-sm text-gray-500 mt-1">Welcome to your admin panel</p>
                </div>
            </div>

            <!-- Nav Menu -->
            <nav class="space-y-2">
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}"
                    class="flex items-center space-x-4 px-4 py-3.5 rounded-xl text-sm font-semibold transition-all duration-300 group relative overflow-hidden text-sidebar-foreground hover:bg-sidebar-accent/80 hover:text-sidebar-foreground">

                    <!-- Icon -->
                    <div
                        class="w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-300 group-hover:scale-110 bg-sidebar-accent text-muted-foreground group-hover:text-sidebar-foreground group-hover:bg-primary/10">
                        <i class="fas fa-home text-sm"></i>
                    </div>

                    <span class="flex-1">Dashboard</span>
                </a>

                <!-- Messages -->
                <a href="{{ url('/chat') }}"
                    class="flex items-center space-x-4 px-4 py-3.5 rounded-xl text-sm font-semibold transition-all duration-300 group relative overflow-hidden text-sidebar-foreground hover:bg-sidebar-accent/80 hover:text-sidebar-foreground">

                    <div
                        class="w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-300 group-hover:scale-110 bg-sidebar-accent text-muted-foreground group-hover:text-sidebar-foreground group-hover:bg-primary/10">
                        <i class="fas fa-comment text-sm"></i>
                    </div>

                    <span class="flex-1">Messages</span>

                    <!-- Message count badge -->
                    <span class="bg-red-500 text-white text-xs p-1 rounded-full min-w-[20px] text-center shadow-sm">Chat</span>
                </a>

                <!-- Tasks Dropdown -->
                <div class="group">
                    <button @click="openDropdown = openDropdown === 'tasks' ? null : 'tasks'"
                        :class="openDropdown === 'tasks' ?
                            'bg-gradient-to-r from-primary/10 to-primary/5 text-sidebar-foreground border-l-2 border-primary' :
                            'text-sidebar-foreground hover:bg-sidebar-accent/80'"
                        class="flex items-center justify-between w-full px-4 py-3.5 rounded-xl text-sm font-semibold transition-all duration-300 relative overflow-hidden">

                        <div class="flex items-center space-x-4">
                            <div :class="openDropdown === 'tasks' ?
                                'bg-primary/20 text-primary shadow-sm' :
                                'bg-sidebar-accent text-muted-foreground group-hover:text-sidebar-foreground group-hover:bg-primary/10'"
                                class="w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-300 group-hover:scale-110">
                                <i class="fas fa-tasks text-sm"></i>
                            </div>
                            <span>Tasks</span>
                        </div>

                        <div class="flex items-center space-x-2">
                            <!-- Task count -->
                            <span class="bg-primary/20 text-primary text-xs px-2 py-1 rounded-full font-medium shadow-sm">
                               {{ \App\Models\Tasks::count() }}
                            </span>
                            <!-- Animated chevron -->
                            <i :class="openDropdown === 'tasks' ?
                                'fas fa-chevron-up text-primary transform transition-transform duration-300' :
                                'fas fa-chevron-down text-muted-foreground group-hover:text-sidebar-foreground transform transition-transform duration-300'"
                                class="text-xs"></i>
                        </div>
                    </button>

                    <!-- Dropdown Content -->
                    <div x-show="openDropdown === 'tasks'" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform -translate-y-2"
                        x-transition:enter-end="opacity-100 transform translate-y-0"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 transform translate-y-0"
                        x-transition:leave-end="opacity-0 transform -translate-y-2"
                        class="ml-4 mt-2 space-y-1.5 border-l-2 border-primary/20 pl-4 py-2">
                        <a href="{{ url('/tasks/create') }}"
                            class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm text-muted-foreground hover:text-sidebar-foreground hover:bg-white/60 transition-all duration-200 group/item">
                            <div
                                class="w-8 h-8 bg-green-100 text-green-600 rounded-lg flex items-center justify-center transition-all duration-200 group-hover/item:scale-110 group-hover/item:bg-green-500 group-hover/item:text-white">
                                <i class="fas fa-plus text-xs"></i>
                            </div>
                            <span>Create Task</span>
                        </a>
                        <a href="{{ url('/tasks') }}"
                            class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm text-muted-foreground hover:text-sidebar-foreground hover:bg-white/60 transition-all duration-200 group/item">
                            <div
                                class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center transition-all duration-200 group-hover/item:scale-110 group-hover/item:bg-blue-500 group-hover/item:text-white">
                                <i class="fas fa-list text-xs"></i>
                            </div>
                            <span>Manage Tasks</span>
                        </a>

                         <a href="{{ route('team.tasks.index') }}"
                            class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm text-muted-foreground hover:text-sidebar-foreground hover:bg-white/60 transition-all duration-200 group/item">
                            <div
                                class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center transition-all duration-200 group-hover/item:scale-110 group-hover/item:bg-blue-500 group-hover/item:text-white">
                                <i class="fas fa-list text-xs"></i>
                            </div>
                            <span>My Tasks</span>
                        </a>
                    </div>
                </div>

                <!-- Users Dropdown -->
                <div class="group">
                    <button @click="openDropdown = openDropdown === 'users' ? null : 'users'"
                        :class="openDropdown === 'users' ?
                            'bg-gradient-to-r from-primary/10 to-primary/5 text-sidebar-foreground border-l-2 border-primary' :
                            'text-sidebar-foreground hover:bg-sidebar-accent/80'"
                        class="flex items-center justify-between w-full px-4 py-3.5 rounded-xl text-sm font-semibold transition-all duration-300 relative overflow-hidden">

                        <div class="flex items-center space-x-4">
                            <div :class="openDropdown === 'users' ?
                                'bg-primary/20 text-primary shadow-sm' :
                                'bg-sidebar-accent text-muted-foreground group-hover:text-sidebar-foreground group-hover:bg-primary/10'"
                                class="w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-300 group-hover:scale-110">
                                <i class="fas fa-users text-sm"></i>
                            </div>
                            <span>Users</span>
                        </div>

                        <div class="flex items-center space-x-2">
                            <span class="bg-green-500 text-white text-xs px-2 py-1 rounded-full font-medium shadow-sm">
{{ \App\Models\User::where('role', '!=', 'admin')->count() }}
                            </span>
                            <i :class="openDropdown === 'users' ?
                                'fas fa-chevron-up text-primary transform transition-transform duration-300' :
                                'fas fa-chevron-down text-muted-foreground group-hover:text-sidebar-foreground transform transition-transform duration-300'"
                                class="text-xs"></i>
                        </div>
                    </button>

                    <div x-show="openDropdown === 'users'" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform -translate-y-2"
                        x-transition:enter-end="opacity-100 transform translate-y-0"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 transform translate-y-0"
                        x-transition:leave-end="opacity-0 transform -translate-y-2"
                        class="ml-4 mt-2 space-y-1.5 border-l-2 border-primary/20 pl-4 py-2">
                        <a href="{{ url('/users') }}"
                            class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm text-muted-foreground hover:text-sidebar-foreground hover:bg-white/60 transition-all duration-200 group/item">
                            <div
                                class="w-8 h-8 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center transition-all duration-200 group-hover/item:scale-110 group-hover/item:bg-purple-500 group-hover/item:text-white">
                                <i class="fas fa-cog text-xs"></i>
                            </div>
                            <span>Manage Users</span>
                        </a>
                        <a href="{{ url('/users/create') }}"
                            class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm text-muted-foreground hover:text-sidebar-foreground hover:bg-white/60 transition-all duration-200 group/item">
                            <div
                                class="w-8 h-8 bg-indigo-100 text-indigo-600 rounded-lg flex items-center justify-center transition-all duration-200 group-hover/item:scale-110 group-hover/item:bg-indigo-500 group-hover/item:text-white">
                                <i class="fas fa-user-plus text-xs"></i>
                            </div>
                            <span>Add User</span>
                        </a>
                    </div>
                </div>

                <!-- Projects Dropdown -->
                <div class="group">
                    <button @click="openDropdown = openDropdown === 'projects' ? null : 'projects'"
                        :class="openDropdown === 'projects' ?
                            'bg-gradient-to-r from-primary/10 to-primary/5 text-sidebar-foreground border-l-2 border-primary' :
                            'text-sidebar-foreground hover:bg-sidebar-accent/80'"
                        class="flex items-center justify-between w-full px-4 py-3.5 rounded-xl text-sm font-semibold transition-all duration-300 relative overflow-hidden">

                        <div class="flex items-center space-x-4">
                            <div :class="openDropdown === 'projects' ?
                                'bg-primary/20 text-primary shadow-sm' :
                                'bg-sidebar-accent text-muted-foreground group-hover:text-sidebar-foreground group-hover:bg-primary/10'"
                                class="w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-300 group-hover:scale-110">
                                <i class="fas fa-briefcase text-sm"></i>
                            </div>
                            <span>Projects</span>
                        </div>

                        <div class="flex items-center space-x-2">
                            <span class="bg-orange-500 text-white text-xs px-2 py-1 rounded-full font-medium shadow-sm">
{{ \App\Models\Projects::count() }}                            </span>
                            <i :class="openDropdown === 'projects' ?
                                'fas fa-chevron-up text-primary transform transition-transform duration-300' :
                                'fas fa-chevron-down text-muted-foreground group-hover:text-sidebar-foreground transform transition-transform duration-300'"
                                class="text-xs"></i>
                        </div>
                    </button>

                    <div x-show="openDropdown === 'projects'" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform -translate-y-2"
                        x-transition:enter-end="opacity-100 transform translate-y-0"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 transform translate-y-0"
                        x-transition:leave-end="opacity-0 transform -translate-y-2"
                        class="ml-4 mt-2 space-y-1.5 border-l-2 border-primary/20 pl-4 py-2">
                        <a href="{{ url('/projects/create') }}"
                            class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm text-muted-foreground hover:text-sidebar-foreground hover:bg-white/60 transition-all duration-200 group/item">
                            <div
                                class="w-8 h-8 bg-yellow-100 text-yellow-600 rounded-lg flex items-center justify-center transition-all duration-200 group-hover/item:scale-110 group-hover/item:bg-yellow-500 group-hover/item:text-white">
                                <i class="fas fa-plus text-xs"></i>
                            </div>
                            <span>Create Project</span>
                        </a>
                        <a href="{{ url('/projects') }}"
                            class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm text-muted-foreground hover:text-sidebar-foreground hover:bg-white/60 transition-all duration-200 group/item">
                            <div
                                class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center transition-all duration-200 group-hover/item:scale-110 group-hover/item:bg-blue-500 group-hover/item:text-white">
                                <i class="fas fa-list text-xs"></i>
                            </div>
                            <span>Manage Projects</span>
                        </a>


                         <a href="{{ url('/manager/projects') }}"
                            class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm text-muted-foreground hover:text-sidebar-foreground hover:bg-white/60 transition-all duration-200 group/item">
                            <div
                                class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center transition-all duration-200 group-hover/item:scale-110 group-hover/item:bg-blue-900 group-hover/item:text-white">
                                <i class="fas fa-list text-xs"></i>
                            </div>
                            <span>My Projects</span>
                        </a>
                    </div>
                </div>

                <!-- Files Dropdown -->
                <div class="group">
                    <button @click="openDropdown = openDropdown === 'files' ? null : 'files'"
                        :class="openDropdown === 'files' ?
                            'bg-gradient-to-r from-primary/10 to-primary/5 text-sidebar-foreground border-l-2 border-primary' :
                            'text-sidebar-foreground hover:bg-sidebar-accent/80'"
                        class="flex items-center justify-between w-full px-4 py-3.5 rounded-xl text-sm font-semibold transition-all duration-300 relative overflow-hidden">

                        <div class="flex items-center space-x-4">
                            <div :class="openDropdown === 'files' ?
                                'bg-primary/20 text-primary shadow-sm' :
                                'bg-sidebar-accent text-muted-foreground group-hover:text-sidebar-foreground group-hover:bg-primary/10'"
                                class="w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-300 group-hover:scale-110">
                                <i class="fas fa-folder text-sm"></i>
                            </div>
                            <span>Files</span>
                        </div>

                        <div class="flex items-center space-x-2">
                            <span class="bg-cyan-500 text-white text-xs px-2 py-1 rounded-full font-medium shadow-sm">
                               {{ \App\Models\Files::count() }}
                            </span>
                            <i :class="openDropdown === 'files' ?
                                'fas fa-chevron-up text-primary transform transition-transform duration-300' :
                                'fas fa-chevron-down text-muted-foreground group-hover:text-sidebar-foreground transform transition-transform duration-300'"
                                class="text-xs"></i>
                        </div>
                    </button>

                    <div x-show="openDropdown === 'files'" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform -translate-y-2"
                        x-transition:enter-end="opacity-100 transform translate-y-0"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 transform translate-y-0"
                        x-transition:leave-end="opacity-0 transform -translate-y-2"
                        class="ml-4 mt-2 space-y-1.5 border-l-2 border-primary/20 pl-4 py-2">
                        <a href="{{ url('/files/create') }}"
                            class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm text-muted-foreground hover:text-sidebar-foreground hover:bg-white/60 transition-all duration-200 group/item">
                            <div
                                class="w-8 h-8 bg-cyan-100 text-cyan-600 rounded-lg flex items-center justify-center transition-all duration-200 group-hover/item:scale-110 group-hover/item:bg-cyan-500 group-hover/item:text-white">
                                <i class="fas fa-upload text-xs"></i>
                            </div>
                            <span>Upload File</span>
                        </a>
                        <a href="{{ url('/files') }}"
                            class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm text-muted-foreground hover:text-sidebar-foreground hover:bg-white/60 transition-all duration-200 group/item">
                            <div
                                class="w-8 h-8 bg-gray-100 text-gray-600 rounded-lg flex items-center justify-center transition-all duration-200 group-hover/item:scale-110 group-hover/item:bg-gray-500 group-hover/item:text-white">
                                <i class="fas fa-list text-xs"></i>
                            </div>
                            <span>Manage Files</span>
                        </a>
                    </div>
                </div>
<div class="group">
    <button @click="openDropdown = openDropdown === 'categories' ? null : 'categories'"
        :class="openDropdown === 'categories' ?
            'bg-gradient-to-r from-primary/10 to-primary/5 text-sidebar-foreground border-l-2 border-primary' :
            'text-sidebar-foreground hover:bg-sidebar-accent/80'"
        class="flex items-center justify-between w-full px-4 py-3.5 rounded-xl text-sm font-semibold transition-all duration-300 relative overflow-hidden">

        <div class="flex items-center space-x-4">
            <div :class="openDropdown === 'categories' ?
                'bg-primary/20 text-primary shadow-sm' :
                'bg-sidebar-accent text-muted-foreground group-hover:text-sidebar-foreground group-hover:bg-primary/10'"
                class="w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-300 group-hover:scale-110">
                <i class="fas fa-tags text-sm"></i>
            </div>
            <span>Categories</span>
        </div>

        <div class="flex items-center space-x-2">
            <span class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full font-medium shadow-sm">
                {{ \App\Models\Category::count() }}
            </span>
            <i :class="openDropdown === 'categories' ?
                'fas fa-chevron-up text-primary transform transition-transform duration-300' :
                'fas fa-chevron-down text-muted-foreground group-hover:text-sidebar-foreground transform transition-transform duration-300'"
                class="text-xs"></i>
        </div>
    </button>

    <div x-show="openDropdown === 'categories'" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform -translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform -translate-y-2"
        class="ml-4 mt-2 space-y-1.5 border-l-2 border-primary/20 pl-4 py-2">
        <a href="{{ route('categories.index') }}"
            class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm text-muted-foreground hover:text-sidebar-foreground hover:bg-white/60 transition-all duration-200 group/item">
            <div
                class="w-8 h-8 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center transition-all duration-200 group-hover/item:scale-110 group-hover/item:bg-purple-500 group-hover/item:text-white">
                <i class="fas fa-list text-xs"></i>
            </div>
            <span>Manage Categories</span>
        </a>
        <a href="{{ route('categories.create') }}"
            class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm text-muted-foreground hover:text-sidebar-foreground hover:bg-white/60 transition-all duration-200 group/item">
            <div
                class="w-8 h-8 bg-indigo-100 text-indigo-600 rounded-lg flex items-center justify-center transition-all duration-200 group-hover/item:scale-110 group-hover/item:bg-indigo-500 group-hover/item:text-white">
                <i class="fas fa-plus text-xs"></i>
            </div>
            <span>Add Category</span>
        </a>
    </div>
</div>


                <!-- TimeLogs Dropdown -->
                <a href="{{ route('admin.time-reports') }}"
                    class="flex items-center space-x-4 px-4 py-3.5 rounded-xl text-sm font-semibold transition-all duration-300 group relative overflow-hidden text-sidebar-foreground hover:bg-sidebar-accent/80 hover:text-sidebar-foreground">

                    <div
                        class="w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-300 group-hover:scale-110 bg-sidebar-accent text-muted-foreground group-hover:text-sidebar-foreground group-hover:bg-primary/10">
                        <i class="fas fa-clock text-sm"></i>
                    </div>

                    <span class="flex-1">TimeLogs</span>

    <span class="bg-pink-500 text-white text-xs px-2 py-1 rounded-full font-medium shadow-sm">
        {{ \App\Models\TimeLog::count() }}
    </span>
</a>
<a href="{{ route('milestones.index') }}"
   class="flex items-center space-x-4 px-4 py-3.5 rounded-xl text-sm font-semibold transition-all duration-300 group relative overflow-hidden text-sidebar-foreground hover:bg-sidebar-accent/80 hover:text-sidebar-foreground">

    <div
        class="w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-300 group-hover:scale-110 bg-sidebar-accent text-muted-foreground group-hover:text-sidebar-foreground group-hover:bg-primary/10">
        <i class="fas fa-flag-checkered text-sm"></i>
    </div>

    <span class="flex-1">Milestones</span>

    <span class="bg-purple-500 text-white text-xs px-2 py-1 rounded-full font-medium shadow-sm">
        {{ \App\Models\Milestones::count() }}
    </span>
</a>
<a href="{{ route('admin.reports') }}"
   class="flex items-center space-x-4 px-4 py-3.5 rounded-xl text-sm font-semibold transition-all duration-300 group relative overflow-hidden text-sidebar-foreground hover:bg-sidebar-accent/80 hover:text-sidebar-foreground {{ request()->routeIs('admin.reports*') ? 'bg-sidebar-accent text-sidebar-foreground' : '' }}">

    <div class="w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-300 group-hover:scale-110 {{ request()->routeIs('admin.reports*') ? 'bg-primary/20 text-primary' : 'bg-sidebar-accent text-muted-foreground' }} group-hover:text-sidebar-foreground group-hover:bg-primary/10">
        <i class="fas fa-chart-bar text-sm"></i>
    </div>

    <span class="flex-1">Analytics</span>

    <span class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full font-medium shadow-sm">
        Stats
    </span>
</a>
            </nav>
        </div>
    </div>

    <!-- User Profile -->
    <div class="p-6 border-t border-slate-200/40 bg-white/50">
        <div
            class="flex items-center space-x-3 p-3 rounded-2xl bg-white/80 border border-slate-200/60 shadow-sm hover:shadow-md transition-all duration-300">

            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-slate-800 truncate">{{ auth()->user()->name }}</p>
                <p class="text-xs text-slate-500 capitalize">{{ auth()->user()->role }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-red-500 hover:text-white flex items-center justify-center transition-all duration-300 group">
                    <i class="fas fa-right-from-bracket text-slate-400 group-hover:text-white text-xs"></i>
                </button>
            </form>
        </div>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
        border-radius: 3px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: linear-gradient(to bottom, #cbd5e1, #94a3b8);
        border-radius: 3px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(to bottom, #94a3b8, #64748b);
    }

    /* Smooth transitions for Alpine.js */
    [x-cloak] {
        display: none !important;
    }

    /* Ensure smooth dropdown animations */
    .transition {
        transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 150ms;
    }
</style>
