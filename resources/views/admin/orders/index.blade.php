@extends('admin.layouts.app')
@section('title', 'Orders')
@section('active_nav', 'orders')
@section('eyebrow', 'From your store to their home')
@section('heading', 'Every order, thoughtfully handled.')
@section('description', 'Keep track of purchases, payments, and the next delivery step.')
@section('content')
    @include('admin.orders._workspace')
    <div class="category-stats" aria-label="Order summary"><div><span>Total orders</span><strong>{{ $total }}</strong><small>All purchases</small></div><div><span>Paid</span><strong>{{ $paid }}</strong><small>Payment recorded</small></div><div><span>To fulfill</span><strong>{{ $toFulfill }}</strong><small>Awaiting dispatch, including COD</small></div></div>
    <section class="category-panel" aria-labelledby="order-list-title">
        <div class="category-panel__heading"><div><h2 id="order-list-title">Your orders</h2><p>The details you need, without the clutter.</p></div></div>
        <form class="category-toolbar" method="GET" action="{{ route('admin.orders.index') }}"><label class="category-search"><span class="category-sr-only">Search orders</span><input type="search" name="search" value="{{ request('search') }}" maxlength="100" placeholder="Order, customer, email, or phone"></label><div class="category-toolbar__filters">
            <label>Payment<select name="payment"><option value="">All payments</option>@foreach(['pending','paid','refunded'] as $state)<option value="{{ $state }}" @selected(request('payment') === $state)>{{ ucfirst($state) }}</option>@endforeach</select></label>
            <label>Status<select name="status"><option value="">All statuses</option>@foreach($statuses as $state)<option value="{{ $state }}" @selected(request('status') === $state)>{{ ucfirst($state) }}</option>@endforeach</select></label>
            <label>Sort<select name="sort"><option value="newest" @selected(request('sort', 'newest') === 'newest')>Newest first</option><option value="oldest" @selected(request('sort') === 'oldest')>Oldest first</option></select></label><button type="submit" class="wg-admin-button">Apply</button><a class="category-text-button" href="{{ route('admin.orders.index') }}">Reset</a>
        </div></form>
        <div class="ao-list-head" aria-hidden="true"><span>Order / customer</span><span>Date</span><span>Total</span><span>Payment</span><span>Status</span><span></span></div>
        <ul class="ao-list" aria-label="Orders">@forelse($orders as $order)
            <li class="ao-row"><div class="ao-row__identity"><a href="{{ route('admin.orders.show', $order) }}">{{ $order->order_number }}</a><p>{{ $order->customer_name }}<br>{{ $order->customer_phone }}</p></div><div class="ao-row__value"><small>Date</small>{{ $order->created_at->format('d M Y') }}</div><div class="ao-row__value"><small>Total</small><strong>{{ $order->currency }} {{ number_format($order->total, 2) }}</strong></div><div class="ao-row__value"><small>Payment</small><span class="ao-badge" data-state="{{ $order->payment_status }}">{{ ucfirst($order->payment_status) }}</span></div><div class="ao-row__value"><small>Status</small><span class="ao-badge" data-state="{{ $order->status }}">{{ ucfirst($order->status) }}</span></div><a class="ao-row__view" href="{{ route('admin.orders.show', $order) }}">View &nearr;</a></li>
        @empty<li class="category-empty"><h3>No orders found.</h3><p>New purchases will appear here. Try clearing your filters.</p><a class="wg-admin-button" href="{{ route('admin.orders.index') }}">Clear filters</a></li>@endforelse</ul>
        <div class="category-list-footer">Showing {{ $orders->firstItem() ?? 0 }}–{{ $orders->lastItem() ?? 0 }} of {{ $orders->total() }} orders<nav class="category-pagination" aria-label="Order pages">@if($orders->previousPageUrl())<a class="wg-admin-button" href="{{ $orders->previousPageUrl() }}">&larr; Previous</a>@endif<span>Page {{ $orders->currentPage() }} of {{ $orders->lastPage() }}</span>@if($orders->nextPageUrl())<a class="wg-admin-button" href="{{ $orders->nextPageUrl() }}">Next &rarr;</a>@endif</nav></div>
    </section>
@endsection
