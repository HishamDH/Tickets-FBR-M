<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureMerchantRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (! Auth::check()) {
            return redirect()->route('merchant.login');
        }

        $user = Auth::user();

        // Check if user has Merchant role using Spatie permission or legacy user_type
        if (! $user->hasRole('Merchant') && $user->user_type !== 'merchant') {
            // If user is not a merchant, deny access
            abort(403, 'Access denied. Merchant role required.');
        }

        return $next($request);
    }
}
