<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Message;

class SimpleMessageController extends Controller
{
    /**
     * Display minimal messages page
     */
    public function index()
    {
        try {
            // Get all users except admin and current user
            $users = User::where('id', '!=', Auth::id())
                ->where('role', '!=', User::ROLE_ADMIN)
                ->get();

            return view('user.messages.simple_direct', [
                'users' => $users,
                'error' => null
            ]);
        } catch (\Exception $e) {
            return view('user.messages.simple_direct', [
                'users' => [],
                'error' => $e->getMessage()
            ]);
        }
    }
}
