# Design A: live-content audit and page plan

Reviewed 19 October 2026. Scope: `mpcbc.local` only. Design B and the retired Astro site were not changed.

Follow-up: [Chinese content placement and PayPal button destination](CHINESE-CONTENT-PLACEMENT.md). The homepage dropdown colour issue was fixed in the child theme and checked over the hero, after scrolling, and in the mobile menu. The proposed interior pages remain recommendations.

Implementation update: About, Beliefs, Give and the Chinese indexes have now been built with English / Traditional / Simplified language versions and one header switcher. See [current multilingual organisation and editing guide](MULTILINGUAL-ORGANISATION.md). Other proposed destinations below remain recommendations.

## Collection and evidence

The live website has two separate WordPress installations: [Chinese/main](https://mpcbc.net/) and [Connect Ministry](https://mpcbc.net/english/). Both were collected as source material for design A.

| Public source | Pages | Posts | Media records |
|---|---:|---:|---:|
| Main | 4 | 23 | 114 |
| English | 13 | 4 | 209 |
| Total | 17 | 27 | 323 |

The collection includes all published pages/posts returned by their public WordPress APIs, their rendered HTML, categories, 1,087 distinct discovered asset/link strings, and 29 downloaded PDFs (195 pages, including duplicate newsletter copies hosted in both installations). The English public events endpoint returned no events. There were no failed page or document downloads in the final collection.

Start with [readable source content](content-source/CONTENT.md), [structured content index](content-source/content-index.json), [media and link inventory](content-source/asset-links.json), [collection report](content-source/collection-report.json), and [document download report](content-source/documents/report.json). Each record retains its original URL, publication date and modification date. `*-pages.json` and `*-posts.json` preserve original rendered content. Matching `.html` files preserve frontend output; `.txt` files also capture text produced by frontend embeds that is absent from the REST content.

Collection limits: private/draft/deleted content is excluded. External YouTube videos were indexed by link, not downloaded or transcribed. Images and other media are catalogued with their public URLs, not all downloaded. PDF text extraction is available beside each PDF; image-only pages require visual review/OCR. Six wholly image-based resources (five colouring sheets and a missions banner) have no extractable text. Covers and some illustrations in the newsletters also trigger the extraction report's low-text flag. No claim is made that every pixel or spoken word has been transcribed.

## Changes applied to the local mockup

Existing Home (21), footer template (16) and the visit form were updated. All content remains in native Elementor widgets / Fluent Forms. No new plugin, theme content or atomic conversion was introduced.

- Corrected Cantonese to 11:00 AM and Mandarin to 9:00 AM in the worship card, congregation cards, footer and form choices. English and children's worship remain 11:00 AM.
- Replaced the placeholder address with the Alhambra address, added real directions, phone and email links, and filled the Chinese church name.
- Corrected the founding year from the mockup's 1976 to 1977.
- Replaced fake live/countdown labels with ordinary worship links and venue descriptions.
- Filled July 2026 newsletter title, issue date, summary, PDF link and reading link. Replaced "weekly, in all three languages" and "Also this week" with wording supported by the published material. January issue, Bible reading and fellowship links fill the sidebar.
- Filled Caleb Cheung / 張健庭 and Edward Lu from the public About page and 2026 newsletters. Left the third pastor and Edward's Chinese name as placeholders. The old About page's English pastor entry conflicts with newer material.
- Filled children's worship and youth Sunday School descriptions, College / Career Fellowship information, and real archive links.
- Replaced the invented giving-verse slot with the live site's cheque instructions and linked the giving page. No payment transaction was performed.

The live facts supersede conflicting copy in `HANDOFF.md` and the design preview (notably Monterey Park as the street location, worship times, founding year and newsletter frequency). The Harbor design, layout and global styling were retained.

Backups are in `content-source/before-content-fill/`. Fresh importable snapshots are `elementor/home-content-filled.json` and `elementor/footer-content-filled.json`. `fluent-forms/connect-content-filled-fields.json` contains the updated field definitions, not a complete Fluent Forms import package. Earlier mockup exports remain reference material; use the content-filled snapshots for this pass.

## Recommended information architecture

Keep the Harbor visual system and use a concise Home page as an introduction. Move full source material into readable interior pages rather than making Home longer.

| Destination | Layout and content | Live source / implementation decision |
|---|---|---|
| Home `/` | Welcome, worship times and venues, worship links, latest published newsletter, team introduction, community highlights, contact, giving | Current Home structure; replace remaining placeholders only when evidence is available |
| Sundays `/sundays/` | Three language cards with time and room; children's worship; street address and directions | [English Home](https://mpcbc.net/english/) and [main Home](https://mpcbc.net/) agree on times |
| Visit `/visit/` | What to expect, casual attire, Bible teaching, arrival address, service selector and contact form | English Home's visitor content. Keep parking, accessibility, check-in procedures as placeholders until supplied |
| English `/english/` | Connect Ministry introduction, worship at 11 in Fellowship Hall, mission, youth/adult classes, fellowship and sermon links | [Connect](https://mpcbc.net/english/), [About Connect](https://mpcbc.net/english/about-connect/) |
| Cantonese `/cantonese/` | Worship at 11 in Sanctuary, brief introduction, pastoral contact once approved, Cantonese archive | Main Home, About and Cantonese sermon category. Do not invent fellowship schedules |
| Mandarin `/mandarin/` | Worship at 9 in Sanctuary, brief history of ministry from 1988, Mandarin archive | English Home, main About and Mandarin category. Current pastor unknown |
| About `/about/` | Mission plus timeline: founded 1977; Mandarin 1988; English 1991; pastoral team | [About](https://mpcbc.net/about-us/) and 2026 newsletters. Use a timeline, then staff cards |
| Beliefs `/beliefs/` | Readable sections for God, Jesus, Spirit, Bible, humanity, salvation, security and eternity, with scripture references | Chinese About and [English beliefs](https://mpcbc.net/english/what-we-believe/). Preserve both statements separately for church review; they are not identical translations |
| Community `/community/` | Group cards by audience, day/time, place and contact; links to Bible reading and prayer | [Fellowship](https://mpcbc.net/english/fellowship-bible-study-and-prayer/). Highlight unresolved adult-study schedule |
| Kids & Youth `/kids/` | Children's worship at 11; youth Sunday School 10–10:45 in education annex; family-ministry introduction | Main Home, English Home, About Connect and January newsletter. Nursery age limits, screening and secure check-in claims require confirmation |
| Sermons `/sermons/` | Three congregation tabs with native cards; group Chinese material by series and preserve original titles and YouTube links | 18 Cantonese series, 5 Mandarin series, English sermon landing page and 2 English sermon posts. Start as static cards in Elementor free; do not introduce Pro loops or a new archive plugin |
| Newsletter `/newsletter/` | July 2026 feature card, January 2026 archive card; each issue has an introduction and PDF button | Two 2026 issues have duplicate landing pages/posts and duplicate hosted PDFs. Choose one local canonical page per issue |
| Next steps `/next-steps/` | Bible reading plan, fellowship introduction, learning resources | [Bible in a Year](https://mpcbc.net/english/bible-in-a-year/), Mandarin introductory series; no invented membership/baptism class dates |
| Care & Prayer `/prayer/` | Invitation, contact details and existing Fluent Form | Live Contact invitation plus local prayer mockup. Church must confirm who reads requests and privacy promises |
| Give `/give/` | Simple ways-to-give cards: cheque instructions, official online giving destination; optional approved stewardship copy | [Giving](https://mpcbc.net/donation/), [English giving](https://mpcbc.net/english/donations/), main Home. Link official destination and confirm details before launch |
| Contact `/contact/` | Address, map link, telephone, email, verified social channels and contact form | [Contact](https://mpcbc.net/contact/). Office hours and WeChat details stay placeholders |
| Resources / archive | Old bulletins, colouring sheets, 40-day Chinese reading resource, historic mission materials | Public media inventory. Show dated historical material as resources, not upcoming events |

Use English headings followed by separate Chinese widgets, global fonts/colours, native containers and generous readable line lengths. Church reviewers should check new translations. Sermon/book titles should retain their original Chinese spelling; translations are editorial work, not evidence of additional content.

## Content decisions and unresolved facts

| Issue | Evidence and treatment |
|---|---|
| Location | Church name still includes Monterey Park, but current public street address is **302 W. Commonwealth Ave., Alhambra, CA 91801**. Keep the official name; use Alhambra for directions |
| Worship times | Cantonese 11, English 11, Mandarin 9, children's worship 11. English worship is Fellowship Hall; Cantonese and Mandarin are Sanctuary |
| Founding year | Main About says 1977; July 2026 newsletter celebrates 49 years. Use 1977 |
| Pastors | Older About lists 張易安 for English; January 2026 newsletter explicitly identifies Ed Lu as lead pastor of Connect, and July includes Edward Lu's article. Use newer evidence for English; verify complete current staff roster and Chinese names |
| Mandarin pastor | No reliable current named assignment found. Keep placeholder rather than infer one pastor per congregation |
| Adult Bible study | Same source says Thursday 7:30–9 PM in its heading and Wednesday 7 PM on Zoom in its paragraph. Do not pick a time; show `[Day and time to be confirmed]` |
| Other schedules | Source lists youth and English Sunday School Sundays 10–10:45; College/Career Mondays 6:30–9 in West Covina. Mark these for routine church confirmation before launch |
| Newsletter | January and July 2026 are available. Weekly frequency and a complete three-language edition schedule are not established |
| Live indicator | Static mockup cannot establish live status or a countdown. Replaced visible claims with ordinary watch links |
| Children | Live site supports children's ministry and worship, but does not establish nursery ages, safety checks, matching tags or language support. Existing mockup claims need church verification |
| Prayer | Local privacy and pastoral-review promises are not established by the public site; confirm practice before launch |
| Current events | Public events API returned zero. Historic March 2021 mission/prayer material and colouring sheets are archive resources, not October 2026 events |
| Social media | Contact page provides church, Pastor Caleb and English YouTube channels and English Facebook. English frontend also links Instagram `@mpcbconnect`. Do not imply these belong to all three congregations |
| Filler | Exclude "Furniture expert", "Affordable Price", generic "best in town" text and lorem-ipsum-style bullets on the main homepage |
| Utility/legal pages | Keep Cookie Policy, My Account and Thank You in the source inventory. Do not migrate account/payment mechanics, submit donation forms or copy legal promises without reviewing the actual new site behaviour |

## Elementor MCP decision

Read-only inspection of local WordPress found Elementor **4.3.4**, Atomic Editor **active**, MCP exposure **off**. Existing Home has classic containers, Heading, Text Editor, Image, Button, Shortcode, Nested Tabs and Icon List widgets. Atomic activation has not converted them.

The [official MCP announcement](https://elementor.com/blog/elementor-mcp-launch/) documents the built-in Elementor MCP connection screen, application-password setup and atomic page generation. Basic/free features remain available without Pro. The [official roadmap](https://elementor.com/roadmap/) still describes broader V3 widget support as coming. Installed source includes a limited V3 bridge (not evidence that every classic widget can be edited). Therefore **do not convert the whole site just to enable MCP**.

Recommended sequence:

1. Keep the working Home/footer/form backups and maintain the current editable mockup.
2. Use Elementor's built-in MCP, avoiding a new third-party plugin. On `mpcbc.local`, open Elementor → Elementor MCP and review its generated Codex/manual connection instructions.
3. Establish a local client connection that can reach `mpcbc.local`; a remote/cloud connector cannot be assumed to resolve a Windows `.local` hostname. Do not expose the site publicly merely to connect it.
4. Keep application passwords out of Git, content exports, chat reports and screenshots. MCP is not enabled or connected by this pass; no new credentials were created.
5. Test on one **draft atomic** interior page, such as About or Sundays. Use only free elements, verify each tool's actual schema and retain the current Global Colours/Fonts as the design reference. Check how atomic variables map to the existing kit before applying styles.
6. Prove desktop/tablet/mobile layout, native editor changes and a safe rollback. Keep Fluent Forms, Fancy Text and existing Nested Tabs where the atomic/MCP equivalent is incomplete.
7. Convert other sections individually only if that test shows a useful benefit. Conversion requires rebuilding and verification; changing `widgetType` strings is not a migration.

No MCP connection was available among this chat's callable tools. Content changes were saved through the installed Elementor document API using WP-CLI, with read-back checks, not represented as MCP edits. Current Elementor has a 4.3.5 update available; this audit did not update plugins.

## Validation and remaining work

Content updates were verified in the rendered `mpcbc.local` Home, footer and service form options. Matching Elementor node IDs were checked and full source backups retained. Responsive observations and screenshots are in `screenshots/content-*` and `content-source/responsive-checks.json`.

This is a content audit and initial fill, not completion of the original Harbor acceptance checklist. Interior pages remain at their existing mockup stage; unused links, placeholder images, unsupported programme claims, translations and unknown details remain in the plan above. Forms were inspected but not submitted. No production site was modified and nothing was pushed to GitHub.
