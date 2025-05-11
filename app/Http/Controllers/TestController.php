<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Message;

class TestController extends Controller
{
    /**
     * Simple method to test if controller routes work
     */
    public function test()
    {
        return view('test.index');
    }

    /**
     * Test method for messages
     */
    public function messages()
    {
        // Get all users except current user
        $users = User::where('id', '!=', Auth::id())->get();

        $html = '<h1>Test Messages</h1>';
        $html .= '<h2>Current User</h2>';
        $html .= '<p>ID: ' . Auth::id() . '</p>';
        $html .= '<p>Name: ' . Auth::user()->name . '</p>';

        $html .= '<h2>Available Users</h2>';
        $html .= '<ul>';
        foreach ($users as $user) {
            $html .= '<li>' . $user->id . ' - ' . $user->name . ' - ' . $user->role . '</li>';
        }
        $html .= '</ul>';

        // Get recent messages
        $messages = Message::where('sender_id', Auth::id())
            ->orWhere('receiver_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $html .= '<h2>Recent Messages</h2>';
        if ($messages->count() > 0) {
            $html .= '<ul>';
            foreach ($messages as $message) {
                $html .= '<li>ID: ' . $message->id . ' | From: ' . $message->sender_id . ' | To: ' . $message->receiver_id . ' | Content: ' . $message->content . '</li>';
            }
            $html .= '</ul>';
        } else {
            $html .= '<p>No messages found.</p>';
        }

        $html .= '<h2>Links</h2>';
        $html .= '<ul>';
        $html .= '<li><a href="' . route('user.messages.index') . '">Messages Home</a></li>';
        $html .= '<li><a href="' . route('user.messages.debug') . '">Debug Page</a></li>';
        $html .= '<li><a href="' . route('user.messages.simple_index') . '">Simple Messages</a></li>';
        $html .= '</ul>';

        return $html;
    }
}
