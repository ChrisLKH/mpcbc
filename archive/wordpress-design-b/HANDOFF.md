# MPCBC WordPress mockup — build brief (Design B)

You are building a **front-page mockup** of the Monterey Park Chinese Baptist
Church website in WordPress (`wordpress-design-b/`), editable by non-technical volunteers using
**Elementor free**. Keep it simple: the least code and fewest plugins that
match the design reference `Homepage v5A (full-bleed, fading nav).dc.html`.

## Hard rules

- Work only inside `wordpress-design-b/`. Do not touch design A (`wordpress-design-a/`),
  the Astro site (`archive/astro-site/`), or `.env`. **Never push to GitHub**.
- Every visible word and image must be editable in Elementor (or Appearance ›
  Menus for navigation). Nothing hard-coded in PHP/CSS/JS.
- Only Elementor free native containers and widgets + the approved plugins listed below.
  No Elementor Pro, no Custom CSS fields (Pro), no HTML widgets or layout divs,
  and no inline `style=` attributes inside widgets (Text Editor holds plain paragraphs,
  bold/italic and links only).
- Search every page's data for `style=` and `<div`; both must be 0.
- Colours and fonts only from Elementor Global Colours / Global Fonts.
  If a font is not in Elementor's font list, tick Elementor › Settings ›
  "Disable Default Fonts" and load it in the child theme.
- Unknown facts stay as `[bracketed placeholders]` exactly as in the design.
  Invent nothing.
- Images: Use `images/logo.png` for the church logo. Use `church-exterior.jpg` and
  `church-sign.jpg` where a church photo fits; labelled placeholder images elsewhere.
- Background video: Uploaded looping MP4 with a small Pause/Play button (`video-toggle.js`), with a fallback image.
- Livestream, sermons, events, calendar are static mock content.
- Stop after 3 failed attempts at anything and ask rather than looping.

## Current task: spacing & header-gap fixes (review 2026-10-10)

The site is built. This pass fixes padding and gaps only — do not rebuild sections,
change copy, or touch anything not listed here. Measured on `http://mpcbc-2.local/`
at 1440px and 390px. Elementor element IDs below are the parent containers on the
Home page (post 23) and Footer template (post 21).

### Fix 1 — White gap above the header (highest priority)

**Cause:** Hello Elementor gives `.site-header` `padding-block: 1rem`, so the header
is 97px tall on desktop (1px border + 16 + 64 + 16) and 149px on mobile (logo and
hamburger wrap onto two rows). The hero only pulls up `-65px`, so 32px (desktop)
/ 84px (mobile) of white body shows behind the transparent header.

**Fix (child theme `style.css`, header CSS is allowed there):**
1. `.site-header { padding-block: 0 !important; }`. `.header-inner` already gives 64px.
2. On the home page, take the header out of flow so the hero never depends on the
   header's height:
   `body.home .site-header { position: fixed !important; top: var(--wp-admin--admin-bar--height, 0px) !important; left: 0; right: 0; }`
   and change the hero rule to `margin-top: 0 !important; padding-top: 65px !important;`.
   Inner pages keep `position: sticky`.
3. Mobile and tablet: logo and hamburger stay on **one row**:
   `.site-header .header-inner { flex-wrap: nowrap !important; }` at ≤1024px. Header
   height must be 65px at 1440, 768 and 390.
4. The hamburger icon is grey on the dark hero. Make it white at the top of the
   home page and `#221419` when scrolled (same rule as the nav links).

**Check:** at 1440/768/390 with scroll at 0, the first pixel row of the page is
the hero video. No white or grey band shows above or behind the header.

### Fix 2 — Remove the stacked 10px container padding

**Cause:** every nested container keeps Elementor's default 10px padding. It
stacks 2–4 times, so content edges sit 10–50px off the header's 1080px edge
and section gaps are bigger than designed. Mobile left/right content edges
currently differ by section: hero 26px, Sunday 20, Welcome 30, Connect 40,
Children 71, Giving 50, footer 30. The design is **20px** everywhere at 390px.

**Fix:** Elementor › Site Settings › Layout › **Container Padding = 0** (all sides,
all breakpoints). Then set padding explicitly only where the design has it (Fix 3
and Fix 4). Don't add padding back on inner/column containers.

**Check:** at 1440 every section's text left edge lines up with the logo's left
edge. At 390 every section's text starts at 20px,
except text inside cards, which starts at 20px plus the card padding.

### Fix 3 — Section top/bottom padding

All sections use a fixed 80px, plus stacked 10px containers, on every
breakpoint. Mobile is too loose and the Children → Events gap is doubled. Set
padding on each section's **parent container**. Use per-breakpoint values, from
the design's `clamp()`:

