@extends('layouts.app')
@section('title', 'Shop Swedish Walls & Accessories | WallGym')
@section('meta_description', 'Explore Swedish walls, gymnastic rings, and exercise mats for movement at home.')
@push('styles') @vite('resources/css/pages/shop.css') @endpush
@push('scripts') @vite('resources/js/pages/shop.js') @endpush
@section('content')
<div class="shop-page" data-shop>
    <div class="shop-page__inner">
        <nav class="shop-breadcrumb" aria-label="Breadcrumb"><a href="{{ url('/') }}">Home</a><span aria-hidden="true">/</span><span aria-current="page">Shop</span></nav>
        <header class="shop-intro"><div><p class="shop-eyebrow">Movement starts here</p><h1>Find your way <span>to move.</span></h1></div><p class="shop-intro__description">Swedish walls and everyday essentials.<br>A little more movement, right at home.</p></header>
        <section class="shop-catalog" aria-label="Shop products">
            <form id="shop-filters" method="GET" action="{{ route('shop.index') }}">
                <div class="shop-controls">
                    <div class="shop-categories" role="group" aria-label="Product category">
                        <a class="shop-category" href="{{ route('shop.index', request()->only('search', 'sort')) }}" @if(!request('category')) aria-current="true" @endif>All products</a>
                        @foreach($categories as $category)<a class="shop-category" href="{{ route('shop.index', array_merge(request()->only('search', 'sort'), ['category' => $category->slug])) }}" @if(request('category') === $category->slug) aria-current="true" @endif>{{ $category->name }}</a>@endforeach
                    </div>
                    <div class="shop-search-group"><label class="shop-search"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><circle cx="10.5" cy="10.5" r="6.5"/><path d="m16 16 4 4"/></svg><span class="shop-sr-only">Search products</span><input name="search" type="search" value="{{ request('search') }}" maxlength="100" placeholder="Search the collection"></label><button class="shop-product__quick" name="category" value="{{ request('category') }}" type="submit">Search</button></div>
                </div>
                <div class="shop-results-bar"><p role="status">{{ $products->total() }} {{ $products->total() === 1 ? 'product' : 'products' }}</p><div class="shop-sort"><label for="shop-sort">Sort by</label><select id="shop-sort" name="sort">@foreach(['recent'=>'Newest', 'name-asc'=>'Name: A to Z', 'name-desc'=>'Name: Z to A'] as $value=>$label)<option value="{{ $value }}" @selected(request('sort', 'recent') === $value)>{{ $label }}</option>@endforeach</select><button class="shop-product__quick" name="category" value="{{ request('category') }}" type="submit">Apply</button></div></div>
            </form>
            @if($products->isNotEmpty())
                <ul class="shop-grid">
                    @foreach($products as $product)
                    <x-shop-product-card :product="$product" :cover="$covers->get($product->id)" :index="$loop->index" />
                    @endforeach
                </ul>
            @else
                <div class="shop-empty"><p class="shop-eyebrow">A fresh start</p><h2>No products found.</h2><p>Try a different search or check back soon for new additions.</p><a class="shop-reset" href="{{ route('shop.index') }}">Show all products <span aria-hidden="true">&rarr;</span></a></div>
            @endif
            @if($products->hasPages())
                <nav class="shop-pagination" aria-label="Product pages">@if($products->previousPageUrl())<a class="shop-category" href="{{ $products->previousPageUrl() }}">&larr; Previous</a>@endif<span>Page {{ $products->currentPage() }} of {{ $products->lastPage() }}</span>@if($products->nextPageUrl())<a class="shop-category" href="{{ $products->nextPageUrl() }}">Next &rarr;</a>@endif</nav>
            @endif
        </section>
        <aside class="shop-help" aria-label="Help choosing your equipment"><div><h2>A little help finding your fit?</h2><p>Start with space, wall compatibility, and what comes in the box.</p></div><a href="{{ url('/') }}#faq">A few useful answers <span aria-hidden="true">&nearr;</span></a></aside>
    </div>
    @foreach($products as $product)
        @php($cover = $covers->get($product->id))
        <dialog class="shop-dialog" id="quick-{{ $product->id }}" aria-labelledby="quick-title-{{ $product->id }}">
            <button class="shop-dialog__close" type="button" data-close-dialog aria-label="Close product preview" autofocus>&times;</button>
            <div class="shop-dialog__layout">
                @if($cover)<img src="{{ $cover->url }}" width="640" height="640" loading="lazy" alt="{{ $cover->alt_text ?? $product->name }}">@else<div class="shop-image-placeholder" role="img" aria-label="No image available">Image coming soon</div>@endif
                <div class="shop-dialog__copy"><p class="shop-eyebrow">{{ $product->category->name }}</p><h2 id="quick-title-{{ $product->id }}">{{ $product->name }}</h2><p class="shop-dialog__price">{{ $product->price !== null && $product->currency ? $product->currency.' '.number_format($product->price, 2) : 'Price unavailable' }}</p><p>{{ $product->description ?: 'More product details coming soon.' }}</p>
                    <div class="shop-dialog__consider"><h3>Product details</h3><p>{{ $product->stock === null ? 'Availability not confirmed' : ($product->stock > 0 ? 'In stock' : 'Out of stock') }}</p>@if($product->dimensions)<p>Dimensions: {{ $product->dimensions }}</p>@endif @if($product->material)<p>Material: {{ $product->material }}</p>@endif @if($product->sku)<p>SKU: {{ $product->sku }}</p>@endif</div>
                    <a href="{{ route('shop.show', $product->slug) }}" class="shop-dialog__continue">View product <span aria-hidden="true">&rarr;</span></a>
                </div>
            </div>
        </dialog>
    @endforeach
</div>
@endsection
