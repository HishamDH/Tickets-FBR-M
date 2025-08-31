<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OfferingResource\Pages;
use App\Filament\Resources\OfferingResource\RelationManagers;
use App\Models\Offering;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Tables\Filters\SelectFilter;

class OfferingResource extends Resource
{
    protected static ?string $model = Offering::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationLabel = 'العروض';

    protected static ?string $modelLabel = 'عرض';

    protected static ?string $pluralModelLabel = 'العروض';

    protected static ?string $navigationGroup = 'إدارة العروض';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('معلومات أساسية')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('اسم العرض')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('location')
                                    ->label('الموقع')
                                    ->maxLength(255),
                            ]),
                        
                        Forms\Components\Textarea::make('description')
                            ->label('الوصف')
                            ->rows(3)
                            ->columnSpanFull(),
                        
                        Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('type')
                                    ->label('نوع العرض')
                                    ->options([
                                        'events' => 'فعاليات',
                                        'conference' => 'مؤتمرات',
                                        'restaurant' => 'مطاعم',
                                        'experiences' => 'تجارب',
                                    ])
                                    ->default('events')
                                    ->required(),
                                
                                Forms\Components\TextInput::make('category')
                                    ->label('الفئة')
                                    ->maxLength(255),
                            ]),
                    ]),

                Section::make('الأسعار والتوقيت')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('price')
                                    ->label('السعر')
                                    ->numeric()
                                    ->prefix('ر.س'),
                                
                                Forms\Components\DateTimePicker::make('start_time')
                                    ->label('وقت البداية'),
                                
                                Forms\Components\DateTimePicker::make('end_time')
                                    ->label('وقت النهاية'),
                            ]),
                    ]),

                Section::make('إعدادات المقاعد')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\Toggle::make('has_chairs')
                                    ->label('يحتوي على مقاعد')
                                    ->live(),
                                
                                Forms\Components\TextInput::make('chairs_count')
                                    ->label('عدد المقاعد')
                                    ->numeric()
                                    ->visible(fn (Forms\Get $get) => $get('has_chairs')),
                            ]),
                    ]),

                Section::make('إعدادات أخرى')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->label('الحالة')
                                    ->options([
                                        'active' => 'نشط',
                                        'inactive' => 'غير نشط',
                                    ])
                                    ->default('inactive'),
                                
                                Forms\Components\Select::make('user_id')
                                    ->label('المالك')
                                    ->relationship('user', 'name')
                                    ->searchable()
                                    ->required(),
                            ]),
                        
                        Forms\Components\FileUpload::make('image')
                            ->label('الصورة')
                            ->image()
                            ->directory('offerings')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('الصورة')
                    ->circular()
                    ->size(50),
                
                Tables\Columns\TextColumn::make('name')
                    ->label('اسم العرض')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('type')
                    ->label('النوع')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'events' => 'success',
                        'conference' => 'info',
                        'restaurant' => 'warning',
                        'experiences' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'events' => 'فعاليات',
                        'conference' => 'مؤتمرات',
                        'restaurant' => 'مطاعم',
                        'experiences' => 'تجارب',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('location')
                    ->label('الموقع')
                    ->searchable()
                    ->limit(30),
                
                Tables\Columns\TextColumn::make('price')
                    ->label('السعر')
                    ->money('SAR')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'نشط',
                        'inactive' => 'غير نشط',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('user.name')
                    ->label('المالك')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('has_chairs')
                    ->label('مقاعد')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),
                
                Tables\Columns\TextColumn::make('chairs_count')
                    ->label('عدد المقاعد')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('start_time')
                    ->label('تاريخ البداية')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('نوع العرض')
                    ->options([
                        'events' => 'فعاليات',
                        'conference' => 'مؤتمرات',
                        'restaurant' => 'مطاعم',
                        'experiences' => 'تجارب',
                    ]),
                
                SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        'active' => 'نشط',
                        'inactive' => 'غير نشط',
                    ]),
                
                SelectFilter::make('user_id')
                    ->label('المالك')
                    ->relationship('user', 'name')
                    ->searchable(),
                
                Tables\Filters\TernaryFilter::make('has_chairs')
                    ->label('يحتوي على مقاعد')
                    ->placeholder('جميع العروض')
                    ->trueLabel('بمقاعد فقط')
                    ->falseLabel('بدون مقاعد فقط'),
                
                Tables\Filters\Filter::make('price_range')
                    ->label('نطاق السعر')
                    ->form([
                        Forms\Components\TextInput::make('price_from')
                            ->label('من')
                            ->numeric()
                            ->prefix('ر.س'),
                        Forms\Components\TextInput::make('price_to')
                            ->label('إلى')
                            ->numeric()
                            ->prefix('ر.س'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['price_from'], fn (Builder $query, $price): Builder => $query->where('price', '>=', $price))
                            ->when($data['price_to'], fn (Builder $query, $price): Builder => $query->where('price', '<=', $price));
                    }),
                
                Tables\Filters\Filter::make('date_range')
                    ->label('نطاق التاريخ')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('من تاريخ'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('إلى تاريخ'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['created_from'], fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date))
                            ->when($data['created_until'], fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('publish')
                    ->label('نشر')
                    ->icon('heroicon-o-eye')
                    ->color('success')
                    ->action(function ($record) {
                        $record->update(['status' => 'active']);
                    })
                    ->visible(fn ($record) => $record->status === 'inactive'),
                
                Tables\Actions\Action::make('unpublish')
                    ->label('إلغاء النشر')
                    ->icon('heroicon-o-eye-slash')
                    ->color('warning')
                    ->action(function ($record) {
                        $record->update(['status' => 'inactive']);
                    })
                    ->visible(fn ($record) => $record->status === 'active'),
                
                Tables\Actions\Action::make('duplicate')
                    ->label('تكرار')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('info')
                    ->action(function ($record) {
                        $newOffering = $record->replicate();
                        $newOffering->name = $record->name . ' (نسخة)';
                        $newOffering->status = 'inactive';
                        $newOffering->save();
                    }),
                
                Tables\Actions\ViewAction::make()
                    ->label('عرض'),
                Tables\Actions\EditAction::make()
                    ->label('تعديل'),
                Tables\Actions\DeleteAction::make()
                    ->label('حذف'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('publish_selected')
                        ->label('نشر المحدد')
                        ->icon('heroicon-o-eye')
                        ->color('success')
                        ->action(function ($records) {
                            $records->each->update(['status' => 'active']);
                        }),
                    
                    Tables\Actions\BulkAction::make('unpublish_selected')
                        ->label('إلغاء نشر المحدد')
                        ->icon('heroicon-o-eye-slash')
                        ->color('warning')
                        ->action(function ($records) {
                            $records->each->update(['status' => 'inactive']);
                        }),
                    
                    Tables\Actions\BulkAction::make('update_type')
                        ->label('تغيير النوع')
                        ->icon('heroicon-o-tag')
                        ->color('info')
                        ->form([
                            Forms\Components\Select::make('type')
                                ->label('النوع الجديد')
                                ->options([
                                    'events' => 'فعاليات',
                                    'conference' => 'مؤتمرات',
                                    'restaurant' => 'مطاعم',
                                    'experiences' => 'تجارب',
                                ])
                                ->required(),
                        ])
                        ->action(function ($records, $data) {
                            $records->each->update(['type' => $data['type']]);
                        }),
                    
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('حذف المحدد'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped();
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ReservationsRelationManager::class,
            RelationManagers\ReviewsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOfferings::route('/'),
            'create' => Pages\CreateOffering::route('/create'),
            'wizard' => Pages\CreateOfferingWizard::route('/wizard'),
            'view' => Pages\ViewOffering::route('/{record}'),
            'edit' => Pages\EditOffering::route('/{record}/edit'),
        ];
    }
}
