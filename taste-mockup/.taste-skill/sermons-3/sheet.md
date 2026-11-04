# DIRECTION SHEET — Type and nothing else
seed 1478049412 · drawn 2026-11-03 · sector nonprofit
Brief: The sermon archive of a Chinese Baptist church in Alhambra: about 700 recorded Sunday sermons in Cantonese, Mandarin and English, searchable by title, speaker and series

| | |
| --- | --- |
| Direction | Type and nothing else |
| Hero | One word, cropped |
| Type | kept from the brand |
| Colour | kept from the brand |
| Sections | showcase (Two giant words compared) → work (Book-spine index) → list (Line-up at one type size) → footer (Columns above, wordmark below) |
| Motion | Scaling wordmark |
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
The first screen is made only of words, colour and space, good enough that nobody misses a picture there. Pictures arrive later, large and few, as relief from the type. This is the honest answer when a subject has nothing worth photographing: instead of inventing illustrations, set what you have to say as well as it can be set. The typography carries a point of view through scale (one enormous statement, everything else small), through measure (a narrow, bookish column or a full-width line), and through one committed colour.

Before anything else, write one sentence in your notes: "On this page, <the subject's own material> becomes <the device>."
Fill the first blank from your inventory of what only this client has (its name and what it means, its street, its numbers, its
constraint), then put the nearest competitor in the sentence: if it still works, the idea is the category's, not the client's.
Then name the one live thing the visitor can do on the page (SKILL.md step 4).

## 2. Composition
First screen: the statement, two lines at most, set at 9–14vw and occupying the full width with tight leading, positioned in the upper two-thirds. One supporting sentence at 20–24px sits bottom-left on the same margin and the single action sits bottom-right on the same baseline. No navigation bar; two or three links in a top corner. Sections that follow are typographic set pieces, each different: a list of five to eight capabilities set at 4–6vw one per line; a paragraph at 28–36px in a 28ch column pushed to the right half; a pair of large numerals with a sentence each; a single quotation filling a colour field. Each section uses a different horizontal position (left, right, full) so the page has movement without imagery.

## 3. Type
Kept from the brand. This direction lives or dies on the face and its settings. Display at tight tracking and leading; text sizes few and far apart (for example 15, 22, 34 and the display size). Use real typographic detail: proper quotes, balanced line breaks, hung punctuation where it helps.

## 4. Colour
Kept from the brand. Either one saturated ground for the whole page with a single ink, or paper and ink with one section inverted and one in the accent as a full field. Colour does the job images would have done.

## 5. Imagery
**Use the images that belong to the project in every picture slot.** For slots they cannot fill, real pictures of the subject's world from the open collections come next (start with `node C:/Projects/mpcbc/.agents/skills/design-taste-frontend/scripts/assets.mjs "alhambra postcard"`, then "alhambra old map" and "church", then queries from your inventory (its street, its materials, its trade's tools and history); credit them on the page), then the dealt photographs.

The first screen has no picture. Further down, real pictures of the subject's world (found with scripts/assets.mjs) arrive large and few: a full-bleed photograph between two type sections, a looping clip in one band, an archival image beside a list. Do not add icons, shapes, patterns or abstract graphics to compensate for anything.

**Floor (the inspector checks it):** at least four real pictures on the page, usually six to twelve; SVG drawings do not count. One moving picture where an honest clip exists: `node <skill>/scripts/assets.mjs --video "<query>"`, used as a muted loop with a poster frame. Whatever this direction says about pictures, it is carried out with real ones.

