#!/usr/bin/env bash
# media-1117: วัตถุ 126 ไฟล์ (16 trail) + ภาพ/GIF Intro -> media/out/{objects-1117,intro-1117} + เพิ่ม entry ใน content/media-manifest.json
# ใช้: bash scripts/media/encode-1117.sh   env: FORCE=1 LIMIT_BYTES JOBS INTRO_MAXW (ดู objects_1117.py)
# ต้องรัน encode.sh (เดิม) ก่อนถ้า manifest ยังไม่มี entry เดิม · encode.sh รันซ้ำจะคง entry 1117 ไว้ (manifest.py เก็บให้)
set -euo pipefail
HERE="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
REPO="$(cd "$HERE/../.." && pwd)"
UNESCO="${UNESCO_ROOT:-$(cd "$REPO/../.." 2>/dev/null && pwd)}"
[ -d "$UNESCO/media/src" ] || UNESCO="$(cd "$REPO/.." && pwd)"
SRC="${MEDIA_SRC_1117:-$UNESCO/media/src/drive-2026-11-17}"
OUT="${MEDIA_OUT:-$UNESCO/media/out}"
command -v ffmpeg ffprobe cwebp gif2webp webpmux python3 >/dev/null
[ -d "$SRC/animals-webm" ] || { echo "ไม่พบต้นฉบับ: $SRC/animals-webm (บล็อก O-5)"; exit 1; }
python3 -I "$HERE/objects_1117.py" "$SRC" "$OUT" "$REPO/content/media-manifest.json"
