<?php

namespace App\Models;

use App\Enums\BinaryStatus;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property string $name
 * @property integer $margin
 * @property integer $trade_duration
 * @property integer $daily_capacity
 * @property integer $current_capacity
 * @property BinaryStatus $status
 */
class CurrencyPair extends Pivot
{
    protected $guarded = ['id'];
}
