<?php

namespace App\Actions;

use App\Enums\WithdrawalStatus;
use App\Models\PriceSchedule;
use App\Models\User;
use App\Models\CryptoTrade;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InitiateWithdrawal
{
    /**
     * Calculate withdrawal fee
     */
    protected function calculateFee(float $amount): float
    {
        $feePercentage = getWithdrawalFeePercentage();

        return $amount * $feePercentage;
    }

    /**
     * Initiate a withdrawal
     *
     * @throws Exception
     */
    public function __invoke(
        User  $user,
        float $coinAmount,
    ) {
        return DB::transaction(function () use (
            $user,
            $coinAmount,
        ) {
            // Check user has sufficient balance
            if (! $user->hasSufficientBalance($coinAmount)) {
                throw new Exception('Insufficient balance for the order.');
            }

            $schedule = PriceSchedule::query()->whereDate('date', today())
                ->first();

            if (! $schedule) {
                throw new Exception("Today's rate has not reflected yet. Please try again later.");
            }

            $currentRate = $schedule->price;

            $fiatAmount = $currentRate * $coinAmount;
            $fiatFee = $this->calculateFee($fiatAmount);
            $fiatAmountAfterFee = $fiatAmount - $fiatFee;

            // Ensure amount after fee is positive
            if ($fiatAmountAfterFee <= 0) {
                throw new Exception('Direct sale amount is too low after fee deduction.');
            }

            // Debit user's main wallet
            $user->debit(
                $coinAmount,
                'Direct Sale to Tikyhub'
            );

            $reference = $this->generateUniqueReference();

            return CryptoTrade::query()->create([
                'user_id' => $user->id,
                'reference' => $reference,
                'amount' => (int) ($coinAmount * 100),
                'amount_sent' => $fiatAmount,
                'fee' => $fiatFee,
                'rate' => $currentRate,
                'amount_payable' => $fiatAmountAfterFee,
                'status' => WithdrawalStatus::PENDING,
                'bank_name' => $user->bank_name,
                'bank_account_name' => $user->bank_account_name,
                'bank_account_number' => $user->bank_account_number,
                'metadata' => null,
            ]);
        });
    }

    /**
     * Generate a unique withdrawal reference
     */
    protected function generateUniqueReference(): string
    {
        do {
            $reference = 'WD-'.strtoupper(Str::random(8));
        } while (CryptoTrade::query()->where('reference', $reference)->exists());

        return $reference;
    }
}
