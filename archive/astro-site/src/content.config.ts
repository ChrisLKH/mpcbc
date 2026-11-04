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

/**
 * The homepage runs announcements as a carousel, which needs more than a
 * headline: a kicker, the Chinese title beside the English one, a short
 * summary, up to three detail rows, and a handwritten aside.
 *
 * Everything past `pinned` is optional and the carousel drops each piece
 * cleanly when it's blank — an announcement with only a title still
 * renders correctly. `summary` is the carousel paragraph; the markdoc
 * `body` stays the longer detail and is not shown on the homepage.
 */
const announcements = defineCollection({
  loader: glob({ pattern: '**/*.md', base: './src/content/announcements' }),
  schema: z.object({
    title: z.string(),
    titleZh: z.string().optional().default(''),
    kicker: z.string().optional().default(''),
    congregation: congregation,
    removeAfter: z.coerce.date(),
    pinned: z.boolean().optional().default(false),
    summary: z.string().optional().default(''),
    ctaText: z.string().optional().default(''),
    ctaLink: z.string().optional().default(''),
    meta: z
      .array(z.object({ label: z.string(), value: z.string() }))
      .optional()
      .default([]),
    note: z.string().optional().default(''),
  }),
});

const events = defineCollection({
  loader: glob({ pattern: '**/*.md', base: './src/content/events' }),
  schema: z.object({
    title: z.string(),
    congregation: congregation,
    startDate: z.coerce.date(),
    startTime: z.string().optional().default(''),
    location: z.string().optional().default(''),
    image: z.string().optional(),
  }),
});

/**
 * Where a page sits in the main menu. `none` keeps it off the menu but
 * still reachable by URL; `top` puts it alongside the built-in items;
 * anything else nests it under that item, which turns that item into a
 * dropdown. The values match the keys in NAV_PARENTS in Base.astro.
 */
export const MENU_PARENTS = ['none', 'top', 'about', 'services', 'newsletter', 'offering'] as const;

/**
 * `sections` is deliberately loose: Tina writes `{ ..., _template }` and
 * older Keystatic files used `{ discriminant, value }`. Blocks.astro
 * normalises both, so validating a shape here would reject content that
 * renders perfectly well.
 */
const pages = defineCollection({
  loader: glob({ pattern: '**/*.json', base: './src/content/pages' }),
  schema: z.object({
    congregation: congregation,
    language: z.string().optional().default('en'),
    description: z.string().optional().default(''),
    menuParent: z.enum(MENU_PARENTS).optional().default('none'),
    menuLabel: z.string().optional().default(''),
    menuLabelZh: z.string().optional().default(''),
    menuOrder: z.number().optional().default(0),
    sections: z.array(z.any()).optional().default([]),
  }),
});

export const collections = { sermons, announcements, events, pages };
