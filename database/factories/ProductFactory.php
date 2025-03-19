<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code'          => $this->faker->unique()->numberBetween(10000000, 99999999),
            'product_name'  => $this->faker->word,
            'quantity'      => $this->faker->randomNumber(2) . ' g',
            'brands'        => $this->faker->company,
            'categories'    => 'Snacks, Baked Goods',
            'status'        => 'published',
            'imported_t'    => now(),
        ];
    }
}
