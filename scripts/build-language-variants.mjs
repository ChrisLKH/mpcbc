import fs from 'node:fs/promises';
import './assert-legacy-build.mjs';
import {createRequire} from 'node:module';
const require=createRequire(import.meta.url);const OpenCC=require('../.content-tools/node_modules/opencc-js');
const simplify=OpenCC.Converter({from:'hk',to:'cn'});
const dir='elementor';const sourceDir='content-source';
const clone=x=>JSON.parse(JSON.stringify(x));
const locales=['en','zh-hant','zh-hans'];
const route=(locale,topic)=>locale==='en'?(topic==='index'?'/':`/${topic}/`):(topic==='index'?`/${locale}/`:`/${locale}/${topic}/`);
const bookNames={'創':'Genesis ','詩':'Psalms ','太':'Matthew ','彼前':'1 Peter ','彼後':'2 Peter ','林後':'2 Corinthians ','林前':'1 Corinthians ','賽':'Isaiah ','約':'John ','來':'Hebrews ','羅':'Romans ','徒':'Acts ','提前':'1 Timothy ','提後':'2 Timothy ','多':'Titus ','弗':'Ephesians ','加':'Galatians ','箴':'Proverbs ','啟':'Revelation '};
function refsEn(s){return s.replace(/彼前|彼後|林後|林前|提前|提後|[創詩太賽約來羅徒多弗加箴啟]/g,t=>bookNames[t]||t);}
let serial=0;const nodeId=()=>`lc${(++serial).toString(16).padStart(5,'0')}`;
function langButtons(locale,topic){return {id:nodeId(),elType:'container',settings:{content_width:'full',flex_direction:'row',flex_wrap:'wrap',flex_gap:{unit:'px',size:12}},elements:locales.map(l=>({id:nodeId(),elType:'widget',widgetType:'button',settings:{text:l==='en'?'English':l==='zh-hant'?'繁體中文':'简体中文',link:{url:route(l,topic)},text_padding:{unit:'px',top:12,right:16,bottom:12,left:16,isLinked:false},border_radius:{unit:'px',top:12,right:12,bottom:12,left:12,isLinked:true},__globals__:{button_text_color:`globals/colors?id=${l===locale?'white':'primary'}`,background_color:`globals/colors?id=${l===locale?'primary':'paper'}`,typography_typography:'globals/typography?id=interior_button'}},elements:[]}))};}
function localize(nodes,locale){const out=[];const isZh=locale!=='en';for(let i=0;i<nodes.length;i++){
 let n=clone(nodes[i]);let s=n.settings??={};
 if(n.widgetType==='heading'&&nodes[i+1]?.widgetType==='heading'&&nodes[i+1].settings.header_size==='div'&&/\p{Script=Han}/u.test(nodes[i+1].settings.title)){
  if(isZh)s.title=nodes[i+1].settings.title;i++;
 }
 if(n.widgetType==='text-editor'){
  let zh=/lang="zh-Hant"/.test(s.editor);let reference=/^[^<]*$/.test(s.editor.replace(/<[^>]+>/g,''))&&/[創詩太約羅啟].*\d/.test(s.editor);
  if(isZh&&!zh&&!s.editor.includes('302 W.'))continue;
  if(!isZh&&zh){if(reference)s.editor=refsEn(s.editor).replace(' lang="zh-Hant"','');else continue;}
 }
 if((n.widgetType==='heading'||n.widgetType==='button')&&/ · /.test(s.title??s.text??'')){
  const key=n.widgetType==='heading'?'title':'text',parts=s[key].split(' · ');s[key]=isZh?parts.at(-1):parts[0];
 }
 if(n.widgetType==='nested-accordion')s.items=s.items.map(item=>({...item,item_title:item.item_title.split('<br>')[isZh?1:0]}));
 if(isZh){
  const g=s.__globals__??={};for(const k of Object.keys(g))if(g[k].includes('globals/typography?id=')){
   if(k==='typography_typography'&&n.widgetType==='heading'){
    const type=s.header_size==='h1'?'h1':s.header_size==='h2'?'h2':'card';g[k]=`globals/typography?id=interior_${locale==='zh-hans'?'hans':'zh'}_${type}`;
   }else if(g[k].includes('interior_zh')&&locale==='zh-hans')g[k]='globals/typography?id=interior_hans';
   if(k==='title_typography_typography')g[k]=`globals/typography?id=interior_${locale==='zh-hans'?'hans':'zh'}_card`;
  }
 }
 if(s.link?.url?.startsWith('/')){let u=s.link.url;const match=u.match(/^\/(about|beliefs|give)\/(.*)$/);if(match)s.link.url=route(locale,match[1])+match[2];}
 if(n.elements)n.elements=localize(n.elements,locale);
 if(locale==='zh-hans'){
  for(const k of ['title','text','editor','image_alt'])if(typeof s[k]==='string')s[k]=simplify(s[k]).replaceAll('zh-Hant','zh-Hans');
  if(s.items)s.items=s.items.map(item=>({...item,item_title:simplify(item.item_title)}));
 }
 out.push(n);
}return out;}
const manifest=[];
for(const [topic,file] of [['about','about'],['beliefs','beliefs'],['give','give'],['index','zh']]){
 const source=`${sourceDir}/bilingual-template-${file}.json`;
 try{await fs.access(source);}catch{await fs.copyFile(`${dir}/${file}-content-filled.json`,source);}
 const original=JSON.parse(await fs.readFile(source,'utf8'));
 for(const locale of locales){if(topic==='index'&&locale==='en')continue;
  // The approved anchored About design has its own locale-aware builder.
  const anchoredAbout=(topic==='about'&&await fs.access(`${sourceDir}/about-anchored-source.json`).then(()=>true,()=>false))||(topic==='beliefs'&&await fs.access(`${sourceDir}/beliefs-connected-design.json`).then(()=>true,()=>false));
   const exp=anchoredAbout?JSON.parse(await fs.readFile(`${dir}/${locale}-${topic}.json`,'utf8')):clone(original);
  if(!anchoredAbout)exp.content=localize(exp.content,locale);
  exp.title=topic==='index'?(locale==='zh-hans'?'简体中文':'繁體中文'):locale==='en'?{about:'About',beliefs:'Beliefs',give:'Give'}[topic]:(locale==='zh-hans'?simplify({about:'關於我們',beliefs:'信仰告白',give:'奉獻'}[topic]):{about:'關於我們',beliefs:'信仰告白',give:'奉獻'}[topic]);
  const name=`${locale}-${topic}.json`;await fs.writeFile(`${dir}/${name}`,JSON.stringify(exp,null,2));manifest.push({locale,topic,path:route(locale,topic),file:name,title:exp.title});
 }
}
await fs.writeFile(`${sourceDir}/language-manifest.json`,JSON.stringify(manifest,null,2));
console.log(`Prepared ${manifest.length} locale pages from shared bilingual sources.`);
