<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Payment Information')
                    ->schema([
                        Forms\Components\TextInput::make('payment_number')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Forms\Components\Select::make('booking_id')
                            ->label('Booking')
                            ->relationship('booking', 'id')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('customer_id')
                            ->label('Customer')
                            ->relationship('customer', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('merchant_id')
                            ->label('Merchant')
                            ->relationship('merchant', 'business_name')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Payment Details')
                    ->schema([
                        Forms\Components\TextInput::make('amount')
                            ->numeric()
                            ->prefix('$')
                            ->required()
                            ->minValue(0),

                        Forms\Components\TextInput::make('gateway_fee')
                            ->numeric()
                            ->prefix('$')
                            ->default(0),

                        Forms\Components\TextInput::make('platform_fee')
                            ->numeric()
                            ->prefix('$')
                            ->default(0),

                        Forms\Components\TextInput::make('total_amount')
                            ->numeric()
                            ->prefix('$')
                            ->required(),

                        Forms\Components\Select::make('currency')
                            ->options([
                                'USD' => 'US Dollar',
                                'SAR' => 'Saudi Riyal',
                                'AED' => 'UAE Dirham',
                            ])
                            ->default('USD')
                            ->required(),

                        Forms\Components\Select::make('payment_method')
                            ->options([
                                'credit_card' => 'Credit Card',
                                'debit_card' => 'Debit Card',
                                'bank_transfer' => 'Bank Transfer',
                                'wallet' => 'Digital Wallet',
                                'cash' => 'Cash',
                            ])
                            ->required(),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Gateway Information')
                    ->schema([
                        Forms\Components\Select::make('payment_gateway_id')
                            ->label('Payment Gateway')
                            ->relationship('paymentGateway', 'name')
                            ->searchable()
                            ->preload(),

                        Forms\Components\TextInput::make('gateway_transaction_id')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('gateway_reference')
                            ->maxLength(255),

                        Forms\Components\KeyValue::make('gateway_metadata')
                            ->label('Gateway Metadata'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Payment Status')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'processing' => 'Processing',
                                'completed' => 'Completed',
                                'failed' => 'Failed',
                                'cancelled' => 'Cancelled',
                                'refunded' => 'Refunded',
                            ])
                            ->required()
                            ->default('pending'),

                        Forms\Components\DateTimePicker::make('initiated_at'),
                        Forms\Components\DateTimePicker::make('completed_at'),
                        Forms\Components\DateTimePicker::make('failed_at'),

                        Forms\Components\Textarea::make('failure_reason')
                            ->maxLength(500)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('notes')
                            ->maxLength(1000)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('payment_number')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('merchant.business_name')
                    ->label('Merchant')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('booking.id')
                    ->label('Booking ID')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->money('USD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_amount')
                    ->money('USD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'processing' => 'info',
                        'completed' => 'success',
                        'failed' => 'danger',
                        'cancelled' => 'secondary',
                        'refunded' => 'primary',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('payment_method')
                    ->badge()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('paymentGateway.name')
                    ->label('Gateway')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('completed_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                        'cancelled' => 'Cancelled',
                        'refunded' => 'Refunded',
                    ]),

                Tables\Filters\SelectFilter::make('payment_method')
                    ->options([
                        'credit_card' => 'Credit Card',
                        'debit_card' => 'Debit Card',
                        'bank_transfer' => 'Bank Transfer',
                        'wallet' => 'Digital Wallet',
                        'cash' => 'Cash',
                    ]),

                Tables\Filters\SelectFilter::make('payment_gateway')
                    ->relationship('paymentGateway', 'name'),

                Tables\Filters\Filter::make('amount_range')
                    ->form([
                        Forms\Components\TextInput::make('amount_from')
                            ->numeric()
                            ->label('Amount From'),
                        Forms\Components\TextInput::make('amount_to')
                            ->numeric()
                            ->label('Amount To'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['amount_from'],
                                fn (Builder $query, $amount): Builder => $query->where('total_amount', '>=', $amount),
                            )
                            ->when(
                                $data['amount_to'],
                                fn (Builder $query, $amount): Builder => $query->where('total_amount', '<=', $amount),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),

                    Tables\Actions\Action::make('mark_completed')
                        ->label('Mark Completed')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn ($record): bool => $record->status === 'processing')
                        ->action(fn ($record) => $record->updateStatus('completed')),

                    Tables\Actions\Action::make('mark_failed')
                        ->label('Mark Failed')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->visible(fn ($record): bool => in_array($record->status, ['pending', 'processing']))
                        ->form([
                            Forms\Components\Textarea::make('failure_reason')
                                ->required()
                                ->label('Failure Reason'),
                        ])
                        ->action(fn ($record, array $data) => $record->updateStatus('failed', $data)),

                    Tables\Actions\Action::make('refund')
                        ->label('Process Refund')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->color('warning')
                        ->visible(fn ($record): bool => $record->canBeRefunded())
                        ->requiresConfirmation()
                        ->action(fn ($record) => $record->updateStatus('refunded')),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                    Tables\Actions\BulkAction::make('mark_completed')
                        ->label('Mark as Completed')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each(fn ($record) => $record->updateStatus('completed'))),
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
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
