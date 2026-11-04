// Builds the Home page from taste-mockup/index.html as native Atomic Elementor JSON.
// Output: content-source/taste-home.json (applied by apply-taste-home.php).
// The hero is kept from the current Home (content-source/before-taste-rebuild/21.json), only re-weighted.
import fs from 'node:fs';
import crypto from 'node:crypto';

const root = new URL('../', import.meta.url);
const read = (p) => JSON.parse(fs.readFileSync(new URL(p, root), 'utf8'));
const media = read('content-source/taste-media.json');
const before = read('content-source/before-taste-rebuild/21.json').meta._elementor_data;
const SITE = 'http://mpcbc.local';
const hex = (n) => crypto.randomBytes(n).toString('hex');

// ---------- tokens (Atomic global variables; values set by apply-taste-palette.php) ----------
const C = { ink: 'e-gv-c70cdc4', page: 'e-gv-f7cfe2a', surface: 'e-gv-824ebc4', muted: 'e-gv-7317e11', paper: 'e-gv-1984c94',
  accent: 'e-gv-0c168a6', couponInput: 'e-gv-428b859', tone3: 'e-gv-3cc97d2', fieldMuted: 'e-gv-468ea41', kids: 'e-gv-kidsfld' };
const F = { en: 'e-gv-d649b5e', tc: 'e-gv-2635213', sc: 'e-gv-99d935c' };

// ---------- prop values ----------
const sz = (size, unit = 'px') => ({ $$type: 'size', value: { size, unit } });
const auto = () => sz('', 'auto');
const cu = (v) => sz(v, 'custom');
const str = (value) => ({ $$type: 'string', value });
// row gap, column gap
const gaps = (r, c) => ({ $$type: 'layout-direction', value: { row: sz(r), column: sz(c) } });
const num = (value) => ({ $$type: 'number', value });
const col = (v) => ({ $$type: 'global-color-variable', value: v });
const side = (v) => (v === 'auto' ? auto() : sz(v));
const dims = (t, r = t, b = t, l = r) => ({ $$type: 'dimensions', value: { 'block-start': side(t), 'inline-end': side(r), 'block-end': side(b), 'inline-start': side(l) } });
const bg = (v) => ({ $$type: 'background', value: { color: col(v) } });
const bgImg = (file, repeat, size, position = 'top left', color) => ({ $$type: 'background', value: {
  ...(color ? { color: col(color) } : {}),
  'background-overlay': { $$type: 'background-overlay', value: [{ $$type: 'background-image-overlay', value: {
    image: { $$type: 'image', value: { src: { $$type: 'image-src', value: { id: { $$type: 'image-attachment-id', value: media[file].id } } }, size: str('full') } },
    repeat: str(repeat), size: typeof size === 'string' ? str(size) : { $$type: 'background-image-size-scale', value: { width: sz(size[0]), height: sz(size[1]) } },
    position: str(position) } }] } } });
const rot = (deg) => ({ $$type: 'transform', value: { 'transform-functions': { $$type: 'transform-functions', value: [
  { $$type: 'transform-rotate', value: { x: sz(0, 'deg'), y: sz(0, 'deg'), z: sz(deg, 'deg') } }] } } });
const flexGrow = (g, s = 1, b = 'auto') => ({ $$type: 'flex', value: { flexGrow: num(g), flexShrink: num(s), flexBasis: cu(b) } });

// ---------- styles: {d, t, m} per breakpoint, or a flat object for desktop ----------
const bps = { d: 'desktop', t: 'tablet', m: 'mobile' };
// flat keys are desktop; d/t/m keys hold per-breakpoint props
const norm = (p = {}) => { const { d = {}, t = {}, m = {}, ...flat } = p; return { d: { ...flat, ...d }, t, m }; };
// a prop value may itself be per-breakpoint: { d: value, m: value }
const perProp = (v) => v && typeof v === 'object' && !v.$type && ('d' in v || 't' in v || 'm' in v);
const spread = (p) => { const out = { d: {}, t: {}, m: {} }; for (const bp in p) for (const k in p[bp]) { const v = p[bp][k]; if (perProp(v)) { for (const b in v) out[b][k] = v[b]; } else out[bp][k] = v; } return out; };
const mix = (...ps) => ps.map((p) => spread(norm(p))).reduce((a, p) => { for (const k in p) a[k] = { ...(a[k] || {}), ...p[k] }; return a; }, {});

function node(type, settings, props, children = [], title) {
  const cls = `e-${hex(4)}-${hex(2)}`;
  const p = spread(norm(props));
  const variants = Object.entries(p).filter(([, v]) => Object.keys(v).length).map(([bp, v]) => ({ meta: { breakpoint: bps[bp], state: null }, props: v, custom_css: null }));
  const widget = ['e-heading', 'e-paragraph', 'e-button', 'e-image', 'e-svg', 'e-divider'].includes(type);
  const n = { id: hex(4), elType: widget ? 'widget' : type, settings: { classes: { $$type: 'classes', value: [cls] }, ...settings }, elements: children,
    styles: { [cls]: { id: cls, label: 'local', type: 'class', variants } }, editor_settings: { title: title || type }, version: '0.0' };
  if (widget) n.widgetType = type; else n.isInner = false;
  return n;
}
const id = (v) => (v ? { _cssid: str(v) } : {});

// ---------- layout ----------
const box = (props, kids, o = {}) => node('e-flexbox', { ...id(o.id), ...(o.href ? linkSet(o.href) : {}) },
  mix({ display: str('flex'), 'flex-direction': str('column'), padding: sz(0), 'min-width': sz(0) }, props), kids, o.title);
const row = (props, kids, o) => box(mix({ 'flex-direction': str('row'), m: { 'flex-direction': str('column') } }, props), kids, o);
const grid = (cols, props, kids, o) => node('e-div-block', { ...id(o?.id) },
  mix({ display: str('grid'), 'grid-template-columns': str(cols), padding: sz(0), 'min-width': sz(0), m: { 'grid-template-columns': str('minmax(0, 1fr)') } }, props), kids, o?.title || 'grid');
const div = (props, kids = [], o = {}) => node('e-div-block', { ...id(o.id) }, mix({ padding: sz(0), 'min-width': sz(0) }, props), kids, o.title || 'div');
const linkSet = (href) => ({ tag: str('a'), link: { $$type: 'link', value: { destination: { $$type: 'url', value: href }, isTargetBlank: { $$type: 'boolean', value: /^https?:/.test(href) && !href.startsWith(SITE) } } } });

