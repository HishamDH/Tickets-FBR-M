<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckMerchantStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Only apply this check to merchants
        if ($user->user_type !== 'merchant') {
            return $next($request);
        }

        // Check merchant verification status
        if ($user->merchant_status === 'pending') {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'حسابك قيد المراجعة، يرجى انتظار الموافقة من الإدارة'], 403);
            }

            return redirect()->route('merchant.status')
                ->with('warning', 'حسابك قيد المراجعة، يرجى انتظار الموافقة من الإدارة');
        }

        if ($user->merchant_status === 'rejected') {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'تم رفض طلب التسجيل، يرجى مراجعة البيانات والتقديم مرة أخرى'], 403);
            }

            return redirect()->route('merchant.status')
                ->with('error', 'تم رفض طلب التسجيل. السبب: '.($user->verification_notes ?? 'لم يتم تحديد السبب'));
        }

        if ($user->merchant_status === 'suspended') {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'تم تعليق حسابك، يرجى التواصل مع الإدارة'], 403);
            }

            return redirect()->route('merchant.status')
                ->with('error', 'تم تعليق حسابك. يرجى التواصل مع الإدارة');
        }

        // If approved, continue with the request
        if ($user->merchant_status === 'approved') {
            return $next($request);
        }

        // Default fallback for any other status
        if ($request->expectsJson()) {
            return response()->json(['message' => 'حالة الحساب غير صحيحة'], 403);
        }

        return redirect()->route('merchant.status')
            ->with('warning', 'حالة حسابك غير واضحة، يرجى التواصل مع الإدارة');
    }
}