| Section (container ID) | Design | Desktop | Tablet | Mobile |
|---|---|---|---|---|
| Hero `cbacc1d6`, content box | `clamp(3rem,8vw,5rem)` / `clamp(2.5rem,6vw,4rem)` | 80 / 64 | 64 / 48 | 48 / 40 |
| Sunday `6d01e132` | `clamp(3rem,8vw,6rem)` | 96 / 96 | 64 / 64 | 48 / 48 |
| Announcements `673daa23` | `clamp(3rem,8vw,5.5rem)` | 88 / 88 | 64 / 64 | 48 / 48 |
| Welcome `0be3fb7f` | `clamp(3.5rem,9vw,6.5rem)` | 104 / 104 | 72 / 72 | 56 / 56 |
| Connect `9d189964` | `clamp(3.5rem,9vw,6rem)` | 96 / 96 | 72 / 72 | 56 / 56 |
| Children `28dc47fb` | `clamp(3.5rem,9vw,6.5rem)` | 104 / 104 | 72 / 72 | 56 / 56 |
| Events `f6320806` | **top 0**, bottom `clamp(3.5rem,9vw,6.5rem)` | **0** / 104 | **0** / 72 | **0** / 56 |
| Leaders `a52ab4d1` | `clamp(3.5rem,9vw,6rem)` | 96 / 96 | 72 / 72 | 56 / 56 |
| Giving `f5280123` | `clamp(3.5rem,9vw,6rem)` / `…6.5rem` | 96 / 104 | 72 / 72 | 56 / 56 |
| Footer `eb2faec4` | `clamp(2.5rem,6vw,4rem)` | 64 / 64 | 48 / 48 | 40 / 40 |

Values are top / bottom in px. Left/right padding on parents stays as it is now
(the 20px gutter). If the boxed inner `.e-con-inner` has its own 80px padding,
set that to 0 so the padding is defined only once.

Today the gap from Children to Events is about 230px. Events has no top
padding in the design, so after this fix the gap is just Children's bottom
padding.

### Fix 4 — Card inner padding (responsive)

| Card | Design | Desktop | Tablet | Mobile |
|---|---|---|---|---|
| Announcement card `bcc35c16` (and the other 2 slides) | `clamp(1.5rem,4vw,3rem)` | 48 | 32 | 24 |
| Children card `555a0381` | `clamp(1.5rem,4vw,3rem)` | 48 | 32 | 24 |
| Giving dark card `bff0e2cf` | `clamp(1.6rem,4vw,3.2rem)` | 51 | 32 | 26 |
| Connect form card | `clamp(1.25rem,3vw,2rem)` | 32 | 24 | 20 |

Today the announcement card has about 33px more space below its last line than
above its first line. Look for an empty widget, a spacer, or a leftover 10px
container at the bottom of each slide and remove it, so the top and bottom inner
space match.

### Fix 5 — Section anchors are missing

The menu links to `/#welcome`, `/#sunday`, `/#news`, `/#connect`, `/#leaders`,
`/#children` and `/#give`, but no element on the page has those IDs, so those
links do nothing. Set Advanced › **CSS ID** on each parent container:
`top`, `sunday`, `news`, `welcome`, `connect`, `children`, `events`, `leaders`,
`give`. Add `[id] { scroll-margin-top: 72px; }` to `style.css` so the fixed
header doesn't cover the section heading after a jump.

### Acceptance for this pass

- [ ] No white or grey gap above the hero at 1440 / 768 / 390, scrolled to 0. Header is 65px tall at all three widths.
- [ ] Section padding matches the Fix 3 table at each breakpoint, within ±4px. Measure from the section edge to the first and last widget.
- [ ] Mobile content left edge is 20px in every section, plus card padding inside cards.
- [ ] Children → Events gap equals Children's bottom padding (104 / 72 / 56).
- [ ] Every menu anchor scrolls to its section with the heading visible below the header.
- [ ] No horizontal scroll at 360px. `style=` and `<div` audit is still 0 (`scripts/audit-styles-divs.php`).
- [ ] Re-export the changed `elementor/*.json`, then retake `screenshots/` with `scripts/take-screenshots.js`.

## Next task: hero video + small Pause button (2026-10-10)

Do this after the spacing pass. Do only this: don't rebuild sections, change copy
or touch other pages. Check on `http://mpcbc-2.local/` at 1440, 768 and 390px.

### 1. Put the hero video in

The video is already made (Remotion project in `hero-video/`, see its README). The
renders are git-ignored, so use the local files:

- `hero-video/out/hero-1080.mp4` (1920×1080, 22 s, muted, loops seamlessly)
- `hero-video/out/hero-poster.jpg` (its first frame)

Upload both to the Media Library. In the Home hero container (`#top`) › Style ›
Background: type **Video**, Video Link = the MP4's Media Library URL, Play Once
**off**, Play On Mobile **on**, Background Fallback = `hero-poster.jpg`. Keep the
existing dark gradient overlay. Don't put any text in or over the video beyond
what's already there.

