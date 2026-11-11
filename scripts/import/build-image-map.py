#!/usr/bin/env python3
"""build-image-map.py — resolve Figma node ids in content/seed to image files (imageRef) for the importer.

The seed (design-dev) stores node ids; the importer needs the image file of each field.
This script walks design/figma-file.json (read only) and writes scripts/import/image-map.json
(committed, so the importer and the server never need the 18 MB Figma dump).

Rules (mechanical, no guessing):
  * image of a node = the top-most *visible* IMAGE fill of the node, else of its first visible descendant
  * component set default/hover = its two variants ordered as in Figma (default first)
  * only nodes listed in DESIGN-SPEC §5 / seed are resolved; every lookup is recorded in "nodes"
  * refs without a file in design/assets are listed in "missing" (the importer leaves that field empty)

usage: python3 scripts/import/build-image-map.py [--figma PATH] [--assets DIR] [--out PATH]
defaults: Unesco/design/figma-file.json · Unesco/design/assets · scripts/import/image-map.json
"""
import argparse
import json
import os
import sys

HERE = os.path.dirname(os.path.abspath(__file__))
REPO = os.path.dirname(os.path.dirname(HERE))


def unesco_root():
    # main repo: <root>/UnescoWordpress · worktree: <root>/.worktrees/UnescoWordpress--x
    for cand in (os.path.dirname(REPO), os.path.dirname(os.path.dirname(REPO))):
        if os.path.isdir(os.path.join(cand, "design")):
            return cand
    return os.path.dirname(REPO)


ap = argparse.ArgumentParser()
ap.add_argument("--figma", default=os.path.join(unesco_root(), "design", "figma-file.json"))
ap.add_argument("--assets", default=os.path.join(unesco_root(), "design", "assets"))
ap.add_argument("--out", default=os.path.join(HERE, "image-map.json"))
# Figma 2026-11-11: only the detail frames (objects) were re-specced against the new file (DESIGN-SPEC §4.8.3 · P-245);
# every other section keeps the older dump first (§1.1b: Home/Intro/Zone not diffed · some nodes, e.g. the timeline
# bar 797:2110, are gone). Nodes missing from one dump are looked up in the other.
ap.add_argument("--figma-fallback", default=os.path.join(unesco_root(), "design", "figma-file.2026-10-27.json"))
args = ap.parse_args()

fig = json.load(open(args.figma, encoding="utf-8"))
old_fig = json.load(open(args.figma_fallback, encoding="utf-8")) if args.figma_fallback and os.path.isfile(args.figma_fallback) else None
seed_dir = os.path.join(REPO, "content", "seed")
seed = {n: json.load(open(os.path.join(seed_dir, n + ".json"), encoding="utf-8"))
        for n in ("objects", "trails", "zones", "timeline-elephant")}
assets = {}
for fn in os.listdir(args.assets):
    base, ext = os.path.splitext(fn)
    if ext.lower() in (".png", ".jpg", ".jpeg", ".webp", ".gif"):
        assets[base] = fn

idx = {}


def index(n, parent=None, old=False, into=None):
    into = idx if into is None else into
    into[n["id"]] = dict(n, _parent=parent, _old=old)
    for c in n.get("children", []):
        index(c, n["id"], old, into)


idx_current, idx_older = {}, {}
if old_fig is not None:
    index(old_fig["document"], old=True, into=idx_current)  # current file below replaces nodes with the same id
index(fig["document"], into=idx_current)
index(fig["document"], into=idx_older)
if old_fig is not None:
    index(old_fig["document"], old=True, into=idx_older)  # older dump wins (sections not re-specced)
idx = idx_current  # objects section; switched to idx_older after it
nodes_log = {}
missing = set()


def own_ref(n):
    for fl in reversed(n.get("fills", [])):
        if fl.get("type") == "IMAGE" and fl.get("visible", True) and fl.get("imageRef"):
            return fl["imageRef"]
    return None


def deep_ref(n):
    if n.get("visible", True) is False:
        return None
    r = own_ref(n)
    if r:
        return r
    for c in n.get("children", []):
        r = deep_ref(c)
        if r:
            return r
    return None


def ref_of(node_id, why):
    n = idx.get(node_id)
    if n is None:
        nodes_log[node_id] = {"for": why, "error": "node not found"}
        return None
    r = deep_ref(n)
    nodes_log[node_id] = {"for": why, "ref": r, **({"older_dump": True} if n.get("_old") else {})}
    if r and r not in assets:
        missing.add(r)
    return r


def variants(set_id, why):
    s = idx.get(set_id)
    if not s or s.get("type") != "COMPONENT_SET":
        nodes_log[set_id] = {"for": why, "error": "not a component set"}
        return None, None
    kids = [c for c in s.get("children", []) if c.get("type") == "COMPONENT"]
    d = ref_of(kids[0]["id"], why + " (default)") if kids else None
    h = ref_of(kids[1]["id"], why + " (hover)") if len(kids) > 1 else None
    return d, h


