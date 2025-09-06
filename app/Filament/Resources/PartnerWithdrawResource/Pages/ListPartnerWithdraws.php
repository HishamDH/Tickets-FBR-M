<?php

namespace App\Filament\Resources\PartnerWithdrawResource\Pages;

use App\Filament\Resources\PartnerWithdrawResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPartnerWithdraws extends ListRecords
{
    protected static string $resource = PartnerWithdrawResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PartnerWithdrawResource\Widgets\PartnerWithdrawStatsWidget::class,
        ];
    }
}
