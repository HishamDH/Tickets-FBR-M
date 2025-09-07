<?php

namespace App\Filament\Merchant\Resources\OfferingResource\Pages;

use App\Filament\Merchant\Resources\OfferingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOffering extends EditRecord
{
    protected static string $resource = OfferingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
