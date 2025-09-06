<?php

namespace App\Filament\Resources\LoyaltyProgramResource\Pages;

use App\Filament\Resources\LoyaltyProgramResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditLoyaltyProgram extends EditRecord
{
    protected static string $resource = LoyaltyProgramResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()
                ->label('عرض'),
            Actions\DeleteAction::make()
                ->label('حذف'),
            Actions\RestoreAction::make()
                ->label('استعادة'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('تم تحديث برنامج الولاء بنجاح')
            ->body('تم حفظ التغييرات على برنامج: ' . $this->record->name);
    }
}
