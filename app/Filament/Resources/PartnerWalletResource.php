<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PartnerWalletResource\Pages;
use App\Models\PartnerWallet;
use App\Services\PartnerCommissionService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PartnerWalletResource extends Resource
{
    protected static ?string $model = PartnerWallet::class;

    protected static ?string $navigationIcon = 'heroicon-o-wallet';

    protected static ?string $navigationLabel = 'محافظ الشركاء';

    protected static ?string $modelLabel = 'محفظة شريك';

    protected static ?string $pluralModelLabel = 'محافظ الشركاء';

    protected static ?string $navigationGroup = 'إدارة الشركاء';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('partner_id')
                    ->label('الشريك')
                    ->relationship('partner', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('balance')
                        ->label('الرصيد الحالي')
                        ->numeric()
                        ->default(0)
                        ->step(0.01)
                        ->suffix('ريال'),

                    Forms\Components\TextInput::make('pending_balance')
                        ->label('الرصيد المعلق')
                        ->numeric()
                        ->default(0)
                        ->step(0.01)
                        ->suffix('ريال'),
                ]),

                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('total_earned')
                        ->label('إجمالي الأرباح')
                        ->numeric()
                        ->default(0)
                        ->step(0.01)
                        ->suffix('ريال'),

                    Forms\Components\TextInput::make('total_withdrawn')
                        ->label('إجمالي المسحوب')
                        ->numeric()
                        ->default(0)
                        ->step(0.01)
                        ->suffix('ريال'),
                ]),

                Forms\Components\Section::make('إعدادات السحب التلقائي')
                    ->schema([
                        Forms\Components\Toggle::make('auto_withdraw')
                            ->label('تفعيل السحب التلقائي')
                            ->default(false)
                            ->live(),

                        Forms\Components\TextInput::make('auto_withdraw_threshold')
                            ->label('حد السحب التلقائي')
                            ->numeric()
                            ->step(0.01)
                            ->suffix('ريال')
                            ->visible(fn (Forms\Get $get) => $get('auto_withdraw'))
                            ->required(fn (Forms\Get $get) => $get('auto_withdraw')),
                    ]),

                Forms\Components\Section::make('المعلومات المصرفية')
                    ->schema([
                        Forms\Components\TextInput::make('bank_name')
                            ->label('اسم البنك')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('bank_account_number')
                            ->label('رقم الحساب المصرفي')
                            ->maxLength(50),

                        Forms\Components\TextInput::make('account_holder_name')
                            ->label('اسم صاحب الحساب')
                            ->maxLength(255),

                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('bank_routing_number')
                                ->label('رقم التوجيه المصرفي')
                                ->maxLength(50),

                            Forms\Components\TextInput::make('swift_code')
                                ->label('رمز SWIFT')
                                ->maxLength(20),
                        ]),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('partner.name')
                    ->label('اسم الشريك')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('balance')
                    ->label('الرصيد المتاح')
                    ->money('SAR')
                    ->sortable()
                    ->color(fn ($record) => $record->balance > 0 ? 'success' : 'gray'),

                Tables\Columns\TextColumn::make('pending_balance')
                    ->label('الرصيد المعلق')
                    ->money('SAR')
                    ->sortable()
                    ->color(fn ($record) => $record->pending_balance > 0 ? 'warning' : 'gray'),

                Tables\Columns\TextColumn::make('total_earned')
                    ->label('إجمالي الأرباح')
                    ->money('SAR')
                    ->sortable()
                    ->color('success'),

                Tables\Columns\TextColumn::make('total_withdrawn')
                    ->label('إجمالي المسحوب')
                    ->money('SAR')
                    ->sortable()
                    ->color('info'),

                Tables\Columns\TextColumn::make('available_balance')
                    ->label('الرصيد القابل للسحب')
                    ->money('SAR')
                    ->getStateUsing(fn ($record) => $record->available_balance)
                    ->color(fn ($record) => $record->available_balance > 0 ? 'primary' : 'gray'),

                Tables\Columns\IconColumn::make('auto_withdraw')
                    ->label('السحب التلقائي')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),

                Tables\Columns\TextColumn::make('auto_withdraw_threshold')
                    ->label('حد السحب التلقائي')
                    ->money('SAR')
                    ->placeholder('غير محدد')
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

                Tables\Filters\Filter::make('has_balance')
                    ->label('لديه رصيد')
                    ->query(fn (Builder $query) => $query->where('balance', '>', 0)),

                Tables\Filters\Filter::make('auto_withdraw_enabled')
                    ->label('السحب التلقائي مفعل')
                    ->query(fn (Builder $query) => $query->where('auto_withdraw', true)),

                Tables\Filters\Filter::make('ready_for_withdrawal')
                    ->label('جاهز للسحب التلقائي')
                    ->query(function (Builder $query) {
                        return $query->where('auto_withdraw', true)
                            ->whereNotNull('auto_withdraw_threshold')
                            ->whereColumn('balance', '>=', 'auto_withdraw_threshold');
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),

                Action::make('add_commission')
                    ->label('إضافة عمولة')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->form([
                        Forms\Components\TextInput::make('amount')
                            ->label('المبلغ')
                            ->numeric()
                            ->required()
                            ->step(0.01)
                            ->suffix('ريال')
                            ->minValue(0.01),

                        Forms\Components\Textarea::make('description')
                            ->label('الوصف')
                            ->required()
                            ->maxLength(500),

                        Forms\Components\Select::make('category')
                            ->label('نوع العمولة')
                            ->options([
                                'booking_commission' => 'عمولة حجز',
                                'merchant_referral' => 'مكافأة إحالة تاجر',
                                'performance_bonus' => 'مكافأة أداء',
                                'special_offer' => 'عرض خاص',
                                'manual_adjustment' => 'تعديل يدوي',
                            ])
                            ->required(),
                    ])
                    ->action(function (PartnerWallet $record, array $data) {
                        try {
                            $record->addCommission(
                                $data['amount'],
                                $data['description'],
                                [
                                    'category' => $data['category'],
                                    'admin_id' => auth()->id(),
                                    'manual_entry' => true,
                                ]
                            );

                            Notification::make()
                                ->title('تم إضافة العمولة بنجاح')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('خطأ في إضافة العمولة')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Action::make('process_auto_withdrawal')
                    ->label('معالجة السحب التلقائي')
                    ->icon('heroicon-o-banknotes')
                    ->color('warning')
                    ->visible(function (PartnerWallet $record) {
                        return $record->auto_withdraw && 
                               $record->auto_withdraw_threshold &&
                               $record->available_balance >= $record->auto_withdraw_threshold;
                    })
                    ->requiresConfirmation()
                    ->modalHeading('تأكيد معالجة السحب التلقائي')
                    ->modalDescription(fn (PartnerWallet $record) => 
                        "سيتم إنشاء طلب سحب بمبلغ {$record->available_balance} ريال"
                    )
                    ->action(function (PartnerWallet $record) {
                        $service = app(PartnerCommissionService::class);
                        
                        if ($service->processAutoWithdrawal($record)) {
                            Notification::make()
                                ->title('تم إنشاء طلب السحب التلقائي')
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('فشل في معالجة السحب التلقائي')
                                ->danger()
                                ->send();
                        }
                    }),

                Action::make('view_transactions')
                    ->label('عرض المعاملات')
                    ->icon('heroicon-o-list-bullet')
                    ->color('info')
                    ->url(fn (PartnerWallet $record) => 
                        PartnerWalletTransactionResource::getUrl('index', [
                            'tableFilters' => [
                                'wallet' => [
                                    'value' => $record->id,
                                ],
                            ],
                        ])
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                    Tables\Actions\BulkAction::make('enable_auto_withdraw')
                        ->label('تفعيل السحب التلقائي')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->form([
                            Forms\Components\TextInput::make('auto_withdraw_threshold')
                                ->label('حد السحب التلقائي')
                                ->numeric()
                                ->required()
                                ->step(0.01)
                                ->suffix('ريال')
                                ->default(500),
                        ])
                        ->action(function ($records, array $data) {
                            foreach ($records as $record) {
                                $record->update([
                                    'auto_withdraw' => true,
                                    'auto_withdraw_threshold' => $data['auto_withdraw_threshold'],
                                ]);
                            }

                            Notification::make()
                                ->title('تم تفعيل السحب التلقائي للمحافظ المحددة')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\BulkAction::make('disable_auto_withdraw')
                        ->label('إلغاء السحب التلقائي')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update([
                                    'auto_withdraw' => false,
                                    'auto_withdraw_threshold' => null,
                                ]);
                            }

                            Notification::make()
                                ->title('تم إلغاء السحب التلقائي للمحافظ المحددة')
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
            'index' => Pages\ListPartnerWallets::route('/'),
            'create' => Pages\CreatePartnerWallet::route('/create'),
            'view' => Pages\ViewPartnerWallet::route('/{record}'),
            'edit' => Pages\EditPartnerWallet::route('/{record}/edit'),
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
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }
}
