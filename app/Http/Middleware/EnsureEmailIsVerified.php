<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Skip verification check for admins
        if ($user->role === 'admin') {
            return $next($request);
        }

        // Check if email is verified
        if (!$user->hasVerifiedEmail()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'يجب تأكيد البريد الإلكتروني للمتابعة',
                    'email' => $user->email
                ], 403);
            }

            return redirect()->route('verification.notice')
                ->with('warning', 'يرجى تأكيد بريدك الإلكتروني للمتابعة');
        }

        return $next($request);
    }
}
