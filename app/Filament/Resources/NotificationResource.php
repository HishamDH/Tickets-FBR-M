<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NotificationResource\Pages;
use App\Filament\Resources\NotificationResource\RelationManagers;
use App\Models\Notification;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class NotificationResource extends Resource
{
    protected static ?string $model = Notification::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('العنوان')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('message')
                    ->label('الرسالة')
                    ->required()
                    ->rows(3),
                Forms\Components\Select::make('type')
                    ->label('النوع')
                    ->options([
                        'info' => 'معلومات',
                        'success' => 'نجاح',
                        'warning' => 'تحذير',
                        'error' => 'خطأ',
                    ])
                    ->required(),
                Forms\Components\Toggle::make('is_read')
                    ->label('تمت القراءة')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('العنوان')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('message')->label('الرسالة')->limit(50),
                Tables\Columns\TextColumn::make('type')->label('النوع')->enum([
                    'info' => 'معلومات',
                    'success' => 'نجاح',
                    'warning' => 'تحذير',
                    'error' => 'خطأ',
                ]),
                Tables\Columns\IconColumn::make('is_read')->label('تمت القراءة')->boolean(),
                Tables\Columns\TextColumn::make('created_at')->label('تاريخ الإرسال')->dateTime(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('النوع')
                    ->options([
                        'info' => 'معلومات',
                        'success' => 'نجاح',
                        'warning' => 'تحذير',
                        'error' => 'خطأ',
                    ]),
                Tables\Filters\TernaryFilter::make('is_read')
                    ->label('حالة القراءة')
                    ->trueLabel('تمت القراءة')
                    ->falseLabel('لم تتم القراءة')
                    ->placeholder('الكل'),
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
            'index' => Pages\ListNotifications::route('/'),
            'create' => Pages\CreateNotification::route('/create'),
            'edit' => Pages\EditNotification::route('/{record}/edit'),
        ];
    }
}
