<?php

namespace App\Filament\Resources\OfferingResource\Widgets;

use App\Models\Offering;
use App\Models\PaidReservation;
use App\Models\CustomerRating;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OfferingStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('إجمالي العروض', Offering::count())
                ->description('العدد الإجمالي للعروض')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('success'),
            
            Stat::make('العروض النشطة', Offering::where('status', 'active')->count())
                ->description('العروض المنشورة حاليًا')
                ->descriptionIcon('heroicon-m-eye')
                ->color('info'),
            
            Stat::make('إجمالي الحجوزات', PaidReservation::count())
                ->description('عدد الحجوزات الكلي')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('warning'),
            
            Stat::make('الحجوزات المدفوعة', PaidReservation::where('payment_status', 'paid')->count())
                ->description('الحجوزات المكتملة الدفع')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),
            
            Stat::make('متوسط التقييم', 
                CustomerRating::where('is_approved', true)->avg('rating') 
                    ? number_format(CustomerRating::where('is_approved', true)->avg('rating'), 1) . '/5'
                    : 'لا توجد تقييمات'
            )
                ->description('متوسط تقييمات العملاء')
                ->descriptionIcon('heroicon-m-star')
                ->color('warning'),
            
            Stat::make('إجمالي الإيرادات', 
                'ر.س ' . number_format(
                    PaidReservation::where('payment_status', 'paid')->sum('total_amount'), 
                    2
                )
            )
                ->description('الإيرادات من الحجوزات المدفوعة')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
        ];
    }
}
