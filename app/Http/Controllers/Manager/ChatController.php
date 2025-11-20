<?php

namespace App\Http\Controllers\Manager;
use App\Providers\NotificationService;

use App\Http\Controllers\Controller;
use App\Models\ChatRoom;
use App\Models\ChatMessage;
use App\Models\Projects;
use App\Models\Tasks;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ChatController extends Controller
{


    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index()
    {
        $user = auth()->user();

        // Debug: Check what projects user has access to
        \Log::info('User accessing chat index', [
            'user_id' => $user->id,
            'role' => $user->role,
            'name' => $user->name
        ]);

        // Get project rooms based on user role
        if ($user->role === 'admin') {
            // For managers: only show projects where they are the manager
            $projectRooms = ChatRoom::where('type', 'project')
                ->whereHas('project', function($query) use ($user) {
                    $query->where('manager_id', $user->id);
                })
                ->whereHas('participants', function($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->with(['project', 'messages' => function($query) {
                    $query->latest()->limit(1);
                }, 'participants.user'])
                ->get();

            \Log::info('Admin project rooms', [
                'count' => $projectRooms->count(),
                'projects' => $projectRooms->pluck('project.name')
            ]);

        } else if ($user->role === 'super_admin') {
            // For super_admin: show ALL project chats
            $projectRooms = ChatRoom::where('type', 'project')
                ->with(['project', 'messages' => function($query) {
                    $query->latest()->limit(1);
                }, 'participants.user'])
                ->get();

            \Log::info('Super Admin project rooms', [
                'count' => $projectRooms->count(),
                'projects' => $projectRooms->pluck('project.name')
            ]);

        } else {
            // For users: show projects where they are team members
            $projectRooms = ChatRoom::where('type', 'project')
                ->whereHas('participants', function($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->with(['project', 'messages' => function($query) {
                    $query->latest()->limit(1);
                }, 'participants.user'])
                ->get();
        }

        // Get direct message rooms
        $directRooms = ChatRoom::where('type', 'direct')
            ->whereHas('participants', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with(['messages' => function($query) {
                $query->latest()->limit(1);
            }, 'participants.user'])
            ->get();

        // ⭐⭐ CHANGED: All users can see ALL other users for direct messaging
        $availableUsers = User::where('id', '!=', $user->id)->get();

        return view('manager.chat.index', compact('projectRooms', 'directRooms', 'availableUsers'));
    }

    public function projectChat(Projects $project)
{
    $user = auth()->user();

    // Role-based access control
    if ($user->role === 'user') {
        // Check if user is team member OR has tasks assigned in this project
        $isTeamMember = $project->teamMembers->contains('id', $user->id);
        $hasTasksInProject = Tasks::where('project_id', $project->id)
                                ->where('assigned_to', $user->id)
                                ->exists();

        if (!$isTeamMember && !$hasTasksInProject) {
            abort(403, 'Access denied. You are not a team member of this project and have no tasks assigned.');
        }

        // If user has tasks but is not a team member, add them as team member
        if (!$isTeamMember && $hasTasksInProject) {
            $project->teamMembers()->syncWithoutDetaching([$user->id]);
        }
    } else {
        // Original access check for admin/super_admin
        if (!$user->canAccessProject($project)) {
            if (!$project->teamMembers->contains('id', $user->id)) {
                $project->teamMembers()->syncWithoutDetaching([$user->id]);
            }
        }
    }

    // Find or create project chat room
    $chatRoom = ChatRoom::firstOrCreate(
        ['project_id' => $project->id, 'type' => 'project'],
        [
            'name' => $project->name . ' Chat',
            'description' => 'Project discussion group',
            'created_by' => $user->id
        ]
    );

    // Add current user to participants if not already
    $chatRoom->participants()->firstOrCreate(['user_id' => $user->id]);

    // Add all project members to chat room
    $allMembers = $project->getAllMembers();
    foreach ($allMembers as $member) {
        $chatRoom->participants()->firstOrCreate(['user_id' => $member->id]);
    }

    $messages = $chatRoom->messages()
        ->with('user')
        ->orderBy('created_at', 'asc')
        ->paginate(50);

    $this->markMessagesAsRead($chatRoom, $user);

    return view('manager.chat.project-chat', compact('project', 'chatRoom', 'messages'));
}

    public function directChat(User $user)
    {
        $currentUser = auth()->user();

        // ⭐⭐ CHANGED: REMOVED ALL RESTRICTIONS - Anyone can chat with anyone
        // No role-based restrictions for direct messaging
        // Any user can start a direct chat with any other user

        // Find or create direct chat room
        $chatRoom = ChatRoom::where('type', 'direct')
            ->whereHas('participants', function($query) use ($currentUser, $user) {
                $query->where('user_id', $currentUser->id);
            })
            ->whereHas('participants', function($query) use ($currentUser, $user) {
                $query->where('user_id', $user->id);
            })
            ->first();

        if (!$chatRoom) {
            $chatRoom = ChatRoom::create([
                'name' => 'Direct Chat',
                'type' => 'direct',
                'created_by' => $currentUser->id
            ]);

            $chatRoom->participants()->createMany([
                ['user_id' => $currentUser->id],
                ['user_id' => $user->id]
            ]);
        }

        $messages = $chatRoom->messages()
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->paginate(50);

        $this->markMessagesAsRead($chatRoom, $currentUser);

        return view('manager.chat.direct-chat', compact('user', 'chatRoom', 'messages'));
    }

    public function sendMessage(Request $request, ChatRoom $chatRoom)
    {
        $request->validate([
            'message' => 'required_without:attachment|string|max:1000',
            'attachment' => 'nullable|file|max:10240'
        ]);

        $user = auth()->user();
        $sender=$user;

        // Enhanced access control based on user role
        if ($user->role === 'user') {
            if ($chatRoom->type === 'project') {
                // Check if user is team member of the project
                if (!$chatRoom->project || !$chatRoom->project->teamMembers->contains('id', $user->id)) {
                    return response()->json(['error' => 'Access denied'], 403);
                }
            } else if ($chatRoom->type === 'direct') {
                // ⭐⭐ CHANGED: Only check if user is participant (no role restrictions)
                $isParticipant = $chatRoom->participants()->where('user_id', $user->id)->exists();
                if (!$isParticipant) {
                    return response()->json(['error' => 'Access denied'], 403);
                }
            }
        } else {
            // Original access check for admin/super_admin
            if (!$user->canAccessChat($chatRoom)) {
                return response()->json(['error' => 'Access denied'], 403);
            }
        }

        try {
            $messageData = [
                'chat_room_id' => $chatRoom->id,
                'user_id' => $user->id,
                'message' => $request->message ?: '[File Attachment]'
            ];

            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $path = $file->store('chat-attachments', 'public');
                $messageData['attachment'] = $path;
                $messageData['attachment_name'] = $file->getClientOriginalName();
            }

            $message = ChatMessage::create($messageData);
            $message->load('user');

            $responseData = [
                'id' => $message->id,
                'message' => $message->message,
                'user_id' => $message->user_id,
                'user' => [
                    'id' => $message->user->id,
                    'name' => $message->user->name,
                    'role' => $message->user->role,
                ],
                'attachment' => $message->attachment,
                'attachment_name' => $message->attachment_name,
                'created_at' => $message->created_at->toISOString(),
                'chat_room_id' => $chatRoom->id
            ];

            \Log::info('Broadcasting message:', $responseData);
            broadcast(new \App\Events\ChatMessageSent($responseData))->toOthers();
            \Log::info('Message broadcast completed');

            $this->sendChatNotifications($chatRoom, $sender, $message->message);


            return response()->json([
                'success' => true,
                'message' => 'Message sent successfully',
                'message_data' => $responseData
            ]);

        } catch (\Exception $e) {
            \Log::error('Message send error:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'error' => 'Failed to send message: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send chat notifications based on room type
     */
   private function sendChatNotifications(ChatRoom $chatRoom, $sender, $message)
{
    try {
        \Log::info('Sending chat notifications', [
            'chat_room_id' => $chatRoom->id,
            'chat_room_type' => $chatRoom->type,
            'sender_id' => $sender->id,
            'message_preview' => Str::limit($message, 50)
        ]);

        if ($chatRoom->type === 'direct') {
            $this->sendDirectMessageNotification($chatRoom, $sender, $message);
        } elseif ($chatRoom->type === 'project') {
            $this->sendProjectMessageNotification($chatRoom, $sender, $message);
        }

    } catch (\Exception $e) {
        \Log::error('Chat notification error: ' . $e->getMessage());
    }
}

/**
 * Send direct message notification
 */
private function sendDirectMessageNotification(ChatRoom $chatRoom, $sender, $message)
{
    // Get the recipient (other participant in direct chat)
    $recipient = $chatRoom->participants()
        ->where('user_id', '!=', $sender->id)
        ->first();

    if ($recipient) {
        \Log::info('Sending direct message notification', [
            'sender_id' => $sender->id,
            'recipient_id' => $recipient->id,
            'chat_room_id' => $chatRoom->id
        ]);

        $this->notificationService->notifyDirectMessage($recipient, $sender, $message, $chatRoom);
    } else {
        \Log::warning('No recipient found for direct message', [
            'chat_room_id' => $chatRoom->id,
            'sender_id' => $sender->id
        ]);
    }
}

/**
 * Send project message notification
 */
private function sendProjectMessageNotification(ChatRoom $chatRoom, $sender, $message)
{
    if (!$chatRoom->project) {
        \Log::warning('No project found for project chat', [
            'chat_room_id' => $chatRoom->id
        ]);
        return;
    }

    \Log::info('Sending project message notification', [
        'project_id' => $chatRoom->project->id,
        'project_name' => $chatRoom->project->name,
        'sender_id' => $sender->id
    ]);

    $this->notificationService->notifyProjectChatMessage(
        $chatRoom->project,
        $sender,
        $message,
        $chatRoom
    );
}
    /**
     * Notify mentioned users in chat messages
     */
    private function notifyMentionedUsers($message, $chatRoom, $sender)
    {
        $mentionedUsers = $this->extractMentionedUsers($message->message);

        if (empty($mentionedUsers)) {
            return;
        }

        $messagePreview = Str::limit($message->message, 100);

        foreach ($mentionedUsers as $username) {
            $user = User::where('name', $username)->first();

            if ($user && $user->id !== $sender->id) {
                $context = $chatRoom->type === 'project' && $chatRoom->project
                    ? "project '{$chatRoom->project->name}' chat"
                    : 'direct chat';

                $this->createNotification($user->id, 'mentioned_in_chat', [
                    'title' => 'You Were Mentioned',
                    'message' => "{$sender->name} mentioned you in {$context}: {$messagePreview}",
                    'action_url' => $this->getChatRoomUrl($chatRoom),
                    'icon' => 'fas fa-at',
                    'color' => 'pink',
                    'mentioned_by' => $sender->name,
                    'mentioned_by_id' => $sender->id,
                    'message_preview' => $messagePreview,
                    'context' => $context,
                    'chat_room_id' => $chatRoom->id,
                ]);
            }
        }
    }

    /**
     * Extract mentioned users from message (@username format)
     */
    private function extractMentionedUsers($content)
    {
        preg_match_all('/@([\w\-\.]+)/', $content, $matches);
        return array_unique($matches[1] ?? []);
    }

    /**
     * Get chat room URL for notifications
     */
    private function getChatRoomUrl($chatRoom)
    {
        if ($chatRoom->type === 'project' && $chatRoom->project) {
            return route('manager.chat.project', $chatRoom->project);
        } elseif ($chatRoom->type === 'direct') {
            $otherParticipant = $chatRoom->participants->where('id', '!=', auth()->id())->first();
            return $otherParticipant ? route('manager.chat.direct', $otherParticipant) : '#';
        }

        return '#';
    }

    /**
     * Create notification in database
     */
    private function createNotification($userId, $type, $data)
    {
        try {
            \App\Models\Notification::create([
                'type' => $type,
                'notifiable_id' => $userId,
                'notifiable_type' => 'App\Models\User',
                'data' => $data,
            ]);

            \Log::info("Notification created for user {$userId}", ['type' => $type]);

        } catch (\Exception $e) {
            \Log::error("Failed to create notification for user {$userId}: " . $e->getMessage());
        }
    }

    private function markMessagesAsRead($chatRoom, $user)
    {
        $participant = $chatRoom->participants()->where('user_id', $user->id)->first();
        if ($participant) {
            $participant->update(['last_read_at' => now()]);
        }

        $chatRoom->messages()
            ->where('user_id', '!=', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function markAsRead(ChatRoom $chatRoom)
    {
        $user = auth()->user();
        $this->markMessagesAsRead($chatRoom, $user);
        return response()->json(['success' => true]);
    }

   public function getMessages(ChatRoom $chatRoom)
{
    $user = auth()->user();

    // Access control (your existing code)
    if ($user->role === 'user') {
        if ($chatRoom->type === 'project') {
            if (!$chatRoom->project || !$chatRoom->project->teamMembers->contains('id', $user->id)) {
                return response()->json(['error' => 'Access denied'], 403);
            }
        } else if ($chatRoom->type === 'direct') {
            $isParticipant = $chatRoom->participants()->where('user_id', $user->id)->exists();
            if (!$isParticipant) {
                return response()->json(['error' => 'Access denied'], 403);
            }
        }
    } else {
        if (!$user->canAccessChat($chatRoom)) {
            return response()->json(['error' => 'Access denied'], 403);
        }
    }

    $messages = $chatRoom->messages()
        ->select(['id', 'message', 'user_id', 'attachment', 'attachment_name', 'created_at', 'read_at'])
        ->with(['user:id,name,role'])
        ->orderBy('created_at', 'desc')
        ->paginate(100);

    \Log::info('📨 GET MESSAGES - EFFICIENT', [
        'chat_room_id' => $chatRoom->id,
        'loaded_count' => $messages->count(),
        'total_messages' => $chatRoom->messages()->count(),
        'memory_usage' => round(memory_get_usage(true) / 1048576, 2) . 'MB'
    ]);

    return response()->json($messages);
}

    // Add these methods to your existing ChatController

    /**
     * Get chat notifications for navbar
     */
    public function getNotifications()
    {
        $user = auth()->user();

        $unreadCount = $this->getUnreadMessagesCount($user->id);
        $recentMessages = $this->getRecentUnreadMessages($user->id);

        return response()->json([
            'unread_count' => $unreadCount,
            'recent_messages' => $recentMessages
        ]);
    }

    /**
     * Mark specific notification as read
     */
    public function markNotificationAsRead(Request $request)
    {
        $user = auth()->user();

        $message = ChatMessage::where('id', $request->message_id)
            ->whereHas('room.participants', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->first();

        if ($message) {
            $message->update(['read_at' => now()]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsAsRead()
    {
        $user = auth()->user();

        // Mark all messages as read
        ChatMessage::whereHas('room.participants', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->where('user_id', '!=', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    /**
     * Get unread messages count
     */
    private function getUnreadMessagesCount($userId)
    {
        return ChatMessage::whereHas('room.participants', function($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->where('user_id', '!=', $userId)
            ->whereNull('read_at')
            ->count();
    }

    /**
     * Get recent unread messages for notifications
     */
    private function getRecentUnreadMessages($userId, $limit = 10)
    {
        return ChatMessage::whereHas('room.participants', function($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->where('user_id', '!=', $userId)
            ->whereNull('read_at')
            ->with(['user', 'room'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function($message) {
                return [
                    'id' => $message->id,
                    'message_id' => $message->id,
                    'sender_name' => $message->user->name,
                    'sender_id' => $message->user->id,
                    'message' => Str::limit($message->message, 100),
                    'room_type' => $message->room->type,
                    'room_id' => $message->room->type === 'project' ? $message->room->project_id : $message->room->id,
                    'created_at' => $message->created_at->toISOString(),
                    'is_read' => !is_null($message->read_at),
                ];
            });
    }
}
