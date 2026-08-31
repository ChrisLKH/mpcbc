import { defineConfig } from 'tinacms';

/**
 * TinaCMS config — visual, click-on-the-page editing.
 *
 * Tina reads its own copy of the content under src/content/tina/, because
 * the two editors store block lists in shapes neither can read:
 *
 *   Keystatic  { discriminant: 'textBlock', value: { heading, body } }
 *   Tina       { _template: 'textBlock', heading, body }
 *
 * Tina's `templates` discriminator is hardwired to a flat `_template` key,
 * so pointing it at Keystatic's files makes every block resolve to the
 * first template with all fields null — the page renders empty with no
 * error. Same story for announcements/events: Keystatic writes .mdoc,
 * which Tina doesn't index at all.
 *
 * A shared tree would mean crippling one editor to match the other's
 * storage format, which would skew the comparison. Each gets its native
 * shape instead; Blocks.astro renders either. `npm run build` publishes
 * from the Keystatic tree — see README.
 *
 * Local mode needs no account:  npm run dev:tina  ->  /admin
 * Tina Cloud (2 free seats) needs clientId + token in .env.
 */

const CONGREGATIONS = [
  { label: 'All congregations', value: 'all' },
  { label: 'English', value: 'english' },
  { label: 'Cantonese 粵語', value: 'cantonese' },
  { label: 'Mandarin 國語', value: 'mandarin' },
];

// The same section library as Keystatic. Adding a template here makes
// it available on every page at once.
const pageBlocks = [
  {
    name: 'textBlock',
    label: 'Text',
    fields: [
      { type: 'string', name: 'heading', label: 'Heading' },
      { type: 'string', name: 'body', label: 'Text', ui: { component: 'textarea' } },
      { type: 'boolean', name: 'tinted', label: 'Shaded background' },
    ],
  },
  {
    name: 'imageText',
    label: 'Image with text',
    fields: [
      { type: 'string', name: 'heading', label: 'Heading' },
      { type: 'string', name: 'body', label: 'Text', ui: { component: 'textarea' } },
      { type: 'image', name: 'image', label: 'Image' },
      { type: 'string', name: 'alt', label: 'Describe the image' },
      {
        type: 'string', name: 'imageSide', label: 'Image on',
        options: [
          { label: 'Left', value: 'left' },
          { label: 'Right', value: 'right' },
        ],
      },
    ],
  },
  {
    name: 'gallery',
    label: 'Photo gallery',
    fields: [
      { type: 'string', name: 'heading', label: 'Heading' },
      {
        type: 'string', name: 'layout', label: 'Layout',
        options: [
          { label: 'Grid', value: 'grid' },
          { label: 'Wide strip', value: 'strip' },
        ],
      },
      {
        type: 'object', name: 'photos', label: 'Photos', list: true,
        ui: { itemProps: (item) => ({ label: item?.alt || 'Photo' }) },
        fields: [
          { type: 'image', name: 'image', label: 'Photo', required: true },
          {
            type: 'string', name: 'alt', label: 'Describe the photo', required: true,
            description: 'For people using a screen reader.',
          },
          { type: 'string', name: 'caption', label: 'Caption' },
        ],
      },
    ],
  },
  {
    name: 'videoEmbed',
    label: 'Video',
    fields: [
      { type: 'string', name: 'heading', label: 'Heading' },
      {
        type: 'string', name: 'youtubeId', label: 'YouTube video ID', required: true,
        description: 'The part after v= in the URL.',
      },
      { type: 'string', name: 'caption', label: 'Caption', ui: { component: 'textarea' } },
    ],
  },
  {
    name: 'buttons',
    label: 'Buttons',
    fields: [
      { type: 'string', name: 'heading', label: 'Heading' },
      {
        type: 'object', name: 'links', label: 'Buttons', list: true,
        ui: { itemProps: (item) => ({ label: item?.text || 'Button' }) },
        fields: [
          { type: 'string', name: 'text', label: 'Button text' },
          { type: 'string', name: 'url', label: 'Link' },
        ],
      },
    ],
  },
  {
    name: 'serviceBoard',
    label: 'Service times board',
    fields: [
      { type: 'string', name: 'heading', label: 'Heading' },
      { type: 'string', name: 'intro', label: 'Intro line', ui: { component: 'textarea' } },
    ],
  },
  {
    name: 'announcements',
    label: 'This week',
    fields: [
      { type: 'string', name: 'heading', label: 'Heading' },
      { type: 'number', name: 'limit', label: 'How many to show' },
    ],
  },
  {
    name: 'recentSermons',
    label: 'Recent sermons',
    fields: [
      { type: 'string', name: 'heading', label: 'Heading' },
      { type: 'number', name: 'limit', label: 'How many to show' },
    ],
  },
  {
    name: 'facebookAlbum',
    label: 'Facebook album link',
    fields: [
      { type: 'string', name: 'heading', label: 'Heading' },
      { type: 'string', name: 'body', label: 'Text', ui: { component: 'textarea' } },
      { type: 'string', name: 'url', label: 'Facebook album URL' },
      { type: 'string', name: 'linkText', label: 'Link text' },
    ],
  },
];

