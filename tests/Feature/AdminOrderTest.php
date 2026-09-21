<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminOrderTest extends TestCase
{
    use RefreshDatabase;
    use \Tests\AuthenticatesAdmin;

    public function createApplication()
    {
        $app = parent::createApplication();
        if ($app['config']->get('database.default') !== 'sqlite' || $app['config']->get('database.connections.sqlite.database') !== ':memory:') {
            throw new \RuntimeException('Tests require SQLite in memory.');
        }

        return $app;
    }

    private function order(array $changes = []): Order
    {
        return Order::create(array_merge(['order_number' => 'WG-'.Str::ulid(), 'customer_name' => 'Test Customer', 'customer_phone' => '01712345678', 'address' => 'House 1', 'area' => 'Dhanmondi', 'city' => 'Dhaka', 'delivery_zone' => 'dhaka', 'subtotal' => '6000.00', 'delivery_charge' => '80.00', 'total' => '6080.00'], $changes));
    }

    private function update(Order $order, array $changes)
    {
        return $this->patch(route('admin.orders.update', $order), array_merge(['action' => 'status', 'expected_status' => $order->status, 'expected_payment' => $order->payment_status], $changes));
    }

    public function test_list_filters_pagination_and_snapshot_details(): void
    {
        $this->get(route('admin.orders.index'))->assertOk()->assertSee('No orders found.');
        for ($i = 0; $i < 16; $i++) {
            $this->order();
        }
        $other = $this->order(['customer_name' => 'Different', 'status' => 'delivered', 'payment_status' => 'paid']);
        $response = $this->get(route('admin.orders.index', ['search' => 'Test', 'status' => 'pending', 'payment' => 'pending']))->assertOk()->assertDontSee('Different');
        $this->assertSame(16, $response->viewData('orders')->total());
        $this->assertCount(15, $response->viewData('orders'));
        $this->get($response->viewData('orders')->nextPageUrl())->assertOk()->assertViewHas('orders', fn ($orders) => $orders->count() === 1);
        $product = Product::factory()->create();
        $other->items()->create(['product_id' => $product->id, 'product_name' => 'Original rings', 'quantity' => 2, 'unit_price' => '3000.00', 'line_total' => '6000.00']);
        $product->delete();
        $this->get(route('admin.orders.show', $other))->assertOk()->assertSee('Original rings')->assertSee('3,000.00')->assertSee('6,080.00')->assertSee('House 1');
        $this->get('/admin/orders/99999')->assertNotFound();
    }

    public function test_cod_can_progress_unpaid_and_payment_is_recorded_after_delivery(): void
    {
        $order = $this->order();
        $this->update($order, ['action' => 'payment', 'payment_status' => 'paid'])->assertSessionHasErrors('payment_status');
        $this->update($order, ['status' => 'delivered'])->assertSessionHasErrors('status');
        foreach (['confirmed', 'processing', 'shipped', 'delivered'] as $state) {
            $this->update($order->fresh(), ['status' => $state])->assertSessionHasNoErrors();
            $this->assertSame($state, $order->fresh()->status);
        }
        $this->assertSame('pending', $order->fresh()->payment_status);
        $this->update($order->fresh(), ['action' => 'payment', 'payment_status' => 'paid'])->assertSessionHasNoErrors();
        $this->assertSame('paid', $order->fresh()->payment_status);
        $this->update($order->fresh(), ['status' => 'cancelled'])->assertSessionHasErrors('status');
        $this->update($order->fresh(), ['action' => 'payment', 'payment_status' => 'refunded'])->assertSessionHasErrors('payment_status');
    }

    public function test_cancellation_restores_stock_once_and_refund_is_separate(): void
    {
        $product = Product::factory()->create(['stock' => 5]);
        $order = $this->order(['payment_status' => 'paid']);
        $order->items()->create(['product_id' => $product->id, 'product_name' => 'Rings', 'quantity' => 2, 'unit_price' => '3000.00', 'line_total' => '6000.00']);
        $this->update($order, ['status' => 'cancelled'])->assertSessionHasNoErrors();
        $this->assertSame(7, $product->fresh()->stock);
        $this->assertSame('paid', $order->fresh()->payment_status);
        $this->update($order, ['status' => 'cancelled'])->assertSessionHasErrors('order');
        $this->update($order->fresh(), ['status' => 'cancelled'])->assertSessionHasErrors('status');
        $this->update($order->fresh(), ['status' => 'confirmed'])->assertSessionHasErrors('status');
        $this->assertSame(7, $product->fresh()->stock);
        $this->update($order->fresh(), ['action' => 'payment', 'payment_status' => 'refunded'])->assertSessionHasNoErrors();
        $this->assertSame('refunded', $order->fresh()->payment_status);
        $this->assertSame('6080.00', $order->fresh()->total);
    }

    public function test_failed_restock_rolls_back_all_changes_and_deleted_products_are_supported(): void
    {
        $first = Product::factory()->create(['stock' => 5]);
        $second = Product::factory()->create(['stock' => null]);
        $order = $this->order();
        foreach ([$first, $second] as $product) {
            $order->items()->create(['product_id' => $product->id, 'product_name' => 'Rings', 'quantity' => 1, 'unit_price' => '3000.00', 'line_total' => '3000.00']);
        }
        $this->update($order, ['status' => 'cancelled'])->assertSessionHasErrors('status');
        $this->assertSame(5, $first->fresh()->stock);
        $this->assertSame('pending', $order->fresh()->status);
        $second->delete();
        $this->update($order, ['status' => 'cancelled'])->assertSessionHasNoErrors();
        $this->assertSame(6, $first->fresh()->stock);
        $this->assertSame(2, $order->items()->count());
    }

    public function test_invalid_updates_cannot_change_financial_snapshots(): void
    {
        $order = $this->order();
        $this->update($order, ['status' => 'confirmed', 'total' => '1.00', 'customer_name' => 'Tampered'])->assertSessionHasNoErrors();
        $this->assertSame('6080.00', $order->fresh()->total);
        $this->assertSame('Test Customer', $order->fresh()->customer_name);
        $this->patch(route('admin.orders.update', $order), [])->assertSessionHasErrors(['action', 'expected_status', 'expected_payment']);
        $this->getJson(route('admin.orders.index', ['status' => 'invalid']))->assertUnprocessable();
    }
}
