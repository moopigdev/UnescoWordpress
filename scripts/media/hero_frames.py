#!/usr/bin/env python3
"""media-hero-frames: ต่อเฟรม hero จาก design/assets (ตาม scripts/media/hero-frames.spec.json) -> WebM/ภาพนิ่ง
ต่อวัตถุ (ชื่อไฟล์ = slug ใน seed):
  video (>=2 เฟรม): <slug>.webm (VP9 · 10fps CFR · เฟรมละ 0.7s · -an · ≤ LIMIT_BYTES) + <slug>.poster.webp (เฟรมแรก)
                    + <slug>.hevc.mov เฉพาะที่โปร่งใสจริง (alpha min < 250)
  still (1 เฟรม):   <slug>.still.webp (ขนาด hero เต็ม) + <slug>.poster.webp (<=640 กว้าง) · ไม่ทำวิดีโอ
  ทุกรายการ:        <slug>.frames.json (sidecar: alpha_min ต่อเฟรม · crf · ใช้โดย manifest.py)
usage: hero_frames.py <spec.json> <out_dir>      env: DESIGN_ASSETS LIMIT_BYTES HERO_CRF FALLBACK_FPS FORCE
รันซ้ำได้: ข้ามถ้า out ใหม่กว่า spec (FORCE=1 = ทำใหม่) · HEVC ไม่สร้างใหม่ถ้ามี (videotoolbox ไม่ deterministic)"""
import json, os, shutil, subprocess, sys, tempfile

spec_path, out_dir = sys.argv[1:3]
spec = json.load(open(spec_path))
here = os.path.dirname(os.path.abspath(spec_path))
repo = os.path.abspath(os.path.join(here, "..", ".."))
unesco = os.environ.get("UNESCO_ROOT") or os.path.abspath(os.path.join(repo, "..", ".."))
if not os.path.isdir(os.path.join(unesco, "design")):
    unesco = os.path.abspath(os.path.join(repo, ".."))
assets = os.environ.get("DESIGN_ASSETS") or os.path.join(unesco, "design", "assets")
LIMIT = int(os.environ.get("LIMIT_BYTES", "3000000"))
CRF0 = int(os.environ.get("HERO_CRF", "24"))
FB_FPS = os.environ.get("FALLBACK_FPS", "10")
FORCE = os.environ.get("FORCE", "0") == "1"
FPS = spec["fps"]
FRAME_S = spec["frame_s"]
os.makedirs(out_dir, exist_ok=True)


def ff(*a, check=True):
    return subprocess.run(["ffmpeg", "-nostdin", "-v", "error", "-y", *a], check=check, capture_output=True, text=True)


def alpha_min_png(p):
    r = ff("-i", p, "-vf", "format=rgba,alphaextract,signalstats,metadata=print:key=lavfi.signalstats.YMIN:file=-", "-f", "null", "-")
    import re
    v = re.findall(r"YMIN=(\d+)", r.stdout + r.stderr)
    return min(int(x) for x in v)


def newer(a, b):  # a ใหม่กว่า b
    return os.path.exists(a) and os.path.exists(b) and os.path.getmtime(a) >= os.path.getmtime(b)


def poster_from(png, dest):
    ff("-i", png, "-vf", "scale='min(640,iw)':-2:flags=lanczos,format=rgba", png + ".p640.png")
    subprocess.run(["cwebp", "-quiet", "-q", "80", "-alpha_q", "90", png + ".p640.png", "-o", dest], check=True)


