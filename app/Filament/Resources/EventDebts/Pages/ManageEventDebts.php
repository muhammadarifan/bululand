<?php

namespace App\Filament\Resources\EventDebts\Pages;

use App\Filament\Resources\EventDebts\EventDebtResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageEventDebts extends ManageRecords
{
    protected static string $resource = EventDebtResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
