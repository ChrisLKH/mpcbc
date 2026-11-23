// Shared builders for the v10 pages: native Atomic Elementor JSON from the DESIGN-v10 pieces (used by build-v10-home.mjs and build-v10-pages.mjs).
// Every text is an Atomic Heading / Paragraph / Button with the 繁 slot (mpcbc_zh_hant); colours only from the global variables.
import crypto from "node:crypto";

const SITE = 'http://mpcbc.local';
const hex = (n) => crypto.randomBytes(n).toString('hex');

// ---------- tokens (Atomic global variables; values set by apply-v10-globals.php) ----------
const C = {
  ink: 'e-gv-c70cdc4', bg: 'e-gv-f7cfe2a', surface: 'e-gv-1984c94', fill: 'e-gv-824ebc4', line: 'e-gv-3cc97d2',
  muted: 'e-gv-7317e11', night: 'e-gv-32ae709', nightText: 'e-gv-v10ntx', accent: 'e-gv-v10acc', accentHover: 'e-gv-55a0d95',
  gold: 'e-gv-0c168a6', kids: 'e-gv-428b859', mint: 'e-gv-v10mnt', sky: 'e-gv-v10sky',
  white: 'e-gv-d98b04b', white88: 'e-gv-803b57a', white75: 'e-gv-fe52081', white30: 'e-gv-436185d', white14: 'e-gv-56ef666',
};
const IMG = { church: 545, vbsWave: 543, vbsFloor: 544, vbsCrafts: 542, stamp: 546, caleb: 547, pastorPh: 80, eventPh: 1324, newsletter: 587 };

// ---------- prop values ----------
const sz = (size, unit = 'px') => ({ $$type: 'size', value: { size, unit } });
const cu = (v) => sz(v, 'custom');
const auto = () => sz('', 'auto');
const str = (value) => ({ $$type: 'string', value });
const num = (value) => ({ $$type: 'number', value });
const col = (v) => ({ $$type: 'global-color-variable', value: v });
const side = (v) => (v === 'auto' ? auto() : typeof v === 'string' ? cu(v) : sz(v));
const dims = (t, r = t, b = t, l = r) => ({ $$type: 'dimensions', value: { 'block-start': side(t), 'inline-end': side(r), 'block-end': side(b), 'inline-start': side(l) } });
const bg = (v) => ({ $$type: 'background', value: { color: col(v) } });
const clear = { $$type: 'background', value: { color: { $$type: 'color', value: 'transparent' } } }; // Atomic buttons have a default fill
const border = (w, color, style = 'solid') => ({ 'border-width': typeof w === 'number' ? sz(w) : { $$type: 'border-width-v2', value: Object.fromEntries(Object.entries(w).map(([k, v]) => [k, sz(v)])) }, 'border-style': str(style), 'border-color': col(color) });
const topLine = (w, color, style) => border({ 'block-start': w, 'inline-end': 0, 'block-end': 0, 'inline-start': 0 }, color, style);
const bottomLine = (w, color, style) => border({ 'block-start': 0, 'inline-end': 0, 'block-end': w, 'inline-start': 0 }, color, style);
const flex = (g, s, b) => ({ $$type: 'flex', value: { flexGrow: num(g), flexShrink: num(s), flexBasis: typeof b === 'string' ? cu(b) : sz(b) } });
const rot = (deg) => ({ $$type: 'transform', value: { 'transform-functions': { $$type: 'transform-functions', value: [
  { $$type: 'transform-rotate', value: { x: sz(0, 'deg'), y: sz(0, 'deg'), z: sz(deg, 'deg') } }] } } });
const gradient = (stops) => ({ $$type: 'background', value: { 'background-overlay': { $$type: 'background-overlay', value: [{ $$type: 'background-gradient-overlay', value: {
  type: str('linear'), angle: num(180),
  stops: { $$type: 'gradient-color-stop', value: stops.map(([c, o]) => ({ $$type: 'color-stop', value: { color: { $$type: 'color', value: c }, offset: num(o) } })) } } }] } } });

