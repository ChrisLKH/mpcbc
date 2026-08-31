/**
 * The Tina island route must run per request, which a fully static build
 * can't do without an adapter. This moves it out of the way for
 * `npm run build` and back in for `npm run dev:tina`.
 */
import { existsSync, mkdirSync, renameSync, rmdirSync } from 'node:fs';
const LIVE = 'src/pages/tina-island';
const PARKED = 'tina-island-route-dev';
const mode = process.argv[2];

if (mode === 'off' && existsSync(`${LIVE}/[name].ts`)) {
  mkdirSync(PARKED, { recursive: true });
  renameSync(`${LIVE}/[name].ts`, `${PARKED}/[name].ts`);
  try { rmdirSync(LIVE); } catch {}
  console.log('Island route parked — static build enabled.');
} else if (mode === 'on' && existsSync(`${PARKED}/[name].ts`)) {
  mkdirSync(LIVE, { recursive: true });
  renameSync(`${PARKED}/[name].ts`, `${LIVE}/[name].ts`);
  console.log('Island route mounted — Tina visual editing enabled.');
} else {
  console.log('Nothing to do.');
}
