@extends('admin.layouts.app')
@section('title', 'Orders')
@section('active_nav', 'orders')
@section('eyebrow', 'From your store to their home')
@section('heading', 'Every order, thoughtfully handled.')
@section('description', 'Keep track of purchases, payments, and the next delivery step.')
@section('content')
    @include('admin.orders._workspace')
    <div data-orders-index>
        <div class="category-stats" aria-label="Sample order summary"><div><span>Total orders</span><strong data-total>4</strong><small>In this sample workspace</small></div><div><span>Paid</span><strong data-paid>3</strong><small>Sample payment records</small></div><div><span>To fulfill</span><strong data-to-fulfill>1</strong><small>Paid and awaiting dispatch</small></div></div>
        <section class="category-panel" aria-labelledby="order-list-title">
            <div class="category-panel__heading"><div><h2 id="order-list-title">Your orders</h2><p>The details you need, without the clutter.</p></div><span class="category-small-note">Sample orders</span></div>
            <div class="category-toolbar" data-tools hidden>
                <label class="category-search"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><circle cx="10.5" cy="10.5" r="6.5"/><path d="m16 16 4 4"/></svg><span class="category-sr-only">Search orders</span><input type="search" data-search placeholder="Search order, customer, or email" autocomplete="off"></label>
                <div class="category-toolbar__filters"><label>Payment<select data-payment-filter><option value="all">All payments</option><option value="paid">Paid</option><option value="pending">Pending</option></select></label><label>Fulfillment<select data-fulfillment-filter><option value="all">All statuses</option><option value="unfulfilled">Unfulfilled</option><option value="processing">Processing</option><option value="shipped">Shipped</option><option value="delivered">Delivered</option></select></label><label>Sort<select data-sort><option value="newest">Newest first</option><option value="oldest">Oldest first</option><option value="total">Highest total</option></select></label></div>
            </div>
            <div class="ao-list-head" aria-hidden="true"><span>Order / customer</span><span>Date</span><span>Total</span><span>Payment</span><span>Fulfillment</span><span></span></div>
            <ul class="ao-list" data-order-list aria-label="Orders"></ul>
            <div class="category-empty" data-empty hidden><h3>No matching orders.</h3><p>Try another search or clear your filters.</p><button class="wg-admin-button" type="button" data-reset>Clear filters</button></div>
            <p class="category-list-footer" data-count role="status" aria-live="polite"></p>
            <noscript><p class="category-notice">Enable JavaScript to explore the sample orders.</p></noscript>
        </section>
    </div>
    <template id="order-row-template"><li class="ao-row"><div class="ao-row__identity"><a data-order-link></a><p data-customer></p></div><div class="ao-row__value"><small>Date</small><span data-date></span></div><div class="ao-row__value"><small>Total</small><strong data-total></strong></div><div class="ao-row__value"><small>Payment</small><span class="ao-badge" data-payment></span></div><div class="ao-row__value"><small>Fulfillment</small><span class="ao-badge" data-fulfillment></span></div><a class="ao-row__view" data-view>View <span aria-hidden="true">&nearr;</span></a></li></template>
@endsection