// ---------- styles: flat props = desktop; d / t / m = breakpoints; hover = desktop :hover ----------
const BP = { d: 'desktop', t: 'tablet', m: 'mobile' };
const merge = (...ps) => ps.reduce((a, p) => {
  for (const [k, v] of Object.entries(p || {})) a[k] = ['d', 't', 'm', 'hover'].includes(k) ? { ...(a[k] || {}), ...v } : v;
  return a;
}, {});
function variants(p) {
  const { d = {}, t = {}, m = {}, hover = {}, ...flat } = p;
  const out = [];
  const add = (props, breakpoint, state = null) => { if (Object.keys(props).length) out.push({ meta: { breakpoint, state }, props, custom_css: null }); };
  add({ ...flat, ...d }, 'desktop'); add(t, 'tablet'); add(m, 'mobile'); add(hover, 'desktop', 'hover');
  return out;
}
function node(type, settings, style, children = [], title) {
  const cls = `e-${hex(4)}-${hex(2)}`;
  const widget = ['e-heading', 'e-paragraph', 'e-button', 'e-image'].includes(type);
  const n = { id: hex(4), elType: widget ? 'widget' : type, settings: { classes: { $$type: 'classes', value: [cls] }, ...settings }, elements: children,
    styles: { [cls]: { id: cls, label: 'local', type: 'class', variants: variants(style) } }, editor_settings: { title: (title || type).slice(0, 48) }, version: '0.0' };
  if (widget) n.widgetType = type; else n.isInner = false;
  return n;
}
const cssid = (v) => (v ? { _cssid: str(v) } : {});
const url = (href) => (href.startsWith('/') ? SITE + href : href);
const linkSet = (href) => ({ link: { $$type: 'link', value: { destination: { $$type: 'url', value: url(href) }, isTargetBlank: { $$type: 'boolean', value: false } } } });

// ---------- layout ----------
const box = (style, kids, o = {}) => node('e-flexbox', { ...cssid(o.id), ...(o.href ? { ...linkSet(o.href), tag: str('a') } : {}) },
  merge({ display: str('flex'), 'flex-direction': str('column'), width: auto(), padding: sz(0), 'min-width': sz(0) }, style), kids, o.title || o.id || 'box'); // Atomic flexboxes default to 100% wide; rows need their items sized by content
const row = (style, kids, o) => box(merge({ 'flex-direction': str('row'), 'flex-wrap': str('wrap') }, style), kids, o);
const grid3 = (kids, o = {}) => node('e-div-block', cssid(o.id), { display: str('grid'), 'grid-template-columns': str('repeat(auto-fit, minmax(min(100%, 19rem), 1fr))'), gap: sz(20), padding: sz(0), 'min-width': sz(0) }, kids, o.title || 'grid of cards');
const wrap = (kids, style) => box(merge({ width: sz(100, '%'), 'max-width': sz(1200), margin: dims(0, 'auto') }, style), kids, { title: 'content width' });
const section = (id, title, style, kids) => box(merge({ width: sz(100, '%'), padding: dims('clamp(64px, 9vw, 104px)', 20), 'scroll-margin-top': sz(76) }, style), [wrap(kids)], { id, title });

// ---------- text: s = 'English' or ['English', '繁體'] ----------
const tr = (s) => (Array.isArray(s) ? s : [s, '']);
const zhField = (en, hant) => (hant && hant !== en ? { mpcbc_zh_hant: str(hant) } : {});
const esc = (s) => s.replaceAll('&', '&amp;');
const reset = { margin: sz(0), 'min-width': sz(0) };
const H = (tag, s, style, id) => { const [en, hant] = tr(s); return node('e-heading', { tag: str(tag), title: { $$type: 'escaped-html', value: esc(en) }, ...zhField(en, hant), ...cssid(id) }, merge(reset, style), [], en); };
const P = (s, style, id) => { const [en, hant] = tr(s); return node('e-paragraph', { paragraph: { $$type: 'escaped-html', value: esc(en) }, tag: str('p'), ...zhField(en, hant), ...cssid(id) }, merge(reset, style), [], en.replace(/<[^>]+>/g, '')); };

