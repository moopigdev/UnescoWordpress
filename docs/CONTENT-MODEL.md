# CONTENT-MODEL — สัญญาข้อมูล (CPT · taxonomy · meta)

**เจ้าของไฟล์:** wp-dev (งาน `core-contract`) · หน้าทุกหน้าอ่านจากที่นี่ ⛔ ห้ามหน้าใดตั้งฟิลด์เอง · ต้องการฟิลด์ใหม่ → `docs/PENDING-DECISIONS.md`
**ที่มา:** DESIGN-SPEC §5 (ฟิลด์ที่หน้าใช้) + `content/seed/*.json` + `content/media-manifest.json` · ขอบเขต import = D7 (trail ช้าง)
**โค้ด:** mu-plugin `wp-content/mu-plugins/sharedheritage-core/` — ตารางในไฟล์นี้ = `inc/fields.php` 1:1 ตรวจด้วย `python3 scripts/import/check-contract.py` (เทียบ key · ชนิด · REST ต่อ group)

## 0. หลักการ / วิธี register (D1 · O-7)
- ข้อมูลอยู่ใน **mu-plugin** ไม่ใช่ theme · meta key ขึ้นต้น `sh_` · ทุกฟิลด์แก้ได้จากหน้า edit มาตรฐาน (post/term/page edit · Settings › Shared Heritage)
- นิยามฟิลด์ที่เดียว `inc/fields.php` → (1) `register_post_meta` / `register_term_meta` / `register_setting` (ชนิด · sanitize · auth = `edit_post`/`edit_term`/`manage_options` · REST) (2) ACF ฟรี `acf_add_local_field_group` (UI · version อยู่ใน git ไม่อยู่ใน DB)
- **ACF ไม่ active → ไม่ fatal:** admin notice + ข้อมูลยังอ่านได้ด้วย `get_post_meta`/API §11 + REST (meta ลงทะเบียนกับ WP เอง) · ACF ฟรีไม่มี repeater/gallery/options page → รายการหลายค่าใช้ฟิลด์เลขลำดับ (`sh_extra_image_1..6`) · options ใช้ Settings API
- **ชนิด → เก็บเป็น:** text/textarea/color_picker → string · number/image/file/post_object/taxonomy → integer (id) · true_false → boolean · relationship → array&lt;int&gt; · textarea ไม่แปลงบรรทัด (`new_lines` ว่าง) — หน้าแยกย่อหน้าด้วยบรรทัดว่าง (`sh_paragraphs()`)
- **"ต้องมีค่า"** = เนื้อหาต้องมีเพื่อให้หน้าตรงดีไซน์ · ACF **ไม่บังคับ** ฟิลด์ใด (`required=0` ให้บันทึกร่างได้) · หน้าต้องรับมือค่าว่าง (DOD B6)
- **REST** ✔ = `show_in_rest` → ค่าอยู่ใน `meta.sh_*` ของ `/wp/v2/object` · `/wp/v2/timeline_item` · `/wp/v2/trail` · `/wp/v2/zone` · `/wp/v2/pages` · `/wp/v2/settings` (ต้อง login) · ACF ไม่เปิด REST ของตัวเอง (`show_in_rest=0`)
- ชื่อ term เก็บแบบ WP (`&` → `&amp;` ใน DB) · แสดงด้วย `esc_html()` (ไม่ escape ซ้ำ)

## 1. Route (ตาราง PLAN · ตายตัว) → ข้อมูลที่รองรับ
| หน้า (frame Final) | template | asset slug (`sh_template_slug_map()`) | ข้อมูล | URL ตัวอย่าง (import แล้ว) |
|---|---|---|---|---|
| Home 1470 | `front-page.php` | home | Page "Home" (slug `home`) ตั้งเป็นหน้าแรก (`show_on_front=page`) · group `home` §6 · zone/trail terms | `/` |
| Intro 1471 (+ map slider) | `page-intro.php` | intro | Page slug **`intro`** · group `intro` §7 | `/intro/` |
| Zone 1–3 (1472–1474 · ใหม่ `3117:3684/3713/3742` §4.3b) | `taxonomy-zone.php` | zone | taxonomy `zone` term `zone-<n>` §5 · กริด = `sh_trails('zone_grid')` · ข้อความ `sh_intro` → Read more `sh_intro_full` | `/zone/zone-1/` `/zone/zone-2/` `/zone/zone-3/` |
| **Zone 4 หน้าคั่นเลือกสัตว์** (1513 `3117:3946` · D37 · §4.10) | `taxonomy-zone.php` (WP เลือก `taxonomy-zone-zone-4.php` ก่อนถ้า theme มี → asset slug = ชื่อไฟล์) | zone (หรือชื่อไฟล์) | term `zone-4` §5 (`sh_cover_image` = พื้นเส้นประ · `sh_banner_image` = แถบ ZONE (Zone 4 active)) · สัตว์ = `sh_trails('home')` ที่มี `sh_chooser_image` §4 · ลิงก์ = `sh_timeline_page` ของ trail (มี = ลิงก์ · ไม่มี = ไม่ลิงก์ D37/D40) · ตำแหน่ง/ขนาดต่อตัว = ค่าคงที่ใน theme (§4.10 · seed `zone4-chooser.json`) | `/zone/zone-4/` |
| **Zone 5 หน้าหลัก** (1514 `3117:3955` · D40 · §4.11) | `taxonomy-zone.php` (หรือ `taxonomy-zone-zone-5.php`) | zone (หรือชื่อไฟล์) | term `zone-5` §5 (`sh_has_page` · `sh_cover_image` · `sh_intro` · `sh_banner_image` · tab "Zone page cards") | `/zone/zone-5/` |
| Trail ช้าง 1501 | `taxonomy-trail.php` | trail | taxonomy `trail` term `elephant` §4 · วัตถุ = `sh_trail_objects()` | `/trail/elephant/` |
| Timeline (Zone 4) | `page-timeline.php` | timeline | Page slug **`timeline`** · group `timeline` §8 · การ์ด = CPT `timeline_item` §3 | `/timeline/` |
| Detail วัตถุ 15 ชิ้น | `single-object.php` | detail | CPT `object` §2 | `/object/ganesha/` (ครบ 15 ใน `docs/smoke-urls`) |
Zone 4 (D37 · 2026-11-11): **หน้าคั่น = หน้า zone ของตัวเอง `/zone/zone-4/`** — `sh_link_page` ของ `zone-4` = ว่าง (การ์ดหน้าแรกจึงลิงก์ term link · `/timeline/` ไม่เปลี่ยน · ช้างในหน้าคั่นลิงก์ไป `/timeline/` ผ่าน `sh_timeline_page` ของ trail elephant) · เดิม (ก่อน D37) `sh_link_page` = หน้า Timeline → refresh `2026-11-11-chooser` ล้างค่านี้บน site เดิม · Zone 5 `sh_has_page` = ตาม seed (D40: true เมื่อ seed มีหน้า — P-172) · แยก layout ต่อ zone = template hierarchy `taxonomy-zone-zone-<n>.php` (ไม่มีฟิลด์ "ชนิดหน้า")

