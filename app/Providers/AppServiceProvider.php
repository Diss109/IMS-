<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \View::composer('*', function ($view) {
            $user = auth()->user();
            if ($user && $user->role === 'admin') {
                $unreadNotificationsCount = \App\Models\Notification::where('is_read', false)->count();
                $view->with('unreadNotificationsCount', $unreadNotificationsCount);
            }
        });
    }
}
