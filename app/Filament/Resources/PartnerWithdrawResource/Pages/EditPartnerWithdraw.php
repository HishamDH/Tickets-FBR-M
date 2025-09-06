<?php

namespace App\Filament\Resources\PartnerWithdrawResource\Pages;

use App\Filament\Resources\PartnerWithdrawResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPartnerWithdraw extends EditRecord
{
    protected static string $resource = PartnerWithdrawResource::class;

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
        return 'تم تحديث طلب السحب بنجاح';
    }
}
