<?php

namespace App\Filament\Resources\WhatsappWhitelists\Pages;

use App\Filament\Resources\WhatsappWhitelists\WhatsappWhitelistResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageWhatsappWhitelists extends ManageRecords
{
    protected static string $resource = WhatsappWhitelistResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
