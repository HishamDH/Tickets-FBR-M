<?php

namespace App\Filament\Merchant\Resources\VenueLayoutResource\Pages;

use App\Filament\Merchant\Resources\VenueLayoutResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVenueLayout extends EditRecord
{
    protected static string $resource = VenueLayoutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
