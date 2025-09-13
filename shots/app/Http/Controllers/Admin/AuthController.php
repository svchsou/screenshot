<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function loginForm(Request $request)
    {
        if ($request->session()->get('is_admin')) {
            return redirect()->to('/admin');
        }
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);
        $key = 'login:'.Str::lower($data['username']).'|'.$request->ip();
        if (RateLimiter::tooManyAttempts($key, (int) env('ADMIN_LOGIN_MAX_ATTEMPTS', 5))) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors(['username' => 'Too many attempts. Try again in '.$seconds.' seconds.'])->withInput();
        }
        $user = env('ADMIN_USERNAME', 'admin');
        $hash = env('ADMIN_PASSWORD_HASH');
        $plain = env('ADMIN_PASSWORD');
        $ok = false;
        if ($hash) {
            $ok = password_verify($data['password'], $hash) && $data['username'] === $user;
        } elseif ($plain) {
            $ok = hash_equals($plain, $data['password']) && $data['username'] === $user;
        }
        if ($ok) {
            RateLimiter::clear($key);
            $request->session()->put('is_admin', true);
            return Redirect::to('/admin');
        }
        RateLimiter::hit($key, (int) env('ADMIN_LOGIN_DECAY_SECONDS', 60));
        return back()->withErrors(['username' => 'Invalid credentials'])->withInput();
    }

    public function logout(Request $request)
    {
        $request->session()->forget('is_admin');
        return Redirect::to('/admin/login');
    }
}
