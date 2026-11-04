# DIRECTION SHEET — Dressed as something else
seed 610105364 · drawn 2026-11-03 · sector nonprofit
Brief: The sermon archive of a Chinese Baptist church in Alhambra: about 700 recorded Sunday sermons in Cantonese, Mandarin and English, searchable by title, speaker and series

| | |
| --- | --- |
| Direction | Dressed as something else |
| Hero | Counter object, wide wordmark |
| Type | kept from the brand |
| Colour | kept from the brand |
| Sections | showcase (Object in front of type) → work (Book-spine index) → list (The printed menu sheet) → footer (Invitation over a cropped name) |
| Motion | Slow crossfade |
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
Art-direct the subject as if it belonged to a different, more vivid category. A money app is sold like a streetwear drop. An industrial roaster is presented like a design object. Drilling hardware gets the calm of a watch brand. A parcel-locker service becomes a fairground of stickers. Choose the borrowed category by asking what feeling the audience is missing from this kind of product (swagger, calm, glamour, play) and which category owns that feeling. Then take its manners completely: its type, its pacing, its way of photographing things. The contrast between dull subject and vivid dress is what people remember.

Before anything else, write one sentence in your notes: "On this page, <the subject's own material> becomes <the device>."
Fill the first blank from your inventory of what only this client has (its name and what it means, its street, its numbers, its
constraint), then put the nearest competitor in the sentence: if it still works, the idea is the category's, not the client's.
Then name the one live thing the visitor can do on the page (SKILL.md step 4).

## 2. Composition
Depends on the borrowed category, so decide it explicitly and write it in your notes. Fashion drop: one shouted condensed headline across the full width, a single cropped image under it, then product facts as a blunt list. Watch or perfume: a centred object on a dark field, a small serif name, very few words, long vertical pauses. Poster or zine: flat colour blocks, heavy type, tilted cut-out images. Museum: one item per screen on a plinth-like band of colour with a short label beside it. Whichever you choose, the page structure stays simple (four to six sections) and every section uses the borrowed manners, including pricing and the footer.

## 3. Type
Kept from the brand. The borrowed category's type, fully committed: condensed and loud, or refined and small. No halfway.

## 4. Colour
Kept from the brand. The borrowed category's palette, which will be unlike the subject's sector norm. That mismatch is intended.

## 5. Imagery
**Use the images that belong to the project in every picture slot.** For slots they cannot fill, real pictures of the subject's world from the open collections come next (start with `node C:/Projects/mpcbc/.agents/skills/design-taste-frontend/scripts/assets.mjs "alhambra postcard"`, then "alhambra old map" and "church", then queries from your inventory (its street, its materials, its trade's tools and history); credit them on the page), then the dealt photographs.

Photograph the subject the way the borrowed category would be photographed. With the dealt set, choose images for attitude, not literal relevance, and crop them hard.

**Floor (the inspector checks it):** at least four real pictures on the page, usually six to twelve; SVG drawings do not count. One moving picture where an honest clip exists: `node <skill>/scripts/assets.mjs --video "<query>"`, used as a muted loop with a poster frame. Whatever this direction says about pictures, it is carried out with real ones.

