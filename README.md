# MPCBC website (WordPress)

Monterey Park Chinese Baptist Church's website, built in WordPress (Elementor free, Hello Elementor child theme). The repo root is the site; local site `mpcbc.local`. See `AGENTS.md` for the layout and rules and `HANDOFF.md` for the build brief.

Retired work is kept for reference in `archive/`: the old Astro site (`archive/astro-site/`) and design B (`archive/wordpress-design-b/`).

---

# MPCBC WordPress Website Mockup

Latest local changes (19 October 2026): Home uses the approved full-video hero with an integrated bottom service rail, replacing the white times card. The service times, address and Directions link fit the first screen at the checked desktop, tablet and phone sizes. The approved anchored About layout is implemented in English, Traditional Chinese and Simplified Chinese. Reading-language selection persists through internal navigation. See [multilingual organisation](MULTILINGUAL-ORGANISATION.md) and the [live-content plan](CONTENT-PLAN.md). Current exports are `elementor/home-content-filled.json`, `home-hero.json`, `home-service-rail.json` and `footer-content-filled.json`; About exports are `elementor/{locale}-about.json`. Edit hero words and times directly in Home with Elementor; colours and fonts are Global tokens under “Hero”. Sources and pre-edit backups are in `content-source/`. Verified live facts override older placeholder facts below. Elementor MCP is not connected; the user will migrate the final local site before MCP setup.

Multilingual pages: see [organisation and editing guide](MULTILINGUAL-ORGANISATION.md). About, Beliefs and Give now have English, Traditional Chinese and Simplified Chinese versions. Use `elementor/en-*.json`, `zh-hant-*.json` and `zh-hans-*.json` for the current page exports. Switch languages using the shared header dropdown; there are no in-page language tabs. The Chinese indexes are `/zh-hant/` and `/zh-hans/`. Home and the shared header/footer retain their bilingual mockup content.

This folder contains the complete WordPress mockup assets, child theme, Elementor template definitions, Fluent Forms setups, and documentation for Monterey Park Chinese Baptist Church (MPCBC), based on the design brief in `HANDOFF.md`.

---

## Deliverables in this Directory

| Directory / File | Description |
|---|---|
| `hello-mpcbc/` | The Hello Elementor child theme source files (`style.css`, `functions.php`, `footer.php`). |
| `hello-mpcbc.zip` | Ready-to-upload child theme zip package for WordPress (Appearance › Themes › Add New). |
| `elementor/` | 20 exported Elementor template JSON files (full pages and individual sections, 100% native widgets, zero inline styles). |
| `fluent-forms/` | Fluent Forms export files for the "Prayer request" and "Stay in the loop" forms. |
| `placeholders/` | 13 labelled placeholder PNG images matching the design specifications. |
| `screenshots/` | Complete responsive screenshots at 390px, 768px, and 1440px plus hover state capture. |
| `scripts/` | Helper scripts for generating placeholders, auditing templates, and verifying logs. |
| `design-reference/` | Original design boards and palette specifications. |

---

## 1. Setup Guide (For Admin / Local Environment)

### Step 1: Child Theme Activation
1. In your WordPress site (Local by WP Engine):
   - **Method A (Junction for development)**:
     Open Command Prompt as Administrator and run:
     ```cmd
     mklink /J "C:\Users\<user>\Local Sites\mpcbc\app\public\wp-content\themes\hello-mpcbc" "C:\Projects\mpcbc\hello-mpcbc"
     ```
   - **Method B (WordPress Admin upload)**:
     In WP Admin, navigate to **Appearance › Themes › Add New › Upload Theme**, choose `hello-mpcbc.zip`, install and activate.
2. Ensure the parent theme **Hello Elementor** is installed.

### Step 2: Install & Configure Plugins
Install and activate the following free plugins from the WordPress plugin repository:
1. **Elementor** (Free version).
2. **Essential Addons for Elementor** (Free):
   - Go to **Essential Addons › Elements**.
   - Turn **OFF** all widgets except **Fancy Text** (used for the rotating greeting on the `/welcome/` page). Save settings.
