// Builds the Sermon Archive mockups sermons-1.html … sermons-5.html and assets/sermons-data.js
// from the read-only WP-CLI export (sermons-export.json, made by export-sermons.php on mpcbc.local).
//   node .taste-skill/sermons/build.mjs        (run from taste-mockup/)
import { readFileSync, writeFileSync } from 'node:fs';
import { createRequire } from 'node:module';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';

const HERE = dirname(fileURLToPath(import.meta.url));
const ROOT = join(HERE, '..', '..');
const require = createRequire(import.meta.url);
const A = require(join(ROOT, 'assets', 'sermons-archive.js'));
const X = JSON.parse(readFileSync(join(HERE, 'sermons-export.json'), 'utf8'));

// ---------- data ----------
const latin = (a) => a.find((s) => /[A-Za-z]/.test(s)) || '';
const aliases = (t) => String(t.meta.aliases || '').split('\n').map((s) => s.trim()).filter(Boolean);
const speakers = X.tax.speaker.filter((t) => t.count > 0).sort((a, b) => b.count - a.count)
  .map((t) => ({ slug: decodeURIComponent(t.slug), name: t.name, aliases: aliases(t), count: t.count, en: /[A-Za-z]/.test(t.name) ? '' : latin(aliases(t)) }));
const seriesT = X.tax.series.filter((t) => t.count > 0)
  .map((t) => ({ slug: decodeURIComponent(t.slug), name: t.name, aliases: aliases(t), count: t.count, en: latin(aliases(t)) }));
const spIdx = new Map(speakers.map((s, i) => [s.name, i]));
const s = X.sermons.map((x) => ({ d: x.date, m: x.ministry, t: x.title, sc: x.scripture || '', sp: x.speaker.map((n) => spIdx.get(n)).filter((i) => i != null), se: x.series.map((n) => seriesT.findIndex((t) => t.name === n)).filter((i) => i >= 0), v: x.video }));
// series facts, newest series first
for (const [i, t] of seriesT.entries()) {
  const its = s.filter((x) => x.se.includes(i));
  t.first = its.at(-1).d; t.last = its[0].d; t.latest = its[0].v;
  t.m = ['cantonese', 'mandarin', 'english'].filter((m) => its.some((x) => x.m.includes(m)));
  t.speakers = [...new Set(its.flatMap((x) => x.sp))].map((k) => speakers[k].name);
}
const order = [...seriesT.keys()].sort((a, b) => seriesT[b].last.localeCompare(seriesT[a].last));
const series = order.map((i) => seriesT[i]);
const remap = new Map(order.map((old, neu) => [old, neu]));
for (const x of s) x.se = x.se.map((i) => remap.get(i));

// Traditional → Simplified, derived from the archive itself: the same sermon preached on one Sunday in
// Cantonese (Traditional title) and Mandarin (Simplified title), compared character by character.
const ts = {}, cjk = /[一-鿿]/;
const byDate = Map.groupBy(s, (x) => x.d);
for (const day of byDate.values()) {
  const c = day.filter((x) => x.m.length === 1 && x.m[0] === 'cantonese'), m = day.filter((x) => x.m.length === 1 && x.m[0] === 'mandarin');
  for (const a of c) for (const b of m) for (const [p, q] of [[a.t, b.t], [a.sc, b.sc]]) {
    if (!p || p.length !== q.length || p === q) continue;
    const pa = [...p], qa = [...q];
    let same = 0; pa.forEach((ch, k) => { if (ch === qa[k]) same++; });
    if (same < pa.length * 0.5) continue; // different sermons
    pa.forEach((ch, k) => { if (ch !== qa[k] && cjk.test(ch) && cjk.test(qa[k])) ts[ch] = qa[k]; });
  }
}
// series and speaker aliases are pairs too (使徒信經 / 使徒信经)
for (const t of [...series, ...speakers]) for (const al of t.aliases) {
  if (al.length !== t.name.length) continue;
  [...t.name].forEach((ch, k) => { const q = [...al][k]; if (q && ch !== q && cjk.test(ch) && cjk.test(q)) ts[ch] = q; });
}
for (const k of Object.keys(ts)) if (ts[ts[k]]) delete ts[k]; // no chains

