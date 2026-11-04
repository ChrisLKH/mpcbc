# Rebuilding the taste mockup in Design A (Elementor)

Prepared 27 October 2026. Nothing has been changed in WordPress yet. This is the plan for turning
`taste-mockup/index.html` into Design A's Home page on `mpcbc.local`, within the repo rules
(`AGENTS.md`, `HANDOFF.md`).

## What Design A already has (checked on the local site)

- WordPress 7.1.3, **Elementor Free 4.3.4** with the Atomic editor on. Home (post 21) and the shared
  footer are already Atomic: `e-flexbox`, `e-div-block`, `e-heading`, `e-paragraph`, `e-button`,
  `e-image`, Atomic Tabs, Atomic Background Video (with play/pause controls), and three Fluent Forms
  placed through classic shortcode widgets (Elementor Free has no Atomic shortcode).
- Atomic widgets available in 4.3.4: heading, paragraph, button, image, SVG, divider, list, tabs,
  accordion, YouTube, self-hosted video, background video, grid, flexbox, div block, form.
- Atomic styles in 4.3.4 include background images and gradient overlays, borders (incl. dotted),
  radius, box shadow, opacity, filter, **transform (rotate, scale)**, **transition** and
  `clip-path`. Free **Interactions** offer fade, slide and scale on page load or scroll-in.
- Global colours and fonts are Atomic global variables (`content-source/atomic-global-variables.json`);
  the child theme `hello-mpcbc` carries fonts, header CSS, `nav.js`, `video-toggle.js` and
  language navigation.
- Home holds three reading languages: English text shared, Traditional and Simplified Chinese as
  separately editable copy pairs, switched by the header language selector.

## Tokens: new global variables

| Variable | Value | Use |
| --- | --- | --- |
| Page | `#E8E8E3` | page ground (newsprint grey) |
| Surface | `#DCDCD6` | This Sunday tiles, Coming up ground |
| Paper | `#FFFFFF` | cards, age tiles, calendar page, envelope, mats |
| Ink | `#181715` | text, dark sections, primary buttons |
| Muted | `#5C5B55` | secondary text |
| Field ink | `#E8E8E3` / muted `#B4B3AB` | text on dark sections |
| Accent | `#FFD21A` | primary buttons, coupon, highlights |
| Kids field | `#FFE680` | kids card, Newsletter ground |
| Coupon input | `#FFF4BF` | form fields on the coupon |
| Tone 3 | `#C4C4BC` | third ticket tile |

Fonts: **Hanken Grotesk** (Google Font; headings 500, text 400) and **Noto Sans TC** for Chinese.
They replace Bricolage Grotesque and Geist in Design A's global fonts.

Type presets (Global Typography / classes): Heading 40–56px/500/-0.035em · Title 20–24px/500 ·
Body 17px/400/1.5 · Small 15px/400 muted · Fact 15px/500 · Hero name 88px (hero keeps its own sizes).

## Section by section

✅ native Atomic · ⚠️ native but needs care or a check · ❌ not possible without code or a plugin

| Section | How to build | |
| --- | --- | --- |
| Header | Unchanged: Hello header + child theme CSS; add "Events" and "News" anchors to Appearance › Menus | ✅ |
| Hero | Existing Atomic Background Video with the same MP4/poster; name, Chinese name, button; a 5-column flexbox row of times | ✅ |
| Hero scrim | Gradient overlay on the video container | ✅ |
| This Sunday | Heading + 3 columns; each a ticket tile (Surface/Kids field/Tone 3) holding an **Atomic YouTube** widget, then title, fact line, latest sermon, links | ✅ |
| Ticket notches | Two 28px round div blocks per tile in the page colour, position absolute at mid-height | ⚠️ 6 extra elements |
| YouTube players | Atomic YouTube needs a **video URL**; it cannot show "this channel's live stream". Use the latest video per congregation, edited weekly, until the sermon plugin in `VIDEO-SERMON-IMPLEMENTATION-PLAN.md` exists | ⚠️ |
| Pastors | Dark section; mission paragraph with a yellow highlight (a span-style highlight needs inline markup → use a separate paragraph on a yellow pill instead); three white cards with image, role, name, Chinese name, line; rotate via transform | ⚠️ highlight |
| Card "dealing" motion | Interactions: slide from left / bottom / right, delays 0 / 90 / 180ms | ✅ |
| Kids | Owner's original layout: yellow card, heading, three white age tiles, two-column checklist, Chinese note, button | ✅ |
| Kids photo slideshow + slow zoom | Atomic has no background slideshow; Ken Burns is a classic-container feature | ❌ → one still photo with the white mat (padding on a white div), or keep one classic Container for the slideshow |
| Church life | Dark panel, three groups, rows with a dotted leader (flex row: name, a 4px dotted-bottom div that grows, value) | ✅ |
| Zigzag edges | No shape dividers in Atomic. Use an **Atomic SVG** zigzag strip above and below the panel | ✅ |
| Coming up | Events as rows (white date leaf + title + fact line), Bible plan, calendar page on the right | ✅ layout |
| Calendar marking event days | Elementor Free has no calendar. Needs **The Events Calendar** (free, month view) + an Elementor add-on, or a static monthly image. **Adding a plugin needs your approval** | ❌ without a plugin |
| Calendar "pad" holes | Dotted 14px top border in the section colour | ✅ |
| Newsletter | Atomic Tabs: July 2026 / January 2026; each panel = yellow plate with a white tilted "cover" (div + headings), title, text, two round buttons; Fluent Forms newsletter form below | ✅ |
| Visit + forms | Church photo, yellow coupon with negative top margin; left stub (address, times, directions); right Atomic Tabs with the two Fluent Forms | ✅ |
| Coupon tear line + notches | Dotted right border (bottom on phones) + two round div blocks | ✅ |
| Fluent Forms styling | Rounded, borderless pale-yellow fields need Fluent Forms' own style settings (free version check pending); otherwise plainer fields | ⚠️ |
| Give | White envelope; yellow striped edge as a repeating SVG **background image** on an outer div with 12px padding (Atomic has no border-image); stamp image rotated; postmark = round div with dotted border and two text lines | ⚠️ striped edge via background |
| Footer | Shared Atomic footer template: dark, four columns, large name, language links | ✅ |
| Motion | Interactions: fade in on scroll for headings, slide up for the coupon, scale-in for the calendar, a delayed drop for the stamp | ✅ (easing presets other than ease-in may be Pro) |
| Language copies | Every Chinese line needs its Traditional and Simplified copy pair, like the current Home | ⚠️ roughly doubles the Chinese text work |

## Decisions needed before building

1. **Where to build:** a new draft page "Home (taste)" beside the current Home (recommended), or
   replace Home 21 after a backup.
2. **Calendar:** install The Events Calendar (free) + an Elementor widget add-on, or show a static
   calendar of recurring days only.
3. **Kids photo:** one still photo (pure Atomic), or keep a classic Container for the slideshow.
4. **YouTube:** confirm one video URL per congregation to show by default.
5. **Fonts:** switch Design A's global fonts to Hanken Grotesk (affects every page, not only Home),
   or scope it to the new Home.

## How it would be built

Same pipeline Design A already uses: back up post 21 and the footer (`content-source/before-…`),
generate the Atomic JSON with a build script in `scripts/`, apply it with a PHP
script using `atomic-helpers.php` (Fluent Forms embeds, tab states), then validate with the existing
checkers and screenshot at 1440/1280/768/390. Media to upload: hero MP4s + poster, seven VBS stills,
church photo, steeple stamp, pastor portrait.
