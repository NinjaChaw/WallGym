@extends('layouts.app')
@section('title', 'Your cart | WallGym')
@push('styles') @vite('resources/css/pages/purchase.css') @endpush
@section('content')
<div class="purchase"><div class="purchase-inner">
    <nav class="purchase-steps" aria-label="Shopping progress"><span aria-current="step"><span>01</span> Cart</span><i aria-hidden="true"></i><span><span>02</span> Checkout</span><i aria-hidden="true"></i><span><span>03</span> Complete</span></nav>
    @if(session('success'))<p class="purchase-feedback" role="status">{{ session('success') }}</p>@endif
    @foreach($notices as $notice)<p class="purchase-feedback" role="status">{{ $notice }}</p>@endforeach
    @if($errors->any())<div class="purchase-feedback" data-error role="alert"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <header class="purchase-intro"><p class="purchase-eyebrow">Make room for movement</p><h1>Your next <em>daily ritual.</em></h1><p>A few good things for your space. Review your selection below.</p></header>
    @if(count($items))
        <div class="purchase-layout">
            <section aria-labelledby="cart-title"><div class="purchase-section-heading"><h2 id="cart-title">Your cart</h2><span>{{ $count }} {{ $count === 1 ? 'item' : 'items' }}</span></div>
                <ul class="purchase-cart-items">
                    @foreach($items as $item)
                        @php($product = $item['product'])
                        <li class="purchase-cart-item">
                            @if($item['cover'])<img src="{{ $item['cover']->url }}" width="112" height="132" alt="{{ $item['cover']->alt_text ?: $product->name }}" loading="lazy">@else<span class="cart-image-placeholder" role="img" aria-label="No image available">No image</span>@endif
                            <div class="purchase-item-info"><a href="{{ route('shop.show', $product->slug) }}">{{ $product->name }}</a><p>{{ $product->currency }} {{ number_format($item['unit'] / 100, 2) }} each</p>
                                <form method="POST" action="{{ route('cart.update', $product) }}" class="cart-quantity-form">@csrf @method('PATCH')
                                    <label class="cart-quantity-label" for="quantity-{{ $product->id }}">Quantity for {{ $product->name }}</label>
                                    <input id="quantity-{{ $product->id }}" type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" max="{{ min(99, $product->stock) }}" step="1" required inputmode="numeric">
                                    <button type="submit" class="purchase-text-link">Update</button>
                                </form>
                                <form method="POST" action="{{ route('cart.destroy', $product->id) }}">@csrf @method('DELETE')<button type="submit" class="purchase-remove" aria-label="Remove {{ $product->name }}">Remove</button></form>
                            </div>
                            <strong>{{ $product->currency }} {{ number_format($item['line'] / 100, 2) }}</strong>
                        </li>
                    @endforeach
                </ul>
                <div class="cart-footer"><a class="purchase-text-link" href="{{ route('shop.index') }}">&larr; Continue shopping</a><form method="POST" action="{{ route('cart.clear') }}">@csrf @method('DELETE')<button class="purchase-text-link" type="submit">Clear cart</button></form></div>
            </section>
            <aside class="purchase-summary purchase-card" aria-labelledby="summary-title"><h2 id="summary-title">Your order</h2><dl class="purchase-totals"><div><dt>Delivery</dt><dd>Calculated at checkout</dd></div>@foreach($totals as $currency => $amount)<div class="purchase-total"><dt>Subtotal{{ count($totals) > 1 ? ' ('.$currency.')' : '' }}</dt><dd>{{ $currency }} {{ number_format($amount / 100, 2) }}</dd></div>@endforeach</dl>
                @if(count($totals) > 1)<p class="purchase-muted">Subtotals are shown separately for each currency. No currency conversion is applied.</p>@endif
                <p class="purchase-muted">Prices reflect the current catalog. Stock is not reserved until an order is placed.</p><a class="purchase-button" href="{{ route('checkout.index') }}">Proceed to Checkout &rarr;</a><p class="purchase-muted">Your cart is saved in this browsing session.</p><p class="purchase-summary__foot">A little closer to everyday movement.</p>
            </aside>
        </div>
    @else
        <section class="purchase-empty purchase-card"><span class="purchase-empty__mark" aria-hidden="true">W</span><h2>A little room for something good.</h2><p>Your cart is empty. Explore the collection and find your everyday essentials.</p><a class="purchase-button" href="{{ route('shop.index') }}">Browse products</a></section>
    @endif
</div></div>
@endsection
