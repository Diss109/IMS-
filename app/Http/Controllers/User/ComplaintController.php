<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Complaint;
use Illuminate\Support\Facades\Auth;

class ComplaintController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Complaint::where('assigned_to', $user->id);
        // Filtering
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('id', $search)
                  ->orWhere('title', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%")
                  ->orWhere('company_name', 'like', "%$search%")
                  ->orWhere('client_name', 'like', "%$search%")
                  ->orWhere('status', 'like', "%$search%")
                  ;
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        $complaints = $query->latest()->paginate(10)->appends($request->query());
        $unreadNotificationsCount = \App\Models\Notification::where('user_id', $user->id)->where('is_read', false)->count();
        return view('user.complaints.index', compact('complaints', 'unreadNotificationsCount'));
    }


    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string',
        ]);
        $complaint = Complaint::where('assigned_to', Auth::id())->findOrFail($id);
        $complaint->status = $request->status;
        $complaint->save();
        // Notify all admins about the status change
        $admins = \App\Models\User::where('role', \App\Models\User::ROLE_ADMIN)->get();
        foreach ($admins as $admin) {
            \App\Models\Notification::create([
                'user_id' => $admin->id,
                'type' => 'complaint_status',
                'message' => 'La réclamation #' . $complaint->id . ' a changé de statut: ' . $complaint->status,
                'is_read' => false,
                'related_id' => $complaint->id
            ]);
        }
        return redirect()->route('user.complaints.index')->with('success', 'Statut mis à jour avec succès.');
    }

    public function show($id)
    {
        $complaint = Complaint::where('assigned_to', Auth::id())->findOrFail($id);
        $unreadNotificationsCount = \App\Models\Notification::where('user_id', \Illuminate\Support\Facades\Auth::id())->where('is_read', false)->count();
        return view('user.complaints.show', compact('complaint', 'unreadNotificationsCount'));
    }
}
