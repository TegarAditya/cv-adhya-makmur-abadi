<?php

namespace App\Filament\Imports;

use App\Models\District;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class DistrictImporter extends Importer
{
    protected static ?string $model = District::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('city')
                ->label('Kode Kabupaten/Kota')
                ->requiredMapping()
                ->relationship(resolveUsing: 'code')
                ->rules(['required']),
            ImportColumn::make('code')
                ->label('Kode Wilayah')
                ->requiredMapping()
                ->guess(['kode'])
                ->rules(['required', 'max:255']),
            ImportColumn::make('name')
                ->requiredMapping()
                ->guess(['nama'])
                ->rules(['required', 'max:255']),
        ];
    }

    public function resolveRecord(): ?District
    {
        // return District::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new District();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your district import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
