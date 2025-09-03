<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MerchantLoginController extends Controller
{
    /**
     * Display the merchant login view.
     */
    public function create(): View
    {
        return view('auth.merchant.login');
    }

    /**
     * Handle an incoming merchant authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Validate the user has merchant role
        $credentials = $request->validated();

        if (Auth::guard('merchant')->attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::guard('merchant')->user();

            // Check if user has merchant role
            if ($user->role !== 'merchant') {
                Auth::guard('merchant')->logout();

                return back()->withErrors([
                    'email' => 'These credentials do not match our merchant records.',
                ]);
            }

            $request->session()->regenerate();

            // Check merchant status and redirect accordingly
            if ($user->merchant_status === 'pending') {
                return redirect()->route('merchant.status')
                    ->with('info', 'حسابك قيد المراجعة، يرجى انتظار الموافقة من الإدارة');
            }

            if ($user->merchant_status === 'rejected') {
                return redirect()->route('merchant.status')
                    ->with('error', 'تم رفض طلب التسجيل، يرجى مراجعة البيانات');
            }

            if ($user->merchant_status === 'suspended') {
                return redirect()->route('merchant.status')
                    ->with('error', 'تم تعليق حسابك، يرجى التواصل مع الإدارة');
            }

            // If approved, redirect to dashboard
            if ($user->merchant_status === 'approved') {
                return redirect()->intended('/merchant/dashboard');
            }

            // For any other status, go to status page
            return redirect()->route('merchant.status');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    /**
     * Destroy an authenticated merchant session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('merchant')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/merchant/login');
    }
}
