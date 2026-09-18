# Development

Everything here is for working on the code. **Nobody editing the website's
words and pictures needs any of it** — they use the app described in
[README.md](README.md) and [GUIDE.md](GUIDE.md), which starts the same
servers and runs the same build.

Reach for this file when the app is not the right tool: setting up a new
Cloudflare environment, debugging a dev server that will not boot, or
testing the livestream cron by hand.

---

## Run it

Two terminals, in this order.

```bash
npm install

npx tinacms dev      # terminal 1 — Tina's GraphQL server on :4001, builds /admin
npm run dev          # terminal 2 — http://localhost:4321
```

Wait for Tina to finish *"Indexing local files"* before starting Astro.

**Use `http://localhost:4321`, not `127.0.0.1`** — the dev server binds IPv6.

| Command | What it does |
|---|---|
| `npm run cms` | Tina's GraphQL server + builds the `/admin` SPA |
| `npm run dev` | The site |
| `npm run build` | Production build into `dist/` |
| `npm run preview` | Build, then serve it through the real Worker runtime |
| `npm run deploy` | Build and deploy the site Worker |
| `npm run deploy:sync` | Deploy the livestream cron Worker |
| `npm run kv:create` | Create the KV namespace (once) |
| `npm run sync:sermons` | Pull new sermons by hand |

`npm run preview` is worth knowing about — `npm run dev` is Vite, but `preview`
runs the actual built Worker with real KV bindings. It's the only local check
that catches Worker-specific breakage before deploying.

### When the dev server breaks

Two failures are common enough to name, and neither means your code is wrong.

**"Dev server failed to start within 30s."** Astro 7 runs `astro dev` as a
detached child and gives it a hardcoded 30 seconds to claim a lock file. A cold
Vite cache here takes longer. The server was fine; the watchdog killed it. Run
it inline instead:

```bash
ASTRO_DEV_BACKGROUND=1 npm run dev          # PowerShell: $env:ASTRO_DEV_BACKGROUND = "1"; npm run dev
```

The real log goes to `.astro/dev.log`, not the console — that's why the error
looks contentless.

**"The file does not exist at .../deps_ssr/… which is in the optimize deps
directory."** Vite re-ran its dependency optimizer and rewrote `deps_ssr` under
a new hash while the workerd runtime was still holding the old one. Every
rendered route then 500s while static assets and 404s still work.

```bash
rm -rf node_modules/.vite     # PowerShell: Remove-Item -Recurse -Force node_modules\.vite
```

then restart Astro. **Editing any config file while dev is running is the usual
trigger**, so stop the server first when changing `astro.config.mjs`,
`tina/config.ts` or `src/content.config.ts`. Builds are never affected, which is
why `npm run build` can pass while `npm run dev` won't boot.

Do **not** use `tinacms dev -c "astro dev"`. Tina's wrapper enforces its own
30-second timeout on the command it spawns and kills Astro before it finishes
bundling.

---

## Layout

```
src/
  components/
    Blocks.astro              renders CMS page sections
    ServiceBoard.astro        the live Sunday board
    home/                     the fixed homepage sections
      Hero.astro  AnnouncementCarousel.astro  PastorNote.astro
      Children.astro  UpcomingEvents.astro
  content/                    what Tina edits, and what the site builds from
    announcements/  events/   .md, frontmatter + body
    sermons/                  one JSON per sermon, filename = YouTube video ID
    pages/  settings/         JSON
  data/services.json          fallback board data when KV is empty
  layouts/Base.astro          masthead, menu, footer, structured data
  pages/
    api/services.json.ts      on-demand — reads live board state from KV
    tina-island/[name].ts     on-demand — re-renders a region while editing in Tina
    [...slug].astro           CMS-created pages
  styles/global.css           design tokens

tina/config.ts                the CMS schema
worker/
  livestream-sync.js          YouTube polling + service matching (cron only)
  wrangler.toml               the cron Worker
wrangler.toml                 the site Worker
scripts/sync-sermons.mjs      nightly sermon import
.github/workflows/            sermon import + deploy
```

