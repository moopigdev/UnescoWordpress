#!/usr/bin/env python3
"""media-1117: วัตถุ 126 ไฟล์ (16 trail · Drive 2026-11-17) + ภาพ/GIF Intro -> media/out/{objects-1117,intro-1117}
แล้วเพิ่ม entry เข้า content/media-manifest.json (ไม่แตะ entry เดิม · รันซ้ำได้ผลเดิม · ไม่มี timestamp)

usage: objects_1117.py <src_root> <out_root> <manifest.json>
  src_root = <Unesco>/media/src/drive-2026-11-17   out_root = <Unesco>/media/out
env: LIMIT_BYTES (3000000) MAXW (1440) MAXFPS (30) POSTER_AT (0.35) FALLBACK_FPS (10) INTRO_MAXW (2880) FORCE (0) JOBS (4)
     ONLY = รายชื่อ "<Trail>/<ไฟล์>.webm" คั่นด้วย "|" -> ทำเฉพาะไฟล์เหล่านี้ (ไม่แตะ Intro · entry อื่นไม่เปลี่ยน · ใช้เมื่อ Drive เพิ่มไฟล์ใหม่ทีหลัง)

มาตรฐานเดียวกับ encode.sh: src <= 3MB และกว้าง <= 1440 -> copy (ไม่ re-encode) · เกิน -> VP9 2-pass -an ลดกว้าง/บิตเรต
poster = เฟรม 35% WebP q80 กว้าง <= 640 · alpha จริง (decode libvpx-vp9 วัด alpha min < 250) -> HEVC-alpha .hevc.mov · ทึบ -> fallback = poster
ตัวเลขทุกตัวมาจาก ffprobe/ffmpeg/webpmux จริง"""
import hashlib, json, os, re, shutil, subprocess, sys, tempfile, unicodedata
from concurrent.futures import ThreadPoolExecutor

SRC, OUT, DEST = [os.path.abspath(a) for a in sys.argv[1:4]]
E = os.environ.get
LIMIT = int(E("LIMIT_BYTES", "3000000"))
MAXW = int(E("MAXW", "1440"))
MAXFPS = int(E("MAXFPS", "30"))
POSTER_AT = float(E("POSTER_AT", "0.35"))
FALLBACK_FPS = int(E("FALLBACK_FPS", "10"))
INTRO_MAXW = int(E("INTRO_MAXW", "2880"))  # 2x ความกว้างแสดงผล 1440
FORCE = E("FORCE", "0") == "1"
JOBS = int(E("JOBS", "4"))
ONLY = [x for x in E("ONLY", "").split("|") if x]

MEDIA_ROOT = os.path.dirname(OUT)  # <Unesco>/media
KEY_PREFIX_OBJ, KEY_PREFIX_INTRO = "drive-1117/", "intro-1117/"
# โฟลเดอร์ trail ของ Drive -> slug trail ใน content/seed/trails.json
TRAIL_SLUG = {"Chicken&Rooster": "rooster", "Serpent": "naga", "Waterbird": "water-bird"}
# ชื่อไฟล์ที่ไม่มีอักษร ASCII เลย -> ตั้งชื่อ ASCII ให้ (ตัดสินเอง: พิมพ์เค้กม้า = แม่พิมพ์เค้กรูปม้า)
NAME_OVERRIDE = {"พิมพ์เค้กม้า": "horse-cake-mold"}


def run(cmd, **kw):
    return subprocess.run(cmd, check=True, capture_output=True, text=True, stdin=subprocess.DEVNULL, **kw).stdout


def fsize(p):
    return os.path.getsize(p)


def ascii_slug(name):
    """ชื่อไฟล์ (ไม่รวม .webm) -> ASCII kebab-case · Đ->d · ตัดอักษรนอก ASCII · ตัด ' ’"""
    if name in NAME_OVERRIDE:
        return NAME_OVERRIDE[name]
    s = name.replace("Đ", "D").replace("đ", "d").replace(" ", " ")
    s = unicodedata.normalize("NFKD", s)
    s = "".join(c for c in s if not unicodedata.combining(c))
    s = re.sub(r"['’‘`]", "", s)
    s = s.encode("ascii", "ignore").decode()
    return re.sub(r"[^a-z0-9]+", "-", s.lower()).strip("-")


