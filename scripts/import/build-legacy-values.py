#!/usr/bin/env python3
"""build-legacy-values.py — values an OLDER import wrote, for the 2026-11-11 refresh steps (refresh-2026-11.php).

A refresh step may replace a value on a live site only while it is still exactly what an older import wrote
(an admin's edit is never overwritten). This script lists those values from the git history of the seed, using the
same formulas as the importer (objects.php sh_import_objects() · entities.php sh_import_zones()):

  objects.<slug>.<meta>   post_title = title · sh_title_raw = title_raw when it differs from title, else ''
                          · text fields = seed value or ''        (in-scope objects = trail_order.elephant)
  zones.<n>.<key>         name = name · sh_card_title = name_raw (else name) · sh_cover_image = "asset:<image-map cover>"
  media.<import key>      sha256 of files an older import copied (not in git — see MEDIA_OLD)

Every commit up to <base> (default f818837 = main before the 2026-11-11 seed) that touched the file is read.
EXTRA = values written on production by hand by the team (orchestrator/LOG.md), which count as "import values".

usage: python3 scripts/import/build-legacy-values.py [base-ref]   → writes scripts/import/legacy-values.json
"""
import json
import os
import subprocess
import sys

HERE = os.path.dirname(os.path.abspath(__file__))
REPO = os.path.dirname(os.path.dirname(HERE))
BASE = sys.argv[1] if len(sys.argv) > 1 else "f818837"
OUT = os.path.join(HERE, "legacy-values.json")

OBJECT_TEXT = {
    "sh_collection": "collection",
    "sh_thematic_section": "thematic_section",
    "sh_period": "period",
    "sh_place_of_production": "place_of_production",
    "sh_material": "material",
    "sh_dimensions": "dimensions",
    "sh_local_name": "local_name",
    "sh_object_biography": "object_biography",
    "sh_caption": "caption",
}

# Production edits made by the team, not by an admin of the client (orchestrator/LOG.md).
EXTRA = {
    # 2026-10-28 10:55 owner's request: Pod Duang sh_title_raw line break → space (prod object 171).
    "objects": {
        "pod-duang-stamped-with-the-chakra-and-great-crown-marks-reign-of-king": {
            "sh_title_raw": ["Pod Duang (Bullet Money) stamped with the Chakra and Great Crown Marks, Reign of King Rama IV"],
        },
    },
}

# Files an older import copied into the media library (media/out is not in git).
MEDIA_OLD = {
    # walking.webm v1 (8.8 s · 24,338 B · manifest before wt/mediawalk c4cc155) = media/src/walking-v1-8.8s.webm.bak.
    "media:walking.webm": ["e4ab355ebb4b28df57514efd623cf6866a74f583f45de46c746b5777407de043"],
}


def git(*args):
    return subprocess.run(["git", "-C", REPO, *args], check=True, capture_output=True, text=True).stdout


def versions(path):
    for sha in git("rev-list", BASE, "--", path).split():
        yield sha, json.loads(git("show", f"{sha}:{path}"))


def add(store, key, sub, meta, value):
    lst = store.setdefault(key, {}).setdefault(sub, {}).setdefault(meta, [])
    if value not in lst:
        lst.append(value)


out = {}
commits = {}
for sha, data in versions("content/seed/objects.json"):
    commits.setdefault("objects.json", []).append(sha[:7])
    for o in data["objects"]:
        if not (o.get("trail_order") or {}).get("elephant"):
            continue
        slug = o["slug"]
        title = str(o.get("title") or "")
        raw = str(o.get("title_raw") or "")
        add(out, "objects", slug, "post_title", title)
        add(out, "objects", slug, "sh_title_raw", raw if raw != title else "")
        for meta, k in OBJECT_TEXT.items():
            v = o.get(k)
            add(out, "objects", slug, meta, v if isinstance(v, str) else "")

for sha, data in versions("content/seed/zones.json"):
    commits.setdefault("zones.json", []).append(sha[:7])
    for z in data["zones"]:
        n = str(int(z["number"]))
        add(out, "zones", n, "name", str(z["name"]))
        add(out, "zones", n, "sh_card_title", str(z.get("name_raw", z["name"])))

for sha, data in versions("scripts/import/image-map.json"):
    commits.setdefault("image-map.json", []).append(sha[:7])
    for n, z in (data.get("zones") or {}).items():
        if z.get("cover"):
            add(out, "zones", str(n), "sh_cover_image", "asset:" + z["cover"])

for group, items in EXTRA.items():
    for sub, metas in items.items():
        for meta, values in metas.items():
            for v in values:
                add(out, group, sub, meta, v)

out["media"] = MEDIA_OLD
out = {
    "_meta": {
        "by": "scripts/import/build-legacy-values.py " + BASE,
        "base": git("rev-parse", "--short", BASE).strip(),
        "commits": commits,
        "note": "values an older import wrote (+ team edits on production) — refresh-2026-11.php replaces a value only while it is one of these",
    },
    **out,
}
with open(OUT, "w", encoding="utf-8") as f:
    json.dump(out, f, ensure_ascii=False, indent=1, sort_keys=False)
    f.write("\n")
print(f"wrote {os.path.relpath(OUT, REPO)} · objects {len(out.get('objects', {}))} · zones {len(out.get('zones', {}))} · commits {commits}")
