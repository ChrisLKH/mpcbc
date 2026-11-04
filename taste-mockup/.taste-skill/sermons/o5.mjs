// Option 5 — The counter (direction: borrowed-category, seed 610105364; borrowed from a watchmaker's counter)
export default ({ A, D, latest, count, esc }) => {
  const L = latest;
  const lat = [['cantonese', 'Cantonese', '粵語'], ['mandarin', 'Mandarin', '國語'], ['english', 'English', '英語']].map(([m, en, zh]) => ({ m, en, zh, x: L[m], n: D.s.filter((s) => s.m.includes(m)).length }));
  const names = D.series.filter((t) => t.count >= 6).slice(0, 8);
  const MIN = { cantonese: 'Cantonese', mandarin: 'Mandarin', english: 'English' };
  const yr = (t) => { const f = A.date(t.first).replace(/ d+,/, ''), l = A.date(t.last).replace(/ d+,/, ''); return f === l ? f : `${f} to ${l}`; };
  const word = 'SERMONS';
  const swap = names.map((_, i) => `.o5-series__body:has(.o5-names li:nth-child(${i + 1}) a:is(:hover, :focus-visible)) .o5-frame img:nth-child(${i + 1}) { opacity: 1; } .o5-names:has(li:nth-child(${i + 1}) a:is(:hover, :focus-visible)) li:nth-child(${i + 1}) a { color: var(--paper); }`).join('\n');
  return {
    file: 'sermons-5.html', name: 'Option 5 · The counter',
    title: 'Sermons 講道 · MPCBC',
    desc: 'Every recorded Sunday sermon from MPCBC’s Cantonese, Mandarin and English congregations, searchable by title, speaker and series.',
    body: ({ archive }) => `
<section class="o5-hero" id="top" aria-labelledby="o5-title">
  <div class="o5-object" aria-label="The latest title card from each congregation">
    ${lat.map((l, i) => `<a href="${A.watch(l.x.v)}" target="_blank" rel="noopener" style="--k:${i}"><img src="${A.thumb(l.x.v, 'maxresdefault')}" width="1280" height="720" alt="Title card: ${esc(l.x.t)}, ${l.en}"${i ? ' loading="lazy"' : ''}>${A.newTab}</a>`).join('')}
  </div>
  <h1 class="o5-wordmark" id="o5-title" aria-label="Sermons 講道">${[...word].map((c) => `<span aria-hidden="true">${c}</span>`).join('')}<span aria-hidden="true" class="zh" lang="zh-Hant">講</span><span aria-hidden="true" class="zh" lang="zh-Hant">道</span></h1>
  <p class="o5-say">Three congregations, one archive since 2021.</p>
  <div class="o5-row">
    <p>${count} sermons</p>
    ${A.searchBox({}, { id: 'o5-q', button: 'button--accent' })}
    <p>粵語 · 國語 · English</p>
  </div>
</section>

<section class="o5-show" id="latest" aria-labelledby="o5-show-title">
  <div class="o5-stack">
    <p aria-hidden="true">This Sunday</p>
    <h2 id="o5-show-title">Latest sermons</h2>
    <p aria-hidden="true" lang="zh-Hant">各堂最新講道</p>
  </div>
  <a class="o5-show__obj" href="${A.watch(L.mandarin.v)}" target="_blank" rel="noopener" data-in><img src="${A.thumb(L.mandarin.v, 'maxresdefault')}" width="1280" height="720" alt="Title card: ${esc(L.mandarin.t)}" loading="lazy">${A.newTab}</a>
  <dl class="o5-facts">
    ${lat.map((l, i) => `<div data-in style="--i:${i}"><dt>${l.en} <span class="zh" lang="zh-Hant">${l.zh}</span> · ${A.date(l.x.d)}</dt><dd><a href="${A.watch(l.x.v)}" target="_blank" rel="noopener" lang="${A.lang(l.x)}">${esc(l.x.t)}${A.newTab}</a> ${[A.speaker(l.x, D), l.x.sc].filter(Boolean).map(esc).join(', ')}. <a class="o5-more" href="${A.href({}, { ministry: l.m })}">All ${l.n} in ${l.en}</a></dd></div>`).join('')}
  </dl>
</section>

<section class="o5-series" id="series" aria-labelledby="o5-series-title">
  <h2 id="o5-series-title" data-in>Sermon series <span class="zh" lang="zh-Hant">講道系列</span></h2>
  <div class="o5-series__body">
    <ul class="o5-names">
      ${names.map((t, i) => `<li data-in style="--i:${i}"><a href="${esc(A.href({}, { series: t.slug }))}"><span class="o5-names__n" lang="zh-Hant">${esc(t.name)}</span><span class="o5-names__meta">${t.en ? esc(t.en) + ' · ' : ''}${t.m.map((m) => MIN[m]).join(', ')} · ${yr(t)} · ${t.count} sermons · ${t.speakers.map(esc).join(', ')}</span></a></li>`).join('\n      ')}
    </ul>
    <div class="o5-frame" aria-hidden="true">${names.map((t) => `<img src="${A.thumb(t.latest, 'maxresdefault')}" width="1280" height="720" alt="" loading="lazy">`).join('')}</div>
  </div>
</section>

<section class="o5-list" id="archive" aria-labelledby="o5-list-title">
  <div class="o5-list__head">
    <h2 id="o5-list-title"><span>Every sermon</span> <span class="zh" lang="zh-Hant">所有講道</span></h2>
    <a class="button" href="index.html#sundays">Watch this Sunday live <span class="zh" lang="zh-Hant">直播</span></a>
  </div>
  <div id="archive-body" class="o5-list__body">${archive}</div>
</section>`,

    // [mpcbc_sermons] output: congregation tabs on the left, the sermons as banded rows on the right
    archive: function (res, p, A, D) {
      const e = A.esc;
      const tabs = [['', 'All', '全部', D.s.length]].concat(Object.entries(A.MIN).map(([k, v]) => [k, v[0], v[1], D.s.filter((s) => s.m.includes(k)).length]))
        .map(([k, en, zh, n]) => `<li><a class="o5-tab" href="${e(A.href(p, { ministry: k, pg: 0 }))}"${(p.ministry || '') === k ? ' aria-current="true"' : ''}><b>${en} <span class="zh" lang="zh-Hant">${zh}</span></b><span>${n} sermons</span></a></li>`).join('');
      const bands = res.items.map((x) => `<li><a class="o5-band" href="${A.watch(x.v)}" target="_blank" rel="noopener"><span class="o5-band__date">${A.date(x.d)}</span><span class="o5-band__main"><b lang="${A.lang(x)}">${e(x.t)}</b><span>${[A.min(x), x.sc, A.series(x, D)].filter(Boolean).map(e).join(' · ')}</span></span><span class="o5-band__by">${e(A.speaker(x, D))}</span><img class="thumb" src="${A.thumb(x.v)}" width="320" height="180" alt="" loading="lazy">${A.newTab}</a></li>`).join('');
      return `<nav class="o5-tabs" aria-label="Congregation"><ul>${tabs}</ul></nav>
<div class="o5-main">${A.form(p, D, { id: 'o5', ministry: 'hidden' })}<div class="o5-sum">${A.summary(res, p, D)}</div>${res.total ? `<ul class="o5-bands">${bands}</ul>` : '<p class="empty">Try another spelling, or <a href="?#archive">clear the filters</a>.</p>'}<div class="o5-pager">${A.pager(res, p)}</div></div>`;
    },

    css: `
.o5-hero { min-height: calc(100svh - 4.25rem); display: grid; justify-items: center; align-content: center; gap: 2rem; padding: 2.5rem var(--margin) 3rem; gap: 1.5rem; background: var(--field); color: var(--field-ink); text-align: center; }
.o5-object { position: relative; width: min(60vw, 100svh); aspect-ratio: 16 / 9; }
.o5-object a { position: absolute; inset: 0; animation: o5-fade 18s linear infinite; animation-delay: calc(var(--k) * 6s); opacity: 0; }
.o5-object a:first-child { opacity: 1; }
.o5-object img { width: 100%; height: 100%; object-fit: cover; filter: brightness(0.9); }
@keyframes o5-fade { 0% { opacity: 0; } 9% { opacity: 1; } 33% { opacity: 1; } 42% { opacity: 0; } 100% { opacity: 0; } }
.o5-object a:hover img { filter: none; }
.o5-wordmark { width: min(72vw, 64rem); display: flex; justify-content: space-between; font-size: clamp(1.75rem, 3vw, 2.75rem); font-weight: 400; line-height: 1; }
.o5-wordmark .zh { font-weight: 400; }
.o5-say { font-size: clamp(1.5rem, 2.4vw, 2.25rem); line-height: 1.2; letter-spacing: -0.02em; }
.o5-row { display: flex; align-items: center; justify-content: center; gap: 3rem; font-size: var(--t-small); color: var(--field-muted); }
.o5-row .hs { width: min(28rem, 40vw); }
.o5-row p { white-space: nowrap; }
@media (prefers-reduced-motion: reduce) { .o5-object a { animation: none; opacity: 1; } .o5-object a:not(:first-child) { display: none; } }

.o5-show { position: relative; display: grid; grid-template-columns: repeat(12, minmax(0, 1fr)); column-gap: var(--gutter); padding: var(--section) var(--margin); overflow: hidden; }
.o5-stack { grid-column: 1 / 8; }
.o5-stack > * { font-size: clamp(4.5rem, 8vw, 8.5rem); font-weight: 500; letter-spacing: -0.05em; line-height: 1.04; white-space: nowrap; color: var(--muted); }
.o5-stack h2 { color: var(--ink); }
.o5-stack p[lang] { font-family: var(--font-zh); letter-spacing: 0; line-height: 1.05; }
.o5-show__obj { grid-column: 1 / 6; grid-row: 2; align-self: start; display: block; transform: rotate(-3deg); margin-top: 4.5rem; }
.o5-show__obj img { width: 100%; aspect-ratio: 16 / 9; object-fit: cover; }
.o5-show__obj:hover { transform: rotate(2deg); transition: transform 300ms var(--ease-out); }
.o5-facts { grid-column: 7 / -1; grid-row: 2; margin-top: 4rem; display: grid; gap: 1.75rem; }
.o5-facts dt { font-size: 1rem; color: var(--muted); }
.o5-facts dd { font-size: 1.25rem; line-height: 1.4; margin-top: 0.25rem; max-width: 46ch; }
.o5-facts dd > a:first-child { font-weight: 500; }
.o5-more { font-size: 1rem; white-space: nowrap; }

.o5-series { padding: var(--section) var(--margin); background: var(--field); color: var(--field-ink); }
.o5-series h2 { font-size: 1.75rem; font-weight: 500; margin-bottom: 3rem; }
.o5-series h2 .zh { margin-left: 0.35em; }
.o5-series__body { display: grid; grid-template-columns: repeat(12, minmax(0, 1fr)); column-gap: var(--gutter); align-items: start; }
.o5-names { grid-column: 1 / 9; }
.o5-names a { display: flex; flex-wrap: nowrap; align-items: baseline; gap: 1.5rem; padding: 0.6rem 0; text-decoration: none; color: var(--field-muted); transition: color 160ms ease; }
.o5-names li:first-child a { color: var(--paper); }
.o5-names:has(a:is(:hover, :focus-visible)) li:first-child a { color: var(--field-muted); }
.o5-names__n { flex: none; font-size: clamp(2.75rem, 5.4vw, 5rem); font-weight: 500; line-height: 1; }
.o5-names__meta { font-size: var(--t-small); }
.o5-frame { grid-column: 9 / -1; position: sticky; top: 15vh; aspect-ratio: 4 / 5; }
.o5-frame img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; opacity: 0; transition: opacity 200ms ease; }
.o5-frame img:first-child { opacity: 1; }
.o5-series__body:has(.o5-names a:is(:hover, :focus-visible)) .o5-frame img:first-child { opacity: 0; }
${swap}

.o5-list { padding: var(--section) var(--margin); }
.o5-list__head { display: flex; justify-content: space-between; align-items: flex-end; gap: 2rem; margin-bottom: 3.5rem; }
.o5-list__head h2 { font-size: clamp(2.25rem, 4vw, 3.75rem); font-weight: 500; letter-spacing: -0.04em; line-height: 1; }
.o5-list__head h2 > span:first-child { display: block; color: var(--muted); }
.o5-list__head h2 .zh { display: block; letter-spacing: 0; }
.o5-list__body { display: grid; grid-template-columns: 10rem 1fr; gap: 2.5rem; align-items: start; }
.o5-tabs ul { display: grid; gap: 6px; position: sticky; top: 5.5rem; }
.o5-tab { display: grid; align-content: center; height: 4.5rem; padding: 0 1rem; background: var(--surface); text-decoration: none; transition: background-color 160ms ease; }
.o5-tab b { font-weight: 500; font-size: 1rem; }
.o5-tab span { font-size: var(--t-small); }
.o5-tab[aria-current] { background: var(--ink); color: var(--paper); }
@media (hover: hover) { .o5-tab:not([aria-current]):hover { background: var(--accent); } }
.o5-main .ar-form { display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 1rem; align-items: end; }
.o5-main .more { display: contents; }
.o5-main .more__body { display: contents; }
.o5-sum { margin: 1.5rem 0; }
.o5-bands { display: grid; gap: 4px; }
.o5-band { display: grid; grid-template-columns: 9rem 1fr 13rem 6rem; gap: 1.25rem; align-items: center; min-height: 4.75rem; padding: 0.6rem 0.75rem 0.6rem 1.25rem; border-radius: 8px; background: var(--paper); text-decoration: none; transition: background-color 160ms ease; }
.o5-bands li:nth-child(even) .o5-band { background: #f3f3ef; }
.o5-band__date { font-size: 1.25rem; font-variant-numeric: tabular-nums; }
.o5-band__main { display: grid; gap: 0.15rem; }
.o5-band__main b { font-size: 1.125rem; font-weight: 500; line-height: 1.3; }
.o5-band__main span { font-size: var(--t-small); color: var(--muted); }
.o5-band__by { font-size: var(--t-small); text-align: right; }
.o5-band img { border-radius: 4px; }
@media (hover: hover) { .o5-bands li .o5-band:hover { background: var(--accent); } }
.o5-pager { margin-top: 3rem; }
`,
  };
};
