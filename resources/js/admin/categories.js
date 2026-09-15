export const slugify = value => value.normalize('NFKD').replace(/[\u0300-\u036f]/g, '').toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '').slice(0, 100).replace(/-$/g, '');

export function selectCategories(items, query = '', status = 'all', sort = 'position') {
    const term = query.trim().toLocaleLowerCase();
    return items.filter(item => (status === 'all' || item.status === status) && `${item.name} ${item.slug}`.toLocaleLowerCase().includes(term))
        .sort((a, b) => sort === 'name' ? a.name.localeCompare(b.name) : a.position - b.position || a.name.localeCompare(b.name));
}

const root = typeof document !== 'undefined' ? document.querySelector('[data-category-root]') : null;
if (root) {
    const base = root.dataset.baseUrl;
    const key = `wallgym:category-preview:v1:${new URL(base).pathname}`;
    const seed = JSON.parse(document.getElementById('category-seed').textContent);
    const feedback = document.querySelector('[data-category-feedback]');
    const notify = (message, error = false) => {
        feedback.textContent = message;
        feedback.hidden = false;
        feedback.toggleAttribute('data-error', error);
    };
    const load = () => {
        try {
            const raw = sessionStorage.getItem(key);
            if (!raw) return structuredClone(seed);
            const data = JSON.parse(raw);
            if (!Array.isArray(data) || !data.every(item => /^\d+$/.test(item.id) && typeof item.name === 'string' && typeof item.slug === 'string' && typeof item.description === 'string' && ['active', 'draft'].includes(item.status) && Number.isInteger(item.position) && item.position >= 1 && item.position <= 999 && Number.isInteger(item.products) && typeof item.image === 'string')) throw new Error('Invalid preview data');
            return data;
        } catch {
            notify('Saved preview data is unavailable. Showing the sample categories.', true);
            return structuredClone(seed);
        }
    };
    const safeImage = source => {
        if (!source) return '';
        if (/^data:image\/(jpeg|png|webp);base64,/i.test(source)) return source;
        try { const url = new URL(source); return url.origin === location.origin ? url.href : ''; } catch { return ''; }
    };
    let items = load();
    const index = document.querySelector('[data-category-index]');
    if (index) {
        const search = index.querySelector('[data-search]');
        const status = index.querySelector('[data-status-filter]');
        const sort = index.querySelector('[data-sort]');
        const list = index.querySelector('[data-category-list]');
        const render = () => {
            const selected = selectCategories(items, search.value, status.value, sort.value);
            const fragment = document.createDocumentFragment();
            selected.forEach(item => {
                const row = document.getElementById('category-row-template').content.cloneNode(true);
                const editURL = `${base}/${encodeURIComponent(item.id)}/edit`;
                const name = row.querySelector('[data-edit-name]');
                name.textContent = item.name; name.href = editURL;
                row.querySelector('[data-edit-link]').href = editURL;
                row.querySelector('[data-edit-link]').setAttribute('aria-label', `Edit ${item.name}`);
                row.querySelector('[data-slug]').textContent = `/${item.slug}`;
                row.querySelector('[data-products]').textContent = String(item.products);
                row.querySelector('[data-position]').textContent = String(item.position).padStart(2, '0');
                const badge = row.querySelector('[data-status]');
                badge.textContent = item.status === 'active' ? 'Active' : 'Draft'; badge.dataset.state = item.status;
                const image = safeImage(item.image);
                if (image) row.querySelector('img').src = image;
                else { row.querySelector('img').hidden = true; row.querySelector('.category-row__image span').hidden = false; }
                fragment.append(row);
            });
            list.replaceChildren(fragment);
            index.querySelector('[data-total]').textContent = String(items.length);
            index.querySelector('[data-active]').textContent = String(items.filter(item => item.status === 'active').length);
            index.querySelector('[data-drafts]').textContent = String(items.filter(item => item.status === 'draft').length);
            index.querySelector('[data-empty]').hidden = selected.length > 0;
            index.querySelector('[data-result-count]').textContent = `Showing ${selected.length} of ${items.length} categories`;
        };
        index.querySelector('[data-category-tools]').hidden = false;
        search.addEventListener('input', render); status.addEventListener('change', render); sort.addEventListener('change', render);
        index.querySelector('[data-clear-filters]').addEventListener('click', () => { search.value = ''; status.value = 'all'; render(); search.focus(); });
        window.addEventListener('pageshow', () => { items = load(); render(); });
        if (new URLSearchParams(location.search).get('preview') === 'saved') notify('Category saved to this tab’s preview. Your storefront is unchanged.');
        render();
    }

    const form = document.querySelector('[data-category-form]');
    if (form) {
        const editing = form.dataset.mode === 'edit';
        const record = editing ? items.find(item => item.id === form.dataset.id) : { name: '', slug: '', description: '', status: 'draft', position: Math.min(999, Math.max(0, ...items.map(item => item.position)) + 1), image: '' };
        if (!record) {
            form.hidden = true; document.querySelector('[data-not-found]').hidden = false;
        } else {
            const field = name => form.elements.namedItem(name);
            const save = form.querySelector('[data-save-category]');
            const imageInput = form.querySelector('#category-image');
            const imageError = form.querySelector('#category-image-error');
            const previewImage = form.querySelector('[data-preview-image]');
            let image = safeImage(record.image), dirty = false, slugEdited = editing, imagePending = false, uploadVersion = 0;
            for (const name of ['name', 'slug', 'description', 'status', 'position']) field(name).value = record[name];
            const updatePreview = () => {
                form.querySelector('[data-preview-name]').textContent = field('name').value.trim() || 'Your category name';
                form.querySelector('[data-preview-description]').textContent = field('description').value.trim() || 'A short description will appear here.';
                form.querySelector('[data-preview-slug]').textContent = `/${field('slug').value.trim() || 'your-category'}`;
                form.querySelector('[data-description-count]').textContent = `${field('description').value.length} / 300`;
                const status = form.querySelector('[data-preview-status]');
                status.textContent = field('status').value === 'active' ? 'Active' : 'Draft'; status.dataset.state = field('status').value;
                previewImage.hidden = !image;
                if (image) previewImage.src = image; else previewImage.removeAttribute('src');
                form.querySelector('[data-preview-placeholder]').hidden = Boolean(image);
                form.querySelector('[data-remove-image]').hidden = !image;
            };
            field('name').addEventListener('input', () => { if (!slugEdited) { field('slug').value = slugify(field('name').value); field('slug').setCustomValidity(''); } });
            field('slug').addEventListener('input', () => { slugEdited = true; });
            form.querySelector('[data-generate-slug]').addEventListener('click', () => { slugEdited = false; field('slug').value = slugify(field('name').value); field('slug').setCustomValidity(''); dirty = true; updatePreview(); });
            form.addEventListener('input', event => { event.target.setCustomValidity?.(''); dirty = true; updatePreview(); });
            form.addEventListener('change', () => { dirty = true; updatePreview(); });
            form.querySelector('[data-image-name]').textContent = image ? 'Current category image' : 'No image selected';
            const imageFailure = message => { imageError.textContent = message; imageError.hidden = false; };
            imageInput.addEventListener('change', async () => {
                const version = ++uploadVersion;
                imagePending = false; save.disabled = false;
                const file = imageInput.files[0];
                imageError.hidden = true;
                if (!file) return;
                if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type) || file.size > 1024 * 1024) { imageInput.value = ''; imageFailure('Choose a JPG, PNG, or WebP image no larger than 1 MB.'); return; }
                imagePending = true; save.disabled = true;
                try {
                    const data = await new Promise((resolve, reject) => { const reader = new FileReader(); reader.onload = () => resolve(reader.result); reader.onerror = reject; reader.readAsDataURL(file); });
                    const check = new Image(); check.src = data; await check.decode();
                    if (version !== uploadVersion) return;
                    image = data; form.querySelector('[data-image-name]').textContent = file.name; updatePreview();
                } catch { if (version === uploadVersion) imageFailure('This image could not be opened. Please choose another.'); }
                finally { if (version === uploadVersion) { imagePending = false; save.disabled = false; } }
            });
            form.querySelector('[data-remove-image]').addEventListener('click', () => { uploadVersion++; imagePending = false; save.disabled = false; image = ''; imageInput.value = ''; imageError.hidden = true; form.querySelector('[data-image-name]').textContent = 'No image selected'; dirty = true; updatePreview(); });
            form.addEventListener('submit', event => {
                event.preventDefault();
                if (imagePending) return;
                field('name').value = field('name').value.trim(); field('slug').value = field('slug').value.trim();
                field('name').setCustomValidity(field('name').value ? '' : 'Enter a category name.');
                items = load();
                field('slug').setCustomValidity(items.some(item => item.slug === field('slug').value && item.id !== record.id) ? 'This slug is already in use. Choose another.' : '');
                if (!form.reportValidity()) return;
                const updated = { id: record.id || String(Date.now()), name: field('name').value, slug: field('slug').value, description: field('description').value.trim(), status: field('status').value, position: Number(field('position').value), products: record.products || 0, image };
                const next = editing ? items.map(item => item.id === record.id ? updated : item) : [...items, updated];
                try { sessionStorage.setItem(key, JSON.stringify(next)); }
                catch { notify('This preview could not be saved in your browser. Try a smaller image or enable session storage. Your form is still here.', true); feedback.scrollIntoView({ block: 'center' }); return; }
                dirty = false; window.location.assign(`${base}?preview=saved`);
            });
            window.addEventListener('beforeunload', event => { if (dirty) { event.preventDefault(); event.returnValue = ''; } });
            save.disabled = false; updatePreview();
        }
    }
}
