<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentGatewayResource\Pages;
use App\Models\PaymentGateway;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentGatewayResource extends Resource
{
    protected static ?string $model = PaymentGateway::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Gateway Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('code')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50)
                            ->rules(['alpha_dash']),

                        Forms\Components\TextInput::make('display_name_en')
                            ->label('Display Name (English)')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('display_name_ar')
                            ->label('Display Name (Arabic)')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Textarea::make('description')
                            ->maxLength(1000)
                            ->columnSpanFull(),

                        Forms\Components\FileUpload::make('icon')
                            ->image()
                            ->imageEditor()
                            ->directory('payment-gateways')
                            ->maxSize(1024),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Gateway Settings')
                    ->schema([
                        Forms\Components\Select::make('provider')
                            ->options([
                                'stripe' => 'Stripe',
                                'paypal' => 'PayPal',
                                'mada' => 'Mada',
                                'visa' => 'Visa',
                                'mastercard' => 'Mastercard',
                                'stc_pay' => 'STC Pay',
                                'apple_pay' => 'Apple Pay',
                                'google_pay' => 'Google Pay',
                                'bank_transfer' => 'Bank Transfer',
                                'cash' => 'Cash',
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('transaction_fee')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->step(0.01),

                        Forms\Components\Select::make('fee_type')
                            ->options([
                                'percentage' => 'Percentage',
                                'fixed' => 'Fixed Amount',
                            ])
                            ->required()
                            ->default('percentage'),

                        Forms\Components\TextInput::make('sort_order')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Features & Status')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),

                        Forms\Components\Toggle::make('supports_refund')
                            ->label('Supports Refunds')
                            ->default(false),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('API Configuration')
                    ->schema([
                        Forms\Components\KeyValue::make('settings')
                            ->label('Gateway Settings')
                            ->keyLabel('Setting Key')
                            ->valueLabel('Setting Value')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('icon')
                    ->circular(),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->sortable()
                    ->badge(),

                Tables\Columns\TextColumn::make('display_name_en')
                    ->label('English Name')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('display_name_ar')
                    ->label('Arabic Name')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('provider')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('transaction_fee')
                    ->label('Fee')
                    ->formatStateUsing(fn ($state, $record) => $record->fee_type === 'percentage'
                            ? $state.'%'
                            : '$'.number_format($state, 2)
                    ),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                Tables\Columns\IconColumn::make('supports_refund')
                    ->label('Refunds')
                    ->boolean(),

                Tables\Columns\TextColumn::make('payments_count')
                    ->label('Payments')
                    ->counts('payments')
                    ->sortable(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Gateways'),

                Tables\Filters\TernaryFilter::make('supports_refund')
                    ->label('Supports Refunds'),

                Tables\Filters\SelectFilter::make('provider')
                    ->options([
                        'stripe' => 'Stripe',
                        'paypal' => 'PayPal',
                        'mada' => 'Mada',
                        'visa' => 'Visa',
                        'mastercard' => 'Mastercard',
                        'stc_pay' => 'STC Pay',
                        'apple_pay' => 'Apple Pay',
                        'google_pay' => 'Google Pay',
                        'bank_transfer' => 'Bank Transfer',
                        'cash' => 'Cash',
                    ]),

                Tables\Filters\SelectFilter::make('fee_type')
                    ->options([
                        'percentage' => 'Percentage',
                        'fixed' => 'Fixed Amount',
                    ]),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),

                    Tables\Actions\Action::make('toggle_active')
                        ->label(fn ($record): string => $record->is_active ? 'Deactivate' : 'Activate')
                        ->icon(fn ($record): string => $record->is_active ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                        ->color(fn ($record): string => $record->is_active ? 'warning' : 'success')
                        ->action(fn ($record) => $record->update(['is_active' => ! $record->is_active])),

                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                    Tables\Actions\BulkAction::make('activate')
                        ->label('Activate Selected')
                        ->icon('heroicon-o-eye')
                        ->color('success')
                        ->action(fn ($records) => $records->each(fn ($record) => $record->update(['is_active' => true]))),

                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Deactivate Selected')
                        ->icon('heroicon-o-eye-slash')
                        ->color('warning')
                        ->action(fn ($records) => $records->each(fn ($record) => $record->update(['is_active' => false]))),
                ]),
            ])
            ->reorderable('sort_order')
            ->defaultSort('sort_order');
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\PaymentGatewayResource\RelationManagers\PaymentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaymentGateways::route('/'),
            'create' => Pages\CreatePaymentGateway::route('/create'),
            'edit' => Pages\EditPaymentGateway::route('/{record}/edit'),
        ];
    }
}
