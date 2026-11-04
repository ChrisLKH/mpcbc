# MPCBC WordPress mockup — build brief

Taste rebuild (27 October 2026, latest; supersedes every Home note below): Home 21 is now the design in `taste-mockup/index.html`, rebuilt as native Atomic Elementor. The whole site uses the new palette and font.
- **Palette and font:**
  - The palette is newsprint grey `#E8E8E3` with bright yellow `#FFD21A`, ink `#181715` and white paper pieces.
  - Hanken Grotesk replaces Bricolage Grotesque and Geist everywhere; Noto Sans TC and SC stay.
  - `scripts/apply-taste-palette.php` changed the values of the existing Atomic variables and Global Colours/Fonts, so every page recoloured without editing pages. It also capped Global Typography weights at 500.
  - The child theme's `style.css` tokens and `functions.php` font link were switched to match.
  - One new variable: `mpcbc-color-kids-field` `#FFE680`.
- **Home sections, in order** (colour rhythm "option 6", chosen 27 Oct from `taste-mockup/rhythm-*.html`; the header is white once scrolled, so the page is built on white with grey, yellow and ink chapters):
  - Existing hero, re-weighted, with times in the order Mandarin, Children, Cantonese, English and a yellow button
  - This Sunday, plain grey (no photo: the hero already moves): taste-skill "Output slips along the edge", three white order-of-service slips (the outer two tilted) on the grey with space below, each showing that congregation's live stream (red "Live now" pill), else its next scheduled stream ("Starts 9:00 AM"), else its latest sermon ("Play"); clicking plays it in the slip. From the `mpcbc-sermons` plugin shortcodes `[mpcbc_live ministry=… part=media|status]` (pre-change Home in `content-source/before-sermon-shortcodes/`)
  - Kids, full-width yellow: the owner's original layout. The photo is one classic Container with a background slideshow and Ken Burns zoom, the only non-Atomic element besides the forms.
  - Pastors, grey: taste-skill "Founder's letter" (sheet): the mission on a white letter sheet with a scalloped foot (scallop.svg strip), signed by the three pastors with 288px round portraits; the sheet spans the full content width, and a dotted tear-off strip with the About link and email
  - Church life: a full-width ink band
  - Coming up, white: events and the Bible plan, plus a static month calendar on a grey page
  - Newsletter, grey: the latest issue's real front page large (white mat), its title, date, Chinese line and buttons beside it; "Past issues" below a dotted rule (January 2026, max three; "All newsletters" link still a placeholder). Front pages are rendered from the PDFs in content-source/documents (newsletter-2026-07.jpg, -01.jpg). Email sign-up removed. New issue: swap the large page and text, move the previous issue to the start of Past issues, drop the oldest beyond three.
  - Visit, grey, last before the footer (swapped with Give): the church as a white-matted framed print (columns 1–3) beside the yellow coupon (columns 4–12); Atomic Tabs with Fluent Forms 5 and 3
  - Give, white: airmail envelope (now before Visit)
- **Footer template 16:** the menu columns (church name and address in the first column) and one bottom row (founding line, language link). The large church name was removed on 28 Oct because the Home page already shows the name several times.
- **Languages:** 47 new Traditional/Simplified copy pairs use ids `mpcbc-copy-zh-hant-taste-*` / `mpcbc-copy-zh-hans-*`.
- **Rebuild:**
  1. `node scripts/build-taste-home.mjs` writes `content-source/taste-home.json` and `taste-footer.json`.
  2. Run `scripts/apply-taste-home.php` through WP-CLI `eval-file` with Local's PHP. Settings for `scripts/wp.ps1` are not repeated here.
     - `TASTE_TARGET=home` writes post 21; the default is the draft page `home-taste-build`.
     - `TASTE_FOOTER=1` also writes footer 16.
     - It refuses to overwrite editor changes.
  3. Media came from `scripts/upload-taste-media.php` (`content-source/taste-media.json`).
- **Monthly:** the calendar is static. Re-run the build with `TASTE_MONTH=YYYY-MM`, or edit it in Elementor.
- **Backups:** Home 21, footer 16, the kit and the old child-theme files are in `content-source/before-taste-rebuild/`.
- **Motion:**
  - 27 Elementor Interactions are built by the same script: hero lines rise on load; headings, services, kids card, programme sheet, agenda, calendar, newsletter tabs and envelope fade in on scroll; pastor cards slide in from left/bottom/right; the coupon slides up; the stamp drops.
  - Free Elementor offers only ease-in and a fixed 100px slide; each slide is paired with a fade.
  - Interactions ignore the visitor's reduce-motion setting, so `hello-mpcbc/functions.php` removes `#elementor-interactions-data` for those visitors.
- **Later changes (same day):** Newsletter on white; pastor cards centred; the church photo is 640px tall with the coupon overlapping only 64px; Coming up on the page grey; Church life with even 32px space.
- **Not done:**
  - Phone and tablet checks: responsive was set aside for now
  - The interior pages' own redesign. They only took the new colours and font.

