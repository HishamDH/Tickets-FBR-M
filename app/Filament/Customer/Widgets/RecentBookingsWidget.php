<?php

namespace App\Filament\Customer\Widgets;

use App\Models\Booking;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class RecentBookingsWidget extends BaseWidget
{
    protected static ?string $heading = 'Recent Bookings';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Booking::query()
                    ->where('customer_id', Auth::id())
                    ->with(['service', 'merchant'])
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\ImageColumn::make('service.image')
                    ->label('Service')
                    ->circular()
                    ->defaultImageUrl(url('/images/placeholder-service.jpg')),

                Tables\Columns\TextColumn::make('service.name')
                    ->label('Service')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('merchant.business_name')
                    ->label('Merchant')
                    ->searchable()
                    ->sortable()
                    ->limit(25),

                Tables\Columns\TextColumn::make('booking_date')
                    ->label('Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('booking_time')
                    ->label('Time')
                    ->time('H:i'),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Amount')
                    ->money('USD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('booking_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'cancelled' => 'danger',
                        'pending' => 'warning',
                        'confirmed' => 'primary',
                        'completed' => 'success',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'cancelled' => 'heroicon-o-x-circle',
                        'pending' => 'heroicon-o-clock',
                        'confirmed' => 'heroicon-o-check-circle',
                        'completed' => 'heroicon-o-check-badge',
                        default => 'heroicon-o-question-mark-circle',
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn (Booking $record): string => '/customer/bookings/'.$record->id)
                    ->openUrlInNewTab(false),

                Tables\Actions\Action::make('cancel')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Booking $record): bool => $record->booking_status === 'pending' || $record->booking_status === 'confirmed'
                    )
                    ->action(function (Booking $record) {
                        $record->update(['booking_status' => 'cancelled']);
                        $this->notify('success', 'Booking cancelled successfully');
                    }),

                Tables\Actions\Action::make('rebook')
                    ->icon('heroicon-o-arrow-path')
                    ->color('primary')
                    ->visible(fn (Booking $record): bool => $record->booking_status === 'completed')
                    ->url(fn (Booking $record): string => '/services/'.$record->service_id),
            ])
            ->emptyStateIcon('heroicon-o-ticket')
            ->emptyStateHeading('No bookings yet')
            ->emptyStateDescription('Start exploring our amazing services!')
            ->emptyStateActions([
                Tables\Actions\Action::make('browse')
                    ->label('Browse Services')
                    ->url('/services')
                    ->icon('heroicon-o-magnifying-glass'),
            ]);
    }
}
