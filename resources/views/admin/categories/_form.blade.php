<div class="category-not-found category-panel" data-not-found hidden><h2>Category not found.</h2><p>This preview may belong to a different browser tab.</p><a class="wg-admin-button" href="{{ url('/admin/categories') }}">Back to categories</a></div>
<form class="category-form" data-category-form data-mode="{{ $mode }}" data-id="{{ request()->route('category') }}">
    <div class="category-form__main">
        <section class="category-panel" aria-labelledby="category-details-title">
            <div class="category-panel__heading"><div><h2 id="category-details-title">Category details</h2><p>A clear name is a good place to start.</p></div><span class="category-section-number" aria-hidden="true">01</span></div>
            <div class="category-panel__body">
                <label class="category-field" for="category-name">Category name <span aria-hidden="true">*</span><input id="category-name" name="name" maxlength="80" required placeholder="e.g. Swedish walls" autocomplete="off" aria-describedby="category-name-help"><small id="category-name-help">Use a short name your customers will recognize.</small></label>
                <div class="category-field"><div class="category-field__label"><label for="category-slug">URL slug <span aria-hidden="true">*</span></label><button type="button" class="category-text-button" data-generate-slug>Generate from name</button></div><input id="category-slug" name="slug" maxlength="100" required pattern="[a-z0-9]+(-[a-z0-9]+)*" placeholder="swedish-walls" autocomplete="off" spellcheck="false" aria-describedby="category-slug-help"><small id="category-slug-help">Lowercase letters, numbers, and hyphens. Must be unique.</small></div>
                <label class="category-field" for="category-description">Description <span class="category-optional">Optional</span><textarea id="category-description" name="description" rows="4" maxlength="300" placeholder="A short introduction to this collection." aria-describedby="category-description-help"></textarea><span class="category-field__hint"><small id="category-description-help">Give your customers a reason to explore.</small><small data-description-count>0 / 300</small></span></label>
            </div>
        </section>

        <section class="category-panel" aria-labelledby="category-image-title">
            <div class="category-panel__heading"><div><h2 id="category-image-title">Category image</h2><p>Keep the look calm, clear, and consistent.</p></div><span class="category-section-number" aria-hidden="true">02</span></div>
            <div class="category-panel__body">
                <div class="category-upload"><span class="category-upload__icon" aria-hidden="true"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="3"/><circle cx="8" cy="8" r="1.5"/><path d="m3 17 6-6 4 4 3-3 5 5" stroke-linejoin="round"/></svg></span><label for="category-image">Choose a category image</label><p id="category-image-help">JPG, PNG, or WebP up to 1 MB. A square image works best.</p><input type="file" id="category-image" accept="image/jpeg,image/png,image/webp" aria-describedby="category-image-help category-image-error"><p id="category-image-error" class="category-error" role="alert" hidden></p></div>
                <div class="category-image-meta"><span data-image-name>No image selected</span><button type="button" class="category-text-button" data-remove-image hidden>Remove image</button></div>
            </div>
        </section>
    </div>

    <aside class="category-form__side" aria-label="Category settings and preview">
        <section class="category-panel" aria-labelledby="category-settings-title"><div class="category-panel__heading"><div><h2 id="category-settings-title">Visibility &amp; order</h2></div></div><div class="category-panel__body">
            <label class="category-field" for="category-status">Status<select id="category-status" name="status"><option value="draft">Draft</option><option value="active">Active</option></select><small>Choose how this category should appear when connected.</small></label>
            <label class="category-field" for="category-position">Display order<input type="number" id="category-position" name="position" min="1" max="999" step="1" value="1" required><small>Lower numbers appear first.</small></label>
        </div></section>
        <section class="category-panel category-live-preview" aria-labelledby="category-preview-title"><div class="category-panel__heading"><h2 id="category-preview-title">Category preview</h2><span class="category-preview-live">Live</span></div><div class="category-panel__body"><div class="category-cover"><img data-preview-image width="400" height="400" alt="Category cover preview" hidden><span data-preview-placeholder aria-hidden="true">W</span><span class="category-badge" data-preview-status>Draft</span></div><h3 data-preview-name>Your category name</h3><p data-preview-description>A short description will appear here.</p><p class="category-preview-slug" data-preview-slug>your-category</p></div></section>
    </aside>

    <div class="category-form__actions"><p><span aria-hidden="true">*</span> Required fields <span class="category-actions-note">&middot; Saves to this tab only</span></p><div><a class="wg-admin-button" href="{{ url('/admin/categories') }}">Cancel</a><button type="submit" class="wg-admin-button wg-admin-button--primary" data-save-category disabled>{{ $mode === 'create' ? 'Create preview' : 'Save preview' }} <span aria-hidden="true">&rarr;</span></button></div></div>
    <noscript><p class="category-notice">Enable JavaScript to use this frontend form preview.</p></noscript>
</form>