Rollback (22 October 2026, latest): the visual pass and its repair below were **rolled back** on post 21 to `content-source/before-visual-pass/21.json` (the pass did not improve the page). Three changes were then applied to that original: all three This Sunday players now show on phones too (English and Mandarin were hidden), so they match at every width; the Newsletter issue card stacks its photo (full width, 240px tall) above the text, with both cards aligned at the top; pastor portraits are 200px circles (160/112px on tablet/phone). The original link "Our story and team" was already correct and needed no fix. The pass-plus-repair state is kept in `content-source/before-rollback/21.json`; before/after shots are in `screenshots/three-fixes/`. The two paragraphs below describe the superseded pass.

Visual pass update (22 October 2026): Home visual pass executed across all content sections (1–8) on post 21. A unified visual system is now in place: 0px border radiuses across all cards, containers, panels, and images; 999px pill buttons; removal of drop shadows; a disciplined 5-step type scale (Display, Title, Subtitle, Body, Small meta); Ink for readable body text and Muted for secondary meta; elimination of kickers above headings; and rhythmic section padding (120px desktop, 88px tablet, 64px mobile). Section layouts vary according to content (asymmetric 58/42 watch grid with English/Mandarin grouped on the right, clean White newsletter panel on Mist, borderless 1:1 pastor portraits, Deep Sea full-width community band with square panel and stacked mobile tabs, Soft Persimmon kids ground with single photo, two-column events/sermons with 0px date chips and 16:9 thumbnails, Mist contact grid with office details and 0px tabbed panel, and Harbor give band with 1:1 image). The hero remains 100% untouched. All copy, links, forms, and bracketed placeholders are strictly preserved. Backups before this pass are in `content-source/before-visual-pass/`.

Repair (22 October 2026, after the visual pass): the pass wiped the styles of 21 Atomic wrappers on post 21, which made them default to flex rows and squeezed most sections into one-word columns. Those 21 styles were copied back from `before-visual-pass/21.json`. Also fixed: an empty unstyled box Gemini added in Contact was removed, so the office details described above were **not** added; the Give text column's flex was restored to grow 1; the featured This Sunday item and the three pastor cards now stack at tablet. The 繁體/简体 pages and the footer were not affected. The state before this repair is in `content-source/before-style-repair/21.json`. Checked at 1440, 768 and 390: no horizontal scroll and no squeezed text. The Pastors "Our story and team" link now has White text (it was Harbor on a Harbor fill) and its 12/24px padding back (the pass had set it to 0).

Approved update (19 October 2026): Home now uses the **integrated bottom service rail**, replacing the overlapping white times card. This supersedes the old hero/times-card instructions below. Native Elementor containers hold the welcome message, three congregation times, address and Directions link over the full-height video and a dark gradient. The pause control has a reserved gap beside the location row. Current exports are `home-hero.json`, `home-service-rail.json` and `home-content-filled.json`; `home-times-card.json` is a compatibility rail export. The remaining Home sections are unchanged. Do not reapply old hero scripts. Backups are in `content-source/before-bottom-rail/`.

Refinement (20 October 2026): the hero leads with “Know God. Grow together.” and a larger display heading. The shorter rail has four entries, including Children / 兒童 at **10:00 AM** supplied by the user. It uses a two-by-two grid on phones. The title is “Sunday mornings / 主日聚會” so the children entry is not labelled as a worship service. Pre-refinement Home data is in `content-source/before-rail-refinement/`; responsive checks are in `content-source/hero-refinement-checks.json`.

Latest approved organisation (20 October 2026): the full Topic reader is now inside About at `#beliefs` in all three languages. All eight original statements/references remain native Elementor Nested Tabs. Old Beliefs URLs redirect to the matching About section, using destination page/anchor metadata. Current merge helpers: `merge-beliefs-about.mjs` / `apply-about-merged-beliefs.php`; backups: `content-source/before-about-beliefs-merge/`. Older standalone About/Beliefs builders are historical: do not apply them over the merged exports. The single header language switcher preserves the About anchor.

Primary menu (lean, 3 Nov 2026, `scripts/apply-lean-menu.php`, previous menu in `content-source/before-lean-menu/`): About (Visit us, About us, Pastors & leaders), Worship (Sermons & livestream, Cantonese, Mandarin, English, Groups & fellowships, Bible study & Sunday school), Children, News (Announcements & events, Newsletter, Photos & videos), Give as the one yellow button, and the existing language switcher. A menu item only for something that is its own page; service times, directions and mission are sections inside pages. No menu item leads to Coming soon. `reorganise-primary-menu.php` and `primary-menu-grouped.json` record this grouping; original menu is in `before-menu-grouping/`. Service language and reading language remain independent.

Current Home identity supersedes the older headline notes above: English church name, Chinese church name, slogan and one Sunday button above the rail.

