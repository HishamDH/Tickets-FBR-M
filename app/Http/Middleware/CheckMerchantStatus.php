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
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Only apply this check to merchants
        if ($user->role !== 'merchant') {
            return $next($request);
        }

        $merchant = $user->merchant;

        if (!$merchant) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'ملف التاجر غير موجود'], 404);
            }
            return redirect()->route('merchant.setup')
                ->with('warning', 'يجب إكمال إعداد ملف التاجر أولاً');
        }

        // Check if merchant account is active
        if ($merchant->status !== 'active') {
            $messages = [
                'pending' => 'حسابك قيد المراجعة، يرجى انتظار الموافقة من الإدارة',
                'suspended' => 'تم تعليق حسابك، يرجى التواصل مع الإدارة',
                'rejected' => 'تم رفض طلب التسجيل، يرجى مراجعة البيانات والتقديم مرة أخرى',
                'inactive' => 'حسابك غير نشط، يرجى التواصل مع الإدارة'
            ];

            $message = $messages[$merchant->status] ?? 'حالة الحساب غير صحيحة';

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                    'status' => $merchant->status
                ], 403);
            }

            return redirect()->route('merchant.status')
                ->with('warning', $message);
        }

        // Check if merchant has completed required setup
        if (!$merchant->is_profile_complete) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'يجب إكمال الملف الشخصي'], 400);
            }
            return redirect()->route('merchant.profile.complete')
                ->with('info', 'يرجى إكمال المعلومات المطلوبة في ملفك الشخصي');
        }

        return $next($request);
    }
}
