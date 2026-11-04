// Builds taste-mockup/about-1.html … about-5.html: five About-page options (taste-skill run, 3 Nov 2026).
// Shared: tokens (Newsprint + bright yellow, Hanken Grotesk + Noto Sans TC, as the live Home), the site header and the site footer (template 16).
// Per option: the dealt blueprints in .taste-skill/about-N/direction.json. Run: node .taste-skill/about-build.mjs
import { writeFileSync, readFileSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';
const ROOT = join(dirname(fileURLToPath(import.meta.url)), '..');
const B = JSON.parse(readFileSync(join(ROOT, '.taste-skill', 'beliefs.json'), 'utf8'));

// ---------- content (existing only; see notes.md "About options" copy deck) ----------
const zh = (t, tag = 'span', cls = 'zh') => `<${tag} class="${cls}" lang="zh-Hant">${t}</${tag}>`;
const hz = (t) => `<p class="h-zh" lang="zh-Hant">${t}</p>`;
const MAP = 'https://www.google.com/maps/search/?api=1&amp;query=302+W.+Commonwealth+Ave.+Alhambra+CA+91801';
const HOME = 'http://mpcbc.local/';
const C = {
  story: 'Our church was founded in 1977 with worship in Cantonese. During the 1980s, as the number of Mandarin-speaking immigrants in Los Angeles grew rapidly, the church saw the need to share the gospel and provide a spiritual home for new arrivals. Mandarin worship began in 1988. English worship was added in 1991, and the church continues to worship in all three languages.',
  storyZh: '教會成立於 1977年。教會剛開始是使用粵語崇拜。但從八十年代起，教會看到講國語的新移民人數在洛杉磯迅速增長，考慮到新移民對基督福音的需要。於是，教會決定開展國語事工，為新移民群體預備一個屬靈的家，於1988年開始了國語崇拜。之後，在1991年教會又增設了英語的崇拜，直到如今，教會一直保持有三種語言的崇拜。',
  storyShort: 'Our church began in 1977 with Cantonese worship. As Mandarin and English ministries grew, we became a church family worshipping in three languages.',
  storyShortZh: '教會於1977年成立，最初以粵語崇拜。隨著國語及英語事工的發展，我們一直保持三種語言的崇拜。',
  s1977: 'Our church was founded in 1977 with worship in Cantonese.', s1977Zh: '教會成立於 1977年。教會剛開始是使用粵語崇拜。',
  s1988: 'During the 1980s, as the number of Mandarin-speaking immigrants in Los Angeles grew rapidly, the church saw the need to share the gospel and provide a spiritual home for new arrivals. Mandarin worship began in 1988.',
  s1988Zh: '從八十年代起，教會看到講國語的新移民人數在洛杉磯迅速增長，於是開展國語事工，為新移民群體預備一個屬靈的家，於1988年開始了國語崇拜。',
  s1991: 'English worship was added in 1991, and the church continues to worship in all three languages.', s1991Zh: '在1991年教會又增設了英語的崇拜，直到如今，教會一直保持有三種語言的崇拜。',
  m1: 'Knowing God is our foundation.', m2: 'Glorifying God is our goal.', mZh: '認識神為基礎，榮耀神是目標。',
  m3: 'Through worship, fellowship, discipleship, ministry, and evangelism, we seek to know God, and make His name known.',
  m3Zh: '我們藉著敬拜、團契、門徒訓練、事奉、傳福音來認識和榮耀神。',
  calebBio: 'Pastor Caleb is from Hong Kong. After being called to ministry, he studied theology at Fuller Theological Seminary and pursued doctoral studies in theology and philosophy of religion at Claremont Graduate University. He has continued serving and shepherding this church and is its senior pastor.',
  calebBioZh: '張健庭牧師來自香港，多年前蒙召在福樂神學院念神學，繼在克箂蒙研究大學進修博士課程專修神學及宗教哲學，後留在本教會事奉與牧養，現任本教會主任牧師。',
  calebShort: 'Originally from Hong Kong, Pastor Caleb studied at Fuller Theological Seminary and Claremont Graduate University and now serves as our senior pastor.',
  edward: 'Lead pastor of Connect Ministry, the church’s English-language ministry.',
  visit1: 'We know visiting a church for the first time can feel intimidating. Our goal is simple: to help you feel welcomed, informed, and comfortable from the moment you arrive.',
  visit2: 'Whether you’re exploring Christianity, looking for a church home, or returning to church after many years, you’ll find authentic community, practical biblical teaching, and people who genuinely care.',
  expect: ['Friendly and welcoming environment', 'Christ-centered worship', 'Bible-based teaching', 'Casual attire', 'Opportunities to meet others', 'Programs for children, youth, college students, and adults'],
  hello: 'Tell us you’re coming and someone will look out for you. Or send a prayer request.',
};
const PEOPLE = [
  { name: 'Rev. Caleb Cheung, Ph.D.', zh: '張健庭牧師', role: 'Senior Pastor · Cantonese congregation', roleZh: '主任牧師及粵語堂牧師', img: 'assets/pastor-caleb.jpg', alt: 'Rev. Caleb Cheung, smiling, in a grey suit and red tie', line: C.calebShort },
  { name: 'Rev. Edward Lu', zh: '呂德勝牧師', role: 'Pastor · English congregation', roleZh: '英語堂牧師', img: null, ph: '[Photo of Rev. Edward Lu]', line: C.edward },
  { name: '[Pastor name]', zh: '[牧師姓名]', role: 'Pastor · Mandarin congregation', roleZh: '國語堂牧師', img: null, ph: '[Photo of the Mandarin pastor]', line: '[One line about this pastor.]' },
];
const TIMES = [
  { t: '9:00', en: 'Mandarin', zh: '國語', room: 'Sanctuary' },
  { t: '10:00', en: 'Children', zh: '兒童', room: '[Room]' },
  { t: '11:00', en: 'Cantonese', zh: '粵語', room: 'Sanctuary' },
  { t: '11:00', en: 'English', zh: '英語', room: 'Fellowship Hall' },
];
const GROUPS = [
  { en: 'Youth Sunday School', when: 'Sundays 10:00–10:45 AM', where: 'Education annex' },
  { en: 'College / Career Fellowship', when: 'Mondays 6:30–9:00 PM', where: 'West Covina' },
  { en: 'Adult Bible study', when: '[Day and time to be confirmed]', where: '[Place]' },
  { en: 'Fellowships in each language', when: '[Day, time]', where: 'Cantonese, English and Mandarin' },
];
const beliefs = B.en.map((b, i) => ({ ...b, zhTitle: B.zh[i].title, zhText: B.zh[i].text, zhRefs: B.zh[i].refs }));

const CREDITS = {
  garfield: '<a href="https://commons.wikimedia.org/w/index.php?curid=66364536">“Chinese Businesses, Garfield Avenue, Monterey Park, California”</a> by Ken Lund, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC BY-SA 2.0</a>, via Wikimedia Commons',
  foochow: '<a href="https://commons.wikimedia.org/wiki/File:Foochow_Bible_in_Characters,_1940,_p.2.jpg">“Foochow Bible in Characters, 1940, p.2”</a>, public domain, via Wikimedia Commons',
};

// ---------- shared shell ----------
const ABOUT_LINKS = [['#new-here', 'New here?', '初次來訪'], ['#about-us', 'About us', '關於我們'], ['#history', 'Our history', '教會歷史'], ['#beliefs', 'Mission & beliefs', '使命與信仰'], ['#pastors', 'Pastors & leaders', '教牧團隊'], ['#connect', 'Get connected', '與我們聯絡'], ['#contact', 'Contact & directions', '交通指南']];
const header = () => `<a class="skip" href="#main">Skip to content</a>
<header class="top">
  <a class="wordmark" href="${HOME}">MPCBC <span class="zh" lang="zh-Hant">慕道園</span></a>
  <nav class="nav" aria-label="Main">
    <a href="${HOME}#sundays">Sundays <span class="zh" lang="zh-Hant">主日</span></a>
    <a href="${HOME}#sundays">Sermons <span class="zh" lang="zh-Hant">講道</span></a>
    <a href="${HOME}#life">Church life <span class="zh" lang="zh-Hant">教會生活</span></a>
    <div class="drop"><a href="#about-us" aria-current="page">About <span class="zh" lang="zh-Hant">關於我們</span></a>
      <div class="drop__menu">${ABOUT_LINKS.map(([h, e, z]) => `<a href="${h}">${e} <span class="zh" lang="zh-Hant">${z}</span></a>`).join('')}</div></div>
    <a href="${HOME}#give">Give <span class="zh" lang="zh-Hant">奉獻</span></a>
    <a class="zh" href="http://mpcbc.local/about/?lang=zh-hant" lang="zh-Hant">繁體</a>
    <a class="zh" href="http://mpcbc.local/about/?lang=zh-hans" lang="zh-Hans">简体</a>
  </nav>
  <button class="menu-button" type="button" aria-expanded="false" aria-controls="menu-panel" data-menu>Menu&nbsp;<span class="zh" lang="zh-Hant">選單</span></button>
</header>
<nav class="menu-panel" id="menu-panel" aria-label="About this page" hidden>
  ${ABOUT_LINKS.map(([h, e, z]) => `<a href="${h}">${e} <span class="zh" lang="zh-Hant">${z}</span></a>`).join('\n  ')}
  <a href="http://mpcbc.local/about/?lang=zh-hant" lang="zh-Hant">繁體中文 <span class="zh">Traditional</span></a>
  <a href="http://mpcbc.local/about/?lang=zh-hans" lang="zh-Hans">简体中文 <span class="zh">Simplified</span></a>
</nav>`;

const footer = (credits) => `<footer class="foot" aria-label="Church details">
  <div class="foot__cols">
    <div><h2>Visit <span class="zh" lang="zh-Hant">交通指南</span></h2>
      <p>302 W. Commonwealth Ave.<br>Alhambra, CA 91801</p>
      <p class="times-mini"><span>Sundays <span class="zh" lang="zh-Hant">主日</span></span>Mandarin 9:00 AM<br>Children 10:00 AM<br>Cantonese 11:00 AM<br>English 11:00 AM</p>
      <ul><li><a href="${MAP}">Get directions <span class="zh" lang="zh-Hant">交通指引</span></a></li></ul></div>
    <div><h2>Contact <span class="zh" lang="zh-Hant">聯絡</span></h2>
      <ul><li><a href="tel:+16262891284">(626) 289-1284</a></li><li><a href="mailto:info@mpcbc.net">info@mpcbc.net</a></li><li><a href="${HOME}#connect">Prayer request <span class="zh" lang="zh-Hant">代禱</span></a></li></ul>
      <p>Office hours: [Days, times]</p></div>
    <div><h2>Follow <span class="zh" lang="zh-Hant">關注</span></h2>
      <ul><li><a href="https://www.instagram.com/mpcbconnect/">Instagram @mpcbconnect</a></li><li><a href="https://www.youtube.com/channel/UCJx8Xl0QqiNkZwwfLXaASHQ">English YouTube</a></li><li><a href="https://www.youtube.com/@MPCBC">YouTube @MPCBC <span class="zh" lang="zh-Hant">中文</span></a></li><li><span>WeChat <span class="zh" lang="zh-Hant">微信群</span> [group link]</span></li></ul></div>
    <div><h2>Explore <span class="zh" lang="zh-Hant">探索</span></h2>
      <ul><li><a href="http://mpcbc.local/visit/">Plan your visit</a></li><li><a href="${HOME}#sundays">Watch live <span class="zh" lang="zh-Hant">直播</span></a></li><li><a href="${HOME}#events">Events <span class="zh" lang="zh-Hant">活動</span></a></li><li><a href="${HOME}#kids">Kids and youth <span class="zh" lang="zh-Hant">兒童及青少年</span></a></li><li><a href="https://mpcbc.net/english/newsletters__trashed/newsletter-july-2026/">Newsletter <span class="zh" lang="zh-Hant">通訊</span></a></li></ul></div>
  </div>
  ${credits.length ? `<p class="credits">Pictures: ${credits.map((k) => CREDITS[k]).join('; ')}. Other photographs: MPCBC.</p>` : ''}
  <div class="foot__bar">
    <p><b>Monterey Park Chinese Baptist Church</b> <span class="zh" lang="zh-Hant">慕道園華人浸信會</span></p>
    <p>[Church logo] Founded in 1977, Alhambra, California.</p>
    <p class="langs"><a href="http://mpcbc.local/about/?lang=zh-hant" lang="zh-Hant">繁體中文</a><a href="http://mpcbc.local/about/?lang=zh-hans" lang="zh-Hans">简体中文</a><a href="#about-us">Back to top</a></p>
  </div>
</footer>`;

const BASE_CSS = `
:root {
  --paper: #ffffff; --grey: #e8e8e3; --grey-2: #dcdcd6; --ink: #181715; --muted: #5c5b55;
  --yellow: #ffd21a; --yellow-soft: #ffe680; --field-ink: #e8e8e3; --field-muted: #b4b3ab;
  --font: 'Hanken Grotesk', 'Noto Sans TC', system-ui, sans-serif; --font-zh: 'Noto Sans TC', system-ui, sans-serif;
  --s-small: clamp(0.875rem, 0.85rem + 0.1vw, 0.9375rem);
  --s-body: clamp(1.0625rem, 1rem + 0.25vw, 1.1875rem);
  --s-lead: clamp(1.25rem, 1.1rem + 0.7vw, 1.625rem);
  --s-title: clamp(2rem, 1.25rem + 2vw, 3rem);
  --s-head: clamp(2.5rem, 1.2rem + 5vw, 5.5rem);
  --margin: clamp(1rem, 2.2vw, 2rem); --gutter: clamp(1rem, 2vw, 2rem); --section: clamp(6rem, 11vw, 10rem);
  --ease: cubic-bezier(0.22, 1, 0.36, 1);
}
*, *::before, *::after { box-sizing: border-box; }
html { -webkit-text-size-adjust: 100%; text-size-adjust: 100%; scroll-behavior: smooth; }
body { margin: 0; background: var(--paper); color: var(--ink); font-family: var(--font); font-size: var(--s-body); line-height: 1.5; -webkit-font-smoothing: antialiased; overflow-x: clip; }
h1, h2, h3, h4, p, figure, ul, ol, dl, dd, blockquote { margin: 0; }
ul, ol { padding: 0; list-style: none; }
img, video { display: block; max-width: 100%; height: auto; }
button, input, select, textarea { font: inherit; color: inherit; }
button { background: none; border: 0; padding: 0; cursor: pointer; }
a { color: inherit; text-underline-offset: 0.2em; text-decoration-thickness: 0.07em; }
:target, section[id] { scroll-margin-top: 5rem; }
::selection { background: var(--yellow); color: var(--ink); }
:focus-visible { outline: 3px solid var(--ink); outline-offset: 3px; }
p { max-width: 62ch; text-wrap: pretty; }
.sr-only { position: absolute; width: 1px; height: 1px; overflow: hidden; clip-path: inset(50%); white-space: nowrap; }
.skip { position: absolute; left: var(--margin); top: 1rem; z-index: 50; clip-path: inset(50%); background: var(--ink); color: var(--paper); padding: 0.75rem 1.25rem; border-radius: 999px; }
.skip:focus { clip-path: none; }
.d { font-weight: 500; letter-spacing: -0.045em; line-height: 0.95; text-wrap: balance; }
.zh { font-family: var(--font-zh); font-weight: 500; }
.h-zh { font-family: var(--font-zh); font-weight: 500; font-size: var(--s-lead); line-height: 1.3; margin-top: 0.6rem; color: var(--muted); }
.zh-p { font-family: var(--font-zh); line-height: 1.8; }
.small { font-size: var(--s-small); line-height: 1.45; }
.muted { color: var(--muted); }
.nums { font-variant-numeric: tabular-nums; }
.button { display: inline-flex; align-items: center; justify-content: center; gap: 0.5em; min-height: 3.25rem; padding: 0 1.6rem; border-radius: 999px; background: var(--ink); color: var(--paper); font-weight: 600; text-decoration: none; white-space: nowrap; transition: transform 140ms var(--ease), background-color 180ms ease, color 180ms ease; }
.button--y { background: var(--yellow); color: var(--ink); }
.button--p { background: var(--paper); color: var(--ink); }
.button:active { transform: scale(0.97); }
.link { display: inline-flex; align-items: center; min-height: 2.75rem; text-decoration: underline; transition: text-decoration-color 180ms ease; }
@media (hover: hover) and (pointer: fine) { .button:hover { background: var(--yellow); color: var(--ink); } .button--y:hover { background: var(--ink); color: var(--paper); } .link:hover { text-decoration-color: transparent; } }
.ph { min-height: 8rem; display: flex; align-items: center; justify-content: center; text-align: center; padding: 1rem; background: var(--grey-2); color: var(--muted); font-size: var(--s-body); }

/* header: the site header (Hello Elementor + child theme), white on interior pages; About has its dropdown open on hover/focus */
.top { position: relative; z-index: 20; display: flex; align-items: center; justify-content: space-between; gap: 1.5rem; padding: 0.75rem var(--margin); background: var(--paper); }
.wordmark { display: inline-flex; align-items: baseline; gap: 0.6rem; text-decoration: none; font-weight: 700; font-size: var(--s-lead); line-height: 1; letter-spacing: -0.02em; }
.wordmark .zh { font-size: var(--s-body); }
.nav { display: flex; align-items: center; gap: clamp(1rem, 2vw, 2rem); }
.nav a { display: inline-flex; align-items: center; gap: 0.4rem; min-height: 2.75rem; text-decoration: none; font-weight: 500; }
.nav a .zh { font-size: var(--s-small); color: var(--muted); }
.nav a:hover, .nav a:focus-visible, .nav a[aria-current] { text-decoration: underline; text-decoration-thickness: 2px; }
.drop { position: relative; }
.drop__menu { position: absolute; top: 100%; left: -1rem; min-width: 17rem; padding: 0.5rem 0; background: var(--paper); display: none; }
.drop:hover .drop__menu, .drop:focus-within .drop__menu { display: grid; }
.drop__menu a { justify-content: space-between; padding: 0 1rem; }
.drop__menu a:hover { background: var(--grey); text-decoration: none; }
.menu-button { display: none; min-height: 2.75rem; padding: 0 1.1rem; border-radius: 999px; background: var(--ink); color: var(--paper); font-weight: 600; }
.menu-panel { position: absolute; z-index: 21; top: 4.5rem; left: var(--margin); right: var(--margin); background: var(--paper); padding: 0.5rem 0; }
.menu-panel[hidden] { display: none; }
.menu-panel a { display: flex; justify-content: space-between; gap: 1rem; padding: 0.75rem 1.5rem; min-height: 3rem; text-decoration: none; font-weight: 500; }
.menu-panel a:hover { background: var(--grey); }
.menu-panel .zh { color: var(--muted); }
@media (max-width: 1099px) { .js .nav { display: none; } .js .menu-button { display: inline-flex; align-items: center; } }

/* footer: site footer template 16 */
.foot { background: var(--ink); color: var(--field-ink); padding: 5rem var(--margin) 1.5rem; }
.foot__cols { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 2.5rem var(--gutter); }
.foot h2 { font-size: var(--s-body); font-weight: 700; }
.foot h2 .zh, .foot li .zh { margin-left: 0.4rem; color: var(--field-muted); }
.foot ul { margin-top: 0.5rem; display: grid; }
.foot li a { display: inline-flex; min-height: 2.75rem; align-items: center; text-decoration: none; }
.foot li a:hover { text-decoration: underline; }
.foot p { margin-top: 0.5rem; }
.foot .times-mini { display: grid; gap: 0.1rem; font-variant-numeric: tabular-nums; }
.foot .times-mini span { color: var(--field-muted); }
.foot .credits { margin-top: 3rem; max-width: none; font-size: var(--s-small); color: var(--field-muted); }
.foot .credits a { color: var(--field-ink); }
.foot__bar { margin-top: 2rem; display: flex; flex-wrap: wrap; justify-content: space-between; gap: 0.5rem 2rem; align-items: center; color: var(--field-muted); font-size: var(--s-small); }
.foot__bar p { margin: 0; }
.foot__bar a { display: inline-flex; min-height: 2.75rem; align-items: center; color: var(--field-ink); }
.foot__bar .langs { display: flex; gap: 1.25rem; }
@media (max-width: 899px) { .foot__cols { grid-template-columns: 1fr 1fr; } }
@media (max-width: 520px) { .foot__cols { grid-template-columns: 1fr; } }

/* accordion rows (Elementor free: Nested Accordion) */
details > summary { list-style: none; cursor: pointer; }
details > summary::-webkit-details-marker { display: none; }
.plus { flex: none; width: 1.75rem; height: 1.75rem; border-radius: 999px; background: var(--yellow); color: var(--ink); display: inline-grid; place-items: center; font-size: 1.125rem; line-height: 1; font-weight: 600; letter-spacing: 0; transition: transform 200ms var(--ease); }
details[open] .plus { transform: rotate(45deg); }

/* motion (Elementor Interactions: fade in + slide up, staggered; off for reduced motion) */
@media (prefers-reduced-motion: no-preference) {
  .js .r { opacity: 0; transform: translateY(24px); transition: opacity 700ms var(--ease), transform 900ms var(--ease); transition-delay: calc(var(--i, 0) * 80ms); }
  .js .r.is-in { opacity: 1; transform: none; }
  .kb { animation: kb 24s ease-in-out infinite alternate; }
  @keyframes kb { from { transform: scale(1); } to { transform: scale(1.08); } }
}
`;

const SCRIPT = `<script>
(() => {
  const btn = document.querySelector('[data-menu]'), menu = document.getElementById('menu-panel');
  const set = (o) => { menu.hidden = !o; btn.setAttribute('aria-expanded', String(o)); };
  btn.addEventListener('click', () => set(menu.hidden));
  menu.addEventListener('click', (e) => { if (e.target.closest('a')) set(false); });
  addEventListener('keydown', (e) => { if (e.key === 'Escape' && !menu.hidden) { set(false); btn.focus(); } });
  // entrance: the parts of each [data-r] block rise in turn, once
  const els = [];
  document.querySelectorAll('[data-r]').forEach((box) => [...box.children].forEach((c, i) => { c.classList.add('r'); c.style.setProperty('--i', i); els.push(c); }));
  if ('IntersectionObserver' in window) {
    const io = new IntersectionObserver((en) => en.forEach((e) => { if (e.isIntersecting) { e.target.classList.add('is-in'); io.unobserve(e.target); } }), { rootMargin: '0px 0px -8% 0px', threshold: 0.05 });
    els.forEach((el) => io.observe(el));
    setTimeout(() => els.forEach((el) => { const r = el.getBoundingClientRect(); if (r.top < innerHeight && r.bottom > 0) el.classList.add('is-in'); }), 2500);
  } else els.forEach((el) => el.classList.add('is-in'));
  // slow crossfade (Elementor free: container background slideshow)
  const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (reduce) document.querySelectorAll('video').forEach((v) => { v.removeAttribute('autoplay'); v.pause(); });
  document.querySelectorAll('[data-fade]').forEach((box) => {
    const imgs = [...box.querySelectorAll('img')]; let i = 0; imgs[0].classList.add('on');
    if (reduce || imgs.length < 2) return;
    let t = setInterval(() => { imgs[i].classList.remove('on'); i = (i + 1) % imgs.length; imgs[i].classList.add('on'); }, 6000);
    document.addEventListener('visibilitychange', () => { clearInterval(t); if (!document.hidden) t = setInterval(() => { imgs[i].classList.remove('on'); i = (i + 1) % imgs.length; imgs[i].classList.add('on'); }, 6000); });
  });
})();
</script>`;

const page = ({ n, title, desc, css, body, credits }) => `<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>${title}</title>
<meta name="description" content="${desc}">
<script>document.documentElement.classList.add('js');</script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;500;600;700&family=Noto+Sans+TC:wght@400;500;700&display=swap" rel="stylesheet">
<style>${BASE_CSS}
/* ===== option ${n} ===== */${css}
</style>
</head>
<body>
${header()}
<main id="main">
${body}
</main>
${footer(credits)}
${SCRIPT}
</body>
</html>
`;

const film = (cls = '') => `<video class="film ${cls}" autoplay muted loop playsinline preload="metadata" poster="assets/hero-poster.jpg" aria-label="VBS Magnified! 2025, filmed at the church"><source src="assets/hero-720.mp4" type="video/mp4"></video>`;
const beliefDetails = (b, cls = '') => `<details class="${cls}"><summary><span class="bname">${b.title} <span class="zh" lang="zh-Hant">${b.zhTitle}</span></span><span class="plus" aria-hidden="true">+</span></summary>
  <div class="bbody"><p>${b.text}</p><p class="zh-p" lang="zh-Hant">${b.zhText}</p><p class="small muted">Scripture references: ${b.refs}</p></div></details>`;
const timesList = (cls) => TIMES.map((x) => `<li class="${cls}"><b class="nums">${x.t}</b> <span>${x.en}${x.zh ? ` <span class="zh" lang="zh-Hant">${x.zh}</span>` : ''}</span> <span class="muted">${x.room}</span></li>`).join('');

// =====================================================================================
// OPTION 1 — page-is-the-object (seed 1103012, tone warm): the church's printed stationery
// =====================================================================================
const o1 = {
  n: 1, title: 'About MPCBC: programme', credits: ['garfield'],
  desc: 'About Monterey Park Chinese Baptist Church: our story since 1977, mission and beliefs, pastors, and how to visit. Option 1.',
  body: `
<section class="o1-hero" id="about-us" aria-labelledby="h1">
  <img class="o1-hero__room kb" src="assets/courtyard-lights.jpg" width="1600" height="900" alt="String lights over the church courtyard on a summer evening, children playing below">
  <div class="o1-card" data-r>
    <h1 id="h1" class="o1-card__title"><span>About</span> <span>our</span> <span>church</span></h1>
    <div class="o1-card__mid">
      <p class="o1-card__facts">Since 1977<br>Cantonese, English and Mandarin<br>302 W. Commonwealth Ave., Alhambra</p>
      <p class="o1-card__line">One church family, worshipping in three languages.</p>
      <p class="o1-card__zh" lang="zh-Hant">慕道園華人浸信會 · 始於1977年<br>一個教會大家庭，以粵語、英語及國語一同敬拜。</p>
    </div>
    <div class="o1-card__foot"><p class="small muted">Pictured: the courtyard</p><a class="button" href="#new-here">Plan your visit</a><a class="link" href="#history">Our story</a></div>
  </div>
</section>

<section class="o1-new" id="new-here" aria-labelledby="new-title">
  <div class="o1-new__row" data-r><div><h2 class="d o1-h" id="new-title">New here?</h2>${hz('初次來訪')}</div><p class="o1-new__note">${C.visit1}</p></div>
  <ul class="o1-facts" data-r>${TIMES.map((x) => `<li><b class="nums">${x.t} AM</b><span>${x.en} <span class="zh" lang="zh-Hant">${x.zh}</span> · ${x.room}</span></li>`).join('')}</ul>
  <div class="o1-new__more">
    <div data-r><h3 class="o1-h3">What to expect</h3><ul class="o1-expect">${C.expect.map((e) => `<li>${e}</li>`).join('')}</ul><p>${C.visit2}</p></div>
    <figure class="o1-new__pic" data-r>${film()}<figcaption class="small muted">VBS Magnified! 2025, filmed at the church</figcaption></figure>
  </div>
</section>

<section class="o1-hist" id="history" aria-labelledby="hist-title">
  <div class="o1-hist__head" data-r><h2 class="d o1-h" id="hist-title">A home for three languages.</h2>${hz('三種語言，同屬一家。')}</div>
  ${[['y', '1977', 'Cantonese', '粵語', C.s1977, C.s1977Zh, 'assets/sanctuary-aisle.jpg', 'The sanctuary aisle, red carpet between wooden pews', 'the sanctuary, where Cantonese and Mandarin worship today'],
     ['k', '1988', 'Mandarin', '國語', C.s1988, C.s1988Zh, 'assets/chinese-businesses-garfield-avenue-monterey-park-california.jpg', 'Chinese bank signs at Garfield and Garvey Avenues, Monterey Park', 'Garfield Avenue, Monterey Park (photo: Ken Lund, 2014)'],
     ['g', '1991', 'English', '英語', C.s1991, C.s1991Zh, 'assets/vbs-crafts.jpg', 'Children making crafts at VBS', 'children at VBS Magnified! 2025']]
    .map(([c, y, en, z, t, tz, img, alt, cap]) => `<article class="o1-sheet o1-sheet--${c}">
    <div class="o1-sheet__text" data-r><h3 class="o1-sheet__name"><span class="nums">${y}</span> ${en} <span class="zh" lang="zh-Hant">${z}</span></h3><p>${t}</p><p class="zh-p" lang="zh-Hant">${tz}</p><p class="small o1-sheet__cap">Pictured: ${cap}</p></div>
    <figure class="o1-sheet__pic"><img src="${img}" width="1600" height="1000" alt="${alt}" loading="lazy"></figure></article>`).join('\n  ')}
</section>

<section class="o1-beliefs" id="beliefs" aria-labelledby="bel-title">
  <div class="o1-menu">
    <svg class="scallop" viewBox="0 0 28 14" preserveAspectRatio="none" aria-hidden="true"><use href="#sc"/></svg>
    <div class="o1-menu__in" data-r>
      <h2 class="o1-menu__title" id="bel-title">Mission &amp; beliefs</h2>${hz('使命宣言 · 信仰告白')}
      <p class="o1-menu__mission">${C.m1}<br>${C.m2}</p>
      <p class="zh-p o1-menu__mzh" lang="zh-Hant">${C.mZh}<br>${C.m3Zh}</p>
      <p class="o1-menu__note">${C.m3}</p>
      <h3 class="o1-menu__group">What we believe <span class="zh" lang="zh-Hant">我們的信仰告白</span></h3>
      <ul class="o1-menu__items">${beliefs.map((b) => `<li><div class="o1-item"><b>${b.title}</b><span class="zh" lang="zh-Hant">${b.zhTitle}</span></div><p>${b.text}</p><p class="small muted">${b.refs}</p></li>`).join('')}</ul>
      <p class="o1-menu__close small">Eight foundations. One shared faith.<br><span class="zh" lang="zh-Hant">八項信仰根基，同一份信仰。</span></p>
    </div>
    <svg class="scallop scallop--b" viewBox="0 0 28 14" preserveAspectRatio="none" aria-hidden="true"><use href="#sc"/></svg>
  </div>
  <svg width="0" height="0" aria-hidden="true" style="position:absolute"><defs><g id="sc"><circle cx="14" cy="14" r="14" fill="#fff"/></g></defs></svg>
</section>

<section class="o1-team" id="pastors" aria-labelledby="team-title">
  <div data-r><h2 class="o1-team__h" id="team-title">Pastors &amp; leaders</h2>${hz('教牧團隊')}</div>
  <ul class="o1-tiles" data-r>${PEOPLE.map((p, i) => `<li><div class="o1-tile o1-tile--${i}">${p.img ? `<img src="${p.img}" width="400" height="500" alt="${p.alt}" loading="lazy">` : `<span class="o1-tile__ph"><b>${p.role.split(" · ")[1].replace(" congregation", "")} <span class="zh" lang="zh-Hant">${p.roleZh.replace("牧師", "")}</span></b>${p.ph}</span>`}</div>
    <h3>${p.name} <span class="zh" lang="zh-Hant">${p.zh}</span></h3><p class="small muted">${p.role} · <span class="zh" lang="zh-Hant">${p.roleZh}</span></p><p class="small">${p.line}</p></li>`).join('')}<li class="o1-tiles__bio"><p>${C.calebBio}</p><p class="zh-p small" lang="zh-Hant">${C.calebBioZh}</p></li></ul>
</section>

<section class="o1-reply" id="connect" aria-labelledby="reply-title">
  <div class="o1-reply__side" data-r><p class="o1-reply__p">${C.hello}</p><p class="small">Youth, college and fellowship groups meet through the week.</p></div>
  <div class="o1-reply__card" data-r>
    <h2 class="o1-reply__h" id="reply-title">Get connected</h2>${hz('與我們聯絡')}
    <ul class="o1-groups">${GROUPS.map((g) => `<li><b>${g.en}</b><span class="nums">${g.when}</span><span class="muted">${g.where}</span></li>`).join('')}</ul>
    <a class="o1-bar" href="${HOME}#connect">Say hello <span class="zh" lang="zh-Hant">聯絡卡</span></a>
  </div>
</section>

<section class="o1-visit" id="contact" aria-labelledby="visit-title">
  <img class="o1-visit__photo" src="assets/church-commonwealth.jpg" width="2000" height="1500" alt="The cream stucco chapel with its white steeple and queen palms at 302 W. Commonwealth Ave.">
  <div class="o1-visit__card">
    <h2 class="o1-visit__h" id="visit-title">Contact &amp; directions</h2>${hz('交通指南')}
    <p>302 W. Commonwealth Ave.<br>Alhambra, CA 91801</p>
    <ul class="o1-hours nums">${TIMES.map((x) => `<li><span>${x.en} <span class="zh" lang="zh-Hant">${x.zh}</span></span><span>${x.t} AM</span></li>`).join('')}</ul>
    <p><a class="link" href="tel:+16262891284">(626) 289-1284</a> · <a class="link" href="mailto:info@mpcbc.net">info@mpcbc.net</a><br><span class="small muted">Office hours: [Days, times]</span></p>
    <a class="button o1-visit__btn" href="${MAP}">Get directions <span class="zh" lang="zh-Hant">交通指引</span></a>
  </div>
</section>`,
  css: `
.o1-h { font-size: var(--s-head); }
.o1-hero { position: relative; min-height: calc(100svh - 4.25rem); display: grid; place-items: center; padding: 3rem var(--margin); overflow: hidden; background: var(--ink); }
.o1-hero__room { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; filter: brightness(0.62); }
.o1-card { position: relative; width: min(56%, 50rem); min-height: 62svh; padding: 2rem; background: var(--yellow); display: flex; flex-direction: column; justify-content: space-between; gap: 2rem; }
.o1-card__title { display: flex; justify-content: space-between; font-size: var(--s-title); font-weight: 500; letter-spacing: -0.03em; line-height: 1; text-transform: uppercase; }
.o1-card__facts, .o1-card__line { font-size: var(--s-lead); font-weight: 500; line-height: 1.15; }
.o1-card__line { margin-top: 1.25rem; max-width: 30ch; }
.o1-card__zh { margin-top: 1rem; font-family: var(--font-zh); font-weight: 500; line-height: 1.6; }
.o1-card__foot { display: grid; grid-template-columns: 1fr auto 1fr; align-items: center; gap: 1rem; }
.o1-card__foot .link { justify-self: end; }
.o1-new { padding: 8.75rem var(--margin) 7.5rem; }
.o1-new__row { display: grid; grid-template-columns: repeat(12, minmax(0, 1fr)); gap: var(--gutter); align-items: end; }
.o1-new__row > div { grid-column: 1 / 7; }
.o1-new__note { grid-column: 9 / 13; }
.o1-facts { margin-top: 7.5rem; display: flex; justify-content: space-between; gap: 2rem; }
.o1-facts b { display: block; font-size: var(--s-title); font-weight: 500; letter-spacing: -0.03em; line-height: 1; }
.o1-facts span { display: block; margin-top: 0.5rem; font-size: var(--s-small); color: var(--muted); }
.o1-new__more { margin-top: 5rem; display: grid; grid-template-columns: repeat(12, minmax(0, 1fr)); gap: var(--gutter); align-items: start; }
.o1-new__more > div { grid-column: 1 / 7; }
.o1-new__pic { grid-column: 8 / 13; }
.o1-new__pic video { width: 100%; aspect-ratio: 4 / 3; object-fit: cover; }
.o1-new__pic figcaption { margin-top: 0.5rem; }
.o1-h3 { font-size: var(--s-lead); font-weight: 500; }
.o1-expect { margin: 1rem 0 1.5rem; display: grid; grid-template-columns: 1fr 1fr; gap: 0.6rem 2rem; }
.o1-expect li { padding-left: 1.4rem; position: relative; }
.o1-expect li::before { content: ""; position: absolute; left: 0; top: 0.5em; width: 0.6rem; height: 0.6rem; border-radius: 999px; background: var(--yellow); }
.o1-hist { background: var(--paper); }
.o1-hist__head { padding: 0 var(--margin) 3rem; }
.o1-sheet { display: grid; grid-template-columns: repeat(12, minmax(0, 1fr)); gap: var(--gutter); min-height: 72svh; padding: 0 0 0 var(--margin); }
.o1-sheet--y { background: var(--yellow); } .o1-sheet--k { background: var(--ink); color: var(--field-ink); } .o1-sheet--g { background: var(--grey); }
.o1-sheet__text { grid-column: 1 / 7; padding: 2.5rem 0 3rem; display: flex; flex-direction: column; gap: 1.25rem; }
.o1-sheet__name { font-size: var(--s-title); font-weight: 500; letter-spacing: -0.03em; line-height: 1; margin-bottom: auto; padding-bottom: 3rem; }
.o1-sheet__text p { font-size: var(--s-lead); line-height: 1.35; max-width: 40ch; }
.o1-sheet__text .zh-p { font-size: var(--s-body); }
.o1-sheet__pic { grid-column: 7 / 13; position: relative; margin-top: 5.5rem; }
.o1-sheet__pic img { width: 100%; height: 100%; object-fit: cover; }
.o1-sheet__text .o1-sheet__cap { font-size: var(--s-small); opacity: 0.8; }
.o1-beliefs { background: var(--yellow); padding: var(--section) var(--margin) 5rem; }
.o1-menu { width: min(100%, 47.5rem); margin: 0 auto; }
.scallop { display: block; width: 100%; height: 14px; }
.o1-menu .scallop { fill: #fff; } .scallop use { fill: #fff; }
.scallop--b { transform: scaleY(-1); }
.o1-menu__in { background: var(--paper); padding: 4rem; text-align: center; }
.o1-menu__in > * { margin-left: auto; margin-right: auto; }
.o1-menu__title { font-size: var(--s-title); font-weight: 500; letter-spacing: -0.03em; line-height: 1; }
.o1-menu__mission { margin-top: 2.5rem; font-size: var(--s-lead); font-weight: 500; line-height: 1.3; }
.o1-menu__mzh { margin-top: 0.75rem; }
.o1-menu__note { margin-top: 1rem; }
.o1-menu__group { margin-top: 3.5rem; font-size: var(--s-lead); font-weight: 500; }
.o1-menu__items { margin-top: 1.5rem; text-align: left; display: grid; gap: 2rem; max-width: 36rem; }
.o1-item { display: flex; justify-content: space-between; gap: 1rem; font-size: var(--s-lead); }
.o1-item b { font-weight: 500; }
.o1-menu__items p { margin-top: 0.35rem; }
.o1-menu__close { margin-top: 3rem; }
.o1-team { padding: 6.5rem var(--margin) var(--section); }
.o1-team__h { font-size: var(--s-title); font-weight: 500; letter-spacing: -0.03em; line-height: 1; }
.o1-tiles { margin-top: 2.5rem; display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 2.25rem 0.75rem; }
.o1-tile { aspect-ratio: 4 / 5; display: grid; place-items: center; }
.o1-tile--0 { background: var(--yellow); } .o1-tile--1 { background: var(--ink); color: var(--field-ink); } .o1-tile--2 { background: var(--grey); color: var(--muted); }
.o1-tile img { width: 100%; height: 100%; object-fit: cover; filter: grayscale(1) contrast(1.1); mix-blend-mode: multiply; }
.o1-tile { position: relative; } .o1-tile__ph { position: absolute; inset: 0; display: flex; flex-direction: column; justify-content: space-between; padding: 1.25rem; font-size: var(--s-small); } .o1-tile__ph b { font-size: var(--s-title); font-weight: 500; letter-spacing: -0.03em; line-height: 1; }
.o1-tiles__bio { display: grid; gap: 1rem; align-content: start; }
.o1-tiles h3 { margin-top: 0.75rem; font-size: var(--s-body); font-weight: 600; }
.o1-tiles p { margin-top: 0.25rem; }
.o1-reply { position: relative; background: var(--ink); color: var(--field-ink); min-height: 80svh; padding: 6rem var(--margin) 0; display: grid; grid-template-columns: repeat(12, minmax(0, 1fr)); gap: var(--gutter); overflow: hidden; }
.o1-reply__side { grid-column: 1 / 5; align-self: end; padding-bottom: 4rem; display: grid; gap: 1rem; }
.o1-reply__p { font-size: var(--s-lead); line-height: 1.3; }
.o1-reply__side .small { color: var(--field-muted); }
.o1-reply__card { grid-column: 6 / 13; align-self: end; margin-bottom: -3.75rem; background: var(--paper); color: var(--ink); padding: 3rem 3rem 6.5rem; text-align: center; transform: rotate(2deg); transform-origin: bottom right; }
.o1-reply__h { font-size: var(--s-title); font-weight: 500; letter-spacing: -0.03em; line-height: 1; }
.o1-groups { margin: 2rem 0; display: grid; gap: 1rem; text-align: left; }
.o1-groups li { display: grid; grid-template-columns: 1.2fr 1fr; gap: 0 1rem; } .o1-groups li .muted { grid-column: 2; }
.o1-bar { display: flex; align-items: center; justify-content: center; gap: 0.5rem; min-height: 3.5rem; background: var(--ink); color: var(--paper); font-weight: 600; text-decoration: none; border-radius: 999px; transition: background-color 180ms ease, color 180ms ease, transform 140ms var(--ease); }
.o1-bar:hover { background: var(--yellow); color: var(--ink); } .o1-bar:active { transform: scale(0.98); }
.o1-visit { position: relative; min-height: 100svh; }
.o1-visit__photo { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; object-position: 60% 50%; }
.o1-visit__card { position: absolute; top: 3.75rem; left: var(--margin); width: 24rem; padding: 2rem; background: var(--paper); display: grid; gap: 1rem; }
.o1-visit__h { font-size: var(--s-title); font-weight: 500; letter-spacing: -0.03em; line-height: 1; }
.o1-hours { display: grid; gap: 0.25rem; }
.o1-hours li { display: flex; justify-content: space-between; }
.o1-visit__btn { width: 100%; }
@media (max-width: 899px) {
  .o1-card { width: 100%; min-height: 70svh; }
  .o1-new__row > div, .o1-new__note, .o1-new__more > div, .o1-new__pic { grid-column: 1 / -1; }
  .o1-facts { display: grid; grid-template-columns: 1fr 1fr; row-gap: 2rem; margin-top: 3rem; }
  .o1-sheet { grid-template-columns: 1fr; padding: 0; }
  .o1-sheet__pic { grid-column: 1; order: -1; margin: 0; } .o1-sheet__pic img { aspect-ratio: 4 / 3; }
  .o1-sheet__text { grid-column: 1; padding: 1.5rem var(--margin) 3rem; } .o1-sheet__name { padding-bottom: 1rem; }
  .o1-menu__in { padding: 2rem 1.25rem; }
  .o1-tiles { grid-template-columns: 1fr 1fr; }
  .o1-reply__side, .o1-reply__card { grid-column: 1 / -1; } .o1-reply__card { transform: rotate(1deg); padding: 2rem 1.25rem 5.5rem; }
  .o1-groups li { grid-template-columns: 1fr; gap: 0; }
  .o1-visit { min-height: 0; display: grid; } .o1-visit__photo { position: relative; height: 70svh; }
  .o1-visit__card { position: relative; top: auto; left: auto; width: auto; margin: -5rem var(--margin) 4rem; }
}
@media (max-width: 520px) { .o1-card__foot { grid-template-columns: 1fr; } .o1-card__foot .button { width: 100%; } .o1-card__foot .link { justify-self: start; }  .o1-expect { grid-template-columns: 1fr; } .o1-tiles { grid-template-columns: 1fr 1fr; } .o1-tiles__bio { grid-column: 1 / -1; } }
`};

// =====================================================================================
// OPTION 2 — editorial-report (seed 1103001): the printed annual
// =====================================================================================
const o2 = {
  n: 2, title: 'About MPCBC: annual', credits: ['foochow'],
  desc: 'About Monterey Park Chinese Baptist Church, laid out like a printed annual: history, pastors, mission, beliefs and visiting. Option 2.',
  body: `
<section class="o2-hero" id="about-us" aria-labelledby="h1">
  <div class="o2-hero__talk" data-r><p class="o2-hero__line">One church family, worshipping in Cantonese, English and Mandarin.</p><p class="zh-p" lang="zh-Hant">一個教會大家庭，以粵語、英語及國語一同敬拜。</p><a class="button" href="#new-here">Plan your visit</a></div>
  <div class="o2-hero__main">
    <nav class="o2-hero__links" aria-label="On this page"><a href="#history">Our history</a><a href="#pastors">Pastors</a><a href="#beliefs">Beliefs</a><a href="#contact">Directions</a></nav>
    <h1 class="d o2-h1" id="h1"><span class="o2-mask"><span>About</span></span> <span class="o2-mask"><span>our church</span></span></h1>
    <p class="o2-h1zh">Monterey Park Chinese Baptist Church <span class="zh" lang="zh-Hant">慕道園華人浸信會</span></p>
    <div class="o2-hero__pic"><div class="o2-fig"><b class="nums">1977</b><span>Founded in Alhambra, with worship in Cantonese</span></div><img src="assets/church-commonwealth.jpg" width="2000" height="1500" alt="The chapel at 302 W. Commonwealth Ave., with its white steeple and queen palms"></div>
  </div>
</section>

<section class="o2-tiles" id="history" aria-labelledby="hist-title">
  <div class="o2-board">
    <div class="o2-tile o2-tile--a" data-r><h2 class="o2-slogan" id="hist-title">A home for three languages</h2>${hz('三種語言，同屬一家。')}</div>
    <div class="o2-tile o2-tile--b"><p class="o2-big nums" aria-label="1988 and 1991">1988<br>1991</p><p class="small">Mandarin worship began, then English.</p></div>
    <figure class="o2-tile o2-tile--c"><img src="assets/sanctuary-aisle.jpg" width="1600" height="900" alt="The sanctuary, red carpet between the pews" loading="lazy"></figure>
  </div>
  <div class="o2-story" data-r><p class="zh-p o2-story__zh" lang="zh-Hant">${C.storyZh}</p><p class="o2-story__en">${C.story}</p></div>
</section>

<section class="o2-cap" id="pastors" aria-labelledby="past-title">
  <div class="o2-cap__side" data-r><h2 class="o2-h2" id="past-title">Pastors &amp; leaders</h2>${hz('教牧團隊')}<dl class="o2-facts small"><div><dt class="muted">From</dt><dd>Hong Kong</dd></div><div><dt class="muted">Studied</dt><dd>Fuller Theological Seminary; Claremont Graduate University</dd></div><div><dt class="muted">Contact</dt><dd><a href="mailto:info@mpcbc.net">info@mpcbc.net</a></dd></div></dl></div>
  <div class="o2-cap__text" data-r>
    <p class="o2-drop"><img src="assets/pastor-caleb.jpg" width="400" height="400" alt="Rev. Caleb Cheung" loading="lazy"><b>Rev. Caleb Cheung, Ph.D. (張健庭牧師)</b> is from Hong Kong. After being called to ministry, he studied theology at Fuller Theological Seminary and pursued doctoral studies in theology and philosophy of religion at Claremont Graduate University. He has continued serving and shepherding this church and is its senior pastor.</p>
    <p class="o2-drop o2-drop--2"><b>Rev. Edward Lu (呂德勝牧師)</b> is lead pastor of Connect Ministry, the church’s English-language ministry. The Mandarin congregation is led by <b>[Pastor name] ([牧師姓名])</b>.</p>
    <p class="zh-p o2-cap__zh" lang="zh-Hant">${C.calebBioZh}</p>
  </div>
</section>

<section class="o2-words" id="mission" aria-labelledby="mis-title">
  <div data-r><h2 class="d o2-mis" id="mis-title">Our mission</h2>${hz('使命宣言')}<p class="o2-mst">${C.m1}<br>${C.m2}</p><p class="zh-p" lang="zh-Hant">${C.mZh}</p></div>
  <p class="o2-pw" data-r>We seek to know God and make His name known through worship <img class="slot" src="assets/sanctuary-aisle.jpg" alt="" width="80" height="42" loading="lazy">, fellowship <img class="slot" src="assets/courtyard-lights.jpg" alt="" width="80" height="42" loading="lazy">, discipleship, ministry <img class="slot" src="assets/vbs-crafts.jpg" alt="" width="80" height="42" loading="lazy"> and evangelism.</p>
  <div class="o2-words__foot" data-r><p class="zh-p" lang="zh-Hant">${C.m3Zh}</p><a class="button" href="#beliefs">Read our beliefs</a></div>
</section>

<section class="o2-table" id="beliefs" aria-labelledby="bel-title">
  <h2 class="d o2-table__h" id="bel-title" data-r><span>Eight foundations.</span> <span>One shared faith.</span></h2>
  <div class="o2-table__grid">
    <figure class="o2-table__pic"><img src="assets/foochow-bible-in-characters-1940-p-2.jpg" width="1920" height="2716" alt="A page of Genesis from a Chinese Bible printed in characters, 1940" loading="lazy"><figcaption class="small muted">Genesis, from a Chinese Bible printed in 1940</figcaption></figure>
    <p class="o2-table__word small">Beliefs<br><span class="zh" lang="zh-Hant">信仰告白</span></p>
    <div class="o2-rows" data-r>${beliefs.map((b) => beliefDetails(b, 'o2-row')).join('')}</div>
  </div>
</section>

<section class="o2-close" id="new-here" aria-labelledby="new-title">
  <div class="o2-strip">${film()}<img src="assets/vbs-boy.jpg" width="1600" height="900" alt="A boy in a blue VBS shirt" loading="lazy"><img src="assets/courtyard-lights.jpg" width="1600" height="900" alt="String lights over the courtyard" loading="lazy"></div>
  <div class="o2-band" id="connect">
    <div class="o2-band__text" data-r><h2 class="o2-h2" id="new-title">New here? Get connected.</h2>${hz('初次來訪 · 與我們聯絡')}
      <p class="o2-band__st">${C.visit1.split('. Our goal')[0]}. <a href="${HOME}#connect">Tell us you’re coming</a> and someone will look out for you.</p>
      <p class="small">Sundays: Mandarin 9:00 · Children 10:00 · Cantonese and English 11:00 AM.<br>Youth Sunday School, Sundays 10:00–10:45 AM · College / Career Fellowship, Mondays 6:30–9:00 PM, West Covina.</p></div>
    <img class="o2-band__face" src="assets/pastor-caleb.jpg" width="300" height="375" alt="Rev. Caleb Cheung" loading="lazy">
  </div>
</section>

<section class="o2-contact" id="contact" aria-labelledby="con-title">
  <p class="o2-contact__s" data-r>Call, write or come by on a Sunday: we are at 302 W. Commonwealth Ave., Alhambra.</p>
  <img class="o2-contact__pic" src="assets/steeple-stamp.jpg" width="300" height="400" alt="The white steeple with its cross" loading="lazy">
  <div class="o2-contact__links" data-r><h2 class="o2-contact__q" id="con-title">Contact &amp; directions <span class="zh" lang="zh-Hant">交通指南</span></h2>
    <a class="link" href="mailto:info@mpcbc.net">info@mpcbc.net</a><a class="link" href="${MAP}">Get directions</a>
    <p class="small muted">Alhambra, CA 91801 · <a href="tel:+16262891284">(626) 289-1284</a> · Office hours: [Days, times]</p></div>
</section>`,
  css: `
:root { --s-st: clamp(1.25rem, 0.5rem + 1.9vw, 2.125rem); }
.o2-hero { display: grid; grid-template-columns: 34fr 66fr; min-height: calc(100svh - 4.25rem); background: var(--grey); padding: 0 var(--margin); column-gap: var(--gutter); overflow: hidden; }
.o2-hero__talk { padding-top: 28svh; display: flex; flex-direction: column; gap: 1.25rem; align-items: flex-start; }
.o2-hero__line { font-size: var(--s-lead); line-height: 1.35; max-width: 24ch; }
.o2-hero__main { display: flex; flex-direction: column; }
.o2-hero__links { display: flex; gap: 2rem; padding-top: 1rem; }
.o2-hero__links a { display: inline-flex; min-height: 2.75rem; align-items: center; font-size: var(--s-small); }
.o2-h1 { margin-top: 2rem; font-size: clamp(4.5rem, 9.4vw, 9.5rem); line-height: 0.86; letter-spacing: -0.055em; }
.o2-mask { display: block; overflow: hidden; padding-bottom: 0.08em; }
.o2-mask > span { display: inline-block; }
@media (prefers-reduced-motion: no-preference) { .js .o2-mask > span { animation: o2up 900ms var(--ease) both; } .js .o2-mask + .o2-mask > span { animation-delay: 110ms; } @keyframes o2up { from { transform: translateY(105%); } } }
.o2-h1zh { margin-top: 1rem; font-size: var(--s-lead); line-height: 1.3; }
.o2-hero__pic { position: relative; margin-top: auto; height: 34svh; }
.o2-hero__pic img { width: 100%; height: 100%; object-fit: cover; object-position: 50% 40%; }
.o2-fig { position: absolute; z-index: 2; left: 0; top: -3.25rem; width: 16.25rem; padding: 1rem 1.25rem; background: var(--yellow); }
.o2-fig b { display: block; font-size: var(--s-title); font-weight: 500; line-height: 1; letter-spacing: -0.03em; }
.o2-fig span { display: block; margin-top: 0.4rem; font-size: var(--s-small); }
.o2-tiles { padding: 8px; }
.o2-board { display: grid; grid-template-columns: repeat(12, minmax(0, 1fr)); grid-template-rows: 38svh 38svh; gap: 8px; }
.o2-tile { overflow: hidden; }
.o2-tile--a { grid-column: 1 / 9; grid-row: 1 / 3; background: var(--ink); color: var(--field-ink); padding: 1.25rem; display: flex; flex-direction: column; justify-content: flex-end; }
.o2-tile--a .h-zh { color: var(--field-muted); }
.o2-slogan { font-size: var(--s-head); font-weight: 500; text-transform: uppercase; letter-spacing: -0.04em; line-height: 0.88; }
.o2-tile--b { grid-column: 9 / 13; background: var(--yellow); padding: 1.25rem; display: flex; flex-direction: column; justify-content: flex-end; }
.o2-big { font-size: var(--s-head); font-weight: 500; letter-spacing: -0.05em; line-height: 0.9; }
.o2-tile--c { grid-column: 9 / 13; } .o2-tile--c img { width: 100%; height: 100%; object-fit: cover; }
.o2-story { display: grid; grid-template-columns: repeat(12, minmax(0, 1fr)); gap: var(--gutter); padding: 2.5rem calc(var(--margin) - 8px) 0; }
.o2-story__zh { grid-column: 1 / 6; } .o2-story__en { grid-column: 7 / 13; font-size: var(--s-lead); line-height: 1.35; }
.o2-cap { display: grid; grid-template-columns: repeat(12, minmax(0, 1fr)); gap: var(--gutter); padding: 8.75rem var(--margin); }
.o2-cap__side { grid-column: 1 / 4; }
.o2-h2 { font-size: var(--s-title); font-weight: 500; letter-spacing: -0.03em; line-height: 1; }
.o2-facts { margin-top: 2rem; display: grid; gap: 1rem; }
.o2-facts dd a { text-decoration: underline; }
.o2-cap__text { grid-column: 4 / 13; }
.o2-drop { font-size: var(--s-st); line-height: 1.2; letter-spacing: -0.02em; max-width: none; color: var(--muted); }
.o2-drop b { color: var(--ink); font-weight: 500; }
.o2-drop img { float: left; width: 3.6em; height: 3.6em; margin: 0.2em 0.5em 0 0; object-fit: cover; }
.o2-drop--2 { margin-top: 1.2em; }
.o2-cap__zh { margin-top: 2rem; }
.o2-words { background: var(--yellow); padding: var(--section) var(--margin); }
.o2-mis { font-size: var(--s-head); }
.o2-words .h-zh { color: var(--ink); }
.o2-mst { margin: 2.5rem 0 0.75rem; font-size: var(--s-st); line-height: 1.2; font-weight: 500; }
.o2-pw { margin-top: 4rem; font-size: var(--s-st); font-weight: 500; line-height: 1.25; max-width: 52ch; }
.slot { display: inline-block; width: 1.9em; height: 1em; vertical-align: -0.12em; object-fit: cover; margin: 0 0.06em; }
.o2-words__foot { margin-top: 4.5rem; margin-left: 50%; display: grid; gap: 1.25rem; justify-items: start; }
.o2-table { padding: var(--section) var(--margin); }
.o2-table__h { font-size: var(--s-head); }
.o2-table__h span { display: block; } .o2-table__h span:first-child { padding-left: 3vw; }
.o2-table__grid { margin-top: 6rem; display: grid; grid-template-columns: repeat(12, minmax(0, 1fr)); gap: var(--gutter); align-items: start; }
.o2-table__pic { grid-column: 1 / 5; }
.o2-table__pic img { width: 100%; aspect-ratio: 4 / 5; object-fit: cover; object-position: 50% 30%; }
.o2-table__pic figcaption { margin-top: 0.5rem; }
.o2-table__word { grid-column: 6 / 7; }
.o2-rows { grid-column: 7 / 13; display: grid; }
.o2-row summary { display: flex; justify-content: space-between; align-items: center; gap: 1rem; min-height: 3.5rem; font-size: var(--s-lead); }
.o2-row summary .zh { margin-left: 0.5rem; color: var(--muted); font-size: var(--s-body); }
.o2-row .bbody { padding: 0.5rem 0 1.5rem; display: grid; gap: 0.75rem; }
.o2-row:nth-child(4n+1):not(:first-child) { margin-top: 1.75rem; }
.o2-strip { display: grid; grid-template-columns: repeat(3, 1fr); }
.o2-strip img, .o2-strip video { width: 100%; height: 40svh; object-fit: cover; }
.o2-band { position: relative; background: var(--ink); color: var(--field-ink); min-height: 44svh; padding: 4rem var(--margin); display: grid; grid-template-columns: repeat(12, minmax(0, 1fr)); gap: var(--gutter); }
.o2-band .h-zh { color: var(--field-muted); }
.o2-band__text { grid-column: 1 / 9; display: grid; gap: 1.5rem; align-content: start; }
.o2-band__st { font-size: var(--s-lead); line-height: 1.3; }
.o2-band__st a { text-decoration-thickness: 4px; text-underline-offset: 6px; text-decoration-color: var(--yellow); }
.o2-band__st a:hover { color: var(--yellow); }
.o2-band__text .small { color: var(--field-muted); }
.o2-band__face { grid-column: 11 / 13; align-self: end; width: 100%; max-width: 9.5rem; justify-self: end; aspect-ratio: 4 / 5; object-fit: cover; }
.o2-contact { display: grid; grid-template-columns: repeat(12, minmax(0, 1fr)); gap: var(--gutter); padding: 10rem var(--margin) 7.5rem; align-items: start; }
.o2-contact__s { grid-column: 1 / 6; font-size: var(--s-st); line-height: 1.1; letter-spacing: -0.02em; }
.o2-contact__pic { grid-column: 7 / 9; width: 100%; max-width: 10rem; aspect-ratio: 1; object-fit: cover; justify-self: end; }
.o2-contact__links { grid-column: 9 / 13; display: grid; justify-items: start; gap: 0.25rem; }
.o2-contact__q { font-size: var(--s-body); font-weight: 600; margin-bottom: 0.75rem; }
.o2-contact__q .zh { margin-left: 0.4rem; color: var(--muted); }
.o2-contact__links .link { font-size: var(--s-body); }
.o2-contact__links p { margin-top: 0.75rem; } .o2-contact__links p a { text-decoration: underline; }
@media (max-width: 899px) {
  .o2-hero { grid-template-columns: 1fr; padding-bottom: 2rem; }
  .o2-hero__talk { order: 2; padding-top: 1.5rem; } .o2-hero__links { display: none; }
  .o2-h1 { font-size: 15vw; }
  .o2-hero__pic { margin-top: 4rem; height: auto; } .o2-hero__pic img { aspect-ratio: 4 / 3; height: auto; }
  .o2-board { grid-template-columns: 1fr 1fr; grid-template-rows: auto; gap: 6px; }
  .o2-tile--a { grid-column: 1 / -1; grid-row: auto; min-height: 46svh; } .o2-tile--b, .o2-tile--c { grid-column: auto; aspect-ratio: 1; }
  .o2-slogan { font-size: 8.6vw; }
  .o2-story__zh, .o2-story__en, .o2-cap__side, .o2-cap__text, .o2-table__pic, .o2-table__word, .o2-rows, .o2-band__text, .o2-contact__s, .o2-contact__links { grid-column: 1 / -1; }
  .o2-story__en { order: -1; }
  .o2-cap { padding: 5rem var(--margin); } .o2-drop { font-size: var(--s-lead); } .o2-drop img { width: 4.8em; height: 4.8em; }
  .o2-words__foot { margin-left: 0; }
  .o2-table__pic { position: static; width: 60%; justify-self: end; order: 3; } .o2-table__word { display: none; } .o2-table__grid { margin-top: 3rem; }
  .o2-strip { grid-template-columns: 1fr 1fr; } .o2-strip img { height: auto; aspect-ratio: 1; } .o2-strip video { grid-column: 1 / -1; aspect-ratio: 4 / 3; height: auto; }
  .o2-band__face { grid-column: 1 / -1; max-width: 6rem; }
  .o2-contact { padding: 5rem var(--margin); } .o2-contact__s { font-size: var(--s-lead); } .o2-contact__pic { grid-column: 1 / 4; justify-self: start; max-width: 6rem; }
  .o2-contact__links { grid-column: 4 / -1; }
}
`};

// =====================================================================================
// OPTION 3 — place-carries-it (seed 1103001): the corner of Commonwealth Avenue
// =====================================================================================
const chapel = `<svg class="o3-chapel" viewBox="0 0 200 160" role="img" aria-label="Outline of the chapel with its steeple, a dot marking 302 W. Commonwealth Ave."><path d="M20 150V92l40-38 40 38V68l20-20V24l6-14 6 14v24l20 20v24l28 0v58Z" fill="currentColor"/><circle cx="60" cy="128" r="7" fill="#ffd21a"/></svg>`;
const o3 = {
  n: 3, title: 'About MPCBC: the place', credits: ['garfield', 'foochow'],
  desc: 'About Monterey Park Chinese Baptist Church at 302 W. Commonwealth Ave., Alhambra: the building, our story, beliefs and pastors. Option 3.',
  body: `
<section class="o3-hero" id="about-us" aria-labelledby="h1">
  <div class="o3-hero__fade" data-fade>
    <img src="assets/church-commonwealth.jpg" width="2000" height="1500" alt="The chapel at 302 W. Commonwealth Ave. in morning light, white steeple and queen palms">
    <img src="assets/sanctuary-aisle.jpg" width="1600" height="900" alt="Inside the sanctuary: the red-carpeted aisle between wooden pews">
    <img src="assets/courtyard-lights.jpg" width="1600" height="900" alt="The courtyard under string lights">
  </div>
  <div class="o3-hero__foot">
    <div class="o3-hero__over"><p class="o3-hero__s">Monterey Park Chinese Baptist Church <span class="zh" lang="zh-Hant">慕道園華人浸信會</span><br>302 W. Commonwealth Ave., Alhambra · since 1977</p><a class="button button--p" href="#new-here">Plan your visit</a></div>
    <h1 class="d o3-h1" id="h1">About MPCBC</h1>
  </div>
</section>

<section class="o3-pano" id="new-here" aria-labelledby="new-title">
  <div data-r><h2 class="d o3-h" id="new-title">Visiting for the first time?</h2>${hz('初次來訪')}</div>
  <div class="o3-caps" data-r><p class="small">Sundays: Mandarin 9:00, Children 10:00, Cantonese and English 11:00 AM.</p><p class="small">Cantonese and Mandarin in the Sanctuary; English in the Fellowship Hall.</p><p class="small">${C.expect.slice(1, 4).join(', ')}.</p></div>
  <figure class="o3-pano__pic">${film()}</figure>
  <div class="o3-pano__p" data-r><p>${C.visit1}</p><p>${C.visit2}</p></div>
</section>

<section class="o3-hist" id="history" aria-labelledby="hist-title">
  <div data-r><h2 class="d o3-h" id="hist-title">Our history</h2>${hz('教會歷史')}<p class="o3-lead">${C.storyShort}</p><p class="zh-p" lang="zh-Hant">${C.storyShortZh}</p></div>
  <div class="o3-mosaic">
    <figure class="o3-wide"><img src="assets/chinese-businesses-garfield-avenue-monterey-park-california.jpg" width="1920" height="1440" alt="Chinese bank and supermarket signs at Garfield and Garvey Avenues, Monterey Park" loading="lazy"><p class="o3-wide__note">${C.s1988}</p><figcaption class="small">Garfield Avenue, Monterey Park, a mile from the church (photo: Ken Lund, 2014)</figcaption></figure>
    <div class="o3-side"><div class="o3-tile">${chapel}</div>
      <dl class="o3-pairs"><div><dt class="small muted">Founded</dt><dd class="nums">1977, in Cantonese</dd></div><div><dt class="small muted">Mandarin</dt><dd class="nums">1988</dd></div><div><dt class="small muted">English</dt><dd class="nums">1991</dd></div><div><dt class="small muted">Today</dt><dd>Three languages</dd></div></dl></div>
  </div>
</section>

<section class="o3-list" id="beliefs" aria-labelledby="bel-title">
  <div class="o3-list__col" data-r>
    <h2 class="o3-list__h" id="bel-title">Mission &amp; beliefs <span class="zh" lang="zh-Hant">使命與信仰</span></h2>
    <p class="o3-group muted">Our mission</p>
    <p class="o3-mission">${C.m1} ${C.m2}<span class="zh-p" lang="zh-Hant">${C.mZh}</span></p>
    <p class="o3-row-p">${C.m3}</p>
    <p class="o3-group muted">What we believe</p>
    ${beliefs.map((b) => `<details class="o3-row"><summary><span class="dot" aria-hidden="true"></span><span class="o3-row__n">${b.title}</span><span class="o3-row__v zh" lang="zh-Hant">${b.zhTitle}</span></summary><div class="bbody"><p>${b.text}</p><p class="zh-p" lang="zh-Hant">${b.zhText}</p><p class="small muted">${b.refs}</p></div></details>`).join('\n    ')}
  </div>
  <figure class="o3-list__pic"><img src="assets/foochow-bible-in-characters-1940-p-2.jpg" width="1920" height="2716" alt="A page of Genesis from a Chinese Bible printed in characters, 1940" loading="lazy"><figcaption class="small muted">Genesis, from a Chinese Bible printed in 1940</figcaption></figure>
</section>

<section class="o3-rows" id="pastors" aria-labelledby="past-title">
  <div class="o3-rows__head" data-r><h2 class="d o3-h" id="past-title">Pastors &amp; leaders</h2>${hz('教牧團隊')}</div>
  ${PEOPLE.map((p, i) => `<article class="o3-person o3-person--${i}"><div data-r><h3 class="o3-person__n">${p.name}</h3><p class="h-zh" lang="zh-Hant">${p.zh} · ${p.roleZh}</p><p class="muted small">${p.role}</p><p>${i === 0 ? C.calebBio : p.line}</p></div>
    ${p.img ? `<img class="o3-person__pic" src="${p.img}" width="400" height="500" alt="${p.alt}" loading="lazy">` : `<span class="o3-person__pic ph">${p.ph}</span>`}</article>`).join('\n  ')}
</section>

<section class="o3-last" id="connect" aria-labelledby="con-title">
  <img class="o3-last__pic" src="assets/courtyard-lights.jpg" width="1600" height="900" alt="A lit bulb over the courtyard, children playing beyond" loading="lazy">
  <div class="o3-last__text" data-r><h2 class="o3-last__h" id="con-title">Get connected</h2><p class="h-zh" lang="zh-Hant">與我們聯絡</p>
    <ul class="o3-g">${GROUPS.map((g) => `<li><b>${g.en}</b> <span class="nums">${g.when}</span> · ${g.where}</li>`).join('')}</ul>
    <a class="button button--p" href="${HOME}#connect">Say hello <span class="zh" lang="zh-Hant">聯絡卡</span></a></div>
</section>

<section class="o3-addr" id="contact" aria-labelledby="addr-title">
  <h2 class="o3-addr__r" id="addr-title"><span>Contact &amp; directions <span class="zh" lang="zh-Hant">交通指南</span></span><img src="assets/steeple-stamp.jpg" width="300" height="400" alt="The white steeple" loading="lazy"></h2>
  <p class="o3-addr__r"><a href="${MAP}">302 W. Commonwealth Ave.</a></p>
  <p class="o3-addr__r">Alhambra, CA 91801</p>
  <p class="o3-addr__r nums">Sundays from 9:00 AM</p>
  <p class="o3-addr__r nums"><a href="tel:+16262891284">(626) 289-1284</a></p>
  <p class="o3-addr__r o3-addr__r--go">Write to us <a class="o3-go" href="mailto:info@mpcbc.net" aria-label="Email info@mpcbc.net">→</a></p>
</section>`,
  css: `
.o3-h { font-size: var(--s-head); }
.o3-hero { position: relative; height: calc(100svh - 4.25rem); min-height: 34rem; overflow: hidden; background: var(--ink); color: var(--paper); }
.o3-hero__fade img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; opacity: 0; transition: opacity 1600ms ease; }
.o3-hero__fade img.on, .o3-hero__fade img:first-child { opacity: 1; }
.js .o3-hero__fade img:first-child:not(.on) { opacity: 0; }
.o3-hero__foot { position: absolute; z-index: 2; inset: auto 0 0 0; padding: 0 var(--margin) 1.5rem; background: rgba(24, 23, 21, 0.5); }
.o3-hero__over { display: flex; justify-content: space-between; align-items: flex-end; gap: 1rem; padding-top: 1.25rem; }
.o3-hero__s { font-size: var(--s-lead); line-height: 1.3; }
.o3-h1 { font-size: clamp(3rem, 16.4vw, 17rem); line-height: 0.9; letter-spacing: -0.06em; white-space: nowrap; margin-top: 3rem; }
.o3-pano { padding: var(--section) 0 0; }
.o3-pano > div, .o3-caps, .o3-pano__p { padding: 0 var(--margin); }
.o3-caps { margin-top: 1.75rem; display: flex; gap: 3rem; }
.o3-caps p { width: 15rem; }
.o3-pano__pic { margin-top: 2.5rem; }
.o3-pano__pic video { width: 100%; aspect-ratio: 21 / 9; object-fit: cover; }
.o3-pano__p { margin-top: 2.5rem; display: grid; grid-template-columns: 1fr 1fr; gap: var(--gutter); font-size: var(--s-lead); line-height: 1.35; }
.o3-hist { padding: var(--section) var(--margin) 0; }
.o3-lead { margin-top: 1.5rem; font-size: var(--s-lead); line-height: 1.35; max-width: 44ch; }
.o3-hist .zh-p { margin-top: 0.75rem; max-width: 44ch; }
.o3-mosaic { margin-top: 4.5rem; display: grid; grid-template-columns: repeat(12, minmax(0, 1fr)); gap: 1rem; align-items: start; }
.o3-wide { grid-column: 1 / 9; position: relative; }
.o3-wide img { width: 100%; aspect-ratio: 16 / 10; object-fit: cover; }
.o3-wide__note { position: absolute; top: 0; right: 0; width: 20rem; padding: 1.25rem; background: var(--paper); font-size: var(--s-body); }
.o3-wide figcaption { margin-top: 0.5rem; color: var(--muted); }
.o3-side { grid-column: 9 / 13; }
.o3-tile { background: var(--yellow); color: var(--ink); display: grid; place-items: center; aspect-ratio: 4 / 3.2; }
.o3-chapel { width: 60%; }
.o3-pairs { margin-top: 1.25rem; display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
.o3-pairs dd { font-weight: 500; }
.o3-list { padding: var(--section) var(--margin); display: grid; grid-template-columns: repeat(12, minmax(0, 1fr)); gap: var(--gutter); align-items: start; }
.o3-list__col { grid-column: 3 / 8; }
.o3-list__h { font-size: var(--s-lead); font-weight: 500; margin-bottom: 2rem; }
.o3-list__h .zh { margin-left: 0.4rem; color: var(--muted); }
.o3-group { margin-top: 2.5rem; margin-bottom: 0.5rem; }
.o3-mission { font-size: var(--s-lead); font-weight: 500; line-height: 1.3; }
.o3-mission .zh-p { display: block; font-size: var(--s-body); font-weight: 500; margin-top: 0.4rem; }
.o3-row-p { margin-top: 0.75rem; }
.o3-row summary { display: flex; align-items: center; gap: 0.5rem; min-height: 2.75rem; }
.dot { width: 1rem; height: 1rem; border-radius: 999px; background: var(--yellow); flex: none; transition: background-color 180ms ease; }
.o3-row[open] .dot, .o3-row summary:hover .dot { background: var(--ink); }
.o3-row__n { font-weight: 500; }
.o3-row__v { margin-left: auto; color: var(--muted); font-size: var(--s-small); }
.o3-row .bbody { padding: 0.25rem 0 1.25rem 1.5rem; display: grid; gap: 0.6rem; }
.o3-list__pic { grid-column: 9 / 13; }
.o3-list__pic img { width: 100%; aspect-ratio: 4 / 5; object-fit: cover; object-position: 50% 25%; }
.o3-list__pic figcaption { margin-top: 0.5rem; }
.o3-rows { background: var(--grey); padding: var(--section) var(--margin); display: grid; grid-template-columns: repeat(12, minmax(0, 1fr)); gap: var(--gutter); row-gap: 7rem; }
.o3-rows__head { grid-column: 5 / 13; }
.o3-person { grid-column: 5 / 13; display: grid; grid-template-columns: 1fr auto; gap: var(--gutter); align-items: start; }
.o3-person > div { max-width: 40ch; display: grid; gap: 0.6rem; }
.o3-person__n { font-size: var(--s-title); font-weight: 500; letter-spacing: -0.03em; line-height: 1; }
.o3-person__pic { object-fit: cover; }
.o3-person--0 .o3-person__pic { width: 15rem; aspect-ratio: 4 / 5; }
.o3-person--1 .o3-person__pic { width: 20rem; aspect-ratio: 3 / 2; }
.o3-person--2 .o3-person__pic { width: 11.25rem; aspect-ratio: 1; }
.o3-last { position: relative; min-height: 90svh; display: grid; grid-template-columns: repeat(12, minmax(0, 1fr)); gap: var(--gutter); padding: 0 var(--margin) 4rem; color: var(--paper); }
.o3-last__pic { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; filter: brightness(0.62); }
.o3-last__text { position: relative; grid-column: 8 / 13; align-self: end; display: grid; gap: 1rem; justify-items: start; }
.o3-last__h { font-size: var(--s-head); font-weight: 400; letter-spacing: -0.04em; line-height: 0.95; }
.o3-last .h-zh { color: var(--paper); }
.o3-g { display: grid; gap: 0.5rem; }
.o3-addr { background: var(--yellow); }
.o3-addr h2.o3-addr__r { font-weight: 400; }
.o3-addr__r { display: flex; align-items: flex-end; justify-content: space-between; min-height: 9.5rem; padding: 0 var(--margin) 1rem; font-size: clamp(2.25rem, 6vw, 5.5rem); line-height: 1; letter-spacing: -0.04em; max-width: none; }
.o3-addr__r:nth-child(odd) { background: var(--yellow-soft); }
.o3-addr__r a { text-decoration: none; } .o3-addr__r a:hover { text-decoration: underline; }
.o3-addr__r img { width: 5.5rem; height: 5.5rem; border-radius: 999px; object-fit: cover; }
.o3-addr__r .zh { font-size: 0.5em; letter-spacing: 0; margin-left: 0.4em; }
.o3-addr__r--go { justify-content: flex-start; gap: 1.5rem; }
.o3-go { display: inline-grid; place-items: center; width: 0.9em; height: 0.9em; border-radius: 999px; background: var(--ink); color: var(--yellow); font-size: 0.8em; transition: transform 160ms var(--ease); }
.o3-go:hover { transform: translateX(4px); text-decoration: none !important; }
@media (max-width: 899px) {
  .o3-h1 { font-size: 16vw; margin: 1rem 0 0.5rem; }
  .o3-hero__foot { display: flex; flex-direction: column; } .o3-h1 { order: -1; } .o3-hero__over { flex-direction: column; align-items: flex-start; }
  .o3-caps { flex-direction: column; gap: 0.5rem; } .o3-caps p { width: auto; }
  .o3-pano__pic video { aspect-ratio: 4 / 5; } .o3-pano__p { grid-template-columns: 1fr; }
  .o3-wide, .o3-side, .o3-list__col, .o3-list__pic, .o3-rows__head, .o3-person, .o3-last__text { grid-column: 1 / -1; }
  .o3-wide__note { position: static; width: auto; padding: 1rem 0 0; }
  .o3-list__pic { position: static; width: 60%; justify-self: end; }
  .o3-person { grid-template-columns: 1fr; } .o3-person > div { max-width: none; } .o3-person__pic { order: -1; } .o3-rows { row-gap: 4rem; }
  .o3-person--0 .o3-person__pic { width: 100%; } .o3-person--1 .o3-person__pic { width: 70%; justify-self: end; } .o3-person--2 { grid-template-columns: 1fr 11rem; } .o3-person--2 .o3-person__pic { width: 11rem; order: 0; }
  .o3-last { min-height: 0; padding: 0; color: var(--ink); } .o3-last__pic { position: relative; grid-column: 1 / -1; width: 100%; height: 70svh; filter: none; } .o3-last .button--p { background: var(--ink); color: var(--paper); } .o3-last__text { padding: 2rem var(--margin) 3rem; } .o3-g { order: 2; } .o3-last .h-zh { color: var(--muted); }
  .o3-addr { padding-top: 6rem; } .o3-addr__r .zh { font-size: 1em; } .o3-addr__r { min-height: 5.25rem; font-size: 8.4vw; }
}
`};

// =====================================================================================
// OPTION 4 — sentence-as-layout (seed 1103003): "Rooted in faith, growing together."
// =====================================================================================
const o4 = {
  n: 4, title: 'About MPCBC: the sentence', credits: [],
  desc: 'About Monterey Park Chinese Baptist Church: rooted in faith, growing together. Story, pastors, mission, beliefs and contact. Option 4.',
  body: `
<section class="o4-hero" id="about-us" aria-labelledby="h1">
  <h1 class="o4-sent" id="h1"><span class="o4-a">Rooted in faith,</span>
    <span class="o4-frame">${film()}</span>
    <span class="o4-b">growing together.</span></h1>
  <p class="o4-hero__zh" lang="zh-Hant">扎根信仰，一同成長。</p>
  <div class="o4-hero__foot" data-r><p class="o4-hero__s">About Monterey Park Chinese Baptist Church <span class="zh" lang="zh-Hant">慕道園華人浸信會</span>: one church family, worshipping in Cantonese, English and Mandarin since 1977.</p><a class="button button--y" href="#connect">Plan your visit</a></div>
</section>

<section class="o4-cross" id="history" aria-labelledby="hist-title">
  <img class="o4-cross__big" src="assets/church-commonwealth.jpg" width="2000" height="1500" alt="The chapel at 302 W. Commonwealth Ave. with its white steeple" loading="lazy">
  <div class="o4-cross__text" data-r><h2 class="o4-cross__h" id="hist-title"><span>A home for</span> <span>three languages.</span></h2>${hz('三種語言，同屬一家。')}<p class="o4-cross__p">${C.storyShort}</p></div>
  <img class="o4-cross__small" src="assets/sanctuary-vbs.jpg" width="1600" height="900" alt="The sanctuary decorated for VBS" loading="lazy">
  <div class="o4-cross__story" data-r><p>${C.story}</p><p class="zh-p" lang="zh-Hant">${C.storyZh}</p></div>
</section>

<section class="o4-facts" id="pastors" aria-labelledby="past-title">
  <div class="o4-facts__l" data-r><img src="assets/pastor-caleb.jpg" width="320" height="320" alt="Rev. Caleb Cheung" loading="lazy"><dl class="small"><div><dt class="muted">Senior pastor</dt><dd>Since [year]</dd></div><div><dt class="muted">From</dt><dd>Hong Kong</dd></div><div><dt class="muted">Studied</dt><dd>Fuller; Claremont</dd></div></dl></div>
  <div class="o4-facts__r" data-r><h2 class="o4-h2" id="past-title">Pastors &amp; leaders</h2>${hz('教牧團隊')}<p class="o4-facts__p"><b>Rev. Caleb Cheung, Ph.D.</b> <span class="zh" lang="zh-Hant">張健庭牧師</span>. ${C.calebBio}</p><p class="zh-p" lang="zh-Hant">${C.calebBioZh}</p></div>
  <p class="o4-facts__word small">Leaders</p>
  <ul class="o4-rec small" data-r>${PEOPLE.map((p) => `<li><span>${p.name} <span class="zh" lang="zh-Hant">${p.zh}</span></span><span class="muted">${p.role.split(' · ')[1]}</span></li>`).join('')}<li><span>[Leader name]</span><span class="muted">[Role]</span></li></ul>
  <ul class="o4-rec small" data-r>${TIMES.map((x) => `<li><span>${x.en} <span class="zh" lang="zh-Hant">${x.zh}</span> · ${x.room}</span><span class="muted nums">${x.t} AM</span></li>`).join('')}</ul>
</section>

<section class="o4-fields" id="mission" aria-labelledby="mis-title">
  <div class="o4-field o4-field--y"><p class="o4-field__p" data-r>${C.m1}<span class="zh" lang="zh-Hant">認識神為基礎。</span></p><h2 class="d o4-field__h" id="mis-title">Knowing God</h2></div>
  <div class="o4-field o4-field--k o4-field--m"><p class="o4-field__p" data-r>${C.m2}<span class="zh" lang="zh-Hant">榮耀神是目標。</span></p><h3 class="d o4-field__h">Glorifying God</h3></div>
  <div class="o4-field o4-field--g"><p class="o4-field__p" data-r>${C.m3}<span class="zh" lang="zh-Hant">${C.m3Zh}</span></p><h3 class="d o4-field__h">His name known</h3></div>
</section>

<section class="o4-line" id="beliefs" aria-labelledby="bel-title">
  <h2 class="o4-line__day" id="bel-title">What we believe <span class="zh" lang="zh-Hant">我們的信仰告白</span></h2>
  <div class="o4-line__rows" data-r>${beliefs.map((b) => `<details class="o4-lr"><summary><span>${b.title}</span><span class="zh" lang="zh-Hant">${b.zhTitle}</span><span class="o4-lr__ref">${b.refs.split(/[;,]/)[0]}</span><span class="plus" aria-hidden="true">+</span></summary><div class="bbody"><p>${b.text}</p><p class="zh-p" lang="zh-Hant">${b.zhText}</p><p class="small muted">${b.refs}</p></div></details>`).join('')}</div>
</section>

<section class="o4-inv" id="connect" aria-labelledby="inv-title">
  <span id="new-here"></span>
  <div data-r><h2 class="d o4-inv__h" id="inv-title"><span>Know God.</span> <span>Grow together.</span></h2><p class="h-zh" lang="zh-Hant">[中文標語]</p></div>
  <ul class="o4-inv__g" data-r>${GROUPS.map((g) => `<li><b>${g.en}</b><span class="nums">${g.when}</span><span>${g.where}</span></li>`).join('')}</ul>
  <div class="o4-inv__foot"><p>New here? ${C.hello.split(' Or')[0]}</p><a class="button button--y" href="${HOME}#connect">Say hello <span class="zh" lang="zh-Hant">聯絡卡</span></a></div>
</section>

<section class="o4-mail" id="contact" aria-labelledby="mail-title">
  <div class="o4-mail__top"><h2 class="o4-h2" id="mail-title">Contact &amp; directions</h2>${hz('交通指南')}<p>Questions about Sundays, kids or prayer? Write to the church.</p></div>
  <a class="o4-mail__addr" href="mailto:info@mpcbc.net">info@mpcbc.net</a>
  <div class="o4-mail__foot small"><p><a href="tel:+16262891284">(626) 289-1284</a> · <a href="${MAP}">302 W. Commonwealth Ave., Alhambra</a> · Office hours: [Days, times]</p><p><a href="https://www.instagram.com/mpcbconnect/">Instagram</a> · <a href="https://www.youtube.com/@MPCBC">YouTube</a></p></div>
</section>`,
  css: `
.o4-hero { position: relative; min-height: calc(100svh - 4.25rem); padding: 0 var(--margin) 2rem; display: grid; grid-template-rows: 1fr auto auto; background: var(--paper); }
:root { --s-xl: clamp(3.25rem, 9.6vw, 9.5rem); }
.o4-sent { align-self: center; display: grid; grid-template-columns: 1fr; gap: 1.25rem; padding-top: 2rem; font-size: var(--s-xl); font-weight: 500; letter-spacing: -0.05em; line-height: 0.9; }
.o4-a { justify-self: start; } .o4-b { justify-self: end; text-align: right; }
.o4-frame { position: relative; display: block; justify-self: center; width: 42vw; aspect-ratio: 16 / 9; overflow: hidden; border-radius: 4px; background: var(--ink); }
.o4-frame video { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }
.o4-frame img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; opacity: 0; transition: opacity 1600ms ease; }
.o4-frame img:first-child, .o4-frame img.on { opacity: 1; }
.js .o4-frame img:first-child:not(.on) { opacity: 0; }
.o4-hero__zh { justify-self: center; font-family: var(--font-zh); font-weight: 500; font-size: var(--s-lead); color: var(--muted); margin-bottom: 3rem; }
.o4-hero__foot { display: flex; justify-content: space-between; align-items: end; gap: 2rem; }
.o4-hero__s { font-size: var(--s-body); max-width: 40ch; }
.o4-cross { position: relative; display: grid; grid-template-columns: repeat(12, minmax(0, 1fr)); gap: var(--gutter); padding: 0 0 var(--section) var(--margin); background: var(--grey); }
.o4-cross__big { grid-column: 7 / 13; grid-row: 1 / 3; width: 100%; height: 100%; min-height: 80svh; object-fit: cover; object-position: 30% 50%; margin-top: 12svh; }
.o4-cross__text { grid-column: 1 / 9; grid-row: 1; position: relative; z-index: 2; padding-top: 18svh; }
.o4-cross__h { font-size: var(--s-head); font-weight: 500; letter-spacing: -0.045em; line-height: 1.02; }
.o4-cross__h span { display: block; } .o4-cross__h span + span { color: var(--ink); background: var(--grey); display: inline; padding-right: 0.15em; }
.o4-cross__p { margin-top: 1.5rem; font-size: var(--s-lead); line-height: 1.4; max-width: 30ch; }
.o4-cross__small { grid-column: 1 / 5; grid-row: 2; width: 100%; aspect-ratio: 9 / 10; object-fit: cover; margin-top: 4rem; }
.o4-cross__story { grid-column: 1 / 7; display: grid; gap: 1rem; padding-right: var(--margin); margin-top: 4rem; }
.o4-cross__story p:first-child { font-size: var(--s-lead); line-height: 1.4; }
.o4-facts { padding: var(--section) var(--margin); display: grid; grid-template-columns: 34fr 66fr; column-gap: 0; row-gap: 7.5rem; }
.o4-facts__l { display: grid; gap: 1.25rem; align-content: start; max-width: 12rem; }
.o4-facts__l img { width: 10rem; aspect-ratio: 1; object-fit: cover; }
.o4-facts__l dl { display: grid; gap: 1.25rem; }
.o4-h2 { font-size: var(--s-title); font-weight: 500; letter-spacing: -0.03em; line-height: 1; }
.o4-facts__r { display: grid; gap: 1rem; max-width: 56rem; }
.o4-facts__p { font-size: var(--s-lead); line-height: 1.35; max-width: none; margin-top: 1rem; }
.o4-facts__p b { font-weight: 600; }
.o4-facts__word { grid-column: 1; }
.o4-rec { grid-column: 2; display: grid; gap: 0.6rem; max-width: 36rem; }
.o4-rec + .o4-rec { margin-top: -6rem; }
.o4-rec li { display: flex; justify-content: space-between; gap: 2rem; }
.o4-fields .o4-field { min-height: 64svh; padding: 3rem var(--margin) 1.75rem; display: grid; grid-template-columns: repeat(12, minmax(0, 1fr)); gap: var(--gutter); grid-template-rows: auto 1fr; }
.o4-field--y { background: var(--yellow); } .o4-field--k { background: var(--ink); color: var(--field-ink); } .o4-field--g { background: var(--grey); }
.o4-field__p { grid-column: 8 / 12; font-size: var(--s-lead); line-height: 1.35; }
.o4-field__p .zh { display: block; margin-top: 0.5rem; font-size: var(--s-body); }
.o4-field__h { grid-column: 1 / 13; align-self: end; font-size: var(--s-xl); line-height: 0.9; }
.o4-field--m .o4-field__p { grid-column: 2 / 6; } .o4-field--m .o4-field__h { text-align: right; }
.o4-line { background: var(--paper); padding: var(--section) var(--margin); }
.o4-line__day { font-size: var(--s-title); font-weight: 500; letter-spacing: -0.03em; line-height: 1; margin-bottom: 2rem; }
.o4-line__day .zh { color: var(--muted); font-size: 0.6em; margin-left: 0.4rem; letter-spacing: 0; }
.o4-lr summary { display: grid; grid-template-columns: 33% 33% 1fr auto; align-items: center; min-height: 1.5em; padding: 0.35rem 0; font-size: var(--s-title); letter-spacing: -0.03em; }
.o4-lr summary:hover > span:first-child { text-decoration: underline; }
.o4-lr__ref { color: var(--muted); font-size: var(--s-body); font-variant-numeric: tabular-nums; }
.o4-lr .bbody { padding: 0.5rem 0 1.5rem 33%; display: grid; gap: 0.75rem; }
.o4-inv { background: var(--ink); color: var(--field-ink); min-height: 90svh; padding: 5rem var(--margin) 2.5rem; display: grid; grid-template-rows: auto 1fr auto; gap: 3rem; }
.o4-inv .h-zh { color: var(--field-muted); }
.o4-inv__h { font-size: var(--s-xl); line-height: 0.9; }
.o4-inv__h span { display: block; }
.o4-inv__g { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: var(--gutter); align-self: end; }
.o4-inv__g li { display: grid; gap: 0.25rem; }
.o4-inv__g span { color: var(--field-muted); font-size: var(--s-small); }
.o4-inv__foot { display: flex; justify-content: space-between; align-items: center; gap: 2rem; }
.o4-inv__foot p { font-size: var(--s-lead); max-width: 34ch; }
.o4-mail { background: var(--yellow); min-height: 86svh; padding: 4rem var(--margin) 2rem; display: grid; grid-template-rows: auto 1fr auto; }
.o4-mail__top { display: grid; gap: 0.5rem; } .o4-mail__top .h-zh { color: var(--ink); margin-top: 0; } .o4-mail__top p:last-child { font-size: var(--s-lead); margin-top: 1rem; }
.o4-mail__addr { align-self: end; margin-bottom: 8svh; font-size: var(--s-xl); letter-spacing: -0.055em; line-height: 1; text-decoration: none; font-weight: 400; transition: color 180ms ease; }
.o4-mail__addr:hover { color: var(--paper); }
.o4-mail__foot { display: flex; justify-content: space-between; flex-wrap: wrap; gap: 1rem; }
.o4-mail__foot a { text-decoration: underline; }
@media (max-width: 899px) {
  :root { --s-xl: 12vw; } .o4-sent { font-size: 11vw; } .o4-frame { width: 100%; aspect-ratio: 4 / 3; } .o4-hero__zh { justify-self: start; }
  .o4-hero__foot { flex-direction: column; align-items: stretch; }
  .o4-cross { padding: 0 var(--margin) 5rem; }
  .o4-cross__big { grid-column: 4 / 13; grid-row: 2; min-height: 0; aspect-ratio: 3 / 4; margin: 0 calc(var(--margin) * -1) 0 0; width: calc(100% + var(--margin)); }
  .o4-cross__text { grid-column: 1 / -1; padding-top: 4rem; } .o4-cross__h { font-size: 10vw; }
  .o4-cross__small { grid-column: 1 / 7; grid-row: 3; } .o4-cross__story { grid-column: 1 / -1; padding: 0; }
  .o4-facts { grid-template-columns: 1fr; row-gap: 2.5rem; } .o4-facts__l { order: 2; max-width: none; } .o4-facts__l dl { grid-template-columns: 1fr 1fr; }
  .o4-facts__word, .o4-rec { grid-column: 1; } .o4-rec + .o4-rec { margin-top: 0; }
  .o4-field__p, .o4-field--m .o4-field__p { grid-column: 1 / 13; } .o4-field--m .o4-field__h { text-align: left; }
  .o4-lr summary { grid-template-columns: 1fr auto; font-size: 1.375rem; } .o4-lr summary .zh { grid-column: 1; grid-row: 2; font-size: var(--s-small); color: var(--muted); } .o4-lr__ref { display: none; } .o4-lr .bbody { padding-left: 0; }
  .o4-inv__g { grid-template-columns: 1fr 1fr; } .o4-inv__foot { flex-direction: column; align-items: stretch; }
  
}
@media (max-width: 520px) { .o4-inv__g { grid-template-columns: 1fr; } }
`};

// =====================================================================================
// OPTION 5 — one-motif (seed 1103002): the chapel's arched window, as a round-topped arch
// =====================================================================================
const arch = (cls = '') => `<span class="arch-mark ${cls}" aria-hidden="true"></span>`;
const o5 = {
  n: 5, title: 'About MPCBC: the arch', credits: ['foochow'],
  desc: 'About Monterey Park Chinese Baptist Church: one arched window shape runs the page through our story, beliefs, pastors and contact. Option 5.',
  body: `
<section class="o5-hero" id="about-us" aria-labelledby="h1">
  <div class="o5-card" data-r>
    <div class="o5-stripe" aria-hidden="true">${'<i></i>'.repeat(40)}</div>
    <div class="o5-card__in">
      <h1 class="o5-h1" id="h1">About our church</h1>
      <p class="o5-card__mark">Monterey Park Chinese Baptist Church <span class="zh" lang="zh-Hant">慕道園華人浸信會 · 認識教會</span></p>
      <p class="o5-card__s">One church family, worshipping in Cantonese, English and Mandarin since 1977, at 302 W. Commonwealth Ave., Alhambra.</p>
      <a class="button o5-btn" href="#new-here">Plan your visit</a>
    </div>
    <p class="o5-card__links"><a href="#history">Our history</a><a href="#beliefs">Mission &amp; beliefs</a><a href="#pastors">Pastors</a></p>
  </div>
  <figure class="o5-hero__room" data-fade><img src="assets/church-commonwealth.jpg" width="2000" height="1500" alt="The chapel at 302 W. Commonwealth Ave., white steeple and queen palms"><img src="assets/sanctuary-aisle.jpg" width="1600" height="900" alt="Inside the sanctuary"></figure>
</section>

<section class="o5-lead" id="new-here" aria-labelledby="new-title">
  <h2 class="o5-h2" id="new-title">New here? <span class="zh" lang="zh-Hant">初次來訪</span></h2>
  <p class="o5-lead__p" data-r>${C.visit1}</p>
  <div class="o5-cols" data-r>
    <p><b>Who comes.</b> <span class="muted">${C.visit2}</span></p>
    <p><b>What to expect.</b> <span class="muted">${C.expect.join(', ')}.</span></p>
    <p><b>Where to go.</b> <span class="muted">302 W. Commonwealth Ave., Alhambra. Cantonese and Mandarin worship in the Sanctuary; English worship in the Fellowship Hall; Youth Sunday School in the education annex.</span></p>
    <p><b>When.</b> <span class="muted">Sundays: Mandarin 9:00 AM, Children 10:00 AM, Cantonese 11:00 AM, English 11:00 AM. Questions: (626) 289-1284 or info@mpcbc.net.</span></p>
  </div>
</section>

<section class="o5-hist" id="history" aria-labelledby="hist-title">
  <figure class="o5-hist__pic"><img src="assets/sanctuary-vbs.jpg" width="1600" height="900" alt="The sanctuary decorated for VBS, children in blue shirts" loading="lazy"></figure>
  <div class="o5-hist__r">
    <div data-r><h2 class="o5-h2" id="hist-title">Our history</h2>${hz('教會歷史')}</div>
    <ul class="o5-points" data-r>
      <li class="c1"><h3>${arch()}<span class="nums">1977</span> Cantonese</h3><p>${C.s1977}</p></li>
      <li class="c2"><h3>${arch()}The 1980s</h3><p>Mandarin-speaking immigrants in Los Angeles grew rapidly; the church saw the need for a spiritual home for new arrivals.</p></li>
      <li class="c3"><h3>${arch()}<span class="nums">1988</span> Mandarin</h3><p>Mandarin worship began, to share the gospel with new arrivals.</p></li>
      <li class="c1"><h3>${arch()}<span class="nums">1991</span> English</h3><p>English worship was added to the church’s Sunday.</p></li>
      <li class="c2"><h3>${arch()}Today</h3><p>The church continues to worship in all three languages.</p></li>
      <li class="c3"><h3>${arch()}One roof</h3><p>Sanctuary for Cantonese and Mandarin; Fellowship Hall for English.</p></li>
    </ul>
    <p class="zh-p o5-hist__zh" lang="zh-Hant" data-r>${C.storyZh}</p>
  </div>
</section>

<section class="o5-menu" id="beliefs" aria-labelledby="bel-title">
  <div class="o5-menu__head" data-r><h2 class="o5-h2" id="bel-title">Mission &amp; beliefs</h2>${hz('使命宣言 · 信仰告白')}<p class="o5-menu__m">${C.m1} ${C.m2}</p><p class="zh-p" lang="zh-Hant">${C.mZh}${C.m3Zh}</p><p>${C.m3}</p></div>
  <div class="o5-cat"><h3 class="o5-cat__h">What we believe</h3><span class="nums">8 foundations</span></div>
  <ul class="o5-items" data-r>${beliefs.map((b) => `<li><span class="o5-bul" lang="zh-Hant" aria-hidden="true">${b.zhTitle.slice(0, 2)}</span><div><p class="o5-items__n"><b>${b.title}</b><span class="zh" lang="zh-Hant">${b.zhTitle}</span></p><p>${b.text}</p><p class="small muted">${b.refs}</p></div></li>`).join('')}</ul>
  <figure class="o5-menu__pic"><img src="assets/foochow-bible-in-characters-1940-p-2.jpg" width="1920" height="2716" alt="A page of Genesis from a Chinese Bible printed in characters, 1940" loading="lazy"><figcaption class="small muted">Genesis, from a Chinese Bible printed in 1940</figcaption></figure>
</section>

<section class="o5-panel" id="pastors" aria-labelledby="past-title">
  <div class="o5-panel__head" data-r><h2 class="o5-panel__h" id="past-title">Pastors &amp; leaders</h2>${hz('教牧團隊')}</div>
    <img class="o5-panel__face" src="assets/pastor-caleb.jpg" width="400" height="500" alt="Rev. Caleb Cheung, smiling, in a grey suit and red tie" loading="lazy">
  <div class="o5-panel__r" data-r>
    ${PEOPLE.map((p, i) => `<div class="o5-who"><h3>${p.name} <span class="zh" lang="zh-Hant">${p.zh}</span></h3><p class="small o5-role">${p.role} · <span class="zh" lang="zh-Hant">${p.roleZh}</span></p><p>${i === 0 ? C.calebShort : p.line}</p></div>`).join('')}
    <a class="button button--y" href="mailto:info@mpcbc.net">Contact Pastor Caleb</a>
  </div>
</section>

<section class="o5-coupon" id="connect" aria-labelledby="cou-title">
  <form class="o5-cp" action="${HOME}#connect" onsubmit="return false">
    <div class="o5-cp__l" data-r><h2 class="o5-cp__h" id="cou-title">Get connected</h2>${hz('與我們聯絡')}<p>${C.hello}</p>
      <p class="small">Youth Sunday School, Sundays 10:00–10:45 AM · College / Career Fellowship, Mondays 6:30–9:00 PM, West Covina.</p></div>
    <div class="o5-cp__r">
      <label class="sr-only" for="f-name">Your name</label><input id="f-name" placeholder="Your name 姓名">
      <label class="sr-only" for="f-mail">Email or phone</label><input id="f-mail" placeholder="Email or phone 電郵或電話">
      <label class="sr-only" for="f-svc">Which service will you visit?</label><select id="f-svc"><option>Which service will you visit? 參加哪一堂？</option><option>Cantonese 粵語, 11:00</option><option>English, 11:00</option><option>Mandarin 國語, 9:00</option><option>Not sure yet</option></select>
      <label class="sr-only" for="f-msg">Anything we should know?</label><input id="f-msg" placeholder="Anything we should know? (optional)">
      <button class="o5-cp__send" type="submit">Send</button>
    </div>
  </form>
</section>

<section class="o5-return" id="contact" aria-labelledby="ret-title">
  <figure class="o5-return__obj">${film()}</figure>
  <div class="o5-return__t" data-r><h2 class="o5-return__h" id="ret-title">Contact &amp; directions</h2>${hz('交通指南')}<p>302 W. Commonwealth Ave.<br>Alhambra, CA 91801</p><p class="o5-return__fact nums"><a href="tel:+16262891284">(626) 289-1284</a></p><p class="small"><a href="mailto:info@mpcbc.net">info@mpcbc.net</a> · Office hours: [Days, times]</p><a class="button" href="${MAP}">Get directions</a></div>
</section>`,
  css: `
.arch-mark { display: inline-block; width: 0.7em; height: 0.85em; border-radius: 999px 999px 0 0; background: currentColor; margin-right: 0.45em; vertical-align: -0.05em; }
.o5-h2 { font-size: var(--s-title); font-weight: 500; letter-spacing: -0.03em; line-height: 1; }
.o5-h2 .zh { font-size: 0.6em; color: var(--muted); letter-spacing: 0; margin-left: 0.3em; }
.o5-hero { display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); gap: 12px; padding: 12px; min-height: calc(100svh - 4.25rem); background: var(--grey); }
.o5-card { min-width: 0; background: var(--paper); display: flex; flex-direction: column; }
.o5-stripe { display: flex; height: 3.5rem; overflow: hidden; }
.o5-stripe i { flex: none; width: 28px; height: 100%; background: var(--yellow); }
.o5-stripe i:nth-child(even) { background: var(--paper); }
.o5-card__in { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; gap: 1rem; padding: 2rem; }
.o5-card__mark { font-weight: 500; font-size: var(--s-lead); line-height: 1.3; }
.o5-card__mark .zh { display: block; font-size: var(--s-body); }
.o5-h1 { font-size: var(--s-head); font-weight: 400; letter-spacing: -0.045em; line-height: 0.95; max-width: 9ch; }
.o5-card__s { max-width: 36ch; }
.o5-btn { border-radius: 999px 999px 0.75rem 0.75rem; }
.o5-card__links { display: flex; justify-content: center; gap: 2rem; padding-bottom: 1.25rem; }
.o5-card__links a { display: inline-flex; min-height: 2.75rem; align-items: center; font-size: var(--s-small); }
.o5-hero__room { position: relative; border-radius: 50vw 50vw 0 0 / 22vw 22vw 0 0; overflow: hidden; background: var(--ink); }
.o5-hero__room img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; opacity: 0; transition: opacity 1600ms ease; }
.o5-hero__room img:first-child, .o5-hero__room img.on { opacity: 1; }
.js .o5-hero__room img:first-child:not(.on) { opacity: 0; }
.o5-lead { padding: 6rem var(--margin); }
.o5-lead__p { margin-top: 1.5rem; font-size: clamp(1.375rem, 1rem + 1.2vw, 2rem); line-height: 1.25; max-width: 60ch; }
.o5-cols { margin-top: 4.5rem; display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 1.5rem; align-items: start; }
.o5-cols p { font-size: var(--s-small); line-height: 1.5; }
.o5-hist { display: grid; grid-template-columns: repeat(12, minmax(0, 1fr)); gap: var(--gutter); padding: var(--section) var(--margin); background: var(--grey); overflow: hidden; }
.o5-hist__pic { grid-column: 1 / 6; margin-left: calc(-12% - var(--margin)); height: 78svh; border-radius: 999px 999px 0 0; overflow: hidden; }
.o5-hist__pic img { width: 100%; height: 100%; object-fit: cover; }
.o5-hist__r { grid-column: 7 / 13; }
.o5-points { margin-top: 3.5rem; display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 2.75rem 2.5rem; }
.o5-points h3 { font-size: var(--s-body); font-weight: 600; }
.o5-points p { margin-top: 0.4rem; font-size: var(--s-small); }
.o5-points .c1 .arch-mark { color: var(--yellow); } .o5-points .c2 .arch-mark { color: var(--ink); } .o5-points .c3 .arch-mark { color: var(--muted); }
.o5-hist__zh { margin-top: 3rem; }
.o5-menu { padding: var(--section) var(--margin); display: grid; grid-template-columns: repeat(12, minmax(0, 1fr)); gap: var(--gutter); }
.o5-menu__head { grid-column: 1 / 8; display: grid; gap: 0.75rem; }
.o5-menu__m { margin-top: 1rem; font-size: var(--s-lead); font-weight: 500; }
.o5-menu__pic { grid-column: 9 / 13; grid-row: 1; align-self: start; }
.o5-menu__pic img { width: 100%; aspect-ratio: 3 / 4; object-fit: cover; object-position: 50% 20%; border-radius: 999px 999px 0 0; }
.o5-menu__pic figcaption { margin-top: 0.5rem; }
.o5-cat { grid-column: 1 / 13; display: flex; justify-content: space-between; align-items: baseline; margin-top: 6rem; }
.o5-cat__h { font-size: var(--s-title); font-weight: 500; letter-spacing: -0.03em; }
.o5-items { grid-column: 1 / 13; margin-top: 2.5rem; display: grid; grid-template-columns: 1fr 1fr; gap: 2.25rem 4rem; }
.o5-items li { display: grid; grid-template-columns: 4rem 1fr; gap: 1.25rem; }
.o5-bul { width: 4rem; height: 4.75rem; border-radius: 999px 999px 0 0; background: var(--yellow); display: grid; place-items: center; padding-top: 0.6rem; font-family: var(--font-zh); font-weight: 700; font-size: var(--s-body); }
.o5-items li:nth-child(4n+2) .o5-bul, .o5-items li:nth-child(4n+3) .o5-bul { background: var(--ink); color: var(--paper); }
.o5-items__n { display: flex; justify-content: space-between; gap: 1rem; font-size: var(--s-body); }
.o5-items li p + p { margin-top: 0.35rem; }
.o5-panel { margin: 0 20px; border-radius: 12px; background: var(--ink); color: var(--field-ink); min-height: 110svh; display: grid; grid-template-columns: 63fr 37fr; grid-template-rows: auto 1fr; overflow: hidden; }
.o5-panel__head { grid-column: 1; grid-row: 1; padding: 3.5rem 3.5rem 0; }
.o5-panel__face { grid-column: 1; grid-row: 2; align-self: end; margin-left: 3.5rem; }
.o5-panel__r { grid-column: 2; grid-row: 1 / 3; }
.o5-panel .h-zh { color: var(--field-muted); }
.o5-panel__h { font-size: var(--s-head); font-weight: 500; letter-spacing: -0.045em; line-height: 0.95; }
.o5-panel__face { width: 26vw; max-width: 24rem; aspect-ratio: 4 / 5; object-fit: cover; object-position: 50% 15%; border-radius: 999px 999px 0 0; margin-top: 3rem; margin-bottom: -4%; }
.o5-panel__r { padding: 3.5rem 3.5rem 3.5rem 0; display: grid; align-content: start; gap: 4rem; justify-items: start; }
.o5-who { max-width: 22rem; display: grid; gap: 0.5rem; }
.o5-who h3 { font-size: var(--s-lead); font-weight: 500; line-height: 1.2; }
.o5-role { color: var(--field-muted); }
.o5-coupon { padding: 7.5rem var(--margin); }
.o5-cp { position: relative; max-width: 72rem; margin: 0 auto; display: grid; grid-template-columns: 46fr 54fr; background: var(--yellow); border-radius: 14px; }
.o5-cp::before, .o5-cp::after { content: ""; position: absolute; left: calc(46% - 14px); width: 28px; height: 28px; border-radius: 999px; background: var(--paper); }
.o5-cp::before { top: -14px; } .o5-cp::after { bottom: -14px; }
.o5-cp__l { padding: 3rem; display: grid; gap: 1rem; align-content: space-between; }
.o5-cp__l .h-zh { color: var(--ink); margin-top: -0.5rem; }
.o5-cp__h { font-size: var(--s-title); font-weight: 600; text-transform: uppercase; letter-spacing: -0.02em; line-height: 1; }
.o5-cp__r { padding: 3rem; display: grid; gap: 10px; border-left: 5px dotted var(--paper); }
.o5-cp__r input, .o5-cp__r select { min-height: 3.25rem; padding: 0 1rem; border: 0; border-radius: 8px; background: #fff4bf; }
.o5-cp__send { min-height: 3.5rem; border-radius: 999px; background: var(--ink); color: var(--paper); font-weight: 600; transition: transform 140ms var(--ease); } .o5-cp__send:active { transform: scale(0.98); }
.o5-return { position: relative; min-height: 90svh; display: grid; grid-template-columns: repeat(12, minmax(0, 1fr)); gap: var(--gutter); padding: 0 var(--margin); background: var(--grey); overflow: hidden; }
.o5-return__obj { grid-column: 1 / 9; align-self: end; margin-left: calc(var(--margin) * -1); height: 80svh; border-radius: 999px 999px 0 0; overflow: hidden; }
.o5-return__obj video { width: 100%; height: 100%; object-fit: cover; }
.o5-return__t { grid-column: 10 / 13; align-self: center; display: grid; gap: 1rem; justify-items: start; }
.o5-return__h { font-size: var(--s-title); font-weight: 500; letter-spacing: -0.03em; line-height: 1; }
.o5-return__fact { font-size: var(--s-lead); }
.o5-return a:not(.button) { text-decoration: underline; }
@media (max-width: 899px) {
  .o5-hero { grid-template-columns: minmax(0, 1fr); } .o5-hero__room { order: -1; height: 48svh; border-radius: 45vw 45vw 0 0 / 30vw 30vw 0 0; } .o5-card { margin-top: -24px; position: relative; }
  .o5-cols { grid-template-columns: 1fr 1fr; row-gap: 2.5rem; }
  .o5-hist__pic { grid-column: 1 / -1; width: 82vw; height: 60svh; } .o5-hist__r { grid-column: 1 / -1; } .o5-points { grid-template-columns: 1fr; gap: 1.75rem; }
  .o5-menu__head, .o5-menu__pic, .o5-cat, .o5-items { grid-column: 1 / -1; } .o5-menu__pic { grid-row: auto; width: 60%; } .o5-items { grid-template-columns: 1fr; } .o5-cat { margin-top: 3rem; }
  .o5-panel { margin: 0 10px; grid-template-columns: 1fr; grid-template-rows: auto; min-height: 0; } .o5-panel__head, .o5-panel__r, .o5-panel__face { grid-column: 1; grid-row: auto; } .o5-panel__head { padding: 2rem 1.25rem 0; } .o5-panel__r { padding: 2rem 1.25rem; gap: 2rem; } .o5-who { max-width: none; }
  .o5-panel__face { width: 70vw; justify-self: center; margin: 1rem 0 -12%; }
  .o5-cp { grid-template-columns: 1fr; } .o5-cp::before, .o5-cp::after { top: calc(var(--cut, 50%) - 14px); bottom: auto; } .o5-cp::before { left: -14px; } .o5-cp::after { left: auto; right: -14px; }
  .o5-cp__l { padding: 2rem 1.25rem; } .o5-cp__r { padding: 2rem 1.25rem; border-left: 0; border-top: 5px dotted var(--paper); }
  .o5-return { min-height: 0; padding: 4rem var(--margin) 0; } .o5-return__t { grid-column: 1 / -1; order: -1; } .o5-return__obj { grid-column: 1 / -1; width: 130vw; height: 55svh; margin-top: 3rem; }
}
@media (max-width: 520px) { .o5-cols { grid-template-columns: 1fr; } .o5-card__links { flex-wrap: wrap; gap: 0 1.25rem; } }
`};

for (const o of [o1, o2, o3, o4, o5]) writeFileSync(join(ROOT, `about-${o.n}.html`), page(o));
console.log('wrote about-1..5.html');
