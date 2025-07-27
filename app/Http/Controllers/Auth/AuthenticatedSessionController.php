<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        // Clear any existing session data when showing login form
        if (session()->has('auth_verified')) {
            session()->flush();
        }

        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Clear any existing session data before authentication
        session()->flush();

        $request->authenticate();

        $request->session()->regenerate();

        // Set authentication flags
        session()->put('auth_verified', true);
        session()->put('user_role', Auth::user()->role);
        session()->put('login_time', now());

        // Redirect based on user role
        $user = Auth::user();
        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'manager':
                return redirect()->route('manager.dashboard');
            case 'surveyor':
                return redirect()->route('surveyor.dashboard');
            default:
                return redirect()->intended(RouteServiceProvider::HOME);
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Get current user role before logout for cleanup
        $userRole = Auth::user() ? Auth::user()->role : null;

        Auth::guard('web')->logout();

        // Complete session cleanup
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $request->session()->flush();

        // Clear any cached data
        if ($userRole) {
            session()->forget(['auth_verified', 'user_role', 'login_time']);
        }

        return redirect('/')->with('success', 'You have been logged out successfully.');
    }
}
