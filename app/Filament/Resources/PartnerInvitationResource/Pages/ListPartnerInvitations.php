<?php

namespace App\Filament\Resources\PartnerInvitationResource\Pages;

use App\Filament\Resources\PartnerInvitationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPartnerInvitations extends ListRecords
{
    protected static string $resource = PartnerInvitationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PartnerInvitationResource\Widgets\PartnerInvitationStatsWidget::class,
        ];
    }
}
