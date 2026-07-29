<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Manager Test Account
        User::firstOrCreate(
            ['email' => 'manager@bleatz.test'],
            [
                'name'              => 'Manager Test',
                'username'          => 'manager_test',
                'password'          => Hash::make('password123'),
                'role'              => 'manager',
                'is_active'         => true,
                'email_verified_at' => now(),
            ]
        );

        // Seller Test Account
        User::firstOrCreate(
            ['email' => 'seller@bleatz.test'],
            [
                'name'              => 'Seller Test',
                'username'          => 'seller_test',
                'password'          => Hash::make('password123'),
                'role'              => 'seller',
                'is_active'         => true,
                'email_verified_at' => now(),
            ]
        );

        // Buyer Test Account (untuk comparison)
        User::firstOrCreate(
            ['email' => 'buyer@bleatz.test'],
            [
                'name'              => 'Buyer Test',
                'username'          => 'buyer_test',
                'password'          => Hash::make('password123'),
                'role'              => 'buyer',
                'is_active'         => true,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Test accounts created successfully!');
        $this->command->info('');
        $this->command->table(
            ['Email', 'Username', 'Password', 'Role'],
            [
                ['manager@bleatz.test', 'manager_test', 'password123', 'manager'],
                ['seller@bleatz.test', 'seller_test', 'password123', 'seller'],
                ['buyer@bleatz.test', 'buyer_test', 'password123', 'buyer'],
            ]
        );
    }
}
