# MPCBC Website — Demo

A working demonstration of the static-site approach: Astro for the site,
Keystatic for editing, and a Cloudflare Worker for automatic livestream detection.

Everything here is sample content. The point is to show the editing experience
and the automation, not the final design.

## Run it

```bash
npm install
npm run dev
```

- Website: http://localhost:4321
- **Editor: http://localhost:4321/keystatic**

The editor needs no login in local mode. Open it, change something, save, and
watch the page update. That's the whole editing experience — no code, no git.

## What's worth looking at

**`/keystatic`** — the admin. Announcements, events, sermons, pages, and service
times. Rich text editor, image uploads, date pickers, dropdowns.

**Homepage** — the service board shows all three states at once: English is live,
Cantonese is scheduled, Mandarin has nothing scheduled and falls back to a channel
link. A visitor can't tell anything is missing from the third one.

**`/sermons`** — 9 sample sermons, 7 with speaker or scripture filled in and 2
without. The archive works either way; optional fields simply don't render when
they're blank.

**Congregation pages** — `/english`, `/cantonese`, `/mandarin` are separate
content with their own titles and descriptions, not translations of each other.

## Layout

```
src/
  components/ServiceBoard.astro   live status board
  content/                        what the CMS edits
    announcements/  events/  sermons/  pages/  settings/
  data/services.json              sample of what the Worker writes
  layouts/Base.astro              nav, footer, structured data
  pages/                          routes
  styles/global.css               design tokens
keystatic.config.ts               defines every field editors see
worker/livestream-sync.js         YouTube polling + service matching
wrangler.toml                     cron schedule
```

## Deploying the site

Build output is fully static:

```bash
npm run build     # -> dist/
```

Point Cloudflare at the repo, build command `npm run build`, output directory
`dist`. Free tier, no server.

## Turning on the editor for real people

Right now Keystatic runs in local mode, which is dev-only. To let others log in
on the live site:

1. In `keystatic.config.ts`, change storage to:
   ```ts
   storage: { kind: 'github', repo: 'ORG/REPO' }
   ```
2. Install the Keystatic GitHub App on the repo.
3. Add the Cloudflare adapter so the auth routes can run:
   ```bash
   npx astro add cloudflare
   ```
   and remove the `isDev` guard in `astro.config.mjs`.

Editors then sign in with GitHub at `/keystatic`. Saving commits to the repo and
triggers a rebuild — live in a minute or two.

**Known friction, worth naming:** editors need GitHub accounts, and publishing
isn't instant. Neither is a blocker, but both are real.

## Deploying the livestream sync

```bash
npx wrangler kv namespace create MPCBC     # paste the id into wrangler.toml
npx wrangler secret put YOUTUBE_API_KEY
npx wrangler deploy
```

Set the two channel IDs in `wrangler.toml` first. Test with `/sync` on the
deployed Worker; read the result at `/api/services`.

**How the matching works.** Each broadcast is scored against each service on two
independent signals — scheduled start time within 90 minutes, and a language
keyword in the title. Both agreeing gives high confidence and links the exact
video. Only one agreeing falls back to
`youtube.com/embed/live_stream?channel=ID`, which plays whatever is live on that
channel. Since our services never overlap, that resolves correctly on its own.
A mistyped title degrades to a working player rather than a broken one.

Broadcasts that match nothing get written to the `unmatched` key so a drifting
title convention surfaces instead of silently losing a sermon.

## Costs

| | |
|---|---|
| Cloudflare hosting | $0 |
| Worker + cron + KV | $0 (well inside free tier) |
| Keystatic | $0, open source |
| YouTube API | $0 (~4 quota units per poll against 10,000/day) |
| Domains | ~$40/year |

## Design notes

Palette comes from the building — the brown roof tile, tan stucco, white trim,
and the Southern California sky that dominates both reference photos. The sky
blue is the accent rather than the warmer clay tone that church sites default to.

Type is Fraunces with Source Sans 3, both of which have Noto CJK counterparts on
compatible metrics, so Chinese and English sit together without the size mismatch
you normally get when CJK falls back to a system font.
