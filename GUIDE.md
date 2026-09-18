# Editing the MPCBC Website

A guide for everyone who looks after the church website. **You do not need to
know anything technical.** If you can use email, you can do this.

> **Easier to read online:** <https://claude.ai/code/artifact/783b33cb-dcdb-48b3-a99d-8c7f77aca36c>
> — the same guide as a web page. Send that link to a new editor; it works
> before they have any of this on their computer.

> Nothing in this guide can break the real website. Everything happens on your
> own computer first, and nothing goes live until you press **Publish**.

---

## How it works, in one picture

```
   Your computer                         The real website
   ─────────────                         ────────────────

   1. Get the latest version   ◀──────── everyone else's changes
   2. Start the website
   3. Make your changes
   4. Look at them
   5. Publish  ─────────────────────────▶ live in 2-4 minutes
```

You always work on a **copy** on your own computer. You look at it, you change
your mind, you undo things. Only step 5 touches the website the public sees.

---

## Part 1 — Setting up your computer (once)

Chris will send you a file called **MPCBC-Website-Setup.zip**.

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
list, not a to-do list, because the single button underneath does all of it:

```
FIRST RUN - THE BUTTON BELOW DOES ALL OF THIS
  ✓  Install the programs it needs (Git and Node.js)
  ✓  Get the website files
  ○  Install the building blocks
  ○  Create the settings file

[     Start Working  (first-time setup)     ]
```

A tick means that part is already done. Click the button and wait — it
finishes whatever is missing, which takes a few minutes, then opens the site.
The checklist disappears and doesn't come back.

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
│  [            Start Working                 ]  │
│  [ Look at the Website ] [  Edit the Words   ] │
│  ────────────────────────────────────────────  │
│  [         Edit with a Helper               ]  │
│  [         Publish My Changes               ]  │
│  [          Undo My Changes                 ]  │
│  ────────────────────────────────────────────  │
│  [ Help ]                    [ Show Details ]  │
└────────────────────────────────────────────────┘
```

<!-- SCREENSHOT: the control panel, stopped state -->

**Click one button: `Start Working`.**

That one button does everything needed to get going — collects anything other
people changed, installs anything missing, and starts the website. It takes a
few seconds most days, or a few minutes the very first time.

When the website is running, the same button turns into **Stop the Website**.

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

Once the dot is green, click **Edit the Words**.

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

Click **Look at the Website** at any time. The page updates by itself as you
edit — you don't need to refresh.

<!-- SCREENSHOT: the Tina editor at /admin with the collection list -->

---

## Part 4 — Editing with a helper

Click **Edit with a Helper** for two other ways to work.

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
at the result** with **Look at the Website** before publishing — and if you
don't like it, **Undo My Changes** puts everything back.

---

## Part 5 — Before your first publish (once)

Looking at the website needs nothing. **Publishing** needs a free GitHub
account, because that's where the website lives.

1. Go to **github.com** and create a free account, if you don't have one.
2. Tell Chris your username. He'll send you an invitation by email.
3. Click **Accept** in that email.

The first time you publish, a window will pop up asking you to sign in to
GitHub. Choose **Sign in with your browser**, log in, and you're done — your
computer remembers it from then on.

---

## Part 6 — Publishing

When you're happy with your changes, click **Publish My Changes**.

**First you'll see a summary in plain English:**

> You are about to publish:
>
>     2 announcements, the homepage, 1 photo
>
> This puts them on the real website, where everyone can see them.

Read it. If it mentions something you didn't mean to change, click **No** and
ask Chris.

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

Click **Show Details**, then **Copy for Chris**. Paste that into a message to
him. That's everything he needs — you don't have to explain what happened.

### 2. You've made a mess and want to start over

Click **Undo My Changes**. It shows you exactly what it's about to throw away,
in plain words, and asks you to confirm.

This puts your computer back to the published version. **Anything already
published is completely safe** — undo only affects your own unfinished work.

### 3. It says your changes collided with someone else's

Stop, and send Chris the message. Nothing has been published and your work is
safe. This happens when two people edit at the same time, which is why
**Start Working** collects everyone else's changes before it opens the site.

### Other things you might hit

| What happened | What to do |
|---|---|
| The website won't start | Close the app, open it again, press Start. If it still won't, use **Copy for Chris**. |
| Nothing happens when you click a button | It's probably greyed out because it isn't the right moment. Check the dot at the top. |
| The editor page won't load | Make sure the dot is green first. The editor needs the website running. |
| It says a program is using the same address | Restart the computer. Something was left running. |

---

## The short list of rules

1. **Start with `Start Working`**, every time you sit down. It collects
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

## Who to ask

Chris — for anything at all. There is no such thing as a silly question here,
and you genuinely cannot break the real website from this app.

<!-- Screenshots still to capture, once tested on a clean machine:
     - SmartScreen "More info / Run anyway"
     - Windows UAC permission dialog
     - Control panel: stopped, running, and red/crashed states
     - Tina editor at /admin
     - The publish confirmation dialog
     - The undo confirmation dialog
-->