Photographs dealt for this build, from the "People" set (figures, silhouettes and portraits, mostly unposed)
Order of preference for imagery: (1) the project's own photographs and product shots; (2) images you generate, if an image tool is
available; (3) real pictures of the subject's world found in the open collections (assets.mjs, downloaded and credited on the page);
(4) drawings of the subject in code; (5) these. They are real photographs (Unsplash licence, served by picsum.photos) of places, light and
texture. They are atmosphere, never a stand-in for a specific product, dish, person or interface. List the ones you use in your report so
they can be replaced.
- `https://picsum.photos/id/669/1600/1000` — Person in a mustard jacket and grey beanie seen from behind in fog · light tone · mustard, pale grey, white · calm area: left · native landscape · photo: Luke Pamer
- `https://picsum.photos/id/685/1600/1000` — Man sitting on a rock looking over a sea of clouds · mid tone · pale gold, grey, dark brown · calm area: right · native landscape · photo: Joshua Earle
- `https://picsum.photos/id/177/1600/1000` — Hiker with a backpack looking over a hazy mountain valley · mid tone · warm tan, hazy gold, olive · calm area: left · native landscape · photo: Danka & Peter
- `https://picsum.photos/id/338/1600/1000` — Person in a hooded coat seen from behind facing a misty sea · mid tone · slate blue, grey, charcoal · calm area: left · native landscape · photo: Patryk Sobczak
- `https://picsum.photos/id/1014/1600/1000` — Woman seen from behind facing a misty sea, a ribbon across her head · light tone · pale teal, soft grey, black · calm area: left · native landscape · photo: Oscar Keys
- `https://picsum.photos/id/821/1600/1000` — Woman seen from behind on a platform as a train blurs past · light tone · white, grey, black · black-and-white · calm area: left · native landscape · photo: Eutah Mizushima
- `https://picsum.photos/id/447/1600/1000` — Man standing on a wall facing a blue lake and distant mountains · mid tone · blue, slate, white · calm area: left · native landscape · photo: Joshua Earle
- `https://picsum.photos/id/334/1600/1000` — Man carrying a guitar walking along a foggy beach, monochrome · light tone · light grey, white, charcoal · black-and-white · calm area: top · native landscape · photo: Kimberly Richards
The last two path numbers are width and height: request the exact crop you render (`/1200/1500` for a 4:5 portrait, `/2400/1350` for a hero) and
add `?grayscale` to desaturate. Use three to six of them, not all eight. Always set `width`, `height`, `alt` and `loading="lazy"` (not on the hero image).
Other sets that fit this brief: fog-and-quiet, city-day, object-still, mountain — `node draw.mjs --photos <set>` prints one in full; `--photos list` shows all sets. Taking photographs from another set is your call and is not a redraw; keep them to one light and one treatment.

## 6. Hero — Counter object, wide wordmark
Borrowed from a watch or perfume counter. One flat charcoal field. Centred slightly above the midline, a single low-key photograph of the object about 31% of the viewport wide (columns 5-8), 4:5, its own dark background matched to the field with filter: brightness(0.9) so it sits without a frame. Under it the name runs on one line as an ultra-wide wordmark spanning 72% of the width yet only about 45px tall: an extended face with letter-spacing near 0.6em, or a regular face stretched with transform: scaleX(2.4). Then one centred serif sentence at clamp(24px, 2.6vw, 38px). Then a single centred row at 15px with 48px between its three parts: one specification, the price inside the one solid light rectangle that is also the action, a second specification. The wordmark repeats small top-left and one text link sits top-right, both on 32px margins. The bottom fifth and both outer quarters of the screen stay empty and dark.

On a phone: The photograph grows to 70% of the width at 4:5, its top at 16% of the height. The wide wordmark spans the full width inside 20px margins at about 28px tall. The serif sentence sets at 6vw on two lines. The row stacks into three centred lines with the action in the middle at full width. The lowest sixth of the screen stays dark and empty.
Headline: The name as a very wide, very low wordmark on one line (up to 12 characters); the serif sentence beneath is the supporting line, up to 40 characters.

Adjusted for this page:
- It names a kind of typeface: use the brand faces.
- It sets a sentence large over several lines: jurors read that as a long headline. Either cut it to a two-line headline at display size, or keep it a statement at 36px or less on three lines (24px, four lines on a phone).

## 7. Sections, in order
Each is a composition to rebuild with this project's content. Keep the geometry; change the subject. Re-order or drop a
section when the argument of the page needs it (see references/copy.md), never to fall back to a card grid.

