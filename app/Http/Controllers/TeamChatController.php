<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ChatRoom;
use App\Models\ChatMessage;
use App\Models\Projects;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamChatController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Get project rooms where team member is participant
        $projectRooms = ChatRoom::where('type', 'project')
            ->whereHas('participants', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with(['project', 'messages' => function($query) {
                $query->latest()->limit(1);
            }, 'participants.user'])
            ->get();

        // Get direct message rooms - ONLY with managers/admins
        $directRooms = ChatRoom::where('type', 'direct')
            ->whereHas('participants', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->whereHas('participants.user', function($query) {
                $query->whereIn('role', ['admin', 'super_admin', 'manager']);
            })
            ->with(['messages' => function($query) {
                $query->latest()->limit(1);
            }, 'participants.user'])
            ->get();

        // Get available users for new DM - ONLY managers/admins for team members
        $availableUsers = User::whereIn('role', ['admin', 'super_admin', 'manager'])
            ->where('id', '!=', $user->id)
            ->get();

        // Get active chat from request parameters
        $activeChatId = request('active_chat');
        $activeChatType = request('active_chat_type');

        $activeChat = null;
        $activeMessages = [];

        if ($activeChatId && $activeChatType) {
            $activeChat = ChatRoom::where('id', $activeChatId)
                ->where('type', $activeChatType)
                ->with(['participants.user', 'project'])
                ->first();

            if ($activeChat && $this->canAccessChat($user, $activeChat)) {
                $activeMessages = $activeChat->messages()
                    ->with('user')
                    ->orderBy('created_at', 'asc')
                    ->get();

                $this->markMessagesAsRead($activeChat, $user);
            }
        }

        return view('team.chat.index', compact(
            'projectRooms',
            'directRooms',
            'availableUsers',
            'activeChat',
            'activeMessages'
        ));
    }

    public function projectChat(Projects $project)
    {
        $user = Auth::user();

        // Check if team member is part of this project
        if (!$project->teamMembers->contains('id', $user->id)) {
            abort(403, 'You are not a member of this project');
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

        // Add all project members to chat room (manager + team members)
        $allMembers = $project->getAllMembers();

        foreach ($allMembers as $member) {
            $chatRoom->participants()->firstOrCreate(['user_id' => $member->id]);
        }

        // Redirect to unified chat interface with active chat
        return redirect()->route('team.chat.index')->with([
            'active_chat_id' => $chatRoom->id,
            'active_chat_type' => 'project'
        ]);
    }

    public function directChat(User $user)
    {
        $currentUser = Auth::user();

        // Check if current user can message this user - Team members can only message managers/admins
        if (!in_array($user->role, ['admin', 'super_admin', 'manager'])) {
            abort(403, 'You can only message managers and administrators');
        }

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

            // Add both users as participants
            $chatRoom->participants()->createMany([
                ['user_id' => $currentUser->id],
                ['user_id' => $user->id]
            ]);
        }

        // Redirect to unified chat interface with active chat
        return redirect()->route('team.chat.index')->with([
            'active_chat_id' => $chatRoom->id,
            'active_chat_type' => 'direct'
        ]);
    }

    public function sendMessage(Request $request, ChatRoom $chatRoom)
    {
        $request->validate([
            'message' => 'required_without:attachment|string|max:1000',
            'attachment' => 'nullable|file|max:10240'
        ]);

        $user = Auth::user();

        // Check if user has access to this chat room - Team member specific check
        if (!$this->canAccessChat($user, $chatRoom)) {
            return response()->json(['error' => 'Access denied'], 403);
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

            // DEBUG: Log before broadcasting
            \Log::info('Broadcasting message:', ['message_id' => $message->id, 'chat_room_id' => $chatRoom->id]);

            // Broadcast the event - PASS THE MESSAGE OBJECT, NOT ARRAY
            broadcast(new \App\Events\ChatMessageSent($message))->toOthers();

            // DEBUG: Log after broadcasting
            \Log::info('Message broadcast completed');

            // Prepare response data
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

    private function markMessagesAsRead($chatRoom, $user)
    {
        $participant = $chatRoom->participants()->where('user_id', $user->id)->first();
        if ($participant) {
            $participant->update(['last_read_at' => now()]);
        }

        // Mark individual messages as read
        $chatRoom->messages()
            ->where('user_id', '!=', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function markAsRead(ChatRoom $chatRoom)
    {
        $user = Auth::user();
        $this->markMessagesAsRead($chatRoom, $user);

        return response()->json(['success' => true]);
    }

    public function getMessages(ChatRoom $chatRoom)
    {
        $user = Auth::user();

        // Check if user has access to this chat room - Team member specific check
        if (!$this->canAccessChat($user, $chatRoom)) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        $messages = $chatRoom->messages()
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->paginate(50);

        return response()->json($messages);
    }

    /**
     * Team member specific access check
     * Team members can only access:
     * 1. Project chats they're part of
     * 2. Direct messages with managers/admins only
     */
    private function canAccessChat($user, $chatRoom)
    {
        // Check if user is participant in the chat room
        if (!$chatRoom->participants()->where('user_id', $user->id)->exists()) {
            return false;
        }

        // For direct messages, ensure the other participant is manager/admin
        if ($chatRoom->type === 'direct') {
            $otherUser = $chatRoom->participants()
                ->where('user_id', '!=', $user->id)
                ->first();

            return $otherUser && in_array($otherUser->user->role, ['admin', 'super_admin', 'manager']);
        }

        // For project chats, no additional restrictions beyond being a participant
        return true;
    }

    /**
     * Additional method for team members to get chat data
     */
    public function getChatData(ChatRoom $chatRoom)
    {
        $user = Auth::user();

        if (!$this->canAccessChat($user, $chatRoom)) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        $messages = $chatRoom->messages()
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        $this->markMessagesAsRead($chatRoom, $user);

        return response()->json([
            'success' => true,
            'chat_room' => $chatRoom->load(['participants.user', 'project']),
            'messages' => $messages
        ]);
    }

    /**
     * Additional method for team members to create new chats
     */
    public function createChat(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'type' => 'required|in:direct', // Team members can only create direct chats
            'user_id' => 'required|exists:users,id'
        ]);

        try {
            $targetUser = User::findOrFail($request->user_id);

            // Team members can only message managers/admins
            if (!in_array($targetUser->role, ['admin', 'super_admin', 'manager'])) {
                return response()->json(['error' => 'You can only message managers and administrators'], 403);
            }

            $chatRoom = ChatRoom::where('type', 'direct')
                ->whereHas('participants', function($query) use ($user, $targetUser) {
                    $query->where('user_id', $user->id);
                })
                ->whereHas('participants', function($query) use ($user, $targetUser) {
                    $query->where('user_id', $targetUser->id);
                })
                ->first();

            if (!$chatRoom) {
                $chatRoom = ChatRoom::create([
                    'name' => 'Direct Chat',
                    'type' => 'direct',
                    'created_by' => $user->id
                ]);

                $chatRoom->participants()->createMany([
                    ['user_id' => $user->id],
                    ['user_id' => $targetUser->id]
                ]);
            }

            return response()->json([
                'success' => true,
                'chat_room' => $chatRoom->load(['participants.user']),
                'redirect_url' => route('team.chat.index') . '?active_chat=' . $chatRoom->id
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to create chat: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Additional method for team members to get chat list
     */
    public function getChatList()
    {
        $user = Auth::user();

        $projectRooms = ChatRoom::where('type', 'project')
            ->whereHas('participants', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with(['project', 'messages' => function($query) {
                $query->latest()->limit(1);
            }, 'participants.user'])
            ->get()
            ->map(function($room) use ($user) {
                return $this->formatChatRoomData($room, $user);
            });

        $directRooms = ChatRoom::where('type', 'direct')
            ->whereHas('participants', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->whereHas('participants.user', function($query) {
                $query->whereIn('role', ['admin', 'super_admin', 'manager']);
            })
            ->with(['messages' => function($query) {
                $query->latest()->limit(1);
            }, 'participants.user'])
            ->get()
            ->map(function($room) use ($user) {
                return $this->formatChatRoomData($room, $user);
            });

        return response()->json([
            'project_chats' => $projectRooms,
            'direct_chats' => $directRooms
        ]);
    }

    /**
     * Helper method to format chat room data
     */
    private function formatChatRoomData($room, $user)
    {
        $otherUser = null;
        if ($room->type === 'direct') {
            $otherUser = $room->participants->where('user_id', '!=', $user->id)->first()->user ?? null;
        }

        $lastMessage = $room->messages->first();
        $unreadCount = $room->messages()
            ->where('user_id', '!=', $user->id)
            ->whereNull('read_at')
            ->count();

        return [
            'id' => $room->id,
            'type' => $room->type,
            'name' => $room->type === 'project' ? $room->project->name : ($otherUser ? $otherUser->name : 'Unknown'),
            'subtitle' => $room->type === 'project' ? 'Project Chat' : ($otherUser ? $otherUser->role : 'User'),
            'last_message' => $lastMessage ? [
                'content' => $lastMessage->message,
                'sender' => $lastMessage->user->name,
                'time' => $lastMessage->created_at->diffForHumans()
            ] : null,
            'unread_count' => $unreadCount,
            'avatar_text' => $room->type === 'project' ? null : ($otherUser ? strtoupper(substr($otherUser->name, 0, 1)) : 'U'),
            'participants_count' => $room->participants->count(),
            'updated_at' => $room->updated_at->toISOString()
        ];
    }
}
