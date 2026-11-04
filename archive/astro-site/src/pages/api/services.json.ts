/**
 * Live service state.
 *
 * The cron Worker (worker/livestream-sync.js) polls YouTube and writes the
 * matched result to KV under `services`. This is the only thing that reads
 * it: the board paints build-time data first, then refreshes from here.
 *
 * Falls back to the sample file when KV has nothing yet — local dev, a
 * namespace that hasn't been created, or the window before the first cron
 * fires. The board keeps working and shows the sample rather than an empty
 * shell.
 *
 * The binding comes from `cloudflare:workers`, NOT Astro.locals.runtime.env
 * — that was removed in Astro 6 and now throws when touched. Only reachable
 * because this route is `prerender = false`.
 */
import { env } from 'cloudflare:workers';
import type { APIRoute } from 'astro';
import sample from '../../data/services.json';

// Must run per request; the whole point is that it isn't baked in.
export const prerender = false;

export const GET: APIRoute = async () => {
  let body: string | null = null;

  // Structural type rather than `KVNamespace`: @cloudflare/workers-types
  // isn't installed, so naming it would break `astro check` for the one
  // method we call. `wrangler types` would generate the real thing.
  const kv = (env as { MPCBC?: { get(k: string, t: 'text'): Promise<string | null> } }).MPCBC;
  if (kv) {
    // A KV miss is normal and falls back quietly. A KV *error* is not, so
    // it gets logged rather than swallowed — an earlier version caught
    // everything here and silently served the sample forever.
    try {
      body = await kv.get('services', 'text');
    } catch (error) {
      console.error('[api/services] KV read failed', error);
    }
  } else {
    console.warn('[api/services] no MPCBC binding — serving sample data');
  }

  return new Response(body ?? JSON.stringify(sample), {
    headers: {
      'content-type': 'application/json; charset=utf-8',
      // The cron runs every 2 minutes on Sunday morning. A short edge cache
      // keeps a Sunday traffic spike off KV without showing a stale board.
      'cache-control': 'public, max-age=30, s-maxage=60',
      // Tells the board whether it got live data or the fallback.
      'x-services-source': body ? 'kv' : 'sample',
    },
  });
};
