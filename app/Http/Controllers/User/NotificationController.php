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
    
    // AJAX methods for user notifications
    
    public function getLatest()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        return response()->json(['notifications' => $notifications]);
    }
    
    public function getUnreadCount()
    {
        $count = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();
        return response()->json(['count' => $count]);
    }
    
    public function markRead(Request $request)
    {
        $id = $request->input('id');
        if (!$id) {
            return response()->json(['success' => false, 'message' => 'ID invalide'], 400);
        }
        
        Notification::where('user_id', Auth::id())
            ->where('id', $id)
            ->update(['is_read' => true]);
            
        return response()->json(['success' => true]);
    }
    
    public function ajaxDestroy(Request $request)
    {
        $id = $request->input('id');
        if (!$id || !is_numeric($id)) {
            return response()->json(['success' => false, 'error' => 'ID manquant ou invalide'], 400);
        }
        
        $notification = Notification::find($id);
        if ($notification && $notification->user_id === Auth::id()) {
            $notification->delete();
            return response()->json(['success' => true]);
        }
        
        return response()->json(['success' => false], 403);
    }
    
    public function destroyAll()
    {
        Notification::where('user_id', Auth::id())->delete();
        return response()->json(['success' => true]);
    }
}
