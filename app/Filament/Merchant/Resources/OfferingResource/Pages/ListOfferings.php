<?php

namespace App\Filament\Merchant\Resources\OfferingResource\Pages;

use App\Filament\Merchant\Resources\OfferingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOfferings extends ListRecords
{
    protected static string $resource = OfferingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
