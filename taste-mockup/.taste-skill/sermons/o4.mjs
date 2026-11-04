// Option 4 — Prints on the wall (direction: uneven-photographs, seed 1597152153)
export default ({ A, D, latest, count, esc }) => {
  const L = latest;
  const scatter = ['重塑生命', '希伯來書', '使徒信經', '傳道書系列', '約翰福音', '生命中九個重要的問題'].map((n) => D.series.find((t) => t.name === n)).filter(Boolean);
  const MIN = { cantonese: 'Cantonese', mandarin: 'Mandarin', english: 'English' };
  const span = (t) => { const f = A.date(t.first).replace(/ \d+,/, ''), l = A.date(t.last).replace(/ \d+,/, ''); return f === l ? f : `${f} to ${l}`; };
  const cap = (x, m) => `<span class="o4-cap"><span>${MIN[m]} <span class="zh" lang="zh-Hant">${{ cantonese: '粵語', mandarin: '國語', english: '英語' }[m]}</span> · ${A.date(x.d)}</span> <b lang="${A.lang(x)}">${esc(x.t)}</b></span>`;
  return {
    file: 'sermons-4.html', name: 'Option 4 · Prints on the wall',
    title: 'Sermons 講道 · MPCBC',
    desc: 'Every recorded Sunday sermon from MPCBC’s Cantonese, Mandarin and English congregations, searchable by title, speaker and series.',
    body: ({ archive }) => `
<section class="o4-hero" id="top" aria-labelledby="o4-title">
  <a class="o4-print o4-print--small" data-in href="${A.watch(L.mandarin.v)}" target="_blank" rel="noopener"><img src="${A.thumb(L.mandarin.v, 'maxresdefault')}" width="1280" height="720" alt="Title card: ${esc(L.mandarin.t)}">${A.newTab}</a>
  <div class="o4-hero__text">
    <h1 id="o4-title">Sermons <span class="zh" lang="zh-Hant">講道</span></h1>
    <p>The newest from each congregation, and all ${count} sermons since April 2021 below.</p>
    ${A.searchBox({}, { id: 'o4-q' })}
    <p class="o4-hero__small">${cap(L.mandarin, 'mandarin')}</p>
  </div>
  <figure class="o4-print o4-print--tall" data-shutter><a href="${A.watch(L.cantonese.v)}" target="_blank" rel="noopener"><span class="o4-shutter"><img src="${A.thumb(L.cantonese.v, 'maxresdefault')}" width="1280" height="720" alt="Title card: ${esc(L.cantonese.t)}"></span>${A.newTab}</a><figcaption>${cap(L.cantonese, 'cantonese')}</figcaption></figure>
  <figure class="o4-print o4-print--wide" data-in style="--i:2"><a href="${A.watch(L.english.v)}" target="_blank" rel="noopener"><img src="${A.thumb(L.english.v, 'maxresdefault')}" width="1280" height="720" alt="Title card: ${esc(L.english.t)}">${A.newTab}</a><figcaption>${cap(L.english, 'english')}</figcaption></figure>
</section>

<section class="o4-series" id="series" aria-labelledby="o4-series-title">
  <h2 id="o4-series-title" data-in>Series <span class="zh" lang="zh-Hant">系列</span></h2>
  <ol class="o4-scatter">
    ${scatter.map((t, i) => `<li class="o4-piece o4-piece--${'abc'[i % 3]}" data-in><a href="${esc(A.href({}, { series: t.slug }))}"><span class="o4-piece__name" lang="zh-Hant">${esc(t.name)}${t.en ? ` <span lang="en">· ${esc(t.en)}</span>` : ''}</span><img src="${A.thumb(t.latest, 'maxresdefault')}" width="1280" height="720" alt="" loading="lazy"><span class="o4-piece__desc">${t.count} sermons · ${t.m.map((m) => MIN[m]).join(', ')} · ${span(t)} · ${t.speakers.map(esc).join(', ')}</span></a></li>${i === 1 ? `<li class="o4-remark" data-in><p>重塑生命 is preached in all three congregations; in English it is called Renovation of the Heart.</p></li>` : ''}`).join('\n    ')}
  </ol>
  <p class="o4-series__more" data-in>${D.series.length - scatter.length} more series, back to 2021, are in the series filter below.</p>
</section>

<section class="o4-list" id="archive" aria-labelledby="o4-list-title">
  <h2 class="o4-statement" id="o4-list-title" data-in><span>Every sermon, newest first</span> <span class="zh" lang="zh-Hant">所有講道，由新至舊</span></h2>
  <div id="archive-body" class="o4-list__body">${archive}</div>
</section>`,

    // [mpcbc_sermons] output: the form on the left, the rows pushed into the right five columns
    archive: function (res, p, A, D) {
      const e = A.esc;
      const rows = A.sundays(res.items).map((g) => `<li class="o4-sunday"><ul>${g.items.map((x) => `<li data-in><a class="o4-row" href="${A.watch(x.v)}" target="_blank" rel="noopener"><span class="o4-row__date">${A.date(x.d)}</span><img class="thumb" src="${A.thumb(x.v)}" width="320" height="180" alt="" loading="lazy"><span class="o4-row__main"><b lang="${A.lang(x)}">${e(x.t)}</b><span>${[A.speaker(x, D), x.sc, A.series(x, D)].filter(Boolean).map(e).join(' · ')}</span></span><span class="o4-row__min">${A.min(x)} <span aria-hidden="true">↗</span></span>${A.newTab}</a></li>`).join('')}</ul></li>`).join('');
      return `<div class="o4-side">${A.form(p, D, { id: 'o4' })}</div>
<p class="o4-word" aria-hidden="true">Sundays</p>
<div class="o4-table"><div class="o4-sum">${A.summary(res, p, D)}</div>${res.total ? `<ol class="o4-rows">${rows}</ol>` : '<p class="empty">Try another spelling, or <a href="?#archive">clear the filters</a>.</p>'}<div class="o4-pager">${A.pager(res, p)}</div></div>`;
    },

    css: `
.o4-hero { display: grid; grid-template-columns: repeat(12, minmax(0, 1fr)); column-gap: var(--gutter); align-items: start; padding: 18svh 0 var(--section) var(--margin); overflow: hidden; }
.o4-print img { width: 100%; object-fit: cover; }
.o4-print--small { grid-column: 1 / 3; grid-row: 1; }
.o4-print--small img { aspect-ratio: 1; object-position: 50% 60%; }
.o4-hero__text { grid-column: 1 / 4; grid-row: 2; padding-top: 1.5rem; display: grid; gap: 1rem; }
.o4-hero__text h1 { font-size: clamp(1.875rem, 2.8vw, 2.75rem); font-weight: 500; line-height: 1.05; letter-spacing: -0.03em; }
.o4-hero__text h1 .zh { display: block; }
.o4-hero__text > p { font-size: 1.125rem; max-width: 30ch; }
.o4-hero__text .hs__row { flex-direction: column; align-items: stretch; }
.o4-hero__text .hs input { width: 100%; flex: none; }
.o4-hero__text .hs .button { align-self: flex-start; }
.o4-hero__small { display: none; }
.o4-print--tall { grid-column: 4 / 10; grid-row: 1 / 3; }
.o4-print--tall img { aspect-ratio: 1; }
.o4-print--wide { grid-column: 11 / -1; grid-row: 1 / 3; margin-right: calc(var(--margin) * -1 - 12vw); }
.o4-print--wide img { aspect-ratio: 16 / 9; }
.o4-print a { display: block; }
.o4-print figcaption { margin-top: 0.75rem; }
.o4-print--wide figcaption { padding-right: calc(12vw + var(--margin) * 2); }
.o4-cap { display: grid; gap: 0.15rem; font-size: var(--t-small); }
.o4-cap > span { color: var(--muted); }
.o4-cap b { font-size: var(--t-body); font-weight: 500; }
.o4-print img, .o4-piece img { transition: transform 600ms var(--ease-out); }
.o4-print a, .o4-piece a { overflow: hidden; }
@media (hover: hover) { .o4-print a:hover img, .o4-piece a:hover img { transform: scale(1.03); } }
.o4-shutter { display: block; overflow: hidden; }
.io .o4-print--tall .o4-shutter { animation: o4-shut 1100ms var(--ease-out) both; }
.io .o4-print--tall .o4-shutter img { animation: o4-settle 1400ms var(--ease-out) both; }
@keyframes o4-shut { from { clip-path: inset(0 0 100% 0); } to { clip-path: inset(0); } }
@keyframes o4-settle { from { transform: scale(1.12); } to { transform: none; } }

.o4-series { padding: var(--section) var(--margin); }
.o4-series h2 { font-size: clamp(2rem, 2.8vw, 2.5rem); font-weight: 500; letter-spacing: -0.03em; line-height: 1; margin-bottom: 4rem; }
.o4-series h2 .zh { margin-left: 0.35em; }
.o4-scatter { display: grid; grid-template-columns: repeat(12, minmax(0, 1fr)); column-gap: var(--gutter); grid-auto-flow: row dense; }
.o4-piece a { display: block; text-decoration: none; }
.o4-piece__name { display: block; font-size: var(--t-small); color: var(--muted); margin-bottom: 0.6rem; }
.o4-piece__desc { display: block; margin-top: 0.75rem; font-size: var(--t-body); max-width: 52ch; }
.o4-piece--a { grid-column: 1 / 7; margin-top: 24vh; }
.o4-piece--a img { aspect-ratio: 3 / 2; }
.o4-piece--b { grid-column: 9 / -1; margin-top: 6rem; }
.o4-piece--b img { aspect-ratio: 4 / 5; object-position: 50% 55%; }
.o4-piece--c { grid-column: 3 / 10; margin-top: 26vh; }
.o4-piece--c img { aspect-ratio: 16 / 9; }
.o4-scatter > li:first-child { margin-top: 0; }
.o4-remark { grid-column: 2 / 7; align-self: center; margin-top: 6rem; }
.o4-remark p { font-size: var(--t-title); line-height: 1.3; max-width: 26ch; }
.o4-series__more { margin-top: 5rem; max-width: 60ch; }

.o4-list { padding: var(--section) var(--margin); background: var(--paper); }
.o4-statement { font-size: clamp(2.5rem, 4.5vw, 4.25rem); font-weight: 500; line-height: 0.98; letter-spacing: -0.04em; }
.o4-statement > span:first-child { display: block; padding-left: 3vw; }
.o4-statement .zh { display: block; letter-spacing: 0; }
.o4-list__body { display: grid; grid-template-columns: repeat(12, minmax(0, 1fr)); column-gap: var(--gutter); margin-top: 6rem; align-items: start; }
.o4-side { grid-column: 1 / 6; position: sticky; top: 5.5rem; padding: 2rem; background: var(--bg); }
.o4-side .ar-form, .o4-side .more__body { display: grid; gap: 1.25rem; }
.o4-word { grid-column: 6 / 8; justify-self: end; padding-right: 0.5rem; font-size: var(--t-small); color: var(--muted); }
.o4-table { grid-column: 8 / -1; }
.o4-sum { margin-bottom: 1.5rem; }
.o4-sunday + .o4-sunday { margin-top: 2.75rem; }
.o4-sunday ul { display: grid; gap: 1.25rem; }
.o4-row { display: grid; grid-template-columns: 5.75rem 6rem 1fr auto; gap: 1rem; align-items: start; text-decoration: none; font-size: 1rem; }
.o4-row__date { color: var(--muted); font-variant-numeric: tabular-nums; }
.o4-row__main { display: grid; gap: 0.15rem; }
.o4-row__main b { font-weight: 500; line-height: 1.3; }
.o4-row__main span { color: var(--muted); font-size: var(--t-small); }
.o4-row__min { text-align: right; white-space: nowrap; }
.o4-row__min span { display: inline-block; transition: transform 200ms var(--ease-out); }
@media (hover: hover) { .o4-row:hover b { text-decoration: underline; } .o4-row:hover .o4-row__min span { transform: translate(3px, -3px); } }
.o4-pager { margin-top: 3rem; }

@media (max-width: 1099px) {
  .o4-hero { padding-top: 10svh; }
  .o4-print--small { grid-column: 1 / 4; }
  .o4-hero__text { grid-column: 1 / 5; }
  .o4-print--tall { grid-column: 5 / 10; }
  .o4-print--wide { grid-column: 10 / -1; }
  .o4-side { grid-column: 1 / -1; position: static; margin-bottom: 2rem; }
  .o4-word { display: none; }
  .o4-table { grid-column: 1 / -1; }
}
@media (max-width: 699px) {
  .o4-hero { display: block; padding: 2rem var(--margin) 4rem; }
  .o4-print--tall { width: 72%; }
  .o4-print--small { display: block; width: 38%; margin: -40px 0 0 auto; position: relative; }
  .o4-print--wide { margin: 2.5rem calc(var(--margin) * -1) 0; }
  .o4-print--wide figcaption { padding: 0 var(--margin); }
  .o4-hero__text { padding-top: 0; margin-top: -4.5rem; width: 58%; }
  .o4-hero__text h1 { font-size: 8vw; }
  .o4-hero__text .hs { width: calc(100vw - 2 * var(--margin)); }
  .o4-hero__text > p { margin-top: 1.25rem; width: calc(100vw - 2 * var(--margin)); max-width: none; }
  .o4-hero__small { display: block; }
  .o4-scatter { display: block; }
  .o4-piece { margin-top: 72px !important; }
  .o4-piece--b { width: 68%; margin-left: auto; }
  .o4-piece--c { width: 82%; }
  .o4-remark { margin: 72px 0 0; }
  .o4-statement { font-size: 9.5vw; }
  .o4-statement > span:first-child { padding-left: 0; }
  .o4-list__body { margin-top: 3rem; }
  .o4-side { padding: 1.25rem; }
  .o4-row { grid-template-columns: 5.5rem 1fr; }
  .o4-row__date { grid-column: 1 / -1; display: flex; justify-content: space-between; }
  .o4-row__min { grid-column: 2; text-align: left; }
}
`,
  };
};
