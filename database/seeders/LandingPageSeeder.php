<?php

namespace Database\Seeders;

use App\Models\Canteen;
use App\Models\Menu;
use App\Models\Review;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LandingPageSeeder extends Seeder
{
    /**
     * Seed dummy data untuk keperluan UI landing page:
     *  - 6 kantin dengan menu
     *  - 4 ulasan customer rating tinggi
     */
    public function run(): void
    {
        // ── Dummy buyer users ──────────────────────────────────────────────────
        $buyers = User::factory(5)->create([
            'role'     => 'buyer',
            'username' => fn () => 'buyer_' . fake()->unique()->numerify('###'),
        ]);

        // ── 6 kantin ───────────────────────────────────────────────────────────
        $openCanteens   = Canteen::factory(3)->open()->create();
        $closedCanteens = Canteen::factory(3)->closed()->create();
        $allCanteens    = $openCanteens->merge($closedCanteens);

        // ── Menu per kantin ────────────────────────────────────────────────────
        foreach ($allCanteens as $canteen) {
            Menu::factory(rand(4, 8))->for($canteen)->create();
        }

        // ── Dummy transactions + reviews ───────────────────────────────────────
        // Review butuh transaction_id (NOT NULL, unique), jadi kita buat dulu transaksinya.
        foreach ($allCanteens->random(4) as $canteen) {
            $buyer = $buyers->random();

            $transaction = Transaction::create([
                'buyer_id'         => $buyer->id,
                'canteen_id'       => $canteen->id,
                'transaction_code' => 'SEED-' . strtoupper(Str::random(8)),
                'status'           => 'done',
                'total_price'      => rand(15000, 80000),
            ]);

            Review::create([
                'transaction_id' => $transaction->id,
                'buyer_id'       => $buyer->id,
                'canteen_id'     => $canteen->id,
                'rating'         => rand(4, 5),
                'comment'        => fake()->randomElement([
                    'Makanannya enak banget, porsinya juga banyak! Pasti balik lagi.',
                    'Pelayanan ramah, makanan cepat tersaji. Recommended!',
                    'Harga terjangkau, rasa ga kalah sama restoran. Top!',
                    'Nasi gorengnya juara, udah jadi langganan sejak setahun lalu.',
                    'Tempatnya bersih, menu bervariasi. Cocok buat makan siang.',
                    'Baksonya enak parah, kuahnya gurih banget. Wajib coba!',
                    'Mantap jiwa! Setiap hari makan di sini ga bosen-bosen.',
                    'Cepet, enak, murah. Trifecta yang jarang ketemu.',
                ]),
            ]);
        }

        $this->command->info('✅  Landing page dummy data seeded!');
        $this->command->line('   6 kantin | menu per kantin | 4 ulasan');
    }
}
