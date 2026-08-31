import { config, fields, collection, singleton } from '@keystatic/core';

const CONGREGATIONS = [
  { label: 'All congregations', value: 'all' },
  { label: 'English', value: 'english' },
  { label: 'Cantonese 粵語', value: 'cantonese' },
  { label: 'Mandarin 國語', value: 'mandarin' },
];

export default config({
  // 'local' = edit without any login, for development.
  // Switch to { kind: 'github', repo: 'org/repo' } once the repo exists.
  storage: { kind: 'local' },

  ui: {
    brand: { name: 'MPCBC Website' },
    navigation: {
      'Weekly updates': ['announcements', 'events'],
      'Sermons': ['sermons'],
      'Pages': ['pages'],
      'Settings': ['services'],
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
          description: 'The announcement disappears from the site on this date. Required.',
          validation: { isRequired: true },
        }),
        pinned: fields.checkbox({
          label: 'Pin to top',
          defaultValue: false,
        }),
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
      slugField: 'videoId',
      path: 'src/content/sermons/*',
      columns: ['title', 'date', 'speaker'],
      schema: {
        videoId: fields.slug({
          name: { label: 'YouTube video ID', description: 'Filled in automatically by the daily sync.' },
        }),
        title: fields.text({ label: 'Title' }),
        congregation: fields.select({
          label: 'Congregation',
          options: CONGREGATIONS.filter((c) => c.value !== 'all'),
          defaultValue: 'english',
        }),
        date: fields.date({ label: 'Date' }),
        speaker: fields.text({
          label: 'Speaker',
          description: 'Optional — leave blank if unknown.',
        }),
        scripture: fields.text({
          label: 'Scripture',
          description: 'Optional. e.g. Romans 8:1-11',
        }),
        series: fields.text({ label: 'Series', description: 'Optional.' }),
        notes: fields.text({ label: 'Notes', multiline: true, description: 'Optional.' }),
        hide: fields.checkbox({
          label: 'Hide from website',
          description: 'Keeps the video on YouTube but removes it from the archive.',
          defaultValue: false,
        }),
      },
    }),

    pages: collection({
      label: 'Pages',
      slugField: 'title',
      path: 'src/content/pages/*',
      format: { contentField: 'body' },
      columns: ['title', 'congregation'],
      schema: {
        title: fields.slug({ name: { label: 'Page title' } }),
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
        body: fields.markdoc({ label: 'Content' }),
      },
    }),
  },

  singletons: {
    homepage: singleton({
      label: 'Homepage',
      path: 'src/content/settings/homepage',
      schema: {
        heroHeadline: fields.text({
          label: 'Headline',
          multiline: true,
          validation: { isRequired: true },
        }),
        heroLede: fields.text({ label: 'Opening paragraph', multiline: true }),
        heroCtaText: fields.text({ label: 'Button text', defaultValue: 'What to expect on Sunday' }),
        heroCtaLink: fields.text({ label: 'Button link', defaultValue: '/visit' }),
        heroVideo: fields.text({
          label: 'Hero video URL',
          description: 'Leave blank to show a still image instead.',
        }),
        heroImage: fields.image({
          label: 'Hero image',
          description: 'Shown before the video loads, and on slow connections.',
          directory: 'public/images/hero',
          publicPath: '/images/hero/',
        }),

        sections: fields.blocks(
          {
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
                      description: 'For people using a screen reader. Required.',
                      validation: { isRequired: true },
                    }),
                    caption: fields.text({ label: 'Caption', description: 'Optional.' }),
                  }),
                  {
                    label: 'Photos',
                    itemLabel: (props) => props.fields.alt.value || 'Photo',
                  }
                ),
              }),
            },
            videoEmbed: {
              label: 'Video',
              schema: fields.object({
                heading: fields.text({ label: 'Heading' }),
                youtubeId: fields.text({
                  label: 'YouTube video ID',
                  description: 'The part after v= in the URL. Videos live on YouTube, not on the website.',
                  validation: { isRequired: true },
                }),
                caption: fields.text({ label: 'Caption', multiline: true }),
              }),
            },
            facebookAlbum: {
              label: 'Facebook album link',
              schema: fields.object({
                heading: fields.text({ label: 'Heading', defaultValue: 'More photos' }),
                body: fields.text({ label: 'Text', multiline: true }),
                url: fields.text({ label: 'Facebook album URL' }),
                linkText: fields.text({ label: 'Link text', defaultValue: 'See the full album on Facebook' }),
              }),
            },
            textBlock: {
              label: 'Text section',
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
          },
          {
            label: 'Page sections',
            description: 'Add, remove, and reorder the sections below the hero.',
          }
        ),
      },
    }),

    services: singleton({
      label: 'Service times',
      path: 'src/content/settings/services',
      schema: {
        intro: fields.text({ label: 'Intro line', multiline: true }),
        services: fields.array(
          fields.object({
            name: fields.text({ label: 'Service name' }),
            nameLocal: fields.text({ label: 'Name in its own language' }),
            time: fields.text({ label: 'Time', description: 'e.g. 9:30 AM' }),
            channelId: fields.text({ label: 'YouTube channel ID' }),
          }),
          { label: 'Services', itemLabel: (props) => props.fields.name.value || 'Service' }
        ),
      },
    }),
  },
});
