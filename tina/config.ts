import { defineConfig } from 'tinacms';

/**
 * TinaCMS config — the site's only CMS, with visual click-on-the-page
 * editing.
 *
 * Every collection points at the same files Astro renders, under
 * src/content/. There is no second tree: what you edit here is what
 * `getCollection()` reads and what the build publishes.
 *
 * This replaced a Keystatic/Tina pair that each kept their own copy of
 * the content. The split was a constant source of "I changed it and
 * nothing happened", because routing and the menu only ever read one of
 * the two. Formats are chosen so a single tree serves both Tina and
 * Astro: markdown for prose collections, JSON for pages and settings.
 *
 * Local mode needs no account. Run the two servers separately —
 * `npx tinacms dev` then `npm run dev` — because `tinacms dev -c` kills
 * Astro after 30s, which is less time than a cold start takes here.
 * Tina Cloud (2 free seats) needs clientId + token in .env.
 */

const CONGREGATIONS = [
  { label: 'All congregations', value: 'all' },
  { label: 'English', value: 'english' },
  { label: 'Cantonese 粵語', value: 'cantonese' },
  { label: 'Mandarin 國語', value: 'mandarin' },
];

// Every piece in a flexible section carries this. A section is a
// two-column grid: full-width pieces span both columns, so a run of them
// reads as an ordinary stack, and two half-width pieces in a row sit side
// by side. That is what lets one section be a heading and a paragraph
// across the top with two columns underneath, rather than forcing the
// whole section to be one shape or the other.
const widthField = {
  type: 'string',
  name: 'width',
  label: 'Width',
  description:
    'Full width sits on its own line. Two Half width pieces in a row sit side by side.',
  options: [
    { label: 'Full width', value: 'full' },
    { label: 'Half width', value: 'half' },
  ],
};

// The component library for the "Flexible section" escape hatch below —
// small pieces an editor can stack in any order, instead of choosing one
// of the nine ready-made shapes. Blocks.astro renders these too, and
// needs its own name list to tell them apart from the section templates.
const flexibleItems = [
  {
    name: 'heading',
    label: 'Heading',
    fields: [{ type: 'string', name: 'text', label: 'Text' }, widthField],
  },
  {
    name: 'paragraph',
    label: 'Paragraph',
    fields: [
      { type: 'string', name: 'text', label: 'Text', ui: { component: 'textarea' } },
      widthField,
    ],
  },
  {
    name: 'image',
    label: 'Image',
    fields: [
      { type: 'image', name: 'src', label: 'Image' },
      { type: 'string', name: 'alt', label: 'Describe the image' },
      widthField,
    ],
  },
  {
    name: 'button',
    label: 'Button',
    fields: [
      { type: 'string', name: 'text', label: 'Button text' },
      { type: 'string', name: 'url', label: 'Link' },
      widthField,
    ],
  },
  {
    name: 'video',
    label: 'Video',
    fields: [
      {
        type: 'string', name: 'url', label: 'Video link or ID',
        description: 'Paste the YouTube web address, or just the part after v= in it.',
      },
      widthField,
    ],
  },
];

