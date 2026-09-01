#!/usr/bin/env node
/**
 * Sermon archive sync
 *
 * Reads the YouTube playlists and writes one JSON file per sermon into
 * src/content/sermons/. Those are the exact files Tina reads and writes,
 * so a synced sermon appears in the editor immediately, ready for
 * someone to add a speaker or scripture if they want to.
 *
 * The filename is the YouTube video ID. That is the join key between
 * the automatic half and the manual half.
 *
 * RULE: this script only ever CREATES files. It never edits or deletes
 * one that already exists. Otherwise a nightly run would wipe out the
 * speaker someone typed in by hand, which would destroy trust in the
 * whole system within a fortnight.
 *
 * Run: YOUTUBE_API_KEY=xxx node scripts/sync-sermons.mjs
 */

import { readdirSync, writeFileSync, mkdirSync, existsSync } from 'node:fs';
import { join } from 'node:path';

const OUT_DIR = 'src/content/sermons';
const API = 'https://www.googleapis.com/youtube/v3';
const KEY = process.env.YOUTUBE_API_KEY;

// One playlist per congregation. The media team adds each finished
// stream to the matching playlist — one click after the service.
const PLAYLISTS = {
  cantonese: process.env.PLAYLIST_CANTONESE,
  mandarin: process.env.PLAYLIST_MANDARIN,
  english: process.env.PLAYLIST_ENGLISH,
};

if (!KEY) {
  console.error('YOUTUBE_API_KEY is not set.');
  process.exit(1);
}

/** Every video in a playlist, following pagination. 1 quota unit per page. */
async function playlistVideos(playlistId) {
  const items = [];
  let pageToken = '';

  do {
    const url =
      `${API}/playlistItems?part=snippet,contentDetails&maxResults=50` +
      `&playlistId=${playlistId}&key=${KEY}` +
      (pageToken ? `&pageToken=${pageToken}` : '');

    const res = await fetch(url);
    if (!res.ok) {
      throw new Error(`playlistItems ${res.status}: ${await res.text()}`);
    }
    const data = await res.json();

    for (const item of data.items || []) {
      // Deleted or private videos leave a placeholder behind.
      if (!item.contentDetails?.videoId) continue;
      if (item.snippet.title === 'Private video') continue;
      if (item.snippet.title === 'Deleted video') continue;

      items.push({
        videoId: item.contentDetails.videoId,
        title: item.snippet.title,
        publishedAt: item.contentDetails.videoPublishedAt || item.snippet.publishedAt,
      });
    }

    pageToken = data.nextPageToken || '';
  } while (pageToken);

  return items;
}

function isoDate(ts) {
  return new Date(ts).toISOString().slice(0, 10);
}

async function main() {
  mkdirSync(OUT_DIR, { recursive: true });

  const existing = new Set(
    readdirSync(OUT_DIR)
      .filter((f) => f.endsWith('.json'))
      .map((f) => f.replace(/\.json$/, ''))
  );

  let created = 0;
  let skipped = 0;
  const problems = [];

  for (const [congregation, playlistId] of Object.entries(PLAYLISTS)) {
    if (!playlistId) {
      problems.push(`No playlist configured for ${congregation}`);
      continue;
    }

    let videos;
    try {
      videos = await playlistVideos(playlistId);
    } catch (err) {
      // One playlist failing shouldn't stop the others.
      problems.push(`${congregation}: ${err.message}`);
      continue;
    }

    for (const v of videos) {
      if (existing.has(v.videoId)) {
        skipped++;
        continue;
      }

      const entry = {
        title: v.title,
        congregation,
        date: isoDate(v.publishedAt),
        // Left blank on purpose. Someone can fill these in through the
        // editor whenever they have time. The archive works without them.
        speaker: '',
        scripture: '',
        series: '',
        notes: '',
        hide: false,
      };

      writeFileSync(
        join(OUT_DIR, `${v.videoId}.json`),
        JSON.stringify(entry, null, 2) + '\n',
        'utf8'
      );

      existing.add(v.videoId);
      created++;
      console.log(`+ ${congregation}  ${entry.date}  ${v.title}`);
    }
  }

  console.log(`\n${created} added, ${skipped} already present.`);

  if (problems.length) {
    console.log('\nProblems:');
    problems.forEach((p) => console.log(`  - ${p}`));
  }

  // Signals to the GitHub Action whether there is anything to commit.
  if (process.env.GITHUB_OUTPUT) {
    const fs = await import('node:fs');
    fs.appendFileSync(process.env.GITHUB_OUTPUT, `created=${created}\n`);
  }
}

main().catch((err) => {
  console.error(err);
  process.exit(1);
});
