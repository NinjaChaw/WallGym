<form class="category-form" data-category-form data-mode="{{ $mode }}" method="POST" action="{{ $mode === 'create' ? route('admin.categories.store') : route('admin.categories.update', $category) }}" enctype="multipart/form-data">
    @csrf
    @if($mode === 'edit') 
        @method('PUT') 
    @endif
    <div class="category-form__main">
        <section class="category-panel" aria-labelledby="category-details-title">
            <div class="category-panel__heading">
                <div>
                    <h2 id="category-details-title">Category details</h2>
                    <p>A clear name is a good place to start.</p>
                </div>
                <span class="category-section-number" aria-hidden="true">01</span>
            </div>
            <div class="category-panel__body">
                <label class="category-field" for="category-name">Category name <span aria-hidden="true">*</span>
                    <input id="category-name" name="name" value="{{ old('name', $category->name) }}" maxlength="80" required placeholder="e.g. Swedish walls" autocomplete="off" aria-describedby="category-name-help">
                    @error('name')
                        <small class="category-error" role="alert">{{ $message }}</small>
                    @enderror
                    <small id="category-name-help">Use a short name your customers will recognize.</small>
                </label>
                <div class="category-field">
                    <div class="category-field__label">
                        <label for="category-slug">URL slug <span aria-hidden="true">*</span></label>
                        <button type="button" class="category-text-button" data-generate-slug>Generate from name</button>
                    </div>
                    <input id="category-slug" name="slug" value="{{ old('slug', $category->slug) }}" maxlength="100" required pattern="[a-z0-9]+(-[a-z0-9]+)*" placeholder="swedish-walls" autocomplete="off" spellcheck="false" aria-describedby="category-slug-help">
                    @error('slug')
                        <small class="category-error" role="alert">{{ $message }}</small>
                    @enderror
                    <small id="category-slug-help">Lowercase letters, numbers, and hyphens. Must be unique.</small>
                </div>
                <label class="category-field" for="category-description">Description <span class="category-optional">Optional</span>
                    <textarea id="category-description" name="description" rows="4" maxlength="300" placeholder="A short introduction to this collection." aria-describedby="category-description-help">{{ old('description', $category->description) }}</textarea>
                    @error('description')
                        <small class="category-error" role="alert">{{ $message }}</small>
                    @enderror
                    <span class="category-field__hint">
                        <small id="category-description-help">Give your customers a reason to explore.</small>
                        <small data-description-count>0 / 300</small>
                    </span>
                </label>
            </div>
        </section>

        <section class="category-panel" aria-labelledby="category-image-title">
            <div class="category-panel__heading">
                <div>
                    <h2 id="category-image-title">Category image</h2>
                    <p>Keep the look calm, clear, and consistent.</p>
                </div>
                <span class="category-section-number" aria-hidden="true">02</span>
            </div>
            <div class="category-panel__body">
                <div class="category-upload">
                    <span class="category-upload__icon" aria-hidden="true">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="3"/><circle cx="8" cy="8" r="1.5"/><path d="m3 17 6-6 4 4 3-3 5 5" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <label for="category-image">Choose a category image</label>
                    <p id="category-image-help">JPG, PNG, or WebP up to 1 MB. A square image works best.</p>
                    <input type="file" name="image" id="category-image" accept="image/jpeg,image/png,image/webp" aria-describedby="category-image-help category-image-error">
                    <p id="category-image-error" class="category-error" role="alert" hidden></p>
                </div>
                @error('image')
                    <p class="category-error" role="alert">{{ $message }}</p>
                @enderror
                <div class="category-image-meta">
                    <span data-image-name>No image selected</span>
                    <label class="category-text-button"><input type="checkbox" name="remove_image" value="1" @checked(old('remove_image'))> Remove saved image</label>
                </div>
            </div>
        </section>
    </div>

    <aside class="category-form__side" aria-label="Category settings and preview">
        <section class="category-panel" aria-labelledby="category-settings-title">
            <div class="category-panel__heading">
                <div>
                    <h2 id="category-settings-title">Visibility &amp; order</h2>
                </div>
            </div>
            <div class="category-panel__body">
                <label class="category-field" for="category-status">Status
                    <select id="category-status" name="status">
                        <option value="0" @selected(!old('status', $category->status))>Draft</option>
                        <option value="1" @selected(old('status', $category->status))>Active</option>
                    </select>
                    @error('status')
                        <small class="category-error" role="alert">{{ $message }}</small>
                    @enderror
                    <small>Active categories are available for storefront integration.</small>
                </label>
                <label class="category-field" for="category-position">Display order<input type="number" id="category-position" name="sort_order" min="1" max="999" step="1" value="{{ old('sort_order', $category->sort_order) }}" required>
                    @error('sort_order')
                        <small class="category-error" role="alert">{{ $message }}</small>
                    @enderror
                    <small>Lower numbers appear first.</small>
                </label>
            </div>
        </section>
        <section class="category-panel category-live-preview" aria-labelledby="category-preview-title">
            <div class="category-panel__heading">
                <h2 id="category-preview-title">Category preview</h2>
                <span class="category-preview-live">Live</span>
            </div>
            <div class="category-panel__body">
                <div class="category-cover">
                    <img data-preview-image width="400" height="400" alt="Category cover preview" data-existing-image="{{ $category->image_url }}" @if($category->image_url) src="{{ $category->image_url }}" @else hidden @endif>
                    <span data-preview-placeholder aria-hidden="true">W</span>
                    <span class="category-badge" data-preview-status>Draft</span>
                </div>
                <h3 data-preview-name>{{ old('name', $category->name) ?: 'Your category name' }}</h3>
                <p data-preview-description>{{ old('description', $category->description) ?: 'A short description will appear here.' }}</p>
                <p class="category-preview-slug" data-preview-slug>{{ old('slug', $category->slug) ?: 'your-category' }}</p>
            </div>
        </section>
    </aside>

    <div class="category-form__actions">
        <p>
            <span aria-hidden="true">*</span> Required fields <span class="category-actions-note">&middot; Saved to the database</span>
        </p>
        <div>
            <a class="wg-admin-button" href="{{ url('/admin/categories') }}">Cancel</a>
            <button type="submit" class="wg-admin-button wg-admin-button--primary" data-save-category>{{ $mode === 'create' ? 'Create category' : 'Save category' }} 
                <span aria-hidden="true">&rarr;</span>
            </button>
        </div>
    </div>

</form>
