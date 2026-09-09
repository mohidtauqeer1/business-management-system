<?php

namespace App\Http\Controllers;

use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt([
            'email'    => $credentials['email'],
            'password' => $credentials['password'],
            'status'   => 'active',
        ])) {
            return back()
                ->withErrors(['email' => 'Invalid email or password.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        // Log successful login
        ActivityLogger::login(
            auth()->user()->name . ' (' . auth()->user()->role . ') logged in'
        );

        return redirect()->intended('/dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        // Log before clearing session
        ActivityLogger::logout(
            auth()->user()?->name . ' logged out'
        );

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', 'Logged out successfully.');
    }
}