Every route prerenders except the two marked on-demand. That is why the site
needs an adapter at all — see [Deployment](#deployment).

---

## Deployment

Two Workers, one KV namespace.

| Worker | Config | Job |
|---|---|---|
| `mpcbc-site` | `wrangler.toml` | the website; reads KV |
| `mpcbc-livestream-sync` | `worker/wrangler.toml` | cron; writes KV |

`output: 'static'` plus an adapter is Astro's hybrid mode: **every route
prerenders unless it opts out.** Only two do, so this is a static site with two
live endpoints, not a server rendering every page. Prerendered HTML is served
straight off the assets layer and never wakes the Worker.

The adapter supplies its own `main` and assets binding and writes a merged
config to `dist/server/wrangler.json` at build time — which is why the deploy
scripts point there, and why `wrangler.toml` deliberately has no `main` field.
Setting one breaks the build, because it's validated before the output exists.

`session: false` is set in `astro.config.mjs`. Nothing uses `Astro.session`, and
left on, the adapter injects a `SESSION` KV binding with no namespace id.

### First deploy

```bash
wrangler login
npm run kv:create                    # paste the id into BOTH wrangler.toml files
npx wrangler secret put YOUTUBE_API_KEY -c worker/wrangler.toml
```

Then fill in the placeholders:

| Placeholder | Where |
|---|---|
| `replace_after_creating_namespace` | `wrangler.toml` **and** `worker/wrangler.toml` |
| `UC_replace_with_real_channel_id` | `worker/wrangler.toml`, both channels |

```bash
npm run deploy          # the site
npm run deploy:sync     # the cron Worker
```

Check the config before you spend a deploy on it:

```bash
npx wrangler deploy --dry-run -c dist/server/wrangler.json
```

Until you set `routes` in `wrangler.toml`, the site lands on
`mpcbc-site.<subdomain>.workers.dev`. `astro.config.mjs` already declares
`site: 'https://mpcbc.org'`, so canonical URLs claim the real domain — point
the domain at the Worker before sending the link anywhere that matters.



### Automatic deploys

[`.github/workflows/deploy.yml`](.github/workflows/deploy.yml) builds and
deploys on every push to `main`. It needs two repository secrets:

| Secret | Where to get it |
|---|---|
| `CLOUDFLARE_API_TOKEN` | Cloudflare dashboard → API Tokens → *Edit Cloudflare Workers* |
| `CLOUDFLARE_ACCOUNT_ID` | Workers dashboard sidebar |

The sermon sync additionally needs `YOUTUBE_API_KEY` as a secret and
`PLAYLIST_CANTONESE`, `PLAYLIST_MANDARIN`, `PLAYLIST_ENGLISH` as repository
variables.

The sync calls the deploy workflow directly rather than relying on its own
commit to trigger it. A push made with `GITHUB_TOKEN` deliberately does not
fire other workflows, so without that call the nightly sermons would land in
the repo and never reach the site.

The cron Worker is deployed by hand (`npm run deploy:sync`) — it changes
about once a year, and it holds the YouTube secret.

### Testing the cron by hand

The sync Worker is cron-only and has no `fetch` handler on purpose — an earlier
version exposed `/sync`, which is an unauthenticated endpoint that spends
YouTube quota. To run a poll manually:

```bash
npx wrangler dev -c worker/wrangler.toml --test-scheduled
curl "http://localhost:8787/__scheduled"
```

Then check what it wrote:

```bash
npx wrangler kv key get services --binding MPCBC --remote -c worker/wrangler.toml
```




### Why the build must be `build:tina`

`tina/__generated__/` is gitignored, and Astro imports the generated client —
so plain `astro build` fails outright. `tinacms build` regenerates it first.
Leave `deploy.yml` on `build:tina`; plain `npm run build` also strips
`public/admin` and would ship a site with no editor.

Editors work in Tina's **local mode**, which needs no credentials, so this
matters only for what the deployed site serves.
