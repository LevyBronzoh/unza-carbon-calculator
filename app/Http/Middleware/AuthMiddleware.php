<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // 1. Check if user is authenticated
        if (Auth::guest()) {
            // 2. If not authenticated and trying to access protected route
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthenticated.'], 401);
            }

            // 3. Redirect to login with intended URL
            return redirect()->guest(route('login'))->with('error', 'Please login to access this page');
        }

        // 4. Check for specific user roles if needed (example)
        // if (!auth()->user()->isAdmin()) {
        //     abort(403, 'Unauthorized action.');
        // }

        // 5. Add security headers
        $response = $next($request);
        return $response->header('X-Frame-Options', 'DENY')
                       ->header('X-Content-Type-Options', 'nosniff');
    }

    public function render()
    {
        return view('components.nav-link');
    }
}
