<?php

namespace App\Filament\Merchant\Resources\VenueLayoutResource\Pages;

use App\Filament\Merchant\Resources\VenueLayoutResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewVenueLayout extends ViewRecord
{
    protected static string $resource = VenueLayoutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