## Entity (ภาพรวม)
| entity | ชนิด WP | REST base | public URL | หมายเหตุ |
|---|---|---|---|---|
| `object` | CPT (`title` · `custom-fields` · `revisions` · ไม่มี editor) | `object` | `/object/<slug>/` | title = ชื่อวัตถุ (seed `figma_2026_11.title` → `title` · D40) · slug = seed `slug` (**ไม่เปลี่ยนตามชื่อ** — ลิงก์เดิมใช้ได้) · บล็อกใต้หัวข้อ (§4.8.2): `sh_period` → `sh_place_of_production` → `sh_material` → `sh_dimensions` → `sh_collection` → `sh_object_biography` (ป้าย "Other information / Object biography:") ข้ามค่าว่าง · trails = taxonomy `trail` (หลายค่า, checkbox) |
| `timeline_item` | CPT ไม่ public (admin + REST เท่านั้น) | `timeline_item` | — | title = หัวการ์ด (seed `items[].title`) · `menu_order` = card_order |
| `trail` | taxonomy (บน `object` · hierarchical เพื่อ checkbox · ไม่ใช้ parent) | `trail` | `/trail/<slug>/` | 17 term (seed `etc` ไม่ใช่ trail → ไม่ import) · name = seed `name` ตามตัว (เช่น "Elephan" "Phonix" — ชื่อแสดงผลยังไม่ออกแบบ P-2xx) · description = คำอธิบายหน้า trail (seed `description`, มีเฉพาะ elephant) |
| `zone` | taxonomy (บน `object` · ยังไม่ผูกวัตถุ/trail — P-212) | `zone` | `/zone/zone-<n>/` | 5 term · name = seed `name` · term ว่างก็ได้ 200 |
| page Home / Intro / Timeline | Page มาตรฐาน | `pages` | `/` `/intro/` `/timeline/` | ฟิลด์ผูกกับหน้า: Home = front page · Intro/Timeline = หน้า slug `intro`/`timeline` |
| site | options (Settings › Shared Heritage) + เมนู WP | `settings` | — | §9 |

## 2. `object` — group: object
| ฟิลด์ (meta key) | ชนิด (ACF → meta) | ต้องมีค่า | ที่มาใน Figma (node) | ตัวอย่าง (seed) | ใช้ในหน้า | REST |
|---|---|---|---|---|---|---|
| (post_title) | title | ✔ | title 36/800 `670:2797` → **2026-11-11 = `3083:813`… (§4.8.2 · D40)** | "Ganesha" · "Hand-carved opium pipe" (seed `figma_2026_11.title` ถ้ามี · ไม่มี = `title`) | detail · trail tile alt · timeline link | ✔ (`title`) |
| (taxonomy `trail`) | term[] | ✔ | section Page 2.1 / กริดช้าง | Ganesha `[elephant]` · Embossed Brass Basin `[elephant, garuda, lion, naga]` | trail · detail | ✔ (`trail`) |
| `sh_title_raw` | `textarea` → string | — (3/15) | title node (ตัวแบ่งบรรทัดดิบ) | Leg Bone: "Elephant Leg Bone\n(Elephas maximus indicus)" · ว่าง = ใช้ post_title | detail | ✔ |
| `sh_collection` | `text` → string | ✔ (14/15) | `Collection:` ใน metadata `670:2798` | "National Museum Bangkok" | detail | ✔ |
| `sh_thematic_section` | `text` → string | ✔ | `Section : …` `670:2798` (หายจาก Figma 2026-11-11 §4.8.2) | "Cosmology Religion & Spirituality" | — (เก็บไว้ · หน้า detail ไม่แสดงตั้งแต่ §4.8.2) | ✔ |
| `sh_period` | `text` → string | — (10/15) | `Period:` | "10th–11th century" | detail | ✔ |
| `sh_place_of_production` | `text` → string | — (11/15) | `Place of Production / Findspot:` | "Candi Singhasari, East Java, Indonesia" | detail | ✔ |
| `sh_material` | `text` → string | — (13/15) | `Material:` | "Andesite, ****Volcanic rock" (คัดลอกตรง) | detail | ✔ |
| `sh_dimensions` | `text` → string | — (13/15) | `Dimensions:` | "Height: 172 cm" | detail | ✔ |
| `sh_local_name` | `text` → string | — (0/15 ช้าง) | บรรทัดใน metadata (Page 2.1) | Liturgical Pulpit Covers: "Pha Hum Thammasan / …" | detail | ✔ |
| `sh_object_biography` | `textarea` → string | — (10/15) | `Specimen/Object biography:` | "The Dutch government in Java presented this…" | detail | ✔ |
| `sh_caption` | `textarea` → string | ✔ (15/15 ตั้งแต่ Figma 2026-11-11) | หลัง "Caption:" `670:2799` → 2026-11-11 §4.8.2 | "Carved from volcanic stone, this monumental statue…" (ย่อหน้าคั่นบรรทัดว่าง · seed `figma_2026_11.caption` ถ้ามี · ไม่มี = `caption`) | detail | ✔ |
| `sh_hero_video_webm` | `file` → integer | — (14/15 มีวิดีโอ) | Frame GIF `1051:8181` (ไฟล์จริง = media manifest `out` ของรายการที่ `object_slug` = slug วัตถุ · `kind: video`) | Ganesha → `ganesha.webm` 1440×810 · Leg Bone ว่าง (`kind: still`) | detail hero | ✔ |
| `sh_hero_video_hevc` | `file` → integer | — (เฉพาะ alpha 6 ไฟล์) | manifest `fallback.file` เฉพาะ `fallback.type = hevc-alpha-mov` (`poster-only` = ไม่มี) | Kendi → `elephant-and-mahout-kendi.hevc.mov` | detail hero (`<source>` แรกสำหรับ Safari) | ✔ |
| `sh_hero_video_has_alpha` | `true_false` → boolean | — | manifest `has_alpha` | Kendi true · Ganesha false (พื้นขาวทึบ) | detail hero | ✔ |
| `sh_hero_poster` | `image` → integer | — (คู่กับวิดีโอ) | manifest `poster` (WebP ≤640 กว้าง) | `ganesha.poster.webp` | detail hero `poster=` | ✔ |
| `sh_hero_image` | `image` → integer | — | manifest `still` ของรายการ `kind: still` (D13 เฟรมเดียว) · ไม่มี → เฟรม GIF แรกที่ opacity>0 ใต้ hero (`figma[].hero_images`) | Leg Bone `elephant-leg-bone.still.webp` 1320×742 · อื่น ๆ เฟรมแรก PNG (ครบ 15/15) | detail hero เมื่อไม่มีวิดีโอ | ✔ |
| `sh_tile_image` | `image` → integer | ✔ | tile `Ele NN` default `939:775` (หน้า trail 1501 `796:1795`) | Leg Bone `70cf8ed7…` 184² | trail grid | ✔ |
| `sh_tile_image_hover` | `image` → integer | ✔ | tile hover `939:776` | Leg Bone `e978e4de…` 210² | trail grid hover | ✔ |
| `sh_extra_image_1` | `image` → integer | — | ภาพในแผงเนื้อหาที่แสดงจริง (ลำดับตาม Figma) | Leg Bone `1041:1452` `4953122e…` 360×589 | detail panel | ✔ |
| `sh_extra_image_2` | `image` → integer | — | 〃 | Brass Basin `964:1808` | detail panel | ✔ |
| `sh_extra_image_3` | `image` → integer | — | 〃 (มีเฉพาะ Bas-relief แกลเลอรี 6) | Bas-relief `670:2857` 359×112 | detail panel | ✔ |
| `sh_extra_image_4` | `image` → integer | — | 〃 | Bas-relief `670:2858` | detail panel | ✔ |
| `sh_extra_image_5` | `image` → integer | — | 〃 | Bas-relief `670:2859` | detail panel | ✔ |
| `sh_extra_image_6` | `image` → integer | — | 〃 | Bas-relief `670:2860` | detail panel | ✔ |
| `sh_panel_bg` | `color_picker` → string | — (ว่าง = ไม่มี fill) | fill แผง `670:2796` | `#B2BBAA` · Patola = ว่าง | detail | ✔ |
| `sh_title_color` | `color_picker` → string | ✔ | fill title | `#D75F47` | detail | ✔ |
| `sh_text_color` | `color_picker` → string | ✔ (14/15) | fill caption | `#212529` | detail | ✔ |
| `sh_meta_color` | `color_picker` → string | — (4/15 · ว่าง = text) | fill metadata block (`figma[].style.meta_color`) | Ganesha `#F8F9FA` | detail | ✔ |
| `sh_collection_color` | `color_picker` → string | — (ว่าง = text) | fill `Collection:` | Leg Bone `#F8F9FA` | detail | ✔ |
| `sh_layout` | `textarea` → string (JSON object · ตรวจตอนบันทึก) | ✔ (ตรง 100%) | ตาราง DESIGN-SPEC §4.8 (`figma[Final].style`) | Ganesha `{"frame_id":"670:2786","panel_y":1000,"panel_h":1005,"panel_padding_top_bottom":[60,80],"hero":[{"bbox":[0,150,1440,810]…}],"back_button":[160,1887,115,50],…}` | detail (geometry) | ✔ |
`sh_layout` keys: `frame_id frame_name frame_size hero[] back_button panel_padding_top_bottom panel_children[] panel_gap panel_node panel_bg panel_has_fill panel_y panel_h title_color collection_color caption_color meta_color` (bbox = `[x,y,w,h]` เทียบ frame 1440) · อ่านด้วย `sh_object_layout()`
- import ส่ง seed `figma[Final].style` **ทั้งก้อน** (คีย์ที่ไม่รู้จักผ่านไปด้วย — DESIGN-SPEC §4.8 เป็นที่นิยามคีย์) · **คีย์ใดก็ได้ (ทุกระดับ) ที่ลงท้าย `_ref`** และค่าเป็นชื่อไฟล์ใน `design/assets` (มี/ไม่มีนามสกุล เช่น ภาพครอปของ design-dev `<ref>.crop-<node>.png`) หรือ imageRef ใน `image-map.json` → ค่าใน `sh_layout` กลายเป็น **attachment id (integer)** · ไม่มีไฟล์ → คงข้อความเดิม + คำเตือนใน import report (ไม่ fatal) · หน้าอ่าน: `is_int( $v )` → `wp_get_attachment_image( $v, … )`

