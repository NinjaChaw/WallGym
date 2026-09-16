const form = document.querySelector('[data-settings-form]');
if (form) {
    const key = `wallgym:settings-preview:v1:${location.pathname}`;
    const fields = form.querySelector('[data-settings-fields]');
    const save = form.querySelector('[data-save]');
    const state = form.querySelector('[data-save-state]');
    const feedback = form.querySelector('[data-settings-feedback]');
    const controls = [...fields.querySelectorAll('[name]')];
    const images = {logo: null, favicon: null};
    let dirty = false;
    let pending = 0;
    const notify = (message, error = false) => {
        feedback.hidden = false;
        feedback.textContent = message;
        feedback.toggleAttribute('data-error', error);
    };
    const markDirty = () => {
        dirty = true;
        state.textContent = 'Unsaved changes';
        feedback.hidden = true;
    };
    const refresh = () => {
        const title = form.elements.namedItem('site_title').value;
        const description = form.elements.namedItem('meta_description').value;
        form.querySelector('[data-title-count]').textContent = `${title.length} / 70 characters`;
        form.querySelector('[data-description-count]').textContent = `${description.length} / 160 characters`;
        form.querySelector('[data-seo-title]').textContent = title.trim() || 'Your site title';
        form.querySelector('[data-seo-description]').textContent = description.trim() || 'Your store description will appear here.';
        form.querySelector('[data-payment-warning]').hidden = form.elements.namedItem('cash_on_delivery').checked;
    };
    const renderImage = (type) => {
        const card = form.querySelector(`[data-upload="${type}"]`);
        const img = card.querySelector('[data-image]');
        img.hidden = !images[type];
        if (images[type]) img.src = images[type].data;
        else img.removeAttribute('src');
        card.querySelector('[data-placeholder]').hidden = !!images[type];
        card.querySelector('[data-remove]').hidden = !images[type];
        card.querySelector('[data-file-name]').textContent = images[type]?.name || 'No image selected';
    };
    try {
        const raw = sessionStorage.getItem(key);
        if (raw) {
            const saved = JSON.parse(raw);
            if (!saved || typeof saved.values !== 'object' || !saved.values || !saved.images) throw new Error();
            for (const control of controls) {
                const value = saved.values[control.name];
                if (control.type === 'checkbox' ? typeof value !== 'boolean' : typeof value !== 'string') throw new Error();
            }
            for (const type of Object.keys(images)) {
                const image = saved.images[type];
                if (image !== null && (!image || typeof image.name !== 'string' || typeof image.data !== 'string' || image.data.length > 1500000 || !/^data:image\/(png|jpeg|webp);base64,[A-Za-z0-9+/=]+$/.test(image.data))) throw new Error();
            }
            controls.forEach(control => {
                if (control.type === 'checkbox') control.checked = saved.values[control.name];
                else control.value = saved.values[control.name];
            });
            Object.assign(images, saved.images);
            state.textContent = 'Saved preview loaded';
        }
    } catch { notify('Saved settings could not be loaded. The sample defaults are shown.', true); }
    Object.keys(images).forEach(type => {
        const card = form.querySelector(`[data-upload="${type}"]`);
        const input = card.querySelector('input');
        const error = card.querySelector('[data-upload-error]');
        let revision = 0;
        renderImage(type);
        input.addEventListener('change', async () => {
            const file = input.files[0];
            if (!file) return;
            const current = ++revision;
            error.hidden = true;
            if (!['image/png', 'image/jpeg', 'image/webp'].includes(file.type) || file.size > 1048576) {
                error.textContent = 'Choose a PNG, JPG, or WebP image up to 1 MB.';
                error.hidden = false;
                input.value = '';
                return;
            }
            pending++;
            save.disabled = true;
            try {
                const data = await new Promise((resolve, reject) => {
                    const reader = new FileReader();
                    reader.onload = () => resolve(reader.result);
                    reader.onerror = reject;
                    reader.readAsDataURL(file);
                });
                const test = new Image();
                test.src = data;
                await test.decode();
                if (current !== revision) return;
                images[type] = {name: file.name, data};
                renderImage(type);
                markDirty();
            } catch {
                if (current === revision) {
                    error.textContent = 'This image could not be opened. Please choose another file.';
                    error.hidden = false;
                }
            } finally {
                pending--;
                save.disabled = pending > 0;
                if (current === revision) input.value = '';
            }
        });
        card.querySelector('[data-remove]').addEventListener('click', () => {
            revision++;
            images[type] = null;
            input.value = '';
            error.hidden = true;
            renderImage(type);
            markDirty();
        });
    });
    controls.forEach(control => {
        control.addEventListener('input', () => {
            control.setCustomValidity('');
            markDirty();
            refresh();
        });
    });
    form.addEventListener('submit', event => {
        event.preventDefault();
        if (pending) return;
        controls.filter(control => control.required && control.type !== 'number').forEach(control => {
            control.setCustomValidity(control.value.trim() ? '' : 'Please complete this field.');
        });
        if (!form.reportValidity()) return;
        const values = Object.fromEntries(controls.map(control => [control.name, control.type === 'checkbox' ? control.checked : control.value.trim()]));
        try {
            sessionStorage.setItem(key, JSON.stringify({values, images}));
            dirty = false;
            state.textContent = 'All changes saved to preview';
            notify('Settings saved in this browser tab. Your live store has not been changed.');
        } catch { notify('Settings could not be saved. Browser storage may be unavailable or full. Try smaller images; your changes are still here.', true); }
        feedback.focus();
    });
    window.addEventListener('beforeunload', event => {
        if (dirty || pending) { event.preventDefault(); event.returnValue = ''; }
    });
    fields.disabled = false;
    save.disabled = false;
    refresh();
}
