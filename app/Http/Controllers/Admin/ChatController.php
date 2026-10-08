<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Get list of Users who have conversed with Admin.
     */
    public function getUsers()
    {
        $adminId = Auth::id();

        // Get user IDs with message interactions
        $messages = Message::where('receiver_id', $adminId)
            ->orWhere('sender_id', $adminId)
            ->orderBy('created_at', 'desc')
            ->get();

        $userIds = [];
        foreach ($messages as $msg) {
            $otherId = ($msg->sender_id == $adminId) ? $msg->receiver_id : $msg->sender_id;
            if (!in_array($otherId, $userIds)) {
                $userIds[] = $otherId;
            }
        }

        $users = User::whereIn('id', $userIds)
            ->where('id', '!=', $adminId)
            ->get()
            ->map(function ($user) use ($adminId) {
                $latestMsg = Message::where(function ($q) use ($user, $adminId) {
                    $q->where('sender_id', $user->id)->where('receiver_id', $adminId);
                })->orWhere(function ($q) use ($user, $adminId) {
                    $q->where('sender_id', $adminId)->where('receiver_id', $user->id);
                })->orderBy('created_at', 'desc')->first();

                $unreadCount = Message::where('sender_id', $user->id)
                    ->where('receiver_id', $adminId)
                    ->where('is_read', false)
                    ->count();

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar_url' => $user->avatar_url,
                    'unread_count' => $unreadCount,
                    'latest_message' => $latestMsg ? $latestMsg->content : '',
                    'latest_message_time' => $latestMsg ? $latestMsg->created_at->format('H:i d/m') : '',
                    'latest_timestamp' => $latestMsg ? $latestMsg->created_at->timestamp : 0,
                ];
            })
            ->sortByDesc('latest_timestamp')
            ->values();

        return response()->json($users);
    }

    /**
     * Get message history with a specific User.
     */
    public function getMessages($userId)
    {
        $adminId = Auth::id();

        // Mark incoming messages from this user as read
        Message::where('sender_id', $userId)
            ->where('receiver_id', $adminId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = Message::with(['sender:id,name,avatar,role', 'receiver:id,name,avatar,role'])
            ->where(function ($q) use ($userId, $adminId) {
                $q->where('sender_id', $userId)->where('receiver_id', $adminId);
            })
            ->orWhere(function ($q) use ($userId, $adminId) {
                $q->where('sender_id', $adminId)->where('receiver_id', $userId);
            })
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($msg) {
                $msg->time_formatted = $msg->created_at->format('H:i - d/m');
                return $msg;
            });

        return response()->json($messages);
    }

    /**
     * Admin send message reply to User.
     */
    public function send(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'message' => 'required|string|max:2000',
        ]);

        $messageText = trim($request->input('message'));
        if (empty($messageText)) {
            return response()->json(['error' => 'Nội dung tin nhắn không được để trống.'], 400);
        }

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->user_id,
            'content' => $messageText,
            'is_read' => true,
        ]);

        $message->load('sender');
        $message->time_formatted = $message->created_at->format('H:i - d/m');

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    /**
     * Total unread messages count across all users for Admin.
     */
    public function unreadCount()
    {
        $adminId = Auth::id();
        $count = Message::where('receiver_id', $adminId)
            ->where('is_read', false)
            ->count();

        return response()->json(['unread_count' => $count]);
    }
}
