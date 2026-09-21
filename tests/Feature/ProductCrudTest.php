<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductCrudTest extends TestCase
{
    use RefreshDatabase;

    public function createApplication()
    {
        $app = parent::createApplication();
        if ($app['config']->get('database.default') !== 'sqlite' || $app['config']->get('database.connections.sqlite.database') !== ':memory:') {
            throw new \RuntimeException('Product tests require an isolated SQLite in-memory database.');
        }

        return $app;
    }

    private function payload(array $changes = []): array
    {
        return array_replace(['name' => 'Oak wall', 'slug' => 'oak-wall', 'sku' => 'WG-TEST', 'category_id' => Category::factory()->create()->id, 'status' => 'draft'], $changes);
    }

    private function image(): UploadedFile
    {
        return UploadedFile::fake()->createWithContent('wall.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+jRZkAAAAASUVORK5CYII='));
    }

    public function test_pages_render_database_records_and_create_uploads(): void
    {
        Storage::fake('public');
        $this->get(route('admin.products.create'))->assertOk()->assertSee('Create product');
        $this->post(route('admin.products.store'), $this->payload(['images' => [$this->image(), $this->image()]]))->assertSessionHasNoErrors();
        $product = Product::sole();
        $this->assertCount(2, $product->images);
        $this->assertSame([0, 1], $product->images->pluck('sort_order')->all());
        foreach ($product->images as $image) {
            Storage::disk('public')->assertExists($image->image_path);
        }
        $this->assertNull($product->price);
        $this->assertNull($product->stock);
        $this->get(route('admin.products.index'))->assertOk()->assertSee('Oak wall')->assertSee('WG-TEST');
        $this->get(route('admin.products.show', $product))->assertOk()->assertSee('Oak wall')->assertSee('Delete product');
        $this->get(route('admin.products.edit', $product))->assertOk()->assertSee('Oak wall')->assertSee('gallery['.$product->images->first()->id.'][alt_text]', false);
    }

    public function test_validation_rejects_duplicates_bad_values_and_unsafe_uploads(): void
    {
        Storage::fake('public');
        Product::factory()->create(['slug' => 'oak-wall', 'sku' => 'WG-TEST']);
        $this->post(route('admin.products.store'), $this->payload([
            'category_id' => 99999, 'status' => 'active', 'price' => -5, 'stock' => 1.5,
            'images' => [UploadedFile::fake()->create('script.svg', 1, 'image/svg+xml')],
        ]))->assertSessionHasErrors(['slug', 'sku', 'category_id', 'price', 'currency', 'stock', 'images.0']);
        $this->post(route('admin.products.store'), $this->payload(['slug' => 'different', 'sku' => null, 'images' => [$this->image()->size(1025)]]))->assertSessionHasErrors('images.0');
        $this->assertDatabaseCount('products', 1);
        $this->assertEmpty(Storage::disk('public')->allFiles());
    }

    public function test_active_requires_commercial_details_but_accepts_zero_values(): void
    {
        $data = $this->payload(['status' => 'active']);
        $this->post(route('admin.products.store'), $data)->assertSessionHasErrors(['price', 'currency', 'stock']);
        $this->post(route('admin.products.store'), array_merge($data, ['price' => 0, 'currency' => 'BDT', 'stock' => 0]))->assertSessionHasNoErrors();
        $product = Product::sole();
        $this->assertSame('0.00', $product->price);
        $this->assertSame(0, $product->stock);
        $this->put(route('admin.products.update', $product), array_merge($data, ['status' => 'draft', 'price' => null, 'currency' => null, 'stock' => null]))->assertSessionHasNoErrors();
        $this->assertNull($product->fresh()->price);
    }

    public function test_update_reorders_describes_removes_and_appends_images(): void
    {
        Storage::fake('public');
        $data = $this->payload(['images' => [$this->image(), $this->image(), $this->image()]]);
        $this->post(route('admin.products.store'), $data)->assertSessionHasNoErrors();
        $product = Product::sole();
        [$first, $second, $third] = $product->images->all();
        $data['name'] = 'Updated wall';
        $data['gallery'] = [
            $first->id => ['alt_text' => 'Side view', 'sort_order' => 9],
            $second->id => ['sort_order' => 1, 'remove' => 1],
            $third->id => ['alt_text' => 'Front view', 'sort_order' => 0],
        ];
        $data['images'] = [$this->image()];
        $this->put(route('admin.products.update', $product), $data)->assertSessionHasNoErrors()->assertRedirect(route('admin.products.show', $product));
        $images = $product->fresh()->images;
        $this->assertCount(3, $images);
        $this->assertSame($third->id, $images[0]->id);
        $this->assertSame('Front view', $images[0]->alt_text);
        $this->assertSame('Side view', $images[1]->alt_text);
        $this->assertSame([0, 1, 2], $images->pluck('sort_order')->all());
        Storage::disk('public')->assertMissing($second->image_path);
        Storage::disk('public')->assertExists($first->image_path);
        $this->delete(route('admin.products.destroy', $product))->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseCount('products', 0);
        $this->assertDatabaseCount('product_images', 0);
        $this->assertDatabaseCount('categories', 1);
        $this->assertEmpty(Storage::disk('public')->allFiles());
    }

    public function test_foreign_image_and_excess_gallery_are_rejected_without_changes(): void
    {
        Storage::fake('public');
        $product = Product::factory()->create();
        $foreign = ProductImage::factory()->create();
        $data = $this->payload(['gallery' => [$foreign->id => ['remove' => 1, 'sort_order' => 0]]]);
        $this->put(route('admin.products.update', $product), $data)->assertSessionHasErrors('gallery');
        $this->assertModelExists($foreign);
        ProductImage::factory()->count(10)->for($product)->create();
        unset($data['gallery']);
        $data['images'] = [$this->image()];
        $this->put(route('admin.products.update', $product), $data)->assertSessionHasErrors('images');
        $this->assertSame(10, $product->images()->count());
        $this->assertNotSame('Oak wall', $product->fresh()->name);
        $this->assertEmpty(Storage::disk('public')->allFiles());
    }

    public function test_database_failure_rolls_back_and_cleans_new_uploads(): void
    {
        Storage::fake('public');
        $dispatcher = ProductImage::getEventDispatcher();
        ProductImage::setEventDispatcher(clone $dispatcher);
        ProductImage::creating(function () {
            throw new \RuntimeException('Simulated image insert failure');
        });
        try {
            $this->withoutExceptionHandling();
            try {
                $this->post(route('admin.products.store'), $this->payload(['images' => [$this->image()]]));
                $this->fail('Expected the image insert to fail.');
            } catch (\RuntimeException $exception) {
                $this->assertSame('Simulated image insert failure', $exception->getMessage());
            }
        } finally {
            ProductImage::setEventDispatcher($dispatcher);
        }
        $this->assertDatabaseCount('products', 0);
        $this->assertDatabaseCount('product_images', 0);
        $this->assertEmpty(Storage::disk('public')->allFiles());
    }

    public function test_deleting_product_preserves_shared_and_non_product_files(): void
    {
        Storage::fake('public');
        $product = Product::factory()->create();
        $other = Product::factory()->create();
        Storage::disk('public')->put('products/shared.jpg', 'shared');
        Storage::disk('public')->put('categories/keep.jpg', 'category');
        ProductImage::factory()->for($product)->create(['image_path' => 'products/shared.jpg']);
        ProductImage::factory()->for($other)->create(['image_path' => 'products/shared.jpg']);
        ProductImage::factory()->for($product)->create(['image_path' => 'categories/keep.jpg']);
        $this->delete(route('admin.products.destroy', $product))->assertSessionHasNoErrors();
        Storage::disk('public')->assertExists(['products/shared.jpg', 'categories/keep.jpg']);
        $this->assertModelExists($other);
        $this->get(route('admin.products.show', $product))->assertNotFound();
        $this->get(route('admin.products.edit', $product))->assertNotFound();
    }

    public function test_filters_and_pagination_use_database(): void
    {
        $category = Category::factory()->create();
        Product::factory()->count(13)->for($category)->active()->create(['name' => 'Find wall']);
        Product::factory()->create(['name' => 'Other product']);
        $url = route('admin.products.index', ['search' => 'Find wall', 'category_id' => $category->id, 'status' => 'active', 'sort' => 'recent']);
        $response = $this->get($url)->assertOk()->assertDontSee('Other product');
        $this->assertSame(13, $response->viewData('products')->total());
        $this->assertCount(12, $response->viewData('products'));
        $this->get($url.'&page=2')->assertOk()->assertViewHas('products', fn ($products) => $products->count() === 1);
    }
}