Atomic conversion (20 October 2026) supersedes the legacy widget build steps below: 22 Elementor documents now use native V4 Atomic layouts/content (1,636 elements). Only four Fluent Forms shortcode placements remain classic, preserving three existing forms under the Free-only rule. Header/menu stays native WordPress. No Pro widget or new plugin was added. Current exports are saved from WordPress, including native hero/service-rail exports; Atomic colour/font variables are recorded in `atomic-global-variables.json`. Old V3 builders/apply scripts must not overwrite these exports. Backups: `content-source/before-atomic-conversion/`. See `MIGRATION-MCP-READINESS.md` for migration and tested MCP status. MCP network access remains disabled until the online restore. Home must retain the `elementor_header_footer` page template.

You are building a **front-page mockup** of the Monterey Park Chinese Baptist
Church website in WordPress, editable by non-technical volunteers using
**Elementor free**. Keep it simple: the least code and fewest plugins that
match the design.

## Hard rules

- Work only on the site at the repo root. Do not touch the archived Astro site
  (`archive/astro-site/`), Design B (`archive/wordpress-design-b/`) or `.env`.
- Every visible word and image must be editable in Elementor (or Appearance ›
  Menus for the menu). Nothing hard-coded in PHP/CSS/JS.
- Only Elementor free widgets + the plugins listed below. No Elementor Pro,
  no Custom CSS fields (Pro), no HTML widgets for content.
- Colours and fonts only from Elementor Global Colours / Global Fonts.
- Unknown facts stay as `[bracketed placeholders]` exactly as in the design.
  Invent nothing.
- Placeholder photos: simple labelled images ("photo · English service").
- Livestream, sermons, events are static mock content. No functionality.

## Current task: rebuild Home as the Harbor hybrid (2026-10-16)

The design is now `design-reference/Hybrid-preview.html`: a working HTML page
that replaces `Combined.dc.html` for the **Home page, header and footer**. Open it
through a local server so the video plays (from the repo root:
`npx --yes http-server@14.1.1 . -p 8099 -c-1`, then
`http://localhost:8099/design-reference/Hybrid-preview.html`).
Read exact sizes, colours, copy and breakpoints from its `<style>` block and
markup. Where this brief and the preview disagree, the preview wins; tell the
user about the conflict.

Measured today on `http://mpcbc.local/`: 6,324px tall at 1440, 11,018px at 390.
The preview is 8,271px at 1440 and 10,330px at 390. Check every step at **1440,
1280, 1110, 768 and 390px**.

### What changes, in one list

| Area | Now on mpcbc.local | Change to |
|---|---|---|
| Colours | Evergreen & Sun (green, yellow, cream) | **Harbor**: blue-teal, sea mist, persimmon (step 1) |
| Menu | 6 flat items, two-line labels, green "Plan your visit" button | 6 items with 3 dropdowns, one-line labels, **no button** (step 2) |
| Hero | 132px headline, Plan your visit + Watch live buttons | 88px headline, one text link, no buttons (step 3) |
| Times card | Overlaps hero bottom | Same card; hero shorter so the whole card is on the first screen (step 3) |
| This Sunday | 3 cards | Keep; restyle to new colours, new header copy (step 4) |
| Find your place | 4 photo cards by language | **Delete** |
| 中文？ band | Green band | **Delete** |
| Newsletter | None | **New section** (step 5) |
| Pastor quote + prayer card | 1 pastor | **Replace** with "Meet our pastors", 3 pastors (step 6) |
| Community | None | **New section**: vertical tabs (step 7) |
| Kids | Full-width yellow band | Keep content, becomes a **soft persimmon card** (step 8) |
| Coming up + Latest messages | Two columns | Keep; rename "Recent sermons" (step 9) |
| Stay in the loop | Social links + email | **Delete** (email moves to Newsletter, social to footer) |
| Contact | Prayer button only | **New section**: tabbed card with two Fluent Forms (step 10) |
| Give | Footer link only | **New strip**: photo, verse, Give online (step 11) |
| Footer | Black, 4 columns | Dark sea-blue, adds office hours (step 12) |

### 1. Global Colours (Site Settings › Global Colours)

Update the four system colours and replace the custom ones. Use
`scripts/set-global-colors.php` (edit its values) or Site Settings by hand.

