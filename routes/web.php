<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ComplaintController as AdminComplaintController;
use App\Http\Controllers\Admin\TransporterController;
use App\Http\Controllers\Admin\ServiceProviderController;
use App\Http\Controllers\Admin\KpiController;
use App\Http\Controllers\Admin\EvaluationController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\UserDashboardController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Notification;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

// Public complaint routes
Route::get('/complaints/create', [ComplaintController::class, 'createPublic'])->name('complaints.create.public');
Route::post('/complaints', [ComplaintController::class, 'storePublic'])->name('complaints.store.public');

// Chatbot réclamation (public, no login)
Route::get('/reclamation-chatbot', [ComplaintController::class, 'chatbotForm'])->name('reclamation.chatbot');
Route::post('/reclamation-chatbot', [ComplaintController::class, 'chatbotStore'])->name('reclamation.chatbot.store');

// Test route for debugging
Route::get('/test-complaint-insert', function() {
    try {
        $id = DB::table('complaints')->insertGetId([
            'company_name' => 'Test Company',
            'first_name' => 'Test User',
            'last_name' => 'Test Lastname', // Added required last_name field
            'email' => 'test@example.com',
            'complaint_type' => 'retard_livraison',
            'description' => 'Test complaint for debugging',
            'urgency_level' => 'high',
            'status' => 'en_attente',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        return 'Test complaint created with ID: ' . $id;
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});

// Authentication Routes
Route::middleware(['web'])->group(function () {
    Route::get('/dashboard', function () {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        if ($user->role === User::ROLE_ADMIN) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('user.dashboard');
    })->name('dashboard');

    // User dashboard routes
    Route::middleware(['auth'])->group(function () {
        Route::get('/user/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
        Route::get('/user/complaints', [UserDashboardController::class, 'complaints'])->name('user.complaints');
        Route::get('/user/complaints/{complaint}', [UserDashboardController::class, 'show'])->name('user.complaints.show');
        Route::put('/user/complaints/{complaint}', [UserDashboardController::class, 'update'])->name('user.complaints.update');
    });

    // Admin routes
    Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::resource('complaints', AdminComplaintController::class);
        Route::resource('transporters', TransporterController::class);
        Route::resource('service-providers', ServiceProviderController::class);
        Route::resource('kpis', KpiController::class);
        Route::resource('evaluations', EvaluationController::class);
        Route::resource('users', UserController::class);
        Route::get('evaluator-permissions', [\App\Http\Controllers\Admin\EvaluatorPermissionController::class, 'index'])->name('evaluator_permissions.index');
        Route::post('evaluator-permissions', [\App\Http\Controllers\Admin\EvaluatorPermissionController::class, 'store'])->name('evaluator_permissions.store');

        // Notifications
        Route::post('/notifications/delete', [App\Http\Controllers\NotificationController::class, 'destroy'])->middleware(['auth']);
        Route::post('/notifications/delete-all', [App\Http\Controllers\NotificationController::class, 'destroyAll'])->middleware(['auth']);

        // Notification endpoints
        Route::post('/notifications/mark-all-read', function(Illuminate\Http\Request $request) {
            $user = Auth::user();
            if (!$user || !$user->isAdmin()) {
                return response()->json(['success' => false], 403);
            }
            Notification::where('user_id', $user->id)->where('is_read', false)->update(['is_read' => true]);
            return response()->json(['success' => true]);
        });
        Route::get('/notifications/latest', function(Illuminate\Http\Request $request) {
            $user = Auth::user();
            if (!$user || !$user->isAdmin()) {
                return response()->json(['notifications' => []]);
            }
            $notifications = Notification::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
            return response()->json(['notifications' => $notifications]);
        });
        Route::post('/notifications/mark-read', function(Illuminate\Http\Request $request) {
            $user = Auth::user();
            $id = $request->input('id');
            if (!$user || !$user->isAdmin() || !$id) {
                return response()->json(['success' => false, 'message' => 'Unauthorized or invalid ID'], 403);
            }
            Notification::where('user_id', $user->id)
                ->where('id', $id)
                ->update(['is_read' => true]);
            return response()->json(['success' => true]);
        });
        Route::get('/notifications/unread-count', function(Illuminate\Http\Request $request) {
            $user = Auth::user();
            if (!$user || !$user->isAdmin()) {
                return response()->json(['count' => 0]);
            }
            $count = Notification::where('user_id', $user->id)->where('is_read', false)->count();
            return response()->json(['count' => $count]);
        });
    });
});

require __DIR__.'/auth.php';
