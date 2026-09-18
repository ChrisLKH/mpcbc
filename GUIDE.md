# Editing the MPCBC Website

A guide for everyone who looks after the church website. **You do not need to
know anything technical.** If you can use email, you can do this.

> **Easier to read online:** <https://claude.ai/artifact/Fr7CB6RGp97HNAwfmWrcJo>
> — the same guide as a web page. Send that link to a new editor; it works
> before they have any of this on their computer.

> Nothing in this guide can break the real website. Everything happens on your
> own computer first, and nothing goes live until you press **Publish**.

---

## How it works, in one picture

```
   Your computer                         The real website
   ─────────────                         ────────────────

   1. Update & Start           ◀──────── everyone else's changes
   2. Make your changes
   3. Preview them
   4. Publish to Live Site  ────────────▶ live in 2-4 minutes
```

You always work on a **copy** on your own computer. You look at it, you change
your mind, you undo things. Only step 4 touches the website the public sees.

---

## Part 1 — Setting up your computer (once)

Download **MPCBC-Website-Setup.zip** from this link — it is always the current
version, so it is safe to bookmark:

<https://github.com/ChrisLKH/mpcbc/raw/main/MPCBC-Website-Setup.zip>

(the website administrator can also just send you the file.)

### 1. Extract it

Right-click the ZIP file → **Extract All...** → **Extract**.

