<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $complaints = Complaint::where('assigned_to', $user->id)
            ->latest()
            ->paginate(10);

        $totalComplaints = $complaints->total();
        $resolvedComplaints = Complaint::where('assigned_to', $user->id)
            ->where('status', 'résolu')
            ->count();
        $pendingComplaints = Complaint::where('assigned_to', $user->id)
            ->where('status', 'en_attente')
            ->count();
        $unsolvedComplaints = Complaint::where('assigned_to', $user->id)
            ->where('status', 'non_résolu')
            ->count();

        return view('user.dashboard', compact(
            'complaints',
            'totalComplaints',
            'resolvedComplaints',
            'pendingComplaints',
            'unsolvedComplaints',
            'user'
        ));
    }
}
