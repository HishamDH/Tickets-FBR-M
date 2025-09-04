<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VenueLayoutResource\Pages;
use App\Filament\Resources\VenueLayoutResource\RelationManagers;
use App\Models\VenueLayout;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VenueLayoutResource extends Resource
{
    protected static ?string $model = VenueLayout::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('service_id')
                    ->relationship('service', 'name')
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('layout_type')
                    ->required(),
                Forms\Components\TextInput::make('total_capacity')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('layout_config')
                    ->required(),
                Forms\Components\TextInput::make('width')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('height')
                    ->required()
                    ->numeric(),
                Forms\Components\Toggle::make('is_active')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('service.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('layout_type'),
                Tables\Columns\TextColumn::make('total_capacity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('width')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('height')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
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
            'edit' => Pages\EditVenueLayout::route('/{record}/edit'),
        ];
    }
}
