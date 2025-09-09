<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class RateLimiterServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        RateLimiter::for('comments', function (Request $request) {
            $user = $request->user('sanctum');
            $key = ($user?->id ?? $request->ip()).'|comments';

            return Limit::perMinute(10)->by($key);
        });

        RateLimiter::for('post-writes', function (Request $request) {
            $user = $request->user('sanctum');
            if ($user && $user->role === \App\Enums\Role::ADMIN->value) {
                return Limit::none();
            }
            $key = ($user?->id ?? $request->ip()).'|post-writes';

            return Limit::perMinute(5)->by($key);
        });
    }
}
