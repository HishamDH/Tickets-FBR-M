<?php

namespace App\Filament\Customer\Resources\MyBookingResource\Pages;

use App\Filament\Customer\Resources\MyBookingResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMyBooking extends ViewRecord
{
    protected static string $resource = MyBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(fn (): bool => in_array($this->record->status, ['pending', 'confirmed'])),
        ];
    }
}
