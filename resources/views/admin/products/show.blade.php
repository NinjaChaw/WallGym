@extends('admin.layouts.app')
@section('title', 'Product overview')
@section('active_nav', 'products')
@section('eyebrow', 'Your product at a glance')
@section('heading', 'Product overview.')
@section('description', 'Review the details before taking the next step.')
@section('breadcrumbs')<a href="{{ url('/admin/products') }}">Products</a><span aria-hidden="true">/</span><span aria-current="page">Overview</span>@endsection
@section('actions')<a class="wg-admin-button" href="{{ url('/admin/products') }}">&larr; All products</a><a class="wg-admin-button wg-admin-button--primary" data-edit-product hidden>Edit product &nearr;</a>@endsection
@section('content')
    @include('admin.products._workspace')
    <div class="category-panel category-not-found" data-not-found hidden><h2>Product not found.</h2><p>This preview may belong to another browser tab.</p><a class="wg-admin-button" href="{{ url('/admin/products') }}">Back to products</a></div>
    <div class="ap-show" data-product-show data-id="{{ request()->route('product') }}" hidden>
        <section class="category-panel ap-show__media" aria-label="Product cover"><div class="ap-show__image"><img data-show-image width="640" height="640" alt="" hidden><span data-show-placeholder aria-hidden="true">W</span></div><p>Cover image &middot; Preview catalog</p></section>
        <section class="category-panel" aria-labelledby="ap-show-title"><div class="category-panel__heading"><p class="ap-show__category" data-show-category></p><span class="category-badge" data-show-status></span></div><div class="category-panel__body"><h2 id="ap-show-title" data-show-name></h2><p class="ap-show__price" data-show-price></p><div class="ap-show__metrics"><div><span>Stock quantity</span><strong data-show-stock></strong></div><div><span>SKU</span><strong data-show-sku></strong></div></div><dl class="ap-specs"><div><dt>URL slug</dt><dd data-show-slug></dd></div><div><dt>Dimensions</dt><dd data-show-dimensions></dd></div><div><dt>Material</dt><dd data-show-material></dd></div></dl></div></section>
        <section class="category-panel ap-show__description" aria-labelledby="ap-description-title"><div class="category-panel__heading"><h2 id="ap-description-title">About this product</h2></div><div class="category-panel__body"><p data-show-description></p></div></section>
        <aside class="ap-review-note"><p class="wg-admin-eyebrow">Before you go live</p><h2>A few final checks.</h2><p>Confirm the price, stock, dimensions, and image match your actual product. These previews do not publish to your store.</p></aside>
    </div>
    <noscript><p class="category-notice">Enable JavaScript to view saved product previews.</p></noscript>
@endsection