export default defineConfig({
  branch: process.env.TINA_BRANCH || 'main',
  clientId: process.env.TINA_CLIENT_ID || null,
  token: process.env.TINA_TOKEN || null,

  build: {
    outputFolder: 'admin',
    publicFolder: 'public',
  },
  media: {
    tina: {
      mediaRoot: 'images',
      publicFolder: 'public',
    },
  },

  schema: {
    collections: [
      {
        name: 'pages',
        label: 'Pages',
        path: 'src/content/tina/pages',
        format: 'json',
        ui: {
          // Click-to-edit needs to know where the page lives on the site.
          router: ({ document }) => `/${document._sys.filename}`,
        },
        fields: [
          {
            type: 'string', name: 'congregation', label: 'Section',
            options: CONGREGATIONS,
          },
          {
            type: 'string', name: 'language', label: 'Language',
            options: [
              { label: 'English', value: 'en' },
              { label: 'Traditional Chinese 繁體', value: 'zh-Hant' },
              { label: 'Simplified Chinese 简体', value: 'zh-Hans' },
            ],
          },
          {
            type: 'string', name: 'description', label: 'Search description',
            ui: { component: 'textarea' },
            description: 'Shown in Google results. Around 150 characters.',
          },
          { type: 'boolean', name: 'showInMenu', label: 'Show in the menu' },
          {
            type: 'object', name: 'sections', label: 'Page sections',
            list: true,
            templates: pageBlocks,
          },
        ],
      },

      {
        name: 'homepage',
        label: 'Homepage',
        path: 'src/content/tina/settings',
        format: 'json',
        match: { include: 'homepage' },
        ui: {
          allowedActions: { create: false, delete: false },
          router: () => '/',
        },
        fields: [
          { type: 'string', name: 'heroHeadline', label: 'Headline', required: true, ui: { component: 'textarea' } },
          { type: 'string', name: 'heroLede', label: 'Opening paragraph', ui: { component: 'textarea' } },
          { type: 'string', name: 'heroCtaText', label: 'Button text' },
          { type: 'string', name: 'heroCtaLink', label: 'Button link' },
          { type: 'string', name: 'heroVideo', label: 'Hero video URL' },
          { type: 'image', name: 'heroImage', label: 'Hero image' },
          {
            type: 'object', name: 'sections', label: 'Page sections',
            list: true,
            templates: pageBlocks,
          },
        ],
      },

      {
        name: 'sermons',
        label: 'Sermons',
        // Flat JSON with no blocks, so both editors read it as-is. Shared
        // on purpose: sync-sermons.mjs writes here.
        path: 'src/content/sermons',
        format: 'json',
        ui: { allowedActions: { create: false } },
        fields: [
          { type: 'string', name: 'title', label: 'Title', isTitle: true, required: true },
          { type: 'string', name: 'congregation', label: 'Congregation', options: CONGREGATIONS },
          { type: 'string', name: 'date', label: 'Date' },
          { type: 'string', name: 'speaker', label: 'Speaker', description: 'Optional.' },
          { type: 'string', name: 'scripture', label: 'Scripture', description: 'Optional.' },
          { type: 'string', name: 'series', label: 'Series', description: 'Optional.' },
          { type: 'string', name: 'notes', label: 'Notes', ui: { component: 'textarea' } },
          { type: 'boolean', name: 'hide', label: 'Hide from website' },
        ],
      },

      {
        name: 'announcements',
        label: 'Announcements',
        path: 'src/content/tina/announcements',
        format: 'mdx',
        fields: [
          { type: 'string', name: 'title', label: 'Title', isTitle: true, required: true },
          { type: 'string', name: 'congregation', label: 'Who is this for?', options: CONGREGATIONS },
          {
            type: 'datetime', name: 'removeAfter', label: 'Remove after', required: true,
            description: 'Disappears from the site on this date.',
          },
          { type: 'boolean', name: 'pinned', label: 'Pin to top' },
          { type: 'rich-text', name: 'body', label: 'Details', isBody: true },
        ],
      },

      {
        name: 'events',
        label: 'Events',
        path: 'src/content/tina/events',
        format: 'mdx',
        fields: [
          { type: 'string', name: 'title', label: 'Event name', isTitle: true, required: true },
          { type: 'string', name: 'congregation', label: 'Who is this for?', options: CONGREGATIONS },
          { type: 'datetime', name: 'startDate', label: 'Date', required: true },
          { type: 'string', name: 'startTime', label: 'Time', description: 'e.g. 7:30 PM' },
          { type: 'string', name: 'location', label: 'Location' },
          { type: 'image', name: 'image', label: 'Photo' },
          { type: 'rich-text', name: 'body', label: 'Description', isBody: true },
        ],
      },
    ],
  },
});
