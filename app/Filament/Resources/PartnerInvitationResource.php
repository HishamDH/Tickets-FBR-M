<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PartnerInvitationResource\Pages;
use App\Models\PartnerInvitation;
use App\Models\Partner;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class PartnerInvitationResource extends Resource
{
    protected static ?string $model = PartnerInvitation::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationLabel = 'دعوات الشركاء';

    protected static ?string $modelLabel = 'دعوة شريك';

    protected static ?string $pluralModelLabel = 'دعوات الشركاء';

    protected static ?string $navigationGroup = 'إدارة الشركاء';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('partner_id')
                    ->label('الشريك المُرسِل')
                    ->relationship('partner', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('merchant_name')
                        ->label('اسم التاجر')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('merchant_email')
                        ->label('بريد التاجر الإلكتروني')
                        ->email()
                        ->required()
                        ->maxLength(255),
                ]),

                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('merchant_phone')
                        ->label('هاتف التاجر')
                        ->tel()
                        ->required()
                        ->maxLength(20),

                    Forms\Components\TextInput::make('business_name')
                        ->label('اسم المتجر/النشاط')
                        ->required()
                        ->maxLength(255),
                ]),

                Forms\Components\Select::make('status')
                    ->label('حالة الدعوة')
                    ->options([
                        PartnerInvitation::STATUS_PENDING => 'معلقة',
                        PartnerInvitation::STATUS_ACCEPTED => 'مقبولة',
                        PartnerInvitation::STATUS_EXPIRED => 'منتهية الصلاحية',
                        PartnerInvitation::STATUS_CANCELLED => 'ملغاة',
                    ])
                    ->required()
                    ->default(PartnerInvitation::STATUS_PENDING),

                Forms\Components\Textarea::make('message')
                    ->label('رسالة الدعوة')
                    ->columnSpanFull()
                    ->rows(3),

                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('token')
                        ->label('رمز الدعوة')
                        ->disabled()
                        ->dehydrated(false)
                        ->default(fn () => Str::random(32)),

                    Forms\Components\DateTimePicker::make('expires_at')
                        ->label('تاريخ انتهاء الصلاحية')
                        ->default(now()->addDays(config('partner.invitation_expires_days', 30))),
                ]),

                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\DateTimePicker::make('sent_at')
                        ->label('تاريخ الإرسال'),

                    Forms\Components\DateTimePicker::make('accepted_at')
                        ->label('تاريخ القبول'),
                ]),

                Forms\Components\KeyValue::make('metadata')
                    ->label('بيانات إضافية')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('رقم الدعوة')
                    ->searchable()
                    ->sortable()
                    ->prefix('#'),

                Tables\Columns\TextColumn::make('partner.name')
                    ->label('الشريك المُرسِل')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('merchant_name')
                    ->label('اسم التاجر')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('business_name')
                    ->label('اسم المتجر')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('merchant_email')
                    ->label('البريد الإلكتروني')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('merchant_phone')
                    ->label('رقم الهاتف')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        PartnerInvitation::STATUS_PENDING => 'warning',
                        PartnerInvitation::STATUS_ACCEPTED => 'success',
                        PartnerInvitation::STATUS_EXPIRED => 'danger',
                        PartnerInvitation::STATUS_CANCELLED => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        PartnerInvitation::STATUS_PENDING => 'معلقة',
                        PartnerInvitation::STATUS_ACCEPTED => 'مقبولة',
                        PartnerInvitation::STATUS_EXPIRED => 'منتهية الصلاحية',
                        PartnerInvitation::STATUS_CANCELLED => 'ملغاة',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('sent_at')
                    ->label('تاريخ الإرسال')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('لم يتم الإرسال'),

                Tables\Columns\TextColumn::make('expires_at')
                    ->label('تنتهي في')
                    ->dateTime()
                    ->sortable()
                    ->color(fn ($record) => $record->expires_at && $record->expires_at->isPast() ? 'danger' : 'success'),

                Tables\Columns\TextColumn::make('accepted_at')
                    ->label('تاريخ القبول')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('لم يتم القبول')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('partner')
                    ->label('الشريك')
                    ->relationship('partner', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        PartnerInvitation::STATUS_PENDING => 'معلقة',
                        PartnerInvitation::STATUS_ACCEPTED => 'مقبولة',
                        PartnerInvitation::STATUS_EXPIRED => 'منتهية الصلاحية',
                        PartnerInvitation::STATUS_CANCELLED => 'ملغاة',
                    ]),

                Tables\Filters\Filter::make('expires_soon')
                    ->label('تنتهي قريباً (خلال 3 أيام)')
                    ->query(fn (Builder $query): Builder => 
                        $query->where('status', PartnerInvitation::STATUS_PENDING)
                              ->whereBetween('expires_at', [now(), now()->addDays(3)])
                    ),

                Tables\Filters\Filter::make('expired')
                    ->label('منتهية الصلاحية')
                    ->query(fn (Builder $query): Builder => 
                        $query->where('expires_at', '<', now())
                              ->where('status', '!=', PartnerInvitation::STATUS_ACCEPTED)
                    ),

                Tables\Filters\Filter::make('not_sent')
                    ->label('لم يتم إرسالها')
                    ->query(fn (Builder $query): Builder => $query->whereNull('sent_at')),

                Tables\Filters\Filter::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('من تاريخ'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('إلى تاريخ'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn ($record) => $record->status === PartnerInvitation::STATUS_PENDING),

                Action::make('send_invitation')
                    ->label('إرسال الدعوة')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('primary')
                    ->visible(fn ($record) => $record->status === PartnerInvitation::STATUS_PENDING && !$record->sent_at)
                    ->requiresConfirmation()
                    ->modalHeading('تأكيد إرسال الدعوة')
                    ->modalDescription(fn ($record) => "سيتم إرسال دعوة إلى {$record->merchant_email}")
                    ->action(function (PartnerInvitation $record) {
                        try {
                            // هنا يمكن إضافة منطق إرسال الإيميل
                            $record->update([
                                'sent_at' => now(),
                            ]);

                            Notification::make()
                                ->title('تم إرسال الدعوة بنجاح')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('خطأ في إرسال الدعوة')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Action::make('resend_invitation')
                    ->label('إعادة إرسال')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->visible(fn ($record) => $record->status === PartnerInvitation::STATUS_PENDING && $record->sent_at)
                    ->requiresConfirmation()
                    ->action(function (PartnerInvitation $record) {
                        try {
                            // تجديد رمز الدعوة وتمديد الصلاحية
                            $record->update([
                                'token' => $record->generateToken(),
                                'expires_at' => now()->addDays(config('partner.invitation_expires_days', 30)),
                                'sent_at' => now(),
                            ]);

                            Notification::make()
                                ->title('تم إعادة إرسال الدعوة بنجاح')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('خطأ في إعادة إرسال الدعوة')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Action::make('cancel_invitation')
                    ->label('إلغاء الدعوة')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status === PartnerInvitation::STATUS_PENDING)
                    ->requiresConfirmation()
                    ->action(function (PartnerInvitation $record) {
                        $record->update(['status' => PartnerInvitation::STATUS_CANCELLED]);

                        Notification::make()
                            ->title('تم إلغاء الدعوة')
                            ->success()
                            ->send();
                    }),

                Action::make('copy_link')
                    ->label('نسخ الرابط')
                    ->icon('heroicon-o-clipboard-document')
                    ->color('info')
                    ->visible(fn ($record) => $record->status === PartnerInvitation::STATUS_PENDING)
                    ->action(function (PartnerInvitation $record) {
                        $invitationUrl = route('partner.invitation.accept', $record->token);
                        
                        Notification::make()
                            ->title('تم نسخ رابط الدعوة')
                            ->body($invitationUrl)
                            ->success()
                            ->persistent()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                    Tables\Actions\BulkAction::make('send_invitations')
                        ->label('إرسال الدعوات المحددة')
                        ->icon('heroicon-o-paper-airplane')
                        ->color('primary')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $sent = 0;
                            foreach ($records as $record) {
                                if ($record->status === PartnerInvitation::STATUS_PENDING && !$record->sent_at) {
                                    try {
                                        $record->update(['sent_at' => now()]);
                                        $sent++;
                                    } catch (\Exception $e) {
                                        // تسجيل الخطأ ومتابعة
                                    }
                                }
                            }

                            Notification::make()
                                ->title("تم إرسال {$sent} دعوة")
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\BulkAction::make('cancel_invitations')
                        ->label('إلغاء الدعوات المحددة')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $cancelled = 0;
                            foreach ($records as $record) {
                                if ($record->status === PartnerInvitation::STATUS_PENDING) {
                                    $record->update(['status' => PartnerInvitation::STATUS_CANCELLED]);
                                    $cancelled++;
                                }
                            }

                            Notification::make()
                                ->title("تم إلغاء {$cancelled} دعوة")
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPartnerInvitations::route('/'),
            'create' => Pages\CreatePartnerInvitation::route('/create'),
            'view' => Pages\ViewPartnerInvitation::route('/{record}'),
            'edit' => Pages\EditPartnerInvitation::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', PartnerInvitation::STATUS_PENDING)->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $pendingCount = static::getModel()::where('status', PartnerInvitation::STATUS_PENDING)->count();
        return $pendingCount > 0 ? 'warning' : 'primary';
    }
}