### 2. Add a small Pause/Play button (WCAG 2.2.2)

Add `hello-mpcbc-b/video-toggle.js` and enqueue it in `functions.php` the same
way as `nav.js`. It puts one button on every Elementor background video, so
nothing goes in Elementor. Its aria-label is the only hard-coded text, which is
allowed here because nobody sees it.

```js
/**
 * video-toggle.js - small Pause/Play button on Elementor background videos (WCAG 2.2.2).
 */
(function () {
  'use strict';
  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  document.querySelectorAll('.elementor-background-video-container').forEach(function (wrap) {
    var video = wrap.querySelector('video');
    if (!video) return;
    var userStarted = false;
    var btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'mpcbc-video-toggle';

    function sync() {
      btn.classList.toggle('is-paused', video.paused);
      btn.setAttribute('aria-label', video.paused ? 'Play background video' : 'Pause background video');
    }

    btn.addEventListener('click', function () {
      userStarted = true;
      if (video.paused) { video.play(); } else { video.pause(); }
    });
    video.addEventListener('play', function () {
      // Visitors who ask for reduced motion start paused until they press Play.
      if (reduceMotion && !userStarted) { video.pause(); }
      sync();
    });
    video.addEventListener('pause', sync);
    sync();
    wrap.parentElement.appendChild(btn);
  });
})();
```

Add to `hello-mpcbc-b/style.css`. The button is subtle: 44px tap target, 60%
opacity, full opacity on hover and focus. `body button.` beats Hello's and the
Kit's `button` styles, including their pink hover.

```css
/* Pause/Play button on background videos (video-toggle.js) */
body button.mpcbc-video-toggle {
  position: absolute;
  right: 24px;
  bottom: 24px;
  z-index: 5;
  width: 44px;
  height: 44px;
  display: grid;
  place-items: center;
  padding: 0;
  border: 1px solid rgba(255, 255, 255, 0.45);
  border-radius: 50%;
  background: rgba(27, 16, 20, 0.35);
  color: #fff;
  opacity: 0.6;
  cursor: pointer;
}
body button.mpcbc-video-toggle:hover,
body button.mpcbc-video-toggle:focus-visible {
  background: rgba(27, 16, 20, 0.55);
  color: #fff;
  opacity: 1;
}
body button.mpcbc-video-toggle:focus-visible {
  outline: 2px solid #fff;
  outline-offset: 2px;
}
/* Pause icon: two bars */
body button.mpcbc-video-toggle::before {
  content: "";
  box-sizing: border-box;
  width: 10px;
  height: 12px;
  border-left: 3px solid currentColor;
  border-right: 3px solid currentColor;
}
/* Play icon: triangle */
body button.mpcbc-video-toggle.is-paused::before {
  width: 0;
  height: 0;
  border-style: solid;
  border-width: 6px 0 6px 10px;
  border-color: transparent transparent transparent currentColor;
}
@media (max-width: 767px) {
  body button.mpcbc-video-toggle { right: 16px; bottom: 16px; }
}
```

The hero content is bottom-aligned. If the button touches the action row or the
service-times line at any width (most likely at 390), change only `bottom` /
`right`.

### Acceptance for this pass

- [ ] Hero video plays muted and loops at 1440 / 768 / 390, with no visible jump at the loop point. The header stays transparent over it with no gap (Fix 1 still holds).
- [ ] Poster image shows while the video loads, and stays if the MP4 is missing.
- [ ] Button is bottom-right, 44×44, clear of the hero text and buttons at all three widths.
- [ ] Tab reaches the button with a visible focus ring. Enter and Space toggle it, and its label switches between Pause and Play.
- [ ] Hover doesn't turn it pink or change its size.
- [ ] With reduced motion emulated (DevTools › Rendering), the video starts paused and Play starts it.
- [ ] No console errors. `style=` and `<div` audit is still 0. Re-export `elementor/home-hero.json`, then retake `screenshots/`.

## Next task: layout review fixes (review 2026-10-10)

Do this after the hero-video task. Fix only what's listed: don't rebuild sections or
change copy. Measured on `http://mpcbc-2.local/` at 1440×900 and 390×844. IDs are
Elementor element IDs on Home (post 23) and the Footer template (post 21). A new
colour palette may follow, so set colours from Global Colours. Don't type hex values
into widgets.

### Fix 1 — Hero fills the screen (highest priority)

Hero `cbacc1d6` (`#top`) has min-height `min(88vh, 820px)`. On any screen taller
than 820px, a white strip of the next section shows under it.

- Layout › Min Height: Desktop **100 vh**, Tablet **100 vh**, Mobile **Custom `100svh`**.
  Plain `100vh` on phones includes the address bar and overflows.
