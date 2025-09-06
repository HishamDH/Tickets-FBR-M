<?php

namespace App\Filament\Resources\PartnerWalletTransactionResource\Pages;

use App\Filament\Resources\PartnerWalletTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPartnerWalletTransactions extends ListRecords
{
    protected static string $resource = PartnerWalletTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
