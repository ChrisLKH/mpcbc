# DIRECTION SHEET — Photographs at uneven sizes
seed 1597152153 · drawn 2026-11-03 · sector nonprofit
Brief: The sermon archive of a Chinese Baptist church in Alhambra: about 700 recorded Sunday sermons in Cantonese, Mandarin and English, searchable by title, speaker and series

| | |
| --- | --- |
| Direction | Photographs at uneven sizes |
| Hero | Three prints, one line |
| Type | kept from the brand |
| Colour | kept from the brand |
| Sections | showcase (Band of unequal panels) → work (Loose scatter with pauses) → list (Statement, table pushed right) → footer (Photograph half, menu half) |
| Motion | Shutter image |
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
A calm page where the pictures are placed like prints on a gallery wall: each at its own size and position, with text set small and light between them. Nothing lines up in the obvious way, yet everything sits on a grid you can feel. The unevenness is what makes it read as art-directed by a person: a small portrait far left, a wide landscape running off the right edge, a square that overlaps the next section. It lets photographs lead without ever becoming a slideshow.

Before anything else, write one sentence in your notes: "On this page, <the subject's own material> becomes <the device>."
Fill the first blank from your inventory of what only this client has (its name and what it means, its street, its numbers, its
constraint), then put the nearest competitor in the sentence: if it still works, the idea is the category's, not the client's.
Then name the one live thing the visitor can do on the page (SKILL.md step 4).

## 2. Composition
A 12-column grid with wide margins (5–7vw). The hero is a single line of large light type (6–9vw) across the top third, and below it one photograph placed off-centre (columns 6–12, 16:10), its top edge overlapping the baseline zone of the headline by a few pixels of deliberate tension. Each following section places two or three photographs with different spans and vertical offsets: for example a 3:4 portrait in columns 2–5 starting 120px lower than a 16:10 landscape in columns 7–12; then a single small 1:1 in columns 9–11 beside a paragraph in columns 2–6. Paragraphs are short (max 40ch) and sit in the gaps. No two consecutive sections use the same arrangement. Vertical spacing is large and irregular on purpose (120–260px).

## 3. Type
Kept from the brand. A light or regular weight at large size for the few headings; small, quiet text. Serif or a slender sans. Everything left-aligned to grid lines.

## 4. Colour
Kept from the brand. A pale neutral ground with one deep ink taken from the photographs. No accent beyond a quiet link colour.

## 5. Imagery
**Use the images that belong to the project in every picture slot.** For slots they cannot fill, real pictures of the subject's world from the open collections come next (start with `node C:/Projects/mpcbc/.agents/skills/design-taste-frontend/scripts/assets.mjs "alhambra postcard"`, then "alhambra old map" and "church", then queries from your inventory (its street, its materials, its trade's tools and history); credit them on the page), then the dealt photographs.

Six to nine photographs from one set, in a deliberate mix of ratios (3:4, 4:5, 1:1, 16:10, 21:9). Square corners. Captions, if any, are one short line under an image in the soft ink.

**Floor (the inspector checks it):** at least four real pictures on the page, usually six to twelve; SVG drawings do not count. One moving picture where an honest clip exists: `node <skill>/scripts/assets.mjs --video "<query>"`, used as a muted loop with a poster frame. Whatever this direction says about pictures, it is carried out with real ones.

Photographs dealt for this build, from the "People" set (figures, silhouettes and portraits, mostly unposed)
Order of preference for imagery: (1) the project's own photographs and product shots; (2) images you generate, if an image tool is
available; (3) real pictures of the subject's world found in the open collections (assets.mjs, downloaded and credited on the page);
(4) drawings of the subject in code; (5) these. They are real photographs (Unsplash licence, served by picsum.photos) of places, light and
texture. They are atmosphere, never a stand-in for a specific product, dish, person or interface. List the ones you use in your report so
they can be replaced.
- `https://picsum.photos/id/685/1600/1000` — Man sitting on a rock looking over a sea of clouds · mid tone · pale gold, grey, dark brown · calm area: right · native landscape · photo: Joshua Earle
- `https://picsum.photos/id/177/1600/1000` — Hiker with a backpack looking over a hazy mountain valley · mid tone · warm tan, hazy gold, olive · calm area: left · native landscape · photo: Danka & Peter
- `https://picsum.photos/id/669/1600/1000` — Person in a mustard jacket and grey beanie seen from behind in fog · light tone · mustard, pale grey, white · calm area: left · native landscape · photo: Luke Pamer
- `https://picsum.photos/id/338/1600/1000` — Person in a hooded coat seen from behind facing a misty sea · mid tone · slate blue, grey, charcoal · calm area: left · native landscape · photo: Patryk Sobczak
- `https://picsum.photos/id/1006/1600/1000` — Person sitting on a rocky overlook above misty forested hills · mid tone · dusky purple, olive green, grey · calm area: top · native landscape · photo: Vladimir Kudinov
- `https://picsum.photos/id/726/1600/1000` — Person in a red jacket standing in a snowy, misty landscape · light tone · white, pale grey, red · calm area: bottom · native landscape · photo: Caleb George
- `https://picsum.photos/id/1011/1600/1000` — Woman paddling a canoe on a turquoise lake edged by forest · mid tone · turquoise, dark green, rust red · native landscape · photo: Roberto Nickson
- `https://picsum.photos/id/447/1600/1000` — Man standing on a wall facing a blue lake and distant mountains · mid tone · blue, slate, white · calm area: left · native landscape · photo: Joshua Earle
The last two path numbers are width and height: request the exact crop you render (`/1200/1500` for a 4:5 portrait, `/2400/1350` for a hero) and
add `?grayscale` to desaturate. Use three to six of them, not all eight. Always set `width`, `height`, `alt` and `loading="lazy"` (not on the hero image).
Other sets that fit this brief: fog-and-quiet, city-day, object-still, mountain — `node draw.mjs --photos <set>` prints one in full; `--photos list` shows all sets. Taking photographs from another set is your call and is not a redraw; keep them to one light and one treatment.

