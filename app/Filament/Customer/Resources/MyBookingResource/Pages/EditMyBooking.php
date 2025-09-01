<?php

namespace App\Filament\Customer\Resources\MyBookingResource\Pages;

use App\Filament\Customer\Resources\MyBookingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMyBooking extends EditRecord
{
    protected static string $resource = MyBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->visible(fn (): bool => in_array($this->record->status, ['pending'])),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
