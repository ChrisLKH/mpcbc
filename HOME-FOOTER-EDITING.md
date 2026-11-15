# Edit the shared Home, footer and Give

There is one active Home page, one site-wide footer template and one Give page.
Each holds all three reading languages.

| What to change | Where to edit |
|---|---|
| Home layout, English copy, photos, video and service rail | Pages → Home → Edit with Elementor (21) |
| Footer on every page | Templates → Saved Templates → MPCBC – Footer (16) |
| Give, all three languages | Pages → Give → Edit with Elementor (65) |
| Menu English / Chinese | Appearance → Menus → Navigation Label / Description |
| Form fields and labels | Fluent Forms; the three existing bilingual forms remain shared |

## Chinese wording (MPCBC Translations plugin)

Select any Heading, Paragraph or Button. Its panel has a **Chinese 中文** section:

- **繁體中文**: shown instead of the main text when a visitor picks 繁體中文 or 简体中文.
  Leave it empty and the main text shows in every language.
- **简体中文**: leave empty. Simplified is converted automatically from the Traditional
  text. Type here only when the automatic wording is wrong.

Home and the footer are bilingual by design: English headings with a Chinese line under them.
That Chinese line is its own element whose main text is written in Traditional. Nothing else
is needed: no second Simplified element, no ID. To add one, duplicate an existing Chinese line.
Example of a kept override: the Home mission line (借着, where conversion gives 藉著).

The editor canvas shows the main text only; the Chinese fields show on the website.
All Traditional text on the page, including menu descriptions, is converted for 简体中文 in the
browser (bundled OpenCC 1.4.2).

The old Simplified Home (511), the old Simplified footer (513, draft) and the 繁體/简体 Give
pages (233, 236) are legacy redirects or archives, not editing destinations. The state before
the move to translation fields is in `content-source/before-translation-fields/`
(`scripts/build-translation-fields.mjs` + `apply-translation-fields.php`, one-time, guarded
against overwriting later edits).

A fact repeated in Home and the footer still has two placements; a future central Church
Details record would be needed to synchronize facts across the entire site.
