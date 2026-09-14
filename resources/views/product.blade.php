@extends('layouts.app')

@php
    // Presentation data only; replace with verified catalog data when available.
    $catalog = [
        'swedish-wall' => [
            'name' => 'Swedish Wall', 'category' => 'Swedish walls', 'image' => 'images/hero/wallgym-interior', 'widths' => [640, 1120], 'height' => 1400,
            'tagline' => 'Your everyday place to move.',
            'description' => 'Make a little room for strength, stretching, and play. A Swedish wall brings movement into the spaces where everyday life happens.',
            'use' => 'Stretching, strength & movement', 'look' => 'Natural wood look',
            'fit' => 'Measure the available wall height and width, including clear space in front of the equipment. Exact dimensions and clearance requirements will be added with the final model specifications.',
            'setup' => 'Wall compatibility and fixings depend on the model and your wall construction. Review the manufacturer’s instructions and consult a qualified installer if you are unsure.',
            'included' => 'The final package contents will be listed here. Rings, mats, and accessories shown in the room concepts should not be assumed to be included.',
        ],
        'gymnastic-rings' => [
            'name' => 'Gymnastic Rings', 'category' => 'Accessories', 'image' => 'images/collection/gymnastic-rings', 'widths' => [480, 800], 'height' => 800,
            'tagline' => 'A fresh perspective on movement.',
            'description' => 'Explore a new part of your routine with gymnastic rings. A simple addition to a compatible setup, with room for your movement to grow.',
            'use' => 'Controlled strength exercises', 'look' => 'Wood & black strap look',
            'fit' => 'Ring dimensions, strap length, and adjustment details will be added with the final product specifications. Plan enough clear space around your setup.',
            'setup' => 'Use only a mounting point approved for the equipment and intended activity. Confirm compatibility, installation requirements, and load limits for the final model.',
            'included' => 'The final listing will confirm the ring pair, straps, and any fittings supplied. Other equipment in the imagery is not part of a confirmed package.',
        ],
        'exercise-mat' => [
            'name' => 'Exercise Mat', 'category' => 'Mats', 'image' => 'images/collection/exercise-mat', 'widths' => [480, 800], 'height' => 800,
            'tagline' => 'A little space to slow down.',
            'description' => 'Roll out a moment for yourself. Create a dedicated spot for floor exercises, a morning stretch, or a quiet reset between busy days.',
            'use' => 'Stretching & floor exercises', 'look' => 'Soft olive look',
            'fit' => 'Length, width, thickness, and material details will be added with the final product specifications. Choose a size that leaves room for your routine.',
            'setup' => 'Review the mat’s intended use and care instructions before choosing. An exercise mat is not a protective landing mat for climbing or falls.',
            'included' => 'The final listing will confirm the mat and any included accessories. Carry straps or bags are not confirmed at this stage.',
        ],
    ];
    $slug = request()->route('product') ?? 'swedish-wall';
    $item = $catalog[$slug];
    $gallery = [
        ['image' => $item['image'], 'widths' => $item['widths'], 'label' => 'Product view', 'alt' => $item['name'] . ' concept image'],
    ];
    if ($slug === 'swedish-wall') {
        $gallery[] = ['image' => 'images/living/wallgym-at-home', 'widths' => [640, 1120], 'label' => 'At home', 'alt' => 'Swedish wall concept in a sunlit living room'];
    }
@endphp

@section('title', $item['name'] . ' | WallGym')
@section('meta_description', $item['description'])

@push('styles')
    @vite('resources/css/pages/product.css')
@endpush
@push('scripts')
    @vite('resources/js/pages/product.js')
@endpush

