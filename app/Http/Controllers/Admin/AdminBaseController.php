<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AdminBaseController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // This will run before any action in admin controllers
        $this->checkAdminAccess();
    }
    
    /**
     * Check if the current user has admin access
     * 
     * @return \Illuminate\Http\RedirectResponse|null
     */
    protected function checkAdminAccess()
    {
        if (Auth::check() && Auth::user()->role === User::ROLE_ADMIN) {
            return null; // Continue execution
        }
        
        // Redirect to user dashboard if not authenticated or not admin
        return redirect()->route('user.dashboard')
            ->with('error', 'Vous n\'avez pas la permission d\'accéder à cette zone.');
    }
}
