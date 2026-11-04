# Migration and MCP review — 20 October 2026

**The Elementor layouts are now Atomic and ready for backup/restore and subsequent MCP connection.** Four existing Fluent Forms shortcode placements remain classic because Elementor Free has no Atomic shortcode/form replacement. The WordPress header/menu remains native WordPress. This is not public-launch approval.

## Verified local state

- WordPress 7.1.3, Elementor Free 4.3.4, PHP 8.2.29, active child theme `hello-mpcbc`.
- Atomic Editor enabled; Elementor MCP access disabled. No MCP connection or credentials were created during this audit.
- Twenty-two maintained Elementor documents now contain 1,732 Atomic elements. Home (21) and the site-wide footer (16) are consolidated masters with 50 separately editable Traditional/Simplified copy pairs. Two former native copies are retained as a legacy redirect (511) and draft archive (513), excluded from these maintained counts.
- Four classic shortcode placements preserve the same three existing Fluent Forms (newsletter, contact and prayer), across Home and Prayer. No other classic content elements remain in the maintained documents. No Pro widgets or new plugin were installed.
- The installed official MCP structure ability reads all 22 converted documents. Its element-edit ability successfully performed a no-op heading update on the isolated preview; it also generated responsive tab refinements as a draft, which were reviewed and applied locally. These were internal PHP ability calls, not an authenticated network MCP connection. The isolated preview is now a draft.
- Three About pages include all eight original doctrine statements and references. Old Beliefs URLs redirect to matching About#beliefs destinations.
- Primary menu uses term 3; groups are Sundays, Sermons, Church life, About, Give, plus the existing language switcher. Recorded sermon language remains independent of reading language.
- All eleven native language document variants pass the existing content/global-palette validator. No missing image files were found in the eight main documents audited.
- Site-relative Elementor URL fields were normalized to absolute local site URLs so a serialized-aware migration can replace them with the complete destination URL, including `/website_3849f7d0/`. Native words, styling and layout were unchanged. Backups: `content-source/before-migration-links/`.
- Child-theme code has no hardcoded local domain or Windows site path. Routing uses WordPress URLs and page metadata.

## Backup / restore handoff

1. Build a **new full files + database** backup after this review. Earlier packages do not contain the final merged About, menu or URL normalization.
2. The local `wp-content/themes/hello-mpcbc` directory is a Windows junction to this repository. Inspect the package to confirm it includes real theme files, including `vendor/opencc-js/` and licenses. A separate current `hello-mpcbc-migration.zip` contains the complete theme. It is a theme fallback, not a site/database backup.
3. Preserve the existing online site separately before restoring. Use the full destination URL `https://qmj.hxy.temporary.site/website_3849f7d0/` during the migration's URL replacement.
4. After restore, regenerate Elementor CSS/data and refresh WordPress permalinks; clear host/browser caches.
5. Check video playback/pause, all four hero times including Children 10:00 AM, menu dropdown contrast, footer, media, forms, About#beliefs in all three locales and old Beliefs redirects. Test a fresh browser session and language persistence from interior pages. Confirm requests no longer use `mpcbc.local` or root-domain `/about/` outside the destination subdirectory.
6. Confirm the child theme and required plugins are active. Remove migration installer/archive files through Duplicator's cleanup flow after a successful restore. No backup package was created by this review; the user handles backup and restore.

## MCP next step

Use the restored staging copy to connect through **Elementor > Elementor MCP**, then make and review a small text-change draft. The layout rebuild is complete. The reader uses native Atomic Tabs/Accordions and the hero uses Atomic Background Video with native controls. A small existing navigation enhancement provides the phone selector and Previous/Next behavior through stable element IDs. Reduced-motion handling remains in the child theme.

MCP edits to published pages are stored as autosave drafts; they do not publish automatically. Preserve the full page settings and page template when accepting a draft. In particular, Home must retain `elementor_header_footer` (Elementor Full Width); this was restored and verified after the local MCP style test. Keep menus in Appearance › Menus and form fields/settings in Fluent Forms. Neither is converted into an Atomic widget.

The shared Atomic footer is found by template title and registered before Elementor queues its styles. Its content stays in the native Elementor library template. A new full backup must include the database's Atomic global variables and all current child-theme files; importing page JSON alone is not a complete migration.

Footer spacing was refined through the official local MCP editing ability (19 native style operations). Subsequent consolidation preserved its proportions and both Chinese wordings. The master Home and footer pass the installed MCP structure reader. The mobile language selector sits outside the hamburger; all three Home choices now switch without a document reload. Only menu Chinese converts automatically. Native linked Flexbox groups keep English labels/URLs shared and Chinese text independently editable; no HTML layout widgets or Pro attributes are used. Evidence: `shared-home-footer-result.json`, `shared-home-footer-validation.json`, `shared-home-footer-ui-checks.json`. Editing guide: `HOME-FOOTER-EDITING.md`.

No extra plugin was installed. A legacy-compatible third-party bridge would require separate selection and user approval under this repository's plugin rule. The plugin-directory search found WordPress.com, which is not the appropriate connector for this self-hosted Elementor installation.

Official references:

- https://elementor.com/mcp/
- https://elementor.com/help/how-to-connect-elementor-to-an-ai-tool-using-mcp/

## Remaining public-launch work

Home still contains bracketed mock content, the footer has logo/office-hours placeholders, and `/visit/` remains a preparation page even though the hero/footer link to it. Shared Home/footer are bilingual rather than three fully localized versions. These do not prevent staging migration, but prevent calling this a completed public launch. Do not invent missing facts to clear placeholders.

Audit evidence: `content-source/migration-mcp-audit.json`, `atomic-conversion-result.json`, `atomic-mcp-validation.json`, `atomic-mcp-edit-proof.json`, `atomic-responsive-checks.json`, `locale-content-validation.json`. Original documents, kit, menu, variables and theme are preserved in `content-source/before-atomic-conversion/`. All pre-Atomic builders are historical and must not be reapplied over the native Atomic exports.

Latest consolidation supersedes the separate-page routing: all Home choices use one document and switch in place. Old `/zh-hans/home/` URLs return 301 to `/?lang=zh-hans`. OpenCC loads asynchronously on demand for menu text only. The shared footer appears on interior pages with the selected native Chinese wording. Local MCP structure checks pass for both shared masters; network MCP remains off. Refresh the full backup after consolidation. Current evidence: `shared-home-footer-validation.json`, `shared-home-footer-ui-checks.json` and `migration-mcp-audit.json`. Earlier speed/routing reports are historical.
