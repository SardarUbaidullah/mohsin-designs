@extends('admin.layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">User Details</h1>
                    <p class="mt-2 text-sm text-gray-600">View and manage user information</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('users.edit', $user->id) }}"
                       class="inline-flex items-center px-4 py-2 bg-primary border border-transparent rounded-lg font-semibold text-white hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all duration-200 shadow-sm">
                        <i class="fas fa-edit mr-2"></i>
                        Edit User
                    </a>
                    <a href="{{ route('users.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-lg font-semibold text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200 shadow-sm">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Users
                    </a>
                </div>
            </div>
        </div>

        @php
            // Calculate stats
            $projectsCount = \App\Models\Projects::where('manager_id', $user->id)->count();
            $tasksCount = \App\Models\Tasks::where('assigned_to', $user->id)->count();
            $milestonesCount = \App\Models\Milestones::whereHas('project', function($query) use ($user) {
                $query->where('manager_id', $user->id);
            })->count();
            $filesCount = \App\Models\Files::where('user_id', $user->id)->count();

            // Get user's projects
            $userProjects = \App\Models\Projects::where('manager_id', $user->id)
                ->withCount(['tasks', 'tasks as active_tasks_count' => function($query) {
                    $query->whereIn('status', ['todo', 'in_progress']);
                }])
                ->latest()
                ->get();

            // Get user's tasks
            $userTasks = \App\Models\Tasks::where('assigned_to', $user->id)
                ->with(['project', 'milestone'])
                ->latest()
                ->get();

            // Get user's milestones
            $userMilestones = \App\Models\Milestones::whereHas('project', function($query) use ($user) {
                $query->where('manager_id', $user->id);
            })->with('project')->latest()->get();

            // Get user's files
            $userFiles = \App\Models\Files::where('user_id', $user->id)
                ->with('project')
                ->latest()
                ->get();
        @endphp

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- User Profile Card -->
                <div class="bg-white shadow-lg rounded-xl border border-gray-200 overflow-hidden mb-6">
                    <div class="p-6">
                        <div class="text-center">
                            <div class="w-24 h-24 bg-gradient-to-br from-primary to-blue-600 rounded-full flex items-center justify-center text-white font-bold text-3xl mx-auto shadow-lg mb-4">
                                <img src="{{$user->profile_photo_url }}" alt="">
                            </div>
                            <h3 class="text-xl font-bold text-gray-900">{{ $user->name }}</h3>
                            <p class="text-sm text-gray-500 mt-1">{{ $user->email }}</p>

                            <div class="mt-4 flex justify-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    {{ $user->role == 'super_admin' ? 'bg-purple-100 text-purple-800' :
                                       ($user->role == 'admin' ? 'bg-red-100 text-red-800' :
                                       ($user->role == 'manager' ? 'bg-blue-100 text-blue-800' :
                                       ($user->role == 'client' ? 'bg-green-100 text-green-800' :
                                       'bg-gray-100 text-gray-800'))) }}">
                                    <i class="fas fa-user-shield mr-1"></i>
                                    {{ ucfirst($user->role) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="bg-white shadow-lg rounded-xl border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Quick Stats</h3>
                    </div>
                    <div class="p-4">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-project-diagram text-white text-sm"></i>
                                    </div>
                                    <span class="font-medium text-gray-700">Projects</span>
                                </div>
                                <span class="text-lg font-bold text-blue-600">{{ $projectsCount }}</span>
                            </div>
                            
                            <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-tasks text-white text-sm"></i>
                                    </div>
                                    <span class="font-medium text-gray-700">Tasks</span>
                                </div>
                                <span class="text-lg font-bold text-green-600">{{ $tasksCount }}</span>
                            </div>
                            
                            <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-flag text-white text-sm"></i>
                                    </div>
                                    <span class="font-medium text-gray-700">Milestones</span>
                                </div>
                                <span class="text-lg font-bold text-purple-600">{{ $milestonesCount }}</span>
                            </div>

                            <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-file-upload text-white text-sm"></i>
                                    </div>
                                    <span class="font-medium text-gray-700">Files</span>
                                </div>
                                <span class="text-lg font-bold text-orange-600">{{ $filesCount }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-3">
                <div class="bg-white shadow-lg rounded-xl border border-gray-200 overflow-hidden">
                    <!-- Tab Navigation -->
                    <div class="border-b border-gray-200">
                        <nav class="flex space-x-8 px-6" aria-label="Tabs">
                            <button 
                                data-tab="personal"
                                class="tab-button py-4 px-1 border-b-2 font-medium text-sm transition-all duration-200 flex items-center space-x-2
                                border-primary text-primary">
                                <i class="fas fa-user-circle"></i>
                                <span>Personal Details</span>
                            </button>
                            <button 
                                data-tab="projects"
                                class="tab-button py-4 px-1 border-b-2 font-medium text-sm transition-all duration-200 flex items-center space-x-2
                                border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                                <i class="fas fa-project-diagram"></i>
                                <span>Managed Projects ({{ $projectsCount }})</span>
                            </button>
                            <button 
                                data-tab="tasks"
                                class="tab-button py-4 px-1 border-b-2 font-medium text-sm transition-all duration-200 flex items-center space-x-2
                                border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                                <i class="fas fa-tasks"></i>
                                <span>Assigned Tasks ({{ $tasksCount }})</span>
                            </button>
                            <button 
                                data-tab="milestones"
                                class="tab-button py-4 px-1 border-b-2 font-medium text-sm transition-all duration-200 flex items-center space-x-2
                                border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                                <i class="fas fa-flag"></i>
                                <span>Milestones ({{ $milestonesCount }})</span>
                            </button>
                            <button 
                                data-tab="files"
                                class="tab-button py-4 px-1 border-b-2 font-medium text-sm transition-all duration-200 flex items-center space-x-2
                                border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                                <i class="fas fa-file-upload"></i>
                                <span>Uploaded Files ({{ $filesCount }})</span>
                            </button>
                        </nav>
                    </div>

                    <!-- Tab Content -->
                    <div class="p-6">
                        <!-- Personal Details Tab -->
                        <div id="personal-tab" class="tab-content active">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Full Name</label>
                                    <p class="text-lg font-semibold text-gray-900">{{ $user->name }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Email Address</label>
                                    <p class="text-lg font-semibold text-gray-900">{{ $user->email }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Role</label>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                        {{ $user->role == 'super_admin' ? 'bg-purple-100 text-purple-800' :
                                           ($user->role == 'admin' ? 'bg-red-100 text-red-800' :
                                           ($user->role == 'manager' ? 'bg-blue-100 text-blue-800' :
                                           ($user->role == 'client' ? 'bg-green-100 text-green-800' :
                                           'bg-gray-100 text-gray-800'))) }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                        {{ $user->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ucfirst($user->status ?? 'active') }}
                                    </span>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Member Since</label>
                                    <p class="text-lg font-semibold text-gray-900">{{ $user->created_at->format('F d, Y') }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Last Updated</label>
                                    <p class="text-lg font-semibold text-gray-900">{{ $user->updated_at->format('F d, Y') }}</p>
                                </div>
                            </div>

                            @if($user->phone)
                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500 mb-1">Phone Number</label>
                                        <p class="text-lg font-semibold text-gray-900">{{ $user->phone }}</p>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Projects Tab -->
                        <div id="projects-tab" class="tab-content hidden">
                            <div class="space-y-4">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Managed Projects ({{ $projectsCount }})</h3>
                                
                                @if($userProjects->count() > 0)
                                <!-- Project Cards -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @foreach($userProjects as $project)
                                    <div class="border border-gray-200 rounded-lg p-4 hover:border-primary transition-colors duration-200">
                                        <div class="flex items-start justify-between mb-3">
                                            <h4 class="font-semibold text-gray-900">{{ $project->name }}</h4>
                                            <span class="px-2 py-1 text-xs rounded-full
                                                {{ $project->status == 'completed' ? 'bg-green-100 text-green-800' :
                                                   ($project->status == 'in_progress' ? 'bg-blue-100 text-blue-800' :
                                                   'bg-gray-100 text-gray-800') }}">
                                                {{ ucfirst($project->status) }}
                                            </span>
                                        </div>
                                        <p class="text-gray-600 text-sm mb-3">{{ Str::limit($project->description, 100) }}</p>
                                        <div class="flex items-center justify-between text-sm text-gray-500">
                                            <span>Due: {{ $project->due_date ? \Carbon\Carbon::parse($project->due_date)->format('M d, Y') : 'Not set' }}</span>
                                            <span>{{ $project->active_tasks_count }}/{{ $project->tasks_count }} Active</span>
                                        </div>
                                        @if($project->tasks_count > 0)
                                        <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                                            @php
                                                $completionPercentage = $project->tasks_count > 0 ? 
                                                    (($project->tasks_count - $project->active_tasks_count) / $project->tasks_count) * 100 : 0;
                                            @endphp
                                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $completionPercentage }}%"></div>
                                        </div>
                                        @endif
                                    </div>
                                    @endforeach
                                </div>
                                @else
                                <div class="text-center py-8">
                                    <i class="fas fa-project-diagram text-gray-400 text-4xl mb-3"></i>
                                    <p class="text-gray-500">No projects managed by this user.</p>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Tasks Tab -->
                        <div id="tasks-tab" class="tab-content hidden">
                            <div class="space-y-4">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Assigned Tasks ({{ $tasksCount }})</h3>
                                
                                @if($userTasks->count() > 0)
                                <!-- Task List -->
                                <div class="space-y-3">
                                    @foreach($userTasks as $task)
                                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:border-primary transition-colors duration-200">
                                        <div class="flex items-center space-x-3">
                                            <input type="checkbox" 
                                                   class="rounded border-gray-300 text-primary focus:ring-primary"
                                                   {{ $task->status == 'done' ? 'checked' : '' }}
                                                   disabled>
                                            <div>
                                                <h4 class="font-medium text-gray-900">{{ $task->title }}</h4>
                                                <p class="text-sm text-gray-500">{{ $task->project->name ?? 'No Project' }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-4">
                                            <span class="px-2 py-1 text-xs rounded-full
                                                {{ $task->status == 'done' ? 'bg-green-100 text-green-800' :
                                                   ($task->status == 'in_progress' ? 'bg-yellow-100 text-yellow-800' :
                                                   'bg-gray-100 text-gray-800') }}">
                                                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                            </span>
                                            <span class="text-sm text-gray-500">
                                                Due: {{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('M d, Y') : 'Not set' }}
                                            </span>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @else
                                <div class="text-center py-8">
                                    <i class="fas fa-tasks text-gray-400 text-4xl mb-3"></i>
                                    <p class="text-gray-500">No tasks assigned to this user.</p>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Milestones Tab -->
                        <div id="milestones-tab" class="tab-content hidden">
                            <div class="space-y-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Project Milestones ({{ $milestonesCount }})</h3>
                                
                                @if($userMilestones->count() > 0)
                                <!-- Milestone Timeline -->
                                <div class="space-y-4">
                                    @foreach($userMilestones as $milestone)
                                    <div class="flex items-start space-x-4 p-4 border border-gray-200 rounded-lg hover:border-primary transition-colors duration-200">
                                        <div class="w-10 h-10 
                                            {{ $milestone->status == 'completed' ? 'bg-green-500' :
                                               ($milestone->status == 'in_progress' ? 'bg-blue-500' :
                                               'bg-purple-500') }} rounded-full flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-flag text-white text-sm"></i>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-gray-900">{{ $milestone->title }}</h4>
                                            <p class="text-gray-600 text-sm mb-2">{{ Str::limit($milestone->description, 150) }}</p>
                                            <div class="flex items-center space-x-4 text-sm text-gray-500">
                                                <span>Project: {{ $milestone->project->name }}</span>
                                                <span>Due: {{ $milestone->due_date ? \Carbon\Carbon::parse($milestone->due_date)->format('M d, Y') : 'Not set' }}</span>
                                            </div>
                                        </div>
                                        <span class="px-2 py-1 text-xs rounded-full
                                            {{ $milestone->status == 'completed' ? 'bg-green-100 text-green-800' :
                                               ($milestone->status == 'in_progress' ? 'bg-yellow-100 text-yellow-800' :
                                               'bg-gray-100 text-gray-800') }}">
                                            {{ ucfirst(str_replace('_', ' ', $milestone->status)) }}
                                        </span>
                                    </div>
                                    @endforeach
                                </div>
                                @else
                                <div class="text-center py-8">
                                    <i class="fas fa-flag text-gray-400 text-4xl mb-3"></i>
                                    <p class="text-gray-500">No milestones found for this user's projects.</p>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Files Tab -->
                        <div id="files-tab" class="tab-content hidden">
                            <div class="space-y-6">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-lg font-semibold text-gray-900">Uploaded Files ({{ $filesCount }})</h3>
                                </div>
                                
                                @if($userFiles->count() > 0)
                                <!-- Files Grid -->
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach($userFiles as $file)
                                    @php
                                        $fileExtension = pathinfo($file->file_name, PATHINFO_EXTENSION);
                                        $fileTypeColor = in_array($fileExtension, ['pdf']) ? 'red' :
                                                        (in_array($fileExtension, ['doc', 'docx']) ? 'blue' :
                                                        (in_array($fileExtension, ['xls', 'xlsx']) ? 'green' :
                                                        (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif']) ? 'purple' : 'gray')));
                                        $fileTypeIcon = in_array($fileExtension, ['pdf']) ? 'file-pdf' :
                                                      (in_array($fileExtension, ['doc', 'docx']) ? 'file-word' :
                                                      (in_array($fileExtension, ['xls', 'xlsx']) ? 'file-excel' :
                                                      (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif']) ? 'file-image' : 'file')));
                                    @endphp
                                    <div class="border border-gray-200 rounded-lg p-4 hover:border-primary transition-colors duration-200">
                                        <div class="flex items-center space-x-3 mb-3">
                                            <div class="w-12 h-12 bg-{{ $fileTypeColor }}-100 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-{{ $fileTypeIcon }} text-{{ $fileTypeColor }}-600"></i>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <h4 class="font-medium text-gray-900 truncate">{{ $file->file_name }}</h4>
                                                <p class="text-sm text-gray-500">
                                                    @if(\Illuminate\Support\Facades\Storage::disk('public')->exists($file->file_path))
                                                        {{ number_format(\Illuminate\Support\Facades\Storage::disk('public')->size($file->file_path) / 1024, 1) }} KB
                                                    @else
                                                        Size unknown
                                                    @endif
                                                </p>
                                                <p class="text-xs text-gray-400">{{ $file->project->name ?? 'No Project' }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center justify-between text-sm text-gray-500">
                                            <span>{{ $file->created_at->format('M d, Y') }}</span>
                                            <a href="{{ route('files.download', $file->id) }}" class="text-primary hover:text-primary/80">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @else
                                <div class="text-center py-8">
                                    <i class="fas fa-file-upload text-gray-400 text-4xl mb-3"></i>
                                    <p class="text-gray-500">No files uploaded by this user.</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');
            
            // Update tab buttons
            tabButtons.forEach(btn => {
                btn.classList.remove('border-primary', 'text-primary');
                btn.classList.add('border-transparent', 'text-gray-500');
            });
            
            this.classList.remove('border-transparent', 'text-gray-500');
            this.classList.add('border-primary', 'text-primary');
            
            // Update tab contents
            tabContents.forEach(content => {
                content.classList.remove('active');
                content.classList.add('hidden');
            });
            
            const targetContent = document.getElementById(`${targetTab}-tab`);
            targetContent.classList.remove('hidden');
            targetContent.classList.add('active');
        });
    });
});
</script>
@endsection