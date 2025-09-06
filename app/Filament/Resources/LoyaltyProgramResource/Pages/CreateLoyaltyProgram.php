<?php

namespace App\Filament\Resources\LoyaltyProgramResource\Pages;

use App\Filament\Resources\LoyaltyProgramResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateLoyaltyProgram extends CreateRecord
{
    protected static string $resource = LoyaltyProgramResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('تم إنشاء برنامج الولاء بنجاح')
            ->body('تم إنشاء برنامج ولاء جديد: ' . $this->record->name);
    }
}
