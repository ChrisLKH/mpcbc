# Servantly suitability review — 20 October 2026

Scope: research only for design A / mpcbc.local. No WordPress plugin was installed, activated, or executed. Downloaded PHP was read as text. This is a compatibility review, not a security audit or a running integration test.

## Recommendation

Build a focused MPCBC content plugin for the sermon/live requirements. Keep live discovery, ministry assignment, multilingual wording and editorial approval in one owned data model. Use a hosting scheduler to trigger WordPress jobs. Avoid modifying Servantly's installed files or running a second importer against the same records.

Servantly is useful as a reference and a possible upstream component if the publisher adds multi-channel/ministry support and a genuine review mode. The current version would need replacement or substantial wrapping of several central functions, rather than a small visual adapter. Do not describe it as abandoned: it has recent updates.

Defer adopting its Announcements, Events & Bulletin plugin until an actual supported distribution and documentation can be reviewed. Meanwhile, use normal WordPress editorial records for dated announcements; only implement event recurrence if editors need it.

## Sources and method

- Publisher catalogue: <https://whoknew.io/servantly/plugins/>
- Publisher landing page, also the destination of servantly.org: <https://whoknew.io/servantly/>
- Sermon directory/readme/changelog: <https://wordpress.org/plugins/servantly-sermon-importer/>
- Official package reviewed: <https://downloads.wordpress.org/plugin/servantly-sermon-importer.2.1.14.zip>
- Directory metadata: <https://api.wordpress.org/plugins/info/1.2/?action=plugin_information&request[slug]=servantly-sermon-importer>
- Advertised bulletin page: <https://whoknew.io/servantly/plugins/bulletin-board/> — returned a visible 404 in the browser during this review.
- WordPress.org API search for Servantly returned Sermon Archive and Ask; no bulletin package was found by that brand search. This does not prove no separately named/private package exists.

WordPress.org metadata checked live: sermon version 2.1.14; last updated 2026-10-02; added 2026-05-11; zero ratings returned. The directory represents adoption as fewer than 10 active installations; the API's rounded zero is not evidence of zero actual sites. Publisher identifies Calvary Chapel Lake Arrowhead as a live deployment. Recent changelog entries address duplicate import races, timezone handling and cache invalidation. These are evidence of current maintenance, not a promise of future support.

## Sermon plugin: useful existing functionality

- Native WordPress posts in the sermons category, with speaker/book/series taxonomies and stored YouTube IDs. Content is more portable than a remote-only archive.
- Recent/full imports, scheduled imports, duplicate checks and import claims.
- Manual edit lock, title delimiter mapping, API error log and cache purging.
- Public read endpoints for sermons, filter options and one live-status result.
- Archive page template, latest-sermon and watch shortcodes with bundled CSS/JS.
- GPL-3.0-or-later licence in the package. Any reused code must retain applicable attribution/licensing obligations.

## Confirmed mismatches in the downloaded 2.1.14 code

| MPCBC requirement | Observed implementation | Consequence |
| --- | --- | --- |
| Chinese and English channels together | One `channel_id` in the saved options | Needs new source configuration and orchestration; not a setting for several ministries |
| Cantonese and Mandarin independent upcoming/live/recording slots | Live detector scans five recent upload IDs and returns immediately on the first active stream; one global transient/result | Two scheduled services are not intrinsically a problem, but the plugin has no ministry-aware selection or separate state |
| Upcoming broadcast selection | Active-live detection uses uploads + videos metadata; no dedicated upcoming discovery in this detector | Must test actual scheduled streams and add explicit discovery/assignment; do not promise reliable upcoming coverage |
| Human approval before archive publication | Import inserts a draft, writes metadata, then publishes; re-import publishes unlocked existing draft/pending records | Temporary technical draft is not an editorial review queue; an after-import hook is too late to guarantee no publication |
| Chinese scripture/speaker extraction | Default Bible book list and special-event keywords are English; delimiter mapping and a parsed-title filter are available | Chinese titles need mapping/parsing work or manual field entry |
| Three independently written reading-language versions, separate from spoken ministry | Single post title/excerpt/content and shared label options; no matching native three-copy fields/ministry filter found | WordPress string translation alone does not meet editorial wording needs |
| Weekly editors with limited permissions | Custom admin management screen and AJAX handlers use `manage_options` | Standard Editors cannot use that management UI; ordinary post editing remains a different interface |
| Editable native Atomic page layouts/global design tokens | PHP archive template and shortcode-produced HTML/CSS/JS; no Elementor widget registration found | Elementor compatibility does not mean native Atomic editing; styling and asset loading need separate integration |

