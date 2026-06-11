<?php

namespace App\Filament\Resources\EventSpendings\Pages;

use App\Filament\Resources\EventSpendings\EventSpendingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageEventSpendings extends ManageRecords
{
    protected static string $resource = EventSpendingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
