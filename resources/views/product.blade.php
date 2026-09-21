@extends('layouts.app')
@section('title', $product->name . ' | WallGym')
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($product->description ?: $product->name), 160))
@push('styles') @vite('resources/css/pages/product.css') @endpush
@push('scripts') @vite('resources/js/pages/product.js') @endpush
@section('content')
<div class="product-page" data-product-page>
    <div class="product-page__inner">
        <nav class="product-breadcrumb" aria-label="Breadcrumb"><a href="{{ url('/') }}">Home</a><span aria-hidden="true">/</span><a href="{{ route('shop.index') }}">Shop</a><span aria-hidden="true">/</span><span aria-current="page">{{ $product->name }}</span></nav>
        <div class="product-layout">
            <section class="product-gallery" aria-label="Product images">
                <div class="product-gallery__stage">
                    @if($cover = $gallery->first())
                        <img data-main-image src="{{ $cover->url }}" width="800" height="800" fetchpriority="high" loading="eager" alt="{{ $cover->alt_text ?: $product->name }}">
                        <button type="button" class="product-gallery__zoom" data-open-zoom aria-label="Enlarge product image" aria-haspopup="dialog" hidden><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><circle cx="10" cy="10" r="6"/><path d="m15 15 5 5M7 10h6m-3-3v6" stroke-linecap="round"/></svg></button>
                    @else
                        <div class="product-image-placeholder" role="img" aria-label="No image available">Image coming soon</div>
                    @endif
                </div>
                @if($gallery->isNotEmpty())
                    <div class="product-gallery__footer"><div class="product-gallery__thumbs" role="group" aria-label="Choose image" data-thumbnails hidden>
                        @foreach($gallery as $photo)
                            <button type="button" class="product-thumbnail" data-gallery-image="{{ $photo->url }}" data-alt="{{ $photo->alt_text ?: $product->name }}" data-label="Image {{ $loop->iteration }} of {{ $gallery->count() }}" aria-label="Show image {{ $loop->iteration }}" aria-pressed="{{ $loop->first ? 'true' : 'false' }}"><img src="{{ $photo->url }}" width="64" height="64" alt="" loading="lazy"></button>
                        @endforeach
                    </div><p data-gallery-label aria-live="polite">Image 1 of {{ $gallery->count() }}</p></div>
                @endif
            </section>
            <section class="product-info" aria-labelledby="product-title">
                <p class="product-eyebrow"><a href="{{ route('shop.index', ['category' => $product->category->slug]) }}">{{ $product->category->name }}</a></p>
                <h1 id="product-title">{{ $product->name }}</h1>
                <p class="product-tagline">{{ $product->stock === null ? 'Availability not confirmed' : ($product->stock > 0 ? 'In stock' : 'Out of stock') }}</p>
                <p class="product-price">{{ $product->price !== null && $product->currency ? $product->currency.' '.number_format($product->price, 2) : 'Price unavailable' }}</p>
                <p class="product-description">{{ $product->description ?: 'More product details coming soon.' }}</p>
                <dl class="product-highlights"><div><dt>Dimensions</dt><dd>{{ $product->dimensions ?: 'Not specified' }}</dd></div><div><dt>Material</dt><dd>{{ $product->material ?: 'Not specified' }}</dd></div>@if($product->sku)<div><dt>SKU</dt><dd>{{ $product->sku }}</dd></div>@endif</dl>
                @if($errors->any())<div class="product-cart-feedback" role="alert">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
                <form class="product-purchase" method="POST" action="{{ route('cart.store') }}">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    @php($canAdd = $product->stock > 0 && $product->price !== null && in_array($product->currency, ['BDT', 'SEK', 'EUR', 'USD'], true))
                    @if($canAdd)
                        <div class="product-quantity"><label for="product-quantity">Quantity</label><div class="product-quantity__stepper"><input id="product-quantity" name="quantity" type="number" min="1" max="{{ min(99, $product->stock) }}" step="1" value="{{ old('quantity', 1) }}" required inputmode="numeric"></div></div>
                    @endif
                    <button class="product-add" type="submit" @disabled(!$canAdd) aria-describedby="ordering-note">{{ $canAdd ? 'Add to cart' : ($product->stock === 0 ? 'Out of stock' : 'Currently unavailable') }} <span aria-hidden="true">&rarr;</span></button>
                    <p id="ordering-note" class="product-ordering-note">{{ $canAdd ? 'Save your selection in your cart and review it at checkout.' : 'This product cannot be added to the cart right now.' }}</p>
                </form>
                <a class="product-help-link" href="{{ url('/') }}#faq">Questions about your setup? <span>Read our FAQ &nearr;</span></a>
            </section>
        </div>
        @if($relatedProducts->isNotEmpty())
            <section class="product-related" aria-labelledby="related-title"><div class="product-related__heading"><div><p class="product-eyebrow">Keep exploring</p><h2 id="related-title">More ways to move.</h2></div><a href="{{ route('shop.index') }}">View collection <span aria-hidden="true">&rarr;</span></a></div>
                <ul class="product-related__grid">@foreach($relatedProducts as $related)
                    <li><a class="product-related__card" href="{{ route('shop.show', $related->slug) }}">
                        @if($image = $relatedCovers->get($related->id))<img src="{{ $image->url }}" width="110" height="110" loading="lazy" alt="">@else<span class="product-related__placeholder" aria-label="No image available">No image</span>@endif
                        <div><p class="product-eyebrow">{{ $related->category->name }}</p><h3>{{ $related->name }}</h3><p>{{ $related->price !== null && $related->currency ? $related->currency.' '.number_format($related->price, 2) : 'Price unavailable' }}</p></div><span class="product-related__arrow" aria-hidden="true">&nearr;</span>
                    </a></li>
                @endforeach</ul>
            </section>
        @endif
    </div>
    @if($gallery->isNotEmpty())<dialog class="product-lightbox" data-lightbox aria-label="Enlarged product image"><button type="button" data-close-zoom aria-label="Close enlarged image" autofocus>&times;</button><img data-zoom-image width="800" height="800" alt=""></dialog>@endif
</div>
@endsection
