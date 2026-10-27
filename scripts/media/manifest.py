#!/usr/bin/env python3
"""เขียน content/media-manifest.json จาก ffprobe จริงของไฟล์ src + out (รันซ้ำได้ผลเดิม · ไม่มี timestamp)
usage: manifest.py <src_dir> <out_dir> <manifest.json> [hero-frames.spec.json]
 รายการ owner-webm (จาก src_dir) + figma-frames (จาก spec · เฟรม hero ต่อเป็นวิดีโอ/ภาพนิ่ง) · ทุกรายการมี object_slug · source · kind · usage"""
import json, os, re, subprocess, sys

src_dir, out_dir, dest = sys.argv[1:4]
spec = json.load(open(sys.argv[4])) if len(sys.argv) > 4 else None
OWNER = (spec or {}).get("owner_webm", {})
LIMIT = int(os.environ.get("LIMIT_BYTES", "3000000"))


def run(cmd):
    return subprocess.run(cmd, check=True, capture_output=True, text=True).stdout


def slug(path):
    b = os.path.basename(path)
    b = re.sub(r"\.webm$", "", b)
    b = re.sub(r"\.webm$", "", b)
    return re.sub(r"[^a-z0-9]+", "-", b.lower()).strip("-")


def probe(path, vcodec=None, count=True):
    cmd = ["ffprobe", "-v", "error"]
    if vcodec:
        cmd += ["-c:v", vcodec]
    cmd += ["-select_streams", "v:0", "-show_entries",
            "stream=codec_name,pix_fmt,width,height,avg_frame_rate:stream_tags=alpha_mode:format=duration",
            "-of", "json", path]
    j = json.loads(run(cmd))
    st = j["streams"][0]
    n, d = st["avg_frame_rate"].split("/")
    fps = round(int(n) / int(d), 3)
    frames = None
    if count:
        cf = json.loads(run(["ffprobe", "-v", "error", "-count_frames", "-select_streams", "v:0", "-show_entries",
                             "stream=nb_read_frames", "-of", "json", path] if not vcodec else
                            ["ffprobe", "-v", "error", "-c:v", vcodec, "-count_frames", "-select_streams", "v:0",
                             "-show_entries", "stream=nb_read_frames", "-of", "json", path]))
        frames = int(cf["streams"][0]["nb_read_frames"])
    return {"frames": frames, "codec": st["codec_name"], "pix_fmt": st["pix_fmt"], "width": st["width"], "height": st["height"],
            "fps": fps, "duration_s": round(float(j["format"]["duration"]), 3),
            "alpha_tag": st.get("tags", {}).get("alpha_mode") == "1"}


def alpha_min(path):
    """alpha ต่ำสุดจากการสุ่ม 2 เฟรม/วินาที (decode ด้วย libvpx-vp9 เพราะ decoder native ไม่อ่าน alpha)"""
    r = subprocess.run(
        ["ffmpeg", "-v", "error", "-c:v", "libvpx-vp9", "-i", path, "-vf",
         "fps=2,format=yuva420p,alphaextract,signalstats,metadata=print:key=lavfi.signalstats.YMIN:file=-",
         "-f", "null", "-"], capture_output=True, text=True)
    vals = [int(x) for x in re.findall(r"YMIN=(\d+)", r.stdout + r.stderr)]
    return min(vals) if vals else 255


rel = lambda p: os.path.relpath(p, os.path.dirname(os.path.dirname(os.path.abspath(src_dir))))
items = []
for root, _, files in os.walk(src_dir):
    for f in files:
        if f.endswith(".webm"):
            items.append(os.path.join(root, f))