### 1. showcase — Object in front of type (statement)
Type and object share one space instead of sitting side by side. Three or four lines of display type, one or two words each, are stacked flush left at clamp(72px, 9vw, 150px) with leading 0.88, starting on the left margin and allowed to leave the right edge (white-space: nowrap; overflow: hidden on the section). The lines step through tones of one ink: one at 45%, one at full strength, the rest at 18%. The object photograph, a cut-out or a studio shot blended with multiply, is absolutely positioned over the upper right of the stack at 40–46vw wide, rotated no more than 6 degrees and above the type in the stacking order, so it hides parts of three or four letters. Under the stack, in columns 8–12, a two-paragraph block at 20px gives the facts. The full-strength line is the heading; nothing sits above it.
On a phone: Type drops to 15vw and still runs off the right edge. The object sits at 78vw wide, overlapping the last two lines from below instead of from the right, and is cropped by the right edge. The facts block follows at full width and 17px.

Adjusted for this page:
- It fades text: anything a visitor reads, inactive items of a list included, is `--ink` or `--muted` at full strength (contrast 4.5:1 or more). The active item is told apart by `--ink` against `--muted`, weight or the accent, not by fading the rest.

### 2. work — Book-spine index (index)
The complete index in the footprint of a banner. Twenty to forty project names are set as single lines rotated to read top to bottom with `writing-mode: vertical-rl`, packed side by side like book spines across the full content width. Each spine is a column 30–34px wide and 420px tall: the name at 18px starts at the top, and the discipline follows after a 12px gap on a flat highlighter-colour background chosen from four palette colours by discipline. Spines are separated only by 6px of space. Hovering or focusing a spine turns it to full ink and dims the others to 40%; the whole spine is the link. A two- or three-word heading at 28px sits top-left above the shelf with a one-sentence note at 15px to its right on the same baseline. No images. Below the shelf, 96px of empty ground. It turns a long client list into one dense typographic object.
On a phone: Rotation is dropped. Names become a horizontal wrap: each name at 17px followed by its highlighted discipline, items flowing inline with 10px gaps and 14px of line spacing, so the index reads as one dense paragraph of links filling the width.

Adjusted for this page:
- It fades text: anything a visitor reads, inactive items of a list included, is `--ink` or `--muted` at full strength (contrast 4.5:1 or more). The active item is told apart by `--ink` against `--muted`, weight or the accent, not by fading the rest.

### 3. list — The printed menu sheet (band)
The list is one tall sheet of paper. A filled paper-colour panel 640px wide sits centred, or offset to columns 7–12, on a contrasting flat ground or a full-bleed photograph, with square corners and its top and bottom edges cut into shallow scallops by `clip-path` or an SVG `mask`. Inside, 64px padding and a centred, symmetrical setting like a printed card: the venue or section name at 40–48px, a one-line note at 15px, then three or four groups 56px apart. Each group opens with its name at 22px, centred. Items follow at 17px with 16px between them: the dish or service at the left of a 420px measure, the price at the right with tabular figures, and a one-line description at 14px in the soft ink under the name. Alignment alone links name and price; there are no leaders. The sheet ends with the opening hours as two centred lines. Holds 12–24 items.
On a phone: The sheet spans the screen with 10px of ground showing at each side and 28px padding; the scalloped edges stay at top and bottom. The measure is the full inner width and prices stay right-aligned. Group names at 20px, items at 16px.

### 4. footer — Invitation over a cropped name (statement)
Close and footer in one full-viewport field of the inverse colour. Upper third, columns 1–7: a two-line invitation at about 5vw, three to five words, with one filled rectangular button beneath it. At columns 10–12, top-aligned with the invitation, three or four social or contact links stacked at 16px. The wordmark fills the bottom of the screen at 26–30vw on one line, `white-space: nowrap`, heavier and larger than the viewport can hold: it is pushed down with `transform: translateY(30%)` so the bottom edge cuts through the letters, and a long name also runs off the right edge. `overflow: hidden` on the footer does the cropping. One row of credits and legal links at 13px sits directly above the wordmark's cap line, spread left and right. The cropped name must be recognisable from its upper two-thirds alone; test that it is.
On a phone: Invitation at 11vw on two lines at the top with a full-width button. Links follow as one row of words at 15px. The wordmark sits at 34vw at the bottom, cropped by both the bottom and the right edge, with the legal row above it at 12px.

