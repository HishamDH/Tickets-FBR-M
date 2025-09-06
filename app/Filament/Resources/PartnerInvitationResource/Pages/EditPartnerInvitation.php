<?php

namespace App\Filament\Resources\PartnerInvitationResource\Pages;

use App\Filament\Resources\PartnerInvitationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPartnerInvitation extends EditRecord
{
    protected static string $resource = PartnerInvitationResource::class;

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
        return 'تم تحديث الدعوة بنجاح';
    }
}