3. **Fluent Forms** (Free).
4. **Duplicator** (Free, used for exporting the finished site to production).

### Step 3: WordPress Core Settings
1. **Reading Settings** (Settings › Reading):
   - **Your homepage displays**: A static page.
   - **Homepage**: Select `Home`.
   - Check **"Discourage search engines from indexing this site"**.
   - Save changes.
2. **Permalinks** (Settings › Permalinks):
   - Set to **Post name** (`/%postname%/`) and save.

### Step 4: Elementor Global Settings & Fonts
1. **Default Page Layout**:
   - Go to **Elementor › Settings › General**.
   - Under Site Settings / Default Page Layout, select **Elementor Full Width**.
2. **Font Route Configuration Report**:
   - In Elementor, Geist (body font) is not included in Elementor's default bundled font dropdown.
   - Therefore, go to **Elementor › Settings › General** and tick:
     - [x] **Disable Default Colors**
     - [x] **Disable Default Fonts**
   - This ensures widgets automatically inherit the typography cleanly enqueued by `hello-mpcbc` (`Bricolage Grotesque` for headings, `Geist` for text, `Noto Sans TC` for Traditional Chinese, and `Noto Sans SC` for Simplified Chinese).
3. **Global Colours (Site Settings › Global Colors)**:
   - **Primary**: `#0E6B5C` (Evergreen)
   - **Secondary**: `#12110E` (Night)
   - **Text**: `#16140F` (Ink)
   - **Accent**: `#F4B942` (Sun)
   - Custom color slots:
     - `Paper`: `#FAF7F2`
     - `Stone`: `#EDE6DA`
     - `Ink soft`: `#5C564D`
     - `Live`: `#D92D27`
     - `Sand`: `#F4EEE3` (Used for Sunday times card interactive hover state)

### Step 5: Navigation Menu (Appearance › Menus)
1. Go to **Appearance › Menus**. Click **Screen Options** at top right and ensure **Description** and **CSS Classes** checkboxes are checked.
2. Create menu named `Primary Menu`, assigned to the **Header** location.
3. Add 8 Custom Links:
   - **Visit** — Description: `初次來訪`
   - **Sundays** — Description: `主日崇拜`
   - **Kids & Youth** — Description: `兒童及青少年`
   - **Watch** — Description: `線上崇拜`
   - **About** — Description: `關於我們`
   - **Give** — Description: `奉獻`
   - **中文** — (Language switcher)
   - **Plan your visit** — Description: `計劃來訪`, CSS Class: `button`

### Step 6: Fluent Forms Import
1. Navigate to **Fluent Forms › Forms › Import Forms**.
2. Import `fluent-forms/prayer-request-form.json` (Creates Form ID 3: "Prayer request").
3. Import `fluent-forms/newsletter-form.json` (Creates Form ID 4: "Stay in the loop").

### Step 7: Elementor Templates & Pages Import
1. Navigate to **Templates › Saved Templates › Import Templates**.
2. Import `elementor/footer-template.json` (Title: `MPCBC – Footer`).
   - The child theme's `footer.php` automatically queries and displays this template at the bottom of every page.
3. Create the pages:
   - **Home (`/`)**: Page Layout = `Elementor Full Width`. Import `elementor/home-full.json`.
   - **Welcome (`/welcome/`)**: Page Layout = `Elementor Canvas`. Import `elementor/welcome-canvas.json`.
   - **Prayer (`/prayer/`)**: Page Layout = `Elementor Full Width`. Import `elementor/prayer-page.json`.
   - Subpages (`/visit/`, `/sundays/`, `/kids/`, `/watch/`, `/about/`, `/give/`, `/zh/`, `/coming-soon/`): Page Layout = `Elementor Full Width`.

---

## 2. Volunteer Guide (Non-Technical Church Volunteers)

Welcome! You can easily update text, times, photos, and colors on the website directly from WordPress without writing any code.

