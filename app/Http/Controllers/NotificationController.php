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
        // Accept JSON
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

    // Delete all notifications for the current user
    public function destroyAll(Request $request)
    {
        Notification::where('user_id', Auth::id())->delete();
        return response()->json(['success' => true]);
    }
}