const D = { s, speakers, series, ts };
writeFileSync(join(ROOT, 'assets', 'sermons-data.js'),
  '/* Real sermon records exported read-only from mpcbc.local (post type sermon), ' + new Date().toISOString().slice(0, 10) + '. Built by .taste-skill/sermons/build.mjs. */\n'
  + 'window.MPCBC_SERMONS=' + JSON.stringify(D) + ';\n');

const latest = {};
for (const m of ['cantonese', 'mandarin', 'english']) latest[m] = s.find((x) => x.m.length === 1 && x.m[0] === m);
const count = s.length, since = s.at(-1).d;
const ctx = { A, D, latest, count, since, esc: A.esc };

// ---------- shared shell ----------
const BASE = readFileSync(join(HERE, 'base.css'), 'utf8');
const nav = [['index.html#sundays', 'Sundays', '主日'], ['sermons', 'Sermons', '講道'], ['index.html#about', 'Pastors', '牧師'], ['index.html#kids', 'Kids', '兒童'], ['index.html#events', 'Events', '活動'], ['index.html#news', 'News', '通訊'], ['index.html#connect', 'Visit', '交通'], ['index.html#give', 'Give', '奉獻']];
const navLinks = (file) => nav.map(([h, en, zh]) => h === 'sermons' ? `<a href="${file}" aria-current="page">${en} <span class="zh" lang="zh-Hant">${zh}</span></a>` : `<a href="${h}">${en} <span class="zh" lang="zh-Hant">${zh}</span></a>`).join('\n    ');
const header = (file) => `<a class="skip" href="#main">Skip to content</a>
<header class="top">
  <a class="wordmark" href="index.html" aria-label="MPCBC home">MPCBC <span class="zh" lang="zh-Hant">慕道園</span></a>
  <nav class="nav" aria-label="Main">
    ${navLinks(file)}
    <a class="zh" href="http://mpcbc.local/sermons/?lang=zh-hant" lang="zh-Hant">繁體</a>
    <a class="zh" href="http://mpcbc.local/sermons/?lang=zh-hans" lang="zh-Hans">简体</a>
  </nav>
  <button class="menu-button" type="button" aria-expanded="false" aria-controls="menu-panel" data-menu>Menu&nbsp;<span class="zh" lang="zh-Hant">選單</span></button>
</header>
<nav class="menu-panel" id="menu-panel" aria-label="Main" hidden>
  ${navLinks(file)}
</nav>`;
const footer = `<footer class="foot" aria-label="Church details">
  <div class="foot__cols">
    <div>
      <h2>Visit <span class="zh" lang="zh-Hant">交通指南</span></h2>
      <p>302 W. Commonwealth Ave.<br>Alhambra, CA 91801</p>
      <p class="times-mini"><span>Sundays <span class="zh" lang="zh-Hant">主日</span></span>Mandarin 9:00 AM<br>Children 10:00 AM<br>Cantonese 11:00 AM<br>English 11:00 AM</p>
    </div>
    <div>
      <h2>Contact <span class="zh" lang="zh-Hant">聯絡</span></h2>
      <ul><li><a href="tel:+16262891284">(626) 289-1284</a></li><li><a href="mailto:info@mpcbc.net">info@mpcbc.net</a></li></ul>
      <p>Office hours: [Days, times]</p>
    </div>
    <div>
      <h2>Watch <span class="zh" lang="zh-Hant">收看</span></h2>
      <ul><li><a href="https://www.youtube.com/@MPCBC">YouTube @MPCBC <span class="zh" lang="zh-Hant">粵語、國語</span></a></li><li><a href="https://www.youtube.com/channel/UCJx8Xl0QqiNkZwwfLXaASHQ">English YouTube</a></li><li><a href="https://www.instagram.com/mpcbconnect/">Instagram @mpcbconnect</a></li></ul>
    </div>
    <div>
      <h2>Explore <span class="zh" lang="zh-Hant">探索</span></h2>
      <ul><li><a href="index.html#sundays">Watch live <span class="zh" lang="zh-Hant">直播</span></a></li><li><a href="index.html#events">Events <span class="zh" lang="zh-Hant">活動</span></a></li><li><a href="index.html#news">Newsletter <span class="zh" lang="zh-Hant">通訊</span></a></li><li><a href="http://mpcbc.local/visit/">Plan your visit</a></li></ul>
    </div>
  </div>
  <div class="foot__bar">
    <p class="foot__id"><b>Monterey Park Chinese Baptist Church</b> <span class="zh" lang="zh-Hant">慕道園華人浸信會</span></p>
    <p>[Church logo] Founded in 1977, Alhambra, California.</p>
    <p class="langs"><a href="http://mpcbc.local/sermons/?lang=zh-hant" lang="zh-Hant">繁體中文</a><a href="http://mpcbc.local/sermons/?lang=zh-hans" lang="zh-Hans">简体中文</a><a href="#top">Back to top</a></p>
  </div>
</footer>`;

