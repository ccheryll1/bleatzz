<?php

namespace Database\Factories;

use App\Models\Canteen;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Canteen>
 */
class CanteenFactory extends Factory
{
    protected $model = Canteen::class;

    private static array $names = [
        'Kantin Barokah',
        'Warung Pak Haji',
        'Dapur Bu Siti',
        'Kantin Sejahtera',
        'Makan Siang Express',
        'Depot Nusantara',
    ];

    private static int $nameIndex = 0;

    public function definition(): array
    {
        $name = self::$names[self::$nameIndex % count(self::$names)];
        self::$nameIndex++;

        return [
            'canteen_name'       => $name,
            'description'        => fake()->sentence(12),
            'photo'              => null,
            'is_open'            => fake()->boolean(70),
            'estimated_time_min' => fake()->numberBetween(10, 30),
        ];
    }

    public function open(): static
    {
        return $this->state(['is_open' => true]);
    }

    public function closed(): static
    {
        return $this->state(['is_open' => false]);
    }
}
