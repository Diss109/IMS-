<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseAdminController;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComplaintController extends BaseAdminController
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Complaint::query();

        // Filter by assigned user if not admin
        if (!$user->isAdmin()) {
            $query->where('assigned_to', $user->id);
        }

        // Apply filters from request
        if ($request->filled('type')) {
            $query->where('complaint_type', $request->input('type'));
        }
        if ($request->filled('urgency')) {
            $query->where('urgency_level', $request->input('urgency'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        // Period filter (date range)
        if ($request->filled('period')) {
            $period = $request->input('period');
            $now = now();
            switch ($period) {
                case 'week':
                    $query->where('created_at', '>=', $now->copy()->startOfWeek());
                    break;
                case 'month':
                    $query->where('created_at', '>=', $now->copy()->startOfMonth());
                    break;
                case 'year':
                    $query->where('created_at', '>=', $now->copy()->startOfYear());
                    break;
                default:
                    // 'total' or any other value: no filter
                    break;
            }
        }

        // Add date filter if present
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->input('date'));
        }

        // Search by complaint id or name
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('id', $search)
                  ->orWhere('first_name', 'like', "%$search%")
                  ->orWhere('last_name', 'like', "%$search%")
                  ->orWhere('company_name', 'like', "%$search%")
                  ;
            });
        }
        $complaints = $query->latest()->paginate(10)->appends($request->query());
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

        $oldAssignedTo = $complaint->assigned_to;
        if ($request->has('assigned_to') && $user->isAdmin()) {
            $complaint->assigned_to = $validated['assigned_to'];
        }

        $oldStatus = $complaint->status;
        $complaint->update($validated);
        // Notify the user if they are newly assigned a complaint
        if (isset($validated['assigned_to']) && $oldAssignedTo != $validated['assigned_to'] && $validated['assigned_to']) {
            \Log::info('Attempting to create notification for assigned complaint', [
                'user_id' => $validated['assigned_to'],
                'complaint_id' => $complaint->id,
                'old_assigned' => $oldAssignedTo,
                'new_assigned' => $validated['assigned_to']
            ]);
            $notification = \App\Models\Notification::create([
                'user_id' => $validated['assigned_to'],
                'type' => 'complaint_assignment',
                'message' => 'Vous avez été assigné à la réclamation #' . $complaint->id,
                'is_read' => false,
                'related_id' => $complaint->id
            ]);
            \Log::info('Notification created', ['notification' => $notification]);
        }
        // Create a notification for ALL admins if status changed
        if ($oldStatus !== $validated['status']) {
            $admins = \App\Models\User::where('role', \App\Models\User::ROLE_ADMIN)->get();
            foreach ($admins as $admin) {
                $notification = \App\Models\Notification::create([
                    'user_id' => $admin->id,
                    'type' => 'complaint_status',
                    'message' => 'La réclamation #' . $complaint->id . ' a changé de statut: ' . $validated['status'],
                    'is_read' => false,
                    'related_id' => $complaint->id
                ]);
                \Log::info('Status change notification created', ['notification' => $notification]);
            }
        }

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
