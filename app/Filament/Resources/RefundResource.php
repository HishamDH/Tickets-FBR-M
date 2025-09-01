<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RefundResource\Pages;
use App\Models\Refund;
use App\Models\Payment;
use App\Models\Booking;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RefundResource extends Resource
{
    protected static ?string $model = Refund::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-uturn-left';
    protected static ?string $navigationGroup = 'Financial Management';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationLabel = 'Refunds';
    protected static ?string $modelLabel = 'Refund';
    protected static ?string $pluralModelLabel = 'Refunds';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Refund Information')
                    ->schema([
                        Forms\Components\TextInput::make('refund_reference')
                            ->label('Refund Reference')
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('Auto-generated'),
                        
                        Forms\Components\Select::make('payment_id')
                            ->label('Payment')
                            ->relationship('payment', 'transaction_id')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->getOptionLabelFromRecordUsing(fn ($record) => 
                                $record->transaction_id . ' - $' . number_format($record->amount, 2)
                            ),
                        
                        Forms\Components\Select::make('booking_id')
                            ->label('Booking')
                            ->relationship('booking', 'reference_number')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->getOptionLabelFromRecordUsing(fn ($record) => 
                                $record->reference_number . ' - ' . $record->service_name
                            ),
                        
                        Forms\Components\Select::make('user_id')
                            ->label('Customer')
                            ->relationship('user', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Refund Details')
                    ->schema([
                        Forms\Components\TextInput::make('amount')
                            ->label('Refund Amount')
                            ->numeric()
                            ->prefix('$')
                            ->required()
                            ->rules(['min:0.01'])
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                $fee = $get('fee') ?? 0;
                                $set('net_amount', $state - $fee);
                            }),
                        
                        Forms\Components\TextInput::make('fee')
                            ->label('Processing Fee')
                            ->numeric()
                            ->prefix('$')
                            ->default(0)
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                $amount = $get('amount') ?? 0;
                                $set('net_amount', $amount - $state);
                            }),
                        
                        Forms\Components\TextInput::make('net_amount')
                            ->label('Net Refund Amount')
                            ->numeric()
                            ->prefix('$')
                            ->disabled()
                            ->dehydrated(),
                        
                        Forms\Components\Select::make('type')
                            ->label('Refund Type')
                            ->options([
                                'full' => 'Full Refund',
                                'partial' => 'Partial Refund',
                                'service_fee' => 'Service Fee Only',
                            ])
                            ->required()
                            ->default('partial'),
                        
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending' => 'Pending',
                                'processing' => 'Processing',
                                'completed' => 'Completed',
                                'failed' => 'Failed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required()
                            ->default('pending'),
                    ])
                    ->columns(3),
                
                Forms\Components\Section::make('Reason & Notes')
                    ->schema([
                        Forms\Components\Textarea::make('reason')
                            ->label('Refund Reason')
                            ->required()
                            ->placeholder('Enter the reason for this refund...'),
                        
                        Forms\Components\Textarea::make('admin_notes')
                            ->label('Admin Notes')
                            ->placeholder('Internal notes (not visible to customer)...'),
                    ])
                    ->columns(1),
                
                Forms\Components\Section::make('Gateway Response')
                    ->schema([
                        Forms\Components\KeyValue::make('gateway_response')
                            ->label('Gateway Response Data')
                            ->disabled()
                            ->columnSpanFull(),
                        
                        Forms\Components\DateTimePicker::make('processed_at')
                            ->label('Processed At')
                            ->disabled(),
                    ])
                    ->collapsed()
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('refund_reference')
                    ->label('Reference')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                
                Tables\Columns\TextColumn::make('payment.transaction_id')
                    ->label('Payment')
                    ->searchable()
                    ->copyable(),
                
                Tables\Columns\TextColumn::make('booking.reference_number')
                    ->label('Booking')
                    ->searchable()
                    ->url(fn ($record) => $record->booking ? route('filament.admin.resources.bookings.view', $record->booking) : null),
                
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->money('USD')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('fee')
                    ->label('Fee')
                    ->money('USD')
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('net_amount')
                    ->label('Net Amount')
                    ->money('USD')
                    ->sortable(),
                
                Tables\Columns\BadgeColumn::make('type')
                    ->label('Type')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'full' => 'Full Refund',
                        'partial' => 'Partial Refund',
                        'service_fee' => 'Service Fee Only',
                        default => $state,
                    })
                    ->colors([
                        'success' => 'full',
                        'warning' => 'partial',
                        'info' => 'service_fee',
                    ]),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'primary' => 'processing',
                        'success' => 'completed',
                        'danger' => 'failed',
                        'secondary' => 'cancelled',
                    ]),
                
                Tables\Columns\TextColumn::make('reason')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Requested')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('processed_at')
                    ->label('Processed')
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
                        'cancelled' => 'Cancelled',
                    ]),
                
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'full' => 'Full Refund',
                        'partial' => 'Partial Refund',
                        'service_fee' => 'Service Fee Only',
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
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    
                    Tables\Actions\Action::make('approve')
                        ->label('Approve Refund')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn ($record): bool => $record->status === 'pending')
                        ->requiresConfirmation()
                        ->modalHeading('Approve Refund')
                        ->modalDescription('Are you sure you want to approve this refund? This action will process the refund with the payment gateway.')
                        ->action(function ($record) {
                            try {
                                $record->update([
                                    'status' => 'processing',
                                    'processed_at' => now(),
                                ]);
                                
                                // Process refund logic here
                                $record->update(['status' => 'completed']);
                                
                                \Filament\Notifications\Notification::make()
                                    ->title('Refund Approved')
                                    ->body('The refund has been processed successfully.')
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
                    
                    Tables\Actions\Action::make('reject')
                        ->label('Reject Refund')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->visible(fn ($record): bool => in_array($record->status, ['pending', 'processing']))
                        ->form([
                            Forms\Components\Textarea::make('rejection_reason')
                                ->label('Rejection Reason')
                                ->required()
                                ->placeholder('Please provide a reason for rejecting this refund...'),
                        ])
                        ->action(function ($record, array $data) {
                            $record->update([
                                'status' => 'failed',
                                'admin_notes' => ($record->admin_notes ? $record->admin_notes . "\n\n" : '') . 
                                               'Rejected: ' . $data['rejection_reason'],
                            ]);
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Refund Rejected')
                                ->success()
                                ->send();
                        }),
                    
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    
                    Tables\Actions\BulkAction::make('approve_selected')
                        ->label('Approve Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $successCount = 0;
                            $errorCount = 0;
                            
                            foreach ($records as $record) {
                                if ($record->status === 'pending') {
                                    try {
                                        $record->update([
                                            'status' => 'completed',
                                            'processed_at' => now(),
                                        ]);
                                        $successCount++;
                                    } catch (\Exception $e) {
                                        $errorCount++;
                                    }
                                }
                            }
                            
                            \Filament\Notifications\Notification::make()
                                ->title("Processed {$successCount} refunds" . ($errorCount ? " ({$errorCount} failed)" : ''))
                                ->success()
                                ->send();
                        }),
                    
                    Tables\Actions\BulkAction::make('export')
                        ->label('Export Refunds')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('info')
                        ->action(function ($records) {
                            return response()->streamDownload(function () use ($records) {
                                $csv = fopen('php://output', 'w');
                                
                                // Headers
                                fputcsv($csv, [
                                    'Reference',
                                    'Payment ID',
                                    'Booking',
                                    'Customer',
                                    'Amount',
                                    'Fee',
                                    'Net Amount',
                                    'Type',
                                    'Status',
                                    'Reason',
                                    'Created At',
                                    'Processed At',
                                ]);
                                
                                // Data
                                foreach ($records as $refund) {
                                    fputcsv($csv, [
                                        $refund->refund_reference,
                                        $refund->payment->transaction_id ?? '',
                                        $refund->booking->reference_number ?? '',
                                        $refund->user->name ?? '',
                                        $refund->amount,
                                        $refund->fee,
                                        $refund->net_amount,
                                        $refund->type_label,
                                        $refund->status_label,
                                        $refund->reason,
                                        $refund->created_at,
                                        $refund->processed_at,
                                    ]);
                                }
                                
                                fclose($csv);
                            }, 'refunds-export-' . now()->format('Y-m-d-H-i-s') . '.csv');
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
            'index' => Pages\ListRefunds::route('/'),
            'create' => Pages\CreateRefund::route('/create'),
            'view' => Pages\ViewRefund::route('/{record}'),
            'edit' => Pages\EditRefund::route('/{record}/edit'),
        ];
    }
}