This matters. If you open the files straight out of the ZIP, nothing will work.
(The setup will notice and tell you, so don't worry about getting it wrong.)

### 2. Double-click **Install MPCBC Website**

Then wait about five minutes. Along the way you may see:

| What you see | What to do |
|---|---|
| **"Windows protected your PC"** | Click **More info**, then **Run anyway**. This appears because the file came from the internet. |
| **"Do you want to allow this app to make changes?"** | Click **Yes**. This is Windows asking permission to install the two programs the website needs. It may appear twice. |
| A window full of scrolling text | Normal. It's working. Leave it alone. |

<!-- SCREENSHOT: the SmartScreen "More info / Run anyway" screen -->
<!-- SCREENSHOT: the Windows UAC permission dialog -->

### 3. That's it

You'll see **"All set"**, and an icon appears on your desktop:

> **MPCBC Website**

That icon is the only thing you need from now on. You can delete the ZIP file.

### If the app says this computer still needs setting up

Open the icon anyway. The window shows a short checklist — this is a progress
list, not a to-do list, because the **Install** button underneath does all of it:

```
THIS COMPUTER STILL NEEDS
  ✓  Install the programs it needs (Git and Node.js)
  ✓  Get the website files
  ○  Install the building blocks
  ○  Create the settings file

[                  Install                   ]
```

A tick means that part is already done. Click **Install** and wait — it
finishes whatever is missing, which takes a few minutes, then opens the site.
The checklist disappears and doesn't come back.

**Install is greyed out whenever there's nothing to install**, so you can tell
at a glance whether this computer needs anything.

Until it's finished, the other buttons stay greyed out on purpose. Nothing is
broken; they simply can't work yet.

**You don't need a GitHub account yet.** You only need one the first time you
publish something — see [Part 5](#part-5--before-your-first-publish-once).

---

## Part 2 — Every time you sit down to work

Double-click the **MPCBC Website** icon. You get one small window:

```
┌─ MPCBC Website ────────────────────────────────┐
│  ● Ready to start                              │
│    2 updates to collect, then the site opens.  │
│  ────────────────────────────────────────────  │
│  [              Update & Start              ]  │
│  [    Preview     ] [   Edit Content   ]       │
│  ────────────────────────────────────────────  │
│  [                   Code                   ]  │
│  [           Publish to Live Site           ]  │
│  [             Undo All Changes             ]  │
│  ────────────────────────────────────────────  │
│  [ Help ]                    [ Show Details ]  │
└────────────────────────────────────────────────┘
```

<!-- SCREENSHOT: the control panel, stopped state -->

**Click one button: `Update & Start`.**

That one button does everything needed to get going — collects anything other
people changed, installs anything missing, and starts the website. It takes a
few seconds most days, or a few minutes the very first time.

When the website is running, the same button turns into **Stop**.

The dot at the top tells you where you are:

| Dot | Meaning |
|---|---|
| ⚪ Grey | Not running. Press Start. |
| 🟡 Yellow | Starting up, or busy. Wait. |
| 🟢 Green | Running. Go ahead. |
| 🔴 Red | Something went wrong — see [Part 7](#part-7--when-something-looks-wrong). |

Buttons grey out when they don't apply. If a button won't click, it isn't
broken — it just isn't the right moment for it.

---

## Part 3 — Changing the words and pictures

Once the dot is green, click **Edit Content**.

Your browser opens the editor. On the left is a list of everything you can
change:

### Homepage

The big headline, the opening paragraph, the button, the background video, and
the stack of sections below.

### Announcements

Notices that appear on the homepage and the announcements page.

> **The one field you must fill in: "Remove after".**
>
> This is the date the announcement disappears by itself. It is how the site
> stays tidy without anyone remembering to come back and delete things. The
> editor won't let you save without it.

Other useful fields: **Pin to top** forces an announcement above the others,
**Title in Chinese** appears underneath the English title, and **Summary** is
the short version shown on the homepage.

### Events

Dated things — a picnic, a baptism, a members' meeting. Needs a name and a date;
everything else is optional.

### Pages

Whole pages, built by stacking sections (text, image + text, a photo gallery, a
video, buttons, and so on). You also control **where the page appears in the
menu** here.

### Sermons

**You never create these.** Every night the website checks YouTube and adds any
new sermons by itself.

What you *can* do is fill in the details it can't know — the speaker, the
scripture reference, the series — or tick **Hide from website** for a video that
shouldn't be there. Anything you type is safe: the nightly check only ever adds
new sermons, and never touches one that already exists.

### Pictures

Upload them in the editor wherever you see an image field. They're saved with
the website automatically.

### Seeing your changes

Click **Preview** at any time. The page updates by itself as you
edit — you don't need to refresh.

<!-- SCREENSHOT: the Tina editor at /admin with the collection list -->

---

## How the website is put together

Useful when you want to change something and aren't sure where to look.

### The pages, and where their words come from

| Page | Where the words live |
|---|---|
| Home `/` | **Homepage** in the editor, plus some fixed sections |
| English / Cantonese / Mandarin | **Fixed text.** Each page lists sermons automatically |
| Events `/events` | **Fixed heading.** The events themselves come from **Events** |
| Sermons `/sermons` | **Fixed heading.** The list comes from **Sermons** |
| Plan your visit `/visit` | **Fixed text — not editable in the editor** |
| Anything you created | **Pages** in the editor |

> **"Fixed text" means ask the website administrator.** Those words live in the website's code
> rather than the editor. It isn't that you're looking in the wrong place —
> they genuinely can't be changed from `Edit Content`, and that's worth
> knowing before you spend twenty minutes hunting.

### Adding a new page to the menu

You can do this yourself, entirely in the editor:

1. **Edit Content** → **Pages** → create a page
2. Fill in **Where in the menu** — *Not in the menu*, *Top level*, or under one
   of the four headings
3. Optionally set **Menu label**, **Menu label 中文**, and **Menu order**
   (lower numbers come first)

The four top-level headings — **About 關於我們**, **Services 崇拜**,
**Newsletter 通訊**, **Offering 奉獻** — are fixed, so the menu can't
accidentally be emptied. Putting a page under one turns it into a dropdown.

### Adding content by hand

If you're editing files rather than using the editor, everything you can
safely change is under **`src/content`**:

```
src/content/
  settings/homepage.json     the homepage
  pages/                     pages you created
  announcements/             one file per announcement
  events/                    one file per event
  sermons/                   one file per sermon  ← don't create these
```

Everything outside `src/content` is the machinery that makes the site work.
Changing it is a job for the website administrator or an AI helper, not a quick manual edit.

---

## Part 4 — Editing with a helper

Click **Code** for other ways to work.

### Open the folder — just look

Opens the website's folder in File Explorer. Nothing to install, and it works
with whatever you already use. If you only want to see what's there, or to
open a file in an editor of your own, start here.

### VS Code — editing the files yourself

Opens the website's files properly. Only useful if you're comfortable with that.

> **If you do this: only change things inside the `src/content` folder.**
> That's the words and pictures. Everything else is the machinery that makes the
> site work.

### Claude Code, Codex, or Antigravity — asking in plain English

These are AI assistants that can make changes for you. A window opens, and you
type what you want in ordinary words:

> *"Add an announcement about the Christmas Eve service on December 24th at
> 7pm in the main hall. It should disappear after December 26th."*

> *"The homepage headline is too long. Make it shorter and warmer."*

> *"Change the children's ministry page to say we meet at 9:30, not 10."*

If the assistant isn't installed, you'll be asked whether to install it. Say
yes; it takes a minute and only happens once.

The website already carries a set of notes for these assistants explaining how
it's built and what not to touch, so they generally get it right. **Always look
at the result** with **Preview** before publishing — and if you
don't like it, **Undo All Changes** puts everything back.

---

## Part 5 — Before your first publish (once)

Looking at the website needs nothing. **Publishing** needs a free GitHub
account, because that's where the website lives.

1. Go to **github.com** and create a free account, if you don't have one.
2. Tell the website administrator your username. They'll send you an invitation by email.
3. Click **Accept** in that email.

The first time you publish, a window will pop up asking you to sign in to
GitHub. Choose **Sign in with your browser**, log in, and you're done — your
computer remembers it from then on.

---

## Part 6 — Publishing

When you're happy with your changes, click **Publish to Live Site**.

**First you'll see a summary in plain English:**

> You are about to publish:
>
>     2 announcements, the homepage, 1 photo
>
> This puts them on the real website, where everyone can see them.

Read it. If it mentions something you didn't mean to change, click **No** and
ask the website administrator.

**Then you'll be asked what you changed.** Write it the way you'd tell a person:

> *Added the Christmas Eve service announcement*

This is saved in the website's history, so anyone can see what happened and
when.

**Then wait 2 to 4 minutes.** A page opens showing the progress. A green tick
means it's live. You can close everything at that point.

---

## Part 7 — When something looks wrong

**Almost everything is covered by three moves:**

### 1. The dot is red, or something behaves oddly

Click **Show Details**, then **Copy Log**. Paste that into a message to
him. That's everything he needs — you don't have to explain what happened.

### 2. You've made a mess and want to start over

Click **Undo All Changes**. It shows you exactly what it's about to throw away,
in plain words, and asks you to confirm.

This puts your computer back to the published version. **Anything already
published is completely safe** — undo only affects your own unfinished work.

### 3. It says your changes collided with someone else's

Stop, and send the website administrator the message. Nothing has been published and your work is
safe. This happens when two people edit at the same time, which is why
**Update & Start** collects everyone else's changes before it opens the site.

### Other things you might hit

| What happened | What to do |
|---|---|
| The website won't start | Close the app, open it again, press Start. If it still won't, use **Copy Log**. |
| Nothing happens when you click a button | It's probably greyed out because it isn't the right moment. Check the dot at the top. |
| The editor page won't load | Make sure the dot is green first. The editor needs the website running. |
| It says a program is using the same address | Restart the computer. Something was left running. |

---

## The short list of rules

1. **Start with `Update & Start`**, every time you sit down. It collects
   everyone else's changes before you begin.
2. **One person at a time**, where you can manage it. If two of you must work
   the same day, tell each other.
3. **Never share the `.env` file** with anyone, or paste it into a message. It's
   the website's password file. (You never need to open it.)
4. **Don't create sermons by hand** — they arrive on their own.
5. **Look before you publish.** Read the summary.
6. **If something says stop, stop**, and ask. Nothing is ever so urgent that
   it's worth guessing.

---

## If you're comfortable with code

Everything above works without knowing any of this. If you'd rather work from
the files, or point an AI assistant at the site, here's the onward path.

**The two documents that matter:**

| Document | What it covers |
|---|---|
| [README.md](README.md) | How the site is built — Astro, Cloudflare, the livestream board, the sermon sync, deployment |
| [AGENTS.md](AGENTS.md) | The brief for AI assistants. Imported by `CLAUDE.md`, and read automatically by Claude Code, Codex and Antigravity |

**Running it by hand**, if you'd rather not use the app:

```bash
npx tinacms dev      # terminal 1 — Tina's content server on :4001
npm run dev          # terminal 2 — the site on :4321
```

Order matters, and so do a few other things that look arbitrary and aren't —
Tina must finish indexing before Astro starts, `ASTRO_DEV_BACKGROUND=1` avoids
a 30-second watchdog, and you want `localhost` rather than `127.0.0.1` because
the dev server binds IPv6. `app/start.ps1` handles all of that, which is why
the app is usually the easier route even if you're technical.

**The editor is TinaCMS in local mode** — no account, no login, no seat limit.
The blank `.env` is deliberate: local mode needs no credentials, and editors
never touch Tina Cloud.

**The one rule:** `src/content/` is content and safe to change. Everything else
is the machinery. In particular, don't hand-write files in
`src/content/sermons/` (the nightly YouTube sync owns them) or edit
`src/data/services.json` (the livestream Worker overwrites it).

---

## Who to ask

The website administrator — for anything at all. There is no such thing as a silly question here,
and you genuinely cannot break the real website from this app.

<!-- Screenshots still to capture, once tested on a clean machine:
     - SmartScreen "More info / Run anyway"
     - Windows UAC permission dialog
     - Control panel: stopped, running, and red/crashed states
     - Tina editor at /admin
     - The publish confirmation dialog
     - The undo confirmation dialog
-->
