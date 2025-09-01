<?php

namespace App\Filament\Resources\OfferingResource\Pages;

use App\Filament\Resources\OfferingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOffering extends CreateRecord
{
    protected static string $resource = OfferingResource::class;

    public function getTitle(): string
    {
        return 'إضافة عرض جديد';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'تم إنشاء العرض بنجاح';
    }
}
