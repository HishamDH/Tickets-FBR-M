<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingResource\Pages;
use App\Filament\Resources\BookingResource\RelationManagers;
use App\Models\Booking;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'الحجوزات';

    protected static ?string $modelLabel = 'حجز';

    protected static ?string $pluralModelLabel = 'الحجوزات';

    protected static ?string $navigationGroup = 'إدارة المحتوى';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('معلومات الحجز')
                    ->schema([
                        Grid::make(2)->schema([
                            Forms\Components\TextInput::make('booking_number')
                                ->label('رقم الحجز')
                                ->required()
                                ->maxLength(20)
                                ->unique(ignoreRecord: true)
                                ->default(fn () => 'BK-' . strtoupper(substr(uniqid(), -6))),
                                
                            Forms\Components\Select::make('booking_source')
                                ->label('مصدر الحجز')
                                ->options([
                                    'online' => 'إلكتروني',
                                    'manual' => 'يدوي',
                                    'pos' => 'نقاط البيع',
                                ])
                                ->default('online')
                                ->required()
                                ->native(false),
                        ]),
                        
                        Grid::make(3)->schema([
                            Forms\Components\Select::make('customer_id')
                                ->label('العميل')
                                ->relationship('customer', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),
                                
                            Forms\Components\Select::make('service_id')
                                ->label('الخدمة')
                                ->relationship('service', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),
                                
                            Forms\Components\Select::make('merchant_id')
                                ->label('التاجر')
                                ->relationship('merchant', 'business_name')
                                ->searchable()
                                ->preload()
                                ->required(),
                        ]),
                        
                        Grid::make(3)->schema([
                            Forms\Components\DatePicker::make('booking_date')
                                ->label('تاريخ الحجز')
                                ->required()
                                ->native(false),
                                
                            Forms\Components\TimePicker::make('booking_time')
                                ->label('وقت الحجز')
                                ->seconds(false),
                                
                            Forms\Components\TextInput::make('guest_count')
                                ->label('عدد الضيوف')
                                ->numeric()
                                ->minValue(1)
                                ->suffix('شخص'),
                        ]),
                    ]),
                    
                Section::make('المعلومات المالية')
                    ->schema([
                        Grid::make(3)->schema([
                            Forms\Components\TextInput::make('total_amount')
                                ->label('المبلغ الإجمالي')
                                ->required()
                                ->numeric()
                                ->prefix('ريال'),
                                
                            Forms\Components\TextInput::make('commission_rate')
                                ->label('نسبة العمولة')
                                ->required()
                                ->numeric()
                                ->suffix('%')
                                ->minValue(0)
                                ->maxValue(100),
                                
                            Forms\Components\TextInput::make('commission_amount')
                                ->label('مبلغ العمولة')
                                ->required()
                                ->numeric()
                                ->prefix('ريال'),
                        ]),
                        
                        Grid::make(2)->schema([
                            Forms\Components\Select::make('payment_status')
                                ->label('حالة الدفع')
                                ->options([
                                    'pending' => 'في الانتظار',
                                    'paid' => 'مدفوع',
                                    'failed' => 'فشل',
                                    'refunded' => 'مسترد',
                                ])
                                ->default('pending')
                                ->required()
                                ->native(false),
                                
                            Forms\Components\Select::make('status')
                                ->label('حالة الحجز')
                                ->options([
                                    'pending' => 'في الانتظار',
                                    'confirmed' => 'مؤكد',
                                    'completed' => 'مكتمل',
                                    'cancelled' => 'ملغي',
                                    'no_show' => 'لم يحضر',
                                ])
                                ->default('pending')
                                ->required()
                                ->native(false),
                        ]),
                    ]),
                    
                Section::make('معلومات إضافية')
                    ->schema([
                        Forms\Components\Textarea::make('special_requests')
                            ->label('طلبات خاصة')
                            ->rows(2)
                            ->columnSpanFull(),
                            
                        Forms\Components\Textarea::make('cancellation_reason')
                            ->label('سبب الإلغاء')
                            ->rows(2)
                            ->columnSpanFull(),
                            
                        Grid::make(2)->schema([
                            Forms\Components\DateTimePicker::make('cancelled_at')
                                ->label('تاريخ الإلغاء')
                                ->native(false),
                                
                            Forms\Components\Select::make('cancelled_by')
                                ->label('ألغي بواسطة')
                                ->relationship('cancelledBy', 'name')
                                ->searchable()
                                ->preload(),
                        ]),
                        
                        Forms\Components\TextInput::make('qr_code')
                            ->label('رمز QR')
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ])
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('booking_number')
                    ->label('رقم الحجز')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable(),
                    
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('العميل')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Booking $record): string => $record->customer->email ?? ''),
                    
                Tables\Columns\TextColumn::make('service.name')
                    ->label('الخدمة')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 30) {
                            return null;
                        }
                        return $state;
                    }),
                    
                Tables\Columns\TextColumn::make('merchant.business_name')
                    ->label('التاجر')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('booking_date')
                    ->label('التاريخ')
                    ->date('d/m/Y')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('booking_time')
                    ->label('الوقت')
                    ->time('H:i'),
                    
                Tables\Columns\TextColumn::make('guest_count')
                    ->label('الضيوف')
                    ->numeric()
                    ->suffix(' شخص')
                    ->alignCenter(),
                    
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('المبلغ')
                    ->money('SAR')
                    ->sortable(),
                    
                Tables\Columns\BadgeColumn::make('payment_status')
                    ->label('حالة الدفع')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'paid',
                        'danger' => 'failed',
                        'secondary' => 'refunded',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'في الانتظار',
                        'paid' => 'مدفوع',
                        'failed' => 'فشل',
                        'refunded' => 'مسترد',
                        default => $state,
                    }),
                    
                Tables\Columns\BadgeColumn::make('status')
                    ->label('حالة الحجز')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'confirmed',
                        'primary' => 'completed',
                        'danger' => 'cancelled',
                        'secondary' => 'no_show',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'في الانتظار',
                        'confirmed' => 'مؤكد',
                        'completed' => 'مكتمل',
                        'cancelled' => 'ملغي',
                        'no_show' => 'لم يحضر',
                        default => $state,
                    }),
                    
                Tables\Columns\BadgeColumn::make('booking_source')
                    ->label('المصدر')
                    ->colors([
                        'primary' => 'online',
                        'warning' => 'manual',
                        'success' => 'pos',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'online' => 'إلكتروني',
                        'manual' => 'يدوي',
                        'pos' => 'نقاط البيع',
                        default => $state,
                    }),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('payment_status')
                    ->label('حالة الدفع')
                    ->options([
                        'pending' => 'في الانتظار',
                        'paid' => 'مدفوع',
                        'failed' => 'فشل',
                        'refunded' => 'مسترد',
                    ]),
                    
                Tables\Filters\SelectFilter::make('status')
                    ->label('حالة الحجز')
                    ->options([
                        'pending' => 'في الانتظار',
                        'confirmed' => 'مؤكد',
                        'completed' => 'مكتمل',
                        'cancelled' => 'ملغي',
                        'no_show' => 'لم يحضر',
                    ]),
                    
                Tables\Filters\SelectFilter::make('booking_source')
                    ->label('مصدر الحجز')
                    ->options([
                        'online' => 'إلكتروني',
                        'manual' => 'يدوي',
                        'pos' => 'نقاط البيع',
                    ]),
                    
                Tables\Filters\Filter::make('booking_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('من تاريخ'),
                        Forms\Components\DatePicker::make('until')
                            ->label('إلى تاريخ'),
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
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('confirm')
                    ->label('تأكيد')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->action(fn (Booking $record) => $record->update(['status' => 'confirmed']))
                    ->visible(fn (Booking $record) => $record->status === 'pending'),
                Tables\Actions\Action::make('cancel')
                    ->label('إلغاء')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->action(fn (Booking $record) => $record->update(['status' => 'cancelled']))
                    ->visible(fn (Booking $record) => in_array($record->status, ['pending', 'confirmed'])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('confirm_selected')
                        ->label('تأكيد المحدد')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['status' => 'confirmed']))
                        ->deselectRecordsAfterCompletion(),
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
            'index' => Pages\ListBookings::route('/'),
            'create' => Pages\CreateBooking::route('/create'),
            'edit' => Pages\EditBooking::route('/{record}/edit'),
        ];
    }
}
