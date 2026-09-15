@extends('admin.layouts.app')
@section('title', 'Order details')
@section('active_nav', 'orders')
@section('eyebrow', 'The details behind the delivery')
@section('heading', 'Order details.')
@section('description', 'Everything you need to review this order in one place.')
@section('breadcrumbs')<a href="{{ url('/admin/orders') }}">Orders</a><span aria-hidden="true">/</span><span aria-current="page">Details</span>@endsection
@section('actions')<a class="wg-admin-button" href="{{ url('/admin/orders') }}">&larr; All orders</a><button type="button" class="wg-admin-button wg-admin-button--primary" data-open-update aria-haspopup="dialog" hidden>Update fulfillment</button>@endsection
@section('content')
    @include('admin.orders._workspace')
    <div class="category-panel category-not-found" data-not-found hidden><h2>Order not found.</h2><p>This sample order does not exist in the preview.</p><a class="wg-admin-button" href="{{ url('/admin/orders') }}">Back to orders</a></div>
    <div data-order-show data-id="{{ request()->route('order') }}" hidden>
        <div class="ao-order-meta"><p data-placed></p><span class="ao-badge" data-payment-badge></span><span class="ao-badge" data-fulfillment-badge></span></div>
        <div class="ao-detail-grid">
            <div class="ao-detail-main">
                <section class="category-panel" aria-labelledby="order-items-title"><div class="category-panel__heading"><div><h2 id="order-items-title">Order items</h2><p data-item-count></p></div><span class="category-small-note">Sample purchase</span></div><ul class="ao-items" data-items></ul></section>
                <section class="category-panel" aria-labelledby="payment-title"><div class="category-panel__heading"><h2 id="payment-title">Payment summary</h2><span class="ao-badge" data-summary-payment></span></div><div class="category-panel__body"><dl class="ao-totals"><div><dt>Subtotal</dt><dd data-subtotal></dd></div><div><dt>Discount</dt><dd data-discount></dd></div><div><dt>Shipping</dt><dd data-shipping></dd></div><div class="ao-totals__total"><dt>Order total</dt><dd data-grand-total></dd></div></dl><p class="ao-small-note">Illustrative amounts only. No live payment or tax calculation is connected.</p></div></section>
                <section class="category-panel" aria-labelledby="activity-title"><div class="category-panel__heading"><div><h2 id="activity-title">Order activity</h2><p>A record of this preview&rsquo;s updates.</p></div></div><div class="category-panel__body"><form class="ao-note-form" data-note-form><label for="order-note">Internal note</label><textarea id="order-note" name="note" rows="3" maxlength="500" required placeholder="Add a useful detail for your team..." aria-describedby="order-note-help"></textarea><div><small id="order-note-help">Saved in this tab only. Never sent to the customer.</small><button class="wg-admin-button" type="submit">Add note</button></div></form><ol class="ao-timeline" data-timeline aria-label="Order history"></ol></div></section>
            </div>
            <aside class="ao-detail-side" aria-label="Customer and delivery details">
                <section class="category-panel" aria-labelledby="customer-title"><div class="category-panel__heading"><h2 id="customer-title">Customer</h2><span class="category-small-note">Sample</span></div><div class="category-panel__body"><div class="ao-customer"><span data-initials aria-hidden="true"></span><div><h3 data-customer-name></h3><p data-customer-email></p></div></div><div class="ao-address"><h3>Shipping address</h3><p data-address></p></div></div></section>
                <section class="category-panel" aria-labelledby="delivery-title"><div class="category-panel__heading"><h2 id="delivery-title">Fulfillment</h2></div><div class="category-panel__body"><span class="ao-badge" data-delivery-status></span><dl class="ao-delivery"><div><dt>Carrier</dt><dd data-carrier></dd></div><div><dt>Tracking reference</dt><dd data-tracking></dd></div></dl><p class="ao-small-note" data-delivery-hint></p></div></section>
                <div class="ao-preview-card"><p class="wg-admin-eyebrow">A thoughtful handover</p><h2>Clear details.<br>Smoother deliveries.</h2><p>Check the items, payment, and address before progressing an order.</p></div>
            </aside>
        </div>
    </div>
    <template id="order-item-template"><li class="ao-item"><img width="56" height="64" alt=""><div class="ao-item__identity"><h3 data-item-name></h3><p data-item-sku></p><small data-item-price></small></div><span class="ao-item__quantity" data-item-quantity></span><strong data-item-total></strong></li></template>
    <dialog class="ao-dialog" data-update-dialog aria-labelledby="update-title">
        <form data-update-form><div class="ao-dialog__heading"><div><p class="wg-admin-eyebrow">Order progress</p><h2 id="update-title">Update fulfillment</h2></div><button type="button" data-close-update class="ao-close" aria-label="Close fulfillment editor">&times;</button></div><p class="ao-small-note">This changes the preview only. No shipment or customer notification is created.</p><p class="ao-dialog-error" role="alert" data-dialog-error hidden></p>
            <label class="category-field" for="order-fulfillment">Fulfillment status<select id="order-fulfillment" name="fulfillment"><option value="unfulfilled">Unfulfilled</option><option value="processing">Processing</option><option value="shipped">Shipped</option><option value="delivered">Delivered</option></select></label>
            <label class="category-field" for="order-carrier">Carrier <span class="category-optional">Optional</span><input id="order-carrier" name="carrier" maxlength="80" placeholder="Carrier name" autocomplete="off"></label>
            <label class="category-field" for="order-tracking">Tracking reference <span class="category-optional">Optional</span><input id="order-tracking" name="tracking" maxlength="100" placeholder="Tracking number" autocomplete="off"></label>
            <div class="ao-dialog__actions"><button type="button" data-close-update class="wg-admin-button">Cancel</button><button type="submit" class="wg-admin-button wg-admin-button--primary">Save preview</button></div>
        </form>
    </dialog>
    <noscript><p class="category-notice">Enable JavaScript to view the sample order details.</p></noscript>
@endsection
