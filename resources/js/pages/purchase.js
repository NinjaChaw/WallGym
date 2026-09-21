// Live checkout uses server-calculated quotes; legacy preview code below is not mounted here.
const checkout = typeof document !== 'undefined' ? document.querySelector('[data-checkout-real]') : null;
if (checkout) {
    const zone = checkout.querySelector('[name="zone"]');
    const delivery = checkout.querySelector('[data-checkout-delivery]');
    const total = checkout.querySelector('[data-checkout-total]');
    const feedback = checkout.querySelector('[data-quote-error]');
    const money = amount => `BDT ${(amount / 100).toLocaleString('en', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    let pending;
    zone.addEventListener('change', async () => {
        pending?.abort();
        feedback.hidden = true;
        if (!zone.value) {
            delivery.textContent = 'Select your area';
            total.textContent = 'Choose delivery zone';
            return;
        }
        const request = new AbortController();
        pending = request;
        delivery.textContent = 'Updating…';
        total.textContent = 'Updating…';
        try {
            const url = new URL(checkout.dataset.quoteUrl, window.location.href);
            url.searchParams.set('zone', zone.value);
            const response = await fetch(url, { headers: { Accept: 'application/json' }, credentials: 'same-origin', signal: request.signal });
            const data = await response.json();
            if (request.signal.aborted) return;
            if (!response.ok) throw new Error(data.message || 'Unable to update delivery.');
            checkout.querySelector('[data-checkout-subtotal]').textContent = money(data.subtotal);
            delivery.textContent = money(data.delivery);
            total.textContent = money(data.total);
        } catch (error) {
            if (request.signal.aborted) return;
            delivery.textContent = 'Select delivery zone again';
            total.textContent = 'Unable to calculate';
            feedback.textContent = `${error.message} Please refresh checkout and check the total before placing your order.`;
            feedback.hidden = false;
        }
    });
}

export const catalog = {
    'swedish-wall': {name: 'Swedish Wall', price: 1850000, image: 'images/hero/wallgym-interior-640.jpg'},
    'gymnastic-rings': {name: 'Gymnastic Rings', price: 240000, image: 'images/collection/gymnastic-rings-480.jpg'},
    'exercise-mat': {name: 'Exercise Mat', price: 180000, image: 'images/collection/exercise-mat-480.jpg'},
};
export const validItems = items => Array.isArray(items) && items.length <= 3 && new Set(items.map(item => item?.id)).size === items.length && items.every(item => item && Object.hasOwn(catalog, item.id) && Number.isInteger(item.quantity) && item.quantity >= 1 && item.quantity <= 10);
export function totals(items, zone = '') {
    const subtotal = items.reduce((sum, item) => sum + catalog[item.id].price * item.quantity, 0);
    const delivery = zone === 'dhaka' ? 8000 : zone === 'outside' ? 15000 : null;
    return {subtotal, delivery, total: delivery === null ? null : subtotal + delivery};
}
export function validateCheckout(values) {
    const errors = {};
    for (const key of ['name','phone','address','area','city']) if (!values[key]?.trim()) errors[key] = 'Please complete this field.';
    if (values.phone?.trim() && !/^(?:\+?880|0)1[3-9]\d{8}$/.test(values.phone.replace(/[\s()-]/g, ''))) errors.phone = 'Enter a valid Bangladesh mobile number.';
    if (!['dhaka','outside'].includes(values.zone)) errors.zone = 'Choose your delivery zone.';
    return errors;
}

const root = typeof document !== 'undefined' ? document.querySelector('[data-purchase]') : null;
if (root) {
    const base = root.dataset.base.replace(/\/$/, '');
    const key = `wallgym:purchase-preview:v1:${new URL(base).pathname}`;
    const money = value => `BDT ${(value / 100).toLocaleString('en', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
    const feedback = root.querySelector('[data-feedback]');
    const notify = (message, error = false) => { feedback.hidden = false; feedback.textContent = message; feedback.toggleAttribute('data-error', error); };
    const initial = () => ({items: [{id:'swedish-wall',quantity:1},{id:'gymnastic-rings',quantity:1}], receipt:null});
    let state = initial();
    try {
        const raw = sessionStorage.getItem(key);
        if (raw) {
            const saved = JSON.parse(raw);
            if (!validItems(saved.items)) throw new Error();
            const receipt = saved.receipt;
            if (receipt !== null && (!receipt || !validItems(receipt.items) || !receipt.items.length || typeof receipt.reference !== 'string' || !receipt.customer || ['name','phone','email','address','area','city','zone','notes'].some(field => typeof receipt.customer[field] !== 'string') || Object.keys(validateCheckout(receipt.customer)).length)) throw new Error();
            state = saved;
        }
    } catch { notify('Saved preview could not be loaded. Showing a fresh sample cart.', true); }
    const saveState = next => {
        try { sessionStorage.setItem(key, JSON.stringify(next)); state = next; return true; }
        catch { notify('This preview needs browser session storage. Your changes could not be saved; please enable it and try again.', true); return false; }
    };
    const updateCount = () => {
        const count = document.getElementById('cart-count');
        if (count?.hasAttribute('data-session-cart')) return;
        if (count) { const number = state.items.reduce((sum,item)=>sum+item.quantity,0); count.textContent=number; count.classList.toggle('hidden',number===0); count.classList.toggle('flex',number>0); }
    };
    const renderTotals = (items, zone) => {
        const value = totals(items, zone);
        root.querySelector('[data-subtotal]').textContent = money(value.subtotal);
        root.querySelector('[data-delivery]').textContent = value.delivery === null ? root.dataset.purchase === 'cart' ? 'At checkout' : 'Select your area' : money(value.delivery);
        root.querySelector('[data-total]').textContent = root.dataset.purchase === 'cart' ? money(value.subtotal) : value.total === null ? 'Choose delivery zone' : money(value.total);
    };
    const miniItems = items => {
        const list = root.querySelector('[data-mini-items]');
        list.replaceChildren();
        items.forEach(item => {
            const product = catalog[item.id], li = document.createElement('li'), image = document.createElement('img'), info = document.createElement('div'), quantity = document.createElement('small'), price = document.createElement('strong');
            image.src = `${base}/${product.image}`; image.alt = ''; image.width = 46; image.height = 56;
            info.textContent = product.name; quantity.textContent = `Quantity ${item.quantity}`; info.append(quantity); price.textContent = money(product.price * item.quantity);
            li.append(image,info,price); list.append(li);
        });
    };
    const mode = root.dataset.purchase;
    if (mode === 'cart') {
        const render = () => {
            root.querySelector('[data-filled]').hidden = !state.items.length;
            root.querySelector('[data-empty]').hidden = !!state.items.length;
            const list = root.querySelector('[data-cart-items]'); list.replaceChildren();
            const count = state.items.reduce((sum,item)=>sum+item.quantity,0);
            root.querySelector('[data-count]').textContent = `${count} ${count === 1 ? 'item' : 'items'}`;
            state.items.forEach(item => {
                const product = catalog[item.id], row = root.querySelector('#cart-item').content.cloneNode(true);
                row.querySelector('[data-image]').src = `${base}/${product.image}`;
                const link = row.querySelector('[data-product-link]'); link.textContent = product.name; link.href = `${base}/shop/${item.id}`;
                row.querySelector('[data-unit-price]').textContent = `${money(product.price)} each`;
                row.querySelector('[data-line-total]').textContent = money(product.price*item.quantity);
                row.querySelector('[data-quantity]').textContent = item.quantity;
                for (const action of ['minus','plus','remove']) {
                    const button = row.querySelector(`[data-${action}]`);
                    button.dataset.focus = `${item.id}-${action}`;
                    button.setAttribute('aria-label', `${action === 'remove' ? 'Remove' : action === 'plus' ? 'Increase quantity of' : 'Decrease quantity of'} ${product.name}`);
                    button.disabled = action === 'minus' && item.quantity === 1 || action === 'plus' && item.quantity === 10;
                    button.addEventListener('click', () => {
                        const items = action === 'remove' ? state.items.filter(value=>value.id!==item.id) : state.items.map(value=>value.id===item.id ? {...value,quantity:value.quantity+(action==='plus'?1:-1)} : value);
                        if (!saveState({...state,items})) return;
                        render(); notify(action === 'remove' ? `${product.name} removed from your cart.` : `${product.name}: quantity ${items.find(value=>value.id===item.id).quantity}.`);
                        const target = root.querySelector(`[data-focus="${item.id}-${action}"]:not(:disabled)`) || root.querySelector('[data-remove]') || root.querySelector('[data-reset]'); target?.focus();
                    });
                }
                list.append(row);
            });
            renderTotals(state.items, ''); updateCount();
        };
        root.querySelector('[data-reset]').addEventListener('click',()=>{ if(saveState(initial())) {render(); root.querySelector('[data-remove]')?.focus();notify('Sample cart loaded.');} });
        root.querySelector('[data-checkout-link]').addEventListener('click', event => { if (!saveState(state)) {event.preventDefault(); feedback.focus();} });
        render();
    }
    if (mode === 'checkout') {
        root.querySelector('[data-filled]').hidden = !state.items.length;
        root.querySelector('[data-empty]').hidden = !!state.items.length;
        const form = root.querySelector('form'), button = root.querySelector('[data-place-order]');
        const controls = [...form.querySelectorAll('input:not([type="radio"]), select, textarea')];
        if (state.items.length) {miniItems(state.items); renderTotals(state.items, form.elements.zone.value); button.disabled=false;}
        controls.forEach(control => control.addEventListener('input', () => {
            control.removeAttribute('aria-invalid');
            const error = root.querySelector(`[data-error="${control.name}"]`); if(error)error.textContent='';
            if(control.name==='zone') renderTotals(state.items, control.value);
        }));
        let submitted = false;
        form.addEventListener('submit', event => {
            event.preventDefault(); if(submitted || !state.items.length)return;
            const customer = Object.fromEntries(controls.map(control=>[control.name,control.value.trim()]));
            const errors = validateCheckout(customer);
            controls.forEach(control => {if (!control.validity.valid && !errors[control.name]) errors[control.name] = control.type==='email'?'Enter a valid email address.':'Please check this field.';});
            controls.forEach(control => {const error=root.querySelector(`[data-error="${control.name}"]`);if(error)error.textContent=errors[control.name]||'';control.toggleAttribute('data-invalid',!!errors[control.name]);control.setAttribute('aria-invalid',errors[control.name]?'true':'false');});
            if(Object.keys(errors).length) {form.elements.namedItem(Object.keys(errors)[0]).focus(); return;}
            const receipt = {reference:`PREVIEW-${Date.now().toString().slice(-8)}`,items:state.items.map(item=>({...item})),customer};
            if(!saveState({items:[],receipt})) {feedback.focus();return;}
            submitted=true;button.disabled=true;button.textContent='Opening your preview…';location.assign(`${base}/checkout/success`);
        });
    }
    if (mode === 'success') {
        const receipt = state.receipt;
        root.querySelector('[data-empty]').hidden = !!receipt;
        root.querySelector('[data-receipt]').hidden = !receipt;
        if(receipt) {
            miniItems(receipt.items);renderTotals(receipt.items,receipt.customer.zone);
            root.querySelector('[data-reference]').textContent=receipt.reference;
            root.querySelector('[data-address]').textContent=[receipt.customer.name,receipt.customer.address,receipt.customer.area,receipt.customer.city,'Bangladesh'].join('\n');
            root.querySelector('[data-contact]').textContent=[receipt.customer.phone,receipt.customer.email].filter(Boolean).join('\n');
        }
    }
    updateCount();
    // Reload restored history entries so completed orders do not reappear in an old cart.
    window.addEventListener('pageshow',event=>{if(event.persisted)location.reload();});
}
