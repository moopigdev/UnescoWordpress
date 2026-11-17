# CONTENT-MODEL — สัญญาข้อมูล (CPT · taxonomy · meta)

**เจ้าของไฟล์:** wp-dev (งาน `core-contract`) · หน้าทุกหน้าอ่านจากที่นี่ ⛔ ห้ามหน้าใดตั้งฟิลด์เอง · ต้องการฟิลด์ใหม่ → `docs/PENDING-DECISIONS.md`
**ที่มา:** DESIGN-SPEC §5 (ฟิลด์ที่หน้าใช้) + §8.4 + §9.6 (รอบ 2026-11-17) + `content/seed/*.json` + `content/media-manifest.json` · ขอบเขต import = **วัตถุทุก trail** (D49 แทน D7 · ตั้งแต่ core-1117)
**โค้ด:** mu-plugin `wp-content/mu-plugins/sharedheritage-core/` — ตารางในไฟล์นี้ = `inc/fields.php` 1:1 ตรวจด้วย `python3 scripts/import/check-contract.py` (เทียบ key · ชนิด · REST ต่อ group)

## 0. หลักการ / วิธี register (D1 · O-7)
- ข้อมูลอยู่ใน **mu-plugin** ไม่ใช่ theme · meta key ขึ้นต้น `sh_` · ทุกฟิลด์แก้ได้จากหน้า edit มาตรฐาน (post/term/page edit · Settings › Shared Heritage)
- นิยามฟิลด์ที่เดียว `inc/fields.php` → (1) `register_post_meta` / `register_term_meta` / `register_setting` (ชนิด · sanitize · auth = `edit_post`/`edit_term`/`manage_options` · REST) (2) ACF ฟรี `acf_add_local_field_group` (UI · version อยู่ใน git ไม่อยู่ใน DB)
- **ACF ไม่ active → ไม่ fatal:** admin notice + ข้อมูลยังอ่านได้ด้วย `get_post_meta`/API §11 + REST (meta ลงทะเบียนกับ WP เอง) · ACF ฟรีไม่มี repeater/gallery/options page → รายการหลายค่าใช้ฟิลด์เลขลำดับ (`sh_extra_image_1..6`) · options ใช้ Settings API
- **textarea `format`:** `json` = ต้องเป็น JSON object (`sh_layout` `sh_trail_layout` `sh_intro_*_layout`) · `inline_html` = ข้อความหลายบรรทัดที่มี `<strong>` `<b>` `<em>` `<i>` `<br>` ได้ (ไม่มี attribute · tag อื่นถูกตัด) → อ่านด้วย `sh_rich_paragraphs()` (`sh_guide_body` · `sh_intro_ch<n>_text_<k>`)
- **ชนิด → เก็บเป็น:** text/textarea/color_picker → string · number/image/file/post_object/taxonomy → integer (id) · true_false → boolean · relationship → array&lt;int&gt; · textarea ไม่แปลงบรรทัด (`new_lines` ว่าง) — หน้าแยกย่อหน้าด้วยบรรทัดว่าง (`sh_paragraphs()`)
- **"ต้องมีค่า"** = เนื้อหาต้องมีเพื่อให้หน้าตรงดีไซน์ · ACF **ไม่บังคับ** ฟิลด์ใด (`required=0` ให้บันทึกร่างได้) · หน้าต้องรับมือค่าว่าง (DOD B6)
- **REST** ✔ = `show_in_rest` → ค่าอยู่ใน `meta.sh_*` ของ `/wp/v2/object` · `/wp/v2/timeline_item` · `/wp/v2/trail` · `/wp/v2/zone` · `/wp/v2/pages` · `/wp/v2/settings` (ต้อง login) · ACF ไม่เปิด REST ของตัวเอง (`show_in_rest=0`)
- ชื่อ term เก็บแบบ WP (`&` → `&amp;` ใน DB) · แสดงด้วย `esc_html()` (ไม่ escape ซ้ำ)

## 1. Route (ตาราง PLAN · ตายตัว) → ข้อมูลที่รองรับ
| หน้า (frame Final) | template | asset slug (`sh_template_slug_map()`) | ข้อมูล | URL ตัวอย่าง (import แล้ว) |
|---|---|---|---|---|
| Home 1470 | `front-page.php` | home | Page "Home" (slug `home`) ตั้งเป็นหน้าแรก (`show_on_front=page`) · group `home` §6 · zone/trail terms | `/` |
| Intro 1471 (+ map slider) | `page-intro.php` | intro | Page slug **`intro`** · group `intro` §7 | `/intro/` |
| Zone 1–3 (1472–1474 · ใหม่ `3117:3684/3713/3742` §4.3b) | `taxonomy-zone.php` | zone | taxonomy `zone` term `zone-<n>` §5 · กริด = **วัตถุ** `sh_zone_grid()` §5b (D57 · เดิม `sh_trails('zone_grid')`) · ข้อความ `sh_intro` → Read more `sh_intro_full` | `/zone/zone-1/` `/zone/zone-2/` `/zone/zone-3/` |
| **Zone 4 หน้าคั่นเลือกสัตว์** (1513 `3117:3946` · D37 · §4.10) | `taxonomy-zone.php` (WP เลือก `taxonomy-zone-zone-4.php` ก่อนถ้า theme มี → asset slug = ชื่อไฟล์) | zone (หรือชื่อไฟล์) | term `zone-4` §5 (`sh_cover_image` = พื้นเส้นประ · `sh_banner_image` = แถบ ZONE (Zone 4 active)) · สัตว์ = `sh_trails('home')` ที่มี `sh_chooser_image` §4 · ลิงก์ = `sh_timeline_page` ของ trail (มี = ลิงก์ · ไม่มี = ไม่ลิงก์ D37/D40) · ตำแหน่ง/ขนาดต่อตัว = ค่าคงที่ใน theme (§4.10 · seed `zone4-chooser.json`) | `/zone/zone-4/` |
| **Zone 5 หน้าหลัก** (1514 `3117:3955` · D40 · §4.11) | `taxonomy-zone.php` (หรือ `taxonomy-zone-zone-5.php`) | zone (หรือชื่อไฟล์) | term `zone-5` §5 (`sh_has_page` · `sh_cover_image` · `sh_intro` · `sh_banner_image` · tab "Zone page cards") | `/zone/zone-5/` |
| Trail ทั้ง 17 (ช้าง 1501 · 16 trail `3377:4608` D49) | `taxonomy-trail.php` | trail | taxonomy `trail` §4 · ช่องกริด = `sh_trail_tiles()` (วัตถุ + ช่องไม่มีหน้า · ภาพช่องของ trail นั้น) · เดิม `sh_trail_objects()` | `/trail/elephant/` `/trail/bird/` … (ครบใน `docs/smoke-urls`) |
| Timeline (Zone 4) | `page-timeline.php` | timeline | Page slug **`timeline`** · group `timeline` §8 · การ์ด = CPT `timeline_item` §3 | `/timeline/` |
| Detail วัตถุทุก trail (ช้าง 15 + 16 trail 101 publish · D49) | `single-object.php` | detail | CPT `object` §2 (16 trail = `sh_layout` generic §8.3) | `/object/ganesha/` `/object/domestic-chicken/` (ช้าง 15 + 1 ชิ้นต่อ trail ใน `docs/smoke-urls`) |
Zone 4 (D37 · 2026-11-11): **หน้าคั่น = หน้า zone ของตัวเอง `/zone/zone-4/`** — `sh_link_page` ของ `zone-4` = ว่าง (การ์ดหน้าแรกจึงลิงก์ term link · `/timeline/` ไม่เปลี่ยน · ช้างในหน้าคั่นลิงก์ไป `/timeline/` ผ่าน `sh_timeline_page` ของ trail elephant) · เดิม (ก่อน D37) `sh_link_page` = หน้า Timeline → refresh `2026-11-11-chooser` ล้างค่านี้บน site เดิม · Zone 5 `sh_has_page` = ตาม seed (D40: true เมื่อ seed มีหน้า — P-172) · แยก layout ต่อ zone = template hierarchy `taxonomy-zone-zone-<n>.php` (ไม่มีฟิลด์ "ชนิดหน้า")

## Entity (ภาพรวม)
| entity | ชนิด WP | REST base | public URL | หมายเหตุ |
|---|---|---|---|---|
| `object` | CPT (`title` · `custom-fields` · `revisions` · ไม่มี editor) | `object` | `/object/<slug>/` | title = ชื่อวัตถุ (seed `figma_2026_11.title` → `title` · D40) · slug = seed `slug` (**ไม่เปลี่ยนตามชื่อ** — ลิงก์เดิมใช้ได้) · บล็อกใต้หัวข้อ (§4.8.2): `sh_period` → `sh_place_of_production` → `sh_material` → `sh_dimensions` → `sh_collection` → `sh_object_biography` (ป้าย "Other information / Object biography:") ข้ามค่าว่าง · trails = taxonomy `trail` (หลายค่า, checkbox) |
| `timeline_item` | CPT ไม่ public (admin + REST เท่านั้น) | `timeline_item` | — | title = หัวการ์ด (seed `items[].title`) · `menu_order` = card_order |
| `trail` | taxonomy (บน `object` · hierarchical เพื่อ checkbox · ไม่ใช้ parent) | `trail` | `/trail/<slug>/` | 17 term (seed `etc` ไม่ใช่ trail → ไม่ import) · name = seed `name` ตามตัว (เช่น "Elephan" "Phonix" — ชื่อแสดงผลยังไม่ออกแบบ P-2xx) · description = คำอธิบายหน้า trail (seed `description` · ครบ 17 ตั้งแต่ core-1117) · **การ์ดสัตว์หน้าแรก (D48) = ฟิลด์ `sh_guide_*` บน term ของ trail นั้น** (17 การ์ด ↔ 17 trail · seed `guides.json cards[].trail_slug`) |
| `zone` | taxonomy (บน `object` · ยังไม่ผูกวัตถุ/trail — P-212) | `zone` | `/zone/zone-<n>/` | 5 term · name = seed `name` · term ว่างก็ได้ 200 |
| page Home / Intro / Timeline | Page มาตรฐาน | `pages` | `/` `/intro/` `/timeline/` | ฟิลด์ผูกกับหน้า: Home = front page · Intro/Timeline = หน้า slug `intro`/`timeline` |
| site | options (Settings › Shared Heritage) + เมนู WP | `settings` | — | §9 |

## 2. `object` — group: object
| ฟิลด์ (meta key) | ชนิด (ACF → meta) | ต้องมีค่า | ที่มาใน Figma (node) | ตัวอย่าง (seed) | ใช้ในหน้า | REST |
|---|---|---|---|---|---|---|
| (post_title) | title | ✔ | title 36/800 `670:2797` → **2026-11-11 = `3083:813`… (§4.8.2 · D40)** | "Ganesha" · "Hand-carved opium pipe" (seed `figma_2026_11.title` ถ้ามี · ไม่มี = `title`) | detail · trail tile alt · timeline link | ✔ (`title`) |
| (taxonomy `trail`) | term[] | ✔ | section Page 2.1 / กริดช้าง | Ganesha `[elephant]` · Embossed Brass Basin `[elephant, garuda, lion, naga]` | trail · detail | ✔ (`trail`) |
| `sh_title_raw` | `textarea` → string | — (3/15) | title node (ตัวแบ่งบรรทัดดิบ) | Leg Bone: "Elephant Leg Bone\n(Elephas maximus indicus)" · ว่าง = ใช้ post_title | detail | ✔ |
| `sh_collection` | `textarea` → string | ✔ (14/15) | `Collection:` ใน metadata `670:2798` | "National Museum Bangkok" · **หลายตัวอย่าง = บรรทัดละค่า** (Giant Clams 2 บรรทัด · core-1117 เปลี่ยนจาก `text` ที่ตัด `\n`) | detail (`\n` = ขึ้นบรรทัดในแถวเดียว) | ✔ |
| `sh_scientific_name` | `textarea` → string | — (13 วัตถุ) | แถว `Scientific name / Taxonomy:` ใน meta node ของ frame detail (§8.3 · §8.4) | Domestic Chicken: "Gallus gallus domesticus (Linnaeus, 1758); Galliformes: Phasianidae" · Giant Clams 2 บรรทัด | detail (แถว "Scientific name / Taxonomy:" · ลำดับแถวเป็นของหน้า D42 · ข้อเสนอ §8.3 = ก่อน Period) | ✔ |
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
| `sh_trail_tile_1_trail` | `taxonomy` → integer (term id) | — | trail อื่นของวัตถุที่อยู่หลาย trail (seed `tiles_by_trail` ที่ไม่ใช่ trail หลัก · §8.2) | Monk’s alms bowl: lion | trail grid (`sh_trail_tiles()`) | ✔ |
| `sh_trail_tile_1_image` | `image` → integer | — | ภาพช่องของ trail นั้น (Materials · พื้นสีของ trail ฝังในภาพ) | `9250a1ef…` (lion) | trail grid ของ trail ใน `sh_trail_tile_1_trail` · ว่าง = ใช้ `sh_tile_image` | ✔ |
| `sh_trail_tile_1_image_hover` | `image` → integer | — | ภาพ hover ของ trail นั้น | | trail grid hover | ✔ |
| `sh_trail_tile_2_trail` | `taxonomy` → integer (term id) | — | trail อื่นของวัตถุที่อยู่หลาย trail (seed `tiles_by_trail` ที่ไม่ใช่ trail หลัก · §8.2) | Monk’s alms bowl: seashell | trail grid (`sh_trail_tiles()`) | ✔ |
| `sh_trail_tile_2_image` | `image` → integer | — | ภาพช่องของ trail นั้น (Materials · พื้นสีของ trail ฝังในภาพ) | `9250a1ef…` (lion) | trail grid ของ trail ใน `sh_trail_tile_2_trail` · ว่าง = ใช้ `sh_tile_image` | ✔ |
| `sh_trail_tile_2_image_hover` | `image` → integer | — | ภาพ hover ของ trail นั้น | | trail grid hover | ✔ |
| `sh_trail_tile_3_trail` | `taxonomy` → integer (term id) | — | trail อื่นของวัตถุที่อยู่หลาย trail (seed `tiles_by_trail` ที่ไม่ใช่ trail หลัก · §8.2) | Monk’s alms bowl: ว่าง | trail grid (`sh_trail_tiles()`) | ✔ |
| `sh_trail_tile_3_image` | `image` → integer | — | ภาพช่องของ trail นั้น (Materials · พื้นสีของ trail ฝังในภาพ) | `9250a1ef…` (lion) | trail grid ของ trail ใน `sh_trail_tile_3_trail` · ว่าง = ใช้ `sh_tile_image` | ✔ |
| `sh_trail_tile_3_image_hover` | `image` → integer | — | ภาพ hover ของ trail นั้น | | trail grid hover | ✔ |
| `sh_trail_tile_4_trail` | `taxonomy` → integer (term id) | — | trail อื่นของวัตถุที่อยู่หลาย trail (seed `tiles_by_trail` ที่ไม่ใช่ trail หลัก · §8.2) | Monk’s alms bowl: ว่าง | trail grid (`sh_trail_tiles()`) | ✔ |
| `sh_trail_tile_4_image` | `image` → integer | — | ภาพช่องของ trail นั้น (Materials · พื้นสีของ trail ฝังในภาพ) | `9250a1ef…` (lion) | trail grid ของ trail ใน `sh_trail_tile_4_trail` · ว่าง = ใช้ `sh_tile_image` | ✔ |
| `sh_trail_tile_4_image_hover` | `image` → integer | — | ภาพ hover ของ trail นั้น | | trail grid hover | ✔ |
| `sh_panel_bg` | `color_picker` → string | — (ว่าง = ไม่มี fill) | fill แผง `670:2796` | `#B2BBAA` · Patola = ว่าง | detail | ✔ |
| `sh_title_color` | `color_picker` → string | ✔ | fill title | `#D75F47` | detail | ✔ |
| `sh_text_color` | `color_picker` → string | ✔ (14/15) | fill caption | `#212529` | detail | ✔ |
| `sh_meta_color` | `color_picker` → string | — (4/15 · ว่าง = text) | fill metadata block (`figma[].style.meta_color`) | Ganesha `#F8F9FA` | detail | ✔ |
| `sh_collection_color` | `color_picker` → string | — (ว่าง = text) | fill `Collection:` | Leg Bone `#F8F9FA` | detail | ✔ |
| `sh_layout` | `textarea` → string (JSON object · ตรวจตอนบันทึก) | ✔ (ตรง 100%) | ตาราง DESIGN-SPEC §4.8 (`figma[Final].style`) | Ganesha `{"frame_id":"670:2786","panel_y":1000,"panel_h":1005,"panel_padding_top_bottom":[60,80],"hero":[{"bbox":[0,150,1440,810]…}],"back_button":[160,1887,115,50],…}` | detail (geometry) | ✔ |
`sh_layout` keys: `frame_id frame_name frame_size hero[] back_button panel_padding_top_bottom panel_children[] panel_gap panel_node panel_bg panel_has_fill panel_y panel_h title_color collection_color caption_color meta_color` (bbox = `[x,y,w,h]` เทียบ frame 1440) · อ่านด้วย `sh_object_layout()`
- **วัตถุ 16 trail (core-1117 · §8.3 โหมด generic):** `sh_layout` = `figma[]` ของ frame หลัก (`is_primary` · ไม่มี = `primary_frame` · ไม่มี = ตัวแรก) → คีย์ generic `frame_size hero back_button panel_* title_color caption_color meta_color text_col image_col images[]` (ไม่มี `text_blocks`) · `images[].crop_ref` → attachment id (กฎ `*_ref`) และ **ภาพเดียวกันใส่ `sh_extra_image_<n>` ตามลำดับ** (admin เปลี่ยนภาพได้ D10) · ภาพช่อง = `tile_image_ref`/`tile_image_hover_ref` (trail หลัก) → `sh_tile_image(_hover)` · trail อื่น = `sh_trail_tile_<n>_*` · hero = manifest key `drive-1117/<hero_video_src>` · status มี `hold_*` (`pending` · `sumatran-serow`) = **draft** (ไม่ publish · admin เปิดเองเมื่อข้อมูลครบ)
- import ส่ง seed `figma[Final].style` **ทั้งก้อน** (คีย์ที่ไม่รู้จักผ่านไปด้วย — DESIGN-SPEC §4.8 เป็นที่นิยามคีย์) · **คีย์ใดก็ได้ (ทุกระดับ) ที่ลงท้าย `_ref`** และค่าเป็นชื่อไฟล์ใน `design/assets` (มี/ไม่มีนามสกุล เช่น ภาพครอปของ design-dev `<ref>.crop-<node>.png`) หรือ imageRef ใน `image-map.json` → ค่าใน `sh_layout` กลายเป็น **attachment id (integer)** · ไม่มีไฟล์ → คงข้อความเดิม + คำเตือนใน import report (ไม่ fatal) · หน้าอ่าน: `is_int( $v )` → `wp_get_attachment_image( $v, … )`

