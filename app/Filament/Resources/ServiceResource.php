<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Filament\Resources\ServiceResource\RelationManagers;
use App\Models\Service;
use App\Models\Merchant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationLabel = 'الخدمات';

    protected static ?string $modelLabel = 'خدمة';

    protected static ?string $pluralModelLabel = 'الخدمات';

    protected static ?string $navigationGroup = 'إدارة المحتوى';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('المعلومات الأساسية')
                    ->schema([
                        Grid::make(2)->schema([
                            Forms\Components\Select::make('merchant_id')
                                ->label('التاجر')
                                ->relationship('merchant', 'business_name')
                                ->searchable()
                                ->preload()
                                ->required(),
                                
                            Forms\Components\Select::make('service_type')
                                ->label('نوع الخدمة')
                                ->options([
                                    'event' => 'فعالية',
                                    'exhibition' => 'معرض',
                                    'restaurant' => 'مطعم',
                                    'experience' => 'تجربة',
                                ])
                                ->required()
                                ->native(false),
                        ]),
                        
                        Forms\Components\TextInput::make('name')
                            ->label('اسم الخدمة')
                            ->required()
                            ->maxLength(255),
                            
                        Forms\Components\Textarea::make('description')
                            ->label('الوصف')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),
                            
                        Grid::make(2)->schema([
                            Forms\Components\TextInput::make('location')
                                ->label('الموقع')
                                ->required()
                                ->maxLength(255),
                                
                            Forms\Components\Select::make('category')
                                ->label('الفئة')
                                ->options([
                                    'general' => 'عام',
                                    'women_only' => 'نسائي',
                                    'families' => 'عائلي',
                                    'children' => 'أطفال',
                                    'vip' => 'VIP',
                                ])
                                ->required()
                                ->native(false),
                        ]),
                    ]),
                    
                Section::make('التسعير والسعة')
                    ->schema([
                        Grid::make(3)->schema([
                            Forms\Components\Select::make('pricing_model')
                                ->label('نموذج التسعير')
                                ->options([
                                    'fixed' => 'سعر ثابت',
                                    'per_person' => 'لكل شخص',
                                    'per_table' => 'لكل طاولة',
                                    'hourly' => 'بالساعة',
                                    'package' => 'باقة',
                                ])
                                ->required()
                                ->native(false),
                                
                            Forms\Components\TextInput::make('base_price')
                                ->label('السعر الأساسي')
                                ->numeric()
                                ->prefix('ريال')
                                ->required(),
                                
                            Forms\Components\TextInput::make('currency')
                                ->label('العملة')
                                ->maxLength(3)
                                ->default('SAR')
                                ->required(),
                        ]),
                        
                        Grid::make(2)->schema([
                            Forms\Components\TextInput::make('capacity')
                                ->label('السعة القصوى')
                                ->numeric()
                                ->suffix('شخص'),
                                
                            Forms\Components\TextInput::make('duration_hours')
                                ->label('المدة (بالساعات)')
                                ->numeric()
                                ->suffix('ساعة'),
                        ]),
                    ]),
                    
                Section::make('المميزات والإعدادات')
                    ->schema([
                        Forms\Components\Textarea::make('features')
                            ->label('المميزات')
                            ->placeholder('مثال: خرائط مقاعد، تحقق QR، بادجات مخصصة')
                            ->rows(2)
                            ->columnSpanFull(),
                            
                        Forms\Components\FileUpload::make('image')
                            ->label('صورة رئيسية')
                            ->image()
                            ->directory('services')
                            ->columnSpanFull(),
                            
                        Grid::make(4)->schema([
                            Forms\Components\Toggle::make('is_featured')
                                ->label('خدمة مميزة')
                                ->default(false),
                                
                            Forms\Components\Toggle::make('is_available')
                                ->label('متاحة للحجز')
                                ->default(true),
                                
                            Forms\Components\Toggle::make('online_booking_enabled')
                                ->label('الحجز الإلكتروني')
                                ->default(true),
                                
                            Forms\Components\Toggle::make('is_active')
                                ->label('نشطة')
                                ->default(true),
                        ]),
                        
                        Forms\Components\Select::make('status')
                            ->label('الحالة')
                            ->options([
                                'draft' => 'مسودة',
                                'active' => 'نشطة',
                                'paused' => 'معلقة',
                                'archived' => 'مؤرشفة',
                            ])
                            ->default('draft')
                            ->required()
                            ->native(false),
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
                    ->label('اسم الخدمة')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                    
                Tables\Columns\TextColumn::make('merchant.business_name')
                    ->label('التاجر')
                    ->sortable()
                    ->searchable(),
                    
                Tables\Columns\BadgeColumn::make('service_type')
                    ->label('النوع')
                    ->colors([
                        'primary' => 'event',
                        'success' => 'exhibition', 
                        'warning' => 'restaurant',
                        'danger' => 'experience',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'event' => 'فعالية',
                        'exhibition' => 'معرض',
                        'restaurant' => 'مطعم',
                        'experience' => 'تجربة',
                        default => $state,
                    }),
                    
                Tables\Columns\TextColumn::make('base_price')
                    ->label('السعر')
                    ->money('SAR')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('capacity')
                    ->label('السعة')
                    ->numeric()
                    ->sortable()
                    ->suffix(' شخص'),
                    
                Tables\Columns\BadgeColumn::make('status')
                    ->label('الحالة')
                    ->colors([
                        'secondary' => 'draft',
                        'success' => 'active',
                        'warning' => 'paused',
                        'danger' => 'archived',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'مسودة',
                        'active' => 'نشطة',
                        'paused' => 'معلقة',
                        'archived' => 'مؤرشفة',
                        default => $state,
                    }),
                    
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('مميزة')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-star')
                    ->trueColor('warning')
                    ->falseColor('gray'),
                    
                Tables\Columns\IconColumn::make('online_booking_enabled')
                    ->label('حجز إلكتروني')
                    ->boolean()
                    ->trueIcon('heroicon-o-globe-alt')
                    ->falseIcon('heroicon-o-globe-alt')
                    ->trueColor('success')
                    ->falseColor('gray'),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('service_type')
                    ->label('نوع الخدمة')
                    ->options([
                        'event' => 'فعالية',
                        'exhibition' => 'معرض',
                        'restaurant' => 'مطعم',
                        'experience' => 'تجربة',
                    ]),
                    
                Tables\Filters\SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        'draft' => 'مسودة',
                        'active' => 'نشطة',
                        'paused' => 'معلقة',
                        'archived' => 'مؤرشفة',
                    ]),
                    
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('خدمة مميزة'),
                    
                Tables\Filters\TernaryFilter::make('online_booking_enabled')
                    ->label('الحجز الإلكتروني'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('activate')
                        ->label('تفعيل المحدد')
                        ->icon('heroicon-o-check')
                        ->action(fn ($records) => $records->each->update(['status' => 'active']))
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
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }
}
