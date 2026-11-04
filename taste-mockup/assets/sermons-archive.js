/* MPCBC sermon archive (mockup). Stands in for the server-side query of the mpcbc-sermons plugin's
   [mpcbc_sermons] shortcode: GET parameters q, ministry, speaker, series, pg; newest first; 12 per page.
   In WordPress this runs in PHP and the page works without JavaScript; here the same logic runs in the
   browser so the static mockups can be searched. Shared by sermons-1.html … sermons-5.html and build.mjs. */
(function (root, factory) {
  const A = factory();
  if (typeof module === 'object' && module.exports) module.exports = A; else root.MPCBCArchive = A;
})(this, function () {
  const PER = 12;
  const MIN = { cantonese: ['Cantonese', '粵語'], mandarin: ['Mandarin', '國語'], english: ['English', '英語'] };
  const MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
  const KEYS = ['q', 'ministry', 'speaker', 'series', 'pg'];

  const esc = (s) => String(s == null ? '' : s).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
  const simp = (s, D) => { let o = ''; for (const c of s) o += D.ts[c] || c; return o; };
  // one spelling for search only: lower case, Simplified characters, no spaces or punctuation
  const norm = (s, D) => simp(String(s || '').toLowerCase(), D).replace(/[\s"“”'‘’.,:;!?()（）\[\]【】\-–—~·﹑、，。：；！？「」『』/]/g, '');

  function doc(x, D) {
    if (x._doc) return x._doc;
    const parts = [x.t, x.r, x.sc];
    for (const i of x.sp) parts.push(D.speakers[i].name, ...D.speakers[i].aliases);
    for (const i of x.se) parts.push(D.series[i].name, ...D.series[i].aliases);
    return (x._doc = norm(parts.join('|'), D));
  }

  function params(search) {
    const u = new URLSearchParams(search), p = {};
    for (const k of ['q', 'ministry', 'speaker', 'series']) { const v = (u.get(k) || '').trim(); if (v) p[k] = v; }
    if (p.ministry && !MIN[p.ministry]) delete p.ministry;
    const pg = parseInt(u.get('pg'), 10); if (pg > 1) p.pg = pg;
    return p;
  }

  function query(D, p) {
    const sp = p.speaker ? D.speakers.findIndex((s) => s.slug === p.speaker) : -1;
    const se = p.series ? D.series.findIndex((s) => s.slug === p.series) : -1;
    const terms = (p.q || '').split(/\s+/).map((t) => norm(t, D)).filter(Boolean);
    const items = D.s.filter((x) => (!p.ministry || x.m.includes(p.ministry))
      && (!p.speaker || x.sp.includes(sp)) && (!p.series || x.se.includes(se))
      && terms.every((t) => doc(x, D).includes(t)));
    const total = items.length, pages = Math.max(1, Math.ceil(total / PER)), page = Math.min(p.pg || 1, pages);
    return { total, pages, page, from: total ? (page - 1) * PER + 1 : 0, to: Math.min(page * PER, total), items: items.slice((page - 1) * PER, page * PER) };
  }

  function href(p, over) {
    const o = Object.assign({}, p, over || {}), u = new URLSearchParams();
    for (const k of KEYS) if (o[k] && !(k === 'pg' && o[k] <= 1)) u.set(k, o[k]);
    const s = u.toString();
    return (s ? '?' + s : '?') + '#archive';
  }

  const date = (d) => { const [y, m, dd] = d.split('-').map(Number); return `${MONTHS[m - 1]} ${dd}, ${y}`; };
  const dateLong = (d) => { const [y, m, dd] = d.split('-').map(Number); return `${['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'][m - 1]} ${dd}, ${y}`; };
  const dateZh = (d) => { const [y, m, dd] = d.split('-').map(Number); return `${y}年${m}月${dd}日`; };
  const thumb = (v, size) => `https://i.ytimg.com/vi/${v}/${size || 'mqdefault'}.jpg`;
  const watch = (v) => `https://www.youtube.com/watch?v=${v}`;
  const min = (x) => x.m.map((m) => MIN[m][0]).join(' + ');
  const minZh = (x) => x.m.map((m) => MIN[m][1]).join('、');
  const speaker = (x, D) => x.sp.map((i) => D.speakers[i].name).join(', ');
  const series = (x, D) => x.se.map((i) => D.series[i].name).join(', ');
  const lang = (x) => (x.m.includes('english') ? 'en' : x.m.includes('mandarin') && !x.m.includes('cantonese') ? 'zh-Hans' : 'zh-Hant');
  const newTab = '<span class="sr-only"> (opens YouTube in a new tab)</span>';

  // group a page of results by Sunday, keeping order
  function sundays(items) {
    const out = [];
    for (const x of items) { const g = out[out.length - 1]; if (g && g.date === x.d) g.items.push(x); else out.push({ date: x.d, items: [x] }); }
    return out;
  }

  // the result line, active filters and Clear: shared wording, each option styles it
  function summary(res, p, D) {
    const bits = [];
    if (p.q) bits.push(['“' + esc(p.q) + '”', { q: '' }]);
    if (p.ministry) bits.push([MIN[p.ministry][0] + ' <span class="zh" lang="zh-Hant">' + MIN[p.ministry][1] + '</span>', { ministry: '' }]);
    if (p.speaker) { const s = D.speakers.find((x) => x.slug === p.speaker); if (s) bits.push([esc(s.name), { speaker: '' }]); }
    if (p.series) { const s = D.series.find((x) => x.slug === p.series); if (s) bits.push([esc(s.name), { series: '' }]); }
    const count = res.total
      ? `<p class="ar-count" role="status"><b>${res.from}–${res.to}</b> of <b>${res.total.toLocaleString('en-US')}</b> sermons<span class="zh" lang="zh-Hant">共 ${res.total} 篇講道</span></p>`
      : '<p class="ar-count" role="status"><b>No sermons</b> match these choices.<span class="zh" lang="zh-Hant">找不到相符的講道</span></p>';
    const chips = bits.length
      ? `<ul class="ar-active" aria-label="Active filters">${bits.map(([label, off]) => `<li><a href="${esc(href(p, Object.assign({ pg: 0 }, off)))}" aria-label="Remove filter">${label} <span aria-hidden="true">×</span></a></li>`).join('')}<li><a class="ar-clear" href="?#archive">Clear filters <span class="zh" lang="zh-Hant">清除</span></a></li></ul>`
      : '';
    return count + chips;
  }

  function pager(res, p) {
    if (res.pages < 2) return '';
    const n = res.page, last = res.pages, nums = new Set([1, last, n - 1, n, n + 1]);
    let prev = 0, links = '';
    for (const i of [...nums].filter((i) => i >= 1 && i <= last).sort((a, b) => a - b)) {
      if (i - prev > 1) links += '<li class="pager__gap" aria-hidden="true">…</li>';
      links += i === n ? `<li><span class="pager__num" aria-current="page">${i}</span></li>` : `<li><a class="pager__num" href="${esc(href(p, { pg: i }))}">${i}</a></li>`;
      prev = i;
    }
    const step = (to, label, rel) => (to < 1 || to > last ? `<span class="pager__step" aria-disabled="true">${label}</span>` : `<a class="pager__step" rel="${rel}" href="${esc(href(p, { pg: to }))}">${label}</a>`);
    return `<nav class="pager" aria-label="Pages of sermons">${step(n - 1, 'Newer <span class="zh" lang="zh-Hant">較新</span>', 'prev')}<ol>${links}</ol>${step(n + 1, 'Older <span class="zh" lang="zh-Hant">較舊</span>', 'next')}</nav>`;
  }

  // the filter form: a plain GET form; with script, a change submits it at once
  function form(p, D, opt) {
    opt = opt || {};
    const id = opt.id || 'ar';
    const radios = [['', 'All', '全部']].concat(Object.entries(MIN).map(([k, v]) => [k, v[0], v[1]]))
      .map(([k, en, zh]) => `<label class="chip"><input type="radio" name="ministry" value="${k}"${(p.ministry || '') === k ? ' checked' : ''}><span>${en} <span class="zh" lang="zh-Hant">${zh}</span></span></label>`).join('');
    const sel = (name, list, label, zh, all) => `<div class="field"><label for="${id}-${name}">${label} <span class="zh" lang="zh-Hant">${zh}</span></label><select id="${id}-${name}" name="${name}"><option value="">${all}</option>${list.map((s) => `<option value="${esc(s.slug)}"${p[name] === s.slug ? ' selected' : ''}>${esc(s.name)}${s.en ? ' · ' + esc(s.en) : ''} (${s.count})</option>`).join('')}</select></div>`;
    const q = `<div class="field field--q"><label for="${id}-q">Search title, speaker or series <span class="zh" lang="zh-Hant">搜尋標題、講員或系列</span></label><input id="${id}-q" name="q" type="search" value="${esc(p.q || '')}" autocomplete="off" spellcheck="false"></div>`;
    const ministry = opt.ministry === 'hidden' ? (p.ministry ? `<input type="hidden" name="ministry" value="${p.ministry}">` : '')
      : `<fieldset class="chips"><legend>Congregation <span class="zh" lang="zh-Hant">會眾</span></legend>${radios}</fieldset>`;
    const more = `<details class="more" open data-more><summary>Speaker and series <span class="zh" lang="zh-Hant">講員及系列</span></summary><div class="more__body">${sel('speaker', D.speakers.filter((s) => s.count > 1 || p.speaker === s.slug), 'Speaker', '講員', 'All speakers 全部')}${sel('series', D.series, 'Series', '系列', 'All series 全部')}</div></details>`;
    return `<form class="ar-form" method="get" action="#archive" role="search" aria-label="Search sermons" data-archive-form>${q}${ministry}${more}<div class="ar-form__go"><button class="button" type="submit">Search <span class="zh" lang="zh-Hant">搜尋</span></button></div></form>`;
  }

  // the small search box for a hero ([mpcbc_sermons part=search]): q only, lands on the results
  function searchBox(p, opt) {
    opt = opt || {};
    const id = opt.id || 'hs';
    return `<form class="hs" method="get" action="#archive" role="search" aria-label="Search sermons"><label class="${opt.labelClass || 'sr-only'}" for="${id}">Search title, speaker or series <span class="zh" lang="zh-Hant">搜尋講道</span></label><span class="hs__row"><input id="${id}" name="q" type="search" value="${esc(p.q || '')}" autocomplete="off" spellcheck="false" placeholder="${esc(opt.placeholder || '希伯來書, Joshua, 張健庭 …')}"><button class="button${opt.button ? ' ' + opt.button : ''}" type="submit">Search <span class="zh" lang="zh-Hant">搜尋</span></button></span></form>`;
  }

  // progressive enhancement: chips and selects submit at once; filters fold on phones
  function enhance(doc) {
    for (const f of doc.querySelectorAll('[data-archive-form]')) {
      f.addEventListener('change', (e) => { if (e.target.matches('input[type=radio], select')) f.requestSubmit(); });
    }
    if (matchMedia('(max-width: 699px)').matches) for (const d of doc.querySelectorAll('[data-more]')) if (!d.querySelector('select option[selected]:not([value=""])')) d.open = false;
  }

  return { PER, MIN, esc, norm, params, query, href, date, dateLong, dateZh, thumb, watch, min, minZh, speaker, series, lang, newTab, sundays, summary, pager, form, searchBox, enhance };
});
