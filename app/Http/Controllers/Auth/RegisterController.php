<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class RegisterController extends Controller
{
    public function __construct(protected AuthService $authService)
    {
    }

    public function showRegistrationForm()
    {
        if (Auth::check()) {
            return Auth::user()->isAdmin()
                ? redirect()->route('admin.dashboard')
                : redirect()->route('member.dashboard');
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        $throttleKey = 'register|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'email' => "Terlalu banyak percobaan pendaftaran dari IP ini. Silakan coba lagi dalam {$seconds} detik.",
            ])->withInput();
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);

        RateLimiter::hit($throttleKey, 60);

        $user = $this->authService->registerMember(
            data: $validated,
            photo: $request->file('photo')
        );

        RateLimiter::clear($throttleKey);

        return redirect()->route('member.dashboard')->with('success', "Selamat datang, {$user->name}! Akun dan QR Code permanen Anda ({$user->member_id}) telah dibuat.");
    }
}
