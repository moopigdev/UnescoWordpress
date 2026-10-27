#!/usr/bin/env bash
# scripts/deploy/remote.sh — ส่วนที่รัน "บน server" (Hostinger · wp-cli) · scripts/deploy.sh ส่งไฟล์นี้ทาง
#   ssh … bash -s -- <phase> [args] < scripts/deploy/remote.sh
# --dry-run = ไม่รันอะไรเลย พิมพ์คำสั่งที่จะรัน (deploy.sh ใช้รันไฟล์นี้ "ในเครื่อง" เพื่อพิมพ์แผน — ห้ามมีผลข้างเคียง)
#
# phase:
#   discover   อ่านอย่างเดียว: หา wp-config.php ใต้ ~/domains · พิมพ์ path + siteurl + home + theme ที่ active
#   preflight  อ่านอย่างเดียว: WP ติดตั้งแล้ว · siteurl ตรง --url · พิมพ์ theme/ACF/จำนวน object
#              production + --import=1 + มี object/timeline_item อยู่แล้ว → exit 3 (หยุดถามเจ้าของ · ห้าม import ทับ prod)
#   backup     (production) mysqldump (ตรงจาก shell) + tar wp-content → <dir>/backups/<env>-<ts>/ (chmod 700) + ROLLBACK.txt
#   prepare    mkdir mu-plugins (ถ้ายังไม่มี) · โฟลเดอร์ tmp import
#   post       ACF · activate theme · permalink /%postname%/ · import (ถ้า --import=1) · ลบ tmp · LiteSpeed purge
#              --import=refresh: ข้าม ACF/theme/permalink · import ด้วย SH_IMPORT_ONLY=refresh (ไม่สร้างอะไรใหม่ · refresh step เท่านั้น)
#              หลัง import: อ่าน runs[-1].warnings ของรายงาน → พิมพ์จำนวน + ทุกบรรทัด · > 0 = exit 4 (หลัง purge · tmp ถูกลบแล้ว)
# args: --env=staging|production --path=<WP absolute> --url=<siteurl ที่คาด> --dir=<งาน · relative กับ $HOME> --ts=<stamp> --import=0|1|refresh
#
# ⛔ Hostinger: PHP disable_functions = system exec shell_exec passthru popen proc_open … (ตรวจจริง 2026-10-27)
#   → wp-cli ที่ spawn process พังทั้งหมด: `db export|import|query|cli` (เรียก mysql/mysqldump) · `rewrite structure`
#     (เรียก `wp rewrite flush` ผ่าน runcommand/launch) · `rewrite flush --hard` · `--launch` ฯลฯ
#   → ไฟล์นี้ใช้เฉพาะ wp-cli ที่ทำงานใน process เดียว (option/plugin/theme/post list/config get/eval/eval-file/cache)
#     และเรียก mysqldump/mysql ตรงจาก shell (ไม่ผ่าน PHP)
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
case "$IMPORT" in 0|1|refresh) ;; *) fail "--import ต้องเป็น 0|1|refresh ($IMPORT)" ;; esac
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
  # ไม่ใช้ process substitution `< <(…)` — Hostinger `ssh … bash -s` ไม่มี /dev/fd · wp อ่าน stdin จาก /dev/null (ไม่กินรายการ)
  cfgs="$(find "$HOME/domains" -maxdepth 4 -name wp-config.php 2>/dev/null | sort)" || true
  if [ -z "$cfgs" ]; then
    say "ไม่พบ wp-config.php ใต้ ~/domains"
  else
    printf '%s\n' "$cfgs" | while IFS= read -r cfg; do
      [ -n "$cfg" ] || continue
      d="$(dirname "$cfg")"
      s="$(wp --path="$d" --skip-plugins --skip-themes option get siteurl 2>/dev/null </dev/null || echo '?')"
      h="$(wp --path="$d" --skip-plugins --skip-themes option get home 2>/dev/null </dev/null || echo '?')"
      t="$(wp --path="$d" --skip-plugins --skip-themes option get stylesheet 2>/dev/null </dev/null || echo '?')"
      printf '  path=%s\n    siteurl=%s · home=%s · theme=%s\n' "$d" "$s" "$h" "$t"
    done
  fi
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