@section('content')
    <div class="product-page" data-product-page>
        <div class="product-page__inner">
            <nav class="product-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ url('/') }}">Home</a><span aria-hidden="true">/</span>
                <a href="{{ url('/shop') }}">Shop</a><span aria-hidden="true">/</span>
                <span aria-current="page">{{ $item['name'] }}</span>
            </nav>

            <div class="product-layout">
                <section class="product-gallery" aria-label="Product images">
                    <div class="product-gallery__stage">
                        <img data-main-image src="{{ asset($item['image'] . '-' . $item['widths'][1] . '.jpg') }}"
                            srcset="{{ asset($item['image'] . '-' . $item['widths'][0] . '.jpg') }} {{ $item['widths'][0] }}w, {{ asset($item['image'] . '-' . $item['widths'][1] . '.jpg') }} {{ $item['widths'][1] }}w"
                            sizes="(min-width: 1280px) 620px, (min-width: 1024px) 52vw, calc(100vw - 40px)"
                            width="{{ $item['widths'][1] }}" height="{{ $item['height'] }}" fetchpriority="high" loading="eager" alt="{{ $gallery[0]['alt'] }}">
                        <button type="button" class="product-gallery__zoom" data-open-zoom aria-label="Enlarge product image" aria-haspopup="dialog" hidden>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><circle cx="10" cy="10" r="6"/><path d="m15 15 5 5M7 10h6m-3-3v6" stroke-linecap="round"/></svg>
                        </button>
                    </div>
                    <div class="product-gallery__footer">
                        <div class="product-gallery__thumbs" role="group" aria-label="Choose image" data-thumbnails hidden>
                            @foreach ($gallery as $photo)
                                <button type="button" class="product-thumbnail" data-gallery-image="{{ asset($photo['image'] . '-' . $photo['widths'][1] . '.jpg') }}" data-alt="{{ $photo['alt'] }}" data-label="{{ $photo['label'] }}" aria-label="Show {{ strtolower($photo['label']) }}" aria-pressed="{{ $loop->first ? 'true' : 'false' }}">
                                    <img src="{{ asset($photo['image'] . '-' . $photo['widths'][0] . '.jpg') }}" width="64" height="64" alt="" loading="lazy">
                                </button>
                            @endforeach
                        </div>
                        <p data-gallery-label aria-live="polite">Product view</p>
                    </div>
                </section>

                <section class="product-info" aria-labelledby="product-title">
                    <p class="product-eyebrow">{{ $item['category'] }}</p>
                    <h1 id="product-title">{{ $item['name'] }}</h1>
                    <p class="product-tagline">{{ $item['tagline'] }}</p>
                    <p class="product-price">Price coming soon</p>
                    <p class="product-description">{{ $item['description'] }}</p>

                    <dl class="product-highlights">
                        <div><dt>Make time for</dt><dd>{{ $item['use'] }}</dd></div>
                        <div><dt>Pictured style</dt><dd>{{ $item['look'] }}</dd></div>
                    </dl>

                    <div class="product-purchase">
                        <div class="product-quantity" data-quantity-control hidden>
                            <label for="product-quantity">Quantity</label>
                            <div class="product-quantity__stepper">
                                <button type="button" data-decrease aria-label="Decrease quantity" disabled>&minus;</button>
                                <input id="product-quantity" type="number" min="1" max="99" step="1" value="1" inputmode="numeric" aria-label="Quantity">
                                <button type="button" data-increase aria-label="Increase quantity">+</button>
                            </div>
                        </div>
                        <button class="product-add" type="button" disabled aria-describedby="ordering-note">Add to cart <span aria-hidden="true">&rarr;</span></button>
                        <p id="ordering-note" class="product-ordering-note">Pricing and online ordering are coming soon.</p>
                    </div>

                    <div class="product-accordions">
                        @foreach (['Size & fit' => $item['fit'], 'Setup & use' => $item['setup'], 'What’s included' => $item['included'], 'Delivery & returns' => 'Delivery options, costs, and return terms will be provided before online ordering opens.'] as $heading => $answer)
                            <details class="product-detail">
                                <summary><h2>{{ $heading }}</h2><span aria-hidden="true" class="product-detail__toggle"></span></summary>
                                <p>{{ $answer }}</p>
                            </details>
                        @endforeach
                    </div>
                    <a class="product-help-link" href="{{ url('/') }}#faq">Questions about your setup? <span>Read our FAQ &nearr;</span></a>
                </section>
            </div>

            <section class="product-related" aria-labelledby="related-title">
                <div class="product-related__heading"><div><p class="product-eyebrow">Keep exploring</p><h2 id="related-title">More ways to move.</h2></div><a href="{{ url('/shop') }}">View collection <span aria-hidden="true">&rarr;</span></a></div>
                <ul class="product-related__grid">
                    @foreach ($catalog as $relatedSlug => $related)
                        @continue($relatedSlug === $slug)
                        <li><a class="product-related__card" href="{{ url('/shop/' . $relatedSlug) }}">
                            <img src="{{ asset($related['image'] . '-' . $related['widths'][0] . '.jpg') }}" width="{{ $related['widths'][0] }}" height="{{ $relatedSlug === 'swedish-wall' ? 800 : $related['widths'][0] }}" loading="lazy" alt="">
                            <div><p class="product-eyebrow">{{ $related['category'] }}</p><h3>{{ $related['name'] }}</h3><p>Price coming soon</p></div>
                            <span class="product-related__arrow" aria-hidden="true">&nearr;</span>
                        </a></li>
                    @endforeach
                </ul>
            </section>
        </div>

        <dialog class="product-lightbox" data-lightbox aria-label="Enlarged product image">
            <button type="button" data-close-zoom aria-label="Close enlarged image" autofocus>&times;</button>
            <img data-zoom-image width="1120" height="1400" alt="">
        </dialog>
    </div>
@endsection