// one section: full width, page margins, section rhythm
const section = (anchor, title, props, kids) => box(mix({ width: sz(100, '%'), padding: { d: dims(104, 32), t: dims(88, 32), m: dims(72, 16) }, 'scroll-margin-top': sz(72) }, props), kids, { id: anchor, title });

// ---------- type system: heading 56 · title 24 · body 17 · small 15, weights 400/500 ----------
const T = {
  head: { d: { 'font-size': sz(56), 'font-weight': str('500'), 'line-height': sz(1, 'em'), 'letter-spacing': sz(-0.035, 'em') }, t: { 'font-size': sz(48) }, m: { 'font-size': sz(40) } },
  title: { d: { 'font-size': sz(24), 'font-weight': str('500'), 'line-height': sz(1.2, 'em'), 'letter-spacing': sz(-0.01, 'em') }, m: { 'font-size': sz(20) } },
  lead: { d: { 'font-size': sz(24), 'font-weight': str('400'), 'line-height': sz(1.35, 'em') }, m: { 'font-size': sz(20) } },
  body: { 'font-size': sz(17), 'font-weight': str('400'), 'line-height': sz(1.5, 'em') },
  small: { 'font-size': sz(15), 'font-weight': str('400'), 'line-height': sz(1.45, 'em') },
  fact: { 'font-size': sz(15), 'font-weight': str('500'), 'line-height': sz(1.45, 'em') },
  strong: { 'font-size': sz(17), 'font-weight': str('500'), 'line-height': sz(1.5, 'em') },
};
const ink = (v) => ({ color: col(v) });
const textBase = (font, kind, color) => mix({ 'font-family': { $$type: 'global-font-variable', value: font }, margin: sz(0), 'max-width': sz(100, '%'), 'min-width': sz(0) }, T[kind], color ? ink(color) : {});
const esc = (s) => s.replaceAll('&', '&amp;');
const H = (text, tag, kind, color, props, cssid) => node('e-heading', { tag: str(tag), title: { $$type: 'escaped-html', value: esc(text) }, ...id(cssid) }, mix(textBase(F.en, kind, color), props), [], text.slice(0, 40));
const P = (text, kind = 'body', color, props, cssid, tag = 'p') => node('e-paragraph', { paragraph: { $$type: 'escaped-html', value: esc(text) }, tag: str(tag), ...id(cssid) }, mix(textBase(F.en, kind, color), props), [], text.slice(0, 40));
// a Chinese line: Traditional + Simplified copies, switched by the header language selector
const zh = (key, hant, hans, kind = 'body', color, props, tag = 'p') => [
  node('e-paragraph', { paragraph: { $$type: 'escaped-html', value: hant }, tag: str(tag), ...id(`mpcbc-copy-zh-hant-taste-${key}`) }, mix(textBase(F.tc, kind, color), props), [], `Traditional / 繁體中文 · ${key}`),
  node('e-paragraph', { paragraph: { $$type: 'escaped-html', value: hans }, tag: str(tag), ...id(`mpcbc-copy-zh-hans-taste-${key}`) }, mix(textBase(F.sc, kind, color), props), [], `Simplified / 简体中文 · ${key}`),
];
const img = (file, alt, props) => node('e-image', { image: { $$type: 'image', value: { src: { $$type: 'image-src', value: { id: { $$type: 'image-attachment-id', value: media[file].id } } }, size: str('full') } } },
  mix({ width: sz(100, '%'), 'max-width': sz(100, '%'), 'min-width': sz(0), 'object-fit': str('cover'), margin: sz(0) }, props), [], file);

// buttons: one round shape; a linked flexbox so the Chinese copy pair can sit inside
const BTN = { ink: [C.ink, C.page], accent: [C.accent, C.ink], paper: [C.paper, C.ink], page: [C.page, C.ink] };
const button = (href, label, tone = 'ink', zhPair, props) => {
  const [b, f] = BTN[tone];
  return row(mix({ 'align-items': str('center'), 'justify-content': str('center'), gap: sz(8), 'min-height': sz(52), padding: dims(0, 26), 'border-radius': sz(999),
    background: bg(b), width: auto(), 'text-decoration': str('none'), m: { 'flex-direction': str('row'), 'flex-wrap': str('wrap') } }, props),
    [P(label, 'strong', f, {}, null, 'span'), ...(zhPair ? zh(zhPair[0], zhPair[1], zhPair[2], 'strong', f, {}, 'span') : [])], { href, title: `button ${label}` });
};
const link = (href, label, color = C.ink, zhPair, kind = 'strong') => row({ 'align-items': str('center'), gap: sz(6), 'min-height': sz(44), width: auto(), 'text-decoration': str('underline'), m: { 'flex-direction': str('row') } },
  [P(label, kind, color, { 'text-decoration': str('underline') }, null, 'span'), ...(zhPair ? zh(zhPair[0], zhPair[1], zhPair[2], kind, color, {}, 'span') : [])], { href, title: `link ${label}` });
// section heading: English, then its Chinese at title size
const heading = (key, en, hant, hans, color = C.ink, tag = 'h2') => box({ gap: sz(8) }, [H(en, tag, 'head', color), ...zh(key, hant, hans, 'title', color)], { title: `heading ${en}` });
const circle = (size, color, props) => div(mix({ position: str('absolute'), width: sz(size), height: sz(size), 'border-radius': sz(50, '%'), background: bg(color), 'z-index': num(2) }, props), [], { title: 'notch' });

// ================= sections =================
const MAPS = 'https://www.google.com/maps/search/?api=1&query=302+W.+Commonwealth+Ave.+Alhambra+CA+91801';

// ---------- hero: the current Home's hero, weights brought to the type system ----------
const hero = structuredClone(before[0]);
(function reweight(n) {
  for (const st of Object.values(n.styles || {})) for (const v of st.variants) {
    const w = v.props['font-weight']?.value; if (w && +w > 500 && v.props['font-family']?.value !== F.tc && v.props['font-family']?.value !== F.sc) v.props['font-weight'] = str('500');
  }
  (n.elements || []).forEach(reweight);
})(hero);
(function heroFix(n) {
  if (n.settings?._cssid?.value === 'mpcbc-rail_three_services') {
    const order = ['mandarin', 'children', 'canto', 'english'];
    n.elements.sort((a, b) => order.indexOf(a.settings._cssid.value.split('_').pop()) - order.indexOf(b.settings._cssid.value.split('_').pop()));
  }
  if (n.settings?._cssid?.value === 'mpcbc-hero_link_plan') {
    const v = Object.values(n.styles)[0].variants.find((x) => x.meta.breakpoint === 'desktop' && !x.meta.state);
    v.props.background = bg(C.accent); v.props.color = col(C.ink);
  }
  (n.elements || []).forEach(heroFix);
})(hero);

