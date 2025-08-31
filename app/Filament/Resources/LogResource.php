<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LogResource\Pages;
use App\Filament\Resources\LogResource\RelationManagers;
use App\Models\Log;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LogResource extends Resource
{
    protected static ?string $model = Log::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('action')
                    ->label('الإجراء')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('details')
                    ->label('التفاصيل')
                    ->rows(3),
                Forms\Components\TextInput::make('user_id')
                    ->label('معرف المستخدم')
                    ->required(),
                Forms\Components\DateTimePicker::make('created_at')
                    ->label('تاريخ السجل')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('action')->label('الإجراء')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('details')->label('التفاصيل')->limit(50),
                Tables\Columns\TextColumn::make('user_id')->label('معرف المستخدم')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label('تاريخ السجل')->dateTime(),
            ])
            ->filters([
                Tables\Filters\Filter::make('action')
                    ->label('بحث بالإجراء')
                    ->form([
                        Forms\Components\TextInput::make('action')->label('الإجراء')->required(),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query->where('action', 'like', "%{$data['action']}%")),
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
            'index' => Pages\ListLogs::route('/'),
            'create' => Pages\CreateLog::route('/create'),
            'edit' => Pages\EditLog::route('/{record}/edit'),
        ];
    }
}
