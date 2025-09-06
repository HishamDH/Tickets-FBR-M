<?php

namespace App\Filament\Resources\MerchantWithdrawResource\Pages;

use App\Filament\Resources\MerchantWithdrawResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMerchantWithdraws extends ListRecords
{
    protected static string $resource = MerchantWithdrawResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
