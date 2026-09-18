# MPCBC Website

Astro site for Monterey Park Chinese Baptist Church, deployed to Cloudflare
Workers. Two things run themselves: the Sunday livestream links and the sermon
archive. TinaCMS is the editor, with visual click-on-the-page editing.

Everything in `src/content/` is sample content. The point is the editing
experience and the automation, not the final copy.

---

## For non-technical editors

Volunteers don't use any of this. They get one desktop icon —
**`MPCBC Website.bat`** — which opens a control panel that starts the site,
opens the CMS, launches an AI helper, publishes, and undoes. **[GUIDE.md](GUIDE.md)**
is written for them; this README is not. The same guide is published as a web
page at <https://claude.ai/code/artifact/783b33cb-dcdb-48b3-a99d-8c7f77aca36c>,
which is the link to send a new editor — it works before they have the repo.

Setup is `scripts/bootstrap/` zipped and sent to them. Everything underneath is
the same scripts documented below, so there is one code path rather than two.

There are exactly **two** things a person ever launches:

| They have | They run | Which is |
|---|---|---|
| Nothing yet | `Install MPCBC Website` (from the ZIP) | `scripts/bootstrap/install.ps1` |
| The desktop icon | `MPCBC Website` | `scripts/control-panel.ps1` |

Everything else is called by those two. Build the ZIP with
`scripts/make-setup-zip.ps1` — it is a build artifact and is gitignored.

The control panel shows a **setup checklist** (programs, files, building
blocks, settings file) until `Get-Prerequisites` reports `Ready`, gates every
other button on it, and then hides the checklist for good. So a machine can
repair itself from the panel; nobody has to find the ZIP again.

| Script | What it does |
|---|---|
| `scripts/control-panel.ps1` | The whole editor-facing UI (WinForms) |
| `scripts/bootstrap/install.ps1` | First run on a blank machine: installs Git + Node, clones to `C:\mpcbc` |
| `scripts/lib/prereqs.ps1` | `Install-Prerequisites` — winget, then portable, then manual. Shared by the bootstrap and the panel |
| `scripts/make-setup-zip.ps1` | Builds `MPCBC-Website-Setup.zip` from the bootstrap + `prereqs.ps1` |
| `scripts/setup.ps1` | Installs missing tools, pull, `npm install`, `.env`, git identity, desktop shortcut |
| `scripts/start.ps1` | Both dev servers, **hidden**, logging to `logs/` |
| `scripts/stop.ps1` | Kills both process trees and clears the ports |
| `scripts/publish.ps1` | Summary → confirm → pull/commit/push |
| `scripts/undo.ps1` | Restore tracked files; clean untracked **content only** |
| `scripts/open-tools.ps1` | VS Code / Claude Code / Codex / Antigravity |
| `scripts/lib/common.ps1` | Shared helpers — ports, PIDs, git, dialogs, logging |

Two constraints in there are load-bearing and easy to undo by accident:

- **The dev servers run hidden.** Console QuickEdit suspends a process when
  someone clicks in the window, which presents as an unexplained freeze. Output
  goes to `logs/tina.log` and `logs/astro.log` instead.
- **Nothing writes to the registry or a persistent PATH.**
  `Update-PathFromRegistry` reads `HKLM`/`HKCU` and applies the value to the
  current process only. Never swap it for
  `[Environment]::SetEnvironmentVariable('Path', ..., 'Machine')`.

`AGENTS.md` (imported by `CLAUDE.md`) is the brief handed to AI assistants
working on this repo.

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

## The CMS

TinaCMS, in local mode, at `/admin`. Every collection points at the same files
Astro renders under `src/content/` — there is no second copy. What you edit is
what `getCollection()` reads and what the build publishes.

> This replaced a Keystatic/Tina pair that each kept their own tree. The split
> was a steady source of "I changed it and nothing happened", because routing
> and the menu only ever read one of the two. Keystatic and `@astrojs/markdoc`
> were removed and the prose collections moved from `.mdoc` to `.md`.

`Blocks.astro` normalises three block shapes: Tina's file `_template`, Tina's
GraphQL `__typename`, and the legacy Keystatic `{ discriminant, value }`. The
**file `_template` path is the one every production build takes**, because the
Tina server isn't running then — that fallback is what makes an editor-made
page render in CI at all.

