@extends('admin.layouts.app')
@section('title', $product->name)
@section('active_nav', 'products')
@section('eyebrow', 'Your product at a glance')
@section('heading', 'Product overview.')
@section('description', 'Review and manage your product details.')
@section('breadcrumbs')<a href="{{ route('admin.products.index') }}">Products</a><span aria-hidden="true">/</span><span aria-current="page">Overview</span>@endsection
@section('actions')
    <a class="wg-admin-button" href="{{ route('admin.products.index') }}">&larr; All products</a>
    <a class="wg-admin-button wg-admin-button--primary" href="{{ route('admin.products.edit', $product) }}">Edit product &nearr;</a>
@endsection
@section('content')
    @include('admin.products._workspace')
    <div class="ap-show">
        <section class="category-panel ap-show__media" aria-label="Product images">
            <div class="ap-show__image">@if($cover = $product->images->first())<img src="{{ $cover->url }}" width="640" height="640" alt="{{ $cover->alt_text ?? $product->name }}">@else<span aria-hidden="true">W</span>@endif</div>
            <p>{{ $cover ? 'Cover image' : 'No images added yet' }}</p>
            @if($product->images->count() > 1)<div class="ap-gallery-thumbnails">@foreach($product->images as $image)<a href="{{ $image->url }}" target="_blank" rel="noopener" aria-label="Open product image {{ $loop->iteration }} in a new tab"><img src="{{ $image->url }}" width="100" height="100" alt="{{ $image->alt_text ?? $product->name }}" loading="lazy"></a>@endforeach</div>@endif
        </section>
        <section class="category-panel" aria-labelledby="ap-show-title"><div class="category-panel__heading"><p class="ap-show__category">{{ $product->category->name }}</p><span class="category-badge" data-state="{{ $product->status }}">{{ ucfirst($product->status) }}</span></div><div class="category-panel__body"><h2 id="ap-show-title" data-show-name>{{ $product->name }}</h2><p class="ap-show__price">{{ $product->price !== null ? $product->currency.' '.number_format($product->price, 2) : 'Price not set' }}</p><div class="ap-show__metrics"><div><span>Stock quantity</span><strong>{{ $product->stock ?? 'Not set' }}</strong></div><div><span>SKU</span><strong>{{ $product->sku ?? 'Not set' }}</strong></div></div><dl class="ap-specs"><div><dt>URL slug</dt><dd>{{ $product->slug }}</dd></div><div><dt>Dimensions</dt><dd>{{ $product->dimensions ?? 'Not set' }}</dd></div><div><dt>Material</dt><dd>{{ $product->material ?? 'Not set' }}</dd></div></dl></div></section>
        <section class="category-panel ap-show__description" aria-labelledby="ap-description-title"><div class="category-panel__heading"><h2 id="ap-description-title">About this product</h2></div><div class="category-panel__body"><p>{{ $product->description ?? 'No description added yet.' }}</p></div></section>
        <aside class="ap-review-note"><h2>Delete product</h2><p>This permanently removes the product and its images.</p><form method="POST" action="{{ route('admin.products.destroy', $product) }}" data-delete-product="{{ $product->name }}">@csrf @method('DELETE')<button class="wg-admin-button" type="submit">Delete product</button></form></aside>
    </div>
@endsection
