<?php

namespace App\Filament\Resources\ReferralProgramResource\Pages;

use App\Filament\Resources\ReferralProgramResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateReferralProgram extends CreateRecord
{
    protected static string $resource = ReferralProgramResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('تم إنشاء برنامج الإحالة بنجاح')
            ->body('تم إنشاء برنامج إحالة جديد: ' . $this->record->name);
    }
}
