<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class HandleExpiredSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated but session is expired
        if (Auth::check() && !session()->has('auth_verified')) {
            // Clear all session data
            session()->flush();
            Auth::logout();

            // Redirect to login with message
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Session expired'], 401);
            }

            return redirect()->route('login')->with('error', 'Your session has expired. Please login again.');
        }

        // Set session verification flag for authenticated users
        if (Auth::check() && !session()->has('auth_verified')) {
            session()->put('auth_verified', true);
            session()->put('user_role', Auth::user()->role);
        }

        return $next($request);
    }
}
