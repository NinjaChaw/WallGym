@extends('layouts.app')
@section('title', 'Checkout | WallGym')
@section('content')
<div class="purchase" data-purchase="checkout" data-base="{{ url('/') }}"><div class="purchase-inner">
    @include('checkout._shared', ['step' => 2])
    <header class="purchase-intro"><p class="purchase-eyebrow">One step closer</p><h1>Movement, <em>on its way.</em></h1><p>Just the essentials. No account needed.</p></header>
    <div class="purchase-layout" data-filled hidden>
        <form id="checkout-form" class="purchase-form" novalidate>
            <section class="purchase-card" aria-labelledby="contact-title"><div class="purchase-section-heading"><h2 id="contact-title">Contact details</h2><span>01</span></div><div class="purchase-fields">
                <label class="purchase-field">Full name<input name="name" autocomplete="name" required maxlength="100" aria-describedby="error-name"><small id="error-name" data-error="name"></small></label>
                <label class="purchase-field">Phone number<input name="phone" type="tel" autocomplete="tel" required maxlength="30" placeholder="+880 1XXXXXXXXX" aria-describedby="error-phone"><small id="error-phone" data-error="phone"></small></label>
                <label class="purchase-field purchase-field--wide">Email <span>Optional</span><input name="email" type="email" autocomplete="email" maxlength="150" placeholder="you@example.com" aria-describedby="error-email"><small id="error-email" data-error="email"></small></label>
            </div></section>
            <section class="purchase-card" aria-labelledby="address-title"><div class="purchase-section-heading"><h2 id="address-title">Delivery address</h2><span>02</span></div><div class="purchase-fields">
                <label class="purchase-field purchase-field--wide">Street address<input name="address" autocomplete="street-address" required maxlength="250" placeholder="House, road, and apartment" aria-describedby="error-address"><small id="error-address" data-error="address"></small></label>
                <label class="purchase-field">Area / neighbourhood<input name="area" autocomplete="address-level3" required maxlength="100" aria-describedby="error-area"><small id="error-area" data-error="area"></small></label>
                <label class="purchase-field">City / district<input name="city" autocomplete="address-level2" required maxlength="100" aria-describedby="error-city"><small id="error-city" data-error="city"></small></label>
                <label class="purchase-field purchase-field--wide">Delivery zone<select name="zone" required aria-describedby="error-zone"><option value="">Choose your delivery zone</option><option value="dhaka">Inside Dhaka — BDT 80</option><option value="outside">Outside Dhaka — BDT 150</option></select><small id="error-zone" data-error="zone"></small></label>
                <label class="purchase-field purchase-field--wide">Delivery instructions <span>Optional</span><textarea name="notes" rows="3" maxlength="500" placeholder="A landmark or anything helpful for your delivery"></textarea></label>
            </div></section>
            <section class="purchase-card" aria-labelledby="payment-title"><div class="purchase-section-heading"><h2 id="payment-title">Payment</h2><span>03</span></div><label class="purchase-payment"><input type="radio" name="payment" value="cod" checked><span><strong>Cash on Delivery</strong><small>Pay when your order arrives.</small></span><span class="purchase-pill">COD</span></label></section>
            <a class="purchase-text-link" href="{{ url('/cart') }}">&larr; Back to cart</a>
        </form>
        @include('checkout._summary', ['step' => 2])
    </div>
    <section class="purchase-empty purchase-card" data-empty hidden><h2>Your cart is empty.</h2><p>Add items before continuing to checkout.</p><a class="purchase-button" href="{{ url('/cart') }}">Back to cart</a></section>
</div></div>
@endsection
