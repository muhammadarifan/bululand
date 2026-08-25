<?php

namespace App\Filament\Imports;

use App\Models\EventItemDonation;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class EventItemDonationImporter extends Importer
{
    protected static ?string $model = EventItemDonation::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('event')
                ->relationship(resolveUsing: 'name')
                ->example('17 Agustusan')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('item_name')
                ->example('Beras')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('quantity')
                ->example('10')
                ->numeric()
                ->rules(['numeric']),
            ImportColumn::make('unit')
                ->example('kg'),
            ImportColumn::make('price')
                ->example('15000')
                ->numeric()
                ->rules(['numeric']),
            ImportColumn::make('house')
                ->relationship(resolveUsing: 'code')
                ->example('A1'),
            ImportColumn::make('donor_name')
                ->example('Budi')
                ->rules(['max:255']),
            ImportColumn::make('is_anonymous')
                ->example('0')
                ->boolean(),
            ImportColumn::make('description')
                ->example('Donasi beras'),
        ];
    }

    public function resolveRecord(): ?EventItemDonation
    {
        return new EventItemDonation();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your event item donation import has completed and '.number_format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
