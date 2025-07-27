<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionController extends Controller
{
    /**
     * Check session status
     */
    public function checkSession(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['authenticated' => false], 401);
        }

        // Verify session integrity
        $user = Auth::user();
        if (!session()->has('auth_verified') || session('user_role') !== $user->role) {
            Auth::logout();
            session()->flush();
            return response()->json(['authenticated' => false], 401);
        }

        // Calculate time remaining (Laravel default session lifetime is 120 minutes)
        $loginTime = session('login_time');
        $sessionLifetime = config('session.lifetime') * 60; // Convert minutes to seconds
        $timeRemaining = $sessionLifetime - (now()->diffInSeconds($loginTime));

        return response()->json([
            'authenticated' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'role' => $user->role
            ],
            'timeRemaining' => max(0, $timeRemaining),
            'sessionLifetime' => $sessionLifetime
        ]);
    }

    /**
     * Extend session
     */
    public function extendSession(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false], 401);
        }

        // Update session timestamp
        session()->put('login_time', now());

        return response()->json(['success' => true]);
    }

    /**
     * Clear expired sessions
     */
    public function clearSession(Request $request)
    {
        Auth::logout();
        session()->flush();

        return response()->json(['success' => true]);
    }
}