// ---------- This Sunday: taste-skill "Output slips along the edge"; the sanctuary, three order-of-service slips on its bottom edge ----------
// The thumbnail + Watch live pill and the Latest line come from the mpcbc-sermons plugin (latest YouTube sermon per congregation).
const sermon = (ministry, part) => ({ id: hex(4), elType: 'widget', widgetType: 'shortcode', settings: { shortcode: `[mpcbc_live ministry="${ministry}" part="${part}"]` }, elements: [] });
const service = (i, key, en, hant, hans, when, links) => // heights step by the slip's tail, not by empty space above it
box({ background: bg(C.paper), padding: dims(16, 16, [24, 64, 44][i], 16), gap: sz(0), ...(i !== 1 ? { transform: rot(i === 0 ? -1.5 : 1) } : {}), m: { padding: dims(16, 16, 24, 16), transform: rot(0) } }, [
  sermon(key, 'media'),
  row({ gap: sz(10), 'flex-wrap': str('wrap'), 'align-items': str('flex-end'), margin: dims(16, 0, 0, 0), m: { 'flex-direction': str('row') } }, [H(en, 'h3', 'title', C.ink), ...zh(`sun-${key}`, hant, hans, 'title', C.ink)]),
  P('- - - - - - - - - - - - - - - - - - - - - - - -', 'small', C.muted, { margin: dims(8, 0), overflow: str('hidden'), 'letter-spacing': sz(0.2, 'em') }),
  P(when, 'fact', C.ink),
  box({ margin: dims(4, 0, 0, 0) }, [sermon(key, 'status')], { title: `latest ${en}` }),
  row({ gap: sz(20), 'flex-wrap': str('wrap'), margin: dims(4, 0, 0, 0), m: { 'flex-direction': str('row') } }, links),
], { title: `service ${en}` });
const sunday = section('watch', 'This Sunday', { background: bg(C.page), padding: { d: dims(104, 32), t: dims(88, 32), m: dims(72, 16) } }, [
  box({ gap: sz(16), 'max-width': sz(640) }, [
    heading('sunday', 'This Sunday', '本主日', '本主日'),
    P('Three congregations worship under one roof at 302 W. Commonwealth Ave. Watch live or catch up on the latest message.', 'lead', C.ink),
  ], { title: 'sunday intro' }),
  grid('repeat(3, minmax(0, 1fr))', { gap: sz(12), 'align-items': str('end'), margin: dims(48, 0, 0, 0), m: { gap: sz(24) } }, [
    service(0, 'mandarin', 'Mandarin worship', '國語主日崇拜', '国语主日崇拜', 'Sundays 9:00 AM, Sanctuary',
      [link('https://mpcbc.net/category/mandarin-sermons/', 'Archive', C.ink, ['archive-m', '存檔', '存档']), link('https://www.youtube.com/@MPCBC', 'YouTube @MPCBC')]),
    service(1, 'cantonese', 'Cantonese worship', '粵語主日崇拜', '粤语主日崇拜', 'Sundays 11:00 AM, Sanctuary',
      [link('https://mpcbc.net/category/cantonese-sermons/', 'Archive', C.ink, ['archive-c', '存檔', '存档']), link('https://www.youtube.com/@MPCBC', 'YouTube @MPCBC')]),
    service(2, 'english', 'English worship', '英語主日崇拜', '英语主日崇拜', 'Sundays 11:00 AM, Fellowship Hall (Connect Ministry)',
      [link('https://mpcbc.net/english/connect-ministry-sermons/', 'Messages'), link('https://www.youtube.com/channel/UCJx8Xl0QqiNkZwwfLXaASHQ', 'English YouTube')]),
  ], { title: 'slips' }),
]);

// ---------- Pastors: taste-skill "Founder's letter" (sheet): the mission on a letter sheet with a scalloped foot, signed by the three pastors ----------
const ring = (face) => face.file
  ? img(face.file, '', { width: sz(288), height: sz(288), 'max-width': sz(100, '%'), 'border-radius': sz(50, '%'), 'object-fit': str('cover') })
  : box({ width: sz(288), height: sz(288), 'max-width': sz(100, '%'), 'border-radius': sz(50, '%'), background: bg(C.page), 'align-items': str('center'), 'justify-content': str('center'), padding: sz(16) }, [P(face.text, 'small', C.ink, { 'text-align': str('center') })], { title: 'portrait placeholder' });
const sign = (i, face, role, name, zhPair, about) => box({ gap: sz(2) }, [
  ring(face),
  H(name, 'h3', 'title', C.ink, { 'font-style': str('italic'), margin: dims(14, 0, 0, 0) }),
  ...zh(`pastor-${i}`, zhPair[0], zhPair[1], 'title', C.ink),
  P(role, 'fact', C.ink),
  P(about, 'body', C.ink, { margin: dims(8, 0, 0, 0) }),
], { title: `signature ${name}` });
const pastors = section('pastors', 'Pastors', { background: bg(C.page) }, [
  heading('pastors', 'Meet our pastors', '認識我們的牧師', '认识我们的牧师', C.ink),
  box({ width: sz(100, '%'), margin: dims(48, 0, 0, 0), gap: sz(0) }, [
    box({ background: bg(C.paper), padding: { d: dims(60, 60, 62, 60), m: dims(32, 24, 40, 24) }, gap: sz(16) }, [
      div({ display: str('block'), 'max-width': sz(760) }, [
        P('Knowing God is our foundation.', 'lead', C.ink, { 'font-size': { d: sz(28), m: sz(20) }, background: bg(C.accent), padding: dims(0, 3), display: str('inline') }, null, 'span'),
        P(' Glorifying God is our goal.', 'lead', C.ink, { 'font-size': { d: sz(28), m: sz(20) }, display: str('inline') }, null, 'span'),
      ], { title: 'mission' }),
      ...zh('mission', '認識神為基礎，榮耀神是目標。我們藉著敬拜﹑團契﹑門徒訓練﹑事奉﹑傳福音來認識和榮耀神。', '认识神为基础，荣耀神是目标。我们借着敬拜﹑团契﹑门徒训练﹑事奉﹑传福音来认识和荣耀神。', 'body', C.ink, { 'line-height': sz(1.8, 'em'), 'max-width': sz(760) }),
      grid('repeat(3, minmax(0, 1fr))', { gap: gaps(32, 64), margin: dims(40, 0, 0, 0) }, [
        sign(0, { file: 'pastor-caleb.jpg' }, 'Pastor, Cantonese', 'Rev. Caleb Cheung, Ph.D.', ['張健庭牧師', '张健庭牧师'], 'Senior pastor; studied at Fuller Theological Seminary and Claremont Graduate University.'),
        sign(1, { text: '[Photo of Rev. Edward Lu]' }, 'Pastor, English', 'Rev. Edward Lu', ['[牧師姓名]', '[牧师姓名]'], 'Lead pastor of Connect Ministry, the church’s English-language ministry.'),
        sign(2, { text: '[Photo of the Mandarin pastor]' }, 'Pastor, Mandarin', '[Pastor name]', ['[牧師姓名]', '[牧师姓名]'], '[One line about this pastor.]'),
      ], { title: 'signatures' }),
    ], { title: 'letter sheet' }),
    div({ width: sz(100, '%'), height: sz(10), background: bgImg('scallop.svg', 'repeat-x', [26, 10]) }, [], { title: 'scalloped foot' }),
    row({ background: bg(C.paper), 'min-height': sz(72), 'justify-content': str('space-between'), 'align-items': str('center'), gap: sz(16), padding: { d: dims(0, 60), m: dims(12, 24) }, margin: dims(4, 0, 0, 0),
      'border-width': { $$type: 'border-width-v2', value: { 'block-start': sz(5), 'inline-end': sz(0), 'block-end': sz(0), 'inline-start': sz(0) } }, 'border-style': str('dotted'), 'border-color': col(C.ink) }, [
      link(`${SITE}/about/`, 'Our story and team', C.ink, ['pastors-btn', '教會介紹及教牧團隊', '教会介绍及教牧团队']),
      link('mailto:info@mpcbc.net', 'info@mpcbc.net'),
    ], { title: 'tear-off strip' }),
  ], { title: 'letter' }),
]);

