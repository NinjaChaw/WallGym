<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OrderController extends Controller
{
    private const TRANSITIONS = [
        'pending' => ['confirmed', 'cancelled'],
        'confirmed' => ['processing', 'cancelled'],
        'processing' => ['shipped', 'cancelled'],
        'shipped' => ['delivered'],
        'delivered' => [],
        'cancelled' => [],
    ];

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(array_keys(self::TRANSITIONS))],
            'payment' => ['nullable', Rule::in(['pending', 'paid', 'refunded'])],
            'sort' => ['nullable', Rule::in(['newest', 'oldest'])],
        ]);
        $query = Order::query();
        if ($search = trim($filters['search'] ?? '')) {
            $query->where(function ($query) use ($search) {
                foreach (['order_number', 'customer_name', 'customer_email', 'customer_phone'] as $field) {
                    $query->orWhere($field, 'like', '%'.$search.'%');
                }
            });
        }
        if ($filters['status'] ?? null) {
            $query->where('status', $filters['status']);
        }
        if ($filters['payment'] ?? null) {
            $query->where('payment_status', $filters['payment']);
        }

        return view('admin.orders.index', [
            'orders' => $query->orderBy('id', ($filters['sort'] ?? 'newest') === 'oldest' ? 'asc' : 'desc')->paginate(15)->withQueryString(),
            'statuses' => array_keys(self::TRANSITIONS),
            'total' => Order::count(), 'paid' => Order::where('payment_status', 'paid')->count(),
            'toFulfill' => Order::whereIn('status', ['pending', 'confirmed', 'processing'])->count(),
        ]);
    }

    public function show(Order $order): View
    {
        return view('admin.orders.show', [
            'order' => $order->load('items'), 'transitions' => self::TRANSITIONS[$order->status],
            'paymentTransitions' => $this->paymentTransitions($order),
        ]);
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'action' => ['required', Rule::in(['status', 'payment'])],
            'expected_status' => ['required', Rule::in(array_keys(self::TRANSITIONS))],
            'expected_payment' => ['required', Rule::in(['pending', 'paid', 'refunded'])],
            'status' => ['required_if:action,status', Rule::in(array_keys(self::TRANSITIONS))],
            'payment_status' => ['required_if:action,payment', Rule::in(['paid', 'refunded'])],
        ]);
        DB::transaction(function () use ($order, $data) {
            $order = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();
            if ($order->status !== $data['expected_status'] || $order->payment_status !== $data['expected_payment']) {
                throw ValidationException::withMessages(['order' => 'This order changed since you opened it. Reload the page before updating.']);
            }
            if ($data['action'] === 'payment') {
                if (! in_array($data['payment_status'], $this->paymentTransitions($order), true)) {
                    throw ValidationException::withMessages(['payment_status' => 'This payment change is not allowed for the current order status.']);
                }
                $order->update(['payment_status' => $data['payment_status']]);

                return;
            }
            if (! in_array($data['status'], self::TRANSITIONS[$order->status], true)) {
                throw ValidationException::withMessages(['status' => 'This order status transition is not allowed.']);
            }
            if ($data['status'] === 'cancelled') {
                // The order lock and terminal cancelled status prevent duplicate restocking.
                $quantities = $order->items()->whereNotNull('product_id')->get()->groupBy('product_id')->map(fn ($items) => $items->sum('quantity'));
                $products = Product::whereIn('id', $quantities->keys())->orderBy('id')->lockForUpdate()->get();
                foreach ($products as $product) {
                    if ($product->stock === null || $product->stock + $quantities[$product->id] > 999999) {
                        throw ValidationException::withMessages(['status' => 'Correct the stock quantity for '.$product->name.' before cancelling this order.']);
                    }
                    $product->increment('stock', $quantities[$product->id]);
                }
            }
            $order->update(['status' => $data['status']]);
        }, 3);

        return to_route('admin.orders.show', $order)->with('success', $data['action'] === 'payment' ? 'Payment record updated. No money was transferred.' : 'Order status updated.');
    }

    private function paymentTransitions(Order $order): array
    {
        if ($order->status === 'delivered' && $order->payment_status === 'pending') {
            return ['paid'];
        }
        if ($order->status === 'cancelled' && $order->payment_status === 'paid') {
            return ['refunded'];
        }

        return [];
    }
}
