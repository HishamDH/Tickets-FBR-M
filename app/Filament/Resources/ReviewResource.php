<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReviewResource\Pages;
use App\Filament\Resources\ReviewResource\RelationManagers;
use App\Models\Review;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('offering_id')
                    ->relationship('offering', 'name')
                    ->required()
                    ->label('العرض'),
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->label('المستخدم'),
                Forms\Components\TextInput::make('rating')
                    ->numeric()
                    ->min(1)
                    ->max(5)
                    ->required()
                    ->label('التقييم'),
                Forms\Components\Textarea::make('review_text')
                    ->label('نص المراجعة')
                    ->rows(3),
                Forms\Components\DateTimePicker::make('reviewed_at')
                    ->required()
                    ->label('تاريخ المراجعة'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('offering.name')->label('العرض'),
                Tables\Columns\TextColumn::make('user.name')->label('المستخدم'),
                Tables\Columns\TextColumn::make('rating')->label('التقييم')->sortable(),
                Tables\Columns\TextColumn::make('review_text')->label('نص المراجعة')->limit(50),
                Tables\Columns\TextColumn::make('reviewed_at')->label('تاريخ المراجعة')->dateTime(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('rating')
                    ->label('التقييم')
                    ->options([
                        '1' => '1 نجمة',
                        '2' => '2 نجوم',
                        '3' => '3 نجوم',
                        '4' => '4 نجوم',
                        '5' => '5 نجوم',
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
            'index' => Pages\ListReviews::route('/'),
            'create' => Pages\CreateReview::route('/create'),
            'edit' => Pages\EditReview::route('/{record}/edit'),
        ];
    }
}
