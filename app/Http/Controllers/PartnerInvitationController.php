<?php

namespace App\Http\Controllers;

use App\Models\PartnerInvitation;
use App\Models\Merchant;
use App\Services\PartnerCommissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PartnerInvitationController extends Controller
{
    /**
     * عرض صفحة قبول الدعوة
     */
    public function show($token)
    {
        $invitation = PartnerInvitation::where('token', $token)
            ->where('status', PartnerInvitation::STATUS_PENDING)
            ->first();

        if (!$invitation) {
            return view('partner.invitation.invalid')->with([
                'message' => 'رابط الدعوة غير صحيح أو منتهي الصلاحية'
            ]);
        }

        // التحقق من انتهاء الصلاحية
        if ($invitation->expires_at && $invitation->expires_at->isPast()) {
            $invitation->update(['status' => PartnerInvitation::STATUS_EXPIRED]);
            
            return view('partner.invitation.expired')->with([
                'invitation' => $invitation
            ]);
        }

        return view('partner.invitation.accept')->with([
            'invitation' => $invitation
        ]);
    }

    /**
     * معالجة قبول الدعوة
     */
    public function accept(Request $request, $token)
    {
        $invitation = PartnerInvitation::where('token', $token)
            ->where('status', PartnerInvitation::STATUS_PENDING)
            ->first();

        if (!$invitation) {
            throw ValidationException::withMessages([
                'token' => 'رابط الدعوة غير صحيح أو منتهي الصلاحية'
            ]);
        }

        // التحقق من انتهاء الصلاحية
        if ($invitation->expires_at && $invitation->expires_at->isPast()) {
            $invitation->update(['status' => PartnerInvitation::STATUS_EXPIRED]);
            
            throw ValidationException::withMessages([
                'token' => 'انتهت صلاحية رابط الدعوة'
            ]);
        }

        $request->validate([
            'business_name' => 'required|string|max:255',
            'business_type' => 'required|string|max:100',
            'business_description' => 'nullable|string|max:1000',
            'business_address' => 'required|string|max:500',
            'business_phone' => 'required|string|max:20',
            'business_email' => 'required|email|max:255|unique:merchants,email',
            'owner_name' => 'required|string|max:255',
            'owner_phone' => 'required|string|max:20',
            'owner_email' => 'required|email|max:255',
            'national_id' => 'required|string|max:20',
            'commercial_register' => 'nullable|string|max:50',
            'tax_number' => 'nullable|string|max:50',
            'bank_name' => 'required|string|max:100',
            'bank_account_number' => 'required|string|max:50',
            'bank_account_name' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
            'terms_accepted' => 'required|accepted',
        ], [
            'business_name.required' => 'اسم المتجر مطلوب',
            'business_type.required' => 'نوع النشاط مطلوب',
            'business_address.required' => 'عنوان المتجر مطلوب',
            'business_phone.required' => 'هاتف المتجر مطلوب',
            'business_email.required' => 'بريد المتجر الإلكتروني مطلوب',
            'business_email.unique' => 'هذا البريد الإلكتروني مستخدم من قبل',
            'owner_name.required' => 'اسم المالك مطلوب',
            'owner_phone.required' => 'هاتف المالك مطلوب',
            'owner_email.required' => 'بريد المالك الإلكتروني مطلوب',
            'national_id.required' => 'رقم الهوية مطلوب',
            'bank_name.required' => 'اسم البنك مطلوب',
            'bank_account_number.required' => 'رقم الحساب المصرفي مطلوب',
            'bank_account_name.required' => 'اسم صاحب الحساب مطلوب',
            'password.required' => 'كلمة المرور مطلوبة',
            'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
            'password.confirmed' => 'تأكيد كلمة المرور غير مطابق',
            'terms_accepted.required' => 'يجب قبول الشروط والأحكام',
        ]);

        try {
            DB::beginTransaction();

            // إنشاء التاجر الجديد
            $merchant = Merchant::create([
                'business_name' => $request->business_name,
                'business_type' => $request->business_type,
                'business_description' => $request->business_description,
                'business_address' => $request->business_address,
                'business_phone' => $request->business_phone,
                'email' => $request->business_email,
                'owner_name' => $request->owner_name,
                'owner_phone' => $request->owner_phone,
                'owner_email' => $request->owner_email,
                'national_id' => $request->national_id,
                'commercial_register' => $request->commercial_register,
                'tax_number' => $request->tax_number,
                'bank_details' => [
                    'bank_name' => $request->bank_name,
                    'account_number' => $request->bank_account_number,
                    'account_holder_name' => $request->bank_account_name,
                ],
                'password' => Hash::make($request->password),
                'partner_id' => $invitation->partner_id,
                'verification_status' => 'pending',
                'status' => 'active',
                'referral_source' => 'partner_invitation',
                'invitation_token' => $invitation->token,
            ]);

            // تحديث الدعوة كمقبولة
            $invitation->accept($merchant->id);

            // إضافة مكافأة إحالة التاجر للشريك
            $commissionService = app(PartnerCommissionService::class);
            $commissionService->processNewMerchantReferral($invitation->partner, $merchant);

            DB::commit();

            return redirect()->route('partner.invitation.success')->with([
                'message' => 'تم إنشاء حسابك بنجاح! سيتم مراجعة طلبك والتواصل معك قريباً.',
                'merchant' => $merchant
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            throw ValidationException::withMessages([
                'general' => 'حدث خطأ أثناء إنشاء الحساب. يرجى المحاولة مرة أخرى.'
            ]);
        }
    }

    /**
     * صفحة نجاح قبول الدعوة
     */
    public function success()
    {
        return view('partner.invitation.success');
    }

    /**
     * إنشاء رابط دعوة جديد (API للشركاء)
     */
    public function createInvitation(Request $request)
    {
        $request->validate([
            'merchant_name' => 'required|string|max:255',
            'merchant_email' => 'required|email|max:255',
            'merchant_phone' => 'required|string|max:20',
            'business_name' => 'required|string|max:255',
            'message' => 'nullable|string|max:1000',
        ]);

        $partner = auth()->user(); // افتراض أن الشريك مسجل دخول

        // التحقق من الحد اليومي للدعوات
        $dailyLimit = config('partner.max_daily_invitations', 10);
        $todayInvitations = PartnerInvitation::where('partner_id', $partner->id)
            ->whereDate('created_at', today())
            ->count();

        if ($todayInvitations >= $dailyLimit) {
            throw ValidationException::withMessages([
                'limit' => "تم تجاوز الحد اليومي للدعوات ({$dailyLimit} دعوات)"
            ]);
        }

        // التحقق من عدم وجود دعوة سابقة لنفس البريد الإلكتروني
        $existingInvitation = PartnerInvitation::where('merchant_email', $request->merchant_email)
            ->where('status', PartnerInvitation::STATUS_PENDING)
            ->first();

        if ($existingInvitation) {
            throw ValidationException::withMessages([
                'merchant_email' => 'يوجد دعوة معلقة لهذا البريد الإلكتروني'
            ]);
        }

        $invitation = PartnerInvitation::create([
            'partner_id' => $partner->id,
            'merchant_name' => $request->merchant_name,
            'merchant_email' => $request->merchant_email,
            'merchant_phone' => $request->merchant_phone,
            'business_name' => $request->business_name,
            'message' => $request->message,
            'token' => Str::random(32),
            'expires_at' => now()->addDays(config('partner.invitation_expires_days', 30)),
            'status' => PartnerInvitation::STATUS_PENDING,
        ]);

        $invitationUrl = route('partner.invitation.show', $invitation->token);

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء رابط الدعوة بنجاح',
            'invitation' => $invitation,
            'invitation_url' => $invitationUrl,
        ]);
    }
}
