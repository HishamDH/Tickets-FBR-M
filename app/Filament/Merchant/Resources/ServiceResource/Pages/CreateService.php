<?php

namespace App\Filament\Merchant\Resources\ServiceResource\Pages;

use App\Filament\Merchant\Resources\ServiceResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateService extends CreateRecord
{
    protected static string $resource = ServiceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();
        $merchant = $user->merchant;

        $data['merchant_id'] = $merchant->id;

        return $data;
    }

    public function getTitle(): string
    {
        return 'Create New Service';
    }
}
