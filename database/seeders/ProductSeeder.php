<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(CategorySeeder::class);

        $samples = [
            ['name' => 'Swedish Wall', 'slug' => 'swedish-wall', 'sku' => 'WG-WALL-001', 'category' => 'swedish-walls', 'price' => '18500.00', 'stock' => 12, 'images' => ['images/hero/wallgym-interior-640.jpg', 'images/living/wallgym-at-home-640.jpg']],
            ['name' => 'Gymnastic Rings', 'slug' => 'gymnastic-rings', 'sku' => 'WG-RING-001', 'category' => 'accessories', 'price' => '2400.00', 'stock' => 25, 'images' => ['images/collection/gymnastic-rings-480.jpg']],
            ['name' => 'Exercise Mat', 'slug' => 'exercise-mat', 'sku' => 'WG-MAT-001', 'category' => 'mats', 'price' => '1800.00', 'stock' => 20, 'images' => ['images/collection/exercise-mat-480.jpg']],
        ];

        DB::transaction(function () use ($samples) {
            foreach ($samples as $sample) {
                // Rerunning this seeder never replaces a product or gallery edited by an admin.
                if (Product::where('slug', $sample['slug'])->exists()) {
                    continue;
                }
                $product = Product::factory()->create([
                    'category_id' => Category::where('slug', $sample['category'])->firstOrFail()->id,
                    'name' => $sample['name'],
                    'slug' => $sample['slug'],
                    'sku' => $sample['sku'],
                    'price' => $sample['price'],
                    'stock' => $sample['stock'],
                ]);

                foreach ($sample['images'] as $order => $source) {
                    $path = 'products/seed-'.$sample['slug'].'-'.$order.'.jpg';
                    if (! Storage::disk('public')->exists($path)) {
                        $contents = file_get_contents(public_path($source));
                        if ($contents === false || ! Storage::disk('public')->put($path, $contents)) {
                            throw new RuntimeException('Unable to store seed image: '.$source);
                        }
                    }
                    $product->images()->create([
                        'image_path' => $path,
                        'alt_text' => $sample['name'].' concept image',
                        'sort_order' => $order,
                    ]);
                }
            }
        });
    }
}
