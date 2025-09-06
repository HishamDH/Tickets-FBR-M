<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReviewResource\Pages;
use App\Models\Review;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

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
                Forms\Components\Toggle::make('is_approved')
                    ->label('معتمد')
                    ->default(false),
                Forms\Components\Textarea::make('admin_notes')
                    ->label('ملاحظات الإدارة')
                    ->rows(2),
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
                Tables\Columns\IconColumn::make('is_approved')
                    ->label('الحالة')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
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
                Tables\Filters\TernaryFilter::make('is_approved')
                    ->label('حالة الموافقة')
                    ->boolean()
                    ->trueLabel('معتمد')
                    ->falseLabel('غير معتمد')
                    ->placeholder('جميع المراجعات'),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('موافقة')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->action(fn (Review $record) => $record->update(['is_approved' => true]))
                    ->visible(fn (Review $record) => !$record->is_approved)
                    ->requiresConfirmation()
                    ->modalHeading('موافقة على المراجعة')
                    ->modalDescription('هل أنت متأكد من الموافقة على هذه المراجعة؟'),
                
                Tables\Actions\Action::make('reject')
                    ->label('رفض')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->action(fn (Review $record) => $record->update(['is_approved' => false]))
                    ->visible(fn (Review $record) => $record->is_approved)
                    ->requiresConfirmation()
                    ->modalHeading('رفض المراجعة')
                    ->modalDescription('هل أنت متأكد من رفض هذه المراجعة؟'),
                
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approve_all')
                        ->label('الموافقة على المحدد')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['is_approved' => true]))
                        ->requiresConfirmation()
                        ->modalHeading('الموافقة على المراجعات المحددة')
                        ->modalDescription('هل أنت متأكد من الموافقة على جميع المراجعات المحددة؟'),
                    
                    Tables\Actions\BulkAction::make('reject_all')
                        ->label('رفض المحدد')
                        ->icon('heroicon-o-x-mark')
                        ->color('danger')
                        ->action(fn ($records) => $records->each->update(['is_approved' => false]))
                        ->requiresConfirmation()
                        ->modalHeading('رفض المراجعات المحددة')
                        ->modalDescription('هل أنت متأكد من رفض جميع المراجعات المحددة؟'),
                        
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
