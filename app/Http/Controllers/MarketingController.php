<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\LoyaltyProgram;
use App\Models\LoyaltyPoint;
use App\Models\LoyaltyTransaction;
use App\Models\ReferralProgram;
use App\Models\Referral;
use App\Models\ReferralReward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MarketingController extends Controller
{
    // ================ COUPON MANAGEMENT ================
    
    public function validateCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'total_amount' => 'required|numeric|min:0',
            'merchant_id' => 'nullable|exists:merchants,id',
            'service_id' => 'nullable|exists:services,id',
        ]);

        $coupon = Coupon::where('code', $request->code)
                       ->active()
                       ->first();

        if (!$coupon) {
            return response()->json([
                'valid' => false,
                'message' => 'كود الخصم غير صحيح أو منتهي الصلاحية'
            ], 404);
        }

        $validation = $coupon->validateForUser(Auth::id(), $request->total_amount, $request->merchant_id, $request->service_id);

        if (!$validation['valid']) {
            return response()->json($validation, 422);
        }

        $discountAmount = $coupon->calculateDiscount($request->total_amount);

        return response()->json([
            'valid' => true,
            'coupon' => [
                'id' => $coupon->id,
                'code' => $coupon->code,
                'name' => $coupon->name,
                'description' => $coupon->description,
                'discount_type' => $coupon->discount_type,
                'discount_value' => $coupon->discount_value,
                'discount_amount' => $discountAmount,
                'final_amount' => $request->total_amount - $discountAmount,
            ]
        ]);
    }

    public function applyCoupon(Request $request)
    {
        $request->validate([
            'coupon_id' => 'required|exists:coupons,id',
            'booking_id' => 'required|exists:bookings,id',
            'original_amount' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $coupon = Coupon::findOrFail($request->coupon_id);
            $discountAmount = $coupon->calculateDiscount($request->original_amount);

            // Record coupon usage
            $usage = CouponUsage::create([
                'coupon_id' => $coupon->id,
                'user_id' => Auth::id(),
                'booking_id' => $request->booking_id,
                'original_amount' => $request->original_amount,
                'discount_amount' => $discountAmount,
                'final_amount' => $request->original_amount - $discountAmount,
            ]);

            // Update coupon usage count
            $coupon->increment('usage_count');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم تطبيق كود الخصم بنجاح',
                'usage' => $usage
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تطبيق كود الخصم'
            ], 500);
        }
    }

    public function getUserCoupons()
    {
        $user = Auth::user();
        $availableCoupons = Coupon::active()
                                 ->where(function($query) use ($user) {
                                     $query->whereNull('user_id')
                                           ->orWhere('user_id', $user->id);
                                 })
                                 ->with(['merchant', 'service'])
                                 ->get();

        $usedCoupons = CouponUsage::where('user_id', $user->id)
                                 ->with(['coupon', 'booking'])
                                 ->latest()
                                 ->get();

        return response()->json([
            'available_coupons' => $availableCoupons,
            'used_coupons' => $usedCoupons
        ]);
    }

    // ================ LOYALTY PROGRAM ================

    public function getUserLoyaltyStatus()
    {
        $user = Auth::user();
        $loyaltyPrograms = LoyaltyProgram::active()->get();

        $loyaltyStatus = [];
        foreach ($loyaltyPrograms as $program) {
            $totalPoints = $program->getUserTotalPoints($user->id);
            $tier = $program->getUserTier($user->id);
            
            $loyaltyStatus[] = [
                'program' => $program,
                'total_points' => $totalPoints,
                'current_tier' => $tier,
                'transactions' => LoyaltyTransaction::where('user_id', $user->id)
                                                  ->where('loyalty_program_id', $program->id)
                                                  ->latest()
                                                  ->limit(10)
                                                  ->get()
            ];
        }

        return response()->json($loyaltyStatus);
    }

    public function redeemLoyaltyPoints(Request $request)
    {
        $request->validate([
            'program_id' => 'required|exists:loyalty_programs,id',
            'points' => 'required|integer|min:1',
            'booking_id' => 'nullable|exists:bookings,id',
        ]);

        try {
            $program = LoyaltyProgram::findOrFail($request->program_id);
            $success = $program->redeemPoints(
                Auth::id(),
                $request->points,
                'استرداد نقاط من التطبيق',
                $request->booking_id
            );

            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكن استرداد هذا العدد من النقاط'
                ], 422);
            }

            $discountAmount = $program->calculateDiscountForPoints($request->points);

            return response()->json([
                'success' => true,
                'message' => 'تم استرداد النقاط بنجاح',
                'discount_amount' => $discountAmount
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء استرداد النقاط'
            ], 500);
        }
    }

    public function awardLoyaltyPoints(Request $request)
    {
        $request->validate([
            'program_id' => 'required|exists:loyalty_programs,id',
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'booking_id' => 'nullable|exists:bookings,id',
        ]);

        try {
            $program = LoyaltyProgram::findOrFail($request->program_id);
            $points = $program->awardPoints(
                $request->user_id,
                $request->amount,
                $request->description,
                $request->booking_id
            );

            if (!$points) {
                return response()->json([
                    'success' => false,
                    'message' => 'لم يتم منح أي نقاط'
                ], 422);
            }

            return response()->json([
                'success' => true,
                'message' => 'تم منح النقاط بنجاح',
                'points_awarded' => $points->points
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء منح النقاط'
            ], 500);
        }
    }

    // ================ REFERRAL PROGRAM ================

    public function generateReferralCode(Request $request)
    {
        $request->validate([
            'program_id' => 'required|exists:referral_programs,id',
        ]);

        try {
            $program = ReferralProgram::findOrFail($request->program_id);
            $referral = $program->generateReferralCode(Auth::id());

            return response()->json([
                'success' => true,
                'referral_code' => $referral->code,
                'expires_at' => $referral->expires_at,
                'share_url' => url('/register?ref=' . $referral->code)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إنشاء كود الإحالة'
            ], 500);
        }
    }

    public function processReferral(Request $request)
    {
        $request->validate([
            'referral_code' => 'required|string',
            'referee_id' => 'required|exists:users,id',
            'order_amount' => 'nullable|numeric|min:0',
            'booking_id' => 'nullable|exists:bookings,id',
        ]);

        try {
            $referral = Referral::where('code', $request->referral_code)->first();
            
            if (!$referral) {
                return response()->json([
                    'success' => false,
                    'message' => 'كود الإحالة غير صحيح'
                ], 404);
            }

            $program = $referral->referralProgram;
            $success = $program->processReferral(
                $request->referral_code,
                $request->referee_id,
                $request->booking_id,
                $request->order_amount
            );

            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكن معالجة هذه الإحالة'
                ], 422);
            }

            return response()->json([
                'success' => true,
                'message' => 'تم تسجيل الإحالة بنجاح'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء معالجة الإحالة'
            ], 500);
        }
    }

    public function getUserReferrals()
    {
        $user = Auth::user();
        
        $referrals = Referral::where('referrer_id', $user->id)
                            ->with(['referee', 'referralProgram', 'rewards'])
                            ->latest()
                            ->get();

        $totalEarnings = ReferralReward::whereIn('referral_id', $referrals->pluck('id'))
                                     ->where('type', 'referrer')
                                     ->sum('reward_value');

        return response()->json([
            'referrals' => $referrals,
            'total_earnings' => $totalEarnings,
            'total_successful' => $referrals->where('is_successful', true)->count()
        ]);
    }

    // ================ ANALYTICS & REPORTS ================

    public function getMarketingAnalytics(Request $request)
    {
        $dateRange = $request->get('date_range', 30);
        $startDate = Carbon::now()->subDays($dateRange);

        // Coupon Analytics
        $couponStats = [
            'total_coupons' => Coupon::count(),
            'active_coupons' => Coupon::active()->count(),
            'total_usage' => CouponUsage::where('created_at', '>=', $startDate)->count(),
            'total_savings' => CouponUsage::where('created_at', '>=', $startDate)->sum('discount_amount'),
        ];

        // Loyalty Analytics
        $loyaltyStats = [
            'total_programs' => LoyaltyProgram::count(),
            'active_programs' => LoyaltyProgram::active()->count(),
            'total_points_awarded' => LoyaltyTransaction::where('type', 'earn')
                                                       ->where('created_at', '>=', $startDate)
                                                       ->sum('points'),
            'total_points_redeemed' => abs(LoyaltyTransaction::where('type', 'redeem')
                                                            ->where('created_at', '>=', $startDate)
                                                            ->sum('points')),
        ];

        // Referral Analytics
        $referralStats = [
            'total_programs' => ReferralProgram::count(),
            'active_programs' => ReferralProgram::active()->count(),
            'total_referrals' => Referral::where('created_at', '>=', $startDate)->count(),
            'successful_referrals' => Referral::where('is_successful', true)
                                             ->where('created_at', '>=', $startDate)
                                             ->count(),
        ];

        return response()->json([
            'coupon_stats' => $couponStats,
            'loyalty_stats' => $loyaltyStats,
            'referral_stats' => $referralStats,
            'date_range' => $dateRange
        ]);
    }

    public function getTopPerformingCoupons(Request $request)
    {
        $limit = $request->get('limit', 10);
        
        $topCoupons = Coupon::withCount('usages')
                           ->orderBy('usages_count', 'desc')
                           ->limit($limit)
                           ->get();

        return response()->json($topCoupons);
    }

    public function getMarketingTrends(Request $request)
    {
        $days = $request->get('days', 30);
        $trends = [];

        for ($i = $days; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            
            $trends[] = [
                'date' => $date,
                'coupon_usage' => CouponUsage::whereDate('created_at', $date)->count(),
                'loyalty_transactions' => LoyaltyTransaction::whereDate('created_at', $date)->count(),
                'referrals' => Referral::whereDate('created_at', $date)->count(),
            ];
        }

        return response()->json($trends);
    }
}
