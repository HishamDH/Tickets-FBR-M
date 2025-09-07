<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Rules\StrongPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class PasswordForceChangeController extends Controller
{
    /**
     * Show the password change form
     */
    public function show()
    {
        return view('auth.password.force-change');
    }

    /**
     * Handle password change request
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        // Check if user account is locked
        if ($user->isLocked()) {
            return back()->withErrors([
                'current_password' => 'حسابك مؤقتاً مقفل. يرجى المحاولة مرة أخرى لاحقاً.'
            ]);
        }
        
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required',
                'string',
                'confirmed',
                new StrongPassword(),
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised()
            ],
        ], [
            'current_password.required' => 'كلمة المرور الحالية مطلوبة.',
            'current_password.current_password' => 'كلمة المرور الحالية غير صحيحة.',
            'password.required' => 'كلمة المرور الجديدة مطلوبة.',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق.',
            'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل.',
        ]);
        
        // Check if new password was used before
        if ($user->wasPasswordUsedBefore($request->password)) {
            return back()->withErrors([
                'password' => 'لا يمكن استخدام كلمة مرور تم استخدامها من قبل. يرجى اختيار كلمة مرور جديدة.'
            ]);
        }
        
        // Add old password to history before changing
        $user->addPasswordToHistory($user->password);
        
        // Update the password
        $user->update([
            'password' => Hash::make($request->password),
        ]);
        
        // Mark password as changed
        $user->markPasswordAsChanged();
        
        // Reset failed login attempts
        $user->resetFailedLoginAttempts();
        
        // Redirect to appropriate dashboard based on user role
        $dashboardRoute = match ($user->role ?? $user->user_type) {
            'admin' => 'admin.dashboard',
            'merchant' => 'merchant.dashboard',
            'partner' => 'partner.dashboard',
            'customer', 'user' => 'customer.dashboard',
            default => 'customer.dashboard'
        };
        
        return redirect()->intended(route($dashboardRoute))->with('status', 'تم تحديث كلمة المرور بنجاح.');
    }
}
