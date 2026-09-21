<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Order;
use App\Services\Cart;
use App\Services\PlaceOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function index(Request $request, Cart $cart): View|RedirectResponse
    {
        $contents = $cart->contents();
        if ($error = $this->cartError($contents)) {
            return to_route('cart.index')->withErrors(['cart' => $error]);
        }
        $customer = $request->session()->get('checkout.customer', []);
        if (! $request->session()->has('checkout.token')) {
            $request->session()->put('checkout.token', (string) Str::uuid());
        }
        $request->session()->put('checkout.review', collect($contents['items'])->mapWithKeys(fn ($item) => [$item['product']->id => ['quantity' => $item['quantity'], 'price' => $item['product']->price]])->all());
        $zone = $request->old('zone', $customer['zone'] ?? '');

        return view('checkout.index', array_merge($contents, [
            'customer' => $customer,
            'deliveryRates' => config('checkout.delivery'),
            'summary' => $this->summary($contents, is_string($zone) ? $zone : ''),
        ]));
    }

    public function store(CheckoutRequest $request, PlaceOrder $placeOrder): RedirectResponse
    {
        $token = $request->validated('checkout_token');
        if ($token === $request->session()->get('checkout.completed_token')) {
            return to_route('checkout.success');
        }
        if ($token !== $request->session()->get('checkout.token')) {
            throw ValidationException::withMessages(['cart' => 'Your checkout session has expired. Reload checkout and try again.']);
        }
        $order = $placeOrder->create($request->validated(), $request->session()->get('cart.items', []), $request->session()->get('checkout.review', []));
        // Only clear the cart after the database transaction has committed successfully.
        $request->session()->forget(['cart.items', 'checkout.customer', 'checkout.review', 'checkout.token']);
        $request->session()->put(['checkout.completed_token' => $token, 'checkout.order_id' => $order->id]);

        return to_route('checkout.success');
    }

    public function success(Request $request): View|RedirectResponse
    {
        $id = $request->session()->get('checkout.order_id');
        if (! $id) {
            return to_route('shop.index');
        }
        $order = Order::with('items')->findOrFail($id);

        return view('checkout.success', compact('order'));
    }

    public function quote(Request $request, Cart $cart): JsonResponse
    {
        $data = $request->validate(['zone' => ['required', Rule::in(array_keys(config('checkout.delivery')))]]);
        $contents = $cart->contents();
        if ($error = $this->cartError($contents)) {
            return response()->json(['message' => $error], 422);
        }
        if ($contents['notices']) {
            return response()->json(['message' => implode(' ', $contents['notices']).' Refresh checkout to review your cart.'], 409);
        }

        return response()->json($this->summary($contents, $data['zone']));
    }

    private function summary(array $contents, string $zone): array
    {
        $subtotal = $contents['totals']['BDT'];
        $delivery = config('checkout.delivery')[$zone] ?? null;

        return ['subtotal' => $subtotal, 'delivery' => $delivery, 'total' => $delivery === null ? null : $subtotal + $delivery];
    }

    private function cartError(array $contents): ?string
    {
        if (! $contents['items']) {
            return 'Your cart is empty. Add available products before continuing to checkout.';
        }
        if (array_keys($contents['totals']) !== ['BDT']) {
            return 'Cash on Delivery checkout currently supports BDT products only. Remove products in other currencies to continue.';
        }

        return null;
    }
}
