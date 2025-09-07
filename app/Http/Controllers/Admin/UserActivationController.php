<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\Partner;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class UserActivationController extends Controller
{
    /**
     * عرض المستخدمين المنتظرين للتفعيل
     */
    public function pending()
    {
        // التجار المنتظرين
        $pendingMerchants = User::where('user_type', 'merchant')
            ->where('merchant_status', 'pending')
            ->with('merchant')
            ->latest()
            ->get();

        // الشركاء المنتظرين
        $pendingPartners = User::where('user_type', 'partner')
            ->where('partner_status', 'pending')
            ->with('partner')
            ->latest()
            ->get();

        return view('admin.users.pending', compact('pendingMerchants', 'pendingPartners'));
    }

    /**
     * تفعيل حساب تاجر
     */
    public function approveMerchant(Request $request, User $user)
    {
        if ($user->user_type !== 'merchant') {
            return back()->with('error', 'هذا المستخدم ليس تاجراً');
        }

        $user->update([
            'merchant_status' => 'approved',
            'email_verified_at' => now()
        ]);

        // إرسال إشعار بالموافقة
        // TODO: إضافة إرسال البريد الإلكتروني

        return back()->with('success', 'تم تفعيل حساب التاجر بنجاح');
    }

    /**
     * رفض حساب تاجر
     */
    public function rejectMerchant(Request $request, User $user)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:255'
        ]);

        if ($user->user_type !== 'merchant') {
            return back()->with('error', 'هذا المستخدم ليس تاجراً');
        }

        $user->update([
            'merchant_status' => 'rejected',
            'rejection_reason' => $request->rejection_reason
        ]);

        // إرسال إشعار بالرفض
        // TODO: إضافة إرسال البريد الإلكتروني

        return back()->with('success', 'تم رفض حساب التاجر');
    }

    /**
     * تفعيل حساب شريك
     */
    public function approvePartner(Request $request, User $user)
    {
        if ($user->user_type !== 'partner') {
            return back()->with('error', 'هذا المستخدم ليس شريكاً');
        }

        $user->update([
            'partner_status' => 'approved',
            'email_verified_at' => now()
        ]);

        // إرسال إشعار بالموافقة
        // TODO: إضافة إرسال البريد الإلكتروني

        return back()->with('success', 'تم تفعيل حساب الشريك بنجاح');
    }

    /**
     * رفض حساب شريك
     */
    public function rejectPartner(Request $request, User $user)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:255'
        ]);

        if ($user->user_type !== 'partner') {
            return back()->with('error', 'هذا المستخدم ليس شريكاً');
        }

        $user->update([
            'partner_status' => 'rejected',
            'rejection_reason' => $request->rejection_reason
        ]);

        // إرسال إشعار بالرفض
        // TODO: إضافة إرسال البريد الإلكتروني

        return back()->with('success', 'تم رفض حساب الشريك');
    }

    /**
     * عرض تفاصيل مستخدم
     */
    public function show(User $user)
    {
        $user->load(['merchant', 'partner']);
        
        return view('admin.users.show', compact('user'));
    }

    /**
     * تعليق حساب مستخدم
     */
    public function suspend(Request $request, User $user)
    {
        $request->validate([
            'suspension_reason' => 'required|string|max:255'
        ]);

        if ($user->user_type === 'merchant') {
            $user->update([
                'merchant_status' => 'suspended',
                'suspension_reason' => $request->suspension_reason
            ]);
        } elseif ($user->user_type === 'partner') {
            $user->update([
                'partner_status' => 'suspended',
                'suspension_reason' => $request->suspension_reason
            ]);
        }

        return back()->with('success', 'تم تعليق حساب المستخدم');
    }

    /**
     * إلغاء تعليق حساب مستخدم
     */
    public function unsuspend(User $user)
    {
        if ($user->user_type === 'merchant') {
            $user->update([
                'merchant_status' => 'approved',
                'suspension_reason' => null
            ]);
        } elseif ($user->user_type === 'partner') {
            $user->update([
                'partner_status' => 'approved',
                'suspension_reason' => null
            ]);
        }

        return back()->with('success', 'تم إلغاء تعليق حساب المستخدم');
    }
}
