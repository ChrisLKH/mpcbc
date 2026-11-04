# Chinese content placement

Design A recommendation, 19 October 2026. This is a page/content proposal; these interior pages have not been rebuilt by the menu-colour fix.

Implementation update: About, Beliefs and Give are now built in English, Traditional Chinese and Simplified Chinese. The approved language architecture supersedes the earlier bilingual-paragraph proposal below. Use the single header language dropdown; no page-level language tabs. See [current multilingual organisation](MULTILINGUAL-ORGANISATION.md) for routes, editing notes and remaining scope.

| Supplied Chinese content | Full-content destination | Home-page use |
|---|---|---|
| 教會介紹 | About `/about/#story`: church introduction followed by a 1977 / 1988 / 1991 timeline and the complete supplied history | A short church introduction and "Our story / 教會介紹" link |
| 使命宣言 | About `/about/#mission`: an important, spacious bilingual statement above the timeline | Include the short Chinese mission under its English translation near the welcome/about introduction |
| 教牧團隊・張健庭博士 | About `/about/#pastors`: portrait, full name, 主任牧師及粵語堂牧師, complete supplied biography and contact link | Existing pastor card: name, role and a short introduction; "Meet our team / 教牧團隊" links to full profiles |
| 我們的信仰告白 | Beliefs `/beliefs/`: eight native Elementor accordion items: 神、耶穌基督、聖靈、聖經、人類、救恩、永遠的保障、永恆 | A "What we believe / 我們的信仰" link from About; no long doctrinal text on Home |
| 奉獻 | Give `/give/`: supplied introduction, mailed-cheque instructions and a "Give with PayPal / 透過 PayPal 奉獻" button | Keep the compact giving strip; primary action opens the church's PayPal donation destination, secondary "Ways to give / 奉獻方式" opens the local Give page |

Use English headings with Chinese underneath, consistent with the existing brief. For long passages, use separate English and Chinese text widgets, both at normal readable body size (Chinese around 18px with generous line spacing); do not shrink entire Chinese paragraphs to the small heading-subtitle size. On mobile, stack the two languages rather than placing full paragraphs in side-by-side narrow columns. Church reviewers should approve English translations of the supplied Chinese statements; the existing English ministry beliefs page is not an exact translation.

Keep scripture references immediately below each belief paragraph. Preserve the supplied wording and references for review; do not silently correct or reinterpret them. The supplied eternity reference list in particular should be checked by the church before publication. "Pastor-Caleb" appears to be an image/file label and should not become visible biography text.

The 中文 navigation item should open a Chinese content index at `/zh/`, with direct links to the Chinese sections of About, Beliefs and Give, plus services, sermons and contact. This gives Chinese readers a clear route without duplicating entire sets of pages during the initial mockup.

## PayPal destination

The linked `https://www.paypal.com/en_US/i/scr/pixel.gif` is a 1×1 tracking image, not a donation URL. The saved source of the live giving page contains a PayPal form with `action="https://www.paypal.com/donate"` and hosted button ID `PQJTK3MEFJ9M2`. Its equivalent button destination is:

`https://www.paypal.com/donate/?hosted_button_id=PQJTK3MEFJ9M2`

This was extracted from the church's public giving form; no donation was initiated. Use a native Elementor Button with this destination when implementing Give. No PayPal HTML widget, tracking image or additional plugin is needed. This recommendation does not change the current giving buttons or create the proposed pages.

## Navigation colour fix

The transparent-home header's white English and Chinese styles previously selected submenu descendants as well as top-level links. Both transparent and scrolled overrides now target only direct top-level links; dropdowns retain dark Ink text and Ink-soft Chinese labels against their white panel. Focused submenu Chinese labels use Harbor along with the English label. The child stylesheet uses its file modification time for cache invalidation.
