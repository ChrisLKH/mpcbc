# Working on the MPCBC website

Astro 7 site for Monterey Park Chinese Baptist Church, deployed to Cloudflare
Workers. TinaCMS is the editor. Two things run themselves: the Sunday
livestream links and the sermon archive.

Much of the time you are being asked for a change by a **non-technical church
volunteer** driving the control panel (`MPCBC Website.bat`). Prefer the smallest
content-only change that answers the request, explain what you did in plain
words, and say when something needs Chris.

## Where content lives

Everything editable is under `src/content/`, and TinaCMS at
`http://localhost:4321/admin` edits **those exact files** — there is no second
copy. What you change is what the build publishes.

| Collection | Path | Format |
|---|---|---|
| Homepage | `src/content/settings/homepage.json` | JSON, single file |
| Pages | `src/content/pages/*.json` | JSON, section blocks |
| Announcements | `src/content/announcements/*.md` | frontmatter + body |
| Events | `src/content/events/*.md` | frontmatter + body |
| Sermons | `src/content/sermons/*.json` | one file per sermon |

Field names and their meanings are defined in `tina/config.ts`, and the Zod
schemas in `src/content.config.ts` validate the same files. **Both must agree**
— add a field to one and the other rejects the content.

## Rules that will bite you

- **`removeAfter` is required on every announcement.** It is how announcements
  expire. A missing one fails the content schema and breaks the build.
- **Never hand-write files in `src/content/sermons/`.** `scripts/sync-sermons.mjs`
  creates them nightly from YouTube, named after the video ID. It only ever
  *creates* — it never edits or deletes — so hand-entered `speaker`, `scripture`
  and `notes` survive. Creating one by hand fights that.
- **Never edit `src/data/services.json`.** The livestream cron Worker overwrites
  it. Per-congregation prose belongs in `src/components/ServiceBoard.astro`,
  which is why it lives there.
- **Never touch `.env`.** It is gitignored and may hold a real Tina token.
- Adding a new section block means editing **both** `tina/config.ts` and
  `src/components/Blocks.astro`. One without the other renders nothing.
- The four top-level menu items are fixed in `NAV_PARENTS` in
  `src/layouts/Base.astro`, so the menu cannot be emptied from the CMS.

## Running it

Use the control panel — `MPCBC Website.bat`, or `app/start.ps1` — rather
than starting servers by hand. It gets the ordering right, and the ordering is
load-bearing:

- **Tina must be up and finished indexing before Astro starts.** Two separate
  processes, in that order.
- **Do not use `tinacms dev -c "astro dev"`.** Tina's wrapper enforces a
  30-second timeout on the command it spawns and kills Astro before it finishes
  bundling.
- **`ASTRO_DEV_BACKGROUND=1`.** Astro 7 otherwise runs `astro dev` as a detached
  child with a hardcoded 30-second window to claim a lock file, which a cold
  Vite cache regularly misses. The real log goes to `.astro/dev.log`, not the
  console — which is why the failure looks contentless.
- **Use `localhost`, not `127.0.0.1`.** The dev server binds IPv6.
- Servers started by the control panel run hidden; their output is in
  `logs/tina.log` and `logs/astro.log`. Read those rather than assuming silence
  means success.

If rendered routes start 500ing while static assets still work, Vite rewrote
`deps_ssr` under a new hash while workerd held the old one: delete
`node_modules/.vite` and restart. **Editing any config file while dev is running
is the usual trigger**, so stop the server before changing `astro.config.mjs`,
`tina/config.ts` or `src/content.config.ts`.

## Publishing

Pushing to `main` deploys the live site via GitHub Actions. Do not push on a
volunteer's behalf unless they asked for it in as many words — the control
panel's "Publish to Live Site" button is the intended route, and it shows them a
summary first.

## Style

`DESIGN-BRIEF.md` has the full brief. Briefly: the palette comes from the church
logo (`--accent` `#943759`); `--live` `#C2410C` is reserved for one thing only, a
stream that is live right now. The `:lang()` rules in `src/styles/global.css` are
typography, not preference — they keep Chinese and English on the same line
without the size mismatch CJK fallback normally causes. Leave them alone.

The congregation pages (`/english`, `/cantonese`, `/mandarin`) are separate
content with their own titles, **not** translations of each other.
