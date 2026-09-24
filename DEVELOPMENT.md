# Development

Everything here is for working on the code. **Nobody editing the website's
words and pictures needs any of it** — they use the app described in
[README.md](README.md) and [GUIDE.md](GUIDE.md), which starts the same
servers and runs the same build.

Reach for this file when the app is not the right tool: setting up a new
Cloudflare environment, debugging a dev server that will not boot, or
testing the livestream cron by hand.

---

## How the editor app is built

### Folder layout

The app is kept separate from the website it edits, so opening the folder
tells you where to start:

```
START HERE.txt        what to run, for a volunteer who opens the folder
MPCBC Website.bat     the everyday launcher (and the shortcut's target)
Install/              the first-time installer — this is what gets zipped
app/                  the control panel and its scripts
scripts/              build automation only: sync-sermons.mjs, clean-admin.mjs
src/ public/ tina/    the Astro website itself
```

`scripts/` keeps its name because `package.json` and the workflows reference
`scripts/sync-sermons.mjs` and `scripts/clean-admin.mjs`. The Astro site stays
at the repository root because Astro, Tina, the Cloudflare adapter and CI all
expect it there.

### One button

**Update & Start** runs `app/start-working.ps1`: install what is missing →
collect updates → install building blocks → start the site. Every step decides
for itself whether there is anything to do, so the same button is a first-time
install and a two-second routine launch.

**Install** runs the same chain. It exists so its greyed-out state can answer
"does this computer need anything?" without anyone reading a word — and it
stays honest as the project grows, because `Get-Prerequisites` compares
`package-lock.json` against the hash stamped by the last successful install,
rather than merely checking that `node_modules` exists.

On launch the panel only **fetches** — that updates our record of what is on
GitHub and touches no file — so it can report updates waiting without having
taken them. Nothing in the working folder changes before the click.

Publishing is deliberately outside that chain and is never a forced
end-of-wizard prompt. Publish and Undo light up when there is something to
publish, so closing the window and returning tomorrow is always a safe answer.

| Script | What it does |
|---|---|
| `app/control-panel.ps1` | The whole editor-facing UI (WinForms) |
| `app/start-working.ps1` | The one-button chain: setup, then start |
| `Install/install.ps1` | First run on a blank machine: installs Git + Node, clones to `C:\mpcbc` |
| `app/lib/prereqs.ps1` | `Install-Prerequisites` — winget, then portable, then manual. Shared by the installer and the panel |
| `app/lib/common.ps1` | Shared helpers — ports, PIDs, git, dialogs, logging |
| `app/setup.ps1` | Installs missing tools, pull, `npm install`, `.env`, git identity, desktop shortcut |
| `app/start.ps1` / `app/stop.ps1` | Both dev servers, **hidden**, logging to `logs/` — and killing their process trees |
| `app/publish.ps1` | Summary → confirm → pull/commit/push |
| `app/undo.ps1` | Restore tracked files; clean untracked **content only** |
| `app/open-tools.ps1` | Explorer / VS Code / Claude Code / Codex / Antigravity |
| `app/make-setup-zip.ps1` | Builds `MPCBC-Website-Setup.zip` |
| `app/make-icon.ps1` | Builds `app/mpcbc.ico` from `public/images/logo-square.png` |

### Five things that look arbitrary and are not

Each of these was a real failure before it was a line of code:

- **The dev servers run hidden.** Console QuickEdit suspends a process when
  someone clicks in the window, which presents as an unexplained freeze.
  Output goes to `logs/tina.log` and `logs/astro.log` instead.
- **The launcher does not pass `-WindowStyle Hidden`.** That sets the process
  `STARTUPINFO` to `SW_HIDE`, which Windows applies to the first top-level
  window — the control panel itself. The app would start and display nothing.
  `control-panel.ps1` hides its own console once it owns it.
- **`Test-PortOpen` probes both `::1` and `127.0.0.1`.** Astro and Tina bind
  IPv6 only, and `localhost` does not always resolve there first. Get this
  wrong and the panel waits forever, then kills a healthy server.
- **Nothing writes to the registry or a persistent PATH.**
  `Update-PathFromRegistry` reads `HKLM`/`HKCU` and applies the value to the
  current process only. Never swap it for
  `[Environment]::SetEnvironmentVariable('Path', ..., 'Machine')`.
- **`app/mpcbc.ico` stores sizes ≤ 128 as BMP, not PNG.** `System.Drawing`
  draws the window icon and cannot decode PNG-compressed ICO entries.

`AGENTS.md` (imported by `CLAUDE.md`) is the brief handed to AI assistants
working on this repo.

---

### Windows only, deliberately

The app is Windows-only. Three `.sh` equivalents used to live in `app/mac/`
and were deleted: they predated the control panel, installed to
`$HOME/Documents` (iCloud-synced, the same `node_modules` problem the Windows
installer avoids), carried none of the publish-time safety checks, and their
`ROOT` path broke when the scripts moved from `scripts/` to `app/`. A broken
script that looks like support is worse than no script.

