<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')->label('المفتاح')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('value')->label('القيمة')->limit(50),
                Tables\Columns\TextColumn::make('description')->label('الوصف')->limit(50),
                Tables\Columns\TextColumn::make('created_at')->label('تاريخ الإنشاء')->dateTime(),
            ])
            ->filters([
                Tables\Filters\Filter::make('key')
                    ->label('بحث بالمفتاح')
                    ->form([
                        Forms\Components\TextInput::make('key')->label('المفتاح')->required(),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query->where('key', 'like', "%{$data['key']}%")),
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
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}
