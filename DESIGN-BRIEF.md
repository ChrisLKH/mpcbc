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

Taken from the building itself — brown roof tile, tan stucco, white trim,
and the Southern California sky that dominates both reference photos. The
sky blue is the accent specifically to avoid the warm-clay tone church
sites default to.

| Token | Hex | Role |
|---|---|---|
| `--ink` | `#2A1D16` | body text, footer ground, dark bands |
| `--ink-soft` | `#5C4A3D` | secondary text, eyebrows, lede |
| `--paper` | `#F8F5EE` | page ground |
| `--stucco` | `#E5D9C5` | ghost-button hover, image placeholders |
| `--stucco-dim` | `#EFE8DA` | alternating section tint |
| `--sky` | `#2C6E9B` | primary button, focus ring, active nav |
| `--sky-deep` | `#1F5375` | link text, button hover |
| `--sage` | `#78896D` | reserved, currently unused |
| `--white` | `#FFFFFF` | button text, "live" card ground |
| `--live` | `#C4402F` | live-broadcast dot and label **only** |

`--live` red is load-bearing: it is the only saturated warm colour on the
site and it means exactly one thing — a stream is live right now. Do not
spend it on anything else.

Section rhythm is `--paper` alternating with `--stucco-dim`, set per block
by the editor rather than by position.

---

## Type

```
--font-display: 'Fraunces', 'Noto Serif TC', Georgia, serif
--font-body:    'Source Sans 3', 'Noto Sans TC', system sans
```

Fraunces and Source Sans 3 were chosen because both have Noto CJK
counterparts drawn on compatible metrics. That is the whole reason — it
means Chinese and English sit together without the size-and-weight mismatch
you normally get when CJK falls back to a system font. **Any substitution
has to clear the same bar.**

Headings use `font-variation-settings: 'SOFT' 0, 'WONK' 1` — Fraunces'
wonk axis on, softness off. Weight 600, `line-height: 1.12`,
`letter-spacing: -0.015em`.

Fluid scale, all `clamp()`:

| Token | Min → Max | Used for |
|---|---|---|
| `--step--1` | 0.83 → 0.9rem | eyebrows, nav, buttons, meta |
| `--step-0` | 1 → 1.11rem | body |
| `--step-1` | 1.2 → 1.5rem | h3, lede, brand Chinese |
| `--step-2` | 1.44 → 2rem | hero placeholder |
| `--step-3` | 1.73 → 2.7rem | h2 |
| `--step-4` | 2.07 → 3.6rem | h1 |

Measure is capped at `34rem` on every paragraph.

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

- `.wrap` — `min(72rem, 100% - 2×gutter)`, centred
- `.wrap--narrow` — `min(46rem, …)` for prose-led blocks
- `--gutter` — `clamp(1.15rem, 4vw, 2.5rem)`
- `.section` — `padding-block: clamp(3rem, 8vw, 5.5rem)`
- `--radius` — **3px.** Nearly square. Deliberate; it reads as civic and
  settled rather than app-like.

Breakpoints in use: `40rem` (footer), `44rem` (card grids, split layouts),
`46rem` (service board).

---

## Signature component — the service board

The most important thing on the site and the hardest to get right. It shows
all three services at once, in a 3-up grid above `46rem`, stacked below,
with 1px hairline dividers on a `rgba(42,29,22,0.13)` ground.

Each card carries, top to bottom: the service name in its own language
(`--step-1`, display face), the English name beneath it when the local name
is Chinese, an optional live badge, day + time, and a single call to action.

Three states, and **a visitor must not be able to tell which one is
"degraded"**:

| State | Ground | Badge | CTA |
|---|---|---|---|
| `live` | `--white` (lifts off the tint) | red dot + "Live now" | **Watch now**, solid |
| `scheduled` | inherits | none | Set a reminder, ghost |
| `unscheduled` | inherits | none | Go to channel, ghost |

The board is machine-driven — a cron Worker matches YouTube broadcasts to
services and the page refreshes state client-side. Design implication:
**every state must look intentional**, because the site cannot guarantee
which one it will be in on any given Sunday, and there is nobody editing it
weekly. Footer text says so: "Live links update automatically from YouTube.
Nobody edits this weekly."

---

## Component inventory

Editors compose pages by stacking these in any order. All nine are
available on every page, including the homepage.

| Block | Contents | Layout notes |
|---|---|---|
| Text | heading, body, tint toggle | `wrap--narrow` |
| Image with text | heading, body, image, alt, side L/R | 2-col ≥44rem, image order flips |
| Photo gallery | heading, layout grid\|strip, photos[] | grid `auto-fill minmax(180px,1fr)`; strip scroll-snaps horizontally |
| Video | heading, YouTube ID, caption | 16:9, `--ink` ground, `--radius` |
| Buttons | heading, links[] | first solid, rest ghost |
| Service times board | heading, intro | the component above |
| This week | heading, limit | 3-up cards, `--sky` 2px top rule |
| Recent sermons | heading, limit | 3-up cards + "All sermons" link |
| Facebook album link | heading, body, url, link text | ghost button |

Cards (`.card`) share one treatment throughout: a 2px `--sky` top border,
eyebrow, h3, optional meta line. Optional fields **must** disappear
cleanly — roughly a third of sermons have no speaker or scripture, and the
layout has to look deliberate either way.

**Chrome:** masthead is brand (Chinese `--step-1` display over English
`--step--1` in `--ink-soft`) plus a wrapping nav, hairline bottom rule on
`--paper`. Active nav item takes a 2px `--sky` underline. Footer is a full
`--ink` band, two columns above `40rem`: church name bilingual + tagline,
and the Sunday times.

**Homepage hero:** full-bleed 16:7 media (video or still) with a
brown→blue→sand gradient placeholder, then headline + lede + CTA in a
separate band below. Not overlaid text.

---

## Pages

| Route | What it is |
|---|---|
| `/` | hero, then editor-composed blocks |
| `/visit` | plain prose Q&A — parking, dress, children, which service, first fifteen minutes |
| `/sermons` | archive, auto-synced from YouTube |
| `/events` | upcoming events |
| `/english`, `/cantonese`, `/mandarin` | congregation pages, independent content |
| `/[...slug]` | anything an editor creates, e.g. `/childrens-ministry` |

Tone throughout is plain and unhurried — "Nobody will ask you to stand up
or introduce yourself." Match it. No exclamation marks, no marketing voice.

---

## Non-negotiables

1. **Bilingual parity.** Chinese is never secondary, never a toggle, never
   smaller. The `:lang()` rules above are typography, not preference.
2. **Accessibility.** Skip link, `:focus-visible` at 2px `--sky` with 3px
   offset, `prefers-reduced-motion` honoured, alt text required on every
   gallery photo at the CMS level, semantic lists with `role="list"`.
3. **The three service states must all look correct.** No "empty" state.
4. **Optional content vanishes cleanly.** Blank speaker, no photos, no
   intro line — all normal.
5. **Everything is editable.** Any block can appear on any page in any
   order, so no design may assume a fixed page composition.
6. **Static-first.** No framework in the delivered page. Effects must be
   achievable in CSS or a few lines of vanilla JS.

## Open to change

The palette and type pairing are considered settled. Genuinely open:
hero treatment, card and gallery styling, the service board's visual
hierarchy, section rhythm and spacing, and whether the congregation pages
should read more distinctly from each other than they currently do.
