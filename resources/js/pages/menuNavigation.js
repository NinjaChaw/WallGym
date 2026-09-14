const button = document.getElementById('mobile-menu-button');
const menu = document.getElementById('mobile-menu');

if (button && menu) {
    const label = document.getElementById('mobile-menu-label');
    const openIcon = document.getElementById('menu-open-icon');
    const closeIcon = document.getElementById('menu-close-icon');
    const desktop = window.matchMedia('(min-width: 1280px)');

    const setOpen = (open, returnFocus = false) => {
        menu.hidden = !open;
        button.setAttribute('aria-expanded', String(open));
        button.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
        if (label) label.textContent = open ? 'Close' : 'Menu';
        openIcon?.classList.toggle('hidden', open);
        closeIcon?.classList.toggle('hidden', !open);
        if (returnFocus) button.focus();
    };

    button.addEventListener('click', () => setOpen(menu.hidden));
    menu.addEventListener('click', (event) => {
        if (event.target.closest('a')) setOpen(false);
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !menu.hidden) setOpen(false, true);
    });
    document.addEventListener('click', (event) => {
        if (!menu.hidden && !menu.contains(event.target) && !button.contains(event.target)) setOpen(false);
    });
    document.addEventListener('focusin', (event) => {
        if (!menu.hidden && !menu.contains(event.target) && !button.contains(event.target)) setOpen(false);
    });
    desktop.addEventListener('change', () => {
        if (desktop.matches) setOpen(false);
    });
    window.addEventListener('pageshow', () => setOpen(false));
}
