# Brief: Home visual pass (Design A, mpcbc.local)

You are editing the Home page of a WordPress + Elementor (free) church site
running in Local at `http://mpcbc.local/`. The structure and content are final.
This pass changes **only how sections look**, not what they say.

Read first, in this order: `../AGENTS.md`, `HANDOFF.md` (top notes are the latest
approved state). Where this brief and those files disagree, stop and ask.

## Hard rules

- Work only in `` and the `mpcbc.local` site. Never touch
  `archive/wordpress-design-b/`, `archive/`, or `.env`. Do not commit or push.
- Elementor **free** only. No HTML widgets, no custom CSS, no new plugins,
  no PHP/CSS in the child theme for content.
- **Atomic (V4) only.** Home is built from Atomic elements; keep it that way:
  - Layout: `e-flexbox` (and `e-background-video` for the hero, untouched),
    tabs: `e-tabs` / `e-tab` / `e-tab-content`.
  - Content: `e-heading`, `e-paragraph`, `e-button`, `e-image`.
  - Never add or convert to legacy V3 elements (`container`, `section`,
    `column`, `heading`, `text-editor`, `button`, `image` widgets).
  - Style through the element's Atomic style classes (`settings.classes` →
    the page's `styles` entries), with typed props (`$$type`: `size`,
    `dimensions`, `border-radius-v2`, `background`, …). Colours as
    `global-color-variable`, fonts as `global-font-variable`, never raw `color`.
  - Responsive values go in the style's breakpoint variants (desktop, tablet,
    mobile), not separate elements per device.
  - Do not run the old V3 builder/apply scripts in `scripts/` (anything that
    writes legacy widgets). Edit in the Elementor V4 editor, or write a new
    small PHP script that edits `_elementor_data` of the Atomic tree in place.
  - Look at `elementor/home-content-filled.json` to see the exact shape before
    changing anything.
- Colours and fonts only from the existing global variables (ids below). Add no
  new hex values.
- **Do not change the hero** (video, headline, button, Sunday times rail, pause
  control). It was approved on 20 Oct 2026.
- Do not change any words, links, forms, or `[bracketed placeholders]`.
  Do not invent content. Do not fix the Children time mismatch (hero says 10:00,
  Community › Kids says 11:00); the owner will decide it.
- Apply every change to **all three Home pages** (English, 繁體, 简体). Find their IDs with
  `scripts/wp.ps1 post list --post_type=page --fields=ID,post_title,post_name`.
  English Home is post 21. Home keeps the `elementor_header_footer` template.

## Before you edit

1. Back up `_elementor_data` of all three Home pages to
   `content-source/before-visual-pass/` (one JSON per page). Follow the pattern in
   `scripts/update-home-order-spacing.php` (loads WP, reads post meta).
2. Screenshot Home at 1440, 768 and 390px wide into
   `screenshots/before-visual-pass/` (see `scripts/take-cdp-screenshots.js`).

## Global variables to use

| Role | Variable | Value |
|---|---|---|
| Ink (all running text) | `e-gv-c70cdc4` | #13232A |
| Muted (meta lines only) | `e-gv-7317e11` | #4F6168 |
| Paper (light ground) | `e-gv-f7cfe2a` | #F6F7F5 |
| Mist (alt light ground) | `e-gv-824ebc4` | #E3EBEA |
| White | `e-gv-1984c94` | #FFFFFF |
| Harbor (primary) | `e-gv-1771cc2` | #155E75 |
| Deep sea (dark ground) | `e-gv-5a81035` | #0F2A33 |
| Soft persimmon | `e-gv-428b859` | #FCE3CC |
| Persimmon (Kids/Give buttons only) | `e-gv-0c168a6` | #F28C38 |
| Text on persimmon | `e-gv-96b5dde` | #2B1404 |
| Display font | `e-gv-d649b5e` | Bricolage Grotesque |
| Text font | `e-gv-f5301be` | Geist |
| Chinese font | `e-gv-2635213` | Noto Sans TC (SC page: `e-gv-99d935c`) |

## Part A: one visual system (every section below the hero, and the footer)

