<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function createApplication()
    {
        $app = parent::createApplication();
        if ($app['config']->get('database.default') !== 'sqlite' || $app['config']->get('database.connections.sqlite.database') !== ':memory:') {
            throw new \RuntimeException('Checkout tests require SQLite in memory.');
        }

        return $app;
    }

    private function prepare(): Product
    {
        Storage::fake('public');
        $product = Product::factory()->for(Category::factory()->create(['status' => true]))->active()->create(['price' => '100.25', 'currency' => 'BDT', 'stock' => 10]);
        $this->withSession(['cart.items' => [$product->id => 2]])->get(route('checkout.index'))->assertOk()->assertSee('Place Order');

        return $product;
    }

    private function customer(array $changes = []): array
    {
        return array_merge(['checkout_token' => session('checkout.token'), 'name' => 'Test Customer', 'phone' => '+880 1712-345678', 'email' => null, 'address' => 'House 5', 'area' => 'Dhanmondi', 'city' => 'Dhaka', 'zone' => 'dhaka', 'notes' => 'Call on arrival', 'payment' => 'cod'], $changes);
    }

    public function test_place_order_snapshots_secure_totals_stock_and_confirmation(): void
    {
        $product = $this->prepare();
        $data = $this->customer(['total' => 1, 'delivery_charge' => 0, 'discount_amount' => 999]);
        $this->post(route('checkout.store'), $data)->assertRedirect(route('checkout.success'))->assertSessionHasNoErrors()->assertSessionMissing('cart.items');
        $order = Order::sole();
        $this->assertSame('280.50', $order->total);
        $this->assertSame('200.50', $order->subtotal);
        $this->assertSame('80.00', $order->delivery_charge);
        $this->assertSame('0.00', $order->discount_amount);
        $this->assertSame('+8801712345678', $order->customer_phone);
        $this->assertSame('pending', $order->payment_status);
        $this->assertSame('cod', $order->payment_method);
        $this->assertSame(8, $product->fresh()->stock);
        $item = $order->items()->sole();
        $this->assertSame($product->name, $item->product_name);
        $this->assertSame('100.25', $item->unit_price);
        $product->update(['name' => 'Changed', 'price' => '999.00']);
        $product->delete();
        $this->get(route('checkout.success'))->assertOk()->assertSee($order->order_number)->assertSee('BDT 280.50')->assertSee($item->product_name)->assertSee('House 5')->assertDontSee('data-purchase', false);
        $this->post(route('checkout.store'), $data)->assertRedirect(route('checkout.success'));
        $this->assertDatabaseCount('orders', 1);
    }

    public function test_retries_are_idempotent_even_with_original_session_restored(): void
    {
        $product = $this->prepare();
        $data = $this->customer();
        $session = ['cart.items' => session('cart.items'), 'checkout.token' => session('checkout.token'), 'checkout.review' => session('checkout.review')];
        $this->post(route('checkout.store'), $data)->assertSessionHasNoErrors();
        $this->withSession(array_merge($session, ['checkout.completed_token' => null]))->post(route('checkout.store'), $data)->assertRedirect(route('checkout.success'));
        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseCount('order_items', 1);
        $this->assertSame(8, $product->fresh()->stock);
    }

    public function test_price_stock_and_visibility_changes_prevent_order_creation(): void
    {
        $product = $this->prepare();
        $data = $this->customer();
        foreach ([['stock' => 1], ['stock' => 10, 'price' => '200.00'], ['price' => '100.25', 'status' => 'draft']] as $changes) {
            $product->update($changes);
            $this->from(route('checkout.index'))->post(route('checkout.store'), $data)->assertSessionHasErrors('cart')->assertSessionHasInput('name', 'Test Customer')->assertSessionHas('cart.items.'.$product->id, 2);
            $this->assertDatabaseCount('orders', 0);
        }
        $product->update(['status' => 'active']);
        $product->category->update(['status' => false]);
        $this->post(route('checkout.store'), $data)->assertSessionHasErrors('cart');
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_database_failure_rolls_back_order_and_keeps_cart_and_stock(): void
    {
        $product = $this->prepare();
        $dispatcher = OrderItem::getEventDispatcher();
        OrderItem::setEventDispatcher(clone $dispatcher);
        OrderItem::creating(function () {
            throw new \RuntimeException('Simulated failure');
        });
        try {
            $this->withoutExceptionHandling();
            try {
                $this->post(route('checkout.store'), $this->customer());
                $this->fail('Expected failure');
            } catch (\RuntimeException $exception) {
                $this->assertSame('Simulated failure', $exception->getMessage());
            }
        } finally {
            OrderItem::setEventDispatcher($dispatcher);
        }
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);
        $this->assertSame(10, $product->fresh()->stock);
        $this->assertSame(2, session('cart.items.'.$product->id));
    }

    public function test_validation_empty_cart_and_confirmation_access(): void
    {
        $this->get(route('checkout.index'))->assertRedirect(route('cart.index'));
        $this->get(route('checkout.success', ['order_id' => 1]))->assertRedirect(route('shop.index'));
        $this->prepare();
        $this->from(route('checkout.index'))->post(route('checkout.store'), $this->customer(['phone' => 'wrong', 'payment' => 'card', 'email' => 'invalid', 'zone' => 'free']))->assertSessionHasErrors(['phone', 'payment', 'email', 'zone'])->assertSessionHasInput('name', 'Test Customer');
        $this->get(route('checkout.index'))->assertOk()->assertSee('Test Customer')->assertSee('House 5');
        $this->post(route('checkout.store'), $this->customer(['checkout_token' => (string) Str::uuid()]))->assertSessionHasErrors('cart');
        $this->withSession(['cart.items' => []])->post(route('checkout.store'), $this->customer())->assertSessionHasErrors('cart');
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_quote_is_server_calculated_and_other_currencies_are_blocked(): void
    {
        $product = $this->prepare();
        $this->getJson(route('checkout.quote', ['zone' => 'outside', 'total' => 1]))->assertOk()->assertExactJson(['subtotal' => 20050, 'delivery' => 15000, 'total' => 35050]);
        $this->getJson(route('checkout.quote', ['zone' => 'free']))->assertUnprocessable();
        $product->update(['currency' => 'USD']);
        $this->post(route('checkout.store'), $this->customer())->assertSessionHasErrors('cart');
        $this->get(route('checkout.index'))->assertRedirect(route('cart.index'));
        $this->assertDatabaseCount('orders', 0);
    }
}
