<?php

namespace App\Filament\Exports;

use App\Models\EventMoneyTransaction;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class EventMoneyTransactionExporter extends Exporter
{
    protected static ?string $model = EventMoneyTransaction::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('event.name')->label('Event'),
            ExportColumn::make('house.code')->label('House'),
            ExportColumn::make('donor_name')->label('Donor Name'),
            ExportColumn::make('is_anonymous')->label('Anonymous Donor'),
            ExportColumn::make('description'),
            ExportColumn::make('type'),
            ExportColumn::make('category'),
            ExportColumn::make('amount'),
            ExportColumn::make('created_at')->label('Transaction Date'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your event money transaction export has completed and '.number_format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to export.';
        }

        return $body;
    }
}
