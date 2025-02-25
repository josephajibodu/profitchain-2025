<?php

namespace App\Actions;

use App\Enums\TradeStatus;
use App\Models\Order;
use App\Models\SellAdvert;
use Exception;
use Illuminate\Support\Facades\DB;

class CancelBuyOrder
{
    /**
     * Cancel a buy order and reverse its effects.
     *
     * @param Order $order
     * @return Order
     * @throws Exception
     */
    public function __invoke(Order $order): Order
    {
        // Ensure the order is still cancellable
        if ($order->status !== TradeStatus::Pending) {
            throw new Exception("Order cannot be cancelled as it is not in a pending state.");
        }

        // Retrieve the related sell advert
        $sellAdvert = $order->sellAdvert;

        return DB::transaction(function () use ($order, $sellAdvert) {
            // Reverse the deducted balance
            $sellAdvert->increment('available_balance', $order->coin_amount);

            // Update the order status to 'Cancelled'
            $order->update([
                'status' => TradeStatus::Cancelled,
            ]);

            return $order;
        });
    }
}