// ---------- Kids: the owner's original layout, one photo slideshow on a white mat ----------
const age = (name, line) => box({ background: bg(C.paper), padding: sz(20), gap: sz(10) }, [P(name, 'title', C.ink), P(line, 'fact', C.muted)], { title: `age ${name}` });
const slideshow = { id: hex(4), elType: 'container', isInner: true, elements: [], settings: {
  content_width: 'full', min_height: { unit: 'px', size: 560 }, min_height_tablet: { unit: 'px', size: 420 }, min_height_mobile: { unit: 'px', size: 300 },
  background_background: 'slideshow', background_slideshow_gallery: ['vbs-crafts.jpg', 'vbs-wave.jpg', 'vbs-floor.jpg'].map((f) => ({ id: media[f].id, url: media[f].url })),
  background_slideshow_loop: 'yes', background_slideshow_slide_duration: 6000, background_slideshow_slide_transition: 'fade', background_slideshow_transition_duration: 1600,
  background_slideshow_background_size: 'cover', background_slideshow_ken_burns: 'yes', background_slideshow_ken_burns_zoom_direction: 'in',
  padding: { unit: 'px', top: '0', right: '0', bottom: '0', left: '0', isLinked: true }, _title: 'Kids photo slideshow (classic container: Atomic has no slideshow)' } };
const kids = section('kids', 'Kids', { background: bg(C.kids) }, [
  grid('minmax(0, 7fr) minmax(0, 5fr)', { gap: gaps(40, 64), 'align-items': str('center'), t: { 'grid-template-columns': str('minmax(0, 1fr)') } }, [
    box({ gap: sz(0) }, [
      heading('kids', 'Your kids will want to come back.', '兒童及青少年', '儿童及青少年'),
      P('Safe, fun classes for every age while you’re in the service.', 'lead', C.ink, { margin: dims(20, 0, 0, 0) }),
      grid('repeat(3, minmax(0, 1fr))', { gap: sz(12), margin: dims(28, 0, 0, 0) }, [age('Nursery', 'Ages 0–3, all morning'), age('Kids', 'Ages 4–11, [time]'), age('Youth', 'Sunday school, Sundays 10:00–10:45 AM')]),
      grid('repeat(2, minmax(0, 1fr))', { gap: gaps(8, 32), margin: dims(24, 0, 0, 0) },
        ['Secure check-in with matching tags', 'Classes taught in English', 'Background-checked volunteers', 'Chinese-speaking helpers for parents'].map((t) => P(`✓  ${t}`))),
      div({ display: str('flex'), margin: dims(20, 0, 0, 0) }, [div({ background: bg(C.paper), padding: dims(8, 16) }, zh('kids-note', '課堂以英語進行，登記處有中文同工協助家長。', '课堂以英语进行，登记处有中文同工协助家长。'))]),
      P('Children’s worship is held on Sundays at 11:00 AM. [The Sunday timetable says 10:00 AM: confirm.]', 'small', C.muted, { margin: dims(16, 0, 0, 0) }),
      row({ margin: dims(28, 0, 0, 0) }, [button(`${SITE}/kids-youth/`, 'See Kids & Youth', 'ink')]),
    ], { title: 'kids text' }),
    box({ gap: sz(10) }, [
      div({ background: bg(C.paper), padding: sz(14) }, [slideshow], { title: 'white mat' }),
      P('VBS Magnified! 2025 at MPCBC.', 'small', C.muted),
    ], { title: 'kids photo' }),
  ], { title: 'kids card' }),
]);

