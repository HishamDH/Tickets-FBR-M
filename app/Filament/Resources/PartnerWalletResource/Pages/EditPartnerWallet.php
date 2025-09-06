<?php

namespace App\Filament\Resources\PartnerWalletResource\Pages;

use App\Filament\Resources\PartnerWalletResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPartnerWallet extends EditRecord
{
    protected static string $resource = PartnerWalletResource::class;

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

    protected function getSavedNotificationTitle(): ?string
    {
        return 'تم تحديث محفظة الشريك بنجاح';
    }
}