def norm_key(s):
    """ชื่อที่ปรับให้เทียบง่าย: NFKC · NBSP/ช่องว่างซ้ำ -> 1 ช่อง · ตัดช่องว่างหัวท้ายก่อน .webm"""
    s = unicodedata.normalize("NFKC", s).replace(" ", " ")
    s = re.sub(r"\s+", " ", s)
    return re.sub(r"\s+\.webm$", ".webm", s).strip()


def probe(path, vcodec=None, count=True):
    cmd = ["ffprobe", "-v", "error"] + (["-c:v", vcodec] if vcodec else [])
    cmd += ["-select_streams", "v:0", "-show_entries",
            "stream=codec_name,pix_fmt,width,height,avg_frame_rate:stream_tags=alpha_mode:format=duration", "-of", "json", path]
    j = json.loads(run(cmd))
    st = j["streams"][0]
    n, d = st["avg_frame_rate"].split("/")
    fr = None
    if count:
        c = ["ffprobe", "-v", "error"] + (["-c:v", vcodec] if vcodec else []) + [
            "-count_frames", "-select_streams", "v:0", "-show_entries", "stream=nb_read_frames", "-of", "json", path]
        fr = int(json.loads(run(c))["streams"][0]["nb_read_frames"])
    return {"frames": fr, "codec": st["codec_name"], "pix_fmt": st["pix_fmt"], "width": st["width"], "height": st["height"],
            "fps": round(int(n) / int(d), 3), "duration_s": round(float(j["format"].get("duration", 0)), 3),
            "alpha_tag": st.get("tags", {}).get("alpha_mode") == "1"}


def has_audio(path):
    return bool(run(["ffprobe", "-v", "error", "-select_streams", "a", "-show_entries", "stream=codec_type", "-of", "csv=p=0", path]).strip())


def alpha_min(path):
    """alpha ต่ำสุดทุก 0.5s (decode ด้วย libvpx-vp9 เพราะ decoder native ไม่อ่าน alpha)"""
    r = subprocess.run(["ffmpeg", "-nostdin", "-v", "error", "-c:v", "libvpx-vp9", "-i", path, "-vf",
                        "fps=2,format=yuva420p,alphaextract,signalstats,metadata=print:key=lavfi.signalstats.YMIN:file=-",
                        "-f", "null", "-"], capture_output=True, text=True)
    vals = [int(x) for x in re.findall(r"YMIN=(\d+)", r.stdout + r.stderr)]
    return min(vals) if vals else 255


def content_md5(path):
    """md5 ของเฟรมที่ decode (หาไฟล์ซ้ำที่ต่างกันแค่ metadata/UID ของ mux)"""
    r = run(["ffmpeg", "-nostdin", "-v", "error", "-c:v", "libvpx-vp9", "-i", path, "-map", "0:v:0", "-f", "md5", "-"])
    return r.strip().replace("MD5=", "")


def file_sha(path):
    h = hashlib.sha256()
    with open(path, "rb") as f:
        for b in iter(lambda: f.read(1 << 20), b""):
            h.update(b)
    return h.hexdigest()


