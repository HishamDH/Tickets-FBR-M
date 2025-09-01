<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StatisticsResource\Pages;
use App\Models\Statistics;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StatisticsResource extends Resource
{
    protected static ?string $model = Statistics::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('اسم الإحصائية')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->label('الوصف')
                    ->rows(3),
                Forms\Components\TextInput::make('value')
                    ->label('القيمة')
                    ->required(),
                Forms\Components\Select::make('type')
                    ->label('النوع')
                    ->options([
                        'number' => 'رقم',
                        'percentage' => 'نسبة مئوية',
                        'currency' => 'عملة',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('اسم الإحصائية')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('description')->label('الوصف')->limit(50),
                Tables\Columns\TextColumn::make('value')->label('القيمة'),
                Tables\Columns\TextColumn::make('type')->label('النوع')->enum([
                    'number' => 'رقم',
                    'percentage' => 'نسبة مئوية',
                    'currency' => 'عملة',
                ]),
                Tables\Columns\TextColumn::make('created_at')->label('تاريخ الإنشاء')->dateTime(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('النوع')
                    ->options([
                        'number' => 'رقم',
                        'percentage' => 'نسبة مئوية',
                        'currency' => 'عملة',
                    ]),
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
            'index' => Pages\ListStatistics::route('/'),
            'create' => Pages\CreateStatistics::route('/create'),
            'edit' => Pages\EditStatistics::route('/{record}/edit'),
        ];
    }
}
