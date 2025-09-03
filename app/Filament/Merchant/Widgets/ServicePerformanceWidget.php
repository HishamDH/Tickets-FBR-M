<?php

namespace App\Filament\Merchant\Widgets;

use App\Models\Offering;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class ServicePerformanceWidget extends BaseWidget
{
    protected static ?string $heading = 'Top Performing Services';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Offering::query()
                    ->where('user_id', Auth::id())
                    ->where('status', 'active')
                    ->withCount(['bookings'])
                    ->withSum(['bookings' => function ($query) {
                        $query->where('status', 'completed');
                    }], 'total_amount')
                    ->withAvg('reviews', 'rating')
                    ->orderByDesc('bookings_count')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Image')
                    ->circular()
                    ->defaultImageUrl(url('/images/placeholder-service.jpg')),

                Tables\Columns\TextColumn::make('title')
                    ->label('Service Name')
                    ->searchable()
                    ->sortable()
                    ->limit(40)
                    ->wrap(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'service' => 'primary',
                        'event' => 'success',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('bookings_count')
                    ->label('Bookings')
                    ->sortable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('bookings_sum_total_amount')
                    ->label('Revenue')
                    ->money('USD')
                    ->sortable()
                    ->color('success'),

                Tables\Columns\TextColumn::make('reviews_avg_rating')
                    ->label('Rating')
                    ->formatStateUsing(fn (string $state): string => number_format($state, 1).'/5')
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        $state >= 4.5 => 'success',
                        $state >= 4.0 => 'primary',
                        $state >= 3.5 => 'warning',
                        default => 'danger',
                    }),

                Tables\Columns\TextColumn::make('available_quantity')
                    ->label('Available')
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state > 10 => 'success',
                        $state > 0 => 'warning',
                        default => 'danger',
                    }),

                Tables\Columns\TextColumn::make('price')
                    ->label('Price')
                    ->money('USD')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn (Offering $record): string => route('filament.merchant.resources.offerings.view', $record))
                    ->openUrlInNewTab(false),

                Tables\Actions\EditAction::make()
                    ->url(fn (Offering $record): string => route('filament.merchant.resources.offerings.edit', $record))
                    ->openUrlInNewTab(false),

                Tables\Actions\Action::make('viewPublic')
                    ->label('View Public')
                    ->icon('heroicon-o-eye')
                    ->color('primary')
                    ->url(fn (Offering $record): string => route('services.show', $record))
                    ->openUrlInNewTab(true),
            ])
            ->emptyStateIcon('heroicon-o-chart-bar')
            ->emptyStateHeading('No services yet')
            ->emptyStateDescription('Create your first service to see performance data!')
            ->emptyStateActions([
                Tables\Actions\Action::make('create')
                    ->label('Create Service')
                    ->url(route('filament.merchant.resources.offerings.create'))
                    ->icon('heroicon-o-plus'),
            ]);
    }
}