Adjusted for this page:
- It sets text at 12px: use your small step (13px or more) instead.

## 8. Motion — Slow crossfade
The principal photograph quietly changes: three or four images of the same framing crossfade over about 1.6 seconds, holding five to six seconds each, or the fixed image changes when its matching section arrives. It should feel like light shifting in a room. Pause when the tab is hidden; under reduced motion show only the first image.
This is the signature move, the one loud moment. Around it the page still needs the rest of the motion layer (SKILL.md step 9): an entrance for the parts of every section, feedback on every control, and one moving picture. The inspector fails a page that barely moves. Recipes: references/motion.md.

## 10. Where the richness comes from
Attitude. The page has a recognisable voice from another world, applied to a subject nobody expected it for.

## 11. How this direction goes wrong
Name the borrowed category in one word before you start, or the result is merely loud. The content must still be clear: what it is, what it costs, how to get it. Avoid borrowing from 'tech startup', which is the default you are escaping.

## 12. Real references
Studied sites that work this way. Take the principle, never the specifics: no names, copy, images or signature assets of theirs.
- **Locker Land** (One Page Love, studied score 7.5) `opl-locker-land`
  Idea: A dry B2B parcel-locker pitch dressed as a fairground of die-cut stickers, where the main navigation is a fan of five tilted illustrated cards.
  Hero: Post-red field overlaid with a faint lighter-red square grid. Small white brand lock-up top-left; a white pill button with a hard dark-red offset shadow top-right. Top-centre, a two-line sticker-style logo (cream letters, thick cream outline, small locker drawing) about 20% of viewport width. Beneath it a two-line centred white sentence at 18px; no larger headline. The lower half is a fan of five portrait cards with thick coloured frames and rounded corners: centre card upright, neighbours rotated about 8 degrees, the outer pair about 18 degrees and cropped by the viewport edges, each with a label tab overlapping its bottom edge. A two-line caption sits centred below.
  Worth taking: Turn the primary navigation into a fan of tilted illustrated cards in the hero, centre card upright and the outer ones rotated and cropped by the viewport edges.
- **Moto Finance** (Awwwards Site of the Day, studied score 7.3) `aww-moto-finance`
  Idea: A metal card presented like a museum piece: lit by a single overhead light box in a black room, then orbiting through dim architectural stills.
  Hero: Black full-bleed 3D scene. A pale silver-lavender light panel, a trapezoid in perspective like a ceiling light box, occupies the top 20% and centre 55% of the viewport. The two-line uppercase headline, centred at 4.4vw in medium grotesk with tight leading, straddles the panel's lower edge so its first line sits half on light, half on black. In the exact centre a small metal card stands on a low plinth, only about 150px wide, surrounded by darkness. A light-grey pill button (about 250 by 52px) is centred near the bottom; the logo is top-left and a dark pill button top-right.
  Worth taking: Let a headline straddle the edge of a light shape so one line is half on light and half on dark.
Full notes on any of them: `node draw.mjs --site <id>`.

## Always
Headlines two lines or fewer at every width. No eyebrow labels, no monospace, no 01/02/03, no thin rules, no gradients, no shadows at rest,
no cream-and-terracotta, no violet. The frame: one h1 on the first screen that says what this is, equal outer margins, no
section huddled on one side with the other half empty, no blank stretch over half a screen, a heading on every section.
`inspect.mjs` measures all of it.

Saved to C:/Projects/mpcbc/taste-mockup/.taste-skill/sermons-5/direction.json · reprint with `node draw.mjs --show`
