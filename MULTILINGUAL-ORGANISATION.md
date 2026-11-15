# One church site, three reading languages

Current scope: local design A. English, Traditional Chinese (`zh-Hant`) and Simplified Chinese (`zh-Hans`) are explicit choices. Cantonese, English and Mandarin congregations are a separate content dimension: changing reading language must not imply changing congregation.

Current implementation is V4 Atomic. All language-page and footer content is in native Atomic Flexbox, Heading, Paragraph, Button, Image, Tabs and Accordion elements. Four existing Fluent Forms shortcode placements remain classic; edit those forms in Fluent Forms. Atomic global variables control page font families and colours; legacy kit globals remain for compatibility. The native header/menu remains in Appearance › Menus.

## Consolidation rules

1. **One topic, linked language versions.** About (including the full Beliefs section) has three page versions; Give is one page holding all three languages (3 Nov 2026). Switching language retains the topic and section anchor. Do not build independent English/Chinese sites with unrelated menus.
2. **One source for shared facts.** Address, phone, service times/rooms, mission history, pastor identity and the PayPal destination should be maintained together. The build source and language mapping are kept with the Elementor exports; native WordPress pages store `_mpcbc_translation_group` and `_mpcbc_locale` metadata. This is an editable mockup workflow, not a new CMS translation plugin.
3. **Translate wording; preserve ministry differences.** Connect's visitor information, fellowships and English service details belong to the English congregation. Cantonese/Mandarin sermon collections retain their original language and series identity. They can be discovered from all three reading languages without implying translated sermons exist.
4. **Review belief statements before merging.** The supplied church-wide Chinese statement and the Connect English statement are not exact equivalents. The current English Beliefs prototype is a translation of the church-wide Chinese statement. The separately collected Connect statement remains source material for church review; it was not silently merged into that translation.
5. **Traditional is the supplied Chinese source.** Keep the full Traditional text and references. Generate a Simplified draft from that same source using OpenCC, then have the church check names, regional wording and references. Simplified conversion is not a new doctrinal statement.
6. **Avoid repeated paragraphs in a single language view.** The completed interior views present their selected language. The header's English / 繁體中文 / 简体中文 dropdown links the matching topic; no duplicate language tabs or buttons appear in the page content. Home and the shared header/footer retain their existing bilingual mockup treatment in this pass.

## Page map implemented

| Topic | English | Traditional | Simplified |
|---|---|---|---|
Since 15 Nov 2026 every page has three addresses: `/about/`, `/zh-hant/about/`, `/zh-hans/about/`. All three serve the **same** WordPress page; the MPCBC Translations plugin reads the prefix and the server sends only that language's text (`lang`, hreflang + `x-default`, canonical included). Links saved as full addresses get the prefix added in the page output. `?lang=` and the browser-stored choice are gone.

| Topic | English | Traditional | Simplified |
|---|---|---|---|
| Home | `/` | `/zh-hant/` | `/zh-hans/` |
| About (Chinese fields not filled yet; old copies 215/218 unreachable) | `/about/` | `/zh-hant/about/` | `/zh-hans/about/` |
| Give | `/give/` | `/zh-hant/give/` | `/zh-hans/give/` |

The existing `/zh/` is a Chinese topic directory. The header is the single language switcher and preserves the current topic/anchor. On mobile its native select sits outside the hamburger; its choices derive from the existing WordPress language menu, with the duplicate hidden inside the panel. New visitors default to English; an explicit selection persists. Welcome retains its language-entry buttons. All About children lead to anchors within the same language-specific About page. The old Beliefs URLs remain 301 redirects to About#beliefs.

Current Home editing model: **Home (21) is the single master** for all three choices: `/`, `/zh-hant/` and `/zh-hans/`. Its shared English/facts/layout appear once; each Chinese line is one native Atomic element written in Traditional. All three choices switch in place. The shared footer is **MPCBC – Footer (16)** on every page, with the same model. Since 3 Nov 2026 the **MPCBC Translations** plugin adds 繁體中文 / 简体中文 fields to every Heading, Paragraph and Button; the old paired `mpcbc-copy-zh-*` IDs are gone. See `HOME-FOOTER-EDITING.md`.

The previous independent Simplified Home (511) is now a published legacy redirect to `/?lang=zh-hans`; the old footer (513) is a draft archive. Their source wording was preserved before consolidation. Do not edit those as current content. Shared Fluent Forms keep their existing bilingual fields, edited in Fluent Forms.

On `/zh-hans/` pages the server converts all Traditional page, footer and menu text to Simplified (OpenCC 1.4.2 dictionaries in `plugins/mpcbc-translations/t2s-data.php`, built by `scripts/build-t2s-data.mjs`, checked by `scripts/check-t2s.mjs`); a 简体中文 field on an element overrides the conversion. In Appearance › Menus › Primary Menu, edit the Traditional Chinese **Description** and English **Navigation Label**. Simplified menu display is generated; language-option names remain in their native scripts. Keep the bundled runtime and license files in migration backups.

Primary menu groups visitor tasks: Sundays contains service times, Sunday worship and directions; Sermons contains the three original-language archives; Church life contains children/youth, groups, care/prayer, Bible reading and newsletter. About holds story, mission, team and beliefs anchors. Give and the single reading-language switcher remain top-level. Congregation service details stay under Sundays; sermon archive entries are explicitly labelled “sermons” to distinguish recorded teaching from service attendance. Reading language never changes a recording's spoken language.

