# MPCBC Website

Astro 7 site for Monterey Park Chinese Baptist Church, deployed to Cloudflare
Workers. TinaCMS is the editor. Two things run themselves: the Sunday
livestream links and the sermon archive.

**Two audiences. Pick yours:**

| You are | Start here |
|---|---|
| Looking after the website's words and pictures | **[GUIDE.md](GUIDE.md)**, or the [same guide as a web page](https://claude.ai/artifact/Fr7CB6RGp97HNAwfmWrcJo). You need nothing else. |
| Comfortable with code, and want to know how it fits together | This README |

Both install and run the site through the app. There are no manual steps in
normal use: no terminal, no `npm`, no deploy command.

---

## For volunteers

Nobody editing the site uses git, a terminal, or npm. They get **one desktop
icon** that opens a small app:

```
┌─ MPCBC Website ────────────────────────────────┐
│  ● Ready to start                              │
│    2 updates to collect, then the site opens.  │
│  ────────────────────────────────────────────  │
│  [                 Install                  ]  │   greyed unless needed
│  [              Update & Start              ]  │   → Stop, once running
│  [    Preview     ] [   Edit Content   ]       │
│  ────────────────────────────────────────────  │
│  [                   Code                   ]  │   folder / VS Code / AI
│  [           Publish to Live Site           ]  │   lights up when changed
│  [             Undo All Changes             ]  │
│  ────────────────────────────────────────────  │
│  [ Help ]                    [ Show Details ]  │
└────────────────────────────────────────────────┘
```

**Getting it onto a new machine — one permanent link:**

```
https://github.com/ChrisLKH/mpcbc/raw/main/MPCBC-Website-Setup.zip
```

`MPCBC-Website-Setup.zip` is committed, and
[`build-setup-zip.yml`](.github/workflows/build-setup-zip.yml) rebuilds and
re-commits it whenever `Install/` or `app/lib/prereqs.ps1` changes — so that
link never serves a stale installer. Send it to anyone; it works once the
repository is public. (`app/make-setup-zip.ps1` builds it by hand.)

They extract it, double-click **Install MPCBC Website**, and five minutes
later there is a desktop icon.
It installs Git and Node if missing — via winget, or portable copies needing
no administrator rights — and clones to `C:\mpcbc`.

The install location is **told, never asked**. A folder picker is a decision a
non-technical editor cannot evaluate, and the wrong answer breaks things:
`Documents` is routinely redirected into OneDrive, which then tries to sync
`node_modules`. `-Path` overrides it where that is genuinely needed.

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
| `app/mac/` | Old, **unmaintained** shell equivalents. They predate the control panel |

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

## Inside the website — what is in `src/`

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

---

## If the site will not start

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

---

## Deploying

**Publishing from the app is the whole story.** An editor clicks *Publish to
Live Site*; that pushes to `main`; GitHub Actions builds and deploys; the site
updates in two to four minutes.

Nothing needs running by hand.

`deploy.yml` must stay on `build:tina` rather than plain `npm run build`:
`tina/__generated__/` is gitignored and Astro imports the generated client, so
`astro build` fails outright — and plain `build` also strips `public/admin`.

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