## 2b. `object` — เวอร์ชันต่อ trail (D59 · multitrail-1117 · BACKLOG #41) — group: object_variant
วัตถุที่อยู่หลาย trail (18 ชิ้น) แสดงหน้า detail ตาม trail ที่ผู้ชมกดมา: **animation (WebM ของ trail นั้น) · สีแผง + สีตัวอักษร · ภาพแผงข้าง** ของ trail นั้น และ**ข้อความของ trail นั้นเฉพาะที่ Figma ต่าง** (ที่เหลือใช้ข้อความชุดเดียวของวัตถุ) · เข้าตรง/ไม่รู้ trail/trail ที่ไม่ใช่ของวัตถุ/trail ที่ไม่มีเวอร์ชัน = วัตถุตามปกติ (trail หลัก) · URL ของวัตถุเดียว (canonical ไม่เปลี่ยน)
- **โครงข้อมูล = ช่อง slot เลขลำดับ** (แบบเดียวกับ `sh_trail_tile_<n>_*` · ACF ฟรีไม่มี repeater): **ค่าของ trail หลัก = ฟิลด์ของวัตถุเอง** (`sh_hero_*` · `sh_panel_bg` · `sh_layout` · `sh_extra_image_*` · ข้อความ — ไม่เปลี่ยนเลย) · **trail อื่นแต่ละ trail = 1 slot** `sh_variant_<n>_*` (n = 1..`SH_VARIANT_SLOTS` = 4 · วัตถุอยู่ได้ถึง 5 trail) · slot ที่ `sh_variant_<n>_trail` ว่าง = ไม่ใช้ · แท็บละ 1 slot ในกล่อง "Trail versions" ของหน้าแก้วัตถุ
- **ช่องว่าง = ใช้ของวัตถุ:** hero (ไม่มีวิดีโอ = hero เดิมของวัตถุ) · สี 5 ตัว · ภาพข้าง · layout · title · caption — ทีละฟิลด์ · **ข้อยกเว้น: แถวข้อมูล 8 ช่อง** (`sh_variant_<n>_scientific_name … _object_biography`) = มีช่องใดช่องหนึ่งมีค่า → แสดง**เฉพาะแถวที่มีค่า แทนทั้งชุดของวัตถุ** (Figma: dragon-shaped tray ใน seashell เหลือแถว Collection แถวเดียว) · ว่างหมด = แถวของวัตถุ
- **ภาพข้าง:** `sh_variant_<n>_image_<k>` แทนภาพลำดับ k ของ layout ของ version (ตำแหน่ง/ขนาดมาจาก `…_layout` `images[]`) · ว่าง = ภาพใน layout (`crop_ref` → attachment id)
- **เลือก version:** `single-object.php` อ่าน query `?sh_trail=<trail slug>` (ไม่ใช้ `?trail=` — เป็น query var ของ taxonomy trail ใน WP: ค่าปลอมทำให้ 404) → `sh_object_variant( $id, $slug )` (whitelist: trail ต้องเป็น term ของวัตถุนั้น และมี slot) · ลิงก์ที่ใส่ `?sh_trail=`: ช่องกริดหน้า trail (`sh_trail_tiles()` · trail ของหน้านั้น) · ช่องกริด Zone 1–3 (`sh_zone_grid()` · `sh_zone_tile_<n>_trail` ใน §5b) · เฉพาะเมื่อวัตถุมี slot ของ trail นั้น (วัตถุ trail เดียว/trail หลัก = ลิงก์เดิม ไม่เติม query) · ปุ่ม Back: เดิมกลับ trail หลัก → ตอนนี้กลับ trail ของ version (ยังกลับ zone เดิมเมื่อมาจาก `?from=zone-<n>` · history back ยังมาก่อน)
- **ที่มา seed:** `objects.json[].variants_by_trail.<trail>` (DESIGN-SPEC §8.7.1) · import = refresh step `2026-11-17-multitrail` (§12 · รันตอน fresh import ด้วย) → ตัวอย่าง: Holy water vessel (trail หลัก dragon) → slot 1 = naga (hero `Serpent/Blackened Brass Ritual Holy Water Vessel.webm` · แผง `#F1ADA0` · ภาพข้าง `946674fb…`) · dragon tray (หลัก dragon) → slot 1 = seashell (title · caption · แถว Collection แถวเดียว) · cake molds (หลัก elephant) → slot 1 = horse (title · caption) · slot 2 = rooster (title) · basin (หลัก elephant) → slot 1–3 = garuda · lion · naga
- **นับ:** 18 วัตถุหลาย trail · variant ใน seed 41 = trail หลัก 18 (= ฟิลด์ของวัตถุ) + **slot 23** (D60: Đông Sơn drum แยกเป็น 2 วัตถุ — `ng-s-n-drum` water-bird · `dong-son-drum-thung-yang` seashell — ไม่ใช่ version)
| ฟิลด์ (meta key) | ชนิด (ACF → meta) | ต้องมีค่า | ที่มาใน seed | ตัวอย่าง | ใช้ในหน้า | REST |
|---|---|---|---|---|---|---|
| `sh_variant_1_trail` | `taxonomy` → integer | — (ว่าง = slot ไม่ใช้) | `variants_by_trail.<trail>` ของ trail ที่ไม่ใช่ trail หลัก (`is_primary:false`) | naga (Blackened brass holy water vessel · trail หลัก = dragon) | detail: เลือก version จาก `?sh_trail=<slug>` | ✔ |
| `sh_variant_1_hero_video_webm` | `file` → integer | — | `variants_by_trail.<trail>.hero_video_src` → manifest key `drive-1117/<path>` `out` | `Serpent/Blackened Brass Ritual Holy Water Vessel.webm` | detail hero (ว่าง = hero ของวัตถุ) | ✔ |
| `sh_variant_1_hero_video_hevc` | `file` → integer | — | manifest `fallback.file` เฉพาะ `hevc-alpha-mov` (ทึบ = ไม่มี) |  | detail hero `<source>` แรกสำหรับ Safari | ✔ |
| `sh_variant_1_hero_video_has_alpha` | `true_false` → boolean | — | manifest `has_alpha` | false | detail hero | ✔ |
| `sh_variant_1_hero_poster` | `image` → integer | — | manifest `poster` |  | detail hero `poster=` | ✔ |
| `sh_variant_1_panel_bg` | `color_picker` → string | — | `variants_by_trail.<trail>.panel_bg` | `#F1ADA0` | detail (ว่าง = สีของวัตถุ) | ✔ |
| `sh_variant_1_title_color` | `color_picker` → string | — | `…title_color` | `#212529` | detail | ✔ |
| `sh_variant_1_text_color` | `color_picker` → string | — | `…caption_color` | `#413B2F` | detail | ✔ |
| `sh_variant_1_meta_color` | `color_picker` → string | — | `…meta_color` | `#413B2F` | detail | ✔ |
| `sh_variant_1_collection_color` | `color_picker` → string | — | `…collection_color` | `#413B2F` | detail | ✔ |
| `sh_variant_1_image_1` | `image` → integer | — | `variants_by_trail.<trail>.side_images[0].crop_ref` (design/assets/objects-1117) | `8e4037fc…crop-3191-3184.jpg` | detail แผงข้าง (แทนภาพลำดับที่ 1 ของ layout) | ✔ |
| `sh_variant_1_image_2` | `image` → integer | — | `…side_images[1].crop_ref` |  | detail แผงข้าง (ลำดับที่ 2) | ✔ |
| `sh_variant_1_layout` | `textarea` → string | — | `figma[frame_id = variant.figma_frame].style` (รูปเดียวกับ `sh_layout` generic · `*_ref` → attachment id) | `{"frame_id":"1203:4085","panel_y":960,…}` | detail (geometry ของ trail นั้น · ว่าง = `sh_layout` ของวัตถุ) | ✔ |
| `sh_variant_1_title` | `textarea` → string | — | `…text_override.title_raw` (ไม่มี = `title`) เฉพาะ frame ที่ Figma ต่าง | dragon-shaped tray (seashell): "Dragon-Shaped Mother-of-Pearl Inlaid Incense Tray" | detail h1 (ว่าง = ชื่อวัตถุ) | ✔ |
| `sh_variant_1_caption` | `textarea` → string | — | `…text_override.caption` |  | detail caption (ว่าง = caption ของวัตถุ) | ✔ |
| `sh_variant_1_scientific_name` | `textarea` → string | — | `…text_override.info_rows[key=scientific_name]` |  | detail แถวข้อมูล (มีช่องใดช่องหนึ่ง = แสดงเฉพาะแถวที่มีค่า แทนแถวของวัตถุทั้งชุด · ว่างหมด = แถวของวัตถุ) | ✔ |
| `sh_variant_1_period` | `text` → string | — | `…text_override.info_rows[key=period]` |  | detail แถวข้อมูล (มีช่องใดช่องหนึ่ง = แสดงเฉพาะแถวที่มีค่า แทนแถวของวัตถุทั้งชุด · ว่างหมด = แถวของวัตถุ) | ✔ |
| `sh_variant_1_place_of_production` | `text` → string | — | `…text_override.info_rows[key=place_of_production]` | Đông Sơn drum (seashell) place: "Thung Yang District, Uttaradit Province" | detail แถวข้อมูล (มีช่องใดช่องหนึ่ง = แสดงเฉพาะแถวที่มีค่า แทนแถวของวัตถุทั้งชุด · ว่างหมด = แถวของวัตถุ) | ✔ |
| `sh_variant_1_material` | `text` → string | — | `…text_override.info_rows[key=material]` |  | detail แถวข้อมูล (มีช่องใดช่องหนึ่ง = แสดงเฉพาะแถวที่มีค่า แทนแถวของวัตถุทั้งชุด · ว่างหมด = แถวของวัตถุ) | ✔ |
| `sh_variant_1_dimensions` | `text` → string | — | `…text_override.info_rows[key=dimensions]` |  | detail แถวข้อมูล (มีช่องใดช่องหนึ่ง = แสดงเฉพาะแถวที่มีค่า แทนแถวของวัตถุทั้งชุด · ว่างหมด = แถวของวัตถุ) | ✔ |
| `sh_variant_1_local_name` | `text` → string | — | `…text_override.info_rows[key=local_name]` |  | detail แถวข้อมูล (มีช่องใดช่องหนึ่ง = แสดงเฉพาะแถวที่มีค่า แทนแถวของวัตถุทั้งชุด · ว่างหมด = แถวของวัตถุ) | ✔ |
| `sh_variant_1_collection` | `textarea` → string | — | `…text_override.info_rows[key=collection]` |  | detail แถวข้อมูล (มีช่องใดช่องหนึ่ง = แสดงเฉพาะแถวที่มีค่า แทนแถวของวัตถุทั้งชุด · ว่างหมด = แถวของวัตถุ) | ✔ |
| `sh_variant_1_object_biography` | `textarea` → string | — | `…text_override.info_rows[key=object_biography]` |  | detail แถวข้อมูล (มีช่องใดช่องหนึ่ง = แสดงเฉพาะแถวที่มีค่า แทนแถวของวัตถุทั้งชุด · ว่างหมด = แถวของวัตถุ) | ✔ |
| `sh_variant_2_trail` | `taxonomy` → integer | — (ว่าง = slot ไม่ใช้) | `variants_by_trail.<trail>` ของ trail ที่ไม่ใช่ trail หลัก (`is_primary:false`) | naga (Blackened brass holy water vessel · trail หลัก = dragon) | detail: เลือก version จาก `?sh_trail=<slug>` | ✔ |
| `sh_variant_2_hero_video_webm` | `file` → integer | — | `variants_by_trail.<trail>.hero_video_src` → manifest key `drive-1117/<path>` `out` | `Serpent/Blackened Brass Ritual Holy Water Vessel.webm` | detail hero (ว่าง = hero ของวัตถุ) | ✔ |
| `sh_variant_2_hero_video_hevc` | `file` → integer | — | manifest `fallback.file` เฉพาะ `hevc-alpha-mov` (ทึบ = ไม่มี) |  | detail hero `<source>` แรกสำหรับ Safari | ✔ |
| `sh_variant_2_hero_video_has_alpha` | `true_false` → boolean | — | manifest `has_alpha` | false | detail hero | ✔ |
| `sh_variant_2_hero_poster` | `image` → integer | — | manifest `poster` |  | detail hero `poster=` | ✔ |
| `sh_variant_2_panel_bg` | `color_picker` → string | — | `variants_by_trail.<trail>.panel_bg` | `#F1ADA0` | detail (ว่าง = สีของวัตถุ) | ✔ |
| `sh_variant_2_title_color` | `color_picker` → string | — | `…title_color` | `#212529` | detail | ✔ |
| `sh_variant_2_text_color` | `color_picker` → string | — | `…caption_color` | `#413B2F` | detail | ✔ |
| `sh_variant_2_meta_color` | `color_picker` → string | — | `…meta_color` | `#413B2F` | detail | ✔ |
| `sh_variant_2_collection_color` | `color_picker` → string | — | `…collection_color` | `#413B2F` | detail | ✔ |
| `sh_variant_2_image_1` | `image` → integer | — | `variants_by_trail.<trail>.side_images[0].crop_ref` (design/assets/objects-1117) | `8e4037fc…crop-3191-3184.jpg` | detail แผงข้าง (แทนภาพลำดับที่ 1 ของ layout) | ✔ |
| `sh_variant_2_image_2` | `image` → integer | — | `…side_images[1].crop_ref` |  | detail แผงข้าง (ลำดับที่ 2) | ✔ |
| `sh_variant_2_layout` | `textarea` → string | — | `figma[frame_id = variant.figma_frame].style` (รูปเดียวกับ `sh_layout` generic · `*_ref` → attachment id) | `{"frame_id":"1203:4085","panel_y":960,…}` | detail (geometry ของ trail นั้น · ว่าง = `sh_layout` ของวัตถุ) | ✔ |
| `sh_variant_2_title` | `textarea` → string | — | `…text_override.title_raw` (ไม่มี = `title`) เฉพาะ frame ที่ Figma ต่าง | dragon-shaped tray (seashell): "Dragon-Shaped Mother-of-Pearl Inlaid Incense Tray" | detail h1 (ว่าง = ชื่อวัตถุ) | ✔ |
| `sh_variant_2_caption` | `textarea` → string | — | `…text_override.caption` |  | detail caption (ว่าง = caption ของวัตถุ) | ✔ |
| `sh_variant_2_scientific_name` | `textarea` → string | — | `…text_override.info_rows[key=scientific_name]` |  | detail แถวข้อมูล (มีช่องใดช่องหนึ่ง = แสดงเฉพาะแถวที่มีค่า แทนแถวของวัตถุทั้งชุด · ว่างหมด = แถวของวัตถุ) | ✔ |
| `sh_variant_2_period` | `text` → string | — | `…text_override.info_rows[key=period]` |  | detail แถวข้อมูล (มีช่องใดช่องหนึ่ง = แสดงเฉพาะแถวที่มีค่า แทนแถวของวัตถุทั้งชุด · ว่างหมด = แถวของวัตถุ) | ✔ |
| `sh_variant_2_place_of_production` | `text` → string | — | `…text_override.info_rows[key=place_of_production]` | Đông Sơn drum (seashell) place: "Thung Yang District, Uttaradit Province" | detail แถวข้อมูล (มีช่องใดช่องหนึ่ง = แสดงเฉพาะแถวที่มีค่า แทนแถวของวัตถุทั้งชุด · ว่างหมด = แถวของวัตถุ) | ✔ |
| `sh_variant_2_material` | `text` → string | — | `…text_override.info_rows[key=material]` |  | detail แถวข้อมูล (มีช่องใดช่องหนึ่ง = แสดงเฉพาะแถวที่มีค่า แทนแถวของวัตถุทั้งชุด · ว่างหมด = แถวของวัตถุ) | ✔ |
| `sh_variant_2_dimensions` | `text` → string | — | `…text_override.info_rows[key=dimensions]` |  | detail แถวข้อมูล (มีช่องใดช่องหนึ่ง = แสดงเฉพาะแถวที่มีค่า แทนแถวของวัตถุทั้งชุด · ว่างหมด = แถวของวัตถุ) | ✔ |
| `sh_variant_2_local_name` | `text` → string | — | `…text_override.info_rows[key=local_name]` |  | detail แถวข้อมูล (มีช่องใดช่องหนึ่ง = แสดงเฉพาะแถวที่มีค่า แทนแถวของวัตถุทั้งชุด · ว่างหมด = แถวของวัตถุ) | ✔ |
| `sh_variant_2_collection` | `textarea` → string | — | `…text_override.info_rows[key=collection]` |  | detail แถวข้อมูล (มีช่องใดช่องหนึ่ง = แสดงเฉพาะแถวที่มีค่า แทนแถวของวัตถุทั้งชุด · ว่างหมด = แถวของวัตถุ) | ✔ |
| `sh_variant_2_object_biography` | `textarea` → string | — | `…text_override.info_rows[key=object_biography]` |  | detail แถวข้อมูล (มีช่องใดช่องหนึ่ง = แสดงเฉพาะแถวที่มีค่า แทนแถวของวัตถุทั้งชุด · ว่างหมด = แถวของวัตถุ) | ✔ |
| `sh_variant_3_trail` | `taxonomy` → integer | — (ว่าง = slot ไม่ใช้) | `variants_by_trail.<trail>` ของ trail ที่ไม่ใช่ trail หลัก (`is_primary:false`) | naga (Blackened brass holy water vessel · trail หลัก = dragon) | detail: เลือก version จาก `?sh_trail=<slug>` | ✔ |
| `sh_variant_3_hero_video_webm` | `file` → integer | — | `variants_by_trail.<trail>.hero_video_src` → manifest key `drive-1117/<path>` `out` | `Serpent/Blackened Brass Ritual Holy Water Vessel.webm` | detail hero (ว่าง = hero ของวัตถุ) | ✔ |
| `sh_variant_3_hero_video_hevc` | `file` → integer | — | manifest `fallback.file` เฉพาะ `hevc-alpha-mov` (ทึบ = ไม่มี) |  | detail hero `<source>` แรกสำหรับ Safari | ✔ |
| `sh_variant_3_hero_video_has_alpha` | `true_false` → boolean | — | manifest `has_alpha` | false | detail hero | ✔ |
| `sh_variant_3_hero_poster` | `image` → integer | — | manifest `poster` |  | detail hero `poster=` | ✔ |
| `sh_variant_3_panel_bg` | `color_picker` → string | — | `variants_by_trail.<trail>.panel_bg` | `#F1ADA0` | detail (ว่าง = สีของวัตถุ) | ✔ |
| `sh_variant_3_title_color` | `color_picker` → string | — | `…title_color` | `#212529` | detail | ✔ |
| `sh_variant_3_text_color` | `color_picker` → string | — | `…caption_color` | `#413B2F` | detail | ✔ |
| `sh_variant_3_meta_color` | `color_picker` → string | — | `…meta_color` | `#413B2F` | detail | ✔ |
| `sh_variant_3_collection_color` | `color_picker` → string | — | `…collection_color` | `#413B2F` | detail | ✔ |
| `sh_variant_3_image_1` | `image` → integer | — | `variants_by_trail.<trail>.side_images[0].crop_ref` (design/assets/objects-1117) | `8e4037fc…crop-3191-3184.jpg` | detail แผงข้าง (แทนภาพลำดับที่ 1 ของ layout) | ✔ |
| `sh_variant_3_image_2` | `image` → integer | — | `…side_images[1].crop_ref` |  | detail แผงข้าง (ลำดับที่ 2) | ✔ |
| `sh_variant_3_layout` | `textarea` → string | — | `figma[frame_id = variant.figma_frame].style` (รูปเดียวกับ `sh_layout` generic · `*_ref` → attachment id) | `{"frame_id":"1203:4085","panel_y":960,…}` | detail (geometry ของ trail นั้น · ว่าง = `sh_layout` ของวัตถุ) | ✔ |
| `sh_variant_3_title` | `textarea` → string | — | `…text_override.title_raw` (ไม่มี = `title`) เฉพาะ frame ที่ Figma ต่าง | dragon-shaped tray (seashell): "Dragon-Shaped Mother-of-Pearl Inlaid Incense Tray" | detail h1 (ว่าง = ชื่อวัตถุ) | ✔ |
| `sh_variant_3_caption` | `textarea` → string | — | `…text_override.caption` |  | detail caption (ว่าง = caption ของวัตถุ) | ✔ |
| `sh_variant_3_scientific_name` | `textarea` → string | — | `…text_override.info_rows[key=scientific_name]` |  | detail แถวข้อมูล (มีช่องใดช่องหนึ่ง = แสดงเฉพาะแถวที่มีค่า แทนแถวของวัตถุทั้งชุด · ว่างหมด = แถวของวัตถุ) | ✔ |
| `sh_variant_3_period` | `text` → string | — | `…text_override.info_rows[key=period]` |  | detail แถวข้อมูล (มีช่องใดช่องหนึ่ง = แสดงเฉพาะแถวที่มีค่า แทนแถวของวัตถุทั้งชุด · ว่างหมด = แถวของวัตถุ) | ✔ |
| `sh_variant_3_place_of_production` | `text` → string | — | `…text_override.info_rows[key=place_of_production]` | Đông Sơn drum (seashell) place: "Thung Yang District, Uttaradit Province" | detail แถวข้อมูล (มีช่องใดช่องหนึ่ง = แสดงเฉพาะแถวที่มีค่า แทนแถวของวัตถุทั้งชุด · ว่างหมด = แถวของวัตถุ) | ✔ |
| `sh_variant_3_material` | `text` → string | — | `…text_override.info_rows[key=material]` |  | detail แถวข้อมูล (มีช่องใดช่องหนึ่ง = แสดงเฉพาะแถวที่มีค่า แทนแถวของวัตถุทั้งชุด · ว่างหมด = แถวของวัตถุ) | ✔ |
| `sh_variant_3_dimensions` | `text` → string | — | `…text_override.info_rows[key=dimensions]` |  | detail แถวข้อมูล (มีช่องใดช่องหนึ่ง = แสดงเฉพาะแถวที่มีค่า แทนแถวของวัตถุทั้งชุด · ว่างหมด = แถวของวัตถุ) | ✔ |
| `sh_variant_3_local_name` | `text` → string | — | `…text_override.info_rows[key=local_name]` |  | detail แถวข้อมูล (มีช่องใดช่องหนึ่ง = แสดงเฉพาะแถวที่มีค่า แทนแถวของวัตถุทั้งชุด · ว่างหมด = แถวของวัตถุ) | ✔ |
| `sh_variant_3_collection` | `textarea` → string | — | `…text_override.info_rows[key=collection]` |  | detail แถวข้อมูล (มีช่องใดช่องหนึ่ง = แสดงเฉพาะแถวที่มีค่า แทนแถวของวัตถุทั้งชุด · ว่างหมด = แถวของวัตถุ) | ✔ |
| `sh_variant_3_object_biography` | `textarea` → string | — | `…text_override.info_rows[key=object_biography]` |  | detail แถวข้อมูล (มีช่องใดช่องหนึ่ง = แสดงเฉพาะแถวที่มีค่า แทนแถวของวัตถุทั้งชุด · ว่างหมด = แถวของวัตถุ) | ✔ |
| `sh_variant_4_trail` | `taxonomy` → integer | — (ว่าง = slot ไม่ใช้) | `variants_by_trail.<trail>` ของ trail ที่ไม่ใช่ trail หลัก (`is_primary:false`) | naga (Blackened brass holy water vessel · trail หลัก = dragon) | detail: เลือก version จาก `?sh_trail=<slug>` | ✔ |
| `sh_variant_4_hero_video_webm` | `file` → integer | — | `variants_by_trail.<trail>.hero_video_src` → manifest key `drive-1117/<path>` `out` | `Serpent/Blackened Brass Ritual Holy Water Vessel.webm` | detail hero (ว่าง = hero ของวัตถุ) | ✔ |
| `sh_variant_4_hero_video_hevc` | `file` → integer | — | manifest `fallback.file` เฉพาะ `hevc-alpha-mov` (ทึบ = ไม่มี) |  | detail hero `<source>` แรกสำหรับ Safari | ✔ |
| `sh_variant_4_hero_video_has_alpha` | `true_false` → boolean | — | manifest `has_alpha` | false | detail hero | ✔ |
| `sh_variant_4_hero_poster` | `image` → integer | — | manifest `poster` |  | detail hero `poster=` | ✔ |
| `sh_variant_4_panel_bg` | `color_picker` → string | — | `variants_by_trail.<trail>.panel_bg` | `#F1ADA0` | detail (ว่าง = สีของวัตถุ) | ✔ |
| `sh_variant_4_title_color` | `color_picker` → string | — | `…title_color` | `#212529` | detail | ✔ |
| `sh_variant_4_text_color` | `color_picker` → string | — | `…caption_color` | `#413B2F` | detail | ✔ |
| `sh_variant_4_meta_color` | `color_picker` → string | — | `…meta_color` | `#413B2F` | detail | ✔ |
| `sh_variant_4_collection_color` | `color_picker` → string | — | `…collection_color` | `#413B2F` | detail | ✔ |
| `sh_variant_4_image_1` | `image` → integer | — | `variants_by_trail.<trail>.side_images[0].crop_ref` (design/assets/objects-1117) | `8e4037fc…crop-3191-3184.jpg` | detail แผงข้าง (แทนภาพลำดับที่ 1 ของ layout) | ✔ |
| `sh_variant_4_image_2` | `image` → integer | — | `…side_images[1].crop_ref` |  | detail แผงข้าง (ลำดับที่ 2) | ✔ |
| `sh_variant_4_layout` | `textarea` → string | — | `figma[frame_id = variant.figma_frame].style` (รูปเดียวกับ `sh_layout` generic · `*_ref` → attachment id) | `{"frame_id":"1203:4085","panel_y":960,…}` | detail (geometry ของ trail นั้น · ว่าง = `sh_layout` ของวัตถุ) | ✔ |
| `sh_variant_4_title` | `textarea` → string | — | `…text_override.title_raw` (ไม่มี = `title`) เฉพาะ frame ที่ Figma ต่าง | dragon-shaped tray (seashell): "Dragon-Shaped Mother-of-Pearl Inlaid Incense Tray" | detail h1 (ว่าง = ชื่อวัตถุ) | ✔ |
| `sh_variant_4_caption` | `textarea` → string | — | `…text_override.caption` |  | detail caption (ว่าง = caption ของวัตถุ) | ✔ |
| `sh_variant_4_scientific_name` | `textarea` → string | — | `…text_override.info_rows[key=scientific_name]` |  | detail แถวข้อมูล (มีช่องใดช่องหนึ่ง = แสดงเฉพาะแถวที่มีค่า แทนแถวของวัตถุทั้งชุด · ว่างหมด = แถวของวัตถุ) | ✔ |
| `sh_variant_4_period` | `text` → string | — | `…text_override.info_rows[key=period]` |  | detail แถวข้อมูล (มีช่องใดช่องหนึ่ง = แสดงเฉพาะแถวที่มีค่า แทนแถวของวัตถุทั้งชุด · ว่างหมด = แถวของวัตถุ) | ✔ |
| `sh_variant_4_place_of_production` | `text` → string | — | `…text_override.info_rows[key=place_of_production]` | Đông Sơn drum (seashell) place: "Thung Yang District, Uttaradit Province" | detail แถวข้อมูล (มีช่องใดช่องหนึ่ง = แสดงเฉพาะแถวที่มีค่า แทนแถวของวัตถุทั้งชุด · ว่างหมด = แถวของวัตถุ) | ✔ |
| `sh_variant_4_material` | `text` → string | — | `…text_override.info_rows[key=material]` |  | detail แถวข้อมูล (มีช่องใดช่องหนึ่ง = แสดงเฉพาะแถวที่มีค่า แทนแถวของวัตถุทั้งชุด · ว่างหมด = แถวของวัตถุ) | ✔ |
| `sh_variant_4_dimensions` | `text` → string | — | `…text_override.info_rows[key=dimensions]` |  | detail แถวข้อมูล (มีช่องใดช่องหนึ่ง = แสดงเฉพาะแถวที่มีค่า แทนแถวของวัตถุทั้งชุด · ว่างหมด = แถวของวัตถุ) | ✔ |
| `sh_variant_4_local_name` | `text` → string | — | `…text_override.info_rows[key=local_name]` |  | detail แถวข้อมูล (มีช่องใดช่องหนึ่ง = แสดงเฉพาะแถวที่มีค่า แทนแถวของวัตถุทั้งชุด · ว่างหมด = แถวของวัตถุ) | ✔ |
| `sh_variant_4_collection` | `textarea` → string | — | `…text_override.info_rows[key=collection]` |  | detail แถวข้อมูล (มีช่องใดช่องหนึ่ง = แสดงเฉพาะแถวที่มีค่า แทนแถวของวัตถุทั้งชุด · ว่างหมด = แถวของวัตถุ) | ✔ |
| `sh_variant_4_object_biography` | `textarea` → string | — | `…text_override.info_rows[key=object_biography]` |  | detail แถวข้อมูล (มีช่องใดช่องหนึ่ง = แสดงเฉพาะแถวที่มีค่า แทนแถวของวัตถุทั้งชุด · ว่างหมด = แถวของวัตถุ) | ✔ |

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
| `sh_thumbnail` | `image` → integer | ✔ | ภาพการ์ด `1232:1714` (D45: ชิ้น 1·7·9 = ภาพที่ Figma วางใต้เส้น `3103:1037` · `3103:1187` · `3103:1220`) | `bbc1e161…` (ชิ้น 1 · D45) | timeline: วัตถุบนเส้น + mini-map (ขนาด 30/60 อิงภาพนี้) | ✔ |
| `sh_thumbnail_2` | `image` → integer | — (การ์ด 6 · D45: การ์ด 9 ว่างแล้ว) | ภาพที่ 2 ของการ์ด | Ivory Fire Piston `8dcebc79…` | timeline: บนเส้น + mini-map (กลุ่มกับ `sh_thumbnail`) | ✔ |
| `sh_below_image` | `image` → integer | — (D45: ชิ้น 1·7·9 มี · ที่เหลือว่าง · admin เพิ่ม/เปลี่ยน/ลบได้) | D45 (สลับ D39/D41): ภาพที่ Figma วางบนเส้น — ชิ้น 1 `1232:1714` · 7 `3103:1181` · 9 กลุ่ม `1232:1764` (ภาพแรก `1232:1765`) · seed `items[].below_ref` (ชื่อไฟล์ที่ราก `design/assets/`) | `4953122e….png` (ชิ้น 1) · ว่าง → ใช้ `sh_thumbnail` (+`_2`) | timeline: ภาพใหญ่ใต้เส้นเท่านั้น (`sh_below_image` (+`_2`) ?: `sh_thumbnail` (+`_2`) · ภาพก่อน `sh_line_art` ซ้อนใต้ภาพนี้) · ⛔ แถวบน/mini-map/หน้า detail ไม่ใช้ · ขนาดแถวบนอิงภาพบนเส้น (DESIGN-SPEC §4.5c.3d) | ✔ |
| `sh_below_image_2` | `image` → integer | — (D45: ชิ้น 9 มี · ที่เหลือว่าง) | ภาพที่ 2 ของกลุ่มใต้เส้น — ชิ้น 9 `1232:1766` (Group 33 เดิมของแถวบน) · seed `items[].below_ref_2` | `1c3aa3a1….png` (ชิ้น 9) | timeline: ภาพใต้เส้นแบบกลุ่ม 2 ภาพ (วางคู่กับ `sh_below_image` เหมือน `sh_thumbnail_2` คู่ `sh_thumbnail`) · **ใช้เมื่อ `sh_below_image` มีค่าเท่านั้น** (`sh_below_image` ว่าง = ใต้เส้นใช้ภาพบนเส้น · ช่องนี้ไม่ถูกใช้) · ⛔ แถวบน/mini-map/หน้า detail ไม่ใช้ | ✔ |
| `sh_line_art` | `image` → integer | — (D35/D36: ชิ้น 2–5 มี · 1, 6–15 ว่าง · admin เพิ่ม/เปลี่ยน/ลบได้) | Figma `3101:609` Section 51 คู่ "ก่อน ▶ หลัง" (`3103:1040` · `3103:1056` · `3246:3914` · `3160:1510`) · seed `items[].line_art_ref` (ชื่อไฟล์ใน `design/assets/lineart/`) | `02-singhasari-ganesha.png` · ว่าง → ภาพใต้เส้นเป็นภาพสีอย่างเดียว | timeline: "ภาพก่อน" ของภาพใหญ่ใต้เส้น (ขาเข้า α 0.40p → settle cross-fade เป็น `sh_thumbnail` · DESIGN-SPEC §4.5c.3b/§6.4c) · ⛔ แถวบน/mini-map ไม่ใช้ (D35) | ✔ |

