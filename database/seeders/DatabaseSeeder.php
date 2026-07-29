<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Akun default untuk testing login
        User::factory()->create([
            'name'     => 'Test User',
            'username' => 'testuser',
            'email'    => 'test@example.com',
            'role'     => 'buyer',
        ]);

        // Test accounts untuk manager, seller, buyer
        $this->call([
            TestAccountSeeder::class,
        ]);

        // Dummy data untuk UI landing page
        $this->call([
            LandingPageSeeder::class,
        ]);
    }
}
