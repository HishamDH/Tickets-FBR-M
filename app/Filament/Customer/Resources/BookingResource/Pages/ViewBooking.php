<?php

namespace App\Filament\Customer\Resources\BookingResource\Pages;

use App\Filament\Customer\Resources\BookingResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBooking extends ViewRecord
{
    protected static string $resource = BookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('download_ticket')
                ->label('Download Ticket')
                ->icon('heroicon-o-document-arrow-down')
                ->url(fn () => route('customer.booking.ticket', $this->record))
                ->openUrlInNewTab()
                ->visible(fn () => $this->record->status === 'confirmed'),
        ];
    }
}