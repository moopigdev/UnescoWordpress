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
| Zone 1–3 (1472–1474) | `taxonomy-zone.php` | zone | taxonomy `zone` term `zone-<n>` §5 · กริด = `sh_trails('zone_grid')` | `/zone/zone-1/` `/zone/zone-2/` `/zone/zone-3/` |
| Trail ช้าง 1501 | `taxonomy-trail.php` | trail | taxonomy `trail` term `elephant` §4 · วัตถุ = `sh_trail_objects()` | `/trail/elephant/` |
| Timeline (Zone 4) | `page-timeline.php` | timeline | Page slug **`timeline`** · group `timeline` §8 · การ์ด = CPT `timeline_item` §3 | `/timeline/` |
| Detail วัตถุ 15 ชิ้น | `single-object.php` | detail | CPT `object` §2 | `/object/ganesha/` (ครบ 15 ใน `docs/smoke-urls`) |
Zone 4 ไม่มีหน้า zone ของตัวเอง: term `zone-4` มี `sh_link_page` = หน้า Timeline (seed: page_frame ของ zone 4 = frame timeline `670:1551`) · Zone 5 `sh_has_page` = false

## Entity (ภาพรวม)
| entity | ชนิด WP | REST base | public URL | หมายเหตุ |
|---|---|---|---|---|
| `object` | CPT (`title` · `custom-fields` · `revisions` · ไม่มี editor) | `object` | `/object/<slug>/` | title = ชื่อวัตถุ (seed `title`) · slug = seed `slug` · trails = taxonomy `trail` (หลายค่า, checkbox) |
| `timeline_item` | CPT ไม่ public (admin + REST เท่านั้น) | `timeline_item` | — | title = หัวการ์ด (seed `items[].title`) · `menu_order` = card_order |
| `trail` | taxonomy (บน `object` · hierarchical เพื่อ checkbox · ไม่ใช้ parent) | `trail` | `/trail/<slug>/` | 17 term (seed `etc` ไม่ใช่ trail → ไม่ import) · name = seed `name` ตามตัว (เช่น "Elephan" "Phonix" — ชื่อแสดงผลยังไม่ออกแบบ P-2xx) · description = คำอธิบายหน้า trail (seed `description`, มีเฉพาะ elephant) |
| `zone` | taxonomy (บน `object` · ยังไม่ผูกวัตถุ/trail — P-212) | `zone` | `/zone/zone-<n>/` | 5 term · name = seed `name` · term ว่างก็ได้ 200 |
| page Home / Intro / Timeline | Page มาตรฐาน | `pages` | `/` `/intro/` `/timeline/` | ฟิลด์ผูกกับหน้า: Home = front page · Intro/Timeline = หน้า slug `intro`/`timeline` |
| site | options (Settings › Shared Heritage) + เมนู WP | `settings` | — | §9 |

## 2. `object` — group: object
| ฟิลด์ (meta key) | ชนิด (ACF → meta) | ต้องมีค่า | ที่มาใน Figma (node) | ตัวอย่าง (seed) | ใช้ในหน้า | REST |
|---|---|---|---|---|---|---|
| (post_title) | title | ✔ | title 36/800 `670:2797` | "Ganesha" | detail · trail tile alt · timeline link | ✔ (`title`) |
| (taxonomy `trail`) | term[] | ✔ | section Page 2.1 / กริดช้าง | Ganesha `[elephant]` · Embossed Brass Basin `[elephant, garuda, lion, naga]` | trail · detail | ✔ (`trail`) |
| `sh_title_raw` | `textarea` → string | — (3/15) | title node (ตัวแบ่งบรรทัดดิบ) | Leg Bone: "Elephant Leg Bone\n(Elephas maximus indicus)" · ว่าง = ใช้ post_title | detail | ✔ |
| `sh_collection` | `text` → string | ✔ (14/15) | `Collection:` ใน metadata `670:2798` | "National Museum Bangkok" | detail | ✔ |
| `sh_thematic_section` | `text` → string | ✔ | `Section : …` `670:2798` | "Cosmology Religion & Spirituality" | detail | ✔ |
| `sh_period` | `text` → string | — (10/15) | `Period:` | "10th–11th century" | detail | ✔ |
| `sh_place_of_production` | `text` → string | — (11/15) | `Place of Production / Findspot:` | "Candi Singhasari, East Java, Indonesia" | detail | ✔ |
| `sh_material` | `text` → string | — (13/15) | `Material:` | "Andesite, ****Volcanic rock" (คัดลอกตรง) | detail | ✔ |
| `sh_dimensions` | `text` → string | — (13/15) | `Dimensions:` | "Height: 172 cm" | detail | ✔ |
| `sh_local_name` | `text` → string | — (0/15 ช้าง) | บรรทัดใน metadata (Page 2.1) | Liturgical Pulpit Covers: "Pha Hum Thammasan / …" | detail | ✔ |
| `sh_object_biography` | `textarea` → string | — (10/15) | `Specimen/Object biography:` | "The Dutch government in Java presented this…" | detail | ✔ |
| `sh_caption` | `textarea` → string | ✔ (14/15 — Bas-relief มีแต่ lorem P-213) | หลัง "Caption:" `670:2799` | "Carved from volcanic stone, this monumental statue…" (ย่อหน้าคั่นบรรทัดว่าง) | detail | ✔ |
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
| `sh_line_art` | `image` → integer | — (ว่างทุกรายการ · seed ไม่มีค่า) | — (D26 · ไม่มีใน Figma) | ว่าง → ใช้ภาพ `sh_thumbnail` แบบจาง/เทา | timeline แถวบน (วัตถุที่ไม่อยู่กลาง) | ✔ |

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
| `sh_cover_image` | `image` → integer | — (elephant) | `796:1799` | `ed0e5ee9…` 1120×497 | trail | ✔ |
| `sh_timeline_page` | `post_object` → integer (page) | — (ว่าง = ไม่มี timeline) | frame `670:1551` | elephant → หน้า Timeline | trail · timeline | ✔ |
| `sh_object_order` | `relationship` → array&lt;int&gt; | ✔ | ลำดับ tile `Ele 2…` §4.8 (seed `trail_order`) | elephant: Leg Bone, Singhasari Ganesha, Ganesha, … Bas-relief (15) | trail (ลาก-วางเรียงใน admin) | ✔ |

