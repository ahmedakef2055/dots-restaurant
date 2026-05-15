<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    private const MAX_LOGIN_ATTEMPTS = 5;

    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:60'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ]);

        $throttleKey = $this->throttleKey($request);

        if (RateLimiter::tooManyAttempts($throttleKey, self::MAX_LOGIN_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'username' => __('messages.auth.throttle', ['seconds' => $seconds]),
            ]);
        }

        $credentials = [
            'username' => $validated['username'],
            'password' => $validated['password'],
        ];

        if (! Auth::attempt($credentials, (bool) ($validated['remember'] ?? false))) {
            RateLimiter::hit($throttleKey, 60);

            throw ValidationException::withMessages([
                'username' => __('messages.auth.invalid_credentials'),
            ]);
        }

        RateLimiter::clear($throttleKey);

        $request->session()->regenerate();

        $user = Auth::user();

        if ($user instanceof User && Schema::hasColumn('users', 'is_active') && ! (bool) $user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'username' => __('messages.auth.account_disabled'),
            ]);
        }

        if ($user instanceof User && $user->employee && $user->employee->status !== 'active') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'username' => __('messages.auth.employee_inactive'),
            ]);
        }

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function throttleKey(Request $request): string
    {
        return mb_strtolower((string) $request->input('username')) . '|' . $request->ip();
    }
}
