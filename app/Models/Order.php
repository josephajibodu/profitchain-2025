<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\TradeStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $reference
 * @property int $user_id
 * @property int $sell_advert_id
 * @property int $coin_amount
 * @property int $total_amount
 * @property int $seller_unit_price
 * @property int $payment_time_limit
 * @property TradeStatus $status
 * @property string $payment_proof
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property-read User $buyer
 * @property-read SellAdvert $sellAdvert
 */
class Order extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'status' => TradeStatus::class
        ];
    }

    public function getTimeLeft(): int
    {
        $orderExpiry = $this->created_at->addMinutes($this->payment_time_limit);
        $timeLeft = now()->diffInSeconds($orderExpiry, false);

        return max(0, $timeLeft);
    }

    public function inPendingState(): bool
    {
        return $this->status === OrderStatus::Pending
            || $this->status === OrderStatus::PaymentNotReceived
            || $this->status === OrderStatus::Paid;
    }

    public function inCompletedState(): bool
    {
        return $this->status === OrderStatus::Cancelled
            || $this->status === OrderStatus::Completed;
    }

    public function isPending(): bool
    {
        return $this->status === OrderStatus::Pending;
    }

    public function isPaid(): bool
    {
        return $this->status === OrderStatus::Paid;
    }

    public function scopeOrderStillOn(Builder $query)
    {
        $query->where(function (Builder $q) {
            $q->where('status', OrderStatus::Pending)
                ->orWhere('status', OrderStatus::Paid);
        });
    }

    public function scopeEnded(Builder $query)
    {
        $query->where(function (Builder $q) {
            $q->where('status', OrderStatus::Completed)
                ->orWhere('status', OrderStatus::Cancelled);
        });
    }

    /**
     * Relationships
     */

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sellAdvert(): BelongsTo
    {
        return $this->belongsTo(SellAdvert::class);
    }
}