<?php

namespace App\Enums;

use App\Traits\HasValues;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum AccountStatus: string implements HasLabel, HasColor
{
    use HasValues;

    case Active = 'active';
    case Banned = 'banned';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Active => 'Active',
            self::Banned => 'Banned',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Active => 'success',
            self::Banned => 'danger',
        };
    }
}