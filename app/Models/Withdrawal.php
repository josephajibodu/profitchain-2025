<?php

namespace App\Models;

use App\Enums\WithdrawalStatus;
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
 * @property float $amount_payable // Amount Payable in Naira (Total Amount - Fee)
 * @property float $fee
 * @property float $rate
 * @property WithdrawalStatus $status
 * @property string $bank_name
 * @property string $bank_account_name
 * @property string $bank_account_number
 * @property string $comment
 * @property string $metadata
 *
 * @property Carbon $created_at Timestamp when the withdrawal was created
 * @property Carbon $updated_at Timestamp when the withdrawal was last updated
 *
 * @property-read User $user
 */
class Withdrawal extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'status' => WithdrawalStatus::class,
            'metadata' => 'array',
        ];
    }

    public function isPending(): bool
    {
        return $this->status === WithdrawalStatus::PENDING;
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
        $query->where('status', WithdrawalStatus::PENDING);
    }

    public function scopeCompleted(Builder $query)
    {
        $query->where('status', WithdrawalStatus::COMPLETED);
    }
    /** End of Scopes */
}