## 3. `timeline_item` — group: timeline_item
| ฟิลด์ (meta key) | ชนิด (ACF → meta) | ต้องมีค่า | ที่มาใน Figma (node) | ตัวอย่าง (seed `timeline-elephant.json`) | ใช้ในหน้า | REST |
|---|---|---|---|---|---|---|
| (post_title) | title | ✔ | `1232:1716` | "Elephant Leg Bone (Elephas maximus indicus)" | timeline card | ✔ |
| `sh_trail` | `taxonomy` → integer (term id, ไม่ผูก term จริง) | ✔ | — (ไฟล์ timeline ของ trail ช้าง) | elephant | timeline (เลือกการ์ด) | ✔ |
| `sh_object` | `post_object` → integer | ✔ | การ์ด ↔ วัตถุ (card_order = ลำดับกริดช้าง) | Leg Bone | timeline "Read more" → detail | ✔ |
| `sh_card_order` | `number` → integer | ✔ | x ของการ์ดใน `Frame 114464` | 1 (x=500) | timeline (ลำดับซ้าย→ขวา) | ✔ |
| `sh_illustration_order` | `number` → integer | — | ลำดับจุดบนภาพ `1232:1711` (P-208) | Leg Bone 14 | timeline (ไฮไลต์จุด) | ✔ |
| `sh_era_label` | `text` → string | — (~5/15 อ่านได้) | ป้ายยุคในภาพ raster | "20th–21st century" | timeline | ✔ |
| `sh_card_meta` | `textarea` → string | ✔ | `1232:1717` 14/24 (บรรทัดละรายการ) | "SECTION : Land\nPeriod: 20th-21st century\n…" | timeline card | ✔ |
| `sh_thumbnail` | `image` → integer | ✔ | ภาพการ์ด `1232:1714` | `4953122e…` 300×202 | timeline card | ✔ |
| `sh_thumbnail_2` | `image` → integer | — (การ์ด 6, 9) | ภาพที่ 2 ของการ์ด | Ivory Fire Piston `8dcebc79…` | timeline card | ✔ |
| `sh_below_image` | `image` → integer | — (D39/D41: ชิ้น 1·7·9 มี · ที่เหลือว่าง · admin เพิ่ม/เปลี่ยน/ลบได้) | Figma `3101:609` Section 51 ภาพ 500×281 บนสุดของการ์ด (`3103:1037` · `3103:1187` · `3103:1220`) · seed `items[].below_ref` (ชื่อไฟล์ที่ราก `design/assets/`) | `bbc1e1610db7….png` (ชิ้น 1) · ว่าง → ใช้ `sh_thumbnail` | timeline: ภาพใหญ่ใต้เส้นเท่านั้น (`sh_below_image ?: sh_thumbnail` · ภาพก่อน `sh_line_art` ซ้อนใต้ภาพนี้) · ⛔ แถวบน/mini-map/หน้า detail ไม่ใช้ · ขนาดแถวบนอิงภาพบนเส้น (DESIGN-SPEC §4.5c.3d) | ✔ |
| `sh_line_art` | `image` → integer | — (D35/D36: ชิ้น 2–5 มี · 1, 6–15 ว่าง · admin เพิ่ม/เปลี่ยน/ลบได้) | Figma `3101:609` Section 51 คู่ "ก่อน ▶ หลัง" (`3103:1040` · `3103:1056` · `3246:3914` · `3160:1510`) · seed `items[].line_art_ref` (ชื่อไฟล์ใน `design/assets/lineart/`) | `02-singhasari-ganesha.png` · ว่าง → ภาพใต้เส้นเป็นภาพสีอย่างเดียว | timeline: "ภาพก่อน" ของภาพใหญ่ใต้เส้น (ขาเข้า α 0.40p → settle cross-fade เป็น `sh_thumbnail` · DESIGN-SPEC §4.5c.3b/§6.4c) · ⛔ แถวบน/mini-map ไม่ใช้ (D35) | ✔ |

