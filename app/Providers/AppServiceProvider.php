<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

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
        // Dynamically register gates for menu permissions
        Gate::before(function ($user) {
            return ($user->role ?? 'user') === 'admin' ? true : null;
        });

        $menuKeys = config('menu.keys', []);
        foreach ($menuKeys as $key) {
            Gate::define($key, function ($user) use ($key) {
                return $user->hasMenu($key);
            });
        }
    }
}
