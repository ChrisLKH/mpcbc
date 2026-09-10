#!/usr/bin/env bash
#
# Starts the MPCBC website on this machine. macOS / Linux.
#
# Replaces the two-terminal dance in the README. Tina's content server has to
# be up and indexing before Astro starts, so this runs Tina in the background,
# waits for it to answer on :4001, then runs the site in the foreground and
# opens the browser once it responds. Ctrl+C stops both.
#
# ASTRO_DEV_BACKGROUND is set on purpose: Astro 7 otherwise runs the dev
# server as a detached child with a hardcoded 30-second window to claim its
# lock file, which a cold Vite cache regularly misses. Running inline removes
# that failure entirely.
#
#   bash scripts/start.sh
#   bash scripts/start.sh --no-browser

set -euo pipefail

NO_BROWSER=0
[ "${1:-}" = "--no-browser" ] && NO_BROWSER=1

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT"

step() { printf '\n\033[35m==> %s\033[0m\n' "$1"; }

if [ ! -d "$ROOT/node_modules" ]; then
  printf '\033[31mThe building blocks are missing.\033[0m\n'
  printf '\033[33mRun this first:  bash scripts/setup.sh\033[0m\n'
  exit 1
fi

# Connect by name, not by 127.0.0.1: the dev server binds IPv6, so an
# IPv4-only probe reports "not up" for a server that is running fine.
wait_for_port() {
  local port="$1" seconds="${2:-180}" i=0
  while [ "$i" -lt "$seconds" ]; do
    if (exec 3<>"/dev/tcp/localhost/$port") 2>/dev/null; then
      exec 3<&- 3>&-
      return 0
    fi
    sleep 1
    i=$((i + 1))
  done
  return 1
}

open_url() {
  if command -v open >/dev/null 2>&1; then open "$1"
  elif command -v xdg-open >/dev/null 2>&1; then xdg-open "$1"
  fi
}

step "Starting the content editor in the background"
npx tinacms dev >/tmp/mpcbc-tina.log 2>&1 &
TINA_PID=$!
trap 'kill "$TINA_PID" 2>/dev/null || true' EXIT INT TERM

printf '\033[90m    Waiting for it to finish indexing...\033[0m\n'
if ! wait_for_port 4001; then
  printf '\033[31mThe content editor never came up. Its log: /tmp/mpcbc-tina.log\033[0m\n'
  exit 1
fi
printf '\033[90m    Content editor ready.\033[0m\n'

if [ "$NO_BROWSER" -eq 0 ]; then
  ( wait_for_port 4321 && sleep 1 && open_url "http://localhost:4321" ) &
fi

step "Starting the website"
printf '\033[36m    The site:   http://localhost:4321\033[0m\n'
printf '\033[36m    The editor: http://localhost:4321/admin\033[0m\n'
printf '\033[90m    Press Ctrl+C to stop both.\033[0m\n'

export ASTRO_DEV_BACKGROUND=1
npm run dev
