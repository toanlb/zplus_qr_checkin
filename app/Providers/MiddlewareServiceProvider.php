<?php

namespace App\Providers;

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\CheckRole;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class MiddlewareServiceProvider extends ServiceProvider
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
        // Register the admin middleware for routes
        Route::aliasMiddleware('admin', AdminMiddleware::class);
        
        // Register the role middleware for routes
        Route::aliasMiddleware('role', CheckRole::class);
    }
}
