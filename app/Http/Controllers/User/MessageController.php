<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MessageController extends Controller
{
    /**
     * Show the main chat interface with user list
     */
    public function index()
    {
        // Get all users except admin and current user
        $users = User::where('id', '!=', Auth::id())
            ->where('role', '!=', User::ROLE_ADMIN)
            ->get();

        // Get selected user if any
        $selectedUser = null;
        if (request()->has('user') && is_numeric(request('user'))) {
            $selectedUser = User::findOrFail(request('user'));
        }

        return view('user.messages.index', compact('users', 'selectedUser'));
    }

    /**
     * Show conversation with specific user
     */
    public function conversation(User $user)
    {
        // Mark messages from this user as read
        Message::where('sender_id', $user->id)
            ->where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        // Get conversation messages
        $messages = Message::where(function($query) use ($user) {
                $query->where('sender_id', Auth::id())
                    ->where('receiver_id', $user->id);
            })
            ->orWhere(function($query) use ($user) {
                $query->where('sender_id', $user->id)
                    ->where('receiver_id', Auth::id());
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return view('user.messages.conversation', compact('user', 'messages'));
    }

    /**
     * Send a message to a user
     */
    public function sendMessage(Request $request, User $user)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        try {
            $message = Message::create([
                'sender_id' => Auth::id(),
                'receiver_id' => $user->id,
                'content' => $request->content,
            ]);

            // Check if request is AJAX in multiple ways for robust detection
            if ($request->ajax() || $request->wantsJson() || $request->expectsJson() ||
                $request->header('X-Requested-With') == 'XMLHttpRequest' ||
                $request->header('Accept') == 'application/json') {

                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            }

            // For regular form submissions, redirect back to the conversation with a success message
            return redirect()->route('user.messages.index', ['user' => $user->id])
                ->with('success', 'Message envoyé avec succès.');
        } catch (\Exception $e) {
            \Log::error('Error sending message: ' . $e->getMessage());

            if ($request->ajax() || $request->wantsJson() || $request->expectsJson() ||
                $request->header('X-Requested-With') == 'XMLHttpRequest' ||
                $request->header('Accept') == 'application/json') {
                return response()->json([
                    'success' => false,
                    'error' => 'Une erreur est survenue lors de l\'envoi du message.',
                    'message' => $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de l\'envoi du message.')
                ->withInput();
        }
    }

    /**
     * Get unread message count for the current user
     */
    public function getUnreadCount()
    {
        $count = Message::where('receiver_id', Auth::id())
                     ->where('is_read', false)
                     ->count();

        return response()->json([
            'count' => $count
        ]);
    }

    /**
     * Get new messages since the last message timestamp
     */
    public function getNewMessages(Request $request, User $user)
    {
        $lastTimestamp = $request->input('last_timestamp');

        $newMessages = Message::where('created_at', '>', $lastTimestamp)
            ->where(function($query) use ($user) {
                $query->where(function($q) use ($user) {
                    $q->where('sender_id', Auth::id())
                      ->where('receiver_id', $user->id);
                })->orWhere(function($q) use ($user) {
                    $q->where('sender_id', $user->id)
                      ->where('receiver_id', Auth::id());
                });
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'messages' => $newMessages
        ]);
    }

    /**
     * Update a message
     */
    public function updateMessage(Request $request, $id)
    {
        $message = Message::findOrFail($id);

        // Check if user owns the message
        if ($message->sender_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $message->content = $request->content;
        $message->save();

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    /**
     * Delete a message (soft delete - just marks as deleted)
     */
    public function deleteMessage($id)
    {
        $message = Message::findOrFail($id);

        // Check if user owns the message
        if ($message->sender_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        // We don't really delete, just update the content
        $message->content = 'Ce message a été supprimé';
        $message->save();

        return response()->json([
            'success' => true
        ]);
    }
}
