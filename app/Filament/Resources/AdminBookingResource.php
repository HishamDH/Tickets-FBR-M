<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdminBookingResource\Pages;
use App\Models\Booking;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AdminBookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Booking Management';

    protected static ?string $navigationLabel = 'All Bookings';

    protected static ?string $modelLabel = 'Booking';

    protected static ?string $pluralModelLabel = 'All Bookings';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Booking Details')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Customer')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('service_id')
                            ->label('Service')
                            ->relationship('service', 'title')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\DateTimePicker::make('booking_date')
                            ->required(),

                        Forms\Components\TextInput::make('guest_count')
                            ->numeric()
                            ->required()
                            ->minValue(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Contact Information')
                    ->schema([
                        Forms\Components\TextInput::make('contact_name')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('contact_phone')
                            ->tel()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('contact_email')
                            ->email()
                            ->maxLength(255),

                        Forms\Components\Textarea::make('special_requests')
                            ->maxLength(1000)
                            ->columnSpanFull(),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Pricing')
                    ->schema([
                        Forms\Components\TextInput::make('base_amount')
                            ->numeric()
                            ->prefix('$')
                            ->required(),

                        Forms\Components\TextInput::make('service_fee')
                            ->numeric()
                            ->prefix('$')
                            ->default(0),

                        Forms\Components\TextInput::make('tax_amount')
                            ->numeric()
                            ->prefix('$')
                            ->default(0),

                        Forms\Components\TextInput::make('discount_amount')
                            ->numeric()
                            ->prefix('$')
                            ->default(0),

                        Forms\Components\TextInput::make('total_amount')
                            ->numeric()
                            ->prefix('$')
                            ->required(),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Status & Tracking')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'confirmed' => 'Confirmed',
                                'in_progress' => 'In Progress',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                                'refunded' => 'Refunded',
                            ])
                            ->required()
                            ->default('pending'),

                        Forms\Components\Select::make('payment_status')
                            ->options([
                                'pending' => 'Pending',
                                'paid' => 'Paid',
                                'partially_paid' => 'Partially Paid',
                                'refunded' => 'Refunded',
                                'failed' => 'Failed',
                            ])
                            ->default('pending'),

                        Forms\Components\TextInput::make('confirmation_code')
                            ->maxLength(255),

                        Forms\Components\DateTimePicker::make('confirmed_at'),

                        Forms\Components\DateTimePicker::make('completed_at'),

                        Forms\Components\DateTimePicker::make('cancelled_at'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Notes & Comments')
                    ->schema([
                        Forms\Components\Textarea::make('admin_notes')
                            ->label('Admin Notes')
                            ->maxLength(1000)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('merchant_notes')
                            ->label('Merchant Notes')
                            ->maxLength(1000)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('cancellation_reason')
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('confirmation_code')
                    ->label('Code')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Confirmation code copied')
                    ->copyMessageDuration(1500),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('service.title')
                    ->label('Service')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('service.merchant.business_name')
                    ->label('Merchant')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('booking_date')
                    ->label('Date & Time')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('guest_count')
                    ->label('Guests')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_amount')
                    ->money('USD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'success',
                        'in_progress' => 'primary',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        'refunded' => 'secondary',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Payment')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'paid' => 'success',
                        'partially_paid' => 'info',
                        'refunded' => 'secondary',
                        'failed' => 'danger',
                        default => 'gray',
                    })
                    ->toggleable(),

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
                        'refunded' => 'Refunded',
                    ]),

                Tables\Filters\SelectFilter::make('payment_status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'partially_paid' => 'Partially Paid',
                        'refunded' => 'Refunded',
                        'failed' => 'Failed',
                    ]),

                Tables\Filters\SelectFilter::make('merchant')
                    ->relationship('service.merchant', 'business_name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('service')
                    ->relationship('service', 'title')
                    ->searchable()
                    ->preload(),

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

                    Tables\Actions\Action::make('confirm')
                        ->label('Confirm')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn (Booking $record): bool => $record->status === 'pending')
                        ->action(function (Booking $record) {
                            $record->update([
                                'status' => 'confirmed',
                                'confirmed_at' => now(),
                                'confirmation_code' => 'BK'.str_pad($record->id, 6, '0', STR_PAD_LEFT),
                            ]);
                        }),

                    Tables\Actions\Action::make('start')
                        ->label('Start Service')
                        ->icon('heroicon-o-play')
                        ->color('primary')
                        ->visible(fn (Booking $record): bool => $record->status === 'confirmed')
                        ->action(fn (Booking $record) => $record->update(['status' => 'in_progress'])),

                    Tables\Actions\Action::make('complete')
                        ->label('Complete')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->visible(fn (Booking $record): bool => $record->status === 'in_progress')
                        ->action(fn (Booking $record) => $record->update([
                            'status' => 'completed',
                            'completed_at' => now(),
                        ])),

                    Tables\Actions\Action::make('cancel')
                        ->label('Cancel')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->visible(fn (Booking $record): bool => in_array($record->status, ['pending', 'confirmed']))
                        ->form([
                            Forms\Components\Textarea::make('cancellation_reason')
                                ->label('Cancellation Reason')
                                ->required()
                                ->maxLength(500),
                        ])
                        ->action(function (Booking $record, array $data) {
                            $record->update([
                                'status' => 'cancelled',
                                'cancelled_at' => now(),
                                'cancellation_reason' => $data['cancellation_reason'],
                            ]);
                        }),

                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                    Tables\Actions\BulkAction::make('confirm_bookings')
                        ->label('Confirm Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                if ($record->status === 'pending') {
                                    $record->update([
                                        'status' => 'confirmed',
                                        'confirmed_at' => now(),
                                        'confirmation_code' => 'BK'.str_pad($record->id, 6, '0', STR_PAD_LEFT),
                                    ]);
                                }
                            });
                        }),

                    Tables\Actions\BulkAction::make('complete_bookings')
                        ->label('Mark Completed')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                if ($record->status === 'in_progress') {
                                    $record->update([
                                        'status' => 'completed',
                                        'completed_at' => now(),
                                    ]);
                                }
                            });
                        }),
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
            'index' => Pages\ListBookings::route('/'),
            'create' => Pages\CreateBooking::route('/create'),
            'view' => Pages\ViewBooking::route('/{record}'),
            'edit' => Pages\EditBooking::route('/{record}/edit'),
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['user', 'service.merchant']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['confirmation_code', 'user.name', 'service.title', 'contact_email', 'contact_phone'];
    }
}
