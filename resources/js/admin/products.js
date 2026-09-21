document.querySelectorAll('[data-delete-product]').forEach(form => {
    form.addEventListener('submit', event => {
        if (!window.confirm(`Delete “${form.dataset.deleteProduct}” and its images? This cannot be undone.`)) event.preventDefault();
    });
});

const form = document.querySelector('[data-product-form]');
if (form) {
    const field = name => form.elements.namedItem(name);
    const text = (selector, value) => { form.querySelector(selector).textContent = value; };
    const uploads = field('images[]');
    let previews = [];
    const retained = () => [...form.querySelectorAll('[data-saved-image]')]
        .filter(item => !item.querySelector('[data-image-remove]').checked)
        .sort((a, b) => Number(a.querySelector('[data-image-order]').value) - Number(b.querySelector('[data-image-order]').value));

    function refresh() {
        text('[data-preview-name]', field('name').value || 'Your product name');
        text('[data-preview-description]', field('description').value || 'A short description will appear here.');
        text('[data-description-count]', `${field('description').value.length} / 2000`);
        text('[data-preview-category]', field('category_id').selectedOptions[0]?.textContent || 'Choose a category');
        text('[data-preview-price]', field('price').value !== '' && field('currency').value
            ? `${field('currency').value} ${Number(field('price').value).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}` : 'Price not set');
        const active = field('status').value === 'active';
        text('[data-preview-status]', active ? 'Active' : 'Draft');
        form.querySelector('[data-preview-status]').dataset.state = field('status').value;
        field('price').required = active || field('currency').value !== '';
        field('currency').required = active || field('price').value !== '';
        field('stock').required = active;
        const cover = retained()[0]?.dataset.src || previews[0];
        const image = form.querySelector('[data-preview-image]');
        image.hidden = !cover;
        if (cover) image.src = cover;
        else image.removeAttribute('src');
        form.querySelector('[data-preview-placeholder]').hidden = !!cover;
        const invalid = [...uploads.files].some(file => file.size > 1024 * 1024 || !['image/jpeg', 'image/png', 'image/webp'].includes(file.type));
        const message = invalid ? 'Choose JPG, PNG, or WebP files up to 1 MB each.'
            : (uploads.files.length + retained().length > 10 ? 'Keep a maximum of 10 images, including your existing images.' : '');
        uploads.setCustomValidity(message);
        const error = form.querySelector('[data-upload-error]');
        error.textContent = message;
        error.hidden = !message;
    }
    form.querySelector('[data-generate-slug]').addEventListener('click', () => {
        field('slug').value = field('name').value.normalize('NFKD').replace(/[\u0300-\u036f]/g, '').toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '').slice(0, 120).replace(/-$/, '');
    });
    uploads.addEventListener('change', () => {
        previews.forEach(url => URL.revokeObjectURL(url));
        previews = [];
        const container = form.querySelector('[data-new-images]');
        container.replaceChildren();
        [...uploads.files].slice(0, 10).forEach(file => {
            if (file.size > 1024 * 1024 || !['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) return;
            const url = URL.createObjectURL(file);
            previews.push(url);
            const image = document.createElement('img');
            image.src = url;
            image.alt = file.name;
            image.width = 100;
            image.height = 100;
            container.append(image);
        });
        refresh();
    });
    form.addEventListener('input', refresh);
    form.addEventListener('change', refresh);
    refresh();
}
document.querySelector('[data-error]')?.focus();