// ---------- Church life: the dark programme sheet with zigzag edges ----------
const item = (nameEls, val, desc, href) => box({ gap: sz(2) }, [
  row({ gap: sz(10), 'align-items': str('flex-end'), m: { 'flex-direction': str('row') } }, [
    href ? row({ gap: sz(6), width: auto(), 'text-decoration': str('none'), m: { 'flex-direction': str('row') } }, nameEls, { href }) : row({ gap: sz(6), width: auto(), m: { 'flex-direction': str('row') } }, nameEls),
    div({ flex: flexGrow(1, 1, '24px'), 'border-width': { $$type: 'border-width-v2', value: { 'block-start': sz(0), 'inline-end': sz(0), 'block-end': sz(4), 'inline-start': sz(0) } }, 'border-style': str('dotted'), 'border-color': col(C.fieldMuted), transform: { $$type: 'transform', value: { 'transform-functions': { $$type: 'transform-functions', value: [{ $$type: 'transform-move', value: { x: sz(0), y: sz(-5), z: sz(0) } }] } } } }, [], { title: 'dotted leader' }),
    P(val, 'strong', C.page, { 'text-align': str('end'), flex: flexGrow(0, 0) }, null, 'span'),
  ]),
  ...(desc ? [P(desc, 'small', C.fieldMuted)] : []),
]);
const nm = (t) => [P(t, 'body', C.page, {}, null, 'span')];
const nmZh = (t, key, hant, hans) => [P(t, 'body', C.page, {}, null, 'span'), ...zh(key, hant, hans, 'body', C.page, {}, 'span')];
const group = (key, en, hant, hans, items) => box({ gap: sz(16) }, [
  row({ gap: sz(8), 'align-items': str('flex-end'), m: { 'flex-direction': str('row') } }, [H(en, 'h3', 'title', C.page), ...zh(`life-${key}`, hant, hans, 'title', C.fieldMuted)]),
  box({ gap: sz(14) }, items),
]);
const FBS = 'https://mpcbc.net/english/fellowship-bible-study-and-prayer/';
const life = section('community', 'Church life', { background: bg(C.ink) }, [
  box({ 'align-items': str('stretch') }, [
    heading('life', 'Church life', '教會生活', '教会生活', C.page),
    grid('repeat(3, minmax(0, 1fr))', { gap: gaps(48, 56), margin: dims(40, 0, 0, 0), t: { 'grid-template-columns': str('minmax(0, 1fr)') } }, [
      group('youth', 'Youth and students', '青少年', '青少年', [
        item(nm('Youth Sunday School'), 'Sun 10:00–10:45 AM', 'In the education annex.', FBS),
        item(nm('College / Career Fellowship'), 'Mon 6:30–9:00 PM', 'In West Covina.', FBS),
      ]),
      group('fellowship', 'Fellowships', '團契', '团契', [
        item(nmZh('Cantonese', 'life-canto', '粵語', '粤语'), '[Day, time]'),
        item(nm('English'), '[Day, time]', null, FBS),
        item(nmZh('Mandarin', 'life-mandarin', '國語', '国语'), '[Day, time]', 'Study the Bible, pray and grow together.'),
      ]),
      group('care', 'Care', '關懷', '关怀', [
        item(nmZh('Prayer request', 'life-prayer', '代禱', '代祷'), 'A pastor reads every one', null, '#connect'),
        item(nm('[Counseling]'), '[Details]'),
        item(nm('[Visitation]'), '[Details]', '[How the church cares for people when life is hard.]'),
      ]),
    ], { title: 'groups' }),
  ], { title: 'programme sheet' }),
]);

// ---------- Coming up: event leaves + Bible plan on the left, a calendar page on the right ----------
const ev = (day, mon, title, meta) => grid('72px minmax(0, 1fr)', { 'column-gap': sz(20), 'align-items': str('start'), m: { 'grid-template-columns': str('72px minmax(0, 1fr)') } }, [
  box({ background: bg(C.page), 'min-height': sz(72), 'align-items': str('center'), 'justify-content': str('center'), padding: dims(8, 4), gap: sz(2) }, [P(day, 'title', C.ink, { 'text-align': str('center') }), P(mon, 'small', C.muted, { 'text-align': str('center') })]),
  box({ padding: dims(6, 0, 0, 0), gap: sz(3) }, [H(title, 'h3', 'title', C.ink), P(meta, 'fact', C.ink)]),
], { title: `event ${title}` });
// a static month: Sundays (worship) filled yellow, Mondays (fellowship) ringed. Rebuild monthly or swap for a calendar plugin later.
const MONTH = process.env.TASTE_MONTH || '2026-10';
const [yy, mm] = MONTH.split('-').map(Number);
const first = new Date(Date.UTC(yy, mm - 1, 1)).getUTCDay(), days = new Date(Date.UTC(yy, mm, 0)).getUTCDate();
const monthName = new Date(Date.UTC(yy, mm - 1, 1)).toLocaleString('en-US', { month: 'long', year: 'numeric', timeZone: 'UTC' });
const dayCell = (d) => {
  const wd = (first + d - 1) % 7;
  const mark = wd === 0 ? { background: bg(C.accent) } : wd === 1 ? { 'border-width': sz(2), 'border-style': str('solid'), 'border-color': col(C.ink) } : {};
  return box({ 'align-items': str('center'), padding: dims(3, 0) }, [P(String(d), 'strong', C.ink, mix({ width: sz(42), height: sz(42), 'border-radius': sz(50, '%'), display: str('flex'), 'align-items': str('center'), 'justify-content': str('center'), 'text-align': str('center') }, mark), null, 'span')]);
};
const keyDot = (mark) => div(mix({ width: sz(16), height: sz(16), 'border-radius': sz(50, '%'), flex: flexGrow(0, 0, '16px') }, mark));
const calendar = box({ background: bg(C.page), padding: dims(32, 32, 24, 32), 'border-width': { $$type: 'border-width-v2', value: { 'block-start': sz(14), 'inline-end': sz(0), 'block-end': sz(0), 'inline-start': sz(0) } }, 'border-style': str('dotted'), 'border-color': col(C.paper), m: { padding: dims(20, 12) } }, [
  H(monthName, 'h3', 'title', C.ink),
  grid('repeat(7, minmax(0, 1fr))', { margin: dims(20, 0, 0, 0), 'text-align': str('center'), m: { 'grid-template-columns': str('repeat(7, minmax(0, 1fr))') } }, [
    ...['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'].map((d) => P(d, 'small', C.muted, { 'text-align': str('center'), padding: dims(0, 0, 12, 0) })),
    ...Array.from({ length: first }, () => div({})),
    ...Array.from({ length: days }, (_, i) => dayCell(i + 1)),
  ], { title: 'month grid' }),
  box({ gap: sz(6), margin: dims(20, 0, 0, 0) }, [
    row({ gap: sz(8), 'align-items': str('center'), m: { 'flex-direction': str('row') } }, [keyDot({ background: bg(C.accent) }), P('Sunday worship', 'fact', C.ink, {}, null, 'span'), ...zh('cal-worship', '主日崇拜', '主日崇拜', 'fact', C.ink, {}, 'span')]),
    row({ gap: sz(8), 'align-items': str('center'), m: { 'flex-direction': str('row') } }, [keyDot({ 'border-width': sz(2), 'border-style': str('solid'), 'border-color': col(C.ink) }), P('College / Career Fellowship, Mondays 6:30 PM', 'fact', C.ink, {}, null, 'span')]),
    P('[Event days are circled here once their dates are added.]', 'small', C.muted),
  ]),
], { title: 'calendar page' });
const events = section('events', 'Coming up', { background: bg(C.paper) }, [
  heading('events', 'Coming up', '近期活動', '近期活动'),
  grid('repeat(12, minmax(0, 1fr))', { gap: gaps(48, 32), margin: dims(40, 0, 0, 0), t: { 'grid-template-columns': str('minmax(0, 1fr)') } }, [
    box({ 'grid-column': { $$type: 'span', value: '1 / 7' }, gap: sz(0), t: { 'grid-column': { $$type: 'span', value: 'auto' } } }, [
      box({ gap: sz(20) }, [ev('[Day]', '[Mon]', '[Event title]', '[Time], [Place]'), ev('[Day]', '[Mon]', '[Event title]', '[Time], [Place]'), ev('[Day]', '[Mon]', '[Event title]', '[Time], [Place]'), ev('Mon', 'weekly', 'College / Career Fellowship', '6:30–9:00 PM, West Covina')]),
      row({ margin: dims(16, 0, 0, 0) }, [link('#events', 'See all events')]),
      box({ margin: dims(40, 0, 0, 0), gap: sz(4) }, [
        row({ gap: sz(8), 'align-items': str('flex-end'), 'flex-wrap': str('wrap'), m: { 'flex-direction': str('row') } }, [H('Read the Bible in a year', 'h3', 'title', C.ink), ...zh('bible', '讀經計劃', '读经计划', 'title', C.ink)]),
        P('Learn about the Reformation Reading Plan and the YouVersion Bible App.'),
        row({}, [link('https://mpcbc.net/english/bible-in-a-year/', 'See the plan', C.ink, null, 'body')]),
      ], { title: 'bible plan' }),
    ], { title: 'agenda' }),
    box({ 'grid-column': { $$type: 'span', value: '7 / 13' }, t: { 'grid-column': { $$type: 'span', value: 'auto' } } }, [calendar], { title: 'events side' }),
  ]),
]);

