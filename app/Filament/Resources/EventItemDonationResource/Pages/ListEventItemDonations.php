<?php

namespace App\Filament\Resources\EventItemDonationResource\Pages;

use App\Filament\Resources\EventItemDonationResource;
use Filament\Resources\Pages\ListRecords;

class ListEventItemDonations extends ListRecords
{
    protected static string $resource = EventItemDonationResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
