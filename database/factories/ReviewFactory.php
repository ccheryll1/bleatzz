<?php

namespace Database\Factories;

use App\Models\Review;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Review>
 */
class ReviewFactory extends Factory
{
    protected $model = Review::class;

    private static array $comments = [
        'Makanannya enak banget, porsinya juga banyak! Pasti balik lagi.',
        'Pelayanan ramah, makanan cepat tersaji. Recommended!',
        'Harga terjangkau, rasa ga kalah sama restoran. Top!',
        'Nasi gorengnya juara, udah jadi langganan sejak setahun lalu.',
        'Tempatnya bersih, menu bervariasi. Cocok buat makan siang.',
        'Baksonya enak parah, kuahnya gurih banget. Wajib coba!',
        'Mantap jiwa! Setiap hari makan di sini ga bosen-bosen.',
        'Cepet, enak, murah. Trifecta yang jarang ketemu.',
    ];

    public function definition(): array
    {
        return [
            'transaction_id' => null,
            'buyer_id'       => null, // diisi di seeder
            'canteen_id'     => null, // diisi di seeder
            'rating'         => fake()->numberBetween(4, 5),
            'comment'        => fake()->randomElement(self::$comments),
        ];
    }
}
