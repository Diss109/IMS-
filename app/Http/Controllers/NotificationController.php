<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class NotificationController extends Controller
{
    // Delete a single notification (by id)
    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:notifications,id',
        ]);
        $notification = Notification::find($request->id);
        if ($notification && $notification->user_id === Auth::id()) {
            $notification->delete();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 403);
    }

    // Delete all notifications for the current user
    public function destroyAll(Request $request)
    {
        Notification::where('user_id', Auth::id())->delete();
        return response()->json(['success' => true]);
    }
}
