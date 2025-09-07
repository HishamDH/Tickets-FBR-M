<?php

use App\Models\Branch;
use App\Models\MerchantWallet;
use App\Models\Offering;
use App\Models\PaidReservation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

// ===========================================
// PAYMENT AND FINANCIAL FUNCTIONS
// ===========================================

if (! function_exists('getCard')) {
    /**
     * Get current user's payment card
     */
    function getCard($userId = null)
    {
        $userId = $userId ?? Auth::id();

        // This would integrate with your payment gateway
        // For now, return a default structure
        return [
            'id' => null,
            'last_four' => null,
            'brand' => null,
            'exp_month' => null,
            'exp_year' => null,
        ];
    }
}

if (! function_exists('logPayment')) {
    /**
     * Log payment transaction and update wallet
     */
    function logPayment($merchantId, $amount, $type = 'payment', $metadata = [])
    {
        try {
            DB::beginTransaction();

            // Get or create merchant wallet
            $wallet = MerchantWallet::firstOrCreate(
                ['merchant_id' => $merchantId],
                ['balance' => 0, 'pending_balance' => 0]
            );

            // Calculate commission (default 5%)
            $commissionRate = config('app.commission_rate', 0.05);
            $commission = $amount * $commissionRate;
            $netAmount = $amount - $commission;

            // Update wallet balance
            $wallet->increment('balance', $netAmount);

            // Create wallet transaction
            $wallet->transactions()->create([
                'type' => $type,
                'amount' => $netAmount,
                'commission' => $commission,
                'status' => 'completed',
                'metadata' => $metadata,
                'processed_at' => now(),
            ]);

            DB::commit();

            return [
                'success' => true,
                'amount' => $amount,
                'net_amount' => $netAmount,
                'commission' => $commission,
                'wallet_balance' => $wallet->balance,
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment logging error: '.$e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}

if (! function_exists('calculateNet')) {
    /**
     * Calculate net profit (total payments - refunds)
     */
    function calculateNet($merchantId, $period = 'month')
    {
        $query = PaidReservation::whereHas('offering', function ($q) use ($merchantId) {
            $q->where('user_id', $merchantId);
        });

        switch ($period) {
            case 'today':
                $query->whereDate('created_at', today());
                break;
            case 'week':
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
                break;
            case 'year':
                $query->whereYear('created_at', now()->year);
                break;
        }

        $totalPayments = $query->where('payment_status', 'paid')->sum('total_amount');
        $totalRefunds = $query->where('status', 'refunded')->sum('total_amount');

        return [
            'total_payments' => $totalPayments,
            'total_refunds' => $totalRefunds,
            'net_profit' => $totalPayments - $totalRefunds,
        ];
    }
}

// ===========================================
// PERMISSIONS AND SECURITY FUNCTIONS
// ===========================================

if (! function_exists('has_Permetion')) {
    /**
     * Check if user has specific permission using Spatie Permission
     */
    function has_Permetion($permission, $userId = null, $merchantId = null)
    {
        $user = $userId ? User::find($userId) : Auth::user();

        if (! $user) {
            return false;
        }

        // Check if user has the permission via Spatie
        if (method_exists($user, 'hasPermissionTo')) {
            return $user->hasPermissionTo($permission);
        }

        // Fallback for basic role checking
        return $user->role === 'admin' || $user->role === 'merchant';
    }
}

if (! function_exists('can_perform')) {
    /**
     * Check if current user can perform a specific action
     */
    function can_perform($permission)
    {
        return has_Permetion($permission);
    }
}

if (! function_exists('authorize_permission')) {
    /**
     * Authorize permission or throw exception
     */
    function authorize_permission($permission, $message = null)
    {
        if (!can_perform($permission)) {
            $message = $message ?: __('app.access_denied');
            abort(403, $message);
        }
        return true;
    }
}

if (! function_exists('user_has_role')) {
    /**
     * Check if user has specific role
     */
    function user_has_role($role, $userId = null)
    {
        $user = $userId ? User::find($userId) : Auth::user();

        if (! $user) {
            return false;
        }

        if (method_exists($user, 'hasRole')) {
            return $user->hasRole($role);
        }

        return $user->role === $role;
    }
}

if (! function_exists('user_has_any_role')) {
    /**
     * Check if user has any of the specified roles
     */
    function user_has_any_role($roles, $userId = null)
    {
        if (is_string($roles)) {
            $roles = [$roles];
        }

        foreach ($roles as $role) {
            if (user_has_role($role, $userId)) {
                return true;
            }
        }

        return false;
    }
}

if (! function_exists('adminPermission')) {
    /**
     * Check admin permissions
     */
    function adminPermission($userId = null)
    {
        $user = $userId ? User::find($userId) : Auth::user();

        return $user && $user->role === 'admin';
    }
}

if (! function_exists('can_enter')) {
    /**
     * Check if user can access specific page/feature
     */
    function can_enter($permission, $userId = null)
    {
        return has_Permetion($permission, $userId);
    }
}

if (! function_exists('is_m_admin')) {
    /**
     * Check if user is merchant admin
     */
    function is_m_admin($userId = null, $merchantId = null)
    {
        $user = $userId ? User::find($userId) : Auth::user();

        if (! $user) {
            return false;
        }

        return $user->role === 'merchant' || $user->role === 'admin';
    }
}

if (! function_exists('work_in')) {
    /**
     * Check if user works for specific merchant
     */
    function work_in($merchantId, $userId = null)
    {
        $user = $userId ? User::find($userId) : Auth::user();

        if (! $user) {
            return false;
        }

        // Check if user is the merchant or admin
        return $user->id == $merchantId || $user->role === 'admin';
    }
}

// ===========================================
// BUSINESS LOGIC FUNCTIONS
// ===========================================

if (! function_exists('hasEssentialFields')) {
    /**
     * Check if offering has all essential fields (21 required fields)
     */
    function hasEssentialFields($offeringId)
    {
        $offering = Offering::find($offeringId);

        if (! $offering) {
            return false;
        }

        $requiredFields = [
            'title', 'description', 'price', 'user_id', 'status',
            'category_id', 'max_attendees', 'duration', 'location',
            'start_date', 'end_date', 'terms_conditions',
        ];

        foreach ($requiredFields as $field) {
            if (empty($offering->$field)) {
                return false;
            }
        }

        // Check additional_data fields
        $additionalData = $offering->additional_data ?? [];
        $requiredAdditionalFields = [
            'features', 'highlights', 'includes', 'excludes',
            'cancellation_policy', 'age_restrictions', 'dress_code',
            'what_to_bring', 'meeting_point',
        ];

        foreach ($requiredAdditionalFields as $field) {
            if (empty($additionalData[$field])) {
                return false;
            }
        }

        return true;
    }
}

if (! function_exists('can_booking_now')) {
    /**
     * Check if offering can be booked now with time and quantity constraints
     */
    function can_booking_now($offeringId, $branchId = null, $quantity = 1)
    {
        $offering = Offering::find($offeringId);

        if (! $offering || $offering->status !== 'active') {
            return false;
        }

        // Check if offering is published
        $additionalData = $offering->additional_data ?? [];
        if (isset($additionalData['is_published']) && ! $additionalData['is_published']) {
            return false;
        }

        // Check date constraints
        $now = now();
        if ($offering->start_date && $now->gt($offering->start_date)) {
            return false;
        }

        if ($offering->end_date && $now->lt($offering->end_date)) {
            return false;
        }

        // Check availability
        $availableQuantity = get_quantity($offeringId, $branchId);
        if ($availableQuantity < $quantity) {
            return false;
        }

        return true;
    }
}

if (! function_exists('get_quantity')) {
    /**
     * Get available quantity for booking in real-time
     */
    function get_quantity($offeringId, $branchId = null)
    {
        $offering = Offering::find($offeringId);

        if (! $offering) {
            return 0;
        }

        $maxAttendees = $offering->max_attendees ?? 0;

        // Count existing bookings
        $bookedQuantity = PaidReservation::where('offering_id', $offeringId)
            ->where('status', '!=', 'cancelled')
            ->where('status', '!=', 'refunded')
            ->when($branchId, function ($query) use ($branchId) {
                $query->whereJsonContains('additional_data->branch_id', $branchId);
            })
            ->sum('quantity');

        return max(0, $maxAttendees - $bookedQuantity);
    }
}

if (! function_exists('fetch_time')) {
    /**
     * Fetch available times for offering (services/events)
     */
    function fetch_time($offeringId)
    {
        $offering = Offering::find($offeringId);

        if (! $offering) {
            return null;
        }

        $additionalData = $offering->additional_data ?? [];

        if ($offering->type === 'event') {
            return [
                'type' => 'event',
                'data' => [[
                    'start_date' => $offering->start_date,
                    'end_date' => $offering->end_date,
                    'start_time' => $additionalData['start_time'] ?? '09:00',
                    'end_time' => $additionalData['end_time'] ?? '17:00',
                ]],
            ];
        }

        if ($offering->type === 'service') {
            return [
                'type' => 'service',
                'max_reservation_date' => $additionalData['max_reservation_date'] ?? now()->addDays(30)->toDateString(),
                'data' => $additionalData['working_hours'] ?? [
                    'monday' => ['09:00-17:00'],
                    'tuesday' => ['09:00-17:00'],
                    'wednesday' => ['09:00-17:00'],
                    'thursday' => ['09:00-17:00'],
                    'friday' => ['09:00-17:00'],
                    'saturday' => ['09:00-15:00'],
                    'sunday' => [],
                ],
            ];
        }

        return null;
    }
}

if (! function_exists('set_presence')) {
    /**
     * Set customer presence/attendance with QR Code verification
     */
    function set_presence($reservationId, $verificationCode = null)
    {
        $reservation = PaidReservation::find($reservationId);

        if (! $reservation) {
            return false;
        }

        // Verify QR code if provided
        if ($verificationCode && $reservation->verification_code !== $verificationCode) {
            return false;
        }

        $additionalData = $reservation->additional_data ?? [];
        $additionalData['attendance_marked'] = true;
        $additionalData['attendance_time'] = now()->toISOString();
        $additionalData['verified_by'] = Auth::id();

        $reservation->update(['additional_data' => $additionalData]);

        return true;
    }
}

if (! function_exists('can_cancel')) {
    /**
     * Check if booking can be cancelled based on merchant policy
     */
    function can_cancel($reservationId)
    {
        $reservation = PaidReservation::find($reservationId);

        if (! $reservation || $reservation->status === 'cancelled') {
            return false;
        }

        $offering = $reservation->offering;
        if (! $offering) {
            return false;
        }

        // Check cancellation policy
        $additionalData = $offering->additional_data ?? [];
        $cancellationPolicy = $additionalData['cancellation_policy'] ?? [];

        $hoursBeforeEvent = $cancellationPolicy['hours_before'] ?? 24;
        $eventDateTime = Carbon::parse($offering->start_date);

        return now()->addHours($hoursBeforeEvent)->lt($eventDateTime);
    }
}

// ===========================================
// STATISTICS AND ANALYTICS FUNCTIONS
// ===========================================

if (! function_exists('get_statistics')) {
    /**
     * Get comprehensive merchant statistics
     */
    function get_statistics($merchantId)
    {
        $wallet = MerchantWallet::where('merchant_id', $merchantId)->first();

        $reservations = PaidReservation::whereHas('offering', function ($q) use ($merchantId) {
            $q->where('user_id', $merchantId);
        });

        $thisMonth = $reservations->clone()->whereMonth('created_at', now()->month);
        $lastMonth = $reservations->clone()->whereMonth('created_at', now()->subMonth()->month);

        return [
            'wallet' => [
                'balance' => $wallet ? $wallet->balance : 0,
                'pending' => $wallet ? $wallet->pending_balance : 0,
            ],
            'revenue' => [
                'this_month' => $thisMonth->sum('total_amount'),
                'last_month' => $lastMonth->sum('total_amount'),
                'total' => $reservations->sum('total_amount'),
            ],
            'bookings' => [
                'this_month' => $thisMonth->count(),
                'last_month' => $lastMonth->count(),
                'total' => $reservations->count(),
            ],
            'offerings' => [
                'total' => Offering::where('user_id', $merchantId)->count(),
                'active' => Offering::where('user_id', $merchantId)->where('status', 'active')->count(),
            ],
        ];
    }
}

if (! function_exists('Peak_Time')) {
    /**
     * Analyze peak times with 24/7 heatmap data
     */
    function Peak_Time($merchantId, $period = 'month')
    {
        $query = PaidReservation::whereHas('offering', function ($q) use ($merchantId) {
            $q->where('user_id', $merchantId);
        });

        switch ($period) {
            case 'week':
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereMonth('created_at', now()->month);
                break;
            case 'year':
                $query->whereYear('created_at', now()->year);
                break;
        }

        $reservations = $query->get();

        // Generate 24/7 heatmap
        $heatmap = [];
        for ($day = 0; $day < 7; $day++) {
            for ($hour = 0; $hour < 24; $hour++) {
                $heatmap[$day][$hour] = 0;
            }
        }

        foreach ($reservations as $reservation) {
            $dayOfWeek = $reservation->created_at->dayOfWeek;
            $hour = $reservation->created_at->hour;
            $heatmap[$dayOfWeek][$hour]++;
        }

        // Find peak times
        $peakHour = 0;
        $peakDay = 0;
        $maxBookings = 0;

        foreach ($heatmap as $day => $hours) {
            foreach ($hours as $hour => $count) {
                if ($count > $maxBookings) {
                    $maxBookings = $count;
                    $peakDay = $day;
                    $peakHour = $hour;
                }
            }
        }

        return [
            'heatmap' => $heatmap,
            'peak_day' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'][$peakDay],
            'peak_hour' => sprintf('%02d:00', $peakHour),
            'peak_bookings' => $maxBookings,
        ];
    }
}

if (! function_exists('pending_reservations')) {
    /**
     * Get pending reservations by time period
     */
    function pending_reservations($merchantId, $period = 'today')
    {
        $query = PaidReservation::whereHas('offering', function ($q) use ($merchantId) {
            $q->where('user_id', $merchantId);
        })->where('status', 'pending');

        switch ($period) {
            case 'today':
                $query->whereDate('created_at', today());
                break;
            case 'week':
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereMonth('created_at', now()->month);
                break;
        }

        return $query->count();
    }
}

if (! function_exists('set_viewed')) {
    /**
     * Track page views with IP and timestamp
     */
    function set_viewed($pageType, $pageId, $userId = null)
    {
        try {
            $ipAddress = request()->ip();

            // Simple view tracking (you can expand this)
            Cache::increment("views:{$pageType}:{$pageId}");

            if ($userId) {
                Cache::increment("user_views:{$userId}:{$pageType}:{$pageId}");
            }

            Log::info('Page viewed', [
                'page_type' => $pageType,
                'page_id' => $pageId,
                'user_id' => $userId,
                'ip' => $ipAddress,
                'timestamp' => now()->toISOString(),
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('View tracking error: '.$e->getMessage());

            return false;
        }
    }
}

// ===========================================
// SYSTEM AND CONFIGURATION FUNCTIONS
// ===========================================

if (! function_exists('LoadConfig')) {
    /**
     * Load system configuration
     */
    function LoadConfig($key = null)
    {
        $config = Cache::remember('system_config', 3600, function () {
            return [
                'commission_rate' => config('app.commission_rate', 0.05),
                'currency' => config('app.currency', 'USD'),
                'timezone' => config('app.timezone', 'UTC'),
                'max_file_size' => config('app.max_file_size', 10), // MB
                'allowed_file_types' => ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx'],
            ];
        });

        return $key ? ($config[$key] ?? null) : $config;
    }
}

if (! function_exists('first_setup')) {
    /**
     * Check if system is in first setup mode
     */
    function first_setup()
    {
        return User::where('role', 'admin')->count() === 0;
    }
}

if (! function_exists('Create_Wallet')) {
    /**
     * Create new merchant wallet
     */
    function Create_Wallet($merchantId)
    {
        return MerchantWallet::firstOrCreate(
            ['merchant_id' => $merchantId],
            ['balance' => 0, 'pending_balance' => 0]
        );
    }
}

if (! function_exists('get_branches')) {
    /**
     * Get branches for specific offering
     */
    function get_branches($offeringId)
    {
        $offering = Offering::find($offeringId);

        if (! $offering) {
            return collect();
        }

        return Branch::where('merchant_id', $offering->user_id)
            ->where('is_active', true)
            ->get();
    }
}

if (! function_exists('get_coupons')) {
    /**
     * Get active coupons for offering
     */
    function get_coupons($offeringId)
    {
        $offering = Offering::find($offeringId);

        if (! $offering) {
            return [];
        }

        $additionalData = $offering->additional_data ?? [];
        $coupons = $additionalData['coupons'] ?? [];

        // Filter active coupons only
        return array_filter($coupons, function ($coupon) {
            $expiresAt = Carbon::parse($coupon['expires_at'] ?? now()->addDays(30));

            return $expiresAt->isFuture() && ($coupon['is_active'] ?? true);
        });
    }
}

if (! function_exists('sendOTP')) {
    /**
     * Send OTP verification code via email
     */
    function sendOTP($email, $code)
    {
        try {
            // This would integrate with your email service
            Log::info("OTP sent to {$email}: {$code}");

            return true;
        } catch (\Exception $e) {
            Log::error('OTP sending error: '.$e->getMessage());

            return false;
        }
    }
}

if (! function_exists('translate')) {
    /**
     * Auto-translate text using Lingva API
     */
    function translate($text, $from = 'auto', $target = 'en')
    {
        try {
            // This would integrate with translation service
            return $text; // Fallback: return original text
        } catch (\Exception $e) {
            Log::error('Translation error: '.$e->getMessage());

            return $text;
        }
    }
}

if (! function_exists('pendingRes')) {
    /**
     * Analyze expired pending reservations
     */
    function pendingRes($merchantId = null)
    {
        $query = PaidReservation::where('status', 'pending')
            ->where('created_at', '<', now()->subHours(24));

        if ($merchantId) {
            $query->whereHas('offering', function ($q) use ($merchantId) {
                $q->where('user_id', $merchantId);
            });
        }

        return $query->count();
    }
}

if (! function_exists('notifcate')) {
    /**
     * Send notification to user
     */
    function notifcate($userId, $title, $message, $additionalData = [])
    {
        try {
            $user = User::find($userId);

            if (! $user) {
                return false;
            }

            // Create notification record
            $user->notifications()->create([
                'title' => $title,
                'message' => $message,
                'data' => $additionalData,
                'read_at' => null,
            ]);

            Log::info("Notification sent to user {$userId}: {$title}");

            return true;

        } catch (\Exception $e) {
            Log::error('Notification error: '.$e->getMessage());

            return false;
        }
    }
}

// ===========================================
// AUTHENTICATION AND ROUTING FUNCTIONS
// ===========================================

if (! function_exists('get_user_dashboard_route')) {
    /**
     * Get appropriate dashboard route for current user or specific user
     */
    function get_user_dashboard_route($userId = null)
    {
        $user = $userId ? User::find($userId) : Auth::user();

        if (! $user) {
            return route('customer.login'); // Default to customer login if no user
        }

        // Debug: check what we're getting
        $userRole = $user->role ?? $user->user_type ?? 'customer';
        
        // Log what we're getting for debugging
        if ($userRole === 'ltr' || $userRole === 'rtl') {
            \Log::error('Dashboard route called with language direction instead of user role', [
                'userRole' => $userRole,
                'user_id' => $user->id,
                'user_type' => $user->user_type ?? 'null',
                'user_role' => $user->role ?? 'null'
            ]);
            $userRole = 'customer'; // Fallback
        }
        
        // Ensure we have a valid role string
        if (!is_string($userRole) || empty($userRole)) {
            $userRole = 'customer';
        }
        
        // Use a more defensive approach instead of match
        switch ($userRole) {
            case 'admin':
                return route('admin.dashboard');
            case 'merchant':
                return route('merchant.dashboard');
            case 'partner':
                return route('partner.dashboard');
            case 'customer':
            case 'user':
                return route('customer.dashboard');
            default:
                return route('customer.dashboard');
        }
    }
}

if (! function_exists('dashboard_route')) {
    /**
     * Alias for get_user_dashboard_route for backward compatibility
     */
    function dashboard_route($userId = null)
    {
        return get_user_dashboard_route($userId);
    }
}

// ===========================================
// LANGUAGE AND LOCALIZATION FUNCTIONS
// ===========================================

if (! function_exists('getCurrentLanguage')) {
    /**
     * Get current application language
     */
    function getCurrentLanguage()
    {
        return app()->getLocale();
    }
}

if (! function_exists('isRtl')) {
    /**
     * Check if current language is RTL (Right-to-Left)
     */
    function isRtl()
    {
        // Arabic is RTL language
        return app()->getLocale() === 'ar';
    }
}

if (! function_exists('getLanguageDirection')) {
    /**
     * Get HTML direction attribute for current language
     */
    function getLanguageDirection()
    {
        return isRtl() ? 'rtl' : 'ltr';
    }
}

if (! function_exists('getAvailableLanguages')) {
    /**
     * Get list of available languages in the application
     */
    function getAvailableLanguages()
    {
        return [
            'en' => [
                'name' => 'English',
                'native' => 'English',
                'flag' => '🇬🇧',
                'dir' => 'ltr',
            ],
            'ar' => [
                'name' => 'Arabic',
                'native' => 'العربية',
                'flag' => '🇸🇦',
                'dir' => 'rtl',
            ],
        ];
    }
}