1. **Corners.** Containers, cards, panels and images: **0px** radius. Buttons:
   fully round (**999px**), matching the hero's "Join us this Sunday" button.
   Leave Fluent Forms fields as they are.
2. **Shadows.** Remove every box shadow from resting elements (cards, panels,
   images, buttons). Keep shadows only on the header dropdown menus.
3. **Type sizes.** Use only these five steps, desktop / tablet / phone:
   - Display 72 / 56 / 44px: each section's main heading (H2), Kids headline included.
   - Title 36 / 30 / 28px: featured item titles (e.g. the Newsletter issue title).
   - Subtitle 22 / 20 / 20px: Chinese section subtitles, card/item titles, pastor names.
   - Body 16px: all paragraphs and buttons.
   - Small 14px: meta lines (dates, speaker, "[Time] · [Place]"), footer legal line.
   Display tracking -0.02em, line height 1.0; body line height 1.55.
4. **Text colour.** Every paragraph and list item in **Ink**. Muted grey only on
   Small meta lines. White text on dark bands.
5. **No labels above headings.** Lines like "Pastor · Cantonese" and
   "Newsletter · July 2026" move to **below** the title as a Small muted meta line.
   Keep their words unchanged.
6. **Section spacing.** Vertical padding 120px desktop, 88px tablet, 64px phone.
   Side margins equal on both sides at every width, matching the header's edges.

## Part B: vary the section layouts

Background order after the hero (no two neighbours the same):

| # | Section | Ground | Layout change |
|---|---|---|---|
| 1 | This Sunday | Paper | Intro line moves under the heading. Drop the card boxes (no border, no fill). Cantonese item becomes the featured one: thumbnail about 60% width on the left, its text below it. English and Mandarin stack in the right column, smaller thumbnails beside their text. Phone: all three stack, featured first. |
| 2 | Newsletter | Mist | Intro under heading. Remove the outer white boxes. Left: issue title, text and buttons with the thumbnail to its right, sized so its label is not clipped. Right: "Explore more" list on a White panel (tone step, no border). |
| 3 | Meet our pastors | Paper | Intro under heading. Three large square portraits, no card box, no border; name, Chinese name, meta and one line under each. |
| 4 | Community | **Deep sea, full width** | White heading and text. Tabs stay on the left, panel on the right with its image filling the panel (square). Phone: tabs become a left-aligned vertical list, full width, not centred pills. Make sure no placeholder caption sits behind panel text. |
| 5 | Kids | **Soft persimmon, full width** (not a rounded card inside the page) | Text left; on the right **one** large image (the Sunday school photo) filling the right half to the band's edge. The other two photos are removed from the layout but stay in the media library. Button stays Persimmon with #2B1404 text. |
| 6 | Coming up / Recent sermons | Paper | Keep the two lists. Apply Part A only. |
| 7 | Contact | Mist | Left column: under the existing text, add the address, phone, email and office hours **copied exactly from the footer** (placeholders stay bracketed). Form card on the right stays White, no shadow. |
| 8 | Give | **Harbor, full width** | Photo left at full band height, square. Heading, Chinese subtitle and text in white. Give button stays Persimmon with #2B1404 text. |

Every section's small grey intro line in the top-right corner moves to sit
directly under the heading and Chinese subtitle, left-aligned, in Ink at Body size.

## After editing

1. `scripts/wp.ps1 elementor flush-css`.
2. Run `scripts/validate-atomic-conversion.php` and fix anything it reports.
3. Screenshot again at 1440, 768 and 390 into `screenshots/after-visual-pass/`.
   Check: no horizontal scroll at 390; every heading fits in two lines; nothing
   overlaps text; text on dark bands passes 4.5:1 contrast; the hero looks
   exactly as before.
4. Update the Home exports the way `HANDOFF.md` describes ("Current exports are
   saved from WordPress"), and add a dated note at the top of `HANDOFF.md`
   (one paragraph) naming this pass and the backup folder.
5. Report: what changed per section, anything you could not do in Elementor free
   (say why), and the before/after screenshot paths. Do not claim checks you did
   not run.
