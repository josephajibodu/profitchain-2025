<?php

namespace App\Models;

use App\Enums\ReferralStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $referred_user_id
 * @property int $amount
 * @property ReferralStatus $status
 *
 * @property-read User $user
 * @property-read User $referredUser
 */
class Referral extends Model
{
    /** @use HasFactory<\Database\Factories\ReferralFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'status' => ReferralStatus::class
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function referredUser(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
