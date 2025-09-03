<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::guard('admin')->check()) {
            return redirect('/admin/login');
        }

        $user = Auth::guard('admin')->user();

        if ($user->role !== 'admin') {
            Auth::guard('admin')->logout();

            return redirect('/admin/login')->with('error', 'Access denied. Admin privileges required.');
        }

        return $next($request);
    }
}
