<?php

namespace App\Filament\Exports;

use App\Models\EventItemDonation;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class EventItemDonationExporter extends Exporter
{
    protected static ?string $model = EventItemDonation::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('event.name')->label('Event'),
            ExportColumn::make('item_name')->label('Item Name'),
            ExportColumn::make('quantity'),
            ExportColumn::make('unit'),
            ExportColumn::make('price')->label('Harga'),
            ExportColumn::make('house.code')->label('House'),
            ExportColumn::make('donor_name')->label('Donor Name'),
            ExportColumn::make('is_anonymous')->label('Anonymous Donor'),
            ExportColumn::make('description'),
            ExportColumn::make('created_at')->label('Donation Date'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your event item donation export has completed and '.number_format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to export.';
        }

        return $body;
    }
}
