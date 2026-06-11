<?php

namespace App\Filament\Resources\EventContributions\Pages;

use App\Filament\Resources\EventContributions\EventContributionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageEventContributions extends ManageRecords
{
    protected static string $resource = EventContributionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
