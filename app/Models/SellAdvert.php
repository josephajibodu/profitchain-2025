<?php

namespace App\Models;

use App\Enums\SellAdvertStatus;
use App\Enums\TransferStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $user_id
 * @property int $available_balance
 * @property int $remaining_balance
 * @property int $unit_price
 * @property int $minimum_sell
 * @property int $max_sell
 * @property SellAdvertStatus $status
 * @property boolean $is_published
 * @property string $bank_name
 * @property string $bank_account_name
 * @property string $bank_account_number
 * @property string $terms
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property-read User $user
 */
class SellAdvert extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'status' => SellAdvertStatus::class
        ];
    }

    /**
     * Relationships
     */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}