def encode_vp9(src, dst, real_alpha, dur, tmp, src_bytes):
    """VP9 2-pass constrained-quality · กว้าง <= MAXW · fps <= MAXFPS · -an · ไม่เกิน LIMIT
    เพดานบิตเรต = min(งบ LIMIT, บิตเรตของ src) -> ไฟล์ที่ย่อขนาดอย่างเดียว (เช่น 1080p -> 1440x810) ไม่ใหญ่กว่า src
    ถ้าเกิน LIMIT ลดเพดาน 15% วนซ้ำ (เหมือน encode.sh)"""
    vf = "scale='min(%d,iw)':-2:flags=lanczos,fps='min(%d,source_fps)'" % (MAXW, MAXFPS)
    pf, extra = ("yuva420p", ["-auto-alt-ref", "0"]) if real_alpha else ("yuv420p", [])
    budget = int(LIMIT * 0.92 * 8 / dur / 1000)
    kbps = max(50, min(budget, int(src_bytes * 8 / dur / 1000)))
    log = os.path.join(tmp, "pass")
    while True:
        for f in glob_clean(log):
            os.remove(f)
        base = ["ffmpeg", "-nostdin", "-v", "error", "-y", "-c:v", "libvpx-vp9", "-i", src, "-vf", vf + ",format=" + pf, "-an",
                "-c:v", "libvpx-vp9", "-pix_fmt", pf] + extra + ["-crf", "30", "-b:v", "%dk" % kbps, "-maxrate", "%dk" % (kbps * 3 // 2),
                "-deadline", "good", "-cpu-used", "1", "-row-mt", "1", "-tile-columns", "1", "-g", "120", "-passlogfile", log]
        run(base + ["-pass", "1", "-f", "null", "/dev/null"])
        run(base + ["-pass", "2", dst])
        if fsize(dst) <= LIMIT:
            return kbps
        kbps = kbps * 85 // 100
        if kbps < 50:
            raise SystemExit("FAIL เกิน limit: " + src)


def glob_clean(prefix):
    d = os.path.dirname(prefix)
    return [os.path.join(d, f) for f in os.listdir(d) if f.startswith(os.path.basename(prefix))]


# ---------------------------------------------------------------- วัตถุ (WebM)
def collect_objects():
    base = os.path.join(SRC, "animals-webm")
    items = []
    for trail in sorted(os.listdir(base)):
        for f in sorted(os.listdir(os.path.join(base, trail))):
            if f.endswith(".webm"):
                items.append((trail, f))
    # ชื่อ out: ASCII kebab · ซ้ำข้าม trail (หรือซ้ำกับไฟล์เดิมใน media/out) -> ต่อท้าย -<trail-slug> ทุกตัวที่ซ้ำ
    existing = {os.path.splitext(n)[0].split(".")[0] for n in os.listdir(OUT) if os.path.isfile(os.path.join(OUT, n))}
    cnt = {}
    for trail, f in items:
        s = ascii_slug(re.sub(r"\.webm$", "", f))
        cnt[s] = cnt.get(s, 0) + 1
    res, seen = [], set()
    for trail, f in items:
        s = ascii_slug(re.sub(r"\.webm$", "", f))
        ts = TRAIL_SLUG.get(trail, trail.lower())
        if not s:
            raise SystemExit("slug ว่าง: %r" % f)
        if cnt[s] > 1 or s in existing:
            s = s + "-" + ts
        assert s not in seen, ("slug ชน", s)
        seen.add(s)
        res.append({"trail": trail, "trail_slug": ts, "file": f, "slug": s,
                    "src": os.path.join(base, trail, f), "key": KEY_PREFIX_OBJ + trail + "/" + f})
    return res


def process_object(it):
    src, s, ts = it["src"], it["slug"], it["trail_slug"]
    od = os.path.join(OUT, "objects-1117", ts)
    os.makedirs(od, exist_ok=True)
    o, p, m = (os.path.join(od, s + x) for x in (".webm", ".poster.webp", ".hevc.mov"))
    rel = lambda path: "media/" + os.path.relpath(path, MEDIA_ROOT)
    tmp = tempfile.mkdtemp(prefix="m1117-")
    try:
        ps = probe(src, "libvpx-vp9")
        audio = has_audio(src)
        a_src = alpha_min(src)
        real = a_src < 250
        sz = fsize(src)
        enc = "copy"
        if FORCE or not os.path.exists(o) or os.path.getmtime(src) > os.path.getmtime(o):
            if sz <= LIMIT and ps["width"] <= MAXW and not audio:
                shutil.copyfile(src, o)
            else:
                encode_vp9(src, o, real, ps["duration_s"], tmp, sz)
                enc = "vp9-2pass"
        else:
            enc = "skip(exists)"
        po = probe(o, "libvpx-vp9")
        a_out = alpha_min(o)
        assert (a_out < 250) == real, ("alpha src/out ไม่ตรงกัน", src, a_src, a_out)
        if enc == "skip(exists)":
            enc = "copy" if fsize(o) == sz else "vp9-2pass"
        # poster
        t = round(po["duration_s"] * POSTER_AT, 3)
        png = os.path.join(tmp, "poster.png")
        run(["ffmpeg", "-nostdin", "-v", "error", "-y", "-c:v", "libvpx-vp9", "-ss", str(t), "-i", o, "-frames:v", "1",
             "-vf", "scale='min(640,iw)':-2:flags=lanczos,format=rgba", png])
        run(["cwebp", "-quiet", "-q", "80", "-alpha_q", "90", png, "-o", p])
        # fallback Safari/iOS
        if real:
            if FORCE or not os.path.exists(m) or os.path.getmtime(o) > os.path.getmtime(m):
                if os.path.exists(m):
                    os.remove(m)
                run(["ffmpeg", "-nostdin", "-v", "error", "-y", "-c:v", "libvpx-vp9", "-i", o, "-vf",
                     "fps=%d,format=bgra" % FALLBACK_FPS, "-an", "-c:v", "hevc_videotoolbox", "-alpha_quality", "0.75",
                     "-allow_sw", "1", "-b:v", "1500k", "-tag:v", "hvc1", "-movflags", "+faststart", m])
            pm = probe(m)
            assert pm["codec"] == "hevc", pm
            assert abs(pm["duration_s"] - po["duration_s"]) < 0.25, ("fallback duration ผิด", pm, po)
            fb = {"type": "hevc-alpha-mov", "file": rel(m), "bytes": fsize(m), "codec": pm["codec"],
                  "pix_fmt": pm["pix_fmt"], "duration_s": pm["duration_s"]}
        else:
            if os.path.exists(m):
                os.remove(m)
            fb = {"type": "poster-only", "file": rel(p),
                  "note": "ทึบ ไม่มี transparency: เบราว์เซอร์ที่เล่น VP9 WebM ได้ใช้ตัว WebM ตรงๆ ; ที่เล่นไม่ได้แสดง poster"}
        return {
            "id": it["key"], "key": it["key"], "key_normalized": KEY_PREFIX_OBJ + it["trail"] + "/" + norm_key(it["file"]),
            "trail": it["trail"], "trail_slug": ts, "object_slug": None, "source": "drive-2026-11-17", "kind": "video",
            "usage": "object-hero",
            "src": "media/src/" + os.path.relpath(src, os.path.join(MEDIA_ROOT, "src")),
            "out": rel(o), "poster": rel(p), "fallback": fb,
            "bytes": fsize(o), "src_bytes": sz, "poster_bytes": fsize(p),
            "duration_s": po["duration_s"], "width": po["width"], "height": po["height"], "fps": po["fps"],
            "frames": po["frames"], "effective_fps": round(po["frames"] / po["duration_s"], 3),
            "has_alpha": real, "alpha_tag": ps["alpha_tag"], "alpha_min_src": a_src, "alpha_min_out": a_out,
            "src_width": ps["width"], "src_height": ps["height"], "src_fps": ps["fps"], "src_has_audio": audio,
            "src_duration_s": ps["duration_s"], "encode": enc,
            "src_content_md5": content_md5(src), "src_sha256": file_sha(src),
            "within_limit": fsize(o) <= LIMIT,
        }
    finally:
        shutil.rmtree(tmp, ignore_errors=True)


# ---------------------------------------------------------------- Intro (ภาพนิ่ง + GIF)
def webp_info(path):
    """webpmux -info -> canvas · animated · loop (0 = วนไม่จบ) · duration ms ต่อเฟรม"""
    t = run(["webpmux", "-info", path])
    m = re.search(r"Canvas size: (\d+) x (\d+)", t)
    lp = re.search(r"Loop Count\s*:\s*(\d+)", t)
    durs = [int(x) for x in re.findall(r"^\s*\d+:\s+\d+\s+\d+\s+\w+\s+\d+\s+\d+\s+(\d+)\s", t, re.M)]
    return {"animated": "animation" in t.split("\n")[1] if len(t.split("\n")) > 1 else False,
            "width": int(m.group(1)), "height": int(m.group(2)), "loop": int(lp.group(1)) if lp else None, "frame_ms": durs}


def collect_intro():
    base = os.path.join(SRC, "introduction")
    items = []
    for folder in sorted(os.listdir(base)):
        for f in sorted(os.listdir(os.path.join(base, folder))):
            if f.lower().endswith((".png", ".jpg", ".jpeg", ".gif")):
                items.append((folder, f))
    return items


def intro_folder_slug(folder):
    return ascii_slug(folder.replace("_", " "))


INTRO_SLUG_COUNT = {}


def process_intro(folder, f):
    src = os.path.join(SRC, "introduction", folder, f)
    stem, ext = os.path.splitext(f)
    fs = intro_folder_slug(folder)
    od = os.path.join(OUT, "intro-1117", fs)
    os.makedirs(od, exist_ok=True)
    s = ascii_slug(stem)
    if INTRO_SLUG_COUNT.get(s, 0) > 1:  # ชื่อซ้ำข้ามบท (เช่น Intro logo.png) -> ต่อท้ายเลขบท เพื่อให้ basename ไม่ชน
        s += "-" + fs.split("-")[0] + "-" + fs.split("-")[1]
    rel = lambda path: "media/" + os.path.relpath(path, MEDIA_ROOT)
    tmp = tempfile.mkdtemp(prefix="i1117-")
    try:
        base = {"id": KEY_PREFIX_INTRO + folder + "/" + f, "key": KEY_PREFIX_INTRO + folder + "/" + f,
                "folder": folder, "object_slug": None, "source": "drive-2026-11-17", "usage": "intro",
                "src": "media/src/" + os.path.relpath(src, os.path.join(MEDIA_ROOT, "src")), "src_bytes": fsize(src),
                "src_sha256": file_sha(src)}
        if ext.lower() == ".gif":
            o = os.path.join(od, s + ".webp")
            pst = os.path.join(od, s + ".poster.webp")
            gi = probe(src, count=True)
            # animated WebP: เล็กกว่า GIF ~4-5 เท่า · มี alpha · ใช้ใน <img> ได้เหมือน GIF · loop/จังหวะเฟรมคงเดิม
            if FORCE or not os.path.exists(o) or os.path.getmtime(src) > os.path.getmtime(o):
                run(["gif2webp", "-mixed", "-q", "80", "-m", "6", src, "-o", o])
            info = webp_info(o)
            png = os.path.join(tmp, "f0.png")
            run(["ffmpeg", "-nostdin", "-v", "error", "-y", "-i", src, "-frames:v", "1", "-vf", "scale='min(%d,iw)':-2:flags=lanczos,format=rgba" % MAXW, png])
            run(["cwebp", "-quiet", "-q", "80", "-alpha_q", "90", png, "-o", pst])
            gdur = gi["duration_s"]
            gfr = gi["frames"]
            return {**base, "key_normalized": base["key"], "kind": "animation", "format": "webp-animated", "out": rel(o), "still": None,
                    "poster": rel(pst), "fallback": {"type": "poster-only", "file": rel(pst),
                                                    "note": "animated WebP (Safari 14+/iOS 14+) ; ที่ไม่รองรับแสดง poster = เฟรมแรก"},
                    "bytes": fsize(o), "poster_bytes": fsize(pst), "width": info["width"], "height": info["height"],
                    "frames": gfr, "duration_s": gdur, "frame_ms": info["frame_ms"], "loop": info["loop"],
                    "src_width": gi["width"], "src_height": gi["height"], "src_frames": gfr, "src_duration_s": gdur,
                    "has_alpha": True, "within_limit": fsize(o) <= LIMIT}
        o = os.path.join(od, s + ".webp")
        pi = probe(src, count=False)
        if FORCE or not os.path.exists(o) or os.path.getmtime(src) > os.path.getmtime(o):
            png = os.path.join(tmp, "r.png")
            run(["ffmpeg", "-nostdin", "-v", "error", "-y", "-i", src, "-vf",
                 "scale='min(%d,iw)':-2:flags=lanczos" % INTRO_MAXW, "-frames:v", "1", png])
            run(["cwebp", "-quiet", "-q", "82", "-alpha_q", "95", png, "-o", o])
        info = webp_info(o)
        sha_alpha = pi["pix_fmt"] in ("rgba", "bgra", "ya8", "pal8") and ext.lower() == ".png"
        return {**base, "key_normalized": base["key"], "kind": "still", "format": "webp", "out": None, "still": rel(o),
                "poster": None, "fallback": None, "bytes": fsize(o), "poster_bytes": None,
                "width": info["width"], "height": info["height"], "frames": 1, "duration_s": None,
                "src_width": pi["width"], "src_height": pi["height"], "has_alpha": sha_alpha,
                "within_limit": fsize(o) <= LIMIT}
    finally:
        shutil.rmtree(tmp, ignore_errors=True)


def main():
    objs = collect_objects()
    if ONLY:
        sel = [o for o in objs if (o["trail"] + "/" + o["file"]) in ONLY]
        missing = set(ONLY) - {o["trail"] + "/" + o["file"] for o in sel}
        if missing:
            raise SystemExit("ONLY ไม่พบในต้นฉบับ: %r" % sorted(missing))
        objs = sel
    with ThreadPoolExecutor(JOBS) as ex:
        obj_entries = list(ex.map(process_object, objs))
    doc = json.load(open(DEST)) if os.path.exists(DEST) else {"limit_bytes": LIMIT, "generated_by": "scripts/media/encode.sh", "files": []}
    if ONLY:
        # เพิ่ม/แทนเฉพาะ entry วัตถุที่ระบุ · entry อื่น (รวม Intro) คงไบต์เดิม · เรียงวัตถุตาม key เหมือนรันเต็ม · คำนวณ dup_of ใหม่ทั้งชุดด้วยกติกาเดิม
        new_ids = {e["id"] for e in obj_entries}
        old_obj = [e for e in doc["files"] if str(e.get("id", "")).startswith(KEY_PREFIX_OBJ) and e["id"] not in new_ids]
        keep = [e for e in doc["files"] if not str(e.get("id", "")).startswith(KEY_PREFIX_OBJ)]
        allobj = sorted(old_obj + obj_entries, key=lambda e: e["key"])
        first = {}
        for e in allobj:
            e["dup_of"] = first.get(e["src_content_md5"])
            first.setdefault(e["src_content_md5"], e["key"])
        intro_part = [e for e in keep if str(e.get("id", "")).startswith(KEY_PREFIX_INTRO)]
        other = [e for e in keep if not str(e.get("id", "")).startswith(KEY_PREFIX_INTRO)]
        doc["files"] = other + allobj + intro_part
        with open(DEST, "w") as fh:
            json.dump(doc, fh, indent=2, ensure_ascii=False)
            fh.write("\n")
        print("manifest: %s (ONLY +%d objects, total objects %d)" % (DEST, len(obj_entries), len(allobj)))
        return
    intro = collect_intro()
    for fo, f in intro:
        k = ascii_slug(os.path.splitext(f)[0])
        INTRO_SLUG_COUNT[k] = INTRO_SLUG_COUNT.get(k, 0) + 1
    with ThreadPoolExecutor(2) as ex:
        intro_entries = list(ex.map(lambda t: process_intro(*t), intro))
    # ไฟล์ซ้ำ (content md5 เท่ากัน) -> บอกใน dup_of (ไม่ตัดทิ้ง: แต่ละ trail ถือไฟล์ของตัวเอง)
    first = {}
    for e in sorted(obj_entries, key=lambda e: e["key"]):
        k = e["src_content_md5"]
        e["dup_of"] = first.get(k)
        first.setdefault(k, e["key"])
    new = sorted(obj_entries, key=lambda e: e["key"]) + sorted(intro_entries, key=lambda e: e["key"])
    assert len({e["out"] or e["still"] for e in new}) == len(new), "out ชนกัน"
    keep = [e for e in doc["files"] if not str(e.get("id", "")).startswith((KEY_PREFIX_OBJ, KEY_PREFIX_INTRO))]
    doc["files"] = keep + new
    with open(DEST, "w") as fh:
        json.dump(doc, fh, indent=2, ensure_ascii=False)
        fh.write("\n")
    print("manifest: %s (+%d objects +%d intro, existing %d)" % (DEST, len(obj_entries), len(intro_entries), len(keep)))


if __name__ == "__main__":
    main()
