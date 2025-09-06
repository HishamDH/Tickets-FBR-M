<?php

namespace App\Filament\Resources\PartnerInvitationResource\Pages;

use App\Filament\Resources\PartnerInvitationResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreatePartnerInvitation extends CreateRecord
{
    protected static string $resource = PartnerInvitationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // إنشاء رمز الدعوة تلقائياً
        $data['token'] = Str::random(32);
        
        // تعيين تاريخ انتهاء الصلاحية إذا لم يتم تحديده
        if (!isset($data['expires_at'])) {
            $data['expires_at'] = now()->addDays(config('partner.invitation_expires_days', 30));
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'تم إنشاء الدعوة بنجاح';
    }
}
