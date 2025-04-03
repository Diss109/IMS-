<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\UserDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Redirect authenticated users based on their role
Route::get('/dashboard', function () {
    if (auth()->check()) {
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('user.dashboard');
    }
    return redirect()->route('login');
})->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Public complaint routes (no authentication required)
Route::get('complaints/public/create', [ComplaintController::class, 'createPublic'])->name('complaints.create-public');
Route::post('complaints/public', [ComplaintController::class, 'storePublic'])->name('complaints.store-public');

// User dashboard routes
Route::middleware(['auth'])->group(function () {
    Route::get('/user/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
});

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('complaints', App\Http\Controllers\Admin\ComplaintController::class);
    Route::resource('service-providers', App\Http\Controllers\Admin\ServiceProviderController::class);
    Route::resource('transporters', App\Http\Controllers\Admin\TransporterController::class);
    Route::resource('evaluations', App\Http\Controllers\Admin\EvaluationController::class);
    Route::resource('kpis', App\Http\Controllers\Admin\KpiController::class);
});

require __DIR__.'/auth.php';
