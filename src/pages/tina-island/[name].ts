/**
 * Tina island endpoint.
 *
 * When an editor types in the admin, the bridge POSTs here with the draft
 * content. This re-renders just that section and posts the HTML back, which
 * is what makes the preview update live instead of reloading the page.
 *
 * Marked experimental by Tina — it sits on Astro's container API.
 */
import { experimental_createIslandRoute } from '@tinacms/astro/experimental';
// Subpath, not the bare package: under Astro's resolver the root export
// condition points at TinaMarkdown.astro.
import { requestWithMetadata } from '@tinacms/astro/data';
import Blocks from '../../components/Blocks.astro';
import Home from '../../components/home/Home.astro';
import { client } from '../../../tina/__generated__/client';

// Needs to run per request, so it can't be prerendered.
export const prerender = false;

export const ALL = experimental_createIslandRoute({
  page: {
    wrapper: { tag: 'div' },
    fetch: async (_request, params) => {
      const slug = params.get('slug');
      if (!slug) return null;
      // requestWithMetadata is what registers the form with the admin —
      // querying the client directly renders the region but leaves the
      // sidebar empty.
      return await requestWithMetadata(
        client.queries.pages({ relativePath: `${slug}.json` }),
        { priority: 'primary' }
      );
    },
    component: Blocks,
    propsFromData: (data) => ({
      sections: data?.data?.pages?.sections ?? [],
    }),
  },

  homepage: {
    wrapper: { tag: 'div' },
    fetch: async () =>
      requestWithMetadata(
        client.queries.homepage({ relativePath: 'homepage.json' }),
        { priority: 'primary' }
      ),
    // The whole homepage, not just its sections list — see index.astro.
    component: Home,
    propsFromData: (data) => ({
      home: data?.data?.homepage ?? {},
    }),
  },
});
