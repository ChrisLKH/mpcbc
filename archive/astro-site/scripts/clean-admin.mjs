/**
 * Remove the locally-built Tina admin before a production build.
 *
 * `tinacms dev` writes a local-mode admin SPA into public/admin/. Astro
 * copies public/ wholesale, so on any machine that has run `npm run
 * dev:tina`, a plain `npm run build` would ship that shell to the live
 * site — a public /admin that tries to reach localhost:4001 and fails.
 * It's gitignored, so CI never sees it and the problem only appears when
 * someone deploys from their own laptop. That's the worst kind of bug.
 *
 * `npm run build:tina` deliberately rebuilds the admin after this runs,
 * so the Tina Cloud path is unaffected. The adapter re-emits
 * admin/bridge.js at build:done either way, which is what visual editing
 * on the deployed site actually needs.
 */
import { rmSync } from 'node:fs';

rmSync('public/admin', { recursive: true, force: true });
console.log('Removed public/admin (local Tina admin) before build.');
