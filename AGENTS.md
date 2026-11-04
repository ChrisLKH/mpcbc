# Working in this repo

This repo holds the MPCBC church WordPress site (formerly "design A"). The repo
root is the site: local site `mpcbc.local`, child theme `hello-mpcbc`, plugin
`mpcbc-sermons`.

## Layout

- `hello-mpcbc/` — the Hello Elementor child theme.
- `plugins/mpcbc-sermons/` — YouTube sermon sync, live slips and sermon archive (`[mpcbc_live]`, `[mpcbc_sermons]`).
- `scripts/` — build/apply/inspect scripts (run through WP-CLI `eval-file` with Local's PHP).
- `elementor/`, `content-source/`, `fluent-forms/`, `assets/`, `placeholders/`, `screenshots/` — exports, sources and backups.
- `taste-mockup/` — tasteskill design mockups (homepage, About and Sermon Archive options).
- `hero-video/` — Remotion project that renders the homepage hero loop MP4. Footage and renders are git-ignored; see its README.
- `archive/` — retired work, reference only: `astro-site/` (old Astro site) and `wordpress-design-b/` (design B, Local site `mpcbc-2.local`). Do not build, run or edit it.
- `HANDOFF.md` is the build brief (rules, stack, acceptance checks). Read it before changing the site.

## Rules

- Elementor **free** only: native containers and widgets, no HTML layouts or
  inline styles inside widgets, colours and fonts from Global Colours / Fonts.
- Plugins: Elementor, Essential Addons for Elementor (Fancy Text only), Fluent
  Forms, Duplicator (deleted after migration), and our own `mpcbc-sermons`. Ask before adding any other.
- Child theme stays tiny: fonts, header CSS, menu description line, `footer.php`
  rendering the Elementor footer template. No page content in the theme.
- Unknown facts stay as `[bracketed placeholders]`. Invent nothing.
- `hello-mpcbc/` and `plugins/mpcbc-sermons/` are linked into the Local site with `mklink /J`;
  moving either folder means re-creating the link.
- Never commit Duplicator packages, `.env`, Local site files or API keys (the YouTube key lives only in `wp-config.php`).
- tasteskill (`.agents/`, `skills-lock.json`) stays local and out of git.
- Don't push to GitHub unless the user asks.