| Slot | Name | Hex | Use |
|---|---|---|---|
| Primary | Harbor | `#155E75` | Buttons, links, selected tab, live-card border |
| Secondary | Deep sea | `#0F2A33` | Hero overlay, panel overlays, shadows |
| Text | Ink | `#13232A` | Body text, headings |
| Accent | Persimmon | `#F28C38` | **Only** Kids button, Kids ticks and Give online button. Always with dark text `#2B1404`. Never next to Live red |
| Custom | Paper | `#F6F7F5` | Page background |
| Custom | Mist | `#E3EBEA` | Alternate section bands |
| Custom | Ink soft | `#4F6168` | Secondary text, Chinese second lines |
| Custom | Harbor deep | `#0E4A5C` | Primary hover |
| Custom | Persimmon soft | `#FCE3CC` | Kids card background |
| Custom | On persimmon | `#2B1404` | Text on persimmon |
| Custom | Persimmon hover | `#E07A26` | Persimmon button hover |
| Custom | Tick | `#C2600F` | Kids check icons |
| Custom | Live | `#D92D27` | Live pills (white text) |
| Custom | Live text | `#B8231E` | Red text on light backgrounds ("Cantonese · 9:30 AM" on the live card) |
| Custom | Card tint | `#EAF2F3` | Live slot and live card background |
| Custom | Line | `#D9E2E1` | Card borders, dividers |
| Custom | Line strong | `#BFCDCC` | Outline buttons, field borders, list rules |
| Custom | Footer | `#0A1F26` | Footer background |
| Custom | White | `#FFFFFF` | Cards on Paper/Mist |

Delete the old customs (Paper `#FAF7F2`, Stone `#EDE6DA`, Ink soft `#5C564D`,
deep green `#0A5247`) once nothing uses them. Then in `hello-mpcbc/style.css`,
replace every old hex used as a `var()` fallback or hard-coded value
(`#0E6B5C`, `#0A5247`, `#12110E`, `#16140F`, `#5C564D`, `#CFC5B4`, `#E3DACB`,
`#EDE6DA`, `#F4B942`, `#FAF7F2`, `rgba(250,247,242,…)`, `rgba(227,218,203,…)`)
with the Harbor equivalents above. The scrolled header is
`rgba(246,247,245,.94)` with a `#D9E2E1` bottom border.

Fonts do not change: Bricolage Grotesque (display, 700, −0.035em), Geist
(body), Noto Sans TC (Chinese).

**The language rule** (applies everywhere): English first; Chinese on its own
line underneath, in Noto Sans TC 500, colour Ink soft, at about 0.42× the
English size and **never under 14px**. Nothing important appears only in
Chinese or only in English. In Elementor, the Chinese line is its own Heading or
Text widget under the English one. No `<span>` styling inside widgets.

### 2. Header and menu

Hello Elementor's header, 80px tall (72px ≤1024), fixed on Home, transparent
over the hero, frosted Paper after 40px of scroll (`nav.js` already does this).

**Appearance › Menus** (Chinese goes in each item's Description field; the
child theme already prints it):

| Top item | Description | Children (with descriptions) |
|---|---|---|
| Sundays | 主日 | Service times and directions 崇拜時間 · Plan your visit 初次來訪 · Cantonese congregation 粵語堂 · English congregation 英語堂 · Mandarin congregation 國語堂 |
| Sermons | 講道 | — |
| Newsletter | 通訊 | — |
| Community | 團契 | Kids and youth 兒童及青少年 · Fellowships and groups 團契小組 · Next steps 信仰成長 · Care and prayer 關懷代禱 |
| About | 關於我們 | Our story 教會歷史 · Beliefs 信仰 · Pastors and staff 教牧同工 · Campus map 教會地圖 |
| Give | 奉獻 | — |
| 中文 | — | (keep the existing pill) |

Remove "Visit", "Kids & Youth", "Watch" and the **Plan your visit button**
(`li.button`). All links go to `/coming-soon/` except Home anchors:
Sundays → `#top`, Service times → `#top`, Sermons → `#sermons`, Newsletter →
`#news`, Community → `#community`, Kids and youth → `#kids`, Care and prayer →
`#connect`, About and Pastors and staff → `#pastors`, Give → `#give`.

**Child theme CSS** (`style.css`, header CSS is allowed there):
- One-line labels: English 16px/500, then the description inline, 14px Noto Sans
  TC at 80% opacity, gap 6px. Remove the stacked `flex-direction: column` rules.
- Item gap 18px; nav gap 20px.
- Dropdowns (`.sub-menu`): hidden; show on `:hover` and `:focus-within` of the
  parent. White, `1px solid #D9E2E1`, radius 16, padding 8, min-width 260,
  shadow `0 16px 40px rgba(15,42,51,.16)`. Rows 44px tall, English left and
  Chinese right, radius 10, hover background Mist and text Primary. Add a small
  chevron after parents (CSS `::after`, not markup).
- The full menu needs about 1,105px. **Collapse to the hamburger at ≤1109px**
  (not 1024). In the mobile panel, show children already open, indented 16px,
  17px text, 48px rows.
- Delete the `li.button` rules and the 1025–1199px tightening block.

### 3. Hero and Sunday worship card

**Hero** (`home-hero.json`):
- Min height `max(560px, 100vh − 110px)` desktop, `max(520px, 100vh − 290px)`
  tablet, `max(360px, 100vh − 440px)` mobile, so the **whole times card is on
  the first screen** at 1440×900, 768×1024 and 390×844. Use Elementor's custom
  unit (`calc()`) if available, otherwise the nearest px values per breakpoint.
