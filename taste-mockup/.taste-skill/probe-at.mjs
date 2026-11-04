// Debug helper: which elements sit at page height Y? node .taste-skill/probe-at.mjs about-1.html 1440 7000 [7400]
import { Browser } from '../../.agents/skills/design-taste-frontend/scripts/lib/cdp.mjs';
import { pathToFileURL } from 'node:url';
import { resolve } from 'node:path';
const [file, w, ...ys] = process.argv.slice(2);
const b = await Browser.launch({ width: +w, height: 900 });
const p = await b.newPage({ width: +w, height: +w < 500 ? 844 : 900, mobile: +w < 500 });
await p.goto(pathToFileURL(resolve(file)).href, { settleMs: 3000 });
const out = await p.eval((ys) => {
  document.documentElement.style.scrollBehavior = 'auto'; document.querySelectorAll('.r').forEach((e) => e.classList.add('is-in'));
  return ys.map((y) => { scrollTo(0, y - 300); const vy = 300; const hits = [0.1, 0.3, 0.5, 0.7, 0.9].map((fx) => { const el = document.elementFromPoint(innerWidth * fx, vy); return el ? el.tagName + '.' + el.className.toString().slice(0, 40) + (el.closest('section') ? ' in #' + el.closest('section').id : '') : null; }); return { y, h: document.documentElement.scrollHeight, hits }; });
}, ys.map(Number));
console.log(JSON.stringify(out, null, 1));
await b.close?.(); process.exit(0);