## 5. `zone` (term) — group: zone
| ฟิลด์ (meta key) | ชนิด (ACF → meta) | ต้องมีค่า | ที่มาใน Figma (node) | ตัวอย่าง (seed `zones.json`) | ใช้ในหน้า | REST |
|---|---|---|---|---|---|---|
| (name / slug) | term | ✔ | `I670:9480;670:9416` | "Animals in Nature & Ecological Wisdom" / `zone-1` | zone · home | ✔ |
| `sh_number` | `number` → integer | ✔ | — | 1 | home · zone | ✔ |
| `sh_card_title` | `textarea` → string | ✔ | ข้อความการ์ด (มี `\n`) | "Animals in Nature \n& Ecological Wisdom" | home | ✔ |
| `sh_color` | `color_picker` → string | ✔ | pixel แถบ zone (§2.1) | `#14705E` | home · zone | ✔ |
| `sh_card_image` | `image` → integer | ✔ | set `Zone 1` `670:9423` default `670:9415` | `342629af…` 373×487 · zone 4/5 560×294 | home | ✔ |
| `sh_card_image_hover` | `image` → integer | ✔ | 〃 hover `670:9419` | `35c9b660…` | home | ✔ |
| `sh_cover_image` | `image` → integer | — (zone 1–3) | `1013:1289` · `1179:5759` · `1179:5762` (ชื่อ zone ในภาพ D8) | zone 1 `10247f33…` 1120×410 | zone | ✔ |
| `sh_banner_image` | `image` → integer | — (zone 1–3) | `1013:1292` · `1013:1343` · `1013:1369` | zone 1 `413791d6…` 1120×166 | zone | ✔ |
| `sh_has_page` | `true_false` → boolean | ✔ | — | zone 1–4 true · zone 5 false | home (คลิกได้ไหม) | ✔ |
| `sh_link_page` | `post_object` → integer (page) | — | page_frame ของ zone = frame timeline | zone 4 → หน้า Timeline | home (ปลายทางการ์ด) | ✔ |

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
| `sh_timeline_reflection_title` | `text` → string | ✔ | `1232:1816` | "Reflection" | timeline | ✔ |
| `sh_timeline_reflection_body` | `textarea` → string | ✔ | `1232:1817` | "The objects in this trail reveal the elephant's enduring presence…" | timeline | ✔ |
page title = ชื่อ zone 4 "Animal Trails: Reflections & Interpretation"

