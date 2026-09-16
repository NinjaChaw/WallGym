const editor = document.querySelector('[data-content-editor]');
if (editor) {
    const kind = editor.dataset.contentEditor;
    const form = editor.querySelector('[data-editor-form]');
    const fieldset = form.querySelector('fieldset');
    const save = form.querySelector('[type=submit]');
    const feedback = form.querySelector('[data-feedback]');
    const stateLabel = form.querySelector('[data-save-state]');
    const key = `wallgym:content-preview:v1:${location.pathname}`;
    const controls = [...form.querySelectorAll('[name]')];
    let questions = kind === 'faq' ? JSON.parse(document.getElementById('faq-seed').textContent) : [];
    let images = {}, dirty = false, pending = 0, removed = null;
    const notify = (text, error = false) => {feedback.hidden=false;feedback.textContent=text;feedback.toggleAttribute('data-error',error);};
    const values = () => Object.fromEntries(controls.map(control=>[control.name,control.type==='checkbox'?control.checked:control.value]));
    const markDirty = () => {dirty=true;stateLabel.textContent='Unsaved changes';feedback.hidden=true;};
    const element = (tag, text, className) => {const node=document.createElement(tag);if(text!==undefined)node.textContent=text;if(className)node.className=className;return node;};
    try {
        const raw=sessionStorage.getItem(key);
        if(raw) {
            const data=JSON.parse(raw);
            if(!data?.values || controls.some(control=>typeof data.values[control.name] !== (control.type==='checkbox'?'boolean':'string')))throw new Error();
            if(kind==='faq' && (!Array.isArray(data.questions)||data.questions.length>20||new Set(data.questions.map(q=>q?.id)).size!==data.questions.length||!data.questions.every(q=>q&&typeof q.id==='string'&&typeof q.question==='string'&&q.question.length<=200&&typeof q.answer==='string'&&q.answer.length<=2000&&typeof q.visible==='boolean')))throw new Error();
            if(kind==='homepage' && (!data.images || Object.entries(data.images).some(([name,img])=>!['hero','living'].includes(name)||typeof img!=='string'||img.length>1500000||!/^data:image\/(png|jpeg|webp);base64,[A-Za-z0-9+/=]+$/.test(img))))throw new Error();
            controls.forEach(control=>{if(control.type==='checkbox')control.checked=data.values[control.name];else control.value=data.values[control.name];});
            if(kind==='faq')questions=data.questions;else images=data.images;
            stateLabel.textContent='Saved draft loaded';
        }
    } catch {notify('Saved content could not be loaded. Showing the original sample content.',true);}
    const preview = () => {
        const data=values();
        if(kind==='homepage') {
            const container=editor.querySelector('[data-home-preview]');container.replaceChildren();
            ['hero','benefits','collection','living'].forEach(section=>{
                if(!data[`${section}_visible`])return;
                const block=element('section',undefined,'wc-preview-section');
                if(data[`${section}_eyebrow`])block.append(element('small',data[`${section}_eyebrow`]));
                block.append(element('h3',data[`${section}_heading`]||'Your heading'));
                if(data[`${section}_description`])block.append(element('p',data[`${section}_description`]));
                for(let i=1;i<=3;i++)if(data[`${section}_title${i}`])block.append(element('h4',data[`${section}_title${i}`]),element('p',data[`${section}_text${i}`]));
                if(section==='collection')for(const [slug,name] of Object.entries({'swedish-wall':'Swedish Wall','gymnastic-rings':'Gymnastic Rings','exercise-mat':'Exercise Mat'}))if(data[`product_${slug}`])block.append(element('h4',name));
                if(data[`${section}_button`])block.append(element('span',data[`${section}_button`],'wc-preview-button'));
                if(section==='hero' && data.hero_secondary)block.append(element('p',data.hero_secondary));
                const source=editor.querySelector(`[data-upload="${section}"] img`);
                if(source) {const img=element('img');img.src=source.src;img.alt=data[`${section}_alt`]||'';block.append(img);}
                if(data[`${section}_caption`])block.append(element('p',data[`${section}_caption`]));
                container.append(block);
            });
            if(!container.children.length)container.append(element('p','All homepage sections are hidden.','wc-hint'));
        } else {
            const container=editor.querySelector('[data-faq-preview]');container.replaceChildren();
            if(!data.visible){container.append(element('p','The FAQ section is hidden.'));return;}
            container.append(element('small',data.eyebrow),element('h3',data.heading),element('p',data.description));
            const visible=questions.filter(q=>q.visible);
            visible.forEach((question,index)=>{const details=element('details');details.open=index===0;details.append(element('summary',question.question||'Your question'),element('p',question.answer||'Your answer'));container.append(details);});
            if(!visible.length)container.append(element('p','No visible answers yet.'));
        }
    };
    const renderQuestions = focusIndex => {
        const list=editor.querySelector('[data-question-list]');list.replaceChildren();
        questions.forEach((question,index)=>{
            const row=document.getElementById('question-template').content.cloneNode(true);
            row.querySelector('[data-question-number]').textContent=`Question ${String(index+1).padStart(2,'0')}`;
            for(const [selector,field] of [['[data-question]','question'],['[data-answer]','answer'],['[data-visible]','visible']]) {
                const control=row.querySelector(selector);if(field==='visible')control.checked=question.visible;else control.value=question[field];
                control.addEventListener('input',()=>{question[field]=field==='visible'?control.checked:control.value;control.setCustomValidity('');markDirty();preview();editor.querySelector('[data-count]').textContent=`${questions.length} questions · ${questions.filter(q=>q.visible).length} visible`;});
            }
            for(const [selector,delta] of [['[data-up]',-1],['[data-down]',1]]) {
                const button=row.querySelector(selector);button.disabled=index+delta<0||index+delta>=questions.length;button.setAttribute('aria-label',`Move question ${index+1} ${delta<0?'up':'down'}`);
                button.addEventListener('click',()=>{[questions[index],questions[index+delta]]=[questions[index+delta],questions[index]];markDirty();renderQuestions(index+delta);});
            }
            row.querySelector('[data-delete]').setAttribute('aria-label',`Remove question ${index+1}`);
            row.querySelector('[data-delete]').addEventListener('click',()=>{removed={question,index};questions.splice(index,1);editor.querySelector('[data-undo-bar]').hidden=false;markDirty();renderQuestions(Math.min(index,questions.length-1));});
            list.append(row);
        });
        editor.querySelector('[data-count]').textContent=`${questions.length} questions · ${questions.filter(q=>q.visible).length} visible`;
        editor.querySelector('[data-empty]').hidden=questions.length>0;
        editor.querySelector('[data-add]').disabled=questions.length>=20;
        if(focusIndex!==undefined)(list.querySelectorAll('[data-question]')[focusIndex]||editor.querySelector('[data-add]')).focus();
        preview();
    };
    if(kind==='faq') {
        editor.querySelector('[data-add]').addEventListener('click',()=>{if(questions.length>=20)return;questions.push({id:crypto.randomUUID(),question:'',answer:'',visible:true});markDirty();renderQuestions(questions.length-1);});
        editor.querySelector('[data-undo]').addEventListener('click',()=>{if(!removed)return;if(questions.length>=20){notify('Remove another question before restoring this one. The preview supports 20 questions.',true);return;}questions.splice(removed.index,0,removed.question);const index=removed.index;removed=null;editor.querySelector('[data-undo-bar]').hidden=true;markDirty();renderQuestions(index);});
        renderQuestions();
    } else {
        editor.querySelectorAll('[data-upload]').forEach(card=>{
            const name=card.dataset.upload,img=card.querySelector('img'),input=card.querySelector('input'),error=card.querySelector('[data-upload-error]');let revision=0;
            img.src=images[name]||img.dataset.default;
            card.querySelector('[data-reset-image]').addEventListener('click',()=>{revision++;delete images[name];img.src=img.dataset.default;input.value='';error.hidden=true;markDirty();preview();});
            input.addEventListener('change',async()=>{
                const file=input.files[0];if(!file)return;const current=++revision;error.hidden=true;
                if(!['image/png','image/jpeg','image/webp'].includes(file.type)||file.size>1048576){error.hidden=false;error.textContent='Choose a PNG, JPG or WebP image up to 1 MB.';input.value='';return;}
                pending++;save.disabled=true;
                try {
                    const data=await new Promise((resolve,reject)=>{const reader=new FileReader();reader.onload=()=>resolve(reader.result);reader.onerror=reject;reader.readAsDataURL(file);});
                    const test=new Image();test.src=data;await test.decode();if(current!==revision)return;
                    images[name]=data;img.src=data;markDirty();preview();
                } catch {if(current===revision){error.hidden=false;error.textContent='This image could not be opened. Choose another file.';}}
                finally {pending--;save.disabled=pending>0;if(current===revision)input.value='';}
            });
        });
    }
    controls.forEach(control=>control.addEventListener('input',()=>{control.setCustomValidity('');markDirty();preview();}));
    form.addEventListener('invalid',event=>{const details=event.target.closest('details');if(details)details.open=true;},true);
    form.addEventListener('submit',event=>{
        event.preventDefault();if(pending)return;
        form.querySelectorAll('[required]').forEach(control=>control.setCustomValidity(control.value.trim()?'':'Please complete this field.'));
        if(!form.reportValidity())return;
        const data=values();
        if(kind==='homepage' && data.collection_visible && !Object.keys(data).some(name=>name.startsWith('product_')&&data[name])){notify('Select at least one featured product, or hide the collection section.',true);editor.querySelector('[name="product_swedish-wall"]').closest('details').open=true;editor.querySelector('[name="product_swedish-wall"]').focus();return;}
        try {sessionStorage.setItem(key,JSON.stringify({values:data,questions,images}));dirty=false;stateLabel.textContent='Draft saved in this tab';notify('Draft saved. Your live website has not been changed.');}
        catch {notify('The draft could not be saved. Browser storage may be unavailable or full. Try smaller images; your changes are still in the editor.',true);}
        feedback.focus();
    });
    window.addEventListener('beforeunload',event=>{if(dirty||pending){event.preventDefault();event.returnValue='';}});
    fieldset.disabled=false;save.disabled=false;preview();
}
