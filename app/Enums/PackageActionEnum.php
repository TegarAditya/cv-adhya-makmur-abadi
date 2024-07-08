<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PackageActionEnum: string implements HasLabel
{
    case ADDITION = 'addition';
    case REDUCTION = 'reduction';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ADDITION => 'Penambahan',
            self::REDUCTION => 'Pengurangan',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::ADDITION => 'success',
            self::REDUCTION => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::ADDITION => 'heroicon-o-plus',
            self::REDUCTION => 'heroicon-o-minus',
        };
    }
}
