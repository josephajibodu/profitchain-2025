<?php

namespace App\Actions;

use App\Enums\SellAdvertStatus;
use App\Models\SellAdvert;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class UpdateSellOrder
{
    /**
     * Update the sell order.
     *
     * @throws Exception
     */
    public function __invoke(SellAdvert $sellAdvert, array $data): SellAdvert
    {
        return DB::transaction(function () use ($sellAdvert, $data) {
            $user = $sellAdvert->user;

            $extraAmount = $data['top_up'];
            $extraAmountInUnit = $extraAmount * 100;

            $totalAvailable = $sellAdvert->available_balance;
            $totalRemaining = $sellAdvert->remaining_balance;

            if ($extraAmountInUnit > 0 && $data['action'] === 'add') {

                if (! $user->hasSufficientBalance($extraAmount)) {
                    throw new Exception('Insufficient coin balance to allocate to the existing sell order.');
                }

                $user->moveToTrading($extraAmount);

                $totalAvailable = $sellAdvert->available_balance + $extraAmountInUnit;
                $totalRemaining = $sellAdvert->remaining_balance + $extraAmountInUnit;

            } elseif ($extraAmountInUnit > 0 && $data['action'] === 'remove') {

                if ($extraAmountInUnit > $sellAdvert->available_balance) {
                    throw new Exception('You cannot remove more than you have available in your sell order.');
                }

                $user->debitTradingWallet($extraAmount, "Release funds from trading back to Main");

                $totalAvailable = $sellAdvert->available_balance - $extraAmountInUnit;
                $totalRemaining = $sellAdvert->remaining_balance - $extraAmountInUnit;
            }

            $sellAdvert->update([
                'minimum_sell' => $data['min_amount'],
                'max_sell' => $data['max_amount'],
                'terms' => $data['terms'],
                'available_balance' => $totalAvailable,
                'remaining_balance' => $totalRemaining,
            ]);

            return $sellAdvert;
        });
    }
}