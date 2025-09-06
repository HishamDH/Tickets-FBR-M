<?php

namespace App\Filament\Resources\ReferralProgramResource\Pages;

use App\Filament\Resources\ReferralProgramResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReferralPrograms extends ListRecords
{
    protected static string $resource = ReferralProgramResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('إنشاء برنامج إحالة جديد')
                ->icon('heroicon-o-plus'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ReferralProgramResource\Widgets\ReferralStatsWidget::class,
        ];
    }
}
