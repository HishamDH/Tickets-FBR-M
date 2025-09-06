<?php

namespace App\Filament\Resources\PartnerWalletResource\Pages;

use App\Filament\Resources\PartnerWalletResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePartnerWallet extends CreateRecord
{
    protected static string $resource = PartnerWalletResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'تم إنشاء محفظة الشريك بنجاح';
    }
}
