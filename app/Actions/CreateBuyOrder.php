<?php

namespace App\Actions;
namespace App\Actions;

use App\Enums\TradeStatus;
use App\Enums\SellAdvertStatus;
use App\Models\Order;
use App\Models\PriceSchedule;
use App\Models\SellAdvert;
use App\Models\User;
use App\Settings\GeneralSetting;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateBuyOrder
{
    public function __construct(public GeneralSetting $generalSetting)
    {}

    /**
     * Create a new buy order.
     *
     * @param User $user
     * @param int $sellAdvertId
     * @param float $amount
     * @return mixed
     * @throws Exception
     */
    public function __invoke(User $user, int $sellAdvertId, float $amount): Order
    {
        if (! $user->kyc?->isCompleted()) {
            throw new Exception('Your identity must be verified before you can create buy Tiky.');
        }

        // check if the user already has a buy order
        $buyOrder = $user->buyOrders()->scopes('orderStillOn')->latest()->first();

        if ($buyOrder) {
            throw new Exception("Please complete your pending order before proceeding with another order.");
        }

        $schedule = PriceSchedule::query()->whereDate('date', today())
            ->first();

        $currentRate = $schedule->price;

        $sellAdvert = SellAdvert::query()->findOrFail($sellAdvertId);

        if ($user->id === $sellAdvert->user->id) {
            throw new Exception("Invalid buy order");
        }

        $amountToPay = $amount * $currentRate;

        // Check if the amount is within the allowed range
        if ($amountToPay < $sellAdvert->minimum_sell) {
            throw new Exception("Amount is less than the minimum amount allowed for this sell order.");
        }

        if ($amountToPay > $sellAdvert->max_sell) {
            throw new Exception("Amount exceeds the maximum amount allowed for this sell order.");
        }

        if (($amount * 100) > $sellAdvert->available_balance) {
            throw new Exception("Amount exceeds the available tiky for this sell order.");
        }

        return DB::transaction(function () use ($currentRate, $amountToPay, $user, $sellAdvert, $amount) {
            // Create the order
            $order = Order::query()->create([
                'reference' => Str::uuid(),
                'user_id' => $user->id,
                'sell_advert_id' => $sellAdvert->id,
                'coin_amount' => $coinAmt = $amount * 100,
                'total_amount' => $amountToPay,
                'seller_unit_price' => $currentRate,
                'payment_time_limit' => $this->generalSetting->order_time_limit,
                'status' => TradeStatus::Pending,
            ]);

            // Reduce the available balance of the sell advert
            $sellAdvert->decrement('available_balance', $coinAmt);

            if ($sellAdvert->refresh()->available_balance < 0) {
                throw new Exception("Amount exceeds the available tiky for this sell order.");
            }

            return $order;
        });
    }
}