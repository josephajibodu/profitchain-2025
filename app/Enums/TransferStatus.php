<?php

namespace App\Enums;

use App\Traits\HasValues;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum TransferStatus: string implements HasColor, HasLabel
{
    use HasValues;

    case Pending = 'pending';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case Refunded = 'refunded';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Cancelled => 'danger',
            self::Refunded => 'primary',
            self::Completed => 'success',
        };
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Cancelled => 'Cancelled',
            self::Refunded => 'Refunded',
            self::Completed => 'Completed',
        };
    }
}