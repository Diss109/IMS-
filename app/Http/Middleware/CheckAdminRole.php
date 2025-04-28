<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class CheckAdminRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if the user is authenticated and is an admin
        if (!Auth::check() || Auth::user()->role !== User::ROLE_ADMIN) {
            return redirect()->route('user.dashboard')->with('error', 'Accès non autorisé');
        }

        return $next($request);
    }
}
