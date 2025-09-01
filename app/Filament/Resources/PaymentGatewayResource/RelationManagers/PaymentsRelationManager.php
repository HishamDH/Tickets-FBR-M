<?php

namespace App\Filament\Resources\PaymentGatewayResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    protected static ?string $title = 'Payments';
    protected static ?string $icon = 'heroicon-o-credit-card';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Read-only form for viewing payment details
                Forms\Components\TextInput::make('transaction_id')
                    ->disabled(),
                
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                        'refunded' => 'Refunded',
                        'partially_refunded' => 'Partially Refunded',
                        'cancelled' => 'Cancelled',
                    ])
                    ->disabled(),
                
                Forms\Components\TextInput::make('amount')
                    ->numeric()
                    ->prefix('$')
                    ->disabled(),
                
                Forms\Components\TextInput::make('gateway_transaction_id')
                    ->label('Gateway Transaction ID')
                    ->disabled(),
                
                Forms\Components\Textarea::make('response_data')
                    ->json()
                    ->disabled(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('transaction_id')
            ->columns([
                Tables\Columns\TextColumn::make('transaction_id')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                
                Tables\Columns\TextColumn::make('booking.reference_number')
                    ->label('Booking')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('amount')
                    ->money('USD')
                    ->sortable(),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'primary' => 'processing',
                        'success' => 'completed',
                        'danger' => 'failed',
                        'gray' => 'refunded',
                        'info' => 'partially_refunded',
                        'secondary' => 'cancelled',
                    ]),
                
                Tables\Columns\TextColumn::make('gateway_transaction_id')
                    ->label('Gateway ID')
                    ->searchable()
                    ->copyable()
                    ->limit(20),
                
                Tables\Columns\TextColumn::make('gateway_fee')
                    ->money('USD')
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('refunded_amount')
                    ->money('USD')
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('processed_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                        'refunded' => 'Refunded',
                        'partially_refunded' => 'Partially Refunded',
                        'cancelled' => 'Cancelled',
                    ]),
                
                Tables\Filters\Filter::make('amount')
                    ->form([
                        Forms\Components\TextInput::make('amount_from')
                            ->numeric()
                            ->prefix('$'),
                        Forms\Components\TextInput::make('amount_to')
                            ->numeric()
                            ->prefix('$'),
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
                
                Tables\Filters\Filter::make('date_range')
                    ->form([
                        Forms\Components\DatePicker::make('created_from'),
                        Forms\Components\DatePicker::make('created_until'),
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
                
                Tables\Actions\Action::make('view_details')
                    ->label('Gateway Details')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->modalContent(function ($record) {
                        return view('filament.resources.payment-gateway-resource.payment-details', [
                            'payment' => $record,
                        ]);
                    }),
                
                Tables\Actions\Action::make('refund')
                    ->label('Process Refund')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('warning')
                    ->visible(fn ($record): bool => 
                        $record->status === 'completed' && 
                        $record->paymentGateway->supports_refund &&
                        $record->refunded_amount < $record->amount
                    )
                    ->form([
                        Forms\Components\TextInput::make('refund_amount')
                            ->label('Refund Amount')
                            ->numeric()
                            ->prefix('$')
                            ->required()
                            ->rules([
                                'numeric',
                                'min:0.01',
                                function ($record) {
                                    return function (string $attribute, $value, \Closure $fail) use ($record) {
                                        $maxRefund = $record->amount - $record->refunded_amount;
                                        if ($value > $maxRefund) {
                                            $fail("Refund amount cannot exceed $" . number_format($maxRefund, 2));
                                        }
                                    };
                                },
                            ]),
                        
                        Forms\Components\Textarea::make('refund_reason')
                            ->label('Refund Reason')
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        try {
                            $record->processRefund($data['refund_amount'], $data['refund_reason']);
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Refund Processed')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            \Filament\Notifications\Notification::make()
                                ->title('Refund Failed')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('export')
                        ->label('Export Payments')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('info')
                        ->action(function ($records) {
                            // Export functionality
                            return response()->streamDownload(function () use ($records) {
                                $csv = fopen('php://output', 'w');
                                
                                // Headers
                                fputcsv($csv, [
                                    'Transaction ID',
                                    'Booking',
                                    'Amount',
                                    'Status',
                                    'Gateway Transaction ID',
                                    'Gateway Fee',
                                    'Refunded Amount',
                                    'Created At',
                                    'Processed At',
                                ]);
                                
                                // Data
                                foreach ($records as $payment) {
                                    fputcsv($csv, [
                                        $payment->transaction_id,
                                        $payment->booking->reference_number ?? '',
                                        $payment->amount,
                                        $payment->status,
                                        $payment->gateway_transaction_id,
                                        $payment->gateway_fee,
                                        $payment->refunded_amount,
                                        $payment->created_at,
                                        $payment->processed_at,
                                    ]);
                                }
                                
                                fclose($csv);
                            }, 'payments-export-' . now()->format('Y-m-d-H-i-s') . '.csv');
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
