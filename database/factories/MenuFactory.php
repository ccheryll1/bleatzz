<?php

namespace Database\Factories;

use App\Models\Menu;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Menu>
 */
class MenuFactory extends Factory
{
    protected $model = Menu::class;

    private static array $foodNames = [
        'Nasi Goreng Spesial',
        'Ayam Bakar Madu',
        'Mie Goreng Jawa',
        'Nasi Uduk Komplit',
        'Soto Ayam',
        'Gado-Gado',
        'Nasi Padang',
        'Bakso Urat',
        'Nasi Rawon',
        'Pecel Lele',
    ];

    private static array $drinkNames = [
        'Es Teh Manis',
        'Jus Alpukat',
        'Es Jeruk Segar',
        'Kopi Susu Gula Aren',
        'Teh Tarik',
    ];

    public function definition(): array
    {
        $category = fake()->randomElement(['food', 'food', 'food', 'drink', 'snack']);

        $name = match ($category) {
            'drink' => fake()->randomElement(self::$drinkNames),
            default => fake()->randomElement(self::$foodNames),
        };

        return [
            'name'         => $name,
            'description'  => fake()->sentence(10),
            'price'        => fake()->randomElement([8000, 10000, 12000, 15000, 18000, 20000, 25000]),
            'photo'        => null,
            'category'     => $category,
            'stock_type'   => 'available',
            'stock_qty'    => null,
            'is_available' => true,
        ];
    }

    public function food(): static
    {
        return $this->state([
            'category' => 'food',
            'name'     => fake()->randomElement(self::$foodNames),
        ]);
    }

    public function drink(): static
    {
        return $this->state([
            'category' => 'drink',
            'name'     => fake()->randomElement(self::$drinkNames),
        ]);
    }
}