## 4. `trail` (term) — group: trail
| ฟิลด์ (meta key) | ชนิด (ACF → meta) | ต้องมีค่า | ที่มาใน Figma (node) | ตัวอย่าง (seed `trails.json`) | ใช้ในหน้า | REST |
|---|---|---|---|---|---|---|
| (name / slug / description) | term | ✔ / ✔ / — | component set `670:9643` · คำอธิบาย `797:1881` | "Elephan" / `elephant` / "Elephants have lived alongside…" | trail · alt | ✔ |
| `sh_title_on_trail_page` | `text` → string | ✔ (17/17 ตั้งแต่ core-1117) | `797:1880` · 16 trail ใหม่ = H1 ใน `3377:4608` (seed `title_on_trail_page`) | "Elephant’s Trail" · "Bird Trail" | trail | ✔ |
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
| `sh_cover_image` | `image` → integer | ✔ (17/17 ตั้งแต่ core-1117) | `796:1799` · 16 trail ใหม่ = seed `cover_crop_ref` (ครอป 2240×980 แล้ว §8.1) | `ed0e5ee9…` 1120×497 · bird `….crop-3377-….png` | trail | ✔ |
| `sh_timeline_page` | `post_object` → integer (page) | — (ว่าง = ไม่มี timeline) | frame `670:1551` | elephant → หน้า Timeline | trail · timeline | ✔ |
| `sh_object_order` | `relationship` → array&lt;int&gt; | ✔ | ลำดับ tile `Ele 2…` §4.8 (seed `trail_order`) | elephant: Leg Bone, Singhasari Ganesha, Ganesha, … Bas-relief (15) | trail (ลาก-วางเรียงใน admin) | ✔ |
| `sh_trail_layout` | `textarea` → string (JSON object · ตรวจตอนบันทึก) | — (16 trail ใหม่ · elephant ว่าง) | seed `trails.json[].trail_page_layout` (§8.1 ตาราง) | bird `{"frame_size":[1440,1416],"cover":[160,150,1120,490],"title":[160,699.2,151,45],"description":[341,676,939,69],"description_font":{…},"title_color":"#212529","back_button":[160,1320,115,50],"grid":[160,829.2,1120,418]}` · `grid` = กล่องกริดช่อง (Frame 114472 · y จาก seed `tiles[].bbox` · Garuda/Fish 875.2 · P-340 core-1117b) | trail (geometry · กว้าง H1 = ตัดบรรทัด Seashell · y กริด) | ✔ |
| `sh_static_tile_1_position` | `number` → integer | — (makara 1 · rooster 11) | ช่องที่ไม่มีหน้า detail (§8.2: Makara ช่อง 1/3 · Rooster #11 `pending` hold_*) | makara 1 | trail grid (`sh_trail_tiles()` · ช่องแสดง ไม่ลิงก์) · 0 = ไม่แสดง | ✔ |
| `sh_static_tile_1_image` | `image` → integer | — | seed `trails.json tiles[]` ที่ `object_slug` = null / ชี้ record hold_* | makara `77d84c86…` | trail grid | ✔ |
| `sh_static_tile_1_image_hover` | `image` → integer | — | 〃 hover | makara `9a4ee322…` | trail grid hover | ✔ |
| `sh_static_tile_1_label` | `text` → string | — | `tiles[].title_note` | "Skull of Saltwater Crocodile" | trail grid (alt ของภาพ) | ✔ |
| `sh_static_tile_2_position` | `number` → integer | — | 〃 | makara 3 | trail grid | ✔ |
| `sh_static_tile_2_image` | `image` → integer | — | 〃 | | trail grid | ✔ |
| `sh_static_tile_2_image_hover` | `image` → integer | — | 〃 | | trail grid hover | ✔ |
| `sh_static_tile_2_label` | `text` → string | — | 〃 | "Sungai Mas Makara" | trail grid | ✔ |
| `sh_static_tile_3_position` | `number` → integer | — | เผื่อ 1 ช่อง (seed สูงสุด 2) | | trail grid | ✔ |
| `sh_static_tile_3_image` | `image` → integer | — | 〃 | | trail grid | ✔ |
| `sh_static_tile_3_image_hover` | `image` → integer | — | 〃 | | trail grid hover | ✔ |
| `sh_static_tile_3_label` | `text` → string | — | 〃 | | trail grid | ✔ |
| `sh_guide_name` | `text` → string | ✔ (17/17) | การ์ด `3339:5488` ชื่อ (เช่น `3339:5505`) · §9.3 | "The Chicken & Rooster" | home การ์ดสัตว์ (D48) | ✔ |
| `sh_guide_words` | `text` → string | ✔ | คำ 3 คำ (`3339:5506`) | "Renewal · Homeliness · Abundance" | home การ์ดสัตว์ | ✔ |
| `sh_guide_body` | `textarea` → string (inline HTML: `<strong>` `<em>` · `\n\n` ย่อหน้า · `\n` = `<br>`) | ✔ | ข้อความเต็ม (`3339:5510`) · seed `body_html` (ตัวเอียง dragon) | ไก่ 5 ย่อหน้า | home การ์ดสัตว์ (Read more) · อ่านด้วย `sh_rich_paragraphs()` | ✔ |
| `sh_guide_follow` | `text` → string | ✔ | `3339:5512` (D53 สะกด INVERTEBRATES) | "→ FOLLOW THE CHICKEN & ROOSTER TRAIL" | home (ลิงก์ → trail นี้) | ✔ |
| `sh_guide_card` | `image` → integer | ✔ | ภาพการ์ด 1469×2392 (`design/assets/pages-1117/guides/<slug>.png` · ย่อ 460×726) | rooster `f8162319…` | home (การ์ดเล็ก 120×190 + ใหญ่ 230×363) · ว่าง = สัตว์นี้ไม่อยู่ในการสุ่ม | ✔ |

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

## 5b. `zone` (term) — กริดวัตถุ Zone 1–3 (D57 · fb-1117 · BACKLOG #39) — group: zone_grid
หน้า Zone 1–3 แสดง**กริดวัตถุ** (ช่อง = วัตถุ ไม่ใช่ trail) — Figma `Frame zone1/2/3` `3377:4473` · `3454:5695` (zone 2 = frame ที่นักออกแบบแก้แล้ว `3117:3713` · 42 ช่อง) · `3454:5354` · 15 / 42 / 83 ช่อง · seed `content/seed/zones-2026-11-17.json` (`zones[].tiles[]` = `order` · `object_slug` · `tile_ref` · `hover_ref`) · **โครงข้อมูล = ช่องเลขลำดับต่อ term** (แบบเดียวกับ `sh_page_card_<n>_*` / `sh_static_tile_<n>_*` · ACF ฟรีไม่มี repeater): ช่อง `n` = ช่องที่ n ของกริด (1 = ซ้ายบน · แถวละ 5 · เรียงแถว→คอลัมน์) · **90 ช่องต่อ zone** (`SH_ZONE_GRID_SLOTS` · seed สูงสุด 83 · เผื่อเพิ่ม) · แท็บละ 10 ช่องในหน้าแก้ term
- `sh_zone_tile_<n>_object` = วัตถุ (post type `object`) · **publish เท่านั้นที่ลิงก์** ไปหน้า detail · ว่าง/ไม่ publish = **ภาพไม่ลิงก์** (ต้องมี `_image` · ไม่มีภาพ = ไม่แสดงช่อง) · ช่องที่ว่างทั้ง object และ image = ข้าม (ช่องถัดไปเลื่อนขึ้น)
- `sh_zone_tile_<n>_image` / `_image_hover` = ภาพช่อง 184² / hover 210² (attachment เดียวกับภาพช่องของวัตถุใน trail นั้น — importer ใช้ attachment เดิมถ้ามี `_sh_import_key` ตรง ไม่อัปโหลดซ้ำ) · **ว่าง = ภาพช่อง/hover ของวัตถุเอง** (`sh_object_trail_tile( $id, 0 )` = `sh_tile_image` / `sh_tile_image_hover`)
- ภาพช่อง = เวอร์ชันของ trail ที่ Figma ใช้ในกริด zone (วัตถุหลาย trail มีภาพคนละพื้นสี → ช่องซ้ำของวัตถุเดียวกันได้คนละภาพ เช่น `pending-3` เขียว/ชมพู) → **เก็บภาพต่อช่อง** ไม่ดึงจากวัตถุเสมอ
- `sh_zone_tile_<n>_trail` = trail ที่ช่องนี้เป็นของ (multitrail-1117 · D59 · วัตถุหลาย trail มีช่องซ้ำคนละ trail เช่น zone 2 ช่อง 6 dragon / ช่อง 7 naga) → ลิงก์ช่องส่ง `?sh_trail=<slug>` (§2b) ให้หน้า detail เปิดตาม trail นั้น · ว่าง = trail หลักของวัตถุ · ตั้งโดย refresh step `2026-11-17-multitrail` เฉพาะเมื่อว่างและช่องยังถือวัตถุของ seed
- ฟิลด์กริด trail เดิม (`sh_show_in_zone_grid` · `sh_zone_grid_order` · `sh_tile_zone_grid` · `sh_tile_zone_grid_hover` ในกลุ่ม trail §4) **ไม่ลบ** แต่หน้า zone 1–3 ไม่ใช้แล้ว
- เติมโดย importer fresh + refresh step `2026-11-17-zone-grid` (§12) · API `sh_zone_grid( $zone )` (§11)
| ฟิลด์ (meta key) | ชนิด (ACF → meta) | ต้องมีค่า | ที่มาใน Figma (node) | ตัวอย่าง (seed `zones-2026-11-17.json`) | ใช้ในหน้า | REST |
|---|---|---|---|---|---|---|
| `sh_zone_tile_1_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | `Frame zone1/2/3` instance ช่อง 1 · `tiles[].object_slug` | zone 1 ช่อง 1: `green-peafowl` | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_1_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) | `4c493fdc…` (Bird 2 · 184²) | zone 1–3 กริด | ✔ |
| `sh_zone_tile_1_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) | `f8cdca6e…` (210²) | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_1_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) | zone 2 ช่อง 6: dragon · ช่อง 7: naga (Holy water vessel) | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_2_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_2_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_2_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_2_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_3_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_3_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_3_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_3_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_4_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_4_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_4_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_4_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_5_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_5_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_5_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_5_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_6_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_6_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_6_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_6_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_7_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_7_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_7_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_7_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_8_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_8_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_8_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_8_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_9_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_9_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_9_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_9_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_10_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_10_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_10_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_10_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_11_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_11_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_11_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_11_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_12_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_12_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_12_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_12_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_13_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_13_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_13_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_13_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_14_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_14_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_14_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_14_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_15_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_15_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_15_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_15_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_16_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_16_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_16_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_16_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_17_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_17_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_17_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_17_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_18_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_18_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_18_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_18_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_19_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_19_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_19_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_19_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_20_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_20_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_20_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_20_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_21_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_21_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_21_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_21_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_22_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_22_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_22_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_22_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_23_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_23_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_23_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_23_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_24_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_24_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_24_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_24_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_25_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_25_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_25_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_25_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_26_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_26_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_26_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_26_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_27_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_27_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_27_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_27_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_28_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_28_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_28_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_28_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_29_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_29_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_29_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_29_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_30_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_30_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_30_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_30_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_31_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_31_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_31_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_31_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_32_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_32_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_32_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_32_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_33_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_33_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_33_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_33_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_34_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_34_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_34_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_34_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_35_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_35_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_35_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_35_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_36_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_36_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_36_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_36_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_37_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_37_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_37_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_37_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_38_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_38_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_38_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_38_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_39_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_39_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_39_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_39_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_40_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_40_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_40_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_40_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_41_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_41_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_41_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_41_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_42_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_42_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_42_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_42_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_43_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_43_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_43_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_43_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_44_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_44_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_44_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_44_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_45_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_45_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_45_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_45_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_46_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_46_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_46_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_46_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_47_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_47_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_47_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_47_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_48_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_48_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_48_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_48_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_49_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_49_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_49_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_49_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_50_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_50_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_50_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_50_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_51_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_51_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_51_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_51_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_52_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_52_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_52_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_52_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_53_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_53_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_53_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_53_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_54_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_54_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_54_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_54_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_55_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_55_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_55_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_55_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_56_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_56_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_56_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_56_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_57_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_57_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_57_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_57_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_58_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_58_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_58_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_58_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_59_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_59_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_59_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_59_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_60_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_60_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_60_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_60_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_61_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_61_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_61_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_61_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_62_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_62_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_62_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_62_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_63_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_63_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_63_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_63_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_64_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_64_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_64_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_64_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_65_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_65_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_65_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_65_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_66_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_66_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_66_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_66_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_67_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_67_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_67_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_67_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_68_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_68_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_68_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_68_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_69_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_69_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_69_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_69_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_70_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_70_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_70_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_70_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_71_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_71_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_71_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_71_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_72_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_72_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_72_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_72_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_73_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_73_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_73_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_73_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_74_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_74_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_74_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_74_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_75_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_75_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_75_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_75_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_76_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_76_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_76_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_76_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_77_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_77_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_77_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_77_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_78_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_78_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_78_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_78_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_79_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_79_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_79_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_79_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_80_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_80_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_80_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_80_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_81_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_81_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_81_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_81_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_82_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_82_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_82_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_82_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_83_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_83_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_83_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_83_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_84_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_84_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_84_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_84_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_85_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_85_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_85_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_85_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_86_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_86_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_86_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_86_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_87_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_87_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_87_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_87_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_88_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_88_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_88_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_88_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_89_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_89_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_89_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_89_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |
| `sh_zone_tile_90_object` | `post_object` → integer | — (zone 1: 15 · zone 2: 42 · zone 3: 83) | 〃 · `tiles[].object_slug` |  | zone 1–3 กริด (ลิงก์) | ✔ |
| `sh_zone_tile_90_image` | `image` → integer | — | 〃 `tiles[].tile_ref` (instance default 184²) |  | zone 1–3 กริด | ✔ |
| `sh_zone_tile_90_image_hover` | `image` → integer | — | 〃 `tiles[].hover_ref` (variant hover 210² · ON_HOVER 0.3s) |  | zone 1–3 กริด hover | ✔ |
| `sh_zone_tile_90_trail` | `taxonomy` → integer | — | `tiles[].trail` (จับคู่จากภาพช่อง/hover ↔ `objects.json tiles_by_trail` · D59) |  | zone 1–3 กริด: ลิงก์ช่องนี้ใส่ `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (§2b) | ✔ |

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
| `sh_home_guide_title` | `text` → string | ✔ | `3339:5267` (§9.3) | "Your animal guide is..." | home การ์ดสัตว์ (D48) | ✔ |
| `sh_home_guide_opening` | `textarea` → string | ✔ | `3339:5270` | "An animal has crossed your path... Can you figure out where it went?" (seed ฟิลด์ปกติ · ไม่ใช่ `*_raw`) | home | ✔ |
| `sh_home_guide_heritage` | `textarea` → string | ✔ | `3339:5271` | "For centuries, animals have inspired artists…" | home | ✔ |
| `sh_home_guide_question` | `textarea` → string | ✔ | `3339:5272` | "Which animal will guide you?" | home | ✔ |
| `sh_home_guide_instructions` | `textarea` → string | ✔ | `3339:5273` | "Pick a card to meet your animal guide…" | home | ✔ |
| `sh_home_guide_cta` | `text` → string | ✔ | `3339:5274` | "CHOOSE A CARD" | home (ปุ่มสุ่ม) | ✔ |
| `sh_home_guide_card_back` | `image` → integer | ✔ | การ์ด "?" `3339:5275` (`c632e52f` ครอป .016/.017/.968/.971 ตอน import · 240×380) | `_card-back.png` | home (การ์ดเริ่ม) | ✔ |
Zone cards 1–5 บนหน้าแรก = term `zone` (§5) · ไอคอน trail 17 = `sh_trails('home')` · navigator 4 จุด = พฤติกรรมหน้า (ไม่ใช่ข้อมูล) · **การ์ดสัตว์ 17 ใบ = `sh_guide_cards()`** (trail ที่มี `sh_guide_card` · §4) · ป้าย Read more/Hide = `sh_label_read_more`/`sh_label_hide` (§9)

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
| `sh_intro_head_emblem` | `image` → integer | ✔ | `3339:6123` (§9.4 B) · manifest still `intro-1117/1.2 Early Centres of Power/Intro logo.png` | `intro-logo….webp` 2617×2486 | intro ใหม่ (D50) | ✔ |
| `sh_intro_head_title` | `textarea` → string | ✔ | `3339:6124` (`\n` = ขึ้นบรรทัดตามต้นฉบับ) | "Across Southeast Asia, creatures travel\nwhere borders cannot…" | intro ใหม่ | ✔ |
| `sh_intro_head_paragraph_1` | `textarea` → string | ✔ | `3339:6126` | seed `header.paragraphs[0]` | intro ใหม่ (`sh_intro_head()`) | ✔ |
| `sh_intro_head_paragraph_2` | `textarea` → string | ✔ | `3339:6127` | seed `header.paragraphs[1]` | intro ใหม่ (`sh_intro_head()`) | ✔ |
| `sh_intro_head_paragraph_3` | `textarea` → string | ✔ | `3339:6128` | seed `header.paragraphs[2]` | intro ใหม่ (`sh_intro_head()`) | ✔ |
| `sh_intro_head_paragraph_4` | `textarea` → string | ✔ | `3339:6129` | seed `header.paragraphs[3]` | intro ใหม่ (`sh_intro_head()`) | ✔ |
| `sh_intro_head_paragraph_5` | `textarea` → string | — | เผื่อ 1 (seed 4) | ว่าง | intro ใหม่ (`sh_intro_head()`) | ✔ |
| `sh_intro_head_credit` | `textarea` → string | ✔ | `3339:6130` | "Presented by the UNESCO Regional Office in Bangkok…" | intro ใหม่ | ✔ |
| `sh_intro_env_heading` | `textarea` → string | ✔ | `3339:6135` | "Southeast Asians and the Environment:\nRelationships Between People, Places, and Landscapes" | intro ใหม่ (หัวข้อเหนือแผนที่ · ทุกตอน) | ✔ |
| `sh_intro_head_layout` | `textarea` → string (JSON object) | — | seed `header` (rel/box/font/crop · `decor[*].image_ref` → attachment id) + `environment_heading.font` + `map_ui` + `url` | `{"header":{"bg":"#DD7F6A",…},"environment_heading":{…},"map_ui":{…}}` | intro ใหม่ (geometry/สี · ไม่ใช่เนื้อหา) | ✔ |
| `sh_intro_ch1_title` | `text` → string | ✔ | seed `chapters[0].title` | "People and Places" | intro ปุ่ม/ตอน 1 | ✔ |
| `sh_intro_ch1_accent` | `color_picker` → string | ✔ | `chapters[0].accent_color` | `#D85A40` | intro ตอน 1 | ✔ |
| `sh_intro_ch1_map_image` | `image` → integer | ✔ | แผนที่ตอน 1 (manifest `intro-1117/<map.drive_file>`: still → ภาพ · animation → poster) | | intro ตอน 1 | ✔ |
| `sh_intro_ch1_map_animation` | `image` → integer (WebP เคลื่อนไหว) | — (ตอน 1) | manifest `kind: animation` `out` | `map1-1-people-place.webp` | intro ตอน 1 (`<img>` แทนภาพนิ่ง) | ✔ |
| `sh_intro_ch1_text_1` | `textarea` → string (inline HTML: `<strong>` `<em>`) | — | ช่องข้อความที่ 1 ของตอน 1 (block `kind:text` ตามลำดับใน `sections[].blocks[]` · seed `html` → `\n\n`/`\n`) · 7 ช่อง = seed สูงสุด 6 + 1 | | intro ตอน 1 (`sh_intro_chapters()` · ตำแหน่ง = `layout` slot 1) | ✔ |
| `sh_intro_ch1_text_2` | `textarea` → string (inline HTML: `<strong>` `<em>`) | — | ช่องข้อความที่ 2 ของตอน 1 (block `kind:text` ตามลำดับใน `sections[].blocks[]` · seed `html` → `\n\n`/`\n`) · 7 ช่อง = seed สูงสุด 6 + 1 | | intro ตอน 1 (`sh_intro_chapters()` · ตำแหน่ง = `layout` slot 2) | ✔ |
| `sh_intro_ch1_text_3` | `textarea` → string (inline HTML: `<strong>` `<em>`) | — | ช่องข้อความที่ 3 ของตอน 1 (block `kind:text` ตามลำดับใน `sections[].blocks[]` · seed `html` → `\n\n`/`\n`) · 7 ช่อง = seed สูงสุด 6 + 1 | | intro ตอน 1 (`sh_intro_chapters()` · ตำแหน่ง = `layout` slot 3) | ✔ |
| `sh_intro_ch1_text_4` | `textarea` → string (inline HTML: `<strong>` `<em>`) | — | ช่องข้อความที่ 4 ของตอน 1 (block `kind:text` ตามลำดับใน `sections[].blocks[]` · seed `html` → `\n\n`/`\n`) · 7 ช่อง = seed สูงสุด 6 + 1 | | intro ตอน 1 (`sh_intro_chapters()` · ตำแหน่ง = `layout` slot 4) | ✔ |
| `sh_intro_ch1_text_5` | `textarea` → string (inline HTML: `<strong>` `<em>`) | — | ช่องข้อความที่ 5 ของตอน 1 (block `kind:text` ตามลำดับใน `sections[].blocks[]` · seed `html` → `\n\n`/`\n`) · 7 ช่อง = seed สูงสุด 6 + 1 | | intro ตอน 1 (`sh_intro_chapters()` · ตำแหน่ง = `layout` slot 5) | ✔ |
| `sh_intro_ch1_text_6` | `textarea` → string (inline HTML: `<strong>` `<em>`) | — | ช่องข้อความที่ 6 ของตอน 1 (block `kind:text` ตามลำดับใน `sections[].blocks[]` · seed `html` → `\n\n`/`\n`) · 7 ช่อง = seed สูงสุด 6 + 1 | | intro ตอน 1 (`sh_intro_chapters()` · ตำแหน่ง = `layout` slot 6) | ✔ |
| `sh_intro_ch1_text_7` | `textarea` → string (inline HTML: `<strong>` `<em>`) | — | ช่องข้อความที่ 7 ของตอน 1 (block `kind:text` ตามลำดับใน `sections[].blocks[]` · seed `html` → `\n\n`/`\n`) · 7 ช่อง = seed สูงสุด 6 + 1 | | intro ตอน 1 (`sh_intro_chapters()` · ตำแหน่ง = `layout` slot 7) | ✔ |
| `sh_intro_ch1_image_1` | `image` → integer | — | ช่องภาพที่ 1 (block `kind:image` ไม่นับ decor · manifest `intro-1117/<drive_file>` still · animation → poster · ไม่มีใน manifest = `figma_fill_file` ย่อ 2×) · 5 ช่อง = seed สูงสุด 4 + 1 | | intro ตอน 1 | ✔ |
| `sh_intro_ch1_image_1_animation` | `image` → integer (WebP เคลื่อนไหว) | — | manifest `kind: animation` `out` ของช่องภาพ 1 | | intro ตอน 1 (`<img>` แทนภาพนิ่ง) | ✔ |
| `sh_intro_ch1_image_2` | `image` → integer | — | ช่องภาพที่ 2 (block `kind:image` ไม่นับ decor · manifest `intro-1117/<drive_file>` still · animation → poster · ไม่มีใน manifest = `figma_fill_file` ย่อ 2×) · 5 ช่อง = seed สูงสุด 4 + 1 | | intro ตอน 1 | ✔ |
| `sh_intro_ch1_image_2_animation` | `image` → integer (WebP เคลื่อนไหว) | — | manifest `kind: animation` `out` ของช่องภาพ 2 | | intro ตอน 1 (`<img>` แทนภาพนิ่ง) | ✔ |
| `sh_intro_ch1_image_3` | `image` → integer | — | ช่องภาพที่ 3 (block `kind:image` ไม่นับ decor · manifest `intro-1117/<drive_file>` still · animation → poster · ไม่มีใน manifest = `figma_fill_file` ย่อ 2×) · 5 ช่อง = seed สูงสุด 4 + 1 | | intro ตอน 1 | ✔ |
| `sh_intro_ch1_image_3_animation` | `image` → integer (WebP เคลื่อนไหว) | — | manifest `kind: animation` `out` ของช่องภาพ 3 | | intro ตอน 1 (`<img>` แทนภาพนิ่ง) | ✔ |
| `sh_intro_ch1_image_4` | `image` → integer | — | ช่องภาพที่ 4 (block `kind:image` ไม่นับ decor · manifest `intro-1117/<drive_file>` still · animation → poster · ไม่มีใน manifest = `figma_fill_file` ย่อ 2×) · 5 ช่อง = seed สูงสุด 4 + 1 | | intro ตอน 1 | ✔ |
| `sh_intro_ch1_image_4_animation` | `image` → integer (WebP เคลื่อนไหว) | — | manifest `kind: animation` `out` ของช่องภาพ 4 | | intro ตอน 1 (`<img>` แทนภาพนิ่ง) | ✔ |
| `sh_intro_ch1_image_5` | `image` → integer | — | ช่องภาพที่ 5 (block `kind:image` ไม่นับ decor · manifest `intro-1117/<drive_file>` still · animation → poster · ไม่มีใน manifest = `figma_fill_file` ย่อ 2×) · 5 ช่อง = seed สูงสุด 4 + 1 | | intro ตอน 1 | ✔ |
| `sh_intro_ch1_image_5_animation` | `image` → integer (WebP เคลื่อนไหว) | — | manifest `kind: animation` `out` ของช่องภาพ 5 | | intro ตอน 1 (`<img>` แทนภาพนิ่ง) | ✔ |
| `sh_intro_ch1_layout` | `textarea` → string (JSON object) | ✔ | seed `chapters[0]` ยกเว้นข้อความ/ไฟล์: `frame_h map{rel,box,crop,fill_mode} map_buttons arrows sections[{name,bg,rel,blocks[]}]` · block = `{kind:"text",slot,role,font,box,rel}` · `{kind:"image",slot,box,rel,crop,fill_mode}` · `{kind:"decor",image_ref(→id),box,rel}` | | intro ตอน 1 (geometry) | ✔ |
| `sh_intro_ch2_title` | `text` → string | ✔ | seed `chapters[1].title` | "Early Centres of Power" | intro ปุ่ม/ตอน 2 | ✔ |
| `sh_intro_ch2_accent` | `color_picker` → string | ✔ | `chapters[1].accent_color` | `#43695D` | intro ตอน 2 | ✔ |
| `sh_intro_ch2_map_image` | `image` → integer | ✔ | แผนที่ตอน 2 (manifest `intro-1117/<map.drive_file>`: still → ภาพ · animation → poster) | | intro ตอน 2 | ✔ |
| `sh_intro_ch2_map_animation` | `image` → integer (WebP เคลื่อนไหว) | —  | manifest `kind: animation` `out` | ว่าง | intro ตอน 2 (`<img>` แทนภาพนิ่ง) | ✔ |
| `sh_intro_ch2_text_1` | `textarea` → string (inline HTML: `<strong>` `<em>`) | — | ช่องข้อความที่ 1 ของตอน 2 (block `kind:text` ตามลำดับใน `sections[].blocks[]` · seed `html` → `\n\n`/`\n`) · 7 ช่อง = seed สูงสุด 6 + 1 | | intro ตอน 2 (`sh_intro_chapters()` · ตำแหน่ง = `layout` slot 1) | ✔ |
| `sh_intro_ch2_text_2` | `textarea` → string (inline HTML: `<strong>` `<em>`) | — | ช่องข้อความที่ 2 ของตอน 2 (block `kind:text` ตามลำดับใน `sections[].blocks[]` · seed `html` → `\n\n`/`\n`) · 7 ช่อง = seed สูงสุด 6 + 1 | | intro ตอน 2 (`sh_intro_chapters()` · ตำแหน่ง = `layout` slot 2) | ✔ |
| `sh_intro_ch2_text_3` | `textarea` → string (inline HTML: `<strong>` `<em>`) | — | ช่องข้อความที่ 3 ของตอน 2 (block `kind:text` ตามลำดับใน `sections[].blocks[]` · seed `html` → `\n\n`/`\n`) · 7 ช่อง = seed สูงสุด 6 + 1 | | intro ตอน 2 (`sh_intro_chapters()` · ตำแหน่ง = `layout` slot 3) | ✔ |
| `sh_intro_ch2_text_4` | `textarea` → string (inline HTML: `<strong>` `<em>`) | — | ช่องข้อความที่ 4 ของตอน 2 (block `kind:text` ตามลำดับใน `sections[].blocks[]` · seed `html` → `\n\n`/`\n`) · 7 ช่อง = seed สูงสุด 6 + 1 | | intro ตอน 2 (`sh_intro_chapters()` · ตำแหน่ง = `layout` slot 4) | ✔ |
| `sh_intro_ch2_text_5` | `textarea` → string (inline HTML: `<strong>` `<em>`) | — | ช่องข้อความที่ 5 ของตอน 2 (block `kind:text` ตามลำดับใน `sections[].blocks[]` · seed `html` → `\n\n`/`\n`) · 7 ช่อง = seed สูงสุด 6 + 1 | | intro ตอน 2 (`sh_intro_chapters()` · ตำแหน่ง = `layout` slot 5) | ✔ |
| `sh_intro_ch2_text_6` | `textarea` → string (inline HTML: `<strong>` `<em>`) | — | ช่องข้อความที่ 6 ของตอน 2 (block `kind:text` ตามลำดับใน `sections[].blocks[]` · seed `html` → `\n\n`/`\n`) · 7 ช่อง = seed สูงสุด 6 + 1 | | intro ตอน 2 (`sh_intro_chapters()` · ตำแหน่ง = `layout` slot 6) | ✔ |
| `sh_intro_ch2_text_7` | `textarea` → string (inline HTML: `<strong>` `<em>`) | — | ช่องข้อความที่ 7 ของตอน 2 (block `kind:text` ตามลำดับใน `sections[].blocks[]` · seed `html` → `\n\n`/`\n`) · 7 ช่อง = seed สูงสุด 6 + 1 | | intro ตอน 2 (`sh_intro_chapters()` · ตำแหน่ง = `layout` slot 7) | ✔ |
| `sh_intro_ch2_image_1` | `image` → integer | — | ช่องภาพที่ 1 (block `kind:image` ไม่นับ decor · manifest `intro-1117/<drive_file>` still · animation → poster · ไม่มีใน manifest = `figma_fill_file` ย่อ 2×) · 5 ช่อง = seed สูงสุด 4 + 1 | | intro ตอน 2 | ✔ |
| `sh_intro_ch2_image_1_animation` | `image` → integer (WebP เคลื่อนไหว) | — | manifest `kind: animation` `out` ของช่องภาพ 1 | | intro ตอน 2 (`<img>` แทนภาพนิ่ง) | ✔ |
| `sh_intro_ch2_image_2` | `image` → integer | — | ช่องภาพที่ 2 (block `kind:image` ไม่นับ decor · manifest `intro-1117/<drive_file>` still · animation → poster · ไม่มีใน manifest = `figma_fill_file` ย่อ 2×) · 5 ช่อง = seed สูงสุด 4 + 1 | | intro ตอน 2 | ✔ |
| `sh_intro_ch2_image_2_animation` | `image` → integer (WebP เคลื่อนไหว) | — | manifest `kind: animation` `out` ของช่องภาพ 2 | | intro ตอน 2 (`<img>` แทนภาพนิ่ง) | ✔ |
| `sh_intro_ch2_image_3` | `image` → integer | — | ช่องภาพที่ 3 (block `kind:image` ไม่นับ decor · manifest `intro-1117/<drive_file>` still · animation → poster · ไม่มีใน manifest = `figma_fill_file` ย่อ 2×) · 5 ช่อง = seed สูงสุด 4 + 1 | | intro ตอน 2 | ✔ |
| `sh_intro_ch2_image_3_animation` | `image` → integer (WebP เคลื่อนไหว) | — | manifest `kind: animation` `out` ของช่องภาพ 3 | | intro ตอน 2 (`<img>` แทนภาพนิ่ง) | ✔ |
| `sh_intro_ch2_image_4` | `image` → integer | — | ช่องภาพที่ 4 (block `kind:image` ไม่นับ decor · manifest `intro-1117/<drive_file>` still · animation → poster · ไม่มีใน manifest = `figma_fill_file` ย่อ 2×) · 5 ช่อง = seed สูงสุด 4 + 1 | | intro ตอน 2 | ✔ |
| `sh_intro_ch2_image_4_animation` | `image` → integer (WebP เคลื่อนไหว) | — | manifest `kind: animation` `out` ของช่องภาพ 4 | | intro ตอน 2 (`<img>` แทนภาพนิ่ง) | ✔ |
| `sh_intro_ch2_image_5` | `image` → integer | — | ช่องภาพที่ 5 (block `kind:image` ไม่นับ decor · manifest `intro-1117/<drive_file>` still · animation → poster · ไม่มีใน manifest = `figma_fill_file` ย่อ 2×) · 5 ช่อง = seed สูงสุด 4 + 1 | | intro ตอน 2 | ✔ |
| `sh_intro_ch2_image_5_animation` | `image` → integer (WebP เคลื่อนไหว) | — | manifest `kind: animation` `out` ของช่องภาพ 5 | | intro ตอน 2 (`<img>` แทนภาพนิ่ง) | ✔ |
| `sh_intro_ch2_layout` | `textarea` → string (JSON object) | ✔ | seed `chapters[1]` ยกเว้นข้อความ/ไฟล์: `frame_h map{rel,box,crop,fill_mode} map_buttons arrows sections[{name,bg,rel,blocks[]}]` · block = `{kind:"text",slot,role,font,box,rel}` · `{kind:"image",slot,box,rel,crop,fill_mode}` · `{kind:"decor",image_ref(→id),box,rel}` | | intro ตอน 2 (geometry) | ✔ |
| `sh_intro_ch3_title` | `text` → string | ✔ | seed `chapters[2].title` | "Rice and Spice: Platforms of Interconnection" | intro ปุ่ม/ตอน 3 | ✔ |
| `sh_intro_ch3_accent` | `color_picker` → string | ✔ | `chapters[2].accent_color` | `#887A61` | intro ตอน 3 | ✔ |
| `sh_intro_ch3_map_image` | `image` → integer | ✔ | แผนที่ตอน 3 (manifest `intro-1117/<map.drive_file>`: still → ภาพ · animation → poster) | | intro ตอน 3 | ✔ |
| `sh_intro_ch3_map_animation` | `image` → integer (WebP เคลื่อนไหว) | —  | manifest `kind: animation` `out` | ว่าง | intro ตอน 3 (`<img>` แทนภาพนิ่ง) | ✔ |
| `sh_intro_ch3_text_1` | `textarea` → string (inline HTML: `<strong>` `<em>`) | — | ช่องข้อความที่ 1 ของตอน 3 (block `kind:text` ตามลำดับใน `sections[].blocks[]` · seed `html` → `\n\n`/`\n`) · 7 ช่อง = seed สูงสุด 6 + 1 | | intro ตอน 3 (`sh_intro_chapters()` · ตำแหน่ง = `layout` slot 1) | ✔ |
| `sh_intro_ch3_text_2` | `textarea` → string (inline HTML: `<strong>` `<em>`) | — | ช่องข้อความที่ 2 ของตอน 3 (block `kind:text` ตามลำดับใน `sections[].blocks[]` · seed `html` → `\n\n`/`\n`) · 7 ช่อง = seed สูงสุด 6 + 1 | | intro ตอน 3 (`sh_intro_chapters()` · ตำแหน่ง = `layout` slot 2) | ✔ |
| `sh_intro_ch3_text_3` | `textarea` → string (inline HTML: `<strong>` `<em>`) | — | ช่องข้อความที่ 3 ของตอน 3 (block `kind:text` ตามลำดับใน `sections[].blocks[]` · seed `html` → `\n\n`/`\n`) · 7 ช่อง = seed สูงสุด 6 + 1 | | intro ตอน 3 (`sh_intro_chapters()` · ตำแหน่ง = `layout` slot 3) | ✔ |
| `sh_intro_ch3_text_4` | `textarea` → string (inline HTML: `<strong>` `<em>`) | — | ช่องข้อความที่ 4 ของตอน 3 (block `kind:text` ตามลำดับใน `sections[].blocks[]` · seed `html` → `\n\n`/`\n`) · 7 ช่อง = seed สูงสุด 6 + 1 | | intro ตอน 3 (`sh_intro_chapters()` · ตำแหน่ง = `layout` slot 4) | ✔ |
| `sh_intro_ch3_text_5` | `textarea` → string (inline HTML: `<strong>` `<em>`) | — | ช่องข้อความที่ 5 ของตอน 3 (block `kind:text` ตามลำดับใน `sections[].blocks[]` · seed `html` → `\n\n`/`\n`) · 7 ช่อง = seed สูงสุด 6 + 1 | | intro ตอน 3 (`sh_intro_chapters()` · ตำแหน่ง = `layout` slot 5) | ✔ |
| `sh_intro_ch3_text_6` | `textarea` → string (inline HTML: `<strong>` `<em>`) | — | ช่องข้อความที่ 6 ของตอน 3 (block `kind:text` ตามลำดับใน `sections[].blocks[]` · seed `html` → `\n\n`/`\n`) · 7 ช่อง = seed สูงสุด 6 + 1 | | intro ตอน 3 (`sh_intro_chapters()` · ตำแหน่ง = `layout` slot 6) | ✔ |
| `sh_intro_ch3_text_7` | `textarea` → string (inline HTML: `<strong>` `<em>`) | — | ช่องข้อความที่ 7 ของตอน 3 (block `kind:text` ตามลำดับใน `sections[].blocks[]` · seed `html` → `\n\n`/`\n`) · 7 ช่อง = seed สูงสุด 6 + 1 | | intro ตอน 3 (`sh_intro_chapters()` · ตำแหน่ง = `layout` slot 7) | ✔ |
| `sh_intro_ch3_image_1` | `image` → integer | — | ช่องภาพที่ 1 (block `kind:image` ไม่นับ decor · manifest `intro-1117/<drive_file>` still · animation → poster · ไม่มีใน manifest = `figma_fill_file` ย่อ 2×) · 5 ช่อง = seed สูงสุด 4 + 1 | | intro ตอน 3 | ✔ |
| `sh_intro_ch3_image_1_animation` | `image` → integer (WebP เคลื่อนไหว) | — | manifest `kind: animation` `out` ของช่องภาพ 1 | | intro ตอน 3 (`<img>` แทนภาพนิ่ง) | ✔ |
| `sh_intro_ch3_image_2` | `image` → integer | — | ช่องภาพที่ 2 (block `kind:image` ไม่นับ decor · manifest `intro-1117/<drive_file>` still · animation → poster · ไม่มีใน manifest = `figma_fill_file` ย่อ 2×) · 5 ช่อง = seed สูงสุด 4 + 1 | | intro ตอน 3 | ✔ |
| `sh_intro_ch3_image_2_animation` | `image` → integer (WebP เคลื่อนไหว) | — | manifest `kind: animation` `out` ของช่องภาพ 2 | | intro ตอน 3 (`<img>` แทนภาพนิ่ง) | ✔ |
| `sh_intro_ch3_image_3` | `image` → integer | — | ช่องภาพที่ 3 (block `kind:image` ไม่นับ decor · manifest `intro-1117/<drive_file>` still · animation → poster · ไม่มีใน manifest = `figma_fill_file` ย่อ 2×) · 5 ช่อง = seed สูงสุด 4 + 1 | | intro ตอน 3 | ✔ |
| `sh_intro_ch3_image_3_animation` | `image` → integer (WebP เคลื่อนไหว) | — | manifest `kind: animation` `out` ของช่องภาพ 3 | | intro ตอน 3 (`<img>` แทนภาพนิ่ง) | ✔ |
| `sh_intro_ch3_image_4` | `image` → integer | — | ช่องภาพที่ 4 (block `kind:image` ไม่นับ decor · manifest `intro-1117/<drive_file>` still · animation → poster · ไม่มีใน manifest = `figma_fill_file` ย่อ 2×) · 5 ช่อง = seed สูงสุด 4 + 1 | | intro ตอน 3 | ✔ |
| `sh_intro_ch3_image_4_animation` | `image` → integer (WebP เคลื่อนไหว) | — | manifest `kind: animation` `out` ของช่องภาพ 4 | | intro ตอน 3 (`<img>` แทนภาพนิ่ง) | ✔ |
| `sh_intro_ch3_image_5` | `image` → integer | — | ช่องภาพที่ 5 (block `kind:image` ไม่นับ decor · manifest `intro-1117/<drive_file>` still · animation → poster · ไม่มีใน manifest = `figma_fill_file` ย่อ 2×) · 5 ช่อง = seed สูงสุด 4 + 1 | | intro ตอน 3 | ✔ |
| `sh_intro_ch3_image_5_animation` | `image` → integer (WebP เคลื่อนไหว) | — | manifest `kind: animation` `out` ของช่องภาพ 5 | | intro ตอน 3 (`<img>` แทนภาพนิ่ง) | ✔ |
| `sh_intro_ch3_layout` | `textarea` → string (JSON object) | ✔ | seed `chapters[2]` ยกเว้นข้อความ/ไฟล์: `frame_h map{rel,box,crop,fill_mode} map_buttons arrows sections[{name,bg,rel,blocks[]}]` · block = `{kind:"text",slot,role,font,box,rel}` · `{kind:"image",slot,box,rel,crop,fill_mode}` · `{kind:"decor",image_ref(→id),box,rel}` | | intro ตอน 3 (geometry) | ✔ |
| `sh_intro_ch4_title` | `text` → string | ✔ | seed `chapters[3].title` | "Animals, Folklore, and Ecological Stewardship" | intro ปุ่ม/ตอน 4 | ✔ |
| `sh_intro_ch4_accent` | `color_picker` → string | ✔ | `chapters[3].accent_color` | `#3F4036` | intro ตอน 4 | ✔ |
| `sh_intro_ch4_map_image` | `image` → integer | ✔ | แผนที่ตอน 4 (manifest `intro-1117/<map.drive_file>`: still → ภาพ · animation → poster) | | intro ตอน 4 | ✔ |
| `sh_intro_ch4_map_animation` | `image` → integer (WebP เคลื่อนไหว) | —  | manifest `kind: animation` `out` | ว่าง | intro ตอน 4 (`<img>` แทนภาพนิ่ง) | ✔ |
| `sh_intro_ch4_text_1` | `textarea` → string (inline HTML: `<strong>` `<em>`) | — | ช่องข้อความที่ 1 ของตอน 4 (block `kind:text` ตามลำดับใน `sections[].blocks[]` · seed `html` → `\n\n`/`\n`) · 7 ช่อง = seed สูงสุด 6 + 1 | | intro ตอน 4 (`sh_intro_chapters()` · ตำแหน่ง = `layout` slot 1) | ✔ |
| `sh_intro_ch4_text_2` | `textarea` → string (inline HTML: `<strong>` `<em>`) | — | ช่องข้อความที่ 2 ของตอน 4 (block `kind:text` ตามลำดับใน `sections[].blocks[]` · seed `html` → `\n\n`/`\n`) · 7 ช่อง = seed สูงสุด 6 + 1 | | intro ตอน 4 (`sh_intro_chapters()` · ตำแหน่ง = `layout` slot 2) | ✔ |
| `sh_intro_ch4_text_3` | `textarea` → string (inline HTML: `<strong>` `<em>`) | — | ช่องข้อความที่ 3 ของตอน 4 (block `kind:text` ตามลำดับใน `sections[].blocks[]` · seed `html` → `\n\n`/`\n`) · 7 ช่อง = seed สูงสุด 6 + 1 | | intro ตอน 4 (`sh_intro_chapters()` · ตำแหน่ง = `layout` slot 3) | ✔ |
| `sh_intro_ch4_text_4` | `textarea` → string (inline HTML: `<strong>` `<em>`) | — | ช่องข้อความที่ 4 ของตอน 4 (block `kind:text` ตามลำดับใน `sections[].blocks[]` · seed `html` → `\n\n`/`\n`) · 7 ช่อง = seed สูงสุด 6 + 1 | | intro ตอน 4 (`sh_intro_chapters()` · ตำแหน่ง = `layout` slot 4) | ✔ |
| `sh_intro_ch4_text_5` | `textarea` → string (inline HTML: `<strong>` `<em>`) | — | ช่องข้อความที่ 5 ของตอน 4 (block `kind:text` ตามลำดับใน `sections[].blocks[]` · seed `html` → `\n\n`/`\n`) · 7 ช่อง = seed สูงสุด 6 + 1 | | intro ตอน 4 (`sh_intro_chapters()` · ตำแหน่ง = `layout` slot 5) | ✔ |
| `sh_intro_ch4_text_6` | `textarea` → string (inline HTML: `<strong>` `<em>`) | — | ช่องข้อความที่ 6 ของตอน 4 (block `kind:text` ตามลำดับใน `sections[].blocks[]` · seed `html` → `\n\n`/`\n`) · 7 ช่อง = seed สูงสุด 6 + 1 | | intro ตอน 4 (`sh_intro_chapters()` · ตำแหน่ง = `layout` slot 6) | ✔ |
| `sh_intro_ch4_text_7` | `textarea` → string (inline HTML: `<strong>` `<em>`) | — | ช่องข้อความที่ 7 ของตอน 4 (block `kind:text` ตามลำดับใน `sections[].blocks[]` · seed `html` → `\n\n`/`\n`) · 7 ช่อง = seed สูงสุด 6 + 1 | | intro ตอน 4 (`sh_intro_chapters()` · ตำแหน่ง = `layout` slot 7) | ✔ |
| `sh_intro_ch4_image_1` | `image` → integer | — | ช่องภาพที่ 1 (block `kind:image` ไม่นับ decor · manifest `intro-1117/<drive_file>` still · animation → poster · ไม่มีใน manifest = `figma_fill_file` ย่อ 2×) · 5 ช่อง = seed สูงสุด 4 + 1 | | intro ตอน 4 | ✔ |
| `sh_intro_ch4_image_1_animation` | `image` → integer (WebP เคลื่อนไหว) | — | manifest `kind: animation` `out` ของช่องภาพ 1 | | intro ตอน 4 (`<img>` แทนภาพนิ่ง) | ✔ |
| `sh_intro_ch4_image_2` | `image` → integer | — | ช่องภาพที่ 2 (block `kind:image` ไม่นับ decor · manifest `intro-1117/<drive_file>` still · animation → poster · ไม่มีใน manifest = `figma_fill_file` ย่อ 2×) · 5 ช่อง = seed สูงสุด 4 + 1 | | intro ตอน 4 | ✔ |
| `sh_intro_ch4_image_2_animation` | `image` → integer (WebP เคลื่อนไหว) | — | manifest `kind: animation` `out` ของช่องภาพ 2 | | intro ตอน 4 (`<img>` แทนภาพนิ่ง) | ✔ |
| `sh_intro_ch4_image_3` | `image` → integer | — | ช่องภาพที่ 3 (block `kind:image` ไม่นับ decor · manifest `intro-1117/<drive_file>` still · animation → poster · ไม่มีใน manifest = `figma_fill_file` ย่อ 2×) · 5 ช่อง = seed สูงสุด 4 + 1 | | intro ตอน 4 | ✔ |
| `sh_intro_ch4_image_3_animation` | `image` → integer (WebP เคลื่อนไหว) | — | manifest `kind: animation` `out` ของช่องภาพ 3 | | intro ตอน 4 (`<img>` แทนภาพนิ่ง) | ✔ |
| `sh_intro_ch4_image_4` | `image` → integer | — | ช่องภาพที่ 4 (block `kind:image` ไม่นับ decor · manifest `intro-1117/<drive_file>` still · animation → poster · ไม่มีใน manifest = `figma_fill_file` ย่อ 2×) · 5 ช่อง = seed สูงสุด 4 + 1 | | intro ตอน 4 | ✔ |
| `sh_intro_ch4_image_4_animation` | `image` → integer (WebP เคลื่อนไหว) | — | manifest `kind: animation` `out` ของช่องภาพ 4 | | intro ตอน 4 (`<img>` แทนภาพนิ่ง) | ✔ |
| `sh_intro_ch4_image_5` | `image` → integer | — | ช่องภาพที่ 5 (block `kind:image` ไม่นับ decor · manifest `intro-1117/<drive_file>` still · animation → poster · ไม่มีใน manifest = `figma_fill_file` ย่อ 2×) · 5 ช่อง = seed สูงสุด 4 + 1 | | intro ตอน 4 | ✔ |
| `sh_intro_ch4_image_5_animation` | `image` → integer (WebP เคลื่อนไหว) | — | manifest `kind: animation` `out` ของช่องภาพ 5 | | intro ตอน 4 (`<img>` แทนภาพนิ่ง) | ✔ |
| `sh_intro_ch4_layout` | `textarea` → string (JSON object) | ✔ | seed `chapters[3]` ยกเว้นข้อความ/ไฟล์: `frame_h map{rel,box,crop,fill_mode} map_buttons arrows sections[{name,bg,rel,blocks[]}]` · block = `{kind:"text",slot,role,font,box,rel}` · `{kind:"image",slot,box,rel,crop,fill_mode}` · `{kind:"decor",image_ref(→id),box,rel}` | | intro ตอน 4 (geometry) | ✔ |

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
| `sh_header_image` | `image` → integer | — (ไม่ใช้ตั้งแต่ D47) | ภาพพื้น Header #1 `670:9386` (โลโก้ในภาพ) | `709572bf…` | — (header เดิมก่อน D47 · เก็บค่าไว้) | ✔ |
| `sh_logo_header` | `image` → integer | ✔ | โลโก้ SH `I3339:5285;3307:1972` (§9.1 · D47) · `pages-1117/fills/877fd074….png` ครอป x .0738 y .1013 w .8768 h .8987 ตอน import → 440×154 | | header ทุกหน้า (ว่าง = ชื่อเว็บเป็นข้อความ) | ✔ |
| `sh_logo_unesco_footer` | `image` → integer | — (0 = SVG ของ theme) | `3339:5249` SVG 283.1×60 (WP ไม่รับอัปโหลด SVG → theme `assets/img/common/unesco-footer-logo.svg`) | 0 | footer ทุกหน้า | ✔ |
| `sh_logo_temasek` | `image` → integer | ✔ | `3339:5256` `8bffcba6….png` 3886×1124 → 416×120 | | footer ทุกหน้า | ✔ |
| `sh_footer_support_text` | `text` → string (default "With the Support of") | ✔ | `3339:5255` | "With the Support of" | footer ทุกหน้า | ✔ |
**เมนู** = เมนู WP (Appearance › Menus) ไม่ใช่ฟิลด์: `Primary` (seed `nav_primary` **6 รายการ** · ผูก location `primary`) · `Utility` (seed `nav_utility` 6 · ผูก location `utility` เมื่อ theme register · ไม่แสดงตั้งแต่ D47) · "Main Page" → หน้าแรก · **"About the Project" (D47) = custom link `site.json nav_primary_links` URL · "Open link in a new tab" (target `_blank`) · Link Relationship `noopener noreferrer`** — URL แก้ที่เมนู (ไม่มี option `sh_about_url` แยก · ที่เดียวกันกับเมนูอื่น) · header ใส่ `rel="noopener noreferrer"` + aria-label "(opens in a new tab)" ให้ทุกรายการที่เปิด new tab เอง · ที่เหลือ custom link `#` (ปลายทางยังไม่กำหนด P-211)
**โลโก้ UNESCO (SVG)** = asset ของ theme (WP ไม่อนุญาตอัปโหลด SVG · เปิดต้องมี D เพราะเป็นชั้นพิเศษ upload) · footer (D47) ใช้ `assets/img/common/unesco-footer-logo.svg` เมื่อ `sh_logo_unesco_footer` = 0 (admin แทนด้วยภาพ PNG ได้)

