<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ShopTest extends TestCase
{
    use RefreshDatabase;

    public function createApplication()
    {
        $app = parent::createApplication();
        if ($app['config']->get('database.default') !== 'sqlite' || $app['config']->get('database.connections.sqlite.database') !== ':memory:') {
            throw new \RuntimeException('Shop tests require an isolated SQLite in-memory database.');
        }

        return $app;
    }

    public function test_only_active_products_in_active_categories_are_shown_with_real_details(): void
    {
        Storage::fake('public');
        $category = Category::factory()->create(['name' => 'Wood equipment', 'status' => true]);
        $product = Product::factory()->for($category)->active()->create(['name' => 'Real oak wall', 'price' => 12500, 'currency' => 'BDT', 'stock' => 0, 'material' => 'Oak']);
        Product::factory()->for($category)->create(['name' => 'Hidden draft', 'status' => 'draft']);
        Product::factory()->active()->create(['name' => 'Hidden category product']);
        Storage::disk('public')->put('products/wall.jpg', 'test');
        ProductImage::factory()->for($product)->create(['image_path' => 'products/wall.jpg', 'alt_text' => 'Oak front view']);
        $response = $this->get(route('shop.index'))->assertOk()->assertSee('Real oak wall')->assertSee('Wood equipment')->assertSee('BDT 12,500.00')->assertSee('Out of stock')->assertSee('Oak front view')->assertSee('Material: Oak')->assertDontSee('Hidden draft')->assertDontSee('Hidden category product')->assertDontSee('concept image');
        $this->assertSame(1, $response->viewData('products')->total());
        $record = $response->viewData('products')->first();
        $this->assertTrue($record->relationLoaded('images'));
        $this->assertTrue($record->relationLoaded('category'));
        $response->assertSee(route('shop.show', $product->slug), false);
    }

    public function test_database_filters_sorting_and_pagination_preserve_query(): void
    {
        Storage::fake('public');
        $category = Category::factory()->create(['name' => 'Wood equipment', 'status' => true]);
        Product::factory()->count(13)->for($category)->active()->create(['name' => 'Training wall', 'description' => 'Solid oak equipment']);
        Product::factory()->for($category)->active()->create(['name' => 'Unrelated rings', 'description' => 'Pair of rings']);
        $parameters = ['search' => 'Training', 'category' => $category->slug, 'sort' => 'name-asc'];
        $response = $this->get(route('shop.index', $parameters))->assertOk()->assertDontSee('Unrelated rings');
        $products = $response->viewData('products');
        $this->assertSame(13, $products->total());
        $this->assertCount(12, $products);
        $this->assertStringContainsString('search=Training', $products->nextPageUrl());
        $this->assertStringContainsString('category='.$category->slug, $products->nextPageUrl());
        $this->get($products->nextPageUrl())->assertOk()->assertViewHas('products', fn ($products) => $products->count() === 1);
        $this->get(route('shop.index', ['sort' => 'name-desc']))->assertOk()->assertViewHas('products', fn ($products) => $products->first()->name === 'Unrelated rings');
        $this->get(route('shop.index', ['category' => 'unknown']))->assertOk()->assertSee('No products found.');
    }

    public function test_empty_catalog_and_missing_images_have_fallbacks(): void
    {
        Storage::fake('public');
        $this->get(route('shop.index'))->assertOk()->assertSee('No products found.')->assertSee('0 products');
        $category = Category::factory()->create(['status' => true]);
        $product = Product::factory()->for($category)->active()->create(['price' => null, 'currency' => null, 'stock' => null]);
        ProductImage::factory()->for($product)->create(['image_path' => 'products/missing.jpg']);
        $this->get(route('shop.index'))->assertOk()->assertSee('Image coming soon')->assertSee('Price unavailable')->assertSee('Availability not confirmed')->assertDontSee('products/missing.jpg');
        Storage::disk('public')->put('products/second.jpg', 'test');
        ProductImage::factory()->for($product)->create(['image_path' => 'products/second.jpg', 'sort_order' => 1]);
        $this->get(route('shop.index'))->assertOk()->assertSee('products/second.jpg')->assertDontSee('Image coming soon');
    }

    public function test_invalid_filters_are_rejected_and_product_text_is_escaped(): void
    {
        $this->getJson(route('shop.index', ['sort' => 'bad', 'page' => -1, 'search' => ['bad']]))->assertUnprocessable()->assertJsonValidationErrors(['sort', 'page', 'search']);
        $category = Category::factory()->create(['status' => true]);
        Product::factory()->for($category)->active()->create(['name' => '<script>alert(1)</script>', 'price' => 0, 'currency' => 'BDT']);
        $this->get(route('shop.index'))->assertOk()->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)->assertDontSee('<script>alert(1)</script>', false)->assertSee('BDT 0.00');
    }
}
