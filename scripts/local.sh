#!/usr/bin/env bash
# local.sh — run Shared Heritage locally on WordPress Playground (SQLite · no MySQL/Docker/wp-cli needed)
#
# usage (cwd anywhere; paths resolve from this script):
#   scripts/local.sh [port]          foreground (Ctrl-C to stop)
#   scripts/local.sh --bg [port]     background: waits until http://127.0.0.1:<port>/ answers, then returns
#   scripts/local.sh --stop          stop the background server of THIS repo/worktree
#   scripts/local.sh --status        print base URL if this repo/worktree's server is up
#
# port: arg > $SH_PORT > $PORT > first free port in 9400..9499  (each worktree can run its own)
# env:  SH_PHP (default 8.5 = production Hostinger) · SH_WP (default latest)
#       SH_PLAYGROUND_VERSION (default 3.1.60 — 3.1.61 deps need node >= 24.18)
#       SH_ACF=0 skip installing ACF (free, from wordpress.org — needs network) · SH_IMPORT=0 skip the seed import
#       SH_IMPORT_RUNS=2 run the import twice (idempotency check: counts must not change)
#       SH_DESIGN_ASSETS / SH_MEDIA_OUT  images / videos outside the repo (default Unesco/design/assets · Unesco/media/out)
#
# What it does: mounts wp-content/themes/sharedheritage (+ each entry of wp-content/mu-plugins if present),
# activates the theme, installs + activates ACF, sets /%postname%/ permalinks, imports content/seed
# (scripts/import/import.php → report .playground/import-report.json), WP_DEBUG + WP_DEBUG_DISPLAY on.
# Site is fresh on every start (data not persisted). Admin: /wp-admin  user admin / password password.
# State (gitignored): .playground/{port,pid,server.log,blueprint.json,import-report.json,uploads/} — smoke.sh reads .playground/port.
set -euo pipefail

REPO="$(cd "$(dirname "$0")/.." && pwd -P)"
STATE="$REPO/.playground"
THEME="$REPO/wp-content/themes/sharedheritage"
MU="$REPO/wp-content/mu-plugins"
PG_VERSION="${SH_PLAYGROUND_VERSION:-3.1.60}"
PHP_VERSION="${SH_PHP:-8.5}"
WP_VERSION="${SH_WP:-latest}"

MODE=fg
case "${1:-}" in
  --bg) MODE=bg; shift ;;
  --stop) MODE=stop; shift ;;
  --status) MODE=status; shift ;;
  -h|--help) sed -n '2,18p' "$0"; exit 0 ;;
esac

is_listening() { nc -z 127.0.0.1 "$1" >/dev/null 2>&1; }

if [ "$MODE" = stop ] || [ "$MODE" = status ]; then
  PORT_SAVED="$(cat "$STATE/port" 2>/dev/null || true)"
  PID_SAVED="$(cat "$STATE/pid" 2>/dev/null || true)"
  if [ "$MODE" = status ]; then
    if [ -n "$PORT_SAVED" ] && is_listening "$PORT_SAVED"; then echo "http://127.0.0.1:$PORT_SAVED"; exit 0; fi
    echo "not running" >&2; exit 1
  fi
  if [ -n "$PID_SAVED" ] && kill -0 "$PID_SAVED" 2>/dev/null; then
    pkill -TERM -P "$PID_SAVED" 2>/dev/null || true
    kill -TERM "$PID_SAVED" 2>/dev/null || true
  fi
  if [ -n "$PORT_SAVED" ]; then   # npx -> node child may outlive the parent: stop whatever this worktree started on its port
    for p in $(lsof -t -iTCP:"$PORT_SAVED" -sTCP:LISTEN 2>/dev/null || true); do kill -TERM "$p" 2>/dev/null || true; done
  fi
  rm -f "$STATE/pid" "$STATE/port"
  echo "stopped"
  exit 0
fi

[ -d "$THEME" ] || { echo "ไม่พบ theme: $THEME" >&2; exit 1; }
command -v npx >/dev/null || { echo "ไม่พบ npx (ต้องมี node)" >&2; exit 1; }
command -v nc >/dev/null || { echo "ไม่พบ nc" >&2; exit 1; }

RUNNING="$(cat "$STATE/port" 2>/dev/null || true)"
if [ -n "$RUNNING" ] && is_listening "$RUNNING"; then
  echo "server ของ repo/worktree นี้รันอยู่แล้ว: http://127.0.0.1:$RUNNING (scripts/local.sh --stop ก่อนถ้าจะเริ่มใหม่)" >&2
  [ "$MODE" = bg ] && { echo "http://127.0.0.1:$RUNNING"; exit 0; }
  exit 1
fi

PORT="${1:-${SH_PORT:-${PORT:-}}}"
if [ -n "$PORT" ]; then
  case "$PORT" in *[!0-9]*) echo "port ต้องเป็นตัวเลข: $PORT" >&2; exit 1 ;; esac
  is_listening "$PORT" && { echo "port $PORT ถูกใช้อยู่แล้ว — เลือก port อื่น (scripts/local.sh <port>)" >&2; exit 1; }
else
  for p in $(seq 9400 9499); do is_listening "$p" || { PORT="$p"; break; }; done
  [ -n "$PORT" ] || { echo "ไม่มี port ว่างใน 9400..9499" >&2; exit 1; }
fi

