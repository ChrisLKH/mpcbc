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
| `/en/visit`, `/zh/visit` | Plan-a-visit page with the form (the conversion). | E's "Plan a visit" band, expanded |

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
5. **Real people** — 2–3 short newcomer stories with photo (placeholder).
6. **Kids** — full-bleed colour section, age cards, trust line (A/E kids).
7. **Pastor's welcome** — short, per language, with portrait.
8. **Coming up + Latest message** — events list + one featured sermon, both
   filtered to this language.
9. **Stay connected** — EN: Instagram/YouTube/email. ZH: **WeChat QR** + YouTube.
10. **Footer** — address, map link, times, give, contact, language switch.

### 0.3 Components kept from A (restyle, don't copy)

| A section | Verdict | New home |
|---|---|---|
| Service board (3 cards + live state) | **Keep the logic**, kill the look | "This Sunday strip", per language |
| Announcements carousel | Keep, simplify to 3 cards, audience-tagged | Below "Coming up" or merge into it |
| Pastor's note | Keep, per language, shorter | Section 7 |
| Children & families (age cards) | **Keep**, strongest content in A | Section 6 + `/kids` |
| Upcoming events list | Keep | Section 8 |
| Recent sermons | Keep, filter by language, show 1 featured | Section 8 |
| 50th anniversary video | **Remove from home** | About page |
| "More photos" Facebook block | **Remove** | Replace with Section 9 |
| Footer newsletter | Keep, only if it actually sends somewhere | Footer |

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

**C4 — No conversion. "Plan a visit" goes to a page of prose.**
Where: all designs.
Fix: one primary action site-wide — **Plan your visit / 計劃來訪** — opening a
short form: name · which service · number and ages of kids · how to reach you
(email / phone / **WeChat ID** on `/zh`) · optional "what would help?". Promise
on submit: *"Someone will meet you at the front door and walk you in."* (Form
can post nowhere in the demo; label it `[FORM ENDPOINT]`.) Same button label
and colour everywhere; nothing else uses that colour.
Done when: the CTA appears in the nav, the hero, after "Your first Sunday", in
the footer and in the mobile sticky bar — and nowhere is there a competing
primary button.

**C5 — There are no phone designs.**
Where: every artboard is 1440 wide.
Why: first visits come from Google Maps, Instagram and WeChat links — on a
phone. A desktop-only review is reviewing the wrong product.
Fix: add a 390×844 (and full-length) artboard for `/`, `/en`, `/zh`. Mobile gets
a **sticky bottom bar**: *Plan your visit* · *Directions* · *Watch live* (Live
pill only when live). Hero on mobile must show headline + CTA + next-service
chip without scrolling.

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

**H3 — Simplified Chinese for Mandarin newcomers.** *(confirm with owner)*
If most new Chinese-speaking visitors are from mainland China, an all-Traditional
site signals "not really for you". Fix: 繁 / 简 switch on `/zh` (build-time
OpenCC conversion or client-side conversion of the same content — never two
hand-maintained copies). Default 繁; remember choice.

**H4 — Navigation is insider-speak.**
Where: About · Services · Newsletter · Offering (+ Chinese on each).
Fix:
- EN: **Visit · Sundays · Kids & Youth · Watch · About** · [Plan your visit] · `中文`
- ZH: **初次來訪 · 主日聚會 · 兒童及青少年 · 線上崇拜 · 關於我們** · [計劃來訪] · `EN | 繁 | 简`
"Give" moves to footer and About. "Newsletter" moves to footer.

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

**H7 — No social proof.**
Fix: "Real people" section: 2–3 cards, photo + first name + one sentence +
where they came from (e.g. `[Name], moved from [city] in [year]`). Separate
stories per language. Placeholder now; real quotes later.

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

### Nice to have

- **N1** Site-wide "● Live now" pill in the header during a live service, per
  language.
- **N2** Scroll-in fades (200 ms, 8px rise) and hover zoom on photo doors;
  disabled under reduced motion. No parallax, no count-up numbers.
- **N3** "Add to calendar" (.ics) on the next-service chip.
- **N4** 60-second "What a Sunday looks like" video on the visit page.
- **N5** Simple illustrated map: entrance, parking, kids' rooms.
- **N6** Seasonal hero swap (Easter, Christmas) via CMS.
- **N7** Prayer request form, per language.
- **N8** Analytics event on *Plan your visit* submit (Cloudflare Web Analytics)
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

Fixes: C1, C2, C3, C8, H4, H7, §3 copy.

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
| First Sunday CTA | Plan your visit — we'll meet you at the door |
| Stories heading | **Why people stay** |
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
| First Sunday CTA | 計劃來訪 — 我們會在門口等你 |
| Stories heading | **他們的故事** |
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
2. **`/` welcome + `/en` + `/zh` desktop and 390px artboards** (C1, C5, C7, C8,
   §0.2) with copy from §3.
3. **Plan-your-visit form + sticky mobile bar** (C4).
4. **Restyled components**: This Sunday strip (H5), doors, first-Sunday steps,
   kids (H6), stories (H7), pastor, coming up/latest message, connect (H8).
5. **Language switch + 简体** (H3, H4, H11).
6. Remove A's artboard from the toggle; keep it on the canvas only as
   "before".
7. Nice-to-haves (N1–N9).
8. Only after the owner picks the direction: port to the Astro repo with the
   content-model changes in H12.
