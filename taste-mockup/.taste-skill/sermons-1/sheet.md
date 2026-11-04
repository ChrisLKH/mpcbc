# DIRECTION SHEET — The page is the object
seed 708000332 · drawn 2026-11-03 · sector nonprofit
Brief: The sermon archive of a Chinese Baptist church in Alhambra: about 700 recorded Sunday sermons in Cantonese, Mandarin and English, searchable by title, speaker and series

| | |
| --- | --- |
| Direction | The page is the object |
| Hero | Card over a dim room |
| Type | kept from the brand |
| Colour | kept from the brand |
| Sections | showcase (Output slips along the edge) → work (Book-spine index) → list (The printed menu sheet) → footer (The back of the card) |
| Motion | Pinned story |
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
Find the printed or physical artefact that already exists in the subject's world and build the page as that thing. A wedding site is the invitation card. A restaurant is its menu. A conference is the ticket; a record is its sleeve; a shop is the receipt or the swing tag; an archive is a stack of tabbed folders; a course is the syllabus sheet. The artefact supplies everything a template would otherwise invent: its proportions, its typographic manners, its way of grouping information. Visitors recognise the object instantly, which is why these pages feel specific.

Before anything else, write one sentence in your notes: "On this page, <the subject's own material> becomes <the device>."
Fill the first blank from your inventory of what only this client has (its name and what it means, its street, its numbers, its
constraint), then put the nearest competitor in the sentence: if it still works, the idea is the category's, not the client's.
Then name the one live thing the visitor can do on the page (SKILL.md step 4).

## 2. Composition
The first screen shows the artefact as a single filled panel (paper colour on a contrasting ground) centred or offset, with believable proportions: a card at 5:7, a ticket at about 2.2:1 with a notch cut by `clip-path`, a menu as a tall sheet. All primary content lives on it, set the way that object is set: centred and symmetrical for an invitation, in tidy aligned columns for a menu or ticket. The ground behind is a flat colour or one full-bleed photograph. As the page continues, each section is another piece of the same stationery: a second card, a tear-off stub, a folded panel, always filled shapes with real edges (straight, notched, scalloped, perforated by a dotted `border` of round dots at 4px or more), never outlined boxes. The footer is the back of the object.

## 3. Type
Kept from the brand. Take the manners of the artefact: a bookish serif with small caps avoided, or a blunt condensed face for tickets and posters. Sizes stay within what print would use relative to the panel; the name is large on the panel, details are small and exact.

## 4. Colour
Kept from the brand. Paper and ink plus the ground the paper sits on. Two or three flat colours in total, like a two-colour print job.

## 5. Imagery
**Use the images that belong to the project in every picture slot.** For slots they cannot fill, real pictures of the subject's world from the open collections come next (start with `node C:/Projects/mpcbc/.agents/skills/design-taste-frontend/scripts/assets.mjs "alhambra postcard"`, then "alhambra old map" and "church", then queries from your inventory (its street, its materials, its trade's tools and history); credit them on the page), then the dealt photographs.

Optional. One photograph behind the object, or small photographs placed on it like tipped-in plates. The object itself is the image.

**Floor (the inspector checks it):** at least four real pictures on the page, usually six to twelve; SVG drawings do not count. One moving picture where an honest clip exists: `node <skill>/scripts/assets.mjs --video "<query>"`, used as a muted loop with a poster frame. Whatever this direction says about pictures, it is carried out with real ones.

