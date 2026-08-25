<?php

namespace App\Filament\Imports;

use App\Models\EventMoneyTransaction;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class EventMoneyTransactionImporter extends Importer
{
    protected static ?string $model = EventMoneyTransaction::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('event')
                ->relationship(resolveUsing: 'name')
                ->example('17 Agustusan')
                ->requiredMapping()
                ->rules(['required']),
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
                ->example('Donasi warga')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('type')
                ->example('in')
                ->requiredMapping()
                ->rules(['required', 'in:in,out']),
            ImportColumn::make('category')
                ->example('donation'),
            ImportColumn::make('amount')
                ->example('100000')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'numeric']),
        ];
    }

    public function resolveRecord(): ?EventMoneyTransaction
    {
        return new EventMoneyTransaction();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your event money transaction import has completed and '.number_format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
