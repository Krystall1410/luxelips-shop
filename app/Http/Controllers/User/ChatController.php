<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Get target Admin ID.
     */
    private function getAdminId(): int
    {
        $admin = User::where('role', 'admin')->first();
        return $admin ? $admin->id : 1;
    }

    /**
     * Send message from User to Admin.
     */
    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ], [
            'message.required' => 'Nội dung tin nhắn không được để trống.',
        ]);

        $messageText = trim($request->input('message'));
        if (empty($messageText)) {
            return response()->json(['error' => 'Nội dung tin nhắn không được để trống.'], 400);
        }

        $adminId = $this->getAdminId();

        try {
            $message = Message::create([
                'sender_id' => Auth::id(),
                'receiver_id' => $adminId,
                'content' => $messageText,
                'is_read' => false,
            ]);

            $message->load('sender');

            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Không thể gửi tin nhắn: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get chat history between current User & Admin.
     */
    public function getMessages()
    {
        $userId = Auth::id();
        $adminId = $this->getAdminId();

        // Mark unread messages sent by admin to user as read
        Message::where('sender_id', $adminId)
            ->where('receiver_id', $userId)
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
     * Get count of unread messages for User.
     */
    public function unreadCount()
    {
        $userId = Auth::id();
        $adminId = $this->getAdminId();

        $count = Message::where('sender_id', $adminId)
            ->where('receiver_id', $userId)
            ->where('is_read', false)
            ->count();

        return response()->json(['unread_count' => $count]);
    }
}
