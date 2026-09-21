<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Category> */
class CategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'Training essentials',
            'slug' => 'training-'.Str::uuid(),
            'description' => 'Sample category for development and testing.',
            'status' => false,
            'sort_order' => 1,
        ];
    }
}
