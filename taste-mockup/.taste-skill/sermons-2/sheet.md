# DIRECTION SHEET — Colour chapters
seed 308125052 · drawn 2026-11-03 · sector nonprofit
Brief: The sermon archive of a Chinese Baptist church in Alhambra: about 700 recorded Sunday sermons in Cantonese, Mandarin and English, searchable by title, speaker and series

| | |
| --- | --- |
| Direction | Colour chapters |
| Hero | Stacked name, two inks |
| Type | kept from the brand |
| Colour | kept from the brand |
| Sections | showcase (Portrait on its own ground) → work (Giant title, uneven wall) → list (One item per poster line) → footer (Name first, places under it) |
| Motion | Masked headline |
| Photographs | the "People" set, as atmosphere |

This is the direction for this build. Adopt it. Do not blend it with the layout you would have made anyway,
and do not redraw because it is unfamiliar: unfamiliar is the point. Redraw a slot only when it conflicts
with a stated brand constraint or needs an asset that does not exist, and say why in your report.

The parts of this sheet were written separately and will disagree in small ways. Settle every disagreement in this order:
**house rules** (including type no smaller than 13px and body text of 16px or more) → **the direction** (the idea, the composition habits, where the
one accent goes) → **the type system and the palette** (which face does which job, the five size steps, which colour is the ground and where the
accent goes) → **the hero blueprint** → **the section blueprints**. Under each blueprint below, "Adjusted for this page" lists where it disagrees.
In practice: take geometry from blueprints (columns, proportions, what sits where) and take everything else from above them. Map every size a
blueprint names onto one of your five type steps instead of adding a sixth. Use one button shape, one corner radius and one pair of page margins on
the whole page, whatever individual blueprints say. If a blueprint names a kind of typeface (a light serif, a heavy grotesk), use the dealt face.

## 1. The idea
Every section is a chapter with its own flat colour, floor to ceiling. The page reads like a set of posters hung in a row, held together by one typeface and one grid. The colours are not decoration: they come from the subject (the brand's packaging, the artist's record sleeves, the product's own colour ways, the categories of the content) and each chapter's colour means that chapter. Decide the sequence like a designer orders a book: a strong opener, a quiet second, a surprise in the middle, a dark or very light closer.

Before anything else, write one sentence in your notes: "On this page, <the subject's own material> becomes <the device>."
Fill the first blank from your inventory of what only this client has (its name and what it means, its street, its numbers, its
constraint), then put the nearest competitor in the sentence: if it still works, the idea is the category's, not the client's.
Then name the one live thing the visitor can do on the page (SKILL.md step 4).

## 2. Composition
Sections are full-bleed fields, each at least 90svh tall, butted directly against each other with no gap and no divider. Inside every field the same 12-column grid and the same margins apply, and each field holds one thing: a huge heading (8–12vw) with a small paragraph, or one photograph with a caption, or one list. Text colour is chosen per field for strong contrast (ink or paper, never grey). Headings start on the same left edge in every chapter, so the eye feels the system as colours change. The navigation and the footer take the colours of the first and last chapter.

## 3. Type
Kept from the brand. One typeface in two weights. Headings very large and tight; body 18–20px. Because colour is loud, the type stays plain: no italics, no second face.

## 4. Colour
Kept from the brand. Four to six flat fields used at full strength, one per section, each with its assigned text colour. No tints, no gradients between them: the hard edge where two fields meet is the look.

## 5. Imagery
**Use the images that belong to the project in every picture slot.** For slots they cannot fill, real pictures of the subject's world from the open collections come next (start with `node C:/Projects/mpcbc/.agents/skills/design-taste-frontend/scripts/assets.mjs "alhambra postcard"`, then "alhambra old map" and "church", then queries from your inventory (its street, its materials, its trade's tools and history); credit them on the page), then the dealt photographs.

Sparing. One photograph or object per chapter at most, either cut out and sitting directly on the colour, or a plain rectangle with generous colour around it. Photographs should be simple enough not to fight the field.

**Floor (the inspector checks it):** at least four real pictures on the page, usually six to twelve; SVG drawings do not count. One moving picture where an honest clip exists: `node <skill>/scripts/assets.mjs --video "<query>"`, used as a muted loop with a poster frame. Whatever this direction says about pictures, it is carried out with real ones.

