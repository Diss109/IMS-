<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // If user is admin, they can access everything
        if ($user->role === User::ROLE_ADMIN) {
            return $next($request);
        }

        // If no roles are required, proceed
        if (empty($roles)) {
            return $next($request);
        }

        // Convert roles string to array if it's a single string
        if (is_string($roles[0]) && str_contains($roles[0], ',')) {
            $roles = explode(',', $roles[0]);
        }

        // Check if user has any of the required roles
        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        // If user doesn't have required role, redirect with error message
        return redirect()
            ->route('dashboard')
            ->with('error', 'Vous n\'avez pas la permission d\'accéder à cette zone.');
    }
}
