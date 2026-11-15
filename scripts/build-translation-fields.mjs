// One-time move to the mpcbc-translations fields (3 Nov 2026).
// Home 21 + footer 16: each Traditional/Simplified element pair becomes one Chinese element;
//   the Simplified copy is dropped when OpenCC gives the same text, else kept as its 简体 field.
// Give: 233 (繁體) and 236 (简体) are folded into English page 65's 繁體/简体 fields.
// Reads content-source/before-translation-fields/*.json, writes content-source/translation-fields/*.json.
import { readFileSync, writeFileSync, mkdirSync } from 'node:fs';
import { createRequire } from 'node:module';

const require = createRequire(import.meta.url);
const simplify = require('../hello-mpcbc/vendor/opencc-js/t2cn.js').Converter({ from: 'hk', to: 'cn' });
const root = new URL('../content-source/', import.meta.url);
const read = (id) => JSON.parse(readFileSync(new URL(`before-translation-fields/${id}.json`, root), 'utf8'));
const TEXT = { 'e-heading': 'title', 'e-paragraph': 'paragraph', 'e-button': 'text' };
const type = (n) => n.widgetType || n.elType;
const text = (n) => n.settings?.[TEXT[type(n)]]?.value ?? '';
const field = (value) => ({ $$type: 'string', value });
const report = {};

function walk(nodes, fn, parent = null) {
  for (const n of nodes) { fn(n, parent); walk(n.elements || [], fn, n); }
}

function mergePairs(id) {
  const data = read(id);
  const pairs = {};
  walk(data, (n, parent) => {
    const m = /^mpcbc-copy-zh-(hant|hans)-(.+)$/.exec(n.settings?._cssid?.value || '');
    if (m) (pairs[m[2]] ||= {})[m[1]] = { n, parent };
  });
  const out = { merged: 0, overrides: [] };
  for (const [key, { hant, hans }] of Object.entries(pairs)) {
    if (!hant || !hans) throw new Error(`${id}: ${key} has no ${hant ? 'Simplified' : 'Traditional'} twin`);
    if (type(hant.n) !== type(hans.n) || !TEXT[type(hant.n)]) throw new Error(`${id}: ${key} is not a matching text pair`);
    if (simplify(text(hant.n)) !== text(hans.n)) {
      hant.n.settings.mpcbc_zh_hans = field(text(hans.n));
      out.overrides.push({ key, auto: simplify(text(hant.n)), kept: text(hans.n) });
    }
    delete hant.n.settings._cssid;
    if (hant.n.editor_settings?.title) hant.n.editor_settings.title = hant.n.editor_settings.title.replace(/^Traditional \/ 繁體中文/, '中文');
    hans.parent.elements = hans.parent.elements.filter((c) => c !== hans.n);
    out.merged++;
  }
  report[id] = out;
  return data;
}

function mergeGive() {
  const [en, hant, hans] = [65, 233, 236].map(read);
  const flat = (d) => { const a = []; walk(d, (n) => a.push(n)); return a; };
  const [a, b, c] = [en, hant, hans].map(flat);
  if (a.length !== b.length || a.length !== c.length) throw new Error('Give pages differ in structure');
  const out = { fields: [], linksDiffer: [] };
  a.forEach((n, i) => {
    if (type(n) !== type(b[i]) || type(n) !== type(c[i])) throw new Error(`Give element ${i} differs in type`);
    if (!TEXT[type(n)]) return;
    const [t, h, s] = [n, b[i], c[i]].map(text);
    if (h !== t) n.settings.mpcbc_zh_hant = field(h);
    if (s !== t && s !== simplify(h)) n.settings.mpcbc_zh_hans = field(s);
    const link = (x) => x.settings?.link?.value?.destination?.value?.value ?? x.settings?.link?.value?.destination?.value ?? null;
    if (JSON.stringify(link(n)) !== JSON.stringify(link(b[i]))) out.linksDiffer.push({ en: t, en_link: link(n), zh_link: link(b[i]) });
    out.fields.push({ en: t, hant: h !== t ? h : null, hans: s !== t && s !== simplify(h) ? s : null });
  });
  report[65] = out;
  return en;
}

mkdirSync(new URL('translation-fields/', root), { recursive: true });
const save = (id, d) => writeFileSync(new URL(`translation-fields/${id}.json`, root), JSON.stringify(d));
save(21, mergePairs(21));
save(16, mergePairs(16));
save(65, mergeGive());
writeFileSync(new URL('translation-fields/report.json', root), JSON.stringify(report, null, 2));
console.log(JSON.stringify(report, null, 2));
