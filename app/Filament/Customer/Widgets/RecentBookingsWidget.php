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

    protected int | string | array $columnSpan = 'full';

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
                    
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'danger' => 'cancelled',
                        'warning' => 'pending',
                        'primary' => 'confirmed',
                        'success' => 'completed',
                    ])
                    ->icons([
                        'heroicon-o-x-circle' => 'cancelled',
                        'heroicon-o-clock' => 'pending',
                        'heroicon-o-check-circle' => 'confirmed',
                        'heroicon-o-check-badge' => 'completed',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn (Booking $record): string => route('customer.bookings.show', $record))
                    ->openUrlInNewTab(false),
                    
                Tables\Actions\Action::make('cancel')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Booking $record): bool => 
                        $record->status === 'pending' || $record->status === 'confirmed'
                    )
                    ->action(function (Booking $record) {
                        $record->update(['status' => 'cancelled']);
                        $this->notify('success', 'Booking cancelled successfully');
                    }),
                    
                Tables\Actions\Action::make('rebook')
                    ->icon('heroicon-o-arrow-path')
                    ->color('primary')
                    ->visible(fn (Booking $record): bool => $record->status === 'completed')
                    ->url(fn (Booking $record): string => route('services.show', $record->service_id)),
            ])
            ->emptyStateIcon('heroicon-o-ticket')
            ->emptyStateHeading('No bookings yet')
            ->emptyStateDescription('Start exploring our amazing services!')
            ->emptyStateActions([
                Tables\Actions\Action::make('browse')
                    ->label('Browse Services')
                    ->url(route('services.index'))
                    ->icon('heroicon-o-magnifying-glass'),
            ]);
    }
}
