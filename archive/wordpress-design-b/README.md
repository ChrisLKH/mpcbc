# MPCBC WordPress Website Mockup — Design B (Homepage v5A)

This directory contains the complete WordPress mockup assets, child theme, Elementor free templates, Fluent Forms configurations, responsive screenshots, and deployment deliverables for Monterey Park Chinese Baptist Church (MPCBC) Design B, based strictly on `design-reference/Homepage v5A (full-bleed, fading nav).dc.html` and `HANDOFF.md`.

---

## Deliverables in this Directory

| Directory / File | Description |
|---|---|
| `hello-mpcbc-b/` | Hello Elementor child theme (`style.css`, `functions.php`, `nav.js`, `slider.js`, `footer.php`). |
| `elementor/` | 13 exported Elementor template JSON files (Home full page, 9 individual homepage sections, Footer template, Coming Soon page, hand-build sample). Strictly **0 `style=`** and **0 `<div`** tags. |
| `placeholders/` | 10 clean branded placeholder images generated for pastoral staff, service video thumbnails, and children's activities. |
| `screenshots/` | Complete viewport and full-page responsive screenshots at 1440px (desktop), 768px (tablet), and 390px (mobile). |
| `scripts/` | Helper scripts for site configuration, automated builder, style/div tag audit, 360px viewport overflow test, and interactive volunteer browser test suite. |
| `design-reference/` | Design reference assets for Homepage v5A (`.dc.html`, `support.js`, `image-slot.js`). |
| `HANDOFF.md` | Full build brief, design tokens, Elementor container architecture, and acceptance criteria. |

---

## 1. Non-Technical Volunteer Editing Guide

Welcome! You can easily update headlines, service details, photos, announcements, menus, and site-wide theme colours directly through WordPress without touching any code.

### How to Edit Headlines and Paragraphs
1. In the WordPress admin dashboard, navigate to **Pages › All Pages**.
2. Hover over **Home** and click **Edit with Elementor**.
3. In the visual preview, click directly on any heading (e.g. *"You are welcome here, whichever language you pray in."*) or body paragraph.
4. The editing panel opens on the left:
   - Type or paste your updated text into the **Title** or text area.
   - Note: Keep text as clean plain paragraphs or bold/italic emphasis. Do not paste external HTML or inline styles.
5. Click the green/pink **Update** button at the bottom left of the panel to publish your changes live immediately.