- Padding 128px top and bottom (104px mobile). Content bottom-left.
- H1 "You’re welcome here." 88 / 64 / 44px.
- Text "A friendly church in Monterey Park, with services in English, Cantonese
  and Mandarin." 20px, white 92%; under it "歡迎你，我們有英語、粵語及國語崇拜。"
  17px, white 80%. **Hide both on mobile** (the card below says the same).
- Remove both buttons. Add a text link "First time here? Plan your visit"
  (white, 2px underline, 44px tap height) with "初次來訪" beside it in white 75%.
  Links to `/coming-soon/`.
- Overlay gradient `rgba(15,42,51, .5 → 0 at 22% → .1 at 45% → .88)`.
- Pause button bottom-right, 128px from the bottom (104 mobile).

**Times card** (`home-times-card.json`): keep the structure. Margin-top −64px
(−48 mobile), white, radius 24, shadow `0 24px 60px rgba(15,42,51,.22)`.
- Desktop one row: title + address | Cantonese | English | Mandarin | Kids |
  Directions / Add to calendar.
- **≤1279px**: title on its own row with a divider, the 4 times in a row, then
  the two links in a row. Mobile: times 2×2.
- Cantonese slot is live: Card tint background, radius 14, red pill
  "● Live now · Watch" linking to `#watch` (drop "· Watch" ≤1279px). The pill
  is 28px tall; give it 44px of tap height (padding or a transparent border).
- Time digits Bricolage 44px (36 mobile).

### 4. This Sunday (`#watch`)

Keep the three cards. Changes only:
- Heading "This Sunday" 72 / 56 / 40px with "本主日" under it. Right-hand text:
  "Each congregation streams on its own channel. Every service goes into the
  sermon archive afterwards."
- Section background Paper (was Stone). Cards Paper with `1px #D9E2E1`; live
  card `2px` Primary border on Card tint; its kicker in **Live text `#B8231E`**
  (contrast).
- Bottom row text: "Missed a service? Every sermon is posted to the archive,
  sorted by congregation." Buttons: All sermons → `#sermons`, Our YouTube channels.
- Tablet: each card becomes a row (thumbnail 280px left, text right).
  Mobile: hide the thumbnails on the two cards that aren't live (only the live
  card keeps its thumbnail).

### 5. Newsletter (`#news`, new)

Mist background. Heading "Newsletter" / "通訊"; right text "The weekly
newsletter, in all three languages."
- Two columns 1.45fr / 1fr, gap 24 (one column ≤1024).
- Left white card, radius 24, padding 40 (24 mobile): "Newsletter · [Date]",
  H3 "[Main announcement title]" 48px (34 mobile), two placeholder lines (EN
  and "[中文摘要]"), buttons **Read the newsletter** (Primary) and **Download
  PDF** (outline). Photo placeholder 220px wide on the right (above the text
  ≤1279px, 160px tall mobile).
- Right white card, radius 24, padding 32: "Also this week" / "本週其他消息",
  three rows "[Announcement title]" + "[One line of detail]" with 1px dividers,
  then the existing Fluent Forms newsletter form ("Get the newsletter by email",
  email + **Subscribe** outline button, one row; stacked on mobile).

### 6. Meet our pastors (`#pastors`, replaces Pastor + prayer)

Paper background. Heading "Meet our pastors" / "認識我們的牧師"; right text
"One pastor for each congregation. Any of them is happy to meet you."
Three cards in a row: square photo (radius 20), kicker "Pastor · Cantonese" /
"Pastor · English" / "Pastor · Mandarin" (Primary, 14px 700), name "[Pastor
name]" 26px with "[牧師姓名]" under it, "[One line about this pastor.]".
Tablet and mobile: each pastor is a row, photo 160px (tablet) / 112px (mobile)
on the left. Use `placeholders/pastor-portrait.png` ×3.

### 7. Community (`#community`, new)

Mist background. Heading "Community" / "團契"; right text "A place for every
age, in every language." Build with Elementor free's **Nested Tabs** widget,
tabs on the left (vertical).
- Tab column 320px, gap 24. Tab titles Bricolage 28px with the Chinese line
  under it (Nested Tabs allows a container as the title; otherwise put both on
  two lines of the title). Selected: Primary background, white text, radius 16.
  Unselected: transparent, hover white 70%.
- Panel: container with background image + overlay
  `linear-gradient(transparent 30%, rgba(15,42,51,.88))`, radius 24, min height
  440 (380 tablet, 440 mobile), padding 40 (32 / 24), content bottom-left: H3
  56px (44 / 32) + Chinese line, one placeholder line, white buttons (44px tall).
- Tablet: tabs become a row of 4 above the panel (20px titles, white 60%
  background on unselected). Mobile: 2×2 tabs (18px).
- Arrow keys must move between tabs (Nested Tabs does this; check it).

