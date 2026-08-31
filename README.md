# MPCBC Website

Astro site for Monterey Park Chinese Baptist Church, deployed to Cloudflare
Workers. Two things run themselves: the Sunday livestream links and the sermon
archive. Two CMSes are installed side by side so we can pick one.

Everything in `src/content/` is sample content. The point is the editing
experience and the automation, not the final copy.

---

## Run it

```bash
npm install
npm run dev          # http://localhost:4321
```

That gives you the site plus Keystatic at `/keystatic`. For Tina instead:

```bash
npm run dev:tina     # site + Tina admin at /admin
```

`dev:tina` is slower to start — it spins up a local GraphQL server on port 4001
and indexes the content first.

| Command | What it does |
|---|---|
| `npm run dev` | Site + Keystatic |
| `npm run dev:tina` | Site + Tina |
| `npm run build` | Production build into `dist/` |
| `npm run preview` | Build, then serve it through the real Worker runtime |
| `npm run deploy` | Build and deploy the site Worker |
| `npm run deploy:sync` | Deploy the livestream cron Worker |
| `npm run kv:create` | Create the KV namespace (once) |
| `npm run sync:sermons` | Pull new sermons by hand |

`npm run preview` is worth knowing about — `npm run dev` is Vite, but `preview`
runs the actual built Worker with real KV bindings. It's the only local check
that catches Worker-specific breakage before deploying.

---

## Layout

```
src/
  components/
    Blocks.astro              renders CMS page sections; understands both CMS shapes
    ServiceBoard.astro        the live Sunday board
  content/                    what Keystatic edits, and what the site builds from
    announcements/  events/   .mdoc, markdoc body
    sermons/                  one JSON per sermon, filename = YouTube video ID
    pages/  settings/         JSON
    tina/                     Tina's parallel copy, in Tina's shape
      announcements/  events/  pages/  settings/
  data/services.json          fallback board data when KV is empty
  layouts/Base.astro          nav, footer, structured data
  pages/
    api/services.json.ts      on-demand — reads live board state from KV
    tina-island/[name].ts     on-demand — re-renders a region while editing in Tina
    [...slug].astro           CMS-created pages
  styles/global.css           design tokens

keystatic.config.ts           Keystatic schema
tina/config.ts                Tina schema
worker/
  livestream-sync.js          YouTube polling + service matching (cron only)
  wrangler.toml               the cron Worker
wrangler.toml                 the site Worker
scripts/sync-sermons.mjs      nightly sermon import
.github/workflows/            runs the sermon import
```

