<?php

namespace App\Filament\Merchant\Widgets;

use App\Models\Booking;
use App\Models\Offering;
use Carbon\Carbon;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class MerchantAnalyticsWidget extends Widget
{
    protected static string $view = 'filament.merchant.widgets.merchant-analytics';

    protected int|string|array $columnSpan = 'full';

    public function getAnalyticsData(): array
    {
        $userId = Auth::id();

        // Basic metrics
        $totalRevenue = Booking::whereHas('offering', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->where('status', 'completed')->sum('total_amount');

        $totalBookings = Booking::whereHas('offering', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->count();

        $totalServices = Offering::where('user_id', $userId)->count();
        $activeServices = Offering::where('user_id', $userId)->where('status', 'active')->count();

        // Revenue trends
        $monthlyRevenue = collect();
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $revenue = Booking::whereHas('offering', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
                ->where('status', 'completed')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('total_amount');

            $monthlyRevenue->push([
                'month' => $month->format('M Y'),
                'revenue' => $revenue,
            ]);
        }

        // Top services by revenue
        $topServices = Offering::where('user_id', $userId)
            ->withSum(['bookings' => function ($query) {
                $query->where('status', 'completed');
            }], 'total_amount')
            ->orderByDesc('bookings_sum_total_amount')
            ->limit(5)
            ->get()
            ->map(function ($service) {
                return [
                    'name' => $service->title,
                    'revenue' => $service->bookings_sum_total_amount ?? 0,
                    'type' => $service->type,
                ];
            });

        // Booking status distribution
        $statusDistribution = Booking::whereHas('offering', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        // Customer acquisition
        $newCustomers = Booking::whereHas('offering', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
            ->whereMonth('created_at', now()->month)
            ->distinct('customer_id')
            ->count('customer_id');

        $returningCustomers = Booking::whereHas('offering', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
            ->whereMonth('created_at', now()->month)
            ->whereExists(function ($query) use ($userId) {
                $query->select(\DB::raw(1))
                    ->from('bookings as b2')
                    ->whereRaw('b2.customer_id = bookings.customer_id')
                    ->whereHas('offering', function ($q) use ($userId) {
                        $q->where('user_id', $userId);
                    })
                    ->where('b2.created_at', '<', \DB::raw('bookings.created_at'));
            })
            ->distinct('customer_id')
            ->count('customer_id');

        return [
            'totalRevenue' => $totalRevenue,
            'totalBookings' => $totalBookings,
            'totalServices' => $totalServices,
            'activeServices' => $activeServices,
            'monthlyRevenue' => $monthlyRevenue,
            'topServices' => $topServices,
            'statusDistribution' => $statusDistribution,
            'newCustomers' => $newCustomers,
            'returningCustomers' => $returningCustomers,
        ];
    }

    public function getViewData(): array
    {
        return [
            'analytics' => $this->getAnalyticsData(),
        ];
    }
}
