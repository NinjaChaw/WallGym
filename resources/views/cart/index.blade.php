@extends('layouts.app')
@section('title', 'Your cart | WallGym')
@section('content')
<div class="purchase" data-purchase="cart" data-base="{{ url('/') }}"><div class="purchase-inner">
    @include('checkout._shared', ['step' => 1])
    <header class="purchase-intro"><p class="purchase-eyebrow">Make room for movement</p><h1>Your next <em>daily ritual.</em></h1><p>A few good things for your space. Review your selection below.</p></header>
    <div class="purchase-layout" data-filled hidden>
        <section aria-labelledby="cart-title"><div class="purchase-section-heading"><h2 id="cart-title">Your cart</h2><span data-count></span></div><ul class="purchase-cart-items" data-cart-items></ul><a class="purchase-text-link" href="{{ url('/shop') }}">&larr; Continue shopping</a></section>
        @include('checkout._summary', ['step' => 1])
    </div>
    <section class="purchase-empty purchase-card" data-empty hidden><span class="purchase-empty__mark" aria-hidden="true">W</span><h2>A little room for something good.</h2><p>Your cart is empty. Explore the collection and find your everyday essentials.</p><a class="purchase-button" href="{{ url('/shop') }}">Browse products</a><button class="purchase-text-link" type="button" data-reset>Load sample cart</button></section>
    <template id="cart-item"><li class="purchase-cart-item"><img data-image width="112" height="132" alt=""><div class="purchase-item-info"><a data-product-link></a><p data-unit-price></p><div class="purchase-quantity"><button type="button" data-minus>&minus;</button><span data-quantity></span><button type="button" data-plus>+</button></div><button type="button" class="purchase-remove" data-remove>Remove</button></div><strong data-line-total></strong></li></template>
</div></div>
@endsection
