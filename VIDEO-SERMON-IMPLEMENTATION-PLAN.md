# MPCBC video and sermon implementation plan

Proposed 21 October 2026; updated 1 November 2026 with the title review and Home slip integration (sections 2, 4 and 8). Planning only: no plugin installed, cron enabled, or live page changed. Scope is design A / the restored mpcbc site. Do not change design B or the archived Astro site.

## Outcome

One free custom plugin, provisionally `mpcbc-sermons`, owns the homepage live cards and the sermon archive together (no second plugin), plus video sources, sync, ministry assignments, sermon records and search. Hosting cron runs its background jobs. Elementor owns the surrounding page design. Weekly editors use a WordPress Sermons dashboard, without editing individual Elementor video URLs.

No paid WordPress plugin, Elementor Pro feature, external automation subscription, AI translation service or external search service is required in the proposed scope. Existing hosting must support a scheduler; its minimum interval affects freshness. A Google project with the YouTube Data API enabled and an API key is needed for public metadata. The WordPress MCP application password is not a YouTube credential and must never be placed in this plugin or repository.

## 1. Content and administration

Register a dedicated sermon post type and taxonomies for spoken ministry, speaker, series and Bible book. Keep one record per YouTube video ID. Two recordings of the same sermon in different ministries remain distinct videos. Do not create three duplicate sermon posts merely to hold reading-language variants.

Shared fields: YouTube video/channel ID, ministry, verified service date, scheduled/actual broadcast times, duration, media type (full service / sermon / other), speaker, canonical book and scripture range, series, thumbnail, optional verified sermon-start timestamp, attachments and source URLs.

Separate editor fields: title, summary and notes in English, Traditional Chinese and Simplified Chinese. Imported source text remains separate from editorial overrides; re-sync never regenerates approved wording. Missing wording falls back to the original title with its spoken-language label rather than inventing a translation. No automatic transcript generation or transcript search in phase one.

Keep two separate states:

- Source availability: upcoming / live / ended-processing / playable recording / unavailable / status unknown.
- Editorial workflow: needs classification / needs review / published / hidden or archived.

Only safely classified public services may appear in the live/recording viewer using public YouTube metadata. Unpublished editorial fields and unfinished translations never appear in public search or responses. A completed service can be watched before its curated sermon entry is published. Its link opens the recording viewer until an approved archive detail page exists.

Admin navigation:

- Sermons: Needs review, Published, Hidden; search and bulk assignment.
- Live services: three ministry rows showing next/current/latest video, last check, source and manual override.
- Sources & Sync: two channels, optional ministry playlists, title rules, API key, schedule, health and logs. Administrator access only for credentials and system settings.

Editors get dedicated content capabilities and access to review/publish permitted sermons, assign ministry, set a video override and request a rate-limited Sync now. They do not need manage_options. No public write endpoint or exposed cron secret is needed.

## 2. Sources and ministry classification

Verified sources from the earlier live-site review:

- Shared Chinese channel: `UCnxu_A53vGhcNohchKzZ7hw` / <https://www.youtube.com/@MPCBC>.
- English: `UCJx8Xl0QqiNkZwwfLXaASHQ` / <https://www.youtube.com/@englishmpcbc8168>. Current services are on its Live tab; its Videos tab stops at 2021.

### Title format (reviewed 1 November 2026)

The last ~30 Chinese and 12 English livestreams (July–November 2026) all follow one pattern:

`[Series (n):] "Title"  Scripture ~ Speaker  [congregation marker + date]`

```
希伯來書系列 (十二): "因信而前行(一)"  希伯來書 11:1-22  ~  張健庭牧師博士  慕道園華人浸信會 (MPCBC) 粵語堂主日崇拜 11/01/2026 11:00am PDT
重塑生命系列 (五): "忿怒与温柔" 雅各书 1:19-21 ~ 张健庭牧师博士 2026年 11月 01日 9:00am 国语堂崇拜直播
Renovation of the Heart (5): "Anger and Meekness"  James 1:19-21 ~ Rev. Dr. Caleb Cheung
```

| Field | Rule | Exceptions seen |
| --- | --- | --- |
| Title | Text inside the first pair of straight or curly quotes | One unquoted title (`信仰後退的危機`): fall back to the text between the series prefix and the scripture |
| Series | Prefix before `:` ending in `系列 (n)` or `(n)`; none means a standalone sermon | Spacing varies (`(十) :`) |
| Scripture | Between the title and `~` | Chapter only (`希伯來書 9`) |
| Speaker | After `~`, up to the congregation marker or end | 42 of 42 had `~` |
| Congregation | `粵語堂` → Cantonese, `国语堂` → Mandarin, English channel → English | `國﹑粤语堂联合崇拜` is a joint service: tag both Cantonese and Mandarin |
| Date / time | From YouTube's `actualStartTime` / `scheduledStartTime`, not the title | — |