Photographs dealt for this build, from the "People" set (figures, silhouettes and portraits, mostly unposed)
Order of preference for imagery: (1) the project's own photographs and product shots; (2) images you generate, if an image tool is
available; (3) real pictures of the subject's world found in the open collections (assets.mjs, downloaded and credited on the page);
(4) drawings of the subject in code; (5) these. They are real photographs (Unsplash licence, served by picsum.photos) of places, light and
texture. They are atmosphere, never a stand-in for a specific product, dish, person or interface. List the ones you use in your report so
they can be replaced.
- `https://picsum.photos/id/685/1600/1000` — Man sitting on a rock looking over a sea of clouds · mid tone · pale gold, grey, dark brown · calm area: right · native landscape · photo: Joshua Earle
- `https://picsum.photos/id/338/1600/1000` — Person in a hooded coat seen from behind facing a misty sea · mid tone · slate blue, grey, charcoal · calm area: left · native landscape · photo: Patryk Sobczak
- `https://picsum.photos/id/177/1600/1000` — Hiker with a backpack looking over a hazy mountain valley · mid tone · warm tan, hazy gold, olive · calm area: left · native landscape · photo: Danka & Peter
- `https://picsum.photos/id/669/1600/1000` — Person in a mustard jacket and grey beanie seen from behind in fog · light tone · mustard, pale grey, white · calm area: left · native landscape · photo: Luke Pamer
- `https://picsum.photos/id/832/1600/1000` — Woman in a pale blue shirt reading a book in soft window light · dark tone · dark olive, pale blue, warm skin · calm area: left · native landscape · photo: hieu le
- `https://picsum.photos/id/349/1600/1000` — Man sitting on a sea wall looking toward a hazy skyline · mid tone · warm grey, sepia, charcoal · calm area: left · native landscape · photo: Caleb George
- `https://picsum.photos/id/22/1600/1000` — Man in a dark suit walking across painted road markings · mid tone · warm grey, charcoal · calm area: left · native landscape · photo: Alejandro Escamilla
- `https://picsum.photos/id/821/1600/1000` — Woman seen from behind on a platform as a train blurs past · light tone · white, grey, black · black-and-white · calm area: left · native landscape · photo: Eutah Mizushima
The last two path numbers are width and height: request the exact crop you render (`/1200/1500` for a 4:5 portrait, `/2400/1350` for a hero) and
add `?grayscale` to desaturate. Use three to six of them, not all eight. Always set `width`, `height`, `alt` and `loading="lazy"` (not on the hero image).
Other sets that fit this brief: fog-and-quiet, city-day, object-still, mountain — `node draw.mjs --photos <set>` prints one in full; `--photos list` shows all sets. Taking photographs from another set is your call and is not a redraw; keep them to one light and one treatment.

## 6. Hero — Card over a dim room
A full-bleed, low-key photograph of the room fills the viewport, darkened with `filter: brightness(0.7)` so it reads as atmosphere. Centred over it is one square-cornered panel in a flat warm tone taken from the photograph: columns 4–10, about 55% of the width and 62% of the height, a filled programme card with 32px padding. Along its top edge the three words of the title are spread to the left, centre and right in bold condensed capitals at clamp(28px, 2.5vw, 38px); that line is the headline. Under it sit two left-aligned blocks in the same face and size, line-height 1.0: the facts (date, time, place) as three short lines, then one supporting sentence held to 30ch. Along the card's bottom edge: a credit at left in 14px, the single action as a solid rectangular button in the centre, one text link at right. There is no navigation outside the card. The card is `position: sticky`: as the page scrolls it shrinks with `transform: scale` toward the bottom-left corner and stays there as a ticket-sized stub while photographs of differing sizes pass on a flat ground.

On a phone: The photograph fills 100svh and the card sits centred at full width minus 16px margins, 70svh tall. The title words stack as a two-line heading at 9vw at the card's top-left. The facts and the supporting sentence follow at 5.2vw in the condensed face. The action button spans the card's bottom edge. On scroll the card shrinks to a 120px stub pinned bottom-left.
Headline: The event or venue name, three words at most and up to about 8 characters each, spread across the card's top edge; two lines on a phone.

Adjusted for this page:
- It names a kind of typeface: use the brand faces.
- It sets a sentence large over several lines: jurors read that as a long headline. Either cut it to a two-line headline at display size, or keep it a statement at 36px or less on three lines (24px, four lines on a phone).

## 7. Sections, in order
Each is a composition to rebuild with this project's content. Keep the geometry; change the subject. Re-order or drop a
section when the argument of the page needs it (see references/copy.md), never to fall back to a card grid.

