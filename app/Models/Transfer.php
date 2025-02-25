<?php

namespace App\Models;

use App\Enums\TransferStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $user_id
 * @property int $recipient_id
 * @property int $amount
 * @property TransferStatus $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property-read User $user
 * @property-read User $recipient
 */
class Transfer extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'status' => TransferStatus::class
        ];
    }

    /**
     * Relationships
     */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }
}