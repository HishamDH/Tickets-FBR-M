<?php

namespace App\Filament\Merchant\Resources;

use App\Filament\Merchant\Resources\OfferingResource\Pages;
use App\Models\Offering;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class OfferingResource extends Resource
{
    protected static ?string $model = Offering::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';

    protected static ?string $navigationLabel = 'عروضي';

    protected static ?string $modelLabel = 'عرض';

    protected static ?string $pluralModelLabel = 'عروضي';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Auth::id());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('معلومات العرض')
                    ->schema([
                        Forms\Components\Hidden::make('user_id')
                            ->default(Auth::id()),

                        Forms\Components\TextInput::make('name')
                            ->label('اسم العرض')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Textarea::make('description')
                            ->label('الوصف')
                            ->required()
                            ->rows(3),

                        Forms\Components\TextInput::make('price')
                            ->label('السعر')
                            ->required()
                            ->numeric()
                            ->prefix('ريال'),

                        Forms\Components\TextInput::make('capacity')
                            ->label('السعة')
                            ->numeric()
                            ->minValue(1),

                        Forms\Components\TextInput::make('available_slots')
                            ->label('الشواغر المتاحة')
                            ->numeric()
                            ->minValue(0),
                    ])->columns(2),

                Forms\Components\Section::make('تفاصيل إضافية')
                    ->schema([
                        Forms\Components\TextInput::make('location')
                            ->label('الموقع')
                            ->maxLength(255),

                        Forms\Components\DateTimePicker::make('start_date')
                            ->label('تاريخ البداية'),

                        Forms\Components\DateTimePicker::make('end_date')
                            ->label('تاريخ النهاية'),

                        Forms\Components\FileUpload::make('image_url')
                            ->label('صورة العرض')
                            ->image()
                            ->directory('offerings'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('نشط')
                            ->default(true),

                        Forms\Components\Toggle::make('featured')
                            ->label('مميز')
                            ->default(false),

                        Forms\Components\Toggle::make('auto_accept')
                            ->label('قبول تلقائي')
                            ->default(false),
                    ])->columns(2),

                Forms\Components\Section::make('شروط وأحكام')
                    ->schema([
                        Forms\Components\Textarea::make('terms_conditions')
                            ->label('الشروط والأحكام')
                            ->rows(3),

                        Forms\Components\Textarea::make('cancellation_policy')
                            ->label('سياسة الإلغاء')
                            ->rows(3),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_url')
                    ->label('الصورة')
                    ->circular(),

                Tables\Columns\TextColumn::make('name')
                    ->label('اسم العرض')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('price')
                    ->label('السعر')
                    ->money('SAR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('capacity')
                    ->label('السعة')
                    ->numeric(),

                Tables\Columns\TextColumn::make('available_slots')
                    ->label('الشواغر المتاحة')
                    ->numeric(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean(),

                Tables\Columns\IconColumn::make('featured')
                    ->label('مميز')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('نشط'),
                Tables\Filters\TernaryFilter::make('featured')
                    ->label('مميز'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListOfferings::route('/'),
            'create' => Pages\CreateOffering::route('/create'),
            'view' => Pages\ViewOffering::route('/{record}'),
            'edit' => Pages\EditOffering::route('/{record}/edit'),
        ];
    }
}
