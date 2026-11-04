// Option 1 — The shelf and the sermon list (direction: page-is-the-object, seed 708000332)
export default ({ A, D, latest, count, esc }) => {
  const run = D.s.slice(0, 6);
  const spines = [
    ...D.series.map((t) => ({ kind: 'series', name: t.name, en: t.en, n: t.count, href: A.href({}, { series: t.slug }), m: t.m })),
    ...D.speakers.filter((t) => t.count >= 4).map((t) => ({ kind: 'speaker', name: t.name, en: t.en, n: t.count, href: A.href({}, { speaker: t.slug }) })),
  ];
  const zhMin = { cantonese: '粵', mandarin: '國', english: '英' };
  return {
    file: 'sermons-1.html', name: 'Option 1 · The shelf and the sermon list',
    title: 'Sermons 講道 · MPCBC',
    desc: 'Every recorded Sunday sermon from MPCBC’s Cantonese, Mandarin and English congregations, searchable by title, speaker and series.',
    body: ({ archive }) => `
<section class="o1-hero" id="top" aria-labelledby="o1-title">
  <img class="o1-hero__room" src="assets/sanctuary-aisle.jpg" width="1600" height="900" alt="">
  <div class="o1-card" data-in>
    <h1 class="o1-title" id="o1-title"><span>Sermon</span> <span>archive</span> <span lang="zh-Hant">講道錄</span></h1>
    <div class="o1-card__mid">
      <p class="o1-facts"><span>${count} recorded sermons</span><span>Cantonese, Mandarin, English</span><span>Every Sunday since April 2021</span></p>
      <p class="o1-say">Find one by its title, speaker or series. <span lang="zh-Hant">中英文皆可搜尋。</span></p>
    </div>
    <div class="o1-card__foot">
      <p class="o1-credit">The sanctuary, 302 W. Commonwealth Ave.</p>
      ${A.searchBox({}, { id: 'o1-q' })}
      <a class="o1-link" href="#series">Browse by series <span class="zh" lang="zh-Hant">系列</span></a>
    </div>
  </div>
</section>

<section class="o1-latest" id="latest" aria-labelledby="o1-latest-title">
  <div class="o1-latest__side">
    <h2 id="o1-latest-title" data-in>Latest sermons <span class="zh" lang="zh-Hant">最新講道</span></h2>
    <a class="o1-link" href="#archive" data-in style="--i:1">All ${count} sermons</a>
    <p class="o1-note" data-in style="--i:2">${A.dateLong(run[0].d)}: one sermon in each congregation. The newest opens large.</p>
  </div>
  <ol class="o1-run" aria-label="The six newest sermons">
    ${run.map((x, i) => `<li class="o1-frame${i === 0 ? ' is-lifted' : ''}" data-in style="--i:${i}">
      <a href="${A.watch(x.v)}" target="_blank" rel="noopener">
        <img class="thumb" src="${A.thumb(x.v, i === 0 ? 'maxresdefault' : 'hqdefault')}" width="${i === 0 ? 1280 : 480}" height="${i === 0 ? 720 : 360}" alt="" loading="${i === 0 ? 'eager' : 'lazy'}">
        <span class="o1-frame__cap"><span lang="${A.lang(x)}">${esc(x.t)}</span><span>${A.min(x)}<br>${A.date(x.d)}</span></span>${A.newTab}
      </a>
    </li>`).join('\n    ')}
  </ol>
</section>

<section class="o1-shelf" id="series" aria-labelledby="o1-shelf-title">
  <div class="o1-shelf__head">
    <h2 id="o1-shelf-title" data-in>Series and speakers <span class="zh" lang="zh-Hant">系列與講員</span></h2>
    <p data-in style="--i:1">${D.series.length} series and ${spines.length - D.series.length} regular speakers, newest series first: white spines are series, grey spines are speakers, and the yellow tab gives the number of sermons and where they were preached (粵 Cantonese, 國 Mandarin, 英 English).</p>
  </div>
  <ul class="o1-spines" aria-label="Series and speakers">
    ${spines.map((t, i) => `<li class="o1-spine o1-spine--${t.kind}${i === D.series.length ? ' is-first-speaker' : ''}" data-in style="--i:${(i % 10) / 4}"><a href="${esc(t.href)}"><span class="o1-spine__name" lang="${/[A-Za-z]/.test(t.name) ? 'en' : 'zh-Hant'}">${esc(t.name)}</span><span class="o1-spine__tag">${t.n}${t.m ? ' ' + t.m.map((m) => zhMin[m]).join('') : ''}</span><span class="sr-only">${t.kind === 'series' ? ' series' : ' speaker'}${t.en ? ', ' + esc(t.en) : ''}, ${t.n} sermons</span></a></li>`).join('\n    ')}
  </ul>
</section>

<section class="o1-archive" id="archive" aria-labelledby="o1-archive-title">
  <div id="archive-body" class="o1-archive__body">${archive}</div>
</section>`,

    // [mpcbc_sermons] output: the order slip (form) beside the printed list sheet
    archive: function (res, p, A, D) {
      const e = A.esc;
      const groups = A.sundays(res.items).map((g) => `<li class="o1-group"><h3><span>Sunday, ${A.dateLong(g.date)}</span> <span class="zh" lang="zh-Hant">主日 ${A.dateZh(g.date)}</span></h3><ul>${g.items.map((x) => `<li class="o1-item"><a href="${A.watch(x.v)}" target="_blank" rel="noopener"><img class="thumb" src="${A.thumb(x.v)}" width="320" height="180" alt="" loading="lazy"><span class="o1-item__text"><span class="o1-item__title" lang="${A.lang(x)}">${e(x.t)}</span><span class="o1-item__desc">${[A.speaker(x, D), x.sc, A.series(x, D)].filter(Boolean).map(e).join(' · ')}</span></span><span class="o1-item__min">${A.min(x)}<span class="zh" lang="zh-Hant">${A.minZh(x)}</span></span>${A.newTab}</a></li>`).join('')}</ul></li>`).join('');
      return `<div class="o1-slip"><h2 class="o1-slip__title">Find a sermon <span class="zh" lang="zh-Hant">尋找講道</span></h2>${A.form(p, D, { id: 'o1' })}</div>
<div class="o1-sheet"><div class="o1-sheet__in">
  <h2 class="o1-sheet__title" id="o1-archive-title">All sermons <span class="zh" lang="zh-Hant">所有講道</span></h2>
  <div class="o1-sheet__note">${A.summary(res, p, D)}</div>
  ${res.total ? `<ol class="o1-groups">${groups}</ol>` : `<p class="empty">Try another spelling, or <a href="?#archive">clear the filters</a>.</p>`}
  <div class="o1-sheet__foot"><p>Newest first · 12 to a page · Page ${res.page} of ${res.pages}</p>${A.pager(res, p)}</div>
</div></div>`;
    },

    css: `
.o1-hero { position: relative; min-height: calc(100svh - 4.25rem); display: grid; grid-template-columns: minmax(0, 1fr); place-items: center; padding: 3rem var(--margin); overflow: hidden; background: var(--field); }
.o1-hero__room { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; filter: brightness(0.7); }
.o1-card { position: relative; width: min(100%, 56vw); min-height: 62svh; display: flex; flex-direction: column; justify-content: space-between; gap: 2rem; padding: 2rem; background: var(--paper); }
.o1-title { display: flex; justify-content: space-between; gap: 1rem; font-size: clamp(2.5rem, 2.8vw, 2.75rem); font-weight: 500; letter-spacing: -0.03em; line-height: 1; }
.o1-title span[lang] { font-family: var(--font-zh); letter-spacing: 0.04em; }
.o1-card__mid { display: grid; gap: 1.5rem; }
.o1-facts, .o1-say { font-size: clamp(1.375rem, 2vw, 2rem); line-height: 1.1; letter-spacing: -0.02em; }
.o1-facts span { display: block; }
.o1-say { max-width: 30ch; color: var(--muted); }
.o1-card__foot > * { min-width: 0; }
.o1-card__foot { display: grid; grid-template-columns: 1fr auto 1fr; align-items: center; gap: 1rem 1.5rem; }
.o1-credit { font-size: var(--t-small); color: var(--muted); max-width: 16ch; }
.o1-card .hs { width: 22rem; max-width: 100%; }
.o1-card .hs input { background: var(--bg); }
.o1-link { justify-self: end; text-align: right; display: inline-flex; align-items: center; min-height: 2.75rem; font-weight: 500; }
.o1-link .zh { margin-left: 0.35em; }

.o1-latest { display: grid; grid-template-columns: repeat(12, minmax(0, 1fr)); column-gap: var(--gutter); padding: var(--section) var(--margin); }
.o1-latest__side { grid-column: 1 / 4; display: flex; flex-direction: column; align-items: flex-start; gap: 0.5rem; padding-right: var(--gutter); }
.o1-latest h2, .o1-shelf h2 { font-size: var(--t-title); font-weight: 500; line-height: 1.2; letter-spacing: -0.01em; }
.o1-latest h2 .zh, .o1-shelf h2 .zh { display: block; }
.o1-note { margin-top: auto; font-size: 1rem; max-width: 26ch; }
.o1-run { grid-column: 4 / -1; display: flex; align-items: flex-end; gap: 6px; overflow-x: auto; scroll-snap-type: x proximity; padding-bottom: 0.75rem; scrollbar-width: thin; }
.o1-frame { flex: none; width: calc(var(--row) * 16 / 9); --row: clamp(8.5rem, 15vh, 10rem); scroll-snap-align: start; }
.o1-frame.is-lifted { --row: clamp(18.5rem, 33vh, 22rem); }
.o1-frame a { display: block; text-decoration: none; }
.o1-frame img { transition: transform 400ms var(--ease-out); }
.o1-frame__cap { display: flex; justify-content: space-between; gap: 0.75rem; margin-top: 0.6rem; font-size: 1rem; line-height: 1.35; min-height: 2.7em; }
.o1-frame__cap > span:first-child { font-weight: 500; }
.o1-frame__cap > span:last-child { flex: none; text-align: right; color: var(--muted); }
.o1-frame.is-lifted .o1-frame__cap { font-size: var(--t-title); }
.o1-frame.is-lifted .o1-frame__cap > span:last-child { font-size: var(--t-small); }
@media (hover: hover) { .o1-frame a:hover img { transform: translateY(-6px); } .o1-frame a:hover .o1-frame__cap > span:first-child { text-decoration: underline; } }

.o1-shelf { background: var(--field); color: var(--field-ink); padding: 6rem var(--margin); }
.o1-shelf__head { display: flex; align-items: baseline; gap: 2rem; margin-bottom: 2rem; }
.o1-shelf__head p { font-size: 1rem; color: var(--field-muted); max-width: 52ch; }
.o1-spines { display: flex; gap: 6px; }
.o1-spine { flex: 1 1 0; min-width: 2.125rem; }
.o1-spine.is-first-speaker { margin-left: 1.75rem; }
.o1-spine a { display: flex; flex-direction: column; align-items: center; height: 420px; padding: 0.9rem 0 0; background: var(--paper); color: var(--ink); text-decoration: none; font-size: 1.125rem; font-weight: 500; line-height: 1; transition: background-color 160ms ease, color 160ms ease, transform 300ms var(--ease-out); }
.o1-spine--speaker a { background: var(--bg); }
.o1-spine__name { writing-mode: vertical-rl; white-space: nowrap; letter-spacing: 0.02em; max-height: 330px; overflow: hidden; }
.o1-spine__tag { margin-top: auto; align-self: stretch; text-align: center; padding: 0.5rem 0.1rem; line-height: 1.2; background: var(--accent); font-size: var(--t-small); font-variant-numeric: tabular-nums; white-space: nowrap; }
.o1-spine--speaker .o1-spine__tag { background: var(--surface); }
.o1-spine a:hover, .o1-spine a:focus-visible { background: var(--ink); color: var(--paper); outline: 3px solid var(--paper); outline-offset: -3px; transform: translateY(-10px); }
.o1-spine a:hover .o1-spine__tag, .o1-spine a:focus-visible .o1-spine__tag { color: var(--ink); }

.o1-archive { padding: var(--section) var(--margin); }
.o1-archive__body { display: grid; grid-template-columns: repeat(12, minmax(0, 1fr)); column-gap: var(--gutter); align-items: start; }
.o1-slip { grid-column: 1 / 5; position: sticky; top: 5.5rem; padding: 2rem; background: var(--accent); }
.o1-slip__title { font-size: var(--t-title); font-weight: 500; line-height: 1.2; margin-bottom: 1.5rem; }
.o1-slip__title .zh { display: block; }
.o1-slip .ar-form { display: grid; gap: 1.25rem; }
.o1-slip .more__body { display: grid; gap: 1.25rem; }
.o1-slip .field input, .o1-slip .field select { border-color: var(--ink); }
.o1-slip .chip > span { background: #fff4bf; }
.o1-slip .chip input:checked + span { background: var(--ink); color: var(--paper); }
.o1-sheet { grid-column: 6 / -1; position: relative; margin: 14px 0; background: var(--paper); }
.o1-sheet::before, .o1-sheet::after { content: ""; position: absolute; left: 0; right: 0; height: 14px; background: url("data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 width=%2742%27 height=%2714%27%3E%3Cpath d=%27M0 14A21 14 0 0 1 42 14Z%27 fill=%27%23fff%27/%3E%3C/svg%3E") 0 0 / 42px 14px repeat-x; }
.o1-sheet::before { bottom: 100%; }
.o1-sheet::after { top: 100%; transform: scaleY(-1); }
.o1-sheet__in { padding: 4.5rem 4rem; }
.o1-sheet__title { text-align: center; font-size: var(--t-head); font-weight: 500; line-height: 1; letter-spacing: -0.035em; }
.o1-sheet__title .zh { display: block; margin-top: 0.5rem; font-size: var(--t-title); letter-spacing: 0; }
.o1-sheet__note { margin-top: 1rem; text-align: center; font-size: var(--t-small); }
.o1-sheet__note .ar-active { justify-content: center; }
.o1-groups { display: grid; gap: 3.5rem; margin-top: 3.5rem; }
.o1-group h3 { text-align: center; font-size: var(--t-title); font-weight: 500; line-height: 1.25; margin-bottom: 1.25rem; }
.o1-group h3 .zh { display: block; font-size: var(--t-small); color: var(--muted); }
.o1-group ul { display: grid; gap: 1rem; }
.o1-item a { display: grid; grid-template-columns: 7.5rem 1fr auto; gap: 1.25rem; align-items: start; text-decoration: none; padding: 0.4rem; margin: -0.4rem; transition: background-color 160ms ease; }
.o1-item__text { display: grid; gap: 0.2rem; }
.o1-item__title { font-weight: 500; line-height: 1.3; }
.o1-item__desc { font-size: 1rem; color: var(--muted); line-height: 1.4; }
.o1-item__min { text-align: right; font-size: var(--t-small); font-weight: 500; line-height: 1.35; white-space: nowrap; }
.o1-item__min .zh { display: block; color: var(--muted); font-weight: 400; }
@media (hover: hover) { .o1-item a:hover { background: var(--bg); } .o1-item a:hover .o1-item__title { text-decoration: underline; } }
.o1-sheet__foot { margin-top: 3.5rem; display: grid; justify-items: center; gap: 1rem; text-align: center; font-size: var(--t-small); }
.o1-sheet__foot .pager { justify-content: center; }

@media (max-width: 1099px) {
  .o1-card { width: 100%; }
  .o1-latest__side { grid-column: 1 / -1; padding-right: var(--margin); margin-bottom: 2rem; }
  .o1-note { margin-top: 0.5rem; }
  .o1-run { grid-column: 1 / -1; }
  .o1-spines { display: flex; flex-wrap: wrap; gap: 10px 10px; }
  .o1-spine { flex: none; max-width: 100%; min-width: 0; }
  .o1-spine.is-first-speaker { margin-left: 0; }
  .o1-spine__name { white-space: normal; }
  .o1-spine__name { writing-mode: horizontal-tb; max-height: none; }
  .o1-spine a { flex-direction: row; height: auto; min-height: 2.75rem; padding: 0 0 0 0.75rem; font-size: 1.0625rem; gap: 0.6rem; }
  .o1-spine__tag { margin: 0; align-self: stretch; display: inline-flex; align-items: center; padding: 0 0.6rem; }

  .o1-slip { grid-column: 1 / -1; position: static; margin-bottom: 2rem; }
  .o1-slip .ar-form { grid-template-columns: 1fr 1fr; }
  .o1-slip .field--q, .o1-slip .chips { grid-column: 1 / -1; }
  .o1-slip .more { grid-column: 1 / -1; }
  .o1-slip .more__body { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; }
  .o1-card__foot { grid-template-columns: auto 1fr; }
  .o1-credit { grid-column: 1 / -1; max-width: none; }
  .o1-link { justify-self: start; }
  .o1-sheet { grid-column: 1 / -1; }
}
@media (max-width: 699px) {
  .o1-hero { padding: 1rem; min-height: auto; }
  .o1-card { min-height: 70svh; padding: 1.5rem; }
  .o1-frame, .o1-frame.is-lifted { --row: 34vw; }
  .o1-frame.is-lifted { width: 100%; flex: none; }
  .o1-run { flex-wrap: wrap; }
  .o1-run .o1-frame:not(.is-lifted) { width: calc(50% - 3px); }
  .o1-frame__cap { flex-direction: column; gap: 0.2rem; }
  .o1-frame__cap > span:last-child { text-align: left; }
  .o1-title { display: block; font-size: 9vw; line-height: 1.05; }
  .o1-title span[lang] { display: block; }
  .o1-facts, .o1-say { font-size: 5.6vw; }
  .o1-card__foot { grid-template-columns: 1fr; }
  .o1-card .hs { width: 100%; }
  .o1-link { justify-self: start; }
  .o1-shelf__head { flex-direction: column; gap: 0.5rem; }
  .o1-slip { padding: 1.5rem 1rem; }
  .o1-slip .ar-form, .o1-slip .more__body { grid-template-columns: 1fr; }
  .o1-archive { padding-left: 10px; padding-right: 10px; }
  .o1-slip { margin-left: calc(var(--margin) - 10px); margin-right: calc(var(--margin) - 10px); }
  .o1-sheet__in { padding: 3rem 1.75rem; }
  .o1-item a { grid-template-columns: 5.5rem 1fr; }
  .o1-item__min { grid-column: 1 / -1; text-align: right; margin-top: -0.25rem; }
  .o1-item__min .zh { display: inline; margin-left: 0.4em; }
}
`,
  };
};
