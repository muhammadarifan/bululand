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
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('item_name')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('quantity')
                ->numeric()
                ->rules(['numeric']),
            ImportColumn::make('unit'),
            ImportColumn::make('price')
                ->numeric()
                ->rules(['numeric']),
            ImportColumn::make('house')
                ->relationship(resolveUsing: 'code'),
            ImportColumn::make('donor_name')
                ->rules(['max:255']),
            ImportColumn::make('is_anonymous')
                ->boolean(),
            ImportColumn::make('description'),
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