# --- DB ตรงจาก shell (wp db export/import ใช้ proc_open → พังบน Hostinger) ---
# ค่าจาก `wp config get` (อ่าน wp-config.php · ไม่โหลด WP · ไม่ spawn) · รหัสผ่านไม่ผ่าน argv/stdout/log —
# อยู่ในไฟล์ option ชั่วคราว (mktemp = 600 · ในโฟลเดอร์ 700) ส่งให้ mysqldump ด้วย --defaults-extra-file แล้วลบทันที
NL=$'\n'; CNF=""; DB_NAME_=""; DB_CS_=""
cnf_q() { printf '%s' "$1" | sed 's/[\\"]/\\&/g'; }   # ค่าในบรรทัด key="…" ของ option file (printf = builtin → ค่าไม่ขึ้น ps)
# db_conn <file> — host/port/socket (ไม่มีความลับ) → <file> (600) · ตั้ง DB_NAME_ DB_CS_
db_conn() {
  local h port="" sock=""
  DB_NAME_="$(wp --path="$WP_PATH" config get DB_NAME)" || fail "อ่าน DB_NAME จาก wp-config ไม่ได้"
  h="$(wp --path="$WP_PATH" config get DB_HOST)" || fail "อ่าน DB_HOST จาก wp-config ไม่ได้"
  DB_CS_="$(wp --path="$WP_PATH" config get DB_CHARSET 2>/dev/null)" || DB_CS_=""
  [ -n "$DB_CS_" ] || DB_CS_=utf8mb4
  [ -n "$DB_NAME_" ] || fail "DB_NAME ว่าง"
  case "$DB_CS_" in *[!A-Za-z0-9_]*) fail "DB_CHARSET แปลก ($DB_CS_)" ;; esac
  case "$h" in                                   # DB_HOST = host | host:port | host:/socket
    *:/*) sock="/${h#*:/}"; h="${h%%:/*}" ;;
    *:*) port="${h##*:}"; h="${h%:*}" ;;
  esac
  case "$port" in *[!0-9]*) fail "port ใน DB_HOST แปลก ($port)" ;; esac
  [ -n "$h" ] || h=localhost
  case "$h$sock" in *"$NL"*) fail "DB_HOST มีขึ้นบรรทัดใหม่" ;; esac
  { printf 'host="%s"\n' "$(cnf_q "$h")"
    [ -z "$port" ] || printf 'port=%s\n' "$port"
    [ -z "$sock" ] || printf 'socket="%s"\n' "$(cnf_q "$sock")"
  } > "$1"
  chmod 600 "$1"
}
# db_cnf <conn-file> — [client] user/password (จาก wp-config) + conn → $CNF ชั่วคราว · ผู้เรียกต้อง rm (trap ด้วย)
db_cnf() {
  local u p
  u="$(wp --path="$WP_PATH" config get DB_USER)" || fail "อ่าน DB_USER จาก wp-config ไม่ได้"
  p="$(wp --path="$WP_PATH" config get DB_PASSWORD)" || fail "อ่าน DB_PASSWORD จาก wp-config ไม่ได้"
  case "$u$p" in *"$NL"*) fail "DB_USER/DB_PASSWORD มีขึ้นบรรทัดใหม่ — ใช้ option file ไม่ได้" ;; esac
  CNF="$(mktemp "$BASE/.db-cnf.XXXXXX")"        # $BASE = 700 · mktemp = 600
  { echo '[client]'
    printf 'user="%s"\n' "$(cnf_q "$u")"
    printf 'password="%s"\n' "$(cnf_q "$p")"
    cat "$1"
  } > "$CNF"
}
cnf_rm() { if [ -n "$CNF" ]; then rm -f "$CNF"; CNF=""; fi; }

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
  if [ "$IMPORT" = refresh ]; then
    say "refresh-only: เนื้อหาที่มีอยู่ไม่ถูกสร้างซ้ำ/ทับ (SH_IMPORT_ONLY=refresh) — ไม่หยุดเพราะมีเนื้อหา"
  elif [ "$ENV" = production ] && [ "$IMPORT" = 1 ]; then
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
  [ "$DRY" = 1 ] || command -v mysqldump >/dev/null || fail "ไม่พบ mysqldump บน server — ยังไม่ได้แตะอะไร"
  trap cnf_rm EXIT                                         # ลบไฟล์รหัสผ่านชั่วคราวแม้ dump พัง
  R mkdir -p "$BASE/backups"
  R chmod 700 "$BASE" "$BASE/backups"
  R mkdir -m 700 "$BK"
  # DB: mysqldump ตรง (ไม่ใช่ `wp db export` — spawn mysqldump ด้วย proc_open)
  DUMP_OPTS=(--single-transaction --quick --skip-lock-tables --no-tablespaces)
  if [ "$DRY" = 1 ]; then
    DB_NAME_='<DB_NAME>'; DB_CS_='<DB_CHARSET|utf8mb4>'
    show "${WP[@]}" config get DB_NAME '|' DB_HOST '|' DB_CHARSET
    echo "  [remote]   → $BK/db-conn.cnf (host/port/socket · ไม่มีรหัส · 600)"
    show "${WP[@]}" config get DB_USER '|' DB_PASSWORD
    echo "  [remote]   → \$(mktemp $BASE/.db-cnf.XXXXXX) (600 · ค่าไม่พิมพ์ · ลบหลัง dump แม้พัง)"
    show mysqldump --defaults-extra-file='<cnf>' "${DUMP_OPTS[@]}" --default-character-set="$DB_CS_" --result-file="$BK/db.sql" "$DB_NAME_"
    show rm -f '<cnf>'
    echo "  [remote]   db.sql บรรทัดสุดท้ายต้องมี 'Dump completed' (ไม่มี = หยุด ก่อนส่งไฟล์ใดๆ)"
  else
    db_conn "$BK/db-conn.cnf"
    db_cnf "$BK/db-conn.cnf"
    mysqldump --defaults-extra-file="$CNF" "${DUMP_OPTS[@]}" --default-character-set="$DB_CS_" \
      --result-file="$BK/db.sql" "$DB_NAME_" || fail "mysqldump ล้ม — ยังไม่ได้ส่งไฟล์ใดๆ"
    cnf_rm
    tail -n 1 "$BK/db.sql" | grep -q 'Dump completed' || fail "db.sql ไม่ครบ (ไม่มี 'Dump completed') — หยุดก่อนส่งไฟล์ใดๆ"
  fi
  if [ "$DRY" = 1 ]; then
    show "${WP[@]}" --skip-plugins --skip-themes option get stylesheet '>' "$BK/stylesheet.txt"
  else
    "${WP[@]}" --skip-plugins --skip-themes option get stylesheet > "$BK/stylesheet.txt"
  fi
  R tar -czf "$BK/wp-content.tar.gz" -C "$WP_PATH" wp-content
  R chmod 600 "$BK/db.sql" "$BK/wp-content.tar.gz"
  # ROLLBACK: หัว = ค่าจริงตอนสำรอง · ตัว = heredoc แบบ quote (ไม่ขยายตัวแปรตอนนี้) — ⛔ ไม่มี wp db import (proc_open)
  rollback_text() {
    cat <<EOF
# ROLLBACK $ENV — สำรองก่อน deploy $TS (รันบน server: ssh -p 65002 <user>@<host> · วางทั้งก้อนใน bash)
# ⛔ Hostinger ปิด proc_open/exec ใน PHP → ห้าม wp db import/export · คืน DB ด้วย mysql ตรง (รหัสจาก wp-config → ไฟล์ 600 ชั่วคราว → ลบ)
WP="$WP_PATH"; BK="$BK"; TS="$TS"; DB_NAME="$DB_NAME_"; DB_CS="$DB_CS_"
# theme เดิม = $(cat "$BK/stylesheet.txt" 2>/dev/null || echo '<ดู stylesheet.txt>')
EOF
    cat <<'EOF'
cd "$WP"
mv wp-content "wp-content.failed-$TS"                        # เก็บของที่ deploy ไว้ตรวจ
tar -xzf "$BK/wp-content.tar.gz" -C "$WP"                     # คืน wp-content (theme · mu-plugin · uploads · plugins)
q() { sed 's/[\\"]/\\&/g'; }
CNF="$(mktemp "$BK/.db-cnf.XXXXXX")"                           # 600 ในโฟลเดอร์ 700
{ echo '[client]'
  printf 'user="%s"\n' "$(wp --path="$WP" config get DB_USER | q)"
  printf 'password="%s"\n' "$(wp --path="$WP" config get DB_PASSWORD | q)"
  cat "$BK/db-conn.cnf"; } > "$CNF"
mysql --defaults-extra-file="$CNF" --default-character-set="$DB_CS" "$DB_NAME" < "$BK/db.sql"; echo "mysql exit=$? (0 = คืน DB แล้ว)"
rm -f "$CNF"
wp --path="$WP" cache flush
wp --path="$WP" litespeed-purge all || true
# ตรวจหน้าเว็บแล้วค่อยลบ: rm -rf "$WP/wp-content.failed-$TS"
EOF
  }
  if [ "$DRY" = 1 ]; then
    show rollback_text '>' "$BK/ROLLBACK.txt"
    echo "  [remote]   (ข้อความ ROLLBACK ด้านล่าง)"
  else
    rollback_text > "$BK/ROLLBACK.txt"
    ls -la "$BK"
  fi
  say "สำรองแล้ว → $BK · คำสั่ง rollback:"
  rollback_text
  ;;
prepare)
  [ "$IMPORT" = refresh ] || R mkdir -p "$WP_PATH/wp-content/mu-plugins"
  if [ "$IMPORT" != 0 ]; then
    R mkdir -p "$BASE"
    R chmod 700 "$BASE"
    R mkdir -p "$TMP"
  fi
  ;;
post)
  cleanup() {
    if [ "$IMPORT" != 0 ]; then
      case "$TMP" in */"$DIR"/tmp-"$ENV"-"$TS") R rm -rf "$TMP" ;; *) echo "remote: ไม่ลบ $TMP (path ไม่ตรงรูปแบบ)" >&2 ;; esac
    fi
  }
  trap cleanup EXIT                                        # ลบ tmp import แม้ขั้นไหนพัง
  # ACF ฟรี (DEFERRED core-contract) — ข้ามถ้ามี ACF Pro active · refresh-only ข้ามทั้งช่วง ACF/theme/permalink
  if [ "$IMPORT" = refresh ]; then
    say "refresh-only: ข้าม ACF · theme · permalink"
  elif Q "${WP[@]}" plugin is-active advanced-custom-fields-pro; then
    say "ACF Pro active อยู่ — ข้าม ACF ฟรี"
  elif Q "${WP[@]}" plugin is-installed advanced-custom-fields; then
    Q "${WP[@]}" plugin is-active advanced-custom-fields || R "${WP[@]}" plugin activate advanced-custom-fields
  else
    R "${WP[@]}" plugin install advanced-custom-fields --activate
  fi
  if [ "$IMPORT" != refresh ]; then
    Q "${WP[@]}" theme is-active sharedheritage || R "${WP[@]}" theme activate sharedheritage
  fi
  read -r -d '' PHP_PERMALINK <<'PHP' || true
