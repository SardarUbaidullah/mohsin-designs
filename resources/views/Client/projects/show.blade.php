@extends('Client.app')

@section('title', $project->name ?? 'Project Details')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- Enhanced Project Header -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 mb-8 overflow-hidden">
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-8 py-6 border-b border-gray-200">
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between">
                <div class="flex-1">
                    <div class="flex items-center space-x-4 mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                            <i class="fas fa-folder-open text-white text-lg"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">{{ $project->name ?? 'Unnamed Project' }}</h1>
                            <p class="text-gray-600 mt-1 text-lg leading-relaxed">{{ $project->description ?? 'No description available' }}</p>
                        </div>
                    </div>
                </div>
                <div class="mt-4 lg:mt-0 lg:ml-6">
                    <span class="px-4 py-2 text-sm font-medium rounded-full border-2
                        @if($project->status == 'completed') bg-green-50 text-green-700 border-green-200
                        @elseif($project->status == 'in_progress') bg-blue-50 text-blue-700 border-blue-200
                        @elseif($project->status == 'on_hold') bg-yellow-50 text-yellow-700 border-yellow-200
                        @else bg-gray-50 text-gray-700 border-gray-200 @endif">
                        {{ ucfirst(str_replace('_', ' ', $project->status ?? 'pending')) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Project Stats -->
        <div class="px-8 py-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-user-tie text-blue-600 text-xl"></i>
                    </div>
                    <p class="text-sm text-gray-500 font-medium">Project Manager</p>
                    <p class="text-lg font-semibold text-gray-900 mt-1">{{ $project->manager->name ?? 'Not assigned' }}</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-tasks text-green-600 text-xl"></i>
                    </div>
                    <p class="text-sm text-gray-500 font-medium">Progress</p>
                    @php
                        $totalTasks = $project->tasks ? $project->tasks->count() : 0;
                        $completedTasks = $project->tasks ? $project->tasks->where('status', 'completed')->count() : 0;
                    @endphp
                    <p class="text-lg font-semibold text-gray-900 mt-1">{{ $completedTasks }}/{{ $totalTasks }} tasks</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-purple-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-play-circle text-purple-600 text-xl"></i>
                    </div>
                    <p class="text-sm text-gray-500 font-medium">Start Date</p>
                    <p class="text-lg font-semibold text-gray-900 mt-1">
                        {{ $project->start_date ? $project->start_date->format('M d, Y') : 'Not set' }}
                    </p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 {{ $project->due_date && $project->due_date->isPast() ? 'bg-red-100' : 'bg-orange-100' }} rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-flag {{ $project->due_date && $project->due_date->isPast() ? 'text-red-600' : 'text-orange-600' }} text-xl"></i>
                    </div>
                    <p class="text-sm text-gray-500 font-medium">Due Date</p>
                    <p class="text-lg font-semibold {{ $project->due_date && $project->due_date->isPast() ? 'text-red-600' : 'text-gray-900' }} mt-1">
                        {{ $project->due_date ? $project->due_date->format('M d, Y') : 'Not set' }}
                        @if($project->due_date && $project->due_date->isPast())
                        <span class="block text-xs text-red-500 mt-1">Overdue</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content - Tasks & Files -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Enhanced Tasks Section -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 rounded-t-2xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900">Project Tasks</h3>
                            <p class="text-sm text-gray-600 mt-1">{{ $totalTasks }} tasks in total</p>
                        </div>
                        <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-clipboard-list text-blue-600"></i>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    @forelse($project->tasks ?? [] as $task)
                    <div class="border border-gray-200 rounded-xl p-6 mb-6 last:mb-0 hover:border-blue-300 transition-all duration-200 bg-white">
                        <!-- Task Header -->
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex-1">
                                <h4 class="text-lg font-semibold text-gray-900 mb-2">{{ $task->title ?? 'Untitled Task' }}</h4>
                                <p class="text-gray-600 text-sm leading-relaxed">{{ $task->description ?? 'No description available' }}</p>
                            </div>
                            <span class="px-3 py-1 text-xs font-medium rounded-full border
                                @if($task->status == 'completed') bg-green-50 text-green-700 border-green-200
                                @elseif($task->status == 'in_progress') bg-blue-50 text-blue-700 border-blue-200
                                @elseif($task->status == 'on_hold') bg-yellow-50 text-yellow-700 border-yellow-200
                                @else bg-gray-50 text-gray-700 border-gray-200 @endif">
                                {{ ucfirst($task->status ?? 'pending') }}
                            </span>
                        </div>

                        <!-- Task Meta -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4 text-sm">
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-user mr-3 text-gray-400 w-4"></i>
                                <span>Assigned to: <span class="font-medium">{{ $task->assignedTo->name ?? 'Unassigned' }}</span></span>
                            </div>
                            @if($task->due_date)
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-calendar mr-3 text-gray-400 w-4"></i>
                                <span>Due: <span class="font-medium">{{ $task->due_date->format('M d, Y') }}</span></span>
                            </div>
                            @endif
                        </div>

                        <!-- Task Comments -->
                        @if($task->comments && $task->comments->count() > 0)
                        <div class="mt-6 border-t pt-6">
                            <h5 class="text-sm font-semibold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-comments mr-2 text-blue-500"></i>
                                Comments ({{ $task->comments->count() }})
                            </h5>
                            <div class="space-y-3">
                                @foreach($task->comments as $comment)
                                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                                    <div class="flex justify-between items-start mb-2">
                                        <div class="flex items-center space-x-2">
                                            <span class="font-medium text-gray-900">{{ $comment->user->name ?? 'Unknown User' }}</span>
                                            <span class="text-xs text-gray-500">•</span>
                                            <span class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                                        </div>
                                        @if($comment->is_internal)
                                        <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">Internal</span>
                                        @endif
                                    </div>
                                    <p class="text-gray-700 text-sm">{{ $comment->content }}</p>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @else
                        <div class="mt-6 border-t pt-6">
                            <p class="text-gray-500 text-sm text-center py-4">No comments yet.</p>
                        </div>
                        @endif

                        <!-- Add Comment Form -->
                        <form action="{{ route('client.tasks.comments.store', $task) }}" method="POST" class="mt-6">
                            @csrf
                            <div class="flex space-x-3">
                                <div class="flex-1">
                                    <input type="text" name="content" placeholder="Add a comment to this task..."
                                           class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                           required>
                                </div>
                                <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-xl text-sm font-medium hover:bg-blue-700 transition-all duration-200 transform hover:scale-105 shadow-sm">
                                    <i class="fas fa-paper-plane mr-2"></i>Comment
                                </button>
                            </div>
                        </form>
                    </div>
                    @empty
                    <div class="text-center py-12">
                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-tasks text-gray-400 text-2xl"></i>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">No Tasks Yet</h4>
                        <p class="text-gray-500">Tasks will appear here once they are added to the project.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Enhanced Files Section -->
            @if($project->files && $project->files->count() > 0)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 rounded-t-2xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900">Project Files</h3>
                            <p class="text-sm text-gray-600 mt-1">{{ $project->files->count() }} files available</p>
                        </div>
                        <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-folder text-green-600"></i>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($project->files as $file)
                        <div class="border border-gray-200 rounded-xl p-4 hover:border-green-300 transition-all duration-200 bg-white">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                    @php
                                        $fileExtension = strtolower(pathinfo($file->original_name, PATHINFO_EXTENSION));
                                        $fileIcons = [
                                            'pdf' => 'file-pdf',
                                            'doc' => 'file-word',
                                            'docx' => 'file-word',
                                            'xls' => 'file-excel',
                                            'xlsx' => 'file-excel',
                                            'ppt' => 'file-powerpoint',
                                            'pptx' => 'file-powerpoint',
                                            'jpg' => 'file-image',
                                            'jpeg' => 'file-image',
                                            'png' => 'file-image',
                                            'gif' => 'file-image',
                                            'zip' => 'file-archive',
                                            'rar' => 'file-archive'
                                        ];
                                        $fileIcon = $fileIcons[$fileExtension] ?? 'file';
                                    @endphp
                                    <i class="fas fa-{{ $fileIcon }} text-blue-600"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-gray-900 text-sm truncate">{{ $file->original_name }}</p>
                                    <p class="text-xs text-gray-500 mt-1">Uploaded {{ $file->created_at->diffForHumans() }}</p>
                                    <p class="text-xs text-gray-400 mt-1">{{ $fileExtension }} • {{ round($file->file_size / 1024) }} KB</p>
                                </div>
                                <a href="{{ route('client.files.download', $file) }}"
                                   class="bg-green-50 text-green-700 hover:bg-green-100 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200 border border-green-200">
                                    <i class="fas fa-download mr-1"></i>Download
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Enhanced Project Discussion Sidebar -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 h-fit sticky top-6">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-purple-50 to-pink-50 rounded-t-2xl">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-semibold text-gray-900">Project Discussion</h3>
                    <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-comments text-purple-600"></i>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <!-- Comment Form -->
                <form action="{{ route('client.projects.comments.store', $project) }}" method="POST" class="mb-6">
                    @csrf
                    <div class="mb-4">
                        <textarea name="content" rows="4" placeholder="Share your thoughts or ask a question about this project..."
                                  class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm resize-none"
                                  required></textarea>
                    </div>
                    <button type="submit"
                            class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white px-4 py-3 rounded-xl text-sm font-medium hover:from-purple-700 hover:to-pink-700 transition-all duration-200 transform hover:scale-[1.02] shadow-sm">
                        <i class="fas fa-paper-plane mr-2"></i>Post Comment
                    </button>
                </form>

                <!-- Comments List -->
                <div class="space-y-4 max-h-96 overflow-y-auto">
                    @forelse($project->comments ?? [] as $comment)
                    <div class="border border-gray-200 rounded-xl p-4 bg-gray-50">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex items-center space-x-2">
                                <span class="font-semibold text-sm text-gray-900">{{ $comment->user->name ?? 'Unknown User' }}</span>
                                <span class="text-xs text-gray-500">•</span>
                                <span class="text-xs text-gray-500">{{ $comment->created_at->format('M j, g:i A') }}</span>
                            </div>
                            @if($comment->is_internal)
                            <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">Internal</span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-700 leading-relaxed">{{ $comment->content }}</p>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-comment-slash text-gray-400 text-xl"></i>
                        </div>
                        <p class="text-gray-500 text-sm">No comments yet. Start the discussion!</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .sticky {
        position: sticky;
    }
    .transform:hover {
        transition: all 0.3s ease;
    }
</style>

<script>
    // Smooth scrolling and animations
    document.addEventListener('DOMContentLoaded', function() {
        // Add hover effects to cards
        const cards = document.querySelectorAll('.bg-white');
        cards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
                this.style.boxShadow = '0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = '';
            });
        });

        // Auto-resize textareas
        const textareas = document.querySelectorAll('textarea');
        textareas.forEach(textarea => {
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });
        });

        // Smooth scroll for internal links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    });
</script>
@endsection