// The section library. Adding a template here makes it available on
// every page at once, and Blocks.astro is where it gets rendered — the
// two lists have to stay in step.
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
      // Sections are a flat list - one cannot be dropped inside another -
      // so a button next to the text had to be a field here rather than a
      // Buttons section nested in this one. Optional: no text, no button.
      {
        type: 'string', name: 'ctaText', label: 'Button text',
        description: 'Leave blank and no button appears.',
      },
      { type: 'string', name: 'ctaLink', label: 'Button link' },
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
  {
    // An escape hatch, not a replacement — the nine sections above cover
    // ordinary editing and are left exactly as they are. This one is for
    // the rarer case where none of those shapes fit: it lets an editor
    // stack a heading, a paragraph, an image, a button and a video in
    // any order and any combination.
    name: 'flexible',
    label: 'Flexible section',
    fields: [
      // No section-wide "stacked or two columns" choice: each piece
      // carries its own Width instead, so one section can be a heading
      // and a paragraph across the top with two columns beneath it.
      {
        type: 'object', name: 'items', label: 'Contents', list: true,
        description: 'Add pieces in whatever order you like — heading, paragraph, image, button, or video.',
        templates: flexibleItems,
        // Without this the sidebar list reads "Item 1, Item 2, Item 3".
        // Falls back to the kind of piece if it has no text yet, and
        // never throws on a blank or half-filled-in row. Half-width
        // pieces are marked, because which ones pair up is otherwise
        // invisible in a flat list.
        ui: {
          itemProps: (item) => {
            const kind = flexibleItems.find((t) => t.name === item?._template)?.label
              || item?._template || 'Item';
            const text = item?.text || item?.alt || item?.url || '';
            const half = item?.width === 'half' ? ' (half)' : '';
            return { label: (text ? `${kind}: ${text}` : kind) + half };
          },
        },
      },
    ],
  },
];

