<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function createApplication()
    {
        $app = parent::createApplication();
        if ($app['config']->get('database.default') !== 'sqlite' || $app['config']->get('database.connections.sqlite.database') !== ':memory:') {
            throw new \RuntimeException('Order schema tests require an isolated SQLite in-memory database.');
        }

        return $app;
    }

    private function order(): Order
    {
        return Order::create([
            'order_number' => 'WG-101', 'customer_name' => 'Original Customer',
            'customer_phone' => '01712345678', 'address' => 'Original delivery address',
            'area' => 'Dhanmondi', 'city' => 'Dhaka', 'delivery_zone' => 'dhaka',
            'subtotal' => '6000.00', 'delivery_charge' => '80.00', 'total' => '6080.00',
        ]);
    }

    public function test_order_snapshots_survive_product_edits_and_deletion(): void
    {
        $product = Product::factory()->create(['name' => 'Gymnastic Rings', 'price' => '3000.00', 'sku' => 'RING-01']);
        $order = $this->order();
        $item = $order->items()->create([
            'product_id' => $product->id, 'product_name' => $product->name,
            'product_sku' => $product->sku, 'quantity' => 2,
            'unit_price' => $product->price, 'line_total' => '6000.00',
        ]);
        $this->assertTrue($item->order->is($order));
        $this->assertTrue($item->product->is($product));
        $product->update(['name' => 'New Rings', 'price' => '3800.00', 'sku' => 'RING-02']);
        $this->assertSame('Gymnastic Rings', $item->fresh()->product_name);
        $this->assertSame('3000.00', $item->fresh()->unit_price);
        $this->assertSame('RING-01', $item->fresh()->product_sku);
        $product->delete();
        $this->assertNull($item->fresh()->product_id);
        $this->assertNull($item->fresh()->product);
        $this->assertSame('6000.00', $item->fresh()->line_total);
        $this->assertSame(2, $item->fresh()->quantity);
        $this->assertSame('Original delivery address', $order->fresh()->address);
        $this->assertSame('6080.00', $order->fresh()->total);
        $this->assertSame('0.00', $order->fresh()->discount_amount);
        $this->assertSame('pending', $order->fresh()->payment_status);
        $this->assertSame('cod', $order->fresh()->payment_method);
    }

    public function test_deleting_order_removes_only_its_items(): void
    {
        $product = Product::factory()->create();
        $order = $this->order();
        $order->items()->create(['product_id' => $product->id, 'product_name' => $product->name, 'quantity' => 1, 'unit_price' => '6000.00', 'line_total' => '6000.00']);
        $order->delete();
        $this->assertDatabaseCount('order_items', 0);
        $this->assertModelExists($product);
    }

    public function test_order_numbers_are_unique(): void
    {
        $this->order();
        $this->expectException(QueryException::class);
        $this->order();
    }
}