Photographs dealt for this build, from the "People" set (figures, silhouettes and portraits, mostly unposed)
Order of preference for imagery: (1) the project's own photographs and product shots; (2) images you generate, if an image tool is
available; (3) real pictures of the subject's world found in the open collections (assets.mjs, downloaded and credited on the page);
(4) drawings of the subject in code; (5) these. They are real photographs (Unsplash licence, served by picsum.photos) of places, light and
texture. They are atmosphere, never a stand-in for a specific product, dish, person or interface. List the ones you use in your report so
they can be replaced.
- `https://picsum.photos/id/177/1600/1000` — Hiker with a backpack looking over a hazy mountain valley · mid tone · warm tan, hazy gold, olive · calm area: left · native landscape · photo: Danka & Peter
- `https://picsum.photos/id/685/1600/1000` — Man sitting on a rock looking over a sea of clouds · mid tone · pale gold, grey, dark brown · calm area: right · native landscape · photo: Joshua Earle
- `https://picsum.photos/id/669/1600/1000` — Person in a mustard jacket and grey beanie seen from behind in fog · light tone · mustard, pale grey, white · calm area: left · native landscape · photo: Luke Pamer
- `https://picsum.photos/id/338/1600/1000` — Person in a hooded coat seen from behind facing a misty sea · mid tone · slate blue, grey, charcoal · calm area: left · native landscape · photo: Patryk Sobczak
- `https://picsum.photos/id/375/1600/1000` — Person in a hooded jacket sitting among white daisies at dusk · dark tone · navy, dark green, white · calm area: top · native landscape · photo: Wellington Sanipe
- `https://picsum.photos/id/660/1600/1000` — Woman in a knitted cardigan holding a lit sparkler in front of her · dark tone · dark teal, grey, warm white · calm area: left · native landscape · photo: Morgan Sessions
- `https://picsum.photos/id/473/1600/1000` — Person in dark clothes standing on a jetty facing misty mountains · mid tone · slate blue, black, pale grey · calm area: right · native landscape · photo: Todd Quackenbush
- `https://picsum.photos/id/1014/1600/1000` — Woman seen from behind facing a misty sea, a ribbon across her head · light tone · pale teal, soft grey, black · calm area: left · native landscape · photo: Oscar Keys
The last two path numbers are width and height: request the exact crop you render (`/1200/1500` for a 4:5 portrait, `/2400/1350` for a hero) and
add `?grayscale` to desaturate. Use three to six of them, not all eight. Always set `width`, `height`, `alt` and `loading="lazy"` (not on the hero image).
Other sets that fit this brief: fog-and-quiet, city-day, object-still, mountain — `node draw.mjs --photos <set>` prints one in full; `--photos list` shows all sets. Taking photographs from another set is your call and is not a redraw; keep them to one light and one treatment.

## 6. Hero — Stacked name, two inks
One saturated flat field covers the whole 1440×900 viewport and there is no image. The name is broken into two lines and set flush left from a 40px margin at clamp(110px, 15vw, 230px), heavy weight, tracking -0.04em, line-height 0.82 so the lines almost touch. The block starts 14% down and fills the top 58% of the screen; the longer line reaches at least column 11. Line one is set in the light ink and line two in the dark ink, so the break reads as a change of voice: split a two-word name between the lines, or break one long name with a hyphen at a syllable. The top edge carries only small text at 15px, 24px in: a monogram top-left, and two or three text links plus the single action as an underlined link top-right. The supporting sentence, 18px in the light ink and max 30ch, is right-aligned in the bottom-right corner on columns 9–12, its last line 40px above the bottom edge. The bottom-left corner stays empty. The next section begins as a different flat field exactly at the fold.

On a phone: The field fills 100svh. The name keeps its two lines and two inks at 21vw, flush left at a 16px margin, starting 18% down; a long name hyphenates rather than dropping below 18vw. The supporting sentence moves to the bottom-left at 16px across the full width, with the action as an underlined link beneath it, 24px above the bottom edge. The top-right links reduce to one word.
Headline: The name on exactly two lines, one per ink, each line up to about 8 characters at 15vw on desktop and 21vw on a phone.

Adjusted for this page:
- Its action is a text link: the primary action on the first screen is a real button (filled or solid-bordered, 44px or taller), placed where the blueprint puts the link. A second action may stay a link.