## 4. `trail` (term) — group: trail
| ฟิลด์ (meta key) | ชนิด (ACF → meta) | ต้องมีค่า | ที่มาใน Figma (node) | ตัวอย่าง (seed `trails.json`) | ใช้ในหน้า | REST |
|---|---|---|---|---|---|---|
| (name / slug / description) | term | ✔ / ✔ / — | component set `670:9643` · คำอธิบาย `797:1881` | "Elephan" / `elephant` / "Elephants have lived alongside…" | trail · alt | ✔ |
| `sh_title_on_trail_page` | `text` → string | — (elephant) | `797:1880` | "Elephant’s Trail" | trail | ✔ |
| `sh_order_home_icons` | `number` → integer | ✔ | ลำดับใน `Frame 12796` `670:9724` | rooster 1 … elephant 17 | home | ✔ |
| `sh_icon_home` | `image` → integer | ✔ | set `icon_tile_component_set` default (`670:9575`) | Rooster `5f216ed1…` 165² | home | ✔ |
| `sh_icon_home_hover` | `image` → integer | ✔ | 〃 hover (`670:9574`) | Rooster `17bbb16a…` 185² | home | ✔ |
| `sh_show_in_zone_grid` | `true_false` → boolean | ✔ | อยู่ในกริด 1472–1474 (P-212) | 15 true · elephant/water-bird false | zone | ✔ |
| `sh_zone_grid_order` | `number` → integer | — (15/17) | ตำแหน่ง tile ในกริด `670:1241` (แถวแล้วคอลัมน์) | bird 1 · buffalo 2 · rooster 3 … turtle 15 | zone | ✔ |
| `sh_tile_zone_grid` | `image` → integer | — (15/17) | set `Bird 1` `1018:1372` default | Bird `4c493fdc…` 184² | zone | ✔ |
| `sh_tile_zone_grid_hover` | `image` → integer | — (15/17) | 〃 hover `1018:1374` | Bird `f8cdca6e…` 210² | zone | ✔ |
| `sh_tile_bg` | `color_picker` → string | ✔ | pixel กริด (`tile_bg.hex`) | rooster `#7EB7A6` · elephant `#E0DEC9` | zone · trail | ✔ |
| `sh_timeline_nav_order` | `number` → integer | ✔ | ลำดับไอคอนใน `Frame trails` `797:2110` | bird 1 … elephant 9 … seashell 17 | timeline | ✔ |
| `sh_icon_timeline` | `image` → integer | ✔ | ไอคอน 35×35 ใน `797:2110` (15 ตัวใช้ภาพ hover ของ home · fish/seashell ภาพของตัวเอง) | elephant `c7504b4f…` | timeline | ✔ |
| `sh_icon_group_color` | `color_picker` → string | — | DESIGN-SPEC §2.1/§4.5 กลุ่ม green/red/mustard/purple | elephant `#C3BC85` | timeline | ✔ |
| `sh_chooser_image` | `image` → integer | — (17/17) | Section 54 `3140:1398` ไฟล์แยกต่อตัว (§4.10 · seed `zone4-chooser.json animals[].asset` ตาม `trail_slug`) | elephant `zone4-animals/elephant.png` 1504×1052 (PNG โปร่ง) | zone 4 หน้าคั่น (ว่าง = ไม่แสดงตัวนั้น) · คลิก → `sh_timeline_page` ของ trail ถ้ามี | ✔ |
| `sh_cover_image` | `image` → integer | — (elephant) | `796:1799` | `ed0e5ee9…` 1120×497 | trail | ✔ |
| `sh_timeline_page` | `post_object` → integer (page) | — (ว่าง = ไม่มี timeline) | frame `670:1551` | elephant → หน้า Timeline | trail · timeline | ✔ |
| `sh_object_order` | `relationship` → array&lt;int&gt; | ✔ | ลำดับ tile `Ele 2…` §4.8 (seed `trail_order`) | elephant: Leg Bone, Singhasari Ganesha, Ganesha, … Bas-relief (15) | trail (ลาก-วางเรียงใน admin) | ✔ |

## 5. `zone` (term) — group: zone
| ฟิลด์ (meta key) | ชนิด (ACF → meta) | ต้องมีค่า | ที่มาใน Figma (node) | ตัวอย่าง (seed `zones.json`) | ใช้ในหน้า | REST |
|---|---|---|---|---|---|---|
| (name / slug) | term | ✔ | `I670:9480;670:9416` · 2026-11-11 H1 `3117:5053/5071/5077` (D38: ชื่อ B ทุกที่ · §4.3b) | "Animals in Nature and Mythology" / `zone-1` | zone (H1) · home · แถบ zone | ✔ |
| `sh_number` | `number` → integer | ✔ | — | 1 | home · zone | ✔ |
| `sh_card_title` | `textarea` → string | ✔ | ข้อความการ์ด (มี `\n` · seed `name_raw`) | "Animals in Nature \nand Mythology" | home | ✔ |
| `sh_color` | `color_picker` → string | ✔ | pixel แถบ zone (§2.1) | `#14705E` | home · zone | ✔ |
| `sh_card_image` | `image` → integer | ✔ | set `Zone 1` `670:9423` default `670:9415` | `342629af…` 373×487 · zone 4/5 560×294 | home | ✔ |
| `sh_card_image_hover` | `image` → integer | ✔ | 〃 hover `670:9419` | `35c9b660…` | home | ✔ |
| `sh_cover_image` | `image` → integer | — (zone 1–5) | zone 1–3: banner ใหม่ `3117:3716/3686/3745` (ชื่อ zone ในภาพ D8 · ผูกตามชื่อในภาพ P-240 · seed `banner_image.file`; alt ของไฟล์ = `name_banner`) · เดิม `1013:1289`… · zone 4: พื้นเส้นประ `3117:3950` (seed chooser `cover_bg.file_cropped`) · zone 5: Cover `[160,170 551×384]` (seed `page.cover`) | zone 1 `zone-banners-2026-11/zone1-animals-in-nature-and-mythology.png` 4096×1503 (แสดง 1120×410) · zone 4 `zone4-animals/_chooser-bg-cover-1120x540.png` | zone | ✔ |
| `sh_banner_image` | `image` → integer | — (zone 1–5) | แถบ ZONE 1–5: zone 1–3 `1013:1292`… · zone 4 `3117:3951` (Zone 4 active · seed chooser `zone_bar.file_cropped`) · zone 5 (Zone 5 active · seed `page.zone_bar`) | zone 1 `413791d6…` 1120×166 · zone 4 `_zone-bar-zone4-active-1120x166.png` | zone | ✔ |
| `sh_has_page` | `true_false` → boolean | ✔ | — | zone 1–4 true · zone 5 = seed `has_page` (D40 → true เมื่อ seed มีหน้า P-172) | home (คลิกได้ไหม) | ✔ |
| `sh_link_page` | `post_object` → integer (page) | — | page_frame ของ zone = frame timeline (ยกเว้น zone 4 ตั้งแต่ D37) | ว่างทุก zone (zone 4 เดิม → หน้า Timeline) | home (ปลายทางการ์ด) · ว่าง = หน้า zone เอง | ✔ |
| `sh_intro` | `textarea` → string | — (zone 1–3 · 5) | zone 1–3 สถานะย่อ `3117:5054/5072/5078` (§4.3b) · zone 5 `3117:3963` (2 ย่อหน้า) | zone 2 "The second zone allows you to explore…" (seed `intro_short` · บรรทัดใหม่ใน seed = ย่อหน้า → เก็บคั่นบรรทัดว่าง) | zone (ใต้แถบ · zone 5 ข้าง Cover) | ✔ |
| `sh_intro_full` | `textarea` → string | — (zone 1–3) | สถานะขยาย `3117:5060/5068/5064` | ข้อความเต็ม (รวมย่อหน้าแรก · seed `body_full`) · ว่าง = ไม่มี Read more | zone (Read more ↔ Hide `sh_label_hide`) | ✔ |
| `sh_page_card_1_image` | `image` → integer | — (zone 5) | `Cover` `3117:5082` default · `[160,770 280×459]` | Passport | zone 5 | ✔ |
| `sh_page_card_1_image_hover` | `image` → integer | — (zone 5) | 〃 hover (ON_HOVER 0.3s) | | zone 5 | ✔ |
| `sh_page_card_1_link` | `post_object` → integer (page) | — | ลูกศร annotation → 1515 (ยังเป็น Lorem D40) | ว่าง = ไม่ลิงก์ | zone 5 | ✔ |
| `sh_page_card_1_soon` | `true_false` → boolean | — | D40 "เร็ว ๆ นี้" | card 1/2 true | zone 5 (ป้าย `sh_label_coming_soon`) | ✔ |
| `sh_page_card_2_image` | `image` → integer | — (zone 5) | `Cover2` `3117:5097` · `[440,770 280×459]` | Card game | zone 5 | ✔ |
| `sh_page_card_2_image_hover` | `image` → integer | — (zone 5) | 〃 hover | | zone 5 | ✔ |
| `sh_page_card_2_link` | `post_object` → integer (page) | — | → 1516 (Lorem) | ว่าง | zone 5 | ✔ |
| `sh_page_card_2_soon` | `true_false` → boolean | — | D40 | true | zone 5 | ✔ |
| `sh_page_card_3_image` | `image` → integer | — (zone 5) | `Cover3` `3117:5087` | "Good day to you, Cast Brass Tabletop Horse" | zone 5 | ✔ |
| `sh_page_card_3_image_hover` | `image` → integer | — (zone 5) | 〃 hover | | zone 5 | ✔ |
| `sh_page_card_3_link` | `post_object` → integer (page) | — | ไม่ระบุใน Figma | ว่าง | zone 5 | ✔ |
| `sh_page_card_3_soon` | `true_false` → boolean | — | — | false | zone 5 | ✔ |
| `sh_page_card_4_image` | `image` → integer | — (zone 5) | `Cover4` `3117:5092` | Shared Histories folder | zone 5 | ✔ |
| `sh_page_card_4_image_hover` | `image` → integer | — (zone 5) | 〃 hover | | zone 5 | ✔ |
| `sh_page_card_4_link` | `post_object` → integer (page) | — | ไม่ระบุ | ว่าง | zone 5 | ✔ |
| `sh_page_card_4_soon` | `true_false` → boolean | — | — | false | zone 5 | ✔ |
| `sh_page_strip_image_1` | `image` → integer | — (zone 5) | แถวภาพ `[160,1254 280×123]` | passport 3 สี | zone 5 | ✔ |
| `sh_page_strip_image_2` | `image` → integer | — (zone 5) | `[440,1254 280×123]` | การ์ด 3 ใบ | zone 5 | ✔ |
| `sh_page_strip_image_3` | `image` → integer | — (zone 5) | `[720,1254 280×123]` | | zone 5 | ✔ |
| `sh_page_strip_image_4` | `image` → integer | — (zone 5) | `[1000,1254 280×123]` | | zone 5 | ✔ |
| `sh_page_history_image` | `image` → integer | — (zone 5) | `History` `3117:5102` History1 `[160,1402 1120×453]` | "Shared Histories" | zone 5 | ✔ |
| `sh_page_history_image_hover` | `image` → integer | — (zone 5) | History2 (hover) | | zone 5 | ✔ |
ชื่อ zone (D38 · §4.3b): term name = B ("Animals in Nature and Mythology" …) = H1 + การ์ดหน้าแรก (`sh_card_title`) · ชื่อ A (ตัวหนังสือในภาพ banner) = alt ของ attachment banner (`_wp_attachment_image_alt` ตั้งตอน import ถ้าว่าง) · tab "Zone page cards" ใช้กับ zone 5 (zone อื่นเว้นว่าง) · การ์ดที่ไม่มีภาพ = ไม่แสดง

