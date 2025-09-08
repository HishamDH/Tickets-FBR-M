<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SiteStatsService
{
    /**
     * Get site-wide statistics
     */
    public function getSiteStats(): array
    {
        return Cache::remember('site_stats', 300, function () {
            try {
                return [
                    'total_services' => Service::where('is_active', true)->count() ?: 0,
                    'total_bookings' => Booking::count() ?: 0,
                    'total_customers' => User::where('user_type', 'customer')
                        ->orWhereHas('roles', function ($q) {
                            $q->where('name', 'Customer');
                        })
                        ->count() ?: 0,
                    'total_merchants' => User::where('user_type', 'merchant')
                        ->orWhere('merchant_status', 'approved')
                        ->count() ?: 0,
                    'completion_rate' => $this->calculateCompletionRate(),
                    'active_bookings_today' => Booking::whereDate('booking_date', today())->count() ?: 0,
                    'total_revenue' => Booking::where('payment_status', 'paid')->sum('total_amount') ?: 0,
                ];
            } catch (\Exception $e) {
                // Fallback stats if database is not available
                return [
                    'total_services' => 0,
                    'total_bookings' => 0,
                    'total_customers' => 0,
                    'total_merchants' => 0,
                    'completion_rate' => 0,
                    'active_bookings_today' => 0,
                    'total_revenue' => 0,
                ];
            }
        });
    }

    /**
     * Get customer-specific stats for header
     */
    public function getCustomerStats($userId): array
    {
        return Cache::remember("customer_stats_{$userId}", 300, function () use ($userId) {
            $bookings = Booking::where('customer_id', $userId);
            $completedBookings = $bookings->where('status', 'completed')->count();
            $totalBookings = $bookings->count();
            
            return [
                'total_bookings' => $totalBookings,
                'completed_bookings' => $completedBookings,
                'completion_rate' => $totalBookings > 0 ? round(($completedBookings / $totalBookings) * 100) : 0,
                'favorite_services' => User::find($userId)?->favoriteServices()?->count() ?? 0,
            ];
        });
    }

    /**
     * Get merchant-specific stats for header
     */
    public function getMerchantStats($userId): array
    {
        return Cache::remember("merchant_stats_{$userId}", 300, function () use ($userId) {
            $services = Service::where('merchant_id', $userId);
            $bookings = Booking::where('merchant_id', $userId);
            
            return [
                'total_services' => $services->count(),
                'active_services' => $services->where('is_active', true)->count(),
                'total_bookings' => $bookings->count(),
                'pending_bookings' => $bookings->where('status', 'pending')->count(),
                'total_revenue' => $bookings->where('payment_status', 'paid')->sum('total_amount'),
                'this_month_bookings' => $bookings->whereMonth('created_at', now()->month)->count(),
            ];
        });
    }

    /**
     * Get cart stats for authenticated customer
     */
    public function getCartStats(): array
    {
        if (!Auth::guard('customer')->check()) {
            return ['items' => 0, 'total' => 0];
        }

        $userId = Auth::guard('customer')->id();
        $sessionId = session()->getId();

        $cartData = \App\Models\Cart::getCartTotal($userId, $sessionId);
        
        return [
            'items' => $cartData['count'] ?? 0,
            'total' => $cartData['total'] ?? 0,
        ];
    }

    /**
     * Get notification count for authenticated user
     */
    public function getNotificationCount(): int
    {
        if (!Auth::check() && !Auth::guard('customer')->check()) {
            return 0;
        }

        $user = Auth::user() ?? Auth::guard('customer')->user();
        
        return $user->unreadNotifications?->count() ?? 3; // Default to 3 if no notifications system
    }

    /**
     * Calculate site-wide completion rate
     */
    private function calculateCompletionRate(): int
    {
        $totalBookings = Booking::count();
        if ($totalBookings === 0) {
            return 0;
        }

        $completedBookings = Booking::where('status', 'completed')->count();
        return round(($completedBookings / $totalBookings) * 100);
    }

    /**
     * Get popular services for footer
     */
    public function getPopularServices($limit = 5): array
    {
        return Cache::remember('popular_services', 3600, function () use ($limit) {
            return Service::withCount('bookings')
                ->where('is_active', true)
                ->orderBy('bookings_count', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($service) {
                    return [
                        'name' => $service->name,
                        'bookings' => $service->bookings_count,
                        'url' => route('customer.services.show', $service),
                    ];
                })
                ->toArray();
        });
    }

    /**
     * Get recent activity for footer
     */
    public function getRecentActivity($limit = 5): array
    {
        return Cache::remember('recent_activity', 600, function () use ($limit) {
            return Booking::with(['service', 'customer'])
                ->where('status', '!=', 'cancelled')
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($booking) {
                    return [
                        'service' => $booking->service->name ?? 'خدمة محذوفة',
                        'customer' => $booking->customer_name ?? $booking->customer->name ?? 'عميل',
                        'date' => $booking->created_at->diffForHumans(),
                        'status' => $booking->status,
                    ];
                })
                ->toArray();
        });
    }
}