- Content stays bottom-aligned. Remove any other place that caps the height.

### Fix 2 — "Give" pill text goes dark after scrolling

In `hello-mpcbc-b/style.css`, the scrolled-header link rule
(`body.is-scrolled .site-header … > li > a { color: … !important }`, ~line 164)
beats the Give pill rule (~line 303). After scrolling, Give gets dark text on the
Primary pill, at a contrast of 2.5:1. Put `body ` in front of every Give selector in
the normal, `:hover` and `.menu-item-description` rules, e.g.
`body .site-header .site-navigation ul > li > a[href*="#give"]`. That gives them the
same specificity as the scrolled rule, and they win because they come later in the
file. Don't touch the scrolled rule itself.

### Fix 3 — Footer contrast (Footer template, post 21)

- The phone and email links (widget `45972a45` and its email sibling) are `#6F2841`
  on `#1B1014`, about 1.8:1. Make them white (Custom 5), underlined on hover.
- The newsletter sign-up field's label is dark on the dark footer, so only the red
  asterisk shows. Make the label light (white at ~80%). Keep the label; don't hide it.
- Footer logo: align left at all breakpoints, so it lines up with the address under it.

### Fix 4 — Connect card "Which service will you visit?" is empty

Fluent Form 3, field `service`, has no options. Add the placeholder
`Choose a service 請選擇`, then the options `Cantonese 粵語 · 9:30`,
`English · 11:00` and `Mandarin 國語 · 11:30`. These are the times already on the page.

### Fix 5 — Event rows: titles don't line up

Rows `e0514744`, `37c4677b`, `3aa92e48`, `dd935d02` and `e747a3d5` use
`justify-content: space-between`. With three children of different widths, the
title column starts at a different x on every row. In each row:

- Justify Content **Start**, Gap **24px**
- Date container (1st child, e.g. `2a746687`): Width **140px**, no grow and no shrink
- Title container (2nd child, e.g. `cab86ace`): **Grow**
- Time container (3rd child, e.g. `31622968`): no shrink, text right-aligned
- Mobile keeps its current stacked layout.

### Fix 6 — Announcement label wraps

`NEWSLETTER · SEPTEMBER 2026` (`b52e3e4d`) wraps onto two lines because the
slider dots and arrows (`aa8b6384`) sit right beside it. On desktop and tablet, put the
label and the controls in one row with Justify **Space Between**: label left, on
one line, and controls right. Mobile can stay as it is.

### Fix 7 — Alt text

Every content image has `alt=""`. Set alt text in the Media Library:

- Pastor note photo `5a937e3c` and the leaders `213f4e26`, `8f452a35`, `70a07c7a`:
  the name shown under each photo, e.g. "Rev. Peter Chan, Senior Pastor".
- Kids `728eaed9`, `725d348e`, `dd886840`: what the placeholder shows
  ("Sunday school room", "Craft table", "Nursery corner").
- The Sunday video thumbnails `db82c894`, `b0659fdc`, `612a6f7c` are decorative.
  Leave them empty.

### Fix 8 — Shorter phone page (mobile breakpoint only)

The 390px page is 13,937px tall. Aim for under 10,500px. Change only the Mobile
breakpoint:

- **Pastors:** each card becomes a row: photo **112px wide** (4:5, Object Fit
  Cover) on the left, kicker, name and bio on the right, gap 16px. It's currently a
  435px-tall portrait stacked over the text, three times.
- **Children:** put the craft (`725d348e`) and nursery (`dd886840`) photos side by
  side (parent direction Row, each 50%, gap 12px). The wide room photo stays on
  top.
- **This Sunday:** hide the Cantonese and Mandarin thumbnails (`db82c894`,
  `612a6f7c`) on mobile (Advanced › Responsive). Keep the live card's
  thumbnail (`b0659fdc`).

### Fix 9 — Footer background stops at 1140px on wide screens

`hello-mpcbc-b/footer.php` wraps the Elementor footer (post 21, root `eb2faec4`) in
`<footer id="colophon" class="site-footer">`. Hello Elementor caps `.site-footer` at
`max-width: 1140px`, so on screens wider than about 1200px the dark footer is a
centred box with white on both sides. Measured at 1920px: footer 1140px wide,
starting at x = 382.

Keep the `<footer>` element, because it's the page's footer landmark for screen
readers. Add one rule to `hello-mpcbc-b/style.css`:

```css
/* Elementor footer is full-bleed; Hello caps .site-footer at 1140px */
body #colophon.site-footer {
  max-width: none;
  width: 100%;
  margin: 0;
  padding: 0;
}
```

The footer's content stays in its own container (`--content-width` 1140px), so
only the dark background goes full width. Don't change `footer.php` and don't touch
Design A.

