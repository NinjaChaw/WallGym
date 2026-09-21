<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function create(): View
    {
        return view('admin.Auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        if (is_string($request->input('email'))) {
            $request->merge(['email' => strtolower(trim($request->input('email')))]);
        }
        $data = $request->validate(['email' => ['required', 'email', 'max:150'], 'password' => ['required', 'string', 'max:1000']]);
        $key = 'admin-login:'.hash('sha256', $data['email'].'|'.$request->ip());
        $ipKey = 'admin-login-ip:'.$request->ip();
        if (RateLimiter::tooManyAttempts($key, 5) || RateLimiter::tooManyAttempts($ipKey, 30)) {
            throw ValidationException::withMessages(['email' => 'Too many login attempts. Please try again in a minute.']);
        }
        if (! Auth::guard('admin')->attempt($data)) {
            RateLimiter::hit($key, 60);
            RateLimiter::hit($ipKey, 60);
            throw ValidationException::withMessages(['email' => 'The provided credentials do not match our records.']);
        }
        RateLimiter::clear($key);
        $request->session()->regenerate();

        return redirect()->intended(url('/admin'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('admin.login')->with('status', 'You have been signed out.');
    }
}