## 9. Site (Settings › Shared Heritage · wp_options) — group: site
| ฟิลด์ (option) | ชนิด (ACF → meta) | ต้องมีค่า | ที่มาใน Figma (node) | ตัวอย่าง (seed `site.json`) | ใช้ในหน้า | REST |
|---|---|---|---|---|---|---|
| `sh_label_read_more` | `text` → string (default "Read more >>") | ✔ | `I670:9375;670:1786` | "Read more >>" | ทุกหน้า | ✔ |
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
- `import.php` (+ `lib.php` `icc.php` `media.php` `entities.php` `objects.php` `refresh.php`) รันใน WP: local อัตโนมัติใน `scripts/local.sh` · server `SH_IMPORT_ASSETS_DIR=… SH_IMPORT_MEDIA_DIR=… wp eval-file scripts/import/import.php`
- อ่าน `content/seed/*.json` + `content/media-manifest.json` + `scripts/import/image-map.json` · ภาพจาก `Unesco/design/assets/` · วิดีโอ/poster จาก `Unesco/media/out/` (นอก repo · path จาก env/constant `SH_IMPORT_ASSETS_DIR` `SH_IMPORT_MEDIA_DIR`)
- `image-map.json` สร้างด้วย `python3 scripts/import/build-image-map.py` จาก `design/figma-file.json` (node ใน seed → imageRef = ชื่อไฟล์ · กฎ: fill IMAGE ที่ visible ชั้นบนสุด · `missing` = ref ที่ไม่มีไฟล์) — รันใหม่เมื่อ seed/Figma/`design/assets` เปลี่ยน
- **วิดีโอ/ภาพ hero อ่านจาก manifest ตรง (ไม่อยู่ใน image-map):** วัตถุ = รายการที่ `object_slug` = slug (`kind: video` → `out` + `poster` + HEVC เฉพาะ `fallback.type = hevc-alpha-mov` · `kind: still` → `still` เป็น `sh_hero_image`) · Home Trails = รายการ `usage: home-trails` · media-dev เพิ่มรายการใหม่ → import รอบหน้าหยิบเอง ไม่ต้องแก้โค้ด
- **ICC → sRGB (`icc.php`):** JPEG/PNG ที่ฝัง ICC profile ที่ชื่อไม่ขึ้นต้น `sRGB` (Apple "Color LCD"/Display P3/DCI-P3 ใน `design/assets` 73 ไฟล์) → ลบ profile (JPEG APP2 · PNG iCCP+cICP+cHRM+gAMA) ตอนคัดลอก **ไม่แปลงค่าพิกเซล** — Figma วาดค่าพิกเซลดิบโดยไม่สนใจ profile · วัดแล้ว: tile `8428e71b` Figma (241,173,160) = ค่าดิบ · ถ้าแปลงสีจริงได้ (253,169,158) ≠ Figma · ไม่ re-encode (lossless) · PHP ล้วน ไม่ต้องมี GD/Imagick
- **idempotent:** หาเดิมด้วย `_sh_import_key` (post/attachment) · slug (term) · path (page) · ชื่อ (menu) → `SH_IMPORT_MODE=missing` (default) ไม่แตะของที่มีแล้ว (ไม่ทับงานที่ admin แก้) · `overwrite` = seed ทับ
- **แก้ค่าที่ importer รุ่นก่อนเขียนผิด = refresh step (`refresh.php`)** รันครั้งเดียวต่อ site (option `sh_import_refresh_done`) แม้ mode `missing` · แก้เฉพาะฟิลด์ที่ระบุ และเฉพาะเมื่อค่าเดิมว่าง หรือยังเป็น attachment ที่ importer สร้าง (มี `_sh_import_key`) — ไฟล์ที่ admin อัปโหลด/เลือกเองไม่ถูกแทน · `2026-10-27-media`: hero วัตถุ (`sh_hero_*`) · `sh_home_trails_video_*` · `sh_intro_map_image` · ICC ของไฟล์ `asset:*` ที่คัดลอกไปแล้ว (มี sub-size → สร้างใหม่) · บังคับรันซ้ำ `SH_IMPORT_REFRESH=<id>|all` · import report มี `refresh: [ids ที่รันรอบนี้]` · attachment เก่าที่เลิกใช้ (เช่น แผนที่ `cd63a05d`) ไม่ลบ
- ผลนับ (local 2026-10-27 · core-import-refresh): object 15 · timeline_item 15 · trail 17 · zone 5 · page 3 · attachment 205 · menu 2 · ICC→sRGB 67 ไฟล์ · คำเตือน 0 · รอบที่ 2 สร้างใหม่ 0 / แก้ 0 · import เก่า→ใหม่บน site เดียวกัน: วิดีโอ 7→14 · HEVC 4→6 · แผนที่ → 39907d2c · ICC 68 · ภาพ hero ที่ admin ตั้งเองคงเดิม
- ภาพนำเข้าโดยไม่สร้าง sub-size (ต้นฉบับใหญ่ · playground ช้า) · server ใช้ `SH_IMPORT_SUBSIZES=1`