global $wp_rewrite;
$want = '/%postname%/';
if ( get_option( 'permalink_structure' ) === $want ) {
	WP_CLI::log( 'permalink already ' . $want );
} else {
	$wp_rewrite->set_permalink_structure( $want );
	WP_CLI::success( 'permalink set ' . $want );
}
PHP
  read -r -d '' PHP_FLUSH <<'PHP' || true
flush_rewrite_rules( false );
if ( ! get_option( 'rewrite_rules' ) ) {
	WP_CLI::error( 'rewrite_rules empty after flush' );
}
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/misc.php';
$ht  = get_home_path() . '.htaccess';
$cur = is_readable( $ht ) ? (string) file_get_contents( $ht ) : '';
if ( false !== strpos( $cur, '# BEGIN WordPress' ) ) {
	WP_CLI::log( '.htaccess has WordPress block - unchanged' );
} else {
	add_filter( 'got_rewrite', '__return_true' );
	if ( ! save_mod_rewrite_rules() ) {
		WP_CLI::error( 'cannot write WordPress block to ' . $ht );
	}
	WP_CLI::success( 'wrote WordPress block to ' . $ht );
}
WP_CLI::success( 'rewrite rules flushed' );
PHP
  # permalink: ⛔ ไม่ใช้ `wp rewrite structure` (spawn `wp rewrite flush` ผ่าน launch) / `rewrite flush --hard` (proc_open)
  # → `wp eval` ใน process เดียว 2 ครั้ง: (1) ตั้ง structure (2) process ใหม่ (init เห็น structure ใหม่ · CPT ลงทะเบียนครบ) flush
  #   .htaccess: มีบล็อก `# BEGIN WordPress` แล้ว = ไม่แตะ · ไม่มี = เขียนบล็อก (save_mod_rewrite_rules) · เขียนไม่ได้ = หยุด
  if [ "$IMPORT" != refresh ]; then
    R "${WP[@]}" eval "$PHP_PERMALINK"
    R "${WP[@]}" eval "$PHP_FLUSH"
  fi
  # คำเตือนของ import = runs[-1].warnings ของรายงาน (ไม่ใช่ระดับบนสุด) · ไม่มีรายงาน/ไม่มี key = error (ไม่ถือว่า 0)
  read -r -d '' PHP_WARN <<'PHP' || true