## 7. Sections, in order
Each is a composition to rebuild with this project's content. Keep the geometry; change the subject. Re-order or drop a
section when the argument of the page needs it (see references/copy.md), never to fall back to a card grid.

### 1. showcase — Portrait on its own ground (full-bleed)
One chapter per screen, three to six chapters. Each is a full-bleed field 100svh tall whose flat colour is taken from its own photograph: the darkest large tone in the picture (a deep brown for a wooden interior, a slate for a winter coast), so picture and page seem to be one surface. In the centre stands one portrait photograph at 3:4, 506×675px (75% of the height), top 112px, square corners, no frame. Type sits in the corners on 32px margins in the light ink: the chapter's title at clamp(28px, 2.4vw, 36px) top-left as its heading; one sentence at 17px, max 32ch, bottom-left; a text link to the chapter's page bottom-right. As each chapter arrives its field starts as a panel inset 20px from the viewport edges and grows to full-bleed, while the portrait scales from 0.92 to 1 (background colour over 600ms, scale over 800ms, both easing out), so the change reads as the room changing, not as a new slide. A chapter may hold a short muted loop or a single enlarged letterform in place of the portrait, on the same kind of field. The first chapter's title doubles as the section heading.
On a phone: The portrait becomes 80% of the width at 3:4, centred at 45% of the height. The title sits top-left at 24px, and the sentence and link stack bottom-left at 16px under the portrait. The field still takes the picture's darkest tone.

Adjusted for this page:
- It names colours (brown): map them onto this palette's roles (ground `--bg`, panels `--surface` and the fields, text `--ink`/`--muted`, the accent `--accent` on the primary action only).

### 2. work — Giant title, uneven wall (mosaic)
A one- or two-word title in capitals at clamp(72px, 9vw, 136px), leading 0.9, sits flush left at the 16px margin; on its baseline at the far right, one underlined link at 15px to the full archive. 32px below, thumbnails pack into an uneven wall with 8px gaps: a flex-wrap layout in which each row has a different fixed height (cycle 240, 360, 200, 300px) and each tile's width follows its image ratio, so widths range from one-sixth to one-half of the page and tile edges do not line up between rows. Rows are justified to both margins with `flex-grow`. Under each image, one caption line at 12px: project name left, year in the soft ink right. Square corners; no hover overlay beyond a slight dimming of the other tiles. Holds 9–16 projects. The section ground is one flat colour, dark or saturated, edge to edge.
On a phone: Title at 17vw on one line. The wall becomes rows of alternating composition: one full-width tile, then two tiles splitting the width 62/38, then 38/62, with 6px gaps and fixed row heights of 220 and 150px. Captions stay as one line under each tile.

Adjusted for this page:
- It sets text at 12px: use your small step (13px or more) instead.
- It names a kind of typeface: use the brand faces.

### 3. list — One item per poster line (statement)
A short list set as a poster. Four to seven items (services, headline acts, courses, opening days) stack one per line at clamp(64px, 10vw, 150px) in the display weight, all capitals or all lowercase, leading 0.92, flush left at a 20px margin on a flat field. Its one detail (a price, a date, a time range) sits at the right margin on the same baseline at 18–20px with tabular figures, so the page has two alignments only: giant words left, small facts right. Items too long for one line at this size are shortened, not shrunk. Between items there is only the tight leading. Hovering an item swaps its ink for the accent. There is no heading: the first item starts 80px below the section top and the last ends 80px above the bottom. The list reuses the hero title's exact type size, so it reads as another poster.
On a phone: Items at 15vw, one per line, flush left with 16px margins. Each detail moves beneath its item at 15px, left-aligned, with 6px above and 22px below. If any single word is wider than the screen at 15vw, drop the whole list to 12vw rather than breaking a word.

Adjusted for this page:
- It names a kind of typeface: use the brand faces.

