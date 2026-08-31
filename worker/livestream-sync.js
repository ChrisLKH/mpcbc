/**
 * MPCBC livestream sync
 *
 * Runs on a Cloudflare cron trigger. Polls the YouTube channels, works out
 * which broadcast belongs to which service, and writes the result to KV.
 * The site Worker reads that KV value through /api/services.json.
 *
 * Cron only — there is deliberately no `fetch` handler. An earlier version
 * exposed /sync so you could trigger a poll from a browser, but that is an
 * unauthenticated endpoint that spends YouTube quota, and once the site
 * reads KV directly nothing needs it. To run a poll by hand:
 *
 *   npx wrangler dev -c worker/wrangler.toml --test-scheduled
 *   curl "http://localhost:8787/__scheduled"
 *
 * Deploy:  npm run deploy:sync
 * Secrets: npx wrangler secret put YOUTUBE_API_KEY -c worker/wrangler.toml
 */

const SERVICES = [
  {
    id: 'cantonese',
    name: 'Cantonese Service',
    nameLocal: '粵語主日崇拜',
    lang: 'zh-Hant',
    channelKey: 'CHINESE',
    weekday: 0,          // Sunday
    time: '09:30',
    hints: ['粵語', '粤语', 'Cantonese'],
  },
  {
    id: 'mandarin',
    name: 'Mandarin Service',
    nameLocal: '國語主日崇拜',
    lang: 'zh-Hant',
    channelKey: 'CHINESE',
    weekday: 0,
    time: '11:30',
    hints: ['國語', '国语', '普通話', 'Mandarin'],
  },
  {
    id: 'english',
    name: 'English Service',
    nameLocal: 'English Worship',
    lang: 'en',
    channelKey: 'ENGLISH',
    weekday: 0,
    time: '11:00',
    hints: ['English'],
  },
];

const TZ = 'America/Los_Angeles';
const WINDOW_MINUTES = 90;

/** Pull recent + upcoming videos cheaply (1 quota unit per call). */
async function fetchChannelVideos(channelId, apiKey) {
  // The uploads playlist ID is the channel ID with the second character
  // switched from C to U. Saves a channels.list call.
  const uploads = 'UU' + channelId.slice(2);

  const listUrl =
    `https://www.googleapis.com/youtube/v3/playlistItems` +
    `?part=contentDetails&maxResults=15&playlistId=${uploads}&key=${apiKey}`;

  const listRes = await fetch(listUrl);
  if (!listRes.ok) throw new Error(`playlistItems ${listRes.status}`);
  const list = await listRes.json();

  const ids = (list.items || []).map((i) => i.contentDetails.videoId);
  if (!ids.length) return [];

  const vidUrl =
    `https://www.googleapis.com/youtube/v3/videos` +
    `?part=snippet,liveStreamingDetails&id=${ids.join(',')}&key=${apiKey}`;

  const vidRes = await fetch(vidUrl);
  if (!vidRes.ok) throw new Error(`videos ${vidRes.status}`);
  const vids = await vidRes.json();

  return (vids.items || [])
    .filter((v) => {
      const s = v.snippet.liveBroadcastContent;
      return s === 'live' || s === 'upcoming';
    })
    .map((v) => ({
      videoId: v.id,
      title: v.snippet.title,
      state: v.snippet.liveBroadcastContent, // 'live' | 'upcoming'
      scheduledStartTime: v.liveStreamingDetails?.scheduledStartTime || null,
      actualStartTime: v.liveStreamingDetails?.actualStartTime || null,
    }));
}

/** Local wall-clock HH:MM for an ISO timestamp, in church time. */
function localClock(iso) {
  const fmt = new Intl.DateTimeFormat('en-US', {
    timeZone: TZ, hour: '2-digit', minute: '2-digit', hour12: false,
  });
  return fmt.format(new Date(iso));
}

function minutesApart(a, b) {
  const [ah, am] = a.split(':').map(Number);
  const [bh, bm] = b.split(':').map(Number);
  return Math.abs(ah * 60 + am - (bh * 60 + bm));
}