## 6. Home (front page) — group: home
| ฟิลด์ (meta key) | ชนิด (ACF → meta) | ต้องมีค่า | ที่มาใน Figma (node) | ตัวอย่าง (seed `site.json` home) | ใช้ในหน้า | REST |
|---|---|---|---|---|---|---|
| `sh_home_hero_image` | `image` → integer | ✔ | `670:9367` (ข้อความในภาพ D8) | `2e3fa9c4…` 1120×466 | home | ✔ |
| `sh_home_intro_title` | `text` → string | ✔ | `670:9372` | "Introduction" | home | ✔ |
| `sh_home_intro_paragraph_1` | `textarea` → string | ✔ | `670:9373` | "What comes to mind when you look at…" | home | ✔ |
| `sh_home_intro_paragraph_2` | `textarea` → string | ✔ | `670:9374` | "To get started, let’s dive into…" | home | ✔ |
| `sh_home_intro_image` | `image` → integer | ✔ | `670:9370` | `3f4cfd00…` 551×284 | home | ✔ |
| `sh_home_intro_link` | `post_object` → integer (page) | ✔ | Read more → หน้า 1471 | หน้า Intro | home | ✔ |
| `sh_home_zone_bar_image` | `image` → integer | ✔ | `885:1201` (ZONE n ในภาพ) | `30d9d301…` 1120×166 | home | ✔ |
| `sh_home_trails_title` | `text` → string | ✔ | `I886:1787;886:1761` | "Trails" | home | ✔ |
| `sh_home_trails_tagline` | `text` → string | ✔ | `I886:1787;886:1762` | "Step beyond traditional gallery walls…" | home | ✔ |
| `sh_home_trails_paragraph_1` | `textarea` → string | ✔ | `I886:1787;886:1763` | "In this borderless digital world…" | home (หลัง Read more) | ✔ |
| `sh_home_trails_paragraph_2` | `textarea` → string | ✔ | `I886:1787;886:1767` | "Forget rigid physical galleries!…" | home (หลัง Read more) | ✔ |
| `sh_home_trails_video_webm` | `file` → integer | — | AAMM -01 `[160,1913 1120×630]` รอยเท้า (§6.2) = manifest รายการ `usage: home-trails` (`walking`) | `walking.webm` 1120×630 | home | ✔ |
| `sh_home_trails_video_poster` | `image` → integer | — | manifest `walking.poster` | `walking.poster.webp` | home | ✔ |
Zone cards 1–5 บนหน้าแรก = term `zone` (§5) · ไอคอน trail 17 = `sh_trails('home')` · navigator 4 จุด = พฤติกรรมหน้า (ไม่ใช่ข้อมูล)

