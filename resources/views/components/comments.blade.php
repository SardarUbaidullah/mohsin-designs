@props([
    'commentable',
    'commentableType' => 'project',
    'showInternal' => true
])

@php
    // Helper functions for the component
    function getStoreRoute($commentable, $commentableType) {
        return match($commentableType) {
            'project' => route('comments.project.store', $commentable),
            'task' => route('comments.task.store', $commentable),
            'file' => route('comments.file.store', $commentable),
            default => route('comments.project.store', $commentable)
        };
    }

    function getUserColor($user) {
        $colors = [
            'super_admin' => 'bg-purple-500',
            'admin' => 'bg-red-500',
            'manager' => 'bg-orange-500',
            'user' => 'bg-blue-500',
            'client' => 'bg-green-500'
        ];
        return $colors[$user->role] ?? 'bg-gray-500';
    }

    function getRoleBadgeColor($role) {
        $colors = [
            'super_admin' => 'bg-purple-100 text-purple-800',
            'admin' => 'bg-red-100 text-red-800',
            'manager' => 'bg-orange-100 text-orange-800',
            'user' => 'bg-blue-100 text-blue-800',
            'client' => 'bg-green-100 text-green-800'
        ];
        return $colors[$role] ?? 'bg-gray-100 text-gray-800';
    }
@endphp

<div class="comments-section space-y-6" id="comments-section">
    <!-- Comment Form -->
    <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
        <h4 class="text-lg font-semibold text-gray-900 mb-4">Add a Comment</h4>
        <form action="{{ getStoreRoute($commentable, $commentableType) }}" method="POST" id="comment-form">
            @csrf

            <div class="mb-4">
                <textarea
                    name="content"
                    rows="4"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 resize-none"
                    placeholder="Write your comment here..."
                    required
                    id="comment-content"
                ></textarea>
            </div>

            @if(auth()->user()->role !== 'client')
            <div class="flex items-center mb-4">
                <input
                    type="checkbox"
                    name="is_internal"
                    id="is_internal"
                    value="1"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                >
                <label for="is_internal" class="ml-2 text-sm text-gray-700 flex items-center">
                    <svg class="w-4 h-4 mr-1 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Internal Comment (Team Members Only)
                </label>
            </div>
            @endif

            <div class="flex justify-end">
                <button
                    type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition duration-200 flex items-center shadow-sm"
                    id="submit-comment"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    Post Comment
                </button>
            </div>
        </form>
    </div>

    <!-- Comments List -->
    @php
        $comments = $showInternal
            ? $commentable->comments()->with('user')->latest()->get()
            : $commentable->publicComments()->with('user')->latest()->get();
    @endphp

    @if($comments->count() > 0)
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h4 class="text-lg font-semibold text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                </svg>
                Comments ({{ $comments->count() }})
            </h4>

            @if($showInternal)
            <div class="text-sm text-gray-500">
                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">{{ $commentable->internalComments()->count() }} internal</span>
                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs ml-2">{{ $commentable->publicComments()->count() }} public</span>
            </div>
            @endif
        </div>

        <div class="space-y-4" id="comments-list">
            @foreach($comments as $comment)
            <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm hover:shadow-md transition-shadow duration-200 {{ $comment->is_internal ? 'border-l-4 border-l-blue-500 bg-blue-50' : '' }}">
                <div class="flex justify-between items-start mb-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 {{ getUserColor($comment->user) }} rounded-full flex items-center justify-center text-white font-medium text-sm shadow-sm">
                            {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="flex items-center space-x-2">
                                <span class="font-semibold text-gray-900">{{ $comment->user->name }}</span>
                                <span class="text-xs px-2 py-1 rounded-full {{ getRoleBadgeColor($comment->user->role) }}">
                                    {{ ucfirst($comment->user->role) }}
                                </span>
                            </div>
                            <div class="flex items-center space-x-2 text-xs text-gray-500 mt-1">
                                <span>{{ $comment->created_at->diffForHumans() }}</span>
                                <span>•</span>
                                <span>{{ $comment->created_at->format('M j, Y g:i A') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center space-x-2">
                        @if($comment->is_internal)
                        <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full font-medium flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            Internal
                        </span>
                        @endif

                        @can('update', $comment)
                        <button onclick="toggleEditComment({{ $comment->id }})"
                                class="text-gray-400 hover:text-yellow-600 p-1 rounded transition-colors duration-200"
                                title="Edit Comment">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                        @endcan

                        @can('delete', $comment)
                        <form action="{{ route('comments.destroy', $comment) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    onclick="return confirm('Are you sure you want to delete this comment?')"
                                    class="text-gray-400 hover:text-red-600 p-1 rounded transition-colors duration-200"
                                    title="Delete Comment">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                        @endcan
                    </div>
                </div>

                <!-- Comment Content -->
                <div class="text-gray-700 leading-relaxed" id="comment-content-{{ $comment->id }}">
                    {!! nl2br(e($comment->content)) !!}
                </div>

                <!-- Edit Form (Hidden by default) -->
                @can('update', $comment)
                <form action="{{ route('comments.update', $comment) }}" method="POST" class="hidden mt-4" id="edit-form-{{ $comment->id }}">
                    @csrf
                    @method('PUT')
                    <textarea name="content" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">{{ $comment->content }}</textarea>
                    <div class="flex justify-end space-x-3 mt-3">
                        <button type="button"
                                onclick="toggleEditComment({{ $comment->id }})"
                                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition duration-200 font-medium">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200 font-medium">
                            Update Comment
                        </button>
                    </div>
                </form>
                @endcan
            </div>
            @endforeach
        </div>
    </div>
    @else
    <div class="text-center py-12 bg-white rounded-xl border border-gray-200">
        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
        </svg>
        <h4 class="text-lg font-medium text-gray-900 mb-2">No comments yet</h4>
        <p class="text-gray-500 max-w-md mx-auto">Be the first to share your thoughts and start the discussion.</p>
    </div>
    @endif
</div>

<script>
function toggleEditComment(commentId) {
    const contentEl = document.getElementById(`comment-content-${commentId}`);
    const formEl = document.getElementById(`edit-form-${commentId}`);

    if (contentEl && formEl) {
        const isEditing = formEl.classList.contains('hidden');

        if (isEditing) {
            contentEl.classList.add('hidden');
            formEl.classList.remove('hidden');
            formEl.querySelector('textarea').focus();
        } else {
            contentEl.classList.remove('hidden');
            formEl.classList.add('hidden');
        }
    }
}

// Auto-resize textarea
document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.getElementById('comment-content');
    if (textarea) {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    }
});
</script>
