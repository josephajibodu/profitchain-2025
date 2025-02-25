<?php

namespace App\Actions;

use App\Enums\TransferStatus;
use App\Enums\WalletType;
use App\Enums\WithdrawalStatus;
use App\Events\DonationSent;
use App\Models\Donation;
use App\Models\Transfer;
use App\Models\User;
use App\Models\CryptoTrade;
use App\Settings\GeneralSetting;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DonateCoin
{
    public function __construct(public GeneralSetting $generalSetting)
    {}

    /**
     * Donate coin
     *
     * @throws Exception
     */
    public function __invoke(User $user, float $amount)
    {
        return DB::transaction(function () use (
            $user,
            $amount,
        ) {
            // Check user has sufficient balance
            if (!$user->hasSufficientBalance($amount)) {
                throw new Exception('Insufficient balance.');
            }

            // Debit user's main wallet
            $user->debit(
                $amount,
                "Donation of $amount to the platform."
            );

            // Get the donation account username
            $donationUsername = $this->generalSetting->donation_username;

            // Find the recipient
            $donationAccount = User::query()->where('username', $donationUsername)->first();

            // Validate recipient exists
            if (!$donationAccount) {
                throw new Exception('Donation failed: destination could not receive the donation');
            }

            // Prevent self-transfer
            if ($user->id === $donationAccount->id) {
                throw new Exception('You cannot donate to this account.');
            }

            $donationAccount->credit(
                $amount,
                "Donation of $amount from {$user->username}"
            );

            $donation = Donation::query()->create([
                'user_id' => $user->id,
                'amount' => (int)($amount * 100),
                'donation_account' => $donationUsername
            ]);

            DonationSent::dispatch($user->id, $amount);

            return $donation;
        });
    }

}