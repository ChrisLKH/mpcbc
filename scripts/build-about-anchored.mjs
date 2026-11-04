import fs from 'node:fs/promises';
import './assert-legacy-build.mjs';
import {createRequire} from 'node:module';
const require=createRequire(import.meta.url);
const simplify=require('../.content-tools/node_modules/opencc-js').Converter({from:'hk',to:'cn'});
const root='.';
const original=JSON.parse(await fs.readFile(`${root}/content-source/bilingual-template-about.json`,'utf8'));
const flat=[];function collect(ns){for(const n of ns){flat.push(n);collect(n.elements||[]);}}collect(original.content);
const paragraph=start=>flat.find(n=>n.widgetType==='text-editor'&&n.settings.editor.includes(start)).settings.editor.replace(/^<p[^>]*>|<\/p>$/g,'');
const source={story:{en:paragraph('Our church was founded'),zh:paragraph('教會成立於')},bio:{en:paragraph('Pastor Caleb is from'),zh:paragraph('張健庭牧師來自')}};
let serial=0;const id=()=>`ab${(++serial).toString(16).padStart(5,'0')}`;
const dim=(t,r=t,b=t,l=r)=>({unit:'px',top:String(t),right:String(r),bottom:String(b),left:String(l),isLinked:false});
const token=(kind,name)=>`globals/${kind}?id=${name}`;
const widget=(type,settings,elements=[])=>({id:id(),elType:'widget',widgetType:type,isInner:false,settings,elements});
const container=(elements,extra={})=>({id:id(),elType:'container',isInner:false,settings:{content_width:'full',flex_direction:'column',flex_gap:{unit:'px',size:20},padding:dim(0),...extra},elements});
const heading=(title,tag,font,color='text',extra={})=>widget('heading',{title,header_size:tag,__globals__:{title_color:token('colors',color),typography_typography:token('typography',font)},...extra});
const text=(value,font,color='text')=>widget('text-editor',{editor:`<p>${value}</p>`,__globals__:{text_color:token('colors',color),typography_typography:token('typography',font)}});
const row=(elements,extra={})=>container(elements,{flex_direction:'row',flex_direction_tablet:'column',flex_gap:{unit:'px',size:40},flex_gap_mobile:{unit:'px',size:28},...extra});
const col=(elements,width)=>container(elements,{width:{unit:'%',size:width},width_tablet:{unit:'%',size:100}});
const section=(elements,color='paper',anchor,extra={})=>container([container(elements,{content_width:'boxed',boxed_width:{unit:'px',size:1120}})],{background_background:'classic',__globals__:{background_color:token('colors',color)},padding:dim(80,56),padding_tablet:dim(64,32),padding_mobile:dim(48,20),...(anchor?{_element_id:anchor}:{}),...extra});
const image=(url,alt,radius=24)=>widget('image',{image:{url,id:0},image_size:'full',image_border_radius:dim(radius),image_alt:alt,link_to:'none'});
const button=(label,url,font)=>widget('button',{text:label,link:{url},align:'left',text_padding:dim(16,24),border_radius:dim(12),__globals__:{background_color:token('colors','primary'),button_text_color:token('colors','white'),typography_typography:token('typography',font)}});
const accordion=(label,elements,font,anchor)=>widget('nested-accordion',{items:[{_id:id(),item_title:label,element_css_id:anchor}],default_state:'all_collapsed',max_items_expended:'multiple',title_tag:'h3',accordion_padding:dim(20),content_padding:dim(4,20,24),accordion_border_radius:dim(12),accordion_background_normal_background:'classic',accordion_background_active_background:'classic',__globals__:{accordion_background_normal_color:token('colors','white'),accordion_background_active_color:token('colors','white'),normal_title_color:token('colors','primary'),hover_title_color:token('colors','harbor_deep'),active_title_color:token('colors','primary'),title_typography_typography:token('typography',font)}},[container(elements)]);
for(const locale of ['en','zh-hant','zh-hans']){
 const zh=locale!=='en';const f=type=>`about_${locale==='en'?'en':locale==='zh-hant'?'tc':'sc'}_${type}`;
 const t=(en,tc)=>locale==='en'?en:locale==='zh-hant'?tc:simplify(tc);
 const label=(en,tc,color='primary')=>heading(t(en,tc),'div',f('label'),color);
 const content=[
  section([row([
   col([label('ABOUT OUR CHURCH','認識教會'),heading(t('Rooted in faith.<br>Growing together.','扎根信仰，<br>一同成長。'),'h1',f('hero')),text(t('One church family, worshipping in Cantonese, English and Mandarin.','一個教會大家庭，以粵語、英語及國語一同敬拜。'),f('intro'),'ink_soft'),text(t('Since 1977 · Alhambra, California','始於1977年 · 加州阿罕布拉市'),f('label'),'primary')],48),
   col([image('__ABOUT_CHURCH__',t('Monterey Park Chinese Baptist Church building','教會建築'))],48)
  ],{flex_align_items:'center'})],'paper',null,{padding:dim(72,56,56),padding_mobile:dim(40,20,32)}),
  section([row([
   heading(t('Our story ↓','教會歷史 ↓'),'div',f('nav'),'primary',{link:{url:'#story'}}),
   heading(t('Our mission ↓','使命宣言 ↓'),'div',f('nav'),'primary',{link:{url:'#mission'}}),
   heading(t('Our pastoral team ↓','教牧團隊 ↓'),'div',f('nav'),'primary',{link:{url:'#pastors'}}),
   heading(t('Our beliefs ↗','信仰告白 ↗'),'div',f('nav'),'primary',{link:{url:locale==='en'?'/beliefs/':`/${locale}/beliefs/`}})
  ],{flex_direction_tablet:'row',flex_wrap:'wrap',flex_gap:{unit:'px',size:32},flex_gap_mobile:{unit:'px',size:20}})],'white',null,{padding:dim(20,56),padding_tablet:dim(20,32),padding_mobile:dim(18,20)}),
  section([row([
   col([label('OUR STORY','教會歷史'),heading(t('A home for<br>three languages.','三種語言，<br>同屬一家。'),'h2',f('section'))],38),
   col([text(t('Our church began in 1977 with Cantonese worship. As Mandarin and English ministries grew, we became a church family worshipping in three languages.','教會於1977年成立，最初以粵語崇拜。隨著國語及英語事工的發展，我們一直保持三種語言的崇拜。'),f('body')),
    row([['1977','Cantonese begins','開始粵語崇拜'],['1988','Mandarin begins','開始國語崇拜'],['1991','English begins','開始英語崇拜']].map(([year,en,tc])=>col([heading(year,'h3',f('year'),'primary'),text(t(en,tc),f('label'),'ink_soft')],30)),{flex_direction_tablet:'row',flex_gap:{unit:'px',size:16}}),
    accordion(t('Read our full story','閱讀完整教會歷史'),[text(t(source.story.en,source.story.zh),f('body'))],f('nav'),'full-story')
   ],58)
  ])],'paper','story'),
  section([row([
   col([label('OUR MISSION','使命宣言','white')],38),
   col([heading(t('Knowing God is our foundation.<br>Glorifying God is our goal.','認識神為基礎，<br>榮耀神是目標。'),'h2',f('mission'),'white'),text(t('Through worship, fellowship, discipleship, ministry, and evangelism, we seek to know God, and make His name known.','我們藉著敬拜、團契、門徒訓練、事奉、傳福音來認識和榮耀神。'),f('body'),'white')],58)
  ])],'secondary','mission'),
  section([row([
   col([label('OUR PASTORAL TEAM','教牧團隊'),heading(t('Meet Pastor Caleb.','認識張健庭牧師。'),'h2',f('section'))],38),
   col([row([
    container([image('__CALEB_PORTRAIT__',t('Rev. Caleb Cheung','張健庭牧師'),16)],{width:{unit:'%',size:27},width_mobile:{unit:'px',size:146}}),
    container([heading(t('Rev. Caleb Cheung, Ph.D.','張健庭博士'),'h3',f('card')),text(t('Senior Pastor · Cantonese Congregation','主任牧師及粵語堂牧師'),f('label'),'primary'),text(t('Originally from Hong Kong, Pastor Caleb studied at Fuller Theological Seminary and Claremont Graduate University and now serves as our senior pastor.','張牧師來自香港，曾於福樂神學院及克箂蒙研究大學進修，現任本教會主任牧師。'),f('body'))],{width:{unit:'%',size:69},width_mobile:{unit:'%',size:100}})
   ],{flex_direction_tablet:'row',flex_direction_mobile:'column',flex_gap:{unit:'px',size:24}}),
   accordion(t('Biography and contact','牧師簡介與聯絡'),[text(t(source.bio.en,source.bio.zh),f('body')),button(t('Contact Pastor Caleb','聯絡張牧師'),'mailto:calebcheung@mpcbc.net',f('nav'))],f('nav'),'pastor-biography')],58)
  ])],'paper','pastors'),
  section([row([
   col([label('WHAT WE BELIEVE','我們的信仰'),heading(t('Our faith, shared.','我們共同的信仰。'),'h2',f('section')),text(t('Read our statement of faith and its scripture references.','閱讀我們的信仰告白及相關經文。'),f('body'))],68),
   col([button(t('Our beliefs ↗','信仰告白 ↗'),locale==='en'?'/beliefs/':`/${locale}/beliefs/`,f('nav'))],28)
  ],{flex_align_items:'center'})],'mist')
 ];
 const exp={version:'0.4',title:t('About','關於我們'),type:'page',page_settings:{hide_title:'yes'},content};
 await fs.writeFile(`${root}/elementor/${locale}-about.json`,JSON.stringify(exp,null,2));
}
await fs.writeFile(`${root}/content-source/about-anchored-source.json`,JSON.stringify({design:'Anchored story · Recommended',source,locales:['en','zh-hant','zh-hans'],builder:'scripts/build-about-anchored.mjs'},null,2));
console.log('Prepared three About pages using native editable Elementor elements.');