## 10. ไม่อยู่ในสัญญา (เหตุผล)
- DESIGN-SPEC §5.1 `timeline_card_image` + `timeline.*` ของวัตถุ → อยู่ที่ `timeline_item` (§3) เพราะหัว/ข้อความการ์ดต่างจากหน้า detail · `title_raw` → `sh_title_raw` · `detail_style.*` → `sh_panel_bg` … `sh_collection_color` · `layout` → `sh_layout`
- `_sh_import_key` (post/term/attachment meta ซ่อน ไม่ลง REST) = ตัวชี้ของ importer สำหรับรันซ้ำ ไม่ใช่ข้อมูลหน้า
- hero GIF หลายเฟรม (`hero_images[]` 3–24 เฟรม) → เก็บ `sh_hero_image` ภาพนิ่ง 1 ใบ + วิดีโอ (D13: media-dev ต่อเฟรมเป็น WebM) · `hero_still` ที่ซ่อนใน Figma ไม่ใช้
- หมุดแผนที่ (`pins[]` x,y) — ไม่มีใน seed · popup ใช้ข้อความ slide เดียวกัน (DESIGN-SPEC §5.5)
- `home.side_navigator` · `home.trails_animation_frames[12]` (ใช้วิดีโอ `walking` แทน) · `status[]` (ข้อสังเกตภายในของ seed) · `tile_bg.group` (ชื่อกลุ่ม ไม่ใช้แสดงผล)
- ความสัมพันธ์ zone ↔ trail/object (P-212) — กริด zone 1–3 = ช่องเลขลำดับ `sh_zone_tile_<n>_*` §5b (D57) · `sh_show_in_zone_grid` เดิมไม่ใช้บนหน้า zone แล้ว