## 6. Hero — Three prints, one line
Photographs lead and the type stays small. A pale ground, a 12-column grid, 5vw side margins. The top row at 15px holds the wordmark left, three text links centred and the single action right. Three photographs with square corners hang from one shared top line at 18% of the viewport height, like prints pinned along a rail, each a different size so their bottom edges fall at three heights. A small 1:1 in columns 1-2 (about 190px). A tall 3:4 in columns 4-7, about 440px wide and 590px tall, the largest thing on screen. A 16:10 landscape in columns 9-12 that continues past the right margin and is cropped by the viewport edge. Columns 3 and 8 are left empty as breathing space. The headline sits under the small photograph, left-aligned to its left edge across columns 1-3: a light serif or slender sans at clamp(30px, 2.8vw, 44px) on two lines, followed by one sentence at 16px in the soft ink, max 30ch. One line of caption at 14px sits under the left corner of each of the other two photographs.

On a phone: The rail turns vertical, so left edges vary instead of bottoms. The tall photograph comes first at 72% of the width on the left margin; the small square follows at 38% of the width, pushed to the right margin and overlapping the first by 40px; the landscape runs full bleed below. The headline at 8vw sits beside the square on the left, with the sentence at 16px under it.
Headline: A quiet 3-6 word line at about 40px, at most 36 characters across two lines.

Adjusted for this page:
- It names a kind of typeface: use the brand faces.

## 7. Sections, in order
Each is a composition to rebuild with this project's content. Keep the geometry; change the subject. Re-order or drop a
section when the argument of the page needs it (see references/copy.md), never to fall back to a card grid.

### 1. showcase — Band of unequal panels (strip)
A modest heading, then one wide band of pictures. The heading, two to four words at 36–44px, starts at column 3, left of centre, with 64px beneath it. The band spans the full viewport width edge to edge and is 68svh tall: four photographs standing side by side with no gaps and deliberately unequal widths, a flex row with flex-grow values of 5, 2, 3 and 2. Each panel is cropped with object-fit: cover to a tall slice, so the band reads as one folded view. The first panel carries a two-line title at 28px in paper colour at its top-left, 24px in, over a neutral scrim that covers only that corner. Hovering a panel raises its flex-grow to 6 over 500ms while the others give way; without hover the widths simply stay. One caption line at 16px sits under the band at the left margin.
On a phone: The heading sits on the margin at 28px. The band becomes a two-by-two block with no gaps, 84svh tall in total: the first row split 64/36 and the second 36/64, so the slices stay unequal. The title stays on the first panel. No hover behaviour.

### 2. work — Loose scatter with pauses (stack)
One word as heading at 32–40px, top-left on the margin. Projects then appear one at a time down the page, each a single photograph at its own width and side, with 22–30vh of empty ground between them. Use a repeating set of three placements on the 12-column grid: columns 1–6 hugging the left margin at 3:2; columns 9–12 hugging the right margin at 4:5, starting 80px above where the previous image ended so the two overlap in height; columns 3–9 inset at 16:9. Above each image's top-left corner, the project name at 13px in the soft ink; beneath its bottom-left corner, a one-line description at clamp(15px, 1.1vw, 17px) in full ink. Once, in the gap beside the narrow right-hand image, a two-line remark at 20px sits alone in columns 2–5. Square corners. Holds five to eight projects; more belongs in a denser index.
On a phone: Images alternate widths against alternating margins: 100%, 68% right, 82% left, with 72px between projects. The name above and the description below each image stay left-aligned to that image's edge. The two-line remark sits full width between the second and third project.