### Acceptance for this pass

- [ ] Scrolled to 0, the hero exactly fills the screen at 1920×1080, 1440×900, 768×1024 and 390×844. No white below it.
- [ ] Give pill text is white at the top of the page, after scrolling, and on hover.
- [ ] Footer phone, email and newsletter label are readable, with contrast ≥ 4.5:1. Footer logo is left-aligned.
- [ ] The Connect card dropdown lists the three services after its placeholder.
- [ ] Event titles start at the same x on all five rows at 1440 and 768.
- [ ] The announcement label is on one line at 1440 and 768.
- [ ] Every non-decorative image has alt text.
- [ ] The 390px page is under 10,500px tall. No horizontal scroll at 360px.
- [ ] At 1920 and 2560px, the dark footer runs edge to edge, with no white beside or under it. Its content stays aligned with the sections above.
- [ ] `style=` and `<div` audit is still 0. Re-export the changed `elementor/*.json`, then retake `screenshots/`.

## Design source

The design source is:
`wordpress-design-b/design-reference/Homepage v5A (full-bleed, fading nav).dc.html`
(along with `support.js`, `image-slot.js`, and `images/`).

Read exact sizes, spacing, colours, fonts, and copy directly from the markup.

## Stack

| Part | Choice |
|---|---|
| Local env | **Local** (by WP Engine), site "mpcbc-2", `http://mpcbc-2.local`, PHP 8.2.29, WordPress 6.x |
| Parent theme | Hello Elementor (unchanged) |
| Child theme | `wordpress-design-b/hello-mpcbc-b/` linked via junction to `C:\Users\chris\Local Sites\mpcbc-2\app\public\wp-content\themes\hello-mpcbc-b` |
| Plugins | Elementor (free), Essential Addons for Elementor (free — Fancy Text only if needed), Fluent Forms (free), Duplicator (free — for migration package) |
| DB / Admin | User: `mpcbc` / Password: `mpcbc` |

### Child theme `hello-mpcbc-b` (~50 lines total)

```
hello-mpcbc-b/
├─ style.css      Theme header, CSS variables from Elementor globals,
│                 fading nav styles (sticky, transparent at top, frosted white on scroll),
│                 two-line menu items, "Give" button pill style, responsive tweaks.
├─ functions.php  Enqueue parent style, Google Fonts (Geist, Noto Sans TC, Noto Serif TC,
│                 Inter, Nanum Pen Script), child style, nav.js and video-toggle.js;
│                 show each menu item's Description as a second line (Chinese).
├─ nav.js         Lightweight scroll listener (<15 lines): toggles .is-scrolled on
│                 header when window.scrollY >= 40.
├─ video-toggle.js  Small Pause/Play button on every Elementor background video.
└─ footer.php     Renders the Elementor template titled "MPCBC – Footer" on every page via
                  Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $id )
                  then wp_footer().
```

### Header behaviour ("Fading Nav")

- Built on **Hello Elementor's built-in header** (Site Settings › Hello Header & Footer).
- Header is `position: sticky; top: 0; z-index: 999; width: 100%; transition: background 0.25s ease, border-color 0.25s ease, color 0.25s ease;`
- **At top of page (`scrollY < 40`)**:
  - Background: `transparent`
  - Border-bottom: `1px solid rgba(255, 255, 255, 0.25)`
  - Text / Nav links: `#FFFFFF`
  - Logo filter: `brightness(0) invert(1)` (white silhouette over dark hero video)
- **Scrolled down (`scrollY >= 40`)**:
  - Background: `rgba(255, 255, 255, 0.92)`
  - Backdrop filter: `blur(8px)`
  - Border-bottom: `1px solid #E3D3D8`
  - Text / Nav links: `#221419`
  - Logo filter: `none` (natural logo colours)
- **Give CTA button**: Pill button (`border-radius: 999px`), background `#943759` (Primary), text `#FFFFFF`, height 40px (desktop) / 44px (mobile).
- **Hero overlap**: On Home the header is `position: fixed` with `padding-block: 0`, so it is 65px tall. The hero has `padding-top: 65px` and no negative margin, so the full-bleed video sits underneath the transparent header. See Fix 1.

## Pages (2)

| Page | Layout | Content |
|---|---|---|
| Home `/` (front page) | Elementor Full Width | All 9 homepage sections + footer |
| Coming soon `/coming-soon/` | Elementor Full Width | Bilingual placeholder for links not yet built |

Settings › Reading: static front page = Home. Tick "Discourage search engines".
Site Settings › Layout › Default Page Layout = Elementor Full Width.

## Home sections (top to bottom)

