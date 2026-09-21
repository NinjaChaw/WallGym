<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PlaceOrder
{
    public function create(array $customer, array $items, array $review): Order
    {
        try {
            return DB::transaction(function () use ($customer, $items, $review) {
                if ($existing = Order::where('checkout_token', $customer['checkout_token'])->first()) {
                    return $existing;
                }
                if (! $items || count($items) > 50) {
                    throw ValidationException::withMessages(['cart' => 'Your cart is empty or invalid. Please review it before ordering.']);
                }
                $products = Product::whereIn('id', array_keys($items))->orderBy('id')->lockForUpdate()->get()->keyBy('id');
                $categories = Category::whereIn('id', $products->pluck('category_id'))->orderBy('id')->lockForUpdate()->get()->keyBy('id');
                if ($existing = Order::where('checkout_token', $customer['checkout_token'])->lockForUpdate()->first()) {
                    return $existing;
                }
                $lines = [];
                $subtotal = 0;
                foreach ($items as $id => $quantity) {
                    $product = $products->get($id);
                    if (! $product || $product->status !== 'active' || ! $categories->get($product->category_id)?->status || $product->currency !== 'BDT' || $product->price === null || ! is_int($quantity) || $quantity < 1 || $quantity > 99 || $product->stock < $quantity) {
                        throw ValidationException::withMessages(['cart' => 'A product is unavailable or has insufficient stock. Please review your cart.']);
                    }
                    if (($review[$id] ?? null) !== ['quantity' => $quantity, 'price' => $product->price]) {
                        throw ValidationException::withMessages(['cart' => 'Your cart or a product price has changed. Please review the updated checkout before placing your order.']);
                    }
                    [$whole, $fraction] = explode('.', $product->price);
                    $unit = (int) $whole * 100 + (int) $fraction;
                    if ($unit < 0) {
                        throw ValidationException::withMessages(['cart' => 'A product price is invalid. Please review your cart.']);
                    }
                    $subtotal += $unit * $quantity;
                    $lines[] = ['product_id' => $id, 'product_name' => $product->name, 'product_sku' => $product->sku, 'quantity' => $quantity, 'unit_price' => $product->price, 'line_total' => $this->decimal($unit * $quantity)];
                }
                $delivery = config('checkout.delivery')[$customer['zone']];
                $order = Order::create([
                    'checkout_token' => $customer['checkout_token'], 'order_number' => 'WG-'.Str::ulid(),
                    'customer_name' => $customer['name'], 'customer_phone' => $customer['phone'], 'customer_email' => $customer['email'] ?? null,
                    'address' => $customer['address'], 'area' => $customer['area'], 'city' => $customer['city'],
                    'delivery_zone' => $customer['zone'], 'notes' => $customer['notes'] ?? null,
                    'currency' => 'BDT', 'subtotal' => $this->decimal($subtotal), 'delivery_charge' => $this->decimal($delivery),
                    'discount_amount' => '0.00', 'total' => $this->decimal($subtotal + $delivery),
                    'payment_method' => 'cod', 'payment_status' => 'pending', 'status' => 'pending',
                ]);
                $order->items()->createMany($lines);
                foreach ($items as $id => $quantity) {
                    $products[$id]->decrement('stock', $quantity);
                }

                return $order;
            }, 3);
        } catch (UniqueConstraintViolationException $exception) {
            // A simultaneous retry may have committed the same checkout first.
            return Order::where('checkout_token', $customer['checkout_token'])->first() ?? throw $exception;
        }
    }

    private function decimal(int $amount): string
    {
        return intdiv($amount, 100).'.'.str_pad((string) ($amount % 100), 2, '0', STR_PAD_LEFT);
    }
}