## 11. API สำหรับ template (mu-plugin `inc/api.php` · คืนค่าดิบ — template ต้อง escape เอง)
| ฟังก์ชัน | คืน |
|---|---|
| `sh_get( $key, $post_id = 0 )` · `sh_get_term( $key, $term )` · `sh_get_option( $key )` | ค่า meta/option (ว่าง = `''`/0) |
| `sh_page_id( 'front'\|'intro'\|'timeline' )` | id หน้า (0 = ไม่มี) |
| `sh_trails( 'home'\|'zone_grid'\|'timeline_nav' )` | `WP_Term[]` เรียงตาม `sh_order_home_icons` / `sh_zone_grid_order` (เฉพาะ `sh_show_in_zone_grid`) / `sh_timeline_nav_order` |
| `sh_zones()` | `WP_Term[]` เรียงตาม `sh_number` |
| `sh_trail_objects( $trail )` | `WP_Post[]` วัตถุ publish ของ trail ตาม `sh_object_order` (ที่ไม่อยู่ในลำดับต่อท้าย A–Z) |
| `sh_timeline_items( $trail )` | `WP_Post[]` การ์ด publish ของ trail ตาม `sh_card_order` |
| `sh_object_hero( $id, $slot = 0 )` | `{webm, hevc, poster (URL), image_id, has_alpha}` · `$slot` > 0 = hero ของ trail version n (`sh_variant_<n>_hero_*` · ไม่มีภาพนิ่ง) |
| `sh_object_variant( $id, $trail_slug )` | version ของ trail ที่กดมา หรือ `null` (ไม่รู้/ปลอม/ไม่ใช่ trail ของวัตถุ/ไม่มี slot = วัตถุตามปกติ): `{slot, trail (WP_Term), hero (เหมือน sh_object_hero · ไม่มีวิดีโอ = hero ของวัตถุ), layout, colors {sh_panel_bg…}, images {k => id}, title, caption, info {key => ค่า}}` (§2b) |
| `sh_object_variant_slots( $id )` · `sh_object_info_value( $id, $key, $variant )` | slot n => term id ของ trail ที่ตั้งไว้ · ค่าแถวข้อมูลที่หน้าแสดง (ของ version ถ้ามีช่องใดมีค่า มิฉะนั้นของวัตถุ) |
| `sh_object_trail_url( $post, $trail )` | permalink ของวัตถุ + `?sh_trail=<slug>` เมื่อวัตถุมี version ของ trail นั้น (ไม่มี = permalink เดิม) |
| `sh_object_extra_images( $id )` · `sh_object_layout( $id )` · `sh_paragraphs( $text )` | id ภาพแผง · layout array · ย่อหน้า string[] |
| `sh_rich_paragraphs( $text )` | ย่อหน้า HTML ที่ปลอดภัยแล้ว (`<strong>` `<em>` `<br>` เท่านั้น) ของฟิลด์ inline HTML (`sh_guide_body` · `sh_intro_ch<n>_text_<k>`) — echo ได้เลย |
| `sh_trail_tiles( $trail )` | ช่องกริดหน้า trail ตามลำดับ: `{object (WP_Post\|null), url ('' = ไม่ลิงก์), label, image, hover}` = วัตถุ publish (ภาพช่องของ trail นั้น) + ช่องไม่มีหน้า `sh_static_tile_<n>_*` ที่ตำแหน่งของมัน |
| `sh_zone_grid( $zone )` | ช่องกริดหน้า Zone 1–3 ตามลำดับช่อง `n`: `{slot, object (WP_Post\|null), url ('' = ไม่ลิงก์), label, image, hover}` · วัตถุ publish = ลิงก์ (ภาพว่าง = ภาพของวัตถุ) · ไม่มีวัตถุ/ไม่ publish = ภาพไม่ลิงก์ · ช่องที่ไม่มีภาพถูกข้าม |
| `sh_object_trail_tile( $id, $trail )` | `{image, hover}` ของวัตถุใน trail นั้น (`sh_trail_tile_<n>_*` ?: `sh_tile_image(_hover)`) |
| `sh_guide_cards()` | การ์ดสัตว์หน้าแรก: `{trail, name, words, body (string[] HTML), follow, card, url}` ของ trail ที่มี `sh_guide_card` (ลำดับ home icons) |
| `sh_intro_head( $page_id = 0 )` | `{emblem, title, paragraphs[], credit, env_heading, layout}` |
| `sh_intro_chapters( $page_id = 0 )` | `[1..4 => {n, title, accent, map_image, map_animation, texts[slot] (string[] HTML), images[slot] {id, animation}, layout}]` |
| `sh_json_value( $value )` | JSON ที่เก็บใน meta → array (`sh_trail_layout` ฯลฯ) |

