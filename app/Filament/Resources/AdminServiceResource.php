<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdminServiceResource\Pages;
use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AdminServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';

    protected static ?string $navigationGroup = 'Service Management';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Service Information')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $context, $state, callable $set) => $context === 'create' ? $set('slug', \Illuminate\Support\Str::slug($state)) : null),

                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->rules(['alpha_dash']),

                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->maxLength(1000)
                            ->columnSpanFull(),

                        Forms\Components\RichEditor::make('full_description')
                            ->maxLength(5000)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Categorization & Merchant')
                    ->schema([
                        Forms\Components\Select::make('category_id')
                            ->label('Category')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('merchant_id')
                            ->label('Merchant')
                            ->relationship('merchant', 'business_name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\TagsInput::make('tags')
                            ->separator(','),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Pricing & Capacity')
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->numeric()
                            ->prefix('$')
                            ->required()
                            ->minValue(0),

                        Forms\Components\TextInput::make('discounted_price')
                            ->numeric()
                            ->prefix('$')
                            ->minValue(0)
                            ->lt('price'),

                        Forms\Components\TextInput::make('max_guests')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->default(1),

                        Forms\Components\TextInput::make('duration_hours')
                            ->numeric()
                            ->suffix('hours')
                            ->minValue(0.5)
                            ->step(0.5)
                            ->default(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Media & Images')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->image()
                            ->imageEditor()
                            ->directory('services')
                            ->maxSize(2048)
                            ->required(),

                        Forms\Components\FileUpload::make('gallery')
                            ->image()
                            ->imageEditor()
                            ->directory('services/gallery')
                            ->multiple()
                            ->maxFiles(10)
                            ->maxSize(2048),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Availability & Settings')
                    ->schema([
                        Forms\Components\TimePicker::make('available_from')
                            ->label('Available From')
                            ->default('09:00'),

                        Forms\Components\TimePicker::make('available_until')
                            ->label('Available Until')
                            ->default('18:00'),

                        Forms\Components\CheckboxList::make('available_days')
                            ->options([
                                'monday' => 'Monday',
                                'tuesday' => 'Tuesday',
                                'wednesday' => 'Wednesday',
                                'thursday' => 'Thursday',
                                'friday' => 'Friday',
                                'saturday' => 'Saturday',
                                'sunday' => 'Sunday',
                            ])
                            ->default(['monday', 'tuesday', 'wednesday', 'thursday', 'friday'])
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Service Status')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                                'suspended' => 'Suspended',
                            ])
                            ->required()
                            ->default('draft'),

                        Forms\Components\Toggle::make('is_featured')
                            ->label('Featured Service'),

                        Forms\Components\Toggle::make('requires_approval')
                            ->label('Requires Manual Approval')
                            ->default(false),

                        Forms\Components\TextInput::make('sort_order')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Location & Requirements')
                    ->schema([
                        Forms\Components\Textarea::make('location')
                            ->maxLength(500)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('requirements')
                            ->label('Special Requirements')
                            ->maxLength(1000)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('cancellation_policy')
                            ->maxLength(1000)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->circular(),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('merchant.business_name')
                    ->label('Merchant')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('price')
                    ->money('USD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('discounted_price')
                    ->money('USD')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('max_guests')
                    ->label('Max Guests')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'secondary',
                        'active' => 'success',
                        'inactive' => 'warning',
                        'suspended' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean(),

                Tables\Columns\TextColumn::make('bookings_count')
                    ->label('Bookings')
                    ->counts('bookings')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'suspended' => 'Suspended',
                    ]),

                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('merchant')
                    ->relationship('merchant', 'business_name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Featured Services'),

                Tables\Filters\Filter::make('price_range')
                    ->form([
                        Forms\Components\TextInput::make('price_from')
                            ->numeric()
                            ->label('Price From'),
                        Forms\Components\TextInput::make('price_to')
                            ->numeric()
                            ->label('Price To'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['price_from'],
                                fn (Builder $query, $price): Builder => $query->where('price', '>=', $price),
                            )
                            ->when(
                                $data['price_to'],
                                fn (Builder $query, $price): Builder => $query->where('price', '<=', $price),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),

                    Tables\Actions\Action::make('activate')
                        ->label('Activate')
                        ->icon('heroicon-o-play')
                        ->color('success')
                        ->visible(fn (Service $record): bool => $record->status !== 'active')
                        ->action(fn (Service $record) => $record->update(['status' => 'active'])),

                    Tables\Actions\Action::make('deactivate')
                        ->label('Deactivate')
                        ->icon('heroicon-o-pause')
                        ->color('warning')
                        ->visible(fn (Service $record): bool => $record->status === 'active')
                        ->requiresConfirmation()
                        ->action(fn (Service $record) => $record->update(['status' => 'inactive'])),

                    Tables\Actions\Action::make('suspend')
                        ->label('Suspend')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->visible(fn (Service $record): bool => in_array($record->status, ['active', 'inactive']))
                        ->requiresConfirmation()
                        ->action(fn (Service $record) => $record->update(['status' => 'suspended'])),

                    Tables\Actions\Action::make('toggle_featured')
                        ->label(fn (Service $record): string => $record->is_featured ? 'Remove Featured' : 'Make Featured')
                        ->icon('heroicon-o-star')
                        ->color('warning')
                        ->action(fn (Service $record) => $record->update(['is_featured' => ! $record->is_featured])),

                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                    Tables\Actions\BulkAction::make('activate_services')
                        ->label('Activate Selected')
                        ->icon('heroicon-o-play')
                        ->color('success')
                        ->action(fn ($records) => $records->each(fn ($record) => $record->update(['status' => 'active']))),

                    Tables\Actions\BulkAction::make('deactivate_services')
                        ->label('Deactivate Selected')
                        ->icon('heroicon-o-pause')
                        ->color('warning')
                        ->action(fn ($records) => $records->each(fn ($record) => $record->update(['status' => 'inactive']))),

                    Tables\Actions\BulkAction::make('feature_services')
                        ->label('Make Featured')
                        ->icon('heroicon-o-star')
                        ->color('warning')
                        ->action(fn ($records) => $records->each(fn ($record) => $record->update(['is_featured' => true]))),
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
            'view' => Pages\ViewService::route('/{record}'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['merchant', 'category']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'description', 'merchant.business_name', 'category.name'];
    }
}
