<aside class="purchase-summary purchase-card" aria-labelledby="summary-title">
    <h2 id="summary-title">Your order</h2>
    <ul class="purchase-mini-items" aria-label="Order items">
        @foreach($items as $item)
            <li>@if($item['cover'])<img src="{{ $item['cover']->url }}" width="46" height="56" alt="{{ $item['cover']->alt_text ?: $item['product']->name }}" loading="lazy">@else<span class="checkout-image-placeholder" role="img" aria-label="No image available">No image</span>@endif<div>{{ $item['product']->name }}<small>Quantity {{ $item['quantity'] }} · BDT {{ number_format($item['unit'] / 100, 2) }} each</small></div><strong>BDT {{ number_format($item['line'] / 100, 2) }}</strong></li>
        @endforeach
    </ul>
    <dl class="purchase-totals" aria-live="polite"><div><dt>Subtotal</dt><dd data-checkout-subtotal>BDT {{ number_format($summary['subtotal'] / 100, 2) }}</dd></div><div><dt>Delivery</dt><dd data-checkout-delivery>{{ $summary['delivery'] === null ? 'Select your area' : 'BDT '.number_format($summary['delivery'] / 100, 2) }}</dd></div><div class="purchase-total"><dt>Total</dt><dd data-checkout-total>{{ $summary['total'] === null ? 'Choose delivery zone' : 'BDT '.number_format($summary['total'] / 100, 2) }}</dd></div></dl>
    <button class="purchase-button" type="submit" form="checkout-form">Place Order <span aria-hidden="true">&rarr;</span></button>
    <p class="purchase-muted">Pay in cash when your order arrives. Please check your address and total before placing your order.</p>
    <p class="purchase-summary__foot">A little closer to everyday movement.</p>
</aside>
