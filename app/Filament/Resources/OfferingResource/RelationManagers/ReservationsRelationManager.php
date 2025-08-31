<?php

namespace App\Filament\Resources\OfferingResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReservationsRelationManager extends RelationManager
{
    protected static string $relationship = 'reservations';

    protected static ?string $title = 'الحجوزات';

    protected static ?string $modelLabel = 'حجز';

    protected static ?string $pluralModelLabel = 'الحجوزات';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('العميل')
                            ->relationship('customer', 'name')
                            ->searchable()
                            ->required(),
                        
                        Forms\Components\DatePicker::make('booking_date')
                            ->label('تاريخ الحجز')
                            ->required(),
                    ]),
                
                Forms\Components\Grid::make(3)
                    ->schema([
                        Forms\Components\TimePicker::make('booking_time')
                            ->label('وقت الحجز'),
                        
                        Forms\Components\TextInput::make('guest_count')
                            ->label('عدد الضيوف')
                            ->numeric()
                            ->default(1)
                            ->required(),
                        
                        Forms\Components\TextInput::make('total_amount')
                            ->label('المبلغ الإجمالي')
                            ->numeric()
                            ->prefix('ر.س')
                            ->required(),
                    ]),
                
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\Select::make('payment_status')
                            ->label('حالة الدفع')
                            ->options([
                                'pending' => 'في الانتظار',
                                'paid' => 'مدفوع',
                                'failed' => 'فشل',
                                'refunded' => 'مسترد',
                            ])
                            ->default('pending'),
                        
                        Forms\Components\Select::make('reservation_status')
                            ->label('حالة الحجز')
                            ->options([
                                'pending' => 'في الانتظار',
                                'confirmed' => 'مؤكد',
                                'completed' => 'مكتمل',
                                'cancelled' => 'ملغى',
                            ])
                            ->default('pending'),
                    ]),
                
                Forms\Components\Textarea::make('special_requests')
                    ->label('طلبات خاصة')
                    ->rows(2)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('customer.name')
            ->columns([
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('العميل')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('booking_date')
                    ->label('تاريخ الحجز')
                    ->date('d/m/Y')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('booking_time')
                    ->label('وقت الحجز'),
                
                Tables\Columns\TextColumn::make('guest_count')
                    ->label('عدد الضيوف')
                    ->numeric()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('المبلغ')
                    ->money('SAR')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('payment_status')
                    ->label('حالة الدفع')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'paid' => 'success',
                        'failed' => 'danger',
                        'refunded' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'في الانتظار',
                        'paid' => 'مدفوع',
                        'failed' => 'فشل',
                        'refunded' => 'مسترد',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('reservation_status')
                    ->label('حالة الحجز')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'في الانتظار',
                        'confirmed' => 'مؤكد',
                        'completed' => 'مكتمل',
                        'cancelled' => 'ملغى',
                        default => $state,
                    }),
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
                
                Tables\Filters\SelectFilter::make('reservation_status')
                    ->label('حالة الحجز')
                    ->options([
                        'pending' => 'في الانتظار',
                        'confirmed' => 'مؤكد',
                        'completed' => 'مكتمل',
                        'cancelled' => 'ملغى',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('إضافة حجز جديد'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('تعديل'),
                Tables\Actions\DeleteAction::make()
                    ->label('حذف'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('حذف المحدد'),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