// ---------- Newsletter: the latest issue's real front page large, past issues as small front pages ----------
const shortcode = (formId) => ({ id: hex(4), elType: 'widget', widgetType: 'shortcode', settings: { shortcode: `[fluentform id="${formId}"]` }, elements: [] });
const tab = (label, tone = C.paper) => node('e-tab', {}, { padding: dims(0, 20), 'min-height': sz(44), 'border-radius': sz(999), background: bg(tone), color: col(C.ink), 'border-width': sz(0), width: auto(), display: str('flex'), 'align-items': str('center') },
  [box({ 'flex-direction': str('row') }, [P(label, 'strong', null, {}, null, 'span')])], `Tab ${label}`);
const selected = (t, b, f) => { const s = Object.values(t.styles)[0]; s.variants.push({ meta: { breakpoint: 'desktop', state: 'e--selected' }, props: { background: bg(b), color: col(f) }, custom_css: null }); return t; };
const tabs = (cssid, tabEls, panels, menuProps) => node('e-tabs', { 'default-active-tab': num(0), ...id(cssid) }, { width: sz(100, '%'), display: str('flex'), 'flex-direction': str('column'), gap: sz(32), padding: sz(0) }, [
  node('e-tabs-menu', {}, mix({ display: str('flex'), 'flex-direction': str('row'), 'justify-content': str('flex-start'), gap: sz(8), 'flex-wrap': str('wrap'), padding: sz(0) }, menuProps), tabEls, 'Tabs menu'),
  node('e-tabs-content-area', {}, { width: sz(100, '%'), padding: sz(0) }, panels.map((p, i) => node('e-tab-content', {}, { width: sz(100, '%'), padding: sz(0) }, [p], `Tab panel ${i + 1}`)), 'Tabs panels'),
], cssid);
// A new issue: put its front page and text in the large slot, move the previous issue to the start of "Past issues", keep three there at most.
const frontPage = (file, date, mat) => div({ background: bg(C.paper), padding: sz(mat) }, [img(file, `Front page of the ${date} newsletter`, { 'aspect-ratio': str('8.5 / 11'), height: auto() })], { title: `front page ${date}` });
const pastIssue = (file, date, title, href) => grid('112px minmax(0, 1fr)', { gap: gaps(0, 20), 'align-items': str('end'), m: { 'grid-template-columns': str('112px minmax(0, 1fr)') } }, [
  frontPage(file, date, 6),
  box({ gap: sz(4) }, [H(title, 'h3', 'title', C.ink), P(date, 'fact', C.ink), row({}, [link(href, 'Read the newsletter', C.ink, null, 'body')])]),
], { title: `past issue ${date}` });
const news = section('news', 'Newsletter', { background: bg(C.page) }, [
  box({ gap: sz(16) }, [heading('news', 'Newsletter', '通訊', '通讯'), P('Stories, reflections and ministry updates from our church family.')]),
  grid('5fr 1fr 6fr', { margin: dims(40, 0, 0, 0), 'align-items': str('start'), t: { 'grid-template-columns': str('minmax(0, 1fr)') } }, [
    frontPage('newsletter-2026-07.jpg', 'July 2026', 14),
    box({ 'grid-column': { $$type: 'span', value: '3' }, gap: sz(0), t: { 'grid-column': { $$type: 'span', value: 'auto' }, margin: dims(32, 0, 0, 0) } }, [
      H('Faith, testimony and mission', 'h3', 'head', C.ink, { 'line-height': sz(1.05, 'em') }),
      P('July 2026', 'fact', C.ink, { margin: dims(8, 0, 0, 0) }),
      ...zh('issue-July2026', '2026年7月會刊：信仰見證、信靠神與宣教分享。', '2026年7月会刊：信仰见证、信靠神与宣教分享。', 'title', C.ink, { margin: dims(12, 0, 0, 0) }),
      P('The July issue shares personal testimonies, reflections on trusting God and mission experiences in Mongolia and Hong Kong.', 'body', C.ink, { 'max-width': sz(520), margin: dims(12, 0, 0, 0) }),
      row({ gap: sz(8), 'flex-wrap': str('wrap'), margin: dims(24, 0, 0, 0), m: { 'flex-direction': str('row') } }, [
        button('https://mpcbc.net/english/newsletters__trashed/newsletter-july-2026/', 'Read the newsletter', 'ink'),
        button('https://mpcbc.net/wp-content/uploads/2026/07/MPCBC-Jul-2026-Newsletter-FINAL-V3.pdf', 'Open PDF', 'paper'),
      ]),
      box({ margin: dims(56, 0, 0, 0), padding: dims(32, 0, 0, 0), gap: sz(20), 'border-width': { $$type: 'border-width-v2', value: { 'block-start': sz(5), 'inline-end': sz(0), 'block-end': sz(0), 'inline-start': sz(0) } }, 'border-style': str('dotted'), 'border-color': col(C.ink) }, [
        row({ gap: sz(8), 'align-items': str('flex-end'), m: { 'flex-direction': str('row') } }, [P('Past issues', 'strong', C.ink, {}, null, 'span'), ...zh('past-issues', '往期通訊', '往期通讯', 'strong', C.ink, {}, 'span')]),
        pastIssue('newsletter-2026-01.jpg', 'January 2026', 'January 2026 newsletter', 'https://mpcbc.net/english/newsletters__trashed/newsletter-january-2026/'),
        P('All newsletters: [link to the newsletter archive]', 'small', C.muted),
      ], { title: 'past issues' }),
    ], { title: 'latest issue text' }),
  ], { title: 'newsletter issue' }),
]);