Every route prerenders except the two marked on-demand. That is why the site
needs an adapter at all — see [Deployment](#deployment).

---

## The Sunday livestream board

Nobody edits service links weekly. A cron Worker works out what's live.

**The problem.** Three services stream to two YouTube channels. Titles are typed
by whoever is on the media rota, so they drift. We can't rely on the title
alone, and we can't rely on the clock alone either.

**How it decides.** [`worker/livestream-sync.js`](worker/livestream-sync.js)
scores every live or upcoming broadcast against every service on two
*independent* signals:

| Signal | Worth | Test |
|---|---|---|
| Time | 2 points | scheduled start within 90 minutes of the service |
| Title | 2 points | a language keyword — `粵語`, `Cantonese`, `國語`, `English`… |

Best-scoring pairs are assigned first, one broadcast to one service. Then:

- **Both signals agreed** → high confidence → link that exact video.
- **Only one agreed** → link `youtube.com/embed/live_stream?channel=ID`, which
  plays whatever is live on that channel. Our services never overlap, so this
  resolves correctly on its own.

A mistyped title degrades to a working player rather than a broken link. That's
the whole design goal.

**Failure behaviour.** If every channel errors, the previous KV value is kept
rather than publishing an empty board. Broadcasts that match nothing are written
to an `unmatched` key with a 7-day TTL, so a drifting title convention surfaces
instead of silently losing a sermon.

**Getting to the page.** The Worker writes to KV; nothing else does.

```
cron (every 15 min; every 2 min Sun morning)
  └─ worker/livestream-sync.js  ──writes──▶  KV: services
                                                 │
                       site Worker  /api/services.json  ──reads
                                                 │
                            ServiceBoard.astro ──fetches──▶ patches 3 cards
```

The board is prerendered with build-time data so it paints instantly and is
correct for search engines, then a small inline script refreshes the three
things that actually change — state, button label, button link. If the fetch
fails the prerendered board stays, which is still usable: real times, real
channel links, just possibly a stale "live" badge.

When KV is empty — before the first cron run, or in local dev — the endpoint
serves [`src/data/services.json`](src/data/services.json) instead. The
`x-services-source` response header says which you got, `kv` or `sample`.

$29/month, which is more than the current HostGator bill. Local mode is
---

## The sermon archive

Same principle, slower clock, different mechanism — this one commits to the repo
rather than writing to KV, because sermons are permanent and editable.

[`scripts/sync-sermons.mjs`](scripts/sync-sermons.mjs) runs nightly from
[a GitHub Action](.github/workflows/sync-sermons.yml) at 3am Pacific. It reads
one YouTube playlist per congregation and writes one JSON file per sermon into
`src/content/sermons/`, **named after the YouTube video ID** — that filename is
the join key between the automatic half and the manual half.

The rule that makes it trustworthy:

> The script only ever **creates** files. It never edits or deletes one that
> already exists.

So a volunteer can open a synced sermon in the CMS, type in the speaker and the
scripture reference, and the next nightly run won't touch it. Without that rule
one bad run would wipe a month of hand-entered detail and nobody would trust the
system again.

Optional fields simply don't render when blank, so a sermon works whether or not
anyone got round to filling it in. Private and deleted videos leave placeholders
in the playlist; those are skipped.

Quota is negligible — the sync reads each channel's uploads playlist directly by
flipping the channel ID's second character from `C` to `U`, which avoids a
`channels.list` call. About 4 units per poll against a 10,000/day allowance.

---

## The two CMSes

Both are installed and both work. They edit **separate copies** of the content.

They can't share files. Keystatic stores a page section as
`{discriminant, value:{…}}`; Tina requires a flat `{_template, …}` and its
discriminator is hardwired. Keystatic writes `.mdoc`; Tina reads `.mdx`. Bending
either one to the other's storage format costs it the features you'd be judging
it on, so each gets its native shape and `Blocks.astro` renders whichever it's
handed.

Sermons **are** shared — flat JSON, no blocks, and the sync script writes there.
The one cost of sharing: Tina shows a plain text box for the sermon date where
Keystatic gives a date picker, because Tina's date field would rewrite
`2026-08-23` as a full ISO timestamp and break the other two readers.

### Keystatic — form-based

```bash
npm run dev     # /keystatic
```

No login in local mode. Fast, free for unlimited editors. You fill in a form and
check the result on the site.

### Tina — visual

```bash
npm run dev:tina    # /admin
```

You see the real page and click into it. Changes appear as you type, and
clicking any heading, paragraph, button or photo jumps the sidebar to that
field. Closest thing to an Elementor-style experience that still keeps content
in the repo.

**The catch:** Tina Cloud's free tier covers 2 editors, then $29/month. Local
mode is free and unlimited but only runs on a developer's machine.

### Which one publishes?

Right now, neither — both are local-mode only, so editing happens on a
developer's machine and `npm run build` publishes from the Keystatic tree. To
put an editor on the live site:

- **Keystatic** — set `storage: { kind: 'github', repo: 'ORG/REPO' }` in
  `keystatic.config.ts`, install the Keystatic GitHub App, and remove the
  `isDev` guard in `astro.config.mjs`. Editors sign in with GitHub; saving
  commits and triggers a rebuild.
- **Tina** — add `TINA_CLIENT_ID` and `TINA_TOKEN`, then build with
  `npm run build:tina`. The `/tina-island/[name]` route already deploys, so
  visual editing works against the live site.

Known friction worth naming for either: editors need an account, and publishing
isn't instant.

---

## Deployment

Two Workers, one KV namespace.

| Worker | Config | Job |
|---|---|---|
| `mpcbc-site` | `wrangler.toml` | the website; reads KV |
| `mpcbc-livestream-sync` | `worker/wrangler.toml` | cron; writes KV |

### The site

`output: 'static'` plus an adapter is Astro's hybrid mode: **every route
prerenders unless it opts out.** Only two do, so this is a static site with two
live endpoints, not a server rendering every page. Prerendered HTML is served
straight off the assets layer and never wakes the Worker.

The adapter supplies its own `main` and assets binding and writes a merged
config to `dist/server/wrangler.json` at build time — which is why the deploy
scripts point there, and why `wrangler.toml` deliberately has no `main` field.
Setting one breaks the build, because it's validated before the output exists.

### First deploy

```bash
npm run kv:create                    # paste the id into BOTH wrangler.toml files
npx wrangler secret put YOUTUBE_API_KEY -c worker/wrangler.toml
```

Set the two channel IDs in `worker/wrangler.toml`, then:

```bash
npm run deploy          # the site
npm run deploy:sync     # the cron Worker
```

The first site deploy provisions a `SESSION` KV namespace automatically —
Astro asks for it and the adapter declares it with no id on purpose.

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

The sermon sync calls that workflow directly rather than relying on its own
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

---

## Costs

| | |
|---|---|
| Cloudflare Workers + assets | $0 |
| Cron + KV | $0 (well inside free tier) |
| Keystatic | $0, open source |
| Tina | $0 local, $29/mo beyond 2 cloud editors |
| YouTube API | $0 (~4 quota units per poll against 10,000/day) |
| Domain | ~$40/year |

---

## Design notes

Full extracted brief — tokens, component inventory, the CJK typography
rules and what's open to change — is in
[DESIGN-BRIEF.md](DESIGN-BRIEF.md). It's written to be handed to a designer
or pasted into a design tool as context. Short version:

Palette comes from the building — the brown roof tile, tan stucco, white trim,
and the Southern California sky that dominates both reference photos. The sky
blue is the accent rather than the warmer clay tone church sites default to.

Type is Fraunces with Source Sans 3, both of which have Noto CJK counterparts on
compatible metrics, so Chinese and English sit together without the size
mismatch you normally get when CJK falls back to a system font.

The congregation pages (`/english`, `/cantonese`, `/mandarin`) are separate
content with their own titles and descriptions, not translations of each other.
