<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
        ]);

        $admin = User::factory()->create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'username' => 'username',
            'email' => 'test@example.com',
        ]);

        $admin->assignRole('Super Admin');

        $this->call([
//            OrderSeeder::class,
//            TransferSeeder::class,
//            WithdrawalSeeder::class
        ]);
    }
}
