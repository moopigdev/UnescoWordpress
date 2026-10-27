#!/usr/bin/env bash
# scripts/deploy/remote.sh — ส่วนที่รัน "บน server" (Hostinger · wp-cli) · scripts/deploy.sh ส่งไฟล์นี้ทาง
#   ssh … bash -s -- <phase> [args] < scripts/deploy/remote.sh
# --dry-run = ไม่รันอะไรเลย พิมพ์คำสั่งที่จะรัน (deploy.sh ใช้รันไฟล์นี้ "ในเครื่อง" เพื่อพิมพ์แผน — ห้ามมีผลข้างเคียง)
#
# phase:
#   discover   อ่านอย่างเดียว: หา wp-config.php ใต้ ~/domains · พิมพ์ path + siteurl + home + theme ที่ active
#   preflight  อ่านอย่างเดียว: WP ติดตั้งแล้ว · siteurl ตรง --url · พิมพ์ theme/ACF/จำนวน object
#              production + --import=1 + มี object/timeline_item อยู่แล้ว → exit 3 (หยุดถามเจ้าของ · ห้าม import ทับ prod)
#   backup     (production) wp db export + tar wp-content → <dir>/backups/<env>-<ts>/ (chmod 700) + ROLLBACK.txt
#   prepare    mkdir mu-plugins (ถ้ายังไม่มี) · โฟลเดอร์ tmp import
#   post       ACF · activate theme · permalink /%postname%/ · import (ถ้า --import=1) · ลบ tmp · LiteSpeed purge
# args: --env=staging|production --path=<WP absolute> --url=<siteurl ที่คาด> --dir=<งาน · relative กับ $HOME> --ts=<stamp> --import=0|1
set -euo pipefail

PHASE="${1:-}"; [ $# -gt 0 ] && shift
DRY=0; ENV=""; WP_PATH=""; URL=""; PROD_URL=""; DIR="sh-deploy"; TS=""; IMPORT=0
for a in "$@"; do
  case "$a" in
    --dry-run) DRY=1 ;;
    --env=*) ENV="${a#*=}" ;;
    --path=*) WP_PATH="${a#*=}" ;;
    --url=*) URL="${a#*=}" ;;
    --prod-url=*) PROD_URL="${a#*=}" ;;
    --dir=*) DIR="${a#*=}" ;;
    --ts=*) TS="${a#*=}" ;;
    --import=*) IMPORT="${a#*=}" ;;
    *) echo "remote: ไม่รู้จัก argument: $a" >&2; exit 2 ;;
  esac
done

fail() { echo "remote[$PHASE]: ✗ $*" >&2; exit 1; }
say() { echo "remote[$PHASE]: $*"; }
# host ของ URL แบบ normalize (เหมือน sh_url_host ใน lib.sh — ไฟล์นี้รันเดี่ยวบน server)
url_host() {
  local u="$1"
  u="${u#*://}"; u="${u%%/*}"; u="${u%%\?*}"; u="${u%%#*}"; u="${u##*@}"; u="${u%%:*}"; u="${u%.}"
  u="$(printf '%s' "$u" | tr '[:upper:]' '[:lower:]')"
  printf '%s\n' "${u#www.}"
}
show() { local a out=""; for a in "$@"; do out="$out $(printf '%q' "$a")"; done; printf '  [remote]%s\n' "$out"; }
# R = คำสั่งที่เขียน/เปลี่ยนสถานะ · dry-run = พิมพ์อย่างเดียว
R() { if [ "$DRY" = 1 ]; then show "$@"; else "$@"; fi; }
# Q = คำสั่งอ่าน (เช็ก) · dry-run = พิมพ์แล้วถือว่า "ยังไม่ทำ" (คืน 1) เพื่อให้เห็นคำสั่งที่จะตามมา
Q() { if [ "$DRY" = 1 ]; then show "$@"; echo "  [remote]   (dry-run: ถือว่าผลเป็น \"ยังไม่มี/ไม่ใช่\")"; return 1; else "$@"; fi; }
# V = คำสั่งอ่านที่ต้องการค่า · dry-run = พิมพ์ + คืน "<?>"
V() { if [ "$DRY" = 1 ]; then show "$@" >&2; echo "<?>"; else "$@"; fi; }

