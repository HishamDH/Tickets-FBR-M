<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class CheckPermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $permission
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, string $permission)
    {
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => __('app.unauthorized')], 401);
            }
            return redirect()->route('login')->with('error', __('app.unauthorized'));
        }

        $user = Auth::user();

        // التحقق من وجود الصلاحية
        if (!$user->hasPermissionTo($permission)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => __('app.access_denied'),
                    'required_permission' => $permission
                ], 403);
            }

            return redirect()->back()->with('error', __('app.access_denied'));
        }

        return $next($request);
    }
}
