<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        // Check if user is not authenticated
        if (!Auth::check()) {
            // Clear any lingering session data
            session()->flush();
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        $user = $request->user();

        // Check if user exists and has correct role
        if (!$user || $user->role !== $role) {
            // If user role doesn't match, logout and redirect
            if ($user && $user->role !== $role) {
                Auth::logout();
                session()->flush();
                return redirect()->route('login')->with('error', 'Access denied. Please login with the correct account.');
            }

            abort(403, 'Unauthorized action.');
        }

        // Verify session integrity
        if (!session()->has('auth_verified') || session('user_role') !== $user->role) {
            session()->put('auth_verified', true);
            session()->put('user_role', $user->role);
        }

        return $next($request);
    }
}
