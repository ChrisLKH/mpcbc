// Checks the PHP 繁 → 简 port against opencc-js on all Chinese text in content-source/*.txt, plus the brief's word swaps.
// Run: node scripts/check-t2s.mjs
import { createRequire } from 'node:module';
import { readdirSync, readFileSync, writeFileSync, mkdtempSync } from 'node:fs';
import { execFileSync } from 'node:child_process';
import { tmpdir } from 'node:os';
import { join } from 'node:path';

const root = new URL('..', import.meta.url).pathname.replace(/^\/(\w:)/, '$1');
const php = 'C:/Users/chris/AppData/Roaming/Local/lightning-services/php-8.2.29+0/bin/win64/php.exe';
const OpenCC = createRequire(import.meta.url)(join(root, 'plugins/mpcbc-translations/opencc-js/t2cn.js'));
const convert = OpenCC.Converter({ from: 'hk', to: 'cn' });

const dir = join(root, 'content-source');
const lines = [...new Set(readdirSync(dir).filter((f) => f.endsWith('.txt'))
  .flatMap((f) => readFileSync(join(dir, f), 'utf8').split(/\r?\n/)).filter((l) => /\p{Script=Han}/u.test(l)))];

const tmp = mkdtempSync(join(tmpdir(), 't2s-'));
writeFileSync(join(tmp, 'in.json'), JSON.stringify([...lines, '國語堂', '國語崇拜', '國語（普通話）', '粵語', '華人教會']));
const out = JSON.parse(execFileSync(php, ['-r', `require '${root}/plugins/mpcbc-translations/t2s.php';
  echo json_encode(array_map('mpcbc_t2s', json_decode(file_get_contents('${tmp.replace(/\\/g, '/')}/in.json'))), JSON_UNESCAPED_UNICODE);`]).toString());

// Lines containing 国语 also get the brief's word swap, so compare those after applying it to the opencc-js result.
const swap = (s) => s.replace(/国语(?![（(]普通话|堂)/gu, '国语（普通话）');
const bad = lines.filter((l, i) => swap(convert(l)) !== out[i]);
const extra = out.slice(lines.length);
const want = ['国语堂', '国语（普通话）崇拜', '国语（普通话）', '粤语', '华人教会'];
console.log(`${lines.length - bad.length}/${lines.length} lines match opencc-js`);
bad.slice(0, 5).forEach((l) => console.log('  in :', l.slice(0, 80), '\n  js :', swap(convert(l)).slice(0, 80), '\n  php:', out[lines.indexOf(l)].slice(0, 80)));
want.forEach((w, i) => extra[i] !== w && console.log(`swap: got ${extra[i]}, want ${w}`));
process.exit(bad.length || extra.some((e, i) => e !== want[i]) ? 1 : 0);
