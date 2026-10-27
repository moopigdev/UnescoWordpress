# shellcheck shell=bash
# scripts/deploy/lib.sh — helpers ของ scripts/deploy.sh (source เท่านั้น · ไม่ใช่ entry point · ไม่เชื่อมต่ออะไร)
# bash 3.2 (macOS) compatible: ไม่มี assoc array / mapfile / ${x,,}

SH_DEPLOY_KEYS="SH_DEPLOY_SSH_HOST SH_DEPLOY_SSH_PORT SH_DEPLOY_SSH_USER SH_DEPLOY_SSH_KEY SH_DEPLOY_REMOTE_DIR
SH_STAGING_URL SH_STAGING_WP_PATH SH_PRODUCTION_URL SH_PRODUCTION_WP_PATH SH_DESIGN_ASSETS SH_MEDIA_OUT"

sh_die() { echo "deploy: ✗ $*" >&2; exit 1; }
sh_say() { echo "deploy: $*" >&2; }

# sh_load_config <file>... — อ่าน KEY=VALUE (ไม่ source · ไม่ eval) เฉพาะ key ใน SH_DEPLOY_KEYS
# ตั้งค่าเฉพาะ key ที่ยังไม่ถูกตั้ง → ไฟล์แรกชนะไฟล์หลัง · env ของ shell ชนะทุกไฟล์
sh_load_config() {
  local f line k v known x
  for f in "$@"; do
    [ -f "$f" ] || continue
    while IFS= read -r line || [ -n "$line" ]; do
      case "$line" in ''|'#'*) continue ;; esac
      case "$line" in *=*) ;; *) continue ;; esac
      k="${line%%=*}"; v="${line#*=}"
      k="$(printf '%s' "$k" | tr -d '[:space:]')"
      known=""
      for x in $SH_DEPLOY_KEYS; do [ "$x" = "$k" ] && known=1; done
      [ -n "$known" ] || continue
      case "$v" in \"*\") v="${v#\"}"; v="${v%\"}" ;; \'*\') v="${v#\'}"; v="${v%\'}" ;; esac
      [ -n "${!k+x}" ] && continue          # ตั้งไว้แล้ว (env หรือไฟล์ก่อนหน้า)
      printf -v "$k" '%s' "$v"
    done < "$f"
  done
  for x in $SH_DEPLOY_KEYS; do [ -n "${!x+x}" ] || printf -v "$x" '%s' ""; done
}

# ~/x → $HOME/x (ฝั่งเครื่องเรา)
sh_expand_home() { case "$1" in "~/"*) printf '%s\n' "$HOME/${1#\~/}" ;; *) printf '%s\n' "$1" ;; esac; }

# Unesco/ (โฟลเดอร์แม่ที่มี orchestrator/) จาก repo หรือ .worktrees/<wt> · ไม่พบ = ว่าง
sh_find_root() {
  local repo="$1"
  if [ -d "$repo/../orchestrator" ]; then (cd "$repo/.." && pwd -P)
  elif [ -d "$repo/../../orchestrator" ]; then (cd "$repo/../.." && pwd -P)
  fi
}

# sh_require_approval <env> <root> — production ต้องมี <root>/orchestrator/deploy-approved ที่ไม่ว่าง (fail-closed)
# ไม่มี override ทาง env/flag โดยตั้งใจ · คืน 0 = ผ่าน · ไม่ผ่าน = exit ≠ 0
sh_require_approval() {
  local env="$1" root="$2" f
  [ "$env" = production ] || return 0
  [ -n "$root" ] || sh_die "หา orchestrator/ ไม่พบ — production ต้องรันจาก Unesco/UnescoWordpress (หรือ worktree) ที่มี orchestrator/deploy-approved"
  f="$root/orchestrator/deploy-approved"
  [ -f "$f" ] || sh_die "production ต้องมี $f (orchestrator เขียนหลังเจ้าของตอบ \"ใช่\" ในแชท) — ไม่มี = ไม่ deploy"
  [ -s "$f" ] || sh_die "$f ว่าง — ต้องระบุสิ่งที่อนุมัติ + เวลา"
  sh_say "อนุมัติ production: $(head -3 "$f" | tr '\n' ' ')"
}

# พิมพ์คำสั่งแบบ copy-paste ได้ (quote ทุก argument)
sh_show() { local a out=""; for a in "$@"; do out="$out $(printf '%q' "$a")"; done; printf '  $%s\n' "$out"; }
