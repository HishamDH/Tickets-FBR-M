<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'غير مصرح لك بالوصول'], 401);
            }
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Check if user has any of the required roles
        if (empty($roles) || in_array($user->role, $roles)) {
            return $next($request);
        }

        // If user doesn't have required role, redirect based on their actual role
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'ليس لديك صلاحية للوصول إلى هذه الصفحة',
                'required_roles' => $roles,
                'user_role' => $user->role
            ], 403);
        }

        // Redirect to appropriate dashboard based on user role
        switch ($user->role) {
            case 'admin':
                return redirect()->route('filament.admin.pages.dashboard')
                    ->with('error', 'ليس لديك صلاحية للوصول إلى هذه الصفحة');
            
            case 'merchant':
                return redirect()->route('merchant.dashboard')
                    ->with('error', 'ليس لديك صلاحية للوصول إلى هذه الصفحة');
            
            case 'partner':
                return redirect()->route('partner.dashboard')
                    ->with('error', 'ليس لديك صلاحية للوصول إلى هذه الصفحة');
            
            case 'customer':
            default:
                return redirect()->route('customer.dashboard')
                    ->with('error', 'ليس لديك صلاحية للوصول إلى هذه الصفحة');
        }
    }
}
