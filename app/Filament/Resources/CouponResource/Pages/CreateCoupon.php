<?php

namespace App\Filament\Resources\CouponResource\Pages;

use App\Filament\Resources\CouponResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateCoupon extends CreateRecord
{
    protected static string $resource = CouponResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('تم إنشاء الكوبون بنجاح')
            ->body('تم إنشاء كوبون جديد بالكود: ' . $this->record->code);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Convert code to uppercase
        $data['code'] = strtoupper($data['code']);
        
        return $data;
    }
}
