#!/usr/bin/env bash
# media-encode: src WebM -> out WebM (<=3MB) + poster.webp + HEVC-alpha .mov (เฉพาะไฟล์ที่โปร่งใสจริง)
# แล้วเรียก manifest.py เขียน content/media-manifest.json   รันซ้ำได้ (ข้ามไฟล์ที่ out ใหม่กว่า src; FORCE=1 = ทำใหม่)
# ใช้: bash scripts/media/encode.sh        env: MEDIA_SRC MEDIA_OUT LIMIT_BYTES FORCE
set -euo pipefail
HERE="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
REPO="$(cd "$HERE/../.." && pwd)"
# media/ อยู่นอก repo: <Unesco>/media ; ใน worktree = <Unesco>/.worktrees/<wt>/scripts/media -> ขึ้น 4 ระดับ
UNESCO="${UNESCO_ROOT:-$(cd "$REPO/../.." 2>/dev/null && pwd)}"
[ -d "$UNESCO/media/src" ] || UNESCO="$(cd "$REPO/.." && pwd)"
SRC="${MEDIA_SRC:-$UNESCO/media/src/File Animated}"
OUT="${MEDIA_OUT:-$UNESCO/media/out}"
LIMIT="${LIMIT_BYTES:-3000000}"          # 3MB (ทศนิยม) เผื่อไว้ต่ำกว่า 3 MiB
MAXW="${MAXW:-1440}"; MAXFPS="${MAXFPS:-30}"
TMP="$(mktemp -d)"; trap 'rm -rf "$TMP"' EXIT
mkdir -p "$OUT"
command -v ffmpeg ffprobe cwebp python3 >/dev/null

# ffmpeg อ่าน stdin จะกิน list ของ while-loop -> บังคับ -nostdin
ffmpeg() { command ffmpeg -nostdin "$@"; }

slug() { # "Bas-relief of a procession.webm" -> bas-relief-of-a-procession
  local b; b="$(basename "$1")"; b="${b%.webm}"; b="${b%.webm}"
  printf '%s' "$b" | tr '[:upper:]' '[:lower:]' | sed -E 's/[^a-z0-9]+/-/g; s/^-+|-+$//g'
}
fsize() { stat -f%z "$1" 2>/dev/null || stat -c%s "$1"; }
# ความโปร่งใสจริง: ต้อง decode ด้วย libvpx-vp9 (decoder native ของ ffmpeg ไม่อ่าน alpha) แล้วดู alpha min ทุก 0.5s
alpha_min() {
  ffmpeg -v error -c:v libvpx-vp9 -i "$1" -vf "fps=2,format=yuva420p,alphaextract,signalstats,metadata=print:key=lavfi.signalstats.YMIN:file=-" -f null - 2>&1 \
    | sed -n 's/.*YMIN=//p' | sort -n | head -1
}
probe_f() { ffprobe -v error -select_streams v:0 -show_entries "$2" -of default=nw=1:nk=1 "$1" | head -1; }

