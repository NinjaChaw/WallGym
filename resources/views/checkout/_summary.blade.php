<aside class="purchase-summary purchase-card" aria-labelledby="summary-title">
    <h2 id="summary-title">Your order</h2>
    @if($step === 2)<ul class="purchase-mini-items" data-mini-items aria-label="Order items"></ul>@endif
    <dl class="purchase-totals"><div @if($step === 1) hidden @endif><dt>Subtotal</dt><dd data-subtotal></dd></div><div><dt>Delivery</dt><dd data-delivery>{{ $step === 1 ? 'At checkout' : 'Select your area' }}</dd></div><div class="purchase-total"><dt>{{ $step === 1 ? 'Subtotal' : 'Total' }}</dt><dd data-total></dd></div></dl>
    @if($step === 1)
        <p class="purchase-muted">Delivery: BDT 80 inside Dhaka or BDT 150 outside Dhaka.</p>
        <a class="purchase-button" href="{{ url('/checkout') }}" data-checkout-link>Proceed to Checkout <span aria-hidden="true">&rarr;</span></a>
    @else
        <button class="purchase-button" type="submit" form="checkout-form" data-place-order disabled>Place Order <span aria-hidden="true">&rarr;</span></button>
        <p class="purchase-muted">Preview only. No payment is collected and no delivery is arranged.</p>
    @endif
    <p class="purchase-summary__foot">A little closer to everyday movement.</p>
</aside>