## 7. Intro (page `intro`) — group: intro
| ฟิลด์ (meta key) | ชนิด (ACF → meta) | ต้องมีค่า | ที่มาใน Figma (node) | ตัวอย่าง (seed `articles.json`) | ใช้ในหน้า | REST |
|---|---|---|---|---|---|---|
| `sh_intro_cover_image` | `image` → integer | ✔ | Cover `[160,150 1120×466]` (ภาพเดียวกับ home hero) | `2e3fa9c4…` | intro | ✔ |
| `sh_intro_title` | `text` → string | ✔ | `670:1223` | "Introduction" | intro | ✔ |
| `sh_intro_paragraph_1` | `textarea` → string | ✔ | `670:1224` | "What comes to mind…" | intro | ✔ |
| `sh_intro_paragraph_2` | `textarea` → string | ✔ | `670:1225` | "To get started…" | intro | ✔ |
| `sh_intro_body` | `textarea` → string | ✔ | `670:1227` | "The Southeast Asian region has long been shaped…" | intro | ✔ |
| `sh_intro_image` | `image` → integer | ✔ | `670:1221` | `bec4f086…` 549×284 | intro | ✔ |
| `sh_intro_knowing_title` | `text` → string | ✔ | `670:1231` | "Knowing Southeast Asia" | intro | ✔ |
| `sh_intro_knowing_paragraph_1` | `textarea` → string | ✔ | `670:1232` | "What comes to mind when we think of Southeast Asia?…" | intro | ✔ |
| `sh_intro_knowing_paragraph_2` | `textarea` → string | ✔ | `670:1233` | "This exhibition suggests it is all this and more…" | intro | ✔ |
| `sh_intro_knowing_body` | `textarea` → string | ✔ | `670:1237` | "Southeast Asia was formalised as a region…" | intro | ✔ |
| `sh_intro_knowing_image` | `image` → integer | ✔ | `670:1236` | `74e1d6fd…` 549×284 | intro | ✔ |
| `sh_intro_map_image` | `image` → integer | ✔ | ภาพแผนที่ในเฟรม 1471 `I670:1239;670:1462` (Frame 12655 fill — P-150 · D14 ไม่ใช่ Section 11 `670:1596` `cd63a05d`) | `39907d2c…` PNG 4000×2250 (แสดง 1120×620) | intro map | ✔ |
| `sh_intro_map_slide_1_title` | `text` → string | ✔ | `670:1479` | ": People, Places, and Shared Histories" | intro map | ✔ |
| `sh_intro_map_slide_1_body` | `textarea` → string | ✔ | `670:1480` `670:1481` (ย่อหน้าคั่นบรรทัดว่าง) | "Southeast Asia is defined by…" | intro map + popup | ✔ |
| `sh_intro_map_slide_2_title` | `text` → string | ✔ | `670:1501` | ": Early Centres of Power" | intro map | ✔ |
| `sh_intro_map_slide_2_body` | `textarea` → string | ✔ | `670:1502–1504` | "In Southeast Asia, early centers of power…" | intro map + popup | ✔ |
| `sh_intro_map_slide_3_title` | `text` → string | ✔ | `670:1524` | ": Rice and Spice" | intro map | ✔ |
| `sh_intro_map_slide_3_body` | `textarea` → string | ✔ | `670:1525–1527` | "Rice and spices have long shaped…" | intro map + popup | ✔ |
| `sh_intro_map_slide_4_title` | `text` → string | ✔ | `670:1547` | ": Animals and Ecosystems" | intro map | ✔ |
| `sh_intro_map_slide_4_body` | `textarea` → string | ✔ | `670:1548` `670:1549` | "Southeast Asia spans mainland river basins…" | intro map + popup | ✔ |

## 8. Timeline (page `timeline`) — group: timeline
| ฟิลด์ (meta key) | ชนิด (ACF → meta) | ต้องมีค่า | ที่มาใน Figma (node) | ตัวอย่าง (seed `timeline-elephant.json`) | ใช้ในหน้า | REST |
|---|---|---|---|---|---|---|
| `sh_timeline_trail` | `taxonomy` → integer (term id) | ✔ | frame `670:1551` = trail ช้าง | elephant | timeline (การ์ด = `sh_timeline_items()`) | ✔ |
| `sh_timeline_illustration` | `image` → integer | ✔ | `1232:1711` (ชื่อ+ป้ายยุคในภาพ D8) | `791ae91b…` 11000×791 | timeline | ✔ |
| `sh_timeline_reflection_title` | `text` → string | ✔ | `3103:1377` (D34 · เดิม `1232:1816`) | "Reflection" | timeline จุดที่ 16 (D34 · DESIGN-SPEC §4.5c.7) · ว่าง → "Reflection" | ✔ |
| `sh_timeline_reflection_body` | `textarea` → string | ✔ | `3103:1378` (D34 · เดิม `1232:1817`) | "The objects in this trail reveal the elephant's enduring cultural presence across South-East Asia. …" (3 ย่อหน้า · บรรทัดว่าง = ย่อหน้า · `\n` เดี่ยว = ขึ้นบรรทัด) | timeline จุดที่ 16 (D34 · §4.5c.7) · **ว่าง = ไม่มีจุดที่ 16** | ✔ |
page title = ชื่อ zone 4 "Animal Trails: Reflections & Interpretation"

## 9. Site (Settings › Shared Heritage · wp_options) — group: site
| ฟิลด์ (option) | ชนิด (ACF → meta) | ต้องมีค่า | ที่มาใน Figma (node) | ตัวอย่าง (seed `site.json`) | ใช้ในหน้า | REST |
|---|---|---|---|---|---|---|
| `sh_label_read_more` | `text` → string (default "Read more >>") | ✔ | `I670:9375;670:1786` | "Read more >>" | ทุกหน้า | ✔ |
| `sh_label_hide` | `text` → string (default "Hide >>") | ✔ | `I3117:4318;3117:4321` (§3.4 อัปเดต) | "Hide >>" | home trails · zone 1–3 (Read more ที่ขยายแล้ว) | ✔ |
| `sh_label_coming_soon` | `text` → string (default "Coming soon") | — | D40 ("เร็ว ๆ นี้" · ไม่มีใน Figma — P-173) | "Coming soon" | zone 5 การ์ดที่ `sh_page_card_<n>_soon` | ✔ |
| `sh_label_back` | `text` → string (default "Back") | ✔ | Button Back `670:1810` | "Back" | ทุกหน้า | ✔ |
| `sh_label_zone_prefix` | `text` → string (default "ZONE") | — | ป้าย zone | "ZONE" | home · zone | ✔ |
| `sh_header_image` | `image` → integer | ✔ | ภาพพื้น Header #1 `670:9386` (โลโก้ในภาพ) | `709572bf…` | header ทุกหน้า | ✔ |
**เมนู** = เมนู WP (Appearance › Menus) ไม่ใช่ฟิลด์: `Primary` (seed `nav_primary` 5 รายการ · ผูก location `primary`) · `Utility` (seed `nav_utility` 6 · ผูก location `utility` เมื่อ theme register) · "Main Page" → หน้าแรก · ที่เหลือ custom link `#` (ปลายทางยังไม่กำหนด P-211)
**โลโก้ UNESCO (SVG)** = asset ของ theme ไม่ใช่ฟิลด์ (WP ไม่อนุญาตอัปโหลด SVG · เปิดต้องมี D เพราะเป็นชั้นพิเศษ upload)

## 10. ไม่อยู่ในสัญญา (เหตุผล)
- DESIGN-SPEC §5.1 `timeline_card_image` + `timeline.*` ของวัตถุ → อยู่ที่ `timeline_item` (§3) เพราะหัว/ข้อความการ์ดต่างจากหน้า detail · `title_raw` → `sh_title_raw` · `detail_style.*` → `sh_panel_bg` … `sh_collection_color` · `layout` → `sh_layout`
- `_sh_import_key` (post/term/attachment meta ซ่อน ไม่ลง REST) = ตัวชี้ของ importer สำหรับรันซ้ำ ไม่ใช่ข้อมูลหน้า
- hero GIF หลายเฟรม (`hero_images[]` 3–24 เฟรม) → เก็บ `sh_hero_image` ภาพนิ่ง 1 ใบ + วิดีโอ (D13: media-dev ต่อเฟรมเป็น WebM) · `hero_still` ที่ซ่อนใน Figma ไม่ใช้
- หมุดแผนที่ (`pins[]` x,y) — ไม่มีใน seed · popup ใช้ข้อความ slide เดียวกัน (DESIGN-SPEC §5.5)
- `home.side_navigator` · `home.trails_animation_frames[12]` (ใช้วิดีโอ `walking` แทน) · `status[]` (ข้อสังเกตภายในของ seed) · `tile_bg.group` (ชื่อกลุ่ม ไม่ใช้แสดงผล)
- ความสัมพันธ์ zone ↔ trail/object (P-212) — กริด zone ใช้ `sh_show_in_zone_grid` ตาม seed

