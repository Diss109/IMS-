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

        // If user is admin, redirect to admin dashboard
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

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

    public function complaints()
    {
        $user = Auth::user();
        $complaints = Complaint::where('assigned_to', $user->id)
            ->latest()
            ->paginate(10);

        return view('user.complaints.index', compact('complaints'));
    }

    public function show(Complaint $complaint)
    {
        $user = Auth::user();
        if ($complaint->assigned_to !== $user->id && $user->role !== 'admin') {
            return redirect()->route('user.dashboard')
                ->with('error', 'Vous n\'avez pas la permission de voir cette réclamation.');
        }

        return view('user.complaints.show', compact('complaint'));
    }

    public function update(Request $request, Complaint $complaint)
    {
        $user = Auth::user();
        if ($complaint->assigned_to !== $user->id && $user->role !== 'admin') {
            return redirect()->route('user.dashboard')
                ->with('error', 'Vous n\'avez pas la permission de modifier cette réclamation.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:en_attente,résolu,non_résolu'],
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $complaint->update($validated);

        return redirect()->route('user.complaints.show', $complaint)
            ->with('success', 'Réclamation mise à jour avec succès.');
    }
}
