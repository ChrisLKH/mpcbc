#!/usr/bin/env bash
#
# Gets the MPCBC website onto a machine and ready to run. macOS / Linux.
#
# Safe to run as many times as you like. On the first run it clones the
# repository into ~/Documents/mpcbc; on every run after that it pulls the
# latest code into the copy it finds. Then it installs dependencies and
# writes a placeholder .env if there isn't one.
#
# This file is also the bootstrap: it works from a Downloads folder with no
# repository present, and from inside an existing clone.
#
#   bash setup.sh                    # into ~/Documents/mpcbc
#   bash setup.sh ~/work/mpcbc       # somewhere else
#   bash setup.sh --start            # start the site when finished

set -euo pipefail

REPO="https://github.com/ChrisLKH/mpcbc.git"
TARGET="$HOME/Documents/mpcbc"
START=0

for arg in "$@"; do
  case "$arg" in
    --start) START=1 ;;
    -*)      echo "Unknown option: $arg" >&2; exit 1 ;;
    *)       TARGET="$arg" ;;
  esac
done

step() { printf '\n\033[35m==> %s\033[0m\n' "$1"; }
note() { printf '\033[90m    %s\033[0m\n' "$1"; }
stop() {
  printf '\n\033[31mStopped: %s\033[0m\n' "$1"
  [ $# -gt 1 ] && printf '\033[33mWhat to do: %s\033[0m\n' "$2"
  exit 1
}

# --- 1. Prerequisites -------------------------------------------------
# Checked before anything is downloaded, so a missing tool costs seconds
# rather than failing halfway through an install.

step "Checking Git and Node"

command -v git  >/dev/null 2>&1 || stop "Git is not installed." "Run: xcode-select --install"
command -v node >/dev/null 2>&1 || stop "Node.js is not installed." "Install the LTS build from https://nodejs.org, then reopen this window."

NODE_RAW="$(node --version | sed 's/^v//')"
NODE_MAJOR="${NODE_RAW%%.*}"
if [ "$NODE_MAJOR" -lt 20 ]; then
  stop "Node $NODE_RAW is too old; the site needs 20 or newer." "Install the LTS build from https://nodejs.org."
fi
note "git $(git --version | awk '{print $3}')  ·  node v$NODE_RAW"

# --- 2. Find or fetch the code ---------------------------------------
# If this script is being run from inside a clone, that clone wins: updating
# the copy you are standing in is always what you meant.

HERE="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
if [ -d "$HERE/.git" ] && [ -f "$HERE/package.json" ]; then
  TARGET="$HERE"
fi

if [ -d "$TARGET/.git" ]; then
  step "Updating the code in $TARGET"
  cd "$TARGET"
  git pull --ff-only || stop "The update was refused, usually because of unfinished local edits." \
    "Run scripts/publish.sh to send your work first, or ask Chris."
else
  step "Downloading the site into $TARGET"
  mkdir -p "$(dirname "$TARGET")"
  git clone "$REPO" "$TARGET" || stop "The download failed." \
    "Check your internet connection, and that your GitHub account has been given access to the repository."
  cd "$TARGET"
fi
note "Now at: $TARGET"

# --- 3. Dependencies --------------------------------------------------

step "Installing the site's building blocks (1-3 minutes)"
npm install --no-fund --no-audit || stop "npm install failed." \
  "Scroll up for the first red ERR! line and send it to Chris."

# --- 4. Environment file ----------------------------------------------
# Local editing runs TinaCMS in local mode, which needs no credentials. The
# file exists so nothing has to guess, and so the real values have an obvious
# home if the live editor is ever switched on.

step "Checking the environment file"
if [ -f "$TARGET/.env" ]; then
  note ".env already exists - left untouched."
else
  cat > "$TARGET/.env" <<'ENVFILE'
# TinaCMS credentials. Local editing does NOT need real values here -
# the editor at localhost:4321/admin works with this file exactly as is.
#
# Only fill these in to run the editor against the live site, and get
# them from Chris. Never commit this file or paste it into a chat.

TINA_CLIENT_ID=
TINA_TOKEN=
TINA_BRANCH=main
ENVFILE
  note ".env created with blank placeholders - nothing else needed for local work."
fi

chmod +x "$TARGET/scripts/start.sh" "$TARGET/scripts/publish.sh" 2>/dev/null || true

# --- 5. Done ----------------------------------------------------------

printf '\n\033[32mReady.\033[0m\n'
echo "The site lives in $TARGET"

if [ "$START" -eq 1 ]; then
  exec bash "$TARGET/scripts/start.sh"
else
  printf '\n\033[36mStart it with:\033[0m\n'
  echo "  cd \"$TARGET\""
  echo "  bash scripts/start.sh"
fi
