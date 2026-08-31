import { defineConfig } from 'astro/config';
import react from '@astrojs/react';
import markdoc from '@astrojs/markdoc';
import keystatic from '@keystatic/astro';

// Keystatic's admin needs server routes. In local storage mode it only runs
// during `npm run dev`, which keeps `npm run build` output fully static.
// To let editors log in on the live site, switch keystatic.config.ts to
// GitHub storage and add the Cloudflare adapter here.
const isDev = process.env.NODE_ENV !== 'production';

export default defineConfig({
  site: 'https://mpcbc.org',
  integrations: [react(), markdoc(), ...(isDev ? [keystatic()] : [])],
  output: 'static',
  vite: {
    server: { allowedHosts: true },
  },
});
