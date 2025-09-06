<?php

namespace App\Filament\Resources\BranchResource\Pages;

use App\Filament\Resources\BranchResource;
use App\Models\Branch;
use App\Models\PaidReservation;
use App\Models\WalletTransaction;
use Filament\Resources\Pages\Page;
use Filament\Widgets\Widget;
use Illuminate\Contracts\Support\Htmlable;

class BranchAnalytics extends Page
{
    protected static string $resource = BranchResource::class;

    protected static string $view = 'filament.resources.branch-resource.pages.branch-analytics';

    public Branch $record;

    protected static ?string $title = 'تحليلات الفرع';

    public function getTitle(): string|Htmlable
    {
        return "تحليلات فرع: {$this->record->name}";
    }

    public function getBreadcrumb(): string
    {
        return 'تحليلات';
    }

    protected function getHeaderWidgets(): array
    {
        return [
            BranchAnalyticsWidget::make(['record' => $this->record]),
        ];
    }

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }

    // إحصائيات الفرع
    public function getBranchStats(): array
    {
        $branch = $this->record;
        
        // إجمالي الحجوزات
        $totalBookings = PaidReservation::where('branch_id', $branch->id)->count();
        
        // الحجوزات هذا الشهر
        $monthlyBookings = PaidReservation::where('branch_id', $branch->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        // إجمالي الإيرادات
        $totalRevenue = WalletTransaction::whereHas('wallet', function($query) use ($branch) {
            $query->where('user_id', $branch->user_id);
        })
        ->where('type', 'credit')
        ->where('category', 'booking')
        ->where('metadata->branch_id', $branch->id)
        ->sum('amount');
        
        // الإيرادات هذا الشهر
        $monthlyRevenue = WalletTransaction::whereHas('wallet', function($query) use ($branch) {
            $query->where('user_id', $branch->user_id);
        })
        ->where('type', 'credit')
        ->where('category', 'booking')
        ->where('metadata->branch_id', $branch->id)
        ->whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->sum('amount');
        
        // متوسط قيمة الحجز
        $averageBookingValue = $totalBookings > 0 ? $totalRevenue / $totalBookings : 0;
        
        // معدل الإشغال (افتراضي بناءً على السعة)
        $occupancyRate = $branch->capacity > 0 && $monthlyBookings > 0 
            ? min(($monthlyBookings / ($branch->capacity * 30)) * 100, 100) 
            : 0;

        return [
            'total_bookings' => $totalBookings,
            'monthly_bookings' => $monthlyBookings,
            'total_revenue' => $totalRevenue,
            'monthly_revenue' => $monthlyRevenue,
            'average_booking_value' => $averageBookingValue,
            'occupancy_rate' => round($occupancyRate, 2),
        ];
    }

    // بيانات الرسم البياني للإيرادات الشهرية
    public function getMonthlyRevenueChart(): array
    {
        $data = [];
        $labels = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $month = $date->format('M Y');
            $revenue = WalletTransaction::whereHas('wallet', function($query) {
                $query->where('user_id', $this->record->user_id);
            })
            ->where('type', 'credit')
            ->where('category', 'booking')
            ->where('metadata->branch_id', $this->record->id)
            ->whereMonth('created_at', $date->month)
            ->whereYear('created_at', $date->year)
            ->sum('amount');
            
            $labels[] = $month;
            $data[] = $revenue;
        }
        
        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }
}
