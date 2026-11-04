# MPCBC — Design Brief

Handoff document for design work. Everything here is extracted from the
built site, not aspirational: the tokens are the real values in
`src/styles/global.css`, and the components are the ones `Blocks.astro`
actually renders.

---

## The church, and the design problem

Monterey Park Chinese Baptist Church, San Gabriel Valley, since 1976. Three
congregations share one building on one Sunday morning:

| | Time | Language |
|---|---|---|
| Cantonese 粵語 | 9:30 AM | Traditional Chinese |
| English | 11:00 AM | English |
| Mandarin 國語 | 11:30 AM | Traditional Chinese |

**The central design problem is that this is one church, not three.**
Grandparents worship in Cantonese, parents in Mandarin, children in English.
A design that reads as three separate micro-sites gets the sociology wrong;
so does one that treats Chinese as a translation layer bolted onto an
English site. The congregation pages are *separate content with their own
voice*, not mirrored copy.

The second problem: **Chinese and English have to sit on the same line, at
the same optical weight, constantly.** The masthead does it. The nav does
it. The service board does it three times over. Any design that only looks
right in one script has failed.

Audience skews older and not especially technical. Legibility and obvious
affordances beat cleverness every time.

---

## Palette

Taken from the church logo — the maroon banner, its white cross and
figures, and the near-black plum it sits on. Maroon is the church's own
colour and the one already printed on the sign, the bulletin and the
banner. This replaced an earlier building-derived scheme (brown tile, tan
stucco, sky blue) in the Homepage v3 redesign.

| Token | Hex | Role |
|---|---|---|
| `--ink` | `#221419` | body text |
| `--ink-soft` | `#6B5560` | secondary text, meta, lede |
| `--paper` | `#FFFFFF` | page ground |
| `--tint` | `#F8F3F4` | alternating section tint, ghost hover, live card |
| `--line` | `#E3D3D8` | hairlines, card borders |
| `--shell` | `#1B1014` | hero ground, footer band, video wells |
| `--accent` | `#943759` | primary button, eyebrow rules, focus ring |
| `--accent-deep` | `#6F2841` | link text, button hover |
| `--accent-pale` | `#F0DFE4` | light-button hover on dark grounds |
| `--white` | `#FFFFFF` | button text on accent and shell |
| `--live` | `#C2410C` | live-broadcast dot and label **only** |
| `--live-soft` | `#E8836B` | the same label on a `--shell` ground |

`--live` orange-red is load-bearing: it is the only colour on the site
outside the maroon family and it means exactly one thing — a stream is
live right now. Do not spend it on anything else. It is deliberately
distinct from `--accent` so a live badge cannot be mistaken for ordinary
brand chrome.

Section rhythm is `--paper` alternating with `--tint`, set per block by
the editor rather than by position.

`global.css` still defines `--sky`, `--sky-deep`, `--stucco`,
`--stucco-dim` and `--sage` as aliases onto the new roles, so pages not
yet migrated retheme with everything else. They are deprecated — use the
role names above, and delete the alias block once `/sermons`, `/events`
and the three congregation pages have been converted.

---

## Type

```
--font-display: 'Geist', 'Noto Sans TC', system-ui, sans-serif
--font-body:    'Geist', 'Noto Sans TC', system-ui, sans-serif
--font-meta:    'Inter', system-ui, sans-serif
--font-zh:      'Noto Serif TC', Georgia, serif
--font-hand:    'Nanum Pen Script', cursive
--font-mono:    ui-monospace, Menlo, Consolas, monospace
```

Geist carries display and body. Inter is used **only** for the small
uppercase meta labels — 13px, tracked out, where Geist's wider forms go
loose. Noto Serif TC sets Chinese at display sizes; Noto Sans TC is the
Chinese body fallback. Both Noto faces are drawn on metrics compatible
with the Latin ones, which is the whole reason they were chosen — it
means Chinese and English sit together without the size-and-weight
mismatch you normally get when CJK falls back to a system font. **Any
substitution has to clear the same bar.**

Nanum Pen Script is the handwritten aside — the note beside an
announcement, the newsletter confirmation. Never use it for anything
load-bearing; it is decorative and it is the first thing to fail a
legibility test with this audience.

Headings are weight 600 (h1 700), `line-height: 1.2`,
`letter-spacing: -0.03em`. There are no variable-font axes any more —
the Fraunces `SOFT`/`WONK` settings were removed with the face.

| Token | Value | Used for |
|---|---|---|
| `--step--2` | 0.8125rem | meta labels, kickers, dates |
| `--step--1` | 0.875rem | small body, nav, buttons |
| `--step-0` | 1rem | body |
| `--step-1` | 1.25rem | h3, Chinese display line |
| `--step-2` | 1.5 → 1.9rem | intermediate |
| `--step-3` | 1.75 → 2.25rem | h2 |
| `--step-4` | 2.2 → 3.4rem | page h1 |
| `--step-hero` | 2.9 → 5.5rem | homepage hero h1 **only** |

