<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Database\Seeders\ProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductSeederTest extends TestCase
{
    use RefreshDatabase;

    public function createApplication()
    {
        $app = parent::createApplication();
        if ($app['config']->get('database.default') !== 'sqlite' || $app['config']->get('database.connections.sqlite.database') !== ':memory:') {
            throw new \RuntimeException('Seeder tests require an isolated SQLite in-memory database.');
        }

        return $app;
    }

    public function test_factories_support_relationships_and_states(): void
    {
        $category = Category::factory()->create();
        $products = Product::factory()->count(3)->for($category)->active()->outOfStock()->create();
        $this->assertDatabaseCount('categories', 1);
        $this->assertSame(3, $products->pluck('slug')->unique()->count());
        $this->assertSame(3, $products->pluck('sku')->unique()->count());
        foreach ($products as $product) {
            $this->assertSame('active', $product->status);
            $this->assertSame(0, $product->stock);
            $this->assertSame('BDT', $product->currency);
        }
        $image = ProductImage::factory()->for($products->first())->create();
        $this->assertTrue($image->product->is($products->first()));
        $this->assertDatabaseCount('products', 3);
        $standalone = ProductImage::factory()->create();
        $this->assertNotNull($standalone->product->category);
    }

    public function test_seeder_creates_real_images_and_preserves_existing_edits(): void
    {
        Storage::fake('public');
        $this->seed(ProductSeeder::class);
        $this->assertDatabaseCount('categories', 3);
        $this->assertDatabaseCount('products', 3);
        $this->assertDatabaseCount('product_images', 4);
        foreach (ProductImage::all() as $image) {
            Storage::disk('public')->assertExists($image->image_path);
        }
        $wall = Product::where('slug', 'swedish-wall')->firstOrFail();
        $this->assertSame('swedish-walls', $wall->category->slug);
        $this->assertSame('18500.00', $wall->price);
        $this->assertSame('draft', $wall->status);
        $this->assertSame([0, 1], $wall->images->pluck('sort_order')->all());
        $wall->update(['name' => 'Edited wall', 'price' => '20000.00']);
        $wall->images->first()->update(['alt_text' => 'Edited image description']);
        $this->seed(ProductSeeder::class);
        $this->assertDatabaseCount('products', 3);
        $this->assertDatabaseCount('product_images', 4);
        $this->assertSame('Edited wall', $wall->fresh()->name);
        $this->assertSame('20000.00', $wall->fresh()->price);
        $this->assertSame('Edited image description', $wall->images()->first()->alt_text);
    }
}
