<!-- Manager Sidebar -->
<div class="sidebar w-64 flex flex-col h-full bg-white border-r border-gray-200">

    <!-- Scrollable -->
    <div class="flex-1 overflow-y-auto">
        <div class="p-6">
            <!-- Logo -->
            <div class="flex items-center space-x-3 mb-8">
                <div class="w-10 h-10 bg-gradient-to-br from-green-600 to-emerald-500 rounded-lg flex items-center justify-center shadow-sm">
                    <span class="text-white font-bold text-sm">M</span>
                </div>
                <div>
                    <span class="font-bold text-gray-900 text-lg block">Manager</span>
                    <span class="text-gray-500 text-sm">Panel</span>
                </div>
            </div>
            <!-- Menu -->
            <nav class="space-y-2">

                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}"
                   class="flex items-center space-x-3 px-3 py-3 rounded-lg text-sm font-medium bg-green-600 text-white border-l-4 border-green-700 transition-colors duration-200">
                    <i class="fas fa-home w-5 text-center"></i>
                    <span>Dashboard</span>
                </a>

                <!-- My Projects -->
                <a href="{{ route('manager.projects.index') }}"
                   class="flex items-center justify-between w-full px-3 py-3 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-briefcase w-5 text-center text-blue-500"></i>
                        <span>My Projects</span>
                    </div>
                    <span class="bg-blue-100 text-blue-600 text-xs px-2 py-1 rounded-full font-medium">
                        {{ \App\Models\Projects::where('manager_id', auth()->id())->count() }}
                    </span>
                </a>

                <!-- Tasks -->
                <a href="{{ route('manager.tasks.index') }}"
                   class="flex items-center justify-between w-full px-3 py-3 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-tasks w-5 text-center text-orange-500"></i>
                        <span>Tasks</span>
                    </div>
                    <span class="bg-orange-100 text-orange-600 text-xs px-2 py-1 rounded-full font-medium">
                        {{ \App\Models\Tasks::whereHas('project', function($q) {
                            $q->where('manager_id', auth()->id());
                        })->count() }}
                    </span>
                </a>
{{-- my tasks --}}
                 <a href="{{ route('team.tasks.index') }}"
                   class="flex items-center justify-between w-full px-3 py-3 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-person w-5 text-center text-orange-500"></i>
                        <span>My Tasks</span>
                    </div>
                    <span class="bg-orange-100 text-orange-600 text-xs px-2 py-1 rounded-full font-medium">
                          {{ \App\Models\Tasks::where('assigned_to', auth()->id())->count() }}
                    </span>
                </a>

                <!-- Files -->
<a href="/files"
   class="flex items-center justify-between w-full px-3 py-3 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors duration-200">
    <div class="flex items-center space-x-3">
        <i class="fas fa-folder w-5 text-center text-blue-500"></i>
        <span>Files</span>
    </div>
    <span class="bg-blue-100 text-blue-600 text-xs px-2 py-1 rounded-full font-medium">
        {{ \App\Models\Files::whereHas('project', function($q) {
            $q->where('manager_id', auth()->id());
        })->count() }}
    </span>
</a>
                <!-- Team -->
                <a href="{{ route('manager.team.index') }}"
                   class="flex items-center justify-between w-full px-3 py-3 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-users w-5 text-center text-green-500"></i>
                        <span>My Team</span>
                    </div>
                    <span class="bg-green-100 text-green-600 text-xs px-2 py-1 rounded-full font-medium">
                        {{ \App\Models\User::whereHas('assignedTasks', function($q) {
                            $q->whereHas('project', function($q2) {
                                $q2->where('manager_id', auth()->id());
                            });
                        })->distinct()->count() }}
                    </span>
                </a>

                <!-- Milestones -->
                <a href="{{ route('manager.milestones.index') }}"
                   class="flex items-center justify-between w-full px-3 py-3 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-flag-checkered w-5 text-center text-purple-500"></i>
                        <span>Milestones</span>
                    </div>
                    <span class="bg-purple-100 text-purple-600 text-xs px-2 py-1 rounded-full font-medium">
                        {{ \App\Models\Milestones::whereHas('project', function($query) {
                            $query->where('manager_id', auth()->id());
                        })->count() }}
                    </span>
                </a>

<a href="{{ url('/chat') }}"
   class="flex items-center justify-between w-full px-3 py-3 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors duration-200">
    <div class="flex items-center space-x-3">
        <i class="fas fa-comment w-5 text-center text-blue-500"></i>
        <span>Chats</span>
    </div>

</a>

<a href="{{ url('/calendar') }}"
   class="flex items-center justify-between w-full px-3 py-3 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors duration-200">
    <div class="flex items-center space-x-3">
        <i class="fas fa-calendar w-5 text-center text-green-500"></i>
        <span>Calendar</span>
    </div>

</a>

                <!-- Profile -->
                <a href="{{ route('profile.edit') }}"
                   class="flex items-center justify-between w-full px-3 py-3 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-user-cog w-5 text-center text-gray-500"></i>
                        <span>My Account</span>
                    </div>
                    <span class="bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded-full font-medium">
                        <i class="fas fa-cog"></i>
                    </span>
                </a>

            </nav>
        </div>
    </div>

    <!-- Profile Footer -->
     <div class="p-6 border-t border-slate-200/40 bg-white/50">
                <div class="flex items-center space-x-3 p-3 rounded-2xl bg-white/80 border border-slate-200/60 shadow-sm hover:shadow-md transition-all duration-300">
                    <div class="relative">
                        <img class="w-12 h-12 rounded-xl object-cover border-2 border-white shadow-md"
                             src="{{ Auth::user()->profile_photo_url }}"
                             alt="{{ auth()->user()->name }}">
                        <div class="absolute -bottom-1 -right-1 w-3 h-3 bg-emerald-400 rounded-full border-2 border-white"></div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-800 truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-slate-500 capitalize">{{ auth()->user()->role }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-red-500 hover:text-white flex items-center justify-center transition-all duration-300 group">
                            <i class="fas fa-right-from-bracket text-slate-400 group-hover:text-white text-xs"></i>
                        </button>
                    </form>
                </div>
            </div>
</div>