Photographs dealt for this build, from the "People" set (figures, silhouettes and portraits, mostly unposed)
Order of preference for imagery: (1) the project's own photographs and product shots; (2) images you generate, if an image tool is
available; (3) real pictures of the subject's world found in the open collections (assets.mjs, downloaded and credited on the page);
(4) drawings of the subject in code; (5) these. They are real photographs (Unsplash licence, served by picsum.photos) of places, light and
texture. They are atmosphere, never a stand-in for a specific product, dish, person or interface. List the ones you use in your report so
they can be replaced.
- `https://picsum.photos/id/338/1600/1000` — Person in a hooded coat seen from behind facing a misty sea · mid tone · slate blue, grey, charcoal · calm area: left · native landscape · photo: Patryk Sobczak
- `https://picsum.photos/id/669/1600/1000` — Person in a mustard jacket and grey beanie seen from behind in fog · light tone · mustard, pale grey, white · calm area: left · native landscape · photo: Luke Pamer
- `https://picsum.photos/id/685/1600/1000` — Man sitting on a rock looking over a sea of clouds · mid tone · pale gold, grey, dark brown · calm area: right · native landscape · photo: Joshua Earle
- `https://picsum.photos/id/177/1600/1000` — Hiker with a backpack looking over a hazy mountain valley · mid tone · warm tan, hazy gold, olive · calm area: left · native landscape · photo: Danka & Peter
- `https://picsum.photos/id/375/1600/1000` — Person in a hooded jacket sitting among white daisies at dusk · dark tone · navy, dark green, white · calm area: top · native landscape · photo: Wellington Sanipe
- `https://picsum.photos/id/841/1600/1000` — Swimmer mid-stroke in a dark lake beneath moody mountains · dark tone · slate grey, dark blue, charcoal · calm area: top · native landscape · photo: Todd Quackenbush
- `https://picsum.photos/id/660/1600/1000` — Woman in a knitted cardigan holding a lit sparkler in front of her · dark tone · dark teal, grey, warm white · calm area: left · native landscape · photo: Morgan Sessions
- `https://picsum.photos/id/726/1600/1000` — Person in a red jacket standing in a snowy, misty landscape · light tone · white, pale grey, red · calm area: bottom · native landscape · photo: Caleb George
The last two path numbers are width and height: request the exact crop you render (`/1200/1500` for a 4:5 portrait, `/2400/1350` for a hero) and
add `?grayscale` to desaturate. Use three to six of them, not all eight. Always set `width`, `height`, `alt` and `loading="lazy"` (not on the hero image).
Other sets that fit this brief: fog-and-quiet, city-day, object-still, mountain — `node draw.mjs --photos <set>` prints one in full; `--photos list` shows all sets. Taking photographs from another set is your call and is not a redraw; keep them to one light and one treatment.

## 6. Hero — One word, cropped
A single word is the whole composition, set so large that the viewport crops it. No image. The word is one line in a heavy condensed or heavy grotesk face at clamp(360px, 46vw, 760px), line-height 0.8, white-space: nowrap, placed so its capitals fill the lower half of the screen: the baseline sits 6% below the bottom edge so the feet of the letters are cut, and the line is shifted 8vw to the left so the first letter is cut by the left edge and the last runs off the right (overflow: hidden on the section). Raise the size until both end letters are cut; three to five letters stay fully visible and the word still reads. The top 30% holds everything else on 28px margins: the wordmark at 16px top-left, three text links top-right, and, starting 96px down on the left margin, one supporting sentence at clamp(20px, 1.7vw, 26px), max 34ch, with the single action directly beneath it as a solid rectangle. Ground and ink are two flat values; the word may take the palette's one accent instead of the ink.

On a phone: The word turns a quarter turn (writing-mode: vertical-rl) and runs down the right side at 62vw, a fifth of its height cut by the right edge and its ends cut by the top and bottom. The left 45% of the screen holds the wordmark, the sentence at 17px and the action, stacked from the top on a 20px margin. Links fold behind a menu word.
Headline: One word of 4-8 letters, the name or the single noun the subject owns; it is cropped but must stay readable.

Adjusted for this page:
- It names a kind of typeface: use the brand faces.

