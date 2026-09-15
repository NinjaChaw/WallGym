@extends('admin.layouts.app')
@section('title', 'Categories')
@section('active_nav', 'categories')
@section('eyebrow', 'Organize your collection')
@section('heading', 'A place for every product.')
@section('description', 'Create clear collections that make your store easier to explore.')
@section('actions')
    <a class="wg-admin-button wg-admin-button--primary" href="{{ url('/admin/categories/create') }}"><span aria-hidden="true">+</span> Create category</a>
@endsection
@section('content')
    @include('admin.categories._workspace')
    <div class="category-workspace" data-category-index>
        <div class="category-stats" aria-label="Category summary">
            <div><span>Total categories</span><strong data-total>3</strong><small>A considered collection</small></div>
            <div><span>Active</span><strong data-active>2</strong><small>Marked active in this preview</small></div>
            <div><span>Drafts</span><strong data-drafts>1</strong><small>A little more work to do</small></div>
        </div>

        <section class="category-panel" aria-labelledby="categories-title">
            <div class="category-panel__heading"><div><h2 id="categories-title">Your categories</h2><p>Simple groups. A better browsing experience.</p></div><span class="category-small-note">Preview collection</span></div>
            <div class="category-toolbar" data-category-tools hidden>
                <label class="category-search"><span class="category-sr-only">Search categories</span><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><circle cx="10.5" cy="10.5" r="6.5"/><path d="m16 16 4 4" stroke-linecap="round"/></svg><input type="search" data-search placeholder="Search by name or slug" autocomplete="off"></label>
                <div class="category-toolbar__filters"><label>Status<select data-status-filter><option value="all">All statuses</option><option value="active">Active</option><option value="draft">Draft</option></select></label><label>Sort<select data-sort><option value="position">Display order</option><option value="name">Name: A to Z</option></select></label></div>
            </div>
            <div class="category-list-heading" aria-hidden="true"><span>Category</span><span>Products</span><span>Status</span><span>Order</span><span></span></div>
            <ul class="category-list" data-category-list aria-label="Categories"></ul>
            <div class="category-empty" data-empty hidden><span aria-hidden="true">&#9638;</span><h3>No categories found.</h3><p>Try another search or clear your filters.</p><button type="button" class="wg-admin-button" data-clear-filters>Clear filters</button></div>
            <p class="category-list-footer" data-result-count role="status" aria-live="polite"></p>
            <noscript><p class="category-notice">Enable JavaScript to explore the sample category list.</p></noscript>
        </section>
    </div>
    <template id="category-row-template">
        <li class="category-row">
            <div class="category-row__identity"><div class="category-row__image"><img width="52" height="52" alt="" loading="lazy"><span aria-hidden="true" hidden>&#9638;</span></div><div><a data-edit-name></a><p data-slug></p></div></div>
            <p class="category-row__products"><span class="category-mobile-label">Products</span><span data-products></span></p>
            <p class="category-row__status"><span class="category-mobile-label">Status</span><span class="category-badge" data-status></span></p>
            <p class="category-row__order"><span class="category-mobile-label">Order</span><span data-position></span></p>
            <a class="category-row__edit" data-edit-link>Edit <span aria-hidden="true">&nearr;</span></a>
        </li>
    </template>
@endsection
