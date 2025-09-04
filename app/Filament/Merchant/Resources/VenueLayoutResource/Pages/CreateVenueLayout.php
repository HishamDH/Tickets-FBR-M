<?php

namespace App\Filament\Merchant\Resources\VenueLayoutResource\Pages;

use App\Filament\Merchant\Resources\VenueLayoutResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateVenueLayout extends CreateRecord
{
    protected static string $resource = VenueLayoutResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['merchant_id'] = Auth::id();
        
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
