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
#
# What it does: mounts wp-content/themes/sharedheritage (+ each entry of wp-content/mu-plugins if present),
# activates the theme, sets /%postname%/ permalinks, WP_DEBUG + WP_DEBUG_DISPLAY on.
# Site is fresh on every start (data not persisted). Admin: /wp-admin  user admin / password password.
# State (gitignored): .playground/{port,pid,server.log,blueprint.json} — scripts/smoke.sh reads .playground/port.
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
cat > "$STATE/blueprint.json" <<'JSON'
{
  "steps": [
    { "step": "activateTheme", "themeFolderName": "sharedheritage" },
    { "step": "runPHP", "code": "<?php require_once '/wordpress/wp-load.php'; global $wp_rewrite; $wp_rewrite->set_permalink_structure('/%postname%/'); flush_rewrite_rules( false );" }
  ]
}
JSON

ARGS=(server --port "$PORT" --php "$PHP_VERSION" --wp "$WP_VERSION"
  --mount "$THEME:/wordpress/wp-content/themes/sharedheritage"
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
for _ in $(seq 1 120); do
  code="$(curl -s -o /dev/null -w '%{http_code}' --max-time 5 "http://127.0.0.1:$PORT/" || true)"
  [ "$code" = 200 ] && { echo "http://127.0.0.1:$PORT"; exit 0; }
  kill -0 "$PID" 2>/dev/null || break
  sleep 2
done
echo "server ไม่ขึ้น (ดู $STATE/server.log):" >&2
grep -v -e EBADENGINE -e '^npm warn' "$STATE/server.log" | tail -20 >&2 || true
"$0" --stop >/dev/null 2>&1 || true
exit 1