items.sort()
res = []
for s in items:
    n = slug(s)
    o = os.path.join(out_dir, n + ".webm")
    if not os.path.exists(o):
        sys.exit("ไม่พบ out: " + o + " (รัน encode.sh ก่อน)")
    ps, po = probe(s, "libvpx-vp9"), probe(o, "libvpx-vp9")
    a_src, a_out = alpha_min(s), alpha_min(o)
    # has_alpha = โปร่งใสจริง (มีพิกเซล alpha < 250) ; alpha_tag = ไฟล์ติด alpha_mode=1 แต่ plane อาจทึบทั้งหมด
    has_alpha = a_src < 250
    poster = os.path.join(out_dir, n + ".poster.webp")
    mov = os.path.join(out_dir, n + ".hevc.mov")
    fb = None
    if has_alpha:
        if not os.path.exists(mov):
            sys.exit("has_alpha แต่ไม่มี fallback: " + mov)
        pm = probe(mov)
        assert abs(pm["duration_s"] - po["duration_s"]) < 0.25, ("fallback duration ผิด", pm, po)
        assert pm["codec"] == "hevc", pm
        fb = {"type": "hevc-alpha-mov", "file": "media/out/" + n + ".hevc.mov", "bytes": os.path.getsize(mov),
              "codec": pm["codec"], "pix_fmt": pm["pix_fmt"], "duration_s": pm["duration_s"]}
    else:
        fb = {"type": "poster-only", "file": "media/out/" + n + ".poster.webp",
              "note": "ทึบ ไม่มี transparency: เบราว์เซอร์ที่เล่น VP9 WebM ได้ใช้ตัว WebM ตรงๆ ; ที่เล่นไม่ได้แสดง poster"}
    ow = OWNER.get(n)
    if spec is not None and ow is None:
        sys.exit("ไฟล์ owner webm ไม่มีใน spec.owner_webm (จับคู่ object_slug): " + n)
    res.append({
        "id": n,
        "object_slug": ow["object_slug"] if ow else None,
        "source": "owner-webm",
        "kind": "video",
        "usage": ow["usage"] if ow else None,
        "src": "media/src/" + os.path.relpath(s, os.path.join(os.path.dirname(src_dir.rstrip("/")))),
        "out": "media/out/" + n + ".webm",
        "poster": "media/out/" + n + ".poster.webp",
        "fallback": fb,
        "bytes": os.path.getsize(o),
        "src_bytes": os.path.getsize(s),
        "poster_bytes": os.path.getsize(poster),
        "duration_s": po["duration_s"],
        "width": po["width"],
        "height": po["height"],
        "fps": po["fps"],
        "frames": po["frames"],
        "effective_fps": round(po["frames"] / po["duration_s"], 3),
        "has_alpha": has_alpha,
        "alpha_tag": ps["alpha_tag"],
        "alpha_min_src": a_src,
        "alpha_min_out": a_out,
        "src_width": ps["width"], "src_height": ps["height"], "src_fps": ps["fps"], "src_frames": ps["frames"], "src_effective_fps": round(ps["frames"] / ps["duration_s"], 3), "src_duration_s": ps["duration_s"],
        "within_limit": os.path.getsize(o) <= LIMIT,
    })