1. **Hero (`#top`)**
   - Full-bleed container, min-height 100vh (mobile `100svh`), so it always fills the first screen.
   - Background = uploaded MP4 video (`hero-video/out/hero-1080.mp4`), looping, muted, small Pause/Play button, with fallback poster image (`hero-video/out/hero-poster.jpg`).
   - Dark gradient overlay: `linear-gradient(to top, rgba(27,16,20,0.86) 0%, rgba(27,16,20,0.2) 62%, rgba(27,16,20,0.4) 100%)`.
   - Content bottom-aligned, max-width 1080px container:
     - H1: "One church.<br />Short Slogan." (white, weight 700, letter-spacing -3.2px, line-height 1.02).
     - Subtitle: "一間教會，三種語言" (white, Noto Serif TC, weight 600, font-size ~1.4rem).
     - Action row:
       - Pill button 1: "Join us this Sunday →" (white bg, `#221419` text, radius 999px, height 52px).
       - Pill button 2: "Tell us you're coming" (transparent bg, white 1px border, white text, radius 999px, height 52px).
       - Service times text: "粵語 9:30 · ENGLISH 11:00 · 國語 11:30" (Inter 13px bold, letter-spacing 0.06em, white 88%).

2. **Sunday Services (`#sunday`)**
   - Padding clamp(3rem, 8vw, 6rem) top and bottom.
   - Section header: Kicker "This Sunday 本主日" (uppercase 13px Inter bold with `#943759` rule), H2 "Three congregations, one morning.", subtext "Each congregation streams on its own channel. Live links update automatically from YouTube — nobody edits this weekly."
   - 3 service cards (3 columns on desktop, 1 column on mobile):
     - **Cantonese (9:30 AM)**: 16:9 placeholder ("youtube · scheduled / 粵語頻道"), H3 "粵語主日崇拜" (Noto Serif TC 20px), kicker "Cantonese · 9:30 AM" (#943759), description, outline pill buttons "Set a reminder" and "Archive 存檔".
     - **English (11:00 AM) — Active/Live highlighted card**: Highlight border `#943759`, bg `#F8F3F4`, 16:9 placeholder with pulsing live dot + "youtube · live now / English channel", H3 "English Worship", kicker "Live now · 11:00 AM" (#C2410C), description, buttons: "Watch now" (primary #943759) and "Archive" (outline).
     - **Mandarin (11:30 AM)**: 16:9 placeholder ("youtube · last recording / 國語頻道"), H3 "國語主日崇拜" (Noto Serif TC 20px), kicker "Mandarin · 11:30 AM" (#943759), description, outline pill buttons "Go to channel" and "Archive 存檔".
   - Bottom row: "Watching from home?..." subtext + outline buttons "All sermons" and "Our YouTube channels".

3. **Announcements (`#news`)**
   - Background `#F8F3F4`, border-block `1px solid #E3D3D8`.
   - Kicker "Announcements 最新消息".
   - White card (`#FFFFFF`, border `1px solid #E3D3D8`, radius 24px, padding 2rem–3rem):
     - Top row: Kicker "Newsletter · September 2026", 3 pagination dots, navigation arrows (← →).
     - 2 columns:
       - Left column: H2 "Read the Bible together in 365 days.", Chinese subtitle "一年讀完聖經 · 全教會一同開始", body paragraph, primary pill CTA "Join the reading plan →".
       - Right column: Meta schedule rows (Starts: Sunday, October 4 / Pace: About 15 minutes a day / Languages: 粵語 · 國語 · English), handwritten note "day one is Genesis 1 — catch up anytime" in Nanum Pen Script (24px, #943759).

4. **Welcome / Pastor Note (`#welcome`)**
   - 2 columns:
     - Left column: Pastor portrait in circular frame (`radius 999px`, placeholder `pastor-portrait.png`), "Rev. Chan 陳牧師", "Senior Pastor".
     - Right column: Kicker "A note from our pastor 牧者的話" (with #943759 accent line), H2 "You are welcome here, whichever language you pray in.", English story paragraph, Chinese paragraph "若您初次來訪，請放心前來，隨意入座。我們不會請您站起來自我介紹。門口的同工會為您指引兒童教室與後方停車場。", outline CTA button "Read our welcome page →".

5. **Connect & Prayer (`#connect`)**
   - Dark background `#1B1014`, text `#FFFFFF`.
   - 2 columns:
     - Left column: Kicker "Say hello 與我們聯絡", H2 "We would rather hear from you than guess.", description in English and Chinese.
     - Right column: White card (radius 24px, text `#221419`) hosting the Fluent Forms form:
       - Connect card fields: Name, Email/Phone, Congregation choice (Cantonese 9:30 / English 11:00 / Mandarin 11:30 / Not sure yet), Optional note, submit button.
       - Prayer request fields: Name (or anonymous), Request textarea, Private checkbox ("Keep this between me and the pastors..."), submit button.

6. **Children & Families (`#children`)**
   - Light container card (`#F8F3F4`, border `#E3D3D8`, radius 24px).
   - Top area:
     - Left: H2 "Children & families", Chinese subtitle "兒童及家庭事工", 2 descriptive paragraphs, primary pill button "Children's ministry 兒童事工 →".
     - Right: 3-photo grid (1 wide 16:9 on top, 2 square photos below) using placeholder photos.
   - Bottom area: 3 ministry breakdown columns:
     - Nursery · 0–3 ("Open 9:15 AM – 12:45 PM, hallway by the courtyard.")
     - Sunday school · 4–11 ("During the 11:00 and 11:30 services, two rooms upstairs.")
     - Youth · 12–18 ("Friday evenings in the Fellowship Hall, with supper.")

7. **Coming Up / Events (`#events`)**
   - Kicker "Coming up 近期活動", H2 "Gatherings and small groups", buttons "Open the church calendar", "Subscribe in your app", subtext "Pulled live from the church Google Calendar. Add or change an event there and it appears here."
   - 5 event rows:
     1. Sep 4 | Friday fellowship 週五團契 | Singing, teaching, and supper. All ages welcome. | 7:30 PM · Fellowship Hall
     2. Sep 6 | Church picnic 教會野餐 | Barnes Park after the 11:30 service. Bring a dish to share. | 1:00 PM · Barnes Park
     3. Sep 12 | Small groups begin 小組開始 | Six groups across three languages, meeting in homes. | Weekly · Various
     4. Sep 20 | Children's teacher training 兒童教師訓練 | Safeguarding refresher for all Sunday school volunteers. | 9:00 AM · Room 2
     5. Oct 18 | 50th Anniversary celebration | A combined trilingual service marking fifty years, followed by lunch. | 10:00 AM · Sanctuary

8. **Pastors & Leaders (`#leaders`)**
   - Background `#F8F3F4`, border-block `1px solid #E3D3D8`.
   - Kicker "Our pastors 教牧同工", H2 "Know who you are walking in to meet.", subtext.
   - 3 pastor cards (4:5 aspect ratio photo, role kicker, Chinese name, English name, bio):
     - **Cantonese**: Rev. Peter Chan / 陳志明 牧師 ("Senior pastor · Cantonese")
     - **English**: Rev. David Lam / 林大偉 牧師 ("Pastor · English")
     - **Mandarin**: Rev. Grace Wang / 王慧珍 牧師 ("Pastor · Mandarin")
   - Bottom note: "Deacons, ministry staff, and the church office are listed on the full staff page."

9. **Giving (`#give`)**
   - Dark card container `#1B1014`, text `#FFFFFF`, radius 24px.
   - Left column: Kicker "Giving 奉獻", H2 "Giving here is worship, not a subscription.", English & Chinese body copy.
   - Right column: 3 pill CTA buttons: "Give online →", "Set up monthly giving →", "Mail a check or give by mail →", plus 501(c)(3) tax receipt note.

10. **Footer (`<footer>`)**
    - Built as Elementor template titled "MPCBC – Footer" (rendered via `footer.php`).
    - Dark background `#1B1014`, text `#FFFFFF`.
    - 3 columns:
      - Column 1: Logo `images/logo.png`, address "000 Garvey Avenue, Monterey Park, CA 91754", outline button "Get directions 交通指引 ↗", parking note, phone/email links, social pills (Facebook, YouTube, Instagram).
      - Column 2: Sunday services schedule (9:30 AM Cantonese, 11:00 AM English, 11:30 AM Mandarin), office hours.
      - Column 3: Weekly email / Newsletter signup heading, subtext, and Fluent Form email subscription.
    - Bottom bar: "© 2026 Monterey Park Chinese Baptist Church 蒙迪園華人浸信會" and YouTube disclaimer.

## Design system

### Global Colours

| Slot | Colour | Usage |
|---|---|---|
| Primary | `#943759` | Primary brand plum, main CTA buttons, kicker lines, active borders |
| Secondary | `#1B1014` | Deep plum-black, hero background, connect dark section, footer background |
| Text | `#221419` | Deep espresso dark text |
| Accent | `#C2410C` | Live now highlight orange / pulse dot (with `#E8836B` subtitle) |
| Custom 1 | `#6F2841` | Primary hover / deep plum |
| Custom 2 | `#6B5560` | Muted plum-grey body text and secondary labels |
| Custom 3 | `#F8F3F4` | Light blush card background, subtle section bands |
| Custom 4 | `#E3D3D8` | Subtle card & separator border |
| Custom 5 | `#FFFFFF` | Clean white cards and button backgrounds |

The child theme CSS must read `var(--e-global-color-primary)` etc. so editing Site Settings updates the header, buttons, and badges uniformly.

### Typography & Global Fonts

| Role | Font Family | Weights | Usage |
|---|---|---|---|
| Primary / Body | Geist | 400, 500, 600, 700 | Body copy, navigation links, descriptions |
| Secondary / Headings | Geist | 600, 700 | H1, H2, H3 English titles (letter-spacing -1.4px to -3.2px) |
| Chinese Headings / Callouts | Noto Serif TC | 600 | Chinese titles and pastoral notes |
| Chinese Body | Noto Sans TC | 400, 500, 700 | Chinese navigation items, subtitles |
| Kickers / Meta | Inter | 600, 700 | Uppercase kicker tags, times, date badges, meta labels |
| Handwritten Note | Nanum Pen Script | 400 (24px) | Script callout notes ("day one is Genesis 1...") |

Disable Default Fonts in Elementor settings; child theme enqueues Google Fonts and sets variables.

### Theme Style

- **Buttons**:
  - Pill shape: border-radius `999px` (all buttons in Design B are pill-shaped).
  - Primary button: Background `#943759`, text `#FFFFFF`, hover `#6F2841`.
  - Secondary/Outline button: Border `1px solid #E3D3D8` (or `1px solid rgba(255,255,255,0.35)` on dark), text `#221419` (or `#FFFFFF` on dark).
  - Heights: 48–56px desktop, 44–48px mobile.
  - Font weight: 600.
- **Cards**: Border-radius `24px` for large cards, `16px` for inner media/thumbnails.
- **Form fields**: Radius `10px`, border `1px solid #E3D3D8`, height 46px.

## Responsive rules

- Elementor breakpoints: Mobile ≤767px, Tablet 768px–1024px, Desktop ≥1025px.
- Container max width: `1080px`, horizontal padding `clamp(1rem, 4vw, 1.5rem)` (`100% - 2.5rem`).
- Breakpoints screenshot verification: 1440px (desktop), 768px (tablet), 390px (mobile).
- No sideways overflow or horizontal scrolling at 360px width.
- Tap targets minimum 44px on touch devices.

## Acceptance checks

- [ ] Each section matches `Homepage v5A (full-bleed, fading nav).dc.html` at 1440px, 768px, and 390px.
- [ ] No overflow or clipping at 360px; mobile drawer menu works smoothly.
- [ ] Header transparent with white text/inverted logo at top; fades to frosted white `#FFFFFF` with dark text and border on scroll.
- [ ] Changing Primary colour once restyles buttons, kickers, active cards, and header CTA.
- [ ] Volunteer test in Elementor editor UI passes (change headline, photo, menu item, Primary colour without code).
- [ ] All pages and footer search for `style=` and `<div`: **both must be 0**.
- [ ] Duplicator package generated cleanly.

## Questions about the design before building

1. **Internal Page Navigation vs Anchor Links**:
   In `Homepage v5A`, menu items jump to in-page anchors (`#welcome`, `#leaders`, `#sunday`, `#children`, `#news`, `#connect`, `#give`). The brief mentions: "Pages: Home (front page) plus a /coming-soon/ placeholder for unbuilt links."
   Should the main navigation links point to homepage anchors (e.g. `#welcome`, `#sunday`, `#connect`), or to `/coming-soon/` (with anchor jumps on the homepage), or both?
   *(Recommendation: Keep anchor links `#welcome`, `#sunday`, etc. on Home for seamless single-page navigation matching v5A, while general unbuilt external links such as full sermon archives, YouTube channel, and staff page link to `/coming-soon/`).*

2. **Announcements Switcher (Section 3)**:
   In the HTML mockup, 3 announcements rotate with dots and prev/next buttons. In Elementor Free, we want non-technical volunteers to easily edit announcements without complex JavaScript.
   Should we render Announcement #1 ("Read the Bible together in 365 days") with static dot/arrow indicators matching the design board, so volunteers can directly click and edit all text, dates, and buttons in native Elementor widgets?
   *(Recommendation: Yes, build Announcement #1 natively in Elementor free containers/widgets with dot indicators so it's 100% volunteer-editable and free of `<div` / inline styles).*

3. **Connect & Prayer Card (Section 5)**:
   The design mockup has a two-tab form ("Connect card" and "Prayer request"). In Fluent Forms free, would you prefer:
   - (A) A clean Fluent Form with a radio button / dropdown to choose "Connect card" or "Prayer request", showing the appropriate fields dynamically; or
   - (B) A tabbed interface hosting two Fluent Forms?
   *(Recommendation: Option A or lightweight Fluent Form tabs that keep the markup 100% compliant).*

4. **Background Video**:
   For the hero looping video, shall we supply a lightweight sample MP4 video uploaded to WordPress Media Library, with `church-exterior.jpg` set as the fallback poster?
   *(Recommendation: Yes, uploaded MP4 video in WordPress Media Library with looping enabled, muted, and church exterior fallback).*
