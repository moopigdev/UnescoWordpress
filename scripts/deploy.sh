#!/usr/bin/env bash
# deploy.sh — ขึ้น Shared Heritage ไป Hostinger (staging / production) ผ่าน SSH + rsync + wp-cli บน server
#
# usage:
#   scripts/deploy.sh [staging|production] [--dry-run] [--discover] [--no-import]
#     (ไม่ระบุ = staging)
#   --dry-run    พิมพ์ทุกคำสั่งที่จะรัน (ssh · rsync · wp-cli ฝั่ง server) ⛔ ไม่เชื่อมต่อ server
#   --discover   อ่านอย่างเดียว: SSH ไปหา WordPress ใต้ ~/domains → พิมพ์ path + siteurl ให้ยืนยัน (ไม่เขียนอะไร)
#   --no-import  ส่งแค่โค้ด (theme + mu-plugin) + ACF/theme/permalink/purge · ไม่ import seed
#
# ลำดับ (real run):
#   0 ตรวจ local (config · key · ไฟล์) · production: ต้องมี orchestrator/deploy-approved + tree สะอาด
#   1 preflight (อ่านอย่างเดียว): siteurl ของ WP path ต้องตรง URL ที่คาด · production+import: มีเนื้อหาแล้ว = หยุด (exit 3)
#   2 production: สำรอง DB + wp-content นอก webroot + พิมพ์คำสั่ง rollback (ก่อนเขียนอะไรทั้งหมด)
#   3 prepare: mkdir mu-plugins · tmp import (นอก webroot)
#   4 rsync mu-plugin แล้ว theme (--delete เฉพาะในโฟลเดอร์ของเรา) · bundle import → tmp
#   5 post: ACF · activate theme · permalink /%postname%/ + flush · import (SH_IMPORT_SUBSIZES=1) · ลบ tmp · LiteSpeed purge
# config: scripts/deploy/hosts.env (ไม่ commit) > scripts/deploy/hosts.env.example · env ของ shell ชนะไฟล์
# รายละเอียด + rollback: docs/DEPLOY.md · ⛔ dev ไม่รันกับ server จริง — orchestrator รัน (staging ก่อนเสมอ)
set -euo pipefail

REPO="$(cd "$(dirname "$0")/.." && pwd -P)"
HERE="$REPO/scripts/deploy"
# shellcheck source=deploy/lib.sh
. "$HERE/lib.sh"

ENV=staging; DRY=0; DISCOVER=0; IMPORT=1; ENV_SET=0
for a in "$@"; do
  case "$a" in
    staging|production) [ "$ENV_SET" = 0 ] || sh_die "ระบุ environment ได้ครั้งเดียว"; ENV="$a"; ENV_SET=1 ;;
    --dry-run) DRY=1 ;;
    --discover) DISCOVER=1 ;;
    --no-import) IMPORT=0 ;;
    -h|--help) sed -n '2,19p' "$0"; exit 0 ;;
    *) sh_die "ไม่รู้จัก argument: $a (ดู --help)" ;;
  esac
done

ROOT="$(sh_find_root "$REPO")"
sh_load_config "$HERE/hosts.env" "$HERE/hosts.env.example"
# ↑ lib ตั้งค่าเป็นตัวแปร shell — บอก shellcheck ว่ามีจริง
: "${SH_DEPLOY_SSH_HOST?}" "${SH_DEPLOY_SSH_PORT?}" "${SH_DEPLOY_SSH_USER?}" "${SH_DEPLOY_SSH_KEY?}" "${SH_DEPLOY_REMOTE_DIR?}"
: "${SH_STAGING_URL?}" "${SH_STAGING_WP_PATH?}" "${SH_PRODUCTION_URL?}" "${SH_PRODUCTION_WP_PATH?}" "${SH_DESIGN_ASSETS?}" "${SH_MEDIA_OUT?}"

# --- 0. production gate ก่อนอย่างอื่น (dry-run ก็ต้องมี — hook ก็บล็อก) ---
sh_require_approval "$ENV" "$ROOT"

