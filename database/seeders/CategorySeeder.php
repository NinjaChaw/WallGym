<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            [
                'name' => 'Swedish walls', 
                'slug' => 'swedish-walls', 
                'description' => 'Make room for everyday strength, stretching, and play.', 
                'status' => true, 
                'sort_order' => 1
            ],
            [
                'name' => 'Accessories', 
                'slug' => 'accessories', 
                'description' => 'Thoughtful additions for a little more movement at home.', 
                'status' => true, 
                'sort_order' => 2
            ],
            [
                'name' => 'Mats', 
                'slug' => 'mats', 
                'description' => 'Create a comfortable place for stretching and floor exercises.', 
                'status' => false, 
                'sort_order' => 3
            ],
        ] as $data) {
            // Preserve store-owner edits when the seeder is run again.
            Category::firstOrCreate(['slug' => $data['slug']], $data);
        }
    }
}