### 4. footer — Name first, places under it (list)
The wordmark opens the footer instead of closing it. On a full-bleed field in the page's inverse or final chapter colour, the name spans the full content width as the first thing, its cap line pushed 8% above the footer's top edge so the tops of the letters are sliced off by the section (`overflow: hidden` and a negative top margin in `em`). 72px beneath, a two-part row: columns 1–5 list the places or ways to reach the studio (cities, or email and telephone), one per line at 32–36px in the accent ink, each a link; columns 7–12 hold three columns of small links at 15px, top-aligned with the first place. A final row 96px lower carries the legal line at the left and a back-to-top text link at the right, both 13px. The field is about 90svh tall, and nothing sits between the wordmark and the row but space.
On a phone: The sliced wordmark stays at the top, fitted to the width. Places follow as a list at 26px, then the link columns two across at 15px. Legal line and back-to-top link share the last row at 12px, at opposite margins.

Adjusted for this page:
- It sets text at 12px: use your small step (13px or more) instead.
- It crops letters along their top: the inspector fails that (jurors read it as broken). Crop at the bottom edge or a side instead.

## 8. Motion — Masked headline
On load, each line of the headline rises from behind its own baseline, about 900ms with a strong ease-out, the second line 110ms after the first; the supporting sentence and the action then fade up together. Nothing else on the page performs. Because headlines are two lines at most, you author the line breaks and wrap each line yourself. Recipe: 'Masked line reveal' in references/motion.md.
This is the signature move, the one loud moment. Around it the page still needs the rest of the motion layer (SKILL.md step 9): an entrance for the parts of every section, feedback on every control, and one moving picture. The inspector fails a page that barely moves. Recipes: references/motion.md.

## 10. Where the richness comes from
Colour at scale. A whole screen of one saturated colour is rich on its own, so a chapter needs few elements; it still needs its content at full size, because a coloured screen with one small line on it reads as empty.

## 11. How this direction goes wrong
Check contrast on every field with the palette's assigned ink. Do not put cards on the fields. Do not use more than six colours. Two neighbouring fields should differ clearly in lightness as well as hue.

## 12. Real references
Studied sites that work this way. Take the principle, never the specifics: no names, copy, images or signature assets of theirs.
- **Paul Kalkbrenner** (Awwwards Site of the Day, studied score 8.1) `aww-paul-kalkbrenner`
  Idea: Swiss poster typography for a techno artist: the name as one black line with a photo set inside it like a glyph, then every chapter gets its own flat colour and visible grid.
  Hero: Pure white viewport. The artist's name sits on one line at exact vertical centre in bold black neo-grotesk, about 10.4vw, spanning roughly 78% of the width with equal side margins. The space between first and last name is replaced by a small landscape black-and-white photo, x-height tall and resting on the baseline. Top-left is a tiny circular monogram; eight nav links in 13px bold are spread at even intervals across the right half of the top edge. Bottom edge: equaliser bars with a grey label over a track title at left, a three-line bold blurb starting just left of centre, a sound toggle hard right. About 85% is empty.
  Worth taking: Replace the space between the two words of a giant name with a small photo cropped to x-height.
- **Anidachi** (One Page Love, studied score 7.6) `opl-anidachi`
  Idea: A toy brand built from one custom modular wordmark and one 3D device, both of which change colour with each full-bleed candy-coloured section.
  Hero: A saturated sky-blue field covering the top 92% of the viewport. The custom modular wordmark is repeated in four stacked rows, each spanning the full width inside 30px margins, drawn tone-on-tone in a slightly lighter blue that brightens row by row toward the bottom, so the name works as wallpaper. A tiny white pixel-cat mark sits top-left; a white rounded button with blue text top-right. A 3D flower-shaped toy device hangs on a carabiner from the top edge at centre, barely entered on desktop and fully visible on mobile, where it covers the pattern. A pale ice section starts at the bottom edge.
  Worth taking: Repeat the wordmark as tone-on-tone rows filling the hero, stepping the tint row by row, so the brand name is the background pattern.
Full notes on any of them: `node draw.mjs --site <id>`.

## Always
Headlines two lines or fewer at every width. No eyebrow labels, no monospace, no 01/02/03, no thin rules, no gradients, no shadows at rest,
no cream-and-terracotta, no violet. The frame: one h1 on the first screen that says what this is, equal outer margins, no
section huddled on one side with the other half empty, no blank stretch over half a screen, a heading on every section.
`inspect.mjs` measures all of it.

Saved to C:/Projects/mpcbc/taste-mockup/.taste-skill/sermons-2/direction.json · reprint with `node draw.mjs --show`
