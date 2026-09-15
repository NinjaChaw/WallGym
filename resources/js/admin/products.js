export const productSlug = value => value.normalize('NFKD').replace(/[\u0300-\u036f]/g, '').toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '').slice(0, 120).replace(/-$/g, '');
export function selectProducts(items, search = '', category = 'all', status = 'all', sort = 'name') {
    const term = search.trim().toLocaleLowerCase();
    return items.filter(item => `${item.name} ${item.sku} ${item.slug}`.toLocaleLowerCase().includes(term) && (category === 'all' || item.category === category) && (status === 'all' || item.status === status))
        .sort((a, b) => sort === 'recent' ? Number(b.id) - Number(a.id) : a.name.localeCompare(b.name));
}
export function validateProduct(product, items) {
    const errors = {};
    if (!product.name.trim()) errors.name = 'Enter a product name.';
    if (!/^[a-z0-9]+(-[a-z0-9]+)*$/.test(product.slug)) errors.slug = 'Use lowercase letters, numbers, and single hyphens.';
    if (items.some(item => item.id !== product.id && item.slug === product.slug)) errors.slug = 'This slug is already in use.';
    if (product.sku && items.some(item => item.id !== product.id && item.sku.toLowerCase() === product.sku.toLowerCase())) errors.sku = 'This SKU is already in use.';
    if (!product.category) errors.category = 'Choose a category.';
    if (product.price !== '' && (!Number.isFinite(Number(product.price)) || Number(product.price) < 0 || Number(product.price) > 99999999 || !/^\d+(\.\d{1,2})?$/.test(product.price))) errors.price = 'Enter a non-negative price with up to two decimal places.';
    if (product.price !== '' && !['SEK','EUR','USD','BDT'].includes(product.currency)) errors.currency = 'Choose a currency for this price.';
    if (product.stock !== '' && (!/^\d+$/.test(product.stock) || Number(product.stock) > 999999)) errors.stock = 'Enter a whole stock quantity from 0 to 999999.';
    if (product.status === 'active' && product.price === '') errors.price = 'Set a price before marking this preview active.';
    return errors;
}
export const displayPrice = item => item.price === '' || !item.currency ? 'Not set' : `${item.currency} ${Number(item.price).toLocaleString('en', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;

const root = typeof document !== 'undefined' ? document.querySelector('[data-admin-products]') : null;
if (root) {
    const base = root.dataset.baseUrl;
    const key = `wallgym:product-preview:v1:${new URL(base).pathname}`;
    const categoryKey = `wallgym:category-preview:v1:${new URL(root.dataset.categoriesUrl).pathname}`;
    const seeds = JSON.parse(document.getElementById('admin-product-seed').textContent);
    const feedback = document.querySelector('[data-feedback]');
    const notify = (message, error = false) => { feedback.hidden = false; feedback.textContent = message; feedback.toggleAttribute('data-error', error); };
    const fields = ['name','slug','sku','category','description','price','currency','stock','status','dimensions','material'];
    const load = () => {
        try {
            const saved = sessionStorage.getItem(key);
            if (!saved) return structuredClone(seeds);
            const parsed = JSON.parse(saved);
            if (!Array.isArray(parsed) || !parsed.every(item => /^\d+$/.test(item.id) && [...fields,'image'].every(field => typeof item[field] === 'string') && ['draft','active'].includes(item.status))) throw new Error('Invalid preview');
            return parsed;
        } catch { notify('Saved product previews could not be loaded. Showing sample data.', true); return structuredClone(seeds); }
    };
    let categories = [{id:'1',name:'Swedish walls'},{id:'2',name:'Accessories'},{id:'3',name:'Mats'}];
    try { const saved = sessionStorage.getItem(categoryKey); if (saved) { const data = JSON.parse(saved); if (Array.isArray(data) && data.every(item => typeof item.id === 'string' && typeof item.name === 'string')) categories = data; } } catch { /* Use the sample categories when storage is unavailable. */ }
    const categoryName = id => categories.find(category => category.id === id)?.name || 'Uncategorized';
    const imageURL = value => {
        if (!value) return '';
        if (/^data:image\/(jpeg|png|webp);base64,/i.test(value)) return value;
        try { const url = new URL(value); return url.origin === location.origin ? url.href : ''; } catch { return ''; }
    };
    const setImage = (image, placeholder, source) => { const url = imageURL(source); image.hidden = !url; placeholder.hidden = Boolean(url); if (url) image.src = url; else image.removeAttribute('src'); };
    const badge = (element, status) => { element.textContent = status === 'active' ? 'Active' : 'Draft'; element.dataset.state = status; };
    const addCategories = select => categories.forEach(category => { const option = document.createElement('option'); option.value = category.id; option.textContent = category.name; select.append(option); });
    let items = load();

    const index = document.querySelector('[data-products-index]');
    if (index) {
        const search = index.querySelector('[data-search]'), category = index.querySelector('[data-category-filter]'), status = index.querySelector('[data-status-filter]'), sort = index.querySelector('[data-sort]');
        addCategories(category);
        const render = () => {
            const selected = selectProducts(items, search.value, category.value, status.value, sort.value);
            const fragment = document.createDocumentFragment();
            selected.forEach(item => {
                const row = document.getElementById('admin-product-row').content.cloneNode(true);
                row.querySelector('[data-name]').textContent = item.name;
                row.querySelector('[data-name]').href = `${base}/${item.id}`;
                row.querySelector('[data-meta]').textContent = `${categoryName(item.category)} · ${item.sku || 'No SKU'}`;
                row.querySelector('[data-price]').textContent = displayPrice(item);
                row.querySelector('[data-stock]').textContent = item.stock === '' ? 'Not set' : item.stock === '0' ? 'Out of stock' : `${item.stock} units`;
                badge(row.querySelector('[data-status]'), item.status);
                setImage(row.querySelector('img'), row.querySelector('.ap-thumbnail span'), item.image);
                for (const [selector, suffix] of [['[data-view]',''], ['[data-edit]','/edit']]) { const link = row.querySelector(selector); link.href = `${base}/${item.id}${suffix}`; link.setAttribute('aria-label', `${suffix ? 'Edit' : 'View'} ${item.name}`); }
                fragment.append(row);
            });
            index.querySelector('[data-product-list]').replaceChildren(fragment);
            index.querySelector('[data-total]').textContent = String(items.length);
            index.querySelector('[data-active]').textContent = String(items.filter(item => item.status === 'active').length);
            index.querySelector('[data-drafts]').textContent = String(items.filter(item => item.status === 'draft').length);
            index.querySelector('[data-empty]').hidden = selected.length > 0;
            index.querySelector('[data-count]').textContent = `Showing ${selected.length} of ${items.length} products`;
        };
        index.querySelector('[data-tools]').hidden = false;
        search.addEventListener('input', render); [category,status,sort].forEach(control => control.addEventListener('change', render));
        index.querySelector('[data-reset]').addEventListener('click', () => { search.value=''; category.value='all'; status.value='all'; sort.value='name'; render(); search.focus(); });
        window.addEventListener('pageshow', () => { items = load(); render(); });
        render();
    }

    const show = document.querySelector('[data-product-show]');
    if (show) {
        const renderShow = () => {
            const item = load().find(item => item.id === show.dataset.id);
            if (!item) { show.hidden=true; document.querySelector('[data-not-found]').hidden=false; document.querySelector('[data-edit-product]').hidden=true; return; }
            document.querySelector('[data-not-found]').hidden=true; show.hidden=false;
            const edit = document.querySelector('[data-edit-product]'); edit.hidden=false; edit.href=`${base}/${item.id}/edit`;
            document.querySelector('.wg-admin-page-heading h1').textContent = item.name;
            document.title = `${item.name} | WallGym Admin`;
            for (const field of ['name','slug','sku','dimensions','material','description']) show.querySelector(`[data-show-${field}]`).textContent = item[field] || (field === 'description' ? 'No description added yet.' : 'Not set');
            show.querySelector('[data-show-category]').textContent = categoryName(item.category);
            show.querySelector('[data-show-price]').textContent = displayPrice(item) === 'Not set' ? 'Price not set' : displayPrice(item);
            show.querySelector('[data-show-stock]').textContent = item.stock === '' ? 'Not set' : `${item.stock} units`;
            badge(show.querySelector('[data-show-status]'), item.status);
            setImage(show.querySelector('[data-show-image]'), show.querySelector('[data-show-placeholder]'), item.image);
            show.querySelector('[data-show-image]').alt = item.name;
        };
        window.addEventListener('pageshow', renderShow); renderShow();
        if (new URLSearchParams(location.search).get('preview') === 'saved') notify('Product saved in this browser tab. No changes were published to your store.');
    }

    const form = document.querySelector('[data-product-form]');
    if (form) {
        const editing = form.dataset.mode === 'edit';
        const record = editing ? items.find(item => item.id === form.dataset.id) : Object.fromEntries([...fields,'image'].map(field => [field, field === 'status' ? 'draft' : '']));
        if (!record) { form.hidden=true; document.querySelector('[data-not-found]').hidden=false; }
        else {
            const field = name => form.elements.namedItem(name), save = form.querySelector('[data-save]');
            addCategories(field('category'));
            if (record.category && !categories.some(category => category.id === record.category)) { const option=document.createElement('option'); option.value=record.category; option.textContent='Previous category (not in this preview)'; field('category').append(option); }
            fields.forEach(name => { field(name).value = record[name]; });
            let image = imageURL(record.image), dirty=false, slugEdited=editing, pending=false, version=0;
            const imageInput = form.querySelector('#ap-image'), imageError = form.querySelector('#ap-image-error');
            const update = () => {
                form.querySelector('[data-preview-name]').textContent = field('name').value.trim() || 'Your product name';
                form.querySelector('[data-preview-description]').textContent = field('description').value.trim().slice(0,180) || 'A short description will appear here.';
                form.querySelector('[data-preview-category]').textContent = field('category').value ? categoryName(field('category').value) : 'Choose a category';
                const price = {price:field('price').value,currency:field('currency').value};
                form.querySelector('[data-preview-price]').textContent = displayPrice(price) === 'Not set' ? 'Price not set' : displayPrice(price);
                form.querySelector('[data-description-count]').textContent = `${field('description').value.length} / 2000`;
                badge(form.querySelector('[data-preview-status]'), field('status').value);
                setImage(form.querySelector('[data-preview-image]'), form.querySelector('[data-preview-placeholder]'), image);
                form.querySelector('[data-remove-image]').hidden = !image;
            };
            field('name').addEventListener('input', () => { if (!slugEdited) { field('slug').value=productSlug(field('name').value); field('slug').setCustomValidity(''); } });
            field('slug').addEventListener('input', () => { slugEdited=true; });
            form.querySelector('[data-generate-slug]').addEventListener('click', () => { slugEdited=false; field('slug').value=productSlug(field('name').value); field('slug').setCustomValidity(''); dirty=true; update(); });
            form.addEventListener('input', () => { fields.forEach(name => field(name).setCustomValidity('')); dirty=true; update(); });
            form.addEventListener('change', () => { fields.forEach(name => field(name).setCustomValidity('')); dirty=true; update(); });
            form.querySelector('[data-image-name]').textContent = image ? 'Current product image' : 'No image selected';
            imageInput.addEventListener('change', async () => {
                const token=++version; pending=false; save.disabled=false; imageError.hidden=true;
                const file=imageInput.files[0]; if (!file) return;
                if (!['image/jpeg','image/png','image/webp'].includes(file.type) || file.size>1024*1024) { imageInput.value=''; imageError.textContent='Choose a JPG, PNG, or WebP image no larger than 1 MB.'; imageError.hidden=false; return; }
                pending=true; save.disabled=true;
                try {
                    const data = await new Promise((resolve,reject) => { const reader=new FileReader(); reader.onload=()=>resolve(reader.result); reader.onerror=reject; reader.readAsDataURL(file); });
                    const test=new Image(); test.src=data; await test.decode();
                    if (token!==version) return;
                    image=data; form.querySelector('[data-image-name]').textContent=file.name; update();
                } catch { if (token===version) { imageError.hidden=false; imageError.textContent='This image could not be opened. Choose another file.'; } }
                finally { if (token===version) { pending=false; save.disabled=false; } }
            });
            form.querySelector('[data-remove-image]').addEventListener('click', () => { version++; pending=false; save.disabled=false; image=''; imageInput.value=''; imageError.hidden=true; form.querySelector('[data-image-name]').textContent='No image selected'; dirty=true; update(); });
            form.addEventListener('submit', event => {
                event.preventDefault(); if (pending) return;
                items=load();
                let id=record.id || String(Date.now()); while (!record.id && items.some(item=>item.id===id)) id=String(Number(id)+1);
                const product={id, image}; fields.forEach(name => { product[name]=field(name).value.trim(); field(name).value=product[name]; field(name).setCustomValidity(''); });
                const errors=validateProduct(product,items); Object.entries(errors).forEach(([name,message])=>field(name).setCustomValidity(message));
                if (!form.reportValidity()) return;
                if (product.price!=='') product.price=Number(product.price).toFixed(2);
                if (product.stock!=='') product.stock=String(Number(product.stock));
                const next=editing ? items.map(item=>item.id===record.id ? product : item) : [...items,product];
                try { sessionStorage.setItem(key,JSON.stringify(next)); }
                catch { notify('Unable to save this preview. Try a smaller image or enable browser session storage. Your form has been kept.',true); feedback.scrollIntoView({block:'center'}); return; }
                dirty=false; window.location.assign(`${base}/${id}?preview=saved`);
            });
            window.addEventListener('beforeunload', event => { if (dirty) { event.preventDefault(); event.returnValue=''; } });
            save.disabled=false; update();
        }
    }
}
