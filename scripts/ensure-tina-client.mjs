/**
 * Generate Tina's client when it is missing, so a fresh checkout builds.
 *
 * Three routes import tina/__generated__/client, which is gitignored.
 * On a developer machine `tinacms dev` has already written it. On
 * Cloudflare's own build (Workers Builds runs plain `npm run build` on
 * a fresh clone) it does not exist, and the build failed outright with
 * "Could not resolve '../../tina/__generated__/client'".
 *
 * `--local` generates it without Tina Cloud credentials, which that
 * build does not have. It also writes a local-mode admin to public/admin
 * that talks to localhost:4001 — useless on the live site — so it is
 * removed again, for the same reason scripts/clean-admin.mjs exists.
 *
 * When the client is already there this does nothing, so a local build
 * never touches the running Tina server's port.
 */
import { existsSync, rmSync } from 'node:fs';
import { execSync } from 'node:child_process';

if (existsSync('tina/__generated__/client.ts')) {
  console.log('Tina client present; not regenerating.');
} else {
  console.log('No Tina client (fresh checkout). Generating it in local mode.');
  execSync('npx tinacms build --local --skip-cloud-checks', { stdio: 'inherit' });
  rmSync('public/admin', { recursive: true, force: true });
}