for it in spec["items"]:
    slug = it["slug"]
    o = os.path.join(out_dir, slug + ".webm")
    p = os.path.join(out_dir, slug + ".poster.webp")
    m = os.path.join(out_dir, slug + ".hevc.mov")
    st = os.path.join(out_dir, slug + ".still.webp")
    side = os.path.join(out_dir, slug + ".frames.json")
    primary = o if it["mode"] == "video" else st
    print("==", slug, it["mode"], "%dx%d" % (it["width"], it["height"]), len(it["layers"]), "layer(s)")
    if not FORCE and os.path.exists(side) and newer(primary, spec_path) and newer(p, spec_path):
        print("   skip (out ใหม่กว่า spec)")
        continue
    tmp = tempfile.mkdtemp(prefix="hero-")
    try:
        pngs, amins = [], []
        for l in it["layers"]:
            dst = os.path.join(tmp, "f%02d.png" % l["order"])
            ff("-i", os.path.join(assets, l["file"]), "-vf", it["vf"], dst)
            pngs.append(dst)
            amins.append(alpha_min_png(dst))
        amin = min(amins)
        real = amin < 250
        print("   alpha_min(frames)=%d real_transparency=%d" % (amin, real))
        info = {"slug": slug, "mode": it["mode"], "alpha_min_frames": amins, "alpha_min_src": amin,
                "has_alpha": real, "frame_refs": [l["ref"] for l in it["layers"]],
                "src_bytes": sum(os.path.getsize(os.path.join(assets, l["file"])) for l in it["layers"])}
        if it["mode"] == "still":
            for f in (o, m):
                if os.path.exists(f):
                    os.remove(f)
            subprocess.run(["cwebp", "-quiet", "-q", "85", "-alpha_q", "90", pngs[0], "-o", st], check=True)
            poster_from(pngs[0], p)
            info["crf"] = None
            print("   still %dB poster %dB" % (os.path.getsize(st), os.path.getsize(p)))
        else:
            n = len(pngs)
            lst = os.path.join(tmp, "list.txt")
            with open(lst, "w") as fh:
                for f in pngs:
                    fh.write("file '%s'\nduration %s\n" % (f, FRAME_S))
                fh.write("file '%s'\n" % pngs[-1])
            pf, extra = ("yuva420p", ["-auto-alt-ref", "0"]) if real else ("yuv420p", [])
            total = round(n * FRAME_S, 3)
            crf = CRF0
            while True:
                base = ["-f", "concat", "-safe", "0", "-i", lst, "-vf", "fps=%d,format=%s" % (FPS, pf), "-t", str(total), "-an",
                        "-c:v", "libvpx-vp9", "-pix_fmt", pf, *extra, "-b:v", "0", "-crf", str(crf), "-deadline", "good",
                        "-cpu-used", "1", "-row-mt", "1", "-tile-columns", "1", "-g", "120", "-passlogfile", os.path.join(tmp, "pass")]
                ff(*base, "-pass", "1", "-f", "null", "/dev/null")
                ff(*base, "-fflags", "+bitexact", "-flags:v", "+bitexact", "-pass", "2", o)
                s = os.path.getsize(o)
                print("   encode crf=%d -> %dB" % (crf, s))
                if s <= LIMIT:
                    break
                crf += 3
                if crf > 55:
                    sys.exit("FAIL: เกิน limit แม้ crf 55: " + slug)
            info["crf"] = crf
            poster_from(pngs[0], p)
            if real:
                if FORCE or not os.path.exists(m) or os.path.getmtime(o) > os.path.getmtime(m):
                    if os.path.exists(m):
                        os.remove(m)
                    ff("-c:v", "libvpx-vp9", "-i", o, "-vf", "fps=%s,format=bgra" % FB_FPS, "-an", "-c:v", "hevc_videotoolbox",
                       "-alpha_quality", "0.75", "-allow_sw", "1", "-b:v", "1500k", "-tag:v", "hvc1", "-movflags", "+faststart", m)
                    print("   hevc-alpha: %dB" % os.path.getsize(m))
            elif os.path.exists(m):
                os.remove(m)
            if os.path.exists(st):
                os.remove(st)
        with open(side, "w") as fh:
            json.dump(info, fh, indent=2)
            fh.write("\n")
    finally:
        shutil.rmtree(tmp, ignore_errors=True)
print("(done hero frames)")
