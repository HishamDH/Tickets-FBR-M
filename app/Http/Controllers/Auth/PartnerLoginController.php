<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PartnerLoginController extends Controller
{
    /**
     * Display the partner login view.
     */
    public function create(): View
    {
        return view('auth.partner.login');
    }

    /**
     * Handle an incoming partner authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Validate the user has partner role
        $credentials = $request->validated();

        if (Auth::guard('partner')->attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::guard('partner')->user();

            // Check if user has partner role
            if ($user->user_type !== 'partner') {
                Auth::guard('partner')->logout();

                return back()->withErrors([
                    'email' => 'هذه البيانات لا تتطابق مع سجلاتنا للشركاء.',
                ]);
            }

            $request->session()->regenerate();

            // Check if partner profile exists
            if (! $user->partner) {
                return redirect()->route('partner.setup')
                    ->with('info', 'يرجى إكمال إعداد ملف الشريك أولاً');
            }

            // Check partner status
            if ($user->partner->status === 'inactive') {
                return redirect()->route('partner.status')
                    ->with('warning', 'حسابك غير نشط، يرجى التواصل مع الإدارة');
            }

            if ($user->partner->status === 'suspended') {
                return redirect()->route('partner.status')
                    ->with('error', 'تم تعليق حسابك، يرجى التواصل مع الإدارة');
            }

            // If active, redirect to dashboard
            return redirect()->intended('/partner/dashboard');
        }

        return back()->withErrors([
            'email' => 'البيانات المقدمة لا تتطابق مع سجلاتنا.',
        ]);
    }

    /**
     * Destroy an authenticated partner session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('partner')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/partner/login');
    }
}
