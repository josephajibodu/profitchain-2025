<?php

namespace App\Models;

use App\Enums\OrderDisputeStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $order_id
 * @property string $description
 * @property array<string> $proofs
 * @property OrderDisputeStatus $status
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class OrderDispute extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'status' => OrderDisputeStatus::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}