<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function createApplication()
    {
        $app = parent::createApplication();
        if ($app['config']->get('database.default') !== 'sqlite' || $app['config']->get('database.connections.sqlite.database') !== ':memory:') {
            throw new \RuntimeException('Cart tests require an isolated SQLite in-memory database.');
        }

        return $app;
    }

    private function product(array $attributes = []): Product
    {
        return Product::factory()->for(Category::factory()->create(['status' => true]))->active()->create(array_merge(['price' => '19.99', 'currency' => 'BDT', 'stock' => 8], $attributes));
    }

    public function test_guest_can_add_merge_update_remove_and_clear_cart(): void
    {
        Storage::fake('public');
        $product = $this->product();
        $this->get('/cart')->assertOk()->assertSee('Your cart is empty.')->assertDontSee('Load sample cart');
        $this->post(route('cart.store'), ['product_id' => $product->id, 'quantity' => 2, 'price' => 1])->assertRedirect(route('cart.index'))->assertSessionHas('cart.items.'.$product->id, 2);
        $this->post(route('cart.store'), ['product_id' => $product->id, 'quantity' => 1])->assertSessionHas('cart.items.'.$product->id, 3);
        $response = $this->get('/cart')->assertOk()->assertSee($product->name)->assertSee('BDT 59.97')->assertSee('No image')->assertSee('3 items')->assertDontSee('data-purchase="cart"', false);
        $this->assertSame(['BDT' => 5997], $response->viewData('totals'));
        $this->patch(route('cart.update', $product), ['quantity' => 5])->assertSessionHas('cart.items.'.$product->id, 5);
        $this->delete(route('cart.destroy', $product->id))->assertSessionMissing('cart.items.'.$product->id);
        $this->post(route('cart.store'), ['product_id' => $product->id, 'quantity' => 1]);
        $this->delete(route('cart.clear'))->assertSessionMissing('cart.items');
        $this->assertSame(8, $product->fresh()->stock);
    }

    public function test_quantities_availability_and_merged_stock_are_validated(): void
    {
        $product = $this->product(['stock' => 3]);
        foreach ([0, -1, 1.5, 100, 'wrong'] as $quantity) {
            $this->post(route('cart.store'), ['product_id' => $product->id, 'quantity' => $quantity])->assertSessionHasErrors('quantity');
        }
        $this->patch(route('cart.update', $product), ['quantity' => 1])->assertSessionHasErrors('quantity');
        $this->post(route('cart.store'), ['product_id' => $product->id, 'quantity' => 2])->assertSessionHasNoErrors();
        $this->post(route('cart.store'), ['product_id' => $product->id, 'quantity' => 2])->assertSessionHasErrors('quantity')->assertSessionHas('cart.items.'.$product->id, 2);
        $this->patch(route('cart.update', $product), ['quantity' => 4])->assertSessionHasErrors('quantity')->assertSessionHas('cart.items.'.$product->id, 2);
        foreach ([['status' => 'draft'], ['stock' => 0], ['stock' => null], ['price' => null], ['currency' => null]] as $attributes) {
            $unavailable = $this->product($attributes);
            $this->post(route('cart.store'), ['product_id' => $unavailable->id, 'quantity' => 1])->assertSessionHasErrors('quantity')->assertSessionMissing('cart.items.'.$unavailable->id);
        }
        $product->category->update(['status' => false]);
        $this->post(route('cart.store'), ['product_id' => $product->id, 'quantity' => 1])->assertSessionHasErrors('quantity');
        $this->post(route('cart.store'), ['product_id' => 9999, 'quantity' => 1])->assertSessionHasErrors('product_id');
    }

    public function test_cart_refreshes_prices_stock_and_removes_deleted_or_hidden_products(): void
    {
        Storage::fake('public');
        $product = $this->product();
        $deleted = $this->product();
        $hidden = $this->product();
        $this->withSession(['cart.items' => [$product->id => 5, $deleted->id => 1, $hidden->id => 1]]);
        $product->update(['price' => '10.01', 'stock' => 2]);
        $deleted->delete();
        $hidden->update(['status' => 'draft']);
        $response = $this->get('/cart')->assertOk()->assertSee('BDT 20.02')->assertSee('quantity was adjusted')->assertSee('was removed')->assertSessionHas('cart.items', [$product->id => 2]);
        $this->assertCount(1, $response->viewData('items'));
        $product->update(['stock' => 0]);
        $this->get('/cart')->assertOk()->assertSee('Your cart is empty.')->assertSessionHas('cart.items', []);
    }

    public function test_totals_keep_currencies_separate_and_zero_prices_are_valid(): void
    {
        Storage::fake('public');
        $bdt = $this->product(['price' => '0.00']);
        $usd = $this->product(['price' => '12.34', 'currency' => 'USD']);
        $this->post(route('cart.store'), ['product_id' => $bdt->id, 'quantity' => 1])->assertSessionHasNoErrors();
        $this->post(route('cart.store'), ['product_id' => $usd->id, 'quantity' => 3])->assertSessionHasNoErrors();
        $response = $this->get('/cart')->assertOk()->assertSee('BDT 0.00')->assertSee('USD 37.02')->assertSee('No currency conversion');
        $this->assertSame(['BDT' => 0, 'USD' => 3702], $response->viewData('totals'));
    }

    public function test_product_page_has_real_add_form_and_header_count(): void
    {
        Storage::fake('public');
        $product = $this->product();
        $this->get(route('shop.show', $product->slug))->assertOk()->assertSee(route('cart.store'), false)->assertSee('name="quantity"', false)->assertSee('Add to cart');
        $this->post(route('cart.store'), ['product_id' => $product->id, 'quantity' => 2]);
        $this->get(route('shop.index'))->assertOk()->assertSee('data-session-cart', false)->assertSessionHas('cart.items.'.$product->id, 2);
        $product->update(['stock' => 0]);
        $this->get(route('shop.show', $product->slug))->assertOk()->assertDontSee('name="quantity"', false)->assertSee('Out of stock');
    }
}
