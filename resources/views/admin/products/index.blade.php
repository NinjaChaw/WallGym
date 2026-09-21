@extends('admin.layouts.app')
@section('title', 'Products')
@section('active_nav', 'products')
@section('eyebrow', 'Your collection')
@section('heading', 'Good products. Thoughtfully organized.')
@section('description', 'Manage the details that bring your WallGym collection to life.')
@section('actions')<a class="wg-admin-button wg-admin-button--primary" href="{{ route('admin.products.create') }}">+ Add product</a>@endsection
@section('content')
    @include('admin.products._workspace')
    <div class="category-stats" aria-label="Product summary"><div><span>Total products</span><strong>{{ $total }}</strong><small>Your collection</small></div><div><span>Active</span><strong>{{ $active }}</strong><small>Completed product details</small></div><div><span>Drafts</span><strong>{{ $total - $active }}</strong><small>Ready for your finishing touches</small></div></div>
    <section class="category-panel" aria-labelledby="product-list-title">
        <div class="category-panel__heading"><div><h2 id="product-list-title">All products</h2><p>A clear view of your growing collection.</p></div></div>
        <form class="category-toolbar" method="GET" action="{{ route('admin.products.index') }}">
            <label class="category-search"><span class="category-sr-only">Search products</span><input type="search" name="search" maxlength="100" value="{{ request('search') }}" placeholder="Search name, SKU, or slug"></label>
            <div class="category-toolbar__filters">
                <label>Category<select name="category_id"><option value="">All categories</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected((string) request('category_id') === (string) $category->id)>{{ $category->name }}</option>@endforeach</select></label>
                <label>Status<select name="status">@foreach(['all'=>'All statuses','active'=>'Active','draft'=>'Draft'] as $value=>$label)<option value="{{ $value }}" @selected(request('status', 'all') === $value)>{{ $label }}</option>@endforeach</select></label>
                <label>Sort<select name="sort"><option value="name" @selected(request('sort', 'name') === 'name')>Name: A to Z</option><option value="recent" @selected(request('sort') === 'recent')>Recently added</option></select></label>
                <button class="wg-admin-button" type="submit">Apply</button><a class="category-text-button" href="{{ route('admin.products.index') }}">Reset</a>
            </div>
        </form>
        <div class="ap-list-head" aria-hidden="true"><span>Product</span><span>Price</span><span>Inventory</span><span>Status</span><span></span></div>
        <ul class="ap-list" aria-label="Products">
            @forelse($products as $product)
                <li class="ap-row">
                    <div class="ap-row__identity"><div class="ap-thumbnail">@if($image = $product->images->first())<img src="{{ $image->url }}" width="56" height="56" alt="" loading="lazy">@else<span aria-hidden="true">W</span>@endif</div><div><a href="{{ route('admin.products.show', $product) }}">{{ $product->name }}</a><p>{{ $product->category->name }} &middot; {{ $product->sku ?? 'No SKU' }}</p></div></div>
                    <div class="ap-row__value"><small>Price</small>{{ $product->price !== null ? $product->currency.' '.number_format($product->price, 2) : 'Not set' }}</div>
                    <div class="ap-row__value"><small>Inventory</small>{{ $product->stock === null ? 'Not set' : ($product->stock === 0 ? 'Out of stock' : $product->stock.' in stock') }}</div>
                    <div class="ap-row__value"><small>Status</small><span class="category-badge" data-state="{{ $product->status }}">{{ ucfirst($product->status) }}</span></div>
                    <div class="ap-row__actions"><a href="{{ route('admin.products.show', $product) }}">View</a><a href="{{ route('admin.products.edit', $product) }}">Edit</a></div>
                </li>
            @empty
                <li class="category-empty"><h3>No products found.</h3><p>Add a product or try another search.</p><a class="wg-admin-button" href="{{ route('admin.products.index') }}">Clear filters</a></li>
            @endforelse
        </ul>
        <div class="category-list-footer"><p>Showing {{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }} of {{ $products->total() }} products</p><nav class="category-pagination" aria-label="Product pages">@if($products->previousPageUrl())<a class="wg-admin-button" href="{{ $products->previousPageUrl() }}">&larr; Previous</a>@endif<span>Page {{ $products->currentPage() }} of {{ $products->lastPage() }}</span>@if($products->nextPageUrl())<a class="wg-admin-button" href="{{ $products->nextPageUrl() }}">Next &rarr;</a>@endif</nav></div>
    </section>
@endsection
