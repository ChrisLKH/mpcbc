import { defineConfig } from 'astro/config';
import react from '@astrojs/react';
import tina from '@tinacms/astro/integration';
import { tinaAdminDevRedirect } from '@tinacms/astro/vite';
import cloudflare from '@astrojs/cloudflare';

// `output: 'static'` + an adapter is Astro's hybrid mode: every route
// prerenders unless it opts out with `export const prerender = false`.
// Only two routes opt out, so the deploy is a static site with two live
// endpoints rather than a server rendering every page:
//
//   /api/services.json    reads the livestream state the cron Worker put in KV
//   /tina-island/[name]   re-renders a region while someone edits in Tina
//
// Without an adapter neither can exist, which is what the old
// scripts/toggle-island.mjs was working around.
export default defineConfig({
  site: 'https://mpcbc.org',
  // Tina is the only CMS. Keystatic used to be added here for dev only,
  // which meant the dev dependency graph differed from the build's — the
  // main reason `npm run build` could pass while `npm run dev` fell over
  // in the Vite dep optimizer.
  integrations: [react(), tina()],
  output: 'static',

  // Nothing on the site uses Astro.session. Left enabled, the adapter
  // injects a `SESSION` KV binding into dist/server/wrangler.json with no
  // namespace id, and `wrangler deploy` rejects the config. Turning it
  // off is better than provisioning a KV namespace nobody reads.
  session: false,

  adapter: cloudflare({
    // Every image on the site is a plain <img src>, so there is nothing to
    // transform at runtime and no reason to provision a Cloudflare Images
    // binding.
    //
    // Must be 'compile', not 'passthrough'. Passthrough pulls in
    // `astro/assets/services/noop`, which Vite only discovers partway
    // through dev startup; the resulting re-optimize invalidates a chunk
    // the workerd runner is already holding and the dev server dies with
    // "The file does not exist at .../deps_ssr/server-*.js". Builds are
    // unaffected either way, so the failure looks baffling: `npm run build`
    // passes while `npm run dev` won't boot.
    imageService: 'compile',
  }),
  vite: {
    server: { allowedHosts: true },
    // Without this a bare /admin 404s in dev — Vite serves public/admin/
    // but won't resolve its directory index.
    plugins: [tinaAdminDevRedirect()],
  },
});
