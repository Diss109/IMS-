<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\Request;

class BaseAdminController extends Controller
{
    /**
     * Create a new controller instance with admin role check.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (Auth::user()->role !== User::ROLE_ADMIN) {
                return redirect()->route('user.dashboard')
                    ->with('error', 'Accès non autorisé à la zone d\'administration');
            }
            
            return $next($request);
        });
    }
}
