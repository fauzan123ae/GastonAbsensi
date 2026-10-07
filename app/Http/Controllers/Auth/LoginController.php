<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function __construct(protected AuthService $authService)
    {
    }

    public function showLoginForm()
    {
        if (Auth::check()) {
            return Auth::user()->isAdmin()
                ? redirect()->route('admin.dashboard')
                : redirect()->route('member.dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $throttleKey = Str::transliterate(Str::lower($credentials['email']) . '|' . $request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'email' => "Terlalu banyak percobaan login. Silakan coba lagi dalam {$seconds} detik.",
            ])->onlyInput('email');
        }

        $result = $this->authService->attemptLogin(
            identifier: $credentials['email'],
            password: $credentials['password'],
            remember: $request->boolean('remember')
        );

        if (!$result['success']) {
            RateLimiter::hit($throttleKey, 60);

            return back()->withErrors([
                $result['error_field'] => $result['error_message'],
            ])->onlyInput('email');
        }

        RateLimiter::clear($throttleKey);

        return $result['user']->isAdmin()
            ? redirect()->intended(route('admin.dashboard'))
            : redirect()->intended(route('member.dashboard'));
    }

    public function logout(Request $request)
    {
        $this->authService->logout();

        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }
}
