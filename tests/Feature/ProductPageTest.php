<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductPageTest extends TestCase
{
    use RefreshDatabase;

    public function createApplication()
    {
        $app = parent::createApplication();
        if ($app['config']->get('database.default') !== 'sqlite' || $app['config']->get('database.connections.sqlite.database') !== ':memory:') {
            throw new \RuntimeException('Product page tests require an isolated SQLite in-memory database.');
        }

        return $app;
    }

    public function test_real_details_and_ordered_gallery_are_rendered_for_any_slug(): void
    {
        Storage::fake('public');
        $category = Category::factory()->create(['status' => true]);
        $product = Product::factory()->for($category)->active()->create(['slug' => 'custom-new-product', 'name' => 'Custom Oak Wall', 'price' => 24500, 'currency' => 'BDT', 'stock' => 0, 'dimensions' => '200 × 80 cm', 'material' => 'Solid oak']);
        foreach (['second', 'first'] as $index => $name) {
            Storage::disk('public')->put('products/'.$name.'.jpg', 'image');
            ProductImage::factory()->for($product)->create(['image_path' => 'products/'.$name.'.jpg', 'sort_order' => 1 - $index, 'alt_text' => $name.' view']);
        }
        $response = $this->get(route('shop.show', $product->slug))->assertOk()->assertSee('Custom Oak Wall')->assertSee('BDT 24,500.00')->assertSee('Out of stock')->assertSee('200 × 80 cm')->assertSee('Solid oak')->assertSee('first view')->assertSee('Image 1 of 2')->assertDontSee('concept image');
        $this->assertSame('products/first.jpg', $response->viewData('gallery')->first()->image_path);
        $this->assertTrue($response->viewData('product')->relationLoaded('category'));
        $this->assertTrue($response->viewData('product')->relationLoaded('images'));
        $this->get(route('shop.index'))->assertSee(route('shop.show', $product->slug), false);
    }

    public function test_unpublished_or_missing_products_are_not_accessible(): void
    {
        $category = Category::factory()->create(['status' => true]);
        $draft = Product::factory()->for($category)->create(['status' => 'draft']);
        $hidden = Product::factory()->active()->create();
        foreach ([$draft->slug, $hidden->slug, 'does-not-exist', (string) $draft->id] as $slug) {
            $this->get(route('shop.show', $slug))->assertNotFound();
        }
        $this->get('/product')->assertRedirect('/shop');
    }

    public function test_missing_files_and_optional_fields_have_fallbacks(): void
    {
        Storage::fake('public');
        $category = Category::factory()->create(['status' => true]);
        $product = Product::factory()->for($category)->active()->create(['description' => null, 'price' => null, 'currency' => null, 'stock' => null, 'dimensions' => null, 'material' => null]);
        ProductImage::factory()->for($product)->create(['image_path' => 'products/missing.jpg']);
        $this->get(route('shop.show', $product->slug))->assertOk()->assertSee('Image coming soon')->assertSee('Price unavailable')->assertSee('Availability not confirmed')->assertSee('Not specified')->assertDontSee('products/missing.jpg')->assertDontSee('data-open-zoom', false)->assertDontSee('More ways to move.');
        $product->update(['price' => 0, 'currency' => 'BDT', 'stock' => 4, 'description' => '<script>alert(1)</script>']);
        $this->get(route('shop.show', $product->slug))->assertOk()->assertSee('BDT 0.00')->assertSee('In stock')->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_related_products_are_visible_limited_and_prefer_same_category(): void
    {
        Storage::fake('public');
        $category = Category::factory()->create(['status' => true]);
        $product = Product::factory()->for($category)->active()->create();
        $same = Product::factory()->for($category)->active()->create();
        $otherCategory = Category::factory()->create(['status' => true]);
        Product::factory()->count(3)->for($otherCategory)->active()->create();
        $draft = Product::factory()->for($category)->create(['status' => 'draft']);
        $hidden = Product::factory()->active()->create();
        $response = $this->get(route('shop.show', $product->slug))->assertOk();
        $related = $response->viewData('relatedProducts');
        $this->assertCount(2, $related);
        $this->assertSame($same->id, $related->first()->id);
        $this->assertEmpty($related->whereIn('id', [$product->id, $draft->id, $hidden->id]));
        foreach ($related as $item) {
            $this->assertTrue($item->relationLoaded('category'));
            $this->assertTrue($item->relationLoaded('images'));
        }
    }
}
