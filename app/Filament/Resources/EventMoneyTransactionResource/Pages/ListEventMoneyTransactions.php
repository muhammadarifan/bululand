<?php

namespace App\Filament\Resources\EventMoneyTransactionResource\Pages;

use App\Filament\Resources\EventMoneyTransactionResource;
use Filament\Resources\Pages\ListRecords;

class ListEventMoneyTransactions extends ListRecords
{
    protected static string $resource = EventMoneyTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
