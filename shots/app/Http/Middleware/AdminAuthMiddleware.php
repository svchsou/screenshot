<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }
        return $next($request);
    }
}

