<form class="category-form ap-form" data-product-form method="POST" enctype="multipart/form-data" action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}">
    @csrf
    @if($product->exists) @method('PUT') @endif
    <div class="category-form__main">
        <section class="category-panel" aria-labelledby="product-details-title">
            <div class="category-panel__heading"><div><h2 id="product-details-title">Product details</h2><p>The essentials your customers need to know.</p></div><span class="category-section-number" aria-hidden="true">01</span></div>
            <div class="category-panel__body">
                <label class="category-field" for="ap-name">Product name *<input id="ap-name" name="name" value="{{ old('name', $product->name) }}" required maxlength="100" placeholder="e.g. Swedish Wall">@error('name')<small class="category-error">{{ $message }}</small>@enderror</label>
                <div class="category-field"><div class="category-field__label"><label for="ap-slug">URL slug *</label><button class="category-text-button" type="button" data-generate-slug>Generate from name</button></div><input id="ap-slug" name="slug" value="{{ old('slug', $product->slug) }}" required maxlength="120" pattern="[a-z0-9]+(-[a-z0-9]+)*" spellcheck="false" placeholder="swedish-wall" aria-describedby="ap-slug-help"><small id="ap-slug-help">Use lowercase letters, numbers, and hyphens. Each product needs a unique slug.</small>@error('slug')<small class="category-error">{{ $message }}</small>@enderror</div>
                <label class="category-field" for="ap-description">Description <span class="category-optional">Optional</span><textarea id="ap-description" name="description" rows="6" maxlength="2000" placeholder="Introduce the product and what makes it useful.">{{ old('description', $product->description) }}</textarea><span class="category-field__hint"><small>Keep it useful, honest, and easy to read.</small><small data-description-count></small></span></label>
                <div class="ap-fields-two">
                    <label class="category-field" for="ap-dimensions">Dimensions <span class="category-optional">Optional</span><input id="ap-dimensions" name="dimensions" value="{{ old('dimensions', $product->dimensions) }}" maxlength="120" placeholder="Height × width × depth, with units"></label>
                    <label class="category-field" for="ap-material">Material <span class="category-optional">Optional</span><input id="ap-material" name="material" value="{{ old('material', $product->material) }}" maxlength="120" placeholder="Add verified material details"></label>
                </div>
            </div>
        </section>
        <section class="category-panel" aria-labelledby="product-media-title">
            <div class="category-panel__heading"><div><h2 id="product-media-title">Product images</h2><p>Up to 10 images. The lowest display order is your cover.</p></div><span class="category-section-number" aria-hidden="true">02</span></div>
            <div class="category-panel__body">
                @if($product->images->isNotEmpty())
                    <div class="ap-gallery">
                        @foreach($product->images as $image)
                            <div class="ap-gallery__item" data-saved-image data-src="{{ $image->url }}">
                                <img src="{{ $image->url }}" width="180" height="180" alt="{{ $image->alt_text ?? $product->name }}" loading="lazy">
                                <label class="category-field" for="image-alt-{{ $image->id }}">Image description<input id="image-alt-{{ $image->id }}" name="gallery[{{ $image->id }}][alt_text]" maxlength="255" value="{{ old('gallery.'.$image->id.'.alt_text', $image->alt_text) }}"></label>
                                <label class="category-field" for="image-order-{{ $image->id }}">Display order<input id="image-order-{{ $image->id }}" name="gallery[{{ $image->id }}][sort_order]" data-image-order type="number" min="0" max="999" required value="{{ old('gallery.'.$image->id.'.sort_order', $image->sort_order) }}"></label>
                                <label class="ap-gallery__remove"><input type="checkbox" name="gallery[{{ $image->id }}][remove]" value="1" data-image-remove @checked(old('gallery.'.$image->id.'.remove', false))> Remove when saved</label>
                            </div>
                        @endforeach
                    </div>
                @endif
                <div class="category-upload"><label for="ap-images">Add product images</label><p id="ap-image-help">JPG, PNG, or WebP, up to 1 MB each. New images are added after your existing images.</p><input type="file" id="ap-images" name="images[]" multiple accept="image/jpeg,image/png,image/webp" aria-describedby="ap-image-help"><p class="category-error" data-upload-error role="alert" hidden></p></div>
                <div class="ap-new-images" data-new-images aria-label="Selected images"></div>
            </div>
        </section>
        <section class="category-panel" aria-labelledby="product-pricing-title">
            <div class="category-panel__heading"><div><h2 id="product-pricing-title">Pricing &amp; inventory</h2><p>Price, currency, and stock are required for active products.</p></div><span class="category-section-number" aria-hidden="true">03</span></div>
            <div class="category-panel__body">
                <div class="ap-fields-two">
                    <label class="category-field" for="ap-price">Price<input id="ap-price" name="price" type="number" min="0" max="99999999" step="0.01" value="{{ old('price', $product->price) }}" placeholder="0.00">@error('price')<small class="category-error">{{ $message }}</small>@enderror</label>
                    <label class="category-field" for="ap-currency">Currency<select id="ap-currency" name="currency"><option value="">Choose currency</option>@foreach(['BDT'=>'Bangladeshi taka','SEK'=>'Swedish krona','EUR'=>'Euro','USD'=>'US dollar'] as $code=>$label)<option value="{{ $code }}" @selected(old('currency', $product->currency) === $code)>{{ $code }} — {{ $label }}</option>@endforeach</select>@error('currency')<small class="category-error">{{ $message }}</small>@enderror</label>
                </div>
                <div class="ap-fields-two">
                    <label class="category-field" for="ap-sku">SKU <span class="category-optional">Optional</span><input id="ap-sku" name="sku" maxlength="60" value="{{ old('sku', $product->sku) }}" placeholder="WG-WALL-001"><small>A unique internal reference for this product.</small>@error('sku')<small class="category-error">{{ $message }}</small>@enderror</label>
                    <label class="category-field" for="ap-stock">Stock quantity<input id="ap-stock" name="stock" type="number" min="0" max="999999" step="1" value="{{ old('stock', $product->stock) }}" placeholder="Not set"><small>Enter 0 for no stock. Blank means unknown.</small>@error('stock')<small class="category-error">{{ $message }}</small>@enderror</label>
                </div>
            </div>
        </section>
    </div>
    <aside class="category-form__side" aria-label="Product organization and preview">
        <section class="category-panel" aria-labelledby="product-organization-title"><div class="category-panel__heading"><h2 id="product-organization-title">Organization</h2></div><div class="category-panel__body">
            <label class="category-field" for="ap-status">Status<select id="ap-status" name="status">@foreach(['draft'=>'Draft','active'=>'Active'] as $value=>$label)<option value="{{ $value }}" @selected(old('status', $product->status) === $value)>{{ $label }}</option>@endforeach</select><small>Use Draft while preparing your product.</small></label>
            <label class="category-field" for="ap-category">Category *<select id="ap-category" name="category_id" required><option value="">Choose category</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected((string) old('category_id', $product->category_id) === (string) $category->id)>{{ $category->name }}{{ $category->status ? '' : ' (draft)' }}</option>@endforeach</select>@error('category_id')<small class="category-error">{{ $message }}</small>@enderror</label>
            @if($categories->isEmpty())<p class="category-notice">Create a <a href="{{ route('admin.categories.create') }}">category</a> before adding a product.</p>@endif
        </div></section>
        <section class="category-panel category-live-preview" aria-labelledby="product-preview-title"><div class="category-panel__heading"><h2 id="product-preview-title">Product preview</h2></div><div class="category-panel__body"><div class="category-cover ap-preview-cover"><img data-preview-image width="400" height="400" alt="Product cover preview" hidden><span data-preview-placeholder aria-hidden="true">W</span><span class="category-badge" data-preview-status>Draft</span></div><p class="ap-preview-category" data-preview-category>Choose a category</p><h3 data-preview-name>{{ old('name', $product->name) ?: 'Your product name' }}</h3><p data-preview-description>{{ old('description', $product->description) }}</p><p class="ap-preview-price" data-preview-price>Price not set</p></div></section>
    </aside>
    <div class="category-form__actions"><p>* Required fields</p><div><a class="wg-admin-button" href="{{ route('admin.products.index') }}">Cancel</a><button type="submit" class="wg-admin-button wg-admin-button--primary">{{ $product->exists ? 'Save changes' : 'Create product' }} <span aria-hidden="true">&rarr;</span></button></div></div>
</form>