### How to change the Headline or Text
1. Go to **Pages** in WordPress admin and click **Edit with Elementor** next to the page you want to update (e.g. `Home`).
2. Click directly on any headline (e.g. *"You’re welcome here."*) or paragraph.
3. A panel opens on the left: change the text in the box.
4. Click the pink/green **Update** button at the bottom left to save.

### How to update Sunday Service Times
1. In the Elementor editor on the Home page, scroll down to the **Sunday times card**.
2. Click on the time you want to edit (e.g. `9:30 AM`).
3. Update the time or notes in the left panel. All 4 service cards share the subtle Sand (`#F4EEE3`) hover effect automatically.
4. Click **Update**.

### Each week: update the live links
1. Go to **Pages** in WordPress admin and click **Edit with Elementor** next to `Home`.
2. Scroll down to the **Watch live** section (directly below the Sunday times card, or click the hero "Watch live" button to jump to it).
3. Click directly on the video player you want to update (English or Chinese):
   - In the left panel under **Content › Video**, look for the **Link** field.
   - Paste that Sunday's YouTube Live stream URL (for example: `https://www.youtube.com/watch?v=...` or `https://www.youtube.com/live/...`).
4. Click the pink/green **Update** button at the bottom left to save and publish.

### How to change a Photo
1. Click on the card or image you want to change.
2. In the left panel under **Content** (or **Style › Background**), click on the image thumbnail.
3. Choose a photo from your Media Library or upload a new one from your computer.
4. Click **Update**.

### How to update Navigation Menu items
1. In the WordPress dashboard, go to **Appearance › Menus**.
2. Click the small arrow on any menu item (e.g. *Sundays*).
3. Change the **Navigation Label** (English) or the **Description** box (Chinese second line).
4. Click **Save Menu**.

### How to change the Primary Theme Colour
1. Open any page in the Elementor editor.
2. Click the hamburger menu icon (☰) at the top left of the Elementor panel.
3. Click **Site Settings › Global Colors**.
4. Click the color circle next to **Primary** and pick your new color.
5. All buttons, links, the Chinese band, and header accents will instantly update across the entire site!
6. Click **Update** to save.

### Where to read Prayer Requests and Newsletter Signups
1. In the WordPress dashboard, click **Fluent Forms**.
2. Click on **Entries** under "Prayer request" or "Stay in the loop".
3. Here you will see all submitted entries with date, name, and message details.

---

## 3. HostGator Migration Guide (For Website Admin)

When ready to migrate to HostGator, the website administrator can generate a fresh Duplicator package in 1 click:

### Creating an Export Package
1. In WordPress admin, go to **Duplicator › Packages › Create New**.
2. Name the package (e.g. `MPCBC_Production`) and click **Next**.
3. Duplicator runs a system scan (all checks will pass `Good`).
4. Click **Build**.
5. Once complete, click **Download › Both Files** to download `installer.php` and the archive `.zip`.

### Migration Steps:
1. **Prepare HostGator Subdomain / Temp Domain**:
   - Create the target subdomain or directory in HostGator cPanel (e.g. `public_html/mpcbc/`).
   - Create a fresh MySQL database and database user with full privileges in cPanel MySQL Databases. Note the database name, username, and password.
2. **Upload Files**:
   - Using cPanel File Manager or SFTP, upload the archive `.zip` and `installer.php` into the root web folder of the destination subdomain.
3. **Run Duplicator Installer**:
   - In your web browser, navigate to `http://your-subdomain.domain.com/installer.php`.
   - In Step 1, verify the archive file is recognized and click **Next**.
   - In Step 2, enter the HostGator database credentials (host `localhost`, database name, username, password) and click **Test Database** followed by **Next**.
   - In Step 3, confirm the new domain URL and site title, then click **Next**.
   - In Step 4, click **Admin Login** to enter the migrated WordPress dashboard.
