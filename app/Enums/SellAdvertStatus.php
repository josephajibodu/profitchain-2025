<?php

namespace App\Enums;

use App\Traits\HasValues;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum SellAdvertStatus: string implements HasLabel, HasColor
{
    use HasValues;

    case Available = 'available';
    case SoldOut = 'sold-out';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Available => 'Available',
            self::SoldOut => 'Sold Out'
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::Available => 'success',
            self::SoldOut => 'danger'
        };
    }
}