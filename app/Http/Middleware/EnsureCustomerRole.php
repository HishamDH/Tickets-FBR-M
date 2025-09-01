<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureCustomerRole
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
            return redirect()->route('customer.login');
        }

        $user = Auth::user();

        // Check if user has Customer role using Spatie permission or legacy user_type
        if (! $user->hasRole('Customer') && $user->user_type !== 'customer') {
            // If user is not a customer, deny access
            abort(403, 'Access denied. Customer account required.');
        }

        return $next($request);
    }
}
