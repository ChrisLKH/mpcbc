// Option 2 — Two inks, one chapter per congregation (direction: colour-chapters, seed 308125052)
export default ({ A, D, latest, count, esc }) => {
  const chapters = [
    ['cantonese', 'Cantonese', '粵語', 'yellow'],
    ['mandarin', 'Mandarin', '國語', 'white'],
    ['english', 'English', '英語', 'ink'],
  ].map(([m, en, zh, ground]) => ({ m, en, zh, ground, x: latest[m], n: D.s.filter((s) => s.m.includes(m)).length }));
  const wall = D.series.filter((t) => t.count >= 5).slice(0, 12);
  const rows = [[0, 3, 240], [3, 5, 360], [5, 9, 200], [9, 12, 300]];
  const yr = (t) => (t.first.slice(0, 4) === t.last.slice(0, 4) ? t.last.slice(0, 4) : `${t.first.slice(0, 4)}–${t.last.slice(2, 4)}`);
  return {
    file: 'sermons-2.html', name: 'Option 2 · Two inks, one chapter per congregation',
    title: 'Sermons 講道 · MPCBC',
    desc: 'Every recorded Sunday sermon from MPCBC’s Cantonese, Mandarin and English congregations, searchable by title, speaker and series.',
    body: ({ archive }) => `
<section class="o2-hero" id="top" aria-labelledby="o2-title">
  <h1 class="o2-title" id="o2-title"><span class="o2-line"><span>Sermons</span></span> <span class="o2-line o2-line--zh" lang="zh-Hant"><span>講道</span></span></h1>
  <div class="o2-hero__say">
    <p>${count} sermons from three congregations, every Sunday since April 2021. Search by title, speaker or series, in English or Chinese.</p>
    ${A.searchBox({}, { id: 'o2-q', button: 'button--accent' })}
  </div>
</section>

${chapters.map((c, i) => `<section class="o2-ch o2-ch--${c.ground}" id="latest-${c.m}" aria-labelledby="o2-ch-${c.m}" data-grow>
  <h2 class="o2-ch__title" id="o2-ch-${c.m}">Latest in ${c.en} <span class="zh" lang="zh-Hant">${c.zh}堂最新</span></h2>
  <a class="o2-ch__pic" href="${A.watch(c.x.v)}" target="_blank" rel="noopener"><img src="${A.thumb(c.x.v, 'maxresdefault')}" width="1280" height="720" alt="Title card: ${esc(c.x.t)}" ${i ? 'loading="lazy"' : ''}>${A.newTab}</a>
  <p class="o2-ch__say"><b lang="${A.lang(c.x)}">${esc(c.x.t)}</b> ${[A.speaker(c.x, D), c.x.sc, A.series(c.x, D)].filter(Boolean).map(esc).join(' · ')} · ${A.date(c.x.d)}</p>
  <a class="o2-ch__all" href="${A.href({}, { ministry: c.m })}">All ${c.n} ${c.en} sermons <span class="zh" lang="zh-Hant">全部${c.zh}講道</span></a>
</section>`).join('\n')}

<section class="o2-series" id="series" aria-labelledby="o2-series-title">
  <div class="o2-series__head">
    <h2 class="o2-giant" id="o2-series-title" data-in>Series <span class="zh" lang="zh-Hant">系列</span></h2>
    <a class="o2-series__all" href="#archive">All ${D.series.length} series in the filters below</a>
  </div>
  <div class="o2-wall">
    ${rows.map(([a, b, h]) => `<ul class="o2-wall__row" style="--h:${h}px">${wall.slice(a, b).map((t, k) => `<li class="o2-tile" data-in style="--i:${k}"><a href="${esc(A.href({}, { series: t.slug }))}"><img src="${A.thumb(t.latest, h >= 300 ? 'maxresdefault' : 'hqdefault')}" width="${h >= 300 ? 1280 : 480}" height="${h >= 300 ? 720 : 360}" alt="" loading="lazy"><span class="o2-tile__cap"><span lang="zh-Hant">${esc(t.name)}${t.en ? ` <span lang="en">${esc(t.en)}</span>` : ''}</span><span>${t.count} · ${yr(t)}</span></span></a></li>`).join('')}</ul>`).join('\n    ')}
  </div>
</section>

<section class="o2-archive" id="archive" aria-labelledby="o2-archive-title">
  <h2 class="o2-giant" id="o2-archive-title" data-in>All sermons <span class="zh" lang="zh-Hant">所有講道</span></h2>
  <div id="archive-body">${archive}</div>
</section>`,

    // [mpcbc_sermons] output: one form row, then each Sunday as a menu category with photo bullets
    archive: function (res, p, A, D) {
      const e = A.esc;
      const cats = A.sundays(res.items).map((g) => `<li class="o2-cat"><div class="o2-cat__head"><h3>${A.dateLong(g.date)} <span class="zh" lang="zh-Hant">${A.dateZh(g.date)}</span></h3><p>${g.items.length} ${g.items.length === 1 ? 'sermon' : 'sermons'}</p></div><ul class="o2-items">${g.items.map((x) => `<li><a class="o2-item" href="${A.watch(x.v)}" target="_blank" rel="noopener"><img class="thumb" src="${A.thumb(x.v)}" width="320" height="180" alt="" loading="lazy"><span class="o2-item__text"><span class="o2-item__top"><span class="o2-item__title" lang="${A.lang(x)}">${e(x.t)}</span><span class="o2-item__min">${A.min(x)} <span class="zh" lang="zh-Hant">${A.minZh(x)}</span></span></span><span class="o2-item__desc">${[A.speaker(x, D), x.sc, A.series(x, D)].filter(Boolean).map(e).join(' · ')}</span></span>${A.newTab}</a></li>`).join('')}</ul></li>`).join('');
      return `${A.form(p, D, { id: 'o2' })}<div class="o2-sum">${A.summary(res, p, D)}</div>${res.total ? `<ol class="o2-cats">${cats}</ol>` : '<p class="empty">Try another spelling, or <a href="?#archive">clear the filters</a>.</p>'}<div class="o2-pager">${A.pager(res, p)}</div>`;
    },

    css: `
.o2-hero { min-height: calc(100svh - 4.25rem); display: grid; grid-template-columns: repeat(12, minmax(0, 1fr)); grid-template-rows: 1fr auto; column-gap: var(--gutter); padding: 9svh var(--margin) 2.5rem; background: var(--field); color: var(--field-ink); overflow: hidden; }
.o2-title { grid-column: 1 / -1; font-size: clamp(6.5rem, 21vw, 20rem); font-weight: 500; letter-spacing: -0.055em; line-height: 0.82; }
.o2-line { display: block; overflow: hidden; padding-bottom: 0.06em; }
.o2-line > span { display: block; animation: o2-rise 900ms var(--ease-out) both; }
.o2-line--zh { color: var(--accent); font-family: var(--font-zh); letter-spacing: 0; line-height: 1; }
.o2-line--zh > span { animation-delay: 110ms; }
@keyframes o2-rise { from { transform: translateY(105%); } to { transform: none; } }
.o2-hero__say { grid-column: 8 / -1; align-self: end; justify-self: end; width: min(100%, 30rem); display: grid; gap: 1.25rem; animation: o2-fade 700ms 500ms var(--ease-out) both; }
.o2-hero__say p { font-size: 1.125rem; }
@keyframes o2-fade { from { opacity: 0; transform: translateY(16px); } }

.o2-ch { position: relative; min-height: 100svh; display: grid; grid-template-columns: 1fr auto 1fr; grid-template-rows: auto 1fr auto; padding: 3rem var(--margin); gap: 1.5rem; transition: clip-path 700ms var(--ease-out); }
.o2-ch--yellow { background: var(--accent); color: var(--ink); }
.o2-ch--white { background: var(--paper); color: var(--ink); }
.o2-ch--ink { background: var(--field); color: var(--field-ink); }
.o2-ch__title { grid-column: 1 / -1; font-size: clamp(1.75rem, 2.4vw, 2.25rem); font-weight: 500; line-height: 1.1; letter-spacing: -0.02em; }
.o2-ch__title .zh { display: block; }
.o2-ch__pic { grid-column: 2; grid-row: 2; align-self: center; width: min(62vw, 128svh); display: block; transition: transform 800ms var(--ease-out); }
.o2-ch__pic img { width: 100%; aspect-ratio: 16 / 9; object-fit: cover; }
.o2-ch__say { grid-column: 1 / 3; grid-row: 3; align-self: end; max-width: 40ch; font-size: var(--t-body); }
.o2-ch__say b { display: block; font-size: var(--t-title); line-height: 1.25; margin-bottom: 0.25rem; }
.o2-ch__all { grid-column: 3; grid-row: 3; justify-self: end; align-self: end; display: grid; justify-items: end; min-height: 2.75rem; font-weight: 500; text-align: right; white-space: nowrap; }
.io .o2-ch[data-grow] { clip-path: inset(20px); }
.io .o2-ch[data-grow] .o2-ch__pic { transform: scale(0.92); }
.io .o2-ch[data-grow].is-in { clip-path: inset(0); }
.io .o2-ch[data-grow].is-in .o2-ch__pic { transform: none; }
@media (hover: hover) { .o2-ch__pic:hover { transform: scale(1.015); } .o2-ch__all:hover { text-decoration-thickness: 2px; } }

.o2-series { padding: var(--section) var(--margin); background: var(--bg); }
.o2-series__head { display: flex; justify-content: space-between; align-items: baseline; gap: 1rem; margin-bottom: 2rem; }
.o2-giant { font-size: clamp(4.5rem, 9vw, 8.5rem); font-weight: 500; letter-spacing: -0.05em; line-height: 0.9; }
.o2-giant .zh { letter-spacing: 0; font-size: 0.6em; }
.o2-series__all { font-size: var(--t-small); font-weight: 500; white-space: nowrap; display: inline-flex; min-height: 2.75rem; align-items: center; }
.o2-wall { display: grid; gap: 8px; }
.o2-wall__row { display: flex; gap: 8px; }
.o2-tile { flex: 1 1 calc(var(--h) * 16 / 9); min-width: 0; }
.o2-tile a { display: block; text-decoration: none; transition: opacity 200ms ease; }
.o2-tile img { width: 100%; height: var(--h); object-fit: cover; }
.o2-tile__cap { display: flex; justify-content: space-between; gap: 1rem; padding-top: 0.5rem; font-size: var(--t-small); line-height: 1.3; }
.o2-tile__cap > span:first-child { font-weight: 500; }
.o2-tile__cap > span:last-child { flex: none; color: var(--muted); font-variant-numeric: tabular-nums; }
@media (hover: hover) { .o2-wall:hover .o2-tile a { opacity: 0.55; } .o2-wall .o2-tile a:hover { opacity: 1; } }

.o2-archive { padding: var(--section) var(--margin); background: var(--paper); }
.o2-archive .o2-giant { margin-bottom: 3rem; }
.o2-archive .ar-form { display: grid; grid-template-columns: minmax(16rem, 1.3fr) auto; gap: 1.25rem 2rem; align-items: end; padding: 1.5rem; background: var(--bg); }
.o2-archive .field--q { grid-column: 1; }
.o2-archive .chips { grid-column: 2; }
.o2-archive .chips .chip > span { background: var(--paper); }
.o2-archive .chips .chip input:checked + span { background: var(--ink); color: var(--paper); }
.o2-archive .more { grid-column: 1; }
.o2-archive .more__body { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; }
.o2-archive .ar-form__go { grid-column: 2; }
.o2-sum { margin-top: 1.5rem; }
.o2-cat { margin-top: 6rem; }
.o2-cat__head { display: flex; justify-content: space-between; align-items: baseline; gap: 1rem; margin-bottom: 2.5rem; }
.o2-cat__head h3 { font-size: clamp(2rem, 3.4vw, 3.125rem); font-weight: 500; letter-spacing: -0.035em; line-height: 1; }
.o2-cat__head h3 .zh { font-size: var(--t-title); letter-spacing: 0; margin-left: 0.4em; color: var(--muted); }
.o2-cat__head p { font-size: var(--t-body); font-variant-numeric: tabular-nums; }
.o2-items { display: grid; grid-template-columns: 1fr 1fr; gap: 2.25rem 4rem; }
.o2-item { display: grid; grid-template-columns: 8rem 1fr; gap: 1.25rem; text-decoration: none; }
.o2-item__text { display: grid; align-content: start; gap: 0.35rem; }
.o2-item__top { display: flex; justify-content: space-between; gap: 1rem; }
.o2-item__title { font-size: var(--t-body); font-weight: 500; line-height: 1.3; }
.o2-item__min { flex: none; font-size: var(--t-small); font-weight: 500; text-align: right; }
.o2-item__min .zh { display: block; color: var(--muted); font-weight: 400; }
.o2-item__desc { font-size: 1rem; color: var(--muted); max-width: 38ch; }
.o2-item img { transition: transform 300ms var(--ease-out); }
@media (hover: hover) { .o2-item:hover img { transform: scale(1.06); } .o2-item:hover .o2-item__title { text-decoration: underline; } }
.o2-pager { margin-top: 5rem; }

@media (max-width: 1099px) {
  .o2-hero__say { grid-column: 5 / -1; }
  .o2-archive .ar-form { grid-template-columns: 1fr; }
  .o2-archive .field--q, .o2-archive .chips, .o2-archive .more, .o2-archive .ar-form__go { grid-column: 1; }
  .o2-items { grid-template-columns: 1fr; }
  .o2-wall__row { flex-wrap: wrap; }
  .o2-tile { flex-basis: 40%; }
}
@media (max-width: 699px) {
  .o2-hero { min-height: calc(100svh - 4.25rem); padding-top: 14svh; grid-template-columns: minmax(0, 1fr); column-gap: 0; }
  .o2-title { font-size: 26vw; }
  .o2-hero__say { grid-column: 1 / -1; justify-self: start; }
  .o2-ch { min-height: auto; grid-template-columns: 1fr; padding: 2rem var(--margin) 2.5rem; }
  .o2-ch__pic, .o2-ch__say, .o2-ch__all { grid-column: 1; grid-row: auto; width: 100%; justify-self: start; text-align: left; }
  .o2-ch__title { font-size: 1.5rem; }
  .o2-giant { font-size: 15vw; }
  .o2-series__head { flex-direction: column; }
  .o2-wall__row { display: grid; grid-template-columns: 62fr 38fr; gap: 6px; }
  .o2-wall__row .o2-tile:first-child { grid-column: 1 / -1; }
  .o2-tile img { height: auto; aspect-ratio: 16 / 9; }
  .o2-tile__cap { flex-direction: column; gap: 0.1rem; }
  .o2-archive .ar-form { padding: 1rem; }
  .o2-archive .more__body { grid-template-columns: 1fr; }
  .o2-cat { margin-top: 5rem; }
  .o2-cat__head { flex-direction: column; gap: 0.25rem; margin-bottom: 1.5rem; }
  .o2-cat__head h3 { font-size: 8.5vw; }
  .o2-cat__head h3 .zh { display: block; margin: 0.35rem 0 0; font-size: 1rem; }
  .o2-item { grid-template-columns: 5.5rem 1fr; gap: 1rem; }
  .o2-item__top { flex-direction: column; gap: 0.2rem; }
  .o2-item__min { text-align: left; }
  .o2-item__min .zh { display: inline; }
}
`,
  };
};
