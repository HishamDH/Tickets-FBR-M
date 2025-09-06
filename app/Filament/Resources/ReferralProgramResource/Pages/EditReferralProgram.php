<?php

namespace App\Filament\Resources\ReferralProgramResource\Pages;

use App\Filament\Resources\ReferralProgramResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditReferralProgram extends EditRecord
{
    protected static string $resource = ReferralProgramResource::class;

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
            ->title('تم تحديث برنامج الإحالة بنجاح')
            ->body('تم حفظ التغييرات على برنامج: ' . $this->record->name);
    }
}
