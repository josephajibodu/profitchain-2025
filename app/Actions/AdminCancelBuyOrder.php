<?php

namespace App\Actions;

use App\Enums\TradeStatus;
use App\Enums\Permissions;
use App\Models\Order;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminCancelBuyOrder
{
    /**
     * Cancel a buy order and sends the coin back to the seller.
     *
     * @param Order $order
     * @return Order
     * @throws Exception
     */
    public function __invoke(Order $order): Order
    {
        if (! Auth::user()->hasPermissionTo(Permissions::ManageOrders)) {
            throw new Exception("You do not have permission to perform this action.");
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