def set_of_instance(inst):
    c = idx.get(inst.get("componentId", ""))
    if not c:
        return None
    p = idx.get(c["_parent"])
    return p if p and p.get("type") == "COMPONENT_SET" else None


def grid_instances(frame_id, size=184):
    out = []

    def rec(n):
        if n.get("visible", True) is False:
            return
        bb = n.get("absoluteBoundingBox") or {}
        if n.get("type") == "INSTANCE" and round(bb.get("width", 0)) == size:
            out.append(n)
            return
        for c in n.get("children", []):
            rec(c)

    rec(idx[frame_id])
    out.sort(key=lambda n: (round(n["absoluteBoundingBox"]["y"]), round(n["absoluteBoundingBox"]["x"])))
    return out


def visible_image_nodes(root_id):
    out = []

    def rec(n):
        if n.get("visible", True) is False:
            return
        if own_ref(n):
            out.append(n)
        for c in n.get("children", []):
            rec(c)

    rec(idx[root_id])
    return out


result = {"objects": {}, "timeline_items": {}, "trails": {}, "zones": {}, "pages": {}, "site": {}}

# ---- objects (D7: trail elephant, 15 Final frames) -------------------------------------------
final_objects = sorted(
    [o for o in seed["objects"]["objects"] if (o.get("trail_order") or {}).get("elephant")],
    key=lambda o: o["trail_order"]["elephant"],
)
# trail page Elephant 1501 (796:1795): tiles row-major = trail_order 1..15 (DESIGN-SPEC §4.8 last paragraph)
tiles = grid_instances("796:1795")
if len(tiles) != len(final_objects):
    sys.exit(f"elephant grid has {len(tiles)} tiles, seed has {len(final_objects)} objects")

for pos, (o, inst) in enumerate(zip(final_objects, tiles), start=1):
    fin = next(g for g in o["figma"] if g["page"].startswith("Page 2  Version") or g["page"].startswith("Page 2 Version"))
    st = fin["style"]
    entry = {"final_frame": fin["frame_id"]}
    cs = set_of_instance(inst)
    entry["tile"], entry["tile_hover"] = variants(cs["id"], f"object {o['slug']} tile ({cs['name']})") if cs else (None, None)
    entry["tile_set"] = cs["id"] if cs else None
    # hero still = first GIF frame with opacity > 0 that has a file (the opacity-0 frame is the GIF's blank
    # first frame; most GIF frames were not downloaded — P-209). None downloaded → first visible ref, listed missing.
    shown = [h for h in fin["hero_images"] if h.get("opacity", 1) and h.get("ref")]
    pick = next((h for h in shown if h["ref"] in assets), shown[0] if shown else None)
    hero = pick["ref"] if pick else None
    if pick:
        nodes_log[pick["node"]] = {"for": f"object {o['slug']} hero still", "ref": hero}
    if hero and hero not in assets:
        missing.add(hero)
    entry["hero_image"] = hero
    # extra images = visible image nodes inside the content panel (DEFERRED: seed extra_images include hidden nodes)
    extra = []
    for n in visible_image_nodes(st["panel_node"]):
        r = own_ref(n)
        nodes_log[n["id"]] = {"for": f"object {o['slug']} extra image", "ref": r}
        if r not in assets:
            missing.add(r)
        extra.append(r)
    entry["extra_images"] = extra
    result["objects"][o["slug"]] = entry

# ---- everything else: the dump the seed was specced against (older first) --------------------------
idx = idx_older

# ---- timeline items (card thumbnails) --------------------------------------------------------
for it in seed["timeline-elephant"]["items"]:
    thumbs = [ref_of(nid, f"timeline card {it['card_order']} thumbnail") for nid in it.get("thumbnail_nodes", [])]
    result["timeline_items"][str(it["card_order"])] = {"thumbnail": thumbs[0] if thumbs else None, "all": thumbs}

# ---- trails: home icons, zone grid tiles, zone-grid order, cover ----------------------------
zone_grid = grid_instances("670:1241")  # Zone 1 (1472); Zone 2/3 grids are identical (DESIGN-SPEC §4.3)
zone_grid_by_set = {}
for pos, inst in enumerate(zone_grid, start=1):
    cs = set_of_instance(inst)
    if cs:
        zone_grid_by_set[cs["name"]] = (pos, cs["id"])
for t in seed["trails"]["trails"]:
    if t.get("not_a_trail"):
        continue
    fg = t.get("figma") or {}
    e = {}
    e["icon_home"], e["icon_home_hover"] = variants(fg["icon_tile_component_set"], f"trail {t['slug']} home icon")
    zg = zone_grid_by_set.get(fg.get("zone_grid_tile_component_set") or "")
    if zg:
        e["zone_grid_order"] = zg[0]
        e["tile_zone_grid"], e["tile_zone_grid_hover"] = variants(zg[1], f"trail {t['slug']} zone grid tile")
    else:
        e["zone_grid_order"], e["tile_zone_grid"], e["tile_zone_grid_hover"] = None, None, None
    e["cover"] = ref_of(t["trail_cover_node"], f"trail {t['slug']} cover") if t.get("trail_cover_node") else None
    e["timeline_nav_order"] = None
    result["trails"][t["slug"]] = e

