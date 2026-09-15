export const fulfillmentStates = ['unfulfilled','processing','shipped','delivered'];
export function orderTotals(order) {
    const subtotal = order.items.reduce((sum, item) => sum + item.unitPrice * item.quantity, 0);
    return { subtotal, discount: order.discount, shipping: order.shipping, total: subtotal - order.discount + order.shipping };
}
export function selectOrders(orders, query = '', payment = 'all', fulfillment = 'all', sort = 'newest') {
    const term = query.trim().replace(/^#/, '').toLowerCase();
    return orders.filter(order => `${order.id} wg-${order.id} ${order.customer} ${order.email}`.toLowerCase().includes(term) && (payment === 'all' || payment === order.payment) && (fulfillment === 'all' || fulfillment === order.fulfillment))
        .sort((a, b) => sort === 'total' ? orderTotals(b).total - orderTotals(a).total : sort === 'oldest' ? a.date.localeCompare(b.date) || Number(a.id)-Number(b.id) : b.date.localeCompare(a.date) || Number(b.id)-Number(a.id));
}
export function validateFulfillment(order, state) {
    if (!fulfillmentStates.includes(state)) return 'Choose a valid fulfillment status.';
    if (order.payment !== 'paid' && state !== 'unfulfilled') return 'Payment is pending. Keep this order unfulfilled until payment is confirmed.';
    return '';
}

const root = typeof document !== 'undefined' ? document.querySelector('[data-admin-orders]') : null;
if (root) {
    const base = root.dataset.baseUrl, key = `wallgym:order-preview:v1:${new URL(base).pathname}`;
    const samples = JSON.parse(document.getElementById('admin-order-seed').textContent);
    const feedback = document.querySelector('[data-feedback]');
    const notify = (text, error=false) => { feedback.hidden=false; feedback.textContent=text; feedback.toggleAttribute('data-error',error); };
    const label = value => value.charAt(0).toUpperCase()+value.slice(1);
    const money = (amount, currency) => `${currency} ${(amount/100).toLocaleString('en', {minimumFractionDigits:2,maximumFractionDigits:2})}`;
    const date = value => new Intl.DateTimeFormat('en-GB',{day:'numeric',month:'short',year:'numeric',timeZone:'UTC'}).format(new Date(value));
    const setBadge = (element, value) => { element.textContent=label(value); element.dataset.state=value; };
    const readChanges = () => {
        try {
            const value=JSON.parse(sessionStorage.getItem(key)||'{}');
            if (!value || Array.isArray(value) || typeof value!=='object') throw new Error('Invalid preview');
            for (const [id,change] of Object.entries(value)) {
                if (!samples.some(order=>order.id===id) || !fulfillmentStates.includes(change.fulfillment) || typeof change.carrier!=='string' || typeof change.tracking!=='string' || !Array.isArray(change.events) || !change.events.every(event=>['note','fulfillment'].includes(event.type) && typeof event.text==='string' && !Number.isNaN(Date.parse(event.at)))) throw new Error('Invalid preview');
            }
            return value;
        } catch { notify('Saved order previews could not be loaded. Showing sample orders.',true); return {}; }
    };
    let changes=readChanges();
    const getOrders=()=>samples.map(order=>({...order,...(changes[order.id]||{}),events:changes[order.id]?.events||[]}));
    const persist=(order, event, fields={})=>{
        const current=readChanges();
        const previous=current[order.id]||{fulfillment:order.fulfillment,carrier:order.carrier,tracking:order.tracking,events:[]};
        const next={...current,[order.id]:{...previous,...fields,events:[...previous.events,event]}};
        try { sessionStorage.setItem(key,JSON.stringify(next)); changes=next; return true; }
        catch { return false; }
    };
    const index=document.querySelector('[data-orders-index]');
    if (index) {
        const search=index.querySelector('[data-search]'),payment=index.querySelector('[data-payment-filter]'),fulfillment=index.querySelector('[data-fulfillment-filter]'),sort=index.querySelector('[data-sort]');
        const render=()=>{
            const all=getOrders(), filtered=selectOrders(all,search.value,payment.value,fulfillment.value,sort.value),fragment=document.createDocumentFragment();
            filtered.forEach(order=>{
                const row=document.getElementById('order-row-template').content.cloneNode(true);
                const link=row.querySelector('[data-order-link]'); link.textContent=`#WG-${order.id}`; link.href=`${base}/${order.id}`;
                row.querySelector('[data-customer]').textContent=order.customer;
                row.querySelector('[data-date]').textContent=date(order.date);
                row.querySelector('[data-total]').textContent=money(orderTotals(order).total,order.currency);
                setBadge(row.querySelector('[data-payment]'),order.payment);setBadge(row.querySelector('[data-fulfillment]'),order.fulfillment);
                row.querySelector('[data-view]').href=`${base}/${order.id}`; row.querySelector('[data-view]').setAttribute('aria-label',`View order WG-${order.id}`);
                fragment.append(row);
            });
            index.querySelector('[data-order-list]').replaceChildren(fragment);
            index.querySelector('[data-empty]').hidden=filtered.length>0;
            index.querySelector('[data-total]').textContent=String(all.length);
            index.querySelector('[data-paid]').textContent=String(all.filter(order=>order.payment==='paid').length);
            index.querySelector('[data-to-fulfill]').textContent=String(all.filter(order=>order.payment==='paid'&&['unfulfilled','processing'].includes(order.fulfillment)).length);
            index.querySelector('[data-count]').textContent=`Showing ${filtered.length} of ${all.length} orders`;
        };
        index.querySelector('[data-tools]').hidden=false;
        search.addEventListener('input',render);[payment,fulfillment,sort].forEach(control=>control.addEventListener('change',render));
        index.querySelector('[data-reset]').addEventListener('click',()=>{search.value='';payment.value='all';fulfillment.value='all';sort.value='newest';render();search.focus();});
        window.addEventListener('pageshow',()=>{changes=readChanges();render();});render();
    }
    const show=document.querySelector('[data-order-show]');
    if (show) {
        let order=getOrders().find(order=>order.id===show.dataset.id);
        if (!order) document.querySelector('[data-not-found]').hidden=false;
        else {
            show.hidden=false;
            const dialog=document.querySelector('[data-update-dialog]'),opener=document.querySelector('[data-open-update]'),updateForm=dialog.querySelector('form');
            const noteForm=show.querySelector('[data-note-form]'),note=noteForm.elements.namedItem('note');
            const render=()=>{
                order=getOrders().find(item=>item.id===show.dataset.id);
                document.querySelector('.wg-admin-page-heading h1').textContent=`Order #WG-${order.id}`;
                document.title=`Order WG-${order.id} | WallGym Admin`;
                show.querySelector('[data-placed]').textContent=`Placed ${date(order.date)}`;
                ['[data-payment-badge]','[data-summary-payment]'].forEach(selector=>setBadge(show.querySelector(selector),order.payment));
                ['[data-fulfillment-badge]','[data-delivery-status]'].forEach(selector=>setBadge(show.querySelector(selector),order.fulfillment));
                const fragment=document.createDocumentFragment();
                order.items.forEach(item=>{
                    const row=document.getElementById('order-item-template').content.cloneNode(true);
                    row.querySelector('img').src=item.image;
                    row.querySelector('[data-item-name]').textContent=item.name;row.querySelector('[data-item-sku]').textContent=item.sku;
                    row.querySelector('[data-item-price]').textContent=`${money(item.unitPrice,order.currency)} each`;
                    row.querySelector('[data-item-quantity]').textContent=`Qty ${item.quantity}`;row.querySelector('[data-item-total]').textContent=money(item.unitPrice*item.quantity,order.currency);
                    fragment.append(row);
                });
                show.querySelector('[data-items]').replaceChildren(fragment);
                const quantity=order.items.reduce((sum,item)=>sum+item.quantity,0);show.querySelector('[data-item-count]').textContent=`${quantity} ${quantity===1?'item':'items'} in this order`;
                const totals=orderTotals(order);
                show.querySelector('[data-subtotal]').textContent=money(totals.subtotal,order.currency);
                show.querySelector('[data-discount]').textContent=`${totals.discount?'−':''}${money(totals.discount,order.currency)}`;
                show.querySelector('[data-shipping]').textContent=money(totals.shipping,order.currency);
                show.querySelector('[data-grand-total]').textContent=money(totals.total,order.currency);
                show.querySelector('[data-customer-name]').textContent=order.customer;show.querySelector('[data-customer-email]').textContent=order.email;show.querySelector('[data-address]').textContent=order.address;
                show.querySelector('[data-initials]').textContent=order.customer.split(' ').map(part=>part[0]).slice(0,2).join('');
                show.querySelector('[data-carrier]').textContent=order.carrier||'Not assigned';show.querySelector('[data-tracking]').textContent=order.tracking||'Not added';
                show.querySelector('[data-delivery-hint]').textContent=order.payment==='pending'?'Payment is pending. Fulfillment should wait until it is confirmed.':'Tracking is a preview reference only; it is not connected to a carrier.';
                const seed=samples.find(item=>item.id===order.id);
                const events=[{type:'created',text:'Sample order created.',at:`${seed.date}T09:00:00Z`},{type:'payment',text:seed.payment==='paid'?'Sample payment marked paid.':'Sample payment is pending.',at:`${seed.date}T09:01:00Z`}];
                if (seed.fulfillment!=='unfulfilled') events.push({type:'fulfillment',text:`Sample fulfillment: ${label(seed.fulfillment)}.`,at:`${seed.date}T15:00:00Z`});
                events.push(...order.events);
                const timeline=document.createDocumentFragment();
                events.slice().reverse().forEach(event=>{
                    const li=document.createElement('li'),heading=document.createElement('h3'),text=document.createElement('p'),time=document.createElement('time');
                    heading.textContent=event.type==='note'?'Internal note':event.type==='fulfillment'?'Fulfillment update':event.type==='payment'?'Payment record':'Order created';text.textContent=event.text;time.dateTime=event.at;time.textContent=new Intl.DateTimeFormat('en-GB',{day:'numeric',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'}).format(new Date(event.at));
                    li.append(heading,text,time);timeline.append(li);
                });
                show.querySelector('[data-timeline]').replaceChildren(timeline);
            };
            note.addEventListener('input',()=>note.setCustomValidity(''));
            noteForm.addEventListener('submit',event=>{
                event.preventDefault();const text=note.value.trim();note.setCustomValidity(text?'':'Enter a note before saving.');if(!noteForm.reportValidity())return;
                if (!persist(order,{type:'note',text,at:new Date().toISOString()})) {notify('Your note could not be saved. Enable browser session storage and try again. The note is still in the form.',true);return;}
                note.value='';render();notify('Internal note saved to this preview only.');note.focus();
            });
            if (typeof dialog.showModal==='function') {
                opener.hidden=false;
                opener.addEventListener('click',()=>{changes=readChanges();render();document.querySelector('[data-dialog-error]').hidden=true;['fulfillment','carrier','tracking'].forEach(name=>updateForm.elements.namedItem(name).value=order[name]);dialog.showModal();updateForm.elements.namedItem('fulfillment').focus();});
                dialog.querySelectorAll('[data-close-update]').forEach(button=>button.addEventListener('click',()=>dialog.close()));
                dialog.addEventListener('close',()=>opener.focus());
                dialog.addEventListener('click',event=>{if(event.target!==dialog)return;const bounds=dialog.getBoundingClientRect();if(event.clientX<bounds.left||event.clientX>bounds.right||event.clientY<bounds.top||event.clientY>bounds.bottom)dialog.close();});
                updateForm.addEventListener('submit',event=>{
                    event.preventDefault();const fields=Object.fromEntries(['fulfillment','carrier','tracking'].map(name=>[name,updateForm.elements.namedItem(name).value.trim()]));
                    const error=validateFulfillment(order,fields.fulfillment),errorBox=document.querySelector('[data-dialog-error]');errorBox.hidden=!error;errorBox.textContent=error;if(error)return;
                    if (Object.entries(fields).every(([name,value])=>order[name]===value)){dialog.close();return;}
                    const text=order.fulfillment===fields.fulfillment?'Delivery details updated in the preview.':`Fulfillment changed from ${label(order.fulfillment)} to ${label(fields.fulfillment)} in the preview.`;
                    if (!persist(order,{type:'fulfillment',text,at:new Date().toISOString()},fields)){errorBox.hidden=false;errorBox.textContent='Could not save. Enable browser session storage and try again.';return;}
                    render();dialog.close();notify('Fulfillment preview updated. No customer notification or shipment was created.');
                });
            }
            window.addEventListener('pageshow',()=>{changes=readChanges();render();});render();
        }
    }
}