mkdir -p "$STATE"
# images/videos live outside the repo: Unesco/design/assets · Unesco/media/out (repo = Unesco/UnescoWordpress or Unesco/.worktrees/<wt>)
ROOT="$(cd "$REPO/.." && pwd -P)"; [ -d "$ROOT/design" ] || ROOT="$(cd "$REPO/../.." && pwd -P)"
ASSETS="${SH_DESIGN_ASSETS:-$ROOT/design/assets}"
MEDIA="${SH_MEDIA_OUT:-$ROOT/media/out}"
mkdir -p "$STATE/empty"
[ -d "$ASSETS" ] || { echo "ไม่พบ $ASSETS — import ข้ามรูป (ตั้ง SH_DESIGN_ASSETS)" >&2; ASSETS="$STATE/empty"; }
[ -d "$MEDIA" ] || { echo "ไม่พบ $MEDIA — import ข้ามวิดีโอ (ตั้ง SH_MEDIA_OUT)" >&2; MEDIA="$STATE/empty"; }
rm -rf "$STATE/uploads" "$STATE/import-report.json"; mkdir -p "$STATE/uploads"   # fresh site → fresh uploads

STEPS='    { "step": "activateTheme", "themeFolderName": "sharedheritage" },'
if [ "${SH_ACF:-1}" != 0 ]; then
  STEPS="$STEPS"'
    { "step": "installPlugin", "pluginData": { "resource": "wordpress.org/plugins", "slug": "advanced-custom-fields" }, "options": { "activate": true } },'
fi
STEPS="$STEPS"'
    { "step": "runPHP", "code": "<?php require_once '"'"'/wordpress/wp-load.php'"'"'; global $wp_rewrite; $wp_rewrite->set_permalink_structure('"'"'/%postname%/'"'"'); flush_rewrite_rules( false );" }'
if [ "${SH_IMPORT:-1}" != 0 ]; then
  RUNS="${SH_IMPORT_RUNS:-1}"; case "$RUNS" in ''|*[!0-9]*) RUNS=1 ;; esac
  for _ in $(seq 1 "$RUNS"); do
    STEPS="$STEPS"',
    { "step": "runPHP", "code": "<?php define('"'"'SH_IMPORT_ASSETS_DIR'"'"', '"'"'/sh/assets'"'"'); define('"'"'SH_IMPORT_MEDIA_DIR'"'"', '"'"'/sh/media'"'"'); define('"'"'SH_IMPORT_REPORT'"'"', '"'"'/sh/state/import-report.json'"'"'); require_once '"'"'/wordpress/wp-load.php'"'"'; require '"'"'/sh/repo/scripts/import/import.php'"'"';" }'
  done
fi
printf '{\n  "steps": [\n%s\n  ]\n}\n' "$STEPS" > "$STATE/blueprint.json"

ARGS=(server --port "$PORT" --php "$PHP_VERSION" --wp "$WP_VERSION"
  --mount "$THEME:/wordpress/wp-content/themes/sharedheritage"
  --mount "$STATE/uploads:/wordpress/wp-content/uploads"
  --mount "$REPO/content:/sh/repo/content"
  --mount "$REPO/scripts/import:/sh/repo/scripts/import"
  --mount "$ASSETS:/sh/assets" --mount "$MEDIA:/sh/media" --mount "$STATE:/sh/state"
  --define-bool WP_DEBUG true --define-bool WP_DEBUG_DISPLAY true
  --blueprint "$STATE/blueprint.json")
if [ -d "$MU" ]; then   # mount entries one by one so Playground's own mu-plugins stay visible
  for e in "$MU"/*; do
    [ -e "$e" ] || continue
    ARGS+=(--mount "$e:/wordpress/wp-content/mu-plugins/$(basename "$e")")
  done
fi

CMD=(npx -y "@wp-playground/cli@$PG_VERSION" "${ARGS[@]}")

if [ "$MODE" = fg ]; then
  echo "$PORT" > "$STATE/port"
  echo "$$" > "$STATE/pid"
  trap 'rm -f "$STATE/pid" "$STATE/port"' EXIT
  echo "Shared Heritage → http://127.0.0.1:$PORT  (PHP $PHP_VERSION · WP $WP_VERSION · playground $PG_VERSION)"
  "${CMD[@]}"
  exit $?
fi

# background
nohup "${CMD[@]}" > "$STATE/server.log" 2>&1 &
PID=$!
echo "$PID" > "$STATE/pid"
echo "$PORT" > "$STATE/port"
for _ in $(seq 1 300); do   # ACF download + import of ~150 MB of images take a few minutes
  code="$(curl -s -o /dev/null -w '%{http_code}' --max-time 5 "http://127.0.0.1:$PORT/" || true)"
  # the port answers before the blueprint (ACF + import) is done → also wait for Playground's "Ready!" line
  if [ "$code" = 200 ] && grep -q 'Ready! WordPress is running' "$STATE/server.log" 2>/dev/null; then
    if [ -s "$STATE/import-report.json" ]; then   # last run's counts (report is pretty-printed JSON)
      { tr -d '\n' < "$STATE/import-report.json" | grep -o '"counts": {[^}]*}' | tail -1 | tr -s ' ' | sed 's/^/import: /' >&2; } || true
    fi
    echo "http://127.0.0.1:$PORT"; exit 0
  fi
  kill -0 "$PID" 2>/dev/null || break
  sleep 2
done
echo "server ไม่ขึ้น (ดู $STATE/server.log):" >&2
grep -v -e EBADENGINE -e '^npm warn' "$STATE/server.log" | tail -20 >&2 || true
"$0" --stop >/dev/null 2>&1 || true
exit 1
