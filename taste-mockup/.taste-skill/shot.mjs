// Screenshot a local HTML page full-length: node .taste-skill/shot.mjs <in.html> <out.jpg> <width>
import { Browser } from '../../.agents/skills/design-taste-frontend/scripts/lib/cdp.mjs';
import { pathToFileURL } from 'node:url';
import { resolve } from 'node:path';
const [inp, out, w] = process.argv.slice(2);
const b = await Browser.launch({ width: +w, height: 1200 });
const p = await b.newPage({ width: +w, height: 1200 });
await p.goto(pathToFileURL(resolve(inp)).href, { settleMs: 1500 });
await p.screenshot({ path: resolve(out), fullPage: true, quality: 80 });
process.exit(0);
