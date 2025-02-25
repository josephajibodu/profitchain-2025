<?php

namespace App\Enums;

use App\Traits\HasValues;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum TradeStatus: string implements HasLabel, HasColor
{
    use HasValues;

    case Trading = 'trading';
    case Completed = 'completed';
    case PaidOut = 'paid-out';
    case Cancelled = 'cancelled';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Trading => 'Trade In Progress',
            self::Completed => 'Completed',
            self::PaidOut => 'ROI Paid Out',
            self::Cancelled => 'Cancelled',
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::Trading => 'warning',
            self::Completed, self::PaidOut => 'success',
            self::Cancelled => 'danger'
        };
    }
}