find "$SRC" -type f -name '*.webm' | LC_ALL=C sort | while IFS= read -r f; do
  n="$(slug "$f")"; o="$OUT/$n.webm"; p="$OUT/$n.poster.webp"; m="$OUT/$n.hevc.mov"
  echo "== $n  <- $f"
  amin="$(alpha_min "$f")"; tag="$(probe_f "$f" stream_tags=alpha_mode)"
  # โปร่งใสจริง = alpha min < 250 (ไฟล์ opaque วัดได้ 254-255)
  if [ "${amin:-255}" -lt 250 ]; then real=1; else real=0; fi
  echo "   alpha_tag=${tag:-none} alpha_min=$amin real_transparency=$real"
  if [ "${FORCE:-0}" = 1 ] || [ ! -f "$o" ] || [ "$f" -nt "$o" ]; then
    sz="$(fsize "$f")"
    if [ "$sz" -le "$LIMIT" ]; then
      cp "$f" "$o"; echo "   copy (src ${sz}B <= limit)"
    else
      dur="$(ffprobe -v error -show_entries format=duration -of csv=p=0 "$f")"
      w="$(probe_f "$f" stream=width)"
      vf="scale='min($MAXW,iw)':-2:flags=lanczos,fps='min($MAXFPS,source_fps)'"
      # opaque -> ทิ้ง alpha plane (yuv420p) ; โปร่งใสจริง -> yuva420p
      if [ "$real" = 1 ]; then pf=yuva420p; extra="-auto-alt-ref 0"; else pf=yuv420p; extra=""; fi
      kbps="$(python3 -I -c "print(int($LIMIT*0.92*8/$dur/1000))")"
      while :; do
        rm -f "$TMP"/pass* 
        ffmpeg -v error -y -c:v libvpx-vp9 -i "$f" -vf "$vf,format=$pf" -an -c:v libvpx-vp9 -pix_fmt $pf $extra \
          -b:v ${kbps}k -maxrate $((kbps*3/2))k -deadline good -cpu-used 1 -row-mt 1 -tile-columns 1 -g 120 -pass 1 -passlogfile "$TMP/pass" -f null /dev/null
        ffmpeg -v error -y -c:v libvpx-vp9 -i "$f" -vf "$vf,format=$pf" -an -c:v libvpx-vp9 -pix_fmt $pf $extra \
          -b:v ${kbps}k -maxrate $((kbps*3/2))k -deadline good -cpu-used 1 -row-mt 1 -tile-columns 1 -g 120 -pass 2 -passlogfile "$TMP/pass" "$o"
        s="$(fsize "$o")"; echo "   encode ${kbps}kbps -> ${s}B"
        [ "$s" -le "$LIMIT" ] && break
        kbps=$((kbps*85/100)); [ "$kbps" -ge 50 ] || { echo "   FAIL: เกิน limit"; exit 1; }
      done
    fi
  else echo "   skip encode (out ใหม่กว่า src)"; fi
  # poster: เฟรมที่ 35% ของความยาว (PNG -> cwebp เก็บ alpha), กว้าง ≤640
  d="$(ffprobe -v error -show_entries format=duration -of csv=p=0 "$o")"
  t="$(python3 -I -c "print(round($d*${POSTER_AT:-0.35},3))")"
  ffmpeg -v error -y -c:v libvpx-vp9 -ss "$t" -i "$o" -frames:v 1 -vf "scale='min(640,iw)':-2:flags=lanczos,format=rgba" "$TMP/poster.png"
  cwebp -quiet -q 80 -alpha_q 90 "$TMP/poster.png" -o "$p"
  # fallback Safari/iOS: VP9-alpha ไม่รองรับ -> HEVC-alpha เฉพาะที่โปร่งใสจริง
  # (src เป็น stepped animation เฟรมละ 0.7-0.8s: fps=10 คงความยาวเท่า src ไม่งั้น mov สั้นกว่าเพราะเฟรมสุดท้ายไม่มี duration)
  # hevc_videotoolbox ไม่ deterministic ระดับไบต์ -> สร้างเฉพาะเมื่อไม่มี/FORCE=1/out ใหม่กว่า (manifest จึงนิ่งเมื่อรันซ้ำ)
  if [ "$real" = 1 ] && { [ "${FORCE:-0}" = 1 ] || [ ! -f "$m" ] || [ "$o" -nt "$m" ]; }; then
    rm -f "$m"
    ffmpeg -v error -y -c:v libvpx-vp9 -i "$o" -vf "fps=${FALLBACK_FPS:-10},format=bgra" -an -c:v hevc_videotoolbox -alpha_quality 0.75 -allow_sw 1 -b:v 1500k -tag:v hvc1 -movflags +faststart "$m"
    echo "   hevc-alpha: $(fsize "$m")B"
  fi
  if [ "$real" = 0 ]; then rm -f "$m"; fi
done
echo "(done encode)"
# hero ที่ไม่มี WebM จากเจ้าของ (D13): ต่อเฟรมจาก design/assets ตาม hero-frames.spec.json (สร้างด้วย hero_spec.py)
SPEC="$HERE/hero-frames.spec.json"
SPECARG=""
if [ -f "$SPEC" ]; then
  # SKIP_HERO_FRAMES=1 ข้ามแค่การ encode ต่อเฟรม — manifest ยังได้ spec เสมอ (ไม่มี sidecar ใน $OUT = manifest.py exit != 0)
  [ "${SKIP_HERO_FRAMES:-0}" = 1 ] || UNESCO_ROOT="$UNESCO" LIMIT_BYTES="$LIMIT" python3 -I "$HERE/hero_frames.py" "$SPEC" "$OUT"
  SPECARG="$SPEC"
fi
# manifest.py probe ไฟล์ src/out เองทั้งหมด (ตัวเลขทุกตัวมาจาก ffprobe)
python3 -I "$HERE/manifest.py" "$SRC" "$OUT" "$REPO/content/media-manifest.json" $SPECARG
