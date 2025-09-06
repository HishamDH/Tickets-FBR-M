<?php

namespace App\Filament\Resources\LoyaltyProgramResource\Pages;

use App\Filament\Resources\LoyaltyProgramResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLoyaltyPrograms extends ListRecords
{
    protected static string $resource = LoyaltyProgramResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('إنشاء برنامج ولاء جديد')
                ->icon('heroicon-o-plus'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            LoyaltyProgramResource\Widgets\LoyaltyStatsWidget::class,
        ];
    }
}
