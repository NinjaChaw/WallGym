export function normalizeQuantity(value) {
    const number = Number(value);
    return Number.isFinite(number) ? Math.min(99, Math.max(1, Math.floor(number))) : 1;
}

const page = typeof document !== 'undefined' ? document.querySelector('[data-product-page]') : null;
if (page) {
    const quantity = page.querySelector('#product-quantity');
    const decrease = page.querySelector('[data-decrease]');
    const increase = page.querySelector('[data-increase]');
    const setQuantity = value => {
        const count = normalizeQuantity(value);
        quantity.value = String(count);
        decrease.disabled = count === 1;
        increase.disabled = count === 99;
    };
    decrease.addEventListener('click', () => setQuantity(normalizeQuantity(quantity.value) - 1));
    increase.addEventListener('click', () => setQuantity(normalizeQuantity(quantity.value) + 1));
    quantity.addEventListener('change', () => setQuantity(quantity.value));
    page.querySelector('[data-quantity-control]').hidden = false;
    setQuantity(quantity.value);

    const main = page.querySelector('[data-main-image]');
    const thumbnails = [...page.querySelectorAll('[data-gallery-image]')];
    if (thumbnails.length > 1) page.querySelector('[data-thumbnails]').hidden = false;
    thumbnails.forEach(button => button.addEventListener('click', () => {
        main.removeAttribute('srcset');
        main.src = button.dataset.galleryImage;
        main.alt = button.dataset.alt;
        thumbnails.forEach(item => item.setAttribute('aria-pressed', String(item === button)));
        page.querySelector('[data-gallery-label]').textContent = button.dataset.label;
    }));

    const dialog = page.querySelector('[data-lightbox]');
    const zoom = page.querySelector('[data-open-zoom]');
    if (typeof dialog.showModal === 'function') {
        zoom.hidden = false;
        zoom.addEventListener('click', () => {
            const image = dialog.querySelector('[data-zoom-image]');
            const selected = thumbnails.find(button => button.getAttribute('aria-pressed') === 'true');
            image.src = selected?.dataset.galleryImage || main.src;
            image.alt = main.alt;
            dialog.showModal();
        });
        dialog.querySelector('[data-close-zoom]').addEventListener('click', () => dialog.close());
        dialog.addEventListener('close', () => zoom.focus());
        dialog.addEventListener('click', event => {
            if (event.target !== dialog) return;
            const bounds = dialog.getBoundingClientRect();
            if (event.clientX < bounds.left || event.clientX > bounds.right || event.clientY < bounds.top || event.clientY > bounds.bottom) dialog.close();
        });
    }
}