/**
 * Score a broadcast against a service.
 * Time and title are independent signals; either alone is usable,
 * both together is high confidence.
 */
function score(video, service) {
  let timeMatch = false;
  let titleMatch = false;

  if (video.scheduledStartTime) {
    const clock = localClock(video.scheduledStartTime);
    if (minutesApart(clock, service.time) <= WINDOW_MINUTES) timeMatch = true;
  }

  const title = video.title || '';
  if (service.hints.some((h) => title.includes(h))) titleMatch = true;

  return { points: (timeMatch ? 2 : 0) + (titleMatch ? 2 : 0), timeMatch, titleMatch };
}

/** Assign broadcasts to services, best match first, one-to-one. */
function match(videos, services) {
  const pairs = [];
  for (const v of videos) {
    for (const s of services) {
      const r = score(v, s);
      if (r.points > 0) pairs.push({ video: v, service: s, ...r });
    }
  }
  pairs.sort((a, b) => b.points - a.points);

  const takenVideos = new Set();
  const takenServices = new Set();
  const result = {};

  for (const p of pairs) {
    if (takenVideos.has(p.video.videoId) || takenServices.has(p.service.id)) continue;
    takenVideos.add(p.video.videoId);
    takenServices.add(p.service.id);
    result[p.service.id] = {
      videoId: p.video.videoId,
      state: p.video.state === 'live' ? 'live' : 'scheduled',
      startsAt: p.video.scheduledStartTime,
      // Both signals agreed -> link the exact video.
      // Only one agreed -> fall back to the channel live URL, which plays
      // whatever is currently live. Our services never overlap, so this
      // resolves correctly on its own.
      confidence: p.timeMatch && p.titleMatch ? 'high' : 'low',
    };
  }

  const unmatched = videos.filter((v) => !takenVideos.has(v.videoId));
  return { result, unmatched };
}

export default {
  async scheduled(event, env, ctx) {
    ctx.waitUntil(sync(env));
  },
};

async function sync(env) {
  const channels = {
    CHINESE: env.CHANNEL_CHINESE,
    ENGLISH: env.CHANNEL_ENGLISH,
  };

  let videos = [];
  const errors = [];

  for (const [key, channelId] of Object.entries(channels)) {
    if (!channelId) continue;
    try {
      const found = await fetchChannelVideos(channelId, env.YOUTUBE_API_KEY);
      videos.push(...found.map((v) => ({ ...v, channelKey: key })));
    } catch (err) {
      errors.push(`${key}: ${err.message}`);
    }
  }

  // If every channel failed, keep the last good value rather than
  // publishing an empty board.
  if (errors.length === Object.keys(channels).length) {
    return { ok: false, errors, kept: 'previous' };
  }

  const { result, unmatched } = match(videos, SERVICES);

  const payload = {
    generatedAt: new Date().toISOString(),
    services: SERVICES.map((s) => {
      const m = result[s.id];
      const channelId = channels[s.channelKey];
      return {
        id: s.id,
        name: s.name,
        nameLocal: s.nameLocal,
        lang: s.lang,
        time: new Date(`2000-01-01T${s.time}`).toLocaleTimeString('en-US', {
          hour: 'numeric', minute: '2-digit',
        }),
        day: 'Sunday',
        state: m ? m.state : 'unscheduled',
        videoId: m && m.confidence === 'high' ? m.videoId : null,
        startsAt: m ? m.startsAt : null,
        channelUrl: `https://www.youtube.com/channel/${channelId}`,
        liveEmbedUrl: `https://www.youtube.com/embed/live_stream?channel=${channelId}`,
        confidence: m ? m.confidence : null,
      };
    }),
  };

  await env.MPCBC.put('services', JSON.stringify(payload));

  // Anything on the channel we couldn't place is worth knowing about —
  // usually a title that drifted from the convention.
  if (unmatched.length) {
    await env.MPCBC.put('unmatched', JSON.stringify(unmatched), { expirationTtl: 604800 });
  }

  return { ok: true, matched: Object.keys(result), unmatched: unmatched.length, errors };
}