A title that fails any required rule (title, speaker, congregation) goes to Needs review instead of guessing, e.g. `08/30/2026 MPCBC CONNECT MINISTRY` or `Cross-Congregational Testimony ~ Sister Carmen Chan`. A clean parse publishes automatically; the editor can still correct it. Older uploads (English 2020–21: `04.11.2021 MPCBC ENGLISH WORSHIP, ...`) use other formats and are backfill work, not the weekly rule.

### Aliases

One person or series appears in up to three spellings: `張健庭牧師博士` / `张健庭牧师博士` / `Rev. Dr. Caleb Cheung`; `重塑生命系列` / `Renovation of the Heart`. Speaker and Series terms each carry an alias list. Parsed text is matched against aliases; an unknown spelling creates nothing and sends the record to Needs review with "new speaker/series?" so editors pick an existing term or add one. Search matches any alias, so searching one spelling finds all three congregations' sermons.

### Playlists

The Chinese channel's playlists are stale (2020–21 series such as 羅馬書, 歌羅西書); the current 希伯來書 and 重塑生命 series have none. Series come from titles; playlists are optional input only, not a requirement on the media team.

Priority: explicit editor assignment, then membership in a configured ministry playlist, then unambiguous agreed title/description markers. Recognise the existing Cantonese/Mandarin markers, including 粵語/粤语 and 國語/国语. English channel membership is a source default, not a guarantee that every upload is a sermon.

Do not classify by Traditional versus Simplified script or 9 AM versus 11 AM alone. If evidence conflicts, leave Needs classification. Store classification provenance and preserve manual overrides. Duplicate checks use video IDs across all sources/playlists.

Use public scheduled/active discovery explicitly, not just a channel's latest upload. Upcoming scheduled videos may require a separate discovery query; known public video URLs can also be pasted manually. Private material is excluded; unlisted material is not automatically discovered or exposed. An explicitly approved unlisted URL requires a separate deliberate assignment.

## 3. Scheduler and sync

The host scheduler invokes WordPress due events every two minutes if supported, preferably through WP-CLI. A host-supported wp-cron.php invocation is a fallback. Do not put an API key or Basic authentication header into a public cron URL. Disable traffic-triggered WP-Cron only after the replacement scheduler is tested, retaining unrelated WordPress scheduled events.

| Job | Proposed frequency | Scope |
| --- | --- | --- |
| New uploads/playlist import | Hourly | Recent items, deduplicated; save progress |
| Public scheduled broadcast discovery | Every 3 hours, plus Sync now | Two channels; discover all eligible ministry candidates |
| Known broadcast status | Every 2 minutes around each scheduled service and while live | Batch known IDs with videos.list; actual video schedule handles special services |
| Fallback active discovery | At most every 15 minutes in service windows | Find replacement streams, subject to search budget |
| Metadata/availability recheck | Daily recent items; older entries in rolling batches | Changed titles, privacy, removed videos, thumbnails |
| Historical backfill | Initial resumable batches | Page through channel/curated playlists; preview before creating drafts |

Provider API calls must be budgeted separately from the host's scheduler cadence. Never run channel search every two minutes. Track a conservative search-call ceiling (initially 75/day, subject to actual Google project limits), stop nonessential discovery before the ceiling, and batch known-ID status calls separately. With two channels, routine three-hourly upcoming discovery is about 16 search calls/day; fallback checks in ordinary Sunday windows fit below the proposed ceiling. Special extended services require the same budget guard rather than assuming unlimited discovery.

Use America/Los_Angeles for church schedules, storing API timestamps in UTC; daylight-saving changes must be tested. The scheduler can run often while each job determines whether work is due. Include locks, retry/backoff, timeouts, deduplication, resumable backfill and last-success tracking. Show when a live result is stale; do not indefinitely assert Live after verification fails. The frontend uses cached site data; page rendering and visitor archive searches do not trigger YouTube API work.

References: <https://developers.google.com/youtube/data/reference/rest/v3/search/list>, <https://developers.google.com/youtube/data/reference/rest/v3/videos>, <https://developer.wordpress.org/plugins/cron/hooking-wp-cron-into-the-system-task-scheduler/>.

## 4. Homepage and Watch page

Retain Home 21, its hero/bottom service rail and existing bilingual reading-language behaviour. Update only the This Sunday dynamic video region and relevant archive links after a preview is approved.

