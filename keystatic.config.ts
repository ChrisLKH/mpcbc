import { config, fields, collection, singleton } from '@keystatic/core';

const CONGREGATIONS = [
  { label: 'All congregations', value: 'all' },
  { label: 'English', value: 'english' },
  { label: 'Cantonese 粵語', value: 'cantonese' },
  { label: 'Mandarin 國語', value: 'mandarin' },
];

/**
 * The section library. Any page — the homepage or one someone creates
 * in the editor — is built by stacking these in whatever order they like.
 * Adding a new type here makes it available on every page at once.
 */
const pageBlocks = () =>
  fields.blocks(
    {
      textBlock: {
        label: 'Text',
        schema: fields.object({
          heading: fields.text({ label: 'Heading' }),
          body: fields.text({ label: 'Text', multiline: true }),
          tinted: fields.checkbox({ label: 'Shaded background', defaultValue: false }),
        }),
      },
      imageText: {
        label: 'Image with text',
        schema: fields.object({
          heading: fields.text({ label: 'Heading' }),
          body: fields.text({ label: 'Text', multiline: true }),
          image: fields.image({
            label: 'Image',
            directory: 'public/images/blocks',
            publicPath: '/images/blocks/',
          }),
          alt: fields.text({ label: 'Describe the image' }),
          imageSide: fields.select({
            label: 'Image on',
            options: [
              { label: 'Left', value: 'left' },
              { label: 'Right', value: 'right' },
            ],
            defaultValue: 'left',
          }),
        }),
      },
      gallery: {
        label: 'Photo gallery',
        schema: fields.object({
          heading: fields.text({ label: 'Heading' }),
          layout: fields.select({
            label: 'Layout',
            options: [
              { label: 'Grid', value: 'grid' },
              { label: 'Wide strip', value: 'strip' },
            ],
            defaultValue: 'grid',
          }),
          photos: fields.array(
            fields.object({
              image: fields.image({
                label: 'Photo',
                directory: 'public/images/gallery',
                publicPath: '/images/gallery/',
                validation: { isRequired: true },
              }),
              alt: fields.text({
                label: 'Describe the photo',
                description: 'For people using a screen reader.',
                validation: { isRequired: true },
              }),
              caption: fields.text({ label: 'Caption' }),
            }),
            { label: 'Photos', itemLabel: (props) => props.fields.alt.value || 'Photo' }
          ),
        }),
      },
      videoEmbed: {
        label: 'Video',
        schema: fields.object({
          heading: fields.text({ label: 'Heading' }),
          youtubeId: fields.text({
            label: 'YouTube video ID',
            description: 'The part after v= in the URL.',
            validation: { isRequired: true },
          }),
          caption: fields.text({ label: 'Caption', multiline: true }),
        }),
      },
      buttons: {
        label: 'Buttons',
        schema: fields.object({
          heading: fields.text({ label: 'Heading' }),
          links: fields.array(
            fields.object({
              text: fields.text({ label: 'Button text' }),
              url: fields.text({ label: 'Link' }),
            }),
            { label: 'Buttons', itemLabel: (props) => props.fields.text.value || 'Button' }
          ),
        }),
      },
      serviceBoard: {
        label: 'Service times board',
        schema: fields.object({
          heading: fields.text({ label: 'Heading', defaultValue: 'Join us this Sunday' }),
          intro: fields.text({ label: 'Intro line', multiline: true }),
        }),
      },
      announcements: {
        label: 'This week',
        schema: fields.object({
          heading: fields.text({ label: 'Heading', defaultValue: 'This week' }),
          limit: fields.integer({ label: 'How many to show', defaultValue: 3 }),
        }),
      },
      recentSermons: {
        label: 'Recent sermons',
        schema: fields.object({
          heading: fields.text({ label: 'Heading', defaultValue: 'Recent sermons' }),
          limit: fields.integer({ label: 'How many to show', defaultValue: 3 }),
        }),
      },
      facebookAlbum: {
        label: 'Facebook album link',
        schema: fields.object({
          heading: fields.text({ label: 'Heading', defaultValue: 'More photos' }),
          body: fields.text({ label: 'Text', multiline: true }),
          url: fields.text({ label: 'Facebook album URL' }),
          linkText: fields.text({ label: 'Link text', defaultValue: 'See the album on Facebook' }),
        }),
      },
    },
    {
      label: 'Page sections',
      description: 'Add, remove, and drag to reorder the sections on this page.',
    }
  );

