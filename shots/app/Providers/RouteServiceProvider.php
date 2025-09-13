<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Define your route model bindings, pattern filters, etc.
     */
    public function boot(): void
    {
        // Define named rate limiters used in routes
        RateLimiter::for('uploads', function (Request $request) {
            return [
                Limit::perMinute((int) env('RATE_UPLOADS_PER_MIN', 5))->by($request->ip()),
            ];
        });

        RateLimiter::for('views', function (Request $request) {
            return [
                Limit::perMinute((int) env('RATE_VIEWS_PER_MIN', 120))->by($request->ip()),
            ];
        });

        // Load application routes
        Route::middleware('web')
            ->group(base_path('routes/web.php'));
    }
}



