@push('styles')
    @vite(['resources/css/admin/categories.css', 'resources/css/admin/orders.css'])
@endpush
@push('scripts')
    @vite('resources/js/admin/orders.js')
@endpush
@php
    // Fictional customers and amounts for frontend design. Money uses integer minor units.
    $wall = ['name' => 'Swedish Wall', 'sku' => 'WG-WALL-001', 'unitPrice' => 599900, 'quantity' => 1, 'image' => asset('images/hero/wallgym-interior-640.jpg')];
    $rings = ['name' => 'Gymnastic Rings', 'sku' => 'WG-RING-001', 'unitPrice' => 69900, 'quantity' => 1, 'image' => asset('images/collection/gymnastic-rings-480.jpg')];
    $mat = ['name' => 'Exercise Mat', 'sku' => 'WG-MAT-001', 'unitPrice' => 49900, 'quantity' => 1, 'image' => asset('images/collection/exercise-mat-480.jpg')];
    $orderSamples = [
        ['id' => '1048', 'date' => '2026-09-15', 'customer' => 'Alex Morgan', 'email' => 'alex.morgan@example.com', 'address' => "12 Example Street\nStockholm 111 22\nSweden", 'payment' => 'paid', 'fulfillment' => 'unfulfilled', 'currency' => 'SEK', 'items' => [$wall, array_merge($rings, ['quantity' => 2])], 'shipping' => 19900, 'discount' => 20000, 'carrier' => '', 'tracking' => ''],
        ['id' => '1047', 'date' => '2026-09-14', 'customer' => 'Jamie Taylor', 'email' => 'jamie.taylor@example.com', 'address' => "24 Sample Lane\nGothenburg 411 03\nSweden", 'payment' => 'paid', 'fulfillment' => 'shipped', 'currency' => 'SEK', 'items' => [$rings, $mat], 'shipping' => 7900, 'discount' => 0, 'carrier' => 'Sample carrier', 'tracking' => 'DEMO-1047'],
        ['id' => '1046', 'date' => '2026-09-13', 'customer' => 'Sam Parker', 'email' => 'sam.parker@example.com', 'address' => "8 Demo Avenue\nMalmo 211 20\nSweden", 'payment' => 'pending', 'fulfillment' => 'unfulfilled', 'currency' => 'SEK', 'items' => [$wall], 'shipping' => 19900, 'discount' => 0, 'carrier' => '', 'tracking' => ''],
        ['id' => '1045', 'date' => '2026-09-12', 'customer' => 'Robin Ellis', 'email' => 'robin.ellis@example.com', 'address' => "36 Example Road\nUppsala 753 20\nSweden", 'payment' => 'paid', 'fulfillment' => 'delivered', 'currency' => 'SEK', 'items' => [array_merge($mat, ['quantity' => 2])], 'shipping' => 7900, 'discount' => 0, 'carrier' => 'Sample carrier', 'tracking' => 'DEMO-1045'],
    ];
@endphp
<script type="application/json" id="admin-order-seed">@json($orderSamples)</script>
<div class="category-preview-note" data-admin-orders data-base-url="{{ url('/admin/orders') }}"><span class="category-preview-note__dot" aria-hidden="true"></span><p><strong>Sample orders.</strong> Customers, prices, and payments are fictional. Notes and fulfillment changes stay in this browser tab; no emails, shipments, or payments are triggered.</p></div>
<p class="category-feedback" data-feedback role="status" aria-live="polite" hidden></p>