| Tab | Panel text | Buttons |
|---|---|---|
| Kids 兒童 | [One line about kids’ ministry: safe check-in, trained volunteers, classes in English.] | Nursery (0–3) · Sunday school (4–11) · text link "More about kids" → `#kids` |
| Youth and students 青少年 | [When and where youth and students meet.] | Youth fellowship · [College / young adults] |
| Fellowships 團契 | [Fellowships and small groups in each language.] | Cantonese 粵語 · English · Mandarin 國語 |
| Care 關懷 | [How the church cares for people when life is hard.] | Prayer request → `#connect` · [Counseling] · [Visitation] |

New placeholders (labelled, same method as `scripts/generate-placeholders.ps1`):
`photo-community-kids.png`, `-youth.png`, `-fellowship.png`, `-care.png`.

### 8. Kids (`#kids`, keep content)

Same content as now. The yellow full-width band becomes a **card** inside the
1328px content width: Persimmon soft background, radius 24, padding 72 (48
tablet, 28/20 mobile). Heading 72 / 56 / 40px. Tiles white. Tick icons colour
Tick `#C2600F`. Chinese note on white 65%. Button **See Kids & Youth** in
Persimmon with On-persimmon text. Section background Paper.

Photos: the **original Design A stack** (`Combined.dc.html`, Kids section), not a
grid. Three images with radius 20 and shadow `0 24px 48px rgba(15,42,51,.25)`,
positioned absolutely in a container with aspect ratio 564 / 660 that fills the
right column. Sizes are percentages of that container so it scales:
1. Sunday school craft: left 0, top 6%, width 67%, ratio 380 / 460, rotate −3°.
2. VBS game: right 0, top 0, width 53%, ratio 300 / 360, rotate 2.5°.
3. Parent at check-in desk: right 7%, bottom 0, width 57%, ratio 320 / 240, rotate −1.5°.

Tablet: same stack, container max width 440px. Mobile: no rotation, no shadow,
one row of three (each 1fr, 120px tall, gap 8px). Use Elementor's Position
(absolute) and Transform › Rotate on each Image widget; existing placeholders
`photo-kids-craft.png`, `photo-kids-vbs.png`, `photo-kids-checkin.png`.

### 9. Coming up + Recent sermons

Mist background. Keep the two columns; headings 56 / 44 / 32px with "近期活動"
and "最近講道" under them. Rename "Latest messages" → **Recent sermons**
(CSS ID `sermons` on that column), "All messages" → **All sermons**. Date chips
and row dividers in the new colours (chip white, rules Line strong).

### 10. Contact (`#connect`, new)

Paper background. Left: heading "We’d love to hear from you." / "與我們聯絡",
text "Tell us you’re coming and someone will look out for you. Or send a prayer
request. A pastor reads every one and keeps it private."
Right: white card, `1px #D9E2E1`, radius 24, with **Nested Tabs** (horizontal):
"Say hello 聯絡卡" and "Prayer request 代禱". Selected tab: Ink text, 3px Primary
underline.
- Say hello: **new Fluent Form** — Your name 姓名, Email or phone 電郵或電話,
  Which service will you visit? 參加哪一堂？ (Choose a service / Cantonese 粵語 ·
  9:30 / English · 11:00 / Mandarin 國語 · 11:30 / Not sure yet), Anything we
  should know? (optional textarea), button **Send**.
- Prayer request: the existing prayer form, button **Send prayer request**.
- Fields 52px, radius 12, `1.5px #BFCDCC`; buttons Primary 56px. Export the new
  form to `fluent-forms/connect-form.json`.

### 11. Give (`#give`, new)

Paper background, no top padding (follows Contact). White strip, `1px #D9E2E1`,
radius 24, padding 16 / 40 / 16 / 16, three columns: photo 300px × 200px
(radius 16) | "Give" 56px + "奉獻" + verse line "“[Short Bible verse about
giving]” [Reference]" with "「[經文]」[出處]" under it | **Give online**
(Persimmon) over a text link "Other ways to give". Tablet: photo 200px left,
buttons in a row under the text. Mobile: stacked, photo 120px tall.
New placeholder `photo-give.png`.

### 12. Footer (template "MPCBC – Footer")

Background Footer `#0A1F26`. Same 4 columns. Add "[教會中文名稱]" under the
English name, "Office hours: [Days, times]" under Contact, and the social links
(Instagram @[handle], YouTube, WeChat 微信群) from the deleted Stay in the loop.
Explore links: Plan your visit, Watch live (`/#watch`), Sermons, Newsletter,
Kids & Youth, Prayer request. Every footer link 44px tap height. Mobile: address
and Sundays full width, Explore and Contact side by side.

### Section order and backgrounds

1. Hero (video) → 2. Times card → 3. This Sunday (Paper) → 4. Newsletter (Mist)
→ 5. Meet our pastors (Paper) → 6. Community (Mist) → 7. Kids card (Paper) →
8. Coming up + Recent sermons (Mist) → 9. Contact (Paper) → 10. Give (Paper,
no top padding) → 11. Footer.