## English material: recommended destinations

| Supplied English material | Destination | Shared or specific? |
|---|---|---|
| Church family introduction and short service information | Home summary + Sundays | Shared facts; Connect wording identified as its ministry voice |
| First-time visitor welcome, casual attire, expectations | Visit | General visitor guidance; distinguish any programme details by congregation |
| One Church, Three Ministries | Sundays | Shared service times and venues, translated from one fact set |
| Who We Are, What We Do, What We Care About, Connect logo | English congregation page `/congregations/english/` | Ministry-specific identity; do not relabel it as the entire church |
| Worship, community, spiritual growth, service | Home introduction + About mission | Consolidate repeated summaries rather than repeating full blocks |
| Adult Bible study and prayer | Community | Preserve the day/time conflict for confirmation, not two published schedules |
| College/Career, youth and English Sunday School | Community + Kids/Youth + Connect page | Each group has one record, linked wherever relevant |
| Bible in a Year | Next steps / resources | One resource with translated introductions and the original destination |
| Latest message and library | Sermons | Speaker, passage, source language and original video link; do not infer a date |
| Address, telephone and social links | Contact + shared footer | Shared contact facts; label English-specific social accounts accurately |
| Tithes/offering copy and PayPal | Give | One donation destination, translated instructions |

These additional English destinations are an information-architecture recommendation, not pages added by this pass. About, Beliefs, Give and the language indexes are implemented.

## Content model for future maintenance

- **Church fact:** one value (time, room, address, URL), reused in each language view.
- **Page topic:** one key (`about`, `beliefs`, `give`), with locale-specific text and matching page IDs.
- **Congregation:** Cantonese / English / Mandarin, independent of the visitor's language selection.
- **Group:** name, audience, congregation, schedule, location, contact and translated description.
- **Sermon:** original spoken language, title, speaker, passage, date if known, series and video URL; translated summaries optional.
- **Newsletter:** issue date, original PDF, available languages and translated introduction; avoid duplicate copies for the same issue.

For production, select a WordPress multilingual solution only after reviewing volunteer editing needs and the existing plugin restriction. It should manage translation relationships, localized menus/footers, canonical tags and language-aware search. No additional WordPress plugin was installed for this mockup. The child theme reads page locale/topic metadata to emit the correct document language and alternate-language links; it contains no translated page copy. Chinese paragraph language attributes and fonts are set in Elementor. The shared header/footer remain bilingual. Do not present the whole website as fully translated until Home, forms, remaining interior pages and shared UI have been reviewed.

## Editing and regeneration

The reading language is the address prefix. Internal links keep it; external links, downloads, mail and telephone links remain unchanged. Shared bilingual content is still bilingual: routing does not generate missing translations. The header label reflects the active reading language, with no duplicate page tabs.

The approved anchored About layout keeps `#story`, `#mission`, `#pastors` and `#beliefs`. Full supplied history/biography are in Atomic Accordions and the full statement of faith in Atomic Tabs. The current WordPress exports are authoritative. The earlier anchored About and language-variant scripts are historical V3 builders and must not overwrite these Atomic pages.

All visible page content is native Atomic elements, apart from the retained Fluent Forms embeds. English, Traditional and Simplified font families are maintained through Atomic global font variables; sizes and spacing are editable native style properties. No HTML layout widget or Pro Custom CSS field was added.

Beliefs editing: open the language-specific **About** page in Elementor, find `#beliefs`, and edit its eight Atomic Tabs labels or Heading/Paragraph elements inside each panel. Previous/Next labels are Atomic Buttons; retain their `#belief-topic-N` links. The phone selector derives labels from native tab triggers. Arrow keys navigate the desktop index. Keep stable element IDs `mpcbc-topic-reader` and `mpcbc-topic-index`; the navigation enhancement uses them. Native tabs remain available without the enhancement. The header switcher is the only language control.

The current Atomic exports are authoritative. All pre-Atomic merge, language and standalone builders/apply scripts are historical and would undo the new structure. `build-atomic-conversion.mjs` is a one-time migration from frozen pre-conversion snapshots, not a routine content-sync tool. Reconcile live editor changes before any scripted rebuild. Native MCP edits produce drafts; preserve page settings/templates when publishing them. About menu labels, Chinese descriptions and links remain editable in Appearance › Menus: overview parent, story, mission, pastoral team and beliefs anchors.

The source bilingual templates are preserved in `content-source/bilingual-template-*.json`; `scripts/build-language-variants.mjs` derives the language views using OpenCC 1.4.2, and `content-source/language-manifest.json` records the page map. Individual Elementor pages remain editable, but a regeneration can overwrite those edits: update the source templates or export/reconcile editor changes first. Source and WordPress exports are not automatically synchronized.

Pre-build exports, original menu and kit settings are in `content-source/before-interior-build/`. `scripts/apply-interior-content.php` and `scripts/apply-language-variants.php` are migration/build helpers, not background jobs. Do not rerun them casually after volunteer edits. The old `fill-verified-live-content.php` is historical and would restore earlier bilingual Home copy.

Church review remains necessary for English translations, Simplified conversion, scripture reference accuracy, current staffing and the contradictory adult Bible-study schedule. The original supplied references are preserved, including the unusual entries in the eternity section.
