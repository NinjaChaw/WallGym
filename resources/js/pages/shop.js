// Pure selection logic keeps the catalog independent of a backend.
export function selectProducts(products, category = 'all', query = '', sort = 'featured') {
    const term = query.trim().toLocaleLowerCase();
    return products.filter(product =>
        (category === 'all' || product.category === category) &&
        `${product.name} ${product.category}`.toLocaleLowerCase().includes(term)
    ).sort((a, b) => {
        if (sort === 'name-asc') return a.name.localeCompare(b.name);
        if (sort === 'name-desc') return b.name.localeCompare(a.name);
        return a.index - b.index;
    });
}

const shop = typeof document !== 'undefined' ? document.querySelector('[data-shop]') : null;
if (shop) {
    const grid = shop.querySelector('[data-shop-grid]');
    const search = shop.querySelector('[data-shop-search]');
    const sort = shop.querySelector('[data-shop-sort]');
    const categories = [...shop.querySelectorAll('button[data-category]')];
    const products = [...grid.querySelectorAll('[data-product]')].map(element => ({
        element, name: element.dataset.name, category: element.dataset.category, index: Number(element.dataset.index),
    }));
    let category = 'all';

    const update = () => {
        const selected = selectProducts(products, category, search.value, sort.value);
        const visible = new Set(selected);
        products.forEach(product => { product.element.hidden = !visible.has(product); });
        selected.forEach(product => grid.append(product.element));
        categories.forEach(button => button.setAttribute('aria-pressed', String(button.dataset.category === category)));
        shop.querySelector('[data-shop-count]').textContent = `${selected.length} ${selected.length === 1 ? 'product' : 'products'}`;
        shop.querySelector('[data-shop-empty]').hidden = selected.length > 0;
        grid.hidden = selected.length === 0;
    };

    categories.forEach(button => button.addEventListener('click', () => { category = button.dataset.category; update(); }));
    search.addEventListener('input', update);
    sort.addEventListener('change', update);
    shop.querySelector('[data-shop-reset]').addEventListener('click', () => {
        category = 'all'; search.value = ''; sort.value = 'featured'; update(); search.focus();
    });

    search.value = new URLSearchParams(window.location.search).get('search') || '';
    shop.querySelector('[data-shop-controls]').hidden = false;
    shop.querySelector('[data-shop-sort-control]').hidden = false;
    update();

    if (typeof HTMLDialogElement !== 'undefined' && 'showModal' in HTMLDialogElement.prototype) {
        shop.querySelectorAll('[data-quick-look]').forEach(button => {
            button.hidden = false;
            const dialog = document.getElementById(button.dataset.quickLook);
            button.addEventListener('click', () => dialog.showModal());
            dialog.addEventListener('close', () => button.focus());
            dialog.querySelectorAll('[data-close-dialog]').forEach(close => close.addEventListener('click', () => dialog.close()));
            dialog.addEventListener('click', event => {
                if (event.target !== dialog) return;
                const bounds = dialog.getBoundingClientRect();
                if (event.clientX < bounds.left || event.clientX > bounds.right || event.clientY < bounds.top || event.clientY > bounds.bottom) dialog.close();
            });
        });
    }
}
