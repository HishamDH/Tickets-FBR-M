<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class CheckPasswordExpiry
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Skip check for certain routes
        $excludedRoutes = [
            'password.*',
            'logout',
            'api.*'
        ];

        foreach ($excludedRoutes as $pattern) {
            if ($request->routeIs($pattern)) {
                return $next($request);
            }
        }

        if ($user && $this->shouldForcePasswordReset($user)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'يجب تحديث كلمة المرور الخاصة بك.',
                    'action' => 'password_reset_required'
                ], 403);
            }

            return redirect()->route('password.force-change')
                ->with('warning', 'كلمة المرور الخاصة بك انتهت صلاحيتها. يرجى تحديثها للمتابعة.');
        }

        return $next($request);
    }

    /**
     * Check if user should be forced to reset password
     */
    private function shouldForcePasswordReset($user): bool
    {
        // Skip for admin users (optional)
        if ($user->role === 'admin') {
            return false;
        }

        // Check if password is older than 90 days
        $passwordAge = Carbon::parse($user->password_changed_at ?? $user->created_at);
        $maxAge = now()->subDays(config('auth.password_expiry_days', 90));

        if ($passwordAge->lt($maxAge)) {
            return true;
        }

        // Check if user is marked for forced password reset
        if ($user->force_password_reset) {
            return true;
        }

        return false;
    }
}
