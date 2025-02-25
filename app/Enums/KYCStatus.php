<?php

namespace App\Enums;

use App\Traits\HasValues;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum KYCStatus: string implements HasLabel, HasColor
{
    use HasValues;

    case Pending = 'pending';
    case Rejected = 'rejected';
    case Completed = 'completed';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Rejected => 'danger',
            self::Completed => 'success',
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Rejected => 'Rejected',
            self::Completed => 'Completed',
        };
    }
}