$f    = (string) getenv( 'SH_IMPORT_REPORT_READ' );
$r    = is_readable( $f ) ? json_decode( (string) file_get_contents( $f ), true ) : null;
$last = is_array( $r ) && ! empty( $r['runs'] ) && is_array( $r['runs'] ) ? end( $r['runs'] ) : null;
if ( ! is_array( $last ) || ! isset( $last['warnings'] ) || ! is_array( $last['warnings'] ) ) {
	WP_CLI::error( 'import report has no runs[-1].warnings: ' . $f );
}
foreach ( $last['warnings'] as $w ) {
	WP_CLI::log( '  WARNING: ' . $w ); // ASCII only: dry-run prints this code with printf %q (bash 3.2 splits UTF-8).
}
WP_CLI::log( 'SH_IMPORT_WARNINGS=' . count( $last['warnings'] ) );
PHP
  IMPORT_WARN=0
  if [ "$IMPORT" != 0 ]; then
    R mkdir -p "$LOGS"
    REPORT="$LOGS/import-$ENV-$TS.json"
    if [ "$IMPORT" = refresh ]; then
      say "import: refresh-only (SH_IMPORT_ONLY=refresh · ไม่สร้างใหม่ · แก้เฉพาะค่าที่ยังเป็นของ import · sub-size ภาพ)"
      ONLY=(SH_IMPORT_ONLY=refresh)
    else
      say "import seed (mode missing = ไม่ทับของที่มี · sub-size ภาพ)"
      ONLY=()
    fi
    R env SH_IMPORT_ASSETS_DIR="$TMP/assets" SH_IMPORT_MEDIA_DIR="$TMP/media" SH_IMPORT_SUBSIZES=1 "${ONLY[@]+"${ONLY[@]}"}" \
      SH_IMPORT_REPORT="$REPORT" "${WP[@]}" eval-file "$TMP/scripts/import/import.php"
    say "รายงาน import: $REPORT"
    if [ "$DRY" = 1 ]; then
      show env SH_IMPORT_REPORT_READ="$REPORT" "${WP[@]}" eval "$PHP_WARN"
      echo "  [remote]   (> 0 = พิมพ์ ⛔ + exit 4 หลัง purge · ไม่มี runs[-1].warnings = หยุด)"
    else
      wout="$(env SH_IMPORT_REPORT_READ="$REPORT" "${WP[@]}" eval "$PHP_WARN")" || fail "อ่านคำเตือนจากรายงาน $REPORT ไม่ได้ — ตรวจเอง (runs[-1].warnings)"
      printf '%s\n' "$wout"
      nw="$(printf '%s\n' "$wout" | sed -n 's/^SH_IMPORT_WARNINGS=\([0-9][0-9]*\)$/\1/p' | tail -1)"
      [ -n "$nw" ] || fail "หา SH_IMPORT_WARNINGS ในผลไม่เจอ — ตรวจรายงาน $REPORT เอง"
      if [ "$nw" = 0 ]; then
        say "คำเตือน import: 0 (runs[-1].warnings)"
      else
        echo "remote[$PHASE]: ⛔ คำเตือน import: $nw รายการ (runs[-1].warnings ของ $REPORT) — รายการอยู่ด้านบน" >&2
        IMPORT_WARN="$nw"
      fi
    fi
  fi
  # cache: ⛔ ล้มตรงนี้ = warning ไม่ใช่ fatal (deploy เสร็จแล้ว) — `wp litespeed-purge all` ยิง HTTP ไปที่เว็บ
  #   (staging ไม่มี DNS → "Could not notify the web server to purge.")
  #   ลำดับ: (1) `wp cache flush` (object cache · ใน process เดียว · ล้ม = warning)
  #          (2) `wp litespeed-purge all` (ถ้ามีคำสั่ง · ล้ม = warning)
  #   ⛔ ไม่ลบไฟล์ใต้ wp-content/litespeed/ เอง: page cache (ลบจาก CLI ไม่ได้ถ้าไม่ผ่าน HTTP) ยังอ้าง CSS/JS ที่ minify ไว้ → 404
  PURGE_WARN=0
  warn() { echo "remote[$PHASE]: ⚠ $*" >&2; PURGE_WARN=1; }
  if [ "$DRY" = 1 ]; then
    show "${WP[@]}" cache flush
    echo "  [remote]   (ถ้าล้ม = warning ไม่ fatal)"
    show "${WP[@]}" cli has-command litespeed-purge
    echo "  [remote]   (ถ้ามี — LiteSpeed Cache active · ล้ม = warning ไม่ fatal):"
    show "${WP[@]}" litespeed-purge all
  else
    "${WP[@]}" cache flush || warn "wp cache flush ล้ม — ข้าม (deploy ไม่ได้รับผล)"
    if "${WP[@]}" cli has-command litespeed-purge; then
      "${WP[@]}" litespeed-purge all || warn "wp litespeed-purge all ล้ม (CLI ต้องยิง HTTP ไปที่เว็บ — DNS/ไม่ถึงจาก server) — deploy สำเร็จแล้ว"
    else
      say "ไม่มีคำสั่ง litespeed-purge — ข้าม"
    fi
    if [ "$PURGE_WARN" = 1 ]; then
      say "⚠ purge ไม่ครบ (ไม่กระทบ deploy) — ให้ purge เองใน wp-admin › LiteSpeed Cache › Toolbox › Purge All หรือ hPanel › LiteSpeed Cache (page cache อาจค้างจนหมดอายุ)"
    fi
  fi
  if [ "$IMPORT_WARN" != 0 ]; then
    echo "remote[$PHASE]: ⛔ import มีคำเตือน $IMPORT_WARN รายการ — ไม่ใช่ 0 · exit 4" >&2
    exit 4
  fi
  ;;
esac
say "เสร็จ"