Section padding 128 / 88 / 64px top and bottom (desktop / tablet / mobile).
Content max width 1328px, side padding 56 / 32 / 16px. Main section headings
72 / 56 / 40px; sub-section headings (Coming up, Recent sermons, Give, cards)
56 / 44 / 32px. Large cards radius 24, tiles and thumbnails 14–16, pills 999.

### Acceptance for this pass

- [ ] Global Colours match step 1; no old green/yellow/cream left in the theme CSS or any page (search the Elementor data for `0E6B5C`, `F4B942`, `FAF7F2`).
- [ ] Menu matches step 2 with working dropdowns (hover and keyboard). Full menu visible at 1110px and up; hamburger at 1109px and below. No "Plan your visit" button.
- [ ] The whole Sunday worship card is visible without scrolling at 1440×900, 768×1024 and 390×844.
- [ ] Find your place, 中文？ band, Pastor quote card and Stay in the loop are gone.
- [ ] Newsletter, Meet our pastors, Community, Contact and Give match the preview at 1440, 768 and 390 (small pixel differences OK).
- [ ] Community and Contact tabs switch by click and arrow keys; each tab group works on its own.
- [ ] Both Contact forms submit and appear in Fluent Forms entries.
- [ ] Every tap target is at least 44px; red text on light backgrounds uses `#B8231E`.
- [ ] Chinese never appears without its English, and no Chinese line is under 14px.
- [ ] Page height ≤ 8,400px at 1440 and ≤ 10,500px at 390. No horizontal scroll at 360px.
- [ ] Volunteer test still passes (change hero headline, a service time, a Community photo, a menu item and the Primary colour without code).
- [ ] Re-export the changed `elementor/*.json`, then retake `screenshots/` at 1440, 768 and 390.

## Design source

`design-reference/` holds the design boards:

| File | Builds |
|---|---|
| `Hybrid-preview.html` | **Home page, header, footer (current)** |
| `Combined.dc.html` | Old Home board (superseded; keep for reference) |
| `Welcome.dc.html` | Welcome page (ignore the dark "Demo designs" bar at top) |
| `Prayer.dc.html` | Prayer page (use the form layout; form via Fluent Forms) |
| `PaletteV2.dc.html` | Old colours (superseded by step 1) |

The `.dc.html` boards are a custom format and won't render standalone; read the
markup. `Hybrid-preview.html` is plain HTML and renders in any browser.

## Stack

| Part | Choice |
|---|---|
| Local env | **Local** (by WP Engine), site "MPCBC" (`http://mpcbc.local`), latest WordPress, PHP 8.2+ |
| Parent theme | Hello Elementor (unchanged) |
| Child theme | `hello-mpcbc/` — see below |
| Plugins | Elementor (free), Essential Addons for Elementor (free — only Fancy Text enabled, switch other EA widgets off), Fluent Forms (free), Duplicator (for the move; deleted after) |
| Hosting (later, by the user) | Fresh WordPress on a HostGator subdomain/temp domain, imported with Duplicator |

### Child theme `hello-mpcbc`

```
hello-mpcbc/
├─ style.css      theme header + header CSS: see-through header over the homepage
│                 video (body.home), one-line bilingual menu, dropdowns, small fixes
├─ functions.php  enqueue parent style, Google Fonts (Bricolage Grotesque,
│                 Geist, Noto Sans TC, Noto Sans SC), nav.js, video-toggle.js; show each
│                 menu item's Description as the Chinese label
├─ footer.php     render the Elementor template titled "MPCBC – Footer" on
│                 every page via
│                 Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $id )
│                 then wp_footer()
├─ nav.js         toggles body.is-scrolled at scrollY ≥ 40 (header fades to Paper)
└─ video-toggle.js  small Pause/Play button on every Elementor background video
```

Header = **Hello Elementor's built-in header** (Site Settings › Hello Header &
Footer): logo, menu, mobile toggle. Do not write a header.php. Menu items are
in step 2.

## Pages

| Page | Layout | Content |
|---|---|---|
| Home `/` (front page) | Elementor Full Width | `Hybrid-preview.html`, steps 3–11 |
| Welcome `/welcome/` | Elementor Canvas | Welcome board. Rotating greeting = Essential Addons **Fancy Text** (Welcome / 歡迎你 / 欢迎你). Buttons: English → `/`, 繁體中文 → `/coming-soon/`, 简体中文 → `/coming-soon/` |
| Prayer `/prayer/` | Elementor Full Width | Prayer board; Fluent Forms "Prayer request" form (request, optional name, optional email/phone, who may see it: pastors only / pastors + prayer team, contact-me checkbox) |
| Coming soon `/coming-soon/` | Elementor Full Width | One bilingual placeholder |

The other pages already in WordPress (About, Give, Kids, Sundays, Visit, Watch,
中文) stay as they are for now; restyle them only through Global Colours.

Set Site Settings › Layout › Default Page Layout = Elementor Full Width.
Settings › Reading: static front page = Home. Tick "Discourage search engines".

