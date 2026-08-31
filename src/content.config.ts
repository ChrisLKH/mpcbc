import { defineCollection, z } from 'astro:content';
import { glob } from 'astro/loaders';

const congregation = z.enum(['all', 'english', 'cantonese', 'mandarin']);

const sermons = defineCollection({
  loader: glob({ pattern: '**/*.json', base: './src/content/sermons' }),
  schema: z.object({
    title: z.string(),
    congregation: congregation,
    date: z.string(),
    speaker: z.string().optional().default(''),
    scripture: z.string().optional().default(''),
    series: z.string().optional().default(''),
    notes: z.string().optional().default(''),
    hide: z.boolean().optional().default(false),
  }),
});

const announcements = defineCollection({
  loader: glob({ pattern: '**/*.mdoc', base: './src/content/announcements' }),
  schema: z.object({
    title: z.string(),
    congregation: congregation,
    removeAfter: z.coerce.date(),
    pinned: z.boolean().optional().default(false),
  }),
});

const events = defineCollection({
  loader: glob({ pattern: '**/*.mdoc', base: './src/content/events' }),
  schema: z.object({
    title: z.string(),
    congregation: congregation,
    startDate: z.coerce.date(),
    startTime: z.string().optional().default(''),
    location: z.string().optional().default(''),
    image: z.string().optional(),
  }),
});

export const collections = { sermons, announcements, events };