## 12. Import (`scripts/import/`)
- `import.php` (+ `lib.php` `refs.php` `icc.php` `media.php` `entities.php` `objects.php` `content-2026-11.php` `refresh.php` `refresh-2026-11.php` · ข้อมูล `image-map.json` `legacy-values.json`) รันใน WP: local อัตโนมัติใน `scripts/local.sh` · server `SH_IMPORT_ASSETS_DIR=… SH_IMPORT_MEDIA_DIR=… wp eval-file scripts/import/import.php`
- อ่าน `content/seed/*.json` + `content/media-manifest.json` + `scripts/import/image-map.json` · ภาพจาก `Unesco/design/assets/` · วิดีโอ/poster จาก `Unesco/media/out/` (นอก repo · path จาก env/constant `SH_IMPORT_ASSETS_DIR` `SH_IMPORT_MEDIA_DIR`)
- `image-map.json` สร้างด้วย `python3 scripts/import/build-image-map.py` จาก `design/figma-file.json` (node ใน seed → imageRef = ชื่อไฟล์ · กฎ: fill IMAGE ที่ visible ชั้นบนสุด · `missing` = ref ที่ไม่มีไฟล์) — รันใหม่เมื่อ seed/Figma/`design/assets` เปลี่ยน
- **วิดีโอ/ภาพ hero อ่านจาก manifest ตรง (ไม่อยู่ใน image-map):** วัตถุ = รายการที่ `object_slug` = slug (`kind: video` → `out` + `poster` + HEVC เฉพาะ `fallback.type = hevc-alpha-mov` · `kind: still` → `still` เป็น `sh_hero_image`) · Home Trails = รายการ `usage: home-trails` · media-dev เพิ่มรายการใหม่ → import รอบหน้าหยิบเอง ไม่ต้องแก้โค้ด
- **ICC → sRGB (`icc.php`):** JPEG/PNG ที่ฝัง ICC profile ที่ชื่อไม่ขึ้นต้น `sRGB` (Apple "Color LCD"/Display P3/DCI-P3 ใน `design/assets` 73 ไฟล์) → ลบ profile (JPEG APP2 · PNG iCCP+cICP+cHRM+gAMA) ตอนคัดลอก **ไม่แปลงค่าพิกเซล** — Figma วาดค่าพิกเซลดิบโดยไม่สนใจ profile · วัดแล้ว: tile `8428e71b` Figma (241,173,160) = ค่าดิบ · ถ้าแปลงสีจริงได้ (253,169,158) ≠ Figma · ไม่ re-encode (lossless) · PHP ล้วน ไม่ต้องมี GD/Imagick
- **ภาพก่อน timeline (`sh_line_art` · D32 → D35/D36):** seed `timeline-elephant.json items[].line_art_ref` = ชื่อไฟล์ใน `design/assets/lineart/` (หาที่นั่นก่อน → ไม่เจอใช้กฎ `*_ref` ที่ราก `design/assets`) → attachment `asset:lineart/<ชื่อไม่มีนามสกุล>` → `sh_line_art` · ไม่มีคีย์ = ไม่แตะฟิลด์ · ไม่มีไฟล์ = คำเตือน · `bundle-files.php` ใส่ไฟล์เหล่านี้ใน bundle เป็น `ref<TAB>lineart/<file>` (path ใต้ assets ชั้นเดียว)
- **ภาพใต้เส้น timeline (`sh_below_image` · D39/D41):** seed `items[].below_ref` = ชื่อไฟล์ที่ราก `design/assets/` (กฎ `*_ref`) → attachment `asset:<ชื่อไม่มีนามสกุล>` → `sh_below_image` · ไม่มีคีย์ = ไม่แตะฟิลด์ · ไม่มีไฟล์ = คำเตือน · `bundle-files.php` ใส่ไฟล์เป็น `ref<TAB><file>` · **D45:** `items[].below_ref_2` → `sh_below_image_2` กฎเดียวกัน (ชิ้น 9) · ชิ้น 1·7·9 สลับ: บนเส้น = `image-map.json` `timeline_items.1/.7/.9` (`bbc1e161` · `555d365b` · `1c0fc05b` · ชิ้น 9 ภาพเดียว → `sh_thumbnail_2` = 0) · ใต้เส้น = `below_ref` (+`_2`) `4953122e` · `621646b5` · `b1ba8a8a` + `1c3aa3a1` (แก้มือ · ไฟล์บนเส้นใหม่อยู่ใน image-map `files` แล้ว) · รูปบนเส้นชิ้น 5/7 (D39) = `image-map.json` `timeline_items.5/.7` **แก้มือ** ตาม seed `thumbnail_refs` (`build-image-map.py` อ่าน `figma-file.json` 08:23Z ไม่ได้ — รันใหม่จะทับค่านี้ · P-261)
- **idempotent:** หาเดิมด้วย `_sh_import_key` (post/attachment) · slug (term) · path (page) · ชื่อ (menu) → `SH_IMPORT_MODE=missing` (default) ไม่แตะของที่มีแล้ว (ไม่ทับงานที่ admin แก้) · `overwrite` = seed ทับ
- **แก้ค่าที่ importer รุ่นก่อนเขียนผิด = refresh step (`refresh.php`)** รันครั้งเดียวต่อ site (option `sh_import_refresh_done`) แม้ mode `missing` · แก้เฉพาะฟิลด์ที่ระบุ และเฉพาะเมื่อค่าเดิมว่าง หรือยังเป็น attachment ที่ importer สร้าง (มี `_sh_import_key`) — ไฟล์ที่ admin อัปโหลด/เลือกเองไม่ถูกแทน · `2026-11-11-reflection` (D34): `sh_timeline_reflection_title`/`_body` ของหน้า timeline → ข้อความ seed ปัจจุบัน **เฉพาะเมื่อค่าบน site ยังตรงข้อความ seed เดิมทุกไบต์** (เทียบ sha256 ของค่าที่ import รุ่นก่อนเขียน) · admin แก้แล้ว = ไม่แตะ · `2026-11-11-line-art` (D35/D36): `timeline_item` ที่มีแล้ว (key `timeline:elephant:<card_order>`) + seed มี `line_art_ref` → ตั้ง `sh_line_art` **เฉพาะเมื่อฟิลด์ยังว่าง** (ค่าที่ admin ตั้ง/ที่ import เขียนไว้แล้วไม่แตะ) · ไฟล์ขาด = ยังไม่นับว่าเสร็จ (รันใหม่รอบหน้า) · `2026-11-11-timeline-images` (D39/D41): `sh_thumbnail` ชิ้น 5/7 → ref ใหม่ใน image-map **เฉพาะเมื่อยังเป็น attachment ที่ import รุ่นก่อนสร้างจากภาพเดิมของชิ้นนั้นพอดี** (key `asset:5d9931b4…` / `asset:867b2833…` · admin อัปโหลด/เลือกภาพอื่น/ลบภาพ = ไม่แตะ) · `sh_below_image` จาก `below_ref` **เฉพาะเมื่อว่าง** · ไฟล์ขาด = ยังไม่นับว่าเสร็จ · `2026-11-11-timeline-swap` (D45): ชิ้น 1·7·9 สลับเป็นค่าปัจจุบันของ import (บน `sh_thumbnail`/`_2` · ใต้ `sh_below_image`/`_2`) **เฉพาะเมื่อทุกช่องของชิ้นนั้นยังเป็น attachment ที่ import สร้างจากภาพของชิ้นนั้นเอง** (`_sh_import_key` อยู่ในชุด ref ก่อน/หลังสลับของชิ้นนั้น · ช่อง `_2` ที่ต้องว่างหลังสลับ = ว่างหรือ ref ของชิ้นนั้น) · ช่องใดเป็นภาพที่ admin อัปโหลด/เลือกเอง หรือถูกล้าง = ข้ามทั้งชิ้น + คำเตือนใน report · เท่าค่าใหม่แล้ว = ไม่แตะ (idempotent) · ไฟล์ขาด = ยังไม่นับว่าเสร็จ · `2026-10-27-media`: hero วัตถุ (`sh_hero_*`) · `sh_home_trails_video_*` · `sh_intro_map_image` · ICC ของไฟล์ `asset:*` ที่คัดลอกไปแล้ว (มี sub-size → สร้างใหม่) · บังคับรันซ้ำ `SH_IMPORT_REFRESH=<id>|all` · import report มี `refresh: [ids ที่รันรอบนี้]` · attachment เก่าที่เลิกใช้ (เช่น แผนที่ `cd63a05d`) ไม่ลบ
- **รอบ Figma 2026-11-11 (core-hzd · D37 D38 D40 · `content-2026-11.php` + `refresh-2026-11.php`):**
  - seed ที่อ่านเพิ่ม: `zone4-chooser.json` · `zones.json` (`name`/`name_raw` = ชื่อ B · `intro_short` → `sh_intro` · `body_full` → `sh_intro_full` · `banner_image.file` → `sh_cover_image` + alt `name_banner` · zone 5 `page` ตาม P-172) · `objects.json` `figma_2026_11.{title,title_raw,caption}` (มี = ใช้แทน `title`/`title_raw`/`caption` · slug = `slug` เสมอ) · ข้อความ zones: บรรทัดใหม่ 1 ครั้ง = ย่อหน้า → เก็บคั่นบรรทัดว่าง (`sh_paragraphs()`)
  - **zone 5 `page` (seed · design-dev เติม P-172):** `{cover, zone_bar, cards: [{image, image_hover, soon}×4], strip: [×4], history: {image, image_hover}}` → `sh_cover_image` `sh_banner_image` `sh_page_card_<n>_image(_hover)` `sh_page_card_<n>_soon` `sh_page_strip_image_<n>` `sh_page_history_image(_hover)` · ลิงก์การ์ด (`sh_page_card_<n>_link`) ไม่ import (admin เลือกเมื่อหน้าย่อยพร้อม) · `cards[].alt` / `history.alt` → alt ของ attachment (ตั้งเมื่อว่าง) · คีย์หมายเหตุ (`frame` `figma_nodes` `figma_target` `strip_nodes`) ไม่อ่าน · seed มาแล้ว (design-dev e62b480 · ภาพ `design/assets/zone5/` 16 ไฟล์)
  - **ค่าภาพใน seed ใหม่** = path `design/assets/<sub>/<file>` (sub-folder ชั้นเดียว · รายชื่อ `refs.php sh_import_asset_subdirs()` = `zone-banners-2026-11` `zone4-animals` `zone5`) หรือ imageRef/ไฟล์ที่ราก · ข้อความหลังช่องว่างแรก = หมายเหตุ (ตัดทิ้ง) · attachment key `asset:<sub>/<ชื่อไม่มีนามสกุล>` · bundle (`bundle-files.php`) รวมให้เอง
  - `image-map.json` (build 2026-11-11): ส่วน objects อ่าน `design/figma-file.json` ใหม่ (P-245: Brass Basin `3190:4671` · ภาพแผงใหม่ Ganesha/Singhasari/Molar/Patola/Bas-relief 3) · ส่วนอื่นอ่าน `design/figma-file.2026-10-27.json` ก่อน (`--figma-fallback` · หน้าอื่นยังไม่ได้ spec กับไฟล์ใหม่ §1.1b — ไฟล์ใหม่เปลี่ยน hero หน้าแรก/zone cover ที่ยังไม่มีใครสั่ง) · `_meta.nodes_from_older_dump`
  - **refresh (site ที่ import ก่อนรอบนี้ = production):** ค่าเดิมแทน **เฉพาะเมื่อยังเท่าค่าที่ import รุ่นก่อนเขียน** — `scripts/import/legacy-values.json` (สร้างด้วย `python3 scripts/import/build-legacy-values.py [base=f818837]` จากประวัติ git ของ seed/image-map ด้วยสูตรเดียวกับ importer · ข้อความเทียบตรง/หลัง sanitize/หลัง decode entity · `sh_layout` เทียบ fingerprint `refs.php sh_import_layout_fingerprint()` (ลำดับ key · รูปแบบตัวเลข · `*_ref` เป็นไฟล์หรือ attachment id ของ import = เท่ากัน) · ภาพแผง 1..6 เทียบรายการ key ทั้งชุด · `EXTRA` = ค่าที่ทีมแก้บน prod ตามคำสั่งเจ้าของ (Pod Duang `sh_title_raw` 2026-10-28)) · ฟิลด์ใหม่เติมเมื่อว่าง (true/false เมื่อยังไม่เคยบันทึก) · step: `2026-11-11-zones` (zone 1–3) · `-objects` (title · title_raw · caption · แถวข้อมูล · สี 5 · `sh_layout` · `sh_extra_image_1..6` · ไม่แตะ slug) · `-chooser` (zone 4 `sh_link_page` = หน้า Timeline → ว่าง · พื้น/แถบ · trail `sh_chooser_image`) · `-zone5` (รอ seed P-172: ไม่บันทึกว่าเสร็จ · ไม่เตือน) · `-media-replace` (ไฟล์ media/out เปลี่ยนแต่ชื่อเดิม: เขียนทับไฟล์ของ attachment ที่ import สร้างเมื่อ sha256 = ไฟล์รุ่นเก่า (`legacy-values.json` `media`) · id/URL เดิม · metadata ใหม่ · admin เปลี่ยนไฟล์ = ไม่ทับ) · ไฟล์ขาด = ไม่บันทึกว่าเสร็จ · ค่าที่ admin แก้ = นับใน report `skipped.kept_admin_value` / `kept_admin_file`
  - ผลตรวจ (local 2026-11-11 · core-hzd): ดูรายงาน core-hzd (`orchestrator/evidence/core-hzd/`)
