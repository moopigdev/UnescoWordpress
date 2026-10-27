#!/usr/bin/env python3
"""check-contract.py — docs/CONTENT-MODEL.md ↔ mu-plugin field definitions must match 1:1 (DOD A2).

Reads the definitions by running PHP on sharedheritage-core/inc/fields.php (no WordPress needed) and
compares, per group: the set of meta keys, the ACF type and show_in_rest, with the field tables of
CONTENT-MODEL.md. A table belongs to a group when the heading above it contains  `group: <id>`.
A row = a line starting with "| `sh_" ; cells: key | type ("<acf> → <meta>") | … | REST (✔ / —) (last cell).

usage: python3 scripts/import/check-contract.py      exit 0 = match · 1 = differences (printed)
"""
import json
import os
import re
import subprocess
import sys

REPO = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
FIELDS = os.path.join(REPO, "wp-content", "mu-plugins", "sharedheritage-core", "inc", "fields.php")
DOC = os.path.join(REPO, "docs", "CONTENT-MODEL.md")

php = (
    "define('ABSPATH', '/'); require %r; $o = array();"
    "foreach (sh_core_field_groups() as $g => $grp) { foreach (sh_core_group_fields($g) as $f) {"
    " $o[$g][$f['name']] = array($f['type'], isset($f['rest']) ? (bool) $f['rest'] : true); } }"
    "echo json_encode($o);"
) % FIELDS
code = json.loads(subprocess.run(["php", "-r", php], check=True, capture_output=True, text=True).stdout)

doc, group = {}, None
for line in open(DOC, encoding="utf-8"):
    if line.startswith("#"):
        m = re.search(r"group: ([a-z_]+)", line)
        group = m.group(1) if m else None
        continue
    if group and line.startswith("| `sh_"):
        cells = [c.strip() for c in line.strip().strip("|").split("|")]
        key = re.search(r"`(sh_[a-z0-9_]+)`", cells[0]).group(1)
        acf_type = cells[1].split("→")[0].strip().strip("`")
        rest = cells[-1].startswith("✔")
        doc.setdefault(group, {})[key] = [acf_type, rest]

errors = []
for g in sorted(set(code) | set(doc)):
    c, d = code.get(g, {}), doc.get(g, {})
    for k in sorted(set(c) - set(d)):
        errors.append(f"{g}: {k} registered but not in CONTENT-MODEL")
    for k in sorted(set(d) - set(c)):
        errors.append(f"{g}: {k} in CONTENT-MODEL but not registered")
    for k in sorted(set(c) & set(d)):
        if c[k][0] != d[k][0]:
            errors.append(f"{g}: {k} type code={c[k][0]} doc={d[k][0]}")
        if c[k][1] != d[k][1]:
            errors.append(f"{g}: {k} REST code={c[k][1]} doc={d[k][1]}")

total = sum(len(v) for v in code.values())
if errors:
    print("\n".join(errors))
    print(f"FAIL ({len(errors)} differences)")
    sys.exit(1)
print(f"OK — {len(code)} groups · {total} fields · CONTENT-MODEL == fields.php")