## 7. Sections, in order
Each is a composition to rebuild with this project's content. Keep the geometry; change the subject. Re-order or drop a
section when the argument of the page needs it (see references/copy.md), never to fall back to a card grid.

### 1. showcase — Two giant words compared (statement)
No photographs: the thing shown is a pair of words, characters or figures that make the point by comparison (before and after, input and output, ours and the usual). At the top of the section, on one row: the heading, two to four words at 28–36px, at the left margin, and a paragraph of up to 60 words at 17px starting at column 7. Then 12vh of space. Below, two specimens side by side, one starting on column 1 and one on column 7, each set at clamp(180px, 34vw, 520px) with leading 0.8, so each fills about half the viewport's width. The section has a fixed height and overflow: hidden, so its bottom edge cuts both specimens at roughly 60% of their height. One short caption at 16px sits above the top-left of each specimen, naming it. Both specimens share one ink; the difference is in the letterforms or values, not in colour.
On a phone: Heading first, then the paragraph, at full width. The two specimens stack, each at 62vw type size in its own 38svh box with overflow hidden so the lower part is cut. Captions sit above each box at 15px. The second box takes the inverse colours of the first.

### 2. work — Book-spine index (index)
The complete index in the footprint of a banner. Twenty to forty project names are set as single lines rotated to read top to bottom with `writing-mode: vertical-rl`, packed side by side like book spines across the full content width. Each spine is a column 30–34px wide and 420px tall: the name at 18px starts at the top, and the discipline follows after a 12px gap on a flat highlighter-colour background chosen from four palette colours by discipline. Spines are separated only by 6px of space. Hovering or focusing a spine turns it to full ink and dims the others to 40%; the whole spine is the link. A two- or three-word heading at 28px sits top-left above the shelf with a one-sentence note at 15px to its right on the same baseline. No images. Below the shelf, 96px of empty ground. It turns a long client list into one dense typographic object.
On a phone: Rotation is dropped. Names become a horizontal wrap: each name at 17px followed by its highlighted discipline, items flowing inline with 10px gaps and 14px of line spacing, so the index reads as one dense paragraph of links filling the width.

Adjusted for this page:
- It fades text: anything a visitor reads, inactive items of a list included, is `--ink` or `--muted` at full strength (contrast 4.5:1 or more). The active item is told apart by `--ink` against `--muted`, weight or the accent, not by fading the rest.

### 3. list — Line-up at one type size (table)
A full-bleed flat section. The programme is a four-column table spanning the full width between 24px margins, with every cell at one size, clamp(20px, 2.1vw, 30px), one weight, no bold: the act or session flush left, the format starting at 33%, the room or stage at 66%, and the time range right-aligned at the far edge with tabular figures. Rows are 1.5em tall and separated by nothing but that leading; the fixed column starts do the organising. Day or part headings are a single line at the same size in the accent ink with 72px above and 20px below, and may be `position: sticky; top: 0` on a strip of the section's ground colour so the current day stays in view. Breaks and changeovers are rows set in capitals in the soft ink. There is no other heading. Comfortably holds 12–40 rows; with fewer than eight, raise the size to 3vw.
On a phone: Each entry becomes two lines: the act at 22px on the first, then format, room and time on one line at 14px in the soft ink with the time right-aligned. 18px between entries. Day headings stay sticky at the top on the ground colour.

Adjusted for this page:
- It names a kind of typeface: use the brand faces.

### 4. footer — Columns above, wordmark below (stack)
The footer inverts ground and ink and fills a whole viewport (min-height 100svh), arranged as three rows in a grid (auto 1fr auto). Top row, 40px from the edge: four small columns on the 12-column grid (address, navigation, social, hours or contact), each a plain list at 15–16px with its first line in the full ink and the rest in the softer ink. Middle row: empty. Bottom row: the wordmark alone, sized to run from the left margin to the right margin on one line. Set it as inline SVG text with a `viewBox` fitted to the word and `width: 100%`, or tune `font-size` in `vw` to the letter count, so it never wraps; its baseline sits 20px above a single line of legal text at 13px. With `position: sticky; bottom: 0` and a lower `z-index` than the page, the last section slides away to uncover it.
On a phone: Columns become two by two at 15px with 32px between rows, at the top. A gap of about 20svh follows. The wordmark spans the full width at the bottom; a name longer than ten letters stacks on two lines, each fitted to the width. Legal line beneath at 12px.

