<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\Cart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(Cart $cart): View
    {
        return view('cart.index', $cart->contents());
    }

    public function store(Request $request, Cart $cart): RedirectResponse
    {
        $data = $request->validate(['product_id' => ['required', 'integer', 'exists:products,id'], 'quantity' => ['required', 'integer', 'min:1', 'max:99']]);
        $cart->set(Product::findOrFail($data['product_id']), (int) $data['quantity'], true);

        return to_route('cart.index')->with('success', 'Product added to your cart.');
    }

    public function update(Request $request, Product $product, Cart $cart): RedirectResponse
    {
        $data = $request->validate(['quantity' => ['required', 'integer', 'min:1', 'max:99']]);
        $cart->set($product, (int) $data['quantity']);

        return to_route('cart.index')->with('success', 'Cart quantity updated.');
    }

    public function destroy(string $id, Cart $cart): RedirectResponse
    {
        $cart->remove($id);

        return to_route('cart.index')->with('success', 'Item removed from your cart.');
    }

    public function clear(Request $request): RedirectResponse
    {
        $request->session()->forget('cart.items');

        return to_route('cart.index')->with('success', 'Your cart has been cleared.');
    }
}
