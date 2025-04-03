<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComplaintController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = Complaint::latest();

        if (!$user->isAdmin()) {
            $query->where('assigned_to', $user->id);
        }

        $complaints = $query->paginate(15);
        $users = User::where('role', '!=', User::ROLE_ADMIN)->get();

        return view('admin.complaints.index', compact('complaints', 'users'));
    }

    public function show(Complaint $complaint)
    {
        $user = Auth::user();
        if (!$user->canViewComplaint($complaint)) {
            abort(403, 'Vous n\'êtes pas autorisé à voir cette réclamation.');
        }

        $users = User::where('role', '!=', User::ROLE_ADMIN)->get();
        return view('admin.complaints.show', compact('complaint', 'users'));
    }

    public function update(Request $request, Complaint $complaint)
    {
        $user = Auth::user();
        if (!$user->canViewComplaint($complaint)) {
            abort(403, 'Vous n\'êtes pas autorisé à modifier cette réclamation.');
        }

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:' . implode(',', Complaint::getStatuses())],
            'admin_notes' => 'nullable|string|max:1000',
            'assigned_to' => 'nullable|exists:users,id'
        ]);

        if ($request->has('assigned_to') && $user->isAdmin()) {
            $complaint->assigned_to = $validated['assigned_to'];
        }

        $complaint->update($validated);

        return redirect()
            ->route('admin.complaints.show', $complaint)
            ->with('success', 'La réclamation a été mise à jour avec succès.');
    }

    public function destroy(Complaint $complaint)
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403, 'Seul l\'administrateur peut supprimer les réclamations.');
        }

        $complaint->delete();

        return redirect()
            ->route('admin.complaints.index')
            ->with('success', 'La réclamation a été supprimée avec succès.');
    }
}