Code references (relative to the downloaded plugin folder):

- `includes/class-servantly-si-admin.php`: `CAPABILITY`, `channel_id` registration/options, custom-screen handlers.
- `includes/class-servantly-si-live-detector.php`: `detect()`, global transient, `detect_from_uploads_playlist()` and first-match return.
- `includes/class-servantly-si-importer.php`: `import_video()`, draft-to-publish transitions and unlocked draft/pending re-publication; `servantly_si_parsed_title` / `servantly_si_after_import` hooks.
- `includes/class-servantly-si-rest.php`: archive arguments speaker/book/series/year/sort/page; one live-status result. No built-in ministry/reading-locale filters in the inspected route.
- `includes/class-servantly-si-title-parser.php`: English canonical Bible books and special-event list.
- `includes/class-servantly-si-watch.php`: shortcode, bundled front-end assets; initial asset check examines post_content, so Elementor-stored shortcode embedding needs a running check.
- `templates/page-sermons.php`: PHP-rendered archive, not an Elementor document.

## Announcements, Events & Bulletin

Publisher advertises one-time/recurring events, automatic expiry/unpublishing, Google Calendar links and shortcode display. That would be useful for a homepage What's On list and a dated bulletin view, avoiding repeated layout editing.

The linked Learn More and Download destinations both pointed to the same page that returned 404. No package/source was obtained, so the following remain unverified: actual release/update mechanism, roles, bilingual/trilingual wording fields, recurrence exceptions and DST, expiry versus history retention, PDF bulletin archives, REST/Elementor integration, and connections to sermon records. The directory's Translate link for Sermon Archive is not evidence of multilingual content support in Bulletin.

Do not claim it generates print/PDF bulletins, sends email, accepts event registrations, or includes a multilingual workflow without documentation/testing. Keep Fluent Forms for existing registration/contact needs.

## Proposed MPCBC workflow

1. AV volunteer schedules each service in YouTube and assigns a ministry playlist or explicit title marker. Manual video-ID assignment is always available; time/script alone does not classify spoken language.
2. Sources represent two channels; ministry slots represent Cantonese, Mandarin and English. Each slot independently tracks upcoming/live/completed/unavailable status.
3. Background discovery finds eligible videos; known IDs are status-checked in batches around services. Cache metadata and retain last valid results on errors, but do not keep asserting Live from stale data.
4. Live viewing does not wait for archive wording review. Completed recordings enter a real Needs review queue and stay there through re-sync until an authorised editor publishes.
5. One recording record holds shared facts, spoken ministry and separately editable EN/zh-Hant/zh-Hans wording. Preserve original YouTube metadata separately from editorial overrides.
6. Homepage, Watch and archive read the same records. Native Atomic page framing remains; any dynamic widget/block integration is a separate explicit implementation task.
7. Announcements share one set of dates/links and three wording fields. Optional event details include actual occurrence time, location, recurrence/exceptions and registration link. Expiry removes an item from current lists without deleting historical records. Existing PDF bulletins remain separately dated media records.

Prefer one installable plugin with independent Sermons/Live and Announcements modules, released in stages. Start with Sermons/Live; avoid building a complex calendar/PDF generator before a real editorial need exists. Keep content in standard WordPress data structures and support export. Provide editor capabilities separate from administrator API/settings access, sync health, retry/manual overrides, deduplication and migration documentation.

Both prebuilt and custom software need periodic compatibility and security maintenance. A custom implementation removes licence fees, not ownership costs. Do not fork the entire Servantly plugin just to preserve its UI; a fork would make MPCBC responsible for upstream fixes and merge conflicts.