// ---------- Visit: the clip-out coupon beside the church as a framed print, on grey ----------
const hour = (en, zhKey, hant, hans, time) => row({ 'justify-content': str('space-between'), gap: sz(16), 'max-width': sz(352), m: { 'flex-direction': str('row') } }, [
  row({ gap: sz(6), m: { 'flex-direction': str('row') } }, [P(en, 'fact', C.ink, {}, null, 'span'), ...(hant ? zh(zhKey, hant, hans, 'fact', C.ink, {}, 'span') : [])]), P(time, 'fact', C.ink, { flex: flexGrow(0, 0) }, null, 'span')]);
const connect = section('connect', 'Visit', { background: bg(C.page) }, [
  grid('repeat(12, minmax(0, 1fr))', { gap: gaps(32, 32), t: { 'grid-template-columns': str('minmax(0, 1fr)') } }, [
    div({ 'grid-column': { $$type: 'span', value: '1 / 4' }, background: bg(C.paper), padding: sz(14), t: { 'grid-column': { $$type: 'span', value: 'auto' } } }, [
      img('church-commonwealth.jpg', 'The church at 302 W. Commonwealth Ave.: a cream stucco chapel with a white steeple, palm trees and a black iron fence', { height: { d: sz(100, '%'), t: sz(360) }, 'object-position': { $$type: 'object-position', value: { x: sz(30, '%'), y: sz(50, '%') } } }),
    ], { title: 'framed print' }),
    grid('46fr 54fr', { 'grid-column': { $$type: 'span', value: '4 / 13' }, position: str('relative'), background: bg(C.accent), 'min-height': sz(512), t: { 'grid-column': { $$type: 'span', value: 'auto' } } }, [
      circle(28, C.page, { 'inset-block-start': sz(-14), 'inset-inline-start': cu('calc(46% - 14px)'), m: { display: str('none') } }),
      circle(28, C.page, { 'inset-block-end': sz(-14), 'inset-inline-start': cu('calc(46% - 14px)'), m: { display: str('none') } }),
      box({ padding: { d: sz(48), m: dims(32, 20) }, 'justify-content': str('space-between'), gap: sz(32), 'border-width': { $$type: 'border-width-v2', value: { 'block-start': sz(0), 'inline-end': sz(5), 'block-end': sz(0), 'inline-start': sz(0) } }, 'border-style': str('dotted'), 'border-color': col(C.ink) }, [
        box({ gap: sz(8) }, [H('We’d love to hear from you.', 'h2', 'head', C.ink, { m: { 'font-size': sz(32) } }), ...zh('connect', '與我們聯絡', '与我们联络', 'title', C.ink)]),
        box({ gap: sz(12) }, [
          P('302 W. Commonwealth Ave., Alhambra, CA 91801', 'lead', C.ink),
          box({ gap: sz(2) }, [hour('Mandarin', 'h-mandarin', '國語', '国语', '9:00 AM'), hour('Children', 'h-children', '兒童', '儿童', '10:00 AM'), hour('Cantonese', 'h-canto', '粵語', '粤语', '11:00 AM'), hour('English', null, null, null, '11:00 AM')]),
        ]),
        row({ gap: sz(24), 'align-items': str('center'), 'flex-wrap': str('wrap'), m: { 'flex-direction': str('row') } }, [button(MAPS, 'Get directions', 'ink', ['directions', '交通指引', '交通指引']), link('tel:+16262891284', '(626) 289-1284')]),
      ], { title: 'coupon stub' }),
      box({ padding: { d: sz(48), m: dims(32, 20) }, gap: sz(20) }, [
        P('Tell us you’re coming and someone will look out for you. Or send a prayer request. A pastor reads every one and keeps it private.'),
        tabs('mpcbc-taste-connect-tabs',
          [selected(tab('Say hello'), C.ink, C.accent), selected(tab('Prayer request'), C.ink, C.accent)].map((t) => { Object.values(t.styles)[0].variants[0].props.background = bg(C.couponInput); return t; }),
          [div({}, [shortcode(5)], { title: 'Say hello (Fluent Forms 5)' }), div({}, [shortcode(3)], { title: 'Prayer request (Fluent Forms 3)' })]),
      ], { title: 'coupon form' }),
    ], { title: 'coupon' }),
  ]),
]);

