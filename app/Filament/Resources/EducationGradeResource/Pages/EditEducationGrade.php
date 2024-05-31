<?php

namespace App\Filament\Resources\EducationGradeResource\Pages;

use App\Filament\Resources\EducationGradeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEducationGrade extends EditRecord
{
    protected static string $resource = EducationGradeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