Adjusted for this page:
- It sets text at 12px: use your small step (13px or more) instead.

## 8. Motion — Scaling wordmark
The giant name responds to the scroll: as the first screen leaves, the wordmark scales down a few percent (or grows from 0.94 to 1 as the footer arrives) on a scroll-linked timeline, so the monument feels heavy. Travel stays under 8%. Static fallback where scroll timelines are unsupported. Recipe: 'Scroll-linked progress' in references/motion.md, animating `scale`.
This is the signature move, the one loud moment. Around it the page still needs the rest of the motion layer (SKILL.md step 9): an entrance for the parts of every section, feedback on every control, and one moving picture. The inspector fails a page that barely moves. Recipes: references/motion.md.

## 10. Where the richness comes from
Scale contrast and colour mass. A capability list set at 5vw is an image in its own right.

## 11. How this direction goes wrong
Without pictures, weak copy has nowhere to hide: write real sentences. Vary the set pieces; four sections of big-heading-plus-paragraph is a text document, not a design. Keep all headings within two lines.

## 12. Real references
Studied sites that work this way. Take the principle, never the specifics: no names, copy, images or signature assets of theirs.
- **bleibtgleich’26** (Awwwards Site of the Day, studied score 7.5) `aww-bleibtgleich26`
  Idea: One thin vertical rule runs down the page and a tight bold grotesk sentence wraps around it, words stepping from one side of the line to the other.
  Hero: White page, black type. Content sits in a narrow band between 26% and 68% of the width. A 1px vertical rule drops from the top edge at 37% for two-thirds of the height. Left of it: a black blob avatar at the top, a tiny role caption at its foot. Right of it: a tiny location note at the top, the name in two lines of 90px medium grotesk at the bottom. The tagline continues below at the same size, flush to the band's left edge, running past the fold. The upper half is otherwise blank. The handle is split into two pale-grey words fixed in the bottom corners; a black menu pill sits top-right.
  Worth taking: Draw one 1px vertical rule and typeset a large sentence around it, alternating words to either side so the line becomes the text's spine.
- **Spatial** (One Page Love, studied score 7.7) `opl-spatial`
  Idea: Typography behaves like sound in a room: plain black statements swell, stretch and bend through an empty white viewport as you scroll.
  Hero: A white viewport holding only the festival dates: two centred lines in Neue Haas Grotesk Roman at roughly 6.7vw, black, tight leading, sitting at the exact vertical middle and spanning about half the width. Large empty fields above and below; no image, no button, no supporting copy. The header is the only other element: a light uppercase wordmark with tight tracking flush to the top-left corner at a 20px inset, and nine small bold uppercase links in a row across the top right. Mobile is the same poster at 40px with a two-line hamburger.
  Worth taking: Open with a single factual line (the dates) centred on an otherwise empty white screen instead of a branded hero.
Full notes on any of them: `node draw.mjs --site <id>`.

## Always
Headlines two lines or fewer at every width. No eyebrow labels, no monospace, no 01/02/03, no thin rules, no gradients, no shadows at rest,
no cream-and-terracotta, no violet. The frame: one h1 on the first screen that says what this is, equal outer margins, no
section huddled on one side with the other half empty, no blank stretch over half a screen, a heading on every section.
`inspect.mjs` measures all of it.

Saved to C:/Projects/mpcbc/taste-mockup/.taste-skill/sermons-3/direction.json · reprint with `node draw.mjs --show`
