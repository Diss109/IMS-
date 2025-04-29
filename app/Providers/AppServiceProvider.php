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
        // Share notifications with both navigation and admin layouts
        \View::composer(['layouts.navigation', 'layouts.admin'], function ($view) {
            if (auth()->check()) {
                $latestNotifications = \App\Models\Notification::where('user_id', auth()->id())
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get();
                $unreadNotificationsCount = \App\Models\Notification::where('user_id', auth()->id())
                    ->where('is_read', false)
                    ->count();
                $view->with('latestNotifications', $latestNotifications)
                    ->with('unreadNotificationsCount', $unreadNotificationsCount);
            }
        });

        \View::composer('*', function ($view) {
            $user = auth()->user();
            if ($user && $user->role === 'admin') {
                $unreadNotificationsCount = \App\Models\Notification::where('is_read', false)->count();
                $view->with('unreadNotificationsCount', $unreadNotificationsCount);
            }
        });
    }
}
