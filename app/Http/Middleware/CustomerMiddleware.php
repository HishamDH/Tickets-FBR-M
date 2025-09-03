<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CustomerMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::guard('customer')->check()) {
            return redirect('/customer/login');
        }

        $user = Auth::guard('customer')->user();

        if ($user->role !== 'customer') {
            Auth::guard('customer')->logout();

            return redirect('/customer/login')->with('error', 'Access denied. Customer account required.');
        }

        return $next($request);
    }
}