export default config({
  // 'local' = edit with no login, for development.
  // Switch to { kind: 'github', repo: 'org/repo' } once the repo exists.
  storage: { kind: 'local' },

  ui: {
    brand: { name: 'MPCBC Website' },
    navigation: {
      Homepage: ['homepage'],
      'Weekly updates': ['announcements', 'events'],
      Sermons: ['sermons'],
      Pages: ['pages'],
      Settings: ['services'],
    },
  },

  collections: {
    announcements: collection({
      label: 'Announcements',
      slugField: 'title',
      path: 'src/content/announcements/*',
      format: { contentField: 'body' },
      columns: ['title', 'removeAfter'],
      schema: {
        title: fields.slug({ name: { label: 'Title' } }),
        congregation: fields.select({
          label: 'Who is this for?',
          options: CONGREGATIONS,
          defaultValue: 'all',
        }),
        removeAfter: fields.date({
          label: 'Remove after',
          description: 'Disappears from the site on this date. Required.',
          validation: { isRequired: true },
        }),
        pinned: fields.checkbox({ label: 'Pin to top', defaultValue: false }),
        body: fields.markdoc({ label: 'Details' }),
      },
    }),

    events: collection({
      label: 'Events',
      slugField: 'title',
      path: 'src/content/events/*',
      format: { contentField: 'body' },
      columns: ['title', 'startDate'],
      schema: {
        title: fields.slug({ name: { label: 'Event name' } }),
        congregation: fields.select({
          label: 'Who is this for?',
          options: CONGREGATIONS,
          defaultValue: 'all',
        }),
        startDate: fields.date({ label: 'Date', validation: { isRequired: true } }),
        startTime: fields.text({ label: 'Time', description: 'e.g. 7:30 PM' }),
        location: fields.text({ label: 'Location' }),
        image: fields.image({
          label: 'Photo',
          directory: 'public/images/events',
          publicPath: '/images/events/',
        }),
        body: fields.markdoc({ label: 'Description' }),
      },
    }),

    sermons: collection({
      label: 'Sermons',
      slugField: 'title',
      path: 'src/content/sermons/*',
      format: { data: 'json' },
      columns: ['title', 'date', 'speaker'],
      schema: {
        title: fields.slug({
          name: { label: 'Title' },
          slug: {
            label: 'YouTube video ID',
            description: 'Filled in by the daily sync. Do not change it.',
          },
        }),
        congregation: fields.select({
          label: 'Congregation',
          options: CONGREGATIONS.filter((c) => c.value !== 'all'),
          defaultValue: 'english',
        }),
        date: fields.date({ label: 'Date' }),
        speaker: fields.text({ label: 'Speaker', description: 'Optional.' }),
        scripture: fields.text({ label: 'Scripture', description: 'Optional.' }),
        series: fields.text({ label: 'Series', description: 'Optional.' }),
        notes: fields.text({ label: 'Notes', multiline: true, description: 'Optional.' }),
        hide: fields.checkbox({
          label: 'Hide from website',
          description: 'Stays on YouTube, removed from the archive.',
          defaultValue: false,
        }),
      },
    }),

    pages: collection({
      label: 'Pages',
      slugField: 'title',
      path: 'src/content/pages/*',
      format: { data: 'json' },
      columns: ['title', 'congregation'],
      // Adds a "view page" link in the editor. Not live preview, but it
      // opens the real styled page — which matters most for Chinese
      // content, where the typography differs from English.
      previewUrl: '/{slug}',
      schema: {
        title: fields.slug({
          name: { label: 'Page title' },
          slug: { label: 'Web address', description: 'e.g. childrens-ministry' },
        }),
        congregation: fields.select({
          label: 'Section',
          options: CONGREGATIONS,
          defaultValue: 'all',
        }),
        language: fields.select({
          label: 'Language',
          options: [
            { label: 'English', value: 'en' },
            { label: 'Traditional Chinese 繁體', value: 'zh-Hant' },
            { label: 'Simplified Chinese 简体', value: 'zh-Hans' },
          ],
          defaultValue: 'en',
        }),
        description: fields.text({
          label: 'Search description',
          description: 'Shown in Google results. Around 150 characters.',
          multiline: true,
        }),
        showInMenu: fields.checkbox({ label: 'Show in the menu', defaultValue: false }),
        sections: pageBlocks(),
      },
    }),
  },

  singletons: {
    homepage: singleton({
      label: 'Homepage',
      path: 'src/content/settings/homepage',
      format: { data: 'json' },
      previewUrl: '/',
      schema: {
        heroHeadline: fields.text({
          label: 'Headline',
          multiline: true,
          validation: { isRequired: true },
        }),
        heroLede: fields.text({ label: 'Opening paragraph', multiline: true }),
        heroCtaText: fields.text({ label: 'Button text' }),
        heroCtaLink: fields.text({ label: 'Button link' }),
        heroVideo: fields.text({
          label: 'Hero video URL',
          description: 'Leave blank to show a still image instead.',
        }),
        heroImage: fields.image({
          label: 'Hero image',
          directory: 'public/images/hero',
          publicPath: '/images/hero/',
        }),
        sections: pageBlocks(),
      },
    }),

    services: singleton({
      label: 'Service times',
      path: 'src/content/settings/services',
      format: { data: 'json' },
      schema: {
        intro: fields.text({ label: 'Intro line', multiline: true }),
        services: fields.array(
          fields.object({
            name: fields.text({ label: 'Service name' }),
            nameLocal: fields.text({ label: 'Name in its own language' }),
            time: fields.text({ label: 'Time' }),
            channelId: fields.text({ label: 'YouTube channel ID' }),
          }),
          { label: 'Services', itemLabel: (props) => props.fields.name.value || 'Service' }
        ),
      },
    }),
  },
});
