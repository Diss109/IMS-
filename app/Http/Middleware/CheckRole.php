<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

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
        if (!$request->user()) {
            return redirect()->route('login');
        }

        $userRole = $request->user()->role;

        if (empty($roles) || in_array($userRole, $roles)) {
            return $next($request);
        }

        if ($userRole === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('user.dashboard')
            ->with('error', 'You do not have permission to access this area.');
    }
}
