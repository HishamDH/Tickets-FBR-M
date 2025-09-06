<?php

namespace App\Filament\Resources\PartnerWalletTransactionResource\Pages;

use App\Filament\Resources\PartnerWalletTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewPartnerWalletTransaction extends ViewRecord
{
    protected static string $resource = PartnerWalletTransactionResource::class;

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
                Infolists\Components\Section::make('معلومات المعاملة')
                    ->schema([
                        Infolists\Components\TextEntry::make('reference_number')
                            ->label('رقم المعاملة')
                            ->copyable(),

                        Infolists\Components\TextEntry::make('partnerWallet.partner.name')
                            ->label('الشريك'),

                        Infolists\Components\TextEntry::make('type')
                            ->label('نوع المعاملة')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'commission' => 'success',
                                'withdrawal' => 'warning',
                                'adjustment' => 'info',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'commission' => 'عمولة',
                                'withdrawal' => 'سحب',
                                'adjustment' => 'تعديل',
                                default => $state,
                            }),

                        Infolists\Components\TextEntry::make('category')
                            ->label('فئة المعاملة')
                            ->badge()
                            ->color('primary')
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'booking_commission' => 'عمولة حجز',
                                'merchant_referral' => 'مكافأة إحالة',
                                'performance_bonus' => 'مكافأة أداء',
                                'manual_adjustment' => 'تعديل يدوي',
                                'withdrawal_request' => 'طلب سحب',
                                default => $state,
                            }),

                        Infolists\Components\TextEntry::make('amount')
                            ->label('المبلغ')
                            ->money('SAR')
                            ->color(fn ($record) => $record->amount > 0 ? 'success' : 'danger'),

                        Infolists\Components\TextEntry::make('status')
                            ->label('الحالة')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'completed' => 'success',
                                'pending' => 'warning',
                                'failed' => 'danger',
                                'cancelled' => 'gray',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'completed' => 'مكتمل',
                                'pending' => 'معلق',
                                'failed' => 'فاشل',
                                'cancelled' => 'ملغي',
                                default => $state,
                            }),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('تفاصيل المعاملة')
                    ->schema([
                        Infolists\Components\TextEntry::make('description')
                            ->label('الوصف'),

                        Infolists\Components\TextEntry::make('reference_id')
                            ->label('رقم المرجع')
                            ->placeholder('غير محدد'),

                        Infolists\Components\TextEntry::make('reference_type')
                            ->label('نوع المرجع')
                            ->placeholder('غير محدد'),

                        Infolists\Components\TextEntry::make('notes')
                            ->label('ملاحظات')
                            ->placeholder('لا توجد ملاحظات'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('البيانات الإضافية')
                    ->schema([
                        Infolists\Components\KeyValueEntry::make('metadata')
                            ->label('البيانات الإضافية')
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => !empty($record->metadata)),

                Infolists\Components\Section::make('معلومات التوقيت')
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
