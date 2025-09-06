<?php

namespace App\Filament\Resources\CouponResource\Pages;

use App\Filament\Resources\CouponResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewCoupon extends ViewRecord
{
    protected static string $resource = CouponResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('تعديل'),
            Actions\DeleteAction::make()
                ->label('حذف'),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('معلومات الكوبون')
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->label('اسم الكوبون'),
                        Infolists\Components\TextEntry::make('code')
                            ->label('الكود')
                            ->badge()
                            ->color('success'),
                        Infolists\Components\TextEntry::make('description')
                            ->label('الوصف')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('تفاصيل الخصم')
                    ->schema([
                        Infolists\Components\TextEntry::make('discount_type')
                            ->label('نوع الخصم')
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'fixed' => 'مبلغ ثابت',
                                'percentage' => 'نسبة مئوية',
                                default => $state,
                            }),
                        Infolists\Components\TextEntry::make('discount_value')
                            ->label('قيمة الخصم')
                            ->formatStateUsing(function ($record): string {
                                if ($record->discount_type === 'fixed') {
                                    return number_format($record->discount_value, 2) . ' ر.س';
                                }
                                return $record->discount_value . '%';
                            }),
                        Infolists\Components\TextEntry::make('minimum_amount')
                            ->label('الحد الأدنى للمبلغ')
                            ->formatStateUsing(fn (?float $state): string => 
                                $state ? number_format($state, 2) . ' ر.س' : 'لا يوجد'
                            ),
                        Infolists\Components\TextEntry::make('maximum_discount')
                            ->label('الحد الأقصى للخصم')
                            ->formatStateUsing(fn (?float $state): string => 
                                $state ? number_format($state, 2) . ' ر.س' : 'لا يوجد'
                            ),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('حدود الاستخدام')
                    ->schema([
                        Infolists\Components\TextEntry::make('usage_limit')
                            ->label('العدد الإجمالي للاستخدامات')
                            ->formatStateUsing(fn (?int $state): string => $state ?: 'غير محدود'),
                        Infolists\Components\TextEntry::make('usage_count')
                            ->label('عدد الاستخدامات الحالي')
                            ->badge()
                            ->color('info'),
                        Infolists\Components\TextEntry::make('usage_limit_per_user')
                            ->label('الحد للمستخدم الواحد')
                            ->formatStateUsing(fn (?int $state): string => $state ?: 'غير محدود'),
                        Infolists\Components\TextEntry::make('remaining_uses')
                            ->label('الاستخدامات المتبقية')
                            ->getStateUsing(function ($record): string {
                                if (!$record->usage_limit) return 'غير محدود';
                                $remaining = $record->usage_limit - $record->usage_count;
                                return $remaining > 0 ? $remaining : 'منتهي';
                            })
                            ->badge()
                            ->color(function ($record): string {
                                if (!$record->usage_limit) return 'gray';
                                $remaining = $record->usage_limit - $record->usage_count;
                                if ($remaining <= 0) return 'danger';
                                if ($remaining <= 5) return 'warning';
                                return 'success';
                            }),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('التواريخ والحالة')
                    ->schema([
                        Infolists\Components\TextEntry::make('starts_at')
                            ->label('تاريخ البداية')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('فوري'),
                        Infolists\Components\TextEntry::make('expires_at')
                            ->label('تاريخ الانتهاء')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('لا ينتهي'),
                        Infolists\Components\IconEntry::make('is_active')
                            ->label('الحالة')
                            ->boolean()
                            ->trueIcon('heroicon-o-check-circle')
                            ->falseIcon('heroicon-o-x-circle')
                            ->trueColor('success')
                            ->falseColor('danger'),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('تاريخ الإنشاء')
                            ->dateTime('d/m/Y H:i'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('النطاق والاستهداف')
                    ->schema([
                        Infolists\Components\TextEntry::make('merchant.business_name')
                            ->label('التاجر')
                            ->placeholder('جميع التجار'),
                        Infolists\Components\TextEntry::make('service.title')
                            ->label('الخدمة')
                            ->placeholder('جميع الخدمات'),
                        Infolists\Components\TextEntry::make('user.name')
                            ->label('مستخدم محدد')
                            ->placeholder('جميع المستخدمين'),
                    ])
                    ->columns(3)
                    ->visible(fn ($record) => $record->merchant_id || $record->service_id || $record->user_id),
            ]);
    }
}