// type scale (DESIGN-v10 › Type)
const T = {
  h2: { 'font-size': cu('clamp(2.25rem, 1.5rem + 2.6vw, 3.6rem)'), 'font-weight': str('500'), 'letter-spacing': sz(-0.02, 'em'), 'line-height': sz(1.08, 'em'), color: col(C.ink) },
  alt: { 'font-size': cu('clamp(18px, 1rem + 0.4vw, 22px)'), 'font-weight': str('500'), 'line-height': sz(1.4, 'em'), color: col(C.muted), margin: dims(8, 0, 0, 0) },
  label: { 'font-size': sz(12), 'font-weight': str('600'), 'letter-spacing': sz(0.12, 'em'), 'text-transform': str('uppercase'), 'line-height': sz(1.4, 'em'), color: col(C.accent) },
  body: { 'font-size': sz(17), 'font-weight': str('400'), 'line-height': sz(1.55, 'em') },
  card: { 'font-size': sz(23), 'font-weight': str('500'), 'letter-spacing': sz(-0.01, 'em'), 'line-height': sz(1.3, 'em'), color: col(C.ink) },
};
// section heading + the other-language title under it: [en, hant] and the line under it [繁 shown on EN pages, English shown on 繁/简]
const heading = ([en, hant], style = {}, altStyle = {}) => box({ gap: sz(0) }, [H('h2', [en, hant], merge(T.h2, style)), P([hant, en], merge(T.alt, altStyle))], { title: `heading ${en}` });

// buttons (Atomic Button): accent / line / whiteLine / ink, 50px tall, fully round; text links are buttons without a pill
const BTN = {
  accent: { background: bg(C.accent), color: col(C.white), hover: { background: bg(C.accentHover) } },
  line: { ...border(1, C.ink), background: clear, color: col(C.ink), hover: { background: bg(C.fill) } },
  whiteLine: { ...border(1, C.white75), background: clear, color: col(C.white), hover: { background: bg(C.white14) } },
  ink: { background: bg(C.ink), color: col(C.white), hover: { background: bg(C.night) } },
};
const btnBase = { display: str('inline-flex'), 'align-items': str('center'), 'justify-content': str('center'), 'min-height': sz(50), padding: dims(0, 26),
  'border-radius': sz(999), 'font-size': sz(16), 'font-weight': str('500'), 'line-height': sz(1.2, 'em'), 'text-decoration': str('none'), 'text-align': str('center') };
const button = (href, s, tone = 'accent', style = {}, id) => { const [en, hant] = tr(s); return node('e-button', { text: { $$type: 'escaped-html', value: esc(en) }, ...linkSet(href), tag: str('a'), ...zhField(en, hant), ...cssid(id) }, merge(btnBase, BTN[tone], style), [], `button ${en}`); };
const textLink = (href, s, color = C.ink, style = {}, id) => { const [en, hant] = tr(s); return node('e-button', { text: { $$type: 'escaped-html', value: esc(en) }, ...linkSet(href), tag: str('a'), ...zhField(en, hant), ...cssid(id) },
  merge({ display: str('inline-flex'), 'align-items': str('center'), 'min-height': sz(44), padding: sz(0), background: clear, 'font-size': sz(17), 'font-weight': str('500'), color: col(color), 'text-decoration': str('none'), hover: { 'text-decoration': str('underline') } }, style), [], `link ${en}`); };
const img = (id, style, title) => node('e-image', { image: { $$type: 'image', value: { src: { $$type: 'image-src', value: { id: { $$type: 'image-attachment-id', value: id } } }, size: str('full') } } },
  merge({ display: str('block'), width: sz(100, '%'), 'max-width': sz(100, '%'), height: auto(), 'object-fit': str('cover'), margin: sz(0) }, style), [], title);
// section top: heading on the left, optional link on the right
const headRow = (title, link) => row({ 'justify-content': str('space-between'), 'align-items': str('flex-end'), gap: { $$type: 'layout-direction', value: { row: sz(16), column: sz(48) } }, margin: dims(0, 0, 44, 0) }, [heading(title), ...(link ? [link] : [])], { title: 'section head' });

export { SITE, hex, C, IMG, sz, cu, auto, str, num, col, side, dims, bg, clear, border, topLine, bottomLine, flex, rot, gradient, BP, merge, variants, node, cssid, url, linkSet, box, row, grid3, wrap, section, tr, zhField, esc, reset, H, P, T, heading, BTN, btnBase, button, textLink, img, headRow };
