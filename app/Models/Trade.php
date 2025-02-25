<?php

namespace App\Models;

use App\Enums\TradeStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\Builder;

/**
 * @property int $id
 * @property string $reference
 * @property int $user_id
 * @property int|null $currency_pair_id
 * @property string $currency_pair_name
 * @property int $capital
 * @property int $total_roi
 * @property int $margin_applied
 * @property int $payment_time_limit
 * @property TradeStatus $status
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property-read User $user
 * @property-read CurrencyPair|null $currencyPair
 */
class Trade extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'status' => TradeStatus::class,
        ];
    }

    public function getTimeLeft(): int
    {
        $tradeExpiry = $this->created_at->addSeconds($this->payment_time_limit);
        $timeLeft = now()->diffInSeconds($tradeExpiry, false);

        return max(0, $timeLeft);
    }

    public function isPending(): bool
    {
        return $this->status === TradeStatus::Trading;
    }

    public function isCompleted(): bool
    {
        return $this->status === TradeStatus::Completed;
    }

    public function isPaidOut(): bool
    {
        return $this->status === TradeStatus::PaidOut;
    }

    public function isCancelled(): bool
    {
        return $this->status === TradeStatus::Cancelled;
    }

    public function scopeActive(Builder $query)
    {
        $query->where('status', 'Trading');
    }

    public function scopeEnded(Builder $query)
    {
        $query->whereIn('status', ['Completed', 'Cancelled']);
    }

    /**
     * Relationships
     */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function currencyPair(): BelongsTo
    {
        return $this->belongsTo(CurrencyPair::class);
    }
}