- **รอบ 2026-11-17 (core-1117 · D47–D53 · `refs-2026-11-17.php` + `content-2026-11-17.php` + `refresh-2026-11-17.php` + `web-image.php`):**
  - seed ที่อ่านเพิ่ม: `guides.json` (การ์ดสัตว์ → trail term `sh_guide_*` ตาม `trail_slug` · `section` → Home `sh_home_guide_*`) · `intro-2026-11.json` (Intro 4 ตอน §7) · `site.json` `brand.header_logo` / `brand.footer.temasek` / `nav_primary_links` · `objects.json` + `trails.json` ของ 16 trail (D49)
  - **ขอบเขตวัตถุ = `sh_import_all_objects()`**: มีตำแหน่งใน trail จริง (`trail_order` ไม่นับ `etc`) หรือ status `hold_*` → 117 วัตถุ (ช้าง 15 + generic 102) + hold 2 (`pending` · `sumatran-serow` = **draft**) · ช้าง 15 ยัง import ด้วย `sh_import_objects()` เดิม (`sh_import_scope_objects()` = D7 ใช้กับ step เก่าเท่านั้น) · `sh_object_order` ทุก trail = วัตถุ publish เรียงตาม `trail_order` (hold ไม่อยู่ในลำดับ — ช่องของมันเป็น `sh_static_tile_*`)
  - **ไฟล์สื่อ = path จาก manifest (P-303):** `out`/`poster`/`still`/`fallback.file` → `media/out/<path>` (`sh_import_media_rel()` · ≤ 2 ชั้น ไม่มี `..`/dot) · import key `media:<path>` (ไฟล์ชั้นบนเดิม = basename เหมือนเดิม → attachment เดิมถูกใช้ซ้ำ) · hero วัตถุ generic = manifest key `drive-1117/<hero_video_src>` (ไม่มี `object_slug`) · Intro = key `intro-1117/<drive_file>`: still → ภาพ · `kind: animation` → `<n>_animation` = WebP เคลื่อนไหว + ภาพ = poster
  - **ภาพ design/assets ใหม่:** ค่า `*_ref` ชื่อเดี่ยว → หาที่ราก แล้ว `objects-1117/` (`sh_import_ref_file()`) · ค่าใน seed หน้า (`guides/…` `fills/…` `intro-icons/…`) → `pages-1117/<sub>/` · sub-folder ที่อนุญาต (`sh_import_asset_subdirs()`): `objects-1117` `pages-1117/fills` `pages-1117/guides` `pages-1117/intro-icons` (+ ของเดิม) · key `asset:<sub>/<ชื่อไม่มีนามสกุล>`
  - **PNG → JPEG (core-1117b):** ผลลัพธ์ PNG > 1 MB ที่ไม่มีพิกเซลโปร่ง — หรือมีสีพื้นที่ภาพวางเสมอ (`matte`: ปก trail 16 ภาพบนหน้าขาว §8.1 · โปร่งแค่ขอบครอป) → JPEG คุณภาพ 82 (ปก 4–5 MB → 415–588 KB · SSIM ที่ 1120 กว้าง 0.96–0.97) · ภาพแผงที่ตัดพื้นโปร่ง (3 ไฟล์ > 1 MB) คงเป็น PNG · ไฟล์ใน bundle อาจเป็น JPEG ภายใต้ชื่อ `.png` เดิม → import ตั้งชื่อ/mime ตามเนื้อไฟล์ (`.jpg`) · import key ไม่เปลี่ยน · ปกช้าง (image-map เดิม = เหมือน prod) ไม่แตะ · **ไม่มี GD = คำเตือนใน report ต่อไฟล์** ("PHP GD missing …") และ bundle หยุด (`web-image.php` exit 1)
  - **ย่อภาพไม่เกิน 2× ขนาดแสดงผล (web spec · `refs-2026-11-17.php`):** ช่อง 184 → 368² · hover 210 → 420² · ปก trail 2240×980 (ครอปมาแล้ว) · การ์ดสัตว์ 460×726 · หลังการ์ด ครอป .016/.017/.968/.971 → 240×380 · โลโก้ header ครอปตาม seed → 440×154 · Temasek 416×120 · ภาพ Intro ที่ไม่มีใน manifest = 2× กล่อง · ครอปภาพแผง `crop_ref` (2× อยู่แล้ว) = คัดลอกตรง · import ย่อ/ครอปตอนคัดลอก (`sh_imp_attachment()` + GD · ไม่มี GD = คัดลอกตรง · นับใน report `updated.resized_2x`) · `deploy.sh` ย่อ bundle ก่อนส่ง (กล่องก่อนครอป · ครอปทำครั้งเดียวตอน import) · ภาพของ image-map/ชุดช้างเดิม = ไม่ย่อ (เหมือน prod)
  - **refresh step (site ที่ import ก่อนรอบนี้ = production · `SH_IMPORT_ONLY=refresh` ได้):** `2026-11-17-objects` สร้างวัตถุที่ยังไม่มี (hold = draft · วัตถุที่ admin สร้างเองด้วย slug เดียวกัน = รับเป็นของ import แต่ไม่ทับ) · วัตถุที่มีแล้ว = เติมเฉพาะฟิลด์ว่าง (`sh_scientific_name` · `sh_trail_tile_*`) ไม่แตะชื่อ/ข้อความ · `2026-11-17-trails` description/H1/cover/layout/ช่องไม่มีหน้า/การ์ดสัตว์ = เติมเมื่อว่าง · `sh_object_order` แทนเฉพาะเมื่อยังเป็นรายการที่ import รุ่นก่อนเขียนพอดี (วัตถุช้างของ trail นั้นตามลำดับ seed) · `2026-11-17-pages` Home การ์ดสัตว์ + Intro 4 ตอน = เติมเมื่อว่าง · `2026-11-17-trail-grid` (core-1117b · P-340) `sh_trail_layout` + `grid` เฉพาะเมื่อว่างหรือยังเท่า layout ที่ core-1117 เขียน (fingerprint) · `2026-11-17-site` option โลโก้/ข้อความ footer = เมื่อยังไม่เคยตั้ง · เมนู Primary เพิ่ม "About the Project" ท้ายเมนู (ลำดับ = max(menu_order ที่เก็บจริง)+1) เมื่อไม่มีรายการชื่อ/URL เดียวกัน (ไม่ลบ/ไม่เรียง/ไม่เปลี่ยนชื่อรายการเดิม) · **ค่าที่ไม่แตะเพราะ admin แก้ = report `kept_admin[]` (อะไร + ค่าที่ site) + `skipped.kept_admin_value`** · มีคำเตือน (ไฟล์ขาด) ระหว่าง step = ยังไม่บันทึกว่าเสร็จ
  - ผลตรวจ (local 2026-11-17 · core-1117): fresh `SH_IMPORT_RUNS=2` → object publish 116 · draft 2 · trail 17 · การ์ดสัตว์ 17 · Intro ตอน 1–4 ข้อความ 5/5/6/4 ช่อง ภาพ 4/3/3/1 · attachment 908 · ย่อ 274 · คำเตือน 0 · รอบ 2 สร้าง/แก้ 0 · ช่องกริด 16 trail ตรง `trails.json tiles[]` ทุกช่อง (ภาพ+วัตถุ) · จำลอง prod (import ด้วย 9fc08ce → admin แก้ 3 จุด → refresh-only ×2) = ค่าเดียวกับ fresh · `kept_admin` 3 รายการ · รอบ 2 แก้ 0 (`orchestrator/evidence/core-1117/`)
  - **กริดวัตถุ Zone 1–3 (fb-1117 · D57 · §5b):** step `2026-11-17-zone-grid` (รันตอน fresh import ท้ายรอบด้วย + บน prod `SH_IMPORT_ONLY=refresh`) เติม `sh_zone_tile_<n>_object/_image/_image_hover` ของ term `zone-1..3` จาก seed `zones-2026-11-17.json` (`zones[].tiles[]` · `order` = ช่อง `n`) **เฉพาะช่องที่ว่าง** · ค่าที่ไม่ว่างและต่างจาก seed (admin แก้) = คงไว้ **และไม่เติมฟิลด์อื่นของช่องนั้น** (ภาพของ seed ข้างวัตถุของ admin จะไม่ตรงกัน) + `kept_admin` (1 บรรทัดต่อฟิลด์) · วัตถุ = `_sh_import_key object:<slug>` (ไม่มีบนไซต์ = ช่องเป็นภาพไม่ลิงก์) · ภาพ = attachment ที่วัตถุมีอยู่แล้ว (หาด้วย import key `asset:<ไฟล์ไม่มีนามสกุล>` ของไฟล์ `objects-1117/<ref>.png` หรือไฟล์ราก/image-map · ไม่อัปโหลดซ้ำ) · มีเฉพาะภาพที่ไม่มีวัตถุไหนถือ (zone 1 ช่อง 8 Buffalo 6 · ไม่ลิงก์) ที่สร้างใหม่จาก bundle (+2 ไฟล์ · นับ `resized_2x`) · hover ที่ Figma ฝั่ง zone ยังเป็นภาพรุ่นเก่า (`tile_image_hover_ref_prev` ของ hover-1117 · kalung · dragon-shaped…tray) แมปเป็นภาพใหม่ · ช่อง `tile_ref` ไม่มีไฟล์ใน `design/assets` (zone 3 ช่อง 21 `elephant-and-mahout-kendi` `1afa6a7f…`) = `_image` ว่าง → ใช้ภาพช่องของวัตถุ (นับ `skipped.zone_tile_no_file` · ไม่ใช่คำเตือน · P-395) · bundle-files: label `zone grid …` ไม่มีไฟล์ = ไม่ error · ผลตรวจ fresh `SH_IMPORT_RUNS=2`: 140 ช่อง (15/42/83) · รอบ 2 แก้ 0 · คำเตือน 0
