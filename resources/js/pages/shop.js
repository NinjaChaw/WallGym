const shop = document.querySelector('[data-shop]');
if (shop && typeof HTMLDialogElement !== 'undefined' && 'showModal' in HTMLDialogElement.prototype) {
    shop.querySelectorAll('[data-quick-look]').forEach(button => {
        const dialog = document.getElementById(button.dataset.quickLook);
        if (!dialog) return;
        button.hidden = false;
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
