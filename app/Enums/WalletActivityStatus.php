<?php

namespace App\Enums;

use App\Traits\HasValues;

enum WalletActivityStatus: string
{
    use HasValues;

    case Pending = 'pending';
    case Committed = 'committed';
}