const SHARED_JS = `
(() => {
  const menuBtn = document.querySelector('[data-menu]'), menu = document.getElementById('menu-panel');
  const setMenu = (open) => { menu.hidden = !open; menuBtn.setAttribute('aria-expanded', String(open)); };
  menuBtn.addEventListener('click', () => setMenu(menu.hidden));
  addEventListener('keydown', (e) => { if (e.key === 'Escape' && !menu.hidden) { setMenu(false); menuBtn.focus(); } });
  // the archive: re-run the server's query for the URL's parameters (WordPress does this in PHP)
  const A = window.MPCBCArchive, D = window.MPCBC_SERMONS, box = document.getElementById('archive-body');
  const p = A.params(location.search);
  if (box && Object.keys(p).length) {
    box.innerHTML = ARCHIVE(A.query(D, p), p, A, D);
    for (const i of document.querySelectorAll('.hs input[name=q]')) i.value = p.q || '';
  }
  A.enhance(document);
  // entrances: each part of a section rises once as it enters; content stays visible without script
  const els = document.querySelectorAll('[data-in], [data-grow]');
  if ('IntersectionObserver' in window && !matchMedia('(prefers-reduced-motion: reduce)').matches) {
    document.documentElement.classList.add('io');
    const io = new IntersectionObserver((es) => { for (const e of es) if (e.isIntersecting) { e.target.classList.add('is-in'); io.unobserve(e.target); } }, { rootMargin: '0px 0px -8% 0px' });
    els.forEach((el) => io.observe(el));
  }
})();`;

function page(o) {
  const res = A.query(D, {});
  const archive = o.archive(res, {}, A, D);
  return `<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>${o.title}</title>
<meta name="description" content="${o.desc}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;500&family=Noto+Sans+TC:wght@400;500&display=swap" rel="stylesheet">
<style>
${BASE}
/* ================= ${o.name} ================= */
${o.css}
</style>
</head>
<body class="${o.bodyClass || ''}">
${header(o.file)}
<main id="main">
${o.body({ ...ctx, archive })}
</main>
${footer}
<script src="assets/sermons-data.js"></script>
<script src="assets/sermons-archive.js"></script>
<script>
const ARCHIVE = ${o.archive.toString()};
${o.js || ''}
${SHARED_JS}
</script>
</body>
</html>
`;
}

const which = process.argv[2];
for (const n of [1, 2, 3, 4, 5]) {
  if (which && String(n) !== which) continue;
  let mod; try { mod = await import(`./o${n}.mjs?${Date.now()}`); } catch (e) { if (e.code === 'ERR_MODULE_NOT_FOUND') continue; throw e; }
  const o = mod.default(ctx);
  writeFileSync(join(ROOT, o.file), page(o));
  console.log('wrote', o.file);
}
console.log(`${count} sermons, ${speakers.length} speakers, ${series.length} series, ${Object.keys(ts).length} Traditional→Simplified pairs`);
