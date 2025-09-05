<?php

namespace App\Filament\Resources\ServiceResource\Widgets;

use App\Models\Review;
use App\Models\Service;
use App\Models\Booking;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ServiceStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('إجمالي الخدمات', Service::count())
                ->description('العدد الإجمالي للخدمات')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('success'),

            Stat::make('الخدمات النشطة', Service::where('status', 'active')->count())
                ->description('الخدمات المنشورة حاليًا')
                ->descriptionIcon('heroicon-m-eye')
                ->color('info'),

            Stat::make('إجمالي الحجوزات', Booking::count())
                ->description('عدد الحجوزات الكلي')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('warning'),

            Stat::make('الحجوزات المدفوعة', Booking::where('payment_status', 'paid')->count())
                ->description('الحجوزات المكتملة الدفع')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),

            Stat::make('متوسط التقييم',
                Review::where('is_approved', true)->avg('rating')
                    ? number_format(Review::where('is_approved', true)->avg('rating'), 1).'/5'
                    : 'لا توجد تقييمات'
            )
                ->description('متوسط تقييمات العملاء')
                ->descriptionIcon('heroicon-m-star')
                ->color('warning'),

            Stat::make('إجمالي الإيرادات',
                'ر.س '.number_format(
                    Booking::where('payment_status', 'paid')->sum('total_amount'),
                    2
                )
            )
                ->description('الإيرادات من الحجوزات المدفوعة')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
        ];
    }
}
