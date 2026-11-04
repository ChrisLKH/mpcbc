# Edit the shared Home and footer

There is one active Home page and one site-wide footer template.

| What to change | Where to edit |
|---|---|
| Home layout, English copy, photos, video and service rail | Pages → Home → Edit with Elementor (21) |
| Home Traditional wording | Home → Structure → `Traditional / 繁體中文` elements |
| Home Simplified wording | Home → Structure → `Simplified / 简体中文` elements |
| Footer on every page | Templates → Saved Templates → MPCBC – Footer (16) |
| Footer Chinese wording | The same footer's Traditional / Simplified elements |
| Menu English / Chinese | Appearance → Menus → Navigation Label / Description |
| Form fields and labels | Fluent Forms; the three existing bilingual forms remain shared |

Open Structure in Elementor to select the named text elements. Both Chinese
versions are visible in the editor so each remains editable. The public site
shows only the selected Chinese version, alongside the shared English content.
Chinese wording is not automatically translated or regenerated.

Bilingual button/tab labels use native Flexbox groups. Edit their English,
Traditional and Simplified text children independently. A linked group has one
Link setting on its parent, so its destination is shared. Text stays in native
Atomic elements; no HTML layout widget or custom HTML editing is required.

The three Home reading choices share the same page and switch without reloading:
`/`, `/?lang=zh-hant` and `/?lang=zh-hans`. The footer uses template 16 on Home,
About, Give, Prayer and other pages. About/Give retain their existing separate
language-specific page content.

The old Simplified Home (511) is now a legacy redirect, not an editing destination.
Its original text and the old Simplified footer (513, draft) are preserved, along
with snapshots in `content-source/before-home-footer-consolidation/`.

Shared facts now have one instance per placement across language views. A fact
repeated in Home and the footer still has two placements; a future central Church
Details record would be needed to synchronize facts across the entire site.

Export current WordPress content before any rebuild. The consolidation scripts
are one-time migration helpers and are guarded against overwriting later edits.
