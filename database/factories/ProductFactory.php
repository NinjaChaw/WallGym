<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Product> */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->randomElement(['Swedish Wall', 'Gymnastic Rings', 'Exercise Mat']);

        return [
            'category_id' => Category::factory(),
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::uuid(),
            'sku' => 'WG-'.Str::upper((string) Str::uuid()),
            'description' => 'Sample product for development and testing. Prices and stock are illustrative.',
            'price' => fake()->numberBetween(50000, 2500000) / 100,
            'currency' => 'BDT',
            'stock' => fake()->numberBetween(1, 50),
            'status' => 'draft',
            'dimensions' => null,
            'material' => null,
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => ['status' => 'active']);
    }

    public function outOfStock(): static
    {
        return $this->state(fn () => ['stock' => 0]);
    }
}
