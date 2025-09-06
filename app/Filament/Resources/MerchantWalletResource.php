<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MerchantWalletResource\Pages;
use App\Models\MerchantWallet;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MerchantWalletResource extends Resource
{
    protected static ?string $model = MerchantWallet::class;

    protected static ?string $navigationIcon = 'heroicon-o-wallet';

    protected static ?string $navigationLabel = 'محافظ التجار';
    
    protected static ?string $modelLabel = 'محفظة تاجر';
    
    protected static ?string $pluralModelLabel = 'محافظ التجار';

    protected static ?string $navigationGroup = 'إدارة المدفوعات';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('معلومات المحفظة')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('التاجر')
                            ->options(User::where('user_type', 'merchant')->pluck('name', 'id'))
                            ->searchable()
                            ->required()
                            ->disabled(fn (string $operation) => $operation === 'edit'),
                        Forms\Components\TextInput::make('balance')
                            ->label('الرصيد الحالي')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01),
                        Forms\Components\TextInput::make('pending_balance')
                            ->label('الرصيد المعلق')
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01)
                            ->default(0),
                        Forms\Components\Toggle::make('is_active')
                            ->label('المحفظة نشطة')
                            ->default(true),
                    ])->columns(2),

                Forms\Components\Section::make('إحصائيات')
                    ->schema([
                        Forms\Components\TextInput::make('total_earned')
                            ->label('إجمالي الأرباح')
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01)
                            ->default(0),
                        Forms\Components\TextInput::make('total_withdrawn')
                            ->label('إجمالي المسحوب')
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01)
                            ->default(0),
                        Forms\Components\DateTimePicker::make('last_transaction_at')
                            ->label('آخر معاملة'),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('التاجر')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('البريد الإلكتروني')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('balance')
                    ->label('الرصيد الحالي')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('pending_balance')
                    ->label('الرصيد المعلق')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('available_balance')
                    ->label('الرصيد المتاح')
                    ->getStateUsing(fn (MerchantWallet $record): float => $record->available_balance)
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_earned')
                    ->label('إجمالي الأرباح')
                    ->money('USD')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('total_withdrawn')
                    ->label('إجمالي المسحوب')
                    ->money('USD')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('الحالة')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                Tables\Columns\TextColumn::make('last_transaction_at')
                    ->label('آخر معاملة')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('حالة المحفظة')
                    ->boolean()
                    ->trueLabel('نشطة')
                    ->falseLabel('معطلة')
                    ->placeholder('جميع المحافظ'),
                Tables\Filters\Filter::make('balance_range')
                    ->label('نطاق الرصيد')
                    ->form([
                        Forms\Components\TextInput::make('balance_from')
                            ->label('من')
                            ->numeric()
                            ->prefix('$'),
                        Forms\Components\TextInput::make('balance_to')
                            ->label('إلى')
                            ->numeric()
                            ->prefix('$'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['balance_from'],
                                fn (Builder $query, $balance): Builder => $query->where('balance', '>=', $balance),
                            )
                            ->when(
                                $data['balance_to'],
                                fn (Builder $query, $balance): Builder => $query->where('balance', '<=', $balance),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('add_balance')
                    ->label('إضافة رصيد')
                    ->icon('heroicon-o-plus')
                    ->color('success')
                    ->form([
                        Forms\Components\TextInput::make('amount')
                            ->label('المبلغ')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01),
                        Forms\Components\Textarea::make('description')
                            ->label('الوصف')
                            ->required()
                            ->placeholder('سبب إضافة الرصيد...'),
                    ])
                    ->action(function (MerchantWallet $record, array $data) {
                        $record->increment('balance', $data['amount']);
                        $record->increment('total_earned', $data['amount']);
                        $record->update(['last_transaction_at' => now()]);
                        
                        // إنشاء معاملة
                        $record->transactions()->create([
                            'transaction_reference' => 'ADJ_' . uniqid(),
                            'type' => 'credit',
                            'category' => 'adjustment',
                            'amount' => $data['amount'],
                            'balance_after' => $record->balance,
                            'description' => $data['description'],
                            'status' => 'completed',
                            'processed_at' => now(),
                        ]);
                    }),

                Tables\Actions\Action::make('deduct_balance')
                    ->label('خصم رصيد')
                    ->icon('heroicon-o-minus')
                    ->color('warning')
                    ->form([
                        Forms\Components\TextInput::make('amount')
                            ->label('المبلغ')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01),
                        Forms\Components\Textarea::make('description')
                            ->label('الوصف')
                            ->required()
                            ->placeholder('سبب خصم الرصيد...'),
                    ])
                    ->action(function (MerchantWallet $record, array $data) {
                        $record->decrement('balance', $data['amount']);
                        $record->update(['last_transaction_at' => now()]);
                        
                        // إنشاء معاملة
                        $record->transactions()->create([
                            'transaction_reference' => 'DED_' . uniqid(),
                            'type' => 'debit',
                            'category' => 'adjustment',
                            'amount' => $data['amount'],
                            'balance_after' => $record->balance,
                            'description' => $data['description'],
                            'status' => 'completed',
                            'processed_at' => now(),
                        ]);
                    }),

                Tables\Actions\Action::make('toggle_status')
                    ->label(fn (MerchantWallet $record) => $record->is_active ? 'تعطيل' : 'تفعيل')
                    ->icon(fn (MerchantWallet $record) => $record->is_active ? 'heroicon-o-pause' : 'heroicon-o-play')
                    ->color(fn (MerchantWallet $record) => $record->is_active ? 'danger' : 'success')
                    ->action(fn (MerchantWallet $record) => $record->update(['is_active' => !$record->is_active]))
                    ->requiresConfirmation(),

                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('activate')
                        ->label('تفعيل المحدد')
                        ->icon('heroicon-o-play')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['is_active' => true])),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('تعطيل المحدد')
                        ->icon('heroicon-o-pause')
                        ->color('warning')
                        ->action(fn ($records) => $records->each->update(['is_active' => false])),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('balance', 'desc');
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
            'index' => Pages\ListMerchantWallets::route('/'),
            'create' => Pages\CreateMerchantWallet::route('/create'),
            'edit' => Pages\EditMerchantWallet::route('/{record}/edit'),
        ];
    }
}
