<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
        return view('user.notifications', compact('notifications'));
    }

    public function destroy($id)
    {
        $notification = Notification::where('user_id', Auth::id())->findOrFail($id);
        $notification->delete();
        return redirect()->route('user.notifications.index')->with('success', 'Notification supprimée.');
    }

    public function markAllRead()
    {
        Notification::where('user_id', Auth::id())->where('is_read', false)->update(['is_read' => true]);
        return back()->with('success', 'Toutes les notifications ont été marquées comme lues.');
    }
}
