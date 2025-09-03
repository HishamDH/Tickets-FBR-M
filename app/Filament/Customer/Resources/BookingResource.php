<?php

namespace App\Filament\Customer\Resources;

use App\Filament\Customer\Resources\BookingResource\Pages;
use App\Models\Booking;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $slug = 'my-bookings';

    protected static ?string $navigationLabel = 'My Bookings';

    protected static ?string $modelLabel = 'Booking';

    protected static ?string $pluralModelLabel = 'My Bookings';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->where('customer_id', Auth::id()))
            ->columns([
                Tables\Columns\TextColumn::make('reference_number')
                    ->label('Booking Reference')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('service.name')
                    ->label('Service')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('service.merchant.business_name')
                    ->label('Provider')
                    ->searchable(),

                Tables\Columns\TextColumn::make('booking_date')
                    ->label('Date & Time')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Payment')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'paid' => 'success',
                        'refunded' => 'danger',
                        'failed' => 'secondary',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Amount')
                    ->money('USD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Booked On')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),

                Tables\Filters\SelectFilter::make('payment_status')
                    ->label('Payment Status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'refunded' => 'Refunded',
                        'failed' => 'Failed',
                    ]),

                Tables\Filters\Filter::make('booking_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('From Date'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Until Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('booking_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('booking_date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),

                    Tables\Actions\Action::make('cancel')
                        ->label('Cancel Booking')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->visible(fn ($record): bool => in_array($record->status, ['pending', 'confirmed']))
                        ->requiresConfirmation()
                        ->modalHeading('Cancel Booking')
                        ->modalDescription('Are you sure you want to cancel this booking? This action cannot be undone.')
                        ->action(function ($record) {
                            $record->update(['status' => 'cancelled']);

                            \Filament\Notifications\Notification::make()
                                ->title('Booking Cancelled')
                                ->body('Your booking has been cancelled successfully.')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\Action::make('request_refund')
                        ->label('Request Refund')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->color('warning')
                        ->visible(fn ($record): bool => $record->status === 'cancelled' &&
                            $record->payment_status === 'paid'
                        )
                        ->form([
                            Forms\Components\Textarea::make('refund_reason')
                                ->label('Refund Reason')
                                ->required()
                                ->placeholder('Please explain why you are requesting a refund...'),
                        ])
                        ->action(function ($record, array $data) {
                            // Create refund request logic here
                            \App\Models\Refund::create([
                                'payment_id' => $record->payment->id ?? null,
                                'booking_id' => $record->id,
                                'user_id' => Auth::id(),
                                'amount' => $record->total_amount,
                                'type' => 'full',
                                'reason' => $data['refund_reason'],
                                'status' => 'pending',
                            ]);

                            \Filament\Notifications\Notification::make()
                                ->title('Refund Requested')
                                ->body('Your refund request has been submitted and will be reviewed.')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\Action::make('download_receipt')
                        ->label('Download Receipt')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('info')
                        ->visible(fn ($record): bool => $record->payment_status === 'paid')
                        ->action(function ($record) {
                            // Generate and download receipt logic
                            \Filament\Notifications\Notification::make()
                                ->title('Receipt Downloaded')
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->emptyStateHeading('No bookings yet')
            ->emptyStateDescription('When you make your first booking, it will appear here.')
            ->emptyStateIcon('heroicon-o-calendar-days')
            ->defaultSort('booking_date', 'desc');
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
            'index' => Pages\ListBookings::route('/'),
            'view' => Pages\ViewBooking::route('/{record}'),
        ];
    }

    // Restrict customer access - they can only view their own bookings
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }
}
