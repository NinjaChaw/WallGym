<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class Cart
{
    public function __construct(private Request $request) {}

    public function contents(): array
    {
        $saved = $this->request->session()->get('cart.items', []);
        $products = Product::with(['category', 'images'])->whereIn('id', array_keys($saved))->get()->keyBy('id');
        $items = [];
        $totals = [];
        $notices = [];
        $current = [];
        foreach ($saved as $id => $quantity) {
            $product = $products->get($id);
            if (! $product || ! $this->available($product)) {
                $notices[] = ($product?->name ?? 'A product').' was removed because it is no longer available.';

                continue;
            }
            $quantity = min((int) $quantity, $product->stock, 99);
            if ($quantity < 1) {
                continue;
            }
            if ($quantity !== $saved[$id]) {
                $notices[] = $product->name.' quantity was adjusted to '.$quantity.' to match availability.';
            }
            $current[$id] = $quantity;
            // Decimal strings are converted to minor units without floating-point arithmetic.
            [$whole, $fraction] = explode('.', $product->price);
            $unit = (int) $whole * 100 + (int) $fraction;
            $line = $unit * $quantity;
            $totals[$product->currency] = ($totals[$product->currency] ?? 0) + $line;
            $cover = $product->images->first(fn ($image) => Storage::disk('public')->exists($image->image_path));
            $items[] = compact('product', 'quantity', 'unit', 'line', 'cover');
        }
        $this->request->session()->put('cart.items', $current);

        return ['items' => $items, 'totals' => $totals, 'notices' => $notices, 'count' => array_sum($current)];
    }

    public function set(Product $product, int $quantity, bool $adding = false): void
    {
        $product->loadMissing('category');
        if (! $this->available($product)) {
            throw ValidationException::withMessages(['quantity' => 'This product is not currently available to add to your cart.']);
        }
        $items = $this->request->session()->get('cart.items', []);
        if (! $adding && ! array_key_exists($product->id, $items)) {
            throw ValidationException::withMessages(['quantity' => 'This product is no longer in your cart.']);
        }
        $quantity += $adding ? ($items[$product->id] ?? 0) : 0;
        if ($quantity > min(99, $product->stock)) {
            throw ValidationException::withMessages(['quantity' => 'You can have up to '.min(99, $product->stock).' of this product in your cart.']);
        }
        if (! isset($items[$product->id]) && count($items) >= 50) {
            throw ValidationException::withMessages(['quantity' => 'Your cart can hold up to 50 different products. Please remove an item first.']);
        }
        $items[$product->id] = $quantity;
        $this->request->session()->put('cart.items', $items);
    }

    public function remove(string $id): void
    {
        $this->request->session()->forget('cart.items.'.$id);
    }

    private function available(Product $product): bool
    {
        return $product->status === 'active' && $product->category?->status
            && $product->stock > 0 && $product->price !== null && (float) $product->price >= 0
            && in_array($product->currency, ['BDT', 'SEK', 'EUR', 'USD'], true);
    }
}
