<?php

namespace App\Enums;

use App\Traits\HasValues;

enum OrderDisputeStatus: string
{
    use HasValues;

    case Open = 'open';
    case Resolved = 'resolved';
}