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
use Illuminate\Support\Facades\DB;
use App\Services\PredictionService;


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

// Assistant de réclamation (public, no login)
Route::get('/reclamation-assistant', [ComplaintController::class, 'assistantForm'])->name('reclamation.assistant');
Route::post('/reclamation-assistant', [ComplaintController::class, 'assistantStore'])->name('reclamation.assistant.store');

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

// Test route for predictions
Route::get('/test-predictions', function() {
    try {
        $service = new PredictionService();
        $providers = \App\Models\ServiceProvider::has('evaluations', '>=', 2)
            ->withCount('evaluations')
            ->with('predictions')
            ->take(5)
            ->get();

        $results = [];
        foreach ($providers as $provider) {
            $trend = $service->getTrendInfo($provider);
            $latestPrediction = $provider->predictions()->latest('prediction_date')->first();

            $results[] = [
                'id' => $provider->id,
                'name' => $provider->name,
                'evaluations_count' => $provider->evaluations_count,
                'trend' => $trend,
                'prediction' => $latestPrediction ? [
                    'score' => $latestPrediction->predicted_score,
                    'confidence' => $latestPrediction->confidence_level,
                    'factors' => $latestPrediction->factors
                ] : null
            ];
        }

        return response()->json([
            'message' => 'Showing prediction data for first 5 providers with 2+ evaluations',
            'results' => $results
        ], 200, [], JSON_PRETTY_PRINT);

    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});

// Test route for debugging AJAX
Route::post('/debug/ajax-test', [App\Http\Controllers\DebugController::class, 'testAjax'])->name('debug.ajaxTest');
Route::get('/debug/ajax-test', [App\Http\Controllers\DebugController::class, 'testAjax'])->name('debug.ajaxTest.get');
Route::get('/debug/ajax-view', function() {
    return view('debug.ajax-test');
})->name('debug.ajaxView');

// Authentication Routes
Route::middleware(['web'])->group(function () {
    // Unified dashboard route: redirects users based on role
    Route::get('/dashboard', function () {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        $user = Auth::user();
        // If admin, go to admin dashboard
        if ($user->role === User::ROLE_ADMIN) {
            return redirect()->route('admin.dashboard');
        }
        // Otherwise, go to user dashboard
        return redirect()->route('user.dashboard');
    })->name('dashboard');

    // User dashboard routes
    Route::middleware(['auth'])->group(function () {
        Route::get('/user/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
        // User complaints routes (new)
        Route::get('/user/complaints', [App\Http\Controllers\User\ComplaintController::class, 'index'])->name('user.complaints.index');
        Route::get('/user/complaints/{id}', [App\Http\Controllers\User\ComplaintController::class, 'show'])->name('user.complaints.show');
        Route::put('/user/complaints/{id}/status', [App\Http\Controllers\User\ComplaintController::class, 'updateStatus'])->name('user.complaints.updateStatus');
        // User notification routes
        Route::get('/user/notifications', [App\Http\Controllers\User\NotificationController::class, 'index'])->name('user.notifications.index');
        Route::delete('/user/notifications/{id}', [App\Http\Controllers\User\NotificationController::class, 'destroy'])->name('user.notifications.destroy');
        Route::post('/user/notifications/mark-all-read', [App\Http\Controllers\User\NotificationController::class, 'markAllRead'])->name('user.notifications.markAllRead');

        // User notification AJAX endpoints
        Route::get('/user/notifications/latest', [App\Http\Controllers\User\NotificationController::class, 'getLatest'])->name('user.notifications.latest');
        Route::get('/user/notifications/unread-count', [App\Http\Controllers\User\NotificationController::class, 'getUnreadCount'])->name('user.notifications.unreadCount');
        Route::post('/user/notifications/mark-read', [App\Http\Controllers\User\NotificationController::class, 'markRead'])->name('user.notifications.markRead');
        Route::post('/user/notifications/delete', [App\Http\Controllers\User\NotificationController::class, 'ajaxDestroy'])->name('user.notifications.ajaxDestroy');
        Route::post('/user/notifications/delete-all', [App\Http\Controllers\User\NotificationController::class, 'destroyAll'])->name('user.notifications.destroyAll');

        // Messages routes
        Route::get('/user/messages', [App\Http\Controllers\User\MessageController::class, 'index'])->name('user.messages.index');
        Route::get('/user/messages/unread-count', [App\Http\Controllers\User\MessageController::class, 'getUnreadCount'])->name('user.messages.unreadCount');
        Route::get('/user/messages/debug', [App\Http\Controllers\User\MessageController::class, 'debug'])->name('user.messages.debug');
        Route::get('/user/messages/test-form/{user}', [App\Http\Controllers\User\MessageController::class, 'testForm'])->name('user.messages.test_form');
        Route::get('/user/messages/{user}', [App\Http\Controllers\User\MessageController::class, 'conversation'])->name('user.messages.conversation');
        Route::post('/user/messages/{user}', [App\Http\Controllers\User\MessageController::class, 'sendMessage'])->name('user.messages.send');
        Route::get('/user/messages/{user}/new', [App\Http\Controllers\User\MessageController::class, 'getNewMessages'])->name('user.messages.new');
        Route::put('/user/messages/{id}', [App\Http\Controllers\User\MessageController::class, 'updateMessage'])->name('user.messages.update');
        Route::delete('/user/messages/{id}', [App\Http\Controllers\User\MessageController::class, 'deleteMessage'])->name('user.messages.delete');
    });

    // Admin routes - protected at the controller level
    Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::resource('complaints', AdminComplaintController::class);
        Route::resource('transporters', TransporterController::class);
        Route::resource('service-providers', ServiceProviderController::class);
        // KPI Dashboard with Google Charts
        Route::get('/kpis', [KpiController::class, 'index'])->name('kpis.index');
        Route::get('/kpis/charts/trend', [\App\Http\Controllers\Admin\GoogleChartController::class, 'trendChart'])->name('kpis.charts.trend');
        Route::get('/kpis/charts/type', [\App\Http\Controllers\Admin\GoogleChartController::class, 'typeChart'])->name('kpis.charts.type');
        Route::get('/kpis/charts/status', [\App\Http\Controllers\Admin\GoogleChartController::class, 'statusChart'])->name('kpis.charts.status');
        Route::get('/kpis/charts/urgency', [\App\Http\Controllers\Admin\GoogleChartController::class, 'urgencyChart'])->name('kpis.charts.urgency');
        // New chart routes
        Route::get('/kpis/charts/provider-types', [\App\Http\Controllers\Admin\GoogleChartController::class, 'providerTypesChart'])->name('kpis.charts.provider_types');
        Route::get('/kpis/charts/evaluations-trend', [\App\Http\Controllers\Admin\GoogleChartController::class, 'evaluationsTrendChart'])->name('kpis.charts.evaluations_trend');
        Route::get('/kpis/charts/user-roles', [\App\Http\Controllers\Admin\GoogleChartController::class, 'userRolesChart'])->name('kpis.charts.user_roles');

        Route::resource('kpis', KpiController::class)->except(['index']);
        Route::get('service-providers/{id}/evaluations/create', [EvaluationController::class, 'create'])->name('evaluations.create');
        Route::resource('evaluations', EvaluationController::class)->except(['create']);
        Route::resource('users', UserController::class);
        Route::get('evaluator-permissions', [\App\Http\Controllers\Admin\EvaluatorPermissionController::class, 'index'])->name('evaluator_permissions.index');
        Route::post('evaluator-permissions', [\App\Http\Controllers\Admin\EvaluatorPermissionController::class, 'store'])->name('evaluator_permissions.store');

        // Predictions routes
        Route::get('predictions', [\App\Http\Controllers\Admin\PredictionController::class, 'index'])->name('predictions.index');
        Route::get('predictions/{id}', [\App\Http\Controllers\Admin\PredictionController::class, 'show'])->name('predictions.show');
        Route::post('predictions/generate', [\App\Http\Controllers\Admin\PredictionController::class, 'generateAll'])->name('predictions.generate');
        Route::post('predictions/{id}/generate', [\App\Http\Controllers\Admin\PredictionController::class, 'generateForProvider'])->name('predictions.generate.provider');
        Route::get('predictions/{id}/debug-chart', [\App\Http\Controllers\Admin\PredictionController::class, 'debugChartData'])->name('predictions.debug.chart');

        // Notifications
        Route::post('/notifications/delete', [App\Http\Controllers\NotificationController::class, 'destroy'])->middleware(['auth']);
        Route::post('/notifications/delete-all', [App\Http\Controllers\NotificationController::class, 'destroyAll'])->middleware(['auth']);

        // Notification endpoints
        Route::get('/notifications/latest', function(Illuminate\Http\Request $request) {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['notifications' => []]);
            }

            $notifications = Notification::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(['id', 'message', 'type', 'is_read', 'created_at']);

            return response()->json(['notifications' => $notifications]);
        });

        Route::get('/notifications/unread-count', function(Illuminate\Http\Request $request) {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['count' => 0]);
            }

            $count = Notification::where('user_id', $user->id)
                ->where('is_read', false)
                ->count();

            return response()->json(['count' => $count]);
        });

        Route::post('/notifications/mark-all-read', function(Illuminate\Http\Request $request) {
            $user = Auth::user();
            if (!$user || !$user->isAdmin()) {
                return response()->json(['success' => false], 403);
            }
            Notification::where('user_id', $user->id)->where('is_read', false)->update(['is_read' => true]);
            return response()->json(['success' => true]);
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
    });
});

require __DIR__.'/auth.php';