## Design system

Colours: step 1. Fonts: Bricolage Grotesque (headings, 700, letter-spacing
−0.035em), Geist (body), Noto Sans TC (Chinese, 500 for second lines, 700 for
Chinese titles), Noto Sans SC (Simplified). If Bricolage Grotesque and Geist are
in Elementor's font list, set them as Global Fonts (Primary / Text). If not, tick
Elementor › Settings › "Disable Default Fonts" so widgets inherit the theme fonts.

The child theme CSS must read `var(--e-global-color-primary)` etc. so one
change in Site Settings restyles the header too.

Theme Style: buttons radius 12px, min-height 56px desktop / 48px mobile (44px
for small buttons in cards and panels), weight 600, Primary background.
Outline buttons: transparent, `1.5px` Line strong border, Ink text, Mist on hover.

## Responsive

Elementor default breakpoints (mobile ≤767, tablet ≤1024), plus two CSS-only
points in the child theme: menu collapse ≤1109px, times card two rows ≤1279px.
Side padding 16 / 32 / 56px; content max width 1328px. No sideways scroll at
360px. Tap targets ≥44px.

## Workflow

1. Build one small container by hand in Elementor and export it, to learn the
   exact JSON keys. Don't guess keys.
2. Global Colours (step 1) → child theme CSS → menu (step 2).
3. Build or change each Home section (by importing JSON you write into
   `elementor/`, or directly in the editor). After each section,
   screenshot at 390, 768, 1440px and compare with the preview; fix, repeat.
4. Footer template.
5. Volunteer test in the Elementor editor: change the hero headline, a service
   time, a Community photo, a menu item and the Primary colour. All must work
   without code.
6. Duplicator package of the finished site.

## Deliverables (in the repo root)

- `hello-mpcbc/` and `hello-mpcbc.zip`
- `elementor/*.json` (if you used the import route), `fluent-forms/connect-form.json`
- `screenshots/` — every page at 390, 768, 1440px
- `README.md` — for volunteers: where to edit what (Elementor, Menus, Site
  Settings, Fluent Forms); for the admin: Duplicator import to HostGator steps
- The Duplicator package (do not commit it to git; tell the user where it is)

## Acceptance

- [ ] Home matches `Hybrid-preview.html` at 1440, 768 and 390px (small pixel differences OK)
- [ ] No overflow or clipping at 360px; menu and dropdowns work by touch and keyboard
- [ ] Welcome buttons go to the right pages; greeting rotates
- [ ] Changing Primary colour once restyles buttons, links, selected tabs and header
- [ ] Volunteer test (workflow step 5) passes
- [ ] Background video autoplays, muted and looping, with a small working Pause button
- [ ] Prayer, connect and email forms submit and appear in Fluent Forms entries
- [ ] Duplicator package imports into a fresh WordPress install
- [ ] Before launch: someone at the church checks every Chinese label (menu, headings, forms)

Latest follow-up: the footer spacing was refined with the installed native MCP ability. Current state is 24 native Elementor documents with 1,990 Atomic elements and seven placements of the same three approved Fluent Forms. Mobile language selection is outside the hamburger; English is the new-visitor default, while choices persist. Only menu descriptions auto-convert to Simplified: edit their Traditional source in Appearance > Menus. Home remains bilingual, with independent Simplified wording on Home · 简体中文 (511) and its footer template (513); edit these in Elementor. Original Home (21) and footer (16) serve English/Traditional. The one-time Simplified seed must not be regenerated over editorial wording. Preserve vendor/opencc-js and its licenses in migration backups. These notes supersede the earlier 22-document/four-placement count.

Language routing fix: language destinations now derive from the current page, preserving its anchor. Home Traditional is /?lang=zh-hant, never the content directory or About. English/Traditional on their shared bilingual document changes preference and URL without a reload. English switch links use their final URL, avoiding the prior extra load. OpenCC loads in the footer only for Simplified requests and converts menu descriptions once; it does not block English/Traditional rendering or observe page content. Desktop/mobile and About#beliefs checks are in content-source/language-routing-fix-checks.json. The installed MCP ability read Simplified Home successfully under the existing administrator context, and a no-op heading update passed on draft preview 420. Network MCP remains disconnected.

CURRENT editing model — shared Home/footer consolidation: Home (21) is the single master for English, Traditional and Simplified bilingual views. One site-wide footer template (16) serves every page. There are 50 pairs of separately editable native Chinese text elements, named Traditional / 繁體中文 and Simplified / 简体中文 in Structure; shared English, links and layout are maintained once per placement. All three Home choices switch without reloading. Legacy Home 511 is a redirect; footer 513 is a draft archive. Maintained totals: 22 documents, 1,732 Atomic elements, four placements of the same three Fluent Forms. This supersedes the earlier independent-Home notes. Read HOME-FOOTER-EDITING.md. The guarded consolidation builders must not overwrite volunteer edits. Use a fresh full backup for migration.
