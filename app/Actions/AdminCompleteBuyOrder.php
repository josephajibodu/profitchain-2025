<?php

namespace App\Actions;

use App\Enums\TradeStatus;
use App\Enums\Permissions;
use App\Enums\WalletType;
use App\Models\Order;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminCompleteBuyOrder
{
    public function __construct()
    {}

    /**
     * Complete a buy order.
     *
     * @param Order $order
     *
     * @return void
     * @throws Exception
     */
    public function __invoke(Order $order): void
    {
        if (! Auth::user()->hasPermissionTo(Permissions::ManageOrders)) {
            throw new Exception("You do not have permission to perform this action.");
        }

        if (! $order->inPendingState()) {
            throw new Exception("Only pending orders can be marked as successful.");
        }

        DB::transaction(function () use ($order) {
            $sellAdvert = $order->sellAdvert;

            if (!$sellAdvert) {
                throw new Exception("Sell advert associated with this order does not exist.");
            }

            $coinAmount = $order->coin_amount;

            if ($sellAdvert->remaining_balance < $coinAmount) {
                throw new Exception("Insufficient remaining balance in the sell advert.");
            }

            $sellAdvert->user->debitTradingWallet($coinAmount / 100, "[Admin] Sell order completion for order #$order->reference");

            // Update sell advert's remaining balance
            $sellAdvert->decrement('remaining_balance', $coinAmount);

            // Credit the buyer's reserve wallet
            $buyer = $order->buyer;
            $buyer->credit($coinAmount / 100, "[Admin] Buy order completion for order #$order->reference", WalletType::Reserve);

            // Update the order status to successful
            $order->update(['status' => TradeStatus::Completed]);
        });
    }
}