<?php

namespace App\Filament\Merchant\Resources\VenueLayoutResource\Pages;

use App\Filament\Merchant\Resources\VenueLayoutResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVenueLayouts extends ListRecords
{
    protected static string $resource = VenueLayoutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
