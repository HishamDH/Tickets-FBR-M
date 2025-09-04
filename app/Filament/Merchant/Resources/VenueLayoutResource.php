<?php

namespace App\Filament\Merchant\Resources;

use App\Filament\Merchant\Resources\VenueLayoutResource\Pages;
use App\Models\VenueLayout;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class VenueLayoutResource extends Resource
{
    protected static ?string $model = VenueLayout::class;

    protected static ?string $navigationIcon = 'heroicon-o-map';

    protected static ?string $navigationLabel = 'تخطيط المقاعد';

    protected static ?string $modelLabel = 'تخطيط المقاعد';

    protected static ?string $pluralModelLabel = 'تخطيطات المقاعد';

    protected static ?string $navigationGroup = 'إدارة الخدمات';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('merchant_id', Auth::id());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('اسم التخطيط')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\Select::make('offering_id')
                            ->label('الخدمة')
                            ->relationship(
                                name: 'offering',
                                titleAttribute: 'title',
                                modifyQueryUsing: fn (Builder $query) => $query->where('user_id', Auth::id())
                            )
                            ->required(),
                    ]),

                Forms\Components\Grid::make(3)
                    ->schema([
                        Forms\Components\TextInput::make('rows')
                            ->label('عدد الصفوف')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->maxValue(50),
                        
                        Forms\Components\TextInput::make('columns')
                            ->label('عدد الأعمدة')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->maxValue(50),
                        
                        Forms\Components\TextInput::make('total_seats')
                            ->label('إجمالي المقاعد')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false),
                    ]),

                Forms\Components\Textarea::make('description')
                    ->label('وصف التخطيط')
                    ->rows(3)
                    ->columnSpanFull(),

                Forms\Components\Toggle::make('is_active')
                    ->label('مفعل')
                    ->default(true),

                Forms\Components\Section::make('تصميم المقاعد')
                    ->description('استخدم الأداة التفاعلية لتصميم تخطيط المقاعد')
                    ->schema([
                        Forms\Components\ViewField::make('seat_designer')
                            ->view('filament.merchant.components.seat-designer')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('اسم التخطيط')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('offering.title')
                    ->label('الخدمة')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_seats')
                    ->label('إجمالي المقاعد')
                    ->sortable()
                    ->badge(),

                Tables\Columns\TextColumn::make('available_seats_count')
                    ->label('المقاعد المتاحة')
                    ->sortable()
                    ->badge()
                    ->color('success'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('الحالة')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('الحالة')
                    ->boolean()
                    ->trueLabel('مفعل')
                    ->falseLabel('معطل')
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\Action::make('designer')
                    ->label('مصمم المقاعد')
                    ->icon('heroicon-o-squares-plus')
                    ->url(fn (VenueLayout $record): string => route('merchant.venue-layout.designer', $record))
                    ->openUrlInNewTab(),
                
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListVenueLayouts::route('/'),
            'create' => Pages\CreateVenueLayout::route('/create'),
            'view' => Pages\ViewVenueLayout::route('/{record}'),
            'edit' => Pages\EditVenueLayout::route('/{record}/edit'),
        ];
    }
}
