<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<ProductImage> */
class ProductImageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            // Test metadata only. ProductSeeder supplies real local image files.
            'image_path' => 'products/factory-'.Str::uuid().'.jpg',
            'alt_text' => 'Sample product image',
            'sort_order' => 0,
        ];
    }
}
