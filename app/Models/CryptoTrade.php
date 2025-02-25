<?php

namespace App\Models;

use App\Enums\CryptoTradeStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\Builder;

/**
 * @property int $id
 * @property int $user_id
 * @property string $reference
 * @property float $amount
 * @property float $amount_sent // Total Amount in Naira
 * @property float $fee
 * @property CryptoTradeStatus $status
 * @property string $network
 * @property string $wallet_address
 * @property string $comment
 * @property string $metadata
 *
 * @property Carbon $created_at Timestamp when the withdrawal was created
 * @property Carbon $updated_at Timestamp when the withdrawal was last updated
 *
 * @property-read User $user
 */
class CryptoTrade extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'status' => CryptoTradeStatus::class,
            'metadata' => 'array',
        ];
    }

    public function isPending(): bool
    {
        return $this->status === CryptoTradeStatus::PENDING;
    }

    /**
     * Relationships
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Scopes */
    public function scopePending(Builder $query)
    {
        $query->where('status', CryptoTradeStatus::PENDING);
    }

    public function scopeCompleted(Builder $query)
    {
        $query->where('status', CryptoTradeStatus::COMPLETED);
    }
    /** End of Scopes */
}