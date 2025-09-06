<?php

namespace App\Filament\Resources\PartnerWalletTransactionResource\Pages;

use App\Filament\Resources\PartnerWalletTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePartnerWalletTransaction extends CreateRecord
{
    protected static string $resource = PartnerWalletTransactionResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'تم إنشاء المعاملة بنجاح';
    }
}
