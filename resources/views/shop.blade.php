@extends('layouts.app')

@section('title', 'Shop Swedish Walls & Accessories | WallGym')
@section('meta_description', 'Explore Swedish walls, gymnastic rings, and exercise mats for movement at home.')

@push('styles')
    @vite('resources/css/pages/shop.css')
@endpush

@push('scripts')
    @vite('resources/js/pages/shop.js')
@endpush

@section('content')
    {{-- Front-end catalog examples. Replace with verified products and prices later. --}}
    @php
        $products = [
            ['id' => 'swedish-wall', 'name' => 'Swedish Wall', 'category' => 'walls', 'label' => 'Swedish walls', 'detail' => 'A place for everyday movement.', 'image' => 'images/hero/wallgym-interior', 'widths' => [640, 1120], 'height' => 1400, 'portrait' => true, 'price' => null,
                'description' => 'Bring stretching, strength, and play into your everyday space with a wall-mounted setup.', 'consider' => 'Check the dimensions, required clearance, wall compatibility, and included accessories for your chosen model.'],
            ['id' => 'gymnastic-rings', 'name' => 'Gymnastic Rings', 'category' => 'accessories', 'label' => 'Accessories', 'detail' => 'A new way to move with your wall.', 'image' => 'images/collection/gymnastic-rings', 'widths' => [480, 800], 'height' => 800, 'portrait' => false, 'price' => null,
                'description' => 'Explore a different kind of movement with a pair of gymnastic rings for your home setup.', 'consider' => 'Confirm the mounting requirements, strap details, and compatibility with your equipment before choosing.'],
            ['id' => 'exercise-mat', 'name' => 'Exercise Mat', 'category' => 'mats', 'label' => 'Mats', 'detail' => 'Make time for a stretch and a reset.', 'image' => 'images/collection/exercise-mat', 'widths' => [480, 800], 'height' => 800, 'portrait' => false, 'price' => null,
                'description' => 'Create a dedicated spot for stretching and floor exercises with a mat that fits your routine.', 'consider' => 'Review the size, thickness, and intended use. An exercise mat is not a substitute for a protective landing mat.'],
        ];
    @endphp

    <div class="shop-page" data-shop>
        <div class="shop-page__inner">
            <nav class="shop-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ url('/') }}">Home</a><span aria-hidden="true">/</span><span aria-current="page">Shop</span>
            </nav>

            <header class="shop-intro">
                <div>
                    <p class="shop-eyebrow">Movement starts here</p>
                    <h1>Find your way <span>to move.</span></h1>
                </div>
                <p class="shop-intro__description">Swedish walls and everyday essentials.<br>A little more movement, right at home.</p>
            </header>

            <section class="shop-catalog" aria-label="Shop products">
                <div class="shop-controls" data-shop-controls hidden>
                    <div class="shop-categories" role="group" aria-label="Product category">
                        @foreach (['all' => 'All products', 'walls' => 'Swedish walls', 'accessories' => 'Accessories', 'mats' => 'Mats'] as $key => $label)
                            <button type="button" class="shop-category" data-category="{{ $key }}" aria-pressed="{{ $key === 'all' ? 'true' : 'false' }}">{{ $label }}</button>
                        @endforeach
                    </div>
                    <label class="shop-search">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><circle cx="10.5" cy="10.5" r="6.5"/><path d="m16 16 4 4" stroke-linecap="round"/></svg>
                        <span class="shop-sr-only">Search products</span>
                        <input type="search" data-shop-search placeholder="Search the collection" autocomplete="off">
                    </label>
                </div>

                <div class="shop-results-bar">
                    <p data-shop-count role="status" aria-live="polite" aria-atomic="true">{{ count($products) }} products</p>
                    <label class="shop-sort" data-shop-sort-control hidden>Sort by
                        <select data-shop-sort aria-label="Sort products">
                            <option value="featured">Featured</option>
                            <option value="name-asc">Name: A to Z</option>
                            <option value="name-desc">Name: Z to A</option>
                        </select>
                    </label>
                </div>

                <ul class="shop-grid" data-shop-grid>
                    @foreach ($products as $product)
                        <x-shop-product-card :product="$product" :index="$loop->index" />
                    @endforeach
                </ul>

                <div class="shop-empty" data-shop-empty hidden>
                    <p class="shop-eyebrow">A fresh start</p>
                    <h2>No products found.</h2>
                    <p>Try a different search or explore the full collection.</p>
                    <button type="button" class="shop-reset" data-shop-reset>Show all products <span aria-hidden="true">&rarr;</span></button>
                </div>
            </section>

            <aside class="shop-help" aria-label="Help choosing your equipment">
                <div><h2>A little help finding your fit?</h2><p>Start with space, wall compatibility, and what comes in the box.</p></div>
                <a href="{{ url('/') }}#faq">A few useful answers <span aria-hidden="true">&nearr;</span></a>
            </aside>
        </div>

        @foreach ($products as $product)
            <dialog class="shop-dialog" id="quick-{{ $product['id'] }}" aria-labelledby="quick-title-{{ $product['id'] }}">
                <button class="shop-dialog__close" type="button" data-close-dialog aria-label="Close product preview" autofocus>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="m6 6 12 12M6 18 18 6" stroke-linecap="round"/></svg>
                </button>
                <div class="shop-dialog__layout">
                    <img src="{{ asset($product['image'] . '-' . $product['widths'][0] . '.jpg') }}" width="{{ $product['widths'][0] }}" height="{{ $product['portrait'] ? 800 : $product['widths'][0] }}" loading="lazy" alt="{{ $product['name'] }} concept image">
                    <div class="shop-dialog__copy">
                        <p class="shop-eyebrow">{{ $product['label'] }}</p>
                        <h2 id="quick-title-{{ $product['id'] }}">{{ $product['name'] }}</h2>
                        <p class="shop-dialog__price">{{ $product['price'] ?? 'Price coming soon' }}</p>
                        <p>{{ $product['description'] }}</p>
                        <div class="shop-dialog__consider"><h3>Before you choose</h3><p>{{ $product['consider'] }}</p></div>
                        <a href="{{ url('/shop/' . $product['id']) }}" class="shop-dialog__continue">View product <span aria-hidden="true">&rarr;</span></a>
                    </div>
                </div>
            </dialog>
        @endforeach
    </div>

@endsection
