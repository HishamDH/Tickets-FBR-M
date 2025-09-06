<?php

namespace App\Filament\Resources\PartnerWalletTransactionResource\Pages;

use App\Filament\Resources\PartnerWalletTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPartnerWalletTransaction extends EditRecord
{
    protected static string $resource = PartnerWalletTransactionResource::class;

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
        return 'تم تحديث المعاملة بنجاح';
    }
}
