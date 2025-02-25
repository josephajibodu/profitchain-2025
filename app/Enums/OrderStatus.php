<?php

namespace App\Enums;

use App\Traits\HasValues;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum OrderStatus: string implements HasLabel, HasColor
{
    use HasValues;

    case Pending = 'pending';
    case Paid = 'paid';
    case PaymentNotReceived = 'payment-not-received';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case Disputed = 'disputed';


    public function getLabel(): ?string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Paid => 'Payment sent',
            self::PaymentNotReceived => 'Payment not received',
            self::Completed => 'Completed',
            self::Cancelled => 'Cancelled',
            self::Disputed => 'Disputed'
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Paid, self::PaymentNotReceived => 'primary',
            self::Completed => 'success',
            self::Cancelled, self::Disputed => 'danger'
        };
    }
}