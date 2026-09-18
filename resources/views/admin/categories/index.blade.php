@extends('admin.layouts.app')
@section('title', 'Categories')
@section('active_nav', 'categories')
@section('eyebrow', 'Organize your collection')
@section('heading', 'A place for every product.')
@section('description', 'Create clear collections that make your store easier to explore.')
@section('actions')<a class="wg-admin-button wg-admin-button--primary" href="{{ route('admin.categories.create') }}">+ Create category</a>@endsection
@section('content')
    @include('admin.categories._workspace')
    <div class="category-workspace">
        <div class="category-stats" aria-label="Category summary">
            <div>
                <span>Total categories</span>
                <strong>{{ $total }}</strong>
                <small>In your database</small>
            </div>
            <div>
                <span>Active</span>
                <strong>{{ $active }}</strong>
                <small>Available categories</small>
            </div>
            <div>
                <span>Drafts</span>
                <strong>{{ $total - $active }}</strong>
                <small>A little more work to do</small>
            </div>
        </div>
        <section class="category-panel" aria-labelledby="categories-title">
            <div class="category-panel__heading">
                <div>
                    <h2 id="categories-title">Your categories</h2>
                    <p>Simple groups. A better browsing experience.</p>
                </div>
            </div>
            <form class="category-toolbar" method="GET" action="{{ route('admin.categories.index') }}">
                <label class="category-search">
                    <span class="category-sr-only">Search categories</span>
                    <input type="search" name="search" value="{{ request('search') }}" maxlength="100" placeholder="Search by name or slug">
                </label>
                <div class="category-toolbar__filters">
                    <label>Status
                        <select name="status">
                            @foreach(['all'=>'All statuses','active'=>'Active','draft'=>'Draft'] as $value=>$label)
                                <option value="{{ $value }}" @selected(request('status','all') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>Sort
                        <select name="sort">
                            <option value="position" @selected(request('sort','position') === 'position')>Display order</option>
                            <option value="name" @selected(request('sort') === 'name')>Name: A to Z</option>
                        </select>
                    </label>
                    <button type="submit" class="wg-admin-button">Apply</button>
                </div>
            </form>
            <div class="category-list-heading" aria-hidden="true">
                <span>Category</span>
                <span>Status</span>
                <span>Order</span>
                <span></span>
                <span></span>
            </div>
            <ul class="category-list" aria-label="Categories">
                @forelse($categories as $category)
                    <li class="category-row">
                        <div class="category-row__identity">
                            <div class="category-row__image">
                                @if($category->image_url)
                                    <img src="{{ $category->image_url }}" width="52" height="52" alt="" loading="lazy">
                                @else
                                    <span aria-hidden="true">W</span>
                                @endif
                            </div>
                            <div>
                                <a href="{{ route('admin.categories.edit', $category) }}">{{ $category->name }}</a>
                                <p>/{{ $category->slug }}</p>
                            </div>
                        </div>
                        <p>
                            <span class="category-mobile-label">Status</span>
                            <span class="category-badge" data-state="{{ $category->status ? 'active' : 'draft' }}">{{ $category->status ? 'Active' : 'Draft' }}</span>
                        </p>
                        <p><span class="category-mobile-label">Order</span>{{ $category->sort_order }}</p>
                        <a class="category-row__edit" href="{{ route('admin.categories.edit', $category) }}">Edit</a>
                        <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" data-delete-category="{{ $category->name }}">
                            @csrf 
                            @method('DELETE')
                            <button type="submit" class="category-text-button">Delete</button>
                        </form>
                    </li>
                @empty
                    <li class="category-empty">
                        <h3>No categories found.</h3>
                        <p>Create a category or try another search.</p>
                        <a class="wg-admin-button" href="{{ route('admin.categories.index') }}">Clear filters</a>
                    </li>
                @endforelse
            </ul>
            <div class="category-list-footer">
                <p>Showing {{ $categories->firstItem() ?? 0 }}–{{ $categories->lastItem() ?? 0 }} of {{ $categories->total() }} categories</p>
                <nav aria-label="Category pages" class="category-pagination">
                    @if($categories->previousPageUrl())
                        <a class="wg-admin-button" href="{{ $categories->previousPageUrl() }}">&larr; Previous</a>
                    @endif
                    <span>Page {{ $categories->currentPage() }} of {{ $categories->lastPage() }}</span>
                    @if($categories->nextPageUrl())
                        <a class="wg-admin-button" href="{{ $categories->nextPageUrl() }}">Next &rarr;</a>
                    @endif
                </nav>
            </div>
        </section>
    </div>
@endsection