4. **Post-Migration Verification & Cleanup**:
   - Once logged into the HostGator WordPress admin:
     - Duplicator will show a banner to delete installer files (`installer.php`, archive zip, and installer logs). Click **Delete Installer Files** for security.
     - Check **Elementor › Tools › Regenerate CSS & Data** to regenerate responsive CSS caches for the new domain.
     - Visit the home page, `/welcome/`, `/prayer/`, and all subpages to confirm fonts, transparent/solid headers, and forms render smoothly.
     - Deactivate and delete the Duplicator plugin when migration is complete.


Hero refinement (20 October 2026): the larger headline is 'Know God. Grow together.' The compact rail includes Children / 兒童 at 10:00 AM and becomes a two-by-two grid on phones. All four entries and the location row fit the first screen at the six checked sizes (360–1440px). Current screenshots: screenshots/home-hero-refined-1440.jpg, home-hero-refined-768.jpg and home-hero-refined-390.jpg. This remains local only.

Identity and Beliefs pass (20 October 2026): Home shows only the English church name, Chinese name, slogan and button above the rail. About and Beliefs share typography and a four-topic navigation strip; full statements remain separate. The approved Topic reader is applied locally in English, Traditional Chinese and Simplified Chinese. Native Nested Tabs contain all eight original statements and Scripture references; a content-neutral navigation enhancement provides the phone selector, desktop index-heading position and Previous/Next behavior. About menu: Our story → Our mission → Our pastoral team → What we believe. Directions remain under Sundays. Sources: `scripts/build-beliefs-reader.mjs`, `scripts/apply-beliefs-reader.php`, `content-source/beliefs-core-*.json`; backups: `content-source/before-beliefs-reader/`. Older connected-accordion scripts are superseded. The Quiet accordion alternative was not applied. Checks: twelve responsive views (360–1440px), all eleven language pages passed native-content validation, keyboard/mobile/adjacent-topic navigation and same-language About links verified. All changes remain local.

Latest organisation (20 October 2026): the full eight-topic Beliefs reader is merged into About#beliefs in all three locales, superseding the separate-page notes above. Former Beliefs URLs redirect to the appropriate About section. Current helpers: merge-beliefs-about.mjs / apply-about-merged-beliefs.php. The menu now groups Sundays, Sermons, Church life, About, Give and the single language switcher. Language-labelled sermon archives replace the old congregation placeholder links; services and directions stay under Sundays. Newsletter is under Church life. Menu helper: reorganise-primary-menu.php; backups: before-about-beliefs-merge/ and before-menu-grouping/. Local changes only.

Pre-migration review (20 October 2026): see MIGRATION-MCP-READINESS.md. Ready for staging backup/restore; not yet fully editable through the official Elementor MCP. Atomic Editor is on, but the existing design is classic V3. MCP access remains off. Site-relative native URL fields were normalized for serialized-aware subdirectory migration; theme fallback ZIP is hello-mpcbc-migration.zip. Use a new full-site backup after this review.

Atomic conversion completed locally (20 October 2026): 22 Elementor documents / 1,636 Atomic elements, with only four shortcode placements for three existing Fluent Forms retained under the Free-only constraint. Header/menu remains native WordPress. All source text is preserved. Native MCP structure reads and a draft-edit test succeeded; network MCP is still disabled pending online restore. Native hero controls, reader navigation, language routing, footer CSS and responsive layouts were checked. Use current WordPress exports and a NEW full backup. The refreshed hello-mpcbc-migration.zip contains the final child theme. Pre-Atomic builders are historical. See MIGRATION-MCP-READINESS.md.

Home/footer consolidated: edit Home (21) once for all reading languages and MPCBC – Footer (16) once for the whole site. Traditional and Simplified wording are separately editable native Atomic text, preserved from the former documents. Existing About/Give language pages remain separate. Legacy Simplified Home 511 redirects to the shared Home; footer 513 is archived as a draft. Current totals: 22 maintained documents, 1,732 Atomic elements, four approved form placements. See HOME-FOOTER-EDITING.md and MIGRATION-MCP-READINESS.md; these notes supersede the separate Home-copy workflow above. Take a new full files/database backup after this update.
