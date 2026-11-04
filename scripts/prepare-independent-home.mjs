import fs from 'node:fs/promises';
import {createRequire} from 'node:module';
const require = createRequire(import.meta.url);
const simplify = require('../.content-tools/node_modules/opencc-js').Converter({from:'hk',to:'cn'});
const root = new URL('../', import.meta.url);
// One-time starter only. Never sync this over subsequent editorial wording.
for (const [source, target] of [['home-content-filled.json','zh-hans-home.json'],['footer-content-filled.json','zh-hans-footer.json']]) {
  const document = JSON.parse(await fs.readFile(new URL(`elementor/${source}`,root),'utf8'));
  function convert(nodes) {
    for (const node of nodes) {
      for (const key of ['title','paragraph','text','alt']) {
        const setting = node.settings?.[key];
        if (setting && typeof setting === 'object' && typeof setting.value === 'string') setting.value = simplify(setting.value).replaceAll('zh-Hant','zh-Hans');
      }
      convert(node.elements ?? []);
    }
  }
  convert(document.content);
  document.title = target.includes('home') ? 'Home · 简体中文' : 'MPCBC – Footer · 简体中文';
  const path = new URL(`elementor/${target}`,root);
  try { await fs.access(path); throw new Error(`${target} already exists; reconcile editor changes first.`); }
  catch(error) { if (error.code !== 'ENOENT') throw error; }
  await fs.writeFile(path,JSON.stringify(document,null,2));
}
console.log('Prepared independent, editable Simplified Home and footer starter content.');
