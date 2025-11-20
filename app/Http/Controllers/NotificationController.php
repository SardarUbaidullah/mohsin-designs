<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        try {
            $user = Auth::user();

            $notifications = Notification::where('notifiable_id', $user->id)
                ->where('notifiable_type', 'App\Models\User')
                ->orderBy('created_at', 'desc')
                ->take(20)
                ->get()
                ->map(function ($notification) {
                    return [
                        'id' => $notification->id,
                        'type' => $notification->type,
                        'data' => $notification->data,
                        'read_at' => $notification->read_at,
                        'created_at' => $notification->created_at->toISOString(),
                        'time_ago' => $notification->created_at->diffForHumans()
                    ];
                });

            $unreadCount = Notification::where('notifiable_id', $user->id)
                ->where('notifiable_type', 'App\Models\User')
                ->whereNull('read_at')
                ->count();

            return response()->json([
                'success' => true,
                'notifications' => $notifications,
                'unread_count' => $unreadCount
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load notifications',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function markAsRead($id)
    {
        try {
            $user = Auth::user();

            $notification = Notification::where('id', $id)
                ->where('notifiable_id', $user->id)
                ->where('notifiable_type', 'App\Models\User')
                ->firstOrFail();

            if (!$notification->read_at) {
                $notification->update(['read_at' => now()]);
            }

            $unreadCount = Notification::where('notifiable_id', $user->id)
                ->where('notifiable_type', 'App\Models\User')
                ->whereNull('read_at')
                ->count();

            return response()->json([
                'success' => true,
                'unread_count' => $unreadCount
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found'
            ], 404);
        }
    }

    public function markAllAsRead()
    {
        try {
            $user = Auth::user();

            Notification::where('notifiable_id', $user->id)
                ->where('notifiable_type', 'App\Models\User')
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            return response()->json([
                'success' => true,
                'unread_count' => 0
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark all as read'
            ], 500);
        }
    }

    public function unreadCount()
    {
        try {
            $user = Auth::user();

            $unreadCount = Notification::where('notifiable_id', $user->id)
                ->where('notifiable_type', 'App\Models\User')
                ->whereNull('read_at')
                ->count();

            return response()->json([
                'success' => true,
                'unread_count' => $unreadCount
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'unread_count' => 0
            ]);
        }
    }

    public function clearAll()
    {
        try {
            $user = Auth::user();

            Notification::where('notifiable_id', $user->id)
                ->where('notifiable_type', 'App\Models\User')
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'All notifications cleared'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear notifications'
            ], 500);
        }
    }
}
