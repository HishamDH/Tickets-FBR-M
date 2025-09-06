<?php

namespace App\Filament\Resources\PartnerWalletResource\Pages;

use App\Filament\Resources\PartnerWalletResource;
use App\Services\PartnerCommissionService;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewPartnerWallet extends ViewRecord
{
    protected static string $resource = PartnerWalletResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('معلومات الشريك')
                    ->schema([
                        Infolists\Components\TextEntry::make('partner.name')
                            ->label('اسم الشريك'),
                        
                        Infolists\Components\TextEntry::make('partner.email')
                            ->label('البريد الإلكتروني'),
                        
                        Infolists\Components\TextEntry::make('partner.phone')
                            ->label('رقم الهاتف'),
                        
                        Infolists\Components\TextEntry::make('partner.commission_rate')
                            ->label('معدل العمولة')
                            ->suffix('%'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('تفاصيل المحفظة')
                    ->schema([
                        Infolists\Components\TextEntry::make('balance')
                            ->label('الرصيد الحالي')
                            ->money('SAR')
                            ->color('success'),

                        Infolists\Components\TextEntry::make('pending_balance')
                            ->label('الرصيد المعلق')
                            ->money('SAR')
                            ->color('warning'),

                        Infolists\Components\TextEntry::make('available_balance')
                            ->label('الرصيد المتاح للسحب')
                            ->money('SAR')
                            ->getStateUsing(fn ($record) => $record->available_balance)
                            ->color('primary'),

                        Infolists\Components\TextEntry::make('total_earned')
                            ->label('إجمالي الأرباح')
                            ->money('SAR')
                            ->color('success'),

                        Infolists\Components\TextEntry::make('total_withdrawn')
                            ->label('إجمالي المسحوب')
                            ->money('SAR')
                            ->color('info'),
                    ])
                    ->columns(3),

                Infolists\Components\Section::make('إعدادات السحب التلقائي')
                    ->schema([
                        Infolists\Components\IconEntry::make('auto_withdraw')
                            ->label('السحب التلقائي')
                            ->boolean()
                            ->trueIcon('heroicon-o-check-circle')
                            ->falseIcon('heroicon-o-x-circle')
                            ->trueColor('success')
                            ->falseColor('danger'),

                        Infolists\Components\TextEntry::make('auto_withdraw_threshold')
                            ->label('حد السحب التلقائي')
                            ->money('SAR')
                            ->placeholder('غير محدد'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('المعلومات المصرفية')
                    ->schema([
                        Infolists\Components\TextEntry::make('bank_name')
                            ->label('اسم البنك')
                            ->placeholder('غير محدد'),

                        Infolists\Components\TextEntry::make('bank_account_number')
                            ->label('رقم الحساب المصرفي')
                            ->placeholder('غير محدد'),

                        Infolists\Components\TextEntry::make('account_holder_name')
                            ->label('اسم صاحب الحساب')
                            ->placeholder('غير محدد'),

                        Infolists\Components\TextEntry::make('bank_routing_number')
                            ->label('رقم التوجيه المصرفي')
                            ->placeholder('غير محدد'),

                        Infolists\Components\TextEntry::make('swift_code')
                            ->label('رمز SWIFT')
                            ->placeholder('غير محدد'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('إحصائيات الأداء')
                    ->schema([
                        Infolists\Components\TextEntry::make('total_merchants')
                            ->label('إجمالي التجار')
                            ->getStateUsing(function ($record) {
                                $service = app(PartnerCommissionService::class);
                                $stats = $service->getPartnerStats($record->partner);
                                return $stats['total_merchants'];
                            }),

                        Infolists\Components\TextEntry::make('active_merchants')
                            ->label('التجار النشطون')
                            ->getStateUsing(function ($record) {
                                $service = app(PartnerCommissionService::class);
                                $stats = $service->getPartnerStats($record->partner);
                                return $stats['active_merchants'];
                            }),

                        Infolists\Components\TextEntry::make('monthly_bookings')
                            ->label('الحجوزات الشهرية')
                            ->getStateUsing(function ($record) {
                                $service = app(PartnerCommissionService::class);
                                $stats = $service->getPartnerStats($record->partner);
                                return $stats['monthly_bookings'];
                            }),

                        Infolists\Components\TextEntry::make('monthly_revenue')
                            ->label('الإيرادات الشهرية')
                            ->money('SAR')
                            ->getStateUsing(function ($record) {
                                $service = app(PartnerCommissionService::class);
                                $stats = $service->getPartnerStats($record->partner);
                                return $stats['monthly_revenue'];
                            }),

                        Infolists\Components\TextEntry::make('monthly_commission')
                            ->label('العمولة الشهرية')
                            ->money('SAR')
                            ->getStateUsing(function ($record) {
                                $service = app(PartnerCommissionService::class);
                                $stats = $service->getPartnerStats($record->partner);
                                return $stats['monthly_commission'];
                            }),
                    ])
                    ->columns(3),

                Infolists\Components\Section::make('معلومات إضافية')
                    ->schema([
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('تاريخ الإنشاء')
                            ->dateTime(),

                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('آخر تحديث')
                            ->dateTime(),
                    ])
                    ->columns(2),
            ]);
    }
}