`--step-hero` is deliberately separate. It is sized for white text on the
full-bleed `--shell` ground and looks wrong anywhere else — a page h1
uses `--step-4`.

Measure is capped at `56ch` on every paragraph.

### Chinese typography is not a preference

`global.css` sets real per-language rules via `:lang()`, and they must
survive any redesign:

- **Leading `1.85`** vs 1.6 Latin — CJK glyphs are denser and squarer, and
  collide visually at Latin leading.
- **`font-size: 1.05em`** — at identical px, CJK reads smaller.
- **`text-align: justify` with `inter-character`** — CJK justifies cleanly
  because every character is one em wide. Latin justified without
  hyphenation looks bad, so it stays ragged-right.
- **`em`/`i` forced to `font-style: normal; font-weight: 600`** — Chinese
  has no italic tradition; browsers synthesise a slant that looks wrong.
- **`line-break: strict`** — different break rules from Latin.
- **English nested inside Chinese reverts to Latin settings.**

Headings override back to `line-height: 1.4`, `letter-spacing: 0.04em`,
ragged, and drop the Fraunces variation axes.

---

## Layout

- `.wrap` — `min(1080px, 100% - 2×gutter)`, centred
- `.wrap--narrow` — `min(46rem, …)` for prose-led blocks
- `--gutter` — `1.25rem`
- `.section` — `padding-block: clamp(3rem, 8vw, 5.5rem)`

Radius is no longer one value. The 3px "nearly square, civic" rule was
dropped with the logo palette; the redesign is built on rounded panels
and pill buttons:

| Token | Value | Used for |
|---|---|---|
| `--radius` | 16px | media wells, images, dropdown panel |
| `--radius-lg` | 24px | cards and panels |
| `--radius-sm` | 8px | inputs |
| `--radius-pill` | 999px | **every** button |

Breakpoints in use: `34rem` (event rows stack), `44rem` (card grids,
split layouts). The service board is `auto-fit / minmax(15rem, 1fr)` and
has no explicit breakpoint.

---

## Signature component — the service board

The most important thing on the site and the hardest to get right. It shows
all three services at once as separate bordered cards on a
`auto-fit / minmax(15rem, 1fr)` grid — 3-up on a wide viewport, stacking
on its own as space runs out.

Each card carries, top to bottom: a 16:9 `--shell` media well labelled
with the channel state, the service name in its own language (`--step-1`,
Noto Serif TC when Chinese), a meta line of name + time, a sentence about
that congregation, and two actions — the state-dependent one, then
"Archive 存檔" linking to `/sermons#<id>`.

The per-congregation sentences live in `ServiceBoard.astro`, **not** in
`services.json`: that file is overwritten by the scheduled YouTube sync,
so prose put there is lost on the next run.

Four states, and **a visitor must not be able to tell which one is
"degraded"**:

| State | Card | Well label | First CTA |
|---|---|---|---|
| `live` | `--tint` ground, `--accent` border | `--live` dot + "youtube · live now" | **Watch now**, solid |
| `scheduled` | inherits | "youtube · scheduled" | Set a reminder, ghost |
| `unscheduled` | inherits | "youtube · last recording" | Go to channel, ghost |
| `ended` | inherits | "youtube · last recording" | Watch the recording, ghost |

The board is machine-driven — a cron Worker matches YouTube broadcasts to
services and the page refreshes state client-side. Design implication:
**every state must look intentional**, because the site cannot guarantee
which one it will be in on any given Sunday, and there is nobody editing it
weekly. Footer text says so: "Live links update automatically from YouTube.
Nobody edits this weekly."

---

## Component inventory

Editors compose pages by stacking these in any order. All nine are
available on every page. On the homepage they render **below** the fixed
designed sections rather than composing the whole page — see Pages.

| Block | Contents | Layout notes |
|---|---|---|
| Text | heading, body, tint toggle | `wrap--narrow` |
| Image with text | heading, body, image, alt, side L/R | 2-col ≥44rem, image order flips |
| Photo gallery | heading, layout grid\|strip, photos[] | grid `auto-fill minmax(180px,1fr)`; strip scroll-snaps horizontally |
| Video | heading, YouTube ID, caption | 16:9, `--shell` ground, `--radius` |
| Buttons | heading, links[] | first solid, rest ghost |
| Service times board | heading, intro | the component above |
| This week | heading, limit | 3-up cards |
| Recent sermons | heading, limit | 3-up cards + "All sermons" link |
| Facebook album link | heading, body, url, link text | ghost button |

Cards (`.card`) share one treatment throughout: a 1px `--line` border at
`--radius-lg`, eyebrow in `--accent`, h3, optional meta line. Optional
fields **must** disappear cleanly — roughly a third of sermons have no
speaker or scripture, and the layout has to look deliberate either way.

