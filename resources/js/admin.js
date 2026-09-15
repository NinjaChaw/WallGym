const sidebar = document.getElementById('admin-sidebar');
const workspace = document.getElementById('admin-workspace');
const toggle = document.querySelector('[data-sidebar-open]');
const backdrop = document.querySelector('.wg-sidebar-backdrop');

if (sidebar && workspace && toggle && backdrop) {
    const desktop = window.matchMedia('(min-width: 1200px)');
    let open = false;
    let previousOverflow = '';
    const close = (restoreFocus = false) => {
        if (open) document.body.style.overflow = previousOverflow;
        open = false;
        document.body.removeAttribute('data-navigation-open');
        backdrop.hidden = true;
        workspace.inert = false;
        sidebar.inert = !desktop.matches;
        sidebar.removeAttribute('role');
        sidebar.removeAttribute('aria-modal');
        toggle.setAttribute('aria-expanded', 'false');
        if (restoreFocus && !desktop.matches) toggle.focus();
    };
    toggle.addEventListener('click', () => {
        if (desktop.matches || open) return;
        previousOverflow = document.body.style.overflow;
        open = true;
        sidebar.inert = false;
        sidebar.setAttribute('role', 'dialog');
        sidebar.setAttribute('aria-modal', 'true');
        document.body.setAttribute('data-navigation-open', '');
        document.body.style.overflow = 'hidden';
        backdrop.hidden = false;
        toggle.setAttribute('aria-expanded', 'true');
        workspace.inert = true;
        sidebar.querySelector('[data-sidebar-close]').focus();
    });
    document.querySelectorAll('[data-sidebar-close]').forEach(button => button.addEventListener('click', () => close(true)));
    sidebar.addEventListener('click', event => { if (event.target.closest('a') && open) close(true); });
    document.addEventListener('keydown', event => {
        if (!open) return;
        if (event.key === 'Escape') { event.preventDefault(); close(true); return; }
        if (event.key !== 'Tab') return;
        const items = [...sidebar.querySelectorAll('a[href], button:not(:disabled), [tabindex="0"]')].filter(element => element.getClientRects().length > 0);
        const first = items[0], last = items[items.length - 1];
        if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
        else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
    });
    desktop.addEventListener('change', () => {
        const focusInSidebar = sidebar.contains(document.activeElement);
        close(!desktop.matches && focusInSidebar);
        if (desktop.matches && document.activeElement?.matches('[data-sidebar-close], [data-sidebar-open]')) sidebar.querySelector('a').focus();
    });
    window.addEventListener('pageshow', () => close());
    close();
}