### The menu

The four top-level items — About 關於我們, Services 崇拜, Newsletter 通訊,
Offering 奉獻 — are fixed in `NAV_PARENTS` in `Base.astro`, so the menu can't be
emptied from the editor. Everything else is editor-controlled. Each page has:

| Field | Does |
|---|---|
| **Where in the menu** | `Not in the menu` / `Top level` / under one of the four |
| **Menu label** | falls back to a title-cased slug |
| **Menu label 中文** | shown beside the English, as every other item is |
| **Menu order** | lower first, among pages in the same place |

Assigning a page to a parent **turns that parent into a dropdown**, styled like
Services; the parent's own destination moves to the foot of the panel so it is
never lost. A parent with no pages under it stays a plain link.

### Publishing

Local mode only, so editing happens on a developer's machine and `npm run build`
publishes from the repo. To put an editor on the live site, set `TINA_CLIENT_ID`
and `TINA_TOKEN` and build with `npm run build:tina`. The `/tina-island/[name]`
route already deploys, so visual editing works against the live site.

`.github/workflows/deploy.yml` already runs `build:tina`, so CI ships the editor
once `TINA_CLIENT_ID` and `TINA_TOKEN` are set as repository secrets. Leave it
on `build:tina` — plain `npm run build` strips `public/admin` and would ship a
site with no editor.

Tina Cloud's free tier covers 2 editors, then $29/month. Local mode is free and
unlimited.

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

```
cron (every 15 min; every 2 min Sun morning)
  └─ worker/livestream-sync.js  ──writes──▶  KV: services
                                                 │
                       site Worker  /api/services.json  ──reads
                                                 │
                            ServiceBoard.astro ──fetches──▶ patches 3 cards
```

The board is prerendered with build-time data so it paints instantly and is
correct for search engines, then a small inline script refreshes what actually
changes — card state, the well label, the meta line, the button. If the fetch
fails the prerendered board stays, which is still usable: real times, real
channel links, just possibly a stale "live" badge.

When KV is empty — before the first cron run, or in local dev — the endpoint
serves [`src/data/services.json`](src/data/services.json) instead. The
`x-services-source` response header says which you got, `kv` or `sample`.

Per-congregation prose lives in `ServiceBoard.astro`, deliberately **not** in
`services.json`: that file is overwritten by the sync, so anything written there
is lost on the next run.

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

### Before a real launch

The design carries placeholder content that must not go live on the real
domain:

- Pastor name, role and portrait
- `(626) 555-0100` and `office@mpcbc.org` in the footer
- Facebook / YouTube / Instagram links
- The **Offering 奉獻** menu item has no destination (`href="#"`)
- The newsletter form has no endpoint — it acknowledges locally and discards
  the address
- The hero background video, and the three children's-ministry photos

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

---

## Costs

| | |
|---|---|
| Cloudflare Workers + assets | $0 |
| Cron + KV | $0 (well inside free tier) |
| Tina | $0 local, $29/mo beyond 2 cloud editors |
| YouTube API | $0 (~4 quota units per poll against 10,000/day) |
| Domain | ~$40/year |

---

## Design notes

Full extracted brief — tokens, component inventory, the CJK typography rules
and what's open to change — is in [DESIGN-BRIEF.md](DESIGN-BRIEF.md). It's
written to be handed to a designer or pasted into a design tool as context.
Short version:

Palette comes from the church logo — the maroon banner, its white cross and
figures, and the near-black plum it sits on. `--accent` `#943759` is the brand;
`--live` `#C2410C` is reserved for one thing only, a stream that is live right
now.

Type is Geist for display and body, with Inter for the small uppercase meta
labels and Noto Serif TC / Noto Sans TC for Chinese — all on compatible
metrics, so Chinese and English sit on the same line without the size mismatch
you normally get when CJK falls back to a system font. The `:lang()` rules in
`global.css` are typography, not preference, and are meant to survive a
redesign.

The congregation pages (`/english`, `/cantonese`, `/mandarin`) are separate
content with their own titles and descriptions, not translations of each other.
