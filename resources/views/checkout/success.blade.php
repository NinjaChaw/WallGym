@extends('layouts.app')
@section('title', 'Order preview complete | WallGym')
@section('content')
<div class="purchase" data-purchase="success" data-base="{{ url('/') }}"><div class="purchase-inner">
    @include('checkout._shared', ['step' => 3])
    <div class="purchase-success" data-receipt hidden>
        <header class="purchase-intro"><span class="purchase-success__mark" aria-hidden="true"><svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="m5 12 4 4L19 6"/></svg></span><p class="purchase-eyebrow">All set for the preview</p><h1>A little more <em>movement.</em></h1><p>Your order preview is complete. No real order has been placed.</p></header>
        <section class="purchase-card" aria-labelledby="receipt-title"><div class="purchase-section-heading"><h2 id="receipt-title">Order summary</h2><span data-reference></span></div><ul class="purchase-mini-items" data-mini-items></ul><dl class="purchase-totals"><div><dt>Subtotal</dt><dd data-subtotal></dd></div><div><dt>Delivery</dt><dd data-delivery></dd></div><div class="purchase-total"><dt>Total</dt><dd data-total></dd></div></dl><div class="purchase-receipt-grid"><div><h3>Deliver to</h3><p data-address></p><p data-contact></p></div><div><h3>Payment method</h3><p>Cash on Delivery</p><h3>What happens next?</h3><p>This is a design preview. No confirmation message or shipment will be sent.</p></div></div></section>
        <a class="purchase-button" href="{{ url('/shop') }}">Continue exploring <span aria-hidden="true">&rarr;</span></a>
    </div>
    <section class="purchase-empty purchase-card" data-empty hidden><h1>No order preview yet.</h1><p>Complete the checkout preview to see your order summary here.</p><a class="purchase-button" href="{{ url('/cart') }}">Go to cart</a></section>
</div></div>
@endsection
