import fs from 'node:fs/promises';
import './assert-legacy-build.mjs';
const root='.';let serial=0;const id=()=>`bf${(++serial).toString(16).padStart(5,'0')}`;
const dim=(t,r=t,b=t,l=r)=>({unit:'px',top:String(t),right:String(r),bottom:String(b),left:String(l),isLinked:false});
const clone=x=>JSON.parse(JSON.stringify(x));
const container=(elements,extra={})=>({id:id(),elType:'container',isInner:false,settings:{content_width:'full',flex_direction:'column',flex_gap:{unit:'px',size:20},padding:dim(0),...extra},elements});
const row=elements=>container(elements,{flex_direction:'row',flex_direction_tablet:'column',flex_gap:{unit:'px',size:40},flex_gap_mobile:{unit:'px',size:28}});
const col=(elements,width)=>container(elements,{width:{unit:'%',size:width},width_tablet:{unit:'%',size:100}});
const section=(elements,color='paper',anchor,extra={})=>container([container(elements,{content_width:'boxed',boxed_width:{unit:'px',size:1120}})],{background_background:'classic',__globals__:{background_color:`globals/colors?id=${color}`},padding:dim(80,56),padding_tablet:dim(64,32),padding_mobile:dim(48,20),...(anchor?{_element_id:anchor}:{}),...extra});
function find(ns,type){for(const n of ns){if(n.widgetType===type)return n;const child=find(n.elements||[],type);if(child)return child;}}
const strings={en:{eyebrow:'ABOUT OUR CHURCH',title:'What we<br>believe.',lead:'Grounded in Scripture.<br>Centred on Jesus.',intro:'The beliefs that guide our worship, our life together and our service.',story:'Our story',mission:'Our mission',team:'Our pastoral team',beliefs:'Our beliefs',label:'OUR STATEMENT OF FAITH',foundation:'The foundations<br>of our faith.',guide:'Explore our eight belief statements and their Scripture references.',next:'Get to know our church.',nextBody:'Discover the story, mission and people behind our shared faith.',back:'Our story and team ↗'},'zh-hant':{eyebrow:'認識教會',title:'我們的<br>信仰告白。',lead:'以聖經為根基，<br>以耶穌為中心。',intro:'我們的信仰，引領我們的敬拜、共同生活與事奉。',story:'教會歷史',mission:'使命宣言',team:'教牧團隊',beliefs:'信仰告白',label:'我們的信仰告白',foundation:'信仰的根基。',guide:'閱讀八項信仰告白及相關經文。',next:'認識我們的教會。',nextBody:'了解我們共同信仰背後的教會歷史、使命及教牧團隊。',back:'教會歷史與教牧團隊 ↗'},'zh-hans':{eyebrow:'认识教会',title:'我们的<br>信仰告白。',lead:'以圣经为根基，<br>以耶稣为中心。',intro:'我们的信仰，引领我们的敬拜、共同生活与事奉。',story:'教会历史',mission:'使命宣言',team:'教牧团队',beliefs:'信仰告白',label:'我们的信仰告白',foundation:'信仰的根基。',guide:'阅读八项信仰告白及相关经文。',next:'认识我们的教会。',nextBody:'了解我们共同信仰背后的教会历史、使命及教牧团队。',back:'教会历史与教牧团队 ↗'}};
for(const locale of ['en','zh-hant','zh-hans']){
 const s=strings[locale],prefix=locale==='en'?'':`/${locale}`,f=type=>`about_${locale==='en'?'en':locale==='zh-hant'?'tc':'sc'}_${type}`;
 const heading=(title,tag,font,color='text',extra={})=>({id:id(),elType:'widget',widgetType:'heading',isInner:false,settings:{title,header_size:tag,__globals__:{title_color:`globals/colors?id=${color}`,typography_typography:`globals/typography?id=${f(font)}`},...extra},elements:[]});
 const text=value=>({id:id(),elType:'widget',widgetType:'text-editor',isInner:false,settings:{editor:`<p>${value}</p>`,__globals__:{text_color:'globals/colors?id=ink_soft',typography_typography:`globals/typography?id=${f('body')}`}},elements:[]});
 const exportPath=`${root}/elementor/${locale}-beliefs.json`;
 const old=JSON.parse(await fs.readFile(exportPath,'utf8'));
 const sourcePath=`${root}/content-source/beliefs-core-${locale}.json`;
 try{await fs.access(sourcePath);}catch{await fs.writeFile(sourcePath,JSON.stringify(find(old.content,'nested-accordion'),null,2));}
 const accordion=JSON.parse(await fs.readFile(sourcePath,'utf8'));
 Object.assign(accordion.settings,{title_tag:'h3',default_state:'expanded',max_items_expended:'multiple',accordion_item_title_space_between:{unit:'px',size:12},accordion_padding:dim(22,24),accordion_padding_mobile:dim(20),accordion_border_radius:dim(12),content_border_border:'none',content_border_radius:dim(0,0,12,12),content_padding:dim(4,24,28),content_padding_mobile:dim(4,20,24),content_background_background:'classic',accordion_border_normal_border:'solid',accordion_border_normal_width:dim(1),accordion_border_active_border:'solid',accordion_border_active_width:dim(1),__globals__:{...accordion.settings.__globals__,content_background_color:'globals/colors?id=white',accordion_border_normal_color:'globals/colors?id=line',accordion_border_active_color:'globals/colors?id=line',title_typography_typography:`globals/typography?id=${f('card')}`}});
 const restyle=ns=>{for(const n of ns){if(n.widgetType){n.settings.__globals__={...n.settings.__globals__,typography_typography:`globals/typography?id=${f(n.widgetType==='heading'?'label':'body')}`};}else{Object.assign(n.settings,{padding:dim(4,24,24),padding_mobile:dim(4,20,24),border_border:'none',border_radius:dim(0,0,12,12),background_background:'classic',__globals__:{background_color:'globals/colors?id=white'}});}restyle(n.elements||[]);}};restyle(accordion.elements);
 const nav=container([heading(`${s.story} ↗`,'div','nav','primary',{link:{url:`${prefix}/about/#story`}}),heading(`${s.mission} ↗`,'div','nav','primary',{link:{url:`${prefix}/about/#mission`}}),heading(`${s.team} ↗`,'div','nav','primary',{link:{url:`${prefix}/about/#pastors`}}),heading(s.beliefs,'div','nav','text',{link:{url:'#beliefs'}})],{flex_direction:'row',flex_wrap:'wrap',flex_gap:{unit:'px',size:32},flex_gap_mobile:{unit:'px',size:20}});
 const button={id:id(),elType:'widget',widgetType:'button',isInner:false,elements:[],settings:{text:s.back,link:{url:`${prefix}/about/#story`},align:'left',text_padding:dim(16,24),border_radius:dim(12),__globals__:{background_color:'globals/colors?id=primary',button_text_color:'globals/colors?id=white',typography_typography:`globals/typography?id=${f('nav')}`}}};
 const exp={...old,content:[
  section([row([col([heading(s.eyebrow,'div','label','primary'),heading(s.title,'h1','hero')],48),col([heading(s.lead,'h2','mission'),text(s.intro)],48)])],'paper',null,{padding:dim(72,56,56),padding_mobile:dim(40,20,32)}),
  section([nav],'white',null,{padding:dim(20,56),padding_tablet:dim(20,32),padding_mobile:dim(18,20)}),
  section([row([col([heading(s.label,'div','label','primary'),heading(s.foundation,'h2','section'),text(s.guide)],38),col([accordion],58)])],'paper','beliefs'),
  section([row([col([heading(s.next,'h2','section'),text(s.nextBody)],68),col([button],28)])],'mist')
 ]};
 await fs.writeFile(exportPath,JSON.stringify(exp,null,2));
 // Preserve the approved About composition; add one matching topic link to its section navigation.
 const aboutPath=`${root}/elementor/${locale}-about.json`,about=JSON.parse(await fs.readFile(aboutPath,'utf8'));
 const aboutNav=about.content[1].elements[0].elements[0];
 if(!aboutNav.elements.some(n=>n.settings?.link?.url?.includes('/beliefs/')))aboutNav.elements.push(heading(`${s.beliefs} ↗`,'div','nav','primary',{link:{url:`${prefix}/beliefs/`}}));
 await fs.writeFile(aboutPath,JSON.stringify(about,null,2));
}
await fs.writeFile(`${root}/content-source/beliefs-connected-design.json`,JSON.stringify({builder:'scripts/build-beliefs-connected.mjs',decision:'Separate full statements; shared About/Beliefs navigation, typography and content width.',doctrinal_text:'Preserved from beliefs-core-{locale}.json'},null,2));
console.log('Prepared connected About/Beliefs pages in all three languages.');