- **เวอร์ชันต่อ trail (multitrail-1117 · D59 · §2b):** step `2026-11-17-multitrail` (รันตอน fresh import ท้ายรอบ + บน prod `SH_IMPORT_ONLY=refresh`) · (1) วัตถุ seed ที่มี `variants_by_trail` → ต่อ trail ที่**ไม่ใช่ trail หลัก** (`sh_import_object_variants()` · `is_primary:false` · ลำดับตาม `trails` ของวัตถุ) หา slot ที่ถือ trail นั้นอยู่แล้ว ไม่มี = slot ว่างแรก → `sh_import_variant_meta()` เติม `sh_variant_<n>_*` **เฉพาะฟิลด์ที่ว่าง** (`sh_import_fill_or_keep()`) · ค่าที่ไม่ว่างและต่างจาก seed (admin แก้) = คงไว้ + `kept_admin` · ไม่ลบอะไร · วัตถุที่ไม่มีบน site / trail ที่ไม่ใช่ term / วัตถุถูกถอดออกจาก trail นั้น = ข้าม · hero = manifest key `drive-1117/<hero_video_src>` (WebM + poster + HEVC เฉพาะ alpha · key `media:objects-1117/<trail>/…` ต่อ trail) · ภาพข้าง = `figma[].style.images[].crop_ref` (attachment เดียวกับใน layout) · layout = `sh_import_layout()` ของ frame นั้น · ข้อความ = `text_override` (title_raw ก่อน title · caption · info_rows → ช่องแถวข้อมูล) · (1b) **D60 drum:** สร้างวัตถุ `dong-son-drum-thung-yang` เมื่อยังไม่มี · ถอด term seashell จาก `ng-s-n-drum` เฉพาะเมื่อ trail ของมัน = water-bird+seashell ที่ importer ให้ (admin แก้ = `kept_admin`) · `sh_object_order` ของ seashell ใส่วัตถุใหม่แทนที่เดิมเฉพาะเมื่อยังเป็นลิสต์ของ importer · ช่องกริด Zone 3 ที่ยังถือ `ng-s-n-drum` → วัตถุใหม่ (ถือวัตถุอื่น = `kept_admin`) · ไม่ลบโพสต์/ไฟล์ · (2) กริด Zone 1–3: `sh_zone_tile_<n>_trail` จาก `tiles[].trail` เฉพาะเมื่อว่างและช่องยังถือวัตถุของ seed (วัตถุอื่น = `kept_admin`) · bundle-files: ครอปภาพข้างของ version ใส่ใน bundle ด้วย (`sh_import_1117_asset_values()`) · WebM/poster ของทุก version อยู่ใน manifest เดิม (`media/out/objects-1117/<trail>/`)
- ผลนับ (local 2026-10-27 · core-import-refresh): object 15 · timeline_item 15 · trail 17 · zone 5 · page 3 · attachment 205 · menu 2 · ICC→sRGB 67 ไฟล์ · คำเตือน 0 · รอบที่ 2 สร้างใหม่ 0 / แก้ 0 · import เก่า→ใหม่บน site เดียวกัน: วิดีโอ 7→14 · HEVC 4→6 · แผนที่ → 39907d2c · ICC 68 · ภาพ hero ที่ admin ตั้งเองคงเดิม
- ภาพนำเข้าโดยไม่สร้าง sub-size (ต้นฉบับใหญ่ · playground ช้า) · server ใช้ `SH_IMPORT_SUBSIZES=1`
