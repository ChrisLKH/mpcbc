# Multilingual content validation

- Built 11 page variants: About, Beliefs and Give in English / Traditional / Simplified, plus two Chinese topic indexes. The existing `/zh/` directory links the topics as well.
- All 12 rendered destinations checked at 1440, 1280, 1110, 768 and 390px (60 observations): no horizontal document overflow and no in-page language buttons.
- Topic-preserving language links verified in the header. Browser click from Simplified About to Traditional About retained the About topic.
- Each beliefs page contains one native Nested Accordion with eight supplied statements. First and last items opened successfully; supplied references remained visible.
- Give page links retain the church's hosted PayPal button ID. No donation or form submission performed.
- Native-widget audit passed: no HTML layout widget or inline styles introduced. Global Colours matched the pre-build palette; the shared menu remained assigned.
- English / Traditional / Simplified document language attributes and matching alternate-language links were checked in rendered pages.
- Responsive screenshots are in `screenshots/en-*`, `hant-*`, `hans-*` and `language-header-only.jpg`. The final screenshots contain no duplicated in-page language tabs.
- PHP syntax check passed. Child-theme ZIP refreshed.
- Home and shared header/footer remain bilingual. Further English ministry pages, complete shared-UI localization and church approval of translations remain documented in `MULTILINGUAL-ORGANISATION.md`.

Reports: `locale-checks-about-beliefs.json`, `locale-checks-give-index.json`, `locale-content-validation.json`.