case "$PHASE" in discover|preflight|backup|prepare|post) ;; *) fail "phase ต้องเป็น discover|preflight|backup|prepare|post" ;; esac
case "$DIR" in ''|/*|*..*) fail "--dir ต้องเป็น path relative กับ home ไม่มี .. ($DIR)" ;; esac

if [ "$DRY" = 1 ]; then HOME_R='$HOME'; else HOME_R="$HOME"; cd "$HOME"; fi
BASE="$HOME_R/$DIR"

if [ "$PHASE" = discover ]; then
  say "หา WordPress ใต้ $HOME_R/domains (อ่านอย่างเดียว)"
  if [ "$DRY" = 1 ]; then
    show find "$HOME_R/domains" -maxdepth 4 -name wp-config.php
    show wp --path='<DIR-of-each-wp-config.php>' --skip-plugins --skip-themes option get siteurl
    show wp --path='<DIR-of-each-wp-config.php>' --skip-plugins --skip-themes option get home
    show wp --path='<DIR-of-each-wp-config.php>' --skip-plugins --skip-themes option get stylesheet
    exit 0
  fi
  command -v wp >/dev/null || fail "ไม่พบ wp-cli บน server"
  found=0
  while IFS= read -r cfg; do
    d="$(dirname "$cfg")"; found=1
    s="$(wp --path="$d" --skip-plugins --skip-themes option get siteurl 2>/dev/null || echo '?')"
    h="$(wp --path="$d" --skip-plugins --skip-themes option get home 2>/dev/null || echo '?')"
    t="$(wp --path="$d" --skip-plugins --skip-themes option get stylesheet 2>/dev/null || echo '?')"
    printf '  path=%s\n    siteurl=%s · home=%s · theme=%s\n' "$d" "$s" "$h" "$t"
  done < <(find "$HOME/domains" -maxdepth 4 -name wp-config.php 2>/dev/null | sort)
  [ "$found" = 1 ] || say "ไม่พบ wp-config.php ใต้ ~/domains"
  say "ยืนยัน path ที่ siteurl ตรงกับ staging/production แล้วใส่ใน scripts/deploy/hosts.env"
  exit 0
fi

[ -n "$ENV" ] && [ -n "$WP_PATH" ] && [ -n "$URL" ] && [ -n "$TS" ] || fail "ต้องมี --env --path --url --ts"
case "$ENV" in staging|production) ;; *) fail "--env ต้องเป็น staging|production" ;; esac
case "$WP_PATH" in /*) ;; '<'*) [ "$DRY" = 1 ] || fail "ยังไม่ได้ตั้ง WP path" ;; *) fail "--path ต้องเป็น absolute ($WP_PATH)" ;; esac
case "$TS" in *[!0-9-]*) fail "--ts ต้องเป็นตัวเลข/ขีด ($TS)" ;; esac
WP=(wp --path="$WP_PATH")
TMP="$BASE/tmp-$ENV-$TS"          # bundle import (deploy.sh rsync มาไว้ที่นี่) — นอก webroot
BK="$BASE/backups/$ENV-$TS"
LOGS="$BASE/logs"

case "$PHASE" in
preflight)
  [ "$DRY" = 1 ] || command -v wp >/dev/null || fail "ไม่พบ wp-cli บน server"
  R "${WP[@]}" --skip-plugins --skip-themes core is-installed
  got="$(V "${WP[@]}" --skip-plugins --skip-themes option get siteurl)"
  if [ "$ENV" = staging ]; then   # F1: siteurl จริงของ staging ต้องไม่ใช่ host production (ตรวจก่อนข้ออื่น)
    [ -n "$PROD_URL" ] || fail "staging ต้องมี --prod-url (กันส่งผิด production) · หยุดโดยไม่แตะอะไร"
    if [ "$DRY" = 1 ]; then
      echo "  [remote]   staging: host ของ siteurl ต้องไม่ใช่ host ของ $PROD_URL (เท่ากัน = หยุด)"
    elif [ "$(url_host "$got")" = "$(url_host "$PROD_URL")" ]; then
      fail "siteurl ของ $WP_PATH = $got คือ production ($PROD_URL) — staging ห้ามเขียน production · หยุดโดยไม่แตะอะไร"
    fi
  fi
  if [ "$DRY" = 1 ]; then
    echo "  [remote]   ต้องเท่ากับ $URL (ไม่ตรง = หยุด · กันส่งผิด site)"
  elif [ "${got%/}" != "${URL%/}" ]; then
    fail "siteurl ของ $WP_PATH = $got ≠ ที่คาด $URL — path ผิด site? (รัน --discover) · หยุดโดยไม่แตะอะไร"
  fi
  say "siteurl ตรง: ${got}"
  say "theme ที่ active: $(V "${WP[@]}" --skip-plugins --skip-themes option get stylesheet)"
  n="$(V "${WP[@]}" --skip-plugins --skip-themes post list --post_type=object,timeline_item --post_status=any --format=count)"
  say "object + timeline_item ที่มีอยู่: $n"
  if [ "$ENV" = production ] && [ "$IMPORT" = 1 ]; then
    if [ "$DRY" = 1 ]; then
      echo "  [remote]   production: ถ้า > 0 → หยุด exit 3 (ห้าม import ทับ prod · ถามเจ้าของ · ส่งแค่โค้ดใช้ --no-import)"
    elif [ "$n" != 0 ]; then
      echo "remote[preflight]: ⛔ production มีเนื้อหาอยู่แล้ว ($n รายการ) — ห้าม import ทับ · ถามเจ้าของ" >&2
      echo "remote[preflight]:    ส่งเฉพาะโค้ด (theme/mu-plugin) ได้ด้วย --no-import" >&2
      exit 3
    fi
  fi
  ;;
backup)
  [ "$ENV" = production ] || say "(สำรองบน $ENV)"
  R mkdir -p "$BASE/backups"
  R chmod 700 "$BASE" "$BASE/backups"
  R mkdir -m 700 "$BK"
  R "${WP[@]}" --skip-plugins --skip-themes db export "$BK/db.sql"
  if [ "$DRY" = 1 ]; then
    show "${WP[@]}" --skip-plugins --skip-themes option get stylesheet '>' "$BK/stylesheet.txt"
  else
    "${WP[@]}" --skip-plugins --skip-themes option get stylesheet > "$BK/stylesheet.txt"
  fi
  R tar -czf "$BK/wp-content.tar.gz" -C "$WP_PATH" wp-content
  R chmod 600 "$BK/db.sql" "$BK/wp-content.tar.gz"
  rb="$(cat <<EOF
# ROLLBACK $ENV — สำรองก่อน deploy $TS (รันบน server: ssh -p 65002 <user>@<host>)
cd "$WP_PATH"
mv wp-content "wp-content.failed-$TS"                       # เก็บของที่ deploy ไว้ตรวจ
tar -xzf "$BK/wp-content.tar.gz" -C "$WP_PATH"               # คืน wp-content (theme · mu-plugin · uploads · plugins)
wp --path="$WP_PATH" db import "$BK/db.sql"                  # คืน DB (theme เดิม = $(cat "$BK/stylesheet.txt" 2>/dev/null || echo '<ดู stylesheet.txt>'))
wp --path="$WP_PATH" cache flush
wp --path="$WP_PATH" litespeed-purge all || true
# ตรวจหน้าเว็บแล้วค่อยลบ: rm -rf "$WP_PATH/wp-content.failed-$TS"
EOF
)"
  if [ "$DRY" = 1 ]; then
    show printf '%s\n' '<rollback-commands-below>' '>' "$BK/ROLLBACK.txt"
  else
    printf '%s\n' "$rb" > "$BK/ROLLBACK.txt"
    ls -la "$BK"
  fi
  say "สำรองแล้ว → $BK · คำสั่ง rollback:"
  printf '%s\n' "$rb"
  ;;
prepare)
  R mkdir -p "$WP_PATH/wp-content/mu-plugins"
  if [ "$IMPORT" = 1 ]; then
    R mkdir -p "$BASE"
    R chmod 700 "$BASE"
    R mkdir -p "$TMP"
  fi
  ;;
post)
  cleanup() {
    if [ "$IMPORT" = 1 ]; then
      case "$TMP" in */"$DIR"/tmp-"$ENV"-"$TS") R rm -rf "$TMP" ;; *) echo "remote: ไม่ลบ $TMP (path ไม่ตรงรูปแบบ)" >&2 ;; esac
    fi
  }
  trap cleanup EXIT                                        # ลบ tmp import แม้ขั้นไหนพัง
  # ACF ฟรี (DEFERRED core-contract) — ข้ามถ้ามี ACF Pro active
  if Q "${WP[@]}" plugin is-active advanced-custom-fields-pro; then
    say "ACF Pro active อยู่ — ข้าม ACF ฟรี"
  elif Q "${WP[@]}" plugin is-installed advanced-custom-fields; then
    Q "${WP[@]}" plugin is-active advanced-custom-fields || R "${WP[@]}" plugin activate advanced-custom-fields
  else
    R "${WP[@]}" plugin install advanced-custom-fields --activate
  fi
  Q "${WP[@]}" theme is-active sharedheritage || R "${WP[@]}" theme activate sharedheritage
  R "${WP[@]}" rewrite structure '/%postname%/'
  R "${WP[@]}" rewrite flush --hard
  if [ "$IMPORT" = 1 ]; then
    R mkdir -p "$LOGS"
    say "import seed (mode missing = ไม่ทับของที่มี · sub-size ภาพ)"
    R env SH_IMPORT_ASSETS_DIR="$TMP/assets" SH_IMPORT_MEDIA_DIR="$TMP/media" SH_IMPORT_SUBSIZES=1 \
      SH_IMPORT_REPORT="$LOGS/import-$ENV-$TS.json" "${WP[@]}" eval-file "$TMP/scripts/import/import.php"
    say "รายงาน import: $LOGS/import-$ENV-$TS.json"
  fi
  if [ "$DRY" = 1 ]; then
    show "${WP[@]}" cli has-command litespeed-purge
    echo "  [remote]   (ถ้ามี — LiteSpeed Cache active):"
    show "${WP[@]}" litespeed-purge all
  elif "${WP[@]}" cli has-command litespeed-purge; then
    "${WP[@]}" litespeed-purge all
  else
    say "ไม่มีคำสั่ง litespeed-purge — ข้าม (purge เองใน wp-admin ถ้าจำเป็น)"
  fi
  ;;
esac
say "เสร็จ"
