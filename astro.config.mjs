import { defineConfig } from 'astro/config';
import react from '@astrojs/react';
import markdoc from '@astrojs/markdoc';
import keystatic from '@keystatic/astro';
import tina from '@tinacms/astro/integration';
import { tinaAdminDevRedirect } from '@tinacms/astro/vite';

// Keystatic's admin needs server routes. In local storage mode it only runs
// during `npm run dev`, which keeps `npm run build` output fully static.
// To let editors log in on the live site, switch keystatic.config.ts to
// GitHub storage and add the Cloudflare adapter here.
const isDev = process.env.NODE_ENV !== 'production';

export default defineConfig({
  site: 'https://mpcbc.org',
  integrations: [react(), markdoc(), tina(), ...(isDev ? [keystatic()] : [])],
  output: 'static',
  vite: {
    server: { allowedHosts: true },
    // Without this a bare /admin 404s in dev — Vite serves public/admin/
    // but won't resolve its directory index.
    plugins: [tinaAdminDevRedirect()],
  },
});
