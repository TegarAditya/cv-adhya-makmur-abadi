<?php

namespace App\Filament\Imports;

use App\Models\Subdistrict;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class SubdistrictImporter extends Importer
{
    protected static ?string $model = Subdistrict::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('district')
                ->label('Kode Kecamatan')
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
            ImportColumn::make('postal_code')
                ->label('Kode Pos')
                ->requiredMapping()
                ->guess(['kode_pos'])
                ->rules(['required', 'max:255']),
        ];
    }

    public function resolveRecord(): ?Subdistrict
    {
        // return Subdistrict::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new Subdistrict();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your subdistrict import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
