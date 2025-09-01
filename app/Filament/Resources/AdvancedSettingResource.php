<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdvancedSettingResource\Pages;
use App\Models\AdvancedSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AdvancedSettingResource extends Resource
{
    protected static ?string $model = AdvancedSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('key')
                    ->label('المفتاح')
                    ->required()
                    ->unique()
                    ->maxLength(255),
                Forms\Components\Textarea::make('value')
                    ->label('القيمة')
                    ->required()
                    ->rows(3),
                Forms\Components\Textarea::make('description')
                    ->label('الوصف')
                    ->rows(2),
                Forms\Components\Select::make('type')
                    ->label('النوع')
                    ->options([
                        'string' => 'نص',
                        'integer' => 'عدد صحيح',
                        'boolean' => 'قيمة منطقية',
                        'json' => 'JSON',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')->label('المفتاح')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('value')->label('القيمة')->limit(50),
                Tables\Columns\TextColumn::make('type')->label('النوع')->enum([
                    'string' => 'نص',
                    'integer' => 'عدد صحيح',
                    'boolean' => 'قيمة منطقية',
                    'json' => 'JSON',
                ]),
                Tables\Columns\TextColumn::make('description')->label('الوصف')->limit(50),
                Tables\Columns\TextColumn::make('created_at')->label('تاريخ الإنشاء')->dateTime(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('النوع')
                    ->options([
                        'string' => 'نص',
                        'integer' => 'عدد صحيح',
                        'boolean' => 'قيمة منطقية',
                        'json' => 'JSON',
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
            'index' => Pages\ListAdvancedSettings::route('/'),
            'create' => Pages\CreateAdvancedSetting::route('/create'),
            'edit' => Pages\EditAdvancedSetting::route('/{record}/edit'),
        ];
    }
}
