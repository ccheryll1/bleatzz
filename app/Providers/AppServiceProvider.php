<?php

namespace App\Providers;

use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        // Override RedirectIfAuthenticated default redirect (laravel default = /dashboard)
        // Biar user yang sudah login kalo akses /login /register → ke home
        RedirectIfAuthenticated::redirectUsing(function () {
            return route('home');
        });

        // Register Breeze anonymous components so <x-component-name> still works
        // even though the components folder is nested under views/breeze/
        Blade::anonymousComponentPath(resource_path('views/breeze/components'));

        // Register Breeze layout components so <x-app-layout> and <x-guest-layout> still work
        Blade::component('breeze.layouts.app', 'app-layout');
        Blade::component('breeze.layouts.guest', 'guest-layout');

        if (env('APP_ENV') === 'production') {
        URL::forceScheme('https');
    }
    }
}
