<?php

namespace App\Filament\Customer\Resources;

use App\Filament\Customer\Resources\MyBookingResource\Pages;
use App\Models\Booking;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class MyBookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'My Bookings';

    protected static ?string $modelLabel = 'My Booking';

    protected static ?string $pluralModelLabel = 'My Bookings';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Auth::id())
            ->with(['service.merchant', 'service.category']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Booking Details')
                    ->schema([
                        Forms\Components\Select::make('service_id')
                            ->label('Service')
                            ->relationship('service', 'title')
                            ->required()
                            ->disabled(),

                        Forms\Components\DateTimePicker::make('booking_date')
                            ->label('Booking Date')
                            ->required()
                            ->disabled(),

                        Forms\Components\TextInput::make('guest_count')
                            ->label('Number of Guests')
                            ->numeric()
                            ->disabled(),

                        Forms\Components\Textarea::make('special_requests')
                            ->label('Special Requests')
                            ->disabled(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Pricing')
                    ->schema([
                        Forms\Components\TextInput::make('base_amount')
                            ->label('Base Amount')
                            ->prefix('$')
                            ->disabled(),

                        Forms\Components\TextInput::make('total_amount')
                            ->label('Total Amount')
                            ->prefix('$')
                            ->disabled(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Status & Contact')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending' => 'Pending',
                                'confirmed' => 'Confirmed',
                                'in_progress' => 'In Progress',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->disabled(),

                        Forms\Components\TextInput::make('contact_phone')
                            ->label('Contact Phone'),

                        Forms\Components\TextInput::make('contact_email')
                            ->label('Contact Email')
                            ->email(),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('service.title')
                    ->label('Service')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('service.merchant.business_name')
                    ->label('Business')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('booking_date')
                    ->label('Date & Time')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('guest_count')
                    ->label('Guests')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total')
                    ->money('USD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'success',
                        'in_progress' => 'primary',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Booked At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'in_progress' => 'In Progress',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
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
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn (Booking $record): bool => in_array($record->status, ['pending', 'confirmed']))
                    ->form([
                        Forms\Components\TextInput::make('contact_phone')
                            ->label('Contact Phone'),

                        Forms\Components\TextInput::make('contact_email')
                            ->label('Contact Email')
                            ->email(),

                        Forms\Components\Textarea::make('special_requests')
                            ->label('Special Requests'),
                    ]),

                Tables\Actions\Action::make('cancel')
                    ->label('Cancel Booking')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn (Booking $record): bool => in_array($record->status, ['pending', 'confirmed']))
                    ->requiresConfirmation()
                    ->action(fn (Booking $record) => $record->update(['status' => 'cancelled'])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => false), // Disable bulk delete for customer bookings
                ]),
            ])
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
            'index' => Pages\ListMyBookings::route('/'),
            'create' => Pages\CreateMyBooking::route('/create'),
            'view' => Pages\ViewMyBooking::route('/{record}'),
            'edit' => Pages\EditMyBooking::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false; // Bookings are created through the booking form, not directly
    }
}
