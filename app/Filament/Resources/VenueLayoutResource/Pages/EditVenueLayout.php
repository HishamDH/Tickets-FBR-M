<?php

namespace App\Filament\Resources\VenueLayoutResource\Pages;

use App\Filament\Resources\VenueLayoutResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVenueLayout extends EditRecord
{
    protected static string $resource = VenueLayoutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
