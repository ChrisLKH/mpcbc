import fs from 'node:fs/promises';
import crypto from 'node:crypto';
const root='.',dir=`${root}/content-source/before-atomic-conversion`;
const kit=JSON.parse(await fs.readFile(`${dir}/kit.json`,'utf8'));
const colors=Object.fromEntries([...(kit.system_colors||[]),...(kit.custom_colors||[])].map(x=>[x._id,x.color]));
const fonts=Object.fromEntries([...(kit.system_typography||[]),...(kit.custom_typography||[])].map(x=>[x._id,x]));
const variables=new Map();
const token=(type,value,label)=>{if(value==null||value==='')return null;label=label||`mpcbc-${type}-${crypto.createHash('md5').update(String(value)).digest('hex').slice(0,9)}`;variables.set(label,{action:'create',type:`global-${type}-variable`,label,value:String(value)});return `var(--${label})`;};
const color=v=>v==='transparent'?'transparent':token('color',v);
const size=v=>typeof v==='object'&&v!==null?(v.unit==='custom'?String(v.size):`${v.size??0}${v.unit||'px'}`):String(v);
const box=v=>v&&typeof v==='object'&&'top'in v?['top','right','bottom','left'].map(k=>v[k]==='auto'?'auto':`${v[k]||0}${v.unit||'px'}`).join(' '):null;
const link=s=>s?.url?{destination:s.url,isTargetBlank:!!s.is_external}:undefined;
const attrs=(n)=>{const a=[{key:'data-mpcbc-id',value:n.id}];const cls=n.settings?._css_classes||n.settings?.css_classes;if(cls)a.push({key:'data-mpcbc-classes',value:cls});return a;};
const decodeGlobal=(s,key)=>{const v=s.__globals__?.[key];if(!v)return s[key];let id=v.split('id=')[1];return key.includes('typography')?fonts[id]:colors[id]??s[key];};
function typography(s,prefix='typography'){
 const global=decodeGlobal(s,`${prefix}_typography`);const merged={...s};if(global&&typeof global==='object')for(const[k,v]of Object.entries(global))if(k.startsWith('typography_'))merged[k.replace('typography_',prefix+'_')]=v;
 const css={base:{},tablet:{},mobile:{}};
 const props={font_family:'font-family',font_size:'font-size',font_weight:'font-weight',line_height:'line-height',letter_spacing:'letter-spacing',text_transform:'text-transform',text_decoration:'text-decoration'};
 for(const[b,suf]of [['base',''],['tablet','_tablet'],['mobile','_mobile']])for(const[k,p]of Object.entries(props)){let v=merged[`${prefix}_${k}${suf}`];if(v==null||v==='')continue;css[b][p]=k==='font_family'?token('font',v):k==='font_size'?size(v):typeof v==='object'?size(v):v;}
 return css;
}
function typographyCss(s,prefix='typography'){
 const ty=typography(s,prefix),render=o=>Object.entries(o).map(([k,v])=>`${k}:${v};`).join('');
 return render(ty.base)+['tablet','mobile'].map(b=>Object.keys(ty[b]).length?`@media (--${b}){${render(ty[b])}}`:'').join('');
}
let input,seq;
function add(type,config={},styles={},children=[],name){const key=(name||'Atomic element')+' '+(++seq);input.element_config[key]=config;let css=typeof styles==='string'?styles:Object.entries(styles).map(([k,v])=>`${k}:${v};`).join('');if(css)input.style[key]=css;return `<${type} configuration-id="${key.replaceAll('&','&amp;').replaceAll('"','&quot;')}">${children.join('')}</${type}>`;}
function makeStyle(n,{container=false,image=false,button=false}={}){
 const s=n.settings||{},base={'min-width':'0','margin':'0'},bp={tablet:{},mobile:{}};
 if(container)Object.assign(base,{display:'flex','flex-direction':'column',width:'100%',padding:'0',gap:'20px'});
 if(!container)base['max-width']='100%';
 const map={width:'width',height:'height',min_height:'min-height',flex_direction:'flex-direction',flex_align_items:'align-items',flex_justify_content:'justify-content',flex_wrap:'flex-wrap',flex_grow:'flex-grow',flex_shrink:'flex-shrink',_flex_size:'flex',object_fit:'object-fit',align:'text-align'};
 for(const[b,suf]of [['base',''],['tablet','_tablet'],['mobile','_mobile']]){
  const out=b==='base'?base:bp[b];for(const[k,p]of Object.entries(map)){let v=s[k+suf];if(v==null||v==='')continue;if(k==='flex_align_items'&&v==='baseline')v='flex-end';if(k==='_flex_size')v=v==='none'?'0 0 auto':v==='grow'?'1 1 0':'0 1 auto';out[p]=typeof v==='object'?size(v):v;if(out[p]==='fit-content'){out[p]='auto';out['align-self']='flex-start';}}
  if(s['flex_gap'+suf])out.gap=size(s['flex_gap'+suf]);for(const p of ['padding','margin','border_width','border_radius']){const v=s[(button&&p==='padding'?'text_padding':p)+suf]??s[(p==='margin'?'_margin':p)+suf];if(box(v))out[p.replaceAll('_','-')]=box(v);}
  const custom=s['_element_custom_width'+suf];if(custom)out['max-width']=size(custom);
 }
 if(n.widgetType==='heading'||n.widgetType==='text-editor'||button){const ty=typography(s);Object.assign(base,ty.base);Object.assign(bp.tablet,ty.tablet);Object.assign(bp.mobile,ty.mobile);}
 const fg=decodeGlobal(s,button?'button_text_color':n.widgetType==='text-editor'?'text_color':'title_color');if(fg)base.color=color(fg);
 if(s.background_background==='classic'||button){const bg=decodeGlobal(s,'background_color');if(bg)base['background-color']=color(bg);}
 if(s.background_image?.url){base['background-image']=`url("${s.background_image.url}")`;base['background-size']=s.background_size||'cover';base['background-position']=s.background_position||'center';}
 if(s.background_overlay_background==='gradient'){
  const c1=decodeGlobal(s,'background_overlay_color')||'transparent',c2=decodeGlobal(s,'background_overlay_color_b')||'transparent';
  const gradient=`linear-gradient(${s.background_overlay_gradient_angle?.size??180}deg, ${color(c1)} ${s.background_overlay_color_stop?.size??0}%, ${color(c2)} ${s.background_overlay_color_b_stop?.size??100}%)`;
  base['background-image']=s.background_image?.url?`${gradient},url("${s.background_image.url}")`:gradient;
 }
 if(s.border_border){base['border-style']=s.border_border;const bc=decodeGlobal(s,'border_color');if(bc)base['border-color']=color(bc);}
 if(image){base.width=s.width?size(s.width):'100%';base.height=s.height?size(s.height):'auto';base['object-fit']=s.object_fit||'cover';if(box(s.image_border_radius))base['border-radius']=box(s.image_border_radius);}
 if(button){delete base['text-align'];Object.assign(base,{display:'inline-flex','justify-content':s.align==='center'?'center':'flex-start','align-items':'center','align-self':'flex-start','text-decoration':'none','border-style':'none',width:'auto'});if(!base.padding)base.padding='12px 24px';if(!base['font-family'])base['font-family']=token('font','Geist');}
 if(s._position==='absolute'){base.position='absolute';for(const[k,p]of [['_offset_x','left'],['_offset_x_end','right'],['_offset_y','top'],['_offset_y_end','bottom']])if(s[k])base[p]=size(s[k]);}
 if(image&&s._position==='absolute')bp.mobile.position='static';
 if(s.hide_mobile==='hidden-mobile')bp.mobile.display='none';
 const output=o=>Object.entries(o).map(([k,v])=>`${k}:${v};`).join('');
 let css=output(base);for(const b of ['tablet','mobile'])if(Object.keys(bp[b]).length)css+=`@media (--${b}){${output(bp[b])}}`;
 if(button){const fg=decodeGlobal(s,'hover_color'),bg=decodeGlobal(s,'button_background_hover_color');if(fg||bg)css+=`&:hover{${fg?'color:'+color(fg)+';':''}${bg?'background-color:'+color(bg)+';':''}}`;}
 return css;
}
function convert(n){
 const s=n.settings||{},type=n.widgetType||n.elType,config={_cssid:s._element_id||`mpcbc-${n.id}`};
 const cls=s._css_classes||s.css_classes||'';
 if(cls.includes('mpcbc-topic-reader'))config._cssid='mpcbc-topic-reader';
 if(cls.includes('mpcbc-topic-index'))config._cssid='mpcbc-topic-index';
 if(cls.includes('reader-unavailable'))config._cssid=`mpcbc-reader-unavailable-${n.id}`;
 if(type==='container'){
  const children=(n.elements||[]).map(convert);let childList=children;
  if(s.content_width==='boxed'){
   const layout=Object.fromEntries(Object.entries(s).filter(([key])=>key.startsWith('flex_')));
   childList=[add('e-flexbox',{},makeStyle({settings:{...layout},elType:'container'},{container:true})+`max-width:${size(s.boxed_width||{size:1140,unit:'px'})};margin-left:auto;margin-right:auto;`,children,'Boxed inner')];
  }
  if(s.background_video_link){
   const content=add('e-background-video-content',{},makeStyle(n,{container:true})+'position:relative;z-index:1;flex-grow:1;',childList,'Video content');
   const labels=['Play background video','Pause background video'];
   const controls=add('e-background-video-controls',{},'position:absolute;right:24px;bottom:24px;z-index:3;display:flex;padding:0;',labels.map((label,i)=>add(i?'e-background-video-pause':'e-background-video-play',{},`color:${color('#ffffff')};background-color:${color('#0F2A33')};border-radius:50%;width:44px;height:44px;padding:8px;`,[add('e-paragraph',{paragraph:i?'Ⅱ':'▶'},`font-family:${token('font','Geist')};font-size:18px;line-height:1;color:inherit;margin:0;`,[],'Video control label')],label)),'Video controls');
   return add('e-background-video',{...config,source:{url:s.background_video_link},autoplay:true,mute:true,loop:true,show_controls:true},'display:flex;flex-direction:column;position:relative;overflow:hidden;width:100%;padding:0;',[content,controls],n.id);
  }
  return add('e-flexbox',config,makeStyle(n,{container:true}),childList,n.id);
 }
 if(type==='heading')return add(/^h[1-6]$/.test(s.header_size)?'e-heading':'e-paragraph',{...config,...(/^h[1-6]$/.test(s.header_size)?{title:s.title,tag:s.header_size}:{paragraph:s.title,tag:'p'}),...(link(s.link)?{link:link(s.link)}:{})},makeStyle(n),[],n.id);
 if(type==='text-editor'){
  const parts=[...(s.editor||'').matchAll(/<p(?:\s[^>]*)?>([\s\S]*?)<\/p>/gi)].map(m=>m[1]);
  if(parts.length>1)return add('e-flexbox',config,'display:flex;flex-direction:column;padding:0;gap:16px;width:100%;',parts.map(p=>add('e-paragraph',{paragraph:p,tag:'p'},makeStyle(n),[],n.id+' paragraph')),n.id);
  return add('e-paragraph',{...config,paragraph:parts.length?parts[0]:s.editor,tag:'p'},makeStyle(n),[],n.id);
 }
 if(type==='button')return add('e-button',{...config,text:s.text,link:link(s.link)||{destination:'#'},tag:'a'},makeStyle(n,{button:true}),[],n.id);
 if(type==='image')return add('e-image',{...config,image:{src:s.image?.id?{id:s.image.id}:{url:s.image?.url},size:s.image_size||'full'},...(link(s.link)?{link:link(s.link)}:{})},makeStyle(n,{image:true}),[],n.id);
 if(type==='shortcode')return add('e-div-block',{_cssid:`mpcbc-embed-${n.id}`},'width:100%;padding:0;',[],n.id+' embed placeholder');
 if(type==='icon-list')return add('e-flexbox',config,'display:flex;flex-direction:column;gap:12px;padding:0;',s.icon_list.map(i=>add('e-paragraph',{paragraph:i.text,...(link(i.link)?{link:link(i.link)}:{})},makeStyle({...n,settings:{...s,title_color:decodeGlobal(s,'text_color')}}),[],'List item')),n.id);
 if(type==='nested-accordion'){
  const children=s.items.map((item,i)=>add('e-accordion-item',{},`border-radius:12px;background-color:${color('#FFFFFF')};`,[
   add('e-accordion-item-header',{},`display:flex;align-items:center;padding:${box(s.accordion_padding)||'20px'};color:${color('#155E75')};cursor:pointer;`,[
    add('e-accordion-item-title',{},'padding:0;width:100%;',[add('e-paragraph',{paragraph:item.item_title},typographyCss(s,'title_typography')+'margin:0;',[],'Accordion label')],'Accordion title')
   ],'Accordion header'),add('e-accordion-item-content',{},`padding:${box(s.content_padding)||'4px 20px 24px'};`,(n.elements[i]?.elements||[]).map(convert),'Accordion content')
  ],'Accordion item'));
  return add('e-accordion',{...config,default_state:s.default_state==='all_collapsed'?'all_collapsed':'first_expanded',max_expanded:'multiple',show_icon:false},'padding:0;width:100%;gap:12px;',children,n.id);
 }
 if(type==='nested-tabs'){
  const vertical=['inline-start','inline-end'].includes(s.tabs_direction),width=size(s.tabs_width||{size:248,unit:'px'});
  const menu=add('e-tabs-menu',{},`display:flex;flex-direction:${vertical?'column':'row'};flex-wrap:${vertical?'nowrap':'wrap'};width:${vertical?width:'100%'};flex-shrink:0;gap:${size(s.tabs_title_space_between||{size:4,unit:'px'})};padding:0;@media (--mobile){width:100%;flex-direction:row;flex-wrap:wrap;}`,s.tabs.map((t,i)=>add('e-tab',{},`text-align:start;width:${vertical?'100%':'auto'};padding:${box(s.padding)||'14px 16px'};border-width:0;border-radius:${box(s.tabs_title_border_radius)||'8px'};background-color:${color(decodeGlobal(s,'tabs_title_background_color_color')||'#F6F7F5')};color:${color(decodeGlobal(s,'title_text_color')||'#4F6168')};&:active{background-color:${color(decodeGlobal(s,'tabs_title_background_color_active_color')||'#E3EBEA')};color:${color(decodeGlobal(s,'title_text_color_active')||'#155E75')};}`, [add('e-paragraph',{paragraph:t.tab_title},typographyCss(s,'title_typography')+'color:inherit;margin:0;',[],'Tab label')],'Tab '+(i+1))),'Tabs menu');
  const panels=add('e-tabs-content-area',{},'min-width:0;width:100%;padding:0;flex-grow:1;',n.elements.map((panel,i)=>add('e-tab-content',{},'width:100%;padding:0;', [convert(panel)],'Tab panel '+(i+1))),'Tabs panels');
  return add('e-tabs',{...config,'default-active-tab':0},`display:flex;align-items:stretch;flex-direction:${vertical?'row':'column'};width:100%;gap:${size(s.tabs_title_spacing||{size:32,unit:'px'})};padding:0;@media (--tablet){gap:32px;}@media (--mobile){flex-direction:column;gap:24px;}`,[menu,panels],n.id);
 }
 if(type==='eael-fancy-text')return add('e-paragraph',{...config,paragraph:(s.eael_fancy_text_strings||[]).map(x=>x.eael_fancy_text_strings_text_field).join(' · ')},makeStyle(n),[],n.id);
 throw new Error(`Unsupported ${type} ${n.id}`);
}
await fs.mkdir(`${root}/content-source/atomic-input`,{recursive:true});
const manifest=JSON.parse(await fs.readFile(`${dir}/manifest.json`,'utf8'));
for(const p of manifest){
 if(![21,64,215,218,65,233,236,16,181,224,227,210,211,66,60,61,62,63,17,18,19,109].includes(p.id))continue;
 const source=JSON.parse(await fs.readFile(`${dir}/${p.id}.json`,'utf8'));seq=0;input={xml_structure:'',element_config:{},style:{}};input.xml_structure=source.data.map(convert).join('');
 await fs.writeFile(`${root}/content-source/atomic-input/${p.id}.json`,JSON.stringify(input,null,2));
}
await fs.writeFile(`${root}/content-source/atomic-variable-input.json`,JSON.stringify([...variables.values()],null,2));
console.log(`Prepared ${manifest.length} source documents and ${variables.size} native Atomic design tokens.`);


