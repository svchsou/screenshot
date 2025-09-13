<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminStorageMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (env('APP_ENV') !== 'production') {
            return $next($request);
        }
        $token = env('ADMIN_STORAGE_TOKEN');
        if ($token && $request->header('X-Admin-Token') === $token) {
            return $next($request);
        }
        abort(403);
    }
}
