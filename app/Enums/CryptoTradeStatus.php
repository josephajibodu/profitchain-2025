<?php

namespace App\Enums;

use App\Traits\HasValues;
use Filament\Support\Contracts\HasColor;

enum CryptoTradeStatus: string implements HasColor
{
    use HasValues;

    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case REJECTED = 'rejected';

    public function getColor(): string
    {
        return match ($this->value) {
            self::PENDING->value => 'warning',
            self::COMPLETED->value => 'success',
            self::CANCELLED->value, self::REJECTED->value => 'danger',
        };
    }
}