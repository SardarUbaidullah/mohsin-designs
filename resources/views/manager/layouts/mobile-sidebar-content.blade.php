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

    <!-- Chats -->
    <a href="{{ url('/chat') }}"
       class="flex items-center justify-between w-full px-3 py-3 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors duration-200">
        <div class="flex items-center space-x-3">
            <i class="fas fa-comment w-5 text-center text-blue-500"></i>
            <span>Chats</span>
        </div>
    </a>

    <!-- Calendar -->
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
