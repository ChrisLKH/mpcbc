# MPCBC Website

Astro site for Monterey Park Chinese Baptist Church. TinaCMS is the editor,
and it is deployed to Cloudflare Workers. Two things run themselves: the
Sunday livestream links and the sermon archive.

**This page is for anyone who looks after the website's content** — whether or
not you write code. It covers three things: getting it onto your computer,
changing content, and publishing.

| Also available | |
|---|---|
| [GUIDE.md](GUIDE.md) | The same ground with no assumed knowledge, and screenshots. Send this to a new volunteer — there is a [web version](https://claude.ai/artifact/Fr7CB6RGp97HNAwfmWrcJo) that works before they have anything installed. |
| [DEVELOPMENT.md](DEVELOPMENT.md) | How the app and the site are built, and how deployment works. |
| [AGENTS.md](AGENTS.md) | The brief AI assistants read. Imported by `CLAUDE.md`. |

---

## 1. Get it onto your computer

One link, and it is always current:

```
https://github.com/ChrisLKH/mpcbc/raw/main/MPCBC-Website-Setup.zip
```

Extract it, double-click **Install MPCBC Website**, wait about five minutes.
It installs Git and Node if they are missing, downloads the site to
`C:\mpcbc`, and leaves one icon on the desktop.

You do not need a GitHub account for this. You need one only to publish.

---

## 2. Open it

Double-click the **MPCBC Website** icon. One window, and on any given day one
button:

```
┌─ MPCBC Website ────────────────────────────────┐
│  ● Ready to start                              │
│    2 updates to collect, then the site opens.  │
│  ────────────────────────────────────────────  │
│  [                 Install                  ]  │  greyed unless needed
│  [              Update & Start              ]  │  → Stop, once running
│  [    Preview     ] [   Edit Content   ]       │
│  ────────────────────────────────────────────  │
│  [                   Code                   ]  │  folder / VS Code / AI
│  [           Publish to Live Site           ]  │  lights up when changed
│  [             Undo All Changes             ]  │
│  ────────────────────────────────────────────  │
│  [ Help ]                    [ Show Details ]  │
└────────────────────────────────────────────────┘
```

**Update & Start** collects everyone else's changes, installs anything
missing, and starts the site. **Preview** opens it; **Edit Content** opens the
editor. A button that will not click is greyed on purpose, not broken.

---

## 3. Change content — in the editor

**Edit Content** opens TinaCMS at `localhost:4321/admin`. It edits the files
under `src/content/` directly; there is no second copy, so what you change is
what gets published.

| Collection | What it is |
|---|---|
| **Homepage** | Headline, opening paragraph, button, hero video, and the stack of sections |
| **Announcements** | Notices on the homepage and announcements page |
| **Events** | Dated things — a picnic, a baptism, a members' meeting |
| **Pages** | Whole pages, built by stacking sections |
| **Sermons** | One per video. **Never create these by hand** |

Three rules the editor will not enforce for you:

- **Every announcement needs "Remove after".** It is how the site stays tidy
  without anyone remembering to come back and delete things.
- **Sermons arrive on their own**, nightly from YouTube, named by video ID.
  Fill in speaker, scripture or series, or tick *Hide from website* — the sync
  only ever adds, so anything you type survives.
- **"Who is this for?" is not the menu.** That field is the congregation. The
  menu is the next field down, *Where in the menu*.

### The menu

Four headings are fixed — About 關於我們, Services 崇拜, Newsletter 通訊,
Offering 奉獻 — so the menu cannot be emptied by accident. Any page you create
can sit under one of them, at top level, or nowhere:

| Field | Does |
|---|---|
| **Where in the menu** | `Not in the menu` / `Top level` / under one of the four |
| **Menu label** | Falls back to a title-cased slug |
| **Menu label 中文** | Shown beside the English, as every other item is |
| **Menu order** | Lower first, among pages in the same place |

Putting a page under a heading turns that heading into a dropdown.

---

## 4. Change content — in the files

Everything you can safely edit lives under **`src/content/`**:

```
src/content/
  settings/homepage.json     the homepage
  pages/*.json               pages you created
  announcements/*.md         one file per announcement
  events/*.md                one file per event
  sermons/*.json             one per sermon  ← don't create these
public/images/               pictures
```

Markdown files carry frontmatter and a body; JSON files are fields only. Field
names come from `tina/config.ts`, and `src/content.config.ts` validates the
same files — **both must agree**, or the build rejects the content.

**Do not hand-edit** `src/content/sermons/` (the nightly sync owns it) or
`src/data/services.json` (the livestream Worker overwrites it). Anything
outside `src/content/` is the machinery — see
[DEVELOPMENT.md](DEVELOPMENT.md).

**Code** on the control panel opens the folder, VS Code, or an AI assistant
(Claude Code, Codex, Antigravity) already pointed at the right place.

> Some pages are not editable at all: `/visit` is entirely fixed text, and the
> congregation, events and sermons pages keep their own headings in code while
> pulling their lists from the CMS.

---

## 5. Publish

**Publish to Live Site** lights up when something has changed. It shows a
plain-language summary first — *"2 announcements, the homepage, 1 photo"* —
then asks what you changed, and pushes. GitHub Actions deploys; the live site
updates in two to four minutes.

This is the one step that needs a GitHub account with access to the
repository. The first publish opens a browser sign-in; after that the computer
remembers.

**Undo All Changes** throws away everything unpublished and returns the folder
to the last published version. It names what it is about to discard first.