Porting is a rewrite of the front, not a port. Most of the ~1,950 lines of
logic survive once nine primitives are swapped — `taskkill` for process
groups, `Get-NetTCPConnection` for `lsof`, the registry PATH read for a shell
profile, `winget` for Homebrew, and so on. The blocker is the ~590-line
WinForms UI, which has no macOS counterpart: PowerShell 7 runs on macOS,
`System.Windows.Forms` does not. A Mac equivalent means AppleScript or
Platypus at lower fidelity, or SwiftUI plus Apple notarisation.

Worth knowing before starting: Preview and Edit Content are browser tabs and
already work anywhere, and Publish is git. The only genuinely Windows-locked
piece is starting the servers with a friendly face on it.

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
emptied from the editor. Everything else is editor-controlled; the fields an
editor fills in are listed in [README.md](README.md#the-menu).

Assigning a page to a parent **turns that parent into a dropdown**, styled like
Services; the parent's own destination moves to the foot of the panel so it is
never lost. A parent with no pages under it stays a plain link.

`congregation` is worth a warning. It is required by the Zod schema on every
collection, but on **pages it is never read** — `[...slug].astro` ignores it
and the menu is built from `menuParent` alone. It only does real work on
sermons, where `/english`, `/cantonese` and `/mandarin` filter on it. It was
labelled "Section" on pages until that was mistaken for the menu field;
removing it outright would mean making it optional in `src/content.config.ts`
first.

### Trial editors: Decap and Sveltia

Two browser-only editors are on trial beside Tina, because editors should not
have to install anything and Tina Cloud's free plan has two seats:

| Page | CMS | Login |
|---|---|---|
| `/admin-decap` | Decap CMS | DecapBridge: email invite, password, Google or Microsoft. No GitHub account. |
| `/admin-sveltia` | Sveltia CMS | GitHub account, via "Sign in with token". Email login is planned for Sveltia 1.0. |

Both load **`public/cms/shared.js`**, which holds the collections and the
preview. Only the `backend` in each `index.html` differs. The collections
mirror `tina/config.ts` field for field, and blocks keep Tina's `_template`
key, so a file saved in any of the three editors reads back in the others and
in `Blocks.astro`. **A field added to Tina must be added there too**, or these
editors silently drop it on save.

Both save straight to **`main`**, like Tina Cloud does, so a save deploys the
live site within a few minutes. There is no review step in between.

**Live preview** is the site itself. The preview panel POSTs the unsaved draft
to `/cms-preview/<kind>` (`src/pages/cms-preview/[kind].astro`, not
prerendered), which renders it with `Base`, `Blocks`, `Hero`,
`AnnouncementCarousel` and `UpcomingEvents` and returns HTML shown in a
script-less iframe. A new section in `Blocks.astro` previews with no change
here. The route renders whatever it is sent under the church's domain, so it
answers only a same-origin JSON POST, and returns 404 for anything else. It
works on the deployed site and under `npm run dev`. The admin page and the
site have to share an origin.

**Setting up DecapBridge:** sign in at decapbridge.com with GitHub, add the
site, install its GitHub App on `ChrisLKH/mpcbc`, and register
`https://mpcbc.org` (plus `http://localhost:4321` for local testing). Put the
site ID it gives you into `identity_url` in `public/admin-decap/index.html`,
and invite editors by email from its dashboard.

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

---

## Flexible sections

### The problem

`pageBlocks` is a flat list of nine section types, each with a fixed set of
fields. An editor can reorder sections by dragging, but cannot compose one:
there is no way to build a section that is "image, then a paragraph, then a
button, then a video" unless a template already has exactly those fields.
Adding `ctaText`/`ctaLink` to `imageText` patched one instance of this; the
general shape of the limitation remains.

### The shape of the fix

Tina supports this. In `@tinacms/schema-tools`, `ObjectField` is either
`{ fields }` or `{ templates }`, and `Template.fields` accepts `ObjectField`
again — so block lists nest recursively.

**All nine presets are kept exactly as they were**, with a tenth alongside
them. Ordinary editing is unchanged; the new one is an escape hatch, not a
replacement. Replacing the presets with one generic container would have cost
the purpose-built sections (service board, recent sermons) their simplicity
and made every page a nesting exercise.

```js
// tina/config.ts — the component library that can appear INSIDE a section
const flexibleItems = [
  { name: 'heading',   label: 'Heading',
    fields: [{ type: 'string', name: 'text', label: 'Text' }] },
  { name: 'paragraph', label: 'Paragraph',
    fields: [{ type: 'string', name: 'text', label: 'Text',
               ui: { component: 'textarea' } }] },
  { name: 'image',     label: 'Image',
    fields: [{ type: 'image',  name: 'src', label: 'Image' },
             { type: 'string', name: 'alt', label: 'Describe the image' }] },
  { name: 'button',    label: 'Button',
    fields: [{ type: 'string', name: 'text', label: 'Button text' },
             { type: 'string', name: 'url',  label: 'Link' }] },
  { name: 'video',     label: 'Video',
    fields: [{ type: 'string', name: 'url', label: 'Video link or ID' }] },
];

// ...added to pageBlocks alongside the existing nine
{
  name: 'flexible',
  label: 'Flexible section',
  fields: [
    // No section-wide "stacked or two columns": each piece carries its
    // own `width` (full | half) from the shared widthField above.
    { type: 'object', name: 'items', label: 'Contents',
      list: true,
      templates: flexibleItems,
      // Without this the sidebar list reads "Item 1, Item 2, Item 3".
      // Labels each row with its kind and its text, e.g. "Button: Give
      // now" — falls back to the kind alone, and never throws on a
      // blank or half-filled-in row.
      ui: { itemProps: (item) => {
        const kind = flexibleItems.find((t) => t.name === item?._template)?.label
          || item?._template || 'Item';
        const text = item?.text || item?.alt || item?.url || '';
        return { label: text ? `${kind}: ${text}` : kind };
      } } },
  ],
}
```

Both `pages` and `homepage` point their `sections` field at the same
`pageBlocks` array, so the flexible section is available in both without any
extra wiring.

### What it does

1. **`src/components/Blocks.astro`** — `'flexible'` is in `BLOCK_NAMES`, and a
   branch maps over `v.items`. Each item carries its own `_template`, so the
   three-shape normalising logic (Keystatic `discriminant`, Tina file
   `_template`, Tina GraphQL `__typename`) applies unchanged at the inner
   level; it was factored into a shared `normaliseAgainst(block, names)` so
   the outer and inner passes reuse one function against two name lists
   (`BLOCK_NAMES` and `ITEM_NAMES`) instead of duplicating it.

   It reuses rather than invents: the file's own `<style>` block supplies
   `.btnrow` and `.embed`, and `.section`, `.wrap`, `.btn` and `.btn--ghost`
   come from `src/styles/global.css`. A flexible section wraps in
   `<section class="section"><div class="wrap">` like every other block, its
   button component emits exactly the markup the `buttons` block does, and
   its video component emits exactly the markup the `videoEmbed` block does
   — same `.embed` wrapper, same youtube-nocookie iframe. A small helper,
   `youtubeId()`, pulls the 11-character ID out of a pasted `watch?v=`,
   `youtu.be/`, `/embed/` or `/live/` link, or passes a bare ID through
   unchanged, so the field can take either. Any component with nothing to
   show — no text, no image, no extractable video ID — renders nothing
   rather than an empty tag.
2. **CSS** — `.flexstack` is a two-column grid above the file's usual
   `44rem` breakpoint, in which **every piece spans both columns unless it
   is marked half width** (`.flexstack__half`, `grid-column: span 1`).

   This is why there is no section-wide arrangement setting. A single
   "stacked or two columns" switch cannot express the common case — a
   heading and a paragraph across the top, then two columns beneath them —
   because the section would have to be one shape or the other. Per-piece
   width covers all three: all full is a stack, all half is two columns,
   and a mix is a mix. It also adds no nesting, which a "row" container
   would have. An odd half-width piece leaves the other column empty.
   Below the breakpoint the grid is a single column, so `half` needs no
   separate mobile handling. Images inherit the site-wide
   `max-width: 100%` and pick up `var(--radius)` to match the other blocks.

   The one non-obvious rule is that the stack **zeroes its children's own
   margins**. `global.css` gives every `h2` a `0.5em` bottom margin and every
   `p` a `1em` one, which would add to the grid gap — so the space between
   two components would depend on which two they happened to be, and an
   editor reordering items would see the spacing change for no visible
   reason. The grid owns the rhythm; the components contribute none. This is
   what "looks right for *any* ordering" actually requires.
3. **`tinaField` markers** are threaded to the nested items: every piece
   renders its own content, then gets one common wrapper that carries both
   the width class and `field(iv)`, while the content inside carries
   `field(iv, 'text' | 'src' | 'url')` — the same pattern the `gallery`
   block already used for its photos. The shared wrapper is also what makes
   the pieces line up: a button row and an image are the same kind of grid
   item because they sit in the same box, not because their own markup was
   made to match. Skipping this was the risk called out below
   — visual editing would have silently stopped working inside flexible
   sections while continuing to work everywhere else.
4. **No Zod change needed.** `sections` is `z.array(z.any())` in
   `src/content.config.ts`, so nested content validates as-is.

### What it will still not be

Tina's editor is nested accordions in a sidebar, not a canvas. Items are
dragged within a list; a button is never dropped *onto* an image. If what is
actually wanted is Webflow-style direct manipulation, this is the wrong tool
and no amount of schema work gets there.

### The reason to think twice

Depth is the cost. A flexible section means clicking into a section, then a
component, then a field. The flat list is a large part of why the current
editor is legible to someone non-technical, and that legibility is worth more
than arrangement freedom on most pages. Adding one flexible type keeps the
easy path easy; making everything composable does not.