**Chrome:** masthead is sticky, `rgba(255,255,255,0.9)` with an 8px
backdrop blur and a `--line` bottom rule. Brand is the logo image at 38px,
not type. Nav is 14px/500 with each item's Chinese in `--ink-soft`
alongside the English, and a "Plan a visit" pill in `--accent`. Footer is
a full `--shell` band in three columns: logo + address + contact +
social, Sunday times + office hours, and the newsletter signup.

The four top-level nav items — About 關於我們, Services 崇拜,
Newsletter 通訊, Offering 奉獻 — are fixed in `NAV_PARENTS` in
`Base.astro` so the menu cannot be emptied from the editor. Everything
else about the menu is editor-controlled: each page carries a
**"Where in the menu"** field (`menuParent`) set to `none`, `top`, or one
of those four keys, plus `menuLabel`, `menuLabelZh` and `menuOrder`.

Any parent that has pages assigned to it **becomes a dropdown**, styled
exactly like Services; its own destination moves to the foot of the panel
so it is never lost, and keeps its Chinese. A parent with no assigned
pages stays a plain link. There is no separate menu document and no
per-page special-casing in code.

A page in the menu is bilingual like every other item — `menuLabelZh` is
what keeps it that way, and leaving it blank is the one route to a nav
item that breaks parity.

**Homepage hero:** full-bleed, `min(84vh, 760px)`, media (video or still)
over a dotted `--shell` ground with a bottom-weighted scrim. Headline,
Chinese line, optional lede, CTA and the three service times sit
**overlaid** at the bottom — not in a separate band. With no media set,
the dotted ground is the finished treatment, not a placeholder gap.

---

## Pages

| Route | What it is |
|---|---|
| `/` | the hero, pinned to the top, then a reorderable list: the five designed sections — service board, announcements carousel, pastor's note, children & families, upcoming events — alongside editor-composed blocks, in whatever order the editor sets |
| `/visit` | plain prose Q&A — parking, dress, children, which service, first fifteen minutes |
| `/sermons` | archive, auto-synced from YouTube |
| `/events` | upcoming events |
| `/english`, `/cantonese`, `/mandarin` | congregation pages, independent content |
| `/[...slug]` | anything an editor creates, e.g. `/childrens-ministry` |

**Tina is the only CMS**, and every collection points at the same files
Astro renders under `src/content/`. There is no second tree: what an
editor changes is what `getCollection()` reads and what the build
publishes. Keystatic was removed along with `@astrojs/markdoc`, and the
prose collections moved from `.mdoc` to `.md`.

`Blocks.astro` still normalises three block shapes — Tina's file
`_template`, Tina's GraphQL `__typename`, and the legacy Keystatic
`{ discriminant, value }`. The file `_template` path is the one every
production build takes, since the Tina server is not running then; the
legacy branch is kept only so older content still renders.

Tone throughout is plain and unhurried — "Nobody will ask you to stand up
or introduce yourself." Match it. No exclamation marks, no marketing voice.

---

## Non-negotiables

1. **Bilingual parity.** Chinese is never secondary, never a toggle, never
   smaller. The `:lang()` rules above are typography, not preference.
2. **Accessibility.** Skip link, `:focus-visible` at 2px `--accent` with
   3px offset, `prefers-reduced-motion` honoured — it disables the
   announcement carousel's auto-advance outright — alt text required on
   every gallery photo at the CMS level, semantic lists with
   `role="list"`. The Services dropdown and the carousel are both
   keyboard-operable and close on Escape.
3. **The service states must all look correct.** No "empty" state.
4. **Optional content vanishes cleanly.** Blank speaker, no photos, no
   intro line, an announcement with no meta rows or handwritten note —
   all normal.
5. **Everything stays editable.** Every block remains available on every
   page. The homepage now leads with a fixed composition, but editor
   blocks still render beneath it, so no content type is locked out.
   Away from `/`, no design may assume a fixed page composition.
6. **Static-first.** No framework in the delivered page. Effects must be
   achievable in CSS or a few lines of vanilla JS. The masthead dropdown,
   the carousel, the signup form and the board's live refresh are each
   plain inline scripts that degrade to working markup without them.

## Open to change

The logo palette and the Geist/Inter pairing are settled — they come from
the church's own logo and were adopted deliberately in Homepage v3.
Genuinely open: the pastor's-note layout, the children's photo grid,
section rhythm and spacing, and whether the congregation pages should
read more distinctly from each other than they currently do.

## Known placeholders

Carried over from the design prototype and **not** real content:

- Pastor name, role and portrait (`PastorNote.astro` defaults)
- Phone `(626) 555-0100` and `office@mpcbc.org` in the footer
- The three children's-ministry photo wells
- The hero background video
- Newsletter signup — the form has no endpoint behind it and
  acknowledges locally only
- The "Offering 奉獻" nav item has no destination yet
