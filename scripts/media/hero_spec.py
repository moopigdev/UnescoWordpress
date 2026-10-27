#!/usr/bin/env python3
"""สร้าง scripts/media/hero-frames.spec.json = รายการวัตถุ trail ช้าง (page Final) ที่ไม่มี WebM จากเจ้าของ
+ เฟรมที่ "แสดงจริง" ของ hero ต่อวัตถุ (ลำดับ + ครอป + ขนาด) อ่านจาก design/figma-file.json (ไฟล์ที่ดาวน์โหลดไว้แล้ว — ไม่เรียก Figma)
รันซ้ำได้ผลเดิม (ไม่มี timestamp) — spec ถูก commit ดังนั้น encode ไม่ต้องใช้ figma-file.json

usage: python3 -I scripts/media/hero_spec.py [figma-file.json] [content/seed/objects.json] > scripts/media/hero-frames.spec.json
กติกาเลือกเฟรม (ตัดสินจากข้อมูลจริง ไม่เดา):
  1. layer ที่ visible:false (ตัวเองหรือ ancestor ใน hero) = ไม่แสดง -> ตัดออก (บันทึกใน `excluded`)
  2. layer opacity 0 = ยังนับ (เฟรมแรกของทุก hero GIF ตั้ง opacity 0 ใน Figma · WebM ของเจ้าของ Elephant-Shaped Relic Containers
     มี 14 เฟรม = 14 layer รวมเฟรม 01 ตรงกับภาพ 01 ตรวจแล้ว)
  3. ลำดับเวลา = เลขนำหน้าชื่อ layer ("01 4" -> 1 · "Frame 08,09" -> 8) · layer เดียวที่มีหลาย IMAGE fill (Patola: "02,03")
     ลำดับตาม fills[] ล่าง->บน · imageTransform แบบ STRETCH = ครอป (Patola)
"""
import json, os, re, sys

here = os.path.dirname(os.path.abspath(__file__))
repo = os.path.abspath(os.path.join(here, "..", ".."))
unesco = os.environ.get("UNESCO_ROOT") or os.path.abspath(os.path.join(repo, "..", ".."))
if not os.path.isdir(os.path.join(unesco, "design")):
    unesco = os.path.abspath(os.path.join(repo, ".."))
figma_path = sys.argv[1] if len(sys.argv) > 1 else os.path.join(unesco, "design", "figma-file.json")
seed_path = sys.argv[2] if len(sys.argv) > 2 else os.path.join(repo, "content", "seed", "objects.json")
assets = os.environ.get("DESIGN_ASSETS") or os.path.join(unesco, "design", "assets")

# วัตถุที่เจ้าของส่ง WebM มา -> slug ใน seed (จับคู่ด้วยชื่อ · ตรวจว่ามีใน seed จริง)
OWNER = {
    "bas-relief-of-a-procession": {"object_slug": "bas-relief-of-a-royal-or-noble-procession-illustrating-the-life-of-the", "usage": "object-hero"},
    "elephant-and-mahout-kendi": {"object_slug": "elephant-and-mahout-kendi", "usage": "object-hero"},
    "elephant-molar-coconut-grater": {"object_slug": "elephant-molar-coconut-grater", "usage": "object-hero"},
    "elephant-shaped-containers": {"object_slug": "elephant-shaped-relic-containers", "usage": "object-hero"},
    "ganesha": {"object_slug": "ganesha", "usage": "object-hero"},
    "singhasari-ganesha": {"object_slug": "singhasari-ganesha", "usage": "object-hero"},
    "tin-ingot-in-the-shape-of-an-elephant": {"object_slug": "tin-ingot-in-the-shape-of-an-elephant", "usage": "object-hero"},
    "walking": {"object_slug": None, "usage": "home-trails"},  # D12: เล่นใน Home Trails ไม่ใช่วัตถุ
}
FRAME_S = 0.7  # D13

fig = json.load(open(figma_path))
seed = json.load(open(seed_path))["objects"]
idx = {}


def walk(n):
    if isinstance(n, dict):
        if "id" in n:
            idx[n["id"]] = n
        for c in n.get("children") or []:
            walk(c)


walk(fig["document"])
by_slug = {o["slug"]: o for o in seed}
for k, v in OWNER.items():
    if v["object_slug"] is not None:
        assert v["object_slug"] in by_slug, ("owner webm -> slug ไม่มีใน seed", k, v)


def first_int(name):
    m = re.search(r"\d+", name)
    return int(m.group()) if m else 10 ** 6


def find_asset(ref):
    for ext in ("png", "jpg", "jpeg", "webp"):
        p = os.path.join(assets, ref + "." + ext)
        if os.path.exists(p):
            return ref + "." + ext
    raise SystemExit("ไม่พบ asset: " + ref)


def image_fills(node):
    return [f for f in node.get("fills", []) if f.get("type") == "IMAGE" and f.get("visible", True)]