The existing This Sunday slips (`scripts/build-taste-home.mjs`, section `#watch`) stay native Elementor: slip, tilt, heading, service time, dashed rule and archive/YouTube links are unchanged. Only the two changing parts of each slip become plugin output, one shortcode with a ministry attribute:

```
[mpcbc_live ministry="mandarin" part="media"]   replaces the photo + "Watch on YouTube" pill
[mpcbc_live ministry="mandarin" part="status"]  replaces the "Latest: [title], [Speaker]" line
```

Plugin markup uses classes and Elementor global colour/font variables, matching the existing pill (44px, fully rounded, accent). State labels (Live now / Next / Latest) are output in English plus the `mpcbc-copy-zh-hant` / `mpcbc-copy-zh-hans` copies so the header language switch still works; the YouTube title stays in its original wording. The slip's current photo, pill and "Latest:" line are placeholders and are removed, not kept as a fallback; the only images shown are real YouTube thumbnails. The shortcodes go into the build script's `service()` helper so rebuilds keep them. First check: the Elementor Free Shortcode widget renders inside the Atomic slip container; if not, one plugin block replaces the media box only.

Three equal ministry cards on desktop; stacked cards on mobile. Each has ministry, public video title, actual date/time/status, thumbnail with play control and ministry archive link. Keep existing header language switcher; no extra reading-language tabs. Ministry choices, where present, are explicitly video filters.

Selection independently per ministry:

1. Verified active service -> Live now.
2. Next eligible scheduled service -> Upcoming, with actual date/time.
3. Most recent eligible completed service -> Latest service, clearly dated.

Every imported video is stored as a sermon record in the WordPress database, so the cards read stored records, not a temporary cache. When YouTube is unreachable or the quota is spent, the stored latest recording stays on the card; only live/upcoming checks pause. There is no image or placeholder fallback:

- A recording whose ministry marker parsed but whose speaker/series wait in Needs review can still fill the card (see section 1).
- A recording later deleted or made private is marked unavailable by the daily recheck; the card moves to the next older recording.
- A ministry with no records at all (e.g. a new channel): the shortcodes output nothing and the slip shows only its Elementor text. No special state is designed for this.

The Upcoming pill links to the channel's `/live` URL (`https://www.youtube.com/@MPCBC/live`, `https://www.youtube.com/@englishmpcbc8168/live`), which YouTube resolves to the current or next stream even if site data is stale.

If an upcoming service has passed its scheduled start but is not active, use Waiting for service to begin. After a bounded configurable overdue interval, stop presenting it as imminent and fall back to the latest service with a notice/override for editors. The clock alone never sets Live. Completed broadcasts may still be processing; avoid a broken player while no playable recording is available. Deleted/cancelled/non-embeddable videos fall back appropriately, with a YouTube link when allowed.

Load an embed only when requested. Upcoming cards show a thumbnail and View scheduled service action; retain Watch latest service beneath them when a completed service exists. No autoplay. Keep the selected player running if a background refresh merely changes other metadata; offer a Live now action rather than replacing a visitor's current playback unexpectedly.

`/watch/` offers a larger selected ministry player, status, service information, original YouTube link and recent recordings. It uses the same records and selection policy as Home. Defaults to a saved explicit ministry preference if present; otherwise a deliberate initial choice rather than inferring from reading language.

## 5. Archive pages and search

One shared archive at `/sermons/`, with all routes resolved through WordPress so the current hosting subdirectory and a later domain move work. Three sermon menu links open filtered views, e.g. `/sermons/?ministry=cantonese`; preserve the current reading-language parameter independently.

Archive layout:

1. Compact Sermons heading and brief introduction.
2. Visible search box: Search title, scripture, speaker or series.
3. Spoken-language chips: All / Cantonese / Mandarin / English.
4. Filters: series, speaker, Bible book, year and full-service/sermon type, collapsed behind Filters on phones.
5. Result count, active filters, Clear filters and sort (newest default; relevance default when searching).
6. Thumbnail cards: selected-language or original title, scripture, speaker, service date, series and spoken-language/type badges.
7. Server-side pagination, 12 results at a time; accessible links, no huge all-video response or grid of live iframe players.

Search only local published archive records. Index original and approved translated titles/summaries, speaker aliases, series and scripture. Support English case/punctuation variants and Chinese substring matching; test both Chinese scripts and derive search-only equivalence aliases where necessary. Never rewrite displayed editorial wording to make search work. Map Bible book names/abbreviations across English/Traditional/Simplified to canonical scripture fields (e.g. John 3:16 / 約翰福音 3:16 / 约翰福音 3:16).