### How to Change or Replace a Photo
1. In the Elementor editor, click on any image you want to change (e.g. Pastor Chan's portrait or a service card thumbnail).
2. In the left panel under **Content › Image**, click on the image preview thumbnail.
3. The WordPress Media Library modal opens:
   - Select an existing photo from the library, or click **Upload Files** to select a JPG/PNG from your computer.
   - Click **Select** at the bottom right.
4. Click **Update** to save.

### How to Edit the Announcements Slider
The Announcements section features an interactive announcement carousel with previous/next arrows and indicator dots:
1. Open **Home** in the Elementor editor.
2. Inside the Elementor editor, all announcement slides are displayed stacked vertically in the editing canvas (`body.elementor-editor-active`), making it simple to edit every slide:
   - Click any announcement heading, date kicker tag, or description text to edit it in the left panel.
   - Click the action button (e.g. *"Sign up for a group"*) and change the link URL.
3. **Adding a new announcement**:
   - Right-click the container of any slide (e.g. Slide 1) and select **Duplicate**.
   - Edit the copy, dates, and link in the newly duplicated slide container.
4. When viewed on the live site, the child theme's `slider.js` automatically turns the stacked slides into an accessible carousel with animated slide transitions, touch swipe, arrow buttons, and indicator dots.
5. Click **Update** to save.

### How to Update Navigation Menus
1. In the WordPress dashboard, go to **Appearance › Menus**.
2. Ensure the **Primary Menu** is selected for editing.
3. Click the dropdown arrow on any menu item (e.g. *Newsletter*, *Prayer*, *Give*):
   - **Navigation Label**: The English title shown in the top navigation bar.
   - **Description**: The Chinese subtitle displayed directly beneath the English link (e.g. `主日崇拜`, `通訊及週刊`, `代禱事項`).
   - **URL**: For homepage section jumps, use `/#sunday`, `/#news`, `/#connect`, or `/#give`.
4. Click **Save Menu** at the bottom or top right.

### How to Manage Forms & Submissions (Connect Card & Prayer Requests)
1. On the homepage, the **Connect & prayer** section contains two tabs inside a white card:
   - Tab 1: **Connect card** (Form ID 3) — for newcomers, contact info, service attended.
   - Tab 2: **Prayer request** (Form ID 4) — for personal prayers and confidential pastoral care.
2. **Viewing Form Submissions**:
   - In the WordPress dashboard, click **Fluent Forms › Submissions**.
   - Select **Connect card** or **Prayer request** from the dropdown.
   - You will see each submitted entry with name, date, email/phone, and notes.
3. **Editing Form Fields or Notification Emails**:
   - Go to **Fluent Forms › All Forms**.
   - Click **Edit** under the form you wish to modify.
   - To change which pastors receive submission emails, go to **Settings & Integrations › Email Notifications** and update the recipient email address.

### How to Change the Site-Wide Primary Colour
1. Open any page in the Elementor editor (e.g. **Pages › Home › Edit with Elementor**).
2. Click the hamburger menu icon (☰) at the top left of the Elementor panel.
3. Click **Site Settings › Global Colors**.
4. Click the color circle next to **Primary** (currently `#943759`).
5. Pick any new color using the visual color picker or enter a hex code.
6. The entire site will instantly restyle: all pill buttons, kicker tags, active announcement indicators, card hover highlights, and the header CTA button update uniformly.
7. Click **Update** to save your new theme color site-wide.

---

## 2. Mock Content to Replace Before Launch

The mockup currently uses placeholder photos and representative church copy based on the design board. Before launching the website publicly, review and replace the following section by section:

### 1. Header & Navigation
- [ ] **Site Logo**: `images/logo.png` is currently loaded. Confirm high-resolution retina assets and favicon.
- [ ] **Phone & Office Details**: Ensure church office hours and main contact lines match current administration.
- [ ] **Unbuilt Subpages**: Any menu links pointing to `/coming-soon/` should be updated to point to dedicated published pages as they are built.

### 2. Hero Section
- [ ] **Hero Photo**: Currently using `church-exterior.jpg`. Replace with high-resolution photo of the congregation or building exterior if a new photo is preferred.
- [ ] **Live Indicator**: The pulsing "LIVE · 9:30 AM" badge is styled via `.mp-pulse`. Update the Sunday service start times if scheduled services change.

### 3. Welcome from Our Pastor
- [ ] **Pastor Portrait**: Currently using placeholder portrait (`pastor-portrait.png`). Replace with an official high-resolution photo of Senior Pastor Rev. Chan.
- [ ] **Pastoral Letter Copy**: Replace sample welcome message with Pastor Chan’s actual personal welcome greeting to visitors.

### 4. Worship Services & Times
- [ ] **Livestream Links**: Service card "Watch live" and "Replay" buttons currently link to `/#sunday`. Update to active YouTube channel or live stream links (`https://youtube.com/@mpcbc/live`).
- [ ] **Room Locations**: Verify room labels (`[Room 101]`, `[Main Sanctuary]`).
- [ ] **Service Card Thumbnails**: Replace placeholder thumbnails (`thumb-cantonese.png`, `thumb-english.png`, `thumb-mandarin.png`) with real photos from recent Cantonese, English, and Mandarin worship gatherings.

### 5. Announcements Carousel
- [ ] **Announcement 1 ("Read the Bible in 365 Days")**: Replace reading plan dates, schedule, and sign-up link.
- [ ] **Announcement 2 ("Annual Members Meeting")**: Replace date (`March 29, 2026`), agenda, and meeting room.
- [ ] **Announcement 3 ("Youth Summer Camp 2026")**: Replace camp dates (`July 17–20, 2026`), fees, and registration form URL.

### 6. Connect Card & Prayer Requests
- [ ] **Notification Emails**: In Fluent Forms, update the default administrator recipient email (`mpcbc@mpcbc.local`) to the actual church office and pastoral prayer team emails.
- [ ] **Privacy Policy / Data Handling**: Confirm consent wording for newcomer contact records.

### 7. Children & Youth Ministry
- [ ] **Ministry Photos**: Currently using placeholders (`kids-room.png`, `kids-craft.png`, `kids-nursery.png`). Replace with genuine photos of children’s Sunday school and youth fellowship (ensure parent media consent).
- [ ] **Safety & Check-in Details**: Confirm nursery check-in procedures, background check badges, and safety guidelines.

### 8. Upcoming Events
- [ ] **Event Dates & Times**: Update Good Friday (`April 3, 2026`), Easter Sunrise (`April 5, 2026`), and Spring Picnic (`April 25, 2026`) to current season church calendar.
- [ ] **RSVP Links**: Update button URLs to active registration pages or forms.

### 9. Pastoral Team & Ministry Leaders
- [ ] **Staff Portraits**: Replace placeholder staff photos (`pastor-cantonese.png`, `pastor-english.png`, `pastor-mandarin.png`) with official headshots of all pastoral team members.
- [ ] **Contact Emails**: Replace mock email links (`[pastor.chan@mpcbc.org]`) with real staff addresses.

### 10. Giving & Stewardship
- [ ] **Zelle Recipient Address**: Replace sample Zelle address (`giving@mpcbc.org`) with the church’s official verified bank Zelle handle.
- [ ] **Online Giving Platform**: Update the "Give online" button to the church’s donation processor (e.g. Subsplash, Tithe.ly, or PayPal).
- [ ] **Mailing Address**: Confirm official mailing address for donation checks.

### 11. Global Footer
- [ ] **Physical Address**: `1234 S Atlantic Blvd, Monterey Park, CA 91754`.
- [ ] **Office Phone**: `(626) 555-0199` — replace with church office phone number.
- [ ] **Social Media URLs**: Replace mock social links with official church Facebook, YouTube, and Instagram profile URLs.

---

## 3. HostGator Migration Guide (For Website Administrator)

The site is packaged using the industry-standard **Duplicator** plugin and is ready for 1-click deployment onto HostGator or any cPanel web host.

### Deliverable Package Files
The migration package has been pre-built and is stored in `wp-content/duplicator-backups/`:
1. **Archive**: `20261010_mpcbc2_1c8a092f46ff71dc5663_20261010081916_archive.zip` (87.0 MB / 91.0 MB uncompressed, contains entire WordPress core, themes, media library, plugins, and SQL database dump).
2. **Installer**: `installer.php` (standalone PHP deployment wizard).

*(Note: Per AGENTS.md rules, Duplicator package archives are never committed to git repositories).*

### Step-by-Step Deployment Instructions

#### Step 1: Prepare HostGator cPanel
1. Log in to your **HostGator cPanel** dashboard.
2. **Target Web Root**:
   - If deploying to the primary domain: use `public_html/`.
   - If deploying to a subdomain or staging directory: create the folder (e.g. `public_html/mpcbc-test/`).
3. **Create MySQL Database**:
   - Go to **cPanel › MySQL Databases**.
   - Create a new database (e.g. `username_mpcbc`).
   - Create a new MySQL user (e.g. `username_mpcbcadmin`) with a strong password.
   - Add the user to the database with **ALL PRIVILEGES**. Note down the Database Name, User, and Password.

#### Step 2: Upload Files
1. Using **cPanel File Manager** or SFTP (FileZilla):
   - Navigate to the destination directory (`public_html/` or subdomain directory).
   - Upload both:
     - `installer.php`
     - `20261010_mpcbc2_1c8a092f46ff71dc5663_20261010081916_archive.zip`
   - Ensure both files sit directly in the root of the target directory.

#### Step 3: Run Duplicator Wizard
1. In your browser, navigate to the installer URL:
   ```
   https://yourdomain.com/installer.php
   ```
   (or `https://subdomain.yourdomain.com/installer.php`).
2. **Step 1 (Archive Validation)**:
   - The installer automatically validates the archive file.
   - Accept the terms and click **Next**.
3. **Step 2 (Database Setup)**:
   - Action: `Empty Database` (or `Create New Database`).
   - Host: `localhost`.
   - Database: Enter HostGator database name.
   - User: Enter HostGator database user.
   - Password: Enter database password.
   - Click **Test Database**. Once validation shows `Success`, click **Next**.
4. **Step 3 (URL & Path Replacement)**:
   - The installer automatically replaces old Local URLs (`http://mpcbc-2.local`) with the new HostGator domain (`https://yourdomain.com`).
   - Click **Next**.
5. **Step 4 (Admin Login & Verification)**:
   - Click **Admin Login**.
   - Log in using your WordPress admin credentials (`mpcbc` / `mpcbc`).

#### Step 4: Post-Migration Housekeeping
1. **Remove Installer Files**:
   - Upon logging in, Duplicator prompts you with a notice to delete installation files.
   - Click **Delete Installer Files** (removes `installer.php` and the archive zip for security).
2. **Regenerate Elementor CSS**:
   - Go to **Elementor › Tools › General**.
   - Click **Regenerate Files & Data**.
3. **Reading Settings**:
   - Once ready for public launch, go to **Settings › Reading** and untick **"Discourage search engines from indexing this site"**.
4. **Remove Duplicator**:
   - Navigate to **Plugins › Installed Plugins**.
   - Deactivate and delete **Duplicator** (no longer needed once migration is complete).

---

## 4. Acceptance Check Verification

Every acceptance requirement from `HANDOFF.md` and user instructions has been verified:

1. **Zero-Tolerance Quality Rule**:
   - `audit-styles-divs.php` executed against all database posts (`_elementor_data`) and all 13 exported Elementor JSON files:
   - **`style=` count: 0**
   - **`<div` count: 0**
2. **Responsive Layout & Mobile Overflow**:
   - Evaluated at exact 360px viewport width: `scrollWidth: 360, clientWidth: 360` (zero horizontal overflow).
   - Responsive screenshots captured at 1440px (desktop), 768px (tablet), and 390px (mobile) for fold and full-page.
3. **Header Fading Navigation**:
   - Transparent with white text over full-bleed hero at scroll position 0.
   - Automatically transitions to frosted white `#FFFFFF` with backdrop blur, dark text, and subtle border at `scrollY >= 40px`.
4. **Interactive Announcements Carousel**:
   - Working slider with dots and next/previous arrow navigation (`slider.js`).
   - Stacks cleanly into editable cards inside the Elementor editor (`body.elementor-editor-active`).
5. **Fluent Forms Integration**:
   - "Connect card" (Form ID 3) and "Prayer request" (Form ID 4) embedded inside Elementor free Tabs widget.
   - "Weekly email" (Form ID 5) integrated in Section 5.
6. **Hero Background**:
   - Clean gradient overlay with `church-exterior.jpg`. Zero MP4/video files used.
7. **Volunteer UI Test Suite**:
   - All 4 tests verified with automated browser test suite (`test-volunteer-full.js`):
     - **Headline**: PASSED (edited and restored)
     - **Photo**: PASSED (edited and restored)
     - **Menu Item**: PASSED (edited and restored)
     - **Primary Global Colour**: PASSED (edited and restored)