if [ "$ENV" = production ]; then URL="$SH_PRODUCTION_URL"; WP_PATH="$SH_PRODUCTION_WP_PATH"
else URL="$SH_STAGING_URL"; WP_PATH="$SH_STAGING_WP_PATH"; fi
ENV_UP="$(printf '%s' "$ENV" | tr '[:lower:]' '[:upper:]')"
HOST="$SH_DEPLOY_SSH_HOST"; PORT="$SH_DEPLOY_SSH_PORT"; USER_="$SH_DEPLOY_SSH_USER"
KEY="$(sh_expand_home "$SH_DEPLOY_SSH_KEY")"; RDIR="$SH_DEPLOY_REMOTE_DIR"
TS="$(date +%Y%m%d-%H%M%S)"
[ -n "$HOST" ] && [ -n "$PORT" ] && [ -n "$USER_" ] && [ -n "$KEY" ] && [ -n "$URL" ] || sh_die "config ไม่ครบ (SSH host/port/user/key · URL) — ดู scripts/deploy/hosts.env.example"
case "$PORT" in *[!0-9]*) sh_die "SH_DEPLOY_SSH_PORT ต้องเป็นตัวเลข" ;; esac
case "$KEY$HOST$USER_" in *[[:space:]]*) sh_die "SSH key path / host / user ห้ามมีช่องว่าง (rsync -e แยกคำด้วยช่องว่าง)" ;; esac
case "$RDIR" in ''|/*|*..*) sh_die "SH_DEPLOY_REMOTE_DIR ต้อง relative กับ home ไม่มี .. ($RDIR)" ;; esac
if [ -n "$SH_STAGING_WP_PATH" ] && [ "${SH_STAGING_WP_PATH%/}" = "${SH_PRODUCTION_WP_PATH%/}" ]; then
  sh_die "SH_STAGING_WP_PATH = SH_PRODUCTION_WP_PATH — staging ต้องเป็นคนละ WP"
fi
if [ "$ENV" = staging ]; then sh_guard_staging_target; fi   # F1: staging ห้ามชี้ไป production (URL host · WP path) — ก่อนเชื่อมต่อ

SSH=(ssh -p "$PORT" -i "$KEY" -o IdentitiesOnly=yes -o BatchMode=yes -o StrictHostKeyChecking=accept-new -o ConnectTimeout=20)
RSH="ssh -p $PORT -i $KEY -o IdentitiesOnly=yes -o BatchMode=yes -o StrictHostKeyChecking=accept-new"
DEST="$USER_@$HOST"

# remote <phase> [args] — real: ส่ง remote.sh ทาง stdin · dry-run: พิมพ์คำสั่ง ssh แล้วรัน remote.sh --dry-run ในเครื่อง (พิมพ์อย่างเดียว)
remote() {
  local phase="$1" q=""; shift
  local a; for a in "$phase" "$@"; do q="$q $(printf '%q' "$a")"; done
  if [ "$DRY" = 1 ]; then
    sh_show "${SSH[@]}" "$DEST" "bash -s --$q" '<' scripts/deploy/remote.sh
    bash "$HERE/remote.sh" "$phase" --dry-run "$@" 2>&1
  else
    "${SSH[@]}" "$DEST" "bash -s --$q" < "$HERE/remote.sh"
  fi
}
# xrsync <args> — real: rsync · dry-run: พิมพ์
xrsync() { if [ "$DRY" = 1 ]; then sh_show rsync "$@"; else rsync "$@"; fi; }

echo "== deploy $ENV · $URL · $(git -C "$REPO" rev-parse --short HEAD 2>/dev/null || echo '?') · $TS$([ "$DRY" = 1 ] && echo ' · DRY-RUN (ไม่เชื่อมต่อ)')"

# --- discover (อ่านอย่างเดียว) ---
if [ "$DISCOVER" = 1 ]; then
  if [ "$DRY" = 0 ]; then [ -f "$KEY" ] || sh_die "ไม่พบ SSH key $KEY"; fi
  remote discover
  exit 0
fi

# --- 0. ตรวจ local ---
if [ -z "$WP_PATH" ]; then
  [ "$DRY" = 1 ] || sh_die "SH_${ENV_UP}_WP_PATH ยังไม่ตั้ง — รัน scripts/deploy.sh staging --discover แล้วใส่ใน scripts/deploy/hosts.env"
  WP_PATH="<SH_${ENV_UP}_WP_PATH>"
  sh_say "⚠ SH_${ENV_UP}_WP_PATH ยังไม่ตั้ง — แผนด้านล่างใช้ $WP_PATH แทน (real run จะหยุด)"
else
  case "$WP_PATH" in /*) ;; *) sh_die "SH_${ENV_UP}_WP_PATH ต้องเป็น absolute path ($WP_PATH)" ;; esac
  WP_PATH="${WP_PATH%/}"
fi
if [ "$DRY" = 0 ]; then
  [ -f "$KEY" ] || sh_die "ไม่พบ SSH key $KEY"
  command -v rsync >/dev/null || sh_die "ไม่พบ rsync"
fi
THEME="$REPO/wp-content/themes/sharedheritage"
MU="$REPO/wp-content/mu-plugins"
[ -d "$THEME" ] || sh_die "ไม่พบ theme $THEME"
[ -d "$MU" ] || sh_die "ไม่พบ mu-plugins $MU"
DIRTY="$(git -C "$REPO" status --porcelain -- wp-content scripts/import content 2>/dev/null || true)"
if [ -n "$DIRTY" ]; then
  [ "$ENV" = production ] && sh_die "production ต้อง deploy จาก commit — มีไฟล์ค้างใน wp-content/scripts/import/content:
$DIRTY"
  sh_say "⚠ มีไฟล์ยังไม่ commit (ส่งตามที่อยู่ในเครื่อง):"; printf '%s\n' "$DIRTY" >&2
fi

EXCL=(--exclude=.git --exclude=.playground --exclude=node_modules --exclude=.DS_Store)
RS=(-rlptzv -e "$RSH")   # ไม่ใช้ -a (-o -g ตั้ง owner ไม่ได้บน shared host) · flag ที่ openrsync ของ macOS รองรับเท่านั้น

# bundle import (เฉพาะไฟล์ที่ import อ่าน): scripts/import · content · ภาพที่ image-map "files" อ้าง · media/out
LB=""
cleanup_local() { if [ -n "$LB" ]; then case "$LB" in */sh-deploy-bundle.*) rm -rf "$LB" ;; esac; fi; }
trap cleanup_local EXIT
if [ "$IMPORT" = 1 ]; then
  ASSETS="${SH_DESIGN_ASSETS:-}"; MEDIA="${SH_MEDIA_OUT:-}"
  [ -n "$ASSETS" ] || { [ -n "$ROOT" ] && ASSETS="$ROOT/design/assets"; }
  [ -n "$MEDIA" ] || { [ -n "$ROOT" ] && MEDIA="$ROOT/media/out"; }
  ASSETS="$(sh_expand_home "${ASSETS:-}")"; MEDIA="$(sh_expand_home "${MEDIA:-}")"
  [ -d "$ASSETS" ] || sh_die "ไม่พบภาพ design/assets ($ASSETS) — ตั้ง SH_DESIGN_ASSETS หรือใช้ --no-import"
  [ -d "$MEDIA" ] || sh_die "ไม่พบ media/out ($MEDIA) — ตั้ง SH_MEDIA_OUT หรือใช้ --no-import"
  LIST="$(python3 - "$REPO/scripts/import/image-map.json" <<'PY'