Do not rely on default WordPress title/content search to find taxonomy/meta fields: implement an explicit normalised search document and query path. At the expected initial library size, a database-backed search index is sufficient; no paid search plugin or remote service. Measure import/search performance against the actual backfilled library before selecting any more elaborate index.

Search is server-backed with sanitised/prepared queries and bounded results. A normal GET form works without JavaScript; progressive filtering updates results and URL. Preserve q, filters, page and reading language for sharing, refresh and Back navigation. Search suggestions or debounce are optional enhancements, not required for phase one. Empty results offer Clear filters without silently broadening a ministry choice.

No claim to search spoken words within videos unless an approved transcript has actually been added and indexed. Records that parsed cleanly against the title rules in section 2 publish automatically; Needs review records are excluded from the curated archive, even if their public service recording is already watchable.

Single sermon page `/sermons/{slug}/`: one responsive player, recording language/date/speaker/scripture/series, approved translated notes, optional verified Start sermon timestamp, approved downloads, related messages and Back to results preserving the search. Series pages `/sermon-series/{slug}/` reuse the same query/cards with series introduction and ordering.

There is one header language selector. Switching it preserves the recording, query and ministry, changes interface/available wording, and never claims the audio changed. Use original wording as a clear fallback for unavailable translations. Do not require all three translations before a valid sermon can be published.

## 6. Elementor/MCP and deployment boundaries

MCP edits Atomic page framing, headings, spacing and approved content placements. It does not install plugin PHP, provision a YouTube API key, or configure hosting cron. Those require plugin installation and host tooling separately.

Prototype the dynamic integration before page implementation: confirm Elementor Free rendering, global styling, responsive behaviour, saved configuration, and what the official MCP schema can actually inspect/edit. The dynamic video/search region is plugin-owned data, not hundreds of copied Atomic text widgets. Do not promise that a plugin-rendered interface is natively Atomic or MCP-editable. No HTML layout widgets, Elementor Pro or hardcoded page copy in the child theme. Any unavoidable exception to the existing all-native-widget model must be made concrete for review before adoption.

Package the custom plugin as an ordinary installable ZIP, with versioned source, no credentials, a migration strategy and exportable records. Preserve current live page data and native global styles before MCP changes. Elementor draft edits retain page settings and the full-width Home template. Stage the implementation locally/isolated first; the existing user-uploaded online copy remains the authoritative baseline for later live layout edits.

## 7. Delivery and acceptance

1. Preview: desktop/mobile Home cards, Watch, searchable archive and sermon detail, including live/upcoming/latest/empty states. No live edits.
2. Integration spike: demonstrate the actual Elementor Free/MCP dynamic component boundary, one test video record, editor permissions and search. Review any native-Atomic limitation before extending the site.
3. Plugin and sync: configure source/ministry rules, import a small sample as review records, test both scheduled Chinese broadcasts plus English. Add host scheduler after local scheduling logic passes.
4. Frontend: integrate the approved Home region and create Watch/archive/detail/series views. Update menu links and existing locale routing through MCP where supported.
5. Backfill and launch: resumable historical import, editorial assignment, measured search tests, cache/mobile checks and backup/rollback before publishing reviewed changes.

Meaningful tests: two scheduled streams on one channel; overlapping live broadcasts; delayed/replaced/cancelled streams; invalid API key/quota failure; duplicate/concurrent imports; source edits preserving editorial overrides and review state; deleted/non-embeddable videos; pending records not leaking through public endpoints/search; Chinese/English scripture and name queries; pagination/filter/language/Back persistence; mobile controls/keyboard navigation; Pacific DST; cron recovery and host cache freshness.

## 8. Volunteer maintenance

YouTube titles are the source of truth; WordPress is where exceptions are fixed. No Google Sheet for ongoing work: a sheet would be a second source of truth, and free-typed speaker names split filters.

- Weekly: nothing, when titles follow the format. Check Sermons → Needs review if anything is waiting (target: under a minute).
- Occasionally: add a new speaker or series with its Traditional / Simplified / English spellings, using dropdowns, not free text.
- Once: a sheet or CSV of old video IDs with speaker/series may be imported for backfill, then discarded.
- Ask the media team to keep the title format above; a format change sends everything to Needs review rather than publishing wrong data.

The archive page is native Elementor around one `[mpcbc_sermons]` shortcode (search box, congregation/series/speaker filters, cards), because Elementor Free has no Loop Grid. Search covers title, speaker and series including aliases.

Future optional module: Announcements & Events can share the multilingual editor patterns after Sermons/Live is stable. It is not required for video launch and should not expand the initial build into a full calendar/PDF/email system.