def fig_items():
    """รายการที่ต่อเฟรมจาก Figma (hero_frames.py สร้างไฟล์ไว้ใน out_dir แล้ว)"""
    for it in (spec or {}).get("items", []):
        n = it["slug"]
        side = json.load(open(os.path.join(out_dir, n + ".frames.json")))
        p = os.path.join(out_dir, n + ".poster.webp")
        src_ref = "figma:%s (%s) hero %s · %d layer" % (it["figma_frame_id"], it["figma_frame_name"], it["hero_node"], len(it["layers"]))
        common = {
            "id": n, "object_slug": n, "source": "figma-frames", "usage": "object-hero",
            "src": src_ref + " · design/assets/<ref>.png",
            "poster": "media/out/" + n + ".poster.webp",
            "poster_bytes": os.path.getsize(p),
            "src_bytes": side["src_bytes"],
            "figma_frame_id": it["figma_frame_id"], "figma_frame_name": it["figma_frame_name"],
            "frame_refs": side["frame_refs"], "steps": len(it["layers"]),
            "frame_duration_s": spec["frame_s"],
        }
        if it["mode"] == "still":
            st = os.path.join(out_dir, n + ".still.webp")
            pr = json.loads(run(["ffprobe", "-v", "error", "-select_streams", "v:0", "-show_entries", "stream=width,height", "-of", "json", st]))["streams"][0]
            assert not os.path.exists(os.path.join(out_dir, n + ".webm")), "still แต่มี webm ค้าง: " + n
            yield {**common, "kind": "still", "out": None, "still": "media/out/" + n + ".still.webp", "fallback": None,
                   "bytes": os.path.getsize(st), "duration_s": None, "width": pr["width"], "height": pr["height"], "fps": None,
                   "frames": 1, "effective_fps": None, "has_alpha": side["has_alpha"], "alpha_tag": None,
                   "alpha_min_src": side["alpha_min_src"], "alpha_min_out": None,
                   "note": "เฟรมที่แสดงจริงมี 1 ภาพ -> ภาพนิ่ง (D13) · layer อื่นของ hero ใน Figma ซ่อนอยู่ (visible=false) ดู spec.excluded",
                   "within_limit": os.path.getsize(st) <= LIMIT}
            continue
        o = os.path.join(out_dir, n + ".webm")
        po = probe(o, "libvpx-vp9")
        a_out = alpha_min(o)
        has_alpha = side["has_alpha"]
        assert (a_out < 250) == has_alpha, ("alpha src/out ไม่ตรงกัน", n, side["alpha_min_src"], a_out)
        assert abs(po["duration_s"] - len(it["layers"]) * spec["frame_s"]) < 0.05, ("ความยาวผิด", n, po)
        mov = os.path.join(out_dir, n + ".hevc.mov")
        if has_alpha:
            if not os.path.exists(mov):
                sys.exit("has_alpha แต่ไม่มี fallback: " + mov)
            pm = probe(mov)
            assert abs(pm["duration_s"] - po["duration_s"]) < 0.25, ("fallback duration ผิด", pm, po)
            assert pm["codec"] == "hevc", pm
            fb = {"type": "hevc-alpha-mov", "file": "media/out/" + n + ".hevc.mov", "bytes": os.path.getsize(mov),
                  "codec": pm["codec"], "pix_fmt": pm["pix_fmt"], "duration_s": pm["duration_s"]}
        else:
            assert not os.path.exists(mov), "ทึบแต่มี hevc ค้าง: " + mov
            fb = {"type": "poster-only", "file": "media/out/" + n + ".poster.webp",
                  "note": "ทึบ ไม่มี transparency: เบราว์เซอร์ที่เล่น VP9 WebM ได้ใช้ตัว WebM ตรงๆ ; ที่เล่นไม่ได้แสดง poster"}
        yield {**common, "kind": "video", "out": "media/out/" + n + ".webm", "fallback": fb, "bytes": os.path.getsize(o),
               "duration_s": po["duration_s"], "width": po["width"], "height": po["height"], "fps": po["fps"],
               "frames": po["frames"], "effective_fps": round(po["frames"] / po["duration_s"], 3),
               "has_alpha": has_alpha, "alpha_tag": po["alpha_tag"], "alpha_min_src": side["alpha_min_src"], "alpha_min_out": a_out,
               "crf": side["crf"], "within_limit": os.path.getsize(o) <= LIMIT}


res.extend(sorted(fig_items(), key=lambda r: r["id"]))
doc = {"limit_bytes": LIMIT, "generated_by": "scripts/media/encode.sh", "files": res}
os.makedirs(os.path.dirname(os.path.abspath(dest)), exist_ok=True)
with open(dest, "w") as fh:
    json.dump(doc, fh, indent=2, ensure_ascii=False)
    fh.write("\n")
print("manifest:", dest, len(res), "files")