def collect(hero):
    """-> (entries, excluded) ; entry = {order_key, tree_index, name, node, fills[]}"""
    entries, excluded = [], []
    for ti, c in enumerate(hero.get("children") or []):
        if c.get("visible", True) is False:
            excluded.append({"node": c["id"], "name": c["name"], "reason": "figma visible=false",
                             "refs": [f["imageRef"] for f in image_fills(c)]})
            continue
        fills, nodes = [], []

        def leaf(n):
            if n.get("visible", True) is False:
                excluded.append({"node": n["id"], "name": n["name"], "reason": "figma visible=false (ancestor/ตัวเอง)",
                                 "refs": [f["imageRef"] for f in image_fills(n)]})
                return
            for f in image_fills(n):
                fills.append(f)
                nodes.append(n["id"])
            for g in n.get("children") or []:
                leaf(g)

        leaf(c)
        if fills:
            entries.append({"key": first_int(c["name"]), "ti": ti, "name": c["name"], "node": c["id"], "fills": fills, "nodes": nodes})
    return entries, excluded


items = []
final = []
for o in seed:
    for f in o["figma"]:
        if "Final" in f["page"]:
            final.append((o, f))
assert len(final) == 15, len(final)
mapped_slugs = {v["object_slug"] for v in OWNER.values() if v["object_slug"]}
assert mapped_slugs <= {o["slug"] for o, _ in final}, mapped_slugs - {o["slug"] for o, _ in final}

for o, f in final:
    if o["slug"] in mapped_slugs:
        continue
    hero_ref = f["style"]["hero"][0]
    hero = idx[hero_ref["node"]]
    bb = hero["absoluteBoundingBox"]
    W, H = int(bb["width"]), int(bb["height"])
    entries, excluded = collect(hero)
    ordered = sorted(entries, key=lambda e: (e["key"], e["ti"]))
    layers = []
    transform = None
    notes = []
    for e in ordered:
        for fl in e["fills"]:
            tr = None
            if fl.get("scaleMode") == "STRETCH" and fl.get("imageTransform"):
                m = fl["imageTransform"]
                if abs(m[0][0] - 1) > 1e-6 or abs(m[0][2]) > 1e-6 or abs(m[1][1] - 1) > 1e-6 or abs(m[1][2]) > 1e-6:
                    assert abs(m[0][1]) < 1e-9 and abs(m[1][0]) < 1e-9, "transform หมุน/เอียง ไม่รองรับ"
                    tr = {"x": m[0][2], "w": m[0][0], "y": m[1][2], "h": m[1][1]}
            if transform is None:
                transform = tr
            assert tr == transform, ("transform ต่างกันในรายการเดียว", o["slug"])
            layers.append({"order": len(layers) + 1, "node": e["nodes"][e["fills"].index(fl)], "name": e["name"],
                           "ref": fl["imageRef"], "file": find_asset(fl["imageRef"])})
    if [e["ti"] for e in ordered] != sorted(e["ti"] for e in ordered):
        notes.append("ลำดับตามเลขในชื่อ layer ไม่ตรงกับลำดับใน tree (tree เรียงกลับ): ใช้เลขในชื่อ")
    if any(len(e["fills"]) > 1 for e in entries):
        notes.append("layer เดียวมีหลาย IMAGE fill (ชื่อ \"NN,MM\") : ใช้ทุก fill ตามลำดับล่าง->บน เป็นเฟรมแยก (seed hero_images เก็บเฉพาะ fill บนสุดของแต่ละ layer)")
    # ขนาดครอป: x0=tx*iw, w=a*iw (imageTransform ของ Figma = ส่วนของภาพที่ถูกยืดให้เต็ม node)
    if transform:
        vf = "crop=w='floor(iw*%.10f)':h='floor(ih*%.10f)':x='round(iw*%.10f)':y='round(ih*%.10f)'," % (
            transform["w"], transform["h"], transform["x"], transform["y"])
    else:
        vf = ""
    vf += "scale=%d:%d:flags=lanczos,format=rgba" % (W, H)
    seed_refs = [h["ref"] for h in f["hero_images"]]
    used = {l["ref"] for l in layers}
    items.append({
        "slug": o["slug"],
        "figma_frame_id": f["frame_id"], "figma_frame_name": f["frame_name"],
        "hero_node": hero_ref["node"], "hero_name": hero["name"],
        "width": W, "height": H, "vf": vf,
        "mode": "video" if len(layers) > 1 else "still",
        "layers": layers,
        "excluded": excluded,
        "seed_hero_image_count": len(seed_refs),
        "seed_refs_not_used": [r for r in seed_refs if r not in used],
        "seed_refs_missing_from_seed": [l["ref"] for l in layers if l["ref"] not in seed_refs],
        "notes": notes,
    })
items.sort(key=lambda i: i["slug"])
doc = {
    "frame_s": FRAME_S, "fps": 10,
    "figma_last_modified": fig.get("lastModified"),
    "rule": "ดูหัวไฟล์ scripts/media/hero_spec.py",
    "owner_webm": OWNER,
    "items": items,
}
json.dump(doc, sys.stdout, indent=2, ensure_ascii=False)
sys.stdout.write("\n")
