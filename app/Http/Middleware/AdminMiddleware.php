<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->role === User::ROLE_ADMIN) {
            return $next($request);
        }
        
        return redirect()->route('user.dashboard')->with('error', 'Accès non autorisé');
    }
}