// Names each section in the sidebar list by its kind and its heading, e.g.
// "Video: A look at our 50th anniversary", instead of just "Video".
const sectionLabel = (item) => {
  const kind = pageBlocks.find((t) => t.name === item?._template)?.label
    || item?._template || 'Section';
  return { label: item?.heading ? `${kind}: ${item.heading}` : kind };
};

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
        path: 'src/content/pages',
        format: 'json',
        ui: {
          // Click-to-edit needs to know where the page lives on the site.
          router: ({ document }) => `/${document._sys.filename}`,
        },
        fields: [
          {
            // Labelled "Section" until it was pointed out that the word
            // reads as "which part of the menu is this under?" - which is
            // the next field down, not this one. Same wording as
            // announcements and events now, since it is the same field.
            type: 'string', name: 'congregation', label: 'Who is this for?',
            description:
              'Which congregation this page is aimed at. This does NOT affect ' +
              'the menu - use "Where in the menu" below for that.',
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
          {
            type: 'string', name: 'menuParent', label: 'Where in the menu',
            description:
              'Choose "Not in the menu" to keep the page reachable by link only. ' +
              'Picking one of the menu items nests this page under it, which turns ' +
              'that item into a dropdown.',
            options: [
              { label: 'Not in the menu', value: 'none' },
              { label: 'Top level', value: 'top' },
              { label: 'Under "About 關於我們"', value: 'about' },
              { label: 'Under "Services 崇拜"', value: 'services' },
              { label: 'Under "Newsletter 通訊"', value: 'newsletter' },
              { label: 'Under "Offering 奉獻"', value: 'offering' },
            ],
          },
          {
            type: 'string', name: 'menuLabel', label: 'Menu label',
            description: 'Leave blank to use the page name.',
          },
          {
            type: 'string', name: 'menuLabelZh', label: 'Menu label 中文',
            description: 'Shown beside the English label, the way the other menu items are.',
          },
          {
            type: 'number', name: 'menuOrder', label: 'Menu order',
            description: 'Lower numbers come first among pages in the same place.',
          },
          {
            type: 'object', name: 'sections', label: 'Page sections',
            list: true,
            templates: pageBlocks,
            ui: { itemProps: sectionLabel },
          },
        ],
      },

      {
        name: 'homepage',
        label: 'Homepage',
        path: 'src/content/settings',
        format: 'json',
        match: { include: 'homepage' },
        ui: {
          allowedActions: { create: false, delete: false },
          router: () => '/',
        },
        // In the order they appear on the page. The designed sections are
        // groups rather than blocks: they are always there and cannot be
        // moved, only reworded. A blank field falls back to the wording
        // built into its component (src/components/home/).
        fields: [
          {
            type: 'object', name: 'hero', label: 'Hero section',
            description: 'The big picture at the top, with the headline.',
            fields: [
              { type: 'string', name: 'headline', label: 'Headline', required: true, ui: { component: 'textarea' } },
              {
                type: 'string', name: 'headlineZh', label: 'Headline in Chinese',
                description: 'Shown under the English headline.',
              },
              { type: 'string', name: 'lede', label: 'Opening paragraph', ui: { component: 'textarea' } },
              { type: 'string', name: 'ctaText', label: 'Button text' },
              { type: 'string', name: 'ctaLink', label: 'Button link' },
              { type: 'string', name: 'video', label: 'Background video URL' },
              {
                type: 'image', name: 'image', label: 'Background image',
                description: 'Used when there is no video, and while the video loads.',
              },
            ],
          },
          {
            type: 'object', name: 'serviceTimes', label: 'Service times board',
            description:
              'The three service cards. Times and live links update from YouTube ' +
              'by themselves; these are the words around them.',
            fields: [
              { type: 'string', name: 'label', label: 'Small label' },
              { type: 'string', name: 'labelZh', label: 'Small label in Chinese' },
              { type: 'string', name: 'heading', label: 'Heading' },
              { type: 'string', name: 'intro', label: 'Intro line', ui: { component: 'textarea' } },
              { type: 'string', name: 'englishBlurb', label: 'English card text', ui: { component: 'textarea' } },
              { type: 'string', name: 'cantoneseBlurb', label: 'Cantonese card text', ui: { component: 'textarea' } },
              { type: 'string', name: 'mandarinBlurb', label: 'Mandarin card text', ui: { component: 'textarea' } },
              { type: 'string', name: 'footerNote', label: 'Note under the cards', ui: { component: 'textarea' } },
              { type: 'string', name: 'youtubeText', label: 'YouTube button text' },
              { type: 'string', name: 'youtubeLink', label: 'YouTube button link' },
            ],
          },
          {
            type: 'object', name: 'announcementsSection', label: 'Announcements heading',
            description: 'The announcements themselves are under Announcements.',
            fields: [
              { type: 'string', name: 'label', label: 'Small label' },
              { type: 'string', name: 'labelZh', label: 'Small label in Chinese' },
            ],
          },
          {
            type: 'object', name: 'pastorNote', label: 'A note from our pastor',
            fields: [
              { type: 'image', name: 'portrait', label: 'Portrait photo', description: 'Shown as a circle.' },
              { type: 'string', name: 'name', label: 'Name' },
              { type: 'string', name: 'nameZh', label: 'Name in Chinese' },
              { type: 'string', name: 'role', label: 'Role' },
              { type: 'string', name: 'label', label: 'Small label' },
              { type: 'string', name: 'labelZh', label: 'Small label in Chinese' },
              { type: 'string', name: 'heading', label: 'Heading', ui: { component: 'textarea' } },
              { type: 'string', name: 'body', label: 'Text', ui: { component: 'textarea' } },
              { type: 'string', name: 'bodyZh', label: 'Text in Chinese', ui: { component: 'textarea' } },
              { type: 'string', name: 'ctaText', label: 'Button text', description: 'Leave blank and no button appears.' },
              { type: 'string', name: 'ctaLink', label: 'Button link' },
            ],
          },
          {
            type: 'object', name: 'children', label: 'Children & families',
            fields: [
              { type: 'string', name: 'heading', label: 'Heading' },
              { type: 'string', name: 'headingZh', label: 'Heading in Chinese' },
              { type: 'string', name: 'body', label: 'Text', ui: { component: 'textarea' } },
              { type: 'string', name: 'note', label: 'Smaller note', ui: { component: 'textarea' } },
              { type: 'string', name: 'ctaText', label: 'Button text', description: 'Leave blank and no button appears.' },
              { type: 'string', name: 'ctaTextZh', label: 'Button text in Chinese' },
              { type: 'string', name: 'ctaLink', label: 'Button link' },
              {
                type: 'object', name: 'photos', label: 'Photos', list: true,
                description: 'Up to three: one wide, then two square. Empty spots show a placeholder.',
                ui: { max: 3, itemProps: (item) => ({ label: item?.alt || 'Photo' }) },
                fields: [
                  { type: 'image', name: 'src', label: 'Photo' },
                  { type: 'string', name: 'alt', label: 'Describe the photo', description: 'For people using a screen reader.' },
                ],
              },
              {
                type: 'object', name: 'groups', label: 'Age groups', list: true,
                ui: { itemProps: (item) => ({ label: item?.title || 'Group' }) },
                fields: [
                  { type: 'string', name: 'title', label: 'Title' },
                  { type: 'string', name: 'body', label: 'Details' },
                ],
              },
            ],
          },
          {
            type: 'object', name: 'upcomingEvents', label: 'Coming up (upcoming events)',
            description: 'The events themselves are under Events.',
            fields: [
              { type: 'string', name: 'label', label: 'Small label' },
              { type: 'string', name: 'labelZh', label: 'Small label in Chinese' },
              { type: 'string', name: 'heading', label: 'Heading' },
              { type: 'string', name: 'note', label: 'Note', ui: { component: 'textarea' } },
              { type: 'string', name: 'ctaText', label: 'Button text' },
              { type: 'string', name: 'ctaLink', label: 'Button link' },
              { type: 'string', name: 'emptyText', label: 'Shown when there are no events' },
            ],
          },
          {
            type: 'object', name: 'sections', label: 'More sections (recent sermons, videos, photos…)',
            description: 'Shown below "Coming up". Add, remove and reorder these freely.',
            list: true,
            templates: pageBlocks,
            ui: { itemProps: sectionLabel },
          },
        ],
      },

      {
        name: 'sermons',
        label: 'Sermons',
        // Written by scripts/sync-sermons.mjs from YouTube. Editing here
        // is for corrections and for hiding a video — creating one by
        // hand would just be overwritten by the next sync.
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
        path: 'src/content/announcements',
        format: 'md',
        fields: [
          { type: 'string', name: 'title', label: 'Title', isTitle: true, required: true },
          {
            type: 'string', name: 'titleZh', label: 'Title in Chinese',
            description: 'Shown under the English title. Leave blank to omit it.',
          },
          {
            type: 'string', name: 'kicker', label: 'Kicker',
            description: 'Small label above the title, e.g. "Save the date · October 2026".',
          },
          { type: 'string', name: 'congregation', label: 'Who is this for?', options: CONGREGATIONS },
          {
            type: 'datetime', name: 'removeAfter', label: 'Remove after', required: true,
            description: 'Disappears from the site on this date.',
          },
          { type: 'boolean', name: 'pinned', label: 'Pin to top' },
          {
            type: 'string', name: 'summary', label: 'Summary',
            description: 'The paragraph shown on the homepage. Two or three sentences.',
            ui: { component: 'textarea' },
          },
          {
            type: 'string', name: 'ctaText', label: 'Button text',
            description: 'Leave blank and no button appears.',
          },
          { type: 'string', name: 'ctaLink', label: 'Button link' },
          {
            type: 'object', name: 'meta', label: 'Details', list: true,
            description: 'Up to three short rows listed beside the announcement.',
            ui: { itemProps: (item) => ({ label: item?.label }) },
            fields: [
              { type: 'string', name: 'label', label: 'Label' },
              { type: 'string', name: 'value', label: 'Value' },
            ],
          },
          {
            type: 'string', name: 'note', label: 'Handwritten note',
            description: 'A short aside in the handwritten face. Optional.',
          },
          { type: 'rich-text', name: 'body', label: 'Full details', isBody: true },
        ],
      },

      {
        name: 'events',
        label: 'Events',
        path: 'src/content/events',
        format: 'md',
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
