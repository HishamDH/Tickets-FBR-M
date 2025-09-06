<?php

namespace App\Filament\Resources\LoyaltyProgramResource\Pages;

use App\Filament\Resources\LoyaltyProgramResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewLoyaltyProgram extends ViewRecord
{
    protected static string $resource = LoyaltyProgramResource::class;

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
                        Infolists\Components\TextEntry::make('type')
                            ->label('نوع البرنامج')
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'points' => 'نقاط',
                                'cashback' => 'استرداد نقدي',
                                'tier' => 'مستويات',
                                default => $state,
                            })
                            ->badge(),
                        Infolists\Components\TextEntry::make('description')
                            ->label('الوصف')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('إعدادات النقاط')
                    ->schema([
                        Infolists\Components\TextEntry::make('points_per_amount')
                            ->label('النقاط لكل ريال')
                            ->suffix(' نقطة'),
                        Infolists\Components\TextEntry::make('redemption_rate')
                            ->label('قيمة النقطة الواحدة')
                            ->formatStateUsing(fn (float $state): string => number_format($state, 3) . ' ر.س'),
                        Infolists\Components\TextEntry::make('minimum_points')
                            ->label('الحد الأدنى للاسترداد')
                            ->suffix(' نقطة'),
                        Infolists\Components\TextEntry::make('maximum_points_per_transaction')
                            ->label('الحد الأقصى لكل معاملة')
                            ->formatStateUsing(fn (?int $state): string => $state ? $state . ' نقطة' : 'غير محدود'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('الإحصائيات')
                    ->schema([
                        Infolists\Components\TextEntry::make('total_points_awarded')
                            ->label('إجمالي النقاط الممنوحة')
                            ->getStateUsing(function ($record): string {
                                $total = $record->points()->sum('points');
                                return number_format($total) . ' نقطة';
                            })
                            ->badge()
                            ->color('success'),
                        Infolists\Components\TextEntry::make('total_points_redeemed')
                            ->label('إجمالي النقاط المستردة')
                            ->getStateUsing(function ($record): string {
                                $total = abs($record->transactions()->where('type', 'redeem')->sum('points'));
                                return number_format($total) . ' نقطة';
                            })
                            ->badge()
                            ->color('warning'),
                        Infolists\Components\TextEntry::make('active_users')
                            ->label('المستخدمون النشطون')
                            ->getStateUsing(function ($record): string {
                                $count = $record->points()
                                               ->where('points', '>', 0)
                                               ->whereNull('used_at')
                                               ->distinct('user_id')
                                               ->count('user_id');
                                return number_format($count) . ' مستخدم';
                            })
                            ->badge()
                            ->color('info'),
                        Infolists\Components\TextEntry::make('total_transactions')
                            ->label('إجمالي المعاملات')
                            ->getStateUsing(function ($record): string {
                                $count = $record->transactions()->count();
                                return number_format($count) . ' معاملة';
                            })
                            ->badge()
                            ->color('primary'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('الحالة والتواريخ')
                    ->schema([
                        Infolists\Components\IconEntry::make('is_active')
                            ->label('الحالة')
                            ->boolean()
                            ->trueIcon('heroicon-o-check-circle')
                            ->falseIcon('heroicon-o-x-circle')
                            ->trueColor('success')
                            ->falseColor('danger'),
                        Infolists\Components\TextEntry::make('expiry_months')
                            ->label('فترة انتهاء النقاط')
                            ->formatStateUsing(fn (?int $state): string => $state ? $state . ' شهر' : 'لا تنتهي'),
                        Infolists\Components\TextEntry::make('merchant.business_name')
                            ->label('التاجر')
                            ->placeholder('برنامج عام'),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('تاريخ الإنشاء')
                            ->dateTime('d/m/Y H:i'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('مستويات الولاء')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('tier_benefits')
                            ->label('المستويات')
                            ->schema([
                                Infolists\Components\TextEntry::make('name')
                                    ->label('اسم المستوى'),
                                Infolists\Components\TextEntry::make('required_points')
                                    ->label('النقاط المطلوبة')
                                    ->suffix(' نقطة'),
                                Infolists\Components\TextEntry::make('bonus_multiplier')
                                    ->label('مضاعف المكافآت')
                                    ->suffix('x'),
                                Infolists\Components\TextEntry::make('benefits')
                                    ->label('المزايا')
                                    ->listWithLineBreaks()
                                    ->columnSpanFull(),
                            ])
                            ->columns(3)
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => !empty($record->tier_benefits)),
            ]);
    }
}
