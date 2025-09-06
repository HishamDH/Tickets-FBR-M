<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PartnerWithdrawResource\Pages;
use App\Models\PartnerWithdraw;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PartnerWithdrawResource extends Resource
{
    protected static ?string $model = PartnerWithdraw::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-tray';

    protected static ?string $navigationLabel = 'طلبات السحب';

    protected static ?string $modelLabel = 'طلب سحب';

    protected static ?string $pluralModelLabel = 'طلبات السحب';

    protected static ?string $navigationGroup = 'إدارة الشركاء';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('partner_wallet_id')
                    ->label('محفظة الشريك')
                    ->relationship('partnerWallet', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->partner->name . ' - محفظة رقم ' . $record->id)
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('amount')
                        ->label('مبلغ السحب')
                        ->numeric()
                        ->required()
                        ->step(0.01)
                        ->suffix('ريال')
                        ->minValue(0.01),

                    Forms\Components\Select::make('status')
                        ->label('حالة الطلب')
                        ->options([
                            PartnerWithdraw::STATUS_PENDING => 'معلق',
                            PartnerWithdraw::STATUS_APPROVED => 'موافق عليه',
                            PartnerWithdraw::STATUS_REJECTED => 'مرفوض',
                            PartnerWithdraw::STATUS_COMPLETED => 'مكتمل',
                            PartnerWithdraw::STATUS_CANCELLED => 'ملغي',
                        ])
                        ->required()
                        ->default(PartnerWithdraw::STATUS_PENDING),
                ]),

                Forms\Components\Section::make('المعلومات المصرفية')
                    ->schema([
                        Forms\Components\KeyValue::make('bank_details')
                            ->label('تفاصيل البنك')
                            ->keyLabel('المفتاح')
                            ->valueLabel('القيمة')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Textarea::make('notes')
                    ->label('ملاحظات')
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('admin_notes')
                    ->label('ملاحظات الإدارة')
                    ->columnSpanFull(),

                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\DateTimePicker::make('requested_at')
                        ->label('تاريخ الطلب')
                        ->default(now()),

                    Forms\Components\DateTimePicker::make('processed_at')
                        ->label('تاريخ المعالجة'),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('رقم الطلب')
                    ->searchable()
                    ->sortable()
                    ->prefix('#'),

                Tables\Columns\TextColumn::make('partnerWallet.partner.name')
                    ->label('الشريك')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('مبلغ السحب')
                    ->money('SAR')
                    ->sortable()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        PartnerWithdraw::STATUS_PENDING => 'warning',
                        PartnerWithdraw::STATUS_APPROVED => 'info',
                        PartnerWithdraw::STATUS_COMPLETED => 'success',
                        PartnerWithdraw::STATUS_REJECTED => 'danger',
                        PartnerWithdraw::STATUS_CANCELLED => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        PartnerWithdraw::STATUS_PENDING => 'معلق',
                        PartnerWithdraw::STATUS_APPROVED => 'موافق عليه',
                        PartnerWithdraw::STATUS_COMPLETED => 'مكتمل',
                        PartnerWithdraw::STATUS_REJECTED => 'مرفوض',
                        PartnerWithdraw::STATUS_CANCELLED => 'ملغي',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('bank_details')
                    ->label('البنك')
                    ->getStateUsing(fn ($record) => $record->bank_details['bank_name'] ?? 'غير محدد')
                    ->searchable(),

                Tables\Columns\TextColumn::make('requested_at')
                    ->label('تاريخ الطلب')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('processed_at')
                    ->label('تاريخ المعالجة')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('لم تتم المعالجة'),

                Tables\Columns\TextColumn::make('days_ago')
                    ->label('منذ')
                    ->getStateUsing(fn ($record) => $record->days_ago)
                    ->color(fn ($record) => $record->days_ago > 7 ? 'danger' : ($record->days_ago > 3 ? 'warning' : 'success')),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('partner_wallet_id')
                    ->label('الشريك')
                    ->relationship('partnerWallet.partner', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        PartnerWithdraw::STATUS_PENDING => 'معلق',
                        PartnerWithdraw::STATUS_APPROVED => 'موافق عليه',
                        PartnerWithdraw::STATUS_REJECTED => 'مرفوض',
                        PartnerWithdraw::STATUS_COMPLETED => 'مكتمل',
                        PartnerWithdraw::STATUS_CANCELLED => 'ملغي',
                    ]),

                Tables\Filters\Filter::make('amount_range')
                    ->label('نطاق المبلغ')
                    ->form([
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('amount_from')
                                ->label('من')
                                ->numeric()
                                ->suffix('ريال'),
                            Forms\Components\TextInput::make('amount_to')
                                ->label('إلى')
                                ->numeric()
                                ->suffix('ريال'),
                        ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['amount_from'],
                                fn (Builder $query, $amount): Builder => $query->where('amount', '>=', $amount),
                            )
                            ->when(
                                $data['amount_to'],
                                fn (Builder $query, $amount): Builder => $query->where('amount', '<=', $amount),
                            );
                    }),

                Tables\Filters\Filter::make('requested_at')
                    ->label('تاريخ الطلب')
                    ->form([
                        Forms\Components\DatePicker::make('requested_from')
                            ->label('من تاريخ'),
                        Forms\Components\DatePicker::make('requested_until')
                            ->label('إلى تاريخ'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['requested_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('requested_at', '>=', $date),
                            )
                            ->when(
                                $data['requested_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('requested_at', '<=', $date),
                            );
                    }),

                Tables\Filters\Filter::make('overdue')
                    ->label('متأخرة (أكثر من 7 أيام)')
                    ->query(fn (Builder $query): Builder => 
                        $query->where('status', PartnerWithdraw::STATUS_PENDING)
                              ->where('requested_at', '<', now()->subDays(7))
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn ($record) => in_array($record->status, [
                        PartnerWithdraw::STATUS_PENDING,
                        PartnerWithdraw::STATUS_APPROVED
                    ])),

                Action::make('approve')
                    ->label('موافقة')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === PartnerWithdraw::STATUS_PENDING)
                    ->form([
                        Forms\Components\Textarea::make('admin_notes')
                            ->label('ملاحظات الموافقة')
                            ->placeholder('اختياري - إضافة ملاحظات للموافقة'),
                    ])
                    ->requiresConfirmation()
                    ->modalHeading('تأكيد الموافقة على طلب السحب')
                    ->modalDescription(fn ($record) => "سيتم الموافقة على طلب سحب بمبلغ {$record->amount} ريال")
                    ->action(function (PartnerWithdraw $record, array $data) {
                        try {
                            $record->approve($data['admin_notes'] ?? null);
                            
                            Notification::make()
                                ->title('تم الموافقة على طلب السحب')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('خطأ في الموافقة على طلب السحب')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Action::make('reject')
                    ->label('رفض')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status === PartnerWithdraw::STATUS_PENDING)
                    ->form([
                        Forms\Components\Textarea::make('admin_notes')
                            ->label('سبب الرفض')
                            ->required()
                            ->placeholder('يرجى تحديد سبب رفض الطلب'),
                    ])
                    ->requiresConfirmation()
                    ->modalHeading('تأكيد رفض طلب السحب')
                    ->modalDescription(fn ($record) => "سيتم رفض طلب سحب بمبلغ {$record->amount} ريال")
                    ->action(function (PartnerWithdraw $record, array $data) {
                        try {
                            $record->reject($data['admin_notes']);
                            
                            Notification::make()
                                ->title('تم رفض طلب السحب')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('خطأ في رفض طلب السحب')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Action::make('complete')
                    ->label('اكتمال')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === PartnerWithdraw::STATUS_APPROVED)
                    ->form([
                        Forms\Components\Textarea::make('admin_notes')
                            ->label('ملاحظات الإكمال')
                            ->placeholder('اختياري - إضافة ملاحظات للإكمال'),
                    ])
                    ->requiresConfirmation()
                    ->modalHeading('تأكيد إكمال عملية السحب')
                    ->modalDescription(fn ($record) => "سيتم تأكيد إكمال عملية سحب بمبلغ {$record->amount} ريال")
                    ->action(function (PartnerWithdraw $record, array $data) {
                        try {
                            $record->markAsCompleted($data['admin_notes'] ?? null);
                            
                            Notification::make()
                                ->title('تم إكمال عملية السحب')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('خطأ في إكمال عملية السحب')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                    Tables\Actions\BulkAction::make('bulk_approve')
                        ->label('موافقة على المحدد')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->form([
                            Forms\Components\Textarea::make('admin_notes')
                                ->label('ملاحظات الموافقة')
                                ->placeholder('اختياري - ملاحظات عامة للموافقة'),
                        ])
                        ->requiresConfirmation()
                        ->action(function ($records, array $data) {
                            $approved = 0;
                            foreach ($records as $record) {
                                if ($record->status === PartnerWithdraw::STATUS_PENDING) {
                                    try {
                                        $record->approve($data['admin_notes'] ?? null);
                                        $approved++;
                                    } catch (\Exception $e) {
                                        // تسجيل الخطأ ومتابعة
                                    }
                                }
                            }

                            Notification::make()
                                ->title("تم الموافقة على {$approved} طلب سحب")
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\BulkAction::make('bulk_reject')
                        ->label('رفض المحدد')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->form([
                            Forms\Components\Textarea::make('admin_notes')
                                ->label('سبب الرفض')
                                ->required()
                                ->placeholder('يرجى تحديد سبب رفض الطلبات'),
                        ])
                        ->requiresConfirmation()
                        ->action(function ($records, array $data) {
                            $rejected = 0;
                            foreach ($records as $record) {
                                if ($record->status === PartnerWithdraw::STATUS_PENDING) {
                                    try {
                                        $record->reject($data['admin_notes']);
                                        $rejected++;
                                    } catch (\Exception $e) {
                                        // تسجيل الخطأ ومتابعة
                                    }
                                }
                            }

                            Notification::make()
                                ->title("تم رفض {$rejected} طلب سحب")
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('requested_at', 'desc');
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
            'index' => Pages\ListPartnerWithdraws::route('/'),
            'create' => Pages\CreatePartnerWithdraw::route('/create'),
            'view' => Pages\ViewPartnerWithdraw::route('/{record}'),
            'edit' => Pages\EditPartnerWithdraw::route('/{record}/edit'),
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
        return static::getModel()::where('status', PartnerWithdraw::STATUS_PENDING)->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $pendingCount = static::getModel()::where('status', PartnerWithdraw::STATUS_PENDING)->count();
        return $pendingCount > 0 ? 'warning' : 'primary';
    }
}
