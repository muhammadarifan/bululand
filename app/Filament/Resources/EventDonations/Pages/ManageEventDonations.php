<?php

namespace App\Filament\Resources\EventDonations\Pages;

use App\Filament\Resources\EventDonations\EventDonationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageEventDonations extends ManageRecords
{
    protected static string $resource = EventDonationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