import json, sys
m = json.load(open(sys.argv[1]))
for f in sorted(set((m.get("files") or {}).values())):
    if "/" in f or f.startswith("."):
        sys.exit("image-map: ชื่อไฟล์ไม่ปลอดภัย: " + f)
    print(f)
PY
)"
  NA=0; MISS=""
  while IFS= read -r f; do
    [ -n "$f" ] || continue
    if [ -f "$ASSETS/$f" ]; then NA=$((NA + 1)); else MISS="$MISS $f"; fi
  done <<< "$LIST"
  [ -z "$MISS" ] || sh_die "ภาพที่ image-map อ้างแต่ไม่มีใน $ASSETS:$MISS"
  sh_say "bundle import: scripts/import + content + ภาพ $NA ไฟล์ (จาก image-map) + media/out"
  if [ "$DRY" = 0 ]; then
    LB="$(mktemp -d "${TMPDIR:-/tmp}/sh-deploy-bundle.XXXXXX")"
    mkdir -p "$LB/scripts" "$LB/assets" "$LB/media"
    cp -R "$REPO/scripts/import" "$LB/scripts/import"
    cp -R "$REPO/content" "$LB/content"
    while IFS= read -r f; do [ -n "$f" ] && cp "$ASSETS/$f" "$LB/assets/$f"; done <<< "$LIST"
    for f in "$MEDIA"/*; do
      [ -f "$f" ] || continue
      case "$f" in *.html) continue ;; esac            # preview.html = หน้าเทสในเครื่อง
      cp "$f" "$LB/media/"
    done
  else
    LB_SHOW="<local-bundle>"
    sh_say "  <local-bundle> = mktemp ในเครื่อง: scripts/import/ · content/ · assets/ ($NA ไฟล์จาก $ASSETS) · media/ (จาก $MEDIA ยกเว้น *.html)"
  fi
fi

RARGS=(--env="$ENV" --path="$WP_PATH" --url="$URL" --prod-url="$SH_PRODUCTION_URL" --dir="$RDIR" --ts="$TS" --import="$IMPORT")

echo "-- 1. preflight (อ่านอย่างเดียว)"
set +e; remote preflight "${RARGS[@]}"; rc=$?; set -e
if [ "$rc" = 3 ]; then sh_say "⛔ หยุด: production มีเนื้อหาแล้ว — ถามเจ้าของก่อน (ส่งแค่โค้ด: --no-import)"; exit 3; fi
[ "$rc" = 0 ] || sh_die "preflight ไม่ผ่าน (exit $rc) — ยังไม่ได้แตะ server"

if [ "$ENV" = production ]; then
  echo "-- 2. backup production (DB + wp-content → ~/$RDIR/backups/$ENV-$TS)"
  remote backup "${RARGS[@]}"
fi

echo "-- 3. prepare"
remote prepare "${RARGS[@]}"

echo "-- 4. rsync (mu-plugin → theme)"
for e in "$MU"/*; do
  [ -e "$e" ] || continue
  b="$(basename "$e")"
  if [ -d "$e" ]; then   # --delete เฉพาะในโฟลเดอร์ของเราเอง
    xrsync "${RS[@]}" --delete "${EXCL[@]}" "$e/" "$DEST:$WP_PATH/wp-content/mu-plugins/$b/"
  else                   # ไฟล์ loader — ไม่มี --delete (ห้ามแตะ mu-plugin อื่นของ Hostinger)
    xrsync "${RS[@]}" "$e" "$DEST:$WP_PATH/wp-content/mu-plugins/$b"
  fi
done
xrsync "${RS[@]}" --delete "${EXCL[@]}" "$THEME/" "$DEST:$WP_PATH/wp-content/themes/sharedheritage/"
if [ "$IMPORT" = 1 ]; then
  xrsync "${RS[@]}" "${EXCL[@]}" "${LB:-${LB_SHOW:-}}/" "$DEST:$RDIR/tmp-$ENV-$TS/"
fi

echo "-- 5. post (ACF · theme · permalink · import · ลบ tmp · purge)"
remote post "${RARGS[@]}"

echo "== เสร็จ: $ENV $URL"
if [ "$ENV" = production ]; then
  echo "   rollback: ~/$RDIR/backups/$ENV-$TS/ROLLBACK.txt บน server (docs/DEPLOY.md §Rollback)"
  echo "   ⛔ orchestrator: ลบ orchestrator/deploy-approved ทันที (อนุมัติใช้ครั้งเดียว)"
fi
