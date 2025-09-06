<?php

namespace App\Filament\Resources\PartnerWithdrawResource\Pages;

use App\Filament\Resources\PartnerWithdrawResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewPartnerWithdraw extends ViewRecord
{
    protected static string $resource = PartnerWithdrawResource::class;

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
                Infolists\Components\Section::make('معلومات طلب السحب')
                    ->schema([
                        Infolists\Components\TextEntry::make('id')
                            ->label('رقم الطلب')
                            ->prefix('#'),

                        Infolists\Components\TextEntry::make('partnerWallet.partner.name')
                            ->label('الشريك'),

                        Infolists\Components\TextEntry::make('amount')
                            ->label('مبلغ السحب')
                            ->money('SAR')
                            ->color('primary'),

                        Infolists\Components\TextEntry::make('status')
                            ->label('الحالة')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'warning',
                                'approved' => 'info',
                                'completed' => 'success',
                                'rejected' => 'danger',
                                'cancelled' => 'gray',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'pending' => 'معلق',
                                'approved' => 'موافق عليه',
                                'completed' => 'مكتمل',
                                'rejected' => 'مرفوض',
                                'cancelled' => 'ملغي',
                                default => $state,
                            }),

                        Infolists\Components\TextEntry::make('requested_at')
                            ->label('تاريخ الطلب')
                            ->dateTime(),

                        Infolists\Components\TextEntry::make('processed_at')
                            ->label('تاريخ المعالجة')
                            ->dateTime()
                            ->placeholder('لم تتم المعالجة'),

                        Infolists\Components\TextEntry::make('days_ago')
                            ->label('منذ')
                            ->getStateUsing(fn ($record) => $record->days_ago)
                            ->color(fn ($record) => $record->days_ago > 7 ? 'danger' : ($record->days_ago > 3 ? 'warning' : 'success')),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('المعلومات المصرفية')
                    ->schema([
                        Infolists\Components\TextEntry::make('bank_details.bank_name')
                            ->label('اسم البنك')
                            ->placeholder('غير محدد'),

                        Infolists\Components\TextEntry::make('bank_details.account_number')
                            ->label('رقم الحساب')
                            ->placeholder('غير محدد'),

                        Infolists\Components\TextEntry::make('bank_details.account_holder_name')
                            ->label('اسم صاحب الحساب')
                            ->placeholder('غير محدد'),

                        Infolists\Components\TextEntry::make('bank_details.routing_number')
                            ->label('رقم التوجيه')
                            ->placeholder('غير محدد'),

                        Infolists\Components\TextEntry::make('bank_details.swift_code')
                            ->label('رمز SWIFT')
                            ->placeholder('غير محدد'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('الملاحظات')
                    ->schema([
                        Infolists\Components\TextEntry::make('notes')
                            ->label('ملاحظات الشريك')
                            ->placeholder('لا توجد ملاحظات'),

                        Infolists\Components\TextEntry::make('admin_notes')
                            ->label('ملاحظات الإدارة')
                            ->placeholder('لا توجد ملاحظات من الإدارة'),
                    ])
                    ->columns(1),

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