## 11. API สำหรับ template (mu-plugin `inc/api.php` · คืนค่าดิบ — template ต้อง escape เอง)
| ฟังก์ชัน | คืน |
|---|---|
| `sh_get( $key, $post_id = 0 )` · `sh_get_term( $key, $term )` · `sh_get_option( $key )` | ค่า meta/option (ว่าง = `''`/0) |
| `sh_page_id( 'front'\|'intro'\|'timeline' )` | id หน้า (0 = ไม่มี) |
| `sh_trails( 'home'\|'zone_grid'\|'timeline_nav' )` | `WP_Term[]` เรียงตาม `sh_order_home_icons` / `sh_zone_grid_order` (เฉพาะ `sh_show_in_zone_grid`) / `sh_timeline_nav_order` |
| `sh_zones()` | `WP_Term[]` เรียงตาม `sh_number` |
| `sh_trail_objects( $trail )` | `WP_Post[]` วัตถุ publish ของ trail ตาม `sh_object_order` (ที่ไม่อยู่ในลำดับต่อท้าย A–Z) |
| `sh_timeline_items( $trail )` | `WP_Post[]` การ์ด publish ของ trail ตาม `sh_card_order` |
| `sh_object_hero( $id )` | `{webm, hevc, poster (URL), image_id, has_alpha}` |
| `sh_object_extra_images( $id )` · `sh_object_layout( $id )` · `sh_paragraphs( $text )` | id ภาพแผง · layout array · ย่อหน้า string[] |

