<?php

namespace Database\Factories;

use App\Enums\KYCDocumentType;
use App\Enums\KYCStatus;
use App\Models\Kyc;
use App\Models\KycVerification;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => $firstname = fake()->firstName(),
            'last_name' => $lastname = fake()->lastName(),
            'username' => $username = fake()->userName(),
            'phone_number' => fake()->phoneNumber,
            'whatsapp_number' => fake()->phoneNumber,

            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),

            'bank_name' => fake()->randomElement(['Access Bank', 'Opay', 'Kuda', 'GT Co', 'Moniepoint']),
            'bank_account_name' => "$firstname $lastname",
            'bank_account_number' => Str::padLeft(fake()->randomNumber(8), 10),

            'referral_code' => Str::random(10)
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function configure(): UserFactory
    {
        return $this->afterCreating(function (User $user) {
            Wallet::query()->create(['user_id' => $user->id]);

            KycVerification::query()->create([
                'user_id' => $user->id,
                'document' => '',
                'document_type' => KYCDocumentType::DriversLicense,
                'status' => KYCStatus::Completed
            ]);

            // $user->credit(10, 'Factory bonus from TikyHub');
        });
    }
}
