@push('styles')
    @vite('resources/css/pages/purchase.css')
@endpush
@push('scripts')
    @vite('resources/js/pages/purchase.js')
@endpush
<nav class="purchase-steps" aria-label="Shopping progress">
    <a href="{{ url('/cart') }}" @if($step === 1) aria-current="step" @endif><span>01</span> Cart</a><i aria-hidden="true"></i>
    <a href="{{ url('/checkout') }}" @if($step === 2) aria-current="step" @endif><span>02</span> Checkout</a><i aria-hidden="true"></i>
    <span @if($step === 3) aria-current="step" @endif><span>03</span> Complete</span>
</nav>
<p class="purchase-preview">Design preview · Sample products and BDT prices. No order or payment will be sent.</p>
<p class="purchase-feedback" data-feedback role="status" tabindex="-1" hidden></p>
<noscript><p class="purchase-feedback">Enable JavaScript to explore this shopping preview.</p></noscript>
