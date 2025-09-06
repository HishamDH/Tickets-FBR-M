<?php

namespace App\Filament\Resources\CouponResource\Pages;

use App\Filament\Resources\CouponResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditCoupon extends EditRecord
{
    protected static string $resource = CouponResource::class;

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
            ->title('تم تحديث الكوبون بنجاح')
            ->body('تم حفظ التغييرات على الكوبون: ' . $this->record->code);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Convert code to uppercase
        $data['code'] = strtoupper($data['code']);
        
        return $data;
    }
}
