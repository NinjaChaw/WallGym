@extends('layouts.app')
@section('title', 'Order received | WallGym')
@push('styles') @vite('resources/css/pages/purchase.css') @endpush
@section('content')
<div class="purchase"><div class="purchase-inner"><div class="purchase-success">
    <header class="purchase-intro"><span class="purchase-success__mark" aria-hidden="true">✓</span><p class="purchase-eyebrow">Order received</p><h1>A little more <em>movement.</em></h1><p>Thank you, {{ $order->customer_name }}. Your order has been placed.</p></header>
    <section class="purchase-card" aria-labelledby="receipt-title"><div class="purchase-section-heading"><h2 id="receipt-title">Order summary</h2><span>{{ $order->order_number }}</span></div>
        <ul class="purchase-mini-items">@foreach($order->items as $item)<li><div>{{ $item->product_name }}<small>Quantity {{ $item->quantity }} · {{ $order->currency }} {{ number_format($item->unit_price, 2) }} each</small></div><strong>{{ $order->currency }} {{ number_format($item->line_total, 2) }}</strong></li>@endforeach</ul>
        <dl class="purchase-totals"><div><dt>Subtotal</dt><dd>{{ $order->currency }} {{ number_format($order->subtotal, 2) }}</dd></div><div><dt>Delivery</dt><dd>{{ $order->currency }} {{ number_format($order->delivery_charge, 2) }}</dd></div>@if($order->discount_amount > 0)<div><dt>Discount</dt><dd>{{ $order->currency }} {{ number_format($order->discount_amount, 2) }}</dd></div>@endif<div class="purchase-total"><dt>Total</dt><dd>{{ $order->currency }} {{ number_format($order->total, 2) }}</dd></div></dl>
        <div class="purchase-receipt-grid"><div><h3>Deliver to</h3><p>{{ $order->customer_name }}<br>{{ $order->address }}<br>{{ $order->area }}, {{ $order->city }}<br>Bangladesh</p><p>{{ $order->customer_phone }}@if($order->customer_email)<br>{{ $order->customer_email }}@endif</p>@if($order->notes)<h3>Delivery instructions</h3><p>{{ $order->notes }}</p>@endif</div><div><h3>Payment method</h3><p>Cash on Delivery · {{ ucfirst($order->payment_status) }}</p><h3>Order status</h3><p>{{ ucfirst($order->status) }}</p><h3>What happens next?</h3><p>Your order is awaiting confirmation. Keep your order number for reference.</p></div></div>
    </section><a class="purchase-button" href="{{ route('shop.index') }}">Continue shopping <span aria-hidden="true">&rarr;</span></a>
</div></div></div>
@endsection