# timeline nav (Frame trails 797:2110, 17 icons left→right): each icon reuses a home-icon image → order per trail
# Two icons (Trails-27 / Trails-31) are not reused from the home set; identified by the trail name printed
# inside the image itself ("Fish & Aquatic Mammals" / "Seashell & Marine Invertebrates").
NAV_BY_IMAGE_TEXT = {
    "a3ff80091ac25a1b0731ab4e5cdc2543d16583d9": "fish",
    "10b2ff544aab69be192001c5ba9cdb02e61e61a9": "seashell",
}
nav = [c for c in idx["797:2110"].get("children", []) if c.get("visible", True) is not False]
for pos, g in enumerate(nav, start=1):
    r = deep_ref(g)
    nodes_log[g["id"]] = {"for": f"timeline nav icon {pos}", "ref": r}
    if r and r not in assets:
        missing.add(r)
    for slug, e in result["trails"].items():
        if r and (r in (e["icon_home"], e["icon_home_hover"]) or NAV_BY_IMAGE_TEXT.get(r) == slug):
            e["timeline_nav_order"] = pos
            e["icon_timeline"] = r

# ---- zones -----------------------------------------------------------------------------------
zone_sets = {1: "670:9423", 2: "670:9425", 3: "670:9432", 4: "670:9461", 5: "670:9517"}  # comp-zone1..5 (Home cards)
zone_cover = {1: "1013:1289", 2: "1179:5759", 3: "1179:5762"}    # [160,150 1120x410] in 1472/1473/1474
zone_banner = {1: "1013:1292", 2: "1013:1343", 3: "1013:1369"}   # [160,579 1120x166]
for z in seed["zones"]["zones"]:
    n = z["number"]
    e = {}
    e["card"], e["card_hover"] = variants(zone_sets[n], f"zone {n} card")
    e["cover"] = ref_of(zone_cover[n], f"zone {n} cover") if n in zone_cover else None
    e["banner"] = ref_of(zone_banner[n], f"zone {n} banner") if n in zone_banner else None
    result["zones"][str(n)] = e

# ---- pages / site ------------------------------------------------------------------------------
result["pages"]["home"] = {
    "hero": ref_of("670:9367", "home hero"),
    "intro_image": ref_of("670:9370", "home introduction image"),
    "zone_bar": ref_of("885:1201", "home zone bar"),
}
result["pages"]["intro"] = {
    "cover": ref_of("670:9367", "intro cover (same image as home hero, DESIGN-SPEC §4.2)"),
    "intro_image": ref_of("670:1221", "intro: Introduction image"),
    "knowing_image": ref_of("670:1236", "intro: Knowing Southeast Asia image"),
    # the map shown in frame 1471 itself (Frame 12767 > Frame 12655 fill 39907d2c) — not Section 11 670:1596 (P-150 · D14)
    "map_image": ref_of("I670:1239;670:1462", "intro: map in frame 1471 (Frame 12655, DESIGN-SPEC §4.7)"),
}
result["pages"]["timeline"] = {"illustration": ref_of("1232:1711", "timeline illustration 11000x791")}
result["site"] = {"header_image": ref_of("670:9386", "Header #1 background (logos in image)")}

# ---- video: not in this map — the importer reads content/media-manifest.json directly
# (object hero = entry with object_slug == seed slug · Home trails = usage "home-trails")

result = {
    "_meta": {
        "generated_by": "scripts/import/build-image-map.py",
        "figma_file": os.path.basename(args.figma),
        "figma_lastModified": fig.get("lastModified"),
        "figma_fallback": os.path.basename(args.figma_fallback) if old_fig is not None else None,
        "nodes_from_older_dump": sorted(k for k, v in nodes_log.items() if v.get("older_dump")),
        "note": "ref = Figma imageRef = file name (without extension) in Unesco/design/assets · null = no image in Figma · see 'missing' for refs without a file",
    },
    **result,
    "files": {},
    "missing": sorted(missing),
    "nodes": nodes_log,
}


def collect(v, acc):
    if isinstance(v, str) and len(v) == 40:
        acc.add(v)
    elif isinstance(v, dict):
        for x in v.values():
            collect(x, acc)
    elif isinstance(v, list):
        for x in v:
            collect(x, acc)


used = set()
for k in ("objects", "timeline_items", "trails", "zones", "pages", "site"):
    collect(result[k], used)
result["files"] = {r: assets[r] for r in sorted(used) if r in assets}

with open(args.out, "w", encoding="utf-8") as fh:
    json.dump(result, fh, ensure_ascii=False, indent=1, sort_keys=False)
    fh.write("\n")
print(f"wrote {args.out}: {len(result['files'])} files · {len(result['missing'])} missing refs")
