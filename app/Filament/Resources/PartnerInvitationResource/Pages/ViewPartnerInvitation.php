<?php

namespace App\Filament\Resources\PartnerInvitationResource\Pages;

use App\Filament\Resources\PartnerInvitationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewPartnerInvitation extends ViewRecord
{
    protected static string $resource = PartnerInvitationResource::class;

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
                Infolists\Components\Section::make('معلومات الدعوة')
                    ->schema([
                        Infolists\Components\TextEntry::make('id')
                            ->label('رقم الدعوة')
                            ->prefix('#'),

                        Infolists\Components\TextEntry::make('partner.name')
                            ->label('الشريك المُرسِل'),

                        Infolists\Components\TextEntry::make('status')
                            ->label('الحالة')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'warning',
                                'accepted' => 'success',
                                'expired' => 'danger',
                                'cancelled' => 'gray',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'pending' => 'معلقة',
                                'accepted' => 'مقبولة',
                                'expired' => 'منتهية الصلاحية',
                                'cancelled' => 'ملغاة',
                                default => $state,
                            }),

                        Infolists\Components\TextEntry::make('token')
                            ->label('رمز الدعوة')
                            ->copyable(),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('معلومات التاجر')
                    ->schema([
                        Infolists\Components\TextEntry::make('merchant_name')
                            ->label('اسم التاجر'),

                        Infolists\Components\TextEntry::make('business_name')
                            ->label('اسم المتجر/النشاط'),

                        Infolists\Components\TextEntry::make('merchant_email')
                            ->label('البريد الإلكتروني')
                            ->copyable(),

                        Infolists\Components\TextEntry::make('merchant_phone')
                            ->label('رقم الهاتف')
                            ->copyable(),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('رسالة الدعوة')
                    ->schema([
                        Infolists\Components\TextEntry::make('message')
                            ->label('الرسالة')
                            ->placeholder('لا توجد رسالة مخصصة'),
                    ])
                    ->visible(fn ($record) => !empty($record->message)),

                Infolists\Components\Section::make('التوقيتات')
                    ->schema([
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('تاريخ الإنشاء')
                            ->dateTime(),

                        Infolists\Components\TextEntry::make('sent_at')
                            ->label('تاريخ الإرسال')
                            ->dateTime()
                            ->placeholder('لم يتم الإرسال'),

                        Infolists\Components\TextEntry::make('expires_at')
                            ->label('تاريخ انتهاء الصلاحية')
                            ->dateTime()
                            ->color(fn ($record) => $record->expires_at && $record->expires_at->isPast() ? 'danger' : 'success'),

                        Infolists\Components\TextEntry::make('accepted_at')
                            ->label('تاريخ القبول')
                            ->dateTime()
                            ->placeholder('لم يتم القبول'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('رابط الدعوة')
                    ->schema([
                        Infolists\Components\TextEntry::make('invitation_url')
                            ->label('رابط الدعوة')
                            ->getStateUsing(fn ($record) => route('partner.invitation.accept', $record->token))
                            ->copyable()
                            ->url(fn ($record) => route('partner.invitation.accept', $record->token))
                            ->openUrlInNewTab(),
                    ])
                    ->visible(fn ($record) => $record->status === 'pending'),

                Infolists\Components\Section::make('البيانات الإضافية')
                    ->schema([
                        Infolists\Components\KeyValueEntry::make('metadata')
                            ->label('البيانات الإضافية')
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => !empty($record->metadata)),

                Infolists\Components\Section::make('معلومات إضافية')
                    ->schema([
                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('آخر تحديث')
                            ->dateTime(),
                    ])
                    ->columns(1),
            ]);
    }
}