### 1. showcase — Output slips along the edge (full-bleed)
For subjects whose result is a piece of text or paper: a receipt, a note, a ticket, a written line. A full-bleed photograph, 100vw by 96svh, sets the scene. In the upper centre, left-aligned in a column starting at 18% of the width, two short paragraphs in paper colour at 28–32px, the first opening with the heading's two to four words; a neutral scrim sits behind that column only. Along the bottom, six paper-coloured slips stand side by side across the full width with 12px gaps, each a filled rectangle of a different height (between 26 and 44svh), their lower ends cut off by the section's bottom edge. Two or three of the slips, not all, are tilted by 1–2 degrees. Each slip carries a sample of real output in the text face at 15px: a bold first line, a divider made of typed characters, then four to eight lines.
On a phone: The photograph is 100svh. The paragraphs sit in the top third at 22px. Only three slips show, each 30vw wide, standing along the bottom at different heights, and the row scrolls sideways to reveal the rest. Slip text stays at 14px; do not scale it down.

Adjusted for this page:
- It draws lines or borders: keep them only inside a table, a form or a `data-ui` fragment; elsewhere separate with space or a change of ground.

### 2. work — Book-spine index (index)
The complete index in the footprint of a banner. Twenty to forty project names are set as single lines rotated to read top to bottom with `writing-mode: vertical-rl`, packed side by side like book spines across the full content width. Each spine is a column 30–34px wide and 420px tall: the name at 18px starts at the top, and the discipline follows after a 12px gap on a flat highlighter-colour background chosen from four palette colours by discipline. Spines are separated only by 6px of space. Hovering or focusing a spine turns it to full ink and dims the others to 40%; the whole spine is the link. A two- or three-word heading at 28px sits top-left above the shelf with a one-sentence note at 15px to its right on the same baseline. No images. Below the shelf, 96px of empty ground. It turns a long client list into one dense typographic object.
On a phone: Rotation is dropped. Names become a horizontal wrap: each name at 17px followed by its highlighted discipline, items flowing inline with 10px gaps and 14px of line spacing, so the index reads as one dense paragraph of links filling the width.

Adjusted for this page:
- It fades text: anything a visitor reads, inactive items of a list included, is `--ink` or `--muted` at full strength (contrast 4.5:1 or more). The active item is told apart by `--ink` against `--muted`, weight or the accent, not by fading the rest.

### 3. list — The printed menu sheet (band)
The list is one tall sheet of paper. A filled paper-colour panel 640px wide sits centred, or offset to columns 7–12, on a contrasting flat ground or a full-bleed photograph, with square corners and its top and bottom edges cut into shallow scallops by `clip-path` or an SVG `mask`. Inside, 64px padding and a centred, symmetrical setting like a printed card: the venue or section name at 40–48px, a one-line note at 15px, then three or four groups 56px apart. Each group opens with its name at 22px, centred. Items follow at 17px with 16px between them: the dish or service at the left of a 420px measure, the price at the right with tabular figures, and a one-line description at 14px in the soft ink under the name. Alignment alone links name and price; there are no leaders. The sheet ends with the opening hours as two centred lines. Holds 12–24 items.
On a phone: The sheet spans the screen with 10px of ground showing at each side and 28px padding; the scalloped edges stay at top and bottom. The measure is the full inner width and prices stay right-aligned. Group names at 20px, items at 16px.

### 4. footer — The back of the card (stack)
The footer is the reverse side of the page's artefact, laid out as the back of a postcard. A paper-coloured panel spans columns 2–11 on the contrasting ground, about 420px tall, its top edge cut into a row of twelve semicircular scallops (an inline SVG path or `clip-path`, each scallop about 100px wide). The panel is divided by space into two halves. Left half: a short sign-off of two to four words at 32px, then two or three lines at 16px, written like a message. Right half: the address block, four left-aligned lines at 18px with 12px between them, each line a link (email, telephone, street, social); above it in the top-right corner, a stamp: one small photograph, 3:4 and 88px wide, with a 6px paper-coloured margin. Along the panel's bottom edge, a single centred line at 12px gives the copyright like a printer's imprint.
On a phone: The panel runs full width with six scallops across its top edge. The stamp sits top-right at 64px wide; the sign-off and message come first, then the address lines at 17px beneath them, then the imprint line centred at 12px.

