<?php

namespace App\Filament\Imports;

use App\Models\School;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class SchoolImporter extends Importer
{
    protected static ?string $model = School::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->label('Nama')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('is_active')
                ->label('Tampil')
                ->requiredMapping()
                ->rules(['required'])
                ->boolean(),
        ];
    }

    public function resolveRecord(): ?School
    {
        // return School::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'name' => $this->data['name'],
        // ]);

        return new School();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your school import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
