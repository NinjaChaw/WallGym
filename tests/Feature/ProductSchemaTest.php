<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function createApplication()
    {
        $app = parent::createApplication();
        if ($app['config']->get('database.default') !== 'sqlite' || $app['config']->get('database.connections.sqlite.database') !== ':memory:') {
            throw new \RuntimeException('Product schema tests require an isolated SQLite in-memory database.');
        }

        return $app;
    }

    private function product(array $attributes = []): Product
    {
        $category = Category::create(['name' => 'Walls', 'slug' => 'walls']);

        return $category->products()->create(array_replace(['name' => 'Swedish Wall', 'slug' => 'swedish-wall'], $attributes));
    }

    public function test_relationships_casts_and_ordered_images(): void
    {
        $product = $this->product(['price' => '18500.50', 'currency' => 'BDT', 'stock' => 0]);
        $last = $product->images()->create(['image_path' => 'products/back.jpg', 'sort_order' => 2]);
        $first = $product->images()->create(['image_path' => 'products/front.jpg', 'sort_order' => 0]);
        $this->assertSame('18500.50', $product->fresh()->price);
        $this->assertSame(0, $product->fresh()->stock);
        $this->assertSame('draft', $product->fresh()->status);
        $this->assertTrue($product->category->products->first()->is($product));
        $this->assertTrue($first->product->is($product));
        $this->assertSame([$first->id, $last->id], $product->images->modelKeys());
        $product->delete();
        $this->assertDatabaseCount('product_images', 0);
        $this->assertDatabaseCount('categories', 1);
    }

    public function test_unset_price_and_stock_remain_null(): void
    {
        $product = $this->product()->fresh();
        $this->assertNull($product->price);
        $this->assertNull($product->currency);
        $this->assertNull($product->stock);
    }

    public function test_category_with_products_cannot_be_deleted(): void
    {
        $product = $this->product();
        $this->expectException(QueryException::class);
        $product->category->delete();
    }

    public function test_duplicate_product_slugs_are_rejected(): void
    {
        $product = $this->product();
        $this->expectException(QueryException::class);
        $product->category->products()->create(['name' => 'Duplicate', 'slug' => $product->slug]);
    }

    public function test_duplicate_skus_are_rejected(): void
    {
        $product = $this->product(['sku' => 'WG-001']);
        $this->expectException(QueryException::class);
        $product->category->products()->create(['name' => 'Other', 'slug' => 'other', 'sku' => 'WG-001']);
    }

    public function test_images_require_an_existing_product(): void
    {
        $this->expectException(QueryException::class);
        ProductImage::create(['product_id' => 999, 'image_path' => 'products/missing.jpg']);
    }
}
