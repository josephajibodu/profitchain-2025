<?php

namespace App\Actions;

use App\Enums\SellAdvertStatus;
use App\Models\SellAdvert;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class CreateSellOrder
{
    /**
     * Create a new sell order.
     *
     * @throws Exception
     */
    public function __invoke(User $user, array $data)
    {
        if (! $user->kyc?->isCompleted()) {
            throw new Exception('Your identity must be verified before you can create a sell order.');
        }

        return DB::transaction(function () use ($user, $data) {
            $amountToSell = floatval($data['amount']);

            if ($user->balance < $amountToSell) {
                throw new Exception('Insufficient coin balance.');
            }

            // Move funds to trading balance
            $user->moveToTrading($amountToSell);

            return SellAdvert::query()->create([
                'user_id' => $user->id,
                'unit_price' => 1, // general price is used
                'minimum_sell' => $data['min_amount'],
                'max_sell' => $data['max_amount'],
                'bank_name' => $user->bank_name,
                'bank_account_name' => $user->bank_account_name,
                'bank_account_number' => $user->bank_account_number,
                'terms' => $data['terms'],
                'available_balance' => $amountToSell * 100,
                'remaining_balance' => $amountToSell * 100,
                'status' => SellAdvertStatus::Available,
            ]);
        });
    }
}