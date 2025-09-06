<?php

namespace App\Filament\Resources\ReferralProgramResource\Pages;

use App\Filament\Resources\ReferralProgramResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewReferralProgram extends ViewRecord
{
    protected static string $resource = ReferralProgramResource::class;

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
                Infolists\Components\Section::make('معلومات البرنامج')
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->label('اسم البرنامج'),
                        Infolists\Components\TextEntry::make('merchant.business_name')
                            ->label('التاجر')
                            ->placeholder('برنامج عام'),
                        Infolists\Components\TextEntry::make('description')
                            ->label('الوصف')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('مكافآت البرنامج')
                    ->schema([
                        Infolists\Components\TextEntry::make('referrer_reward_display')
                            ->label('مكافأة المُحيل')
                            ->getStateUsing(function ($record): string {
                                return match ($record->referrer_reward_type) {
                                    'fixed' => number_format($record->referrer_reward_value, 2) . ' ر.س',
                                    'percentage' => $record->referrer_reward_value . '%',
                                    'points' => $record->referrer_reward_value . ' نقطة',
                                    default => $record->referrer_reward_value,
                                };
                            })
                            ->badge()
                            ->color('success'),
                        Infolists\Components\TextEntry::make('referee_reward_display')
                            ->label('مكافأة المُحال إليه')
                            ->getStateUsing(function ($record): string {
                                return match ($record->referee_reward_type) {
                                    'fixed' => number_format($record->referee_reward_value, 2) . ' ر.س',
                                    'percentage' => $record->referee_reward_value . '%',
                                    'points' => $record->referee_reward_value . ' نقطة',
                                    default => $record->referee_reward_value,
                                };
                            })
                            ->badge()
                            ->color('info'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('شروط وحدود البرنامج')
                    ->schema([
                        Infolists\Components\TextEntry::make('minimum_order_amount')
                            ->label('الحد الأدنى لمبلغ الطلب')
                            ->formatStateUsing(fn (?float $state): string => 
                                $state ? number_format($state, 2) . ' ر.س' : 'لا يوجد'
                            ),
                        Infolists\Components\TextEntry::make('maximum_uses_per_referrer')
                            ->label('الحد الأقصى لكل مُحيل')
                            ->formatStateUsing(fn (?int $state): string => $state ?: 'غير محدود'),
                        Infolists\Components\TextEntry::make('maximum_total_uses')
                            ->label('الحد الأقصى الإجمالي')
                            ->formatStateUsing(fn (?int $state): string => $state ?: 'غير محدود'),
                        Infolists\Components\TextEntry::make('validity_days')
                            ->label('فترة صلاحية الرمز')
                            ->formatStateUsing(fn (?int $state): string => $state ? $state . ' يوم' : 'لا تنتهي'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('إحصائيات الأداء')
                    ->schema([
                        Infolists\Components\TextEntry::make('total_referrals')
                            ->label('إجمالي الإحالات')
                            ->getStateUsing(function ($record): string {
                                $count = $record->referrals()->count();
                                return number_format($count);
                            })
                            ->badge()
                            ->color('gray'),
                        Infolists\Components\TextEntry::make('successful_referrals')
                            ->label('الإحالات الناجحة')
                            ->getStateUsing(function ($record): string {
                                $count = $record->referrals()->where('is_successful', true)->count();
                                return number_format($count);
                            })
                            ->badge()
                            ->color('success'),
                        Infolists\Components\TextEntry::make('pending_referrals')
                            ->label('الإحالات المعلقة')
                            ->getStateUsing(function ($record): string {
                                $count = $record->referrals()->where('is_successful', false)->whereNull('referee_id')->count();
                                return number_format($count);
                            })
                            ->badge()
                            ->color('warning'),
                        Infolists\Components\TextEntry::make('conversion_rate')
                            ->label('معدل التحويل')
                            ->getStateUsing(function ($record): string {
                                $total = $record->referrals()->count();
                                $successful = $record->referrals()->where('is_successful', true)->count();
                                $rate = $total > 0 ? ($successful / $total) * 100 : 0;
                                return number_format($rate, 1) . '%';
                            })
                            ->badge()
                            ->color('info'),
                        Infolists\Components\TextEntry::make('total_rewards')
                            ->label('إجمالي المكافآت')
                            ->getStateUsing(function ($record): string {
                                $total = $record->referrals()
                                               ->with('rewards')
                                               ->get()
                                               ->sum(function ($referral) {
                                                   return $referral->rewards->sum('reward_value');
                                               });
                                return number_format($total, 2) . ' ر.س';
                            })
                            ->badge()
                            ->color('success'),
                        Infolists\Components\TextEntry::make('active_referrers')
                            ->label('المُحيلون النشطون')
                            ->getStateUsing(function ($record): string {
                                $count = $record->referrals()
                                               ->where('is_successful', true)
                                               ->distinct('referrer_id')
                                               ->count('referrer_id');
                                return number_format($count);
                            })
                            ->badge()
                            ->color('primary'),
                    ])
                    ->columns(3),

                Infolists\Components\Section::make('الحالة والتواريخ')
                    ->schema([
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
                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('آخر تحديث')
                            ->dateTime('d/m/Y H:i'),
                    ])
                    ->columns(3),
            ]);
    }
}
