<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class MerchantMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('merchant')->check()) {
            return redirect('/merchant/login');
        }

        $user = Auth::guard('merchant')->user();
        
        if ($user->role !== 'merchant') {
            Auth::guard('merchant')->logout();
            return redirect('/merchant/login')->with('error', 'Access denied. Merchant privileges required.');
        }

        return $next($request);
    }
}