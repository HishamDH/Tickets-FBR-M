<?php

namespace App\Filament\Resources\OfferingResource\Pages;

use App\Filament\Resources\OfferingResource;
use App\Filament\Resources\OfferingResource\Widgets\OfferingStatsWidget;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOfferings extends ListRecords
{
    protected static string $resource = OfferingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('إضافة عرض جديد')
                ->icon('heroicon-o-plus'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            OfferingStatsWidget::class,
        ];
    }

    public function getTitle(): string
    {
        return 'إدارة العروض';
    }
}