// ---------- Give: the airmail envelope ----------
const give = section('give', 'Give', { background: bg(C.paper) }, [
  box({ position: str('relative'), width: sz(100, '%'), 'max-width': cu('calc(100% * 10 / 12)'), margin: dims(0, 'auto'), m: { 'max-width': sz(100, '%') } }, [
    box({ position: str('absolute'), 'inset-block-start': sz(48), 'inset-inline-end': sz(52), 'z-index': num(3), width: sz(88), m: { 'inset-block-start': sz(24), 'inset-inline-end': sz(24), width: sz(68) } }, [
      box({ padding: sz(8), background: bg(C.paper), transform: rot(3) },
        [img('steeple-stamp.jpg', 'The church’s white steeple and cross', { 'aspect-ratio': str('3 / 4'), height: auto() })], { title: 'stamp' })], { title: 'stamp drop' }),
    box({ position: str('absolute'), 'inset-block-start': sz(44), 'inset-inline-end': sz(160), 'z-index': num(3), width: sz(96), height: sz(96), 'border-radius': sz(50, '%'), 'border-width': sz(4), 'border-style': str('dotted'), 'border-color': col(C.ink), 'align-items': str('center'), 'justify-content': str('center'), transform: rot(-12), opacity: sz(60, '%'), t: { display: str('none') } },
      [P('ALHAMBRA CA', 'fact', C.ink, { 'font-size': sz(13), 'text-align': str('center'), 'line-height': sz(1.3, 'em') }), P('91801', 'fact', C.ink, { 'font-size': sz(13), 'text-align': str('center') })], { title: 'postmark' }),
    div({ padding: sz(12), background: bgImg('airmail-stripes.svg', 'repeat', [48, 48]) }, [
      grid('repeat(2, minmax(0, 1fr))', { background: bg(C.paper), padding: { d: sz(56), m: dims(96, 24, 40, 24) }, gap: gaps(48, 64) }, [
        box({ gap: sz(0) }, [
          heading('give', 'Give', '奉獻', '奉献'),
          P('Make checks payable to MPCBC and mail to the church address.', 'body', C.ink, { margin: dims(16, 0, 0, 0) }),
          ...zh('give-line', '奉獻支票抬頭請寫 MPCBC，郵寄至教會地址。', '奉献支票抬头请写 MPCBC，邮寄至教会地址。', 'body', C.ink, { margin: dims(8, 0, 0, 0) }),
          row({ gap: sz(24), 'align-items': str('center'), 'flex-wrap': str('wrap'), margin: dims(32, 0, 0, 0), m: { 'flex-direction': str('row') } }, [
            button('https://www.paypal.com/donate/?hosted_button_id=PQJTK3MEFJ9M2', 'Give with PayPal', 'accent', ['paypal', '透過 PayPal 奉獻', '透过 PayPal 奉献']),
            link(`${SITE}/give/`, 'Ways to give', C.ink, ['ways', '奉獻方式', '奉献方式']),
          ]),
        ], { title: 'envelope message' }),
        box({ 'align-self': str('end'), gap: sz(6) }, [
          P('Pay to MPCBC, mail to', 'fact', C.muted),
          P('Monterey Park Chinese Baptist Church', 'lead', C.ink),
          ...zh('give-church', '慕道園華人浸信會', '慕道园华人浸信会', 'body', C.muted),
          P('302 W. Commonwealth Ave., Alhambra, CA 91801', 'lead', C.ink),
        ], { title: 'envelope address' }),
      ], { title: 'envelope paper' }),
    ], { title: 'airmail edge' }),
  ], { title: 'envelope' }),
]);

const page = [hero, sunday, kids, pastors, life, events, news, give, connect];

// ---------- motion: Elementor free Interactions (fade / slide on load or scroll-in, ease-in only) ----------
const ixItem = (trigger, effect, direction, delay, duration) => ({ $$type: 'interaction-item', value: {
  trigger: str(trigger), animation: { $$type: 'animation-preset-props', value: { effect: str(effect), type: str('in'), direction: str(direction),
    timing_config: { $$type: 'timing-config', value: { duration: sz(duration, 'ms'), delay: sz(delay, 'ms') } }, config: { $$type: 'config-v2', value: { easing: str('easeIn') } } } } } });
// Elementor's slide only moves (100px), so a slide also gets a fade running alongside it
const motion = (n, effect, direction = '', delay = 0, trigger = 'scrollIn', duration = 700) => { n.interactions = { version: 1,
  items: [ixItem(trigger, effect, direction, delay, duration), ...(effect === 'slide' ? [ixItem(trigger, 'fade', '', delay, duration)] : [])] }; };
const byTitle = (re) => { const out = []; (function w(ns) { for (const n of ns) { if (re.test(n.editor_settings?.title || '')) out.push(n); w(n.elements || []); } })(page); return out; };
const byCss = (id) => byTitle(/./).find((n) => n.settings?._cssid?.value === id);
// hero: name, Chinese name, slogan, button and times rise in one after another on load
['mpcbc-hero_heading', 'mpcbc-copy-zh-hant-mpcbc-rail_church_name_zh', 'mpcbc-copy-zh-hans-mpcbc-rail_church_name_zh', 'mpcbc-rail_intro', 'mpcbc-hero_link_plan', 'sunday-times']
  .forEach((id, i) => motion(byCss(id), 'slide', 'bottom', [0, 90, 90, 180, 270, 360][i], 'load', 900));
byTitle(/^heading /).forEach((n) => motion(n, 'fade'));
byTitle(/^service /).forEach((n, i) => motion(n, 'fade', '', i * 90));
['kids card', 'letter', 'programme sheet', 'agenda', 'calendar page', 'envelope'].forEach((t) => motion(byTitle(new RegExp('^' + t + '$'))[0], 'fade'));
motion(byTitle(/^newsletter issue$/)[0], 'fade');
motion(byTitle(/^coupon$/)[0], 'slide', 'bottom', 0, 'scrollIn', 900);
motion(byTitle(/^stamp drop$/)[0], 'slide', 'top', 600);
const moved = byTitle(/./).filter((n) => n.interactions).length;
// 6 hero + 7 headings + 3 services + 6 blocks + newsletter issue + coupon + stamp
if (moved !== 25) throw Error('expected 25 animated elements, got ' + moved);

// ---------- footer (shared template 16): the existing columns and bottom bar, weights to 400/500 ----------
const footer = structuredClone(read('content-source/before-taste-rebuild/16.json').meta._elementor_data);
const reweightAll = (n) => { for (const st of Object.values(n.styles || {})) for (const v of st.variants) { const w = v.props['font-weight']?.value; if (w && +w > 500) v.props['font-weight'] = str('500'); } (n.elements || []).forEach(reweightAll); };
footer.forEach(reweightAll);
// the large church name was removed (owner, 28 Oct): the Home page already shows it several times, and the footer's first column names the church
fs.writeFileSync(new URL('content-source/taste-footer.json', root), JSON.stringify(footer));

// self-check: every Chinese copy has its pair, ids are unique
const ids = []; const zhIds = [];
(function walk(ns) { for (const n of ns) { ids.push(n.id); const c = n.settings?._cssid?.value; if (c?.startsWith('mpcbc-copy-zh-')) zhIds.push(c); walk(n.elements || []); } })(page);
if (new Set(ids).size !== ids.length) throw Error('duplicate element ids');
for (const c of zhIds.filter((c) => c.includes('-zh-hant-taste-'))) if (!zhIds.includes(c.replace('-zh-hant-', '-zh-hans-'))) throw Error('unpaired ' + c);
if (new Set(zhIds).size !== zhIds.length) throw Error('duplicate copy ids: ' + zhIds.filter((c, i) => zhIds.indexOf(c) !== i));

fs.writeFileSync(new URL('content-source/taste-home.json', root), JSON.stringify(page));
console.log(`taste-home.json: ${ids.length} elements, ${zhIds.length / 2} Chinese copy pairs, calendar ${monthName}`);