### 3. list — Statement, table pushed right (table)
A heading of four to six words spans the full width on two lines at clamp(40px, 4.5vw, 68px), left-aligned with its first line indented 3vw and leading 0.98. 96px below, the table is pushed into the right-hand five columns (8–12) and the left stays open. One plain word naming the table (offices, hours, rates) hangs at 14px in column 7, top-aligned with the first row. Each row is a three-part line at 15–16px with 26px between rows: the kind or day in the soft ink at the left of the measure, the place or service at 30%, and the address, time or price right-aligned, with a small arrow where the row links. Rows are separated by space only; every fourth row has 44px above it to form groups. In columns 1–4, bottom-aligned with the last row, one 4:5 photograph 22vw wide may sit alone. Holds 5–14 rows.
On a phone: Heading at 9.5vw without the indent. The table takes the full width at 16px; each row stacks the kind above the place, with the value right-aligned on the first line. The naming word is dropped because the heading already says it. The photograph follows at 60% width on the right margin.

### 4. footer — Photograph half, menu half (split)
A split footer one viewport tall. The left half is a single photograph filling columns 1–6 edge to edge, cropped hard by the left and bottom viewport edges: a close crop of the page's main subject, larger than it appeared anywhere above. The right half, on a flat field, is the site's menu set large: five or six section names stacked as links at about 2.6vw bold, 28px apart, starting at column 8 and 80px from the top. Rows are separated by space only; on hover a row's label shifts right by 12px and the others drop to the softer ink. Under the menu, 64px lower, two short blocks at 15px side by side: contact and social. The legal line runs along the very bottom of the right half at 12px. The wordmark sits small at the top of the right half, on the menu's left edge.
On a phone: The photograph leads at full width and 50svh, cropped tight. The menu follows on the flat field at 8vw per item with 20px between items, then contact and social stacked at 15px, then the legal line at 12px. The wordmark sits small above the menu.

Adjusted for this page:
- It sets text at 12px: use your small step (13px or more) instead.

## 8. Motion — Shutter image
The one hero or showcase image opens from an edge with a clip-path over about 1.1 seconds while the picture inside settles from 1.12 scale to rest. Use it once, on the image that matters most; later images simply are there. Recipe: 'Image shutter' in references/motion.md.
This is the signature move, the one loud moment. Around it the page still needs the rest of the motion layer (SKILL.md step 9): an entrance for the parts of every section, feedback on every control, and one moving picture. The inspector fails a page that barely moves. Recipes: references/motion.md.

## 10. Where the richness comes from
Placement. The differing sizes and offsets create movement on a still page.

## 11. How this direction goes wrong
Uneven is not random: every image edge lands on a column line, and offsets come from a small set of values. On a phone, keep the unevenness with alternating widths (100%, 72% left, 60% right) rather than stacking everything full width.

## 12. Real references
Studied sites that work this way. Take the principle, never the specifics: no names, copy, images or signature assets of theirs.
- **Sobha Privy Collection** (Awwwards Site of the Day, studied score 7.1) `aww-sobha-privy-collection`
  Idea: Luxury homes sold by restraint: a black sculptural render and a script-over-capitals title, then white pages where interior images grow and overlap huge serif capitals as you scroll.
  Hero: Full-bleed near-black video of a glossy black sculptural form, wide curved bands catching thin highlights, filling the frame at very low contrast. A centred two-line title about 46% of the width: a formal script phrase at about 6.9vw, then a line of light roman serif capitals at about 6vw directly beneath, wider than the script. Nothing else in the middle. Corners only: a two-line hamburger with a menu label top-left, a small serif wordmark with a micro subtitle top-centre, a text link top-right, a circled down-arrow bottom-left, and bottom-right a greyscale 3D-map thumbnail about 17% wide with a numeral and a two-line label.
  Worth taking: Pair one short phrase in formal script with a line of light serif capitals beneath it, letting the script overlap the capitals or run into an adjacent image.
- **Zara Webb** (One Page Love, studied score 8) `opl-zara-webb`
  Idea: A one-woman newborn-care practice presented like a hand-made keepsake: a mascot rabbit, scalloped and flower-shaped cut-outs, candid film photographs.
  Hero: Split exactly in half. Left, on cream: two-line mono caps nav stacks sit at the column's top-left and top-right with a handwritten script logo centred between them. Centred below, a five-line headline in a condensed serif, each line broken to one to three words so the block forms a ragged lozenge; then a three-line 16px sans paragraph and an outlined pill button in mono caps. A line-drawn rabbit on a skateboard rides a hairline at the column's bottom-right. Right: a candid flash photograph bleeds off the top and right edges and stops at 92% height; a lilac pill button and a round chat icon overlay its top-right corner.
  Worth taking: Mask photographs into soft custom shapes (a four-petal flower, a scalloped egg) and let their container crop them.
Full notes on any of them: `node draw.mjs --site <id>`.

## Always
Headlines two lines or fewer at every width. No eyebrow labels, no monospace, no 01/02/03, no thin rules, no gradients, no shadows at rest,
no cream-and-terracotta, no violet. The frame: one h1 on the first screen that says what this is, equal outer margins, no
section huddled on one side with the other half empty, no blank stretch over half a screen, a heading on every section.
`inspect.mjs` measures all of it.

Saved to C:/Projects/mpcbc/taste-mockup/.taste-skill/sermons-4/direction.json · reprint with `node draw.mjs --show`
