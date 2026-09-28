# MPCBC Homepage — Critique & Fix List

Source reviewed: design canvas **"MPCBC Homepage Concepts"**
(https://claude.ai/artifact/Gd99SV25TnFbZpfgYQACo8) — artboards
`Main.dc.html` (A · current), `FrontDoor.dc.html` (B), `Hybrid.dc.html` (C),
`Highlights.dc.html` (E), `Palette.dc.html`. Reviewed from the artboard source
(markup, copy, colours, type), not from rendered screenshots.

Owner direction for this round (overrides the old DESIGN-BRIEF constraints):

- **Design A is retired as a page.** Its good sections survive only as reusable
  components inside the new design.
- **All previous design restrictions are lifted** (bilingual-on-one-line parity,
  maroon palette, `--live` rules, "Chinese is never a toggle", etc.).
- **Wording is bad everywhere** — rewrite it.
- **Serve pure-English and pure-Chinese visitors.** English-side feedback: A has
  too much Chinese and an old colour/design pattern that turns them away. The
  church is also getting **many Chinese-speaking newcomers**.
- **Keep the looping hero video.**
- Placeholder content is fine; mark it `[LIKE THIS]`.

Decisions made after the first draft (2026-09-28):

- **Simplified Chinese: confirmed.** `/zh` gets a 繁 / 简 switch (H3).
- **"Real people" / newcomer stories section: dropped.**
- **Plan a visit: kept as an information page, no sign-up form for now** (C4).
- **Give stays in the main menu** (H4).
- **Two demo designs, desktop only**: Design 1 = separate languages (§0.1),
  Design 2 = combined English-first homepage with minimal Chinese (§0.4).
- **Phone designs postponed** until the desktop design is chosen (C5).
- **Add a prayer request button + form** (H13).
- **Kids' classes are taught in English**; Chinese-speaking helpers at check-in
  for parents. Say so on both language versions (and in one short Chinese line
  in Design 2's kids section).
- **Colour: "Evergreen & Sun" recommended** (evergreen `#0E6B5C`, sun
  `#F4B942`); alternative "Plum & Apricot" (`#7A2E5E`, `#F2A65A`) keeps a link
  to the logo maroon. Both are drawn on the design canvas (Round 2 page) with a
  Colour switch on every artboard.

Technical rules in `AGENTS.md` still apply when this reaches the Astro repo
(Tina + `content.config.ts` + `public/cms/shared.js` in step; never touch
`services.json`, `.env`, sermon files; stop the dev server before editing
config; do not push to `main`).

---

## 0. The target (read this first)

One sentence: **a video-led welcome that splits into two monolingual sites —
English and 中文 — each built from the same components, each with one job:
get a first-time visitor to plan a Sunday visit.**

### 0.1 Merge of B + C + E (A dissolves into components)

| Route | What it is | Built from |
|---|---|---|
| `/` | **Welcome.** Full-screen looping reel (E's hero). Only screen allowed to mix languages. Two huge buttons: **English** · **中文** (with small 繁 / 简 under 中文). Remembers the choice; returning visitors skip straight through (with a visible "change language" link). | E hero + B's front-door idea |
| `/en` | English home. 100% English. | E structure + C's "doors" |
| `/zh` | 中文 home. 100% Traditional Chinese, with a 简体 switch. Written for Chinese readers, not translated. | E structure + C's "doors" |
| `/en/kids`, `/zh/kids` | Children & families, per language (parents may be either). | A's kids section, rebuilt |
| `/en/visit`, `/zh/visit` | Plan-a-visit page: times, parking, kids, what happens, contact (phone / email / WeChat). No form for now. | E's "Plan a visit" band, expanded |

Why not auto-redirect `/` by browser language: many Chinese-speaking families
here run English phones/OSes, and many English speakers share a device with
Chinese-reading parents. A one-tap choice on top of a beautiful video is not a
dead splash — it *is* the hero. Use `Accept-Language` / `navigator.language`
only to **pre-highlight** the likely button.

### 0.2 Section order — `/en` (and mirror for `/zh`)

1. **Hero** — looping video, 1 headline, 1 line, primary CTA *Plan your visit*,
   secondary *Watch live*. Bottom of hero: next service chip
   ("Sunday 11:00 AM · 555 Example St · Directions").
2. **This Sunday strip** — service time(s) for *this* language + live status
   (A's service board logic, restyled to one row).
3. **Find your place** — 3 photo doors (C): *Sunday service* · *Kids (0–11)* ·
   *Youth & young adults*. On `/zh`: *粵語崇拜* · *國語崇拜* · *兒童及青少年*.
4. **Your first Sunday** — 4 numbered steps (park → greeted → kids check-in →
   lunch) + CTA. (from E's Plan-a-visit)
5. **Kids** — full-bleed colour section, age cards, trust line (A/E kids).
6. **Pastor's welcome** — short, per language, with portrait.
7. **Coming up + Latest message** — events list + one featured sermon, both
   filtered to this language.
8. **Stay connected** — EN: Instagram/YouTube/email. ZH: **WeChat QR** + YouTube.
9. **Footer** — address, map link, times, give, contact, language switch.

### 0.3 Components kept from A (restyle, don't copy)

| A section | Verdict | New home |
|---|---|---|
| Service board (3 cards + live state) | **Keep the logic**, kill the look | "This Sunday strip", per language |
| Announcements carousel | Keep, simplify to 3 cards, audience-tagged | Below "Coming up" or merge into it |
| Pastor's note | Keep, per language, shorter | Section 6 |
| Children & families (age cards) | **Keep**, strongest content in A | Section 5 + `/kids` |
| Upcoming events list | Keep | Section 7 |
| Recent sermons | Keep, filter by language, show 1 featured | Section 7 |
| 50th anniversary video | **Remove from home** | About page |
| "More photos" Facebook block | **Remove** | Replace with Section 8 |
| Footer newsletter | Keep, only if it actually sends somewhere | Footer |

### 0.4 The two demos to build (desktop only, 1440 wide)

Both demos use the **same new visual system and the same components**, so the
owner compares *structure*, not styling. A simple toggle bar at the top
switches between them: **Design 1 · Separate languages** | **Design 2 ·
Combined**.

**Design 1 — Separate languages** (the structure in §0.1–0.3)
Artboards: `/` Welcome (video + English / 中文 buttons), `/en` home, `/zh`
home (with 繁/简 switch), `/en/visit`, `/zh/visit`, prayer request page.

**Design 2 — Combined, English-first** (successor to the original A)
One homepage for everyone. English leads; Chinese appears **only** where a
Chinese-only visitor needs it to find their way:

| Where | Chinese allowed |
|---|---|
| Nav | one `中文` link (goes to a Chinese visit/info page) — no Chinese after every menu item |
| Hero | one small line under the English headline, e.g. 「歡迎你 ・ 粵語 9:30 ・ 國語 11:30」 |
| This Sunday strip | the two Chinese service names (粵語崇拜, 國語崇拜) beside their English label |
| Find your place | the 粵語 and 國語 doors carry their Chinese name + one short Chinese line |
| One "中文訪客" strip | a single band: 「第一次來？中文資訊 →」 linking to `/zh/visit` |
| Everywhere else | **English only** — no bilingual eyebrows, buttons, headings or footers |

Section order: hero (video) → This Sunday → Find your place (4 doors: English
· 粵語 · 國語 · Kids) → Your first Sunday → 中文訪客 strip → Kids → Pastor's
welcome + prayer request button → Coming up + Latest message → Stay connected
→ Footer.
Rule of thumb: an English visitor should be able to read the whole page and
see Chinese only as labels; aim for **<10% of visible text in Chinese**.

---

## 1. Pass one — the designer tearing it apart

The honest summary: **none of the four designs would make a stranger want to
come.** A looks like a 2015 church-bulletin theme. B/C/E swapped the maroon for
a primary-colour triad that looks like a bank-meets-preschool starter kit. All
four share the same template skeleton: eyebrow-with-a-rule → h2 → grey
paragraph → grid of identical 24px-radius bordered cards → repeat, at 88px
padding, forever. The copy talks about the church's *history and logistics*,
never about the *visitor*.

### Critical

**C1 — Two languages on every line is killing both audiences.**
Where: A everywhere; B/C/E nav, headings, eyebrows, buttons ("Archive 存檔",
"Children's ministry 兒童事工", "Find your place 找到您的群體").
Why: an English-only visitor sees half the page as noise and concludes "not for
me". A Chinese-only visitor gets half-sentences. Every line doubles cognitive
load.
Fix: monolingual `/en` and `/zh` (§0.1). Mixed language allowed **only** on `/`
and in the language switcher. Remove every inline `<span lang="zh-Hant">` from
English pages and every English fragment from Chinese pages (except proper
nouns like "YouTube").
Done when: `/en` contains zero CJK characters outside the language switcher;
`/zh` contains no English UI words.

**C2 — The story told to visitors is the church's family tree, and it repels
the exact people you want.**
Where: A hero lede ("Grandparents worship in Cantonese, parents in Mandarin,
children in English"), A English card ("Mostly the children and grandchildren
of the founding families"), A/E Mandarin ("Our newest congregation"), E "Who
we are".
Why: to an English-speaking adult this says *the English service is for
the kids of members*. To a new Mandarin family from abroad it says *this is
someone else's family reunion*. Nobody reads "founding families" as "you
belong".
Fix: every headline and card is about the **visitor** and what they get. Use
the copy deck in §3. History goes to About.

**C3 — Internal / operational copy is leaking onto the page.**
Where: A "Live links update automatically from YouTube — nobody edits this
weekly", "Videos are embedded from YouTube, never stored on the website", "We
post photos from events to Facebook, where our members already are",
"youtube · scheduled", "youtube · live now", "Archive".
Fix: delete all of it. Status labels become human: **Live now**, **Starts
Sunday 11:00 AM**, **Watch last week's service**.

**C4 — No clear next step.**
Where: all designs — five different CTAs per page.
Decision: **keep "Plan your visit / 計劃來訪" as the one primary action, but it
opens an information page, not a sign-up form** (no one to follow up form
submissions yet; a form that goes nowhere is worse than none).
The visit page answers, in order: when · where + parking · what happens (the
4 steps) · kids · what to wear / how long · contact (phone, email; **WeChat QR
on `/zh`**). Optional later: a "Let us know you're coming" form once someone
owns the inbox.
Same button label and colour everywhere; nothing else uses that colour.
Done when: the CTA appears in the nav, the hero, after "Your first Sunday", in
the footer and in the mobile sticky bar — and no competing primary button
exists.

**C5 — There are no phone designs.** *(postponed by owner — do after the
desktop design is chosen)*
When it's time: 390×844 artboards for every page; a **sticky bottom bar**
(*Plan your visit* · *Directions* · *Watch live*); headline + CTA + next-service
chip visible without scrolling.

**C6 — The visual system reads as "old" (A) or "template" (B/C/E).**
Where: A maroon + dotted `--shell` + serif Chinese + Nanum Pen handwriting =
church bulletin. Palette board `#2F5BD3 / #FFC43D / #237A4B` on cream = the
default-blue + primary triad every AI starter kit ships.
Fix: new tokens, one accent, one warm highlight, everything else neutral.
Proposed (swap freely, but keep the structure — one accent, one highlight):

| Token | Hex | Use |
|---|---|---|
| `--ink` | `#16140F` | text |
| `--ink-2` | `#5C564D` | secondary text (≥4.5:1 on paper) |
| `--paper` | `#FAF7F2` | page |
| `--stone` | `#EDE6DA` | alternate sections, cards |
| `--night` | `#12110E` | hero scrim, footer |
| `--accent` | `#0E6B5C` | the **one** CTA colour, links (white text ≈ 6:1) |
| `--sun` | `#F4B942` | kids section, highlights; **dark text only** |
| `--live` | `#E0312B` | live-now pill only, with pulsing dot + the word "Live" |

Delete: the dotted placeholder texture, the maroon family, the tri-colour
"one colour per congregation" idea (it makes three brands, not one church).
Congregations are distinguished by photo and name, not colour.

**C7 — The hero is a placeholder, and its text fights the video.**
Where: A (720px, 5 stacked items over the video: h1, zh line, 3-line lede,
button, tracked meta line), B (no video — a navy panel with a cycling word),
C/E (fine structure, weak words).
Fix (all heroes): full-bleed video, `100svh` on `/`, `88svh` on `/en` `/zh`;
bottom-up gradient scrim `rgba(18,17,14,0)`→`.75`; **max three things on top:**
headline (≤ 7 words), one line (≤ 20 words), CTA pair. Pause/play button bottom
right (keep C/E's). `poster` image always; `prefers-reduced-motion` → poster
only; small mobile rendition. Video spec: 20–30 s, muted, loop, H.264 + WebM,
1080p ≤ 6 MB, mobile 720p ≤ 2.5 MB, hosted on R2.

**C8 — The first screen doesn't answer "when, where, and is it in my
language?"**
Where: A buries the address in the footer; B has none; C/E have times but no
address or directions.
Fix: hero bottom chip on every home: **"Sundays 11:00 AM · [Street], Monterey
Park · Directions →"** (Directions opens Maps). On `/zh`: 「粵語 9:30 ・ 國語
11:30 ・ [地址] ・ 導航 →」.

### High impact

**H1 — Template tells to remove.**
- Eyebrow label with a 28px rule before every heading (`.eyebrow::before`).
- 13px uppercase letter-spaced Inter meta labels (and uppercase is meaningless
  in Chinese — `ENGLISH 11:00` next to `粵語` looks broken).
- Every block is a white card, 1px border, 24px radius, identical padding.
- Every section is `padding: 88px 0` alternating white/tint.
- Arrow "→" on every link.
- Handwritten Nanum Pen Script notes ("bring a friend —",
  "every volunteer is background-checked") — reads as clip-art.
Fix: vary rhythm (full-bleed photo section → tight strip → big-type statement
→ split layout); cards only where things are genuinely parallel (the three
doors, age groups); section padding from a scale (48 / 96 / 144) chosen by
content; arrows only on text links.

**H2 — Typography has no voice and the Chinese looks formal/old.**
Fix: English display in a face with personality (e.g. *Bricolage Grotesque*
or *Instrument Serif* for headlines) over a clean body (e.g. *Geist* is fine
for body). Chinese: **Noto Sans TC / 思源黑體 Bold** for headings (modern,
friendly) instead of Noto Serif TC (formal, hymnal); Noto Sans TC 400 body.
Body 18px minimum (older readers), Chinese line-height 1.8, English 1.6.
No letter-spacing on CJK.

**H3 — Simplified Chinese for Mandarin newcomers.** *(confirmed by owner)*
If most new Chinese-speaking visitors are from mainland China, an all-Traditional
site signals "not really for you". Fix: 繁 / 简 switch on `/zh` (build-time
OpenCC conversion or client-side conversion of the same content — never two
hand-maintained copies). Default 繁; remember choice.

**H4 — Navigation is insider-speak.**
Where: About · Services · Newsletter · Offering (+ Chinese on each).
Fix:
- EN: **Visit · Sundays · Kids & Youth · Watch · About · Give** · [Plan your visit] · `中文`
- ZH: **初次來訪 · 主日聚會 · 兒童及青少年 · 線上崇拜 · 關於我們 · 奉獻** · [計劃來訪] · `EN | 繁 | 简`
- Design 2 (combined): the EN menu, with `中文` linking to `/zh/visit`.
"Give / 奉獻" **stays in the main menu** (owner decision) — last plain item,
never styled as a button. "Newsletter" moves to the footer.

**H5 — Service board: great logic, dead presentation.**
Where: A 3 cards with 200px dotted "youtube · …" wells.
Fix: one row per service in this language: name · time · status. Status
states: **● Live now — Watch** (live red pill), **Starts in 2 days** (countdown),
**Watch last Sunday** (ended/unscheduled). All four states must look
intentional; none is "empty". On `/en` it's one row, so render it inline in the
hero chip + a small "Watch" card rather than a whole section.

**H6 — Kids section is the best content and it's under-sold.**
Fix: full-bleed `--sun` section with real photos (consent-cleared), headline
about the parent's worry, three age cards (Nursery 0–3 · Kids 4–11 · Youth
12–18), and a **trust row**: check-in with a matching tag · background-checked
volunteers · classes in English (and Mandarin, if true). Replace the
handwritten note with a real line. CTA: *Plan a visit with kids*.

**H7 — (dropped by owner)** Newcomer stories / social proof section — not doing it.

**H8 — Channels don't match the audiences.**
Fix: `/zh` gets a **WeChat** block (QR + "加入新朋友群"). `/en` gets
Instagram + YouTube. Drop the "we post photos to Facebook" block.

**H9 — B (front door) specific problems, if any of it is reused.**
- "Welcome back. Last time you chose 中文" bar is visible to first-time visitors
  in the mock — confusing. Only show it when a stored choice exists.
- Door captions sit in 90%-opaque navy boxes that hide the "window into the
  congregation" video — use a gradient scrim instead.
- Three doors (Kids / English / 中文) mix *audience* with *language*. Language
  first (English / 中文), kids inside each.
- "Where would you like to start? / 您想從哪裡開始？" is a question the visitor
  didn't ask. `/` just says **Welcome · 歡迎** and shows two buttons.
- 你 vs 您 is inconsistent (歡迎你 / 您想從…). Pick **你** site-wide (warm, the
  norm in HK and mainland church writing).

**H10 — C and E specific problems.**
- C door CTAs are inconsistent: "What to expect →", "Watch now", "English
  congregation →", "國語堂 →". One verb pattern: *See [thing]*.
- C English door has a stray "—" where the other doors have a subtitle.
- E hero is a still-photo slideshow with dots — fine as fallback, but the owner
  wants a looping **video**; dots become meaningless then. Keep only pause/play.
- E "This Sunday" 4-cell bar is the best idea in the canvas — keep it
  (per-language version) as the hero chip / strip.
- E "Find your congregation / Three services, one family" repeats the
  bilingual framing; on monolingual pages it becomes doors (§0.2 step 3).

**H11 — Accessibility.**
- `rgba(255,255,255,.6)` meta text on `#1B1014`/`#121A33` fails 4.5:1 at 13px.
- 13–15px secondary text in `#6B5560`/`#56607A` on tint is borderline; use 16px+
  and `--ink-2` as defined.
- Every video/slideshow: pause control, `aria-label`, poster, reduced-motion.
- `lang` on `<html>` must be `en` or `zh-Hant`/`zh-Hans` per page (not
  per-span once languages are split).
- Language switch: real links with `hreflang`, labelled in their own language.
- Touch targets ≥ 44px (the demo toggle bar tabs are 40px).

**H12 — Content model changes (when ported to the repo).**
Announcements, events and pastor note need a `language` field
(`en | zh | all`); homepage becomes two documents (`homepage-en.json`,
`homepage-zh.json`). Sermons already carry a congregation — filter by it. Per
`AGENTS.md`, update `tina/config.ts`, `src/content.config.ts` and
`public/cms/shared.js` together, and stop the dev server first.

**H13 — Prayer request (new, owner request).**
Button label: **Prayer request** / **代禱請求**. Placement: under the pastor's
welcome ("How can we pray for you?" / 「我們可以怎樣為你禱告？」), in the
footer, and on the About/Visit pages. Not in the main menu.
It opens a short page/form:
- Your request (required, multiline)
- Name (optional) · Email or phone (optional) · **WeChat ID** on `/zh` (optional)
- Who may see it: ◉ Pastors only ○ Pastors and the prayer team
- ☐ I'd like someone to contact me
- Submit → "Thank you. Our pastors will pray for you this week." (in the page's
  language)
- Line under the button: who reads requests and that they are kept private.

Implementation options, simplest first:

| Option | How | Pros | Cons |
|---|---|---|---|
| **1. Google Form or Tally, embedded** *(recommended to start)* | A volunteer builds the form in the church's Google account; the site shows it on `/prayer` (or links out). Responses go to a Sheet + email alert to the pastor. | No code, free, volunteer-owned, working in an hour | Looks less native; data sits in Google/Tally |
| 2. `mailto:` button | Opens the visitor's mail app to a prayer address | Zero setup | Many phones have no mail app set up; exposes the address to spam |
| 3. Native form → Cloudflare Worker | Form posts to a new `/api/prayer` route on the existing Worker; Cloudflare Turnstile blocks spam; the Worker emails the prayer inbox (e.g. Resend or Cloudflare Email) and optionally stores in D1 with auto-delete after 30 days | Fully on-brand, bilingual, private | Needs an email service key (secret), someone to maintain it |

Recommendation: ship the **button + page with option 1** now; move to option 3
only if the church wants it fully native. For the demo, draw the form natively
with a `[FORM ENDPOINT]` placeholder action. Never store requests in the git
repo or in the CMS content.

### Nice to have

- **N1** Site-wide "● Live now" pill in the header during a live service, per
  language.
- **N2** Scroll-in fades (200 ms, 8px rise) and hover zoom on photo doors;
  disabled under reduced motion. No parallax, no count-up numbers.
- **N3** "Add to calendar" (.ics) on the next-service chip.
- **N4** 60-second "What a Sunday looks like" video on the visit page.
- **N5** Simple illustrated map: entrance, parking, kids' rooms.
- **N6** Seasonal hero swap (Easter, Christmas) via CMS.
- **N8** Analytics event on *Plan your visit* clicks (Cloudflare Web Analytics)
  so the owner can see if the redesign converts.
- **N9** Demo toggle bar: add `/`, `/en`, `/zh`, mobile views; drop the A tab
  once components are merged.

---

## 2. Pass two — first-time visitors clicking through

Three personas walked through A, B, C and E as they exist today.

### Persona 1 — Jake, 29, English only, just moved to Alhambra, found you on Google Maps (phone)

| Step | What happened | Wanted to leave? |
|---|---|---|
| Lands on **A** | Sees "A church for three languages." then Chinese characters. Thinks: *this is a Chinese church, I'm the outsider.* | **Yes — first 5 seconds.** |
| Reads lede | "…children in English." *So English is the kids' service?* | Yes |
| Nav | "Offering 奉獻" — *they want money before hello.* "Newsletter" — why is that top-level? | — |
| Service board | "youtube · scheduled", "Archive 存檔". English card: "Mostly the children and grandchildren of the founding families." *Closed circle. Not for me.* | **Leaves here.** |
| Tries **B** | Blue panel, "Where would you like to start?" Picks English → (mock) no English home exists yet. | Confused |
| Tries **C** | Better — photo doors. English door says "—" and "English congregation →". Still Chinese in every heading. | Hesitant |
| Tries **E** | Slideshow feels alive. "This Sunday" bar is useful. Then "Who we are" is a paragraph about grandparents again. No address on screen. Where do I park? | Scrolls to bottom to find address |
| Overall | Wants: time, address, "is anyone my age there?", "will it be weird if I come alone?" None answered above the fold. | — |

Fixes: C1, C2, C3, C8, H4, §3 copy.

### Persona 2 — 王太太 (Mrs. Wang), 38, moved from Guangzhou/Shenzhen 8 months ago, Mandarin, reads Simplified, two kids (6 and 9), a friend sent the link in WeChat (phone)

| Step | What happened | Wanted to leave? |
|---|---|---|
| Lands on **A** from WeChat | English headline first. Chinese line is a slogan, not information. | Unsure |
| Looks for Mandarin | 國語 card says "Our newest congregation, with traditional-character subtitles" — in English. | Confused |
| Traditional characters everywhere | Readable but feels like a Hong Kong/Taiwan church. *Is it for mainland people?* | Doubt |
| Kids | "Sunday school at the 11:30 hour in Mandarin" — in English only. Is the class in Mandarin or English? | **Key question unanswered** |
| Wants to contact someone | No WeChat. Phone/email in footer, placeholders. | **Leaves — asks her friend instead.** |
| **B** | Picks 中文 door — good instinct. "粵語 · 國語" under it is clear. | Better |
| **E** | "國語堂 · 我們最年輕的會眾，附繁體中文字幕" — 會眾/附…字幕 feels translated. | — |

Fixes: C1, H3, H6, H8, H9 (你), §3 zh copy, WeChat block.

### Persona 3 — 陳婆婆 (Mrs. Chan), 74, Cantonese, reads Traditional, large text on an old iPhone, grandson showed her

| Step | What happened | Wanted to leave? |
|---|---|---|
| **A** | Small 13px uppercase labels unreadable. The 粵語 time is inside an English sentence. | Frustrated |
| **B** | Cycling "Welcome / 歡迎你" moves before she reads it. Door text small on dark blue. | Confused |
| **E** | Auto-advancing slideshow moves too fast. Finds the pause button (good). | — |
| Wants | "What time is Cantonese? Is there a live stream today?" | Answer exists in C/E, but in mixed language and small type. |

Fixes: C1, C8, H2 (18px body), H5 (Live now — 直播中), H11.

### Cross-cutting confusion points (all designs)

1. **"Which one am I?"** — language and congregation choices are presented as
   history, not as "choose this if…".
2. **No clear next step** — five different CTAs per page ("What to expect",
   "Read our welcome page", "Open the church calendar", "Enter", "Plan a
   visit").
3. **No answer to the fear question** — "Will I be singled out? Can I come
   alone? What about my kids?" Only E hints at it, far down the page.
4. **No sign of real people** — all placeholders are labelled "photo". Even in
   a demo, use real-looking stock or the church's own photos so the design can
   be judged.

---

## 3. Copy deck (placeholders in `[BRACKETS]`)

Tone: warm, short, specific, second person. No history in headlines. No
jargon (congregation, fellowship, archive, ministry) on the home pages.

### `/` — Welcome

- Headline (both, stacked, equal size): **Welcome** / **歡迎**
- Buttons: **English** · **中文** (under 中文, small: 繁體 · 简体)
- Small line under buttons: `[Street], Monterey Park · Since 1976`
- Returning: "Continue in English →" / 「繼續瀏覽中文版 →」 with "change" link.

### `/en`

| Slot | Copy |
|---|---|
| Hero headline | **You're welcome here.** |
| Hero line | A friendly church in Monterey Park. Come as you are — we'll save you a seat. |
| Primary CTA | Plan your visit |
| Secondary CTA | Watch live |
| Hero chip | Sundays 11:00 AM · [Street], Monterey Park · Directions |
| This Sunday | **This Sunday** — English service, 11:00 AM · Kids' classes at the same time |
| Doors heading | **Find your place** |
| Door 1 | **Sunday service** — 11:00 AM. Honest teaching, good music, lunch after. → See Sundays |
| Door 2 | **Kids (0–11)** — Safe, fun classes while you're in the service. → See Kids |
| Door 3 | **Youth & young adults** — Fridays [7:30 PM]. Food, friends, real questions. → See Youth |
| First Sunday heading | **Your first Sunday** |
| Steps | 1 **Park behind the building.** 2 **Someone will say hi at the door** and show you around. 3 **Check the kids in** — takes two minutes. 4 **Stay for lunch.** No one will single you out. |
| First Sunday CTA | Plan your visit |
| Kids heading | **Your kids will want to come back.** |
| Kids trust row | Secure check-in · Background-checked volunteers · Same time as the service |
| Pastor heading | **A word from [Pastor name]** — 2–3 sentences, first person, to the visitor. |
| Coming up | **Coming up** |
| Latest message | **Latest message** — [Title] · [Speaker] · Watch |
| Connect | **Stay in the loop** — Instagram · YouTube · Email |
| Footer | [Street], Monterey Park, CA · [Phone] · [Email] · Give · 中文 |
| Language hint | Looking for Cantonese or Mandarin services? **中文 →** |

### `/zh` (Traditional; 简体 via conversion)

| Slot | Copy |
|---|---|
| Hero headline | **在異鄉，也有一個家。** |
| Hero line | 蒙特利公園華人浸信會，無論你從哪裡來，這裡都有你的位置。 |
| Primary CTA | 計劃來訪 |
| Secondary CTA | 觀看直播 |
| Hero chip | 粵語 9:30 ・ 國語 11:30 ・ [地址] ・ 導航 |
| This Sunday | **本主日** — 粵語崇拜 上午 9:30 ・ 國語崇拜 上午 11:30 ・ 兒童主日學同時進行 |
| Doors heading | **找到適合你的聚會** |
| Door 1 | **粵語崇拜** — 上午 9:30。崇拜後一起飲茶傾偈。→ 了解更多 |
| Door 2 | **國語崇拜** — 上午 11:30。新朋友很多，你不會是唯一的一個。→ 了解更多 |
| Door 3 | **兒童及青少年** — 孩子在安全、有趣的環境中認識神。→ 了解更多 |
| First Sunday heading | **第一次來？** |
| Steps | 1 **車可停在教會後方。** 2 **門口有同工迎接你**，帶你熟悉環境。 3 **為孩子登記**，兩分鐘完成。 4 **崇拜後一起吃午飯。** 不用上台，也不用自我介紹。 |
| First Sunday CTA | 計劃來訪 |
| Kids heading | **孩子會想再來。** |
| Kids trust row | 安全簽到 ・ 同工均經背景審查 ・ 與崇拜同時進行 ・ 課堂語言：[英語／國語] |
| Pastor heading | **[牧師姓名]牧師的話** |
| Coming up | **近期活動** |
| Latest message | **最新講道** — [題目] ・ [講員] ・ 觀看 |
| Connect | **加入我們的微信群** — [QR] ・ YouTube |
| Footer | [地址] ・ [電話] ・ [電郵] ・ 奉獻 ・ English |

Words to never use on the home pages: *congregation / 會眾*, *archive / 存檔*,
*founding families*, *three languages / 三種語言* as a headline, *scheduled*,
*nobody edits this weekly*.

---

## 4. Suggested implementation order

1. **Tokens + type** (C6, H2) — one shared stylesheet/`helmet` block.
2. **Shared components** in the new style: hero with video, This Sunday strip
   (H5), doors, first-Sunday steps, kids (H6), pastor + prayer button (H13),
   coming up/latest message, connect (H8), nav with Give (H4), footer.
3. **Design 1 — Separate languages** (desktop): `/` Welcome, `/en`, `/zh`
   (繁/简 switch), `/en/visit`, `/zh/visit`, `/prayer` — copy from §3.
4. **Design 2 — Combined** (desktop): one homepage per §0.4, plus the
   `/zh/visit` page it links to.
5. **Toggle bar** between Design 1 and Design 2; drop the old A/B/C/E tabs
   (keep the old artboards on the canvas as "before").
6. Owner picks a direction → **phone designs** (C5).
7. Nice-to-haves (N1–N9).
8. Port to the Astro repo with the content-model changes in H12 and the
   prayer form option chosen in H13.
