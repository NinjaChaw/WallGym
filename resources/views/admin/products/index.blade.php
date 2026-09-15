@extends('admin.layouts.app')
@section('title', 'Products')
@section('active_nav', 'products')
@section('eyebrow', 'Your collection')
@section('heading', 'Good products. Thoughtfully organized.')
@section('description', 'Manage the details that bring your WallGym collection to life.')
@section('actions')
    <a class="wg-admin-button wg-admin-button--primary" href="{{ url('/admin/products/create') }}"><span aria-hidden="true">+</span> Add product</a>
@endsection
@section('content')
    @include('admin.products._workspace')
    <div data-products-index>
        <div class="category-stats" aria-label="Product summary"><div><span>Total products</span><strong data-total>3</strong><small>Your preview collection</small></div><div><span>Active</span><strong data-active>0</strong><small>Marked active in this preview</small></div><div><span>Drafts</span><strong data-drafts>3</strong><small>Ready for your finishing touches</small></div></div>
        <section class="category-panel" aria-labelledby="product-list-title">
            <div class="category-panel__heading"><div><h2 id="product-list-title">All products</h2><p>A clear view of your growing collection.</p></div><span class="category-small-note">Preview catalog</span></div>
            <div class="category-toolbar" data-tools hidden>
                <label class="category-search"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><circle cx="10.5" cy="10.5" r="6.5"/><path d="m16 16 4 4"/></svg><span class="category-sr-only">Search products</span><input type="search" data-search placeholder="Search name, SKU, or slug" autocomplete="off"></label>
                <div class="category-toolbar__filters"><label>Category<select data-category-filter><option value="all">All categories</option></select></label><label>Status<select data-status-filter><option value="all">All statuses</option><option value="active">Active</option><option value="draft">Draft</option></select></label><label>Sort<select data-sort><option value="name">Name: A to Z</option><option value="recent">Recently added</option></select></label></div>
            </div>
            <div class="ap-list-head" aria-hidden="true"><span>Product</span><span>Price</span><span>Inventory</span><span>Status</span><span></span></div>
            <ul class="ap-list" data-product-list aria-label="Products"></ul>
            <div class="category-empty" data-empty hidden><h3>No matching products.</h3><p>Try a different search or reset the filters.</p><button type="button" class="wg-admin-button" data-reset>Clear filters</button></div>
            <p class="category-list-footer" data-count role="status" aria-live="polite"></p>
            <noscript><p class="category-notice">Enable JavaScript to browse this frontend catalog preview.</p></noscript>
        </section>
    </div>
    <template id="admin-product-row"><li class="ap-row">
        <div class="ap-row__identity"><div class="ap-thumbnail"><img width="56" height="56" alt="" loading="lazy"><span aria-hidden="true" hidden>W</span></div><div><a data-name></a><p data-meta></p></div></div>
        <div class="ap-row__value"><small>Price</small><span data-price></span></div>
        <div class="ap-row__value"><small>Inventory</small><span data-stock></span></div>
        <div class="ap-row__value"><small>Status</small><span class="category-badge" data-status></span></div>
        <div class="ap-row__actions"><a data-view>View</a><a data-edit>Edit <span aria-hidden="true">&nearr;</span></a></div>
    </li></template>
@endsection