## 12. Import (`scripts/import/`)
- `import.php` (+ `lib.php` `refs.php` `icc.php` `media.php` `entities.php` `objects.php` `content-2026-11.php` `refresh.php` `refresh-2026-11.php` · ข้อมูล `image-map.json` `legacy-values.json`) รันใน WP: local อัตโนมัติใน `scripts/local.sh` · server `SH_IMPORT_ASSETS_DIR=… SH_IMPORT_MEDIA_DIR=… wp eval-file scripts/import/import.php`
- อ่าน `content/seed/*.json` + `content/media-manifest.json` + `scripts/import/image-map.json` · ภาพจาก `Unesco/design/assets/` · วิดีโอ/poster จาก `Unesco/media/out/` (นอก repo · path จาก env/constant `SH_IMPORT_ASSETS_DIR` `SH_IMPORT_MEDIA_DIR`)
- `image-map.json` สร้างด้วย `python3 scripts/import/build-image-map.py` จาก `design/figma-file.json` (node ใน seed → imageRef = ชื่อไฟล์ · กฎ: fill IMAGE ที่ visible ชั้นบนสุด · `missing` = ref ที่ไม่มีไฟล์) — รันใหม่เมื่อ seed/Figma/`design/assets` เปลี่ยน
- **วิดีโอ/ภาพ hero อ่านจาก manifest ตรง (ไม่อยู่ใน image-map):** วัตถุ = รายการที่ `object_slug` = slug (`kind: video` → `out` + `poster` + HEVC เฉพาะ `fallback.type = hevc-alpha-mov` · `kind: still` → `still` เป็น `sh_hero_image`) · Home Trails = รายการ `usage: home-trails` · media-dev เพิ่มรายการใหม่ → import รอบหน้าหยิบเอง ไม่ต้องแก้โค้ด
- **ICC → sRGB (`icc.php`):** JPEG/PNG ที่ฝัง ICC profile ที่ชื่อไม่ขึ้นต้น `sRGB` (Apple "Color LCD"/Display P3/DCI-P3 ใน `design/assets` 73 ไฟล์) → ลบ profile (JPEG APP2 · PNG iCCP+cICP+cHRM+gAMA) ตอนคัดลอก **ไม่แปลงค่าพิกเซล** — Figma วาดค่าพิกเซลดิบโดยไม่สนใจ profile · วัดแล้ว: tile `8428e71b` Figma (241,173,160) = ค่าดิบ · ถ้าแปลงสีจริงได้ (253,169,158) ≠ Figma · ไม่ re-encode (lossless) · PHP ล้วน ไม่ต้องมี GD/Imagick
- **ภาพก่อน timeline (`sh_line_art` · D32 → D35/D36):** seed `timeline-elephant.json items[].line_art_ref` = ชื่อไฟล์ใน `design/assets/lineart/` (หาที่นั่นก่อน → ไม่เจอใช้กฎ `*_ref` ที่ราก `design/assets`) → attachment `asset:lineart/<ชื่อไม่มีนามสกุล>` → `sh_line_art` · ไม่มีคีย์ = ไม่แตะฟิลด์ · ไม่มีไฟล์ = คำเตือน · `bundle-files.php` ใส่ไฟล์เหล่านี้ใน bundle เป็น `ref<TAB>lineart/<file>` (path ใต้ assets ชั้นเดียว)
- **ภาพใต้เส้น timeline (`sh_below_image` · D39/D41):** seed `items[].below_ref` = ชื่อไฟล์ที่ราก `design/assets/` (กฎ `*_ref`) → attachment `asset:<ชื่อไม่มีนามสกุล>` → `sh_below_image` · ไม่มีคีย์ = ไม่แตะฟิลด์ · ไม่มีไฟล์ = คำเตือน · `bundle-files.php` ใส่ไฟล์เป็น `ref<TAB><file>` · รูปบนเส้นชิ้น 5/7 (D39) = `image-map.json` `timeline_items.5/.7` **แก้มือ** ตาม seed `thumbnail_refs` (`build-image-map.py` อ่าน `figma-file.json` 08:23Z ไม่ได้ — รันใหม่จะทับค่านี้ · P-261)
- **idempotent:** หาเดิมด้วย `_sh_import_key` (post/attachment) · slug (term) · path (page) · ชื่อ (menu) → `SH_IMPORT_MODE=missing` (default) ไม่แตะของที่มีแล้ว (ไม่ทับงานที่ admin แก้) · `overwrite` = seed ทับ
- **แก้ค่าที่ importer รุ่นก่อนเขียนผิด = refresh step (`refresh.php`)** รันครั้งเดียวต่อ site (option `sh_import_refresh_done`) แม้ mode `missing` · แก้เฉพาะฟิลด์ที่ระบุ และเฉพาะเมื่อค่าเดิมว่าง หรือยังเป็น attachment ที่ importer สร้าง (มี `_sh_import_key`) — ไฟล์ที่ admin อัปโหลด/เลือกเองไม่ถูกแทน · `2026-11-11-reflection` (D34): `sh_timeline_reflection_title`/`_body` ของหน้า timeline → ข้อความ seed ปัจจุบัน **เฉพาะเมื่อค่าบน site ยังตรงข้อความ seed เดิมทุกไบต์** (เทียบ sha256 ของค่าที่ import รุ่นก่อนเขียน) · admin แก้แล้ว = ไม่แตะ · `2026-11-11-line-art` (D35/D36): `timeline_item` ที่มีแล้ว (key `timeline:elephant:<card_order>`) + seed มี `line_art_ref` → ตั้ง `sh_line_art` **เฉพาะเมื่อฟิลด์ยังว่าง** (ค่าที่ admin ตั้ง/ที่ import เขียนไว้แล้วไม่แตะ) · ไฟล์ขาด = ยังไม่นับว่าเสร็จ (รันใหม่รอบหน้า) · `2026-11-11-timeline-images` (D39/D41): `sh_thumbnail` ชิ้น 5/7 → ref ใหม่ใน image-map **เฉพาะเมื่อยังเป็น attachment ที่ import รุ่นก่อนสร้างจากภาพเดิมของชิ้นนั้นพอดี** (key `asset:5d9931b4…` / `asset:867b2833…` · admin อัปโหลด/เลือกภาพอื่น/ลบภาพ = ไม่แตะ) · `sh_below_image` จาก `below_ref` **เฉพาะเมื่อว่าง** · ไฟล์ขาด = ยังไม่นับว่าเสร็จ · `2026-10-27-media`: hero วัตถุ (`sh_hero_*`) · `sh_home_trails_video_*` · `sh_intro_map_image` · ICC ของไฟล์ `asset:*` ที่คัดลอกไปแล้ว (มี sub-size → สร้างใหม่) · บังคับรันซ้ำ `SH_IMPORT_REFRESH=<id>|all` · import report มี `refresh: [ids ที่รันรอบนี้]` · attachment เก่าที่เลิกใช้ (เช่น แผนที่ `cd63a05d`) ไม่ลบ
- **รอบ Figma 2026-11-11 (core-hzd · D37 D38 D40 · `content-2026-11.php` + `refresh-2026-11.php`):**
  - seed ที่อ่านเพิ่ม: `zone4-chooser.json` · `zones.json` (`name`/`name_raw` = ชื่อ B · `intro_short` → `sh_intro` · `body_full` → `sh_intro_full` · `banner_image.file` → `sh_cover_image` + alt `name_banner` · zone 5 `page` ตาม P-172) · `objects.json` `figma_2026_11.{title,title_raw,caption}` (มี = ใช้แทน `title`/`title_raw`/`caption` · slug = `slug` เสมอ) · ข้อความ zones: บรรทัดใหม่ 1 ครั้ง = ย่อหน้า → เก็บคั่นบรรทัดว่าง (`sh_paragraphs()`)
  - **zone 5 `page` (seed · design-dev เติม P-172):** `{cover, zone_bar, cards: [{image, image_hover, soon}×4], strip: [×4], history: {image, image_hover}}` → `sh_cover_image` `sh_banner_image` `sh_page_card_<n>_image(_hover)` `sh_page_card_<n>_soon` `sh_page_strip_image_<n>` `sh_page_history_image(_hover)` · ลิงก์การ์ด (`sh_page_card_<n>_link`) ไม่ import (admin เลือกเมื่อหน้าย่อยพร้อม) · `cards[].alt` / `history.alt` → alt ของ attachment (ตั้งเมื่อว่าง) · คีย์หมายเหตุ (`frame` `figma_nodes` `figma_target` `strip_nodes`) ไม่อ่าน · seed มาแล้ว (design-dev e62b480 · ภาพ `design/assets/zone5/` 16 ไฟล์)
  - **ค่าภาพใน seed ใหม่** = path `design/assets/<sub>/<file>` (sub-folder ชั้นเดียว · รายชื่อ `refs.php sh_import_asset_subdirs()` = `zone-banners-2026-11` `zone4-animals` `zone5`) หรือ imageRef/ไฟล์ที่ราก · ข้อความหลังช่องว่างแรก = หมายเหตุ (ตัดทิ้ง) · attachment key `asset:<sub>/<ชื่อไม่มีนามสกุล>` · bundle (`bundle-files.php`) รวมให้เอง
  - `image-map.json` (build 2026-11-11): ส่วน objects อ่าน `design/figma-file.json` ใหม่ (P-245: Brass Basin `3190:4671` · ภาพแผงใหม่ Ganesha/Singhasari/Molar/Patola/Bas-relief 3) · ส่วนอื่นอ่าน `design/figma-file.2026-10-27.json` ก่อน (`--figma-fallback` · หน้าอื่นยังไม่ได้ spec กับไฟล์ใหม่ §1.1b — ไฟล์ใหม่เปลี่ยน hero หน้าแรก/zone cover ที่ยังไม่มีใครสั่ง) · `_meta.nodes_from_older_dump`
  - **refresh (site ที่ import ก่อนรอบนี้ = production):** ค่าเดิมแทน **เฉพาะเมื่อยังเท่าค่าที่ import รุ่นก่อนเขียน** — `scripts/import/legacy-values.json` (สร้างด้วย `python3 scripts/import/build-legacy-values.py [base=f818837]` จากประวัติ git ของ seed/image-map ด้วยสูตรเดียวกับ importer · ข้อความเทียบตรง/หลัง sanitize/หลัง decode entity · `sh_layout` เทียบ fingerprint `refs.php sh_import_layout_fingerprint()` (ลำดับ key · รูปแบบตัวเลข · `*_ref` เป็นไฟล์หรือ attachment id ของ import = เท่ากัน) · ภาพแผง 1..6 เทียบรายการ key ทั้งชุด · `EXTRA` = ค่าที่ทีมแก้บน prod ตามคำสั่งเจ้าของ (Pod Duang `sh_title_raw` 2026-10-28)) · ฟิลด์ใหม่เติมเมื่อว่าง (true/false เมื่อยังไม่เคยบันทึก) · step: `2026-11-11-zones` (zone 1–3) · `-objects` (title · title_raw · caption · แถวข้อมูล · สี 5 · `sh_layout` · `sh_extra_image_1..6` · ไม่แตะ slug) · `-chooser` (zone 4 `sh_link_page` = หน้า Timeline → ว่าง · พื้น/แถบ · trail `sh_chooser_image`) · `-zone5` (รอ seed P-172: ไม่บันทึกว่าเสร็จ · ไม่เตือน) · `-media-replace` (ไฟล์ media/out เปลี่ยนแต่ชื่อเดิม: เขียนทับไฟล์ของ attachment ที่ import สร้างเมื่อ sha256 = ไฟล์รุ่นเก่า (`legacy-values.json` `media`) · id/URL เดิม · metadata ใหม่ · admin เปลี่ยนไฟล์ = ไม่ทับ) · ไฟล์ขาด = ไม่บันทึกว่าเสร็จ · ค่าที่ admin แก้ = นับใน report `skipped.kept_admin_value` / `kept_admin_file`
  - ผลตรวจ (local 2026-11-11 · core-hzd): ดูรายงาน core-hzd (`orchestrator/evidence/core-hzd/`)
- ผลนับ (local 2026-10-27 · core-import-refresh): object 15 · timeline_item 15 · trail 17 · zone 5 · page 3 · attachment 205 · menu 2 · ICC→sRGB 67 ไฟล์ · คำเตือน 0 · รอบที่ 2 สร้างใหม่ 0 / แก้ 0 · import เก่า→ใหม่บน site เดียวกัน: วิดีโอ 7→14 · HEVC 4→6 · แผนที่ → 39907d2c · ICC 68 · ภาพ hero ที่ admin ตั้งเองคงเดิม
- ภาพนำเข้าโดยไม่สร้าง sub-size (ต้นฉบับใหญ่ · playground ช้า) · server ใช้ `SH_IMPORT_SUBSIZES=1`
