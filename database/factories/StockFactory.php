<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Features\Stock\Enums\StockStatus;
use App\Features\Stock\Models\Stock;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Features\Stock\Models\Stock>
 */
class StockFactory extends Factory
{
    protected $model = Stock::class;

    public function definition(): array
    {
        $quantity = fake()->numberBetween(0, 200);
        $minStock = fake()->numberBetween(5, 20);

        return [
            'user_id'          => User::factory(),
            'name'             => fake()->words(3, true),
            'sku'              => strtoupper(fake()->bothify('??-###-####')),
            'description'      => fake()->paragraph(),
            'quantity'         => $quantity,
            'unit'             => fake()->randomElement(['pcs', 'kg', 'liter', 'box', 'pack']),
            'price'            => fake()->randomFloat(2, 1000, 5000000),
            'min_stock'        => $minStock,
            'status'           => StockStatus::fromQuantity($quantity, $minStock)->value,
            'cover_image_path' => null,
        ];
    }

    public function available(): static
    {
        return $this->state(fn (array $attributes) => [
            'quantity'  => 100,
            'min_stock' => 5,
            'status'    => StockStatus::Available->value,
        ]);
    }

    public function lowStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'quantity'  => 3,
            'min_stock' => 5,
            'status'    => StockStatus::LowStock->value,
        ]);
    }

    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'quantity'  => 0,
            'min_stock' => 5,
            'status'    => StockStatus::OutOfStock->value,
        ]);
    }
}
