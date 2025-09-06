<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PartnerWalletTransactionResource\Pages;
use App\Models\PartnerWalletTransaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PartnerWalletTransactionResource extends Resource
{
    protected static ?string $model = PartnerWalletTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';

    protected static ?string $navigationLabel = 'معاملات المحافظ';

    protected static ?string $modelLabel = 'معاملة محفظة';

    protected static ?string $pluralModelLabel = 'معاملات المحافظ';

    protected static ?string $navigationGroup = 'إدارة الشركاء';

    protected static ?int $navigationSort = 3;

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
                    Forms\Components\Select::make('type')
                        ->label('نوع المعاملة')
                        ->options([
                            PartnerWalletTransaction::TYPE_COMMISSION => 'عمولة',
                            PartnerWalletTransaction::TYPE_WITHDRAWAL => 'سحب',
                            PartnerWalletTransaction::TYPE_ADJUSTMENT => 'تعديل',
                        ])
                        ->required(),

                    Forms\Components\Select::make('category')
                        ->label('فئة المعاملة')
                        ->options([
                            PartnerWalletTransaction::CATEGORY_BOOKING_COMMISSION => 'عمولة حجز',
                            PartnerWalletTransaction::CATEGORY_MERCHANT_REFERRAL => 'مكافأة إحالة تاجر',
                            PartnerWalletTransaction::CATEGORY_PERFORMANCE_BONUS => 'مكافأة أداء',
                            PartnerWalletTransaction::CATEGORY_MANUAL_ADJUSTMENT => 'تعديل يدوي',
                            PartnerWalletTransaction::CATEGORY_WITHDRAWAL_REQUEST => 'طلب سحب',
                        ])
                        ->required(),
                ]),

                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('amount')
                        ->label('المبلغ')
                        ->numeric()
                        ->required()
                        ->step(0.01)
                        ->suffix('ريال'),

                    Forms\Components\Select::make('status')
                        ->label('الحالة')
                        ->options([
                            PartnerWalletTransaction::STATUS_PENDING => 'معلق',
                            PartnerWalletTransaction::STATUS_COMPLETED => 'مكتمل',
                            PartnerWalletTransaction::STATUS_FAILED => 'فاشل',
                            PartnerWalletTransaction::STATUS_CANCELLED => 'ملغي',
                        ])
                        ->required()
                        ->default(PartnerWalletTransaction::STATUS_PENDING),
                ]),

                Forms\Components\Textarea::make('description')
                    ->label('الوصف')
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('reference_id')
                    ->label('رقم المرجع')
                    ->maxLength(255),

                Forms\Components\TextInput::make('reference_type')
                    ->label('نوع المرجع')
                    ->maxLength(255),

                Forms\Components\KeyValue::make('metadata')
                    ->label('بيانات إضافية')
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('notes')
                    ->label('ملاحظات')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference_number')
                    ->label('رقم المعاملة')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('partnerWallet.partner.name')
                    ->label('الشريك')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('النوع')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        PartnerWalletTransaction::TYPE_COMMISSION => 'success',
                        PartnerWalletTransaction::TYPE_WITHDRAWAL => 'warning',
                        PartnerWalletTransaction::TYPE_ADJUSTMENT => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        PartnerWalletTransaction::TYPE_COMMISSION => 'عمولة',
                        PartnerWalletTransaction::TYPE_WITHDRAWAL => 'سحب',
                        PartnerWalletTransaction::TYPE_ADJUSTMENT => 'تعديل',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('category')
                    ->label('الفئة')
                    ->badge()
                    ->color('primary')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        PartnerWalletTransaction::CATEGORY_BOOKING_COMMISSION => 'عمولة حجز',
                        PartnerWalletTransaction::CATEGORY_MERCHANT_REFERRAL => 'مكافأة إحالة',
                        PartnerWalletTransaction::CATEGORY_PERFORMANCE_BONUS => 'مكافأة أداء',
                        PartnerWalletTransaction::CATEGORY_MANUAL_ADJUSTMENT => 'تعديل يدوي',
                        PartnerWalletTransaction::CATEGORY_WITHDRAWAL_REQUEST => 'طلب سحب',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('amount')
                    ->label('المبلغ')
                    ->money('SAR')
                    ->sortable()
                    ->color(fn ($record) => $record->amount > 0 ? 'success' : 'danger'),

                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        PartnerWalletTransaction::STATUS_COMPLETED => 'success',
                        PartnerWalletTransaction::STATUS_PENDING => 'warning',
                        PartnerWalletTransaction::STATUS_FAILED => 'danger',
                        PartnerWalletTransaction::STATUS_CANCELLED => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        PartnerWalletTransaction::STATUS_COMPLETED => 'مكتمل',
                        PartnerWalletTransaction::STATUS_PENDING => 'معلق',
                        PartnerWalletTransaction::STATUS_FAILED => 'فاشل',
                        PartnerWalletTransaction::STATUS_CANCELLED => 'ملغي',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('description')
                    ->label('الوصف')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 50 ? $state : null;
                    }),

                Tables\Columns\TextColumn::make('reference_id')
                    ->label('رقم المرجع')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('partner_wallet_id')
                    ->label('محفظة الشريك')
                    ->relationship('partnerWallet.partner', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('type')
                    ->label('نوع المعاملة')
                    ->options([
                        PartnerWalletTransaction::TYPE_COMMISSION => 'عمولة',
                        PartnerWalletTransaction::TYPE_WITHDRAWAL => 'سحب',
                        PartnerWalletTransaction::TYPE_ADJUSTMENT => 'تعديل',
                    ]),

                Tables\Filters\SelectFilter::make('category')
                    ->label('فئة المعاملة')
                    ->options([
                        PartnerWalletTransaction::CATEGORY_BOOKING_COMMISSION => 'عمولة حجز',
                        PartnerWalletTransaction::CATEGORY_MERCHANT_REFERRAL => 'مكافأة إحالة تاجر',
                        PartnerWalletTransaction::CATEGORY_PERFORMANCE_BONUS => 'مكافأة أداء',
                        PartnerWalletTransaction::CATEGORY_MANUAL_ADJUSTMENT => 'تعديل يدوي',
                        PartnerWalletTransaction::CATEGORY_WITHDRAWAL_REQUEST => 'طلب سحب',
                    ]),

                Tables\Filters\SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        PartnerWalletTransaction::STATUS_PENDING => 'معلق',
                        PartnerWalletTransaction::STATUS_COMPLETED => 'مكتمل',
                        PartnerWalletTransaction::STATUS_FAILED => 'فاشل',
                        PartnerWalletTransaction::STATUS_CANCELLED => 'ملغي',
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
                    ->visible(fn ($record) => $record->status === PartnerWalletTransaction::STATUS_PENDING),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListPartnerWalletTransactions::route('/'),
            'create' => Pages\CreatePartnerWalletTransaction::route('/create'),
            'view' => Pages\ViewPartnerWalletTransaction::route('/{record}'),
            'edit' => Pages\EditPartnerWalletTransaction::route('/{record}/edit'),
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
        return static::getModel()::where('status', PartnerWalletTransaction::STATUS_PENDING)->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $pendingCount = static::getModel()::where('status', PartnerWalletTransaction::STATUS_PENDING)->count();
        return $pendingCount > 0 ? 'warning' : 'primary';
    }
}
