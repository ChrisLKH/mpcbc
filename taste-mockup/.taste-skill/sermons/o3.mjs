// Option 3 — Set in type (direction: type-only-statement, seed 1478049412)
export default ({ A, D, latest, count, esc }) => {
  const lat = [['cantonese', 'Cantonese', '粵語'], ['mandarin', 'Mandarin', '國語'], ['english', 'English', '英語']].map(([m, en, zh]) => ({ m, en, zh, x: latest[m] }));
  const caleb = D.speakers.find((s) => s.name === '張健庭');
  const span = `${A.dateLong(D.s.at(-1).d).replace(/ \d+,/, '')} to ${A.dateLong(D.s[0].d).replace(/ \d+,/, '')}`;
  return {
    file: 'sermons-3.html', name: 'Option 3 · Set in type',
    title: 'Sermons 講道 · MPCBC',
    desc: 'Every recorded Sunday sermon from MPCBC’s Cantonese, Mandarin and English congregations, searchable by title, speaker and series.',
    body: ({ archive }) => `
<section class="o3-hero" id="top" aria-labelledby="o3-title">
  <div class="o3-hero__top">
    <div class="o3-hero__say">
      <p class="o3-lead"><span class="zh" lang="zh-Hant">講道</span> Every Sunday sermon from our Cantonese, Mandarin and English congregations: ${count} recordings since April 2021.</p>
      ${A.searchBox({}, { id: 'o3-q', button: 'button--accent' })}
    </div>
    <ul class="o3-latest" aria-label="On this page">
      <li><a href="#latest"><span>This week 本週</span><b>Latest sermons</b></a></li>
      <li><a href="#series"><span>${D.series.length} series 系列</span><b>Sermon series</b></a></li>
      <li><a href="#archive"><span>${count} sermons 講道</span><b>Search the archive</b></a></li>
    </ul>
  </div>
  <h1 class="o3-word" id="o3-title"><span aria-hidden="true">Sermons</span><span class="sr-only">Sermons 講道</span></h1>
</section>

<section class="o3-relief" id="latest" aria-labelledby="o3-relief-title">
  <h2 id="o3-relief-title" data-in>Latest sermons <span class="zh" lang="zh-Hant">最新講道</span></h2>
  <ul class="o3-relief__row">
    ${lat.map((l, i) => `<li data-in style="--i:${i + 1}"><a href="${A.watch(l.x.v)}" target="_blank" rel="noopener"><img class="thumb" src="${A.thumb(l.x.v, 'maxresdefault')}" width="1280" height="720" alt="Title card: ${esc(l.x.t)}" loading="lazy"><span class="o3-relief__min">${l.en} <span class="zh" lang="zh-Hant">${l.zh}</span> · ${A.date(l.x.d)}</span><b lang="${A.lang(l.x)}">${esc(l.x.t)}</b><span class="o3-relief__by">${[A.speaker(l.x, D), l.x.sc].filter(Boolean).map(esc).join(' · ')}</span>${A.newTab}</a></li>`).join('')}
  </ul>
</section>

<section class="o3-scripts" id="search-help" aria-labelledby="o3-scripts-title">
  <div class="o3-scripts__head">
    <h2 id="o3-scripts-title" data-in>One search, both scripts <span class="zh" lang="zh-Hant">繁簡通用</span></h2>
    <p data-in style="--i:1">Cantonese titles are written in Traditional characters, Mandarin titles in Simplified, English titles in English. Search in any of them: 張健庭, 张健庭 and Rev. Dr. Caleb Cheung find the same ${caleb.count} sermons.</p>
  </div>
  <div class="o3-spec">
    <figure><figcaption><span>希伯來書, from a Cantonese title: Traditional characters</span><a href="${A.watch(latest.cantonese.v)}" target="_blank" rel="noopener"><img class="thumb" src="${A.thumb(latest.cantonese.v, 'hqdefault')}" width="480" height="360" alt="Title card of ${esc(latest.cantonese.t)}, ${esc(latest.cantonese.sc)}" loading="lazy"></a></figcaption><p class="o3-spec__glyph" lang="zh-Hant" aria-hidden="true">書</p></figure>
    <figure><figcaption><span>雅各书, from a Mandarin title: Simplified characters</span><a href="${A.watch(latest.mandarin.v)}" target="_blank" rel="noopener"><img class="thumb" src="${A.thumb(latest.mandarin.v, 'hqdefault')}" width="480" height="360" alt="Title card of ${esc(latest.mandarin.t)}, ${esc(latest.mandarin.sc)}" loading="lazy"></a></figcaption><p class="o3-spec__glyph" lang="zh-Hans" aria-hidden="true">书</p></figure>
  </div>
</section>

<section class="o3-series" id="series" aria-labelledby="o3-series-title">
  <p class="o3-para"><span class="o3-para__head" id="o3-series-title" role="heading" aria-level="2">Sermon series:</span> ${D.series.map((t, i) => `<a href="${esc(A.href({}, { series: t.slug }))}" lang="zh-Hant">${esc(t.name)}${i < 8 ? `<span class="o3-frame"><img src="${A.thumb(t.latest)}" width="320" height="180" alt="" loading="lazy"></span>` : ''}</a>`).join('<span class="o3-comma">, </span>')}<span class="o3-comma">.</span></p>
  <p class="o3-series__note" data-in>${D.series.length} series, ${span}. English series names: ${D.series.filter((t) => t.en).map((t) => `${esc(t.name)} is ${esc(t.en)}`).join('; ')}.</p>
</section>

<section class="o3-list" id="archive" aria-labelledby="o3-list-title">
  <h2 class="sr-only" id="o3-list-title">All sermons 所有講道</h2>
  <div id="archive-body">${archive}</div>
</section>`,

    // [mpcbc_sermons] output: the search as one line of controls, then a line-up at one type size
    archive: function (res, p, A, D) {
      const e = A.esc;
      const days = A.sundays(res.items).map((g) => `<tbody><tr class="o3-day"><th colspan="5" scope="colgroup">Sunday, ${A.dateLong(g.date)} <span class="zh" lang="zh-Hant">主日</span></th></tr>${g.items.map((x) => `<tr class="o3-row" data-in><td class="o3-row__pic"><img class="thumb" src="${A.thumb(x.v)}" width="320" height="180" alt="" loading="lazy"></td><td class="o3-row__title"><a href="${A.watch(x.v)}" target="_blank" rel="noopener" lang="${A.lang(x)}">${e(x.t)}${A.newTab}</a><span class="o3-row__sub">${[x.sc, A.series(x, D)].filter(Boolean).map(e).join(' · ') || '—'}</span></td><td>${e(A.speaker(x, D))}</td><td>${A.min(x)}</td><td class="o3-row__date">${A.date(x.d)}</td></tr>`).join('')}</tbody>`).join('');
      return `<div class="o3-find"><h3 class="o3-find__title">All sermons <span class="zh" lang="zh-Hant">所有講道</span></h3>${A.form(p, D, { id: 'o3' })}</div>
<div class="o3-sum">${A.summary(res, p, D)}</div>
${res.total ? `<table class="o3-table"><caption class="sr-only">Sermons, newest first</caption><thead class="sr-only"><tr><th scope="col">Picture</th><th scope="col">Title</th><th scope="col">Speaker</th><th scope="col">Congregation</th><th scope="col">Date</th></tr></thead>${days}</table>` : '<p class="empty">Try another spelling, or <a href="?#archive">clear the filters</a>.</p>'}
<div class="o3-pager">${A.pager(res, p)}</div>`;
    },

    css: `
.o3-hero { position: relative; min-height: calc(100svh - 4.25rem); display: flex; flex-direction: column; background: var(--field); color: var(--field-ink); overflow: hidden; }
.o3-hero__top { display: grid; grid-template-columns: repeat(12, minmax(0, 1fr)); column-gap: var(--gutter); padding: 3.5rem var(--margin) 0; }
.o3-hero__say { grid-column: 1 / 6; display: grid; gap: 1.5rem; }
.o3-lead { font-size: clamp(1.25rem, 1.7vw, 1.625rem); line-height: 1.3; max-width: 34ch; }
.o3-lead .zh { display: block; font-size: 1.5em; color: var(--accent); margin-bottom: 0.25rem; }
.o3-latest { grid-column: 9 / -1; display: grid; gap: 0.75rem; align-content: start; justify-items: end; text-align: right; }
.o3-latest a { display: grid; text-decoration: none; padding: 0.25rem 0; }
.o3-latest a span { font-size: var(--t-small); color: var(--field-muted); }
.o3-latest a b { font-size: var(--t-title); font-weight: 500; line-height: 1.2; }
@media (hover: hover) { .o3-latest a:hover b { text-decoration: underline; } }
.o3-word { position: absolute; left: -8vw; bottom: -6%; margin: 0; font-size: 34vw; font-weight: 500; line-height: 0.8; letter-spacing: -0.06em; white-space: nowrap; color: var(--accent); transform-origin: 50% 100%; }
@supports (animation-timeline: scroll()) {
  @media (prefers-reduced-motion: no-preference) { .o3-word { animation: o3-weigh linear both; animation-timeline: scroll(root); animation-range: 0 100svh; } }
}
@keyframes o3-weigh { to { transform: scale(0.94); } }

.o3-relief { padding: var(--section) var(--margin); background: var(--bg); }
.o3-relief h2 { font-size: clamp(1.75rem, 2.4vw, 2.25rem); font-weight: 500; line-height: 1.1; letter-spacing: -0.02em; margin-bottom: 2.5rem; }
.o3-relief h2 .zh { margin-left: 0.35em; }
.o3-relief__row { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: var(--gutter); }
.o3-relief__row a { display: grid; gap: 0.4rem; text-decoration: none; }
.o3-relief__row img { margin-bottom: 0.75rem; transition: transform 300ms var(--ease-out); }
.o3-relief__min { font-size: 1rem; color: var(--muted); }
.o3-relief__row b { font-size: var(--t-title); font-weight: 500; line-height: 1.2; }
.o3-relief__by { font-size: 1rem; }
@media (hover: hover) { .o3-relief__row a:hover img { transform: translateY(-6px); } .o3-relief__row a:hover b { text-decoration: underline; } }
.o3-scripts { position: relative; height: clamp(46rem, 100svh, 60rem); overflow: hidden; background: var(--accent); color: var(--ink); padding: var(--section) var(--margin) 0; }
.o3-scripts__head { display: grid; grid-template-columns: repeat(12, minmax(0, 1fr)); column-gap: var(--gutter); }
.o3-scripts h2 { grid-column: 1 / 6; font-size: clamp(1.75rem, 2.4vw, 2.25rem); font-weight: 500; line-height: 1.1; letter-spacing: -0.02em; }
.o3-scripts h2 .zh { display: block; }
.o3-scripts__head p { grid-column: 7 / -1; max-width: 52ch; }
.o3-spec { position: absolute; left: var(--margin); right: var(--margin); bottom: 0; display: grid; grid-template-columns: 1fr 1fr; column-gap: var(--gutter); }
.o3-spec figcaption { display: flex; justify-content: space-between; align-items: flex-end; gap: 1.5rem; font-size: 1rem; font-weight: 500; }
.o3-spec figcaption span { max-width: 24ch; }
.o3-spec figcaption a { flex: none; width: clamp(12rem, 17vw, 16rem); }
.o3-spec__glyph { font-family: var(--font-zh); font-size: clamp(11rem, 34vw, 32rem); line-height: 0.8; height: 0.6em; overflow: hidden; margin-top: 1rem; }

.o3-series { padding: var(--section) var(--margin); background: var(--paper); }
.o3-para { font-size: clamp(2.25rem, 5.2vw, 4.875rem); line-height: 1.1; letter-spacing: -0.03em; font-weight: 500; color: var(--muted); max-width: none; }
.o3-para__head { color: var(--ink); }
.o3-para a { color: var(--muted); text-decoration: none; transition: color 160ms ease; }
.o3-para .o3-frame { display: inline-block; width: 1.2em; height: 0.8em; margin-left: 0.15em; vertical-align: -0.02em; overflow: hidden; transform-origin: 0 70%; transition: transform 300ms var(--ease-out); }
.o3-para .o3-frame img { width: 100%; height: 100%; object-fit: cover; }
.o3-para a:hover, .o3-para a:focus-visible { color: var(--ink); }
.o3-para a:hover .o3-frame, .o3-para a:focus-visible .o3-frame { transform: scale(1.6); }
.o3-series__note { margin-top: 2.5rem; font-size: 1rem; max-width: 80ch; }

.o3-list { padding: var(--section) var(--margin); background: var(--bg); --one: clamp(1.125rem, 1.55vw, 1.5rem); }
.o3-find { display: grid; gap: 1.5rem; }
.o3-find__title { font-size: var(--t-head); font-weight: 500; line-height: 1; letter-spacing: -0.035em; }
.o3-find__title .zh { margin-left: 0.4em; }
.o3-list .ar-form { display: grid; grid-template-columns: 2fr 3fr; gap: 1.25rem var(--gutter); align-items: end; }
.o3-list .field--q { grid-column: 1; }
.o3-list .field--q input { min-height: 3.5rem; font-size: var(--one); }
.o3-list .chips { grid-column: 2; }
.o3-list .chip > span { font-size: var(--one); min-height: 3.5rem; padding: 0 1.25rem; }
.o3-list .more { grid-column: 1 / 2; }
.o3-list .more__body { display: grid; grid-template-columns: 1fr 1fr; gap: var(--gutter); }
.o3-list .ar-form__go { grid-column: 2; }
.o3-sum { margin-top: 2rem; font-size: var(--one); }
.o3-sum .ar-count .zh { font-size: 0.75em; }
.o3-table { width: 100%; border-collapse: collapse; font-size: var(--one); line-height: 1.35; margin-top: 1rem; }
.o3-table td { padding: 0 0 1.25rem; vertical-align: top; }
.o3-day th { position: sticky; top: 4.25rem; z-index: 1; background: var(--bg); text-align: left; font-weight: 500; padding: 4.5rem 0 1.25rem; }
.o3-day th .zh { margin-left: 0.35em; }
.o3-row__pic { width: 7rem; padding-right: 1.25rem !important; }
.o3-row__pic img { width: 7rem; }
.o3-row__title { width: 38%; padding-right: 2rem !important; }
.o3-row__title a { font-weight: 500; text-decoration: none; }
.o3-row__title a:hover { text-decoration: underline; }
.o3-row__sub { display: block; font-size: 1rem; color: var(--muted); margin-top: 0.25rem; }
.o3-row td:nth-child(3) { width: 24%; padding-right: 1.5rem; }
.o3-row td:nth-child(4) { width: 16%; }
.o3-row__date { text-align: right; white-space: nowrap; font-variant-numeric: tabular-nums; }
.o3-row img { transition: transform 250ms var(--ease-out); }
@media (hover: hover) { .o3-row:hover img { transform: scale(1.08); } }
.o3-pager { margin-top: 4rem; font-size: var(--one); }

@media (max-width: 1099px) {
  .o3-hero__say { grid-column: 1 / 8; }
  .o3-latest { grid-column: 8 / -1; }
  .o3-list .ar-form { grid-template-columns: 1fr; }
  .o3-list .field--q, .o3-list .chips, .o3-list .more, .o3-list .ar-form__go { grid-column: 1; }
  .o3-table, .o3-table tbody, .o3-table tr, .o3-table td, .o3-table th { display: block; width: auto !important; }
  .o3-row { display: grid !important; grid-template-columns: 6rem 1fr auto; column-gap: 1rem; padding-bottom: 1.25rem; }
  .o3-row td { padding: 0 !important; }
  .o3-row__pic { grid-row: 1 / 4; }
  .o3-row__pic img { width: 6rem; }
  .o3-row__title { grid-column: 2 / -1; }
  .o3-row td:nth-child(3), .o3-row td:nth-child(4) { grid-column: 2; font-size: 1rem; color: var(--muted); }
  .o3-row__date { grid-column: 3; grid-row: 3; font-size: 1rem; }
  .o3-day th { padding-top: 3rem; }
}
@media (max-width: 699px) {
  .o3-hero { min-height: calc(100svh - 4.25rem); }
  .o3-hero__top { display: block; padding: 1.5rem var(--margin) 2rem; width: 60%; }
  .o3-lead { font-size: 1.125rem; }
  .o3-relief__row { grid-template-columns: 1fr; gap: 2.5rem; }
  .o3-hero .hs__row { flex-direction: column; }
  .o3-hero .hs input { width: 100%; flex: none; }
  .o3-latest { margin-top: 2rem; justify-items: start; text-align: left; }
  .o3-latest a b { font-size: 1.0625rem; }
  .o3-word { left: auto; right: -0.16em; top: -0.1em; bottom: auto; writing-mode: vertical-rl; font-size: 62vw; line-height: 0.8; }
  .o3-scripts { height: auto; padding: 4rem var(--margin) 0; }
  .o3-scripts__head { display: block; }
  .o3-scripts__head p { margin-top: 1rem; }
  .o3-spec { position: static; grid-template-columns: 1fr; margin: 2rem calc(var(--margin) * -1) 0; }
  .o3-spec figcaption a { width: 45%; }
  .o3-spec figure { height: 52svh; overflow: hidden; padding: 1rem var(--margin) 0; }
  .o3-spec figure + figure { background: var(--ink); color: var(--paper); }
  .o3-spec__glyph { font-size: 62vw; height: auto; }
  .o3-para { font-size: 9vw; color: var(--ink); line-height: 1.25; }
  .o3-para a { color: var(--ink); text-decoration: underline; text-decoration-thickness: 0.04em; }
  .o3-para a:nth-of-type(n+4) .o3-frame { display: none; }
}
`,
  };
};
