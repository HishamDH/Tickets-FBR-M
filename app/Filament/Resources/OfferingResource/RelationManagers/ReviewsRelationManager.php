<?php

namespace App\Filament\Resources\OfferingResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ReviewsRelationManager extends RelationManager
{
    protected static string $relationship = 'reviews';

    protected static ?string $title = 'التقييمات';

    protected static ?string $modelLabel = 'تقييم';

    protected static ?string $pluralModelLabel = 'التقييمات';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('العميل')
                            ->relationship('customer', 'name')
                            ->searchable()
                            ->required(),

                        Forms\Components\Select::make('rating')
                            ->label('التقييم')
                            ->options([
                                1 => '⭐ نجمة واحدة',
                                2 => '⭐⭐ نجمتان',
                                3 => '⭐⭐⭐ ثلاث نجوم',
                                4 => '⭐⭐⭐⭐ أربع نجوم',
                                5 => '⭐⭐⭐⭐⭐ خمس نجوم',
                            ])
                            ->required(),
                    ]),

                Forms\Components\Textarea::make('review')
                    ->label('المراجعة')
                    ->rows(3)
                    ->columnSpanFull(),

                Forms\Components\Toggle::make('is_approved')
                    ->label('موافق عليه')
                    ->default(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('customer.name')
            ->columns([
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('العميل')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('rating')
                    ->label('التقييم')
                    ->formatStateUsing(fn (int $state): string => str_repeat('⭐', $state))
                    ->sortable(),

                Tables\Columns\TextColumn::make('review')
                    ->label('المراجعة')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }

                        return $state;
                    }),

                Tables\Columns\IconColumn::make('is_approved')
                    ->label('موافق عليه')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ التقييم')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('rating')
                    ->label('التقييم')
                    ->options([
                        1 => '⭐ نجمة واحدة',
                        2 => '⭐⭐ نجمتان',
                        3 => '⭐⭐⭐ ثلاث نجوم',
                        4 => '⭐⭐⭐⭐ أربع نجوم',
                        5 => '⭐⭐⭐⭐⭐ خمس نجوم',
                    ]),

                Tables\Filters\TernaryFilter::make('is_approved')
                    ->label('حالة الموافقة')
                    ->trueLabel('موافق عليه')
                    ->falseLabel('غير موافق عليه')
                    ->placeholder('الكل'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('إضافة تقييم جديد'),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('موافقة')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn ($record) => ! $record->is_approved)
                    ->action(fn ($record) => $record->update(['is_approved' => true])),

                Tables\Actions\Action::make('reject')
                    ->label('رفض')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn ($record) => $record->is_approved)
                    ->action(fn ($record) => $record->update(['is_approved' => false])),

                Tables\Actions\EditAction::make()
                    ->label('تعديل'),

                Tables\Actions\DeleteAction::make()
                    ->label('حذف'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approve_selected')
                        ->label('الموافقة على المحدد')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['is_approved' => true])),

                    Tables\Actions\BulkAction::make('reject_selected')
                        ->label('رفض المحدد')
                        ->icon('heroicon-o-x-mark')
                        ->color('danger')
                        ->action(fn ($records) => $records->each->update(['is_approved' => false])),

                    Tables\Actions\DeleteBulkAction::make()
                        ->label('حذف المحدد'),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
