#!/usr/bin/env bash
#
# Sends your changes to GitHub, which publishes the live site. macOS / Linux.
#
# Collects whatever landed on GitHub while you were working, packages up your
# edits with a description, and pushes. A push to main triggers the deploy
# workflow, so the live site follows two to four minutes later.
#
# The pull is --rebase --autostash rather than a plain pull: the nightly sermon
# import commits to main on its own, so a helper who worked for an hour will
# routinely be behind, and an unfinished edit shouldn't block catching up.
#
#   bash scripts/publish.sh "Shorten the homepage welcome text"

set -euo pipefail

MESSAGE="${1:-}"
if [ -z "$MESSAGE" ]; then
  printf '\033[31mSay what you changed.\033[0m\n'
  echo 'For example:  bash scripts/publish.sh "Shorten the homepage welcome text"'
  exit 1
fi

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT"

ACTIONS="https://github.com/ChrisLKH/mpcbc/actions"
step() { printf '\n\033[35m==> %s\033[0m\n' "$1"; }

# Nothing to send is a normal outcome, not an error - say so and stop before a
# confusing "nothing to commit" from git.
if [ -z "$(git status --porcelain)" ]; then
  printf '\033[33mNothing has changed since your last publish.\033[0m\n'
  exit 0
fi

step "What is about to go live"
git status --short

step "Collecting anything new from GitHub"
if ! git pull --rebase --autostash; then
  printf "\n\033[31mYour edits and someone else's have collided, and this needs a person.\033[0m\n"
  printf '\033[33mNothing has been published. Send Chris this message and stop here.\033[0m\n'
  exit 1
fi

step "Packaging your changes"
git add -A
git commit -m "$MESSAGE"

step "Publishing"
if ! git push; then
  printf '\n\033[31mThe push was refused. Run this script again - the pull at the top usually clears it.\033[0m\n'
  exit 1
fi

printf '\n\033[32mSent.\033[0m\n'
echo "The live site rebuilds automatically and updates in about 2-4 minutes."
echo "Watch it here: $ACTIONS"

if command -v open >/dev/null 2>&1; then open "$ACTIONS"
elif command -v xdg-open >/dev/null 2>&1; then xdg-open "$ACTIONS"
fi
