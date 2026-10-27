#!/usr/bin/env bash
# smoke.sh — curl every path in docs/smoke-urls against a running local server (scripts/local.sh)
#
# contract (docs/CONVENTIONS.md): last line of stdout = PASS | FAIL(…) | SKIPPED(…)
#   each path → HTTP 200 (redirects are NOT followed) and no PHP error output
#   (Fatal error · Parse error · Warning · Notice · Deprecated) and no WP "critical error" screen
# base URL: $SMOKE_BASE > this repo/worktree's .playground/port (written by scripts/local.sh) > http://127.0.0.1:9400
# docs/smoke-urls: one path per line, starting with "/" · blank lines and lines starting with "#" are ignored
set -u

REPO="$(cd "$(dirname "$0")/.." && pwd -P)"
cd "$REPO" || { echo "FAIL(cd $REPO)"; exit 1; }
URLS="docs/smoke-urls"

if [ -n "${SMOKE_BASE:-}" ]; then
  BASE="$SMOKE_BASE"
elif [ -s .playground/port ]; then
  BASE="http://127.0.0.1:$(tr -cd '0-9' < .playground/port)"
else
  BASE="http://127.0.0.1:9400"
fi
BASE="${BASE%/}"

[ -f "$URLS" ] || { echo "FAIL(ไม่มี $URLS)"; exit 1; }
command -v curl >/dev/null || { echo "FAIL(env — ไม่พบ curl)"; exit 1; }

probe="$(curl -s -o /dev/null -w '%{http_code}' --max-time 10 "$BASE/" 2>/dev/null)"
if [ -z "$probe" ] || [ "$probe" = 000 ]; then
  echo "SKIPPED(ไม่มี server ที่ $BASE — รัน scripts/local.sh --bg ก่อน)"
  exit 0
fi

# PHP display_errors output (html or text) · WP fatal-error handler screen
ERR_RE='(Fatal error|Parse error|Warning|Notice|Deprecated|Strict Standards)(</b>)?:[[:space:]].* on line|There has been a critical error on this website'

TMP="$(mktemp)"
trap 'rm -f "$TMP"' EXIT
fails=()
n=0
while IFS= read -r line || [ -n "$line" ]; do
  path="$(printf '%s' "$line" | tr -d '\r' | sed -e 's/^[[:space:]]*//' -e 's/[[:space:]]*$//')"
  case "$path" in ''|'#'*) continue ;; esac
  case "$path" in /*) ;; *) fails+=("$path=ไม่ขึ้นต้นด้วย /"); continue ;; esac
  n=$((n + 1))
  code="$(curl -s -o "$TMP" -w '%{http_code}' --max-time 60 "$BASE$path" 2>/dev/null)"
  hit="$(grep -Eo "$ERR_RE" "$TMP" 2>/dev/null | head -1 | sed -e 's/<[^>]*>//g' | cut -c1-120)"
  if [ "$code" != 200 ]; then
    echo "✗ $path → HTTP $code"
    fails+=("$path=HTTP $code")
  elif [ -n "$hit" ]; then
    echo "✗ $path → $hit"
    fails+=("$path=$hit")
  else
    echo "✓ $path → 200"
  fi
done < "$URLS"

if [ "$n" = 0 ] && [ "${#fails[@]}" = 0 ]; then
  echo "FAIL($URLS ไม่มี path)"
  exit 1
fi
if [ "${#fails[@]}" -gt 0 ]; then
  msg="$(printf '%s; ' "${fails[@]}")"
  echo "FAIL(${#fails[@]}/$n ที่ $BASE: ${msg%; })"
  exit 1
fi
echo "PASS"
