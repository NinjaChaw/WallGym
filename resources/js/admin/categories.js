export const slugify = value => value.normalize('NFKD').replace(/[\u0300-\u036f]/g, '').toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '').slice(0, 100).replace(/-$/g, '');

// Enhance the real Laravel form; never intercept its persistence with browser storage.
const form = document.querySelector('[data-category-form]');
if (form) {
    const field = name => form.elements.namedItem(name);
    const image = form.querySelector('[data-preview-image]');
    const input = field('image');
    const error = form.querySelector('#category-image-error');
    let objectURL = null;
    let editedSlug = !!field('slug').value;
    const render = () => {
        form.querySelector('[data-preview-name]').textContent = field('name').value.trim() || 'Your category name';
        form.querySelector('[data-preview-description]').textContent = field('description').value.trim() || 'A short description will appear here.';
        form.querySelector('[data-preview-slug]').textContent = `/${field('slug').value || 'your-category'}`;
        form.querySelector('[data-description-count]').textContent = `${field('description').value.length} / 300`;
        const status = form.querySelector('[data-preview-status]');
        status.textContent = field('status').value === '1' ? 'Active' : 'Draft';
        status.dataset.state = field('status').value === '1' ? 'active' : 'draft';
        const source = objectURL || (field('remove_image').checked ? '' : image.dataset.existingImage);
        if (source) image.src = source; else image.removeAttribute('src');
        image.hidden = !source;
        form.querySelector('[data-preview-placeholder]').hidden = !!source;
    };
    field('name').addEventListener('input', () => { if (!editedSlug) field('slug').value = slugify(field('name').value); });
    field('slug').addEventListener('input', () => editedSlug = true);
    form.querySelector('[data-generate-slug]').addEventListener('click', () => {field('slug').value = slugify(field('name').value); editedSlug = false; render();});
    form.addEventListener('input', render);
    input.addEventListener('change', () => {
        if (objectURL) URL.revokeObjectURL(objectURL);
        objectURL = null; error.hidden = true;
        const file = input.files[0];
        if (file && (!['image/jpeg','image/png','image/webp'].includes(file.type) || file.size > 1048576)) {
            input.value = ''; error.hidden = false; error.textContent = 'Choose a JPG, PNG or WebP image up to 1 MB.';
        } else if (file) {
            objectURL = URL.createObjectURL(file);
            field('remove_image').checked = false;
        }
        form.querySelector('[data-image-name]').textContent = input.files[0]?.name || 'No new image selected';
        render();
    });
    field('remove_image').addEventListener('change', () => {
        if (field('remove_image').checked) { input.value = ''; if (objectURL) URL.revokeObjectURL(objectURL); objectURL = null; form.querySelector('[data-image-name]').textContent = 'No new image selected'; }
        render();
    });
    render();
}
document.querySelectorAll('[data-delete-category]').forEach(form => form.addEventListener('submit', event => {
    if (!window.confirm(`Delete “${form.dataset.deleteCategory}”? This permanently removes the category and its image.`)) event.preventDefault();
}));
