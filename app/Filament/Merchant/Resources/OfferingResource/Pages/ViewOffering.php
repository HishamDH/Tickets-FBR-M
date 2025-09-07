<?php

namespace App\Filament\Merchant\Resources\OfferingResource\Pages;

use App\Filament\Merchant\Resources\OfferingResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewOffering extends ViewRecord
{
    protected static string $resource = OfferingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
