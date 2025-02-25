<?php

namespace App\Enums;

use App\Traits\HasValues;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ReferralStatus: string implements HasLabel, HasColor
{
    use HasValues;

    case Pending = 'pending';
    case Completed = 'completed';
    case Invalidated = 'invalidated';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Completed => 'Completed',
            self::Invalidated => 'Banned',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Completed => 'success',
            self::Invalidated => 'danger',
        };
    }
}
