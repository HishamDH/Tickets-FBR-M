<?php

namespace App\Filament\Resources\PartnerWithdrawResource\Pages;

use App\Filament\Resources\PartnerWithdrawResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePartnerWithdraw extends CreateRecord
{
    protected static string $resource = PartnerWithdrawResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'تم إنشاء طلب السحب بنجاح';
    }
}