Adjusted for this page:
- It sets text at 12px: use your small step (13px or more) instead.

## 8. Motion — Pinned story
One element holds its place with `position: sticky` (a title, a frame, a product fragment) while the items that belong to it scroll past or swap inside it. The page's ordinary scrolling does the work; nothing is hijacked. Hold it for about one screen per step and three screens at most, two held stages per page at most, with a line of text on every held screen. Recipe: 'Pinned story' in references/motion.md.
This is the signature move, the one loud moment. Around it the page still needs the rest of the motion layer (SKILL.md step 9): an entrance for the parts of every section, feedback on every control, and one moving picture. The inspector fails a page that barely moves. Recipes: references/motion.md.

## 10. Where the richness comes from
Recognition. The shaped edges, the print-like alignment and the limited inks make a familiar object out of plain HTML.

## 11. How this direction goes wrong
Choose an artefact that truly belongs to the subject, or it becomes costume. Shape the edges with fills and `clip-path`; an outlined rectangle with a thin border is not an object. Keep text sizes readable on a phone, where the object becomes full width with its shaped edge kept at top and bottom.

## 12. Real references
Studied sites that work this way. Take the principle, never the specifics: no names, copy, images or signature assets of theirs.
- **Sarah & Matt Wedding** (One Page Love, studied score 8) `opl-sarah-matt-wedding`
  Idea: The site is the invitation: a letterpress-style card rendered as a physical object opens the page, and its ornaments, rules and small typographic parts become the components for everything after it.
  Hero: A flat blush-pink field fills the viewport. Top-left sits a gold filigree monogram about 120px square; three plain nav links are centred on the same line; a solid gold rectangular RSVP button sits top-right. Below, centred, a portrait invitation card half the viewport wide starts 200px down and is cropped by the bottom edge, with a soft halo shadow and a debossed border with scalloped inner corners. On the card: the two names in a large italic swash serif, staggered left and right against two ornamental roundels, separated by hairline rules carrying tiny wide-tracked caps for date and time.
  Worth taking: Make a real-world object (an invitation, a ticket, a label) the hero and reuse its small typographic parts, such as a boxed address line with slashes, as recurring components down the page.
- **ARCHIV** (One Page Love, studied score 7.2) `opl-archiv`
  Idea: A waitlist page as a museum plinth: one cut-out everyday object at a time, centred on off-white beneath a heavy serif wordmark.
  Hero: Off-white single screen with no scroll. A heavy high-contrast serif wordmark in black caps sits top-left at a 56px margin, about 410px wide (28% of the viewport) and 80px tall. Opposite, top-right at the same margin, one line of 14px sans description aligns with the wordmark's top. Dead centre, one photographed object (a painted wooden pepper mill about 140px wide and 440px tall) stands on a barely visible lighter rounded square with a soft contact shadow; other objects replace it in turn. Below, a centred 11px grey line, then a white rounded email field 236px wide with a circled arrow inside its right end. Roughly 80% is empty.
  Worth taking: Build a pre-launch page from four elements only (wordmark top-left, one-line description top-right, a single centred object, an email field) and leave the rest of the screen empty.
Full notes on any of them: `node draw.mjs --site <id>`.

## Always
Headlines two lines or fewer at every width. No eyebrow labels, no monospace, no 01/02/03, no thin rules, no gradients, no shadows at rest,
no cream-and-terracotta, no violet. The frame: one h1 on the first screen that says what this is, equal outer margins, no
section huddled on one side with the other half empty, no blank stretch over half a screen, a heading on every section.
`inspect.mjs` measures all of it.

Saved to C:/Projects/mpcbc/taste-mockup/.taste-skill/sermons-1/direction.json · reprint with `node draw.mjs --show`
