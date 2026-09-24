/**
 * Shared setup for the two trial editors, /admin-decap and /admin-sveltia.
 *
 * Both are the same kind of CMS (Sveltia is a rewrite of Decap that reads
 * the same config), so everything except the login lives here once. Each
 * admin page supplies its own `backend` and calls MPCBC_CMS.start().
 *
 * These edit the same files Tina does. The field list below mirrors
 * tina/config.ts and must stay in step with it and with
 * src/content.config.ts — a field one of them does not know about is
 * either dropped or rejected.
 *
 * Two differences from Tina that are deliberate:
 *
 *   - Blocks keep Tina's `_template` key (typeKey below), so a page saved
 *     here reads back correctly in Tina and in Blocks.astro.
 *   - Dates are stored as plain YYYY-MM-DD. Tina writes a full UTC
 *     timestamp at midnight; both parse to the same instant.
 */
(function () {
  // Decap marks every field required unless told otherwise. Tina and the
  // Zod schemas treat almost everything as optional, so the helpers flip
  // the default and the few required fields say so.
  const opt = (field) => ({ required: false, ...field });

  const str = (name, label, extra = {}) => opt({ name, label, widget: 'string', ...extra });
  const text = (name, label, extra = {}) => opt({ name, label, widget: 'text', ...extra });
  const num = (name, label, extra = {}) => opt({ name, label, widget: 'number', value_type: 'int', ...extra });
  const bool = (name, label, extra = {}) => opt({ name, label, widget: 'boolean', default: false, ...extra });
  const image = (name, label, extra = {}) => opt({ name, label, widget: 'image', ...extra });
  const select = (name, label, options, extra = {}) =>
    opt({ name, label, widget: 'select', options, ...extra });
  const date = (name, label, extra = {}) =>
    opt({
      name, label, widget: 'datetime',
      format: 'YYYY-MM-DD', date_format: 'YYYY-MM-DD', time_format: false,
      ...extra,
    });

  const CONGREGATIONS = [
    { label: 'All congregations', value: 'all' },
    { label: 'English', value: 'english' },
    { label: 'Cantonese 粵語', value: 'cantonese' },
    { label: 'Mandarin 國語', value: 'mandarin' },
  ];

  const width = select('width', 'Width', [
    { label: 'Full width', value: 'full' },
    { label: 'Half width', value: 'half' },
  ], {
    default: 'full',
    hint: 'Full width sits on its own line. Two Half width pieces in a row sit side by side.',
  });

  // flexibleItems in tina/config.ts
  const flexibleItems = [
    { name: 'heading', label: 'Heading', summary: 'Heading: {{fields.text}}', fields: [str('text', 'Text'), width] },
    { name: 'paragraph', label: 'Paragraph', summary: 'Paragraph: {{fields.text}}', fields: [text('text', 'Text'), width] },
    {
      name: 'image', label: 'Image', summary: 'Image: {{fields.alt}}',
      fields: [image('src', 'Image'), str('alt', 'Describe the image'), width],
    },
    {
      name: 'button', label: 'Button', summary: 'Button: {{fields.text}}',
      fields: [str('text', 'Button text'), str('url', 'Link'), width],
    },
    {
      name: 'video', label: 'Video', summary: 'Video: {{fields.url}}',
      fields: [
        str('url', 'Video link or ID', {
          hint: 'Paste the YouTube web address, or just the part after v= in it.',
        }),
        width,
      ],
    },
  ].map((t) => ({ ...t, widget: 'object' }));

  // pageBlocks in tina/config.ts
  const pageBlocks = [
    {
      name: 'textBlock', label: 'Text', summary: 'Text: {{fields.heading}}',
      fields: [str('heading', 'Heading'), text('body', 'Text'), bool('tinted', 'Shaded background')],
    },
    {
      name: 'imageText', label: 'Image with text', summary: 'Image with text: {{fields.heading}}',
      fields: [
        str('heading', 'Heading'),
        text('body', 'Text'),
        image('image', 'Image'),
        str('alt', 'Describe the image'),
        select('imageSide', 'Image on', [
          { label: 'Left', value: 'left' },
          { label: 'Right', value: 'right' },
        ]),
        str('ctaText', 'Button text', { hint: 'Leave blank and no button appears.' }),
        str('ctaLink', 'Button link'),
      ],
    },
    {
      name: 'gallery', label: 'Photo gallery', summary: 'Photo gallery: {{fields.heading}}',
      fields: [
        str('heading', 'Heading'),
        select('layout', 'Layout', [
          { label: 'Grid', value: 'grid' },
          { label: 'Wide strip', value: 'strip' },
        ], { default: 'grid' }),
        opt({
          name: 'photos', label: 'Photos', widget: 'list', summary: '{{fields.alt}}',
          fields: [
            image('image', 'Photo', { required: true }),
            str('alt', 'Describe the photo', { required: true, hint: 'For people using a screen reader.' }),
            str('caption', 'Caption'),
          ],
        }),
      ],
    },
    {
      name: 'videoEmbed', label: 'Video', summary: 'Video: {{fields.heading}}',
      fields: [
        str('heading', 'Heading'),
        str('youtubeId', 'YouTube video ID', { required: true, hint: 'The part after v= in the URL.' }),
        text('caption', 'Caption'),
      ],
    },
    {
      name: 'buttons', label: 'Buttons', summary: 'Buttons: {{fields.heading}}',
      fields: [
        str('heading', 'Heading'),
        opt({
          name: 'links', label: 'Buttons', widget: 'list', summary: '{{fields.text}}',
          fields: [str('text', 'Button text'), str('url', 'Link')],
        }),
      ],
    },
    {
      name: 'serviceBoard', label: 'Service times board', summary: 'Service times board',
      fields: [str('heading', 'Heading'), text('intro', 'Intro line')],
    },
    {
      name: 'announcements', label: 'This week', summary: 'This week: {{fields.heading}}',
      fields: [str('heading', 'Heading'), num('limit', 'How many to show', { default: 3 })],
    },
    {
      name: 'recentSermons', label: 'Recent sermons', summary: 'Recent sermons: {{fields.heading}}',
      fields: [str('heading', 'Heading'), num('limit', 'How many to show', { default: 3 })],
    },
    {
      name: 'facebookAlbum', label: 'Facebook album link', summary: 'Facebook album: {{fields.heading}}',
      fields: [
        str('heading', 'Heading'),
        text('body', 'Text'),
        str('url', 'Facebook album URL'),
        str('linkText', 'Link text'),
      ],
    },
    {
      name: 'flexible', label: 'Flexible section', summary: 'Flexible section',
      fields: [
        opt({
          name: 'items', label: 'Contents', widget: 'list',
          hint: 'Add pieces in whatever order you like — heading, paragraph, image, button, or video.',
          typeKey: '_template',
          types: flexibleItems,
        }),
      ],
    },
  ].map((t) => ({ ...t, widget: 'object' }));

  const sections = opt({
    name: 'sections', label: 'Page sections', widget: 'list',
    typeKey: '_template',
    types: pageBlocks,
  });

  const collections = [
    {
      name: 'homepage',
      label: 'Homepage',
      files: [
        {
          name: 'homepage',
          label: 'Homepage',
          file: 'src/content/settings/homepage.json',
          fields: [
            text('heroHeadline', 'Headline', { required: true }),
            text('heroLede', 'Opening paragraph'),
            str('heroCtaText', 'Button text'),
            str('heroCtaLink', 'Button link'),
            str('heroVideo', 'Hero video URL'),
            image('heroImage', 'Hero image'),
            sections,
          ],
        },
      ],
    },
    {
      name: 'pages',
      label: 'Pages',
      folder: 'src/content/pages',
      extension: 'json',
      format: 'json',
      create: true,
      // Pages have no title field — the filename is the web address. The
      // menu label is the nearest thing to a name, so it doubles as one
      // here and becomes the address when a new page is created.
      identifier_field: 'menuLabel',
      slug: '{{slug}}',
      summary: '{{menuLabel}}  ·  /{{filename}}',
      fields: [
        select('congregation', 'Who is this for?', CONGREGATIONS, {
          required: true, default: 'all',
          hint: 'Which congregation this page is aimed at. This does NOT affect the menu - use "Where in the menu" below for that.',
        }),
        select('language', 'Language', [
          { label: 'English', value: 'en' },
          { label: 'Traditional Chinese 繁體', value: 'zh-Hant' },
          { label: 'Simplified Chinese 简体', value: 'zh-Hans' },
        ], { default: 'en' }),
        text('description', 'Search description', { hint: 'Shown in Google results. Around 150 characters.' }),
        select('menuParent', 'Where in the menu', [
          { label: 'Not in the menu', value: 'none' },
          { label: 'Top level', value: 'top' },
          { label: 'Under "About 關於我們"', value: 'about' },
          { label: 'Under "Services 崇拜"', value: 'services' },
          { label: 'Under "Newsletter 通訊"', value: 'newsletter' },
          { label: 'Under "Offering 奉獻"', value: 'offering' },
        ], {
          default: 'none',
          hint: 'Choose "Not in the menu" to keep the page reachable by link only. Picking one of the menu items nests this page under it, which turns that item into a dropdown.',
        }),
        str('menuLabel', 'Page name / menu label', {
          required: true,
          hint: 'Shown in the menu. When you create a new page, this also becomes its web address.',
        }),
        str('menuLabelZh', 'Menu label 中文', {
          hint: 'Shown beside the English label, the way the other menu items are.',
        }),
        num('menuOrder', 'Menu order', { default: 0, hint: 'Lower numbers come first among pages in the same place.' }),
        sections,
      ],
    },
    {
      name: 'announcements',
      label: 'Announcements',
      folder: 'src/content/announcements',
      extension: 'md',
      format: 'frontmatter',
      create: true,
      slug: '{{slug}}',
      summary: '{{title}}  ·  until {{removeAfter}}',
      sortable_fields: ['title', 'removeAfter'],
      fields: [
        str('title', 'Title', { required: true }),
        str('titleZh', 'Title in Chinese', { hint: 'Shown under the English title. Leave blank to omit it.' }),
        str('kicker', 'Kicker', { hint: 'Small label above the title, e.g. "Save the date · October 2026".' }),
        select('congregation', 'Who is this for?', CONGREGATIONS, { required: true, default: 'all' }),
        // Required everywhere: it is how announcements expire, and a
        // missing one fails the content schema and breaks the build.
        date('removeAfter', 'Remove after', { required: true, hint: 'Disappears from the site on this date.' }),
        bool('pinned', 'Pin to top'),
        text('summary', 'Summary', { hint: 'The paragraph shown on the homepage. Two or three sentences.' }),
        str('ctaText', 'Button text', { hint: 'Leave blank and no button appears.' }),
        str('ctaLink', 'Button link'),
        opt({
          name: 'meta', label: 'Details', widget: 'list', max: 3, summary: '{{fields.label}}',
          hint: 'Up to three short rows listed beside the announcement.',
          fields: [str('label', 'Label'), str('value', 'Value')],
        }),
        str('note', 'Handwritten note', { hint: 'A short aside in the handwritten face. Optional.' }),
        opt({ name: 'body', label: 'Full details', widget: 'markdown' }),
      ],
    },
    {
      name: 'events',
      label: 'Events',
      folder: 'src/content/events',
      extension: 'md',
      format: 'frontmatter',
      create: true,
      slug: '{{slug}}',
      summary: '{{startDate}}  ·  {{title}}',
      sortable_fields: ['startDate', 'title'],
      fields: [
        str('title', 'Event name', { required: true }),
        select('congregation', 'Who is this for?', CONGREGATIONS, { required: true, default: 'all' }),
        date('startDate', 'Date', { required: true }),
        str('startTime', 'Time', { hint: 'e.g. 7:30 PM' }),
        str('location', 'Location'),
        image('image', 'Photo'),
        opt({ name: 'body', label: 'Description', widget: 'markdown' }),
      ],
    },
    {
      // Created nightly by scripts/sync-sermons.mjs from YouTube and named
      // after the video ID. Editing here is for adding a speaker or
      // scripture and for hiding a video. No creating (the sync does
      // that) and no deleting (the sync would put it straight back —
      // "Hide from website" is the way to remove one).
      name: 'sermons',
      label: 'Sermons',
      folder: 'src/content/sermons',
      extension: 'json',
      format: 'json',
      create: false,
      delete: false,
      summary: '{{date}}  ·  {{title}}',
      sortable_fields: ['date', 'title'],
      view_filters: [
        { label: 'English', field: 'congregation', pattern: 'english' },
        { label: 'Cantonese 粵語', field: 'congregation', pattern: 'cantonese' },
        { label: 'Mandarin 國語', field: 'congregation', pattern: 'mandarin' },
      ],
      fields: [
        str('title', 'Title', { required: true }),
        select('congregation', 'Congregation', CONGREGATIONS, { required: true }),
        str('date', 'Date', { required: true }),
        str('speaker', 'Speaker', { hint: 'Optional.' }),
        str('scripture', 'Scripture', { hint: 'Optional.' }),
        str('series', 'Series', { hint: 'Optional.' }),
        text('notes', 'Notes'),
        bool('hide', 'Hide from website'),
      ],
    },
  ];

  /**
   * Live preview.
   *
   * The right-hand panel does not imitate the site: it sends the draft to
   * /cms-preview/<kind>, where the real Astro components render it, and
   * shows the HTML that comes back. A section added to Blocks.astro
   * therefore previews correctly with no change here.
   *
   * Requests are debounced so typing does not fire one per keystroke, and
   * a newer draft aborts an older request still in flight.
   */
  const PREVIEW_KINDS = {
    homepage: 'homepage',
    pages: 'page',
    announcements: 'announcement',
    events: 'event',
    sermons: 'sermon',
  };

  // An image uploaded but not yet saved exists only in the browser. The
  // CMS hands out a temporary URL for it through getAsset(); swap those
  // in before sending, or a new photo previews as a broken image.
  function resolveAssets(value, getAsset) {
    if (typeof value === 'string') {
      if (!value.startsWith('/images/') || !getAsset) return value;
      try {
        const asset = getAsset(value);
        const url = asset && (asset.url || String(asset));
        return url && url !== '[object Object]' ? url : value;
      } catch {
        return value;
      }
    }
    if (Array.isArray(value)) return value.map((v) => resolveAssets(v, getAsset));
    if (value && typeof value === 'object') {
      const out = {};
      for (const [k, v] of Object.entries(value)) out[k] = resolveAssets(v, getAsset);
      return out;
    }
    return value;
  }

  function registerPreviews(CMS) {
    const h = window.h;
    const createClass = window.createClass;

    CMS.registerPreviewStyle(
      'html, body { margin: 0; padding: 0; height: 100%; } body > div, body > div > div { height: 100%; }',
      { raw: true },
    );

    const makeTemplate = (kind) =>
      createClass({
        getInitialState() {
          return { html: '', status: 'Loading preview…' };
        },
        componentDidMount() {
          this.schedule();
        },
        componentDidUpdate(prevProps) {
          if (prevProps.entry !== this.props.entry) this.schedule();
        },
        componentWillUnmount() {
          clearTimeout(this.timer);
          if (this.controller) this.controller.abort();
        },
        schedule() {
          clearTimeout(this.timer);
          this.timer = setTimeout(() => this.refresh(), 350);
        },
        async refresh() {
          const entry = this.props.entry;
          const raw = entry && entry.get('data');
          const data = raw && raw.toJS ? raw.toJS() : raw || {};
          const payload = {
            data: resolveAssets(data, this.props.getAsset),
            slug: (entry && entry.get('slug')) || '',
          };

          if (this.controller) this.controller.abort();
          this.controller = new AbortController();

          try {
            const res = await fetch(`/cms-preview/${kind}`, {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify(payload),
              signal: this.controller.signal,
            });
            if (!res.ok) throw new Error(`The preview server answered ${res.status}.`);
            this.setState({ html: await res.text(), status: '' });
          } catch (err) {
            if (err.name === 'AbortError') return;
            this.setState({
              status:
                'The preview could not be loaded. Your changes are not affected — ' +
                'they save as normal. (' + err.message + ')',
            });
          }
        },
        render() {
          if (!this.state.html) {
            return h('p', { style: { font: '15px system-ui', padding: '1.5rem', color: '#555' } }, this.state.status);
          }
          // No allow-scripts: the page's own scripts (menu, carousel,
          // live board polling) stay off in the preview, which is what
          // keeps a rendered draft inert.
          return h('iframe', {
            title: 'Preview',
            srcDoc: this.state.html,
            sandbox: 'allow-same-origin',
            style: { border: 0, width: '100%', height: '100%', minHeight: '100vh', display: 'block' },
          });
        },
      });

    // Folder collections register by collection name, file collections by
    // file name. The homepage's file and collection are both "homepage".
    for (const [name, kind] of Object.entries(PREVIEW_KINDS)) {
      CMS.registerPreviewTemplate(name, makeTemplate(kind));
    }
  }

  window.MPCBC_CMS = {
    // `extra` is for top-level settings only one login needs, such as
    // DecapBridge's `auth` block.
    start(CMS, backend, extra = {}) {
      registerPreviews(CMS);
      CMS.init({
        config: {
          load_config_file: false,
          backend,
          // Whatever address the editor was opened on — the workers.dev
          // URL now, the church domain once it points here.
          site_url: location.origin,
          logo_url: '/images/logo-square.png',
          media_folder: 'public/images',
          public_folder: '/images',
          collections,
          ...extra,
        },
      });
    },
  };
})();
