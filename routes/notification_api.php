<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Notification;

Route::get('/admin/notifications/unread-count', function(Request $request) {
    $count = Notification::where('user_id', 1)->where('is_read', false)->count();
    return response()->json(['count' => $count]);
});

Route::middleware(['auth', 'web'])->get('/admin/notifications/unread-count', function(Request $request) {
    $user = Auth::user();
    if (!$user || !$user->isAdmin()) {
        return response()->json(['count' => 0]);
    }
    $count = DB::table('notifications')
        ->where('user_id', $user->id)
        ->where('is_read', false)
        ->count();
    return response()->json(['count' => $count]);
});
