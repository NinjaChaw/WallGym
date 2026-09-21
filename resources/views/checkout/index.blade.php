@extends('layouts.app')
@section('title', 'Checkout | WallGym')
@push('styles') @vite('resources/css/pages/purchase.css') @endpush
@push('scripts') @vite('resources/js/pages/purchase.js') @endpush
@section('content')
<div class="purchase" data-checkout-real data-quote-url="{{ route('checkout.quote') }}"><div class="purchase-inner">
    <nav class="purchase-steps" aria-label="Shopping progress"><a href="{{ route('cart.index') }}"><span>01</span> Cart</a><i aria-hidden="true"></i><span aria-current="step"><span>02</span> Checkout</span><i aria-hidden="true"></i><span><span>03</span> Complete</span></nav>
    @if(session('success'))<p class="purchase-feedback" role="status">{{ session('success') }}</p>@endif
    @foreach($notices as $notice)<p class="purchase-feedback" role="status">{{ $notice }}</p>@endforeach
    @if($errors->any())<div class="purchase-feedback" data-error role="alert"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <p class="purchase-feedback" data-quote-error role="status" hidden></p>
    <header class="purchase-intro"><p class="purchase-eyebrow">One step closer</p><h1>Movement, <em>on its way.</em></h1><p>Just the essentials. No account needed.</p></header>
    <div class="purchase-layout">
        <form id="checkout-form" class="purchase-form" method="POST" action="{{ route('checkout.store') }}">
            @csrf
            <input type="hidden" name="checkout_token" value="{{ session('checkout.token') }}">
            <section class="purchase-card" aria-labelledby="contact-title"><div class="purchase-section-heading"><h2 id="contact-title">Contact details</h2><span>01</span></div><div class="purchase-fields">
                @foreach(['name'=>['Full name','text','name',100], 'phone'=>['Phone number','tel','tel',30], 'email'=>['Email','email','email',150]] as $field=>$settings)
                    <label class="purchase-field {{ $field === 'email' ? 'purchase-field--wide' : '' }}">{{ $settings[0] }} @if($field === 'email')<span>Optional</span>@endif<input name="{{ $field }}" type="{{ $settings[1] }}" autocomplete="{{ $settings[2] }}" maxlength="{{ $settings[3] }}" value="{{ old($field, $customer[$field] ?? '') }}" @required($field !== 'email') aria-describedby="error-{{ $field }}" aria-invalid="{{ $errors->has($field) ? 'true' : 'false' }}"><small id="error-{{ $field }}">@error($field){{ $message }}@enderror</small></label>
                @endforeach
            </div></section>
            <section class="purchase-card" aria-labelledby="address-title"><div class="purchase-section-heading"><h2 id="address-title">Delivery address</h2><span>02</span></div><div class="purchase-fields">
                @foreach(['address'=>['Street address','street-address',250], 'area'=>['Area / neighbourhood','address-level3',100], 'city'=>['City / district','address-level2',100]] as $field=>$settings)
                    <label class="purchase-field {{ $field === 'address' ? 'purchase-field--wide' : '' }}">{{ $settings[0] }}<input name="{{ $field }}" autocomplete="{{ $settings[1] }}" required maxlength="{{ $settings[2] }}" value="{{ old($field, $customer[$field] ?? '') }}" aria-describedby="error-{{ $field }}" aria-invalid="{{ $errors->has($field) ? 'true' : 'false' }}"><small id="error-{{ $field }}">@error($field){{ $message }}@enderror</small></label>
                @endforeach
                <label class="purchase-field purchase-field--wide">Delivery zone<select name="zone" required aria-describedby="error-zone" aria-invalid="{{ $errors->has('zone') ? 'true' : 'false' }}"><option value="">Choose your delivery zone</option>@foreach($deliveryRates as $zone=>$amount)<option value="{{ $zone }}" @selected(old('zone', $customer['zone'] ?? '') === $zone)>{{ $zone === 'dhaka' ? 'Inside Dhaka' : 'Outside Dhaka' }} — BDT {{ number_format($amount / 100, 2) }}</option>@endforeach</select><small id="error-zone">@error('zone'){{ $message }}@enderror</small></label>
                <label class="purchase-field purchase-field--wide">Delivery instructions <span>Optional</span><textarea name="notes" rows="3" maxlength="500" placeholder="A landmark or anything helpful for your delivery" aria-describedby="error-notes">{{ old('notes', $customer['notes'] ?? '') }}</textarea><small id="error-notes">@error('notes'){{ $message }}@enderror</small></label>
            </div></section>
            <section class="purchase-card" aria-labelledby="payment-title"><div class="purchase-section-heading"><h2 id="payment-title">Payment</h2><span>03</span></div><label class="purchase-payment"><input type="radio" name="payment" value="cod" required @checked(old('payment', $customer['payment'] ?? 'cod') === 'cod')><span><strong>Cash on Delivery</strong><small>Pay when your order arrives.</small></span><span class="purchase-pill">COD</span></label>@error('payment')<p class="purchase-feedback" data-error>{{ $message }}</p>@enderror</section>
            <a class="purchase-text-link" href="{{ route('cart.index') }}">&larr; Back to cart</a>
        </form>
        @include('checkout._summary')
    </div>
</div></div>
@endsection
