<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user is logged in
        if (!Auth::check()) {
            return redirect()
                ->route('admin.login')
                ->with('error', 'Please login to access admin panel.');
        }

        $user = Auth::user();

        // Check if user is admin
        if ($user->role !== 'admin') {
            abort(403, 'Access denied. Admins only.');
        }

        return $next($request);
    }
}