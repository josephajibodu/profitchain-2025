<?php

namespace Database\Seeders;

use App\Actions\CreateSellOrder;
use App\Models\Order;
use App\Models\SellAdvert;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(CreateSellOrder $createSellOrder): void
    {
        // Clear existing data
        Order::query()->delete();
        SellAdvert::query()->delete();

        // Create primary seller
        $seller = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Seller',
            'username' => 'john.seller',
            'email' => 'john.seller@example.com',
        ]);

        $seller->credit(200, 'Pioneer balance');

        // get the current price
        $currentPrice = PriceSchedule::query()->latest('date')->first();

        // Create sell advert for the seller
        $sellAdvert = $createSellOrder($seller, [
            'amount' => 50,
            'rate' => $currentPrice->price,
            'min_amount' => 5_000,
            'max_amount' => 14_500,
            'terms' => 'Standard selling terms',
        ]);

        // Create 3 buyers
        $buyers = User::factory()->count(3)->create();

        // Create buy orders for each buyer
        $buyers->each(function ($buyer) use ($seller, $sellAdvert) {
            $unit = rand(5, 12) * 100;

            Order::query()->create([
                'reference' => Str::uuid(),
                'user_id' => $buyer->id,
                'sell_advert_id' => $sellAdvert->id,
                'coin_amount' => $unit,
                'total_amount' => $sellAdvert->unit_price * $unit,
                'seller_unit_price' => $sellAdvert->unit_price,
                'payment_time_limit' => 12, // 12 minutes
                'status' => OrderStatus::Pending,
                'payment_proof' => null,
            ]);

            $sellAdvert->decrement('available_balance', $unit);
        });

        // Create 40 additional users and their sell adverts
        $users = User::factory()->count(40)->create();

        foreach ($users as $user) {
            // Credit balance for each user
            $user->credit(rand(500, 1500), 'Initial balance');

            // Create a sell advert for each user
            $createSellOrder($user, [
                'amount' => rand(10, 100),
                'rate' => $currentPrice->price,
                'min_amount' => rand(1_900, 8_000),
                'max_amount' => rand(50_000, 125_000),
                'terms' => 'Generated selling terms',
            ]);
        }
    }
}
