import fs from 'node:fs/promises';
import crypto from 'node:crypto';
const root=new URL('../',import.meta.url);
try { await fs.access(new URL('content-source/shared-home-footer-complete.json',root)); throw Error('Consolidation is complete. Reconcile current editor content before rebuilding.'); } catch(error) { if(error.code!=='ENOENT')throw error; }
const clone=x=>structuredClone(x);
const han=/\p{Script=Han}/u;
const strip=s=>s.replace(/<[^>]+>/g,'');
const escape=s=>s.replaceAll('&','&amp;').replaceAll('<','&lt;').replaceAll('>','&gt;');
const pairs=[];
const fresh=()=>crypto.randomBytes(4).toString('hex');
function nativeText(text,title,id,font){
 const styleId='e-'+crypto.randomBytes(6).toString('hex');
 const props={'margin':{$$type:'size',value:{size:0,unit:'px'}},'padding':{$$type:'size',value:{size:0,unit:'px'}},'min-width':{$$type:'size',value:{size:0,unit:'px'}}};
 if(font)props['font-family']={$$type:'global-font-variable',value:font};
 return {id:fresh(),elType:'widget',widgetType:'e-paragraph',settings:{paragraph:{$$type:'escaped-html',value:escape(text)},tag:{$$type:'string',value:'span'},classes:{$$type:'classes',value:[styleId]},...(id?{_cssid:{$$type:'string',value:id}}:{})},elements:[],styles:{[styleId]:{id:styleId,label:'local',type:'class',variants:[{meta:{breakpoint:'desktop',state:null},props,custom_css:null}]}},editor_settings:{title},version:'0.0'};
}
function inlineParts(html){
 const plain=strip(html);const chars=[...plain];const start=chars.findIndex(c=>han.test(c));let end=chars.length-1;while(end>=0&&!han.test(chars[end]))end--;
 return {prefix:chars.slice(0,start).join(''),chinese:chars.slice(start,end+1).join(''),suffix:chars.slice(end+1).join('')};
}
function merge(a,b,doc){
 if(a.length!==b.length)throw Error('Source structures differ; reconcile current editorial changes first.');
 const out=[];
 for(let i=0;i<a.length;i++){
  let n=clone(a[i]),m=b[i];if((n.widgetType||n.elType)!==(m.widgetType||m.elType))throw Error('Source types differ.');
  n.elements=merge(n.elements||[],m.elements||[],doc);
  let found=false;
  for(const key of ['title','paragraph','text','alt']){
   const source=n.settings?.[key]?.value,target=m.settings?.[key]?.value;
   if(typeof source!=='string')continue;
   if(!han.test(source)){if(source!==target)throw Error('Different English source needs editorial reconciliation.');continue;}
   if(typeof target!=='string')throw Error('Missing Simplified copy.');
   // Attributes such as alt text need explicit future localization rather than HTML.
   if(key==='alt')throw Error('Chinese alt text requires an explicit attribute pair.');
   const t=inlineParts(source),s=inlineParts(target);
   const mixed=/[A-Za-z]/.test(t.prefix+t.suffix)||Boolean(n.settings.link);
   const title=n.editor_settings?.title||n.settings?._cssid?.value||n.id;
   if(mixed){
    if(t.prefix!==s.prefix||t.suffix!==s.suffix)throw Error('Different shared English/facts in '+title);
    // Native linked Flexbox shares one URL and the English/facts, while each
    // Chinese label is its own editable Atomic Paragraph (span tag).
    const stable=n.settings._cssid?.value||n.id;
    const tc=nativeText(t.chinese,`Traditional / 繁體中文 · ${title}`,`mpcbc-copy-zh-hant-${stable}`,'e-gv-2635213');
    const sc=nativeText(s.chinese,`Simplified / 简体中文 · ${title}`,`mpcbc-copy-zh-hans-${stable}`,'e-gv-99d935c');
    const children=[];
    if(t.prefix.trim()){
      const english=nativeText(t.prefix.trim(),`English / shared · ${title}`);
      if(n.widgetType==='e-heading'){
        english.widgetType='e-heading';english.settings.title=english.settings.paragraph;delete english.settings.paragraph;
        english.settings.tag=clone(n.settings.tag||{$$type:'string',value:'h3'});
        const style=Object.values(english.styles)[0];
        const original=Object.values(n.styles||{}).flatMap(s=>s.variants||[]);
        for(const v of original){const size=v.props?.['font-size'];if(!size)continue;if(v.meta.breakpoint==='desktop')style.variants[0].props['font-size']=clone(size);else style.variants.push({meta:clone(v.meta),props:{'font-size':clone(size)},custom_css:null});}
      }
      children.push(english);
    }
    children.push(tc,sc);
    if(t.suffix.trim())children.push(nativeText(t.suffix.trim(),`Shared time / suffix · ${title}`));
    delete n.settings[key];n.settings.tag={$$type:'string',value:n.settings.link?'a':'div'};
    n.elType='e-flexbox';delete n.widgetType;n.elements=children;n.isInner=false;
    for(const style of Object.values(n.styles||{})){
      let desktop=style.variants.find(v=>v.meta.breakpoint==='desktop'&&!v.meta.state);
      if(!desktop){desktop={meta:{breakpoint:'desktop',state:null},props:{},custom_css:null};style.variants.push(desktop);}
      desktop.props.padding??={$$type:'size',value:{size:0,unit:'px'}};
      desktop.props.width??={$$type:'size',value:{size:'',unit:'auto'}};
      Object.assign(desktop.props,{'display':{$$type:'string',value:'inline-flex'},'flex-direction':{$$type:'string',value:'row'},'flex-wrap':{$$type:'string',value:'wrap'},'gap':{$$type:'size',value:{size:/[A-Za-z]/.test(t.prefix+t.suffix)?4:0,unit:'px'}},'align-items':{$$type:'string',value:'center'},'text-decoration':{$$type:'string',value:'none'}});
    }
    n.editor_settings={...n.editor_settings,title:`${title} · shared English / URL + Chinese wording`};
    pairs.push({document:doc,kind:'native-group',element_id:n.id,traditional_id:tc.id,simplified_id:sc.id,field:'paragraph',traditional:t.chinese,simplified:s.chinese});
   }else{
    const id=n.settings?._cssid?.value||n.id;
    n.settings._cssid={$$type:'string',value:`mpcbc-copy-zh-hant-${id}`};
    n.editor_settings={...n.editor_settings,title:`Traditional / 繁體中文 · ${title}`};
    const variant=clone(n);variant.id=crypto.randomBytes(4).toString('hex');
    variant.settings[key].value=target;
    variant.settings._cssid.value=`mpcbc-copy-zh-hans-${id}`;
    variant.editor_settings.title=`Simplified / 简体中文 · ${title}`;
    const convertFonts=x=>{if(!x||typeof x!=='object')return; if(x.$$type==='global-font-variable'&&x.value==='e-gv-2635213')x.value='e-gv-99d935c';Object.values(x).forEach(convertFonts);};convertFonts(variant.styles);
    const remap={};variant.styles=Object.fromEntries(Object.entries(variant.styles||{}).map(([old,style])=>{const fresh='e-'+crypto.randomBytes(6).toString('hex');remap[old]=fresh;style.id=fresh;return [fresh,style];}));
    if(variant.settings.classes?.value)variant.settings.classes.value=variant.settings.classes.value.map(id=>remap[id]||id);
    out.push(n,variant);found=true;
    pairs.push({document:doc,kind:'native',traditional_id:n.id,simplified_id:variant.id,field:key,traditional:source,simplified:target});
   }
  }
  if(!found)out.push(n);
 }
 return out;
}
for(const [id,other,name] of [[21,511,'home'],[16,513,'footer']]){
 const read=async id=>JSON.parse(await fs.readFile(new URL(`content-source/before-home-footer-consolidation/${id}.json`,root),'utf8'));
 const a=await read(id),b=await read(other);
 const data=merge(a.data,b.data,id);
 await fs.writeFile(new URL(`content-source/shared-${name}-compiled.json`,root),JSON.stringify(data,null,2));
}
await fs.writeFile(new URL('content-source/shared-language-copy-map.json',root),JSON.stringify(pairs,null,2));
console.log(`Prepared ${pairs.length} independent Chinese copy pairs inside two shared Atomic documents.`);
