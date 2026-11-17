# DESIGN-SPEC — สเปกดีไซน์จาก Figma (desktop 1440 ต้องตรง 100%)

**เจ้าของไฟล์:** design-dev (งาน `design-extract`) · **ที่มา:** Figma file `EhcqSXTMEArlrfSGUQVCwl` ดึงด้วย REST (`design/figma-fetch.sh`) ไม่ใช้ MCP
**Figma lastModified:** `2026-10-27T09:48:30Z` · file name "UNESCO (สำรอง ห้ามลบ)" · version `2414978793958272801` · ไฟล์ดิบ `design/figma-file.json` (18.1 MB, ดึง 1 ครั้ง)
**นับจากไฟล์จริง:** 6 page · 95 section · 3,205 frame · 171 component set · 4,843 text node · 14,982 node ทั้งหมด
**กฎ D5 (เจ้าของ):** desktop 1440 ตรง Figma 100% · tablet/mobile = responsive ตามเหมาะสม (ไม่บังคับ frame) · **ไม่พบ mobile/tablet frame ที่ใช้จริงในไฟล์** (นับ: มีเฉพาะแผ่น style guide "Tablet — TBD" 768 กว้าง `670:974`; frame 320/360/414 เป็นชิ้นส่วนใน component)
**เอกสารประกอบ (อยู่นอก repo ที่ `Unesco/design/`):** `layout/*.md` = dump ทุก node (x,y,w,h เทียบ frame · fill · stroke · radius · shadow · opacity · auto-layout padding/gap · font/size/weight/lh/letter-spacing/case/สี) ของทุกหน้า+component · `frames/*.png` · `assets/` · `image-fills.json` · `figma-fetch.sh`
**อ่านลำดับ:** §1 สารบัญ frame → §2 tokens → §3 component กลาง → §4 โครงต่อหน้า → §5 ฟิลด์ → §6 พฤติกรรม → §7 ข้อสังเกต/P-2xx

> ค่าทุกตัวในเอกสารนี้คัดจาก Figma ตรง ๆ ไม่ปัดเศษ · พิกัด `x,y` เทียบมุมซ้ายบนของ frame ที่ระบุ (frame 1440 กว้าง) · `[x,y w×h]`

---
## 1. สารบัญ page / frame

### 1.1 Page ทั้ง 6 (นับจาก figma-file.json)
| page id | ชื่อ | children (top-level) | node | บทบาท |
|---|---|---|---|---|
| `670:860` | **Page 2  Version #Final 01092026** | 12 | 2,471 | **หน้า final** (ชื่อมี "Final") — style guide, home, intro, zone 1-3, trail ช้าง, timeline ช้าง (zone 4), detail ช้าง 15 ชิ้น, component ทั้งหมด |
| `998:952` | Page 2.1  ALL GIF trails | 18 sections | 6,349 | **detail ของวัตถุทุก trail (141 frame)** — เนื้อหาเดียวที่มีของ 16 trail นอกจากช้าง · ยังไม่ใช่ "Final" ตามชื่อ (P-201) |
| `3004:4156` | Page 6 | 3 | 770 | สำเนา section Elephan + เฟรม GIF 2 ชิ้น (ซ้ำ ไม่ใช้) |
| `263:362` | Page 2  Version #Option | 22 | 3,075 | เวอร์ชันเก่า (Option) — ไม่ใช้ |
| `0:1` | Page 1 | 19 | 2,122 | เวอร์ชันแรก (animation draft) — ไม่ใช้ |
| `669:1259` | Page 3 Picture trails | 18 | 194 | รูปประกอบ trail (ร่าง) — ไม่ใช้ |

### 1.1b Page ในไฟล์ **หลังอัปเดต 2026-11-11** (`lastModified 2026-11-11T08:23:07Z` · version `2420516781693135680` · นับจาก `design/figma-file.json` ใหม่ · ไฟล์เก่าเก็บที่ `design/figma-file.2026-10-27.json`) — **ตาราง §1.1 ข้างบนเป็นของ 2026-10-27 (6 page) ล้าสมัย: ตอนนี้ 5 page** · page เก่า Page 6 / Option / Page 1 / Picture trails **หายไปแล้ว**
| page id | ชื่อ | children | node | Desktop frame | บทบาท |
|---|---|---|---|---|---|
| `670:860` | Master Page Version #Final 01092026 | 15 | 4,104 | 48 | หน้า final เดิม + **ใหม่:** `Section 43` `680:10489` (AAMM-01 รอยเท้า) · `Zone 4` `820:2615` (timeline) · `Section 51` `3083:924` (Detail text Zone 1–3 · Cover×4 · History) · **`All : 17 Trails` `3153:1083` (หน้า trail ครบ 17)** · **`Materials` `3159:1295`** (17 หัวข้อ + "Picture Cover Header All Trail") · **`Introduction (แก้ไขใหม่)` `3224:1529`** (4 หน้า: People and Places · Early Centres of Power · Rice and Spice · Animals, Folklore and Ecological Stewardship) |
| `3117:3314` | Master Page Version (ปรับแก้ไข:1) | 10 | 2,293 | 27 | **หน้าที่ลูกค้าแก้ตามคอมเมนต์ (D37)** — Home · Intro · Zone 1–3 (+banner ใหม่) · Zone 4 (หน้าคั่น + Section 54 ภาพสัตว์) · Zone 5 (3 หน้า) · detail ช้าง 15 (เหมือน Final) |
| `3101:609` | Page : Detail Zone4 ALL | 3 | 483 | 2 | detail วัตถุช้าง/timeline Zone 4 ทั้งชุด + Zone 5 `3246:4498` |
| `998:952` | Page 2.1  ALL GIF trails (ข้อมูลสำคัญ) | 36 | 7,359 | 162 | detail วัตถุทุก trail (เพิ่ม 21 frame จากเดิม 141) + **17 หน้า trail `3161:3921…4335`** + "ไฟล์ที่ไม่ใช้แล้ว" |
| `3224:1530` | Page 5 | 5 | 315 | 5 | 5 หน้า "Desktop — People and Places / … Reference match / Early Centres of Power / Rice and Spice / Animals, Folklore and Ecological Stewardship" (= ชุดเนื้อหา Introduction แก้ไขใหม่ · **ยังไม่ได้ spec** — DEFERRED) |
**สิ่งที่เปลี่ยนแล้วกระทบสเปกเดิม:** node id ข้อความใน detail ช้าง 15 frame ถูกแทนใหม่ทั้งหมด (§4.8.2) · หัวข้อ/caption 14/15 ต่างจาก seed · หน้า trail 17 หน้ามีแล้ว (§4.4) · banner Zone 1–3 ใหม่ (§4.3b) · Zone 5 หน้าหลักมีสเปกเต็มแล้ว (§4.11a) · **id ในสเปกนี้ที่ไม่มีในไฟล์ใหม่ = 64 จาก 369** (นับด้วยสคริปต์: ทุก `NNN:NNN` ในสเปก) — กลุ่มหลัก: (ก) **Intro `670:1215` → ย้ายเป็น `3117:3656` (Desktop - 1471 · 1440×3186 เท่าเดิม)** · `670:1221…1237` = ลูกของ Intro (ถูกแทน) (ข) **Timeline `670:1551` → `1232:632` / `3101:611` (Desktop - zone 6)** และ node ข้างใน `1232:17xx` (การ์ด/ภาพ raster) ถูกแทน — เป็นของ design-dev timeline (§4.5c) · `797:2110` แถบไอคอน (ค) text node ของ detail ช้าง `670:2796…2813` `956:976…980` `964:1522…1808` `1057:1177` (§4.8.2) · (ง) page เก่า `263:362` `3004:4156` `669:1259` · **frame ที่ยังอยู่: Home `670:1152` (และสำเนา `3117:3607`) · Zone `670:1241/1296/1349` · Trail ช้าง `796:1795` · detail 15 frame (`670:2786` …)** · **ไม่ได้ diff เนื้อหา** Home/Intro/Zone ทั้งหน้า ยกเว้นที่ระบุใน §3.4 §3.5 §4.3b §4.4 §4.8.2

**mobile frame:** ไม่มี (D5: ไม่บังคับ) · Figma มี tablet 768 เฉพาะ style guide ("TBD")

### 1.2 Frame หลักของ page Final (1440 กว้างทั้งหมด) + ไฟล์ภาพ
`design/frames/*.png` ที่ **scale 1** · ⚠ ภาพที่ export ไว้แล้วกว้าง 1448 (เงา header ขยาย bounds: **x_png = x_figma + 4 · y_png = y_figma + 3** (วัดจริง: ภาพครอปเทียบ PNG แล้ว diff ต่ำสุดที่ offset (4,3) ทุกภาพที่ทดสอบ)) — **ต้อง re-export ด้วย `use_absolute_bounds=true` เพื่อ screenshot diff แบบพอดี 1440** · ติด Figma image-render rate limit (Retry-After 398,727 s ≈ 4.6 วัน ≈ **2026-11-01**) → DEFERRED #1 · สคริปต์ `figma-fetch.sh` ใส่พารามิเตอร์แล้ว รันซ้ำได้เลยเมื่อพ้นเวลา

| node id | ชื่อใน Figma | บทบาท | ขนาด | ไฟล์ภาพ (png) |
|---|---|---|---|---|
| `670:1152` | Desktop - 1470 | **Home** | 1440×3222 | `home-1470.png` |
| `670:1215` | Desktop - 1471 | **Introduction / Knowing Southeast Asia** (หน้า "Read more" ของ intro) + map slider | 1440×3186 | `intro-1471.png` |
| `670:1241` | Desktop - 1472 | **Zone 1** (Animals in Nature & Ecological Wisdom) กริด trail 5×3 | 1440×1646 | `zone1-1472.png` |
| `670:1296` | Desktop - 1473 | **Zone 2** (Beyond the Visible…) | 1440×1646 | `zone2-1473.png` |
| `670:1349` | Desktop - 1474 | **Zone 3** (Material Cultures…) | 1440×1629 | `zone3-1474.png` |
| `796:1795` | Desktop - 1501 | **Trail page — Elephant's Trail** (กริดวัตถุ 5×3) | 1440×1632 | `trail-elephant-1501.png` |
| `670:1551` | Desktop - zone 4 | **Timeline แนวนอน (trail ช้าง)** = หน้า Zone 4 "Animal Trails: Reflections & Interpretation" · เนื้อหากว้าง ~11,200 | 1440×1582 (เนื้อหาล้นถึง 11,197) | `timeline-zone4.png` (11197×1585) |
| `1232:632` | Desktop - zone 6 | สำเนาของ zone 4 (ไม่มีการอ้างถึง) | 1440×1549 | `timeline-zone6.png` |
| `670:2786` `670:2802` `670:2828` `670:2847` `956:975` `670:2872` `3004:804` `961:1024` `964:1522` `964:2090` `1006:561` `1041:866` `1006:922` `1041:1290` `1042:1719` | Desktop - 1468 · 1476 · 1483 · 1484 · 1503 · 1493 · 1512 · 1504 · 1505 · 1506 · 1508 · 1509 · 1507 · 1510 · 1511 | **Detail วัตถุ (ช้าง) 15 ชิ้น** | 1440×1738…2674 | `detail-final-elephan-<เลข>.png` (15 ไฟล์) |
| `1220:3036` … | Desktop - 1620 … (Page 2.1) | detail วัตถุ trail อื่น (141 frame) — export แล้ว 3 (`detail-p21-water-bird-1620/1621/1622.png`) ที่เหลือ **รอ rate limit** (รายการใน `design/detail-frames.list`, สคริปต์ `design/export-detail-frames.sh`) | 1440×1562…2847 | `detail-p21-<trail>-<เลข>.png` |
| `670:861` | Section 2 (style guide) | tokens + component | 5262×2689 | `sec-tokens.png` (0.25x ดูเฉย ๆ) |
| ส่วนอื่น | Section 3 sub-sections (Action Zone/Trails, Navigator, Detail, Section 50), Section 11 (popup), Section 4/29/49 (component), Section 43 (animation), Elephan tail | อ้างอิง | — | `sec-*.png` (0.25x) |

**รายการ detail frame ทั้งหมด (นับจากไฟล์):** 156 frame = 15 (Final) + 141 (Page 2.1) → **120 record วัตถุที่ไม่ซ้ำ** (15 ชิ้นอยู่หลาย trail · ระบุซ้ำด้วย ชื่อ+caption) · แยกตาม trail ดู §4.6 · ข้อมูลครบใน `content/seed/objects.json`

---
## 2. Tokens

### 2.1 สี (hex ตรงจาก Figma · ไม่มี Figma variables สำหรับสีแบรนด์ — ค่า zone/tile ได้จาก pixel ของ render เพราะภาพเป็น raster)
**Neutral (style "Neutral 2/…", ใช้จริง):**
| token | hex | ใช้ที่ |
|---|---|---|
| neutral-900 | `#212529` | ข้อความหลัก (หัวเรื่อง/เนื้อหา/Read more) |
| neutral-800 | `#343A40` | ข้อความเมนู/ปุ่ม Back/caption |
| neutral-700 | `#495057` | หัวเรื่อง detail บางชิ้น |
| neutral-600 | `#6C757D` | collection (detail บางชิ้น) |
| neutral-500 | `#ADB5BD` | เส้นขอบ navigator dot |
| neutral-400 | `#CED4DA` | — |
| neutral-300 | `#DEE2E6` | พื้นปุ่มเลขแผนที่ · เส้นขอบ trail page |
| neutral-200 | `#E9ECEF` | placeholder "Picture map" |
| neutral-100 | `#F8F9FA` | ข้อความบนพื้นเข้ม · พื้นปุ่ม Back ตอน hover |
| black / white | `#000000` / `#FFFFFF` | พื้นหน้า = `#FFFFFF` |
| (style sheet อื่น) | `#1E1E1E` `#434343` `#8E8E93` `#8C8C8C` | ปรากฏในแผ่น style guide เท่านั้น |

**แบรนด์/accent:** UNESCO blue `#0069B4` (โลโก้ vector) · accent coral `#D65F47` (Read more hover · ปุ่มเลขแผนที่ active · ป๊อปอัพแผนที่ · วงกลม glow ปุ่ม Back `#D65F47` opacity 0.40 · พื้น active ของแถบ zone nav `#D65F47` opacity 0.20) · **Header #02 = พื้น `#FFFFFF` ไม่มีเส้นขอบ (stroke `#AACFD8` ใน node เป็น `visible:false`) มีแค่ Drop Shadow/100 · Header #1 = พื้น `#FFFFFF` + ภาพพื้น มี Drop Shadow/100 และ **เส้นล่าง `#AACFD8` หนา 1.5 INSIDE เฉพาะขอบล่าง (individualStrokeWeights top0/right0/bottom1.5/left0) ทุกหน้า ยกเว้น Home (`670:9386`) และ Intro (`699:11020`) ที่ไม่มีเส้น** (21 จาก 23 instance ของ page Final มีเส้น: ทุก detail 15 frame · zone 1-3 · trail 1501 · timeline) · fill ครีม `#F1F1E5` ของ `Frame 12789` (Header #02) เป็น `visible:false` ในทุก instance (670:9756, 670:9771, 796:1846 ฯลฯ) และ stroke/fill ของ component `670:1143` เองก็ `visible:false` — พื้นครีม `#F1F1E5` ที่แสดงจริงมีที่ `670:1144` (Frame 12789 ใน component ต้นแบบ) และ `Frame 114477 [160,86 1120×64]` ใน Home ซึ่งถูก Header #02 (ขาวทึบ 1440×64) ทับมิด → ใน PNG แถบ y 86–149 เป็นสีขาวล้วน

**Zone (3 ธีมหลัก = Zone 1/2/3 · + Zone 4/5)** — จาก pixel แถบ zone ใน render (`zone*-14xx.png`, `home-1470.png`):
| zone | ชื่อ | active `hex` | inactive (แถบจาง) |
|---|---|---|---|
| 1 | Animals in Nature & Ecological Wisdom | `#14705E` | `#D0E2DF` |
| 2 | Beyond the Visible: Animal Lore & Cosmic Order | `#D6A2BA` | `#EFDAE3` |
| 3 | Material Cultures: Animals in Craft & Ritual | `#E6553D` | `#F5BBB1` |
| 4 | Animal Trails: Reflections & Interpretation | `#EC7966` | `#FADDD8` |
| 5 | The Curious Minds’s Play Zone | `#4BBAA3` | `#B7E3DA` |
พื้นภาพ zone cover: Zone1 `#A4927A` (น้ำตาลหม่น) · (cover เป็นภาพ raster ทั้งใบ — สี/ข้อความฝังในภาพ)

**Trail tile background (กริด 184×184 ของหน้า zone):** green `#7EB7A6` · beige `#DEDCC3` · salmon `#F1ADA0` · mauve `#CFA5BA` · (tile ช้าง `#E1DEC6`/render `#E0DEC9`) — ต่อ trail ดู `content/seed/trails.json` `tile_bg`
**Trail icon group (ไอคอน nav ใน timeline 35×35):** green `#168E73` · red-orange `#E7553C` · mustard `#C3BC85` · purple `#B36F94`
**พื้นแผง detail ต่อวัตถุ (ค่าต่างกันทีละวัตถุ — ไม่ใช่ต่อ trail):** `#E1DEC6` `#EEDFB4` `#D6A2BA` `#E2B3A7` `#B2BBAA` `#178C74` `#DEDCC3` `#E0DEC7` `#705F4D` `#EABF7A` `#F0ADA0` `#AACFD8` `#E2C675` `#B3B3B3` `#BDBDB0` `#CFA5BA` `#363638` `#F7EFDC` `#6B5837` `#8E9FA6` `#DBD3AC` `#D75F47` `#B4C5D4` `#C0885F` (24 สี) · สีหัวเรื่อง/ตัวอักษรคู่กัน: `#FFFFFF` `#D75F47` `#534741` `#413B2F` `#495057` `#343A40` `#617250` `#C2BC8A` `#F8F9FA` · เก็บเป็นฟิลด์ต่อวัตถุ (`detail_style`) ใน seed → ดู §5.2
**เงา:** "Drop Shadow/100" = `0 1px 4px 0 #0C0C0D @ 5%` (header #1/#02) · "Drop Shadow/200" = `0 1px 4px #0C0C0D@5%` + `0 1px 4px #0C0C0D@10%` (การ์ดรูปมน r22 ใน intro/home)
**ข้อมูลสีที่ไม่มีใน Figma → PENDING:** สีพื้นหลังหน้า = `#FFFFFF` ทุกหน้า (ไม่มี theme พื้นหลังต่อ zone)

### 2.2 Typography (family · size · weight · line-height · letter-spacing · case · สี — ตรงจาก Figma · letter-spacing = 0 และ text-case = ORIGINAL ทุก style ที่ใช้งาน · ไม่มี italic)
| ระดับ | family / PostScript | size px | weight (ตาม Figma) | line-height px | align | สี | ใช้ที่ |
|---|---|---|---|---|---|---|---|
| Page/Section title | Komet / Komet-Heavy | 36 | 800 | 45 | left | `#212529` | Introduction · Knowing SEA · Elephant's Trail · Reflection · ชื่อวัตถุ (detail) |
| Section title (center) | Komet-Heavy | 34 | 800 | 42.5 | center | `#212529` | "Trails" (home) |
| Detail title (พิเศษ) | Komet-Heavy | 40 | 800 | 50 | left | `#D75F47` | frame 1479 (วัตถุเดียว) |
| Card title | Komet-Heavy | 20 | 800 | 25 | center (zone) / left (timeline) | `#212529` | zone card · timeline card |
| Lead/tagline | Komet-Regular | 20 | 500 | 22 | center | `#000000` | "Step beyond traditional gallery walls…" |
| Body (home) | Komet-Regular | 18 | 500 | 22 | left | `#000000` | intro / trails paragraphs ใน Home |
| Body (intro page) | Komet-Regular | 18 | 500 | 24 | left | `#212529` | หน้า 1471 (และ article) |
| Caption (Final) | Komet-Light | 18 | 400 | 24 | left | `#343A40`(หรือ `#413B2F`/`#495057` ตามแผง) | detail (Final) |
| Caption (Page 2.1) | Komet-Bold | 18 | 700 | 24 | left | ตามแผง | detail 119/141 frame (นับ node caption) — P-206 |
| Collection | Komet-Regular 20/25 w500 (Final) · Komet-Bold 20/25 w700 (2.1 ส่วนใหญ่) · Barlow Condensed SemiBold 20/24 w600 (17 frame) | 20 | 500/700/600 | 25/24 | left | ตามแผง | P-206 |
| Metadata block (Final เก่า) | Komet-Bold 24/30 w700 หรือ Komet-Medium 24/30 w600 | 24 | 700/600 | 30 | left | `#343A40` | detail ที่มี Section/Period/… (P-206) |
| Timeline card meta | Komet-Regular / Komet 14 | 14 | 500 | 24 (บางอัน 17.5) | left | `#212529` | การ์ด timeline |
| Nav (Header #02/#1) | Komet-Medium | 14 | 600 | 17.5 | left | `#343A40` | เมนู |
| Read more (Home) | Komet-Regular | 16 | 500 | 22 | right/center/left ตามที่ | `#212529` | ปุ่มลิงก์ Home |
| Read more (หน้าอื่น) | Barlow Condensed-Regular | 20 | 400 | 22 | right | `#212529` | intro/trail/timeline |
| Read more hover (Home) | Komet-Bold | 16 | 700 | 22 | เดิม | `#D65F47` + underline | hover (§3.4) |
| Back button | Roboto Flex / RobotoFlex-Regular | 20 | 400 | 23.4375 | left | `#343A40` | ปุ่ม Back |
| Map number / pin | Barlow Condensed-SemiBold | 14 | 600 | 16.8 | center | `#212529` (active `#D65F47`) | ปุ่มเลข 1-4 |
| Map popup title | Barlow Condensed-SemiBold | 16 | 600 | 19.2 | left | `#212529` | popup |
| Map popup body | Barlow Condensed-Regular | 14 | 400 | 16.8 | left | `#212529` | popup |
| Side-nav labels | Komet-Regular | 18 | 500 | 22.5 | center | `#212529` | "Intro/Zone/Trails" (ตัวเก่า — P-210) |
| Placeholder map | Inter Light/SemiBold | 35 | 300/600 | 42.36 | left | `#000` | "Picture map 1120x630 px" (placeholder) |
| Style guide (ไม่ใช้บนเว็บ) | Prompt (EN/TH) 10–60 | — | 300–700 | — | — | — | แผ่น style guide เท่านั้น |

**ฟอนต์ + แหล่ง:**
| ฟอนต์ | ใช้ | แหล่ง | สถานะ |
|---|---|---|---|
| **Komet** (Light/Regular/Medium/Bold/Heavy) | เนื้อหาหลักทั้งหมด | ฟอนต์เสียเงินจากผู้จำหน่าย — **ไม่มีไฟล์ใน Figma export/ไม่มีใน repo** | 🔴 **P-205** ต้องได้ไฟล์ + ไลเซนส์เว็บ (woff2) จากเจ้าของ · map น้ำหนัก: Figma weight 400=Light, 500=Regular, 600=Medium, 700=Bold, 800=Heavy (ชื่อ PostScript ตัดสิน ไม่ใช่เลข 400/500) |
| Barlow Condensed (Regular 400/Medium 500/SemiBold 600/Bold 700) | Read more · ปุ่มเลข · popup · collection | Google Fonts (OFL) | ใช้ได้ self-host |
| Roboto Flex (Regular 400) | ปุ่ม Back | Google Fonts (OFL) | ใช้ได้ self-host |
| Inter / Prompt | placeholder + style guide | Google Fonts | ไม่จำเป็นบนเว็บจริง |
(ห้ามฝัง CDN — self-host ตาม CONVENTIONS)

### 2.3 Spacing / grid / ขนาด
- **Layout grid desktop (frame 1440):** 6 คอลัมน์ · คอลัมน์ 170 · gutter 20 · offset (margin ซ้าย/ขวา) **160** → **container กว้าง 1120 ที่ x=160** (Figma style guide ระบุ "6 columns — center wide 20px gutter, Margin 24px, 1440px wide" แต่ค่าที่ใช้จริงใน frame คือ offset 160, 12 คอลัมน์ในแผ่น `670:969`)
- Tablet (style guide): 6 คอลัมน์ · gutter 20 · margin 24 · 768 กว้าง · "TBD"
- กฎ: "ใช้พหุคูณ 5 หรือ 10" · gap ที่ใช้จริง: 1, 5, 10, 12, 15, 16, 20, 22, 25, 30, 40, 50, 60, 80
- Padding แผง detail: `160/160` ซ้าย-ขวา · top 60 · bottom 60 (บางแบบ 80) · gap คอลัมน์ 22
- กริด tile: **184×184**, ช่องไฟ 50 (x = 160, 394, 628, 862, 1096; แถว y +234) · hover → 210×210
- Header: Header #1 สูง 86 (y 0–86) · Header #02 สูง 64 (y 86–150) · เนื้อหาเริ่ม y=150

### 2.4 Radius / border / shadow / opacity
| ชนิด | ค่า |
|---|---|
| การ์ดรูป intro/home/zone banner | radius **22** + Drop Shadow/200 |
| ปุ่ม Back | radius **5** |
| popup แผนที่ | radius **4** (พื้น `#D65F47`) |
| ปุ่มเลขแผนที่ | **34×34** radius **20** (พื้น `#DEE2E6`; active: stroke `#D65F47` 1 inside) |
| navigator dot | วงกลม 30×30 stroke `#ADB5BD` 2 inside พื้น `#FFFFFF` |
| Header #1 | พื้น `#FFFFFF` + ภาพ · **เส้นล่าง `#AACFD8` 1.5 inside (เฉพาะขอบล่าง) ทุกหน้ายกเว้น Home `670:9386` / Intro `699:11020`** · Drop Shadow/100 |
| Header #02 | พื้น `#FFFFFF` · **ไม่มี stroke** (`#AACFD8` ใน node = visible:false) · Drop Shadow/100 |
| trail page frame | stroke `#DEE2E6` 1 inside |
| ปุ่ม Back พื้น | `#000000` opacity 0.10 |
| glow ปุ่ม Back | วงกลม `#D65F47` opacity 0.40 |
| เวลา transition (Figma prototype) | hover tile 0.3s ease-in-out smart-animate · hover zone card/Back 0.4s · page nav dissolve 0.3–0.4s · scroll-to 0.3–0.5s · แอนิเมชันรอยเท้า after-timeout 0.1s + 0.4s |

---
## 3. Component กลาง (ใช้ซ้ำทุกหน้า)

### 3.1 Header #1 (บน) — `670:9386` ใน Home · component set `670:1057`
`[0,0 1440×86]` fill `#FFFFFF` + **ภาพพื้น** (IMG `709572bf` — มีโลโก้ TEMASEK FOUNDATION และ SHARED HERITAGE · MUSEUMS IN SOUTH-EAST ASIA แบบ raster) · **stroke `#AACFD8` 1.5 INSIDE เฉพาะขอบล่าง (individualStrokeWeights bottom 1.5) แสดงจริงทุกหน้า ยกเว้น Home `670:9386` และ Intro `699:11020` (ไม่มีเส้น)** · Drop Shadow/100 · auto-layout แนวนอน padding-left/right **160**, space-between, กลางแนวตั้ง
- โลโก้ UNESCO (vector 4 path, `#0069B4` + `#FFFFFF`) `[160,14 171×36]` → `design/assets/svg/unesco-logo.svg`
- เมนู utility ขวา `[478,13 484×38]` (ข้อความ y=23) ข้อความ 14/17.5 Komet-Medium `#343A40` gap 30: `Visit` · `Exhibition and Events` · `Art` · `Research` · `About` · `Contact` (มีตำแหน่ง x ใน `layout/comp-header1.md`)
  ⚠ โลโก้ TEMASEK + SHARED HERITAGE ฝังในภาพพื้น (ไม่แยกชิ้น) — ใช้ภาพ `design/assets/709572bf7eca….jpg` ทั้งแถบ
### 3.2 Header #02 (เมนูหลัก) — `670:9756` · set `670:1142`
`[0,86 1440×64]` fill `#FFFFFF` · **ไม่มี stroke ที่แสดง** · Drop Shadow/100 · auto-layout padding 160/160, ชิดขวา · `Frame 12789 [160,86 1120×64]` **ไม่มี fill ที่แสดง** (fill ครีม `#F1F1E5` = visible:false ในทุก instance; พื้นครีมที่แสดงจริงมีที่ `670:1144` ใน component ต้นแบบ — stroke/fill ของ `670:1143` เองก็ `visible:false`) · แถว `Frame 12653` padding 10/10 gap **30** ชิดขวา
เมนู (Komet-Medium 14/17.5 `#343A40`): `Main Page` (x≈580) · `Shared Heritage Framework` · `Museums and Partnership` · `Index` · `Events and Activities` (สิ้นสุด x=1280)
### 3.3 ปุ่ม Back — `Button Back B1` (component set `670:1810`)
default: `[160,y 115×50]` พื้น `#000000`@10% · radius 5 · padding 20/20/8/8 · gap 15 · clip · ไอคอนลูกศร (`back-arrow.svg` 10.43×18.03 สี `#343A40` หมุน 180°) + ข้อความ "Back" Roboto Flex 20/23.4375 `#343A40` · วงกลม glow 142×142 `#D65F47`@40% ที่ (-104,-100) เทียบปุ่ม (โผล่มุมซ้ายบน)
hover (variant `Frame 12770`): พื้น `#F8F9FA` · วงกลม glow ขยายเป็น 212×212 ที่ (148-203=-55… ครอบทั้งปุ่ม) · 0.4s smart-animate · คลิก = BACK (ย้อนหน้า)
### 3.4 Read more
- Home (`Read more 1/3`): Komet-Regular 16/22 `#212529` → hover Komet-Bold 16/22 **underline `#D65F47`** (variant2)
- หน้าอื่น: Barlow Condensed Regular 20/22 ขวา `#212529` (hover ไม่มี variant แยกใน Figma — ใช้พฤติกรรมเดียวกับ Home: เป็น P-2xx ย่อย ไม่เปิดเพิ่ม)
- ข้อความ UI: `Read more >>`
- **อัปเดต 2026-11-11 (BACKLOG #4 · Figma `3117:3314`):** component `Read more 3` (`886:1826`; ในหน้า `3117:4321`) มี 2 สถานะ — default **"Read more >>"** ↔ ขยายแล้ว **"Hide >>"** (`I3117:4318;3117:4321` · Komet 16/22 (500) กึ่งกลาง `#212529` · **ไม่มี underline** · กล่อง 58×22) · คลิก "Read more >>" → ขยาย + ตัวหนังสือปุ่มเปลี่ยนเป็น "Hide >>" · คลิก "Hide >>" → ยุบกลับ · smart-animate **0.3s** · hover (ทั้งสองสถานะ) = ตัวหนา 700 + underline `#D65F47` (`3117:4322`) · ใช้กับ: trails Home (`trails Detail text` set `3117:4306`: ยุบ 127px → ขยาย 277px · Hide อยู่ y 255; ย่อหน้าที่ขยาย 18/22 ชิด**ซ้าย** 2 ย่อหน้า 44+66px) และ Zone 1–3 (§4.3b)
### 3.5 Navigator (จุดข้างหน้า Home)
**ตัวจริง (ตามหมายเหตุ Figma `929:1597`/ภาพ `annot-navigator.png`):** วงกลม 4 ดวง ไม่มี icon/ข้อความ — `Navigator [176,587 30×150]` แนวตั้ง gap 10 · แต่ละดวง 30×30 พื้น `#FFFFFF` stroke `#ADB5BD` 2 · คลิกเลื่อนไป: 1) Introduction (`670:9368`) 2) Zone 1-3 (`670:9479`) 3) Trails (`886:1795`) 4) Zone 4-5 (`670:9541`) · scroll-animate 0.3s
**ยืนยันใน Figma ใหม่ 2026-11-11 (BACKLOG #3 · `3117:3638` ใน Home `3117:3607`):** `Navigator [176,587 30×150]` · 4 ดวง 30×30 gap 10 (y = 587/627/667/707) · พื้น `#FFFFFF` · stroke **2px `#ADB5BD`** · ไม่มีไอคอน/ข้อความ · ON_CLICK → SCROLL_TO (scroll-animate **0.3s**): 1 → Cover หน้าแรก `3117:3609` (บนสุดของหน้า) · 2 → Zone 1–3 `3117:3629` (`Frame 114481`) · 3 → Trails `3117:3622` (`Frame All trails`) · 4 → Zone 4–5 `3117:3625` (`Frame 12792`) · ภาพหมายเหตุของลูกค้า: `design/assets/2208bf9c….png` (ข้อความ "ขอเป็น 4 วงกลมขนาดประมาณในภาพครับ ไม่ต้องมี icon หรือ wording ข้างใน")
(ตัวเก่า `Frame 12744 [1222,256 58×194]` วงกลม 58 พร้อมข้อความ Intro/Zone/Trails — ขัดกับหมายเหตุ → ไม่ใช้ · P-210)
### 3.6 Trail tile (กริด 184) + hover
component set `Bird 1/Buffalo/…` (`1018:1372…`): default `184×184` (fill ภาพ FILL, clip) → hover `210×210` (ภาพคนละไฟล์: ภาพ "พื้นสี+วัตถุ" ขยาย) · 0.3s ease-in-out smart-animate · ช่องไฟ 50
### 3.7 Zone card + hover
`Zone 1/2/3` `373.333×553` (รูป `373.333×487` + gap 16 + ข้อความ 50) · Zone 4/5 `560×360` (รูป `560×294` + gap 16 + ข้อความ 50/25) · hover (variant `Frame 12789`): รูปเปลี่ยนไฟล์ (annotation `670:1586`: "Mouse Hover จะเกิดการเปลี่ยนของรูป") · 0.4s smart-animate · คลิก → หน้า zone (0.4s smart-animate)

---
## 4. โครงต่อหน้า (ลำดับ section · พิกัด [x,y w×h] เทียบ frame 1440)

### 4.1 Home — `670:1152` (1440×3222) · ภาพ `home-1470.png` · layout dump `layout/home-1470.md`
| # | section | พิกัด | รายละเอียด |
|---|---|---|---|
| — | Header #1 | `[0,0 1440×86]` | §3.1 |
| — | Header #02 | `[0,86 1440×64]` | §3.2 |
| — | แถบ cream `Frame 114477` (Home เท่านั้น) | `[160,86 1120×64]` fill `#F1F1E5` padding 10 | **มองไม่เห็นใน PNG** — ถูก Header #02 (ขาวทึบ 1440×64) ทับสนิท · ไม่ต้องสร้าง |
| 1 | **Hero cover** `Cover` | `[160,150 1120×466]` | ภาพ raster ใบเดียว (IMG `2e3fa9c4`) — ข้อความ **"ALONG ANIMAL TRAILS / Creations that Bind South-East Asia"** ฝังในภาพ (P-207) |
| 2 | **Introduction** `Frame 12685` | `[160,616 1120×552]` padding บน/ล่าง 40 · gap 22 | แถว `Frame 114468 [160,656 1120×284]` gap 20: ซ้าย การ์ดรูป `[160,656 551×284]` r22 + shadow200 (IMG `3f4cfd00`) · ขวา ข้อความ `[731,656 549×281]` gap 20: หัวเรื่อง "Introduction" 36/45 Heavy · ย่อหน้า 1 `[731,721 549×88]` 18/22 `#000` · ย่อหน้า 2 `[731,829 549×66]` · "Read more >>" `[731,915 549×22]` Komet 16 ขวา |
| 3 | **Zone bar** `Frame 12684` | `[160,962 1120×166]` r22 + shadow200 | ภาพ 1120×166 (IMG `30d9d301`) — แถบ ZONE 1–5 (สีตาม §2.1) + สัตว์วิ่ง; ข้อความ "ZONE n" ฝังในภาพ |
| 4 | **Zone 1-2-3 cards** `Zone 123` | `[160,1198 1120×553]` แนวนอน gap 0 | 3 การ์ด `373.333×553` ที่ x=160 / 533.3 / 906.7: รูป `373.333×487` + gap 16 + ชื่อ 20/25 Heavy center `#212529` (`Animals in Nature \n& Ecological Wisdom` · `Beyond the Visible: \nAnimal Lore & Cosmic Order` · `Material Cultures: \nAnimals in Craft & Ritual`) |
| 5 | **Trails** `Frame 114476` (gap 32) → `Frame All trails` | `[160,1766 1120×777]` gap 20 | `trails Detail text [160,1766 1120×127]` gap 20: "Trails" 34/42.5 center · tagline 20/22 center · (ย่อหน้ายาว 2 อัน 18/22 `[160,1873]`,`[160,1937]` — ซ่อนหลัง "Read more >>" `[160,1871 1120×22]` center) · ใต้ข้อความ: **AAMM -01** `[160,1913 1120×630]` = พื้นที่แอนิเมชันรอยเท้า → โชว์ไอคอน trail 17 ตัว (§6.2) |
| 6 | **Zone 4 + Zone 5** `Frame 12792` | `[160,2575 1120×360]` แนวนอน | `Zone 4 [160,2575 560×360]` · `Zone 5 [720,2575 560×360]` (§3.7) |
| — | Navigator (4 จุด) | `[176,587 30×150]` | §3.5 |
| — | ความสูงที่เหลือท้ายหน้า | 2935→3222 (287px) | ไม่มี footer ใน Figma (P-211) |
**ข้อความ UI ทั้งหมดของ Home:** ดู `content/seed/site.json` (`home`) — คัดลอกตรง

### 4.2 Introduction / Knowing Southeast Asia — `670:1215` (1440×3186) · `intro-1471.png` · `layout/intro-1471.md`
| section | พิกัด | รายละเอียด |
|---|---|---|
| Header #1/#02 | `[0,0]`/`[0,86]` | §3 |
| Cover | `[160,150 1120×466]` | ภาพเดียวกับ Home hero (IMG `2e3fa9c4`) |
| Introduction block `Frame 12685` | `[160,636 1120×704]` padding 20/20 gap 20 | แถว `[160,656 1120×284]` gap **22**: รูป `[160,656 549×284]` r22 shadow200 (ภาพ `bec4f086`) · ข้อความ `[731,656 549×263]` padding-bottom 10 gap 20: "Introduction" 36/45 · ย่อหน้า 18/**24** `#212529` ×2 · Read more (Barlow 20/22 ขวา) `[731,891]` · แล้ว body ยาว `670:1227` `[160,960 1120×360]` 18/24 |
| Knowing SEA `Frame 12717` | `[160,1360 1120×635]` padding 20/20 gap 20 | แถว `[160,1380 1120×287]` gap 22: ข้อความซ้าย `[160,1380 549×287]` ("Knowing Southeast Asia" 36/45 + 2 ย่อหน้า + Read more) · รูปขวา `[731,1380 549×284]` r22 (ภาพ `74e1d6fd`) · แล้ว body ยาว `670:1237` `[160,1687 1120×288]` |
| Map slider `Frame 12767` | `[160,2015 1120×951]` gap 20 | แผนที่ `Frame 12655 [160,2015 1120×620]` พื้น `#F2F2F2`+ภาพ `39907d2c` (ในดีไซน์ = placeholder "Picture map 1120x630 px") · ปุ่มลูกศรซ้าย `[160,2285 60×80]` / ขวา `[1220,2285 60×80]` (padding 15/15/10/10, ไอคอน 30×60 `#343A40`) · เลข 1-4 `[1092,2037 166×34]` (34×34 gap 10, active stroke+ตัวเลข `#D65F47`) · popup `[841,2078 336×251]` พื้น `#D65F47` r4 padding 12 gap 15 (ใน frame ใช้ Inter + lorem — ตัวจริง Section 11 ใช้ Barlow Condensed ดู §4.7) · ใต้แผนที่: `Frame 12683 [160,2655 1120×311]` padding-bottom 10 gap 20: หัวเรื่อง 36/45 (เช่น ": People, Places, and Shared Histories") + 2 ย่อหน้า 18/24 + Read more `[160,2912]` |
| ปุ่ม Back | `[160,3055 115×50]` | §3.3 |
slide 4 ชุด: ข้อความในหัวข้อ ": People, Places, and Shared Histories" / ": Early Centres of Power" / ": Rice and Spice" / ": Animals and Ecosystems" (คัดลอกตรง รวม ": " นำหน้า) → `content/seed/articles.json`

### 4.3 Zone 1/2/3 — `670:1241` / `670:1296` / `670:1349` (โครงเหมือนกัน เปลี่ยนภาพ cover + ภาพ banner + tab active)
| section | พิกัด (zone 1) | รายละเอียด |
|---|---|---|
| Header #1/#02 | `[0,0]`,`[0,86]` | |
| Cover | `[160,150 1120×410]` | ภาพ (IMG `10247f33` zone 1) — ชื่อ zone ฝังในภาพ ("Animals in Nature & ecological wisdom" ฯลฯ) |
| Zone banner `Frame 12684` | `[160,579 1120×166]` r22 + shadow200 | ภาพแถบ ZONE 1–5 (active zone เข้ม, อื่นจาง) + ไอคอนสัตว์ประจำ zone เหนือแถบ (ภาพ `413791d6`) · zone 3 สูงต่างกัน (1629) |
| กริด trail `Frame 114472` | `[0,795 1440×652]` padding 160/160, แนวตั้ง gap **50** | 3 แถว × 5 tile 184×184 (gap 50) ที่ y = 795 / 1029 / 1263 · tile 15 ตัว (ดู §4.6 รายการ; **water-bird และ elephant ไม่อยู่ในกริดนี้** — P-212) |
| ปุ่ม Back | `[160,1521 115×50]` | |
**หมายเหตุ:** กริด 15 tile ภาพ/ลำดับเหมือนกันทั้ง 3 zone ใน Figma (ต่างแค่ cover/banner) → ความสัมพันธ์ trail↔zone ยังไม่ระบุ (P-212)

### 4.3b Zone 1/2/3 — **อัปเดต 2026-11-11** (คอมเมนต์ลูกค้า · BACKLOG #7 #8 · Figma page `3117:3314` "Master Page Version (ปรับแก้ไข:1)" · lastModified ไฟล์ `2026-11-11T08:23:07Z`) — **ทับ §4.3 เฉพาะที่ระบุ** (กริด 15 tile/Header/Back/ภาพแถบ ZONE ไม่เปลี่ยน)
frame ใหม่: `3117:3684` (zone 1, Desktop - 1472) · `3117:3713` (zone 2, 1473) · `3117:3742` (zone 3, 1474) · 1440×**1930** (สถานะย่อ) · ภาพ render `design/frames/zone{1,2,3}-3117-147{2,3,4}.png`

| section | พิกัด (เทียบ frame 1440) | รายละเอียด |
|---|---|---|
| Header #1/#02 | `[0,0]`,`[0,86]` | เหมือนเดิม |
| Cover (banner) | `[160,150 1120×410]` | **ภาพ banner ใหม่ 3 ใบ** (ตัวหนังสืออยู่ในภาพ — ไม่ต้องวางข้อความทับ) · ไฟล์ 4096×1503 (**3.66×**) ที่ `design/assets/zone-banners-2026-11/zone1-animals-in-nature-and-mythology.png` · `zone2-beyond-the-visible-animals-and-cosmic-order.png` · `zone3-material-cultures-animals.png` (ชื่อไฟล์ = ref ต้นฉบับ `57fe08a9…` / `262bafe2…` / `1fda5191…` ในโฟลเดอร์ `design/assets/` ด้วย) |
| แถบ ZONE 1–5 `Frame 12684` | `[160,580 1120×166]` r22 + shadow | ภาพ raster เดิม (zone 1 = `413791d6` · zone 2 = `e5dbe735` · zone 3 = `172ff28b`) |
| **บล็อกข้อความ (ใหม่)** `Detail text Zone 1` (component set `3117:5051`) | `[160,826 1120×H]` H = 193 (zone 1 ย่อ) / 149 (zone 2 ย่อ) / 193 (zone 3 ย่อ) · ขยาย 303 / 523 / 347 | แนวตั้ง gap **20** · **หัวข้อ** Komet-Heavy **34/42.5** (800) กึ่งกลาง `#212529` · **ข้อความ** Komet 20/22 (500) กึ่งกลาง `#000000` (ย่อหน้า = ขึ้นบรรทัด 1 ครั้ง ไม่มีช่องว่างย่อหน้า) · **Read more** Komet 16/22 (500) กึ่งกลาง `#212529` |
| กริด trail `Frame 114472` | `[160, 826+H+60]` 1120×652 | 3 แถว × 5 tile 184² gap 50 · ลำดับ tile เดิม (Bird · Buffalo · Chicken · Deer · Dragon / Fish · Garuda · Hornbill · Horse · Lion / Makara · Phoenix · Seashell · Serpent · Turtle) |
| ปุ่ม Back | `[160, กริดล่าง+74]` (zone 1: y=1805) | ตำแหน่งใน Figma zone 2 = 1805 (ควรเป็น 1761) และ zone 3 = 1817 → **ไม่สม่ำเสมอ — ใช้กฎ "กริดล่าง+74"** (P-243) |

**พฤติกรรม Read more (ใหม่)** — คลิก "Read more >>" → บล็อกขยายเป็นสถานะเต็ม (variant `Zone 1.1` / `Zone 2.1` / `Zone 3.1`: ข้อความเต็มแทนข้อความสั้น · ตัวอักษรปุ่มเปลี่ยนเป็น **"Hide >>"**) · คลิก "Hide >>" → ยุบกลับ (variant ย่อ) · smart-animate **0.3s** (zone 1 ระบุใน Figma · zone 2/3 ไม่ระบุ transition → ใช้ 0.3s เท่ากัน) · hover = ตัวหนา + ขีดเส้นใต้ `#D65F47` (variant `Property 1=Default` `3117:4322` = §3.4) · ทั้งสามหน้ากริด/Back เลื่อนลงตามความสูงที่เพิ่ม (flow)

**ข้อความ (คัดลอกตรงจาก Figma → `content/seed/zones.json` zones[1–3].`intro_short` / `body_full`)** — Figma ไม่มีตัวแปร "ข้อความสั้นแยก": ข้อความสั้น = ข้อความในสถานะย่อ, ข้อความเต็ม = สถานะขยาย (zone 1: ย่อ = 2 ย่อหน้าแรก · zone 2/3: ย่อ = ย่อหน้าแรก) · ข้อความที่ซ่อน (`visible:false`) 2 ย่อหน้า "In this borderless digital world…" / "Forget rigid physical galleries!…" ในสถานะย่อ = สำเนาจาก Home → **ไม่แสดง** · ข้อความ zone 1 สถานะขยายจบที่ "…the Garuda" **ไม่มีจุด** (ตามต้นฉบับ — P-242)

**ชื่อ zone — 3 รูปแบบใน Figma + ข้อเสนอว่าใช้ที่ไหน**
| zone | A: ในภาพ banner (สั้น) | B: หัวข้อในหน้า (`3117:5053/5067/5063`) | C: การ์ด Home ใน Figma (`3117:3633…`) — **ยังเป็นชื่อเก่า** |
|---|---|---|---|
| 1 | Animals in Nature & Mythology | Animals in Nature and Mythology | Animals in Nature & Ecological Wisdom |
| 2 | Beyond the Visible: Animals & Cosmic Order | Beyond the Visible: Animals and the Cosmic Order | Beyond the Visible: Animal Lore & Cosmic Order |
| 3 | Material Cultures Animals | Material Cultures: Animals in Rituals and Daily Life | Material Cultures: Animals in Craft & Ritual |

ข้อเสนอ (ตัดสินเอง · ถ้าไม่เห็นด้วยแก้ที่ seed ที่เดียว): **ชื่อ term zone (taxonomy) = B** · **H1 ในหน้า = B** (ตรงที่ Figma วางเป็นข้อความ) · **การ์ดหน้าแรก = B** (C เป็นชื่อเก่าที่ยังไม่ได้แก้ — P-241) · **alt ของภาพ banner = A** (ตรงตัวอักษรในภาพ) · แถบ ZONE = ภาพ raster ไม่มีข้อความ · ชื่อ zone 4/5 ไม่เปลี่ยน
**⚠ ภาพ banner สลับที่ใน Figma:** ใน frame zone 1 (`3117:3684`) ภาพ Cover (`262bafe2`) เขียนว่า "Beyond the Visible…" และใน frame zone 2 (`3117:3713`) เขียนว่า "Animals in Nature & Mythology" → **ผูกภาพตามชื่อที่อยู่ในภาพ** (zone 1 = `57fe08a9` · zone 2 = `262bafe2`) ตามคำสั่งเจ้าของ #7 · zone 3 ตรง (P-240)
banner Zone 4/5 ใหม่ใน tab เดียวกัน: **ไม่มี** (Zone 4 = Cover ที่เป็นภาพเส้นประ §4.10 · Zone 5 = หน้าหลักไม่มี banner กว้าง §4.11)

### 4.4 Trail page — Elephant's Trail `796:1795` (1440×1632, clip) · `trail-elephant-1501.png`
| section | พิกัด | รายละเอียด |
|---|---|---|
| Header #1/#02 | `[0,0]`,`[0,86]` | |
| Cover `Frame 12685` | `[0,150 1440×497.228]` padding 160/160 · stroke `#DEE2E6` 1 inside | ภาพ `Elephant_Zone2-1 1` `[160,150 1120×497.228]` (IMG `ed0e5ee9`) |
| หัวข้อ `Frame 12683` | `[160,677.2 1120×112]` แนวนอน gap 30 padding-bottom/top 10 กลางแนวตั้ง | "Elephant’s Trail" 36/45 Heavy `[160,710.7 257×45]` · คำอธิบาย `[~447,687 833×92]` Komet 18/24 · Read more (Barlow 20/22) |
| กริดวัตถุ `Frame 114472` | `[160,829.2 1120×652]` แนวตั้ง gap 50 | 3 แถว × 5 tile 184×184 gap 50 ที่ y = 829.2 / 1063.2 / 1297.2 · **15 วัตถุ** (ลำดับ §4.8) |
| ปุ่ม Back | `[160,1536 115×50]` | |
tile ช้างพื้น `#E1DEC6` (render `#E0DEC9`) ภาพ FILL · hover 210 (`Ele NN` variants)
~~trail อื่นอีก 16 trail ไม่มีหน้า trail ใน Figma~~ → **ล้าสมัย 2026-11-11:** ไฟล์ใหม่มีหน้า trail ครบ **17 หน้า** (ดูข้างล่าง · P-201 ส่วนนี้คลี่คลาย)

**อัปเดต 2026-11-11 (BACKLOG #6 "ลบเส้นแนวนอนใต้ภาพ hero")** — Figma `796:1795` (ชื่อใหม่ "Desktop - Elephant Trails" · ตอนนี้อยู่ใน section "All : 17 Trails" `3153:1083` ของ page `670:860`):
- `Frame 12685` เดิม = `[0,150 1440×497.228]` มี **stroke ล่างกว้าง 2px `#DEE2E6` ทั้งความกว้าง 1440** (เส้นแนวนอนใต้ hero) → **ใหม่ = `[160,150 1120×490]` ไม่มี stroke (`strokes: []`)** · ภาพ `Elephant_Zone2-1 1` (ref `ed0e5ee9`) **`[160,150 1120×490]`** (เดิม 497.228) → **ลบเส้น + hero สูง 490** · ตำแหน่ง/ขนาดส่วนล่างไม่ขยับ (หัวข้อ `[160,677 1120×112]` · กริด `[160,829…]` · Back `[160,1536]` ตามเดิม → ช่องว่าง hero→หัวข้อ 37px) · ภาพ render ยังไม่ดึง (ใช้ผลจาก JSON)
- **หน้า trail ใหม่ทุก trail (17):** hero = `Hearder Trail : <ชื่อ>` `[160,150 1120×490]` **ไม่มีเส้น/stroke ทุกหน้า** · โครงเหมือน Elephant (Header · hero · หัวข้อ+คำอธิบาย `Frame 12683` `[160,677 1120×112]` · กริดวัตถุ `Frame 114472` · Back) · **ยังไม่ได้เขียนสเปกรายหน้า** (นอกขอบเขตรอบนี้ — DEFERRED) · ข้อมูลอ้างอิง (id ใน section "All : 17 Trails" · id สำเนาใน page 2.1 `998:952` · ความสูง frame · H1 ใน Figma):
| trail | frame (section All : 17 Trails) | สำเนาใน page 2.1 | สูง | H1 ใน Figma |
|---|---|---|---|---|
| Bird Trails | `3153:1003` | `3161:3921` | 1416 | Bird Trail |
| Buffalo Trails | `3153:1084` | `3161:3945` | 1184 | Buffalo Trail |
| Chicken Trails | `3153:1129` | `3161:3969` | 1627 | Chicken & Rooster Trail |
| Deer Trails | `3155:693` | `3161:3999` | 1287 | Deer Trail |
| Elephant Trails | `796:1795` | `3161:4029` | 1632 | Elephant Trail |
| Dragon Trails | `3155:747` | `3161:4059` | 1396 | Dragon Trail |
| Fish Trails | `3155:821` | `3161:4083` | 1444 | Fish & Aquatic Mammals Trail |
| Garuda Trails | `3156:884` | `3161:4107` | 1444 | Garuda Trail |
| HornBill Trails | `3157:932` | `3161:4131` | 1416 | Hornbill Trail |
| Horse Trails | `3159:780` | `3161:4155` | 1416 | Horse Trail |
| Lion Trails | `3159:825` | `3161:4179` | 1416 | Lion Trail |
| Makara Trails | `3159:870` | `3161:4203` | 1416 | Makara Trail |
| Phoenix Trails | `3159:974` | `3161:4227` | 1416 | Phoenix Trail |
| Seashell Trails | `3159:1031` | `3161:4251` | 1646 | Seashell &  Marine Invertebrates Trail |
| Serpent Trails | `3159:1076` | `3161:4281` | 1646 | Serpentine & Naga Trail |
| Turtle Trails | `3159:1121` | `3161:4311` | 1214 | Turtle Trail |
| Waterbird Trails | `3159:1169` | `3161:4335` | 1637 | Waterbirds & Hamsa Trail |

### 4.5 Timeline แนวนอน — Zone 4 / trail ช้าง `670:1551` · `timeline-zone4.png` · `layout/timeline-zone4.md`
frame 1440×1582 แต่เนื้อหาในแนวนอนกว้าง **11,197** (frame ไม่ clip) · พื้น `#FFFFFF`
| ชิ้น | พิกัด | รายละเอียด |
|---|---|---|
| Header #1/#02 | `[0,0]`,`[0,86]` | |
| `Frame trails` (nav trail 17 ไอคอน) | `[0,150 725×55]` padding 20/20/5/5 gap 5 | ไอคอน 35×35 ×17 (ตัวที่ active = **45×45** ที่ x≈260) · สีตามกลุ่ม §2.1 (green×4: Birds, Hornbill, Water Bird & Herons, Chicken & Rooster · red×4: Garuda, Phoenix, Dragon, Naga · mustard×5: Elephant, Buffalo, Horse, Deer, Lion · purple×4: Makara, Turtle, Fish & Aquatic Mammals, Seashell & Marine Invertebrates — ชื่ออ่านจากภาพ raster อ้างอิง, ไม่ใช่ text node) |
| เส้นเวลา+ภาพ `Group 31 / ROOM4 ele 2` | `[0,200 11000×791.481]` | **ภาพ raster ใบเดียว** (IMG `791ae91b`) มีเส้น+จุด 15 จุด+ภาพวัตถุตัดขอบ+ชื่อ+ป้ายยุค (เช่น "10th–11th century", "13th century", "15th–early 16th century CE", "19th century CE", "20th–21st century") + คนยืนดู + โครงกระดูกช้างจาง ๆ — **ข้อความทั้งหมดฝังในภาพ** (P-207/P-208) |
| แถวการ์ดข้อมูล `Frame 114464` | `[0,991.5 11193×549]` แนวนอน padding-left **500** padding-bottom 100 gap 0 | การ์ด 15 ใบ (กว้าง 507–1012) เรียงซ้าย→ขวา: `[ภาพ ~180–300 กว้าง] + gap 10 + Frame 12683 (หัวเรื่อง 20/25 Heavy `#212529` · meta 14/24 Komet-Regular · "Read more >>" Barlow 20/22 ขวา)` · gap ระหว่างข้อความ 15 · การ์ด 15 ใบ `[x,1091 w×h]`: 500 (1012×202) · 1512 (859×279) · 2371 (666×187) · 3037 (667×281) · 3704 (695×208) · 4399 (789×232) · 5188 (739×168) · 5927 (507×208) · 6434 (667×256) · 7101 (667×282) · 7768 (739×280) · 8507 (567×208) · 9074 (667×449) · 9741 (787×160) · 10528 (665×263) · ข้อความของแต่ละใบใน `layout/timeline-zone4.md` |
| Reflection `Frame 114446` | `[10773,991.5 1448×1079]` padding 50/30 gap 30 (เนื้อหาเริ่ม x=11536 หลังการ์ดสุดท้าย) | "Reflection" 36/45 `[11536,1001.5]` + ย่อหน้า 20/24 Komet-Regular `[11536,1066 655×936]` + Read more `[11990,1226]` |
| ปุ่ม Back | `[160,1476 115×50]` | §3.3 |
**ลำดับ:** ลำดับการ์ด (card_order) ≠ ลำดับจุดบนภาพ (illustration_order) — ดู `content/seed/timeline-elephant.json` + P-208

### 4.5b Timeline v2 (D23) — ❌ **ถูกแทนด้วย D26 → §4.5c** (D24/D25 ตีความ "layout ตาม zone 4" ผิด) · เก็บไว้เป็นค่าวัด/ประวัติ · layout = §4.5 เดิม (frame `670:1551`) · เพิ่มเฉพาะสิ่งที่ animation ต้องใช้
**D24 (เจ้าของ 2026-10-28) = ทางเลือก A:** raster `1232:1711` คงเดิม + จุดบนเส้นกดได้ + animation ตาม Slides ใช้กับ**ภาพการ์ด** · ⛔ **ไม่ทำ:** ลายเส้นแยกชิ้น · mini-map · เส้นประแดง · ชั้นเหนือเส้นต่อชิ้น · grayscale (ทางเลือก B/C ที่เหลือใน §4.5b.4 = บันทึกประวัติเท่านั้น)
**ขอบเขต (เจ้าของสั่ง D23 · ชี้แจงเพิ่ม 2026-10-27):** การเรียงข้อมูล/layout = frame `Desktop - zone 4` ตาม §4.5 ทุกค่า (ไม่เปลี่ยน) · พฤติกรรม/animation = Google Slides 31–54 (§6.4b) · **ไม่ใช้ layout ของ Slides** (mini-map · ลูกศร ‹ › · เส้นประแดง · ภาพถ่ายใต้เส้นแบบ Slides) — ค่าเหล่านั้นวัดไว้ใน §4.5b.6 เผื่อเจ้าของให้เพิ่ม (P-219)
**ที่มาค่า:** `[N]` = node Figma (figma-file.json) · `[R]` = วัดจากภาพอ้างอิงที่ฝังใน Figma Section 50 (`annot-zone4-ref-1..3.png` 1424–1431 กว้าง → ×1440/กว้าง) · `[S]` = วัดจากภาพ Slides `design/timeline-ref/slide-31..54.png` (ขนาดภาพ 755/647/486/432 กว้าง → ×1440/กว้าง · ความละเอียดต่ำ ±3px) · `[P]` = ข้อเสนอ (ไม่มีใน Figma/Slides)

**4.5b.1 องค์ประกอบของ zone 4 ที่ animation แตะ (ทุกค่า geometry อื่นตาม §4.5)**
| ชิ้น (ตาม §4.5) | ภาพที่ animate | state พัก (rest) | state เลือก (active) | asset ที่ต้องใช้ | มีแล้วไหม |
|---|---|---|---|---|---|
| การ์ดวัตถุ ×15 `Frame 1146xx` (ภาพ + `Frame 12683` ข้อความ) | **ภาพการ์ด** (cutout สี พื้นโปร่ง · ref ใน `content/seed/timeline-elephant-v2.json` → `items[].thumbnails[]`) | scale 1.0 · opacity **0.5** `[P]` (Slides ซ่อนภาพตอนพัก ไม่มีค่า rest ให้ยึด — เลือกให้ภาพยังอ่านออก) | scale **1.5** `[S]` (ขอบเขต 1.35–1.55) · opacity 1.0 (ก้าวระหว่างทางตาม §6.4b: 0.05/0.20/0.40/0.60 → 1.0) | ภาพการ์ดเดิม `sh_thumbnail` (+ `_2` การ์ด 6, 9) — **ไม่ต้องมีภาพใหม่** | ✔ 15/15 (ต้อง trim พื้นโปร่ง — P-220) |
| ข้อความการ์ด (title 20/25 Heavy · meta 14 · Read more) | ข้อความ | opacity 1.0 `[P]` (Slides จางข้อความตามภาพ แต่ที่ α≈0.2 คอนทราสต์ต่ำมาก → ไม่ทำตาม; เลือกได้ — P-219) | opacity 1.0 (title/ข้อความ `#212529` ตาม token เดิม · Slides สีเต็มวัดได้เข้ม ≈`#080808`/`#3B3B3B` ตัวหนา `[S]`) | — | ✔ |
| จุด ● บนเส้นเวลา ×15 (ฝังใน raster `1232:1711`) | **`<button>` กดได้ทับจุด + ทำเครื่องหมายจุดที่ active** (รายละเอียด §6.4b) | ปุ่มโปร่งใส 44×44 (มองไม่เห็น) | วงแหวนรอบจุดเดิม ⌀ ≈60 `[P]` ที่ (`strip_dot.frame_x`, y≈806) (ตาราง §4.5b.3) | พิกัดจุด — เพิ่มใน seed v2 แล้ว | ✔ (วัดแล้ว 15/15) |
| เส้นเวลา+ภาพ `ROOM4 ele 2` `1232:1711` (raster ใบเดียว 4096×285 แสดง 11000×791.481 · `[0,200]`) | **ไม่ animate ต่อชิ้น** (D24 = A: ทุกวัตถุเป็นพิกเซลเดียวกัน) | static ตาม §4.5 | static + **เลื่อนอัตโนมัติ** ให้จุดชิ้น active อยู่ x=720 | ไม่ต้องมีใหม่ | ✔ |
| `Frame trails` (ไอคอน trail 17) | — | ตาม §4.5 (active 45×45 ที่ x≈260) | ไม่เปลี่ยน | — | ✔ |

**4.5b.2 การจัดวาง state (เพิ่มจาก §4.5)**
- การ์ดคงกรอบ `Frame 1146xx` เดิม (x/y/w/h ตาม §4.5 · ข้อความไม่ขยับในสถานะพัก) · ภาพ active ขยายด้วย `transform: scale(1.5)` โดย **`transform-origin` = กึ่งกลางภาพ (การ์ดที่มี 2 ภาพ = กึ่งกลาง bbox กลุ่ม)** → กึ่งกลางภาพไม่ขยับ = จุดยึดเลื่อนมา x 720 (§6.4b) · ข้อความของการ์ดเลื่อนขวา `0.25 × กว้างภาพ/กลุ่ม` พร้อมกัน `[P]` · กว้างภาพ/กลุ่ม = node `[N]` เช่น Leg Bone 300 → 450 (ข้อความ +75) · Ganesha 180 → 270 (+45)
- **การ์ด 6 และ 9 มี 2 ภาพซ้อน** (`Group 34` `1232:1744` 174×152 @x4399 · `Group 33` `1232:1764` 180×150 @x6434): scale **ทั้งกลุ่มเป็นหน่วยเดียว** (ไม่ใช่ทีละภาพ) · กว้างที่ใช้ดันข้อความ = bbox กลุ่ม (174 → ข้อความ +43.5 · 180 → +45) · ลำดับซ้อนตาม node (ภาพ `…1746`/`…1766` อยู่บน)
- ภาพที่ขยายซ้ายอาจเข้าไปในที่ว่างท้ายการ์ดก่อนหน้า (≤0.25×กว้างภาพ = ≤75px) · ถ้าชนข้อความการ์ดก่อน/หลัง (การ์ด 2, 6 ที่ข้อความเต็มกรอบ) → ขยายกรอบการ์ด active ด้วย (animate width + `0.25×กว้างภาพ` ทั้งสองข้าง) แล้วชดเชย scroll ให้กึ่งกลางภาพคง x 720 `[P]` — wp-dev วัดจริงตอนทำ (ผมไม่ได้เรนเดอร์ฟอนต์ Komet)
- ภาพ **ไม่ขยายเกินความละเอียดไฟล์**: ปัญหา = Pod Duang ไฟล์ 227×227 (ขยาย 270px ที่ 1.5× = 1.19× ของไฟล์ บน DPR1; DPR2 ไม่พอ) · Patola 625×589 พอ · ที่เหลือ ≥ 400px (ตาราง `trimmed_px` ใน seed v2) → P-220
- state พัก/เลือกเป็น class ต่อการ์ด (`is-active`) ตัวเดียวที่ active (ไม่มีหลายใบพร้อมกัน) · ไม่เลือกอะไร = ทุกใบ rest (state เริ่มต้น เหมือน Slides 31 ที่ยังไม่เลือก)
- `prefers-reduced-motion`: สลับ state ทันทีไม่ animate `[P]`

**4.5b.3 พิกัดจุดบนเส้นเวลา (raster `1232:1711`) ↔ การ์ด — ⚠️ P-170: `frame_x` ในตารางนี้ไม่คิด imageTransform ของ raster (a 0.967019 · tx 0.006978) → ค่าที่ถูก = `(raster_x/4096 − 0.006978)/0.967019 × 11000` เช่น Leg Bone **9052** · Siwaih **10535** · Ganesha **318** (ครบ 15 ค่าใน seed v2 `strip_dot.frame_x_corrected`) · D26 ไม่ใช้ raster แล้ว ค่านี้เก็บไว้เผื่อใช้ raster ต่อ — ต้องใช้ทำ marker · วัดจาก raster ด้วยสคริปต์ (ไม่เรียก API)**
แปลง raster→frame: x×11000/4096 = **×2.68555** · y = 200 + y×791.481/285 = **200 + ×2.77713** · เส้นเวลาสูง 5px raster ≈ 14 frame px ที่ y≈797–808 · **จุด ⌀ ≈14–15 raster (รวมขอบ antialias) ≈ 38–40 frame px · ศูนย์กลาง y ≈ 806 ±3 (raster y≈217–219.5 ต่อจุด)** (แก้จากรอบแรกที่เขียน 11–12 / 803 — ค่านั้นวัดเฉพาะแกนทึบ)
| card_order | วัตถุ (key) | ภาพ illustration_order | จุด raster x → **frame x** | การ์ด frame x `[N]` | ลายเส้นใน raster? |
|---|---|---|---|---|---|
| 1 | elephant leg bone | 14 | 3288 → **8830** | 500 | โครงกระดูกจาง+กระดูก (ลายเส้น/ghost) |
| 2 | singhasari ganesha | 3 | 658 → **1767** | 1512 | ลายเส้น ("Ganesha Statue" 13th c.) |
| 3 | ganesha | 1 | 143 → **384** | 2371 | ลายเส้น ("Ganesha / Four-armed Ganesha" 10th–11th c.) |
| 4 | elephant molar | 8 | 1762 → **4732** | 3037 | ลายเส้น (กรามช้าง) |
| 5 | embossed brass | 7 | 1534 → **4120** | 3704 | สี |
| 6 | ivory fire piston | 13 | 2972 → **7981** | 4399 | สี |
| 7 | elephant and mahout | 4 | 866 → **2326** | 5188 | สี |
| 8 | patola | 12 | 2771 → **7442** | 5927 | สี |
| 9 | elephant shaped relic | 6 | 1314 → **3529** | 6434 | สี |
| 10 | pod duang | 11 | 2556 → **6864** | 7101 | สี |
| 11 | hand carved ivory | 10 | 2322 → **6236** | 7768 | สี |
| 12 | tin ingot | 5 | 1145 → **3075** | 8507 | สี |
| 13 | wooden cold press | 9 | 2053 → **5513** | 9074 | สี |
| 14 | siwaih | 15 | 3822 → **10264** | 9741 | สี |
| 15 | bas relief of a royal | 2 | 372 → **999** | 10528 | สี (ภาพขาวดำ) |
(ครบใน `content/seed/timeline-elephant-v2.json` พร้อม bbox วัตถุต่อชิ้นบน raster) · **ลำดับบนเส้น ≠ ลำดับการ์ด** (illustration_order ≠ card_order · P-208ก ยังจริงสำหรับ raster) แต่ใน Slides + mini-map 15 ชิ้นเรียงตาม **card_order** (อ่านจากภาพ — P-218) → **D24: คงลำดับ raster** · การเลือกชิ้นหนึ่งเลื่อน 2 แถวไปคนละตำแหน่ง (แถว raster ไป `strip_dot.frame_x` · แถวการ์ดไป `card_frame_x`) — ไม่ตรงกันเหมือน Slides ที่ภาพ/จุด/ข้อความอยู่แนวเดียวกัน

**4.5b.4 "ภาพวัตถุเหนือเส้น" — ✅ D24 (เจ้าของ 2026-10-28) = ทางเลือก A** (ปิด P-217)
| | ทำอะไร | สถานะ |
|---|---|---|
| **A = ทำ** | raster `1232:1711` คงเดิม (static) · จุดกดได้ 15 จุด (`<button>`) + วงแหวนจุด active · raster เลื่อนตามอัตโนมัติให้จุดชิ้น active อยู่ x=720 · animation fade+scale ที่**ภาพการ์ด** · ไม่ต้องมี asset ใหม่ | ทำได้ทันที |
| ~~B~~ ไม่ทำ | เลเยอร์ต่อชิ้นจากภาพการ์ด (rest จาง+เทา → active สี 1.5×) | ⛔ บันทึกประวัติ |
| ~~C~~ ไม่ทำ | ลายเส้น→สี ตรง Slides (ต้องลายเส้น 15 ภาพจากนักออกแบบ) | ⛔ บันทึกประวัติ |
**ลำดับ (ปิด P-218):** คงลำดับ raster (illustration_order) · แถวการ์ดเป็น scroller หลัก แถว raster ตามอัตโนมัติ (§6.4b)
**หลักฐานที่ raster animate ต่อชิ้นไม่ได้ (เหตุผลที่ A):** IMG `791ae91b` เก็บที่ 4096×285 แต่แสดง 11000×791 (×2.69) — วัตถุสูงแค่ ~93 px ใน raster · ตัวอย่างที่ตัดได้ `design/assets/timeline-v2/` : `ganesha-fourarmed-lineart.png` 97×90 · `singhasari-ganesha-lineart.png` 62×87 → ตัดได้แต่ใช้จริงไม่ได้ (ไม่ใช้ตาม D24)

**4.5b.5 สิ่งที่ Slides แสดงแล้ววิเคราะห์ว่า "ภาพไหน" fade/ขยาย (สรุป)**
1. วัตถุเหนือเส้น (ลายเส้น→สี ×≈1.5 ฐานติดเส้น · เพื่อนบ้านไม่เปลี่ยนขนาด: ลายเส้น Singhasari 127×178 คงเดิมทุกเฟรม 31–36 `[S]` · ช้างการ์ตูน 181×129 คงเดิม `[S]`) → zone 4 (D24 = A): **ไม่ animate ต่อชิ้น** · raster คงเดิม
2. ภาพถ่ายใต้เส้น (opacity **0.05→0.20→0.40→0.60** ใน 31→34 · **เต็ม 1.0 = slide 35** · 0.40→0.20→0.10→0 ใน 36→39 `[S]` วัดซ้ำเทียบ slide 35 · alpha เท่ากันทุกช่อง R/G/B) → zone 4 = **ภาพการ์ด** (cutout สีพื้นโปร่ง ไม่ต้องมี asset ใหม่) · สีที่เห็นจางกว่าเหนือเส้นใน slide 34 คือ alpha 0.6 ของภาพเดียวกัน ไม่ใช่ฟิลเตอร์/ไฟล์ต่าง
3. ชื่อวัตถุ: α ≈0.19/0.61/0.81/**1.0** (32/33/34/35) แล้ว 0.80/0.60/หาย (36/37/38) · สีเต็มพิกเซลเข้มสุด ≈`#080808` ตัวหนา ≈18px ที่ 1440 (กว้าง "Ganesha" 72px) `[S]` · (ภาพอ้างอิง ref-3 `#333333` = สถานะกลาง ≈ slide 34)
4. metadata: α ≈0.22/0.45/0.67/**1.0** (32–35) → 0.40/0.40/0.10 (36–38) · สีเต็ม ≈`#3B3B3B` (พิกเซลเข้มสุด) · ระยะบรรทัด ≈18.6px · ขนาด ≈13–14px (token Komet 14/17.5 ใกล้สุด) · 7 บรรทัดสำหรับ Ganesha
5. ชิ้นเดียวที่ต่าง: วัตถุ 4 (กรามช้าง) ใน Slides 41–45 มี "ช้างลายเส้นใหญ่" ใต้เส้น crossfade เป็นภาพถ่ายกราม — เป็นเฉพาะชิ้นนี้ (ช้างการ์ตูนใช้แทนกราม) → ไม่ทำ (P-219)

**4.5b.6 ของ Slides ที่ไม่อยู่ใน layout zone 4 (วัดไว้ ไม่ทำ จนกว่าเจ้าของยืนยัน — P-219)**
ทั้งหมดเป็นพิกัดที่ **1440 กว้าง · y วัดจากเส้นล่าง header ที่ y≈86.5** `[R]` (ภาพอ้างอิงละเอียดสุด ref-3)
| ชิ้น | ค่า |
|---|---|
| แถบไอคอน trail | ไอคอน **25.3×26.3** gap 3.0 ×17 · x 19→498 · y 113→140 (= header+27) · สี green `#178D74` red `#E7553D` mustard `#C3BC85` purple `#B47093` — **เล็กกว่า Figma node (35×35 gap 5 ที่ `[0,150]`)** → ใช้ node ตาม §4.5 |
| mini-map | x 639→1412 · y 93→158 (กว้าง 773 สูง ≈65) · เป็น "ภาพเส้นเวลาทั้งเส้นย่อ ≈0.135×" (15 ชิ้นตาม card_order) · กรอบสี่เหลี่ยมครีม `#EDEBDA` กว้าง **≈195** สูง ≈50 ที่ header+14.6 (= viewport 1440 ÷ ความยาวเส้น ≈5714) อยู่กึ่งกลางจุดที่เลือก |
| เส้นเวลา (Slides) | ดำ หนา ≈4px · x 31→1412 · y ≈469 (header+382) · จุด ⌀ ≈12 · ระยะจุดสม่ำเสมอ **376** · จุดที่เลือกอยู่ x=720 (กลางจอ) |
| วัตถุเหนือเส้น | ฐานติดเส้น · สูง 90–180 (ลายเส้น) → สี active กว้าง 200 สูง 237–280 · ชื่อเหนือวัตถุ ≈ y 183–196 |
| ลูกศร ‹ › | ชุดเดียว 25×48 · ซ้าย x 27→53 · ขวา x ≈1387→1412 · กึ่งกลาง y ≈349 (header+262) · สีดำเส้นหนา ≈3 |
| ภาพถ่ายใต้เส้น (ชัด) | กึ่งกลางที่ x=719 (ใต้จุด) · Ganesha 221×324 · ข้อความเริ่ม x=907 (= จุด+188 = ครึ่งระยะจุด) |
| เส้นประแดง | ชัด/active: 3 เส้น `#FF1D25` ที่ x = 719 · 814 · 907 · dash 9/gap 9 · หนา 1–3 · จากเส้นเวลาลงถึงขอบล่าง · (Slides ระหว่าง transition มีเพิ่มสีส้ม `#FF931E` x≈621 และสีน้ำเงิน `#0000FF` ใต้ทุกจุด ในภาพ ref-1 → **ตีความว่าเป็นเส้นไกด์ของนักออกแบบ** ไม่ใช่ UI) |

**4.5b.7 บัญชี asset ต่อวัตถุ (15 ชิ้น trail ช้าง) — ตรวจจากไฟล์ใน `design/assets` + `figma-file.json` (ไม่เรียก API)**
| card | วัตถุ | ภาพสีตัดพื้น (= `sh_thumbnail`) node · imageRef · ไฟล์ px → trim px | ลายเส้นแยกชิ้น | ภาพถ่ายใต้เส้นแบบ Slides | thumbnail mini-map |
|---|---|---|---|---|---|
| 1 | elephant leg bone | `1232:1714` `4953122e…` 1920×1080 → **1360×720** | ใน raster (ต่ำ ~0.37×) | ใช้ภาพการ์ดเดียวกัน (ไม่มีไฟล์แยก) | ไม่มีไฟล์แยก — Slides ย่อเส้นเวลาทั้งเส้น (ไม่อยู่ใน layout zone 4) |
| 2 | singhasari ganesha | `1232:1720` `b0afe5ba…` 4096×2304 → **1340×1885** | ใน raster (ต่ำ ~0.37×) | ใช้ภาพการ์ดเดียวกัน (ไม่มีไฟล์แยก) | ไม่มีไฟล์แยก — Slides ย่อเส้นเวลาทั้งเส้น (ไม่อยู่ใน layout zone 4) |
| 3 | ganesha | `1232:1726` `bc3f275a…` 4096×2304 → **1563×1878** | ใน raster (ต่ำ ~0.37×) | ใช้ภาพการ์ดเดียวกัน (ไม่มีไฟล์แยก) | ไม่มีไฟล์แยก — Slides ย่อเส้นเวลาทั้งเส้น (ไม่อยู่ใน layout zone 4) |
| 4 | elephant molar | `1232:1732` `378ea9bd…` 1920×1080 → **767×383** | ใน raster (ต่ำ ~0.37×) | ใช้ภาพการ์ดเดียวกัน (ไม่มีไฟล์แยก) | ไม่มีไฟล์แยก — Slides ย่อเส้นเวลาทั้งเส้น (ไม่อยู่ใน layout zone 4) |
| 5 | embossed brass | `1232:1738` `5d9931b4…` 1344×961 → **1343×960** | ✘ ไม่มี | ใช้ภาพการ์ดเดียวกัน (ไม่มีไฟล์แยก) | ไม่มีไฟล์แยก — Slides ย่อเส้นเวลาทั้งเส้น (ไม่อยู่ใน layout zone 4) |
| 6 | ivory fire piston | `1232:1745` `f8e2d79d…` 4096×3084 → **533×1430**<br>`1232:1746` `8dcebc79…` 4096×3084 → **503×1586** | ✘ ไม่มี | ใช้ภาพการ์ดเดียวกัน (ไม่มีไฟล์แยก) | ไม่มีไฟล์แยก — Slides ย่อเส้นเวลาทั้งเส้น (ไม่อยู่ใน layout zone 4) |
| 7 | elephant and mahout | `1232:1752` `867b2833…` 4096×2731 → **1401×2203** | ✘ ไม่มี | ใช้ภาพการ์ดเดียวกัน (ไม่มีไฟล์แยก) | ไม่มีไฟล์แยก — Slides ย่อเส้นเวลาทั้งเส้น (ไม่อยู่ใน layout zone 4) |
| 8 | patola | `1232:1758` `07e22a68…` 625×589 → **611×580** | ✘ ไม่มี | ใช้ภาพการ์ดเดียวกัน (ไม่มีไฟล์แยก) | ไม่มีไฟล์แยก — Slides ย่อเส้นเวลาทั้งเส้น (ไม่อยู่ใน layout zone 4) |
| 9 | elephant shaped relic | `1232:1765` `b1ba8a8a…` 1381×922 → **401×289**<br>`1232:1766` `1c3aa3a1…` 1381×922 → **787×390** | ✘ ไม่มี | ใช้ภาพการ์ดเดียวกัน (ไม่มีไฟล์แยก) | ไม่มีไฟล์แยก — Slides ย่อเส้นเวลาทั้งเส้น (ไม่อยู่ใน layout zone 4) |
| 10 | pod duang | `1232:1772` `bf841c7f…` 227×227 → **226×226** | ✘ ไม่มี | ใช้ภาพการ์ดเดียวกัน (ไม่มีไฟล์แยก) | ไม่มีไฟล์แยก — Slides ย่อเส้นเวลาทั้งเส้น (ไม่อยู่ใน layout zone 4) |
| 11 | hand carved ivory | `1232:1778` `ba68e852…` 2098×411 → **2017×270** | ✘ ไม่มี | ใช้ภาพการ์ดเดียวกัน (ไม่มีไฟล์แยก) | ไม่มีไฟล์แยก — Slides ย่อเส้นเวลาทั้งเส้น (ไม่อยู่ใน layout zone 4) |
| 12 | tin ingot | `1232:1784` `32fba036…` 1920×1080 → **954×637** | ✘ ไม่มี | ใช้ภาพการ์ดเดียวกัน (ไม่มีไฟล์แยก) | ไม่มีไฟล์แยก — Slides ย่อเส้นเวลาทั้งเส้น (ไม่อยู่ใน layout zone 4) |
| 13 | wooden cold press | `1232:1790` `730ba23e…` 1920×1080 → **936×287** | ✘ ไม่มี | ใช้ภาพการ์ดเดียวกัน (ไม่มีไฟล์แยก) | ไม่มีไฟล์แยก — Slides ย่อเส้นเวลาทั้งเส้น (ไม่อยู่ใน layout zone 4) |
| 14 | siwaih | `1232:1796` `9c5706f8…` 1920×1080 → **981×252** | ✘ ไม่มี | ใช้ภาพการ์ดเดียวกัน (ไม่มีไฟล์แยก) | ไม่มีไฟล์แยก — Slides ย่อเส้นเวลาทั้งเส้น (ไม่อยู่ใน layout zone 4) |
| 15 | bas relief of a royal | `1232:1802` `91cfd6ae…` 4096×1270 → **4096×1270** | ✘ ไม่มี | ใช้ภาพการ์ดเดียวกัน (ไม่มีไฟล์แยก) | ไม่มีไฟล์แยก — Slides ย่อเส้นเวลาทั้งเส้น (ไม่อยู่ใน layout zone 4) |

**ขาด/ต้องขอ (หลัง D24 = A ข้อ (1)–(2) ไม่ต้องขอแล้ว):** (1) ลายเส้นแยกชิ้น **11/15 ไม่มี** (ที่มี 4: ชิ้น 1 โครงกระดูก+กระดูก · 2 · 3 · 4 กราม — ใน raster `791ae91b` ความละเอียดต่ำ) และลายเส้น **ช้างการ์ตูน** ที่ Slides ใช้แทนชิ้น 4 ไม่มีใน Figma → ต้องขอก็ต่อเมื่อเลือกทางเลือก C (P-217) · (2) raster ความละเอียดสูง/ชิ้นคนยืน 4 + โครงกระดูก (ทางเลือก B) · (3) Pod Duang `bf841c7f` 227×227 ต่ำเกินสำหรับ 1.5×/DPR2 → ขอไฟล์ใหญ่ (P-220) · (4) ภาพทุกใบ **ไม่มีไฟล์ใหม่ใน Figma/ดาวน์โหลดเพิ่ม** — ใช้ของที่มี 100% (ไม่เรียก API)
**ตัวอย่างตัดลายเส้นจาก raster (พิสูจน์ ไม่ใช่ของใช้จริง):** `design/assets/timeline-v2/ganesha-fourarmed-lineart.png` 97×90 · `singhasari-ganesha-lineart.png` 62×87 + `manifest.json` · สคริปต์ `design/tools/crop-timeline-lineart.py` (`--help` · `--no-write` ไม่เขียนอะไร · เขียนผ่าน temp dir แล้ว `os.replace` ต่อไฟล์) · `design/` ไม่อยู่ใน repo (ไม่ commit)

### 4.5c Timeline v3 (D26) — หน้าตา = Section 50 / Google Slides 31–54 **100%** · ต่างแค่เนื้อหา (timeline_item 15 ชิ้น เรียงตาม `sh_card_order`)
**D35 + D36 (2026-11-11 · ลูกค้า ผ่านเจ้าของ · `design/timeline-ref/owner-under-4pairs.png`) แก้ D32/D33: ❌ แถวบนเส้นเวลา "ไม่ใช้ลายเส้น" — กลับเป็นแบบ D26 เดิม (ไม่ active = ภาพสี `sh_thumbnail` opacity 0.6 → active = สีขยาย) ทั้ง 15 ชิ้นรวมชิ้น 1–4 · ✅ ภาพ "ก่อน" ▶ "หลัง" ไปอยู่ที่ภาพใหญ่ใต้เส้น เฉพาะ 4 ชิ้นที่มีคู่ ▶ ใน Figma `3101:609` Section 51: ชิ้น 2 Singhasari · 3 Ganesha · 4 Molar · 5 Embossed brass basin (ชิ้น 1 Leg Bone และที่เหลือเหมือนเดิม) → ดู 4.5c.3b + §6.4c · ฟิลด์ `sh_line_art` = "ภาพก่อน" (ไม่จำเป็นต้องเป็นลายเส้น) · ส่วนที่แก้มีป้าย `(D35/D36)` · ข้อความ D32/D33 เดิมที่เหลือเป็นประวัติ ❌ (Figma `3101:609` lastModified 2026-11-11T03:55:12Z)**
**D39 (2026-11-11 · เจ้าของดู preview · `design/timeline-ref/owner-basin-before-smaller.png` · `owner-basin-new-image.png` · `owner-kendi-figma.png`) รูปบนเส้น/ใต้เส้นตาม Figma Section 51 ต่อชิ้น: ฟิลด์ใหม่ `sh_below_image` (ภาพใต้เส้น · ไม่บังคับ · ว่าง = `sh_thumbnail`) · ชิ้น 5 บนเส้น+ใต้เส้น = `cba74a7d` + ภาพก่อนเล็กลง `before_scale` 0.62 · ชิ้น 7 บนเส้น `621646b5` / ใต้เส้น `555d365b` (kendi + คนลายเส้น) · ชิ้น 1·9 ใต้เส้นต่างจากบนเส้น · ที่เหลือตรง → ตารางตรวจทั้ง 15 ชิ้น + กฎขนาดที่คำนวณใหม่ + ค่าที่ธีมต้องรู้อยู่ที่ §4.5c.3d · ส่วนที่แก้มีป้าย `(D39)` · ไม่กระทบหน้า detail**
**(D34 · 2026-11-11) Reflection = จุดที่ 16 ท้ายเส้น (แทน D16 ที่ซ่อน) — ไม่มีรูป · รายละเอียด/ค่าทั้งหมดอยู่ที่ §4.5c.7 · จุดที่เปลี่ยนในตารางอื่นมีป้าย `(D34)`: 4.5c.1 ลูกศร ‹ › · 4.5c.2 ช่วง c · 4.5c.4 · §6.4c**
**แทน §4.5b ทั้งหมด (❌ D24/D25 ถูกแทนด้วย D26 · §4.5b เก็บไว้เป็นค่าวัด)** · ภาพอ้างอิงเจ้าของ: `design/timeline-ref/owner-ref-before.png` (ก่อน = Slides 31 = annot ref-1) · `owner-ref-transition.png` (ระหว่าง = ≈Slides 32–34 = ref-2) · `owner-ref-active.png` (ถึงกลาง = ≈Slides 35 = ref-3) · `section50-owner-note.png`
**ที่มาค่า:** `[N]` node Figma · `[R]` วัดจากภาพอ้างอิง Section 50 ละเอียดสุด (ref-3 1424 กว้าง → ×1440/1424=1.0112 · ref-1 1426 → ×1.0098) · `[S]` วัดจากภาพ Slides 31–54 (755/647/486/432 กว้าง → ×1440/กว้าง · ±3px) · `[P]` ข้อเสนอ
**ระบบพิกัด:** x = px ที่ 1440 กว้าง · **y_rel = วัดจากเส้นล่างของ header ใน Slides** (ภาพ Slides y≈86.5 = header สูง ≈86) · header จริงของเว็บใช้ component §3 เดิม (Header #1 + #02) — **stage เริ่มใต้ header จริง** และใช้ y_rel ทั้งหมดนับจากขอบล่าง header จริง `[P]` (Slides วาด header เป็นแถบเดียว ไม่ตรงกับ Figma Header #1/#02 จึงไม่ลอกส่วนนี้) · ความสูง stage ออกแบบ ≈ **723** (= 810 − 86.5 · Slides 16:9) `[S]` · **ย่อทั้ง stage ให้พอดีความสูงจอ** ดู §4.5c.5 (D27)

**4.5c.1 โครง stage (บน → ล่าง)**
| ชิ้น | x | y_rel | w×h | รายละเอียด | ที่มา |
|---|---|---|---|---|---|
| แถบไอคอน trail ×17 | 19 → 498 (ไอคอนแรก x=19.2) | 27 → 54 | ไอคอน **25.3×26.3** · pitch 28.3 (gap 3.0) | เรียง: Birds · Hornbill · Water Bird · Chicken · Garuda · Phoenix · Dragon · Naga · Elephant · Buffalo · Horse · Deer · Lion · Makara · Turtle · Fish · Seashell · สีกลุ่ม green `#178D74` ×4 · red `#E7553D` ×4 · mustard `#C3BC85` ×5 · purple `#B47093` ×4 · **ทุกไอคอนขนาดเท่ากัน ไม่มีตัวขยาย** (Figma §4.5 มี active 45 — D26 ให้ตาม Slides) · ภาพไอคอน = รูปเดิม 17 ใบใน `Frame trails` · คลิก → หน้า trail นั้น (เหมือนที่อื่น) | `[R]` · ลิงก์ `[P]` |
| mini-map (ภาพย่อ 15 ชิ้น) | 639 → 1413 (กว้าง 774) | 6.5 → 71.5 (สูง 65) | 15 ช่อง pitch **51.6** · จุดช่องที่ i อยู่ x = 658 + 51.6·(i−1) | ภาพวัตถุสี (จาก `sh_thumbnail`) ตามตาราง 4.5c.3 คอลัมน์ mini-map · **ทุกภาพ contain ในกล่อง 48×38 และ CSS ต้องมีเพดาน `max-width:48px; max-height:38px` (× k) ทุกชิ้น — ไม่มีภาพไหนเกินกรอบ mini-map (F2 รอบ 1)** · ฐานวัตถุที่ y_rel ≈ 57–61 บนเส้นบางสีเทา+จุด ⌀≈3 · ข้อความเล็กในภาพย่อของ Slides (ชื่อ/ยุค) **ไม่ทำ** (อ่านไม่ออก) `[P]` · คลิกชิ้น → กระโดดไปชิ้นนั้น (D26) | `[R]` · ข้อความ/คลิก `[P]` |
| กรอบ viewport ใน mini-map (ไฮไลต์) | กว้าง **195** ศูนย์กลาง = ตำแหน่งกึ่งกลางจอ ณ ปัจจุบัน (ต่อเนื่องตามการเลื่อน) | 14.5 → 64.5 (สูง 50) | | สี่เหลี่ยมพื้น `#EDEBDA` ทึบ (วัตถุวาดทับ) · สัดส่วน = 1440 ÷ (376.6/51.6) = 197 ✓ · เริ่มต้น (ก่อน) ครอบ x 635–830 · ถึงกลางชิ้น 3: 665–860 · ใกล้ปลายทางปล่อยเลยขอบ mini-map ได้ (ไม่ clamp) `[P]` | `[R]` |
| ลูกศร ‹ › | ซ้าย 27 → 53 · ขวา 1387 → 1412 | กึ่งกลาง y_rel **262** (ช่วง 238 → 286) | 25×48 | chevron ดำ stroke ≈3 · hit target ≥44×60 รอบ chevron `[P]` · **D18 (F3): ซ่อนเมื่อสุดทาง** — ชิ้น 1 (c≤1) ซ่อน ‹ · **(D34) Reflection (c≥16) ซ่อน ›** (ชิ้น 15 แสดง › แล้ว) ด้วย `visibility:hidden` (ไม่โฟกัสได้/ไม่รับคลิก · ⛔ ไม่ใช่ opacity .3) · **ไม่แสดงบนมือถือ (<768)** (D26 ครอบเฉพาะ desktop · ดู 4.5c.6) | `[R]` · hit `[P]` · ซ่อน D18 |
| เส้นเวลา | 31 → 1412 (ยืนคงที่ ไม่เลื่อนตามชิ้น) | กึ่งกลาง **382.2** (บน 380.2) | หนา **4** | ดำ `#000` · จุด ● ⌀ **12** `#000` กึ่งกลางเส้น เลื่อนไปกับชิ้น · จุดที่ x นอก 31–1412 ไม่วาด `[S]` (frame 31→33 เส้นไม่ขยับ จุดเลื่อน) | `[R]`/`[S]` |
| ชิ้นบนเส้น (แถวบน) | ศูนย์กลาง = x ของจุด | ฐานวัตถุ = **380.2 − 2** (บนเส้นลบ 2) ทั้ง rest/active · โตขึ้นด้านบน | ดู 4.5c.3 | **(D35 · ❌ ไม่ใช้ลายเส้นแถวบนแล้ว — แทน D32/D33) ทุกชิ้น 1–15 = ภาพสี `sh_thumbnail` ใบเดียว: rest opacity 0.6 (ไม่ทำเทา) → active opacity 1 · **ขนาด (#14 · `[C]` ลูกค้า S18–S20): rest = 0.30 × ภาพใต้เส้นของชิ้นเดียวกัน → active = 0.60 × ภาพใต้เส้น (= scale ×2.00 ทุกชิ้น) · ตาราง 4.5c.3** · α = 0.6 + 0.4·p (§6.4c) · ไม่มีเลเยอร์ที่สอง · เพื่อนบ้านไม่เปลี่ยนขนาด** | `[S]` ฐาน · **`[C]` ขนาด 30/60 (#14)** · opacity `[P]` · D35
| ชื่อเหนือชิ้นกลาง | กึ่งกลางที่ x ของชิ้น | ล่างสุดของข้อความ = **บนวัตถุ active − 19** (**#14: Ganesha active สูง 159 → วัตถุบน y_rel 219.2 → ล่างสุดข้อความ 200.2** · ค่าเดิม 97–110/129 ของ Slides 31–54 ❌ แทนด้วยขนาดใหม่ · ช่อง 19 ไม่เปลี่ยน · ตาราง y ต่อชิ้นท้าย 4.5c.3) | กว้างสูงสุด **280** (× k) พับบรรทัด (F5: slide 43 ตัดที่ "…Grater / (Dwarf Elephant Bone)" · slide 51 ตัดที่ "…Basin with / Mythical Animal Motifs") · โตขึ้นด้านบน | **(D27) ชื่อเป็นลิงก์ไป `/object/<slug>/` (เฉพาะชิ้นกลาง)** · Komet-Heavy ≈**18/22** (กว้าง "Ganesha" 72px) สี token `#212529` (วัดสีเต็ม ≈`#080808`) กึ่งกลาง · ชื่อ 2 บรรทัดพบใน Slides (Molar) pitch ≈21–22 | `[S]` ขนาด/ตำแหน่ง · token `[P]` |
| ภาพถ่ายใต้เส้น (ชิ้นกลาง) | **กึ่งกลาง x=720** (กล่อง 560–880) | กล่อง **428.5 → 693.5** (สูง 265) · **กึ่งกลางแนวตั้งของกล่อง y_rel 561** (F1 · ref-3 / slide 34/35/43/51) | กล่อง **≤ 320 × 265** · ภาพ contain จัดกึ่งกลาง**ทั้งสองแกน**ในกล่อง · top ภาพ = 561 − h/2 (Ganesha 221×265 top 428.5) · ขนาดต่อชิ้นดู 4.5c.3 | แสดงชิ้นกลางชิ้นเดียว · ไม่ขยับตามการเลื่อน (อยู่กลางจอเสมอ) · ใช้ **`sh_below_image` ถ้ามี (D39 · ชิ้น 1·7·9) ไม่งั้น `sh_thumbnail`** **(#14: ภาพนี้ = 100% ของกฎขนาด — แถวบน active 60% / rest 30% อิงกล่องนี้)** · **(D35/D36 — ชิ้น 2·3·4·5 ขาเข้าแสดงภาพ "ก่อน" `sh_line_art` ก่อน แล้ว cross-fade เป็นสี · กล่อง/จุดกึ่งกลางเดิม ภาพก่อน contain แยกต่อภาพ · ดู 4.5c.3b + §6.4c)** · ⛔ ไม่ขยายเกิน px ไฟล์ (Pod Duang 226) · **(D27) คลิกภาพ = ไปหน้า detail `/object/<slug>/` (เฉพาะชิ้นกลาง)** · ขอบขวากล่อง 880 ห่างข้อความ metadata ที่ x=907 = 27 · **(เดิมสเปกวางบน 389 กล่อง 340×324 — ผิด แก้แล้ว)** | `[R]` ref-3 + Slides 34/35/43/51 · กล่อง `[R]` |
| metadata | เริ่ม **x=907** (= จุดกลาง +188 = ½ pitch) · กว้างสูงสุด **442** (ถึง 1349) | **line-box ของบรรทัดแรกบน 496** (= ยอดตัวอักษรบรรทัดแรก **≈501**  `[R]` ref-3 503 · slide 43/51 500 ลบ ≈5 ที่ half-leading ของ 14px/18.6) · สูง = **n×18.6** (7 บรรทัด = 130 · ล่างสุดของตัวอักษรบรรทัด 7 ≈ **626–628**) | ระยะบรรทัด **18.6** | Komet ≈14px/18.6 (token Komet 14/17.5 ใกล้สุด) · สี `#212529` · บรรทัด: Period / Place of Production / Material / Dimensions / Collection / Specimen·Object biography (พับบรรทัดในกว้าง 442) · **ไม่แสดงบรรทัด "Section:"** (Slides ไม่มี) · ตัวหนา/น้ำหนัก ≈ Medium `[S]` | `[R]` |
| เส้นประแดงแนวตั้ง (3 สี: แดง/ส้ม/น้ำเงิน) | — | — | — | ❌ **ไม่แสดง** (เจ้าของ: เป็นเส้นไกด์) | เจ้าของ D26 |
| ปุ่ม Back (D27: คงตาม §3.3) | **160** `[N]` (x ของ Back ใน zone 4 `[160,…]`) | บน **649** → 699 (ห่างล่าง stage 24) `[P]` | 115×50 | component `Button Back B1` §3.3 ทุกค่า (พื้น `#000000`@10% · glow `#D65F47`@40% · คลิก = ย้อนหน้า) · อยู่ล่างซ้ายของ stage ว่างอยู่ (ซ้ายของกล่องภาพใต้เส้น x 560 · ใต้เส้น) · ไม่ชนภาพ/metadata ใน desktop · มือถือ/แท็บเล็ต: ใต้ stage ชิดซ้าย padding 20 | x `[N]` · y `[P]` |
| (D35/D36) โครงกระดูกช้าง + คนยืน + วงกลมกระดูกขา (ไฟล์ `01-elephant-leg-bone.png`) | — | — | — | ❌ **ไม่ใช้** (แถวบนเป็นภาพสี · ใต้เส้นชิ้น 1 ไม่มีคู่ ▶ — D36 "ชิ้น 1 ไม่เปลี่ยน") · ไฟล์คงไว้ใน `design/assets/lineart/` เป็นประวัติ · ชิ้น 1 ไม่มี `line_art_ref` · ภาพใต้เส้นชิ้น 1 = กระดูกสีอย่างเดียว (P-224 → D27) · คนยืนข้างชิ้น 7 (kendi) ไม่ทำ (P-228 ปิด → D33/D35) | `[N]` 3101:609 · D36 |
**ลำดับ z (ล่างขึ้นบน):** พื้นขาว → เส้นเวลา → ชิ้น rest → ชิ้น active → จุด ● → ชื่อ → ภาพใต้เส้น/metadata → ลูกศร/mini-map/แถบไอคอน

**4.5c.2 ระยะห่างและตำแหน่งบนเส้น**
- **ระยะห่างจุด = เท่ากันทุกชิ้น = 376.6 px** (ref-3: 372.5×1.0112 · ref-1: 372.5×1.0098 = 376.2 · Slides 31: 377) `[R]` ไม่ขึ้นกับขนาดวัตถุ
- ใน viewport 1440: ชิ้นกลาง + 2 ข้าง (±376.6) เต็ม + ชิ้นที่ ±753 ล้นขอบและโผล่ ≈ 0–120px ที่ขอบ (Slides มี "ขอบโครงกระดูกซ้าย" และ "ขอบขันทองเหลืองขวา" ลักษณะนี้) · เห็นจุดเต็ม 3 จุด (ก่อน: 4 จุด)
- **ตำแหน่งเริ่มต้น (ก่อน = Slides 31):** จุดที่ 1 อยู่ x=154 → กึ่งกลางจอ (720) = กึ่งกลางระหว่างชิ้น 2 กับ 3 (c = 2.5) · ยังไม่ active · ไม่ autoplay `[S]`
- ช่วงเลื่อน c ∈ [1, 15] (c = ดัชนีชิ้นที่อยู่กึ่งกลางจอ แบบต่อเนื่อง) — ชิ้น 1 และ 15 ไปถึงกลางจอได้ `[P]` (Slides ไม่แสดงปลายทาง) · **(D34) ช่วงเป็น [1, 16] เมื่อมีเนื้อหา Reflection — จุดที่ 16 ที่ x = 720 + (16 − c)·376.6 · ดู §4.5c.7**
- จุดชิ้น i อยู่ที่ x = 720 + (i − c)·376.6

**4.5c.3 ขนาดภาพบนเส้น / mini-map / ภาพใต้เส้น — กฎคำนวณจาก `sh_thumbnail` (trim พื้นโปร่งก่อน — P-220)**
**⭐ #14 `[C]` (ลูกค้า · Google Slides "UNESCO (Comment Dev)" S18 · S20 · 2026-11-11 · `design/timeline-ref/comment-dev/slide-18/20.jpg`) — แทนกฎ rest 163×180/active 247×250 ด้านล่าง (❌ เก็บเป็นประวัติ):** "ถึงคิวแล้ว ส่วนล่าง 100% / ส่วนบนบนเส้น 60% / ที่เหลือบนเส้นที่ยังไม่ถึงคิว ~30%"
- **ฐาน 100% = ภาพใต้เส้นของชิ้นเดียวกัน** = กล่อง contain ≤ 320×265 (สูตร F1 ด้านล่าง · คอลัมน์ "ภาพใต้เส้น" ในตาราง · ไม่เกินพิกเซลไฟล์) — ชิ้นที่มี `sh_line_art` (2·3·4·5) ใช้ขนาด**ภาพสี** (ภาพ "ก่อน" ไม่มีผลต่อสัดส่วน)
- **(D39 · D45 สลับรูปบน/ล่างของชิ้น 1·7·9 แล้ว — ตัวเลขที่ §4.5c.3d ตาราง D45) ชิ้นที่มี `sh_below_image` (ภาพใต้เส้นต่างจากบนเส้น · ชิ้น 1·7·9):** ฐานของ rest/active = **ภาพบนเส้น** contain ≤320×265 (ไม่ใช่ภาพใต้เส้น) → rest = 0.30 × ฐาน · active = 0.60 × ฐาน · ภาพใต้เส้น = contain ≤320×265 ของ `sh_below_image` แยกต่างหาก · ชิ้นที่ภาพสองตำแหน่งอัตราส่วนเดียวกัน (ทุกชิ้นอื่น) ผลเท่าสูตร #14 เดิมทุกค่า · `[P]` (Figma ไม่ระบุสัดส่วนบน:ล่าง · ตัวเลขต่อชิ้น §4.5c.3d)
- **active (ถึงคิว · p=1) = 0.60 × ภาพใต้เส้น** (กว้างและสูง) · **rest (ไม่ถึงคิว · p=0) = 0.30 × ภาพใต้เส้น** · ⇒ **scale rest→active = ×2.00 เท่ากันทุกชิ้น** (แทน 1.34–1.52) · ระหว่างเลื่อน **size(p) = ภาพใต้เส้น × (0.30 + 0.30·p)** (§6.4c) · ฐานติดเส้น (380.2 − 2) · origin = กึ่งกลางล่าง · ค่าตัวเลขใน px ที่ k=1 (× k ตาม 4.5c.5)
- **ตรวจไม่ชน (คำนวณทุกคู่ติดกัน · pitch 376.6):** ที่เวลาใดมีชิ้นเดียวที่ p>0 (§6.4c) → กรณีแย่สุด = active (กว้างสุด 192) + rest ข้างเคียง (กว้างสุด 96): ช่องว่างต่ำสุด **376.6 − 96 − 48 = 232.6 px** · rest↔rest ต่ำสุด **376.6 − 48 − 48 = 280.6 px** · active กว้างสุด **192** สูงสุด **159** (ชิ้น 2·3·6·7·8·9) · ชื่อ (กว้าง ≤280) ครึ่ง 140 < 376.6 − 48 ✓ ไม่ชนวัตถุข้างเคียง
- **ชื่อเหนือวัตถุ: ล่างสุดข้อความ = บนวัตถุ active − 19 (ไม่เปลี่ยน)** → y_rel ล่างสุดข้อความ = 378.2 − active_h − 19 (ตาราง) · mini-map **ไม่เปลี่ยน** (กล่อง 48×38 · ภาพสี opacity 1)
- ❌ **(ประวัติ — แทนด้วย #14 ด้านบน)** **ค่าวัดภาพสีจริงใน Slides `[S]` (วัดซ้ำจาก slide 31/33/35/43/47/49/51/54 · ×1440/กว้างภาพ · ไม่รวมจุดและชื่อ):** การ์ด **5 Embossed Brass Basin** rest **163×113** (slide 43/47; ฐานลอย ≈23px เหนือเส้น) → active **247×173** (slide 49–51; **×1.52**) · การ์ด **6 Ivory Fire Piston** (2 ชิ้นสูงเรียงข้างกัน) rest **107×163** (slide 47/49/51) · การ์ด **3 Ganesha** active ภาพสี **200×249** (slide 33/35) / rest เป็นลายเส้น **149×180** · การ์ด **2 Singhasari** rest ลายเส้น **126×178** (คงเดิมทุกเฟรม 31–36)
- ❌ **(ประวัติ — แทนด้วย #14 ด้านบน)** **กฎที่สอดคล้องค่าวัด `[P]` (ใช้กับชิ้นที่ Slides ไม่แสดง):** rest = "contain" ในกล่อง **163×180** · active = "contain" ในกล่อง **247×250** · เช็ก: Basin ได้ 163×116 / 247×176 (วัด 163×113 / 247×173 ✓) · Ganesha ได้ 150×180 / 208×250 (วัด 149×180 / 200×249 ✓) · Singhasari ได้ 128×180 (วัด 126×178 ✓) · ชิ้นที่มีค่าวัดตรง (2, 3, 5, 6) ใช้ค่าวัดในตาราง · **D27 ภาพอัตราส่วน > 3:1** (Opium Pipe 7.5 · Siwaih 3.9 · Cold-Press 3.3 · Bas-relief 3.2): D27 ตัดสินกล่อง 320 แทน 260 (×1.23) บนฐานกฎเดิม → ฐานใหม่ 163 คูณ ×1.23 ได้ **กล่อง rest 200×180 / active 304×250** `[P]` (ถ้าใช้ 320 ตรง ๆ กับ active ×1.52 = 486 จะชนข้างเคียง: 243+160 = 403 > 376.6) — ❌ ประวัติ: กฎ #14 ใช้ 0.30/0.60 ของภาพใต้เส้น (กว้างสุด 96/192) ไม่ต้องมีกล่อง 200/304
- ❌ **(ประวัติ — แทนด้วย #14 ด้านบน)** **ยกเลิกเพดาน active 400** (รอบก่อนเป็น `[P]` ของผมเพื่อกัน 0.6px) — กฎใหม่ active กว้างสุด 304 จึงไม่ต้องมี
- ❌ **(ประวัติ — ค่าตามกฎเดิม · ค่าใหม่ดูข้อ #14 ด้านบน)** **ตรวจไม่ชนที่ pitch 376.6 (กฎเดิม):** active + rest ข้างเคียง ช่องว่างต่ำสุด **124.6px** · rest↔rest ต่ำสุด **176.6px** · ระหว่างเลื่อน (หนึ่งชิ้นขึ้นหนึ่งชิ้นลง ทุก f 0–1 ผ่านสูตร p) ต่ำสุด **124.6px** · mini-map (pitch 51.6 · กล่อง 48×38 — ไม่ขยายกล่อง) ต่ำสุด 3.6px
- ชิ้นที่มี 2 ภาพ: **การ์ด 6** ใช้ตามที่ Slides 49/51 แสดง (F4) · (#14: ขนาดกลุ่ม rest/active = 0.30/0.60 × กลุ่มใต้เส้น 174×265 = **52×80 / 104×159** — สัดส่วนกล่องภายในกลุ่มด้านล่างคงเดิม · ตัวเลข 107×163 ❌ ประวัติ) กลุ่ม rest **107×163** (ratio 0.656) · ภาพทั้งสองจัด**เป็นสัดส่วนของกลุ่ม** (ไม่ซ้อนกัน): **ซ้าย = `sh_thumbnail_2` (ชิ้นแหลม · node `1232:1746` · tall 503×1586)** box **[0, 0, 0.483, 1]** = rest 51.7×163 ที่ (0,0) · **ขวา = `sh_thumbnail` (ขวดมีฐาน · node `1232:1745` · bottle 533×1430)** box **[0.548, 0.204, 0.452, 0.796]** = rest 48.4×129.8 ที่ (58.6, 33.2) · **ฐานเสมอกัน** (ล่างทั้งสองที่ 163) · **ช่องว่างระหว่างภาพ ≈7px (6.9) · ไม่ซ้อน** · แต่ละภาพ contain ในกล่องของตัวเอง (ratio 0.317 / 0.373 ตรงกล่องพอดี) · ทั้งกลุ่มเป็นก้อนเดียวที่ scale/opacity ร่วมกัน (rest→active ×1.523 = 163×248) · (ไม่ใช้ตำแหน่งซ้อนของการ์ดเดิม `Group 34` 174×152 เพราะ Slides แคบกว่า) `[S]` · **การ์ด 9** ไม่แสดงใน Slides → ใช้ geometry กลุ่มใน Figma `Group 33` `1232:1764` 180×150 `[N]` (`1232:1766` [0,0 110.3×101.8] · `1232:1765` [28.6,35.4 151.4×114.6]) · ทั้งสอง scale เป็นกลุ่มเดียว
- ⛔ ห้ามขยายเกินพิกเซลไฟล์ (Pod Duang 226×226 → ภาพใต้เส้นไม่เกิน 226 · **#14: active บนเส้น 136 < 226 ไม่ขยายเกินไฟล์ ✓** · P-220)
- **สูตรภาพใต้เส้น (F1):** `scale = min(320/w, 265/h, 1 ถ้าเกินพิกเซลไฟล์)` บนอัตราส่วนไฟล์ trim (การ์ด 6/9 = อัตราส่วนกลุ่ม 107:163 / 180:150) · ปัดเป็นจำนวนเต็ม · **ตัวอย่างตรวจ (ค่าตัดสิน orchestrator ≈):** Ganesha 221×265 ✓ · Molar 320×160 (≈320×161 ✓) · **Basin คำนวณจากไฟล์ 320×229** ต่างจากค่าวัดใน ref-3 ≈317×223 (ratio 1.42 vs ไฟล์ 1.399 — ขอบโปร่ง/วัดจากภาพ ±3px) → ใช้ค่าจากไฟล์ (เพดาน 320 ตรงกัน) `[P]`
| card | วัตถุ | ไฟล์ trim px (อัตราส่วน) | **ภาพใต้เส้น 100%** (กล่อง ≤320×265 · กึ่งกลาง x720/y_rel561 · ไม่เกินพิกเซลไฟล์) | **rest 30%** (w×h) | **active 60%** (w×h) | สเกล | ที่มา | ล่างสุดชื่อ y_rel (= 378.2 − active_h − 19) | mini-map (≤48×38 · ไม่เปลี่ยน) |
|---|---|---|---|---|---|---|---|---|---|
| 1 | elephant leg bone | **(D45) บนเส้น `bbc1e161` trim 1295×701 (1.85:1)** · ❌ ก่อน D45 `4953122e` 1360×720 | **(D45) ฐานบนเส้น 320×173** · ใต้เส้น `sh_below_image` = `4953122e` 1360×720 → 320×169 | **96×52** | **192×104** | ×2.00 | `[C]` + D45 `[P]` | **255.2** | **48×26** |
| 2 | singhasari ganesha | 1340×1885 (0.71:1) | 188×265 | **56×80** | **113×159** | ×2.00 | `[C]` | 200.2 | 27×38 |
| 3 | ganesha | 1563×1878 (0.83:1) | 221×265 | **66×80** | **133×159** | ×2.00 | `[C]` | 200.2 | 32×38 |
| 4 | elephant molar | 767×383 (2.0:1) | 320×160 | **96×48** | **192×96** | ×2.00 | `[C]` | 263.2 | 48×24 |
| 5 | embossed brass | 1343×960 (1.4:1) **(D39: `cba74a7d` ขนาด/crop เท่า `5d9931b4` ทุกค่า → ค่าในแถวไม่เปลี่ยน)** | 320×229 | **96×69** | **192×137** | ×2.00 | `[C]` | 222.2 | 48×34 |
| 6 | ivory fire piston | group 107:163 (Slides 49/51 · box ซ้าย/ขวา ดูข้อ "ชิ้นที่มี 2 ภาพ") (0.656:1) | 174×265 | **52×80** | **104×159** | ×2.00 | `[C]` | 200.2 | 25×38 |
| 7 | elephant and mahout | **(D45) บนเส้น `555d365b` trim 1150×898 (1.28:1)** · ❌ ก่อน D45 `621646b5` 2842×3844 | **(D45) ฐานบนเส้น 320×250** · ใต้เส้น `sh_below_image` = `621646b5` (contain 320×265) **196×265** | **96×75** | **192×150** | ×2.00 | `[C]` + D45 `[P]` | **209.2** | **48×37** |
| 8 | patola | 611×580 (1.05:1) | 279×265 | **84×80** | **167×159** | ×2.00 | `[C]` | 200.2 | 40×38 |
| 9 | elephant shaped relic | **(D45) บนเส้น `1c0fc05b` trim 3405×1940 (1.755:1)** · ❌ ก่อน D45 กลุ่ม 180×150 | **(D45) ฐานบนเส้น 320×182** · ใต้เส้น = กลุ่ม 2 ภาพเดิม `sh_below_image` `b1ba8a8a` + `sh_below_image_2` `1c3aa3a1` (180×150 · 1.2:1) → **318×265** | **96×55** | **192×109** | ×2.00 | `[C]` + D45 `[P]` | **250.2** | **48×27** |
| 10 | pod duang | 226×226 (1.0:1) | 226×226 | **68×68** | **136×136** | ×2.00 | `[C]` | 223.2 | 38×38 |
| 11 | hand carved ivory | 2017×270 (7.47:1) | 320×43 | **96×13** | **192×26** | ×2.00 | `[C]` | 333.2 | 48×6 |
| 12 | tin ingot | 954×637 (1.5:1) | 320×214 | **96×64** | **192×128** | ×2.00 | `[C]` | 231.2 | 48×32 |
| 13 | wooden cold press | 936×287 (3.26:1) | 320×98 | **96×29** | **192×59** | ×2.00 | `[C]` | 300.2 | 48×15 |
| 14 | siwaih | 981×252 (3.89:1) | 320×82 | **96×25** | **192×49** | ×2.00 | `[C]` | 310.2 | 48×12 |
| 15 | bas relief of a royal | 4096×1270 (3.23:1) | 320×99 | **96×30** | **192×59** | ×2.00 | `[C]` | 300.2 | 48×15 |
- **ชิ้นที่ไม่อยู่กลาง (D35 · แทนข้อ D32/D33 ❌ "ชิ้น 1–4 ใช้ลายเส้น"):** ทุกชิ้น 1–15 ใช้ภาพสี `sh_thumbnail` ขนาด rest (ตารางบน · **#14 = 30% ของภาพใต้เส้น**) · opacity 0.6 `[P]` ค่าเดียว · ไม่ทำเทา/ไม่ทำ filter (เหตุผล: จางเล็กน้อยตามนักออกแบบ 0.5–0.7 · ยังเห็นสีพอรู้ชิ้น) · active = opacity 1 ภาพสีเดียวกัน เปลี่ยนแค่ opacity+scale (ไม่มี cross-dissolve ที่แถวบน)
- mini-map: ใช้ภาพสี `sh_thumbnail` opacity 1 ทุกชิ้น `[P]` (**D32/D35: ไม่เปลี่ยน — ยืนยันตาม D33/D35 = ภาพสี** · ภาพอ้างอิง Section 51 `image 8` (`5b53bbdc`) แถบ "Bar" ยังแสดง 4 ชิ้นแรกเป็นลายเส้นจางในกรอบไฮไลต์ แต่ D32 พูดถึงเฉพาะชิ้นบนเส้น → รอเจ้าของยืนยันว่าจะให้ mini-map ใช้ลายเส้นด้วยไหม → P-228 ปิด)
**4.5c.3b ภาพ "ก่อน" ▶ "หลัง" ใต้เส้น (D35/D36 · แทน "ลายเส้นต่อชิ้น/แถวบน" ของ D32/D33 ❌ · Figma `3101:609` section `Section 51` `3102:1028` > `Frame 114443` > `Frame 114464` · แต่ละการ์ดมีคู่ ก่อน ▶ หลัง 320×180 · ไฟล์ `design/assets/lineart/<card_order>-<slug>.png` พื้นโปร่ง RGBA trim ขอบโปร่ง ไม่ resample · manifest `design/assets/lineart/manifest.json` · สคริปต์ `design/tools/export-lineart.py` (ชื่อโฟลเดอร์เดิม — เนื้อหาคือ "ภาพก่อน" ไม่จำเป็นต้องเป็นลายเส้น) · ไม่เรียก Figma API เพิ่ม)**
**ใช้กับ 4 ชิ้นเท่านั้น (D36 · คู่ ▶ ใน Section 51 · ภาพเจ้าของ `design/timeline-ref/owner-under-4pairs.png`)**
| card | วัตถุ | ภาพ "ก่อน" (ซ้ายของ ▶) = `sh_line_art` | ไฟล์ (px trim · อัตราส่วน) | Figma node · imageRef (ต้นฉบับ px) | ภาพ "หลัง" (ขวา ▶) = `sh_thumbnail` | กล่องใต้เส้น ภาพก่อน (contain ≤320×265 · @1440) | ตรวจ DPR2 (≥ 2× กล่อง) |
|---|---|---|---|---|---|---|---|
| 2 | singhasari ganesha | ลายเส้น Singhasari Ganesha | `02-singhasari-ganesha.png` 629×884 (0.712) | `3103:1040` · `7e07d19a…` (1920×1080) | ภาพสี Ganesha (`3fba9728…`) | **189×265** (ภาพหลัง 188×265) | 378×530 ≤ 629×884 ✓ |
| 3 | ganesha | ลายเส้น Ganesha | `03-ganesha.png` 693×831 (0.834) | `3103:1056` · `14cb9029…` (1920×1080) | ภาพสี Ganesha (`a0d982dc…`) | **221×265** (ภาพหลัง 221×265) | 442×530 ≤ 693×831 ✓ |
| 4 | elephant molar | **ไอคอนช้างลายเส้นทั่วไป** (ไม่ใช่รูปกราม — D36 ยืนยันใช้เป็นภาพก่อน) | `04-elephant-molar.png` 648×469 (1.382) | `3246:3914` · `2511e717…` (942×913) | ภาพสีกราม | **320×232** (ภาพหลัง 320×160) | 640×464 ≤ 648×469 ✓ |
| 5 | embossed brass basin | **ช้างทองเหลืองตัดชิ้นเล็ก (สี · ไม่ใช่ลายเส้น)** | `05-embossed-brass-basin.png` 567×382 (1.484) · ไม่มีพื้นให้ trim | `3160:1510` ("กระดูกช้าง 3" · ซ้ายของ ▶ · 320×180 · `87965132…` 567×382) | **(D39) ภาพ "หลัง" = `sh_thumbnail` ใหม่ = `cba74a7d…` (`3160:1508` ขวาของ ▶ · 1344×961 ตัดพื้น)** — แทน `5d9931b4` (ปิด P-230) · ภาพบนเส้นเป็นภาพเดียวกัน | **(D39) ภาพก่อน 198×134** (= `before_scale` 0.62 × ภาพหลัง 320×229 → กล่อง 198.4×141.8 · ❌ เดิม 320×216) · ภาพหลัง **320×229** | **396×268 ≤ 567×382 ✓** (เหลือ ×2.86 — แก้ปัญหา ×1.77 เดิม) |
- **ยืนยันซ้าย/ขวาชิ้น 5 (ตำแหน่ง x เทียบ ▶):** frame `3103:1097` > `Frame 114483` `3160:1504` (1000×281) = 2 ช่อง 500×281: `3160:1510` x=4207 (ซ้าย) · `3160:1508` x=4707 (ขวา) · ▶ ถูกวาดอยู่ระหว่าง (ไม่เป็น node แยก) · ภาพเจ้าของ: ช้างเล็กสี ▶ ขัน ✓ · `5d9931b4…` (180×129 · x4611 · "ขันช้าง6 1" ในเฟรมข้อความ `3103:1106`) **ไม่ใช่ภาพในคู่** — เป็นภาพขันขนาดเล็กข้างข้อความ (= `sh_thumbnail` เดิม) ไม่ใช่ "ก่อน" · **(D39) แก้: ไฟล์ 08:23Z คู่ชิ้น 5 เป็น 320×180 ที่ y6475 (ใต้แถวข้อความ) และเพิ่ม ▶ `3274:1053` · ขันเล็ก `5d9931b4` (`3103:1107`) ไม่ใช่ภาพในคู่ แต่เจ้าของเห็นขันเล็กเป็น `cba74a7d` แล้ว (ภาพ 15:47) → บนเส้น = ใต้เส้น = `cba74a7d` (§4.5c.3d)**
- **ชื่อ node `กระดูกช้าง 3/4` ในชิ้น 5 เป็นชื่อเหลือจากการคัดลอกของนักออกแบบ** (ไม่ใช่ชิ้น 1) — ใช้ตำแหน่ง+เนื้อภาพเป็นหลักฐาน
- **ชิ้น 1 และ 6–15: ไม่มีคู่ ▶ ใน Figma → ไม่มี `sh_line_art` → ภาพใต้เส้นเป็นภาพสีเหมือนเดิม (§6.4c แถวเดิม · ไม่มี before/after)** · ทีมเติมภายหลังได้ที่ admin ช่อง `sh_line_art` (D33) · ไม่ต้องแก้โค้ด
- **raster ใบเดียวที่รวมหลายชิ้น:** `Order 1` (`3103:1381` · 5009×323 · imageRef `b3129050…`) = แถบอ้างอิงรวม 15 ชิ้นที่สถานะ rest ของ D32 (ลายเส้น 1–4) — **❌ ไม่ใช่แบบแล้ว (D35)** ไม่ใช้ · `image 8` (`3102:1026`) = ภาพอ้างอิง D32 (ลายเส้นซ้อนบนสีที่แถวบน) — ❌ แทนด้วย D35 (เปลี่ยนที่ภาพใต้เส้น)
- **ภาพก่อน/หลังไม่ต้องซ้อนทับตรงกัน:** คนละอัตราส่วนได้ (ชิ้น 4: ไอคอนช้าง 1.38 vs กราม 2.0 · ชิ้น 5: ช้างเล็ก vs ขัน) → contain ต่อภาพ กึ่งกลางกล่องเดียวกัน (x 720 / y_rel 561) ไม่พยายามจัดให้ตรง · ชิ้น 2/3: ไฟล์ก่อน/หลัง canvas 1920×1080 เดียวกัน ขนาด trim ใกล้เคียง (629×884 vs 628×884 ; 693×831 vs 691×827) → ซ้อนตรงกัน ±1px
- **สีของภาพก่อน:** ลายเส้นดำ/เทาเข้มบนพื้นโปร่ง (ชิ้น 2·3·4) · ชิ้น 5 เป็นภาพสีทอง — ไม่ recolor/ไม่ทำเทา · ไม่แสดงลูกศร ▶ ใน UI (เป็นแค่สัญลักษณ์ใน Figma ว่า "ก่อน → หลัง")
- **mini-map/แถวบน:** ภาพสี `sh_thumbnail` เหมือนเดิม (D35) · ไม่ใช้ `sh_line_art` ที่อื่น

**4.5c.3c เทียบ `3101:609` กับ §4.5c ปัจจุบัน — ที่เปลี่ยนนอกจากลายเส้น (ตรวจแล้ว)**
- แคนวาส `3101:609` ("Page : Detail Zone4 ALL" · ประเภท CANVAS) มี 3 section: `Zone 4` (`3101:610` ← `Desktop - zone 6` `3101:611` 1440×1096 + `Section 50` `3101:775` ภาพอ้างอิง Slides 3 ภาพ `4d9fa11a`/`67e6e00b`/`dc288f1a`) · **`Section 51` `3102:1028` (ใหม่ — ที่มาของลายเส้น)** · `Zone 5` `3246:4498` (`Desktop - 1513` + `Section 54`: ภาพ `Zone4 header-03…19` 17 ใบ · ไม่ใช่ลายเส้น timeline — ไม่ได้ตรวจเนื้อหา) · ไม่มี frame "ก่อน/ระหว่าง/active" แยกเป็นสถานะ — `Frame 114464` มี 16 frame = การ์ด 15 + Reflection (แต่ละการ์ดวางภาพเหนือเส้น 500×281 หรือคู่ ลายเส้น→▶→สี 320×180 + ภาพใต้เส้น + ข้อความ) และภาพอ้างอิง `image 8`
- `3101:611` (`Desktop - zone 6` ที่นักออกแบบปรับ): เทียบกับ `1232:632` (ในสำเนา 2026-10-27) → **ตัด raster `ROOM4 ele 2` 11000×791 (`791ae91b`) ออก เหลือแถวการ์ดข้อมูล** · ชื่อการ์ดชิ้น 1 "Elephant Leg Bone" (เดิม "…(Elephas maximus indicus)") · บรรทัด "SECTION : …" ในกล่อง metadata **ถูกลบ** → สอดคล้อง D26/D31 และ §4.5c (ไม่แสดง Section) อยู่แล้ว · ตำแหน่ง Back `[160,990]` ตามเฟรมสูง 1096 (§4.5c.1 ใช้ x=160 อยู่แล้ว) → **ไม่ต้องแก้ค่าใน §4.5c**
- **ไม่มีค่า layout/ขนาด/ตำแหน่ง/opacity/transition ใหม่ในไฟล์** — ขนาด rest ของลายเส้นไม่ได้ระบุใน node (ภาพในบอร์ดเป็นกล่อง 320×180 = canvas 1920×1080 ÷6 ไม่ใช่ขนาดบนเส้น) → ใช้ค่าวัดจาก Slides `[S]` ตาม 4.5c.3b · opacity/transition ลายเส้น→สี = `[P]` (หลักฐาน `[R]` ภาพ ①) §6.4c · **(D35/D36) ขนาด rest/opacity ของแถวบนกลับเป็นแบบ D26 ทั้งหมด · ที่ใหม่จริงมีเฉพาะภาพ "ก่อน" ใต้เส้น 4 ชิ้น (4.5c.3b) — ค่าเวลา/α = `[P]` §6.4c**


**4.5c.3d (D39) ตรวจรูปบนเส้น/ใต้เส้น ทั้ง 15 ชิ้น เทียบ Figma Section 51 + ค่าที่ธีมต้องรู้**
**⭐ D45 (2026-11-11 · เจ้าของดูเว็บจริง · `design/timeline-ref/owner-swap-1/7/9.png` · P-187 ✅ ปิด) ชิ้น 1·7·9 สลับรูปบนเส้น ↔ ใต้เส้น — ทับข้อความ D39 ของ 3 ชิ้นนี้ทุกจุดด้านล่าง (ชิ้นอื่นไม่เปลี่ยน · 0 คำขอ Figma API):**
| ชิ้น | บนเส้น + mini-map (`sh_thumbnail` · image-map `all`) | ใต้เส้น (`sh_below_image` · image-map `below`) | ฐานบนเส้น → rest 30% / active 60% / mini | ชื่อล่างสุด y_rel |
|---|---|---|---|---|
| 1 | `bbc1e161` โครงกระดูกช้างลายเส้น+คน (3103:1037) | `4953122e` กระดูกขาสี (3102:1031) → 320×169 | 320×173 → **96×52 / 192×104 / 48×26** | **255.2** (เดิม 258.2) |
| 7 | `555d365b` kendi+คนลายเส้น (3103:1187) | `621646b5` kendi เล็กมีน้ำ (3103:1181) → **196×265** | 320×250 → **96×75 / 192×150 / 48×37** | **209.2** (เดิม 200.2) |
| 9 | `1c0fc05b` 2 ชิ้นมีเส้นขอบ (3103:1220) | กลุ่ม 2 ภาพเดิม `b1ba8a8a` (`sh_below_image` · 3103:1233) + `1c3aa3a1` (`sh_below_image_2` · 3103:1234) → **318×265** (กล่องภายในกลุ่มคงเดิม §4.5c.3 "ชิ้นที่มี 2 ภาพ") | 320×182 → **96×55 / 192×109 / 48×27** | **250.2** (เดิม 200.2) |
- **กฎ:** ฐาน rest/active = **ภาพบนเส้นใหม่** contain ≤320×265 (D39 กฎเดิม · คำนวณใหม่ตามรูปบน) · ภาพใต้เส้น = contain ≤320×265 ของรูปล่าง · ชื่อล่างสุด = 378.2 − active_h − 19 · admin แก้ได้ (สลับ `sh_thumbnail` ↔ `sh_below_image`) · ค่าที่ import ใช้จริง = `scripts/import/image-map.json` `timeline_items.1/.7/.9` (`all` = บน · `below` = ล่าง · อ่านก่อน seed ใน `refs.php sh_import_timeline_below_refs()`) · seed `timeline-elephant.json` ตรงกันแล้ว (`thumbnail_nodes`/`thumbnail_refs` = บน · `below_ref`/`below_node` (+ `below_ref_2`/`below_node_2` ชิ้น 9) = ล่าง · ฟิลด์ `d45` ต่อชิ้น)
- **ตรวจไม่ชน (ใหม่):** active กว้างสูงสุด 192 (ชิ้น 1·7·9) ไม่เกินค่าเดิม · rest ≤96 → ช่องว่างต่ำสุด 376.6 − 96 − 48 = **232.6** (§4.5c.3 #14) ไม่เปลี่ยน · active สูงสุดยัง 159 (ชิ้น 2·3·6·8) · เช็ก DPR2 รูปบน: ชิ้น 1 384×208 ≤ 1295×701 ✓ · ชิ้น 7 384×300 ≤ 1150×898 ✓ · ชิ้น 9 384×218 ≤ 3405×1940 ✓ · รูปล่าง: ชิ้น 1 640×338 ≤ 1360×720 ✓ · ชิ้น 7 392×530 ≤ 2842×3844 ✓ · ชิ้น 9 (กลุ่ม 318×265 ×2 = 636×530 — ไฟล์กลุ่มเดิมใช้ที่ขนาดนี้อยู่แล้ว ไม่แย่ลง)
- **ธีม (wp-dev) ต้องรู้:** `geometry.php` ชิ้น 1·7·9 = ค่าในตาราง D45 ด้านบน (รูปบน = ฐาน) · key `below` [w,h] ตามรูปล่าง: ชิ้น 1 [320,169] · 7 [196,265] · 9 [318,265] (ชิ้น 9 กลุ่ม 2 ภาพ) · ตำแหน่งชื่อล่างสุดต่อชิ้นตามตารางหลัก §4.5c.3 (255.2 / 209.2 / 250.2) · **คำนวณจากรูปจริง wp-dev ยืนยันอีกครั้ง — ค่านี้คำนวณจากตาราง trim ใน §4.5c.3d ไม่ได้วัดภาพใหม่ `[P]`**
**ที่มา:** Figma `3101:609` › `Section 51` `3102:1028` › `Frame 114443` `3101:819` › `Frame 114464` `3101:820` (17 frame ลูก = การ์ด 15 + Reflection + ซ่อน 1) · ใช้ `design/figma-file.json` lastModified **2026-11-11T08:23:07Z** (ตรงกับ `figma-3101-609.json` 03:55:12Z ทุกอย่าง ยกเว้นคู่ ▶ ชิ้น 5 ถูกย่อจาก 500×281 เป็น 320×180 + เพิ่ม ▶ `3274:1053`) · **0 คำขอ Figma API** · ⚠ ภาพเจ้าของ 15:47 (= 08:47Z) `owner-basin-new-image.png` แสดงขันเล็กบนเส้นเป็น `cba74a7d` แต่ไฟล์ที่ดึง 08:23Z ยังเป็น `5d9931b4` (`3103:1107`) → นักออกแบบแก้ Figma หลังดึง → ชิ้นอื่นอาจเปลี่ยนอีก → P-261 (ขอดึง node `3102:1028` ใหม่ 1 คำขอ)
**นิยาม:** บนเส้น = กล่องเล็ก 180–200 กว้างในแถว "ภาพ+ข้อความ" (`Frame 1146xx`) · ใต้เส้น = กล่อง 500×281 บนสุดของการ์ด (ชิ้น 2–5 = ภาพ "หลัง" ขวาของ ▶ ในคู่ 320×180) · "ปัจจุบัน" = `scripts/import/image-map.json` timeline_items (sh_thumbnail/_2 · ใช้ทั้งบนเส้นและใต้เส้น) + `line_art_ref` (ภาพก่อน ชิ้น 2–5)
| # | บนเส้น Figma (node · ref · กล่อง) | บนเส้น ปัจจุบัน | ใต้เส้น Figma (node · ref · กล่อง) | ใต้เส้น ปัจจุบัน | ผล |
|---|---|---|---|---|---|
| 1 | `3102:1031` `4953122e` 200×112 | `4953122e` | `3103:1037` **`bbc1e161`** 500×281 (โครงกระดูกช้างลายเส้น + คนยืน + วงกลมกระดูก 1421×1080 trim 1295×701) | `4953122e` (กระดูกสี) | บน ✅ ตรง · **ใต้ ❌ ต่าง** → `below_ref` `bbc1e161` ⚠ ขัด P-224/D27 (default ของ orchestrator ไม่ใช่เจ้าของ · D39(3) ใหม่กว่า) → P-260 · **D45 (2026-11-11) สลับ:** บน = `bbc1e161` · ใต้ = `4953122e` |
| 2 | `3103:1048` `3fba9728` 180×168 | `b0afe5ba` | หลัง `3103:1054` `3fba9728` 320×180 (ก่อน `3103:1040` `7e07d19a`) | `b0afe5ba` + ก่อน `02-…png` | ✅ ตรง (**ภาพเดียวกัน คนละไฟล์**: trim 628×884 vs 1340×1885 · ssim 0.96 · คงไฟล์เดิมที่ละเอียดกว่า) |
| 3 | `3103:1075` `bc3f275a` 180×187 | `bc3f275a` | หลัง `3103:1057` `a0d982dc` 320×180 (ก่อน `3103:1056` `14cb9029`) | `bc3f275a` + ก่อน `03-…png` | ✅ ตรง (a0d982dc = ภาพเดียวกับ bc3f275a · ssim 0.87 · trim 691×827 vs 1564×1878) |
| 4 | `3246:3904` `378ea9bd` 180×95 | `378ea9bd` | หลัง `3246:3916` `378ea9bd` (ก่อน `3246:3914` `2511e717`) | `378ea9bd` + ก่อน `04-…png` | ✅ ตรง |
| 5 | `3103:1107` `5d9931b4` 180×129 ในไฟล์ 08:23Z · **ภาพเจ้าของ 15:47 = `cba74a7d`** | `5d9931b4` | หลัง `3160:1508` **`cba74a7d`** 320×180 (ก่อน `3160:1510` `87965132`) | `5d9931b4` + ก่อน `05-…png` | **❌ ต่าง (D39)** → `thumbnail` = `cba74a7d` (บน+ใต้เส้น) · ไม่ใส่ `below_ref` · ก่อนเล็กลง `before_scale` |
| 6 | `3103:1126/1127` กลุ่ม `f8e2d79d` + `8dcebc79` | เหมือนกัน | `3103:1134/1135` กลุ่มเดียวกัน (Figma วางซ้อนกัน 369×322) | เหมือนกัน (กลุ่มไม่ซ้อน 174×265 ตาม Slides) | ✅ ตรง ref · layout ต่างโดยตั้งใจ (Slides 49/51 `[S]`) → P-262 |
| 7 | `3103:1181` **`621646b5`** 180×168 (kendi เล็ก + น้ำ) | `867b2833` | `3103:1187` **`555d365b`** 500×281 (kendi + คนลายเส้น 2 คน) | `867b2833` | **❌ ต่างทั้งคู่ (D39)** → `thumbnail` = `621646b5` · `below_ref` = `555d365b` · **D45 สลับ:** บน = `555d365b` · ใต้ = `621646b5` |
| 8 | `3103:1213` `07e22a68` 200×188 | `07e22a68` | `3103:1201` `a3d77ee3` 500×281 | `07e22a68` | ✅ ตรง (ผ้าผืนเดียวกัน ตัดขอบต่างเล็กน้อย 588×554 vs 612×582 · คงเดิม) |
| 9 | `3103:1233/1234` กลุ่ม `b1ba8a8a` + `1c3aa3a1` | เหมือนกัน | `3103:1220` **`1c0fc05b`** 500×281 (ภาชนะ 2 ใบมีเส้นขอบดำ รวมในภาพเดียว 4096×2305 trim 3405×1940) | กลุ่ม 2 ภาพ | บน ✅ ตรง · **ใต้ ❌ ต่าง** → `below_ref` `1c0fc05b` · **D45 สลับ:** บน = `1c0fc05b` · ใต้ = กลุ่ม `b1ba8a8a` + `1c3aa3a1` |
| 10 | `3103:1255` `bf841c7f` | เหมือนกัน | `3103:1241` `bf841c7f` | เหมือนกัน | ✅ ตรง |
| 11 | `3103:1274` `ba68e852` | เหมือนกัน | `3103:1262` `ba68e852` | เหมือนกัน | ✅ ตรง |
| 12 | `3103:1294` `32fba036` | เหมือนกัน | `3103:1282` `32fba036` | เหมือนกัน | ✅ ตรง |
| 13 | `3103:1313` `730ba23e` | เหมือนกัน | `3103:1301` `60b979fd` 527×166 | `730ba23e` 936×287 | ✅ ตรง (ไม้พิมพ์ชิ้นเดียวกัน · ของเราละเอียดกว่า) |
| 14 | `3103:1332` `9c5706f8` | เหมือนกัน | `3103:1320` `148537c0` 982×253 | `9c5706f8` 981×252 | ✅ ตรง (ssim 0.94) |
| 15 | `3103:1353` `91cfd6ae` | เหมือนกัน | `3103:1341` `758e3fe7` 1855×575 | `91cfd6ae` 4096×1270 | ✅ ตรง (ssim 0.99 · อัตราส่วน 3.226 vs 3.225) |
- **สรุป:** ต่าง 4 ชิ้น — ชิ้น 1 (ใต้เส้น) · **5 (บน+ใต้เส้น)** · **7 (บน+ใต้เส้น)** · 9 (ใต้เส้น) · ตรงด้วยเนื้อภาพ 11 ชิ้น (6 ชิ้น = ref เดียวกันเป๊ะ · 2·3·8·13·14·15 = ภาพเดียวกันคนละไฟล์ → คงไฟล์เดิมที่ละเอียดเท่าหรือกว่า ไม่ downgrade) · ไฟล์ภาพที่ใช้ทั้งหมดมีใน `design/assets/` แล้ว (ไม่ต้องดาวน์โหลดเพิ่ม · 0 ไฟล์ใหม่)
- **ไม่ใช่ภาพชิ้นจริง (ไม่ใช้):** การ์ด Reflection `3103:1359` มีภาพ 500×281 `758e3fe7` = ภาพชิ้น 15 ซ้ำ (ตกค้างจากการคัดลอก · D34 Reflection ไม่มีรูป → ไม่ใช้) · `Frame 114485` ในการ์ด 4/7–16 (ภาพ `621646b5`/`e407dd39`/`428c1509` ×3) `visible:false` · frame `3101:915` ซ่อน
- **ฟิลด์ `sh_below_image`** (image · ไม่บังคับ · §5.4): ว่าง = ภาพใต้เส้น = `sh_thumbnail` (ชิ้น 2–6, 8, 10–15 · ชิ้น 5 หลัง D39) · ใส่ = ภาพใต้เส้นใช้ภาพนี้แทน (ชิ้น 1·7·9) · **ใช้เฉพาะภาพใต้เส้น** (ไม่กระทบแถวบนเส้น/mini-map/หน้า detail) · seed `items[].below_ref` = ชื่อไฟล์ใน `design/assets/` (`<imageRef เต็ม>.png`) · ภาพก่อน (`sh_line_art`) ยังซ้อนใต้ภาพหลัง = `sh_below_image ?: sh_thumbnail`
**กฎขนาดที่คำนวณใหม่ (ค่าที่ k=1 · วิธี §4.5c.3 F1 · trim alpha>8):**
| card | ภาพบนเส้น (trim · อัตราส่วน) | ฐานบนเส้น (contain 320×265) | rest 30% | active 60% | mini (≤48×38) | ภาพใต้เส้น (trim → contain 320×265) | เหตุที่เปลี่ยน |
|---|---|---|---|---|---|---|---|
| 1 | **(D45)** `bbc1e161` trim 1295×701 (1.847) · src 1421×1080 (trim ตามตาราง D39 ด้านบน · crop ธีมคำนวณ alpha>8 เอง) | 320×173 | **96×52** (เดิม 96×51) | **192×104** (เดิม 192×101) | **48×26** (เดิม 48×25) | ใต้เส้น `4953122e` 1360×720 (1.89) → **320×169** | D45 สลับ · ความสูง active 104 → ชื่อล่างสุด **255.2** (เดิม 258.2) |
| 5 | `cba74a7d` 1343×960 (1.40) · crop [1,1,1344,961] | 320×229 | 96×69 | 192×137 | 48×34 | = บนเส้น **320×229** | เปลี่ยนแค่ไฟล์ (ขนาด/crop เท่า `5d9931b4`) · ภาพก่อน → `before_scale` |
| 7 | **(D45)** `555d365b` 1150×898 (1.281) · crop [101,111,1251,1009] · src 1421×1080 | **320×250** (เดิม 196×265) | **96×75** (เดิม 59×80) | **192×150** (เดิม 118×159) | **48×37** (เดิม 28×38) | `621646b5` **2842×3844 (0.739)** · crop [145,76,2987,3920] · src 3403×4096 → **196×265** | D45 สลับ · active สูง 150 (เดิม 159) → ชื่อล่างสุด **209.2** (เดิม 200.2) |
| 9 | **(D45)** `1c0fc05b` 3405×1940 (1.755) · crop [485,250,3890,2190] · src 4096×2305 | **320×182** (เดิม 318×265) | **96×55** (เดิม 95×80) | **192×109** (เดิม 191×159) | **48×27** (เดิม 46×38) | กลุ่ม 180×150 (1.2) `b1ba8a8a` + `1c3aa3a1` → **318×265** | D45 สลับ · active สูง 109 (เดิม 159) → ชื่อล่างสุด **250.2** (เดิม 200.2) |
| 2–4, 6, 8, 10–15 | ไม่เปลี่ยน — ใช้ตารางด้านบน | | | | | = บนเส้น | — |
- **เช็ก ≥2× (DPR2) ของภาพใต้เส้นใหม่:** ชิ้น 1 640×346 ≤ 1295×701 ✓ · ชิ้น 7 640×500 ≤ 1150×898 ✓ · ชิ้น 9 640×364 ≤ 3405×1940 ✓ · ชิ้น 7 บนเส้น active 118×159 ×2 = 236×318 ≤ 2842×3844 ✓ · ชิ้น 5 ภาพก่อน ดูด้านล่าง
- **(ก่อน D45 · ใช้ไม่ได้ — ดูตาราง D45 ด้านบน) ตรวจไม่ชน:** active กว้างสุดยังเป็น 192 (ชิ้น 1) · ชิ้น 7 active 118 < 192 → ช่องว่างต่ำสุด 232.6px (§4.5c.3 #14) ไม่เปลี่ยน
**`before_scale` ชิ้น 5 `[P]` (ค่าคงที่ต่อชิ้นในธีม — ไม่ใช่ฟิลด์ admin · ค่า = 0.62):**
- วัดจาก Figma คู่ ▶ ชิ้น 5 (กล่อง 320×180 ทั้งสอง · ภาพกึ่งกลางทั้งสองแกน · aspect คงที่) · `imageTransform` ของ STRETCH = node→image (ส่วนที่เห็นของไฟล์ = [tx, tx+a] × [ty, ty+d]) → ภาพก่อน `3160:1510` `87965132` (567×382) วาด **137.2×92.5** (a 2.3319 · tx −0.6673 · d 1.9452 · ty −0.4730 → ภาพกินกลางกล่อง 42.9% × 51.4%) · ภาพหลัง `3160:1508` `cba74a7d` (1344×961) วาด **222.7×159.4** (a 1.4368 · tx −0.2189 · d 1.1293 · ty −0.0669) → ก่อน÷หลัง = **0.616 (กว้าง) · 0.580 (สูง)** · ตรงกับภาพเจ้าของ (`owner-basin-before-smaller.png`: ช้าง ≈80px ต่อขัน ≈135px ในกรอบ 190px = 0.59×)
- **กฎ:** ภาพก่อน = contain (สัดส่วนไฟล์ก่อน 1.484) ในกล่อง **(0.62 × W_U) × (0.62 × H_U)** · W_U×H_U = ขนาดภาพหลังใต้เส้น = 320×229 → กล่อง 198.4×141.8 → **ภาพก่อน 198×134 px** (จำกัดด้านกว้าง) · กึ่งกลางทั้งสองแกนในกล่องเดียวกับภาพหลัง (x720 / y_rel561) · ตรง Figma ±1px (Figma 0.616 → 197×133)
- ชิ้น 2·3: Figma ก่อน/หลัง canvas เดียวกัน ซ้อนตรงกัน → `before_scale` = 1 (ค่าเดิม) · ชิ้น 4: Figma ก่อน:หลัง กว้าง 186.4:177.0 (1.05) ≈ ค่าเดิม contain แยกต่อภาพ → ไม่ต้องมี · มีเฉพาะชิ้น 5 · `before_scale` ใช้เฉพาะ phase=ก่อน (§6.4c) ไม่กระทบ cross-fade/α
**(ก่อน D45 — หลัง D45 `555d365b` เป็นรูปบนเส้นชิ้น 7 · ตัวเลขที่ตาราง D45) ภาพใต้เส้นชิ้น 7 (`555d365b` kendi + คนลายเส้น 2 คน) `[P]`:** กล่อง **≤320×265 ใช้ได้ ไม่ต้องกว้างกว่า** — trim 1150×898 (1.281) กว้างกว่าอัตราส่วนกล่อง (1.208) → จำกัดด้านกว้าง → **320×250** · kendi เองในภาพ (ส้ม 773×873 px = 67% ของความกว้าง trim) = **215×243** (เดิม `867b2833` 169×265 → กว้างขึ้น 27% · สูงลด 8%) · คนซ้าย/ขวาอยู่ในกรอบ 320 ไม่ล้น · **ไม่แนะนำขยาย**: ถ้าให้ kendi สูง 265 เท่าเดิมต้องกว้าง 349 → ขอบขวาภาพ 894.5 ห่าง metadata (x907) เหลือ 12.5px (ปกติ 27) · ภาพบนเส้น active (kendi ส้มสูง ≈125) = ≈51% ของ kendi ใต้เส้น (243) — ต่ำกว่า 60% เล็กน้อยเพราะภาพบนเส้นมีน้ำ/ขอบโปร่ง (ภาพบนเส้น 0.74 ต่างอัตราส่วนกับใต้เส้น 1.28) · ยอมรับ `[P]` (Figma ไม่ระบุสัดส่วนระหว่างสองภาพ)
**ธีม (wp-dev) ต้องทำ (D39 · ชิ้น 1·7·9 ถูกแทนด้วยตาราง D45 ด้านบน):** (1) `geometry.php` ชิ้น 5 เปลี่ยนไฟล์เป็น `cba74a7d` (src [1344,961] crop [1,1,1344,961] เท่าเดิม) · ชิ้น 7 `photo` [196,265] · `mini` [28,38] · `ratio` 2842/3844 · images = `621646b5` src [3403,4096] crop [145,76,2987,3920] · เพิ่มคีย์ `below` [w,h] + `below_images` (src/crop ของ `sh_below_image`) เมื่อมี `below_ref`: ชิ้น 1 [320,173] · 7 [320,250] · 9 [320,182] · (2) `before_scale` 0.62 ชิ้น 5 (3) importer: seed `below_ref` → `sh_below_image` (ค้นไฟล์ที่ `design/assets/<ref>` ระดับบน · ไฟล์ไม่มี/ว่าง = ไม่แตะฟิลด์) + `bundle-files.php` รวมไฟล์ `below_ref` + patch `image-map.json` `timeline_items.5/.7` (seed `thumbnail_refs` — `build-image-map.py` ใช้กับ `figma-file.json` ใหม่ไม่ได้ · ดู DEFERRED)

**4.5c.4 ฟิลด์ที่ใช้ (จาก `timeline_item` เดิมทั้งหมด · ไม่เพิ่มฟิลด์ใหม่) + ลิงก์ (D27)**
`post_title` → ชื่อ (ตัวแบ่งบรรทัดดิบถ้ามี) · `sh_thumbnail` (+ `sh_thumbnail_2` การ์ด 6, 9) → ทุกภาพ (บนเส้น/mini-map/ใต้เส้น) · **(D39) `sh_below_image` (image · ไม่บังคับ) = ภาพใต้เส้น — ว่าง = `sh_thumbnail` · ใช้ชิ้น 1·7·9 · §4.5c.3d** · **(D35/D36) `sh_line_art` (image · ไม่บังคับ · 1 ภาพ/ชิ้น) = "ภาพก่อน" ของภาพใต้เส้น (ไม่จำเป็นต้องเป็นลายเส้น) → ใช้เฉพาะชิ้นที่มี (2·3·4·5 — ภาพใต้เส้นขาเข้าแสดงก่อนแล้ว cross-fade เป็น `sh_thumbnail`) · ว่าง = ภาพใต้เส้นเป็นสีอย่างเดียว (ชิ้น 1, 6–15) · ⛔ ไม่ใช้ที่แถวบน/mini-map** · seed `items[].line_art_ref` · `sh_card_meta` → metadata (ตัดบรรทัดที่ขึ้นต้น `Section :`) `[P]` · `menu_order`/`sh_card_order` → ลำดับ · `sh_object` → URL `/object/<slug>/`
- **ไปหน้า detail (D27):** คลิก **ชื่อ + ภาพถ่ายใต้เส้นของชิ้นกลาง** = ลิงก์ `/object/<slug>/` (`<a>` ทั้งสอง · ไม่มีปุ่ม "Read more >>" ใน stage) · "ชิ้นกลาง" = ชิ้นที่ p ≥ 0.5 (|d| ≤ 94px) — ภาพ/ชื่อของชิ้นที่กำลังจาง (p<0.5) `pointer-events:none` กันกดพลาด
- **คลิกชิ้นที่ไม่อยู่กลาง** (ภาพบนเส้นหรือจุด) = เลื่อนมากลาง (ไม่ไปหน้า detail) · คลิกภาพบนเส้นของชิ้นกลาง = ไม่ทำอะไร (ใช้ชื่อ/ภาพใต้เส้น)
- **Reflection: แสดงเป็นจุดที่ 16 (D34 แทน D16 · ไม่มีรูป) — ฟิลด์ `sh_timeline_reflection_title`/`_body` · ดู §4.5c.7** · Back ตาม §3.3 ตำแหน่งใน 4.5c.1
**4.5c.5 ย่อ stage ให้พอดีความสูงจอ (D27 · P-222) `[P]`**
- stage ออกแบบ 1440×723 (§4.5c) ย่อ**ทั้งก้อนคงสัดส่วน**ด้วย `k = clamp( min( W/1440 , (innerHeight − headerH − 24) / 723 ), k_min, 1 )` · W = ความกว้าง viewport (≥1280) · headerH = ความสูง header จริง (Header #1 + #02 = 150 ที่ 1440; วัดจาก DOM) · 24 = margin ล่าง
- **k_min = 0.62** — ต่ำกว่านี้ภาพ/ตัวอักษรเล็กเกินอ่านได้ (metadata 14px×0.62 ≈ 8.7px) จึงหยุดย่อแล้วให้เลื่อนหน้าแนวตั้ง · **พื้นตัวอักษร:** ขนาดฟอนต์ = max(k·ค่าออกแบบ, 12px) (ชื่อ ≥14px) · ความกว้าง metadata/ชื่อคำนวณต่อจากฟอนต์จริง
- ใช้ `transform: scale(k)` (origin บนกึ่งกลาง) บนกล่อง 1440×723 + ตั้งความสูงกล่องนอก = 723·k · เมื่อ k = H-limited (< W/1440) stage แคบกว่า viewport → จัดกึ่งกลางแนวนอน · **เส้นเวลาและพื้นขาวขยายเต็มความกว้าง viewport** (เส้นยาวเกินกรอบ 31–1412 ออกไปถึงขอบ) · ชิ้นที่ล้นกรอบ stage ยังโผล่ที่ขอบ viewport (ไม่ตัดที่กรอบ stage) `[P]`
- ตัวอย่าง: จอ 1440×900 → (900−150−24)/723 = **1.0 (1.004→1)** · 1440×800 → 0.866 · 1366×768 → min(0.949, 0.82)= **0.82** · 1280×720 → min(0.889, 0.755) = **0.76** · 1280×600 → 0.589 → ใช้ **0.62** (เลื่อนหน้าได้)
- ผลต่อ responsive: ใช้เฉพาะ layout desktop (W ≥ 1280) · 768–1279 และ <768 ใช้ layout เรียงซ้อน (ข้างล่าง) ไม่ย่อตามความสูง เพราะ stage สูงกว่าอยู่แล้วโดยออกแบบ
- ค่าที่ขึ้นกับ k: ทุกขนาด/ระยะ/pitch/hit target ใน §4.5c และ §6.4c คูณ k (ยกเว้นพื้นฟอนต์ 12px และ hit target ปุ่มขั้นต่ำ 44×44 จริง)

**4.5c.6 Responsive `[P]` (Slides มีแต่ desktop — D5: tablet/mobile ต้อง responsive)**
| จอ | เปลี่ยน |
|---|---|
| ≥1280 | ตาม 4.5c.1 ที่ 1440 แล้วย่อทั้ง stage ด้วย `k` (§4.5c.5 — รวมทั้งความกว้าง W/1440 และความสูงจอ) |
| 768–1279 (แท็บเล็ต) | pitch = 0.2615·W (200–335) · **ภาพบนเส้น (#14): rest = 0.30 / active = 0.60 × ภาพใต้เส้นของชิ้นนั้น (ตามกฎ 4.5c.3 — ❌ กล่อง ≤0.18·W×140 เดิมเลิกใช้)** · metadata ย้ายจากขวาไป **ใต้ภาพ** กึ่งกลาง (กว้าง ≤ 560) · ภาพใต้เส้นกล่องสูงสุด 280×260 (→ บนเส้น rest ≤ 84×78 / active ≤ 168×156 · ช่องว่างข้างเคียงต่ำสุดที่ W=768 (pitch 200.8): 200.8 − 84 − 42 = **74.8px** ✓ ไม่ชน) · **ระยะห่างเส้น (#14 · S19 `[P]`): ขอบล่างเส้น → ขอบบน "ภาพจริง" ใต้เส้น ≥ 40px · ขอบล่างภาพ → บรรทัดแรก metadata ≥ 24px · (⇒ เส้น → ข้อความ ≥ 64px + ความสูงภาพ)** · กล่องภาพใต้เส้นสูง = **ความสูงภาพจริง (auto)** ไม่ใช่กล่องคงที่ที่ภาพลอยกลาง (กันภาพชิด/ทับข้อความ) · mini-map ยืดเต็มกว้างใต้แถบไอคอน (pitch = (W−2·24)/15) · ลูกศรเหมือนเดิม (ซ่อนสุดทาง D18) |
| <768 (มือถือ) | แถบไอคอน trail เลื่อนแนวนอนได้ (ไม่พับแถว) 1 แถว · mini-map เต็มกว้าง สูง 48 · pitch = 62vw (เห็นชิ้นกลาง + ชิ้นข้างโผล่) · **ภาพบนเส้น (#14): rest = 0.30 / active = 0.60 × ภาพใต้เส้น (ตาม 4.5c.3 · ❌ กล่อง ≤52vw×120 เดิมเลิกใช้) → rest ≤ 21vw×72 · active ≤ 42vw×144 · ข้างเคียงต่ำสุด 62vw − 21vw − 10.5vw = 30.5vw ✓** · ชื่อ 16/20 · ภาพใต้เส้นกล่อง ≤ 70vw×240 กึ่งกลางแนวนอน · **ระยะห่างเส้น (#14 · S19 `[P]`): ขอบล่างเส้น → ขอบบนภาพจริง ≥ 32px · ขอบล่างภาพ → metadata ≥ 20px (⇒ เส้น → ข้อความ ≥ 52px + ความสูงภาพ) · ความสูงกล่องภาพ = ภาพจริง (auto)** · metadata ใต้ภาพ กว้างเต็ม (padding 20) ฟอนต์ 14/20 · **ไม่มีลูกศร ‹ › บนมือถือ (F3 · D26 ครอบเฉพาะ desktop)** เลื่อนด้วยลาก/ปัด + mini-map + จุด/คีย์ · ไม่มี hover |
| ทุกขนาด | เส้นเวลา/จุด ใช้ค่า px เดิม (เส้นหนา 4, จุด 12) · ปุ่มจุด ≥44×44 · **วงกลมกลวง Reflection (#15 · §4.5c.7 ข้อ 8): desktop ⌀44·k · แท็บเล็ต ⌀40 · มือถือ ⌀32 (ขอบ 2px) · hit target ≥44×44 เสมอ** · **desktop (#14 S19): ระยะเส้น→ภาพใต้เส้นเดิม = 428.5 − 384.2 = 44.3px (×k) ≥ ขั้นต่ำแท็บเล็ต 40 ✓ ไม่เปลี่ยน · เส้น→metadata 111.8 ✓** |

**4.5c.7 Reflection stop (D34 · แทน D16 · Figma `3101:609` Section 51 · lastModified 2026-11-11T03:55:12Z · ไม่ดึง API ใหม่ — ใช้ `design/figma-3101-609.json`)**
**ที่มาใน Figma — 3 node ชื่อ "Reflection" แต่แสดงจริงแค่ 1:** `3103:1377` (title) + `3103:1378` (body) อยู่ใน `Frame 114490` `3103:1359` = **ใบที่ 16 (visible) ท้ายแถว `Frame 114464` `3101:820`** ต่อจากใบที่ 15 (Bas-relief `Frame 114489` `3103:1340`) — `3101:820` มี children 17 = 16 visible + 1 hidden · ⚠ `3101:735` (`Zone 4 › Desktop - zone 6`) และ `3101:924` (Section 51) **node ตัวเองเป็น visible แต่แม่ `Frame 114446` (`3101:726` / `3101:915`) เป็น `visible:false`** = เลย์เอาต์เก่า 2 คอลัมน์ (ภาพ 6 ใบ `Group 32` + ข้อความ 36/45 + 20/24) ที่ไม่แสดง → **ไม่ใช้** (D34 "ไม่มีรูป") · ข้อความในสองตัวนั้น = ข้อความเดิม (2229 ตัวอักษร = seed เก่า) · **ข้อความที่แสดงจริงคือ `3103:1378` (2036 ตัวอักษร · เปลี่ยนแล้ว — ดูข้อ 7)** · ในใบที่ 16 มีสี่เหลี่ยมภาพ `3103:1360` 500×281 (imageRef `758e3fe7…` FIT = placeholder ตัวเดียวกับทุกใบ `กระดูกช้าง 1`) → **ไม่ใช้** (D34: ไม่มีรูป) · ใบนี้เลื่อนขวา +50 (`paddingLeft 50`) ต่างจากใบอื่น
| # | หัวข้อ | ค่า | ที่มา |
|---|---|---|---|
| 1 | **ตำแหน่งบนเส้น** | **จุดที่ 16 · pitch เดียวกัน 376.6** · จุดอยู่ x = 720 + (16 − c)·376.6 (c=15 → **1096.6**; c=16 → 720) · **(#15) มี "วงกลมกลวงใหญ่" กึ่งกลางเส้น (แทนจุด ● ⌀12 — ข้อ 8)** (เลื่อนไปกับแถว · นอก 31–1412 ไม่วาด) · เส้นเวลายังยืนคงที่ 31→1412 (ไม่หยุดที่จุด 16) · ช่วง c ∈ **[1, 16]** · คลิกจุด/โฟกัสได้เหมือนจุดอื่น (≥44×44) · Figma ไม่วาดจุด/เส้น (บอร์ดเป็นแถบการ์ด ความกว้างไม่เท่ากัน 667–1245) → ใช้ pitch ของ Slides ตาม D34 "จุดที่ 16" | pitch `[S]` ตาม 4.5c.2 · จุด 16 `[P]` (D34) |
| 2a | **แถวบน (เหนือเส้น) ตอน active** | **ไม่มีภาพ · ไม่มีชื่อเหนือเส้น · เหลือวงกลมกลวง (#15) อย่างเดียว** (Figma ไม่มีองค์ประกอบเหนือเส้นของใบที่ 16 · ชื่อ "Reflection" อยู่เฉพาะกล่องล่าง ข้อ 2b — ไม่ซ้ำสองที่) · aria-label ของจุด = ค่าฟิลด์ `sh_timeline_reflection_title` | `[N]` (ไม่มี node) · `[P]` ไม่มีชื่อเหนือเส้น → P-229 (1) |
| 2b | **แถวล่าง ตอน active — กล่อง** | **x = 560 → 1340 (กว้าง 780) · ชิดซ้ายที่ขอบซ้ายกล่องภาพใต้เส้น (560)** · top ของ **title = y_rel 428.5** (= top กล่องภาพใต้เส้น) · พื้นที่ภาพ/metadata ปกติ (กล่อง 560–880 + 907–1349) ว่างทั้งหมด ไม่มีภาพ · กว้าง 780 `[N]` `3103:1376` (`layoutSizingHorizontal: FILL` ใน `Frame 114490` กว้าง 830 padding-left 50) · ขอบขวา 1340 ≤ ขอบขวา metadata 1349 · ใน Figma ข้อความชิดซ้ายเดียวกับสี่เหลี่ยมภาพ (x 12211) → ตีความเป็นซ้าย = ซ้ายกล่องภาพ | กว้าง/พิกัดภายใน `[N]` · x=560,y=428.5 `[P]` → P-229 (2) |
| 2c | **title "Reflection"** (TEXT `3103:1377`) | Komet-Heavy (`Komet-Heavy`) **800** · **20px / line-height 25** (`INTRINSIC_%` 100%) · letterSpacing 0 · ชิดซ้าย · สี **`#212529`** (fill style `1:334`) · กว้าง 780 สูง 25 (hug) | `[N]` |
| 2d | **body** (TEXT `3103:1378`) | Komet **500** (Medium) · **14px / line-height 17.5** (`INTRINSIC_%`) · letterSpacing 0 · ชิดซ้าย · สี **`#212529`** · **กว้าง 780** · **สูง 396** (ที่ 780 กว้าง · 2036 ตัวอักษร) · **top = title top + 25 + 15 = y_rel 468.5** (gap `itemSpacing` 15 ใน `Frame 12683` `3103:1376` `[N]`) · **ระยะย่อหน้า:** ใน node ไม่มี `paragraphSpacing` (ไม่ตั้ง) — ย่อหน้าคั่นด้วย**บรรทัดว่าง 1 บรรทัด (= 17.5)** ในตัวข้อความเอง · `\n` เดี่ยว = ขึ้นบรรทัดใหม่ **ไม่เว้น** (ย่อหน้า 2 มี 2 ท่อน ท่อนหลังขึ้นต้น "As essential ‘ecosystem engineers’…" — ตรงภาพเจ้าของ) · **ไม่มี "Read more >>"** (`3103:1379` `visible:false`) · **ไม่มีเลขรายการ/ตัวหนา/ลิงก์ในเนื้อ** (`characterStyleOverrides: []`) | `[N]` · ระยะย่อหน้า = นับจากตัวอักษร `[N]` |
| 2e | **ความสูงข้อความยาว → stage สูงขึ้น** (⚠ §4.5c.5 ฉบับปัจจุบัน**ไม่มี**กฎ "metadata ยาว" ตามที่ brief อ้าง → กำหนดที่นี่ `[P]`) | ปกติ stage = 723 (§4.5c.5 · k คำนวณจาก 723 เท่าเดิม) · ที่ Reflection: **H_ref = max(723, 468.5 + h_body + 24)** (h_body วัดจาก DOM · Figma 396 → ล่างสุดข้อความ 864.5 → **H_ref ≈ 889**) · **ความสูงกล่อง stage ภายนอก = 723 + (H_ref − 723)·p₁₆** (p₁₆ ตาม §6.4c · ค่าไล่ตามการเลื่อน ไม่กระโดด) · เส้น/ไอคอน/mini-map/Back ไม่ขยับ (Back คงที่ x160 y_rel 649 — ไม่ชนกล่องข้อความที่ x≥560) · ที่จอ 1440×900 เนื้อล่างสุดต่ำกว่าจอ ≈ 140 → **เลื่อนหน้าแนวตั้งเพื่ออ่านต่อ** (ยอมรับ · ข้อความ 2000 ตัวอักษรไม่ย่อ: พื้นฟอนต์ 12px ยังบังคับ → ถ้า k·14 < 12 ใช้ 12) · ตัวเลือกแทน (ย่อฟอนต์/กล่องเลื่อนภายใน) ไม่เลือก — กล่องเลื่อนซ้อนในหน้าเลื่อนใช้ยากบนมือถือ | `[P]` |
| 3 | **ตอนไม่ active (ชิ้น 15 อยู่กลาง · c=15)** | ด้านขวาของชิ้น 15: **วงกลมกลวง ⌀44 (#15) ที่ x=1096.6 บนเส้น + ไม่มีอะไรอื่น** (ไม่มีภาพ/ลายเส้น/ชื่อ/เนื้อหา · α กล่องล่าง = 0) · เส้นยาวต่อไปถึง 1412 · ผู้ใช้เห็นว่ามีจุดต่อ + ลูกศร › (ยังแสดงที่ชิ้น 15 ตาม D34/D18) | `[P]` → P-229 (1) (Figma ไม่มี rest ของใบที่ 16) |
| 4 | **mini-map** | **ไม่มีช่องที่ 16** (ไม่มีรูปให้ย่อ · และช่องที่ 16 อยู่ x 1408–1456 เกินขอบ 1440 / กรอบ mini-map 639–1413) · mini-map 15 ช่องคงเดิมทุกค่า · **กรอบไฮไลต์ `#EDEBDA` 195 ไล่ต่อเนื่อง c ∈ [1,16]**: ศูนย์กลาง x = 658 + 51.6·(c−1) → c=16 ศูนย์กลาง **1432** (กรอบ 1334.5 → 1529.5 ถูกตัดที่ขอบ viewport 1440 · ใช้กฎ "ไม่ clamp" เดิมของ 4.5c.1) = ครอบช่อง 12–15 + พ้นช่องสุดท้าย → บอกว่า "เลยชิ้น 15 ไปแล้ว" · คลิก mini-map ไปชิ้น 1–15 ตามเดิม (ไปจุด 16 ด้วย ›/จุด/End) | `[P]` → P-229 (3) |
| 5 | **‹ › (D18)** | **› ซ่อนที่ c ≥ 16** (Reflection) แทน c ≥ 15 · ‹ ซ่อนที่ c ≤ 1 เท่าเดิม · `visibility:hidden` (ไม่โฟกัส/ไม่รับคลิก — ⛔ ไม่ใช่ opacity) · **ชิ้น 15 แสดง ›** (กดไป Reflection) · `End` → c=16 · `Home` → c=1 · `→` ที่ c=15 ไป Reflection | D18 · D34 |
| 6 | **α / settle** | ใช้สูตรเดียวกับ metadata (§6.4c): **p₁₆ = clamp(1 − \|d₁₆\|/188, 0, 1)** · title + body α ระหว่างเลื่อน = **0.47·p₁₆** → **settle 400ms ease-out → 1.0** เมื่อไม่มีอินพุต 150ms และ \|d\| < 8px · ทั้ง title กับ body ใช้ α เดียวกัน (ก้อนเดียว) · **วงกลมกลวงไม่จาง** (ทึบเสมอ · #15) · ไม่มีเลเยอร์ภาพ/ลายเส้น/scale (ไม่มีรูป) · reduced-motion: p=1 ทันที ไม่ settle | `[P]` (ลอก metadata `[S]`) |
| 7 | **ข้อความใน Figma เทียบ seed** | ⚠ **ไม่ตรง:** seed เดิม `reflection.body` (node `1232:1817`) = ข้อความ **เก่า 4 ย่อหน้า 2229 ตัวอักษร** ("…enduring presence across Southeast Asia. Archaeological and art-historical evidence…") = ตรง `3101:736`/`3101:925` ที่**ซ่อน** · ข้อความที่ **แสดงจริง** `3103:1378` = **ใหม่ 3 ย่อหน้า 2036 ตัวอักษร** ("…enduring **cultural** presence across **South-East** Asia. Elephants have been part of human life…" · เปลี่ยนคำ/ลำดับ · ใช้ ‘ ’ โค้งเฉพาะ "‘ecosystem engineers’" · ท้ายสุดเพิ่มประโยค "…and that safeguarding cultural heritage often also requires protecting natural heritage.") · **ภาพเจ้าของ `owner-reflection-last.png` = ข้อความใหม่** (หัวข้อ + 3 ย่อหน้า · ย่อหน้า 2 ท่อนหลังขึ้นบรรทัดไม่เว้น) · → **แก้ seed แล้ว** (`content/seed/timeline-elephant.json` `reflection.body` = `3103:1378` ตรงทุกตัวอักษร · node → `3103:1377`/`3103:1378`/`3103:1359`) · title "Reflection" ตรง | `[N]` |
| 8 | **(#15 `[S]` · ลูกค้า S21 `design/timeline-ref/comment-dev/slide-21.jpg` "เพิ่ม Reflection ตอนจบ") วงกลมกลวงใหญ่ แทนจุด ● ⌀12** | **วงกลมกลวง: ⌀นอก 44 (box-sizing border-box) · ขอบ 2px `#000000` · พื้นในขาว `#FFFFFF` ทึบ (ปิดเส้นเวลาด้านหลัง — ในภาพเส้นขาดที่ตัววงกลม) · ศูนย์กลางบนเส้นกึ่งกลาง y_rel 382.2 และ x = 720 + (16 − c)·376.6** · z อยู่เหนือเส้น เท่าจุด ● (ล่างชื่อ/ภาพ) · ไม่ scale/ไม่จางตาม p · focus ring ตามปกติ · hit target 44×44 (= ตัววงกลม) · aria-label = `sh_timeline_reflection_title` · วัด slide-21 (ภาพกว้าง 1507 · ชั้น 1385px ของสไลด์): วงกลม **ศูนย์กลาง (898, 354) ⌀นอก ≈ 27px · ขอบ ≈ 1.5px** · จุด ● ในภาพเดียวกัน **≈ 7.5px** · เส้น **≈ 2.5px** · สเกลภาพเทียบ spec: เส้น 2.5px ↔ 4px (×1.6) · จุด 7.5px ↔ 12px (×1.6) → วงกลม 27 × 1.6 = 43.2 → **⌀44** · ขอบ 1.5 × 1.6 = 2.4 → **2px** | `[S]` วัดจากภาพ · ปัด 44 `[P]` |
**ฟิลด์ที่หน้าใช้ (Reflection):**
| ฟิลด์ | ชนิด | ที่ | node | ตัวอย่าง | seed key |
|---|---|---|---|---|---|
| `sh_timeline_reflection_title` | text | หน้า timeline (ไม่ใช่ `timeline_item`) | `3103:1377` | "Reflection" | `reflection.title` |
| `sh_timeline_reflection_body` | textarea | หน้า timeline | `3103:1378` | "The objects in this trail reveal the elephant's enduring cultural presence across South-East Asia. …" | `reflection.body` (`\n\n` = ย่อหน้า · `\n` เดี่ยว = ขึ้นบรรทัด) |
- **ไม่เพิ่มฟิลด์ใหม่** (CONTENT-MODEL มีทั้งสองแล้ว — แถว 168–169 · ⛔ ผมไม่แก้ · **ขอ core แก้ node อ้างอิงจาก `1232:1816`/`1232:1817` เป็น `3103:1377`/`3103:1378`** และตัวอย่างข้อความตามข้อ 7)
- **เรนเดอร์ body:** บรรทัดว่าง = ย่อหน้าใหม่ (`<p>` margin-bottom 17.5·k) · `\n` เดี่ยว = `<br>` (ไม่มี margin) — เช่น `wpautop` · escape ด้วย `wp_kses_post` · ⛔ ไม่ใช้ `white-space: pre-wrap` กับ `\n\n` (จะได้ช่องว่างสองเท่า)
- **ฟิลด์ว่าง `[P]`:** `body` ว่าง → **ไม่สร้างจุดที่ 16** (ช่วง c = [1,15] · › ซ่อนที่ 15 ตาม D18 เดิม) · `title` ว่าง → ใช้ "Reflection" (labels)
- **ไม่เป็นลิงก์** (ไม่มี `/object/<slug>/` · ไม่มี Read more · title/body = ข้อความล้วน) · ชิ้นกลาง "p≥0.5" ใช้กับ a11y live region อ่าน title ตอน settle · a11y: จุด 16 = `<button aria-label="Reflection">` ≥44×44 · กล่องข้อความ `<section aria-label="Reflection">` อยู่ในลำดับอ่านต่อจากชิ้น 15
- **Responsive `[P]`:** 768–1279 → กล่องข้อความใต้เส้น **กึ่งกลาง** กว้าง min(780, W − 48) · top = ตำแหน่งเดียวกับ "ภาพใต้เส้น" ของแท็บเล็ต (ว่าง · ไม่มีภาพ) · title 20/25 · body 14/17.5 · <768 → ใต้แถบจุด กว้างเต็ม padding 20 · title **18/22** · body **14/20** (เท่า metadata มือถือ) · ไม่มีลูกศร · ไม่ย่อด้วย k (เลย์เอาต์เรียงซ้อน)

### 4.6 รายการ trail 17 + "Etc."
ดู `content/seed/trails.json` — rooster · turtle · deer · dragon · fish · hornbill · horse · naga (=Serpent ใน 2.1) · water-bird · makara · lion · seashell · buffalo · phoenix · bird · garuda · elephant (17) + `etc` (4 วัตถุที่ยังไม่จัด trail) · จำนวนวัตถุต่อ trail (นับ record ใน objects.json): elephant 16 (15 final + Gringsing) · seashell 14 · naga 13 · rooster 10 · water-bird 10 · dragon 9 · phoenix 8 · fish 8 · bird 8 · hornbill 7 · horse 7 · lion 6 · turtle 5 · garuda 5 · buffalo 5 · deer 3 · makara 5 (3 + 2 ใหม่ 2026-11-17 · §8.6) · etc 4

### 4.7 แผนที่ + popup (Section 11 / Frame 12718 `670:1596`)
แผนที่ `1120×620` พื้น `#F2F2F2` + ภาพ (IMG `cd63a05d`) · ปุ่ม ‹ › 60×80 กลางแนวตั้ง · เลข 1–4 มุมขวาบน `[932,22 166×34]` · หมุด 34×34 (instance `Frame 12681`, ON_PRESS เปลี่ยนสถานะ, ON_CLICK เปิด overlay popup) · popup `336×h` พื้น `#D65F47` r4 padding 12 gap 10 · ปุ่มปิด `basil:cross-solid` 24×24 (`close-cross.svg` สี `#000000`) ที่มุมขวาบน (404,66 เทียบ section) · ชื่อ Barlow SemiBold 16/19.2 · เนื้อ Barlow Regular 14/16.8 (กว้างข้อความ 312) · popup 4 ใบสูง 369/284/464/352 ตามเนื้อ

### 4.8 Detail วัตถุ (หน้า "Desktop - NNNN") — โครง + ตาราง geometry รายเฟรม (1440 กว้าง · 15 frame Final = ที่ต้องตรง 100%)
> ⚠ **ตาราง geometry ด้านล่างเป็นค่าก่อน Figma 2026-11-11 (ประวัติ)** — ค่าปัจจุบัน 15 frame = §4.8.3 + seed `figma[].style` · ข้อเท็จจริงเรื่อง `Frame 12682`/`Rectangle 1` ซ่อน และโครงทั่วไปยังใช้ได้
แถวบนสุดเหมือนทุกหน้า: Header #1 `[0,0 1440×86]`, Header #02 `[0,86 1440×64]` (§3) · ใต้ header = **Hero** (เฟรมภาพ/GIF) แล้ว **แผงเนื้อหา** (พื้นสีต่อวัตถุ) แล้วปุ่ม Back
**ข้อเท็จจริงจากไฟล์ที่ต้องรู้ก่อนอ่านตาราง**
- `Frame 12682 [0,90 1440×840]` + `Rectangle 1` (ภาพพื้น IMG `0652a86e`) มีใน 125 frame แต่ **`visible:false` ทั้งหมด → ไม่แสดง ไม่ต้องสร้าง**
- ค่า "มาตรฐาน": hero `[0,150 1440×810]` (ภาพนิ่งซ้อนกัน 3–24 เฟรม = GIF; เฟรมแรก opacity 0; หรือภาพ `ezgif…` ชิ้นเดียว) · แผง `[0,960 1440×H]` · padding ซ้าย/ขวา **160** บน **60** ล่าง **60 หรือ 80** · gap คอลัมน์ **22** (คอลัมน์ข้อความ gap 22/25) · **7 ใน 15 frame Final เป็นแบบมาตรฐาน** (1504, 1505, 1506, 1507, 1508, 1509, 1511) · **8 frame ต่าง**: 1468 และ 1476 (panel y=1000) · 1483 (y=985) · 1493 (y=990, hero 1440×840, pad ล่าง 100) · 1503 (y=1011, hero `[160,150 1120×831]`) · 1484 (y=1050, hero `[60,150 1320×870]`) · 1510 (y=892, hero `[60,150 1320×742]`) · 1512 (y=880, hero `[160,150 1120×730]`, แผงไม่มี fill)
- คอลัมน์ซ้าย (ข้อความ) `[160,panelY+60 739×h]` (หรือ `1120×h` เมื่อไม่มีรูปข้าง) · คอลัมน์ขวา (รูป) `[921,panelY+60 359–360×h]` · ลำดับในคอลัมน์ซ้าย: ชื่อ 36/45 → (metadata block 24/30 หรือ `Collection:` 20/25) → caption 18/24
- ปุ่ม Back `[160,y 115×50]` ต่อท้ายคอลัมน์ข้อความ (ค่า y ต่อ frame อยู่ในตาราง/ใน seed `figma[].style.back_button`)
- **ค่า geometry ต่างกันต่อวัตถุ** → เก็บเป็นข้อมูลต่อวัตถุใน seed: `objects[].figma[].style` = `{panel_y, panel_h, panel_padding_top_bottom, panel_gap, panel_children[] (node+bbox), hero[] (node+bbox), back_button, frame_size}` (ดู §5.1 ฟิลด์ `layout`)

**ตาราง geometry — 15 frame Final (หน้า Final ตรงตามลำดับ trail ช้าง; bbox = `[x,y w×h]` เทียบ frame 1440)**
| # | frame | วัตถุ | hero (node · ชื่อ · bbox · จำนวนภาพ) | แผงเนื้อหา (node · y · h · fill · padding) | คอลัมน์ในแผง | Back |
|---|---|---|---|---|---|---|
| 1 | `1041:1290` Desktop - 1510 1440×2283 | Elephant Leg Bone | `1041:1293` Frame Gif [60,150 1320×742] (6 ภาพ) | `1041:1366` y=892 h=1391 · #B3B3B3 · pad บน/ล่าง 60/60 (ซ้าย/ขวา 160, gap 22) | Frame 12683 [160,952 739×1180]; Frame 114479 [921,952 360×689] | [160,2169 115×50] |
| 2 | `670:2802` Desktop - 1476 1440×2005 | Singhasari Ganesha | `1057:968` Frame GIF [0,160 1440×810] (10 ภาพ) | `670:2810` y=1000 h=1003 · #B2BBAA · pad บน/ล่าง 60/80 (ซ้าย/ขวา 160, gap 22) | Singhasari Ganesha [160,1060 1120×45]; Section : Cosm… [160,1127 1120×240]; Caption: Carve… [160,1389 1120×456] | [160,1890 115×50] |
| 3 | `670:2786` Desktop - 1468 1440×2005 | Ganesha | `1051:8181` Frame GIF [0,150 1440×810] (10 ภาพ) | `670:2796` y=1000 h=1005 · #B2BBAA · pad บน/ล่าง 60/80 (ซ้าย/ขวา 160, gap 22) | Ganesha [160,1060 1120×45]; Section : Cosm… [160,1127 1120×240]; Caption: Carve… [160,1389 1120×456] | [160,1887 115×50] |
| 4 | `956:975` Desktop - 1503 1440×2094 | Elephant Molar Coconut Grater | `958:1226` Rectangle 26 [160,150 1120×831] (1 ภาพ) | `956:976` y=1011 h=1083 · #B2BBAA · pad บน/ล่าง 60/60 (ซ้าย/ขวา 160, gap 22) | Frame 12683 [160,1071 1120×881] | [160,1996 115×50] |
| 5 | `964:1522` Desktop - 1505 1440×2674 | Embossed Brass Basin | `964:1525` Frame GIF [0,150 1440×810] (15 ภาพ) | `964:1540` y=960 h=1714 · #DEDCC3 · pad บน/ล่าง 60/80 (ซ้าย/ขวา 160, gap 22) | Frame 12747 [160,1020 549×1483]; Frame 114475 [731,1020 549×840] | [160,2528 115×50] |
| 6 | `1006:922` Desktop - 1507 1440×2239 | Ivory Fire Piston | `1006:923` Frame GIF [0,150 1440×810] (15 ภาพ) | `1006:1185` y=960 h=1279 · #DEDCC3 · pad บน/ล่าง 60/80 (ซ้าย/ขวา 160, gap 22) | Frame 12747 [160,1020 739×1074]; Frame 114475 [921,1020 359×334] | [160,2138 115×50] |
| 7 | `670:2872` Desktop - 1493 1440×1738 | Elephant and Mahout Kendi | `670:2884` Frame 12779 [0,150 1440×840] (7 ภาพ) | `670:2873` y=990 h=748 · #AACFD8 · pad บน/ล่าง 60/100 (ซ้าย/ขวา 160, gap 22) | Frame 12683 [160,1050 549×588]; Frame 12684 [731,1050 549×588] | [160,1667 115×50] |
| 8 | `3004:804` Desktop - 1512 1440×2069 | Patola cloth | `3004:815` Frame 12706 [160,150 1120×730] (5 ภาพ) | `3004:805` y=880 h=1189 · ไม่มี fill (พื้นขาว) · pad บน/ล่าง 60/60 (ซ้าย/ขวา 160, gap 22) | Frame Text [160,940 549×989]; Frame Picture [731,940 549×588] | [160,1967 115×50] |
| 9 | `961:1024` Desktop - 1504 1440×2160 | Elephant-Shaped Relic Containers | `961:1027` Frame GIF [0,150 1440×810] (14 ภาพ) | `961:1217` y=960 h=1200 · #B2BBAA · pad บน/ล่าง 60/80 (ซ้าย/ขวา 160, gap 22) | Frame 12747 [160,1020 549×1011]; Frame 12742 [731,1020 549×302.2] | [160,2071 115×50] |
| 10 | `964:2090` Desktop - 1506 1440×1943 | Pod Duang | `964:2093` Frame GIF [0,150 1440×810] (15 ภาพ) | `964:2109` y=960 h=983 · #363638 · pad บน/ล่าง 60/80 (ซ้าย/ขวา 160, gap 22) | Frame 12747 [160,1020 739×759]; Frame 114475 [921,1020 359×445] | [160,1828 115×50] |
| 11 | `1042:1719` Desktop - 1511 1440×2470 | Opium Pipe | `1042:1720` Frame GIF [0,150 1440×810] (6 ภาพ) | `1042:1931` y=960 h=1510 · #EEDFB4 · pad บน/ล่าง 60/80 (ซ้าย/ขวา 160, gap 22) | Frame 12747 [160,1020 739×1338]; Frame 114479 [921,1020 360×689] | [160,2400 115×50] |
| 12 | `670:2828` Desktop - 1483 1440×1745 | Tin Ingot | `670:2831` Frame Gif [0,150 1440×810] (6 ภาพ) | `670:2839` y=985 h=760 · #B3B3B3 · pad บน/ล่าง 60/60 (ซ้าย/ขวา 160, gap 22) | Frame 12683 [160,1045 684×535]; Frame 114480 [866,1045 414×382.7] | [160,1631 115×50] |
| 13 | `1041:866` Desktop - 1509 1440×2236 | Wooden Cold-Press Cake Molds | `1041:867` Frame GIF [0,150 1440×810] (12 ภาพ) | `1041:883` y=960 h=1276 · #EEDFB4 · pad บน/ล่าง 60/80 (ซ้าย/ขวา 160, gap 22) | Frame 12747 [160,1020 739×1119]; Frame 114479 [921,1020 360×689] | [160,2166 115×50] |
| 14 | `1006:561` Desktop - 1508 1440×2027 | Siwaih | `1006:562` Frame GIF [0,150 1440×810] (15 ภาพ) | `1006:824` y=960 h=1067 · #F7EFDC · pad บน/ล่าง 60/80 (ซ้าย/ขวา 160, gap 22) | Frame 12747 [160,1020 739×858]; Frame 114479 [921,1020 360×589] | [160,1918 115×50] |
| 15 | `670:2847` Desktop - 1484 1440×1915 | Bas-relief / Procession | `670:2861` Frame 12756 [60,150 1320×870] (8 ภาพ) | `670:2848` y=1050 h=865 · #B2BBAA · pad บน/ล่าง 60/60 (ซ้าย/ขวา 160, gap 22) | Frame 12683 [160,1110 739×685]; Frame 12771 [921,1110 359×722] | [160,1830 115×50] |

ข้อสังเกตจากตาราง: hero มี 5 รูปแบบ — (1) GIF เต็มกว้าง `1440×810` @y150 (10 frame; 1476 อยู่ที่ y=160) · (2) `1440×840` @y150 (1493, panel y990, pad ล่าง 100) · (3) กรอบกลาง `1320×870`/`1320×742` @x60 (1484 แกลเลอรี Bas-relief, 1510 `Frame Gif`) · (4) ภาพเดียว `1120×831` @x160 (1503 `Rectangle 26`) · (5) `1120×730` @x160 (1512 Patola `Frame 12706`) · panel y ตั้งแต่ 880 ถึง 1050 ตาม hero · 1512 ไม่มี fill แผง · 1493 แผง `#AACFD8` ฯลฯ ตามสีใน §2.1

### 4.8.1 คีย์ภายในแผง detail — เติมโดย `design-detail-style` (P-161 · P-162) · เฉพาะ 15 frame Final trail ช้าง
> ⚠ นิยามคีย์ใช้ได้ต่อ (เพิ่มคีย์ใหม่ใน §4.8.3) · **ตัวอย่าง/ตาราง "ตารางต่อ frame" ด้านล่างเป็นค่าเก่า — ใช้ §4.8.3 + seed** · label มาตรฐานตอนนี้ = "Place of production / Findspot:" · "Local name:" · "Other information / Object biography:" · ไม่มี `Section :` ใน frame ใหม่
สร้างด้วย `design/detail-style.py` (รันซ้ำได้ผลเดิม: `/usr/bin/python3 design/detail-style.py` — ต้องใช้ Python ของ Xcode ที่มี Pillow) จาก `design/figma-file.json` + `design/assets` เท่านั้น · ทุกค่าเก็บ node-id ของที่มาไว้ข้างค่า · พิกัด `bbox`/`[x,y,w,h]` = เทียบ frame 1440 (px) · ใส่ใน `objects[].figma[].style` (importer ส่งทั้งก้อนเข้า `sh_layout`) · **ไม่มี**ในเฟรม Page 2.1
| คีย์ใน `style` | ชนิด | ความหมาย | ตัวอย่าง (Elephant Leg Bone 1510) |
|---|---|---|---|
| `text_col` | object | คอลัมน์ข้อความ: `node` `name` `bbox` `layout` `gap` (ระหว่าง block) `padding_top` (title เลื่อนลงเท่านี้ในคอลัมน์ — 1510·1503·1483·1484 = 30) `content_top` (y ของ block แรก) `bottom` · `direct_in_panel:true` = ข้อความเป็นลูกตรงของแผง (1468·1476: gap = gap ของแผง 22, `node`=แผง) | `{node:"1041:1367", bbox:[160,952,739,1180], gap:25, padding_top:30, content_top:982}` |
| `text_blocks[]` | array | block ที่แสดงจริง **ตามลำดับบนลงล่าง** (เฉพาะ node `visible`): `order` · `role` (`title` `meta` `collection` `caption` `plain_meta`=ข้อความไม่มีป้าย `lorem_placeholder`) · `node` · `bbox` · `y_in_col` (y − `content_top`) · `gap_before` · `font{family,weight,size,line_height,letter_spacing,align}` · `color` (hex) · `auto_resize` · `runs[]` (ช่วงตัวอักษร: `start` `length` `weight` `color` [`size`] + `text` ถ้า ≤120 ตัว ไม่งั้น `text_head`) | `{role:"collection", node:"1041:1391", bbox:[160,1097,739,26], font:{weight:600,size:24,line_height:26}, color:"#F8F9FA", gap_before:25, runs:[{weight:700,text:"Collection:"},{weight:600,text:" "},{weight:500,text:"National Museum Bangkok"}]}` |
| `text_blocks[].rows[]` | array (role `meta`/`collection`) | แถว "ป้าย: ค่า": `label` (ข้อความป้ายตามที่ Figma แสดง) · `field` (ฟิลด์ seed ที่ตรง) · `value` · `label_weight` · `value_weight` · `label_color` · `value_color` — ป้าย = 700 · ค่า = 500 ทุก frame | `{label:"Collection:", field:"collection", label_weight:700, value_weight:500}` |
| `text_blocks[].lines[]` | array (เฉพาะ block ที่มี list) | บรรทัดของ block: `text` · `list` (`UNORDERED`/`NONE`) · `indent` — **1493 Kendi**: `670:2876` และบรรทัดแรกของ `670:2877` มีจุด bullet, บรรทัดที่สอง "Brunei Darussalam Maritime Museum" ไม่มี bullet · ไม่มีป้ายใดๆ | `[{text:"15th–early 16th century CE", list:"UNORDERED", indent:1},{text:"Brunei Darussalam Maritime Museum", list:"NONE"}]` · **P-163:** block role `plain_meta` เก็บข้อความเต็มเสมอ — `runs[].text` = `characters` ของ node ครบทุกตัว (ไม่มี `text_head`; role อื่นที่ run ยาว >120 ตัวยังเป็น `text_head`) และมี `lines[]` เมื่อมีการขึ้นบรรทัด (`list:"NONE"` `indent:0` ถ้าไม่ใช่ list) — **1484 Bas-relief** `670:2851` = 3 บรรทัด (`…Buddha.` / `Location: …` / `Source: …`; ปลายบรรทัดมีช่องว่าง 1 ตัวตามต้นฉบับ Figma) |
| `meta_labels[]` | array string | **ป้ายที่ frame แสดงจริง** ตามลำดับ (รวมทุก block `meta`/`collection`) — วัตถุที่ frame แสดงแค่ Collection = `["Collection:"]` (ไม่ต้องแสดง period/place… ที่ seed เติมจากการ์ด timeline) · ป้าย bio: **1468 = "Specimen/Object biography:"** · 1476·1503·1504 = "Other Information / Object Biography:" · 1503 ไม่มี Section · 1493 ไม่มีป้าย = `[]` | `["Collection:"]` |
| `caption` | object | `node` · `label` (`"Caption:"` หรือ `null` = frame ไม่มีป้าย: **1493 · 1512 · 1483**) · `label_weight` `label_color` · `label_breaks` (จำนวน `\n` ระหว่างป้ายกับย่อหน้าแรก: **2 = มีบรรทัดว่าง** (1510·1468·1476·1511·1507·1509·1508) · **1 = ไม่มี** (1503·1505·1504·1506)) · `body_weight` (400 ทุก frame — 1511 base node=700 แต่ run เนื้อหา=400) `body_color` · `body_break_runs` (นับรอยแบ่งย่อหน้าตามจำนวน `\n`: `{"2":4}` = 4 ช่องแบบเว้นบรรทัด · `{"1":3}` = ขึ้นบรรทัดใหม่ธรรมดาไม่เว้น (1493·1504·1483)) · `paragraph_indent_nbsp[]` (เฉพาะ 1493: ย่อหน้า 2·3 เริ่มด้วย NBSP 15 ตัว = ย่อหน้าเยื้อง) | `{label:"Caption:", label_weight:700, label_breaks:2, body_weight:400, body_color:"#343A40", body_break_runs:{"2":4}}` |
| `image_col` | object | คอลัมน์รูปในแผง: `node` `nodes[]` `bbox` `layout` `gap` `padding_top` (รูปเลื่อนลง: 1510·1511·1509·1483 = 100 · 1505·1506·1507 = 80) | `{node:"1041:1451", bbox:[921,952,360,689], gap:10, padding_top:100}` |
| `images[]` | array | **รูปที่แสดงจริงในแผง ตามลำดับ (ตัวหลังอยู่บน)** เฉพาะ node visible: `order` `node` `name` `bbox` · `radius` (= **มุมที่เรนเดอร์จริง** = 0 ทุกรูป: frame ครอบของ 1493·1512 มี `cornerRadius` 22/10 แต่ไม่มี fill และ `clipsContent=false` จึงไม่ตัดรูปลูก — PNG 1512 `3004:814` มุมเหลี่ยม วัดด้วย corner.py: มุมเหลี่ยม diff 1.3–3.7 · มน r10 diff 7.0–31.7 · ค่าของ frame เก็บไว้อ้างอิงที่ `container.radius`) · `container{node,bbox,clips_content,radius}` (radius ของ frame ครอบ ไม่ใช่ที่เรนเดอร์) · `shadow[]` (drop shadow ของ frame ครอบ: y1 blur4 `#0C0C0D` 5%+10%) · `scale_mode` · `image_transform` (2×3 เดิมจาก Figma) · `paint_rotation` · `source_image` (imageRef) `source_file` · **`crop_ref`** = ไฟล์ PNG ที่ครอป/หมุน/ย้ายตาม imageTransform (หรือ FILL=cover) **ลงขนาดกรอบ `bbox` แล้ว** ที่ `design/assets/<ref>.crop-<node โดย ":"→"-">.png` · `crop_size` (px จริง = bbox × `crop_scale` 1–2) · พื้นที่นอกภาพเดิม = โปร่งใส (เช่น 1493 cutout) · **ใช้ `crop_ref` วางที่ `bbox` (fill)** ไม่ต้องครอป/หมุนซ้ำ | `{order:1, node:"1041:1452", bbox:[921,1052,360,589], radius:0, paint_rotation:450, crop_ref:"4953122e…crop-1041-1452.png", crop_size:[720,1178]}` |
| `hero_bg` | object (เฉพาะ **1493**) | `Rectangle 1` `670:2891` [0,200 1440×790] ใน hero `Frame 12779` — **`visible:true` ใน 1493 เท่านั้น** (อีก 125 frame ซ่อน): เป็น**ภาพนิ่งเต็ม hero** (พื้นขาว + น้ำฟ้า + Kendi) เป็นลูกสุดท้าย (z 7/7) เหนือเฟรมแอนิเมชัน 01–06 · เรนเดอร์ใน PNG ตรงกับไฟล์นี้ (diff เฉลี่ย 0.38/255) · `crop_ref` = ไฟล์ครอป 1440×790 · ดู P-216 | `{node:"670:2891", bbox:[0,200,1440,790], z_in_parent:7, z_of:7, crop_ref:"0652a86e…crop-670-2891.png"}` |

**แบบแผน `*_ref`:** คีย์ใดใน `style` (ซ้อนลึกก็ได้) ที่ลงท้าย `_ref` = ชื่อไฟล์ใน `design/assets` (ปัจจุบัน `images[].crop_ref` · `hero_bg.crop_ref`) — `source_image`/`source_file` ไม่ใช่ `_ref` (เป็นต้นฉบับ ไม่ต้อง import) · `title_raw` ของ 3 วัตถุ (Leg Bone · Pod Duang · Cake Molds) เก็บ `\n` ตามตัวขึ้นบรรทัดของ Figma แล้ว (P-162) — ตัดช่องว่างท้ายบรรทัดก่อน `\n` ทิ้ง (Figma มี "Bone \n(") · `title` ยังยุบเป็นช่องว่างเดียว
**ข้อควรรู้เมื่อใช้:** (1) **1506 Pod Duang**: seed `extra_images` มี 2 รายการ (`964:2117`, `964:2119`) แต่ `964:2119` อยู่ใน frame `visible:false` → Figma แสดง **1 รูป** — ใช้ `style.images[]` เป็นตัวจริง (2) **1484 Bas-relief**: text 3 ก้อนท้าย = หัวเรื่องซ้ำ (`plain_meta`) + lorem ipsum ×2 (`lorem_placeholder`) — P-213 ตัดสินแล้วว่าไม่มี caption (ไม่แสดง lorem) · รูปในแผงคือ 6 thumbnails `359×112` (FILL) ส่วนภาพ hero 8 รูปอยู่ที่ `hero[]` (3) 1512 hero layer `Frame 01…05` ไม่ใช่ `hero_bg`

**ตารางต่อ frame (ค่าทั้งหมดอยู่ใน seed `figma[].style` — ตารางนี้สรุปจากสคริปต์):**
| frame | คอลัมน์ข้อความ (node · pad-top · gap · เริ่ม y) | block ตามลำดับ (role node y/h · ตัวอักษร weight/size/lh · สี) | ป้ายที่แสดง (`meta_labels` · Caption) | ภาพ (`images[]` node bbox · r) |
|---|---|---|---|---|
| 1510 | `1041:1367` · 30 · 25 · y=982 | title `1041:1379` 982/90 800/36/45 #FFFFFF<br>collection `1041:1391` 1097/26 600/24/26 #F8F9FA<br>caption `1041:1403` 1148/984 400/18/24 #343A40 | Collection: · Caption: (ขึ้นบรรทัด 2×) | `1041:1452` [921,1052 360×589] r0 หมุน |
| 1476 | `670:2810` (=แผง) · 0 · 22 · y=1060 | title `670:2811` 1060/45 800/36/45 #D75F47<br>meta `1057:1177` 1127/240 500/24/30 #F8F9FA<br>caption `670:2813` 1389/456 400/18/24 #212529 | Section :, Period:, Place of Production / Findspot:, Material:, Dimensions:, Collection:, Other Information / Object Biography: · Caption: (ขึ้นบรรทัด 2×) | — |
| 1468 | `670:2796` (=แผง) · 0 · 22 · y=1060 | title `670:2797` 1060/45 800/36/45 #D75F47<br>meta `670:2798` 1127/240 700/24/30 #F8F9FA<br>caption `670:2799` 1389/456 400/18/24 #212529 | Section :, Period:, Place of Production / Findspot:, Material:, Dimensions:, Collection:, Specimen/Object biography: · Caption: (ขึ้นบรรทัด 2×) | — |
| 1503 | `956:977` · 30 · 25 · y=1101 | title `956:978` 1101/45 800/36/45 #D75F47<br>meta `956:979` 1171/156 600/20/26 #F8F9FA<br>caption `956:980` 1352/600 400/18/24 #343A40 | Material:, Dimensions:, Collection:, Other Information / Object Biography: · Caption: (ขึ้นบรรทัด 1×) | — |
| 1505 | `964:1541` · 0 · 22 · y=1020 | title `964:1542` 1020/45 800/36/45 #495057<br>collection `964:1543` 1087/50 500/20/25 #343A40<br>caption `964:1544` 1159/1344 400/18/24 #343A40 | Collection: · Caption: (ขึ้นบรรทัด 1×) | `964:1547` [731,1100 549×365] r0; `964:1808` [731,1495 549×365] r0 |
| 1507 | `1006:1186` · 0 · 22 · y=1020 | title `1006:1198` 1020/45 800/36/45 #495057<br>collection `1006:1210` 1087/25 500/20/25 #343A40<br>caption `1006:1222` 1134/960 400/18/24 #343A40 | Collection: · Caption: (ขึ้นบรรทัด 2×) | `1006:1237` [941,1100 232×243] r0; `1006:1249` [1039,1110 221×244] r0 |
| 1493 | `670:2874` · 0 · 15 · y=1050 | title `670:2875` 1050/45 800/36/45 #617250<br>plain_meta `670:2876` 1110/30 600/24/30 #FFFFFF<br>plain_meta `670:2877` 1155/60 600/24/30 #FFFFFF<br>caption `670:2878` 1230/408 400/18/24 #000000 | — · ไม่มีป้าย Caption | `670:2881` [731,1050 549×284] r0 (container r22); `670:2883` [731,1354 549×284] r0 (container r10) |
| 1512 | `3004:806` · 0 · 15 · y=940 | title `3004:807` 940/45 800/36/45 #495057<br>collection `3004:808` 1000/50 600/20/25 #343A40<br>caption `3004:809` 1065/864 400/18/24 #343A40 | Collection: · ไม่มีป้าย Caption | `3004:812` [731,940 549×284] r0 (container r22); `3004:814` [731,1244 549×284] r0 (container r10) |
| 1504 | `961:1218` · 0 · 22 · y=1020 | title `961:1219` 1020/45 800/36/45 #D75F47<br>meta `961:1220` 1087/250 700/20/25 #F8F9FA<br>caption `961:1221` 1359/672 400/18/24 #212529 | Section :, Period:, Place of Production / Findspot:, Material:, Dimensions:, Collection:, Other Information / Object Biography: · Caption: (ขึ้นบรรทัด 1×) | `961:1231` [731,1020 549×302.24] r0 |
| 1506 | `964:2110` · 0 · 22 · y=1020 | title `964:2111` 1020/90 800/36/45 #FFFFFF<br>collection `964:2112` 1132/25 500/20/25 #F8F9FA<br>caption `964:2113` 1179/600 400/18/24 #E9ECEF | Collection: · Caption: (ขึ้นบรรทัด 1×) | `964:2117` [921,1100 359×365] r0 |
| 1511 | `1042:1932` · 0 · 22 · y=1020 | title `1042:1944` 1020/45 800/36/45 #495057<br>collection `1042:1956` 1087/25 500/20/25 #343A40<br>caption `1042:1968` 1134/1224 700/18/24 #343A40 | Collection: · Caption: (ขึ้นบรรทัด 2×) | `1042:1982` [921,1120 360×589] r0 หมุน |
| 1483 | `670:2840` · 30 · 25 · y=1075 | title `670:2841` 1075/45 800/36/45 #FFFFFF<br>collection `670:2842` 1145/26 600/24/26 #F8F9FA<br>caption `670:2843` 1196/384 400/18/24 #343A40 | Collection: · ไม่มีป้าย Caption | `1041:1458` [866,1145 414×282.68] r0 |
| 1509 | `1041:884` · 0 · 22 · y=1020 | title `1041:885` 1020/90 800/36/45 #495057<br>collection `1041:886` 1132/25 500/20/25 #343A40<br>caption `1041:887` 1179/960 400/18/24 #343A40 | Collection: · Caption: (ขึ้นบรรทัด 2×) | `1041:890` [921,1120 360×589] r0 |
| 1508 | `1006:825` · 0 · 22 · y=1020 | title `1006:837` 1020/45 800/36/45 #495057<br>collection `1006:849` 1087/25 500/20/25 #343A40<br>caption `1006:861` 1134/744 400/18/24 #343A40 | Collection: · Caption: (ขึ้นบรรทัด 2×) | `1038:579` [921,1020 360×589] r0 หมุน |
| 1484 | `670:2849` · 30 · 25 · y=1140 | title `670:2850` 1140/90 800/36/45 #D75F47<br>plain_meta `670:2851` 1255/130 600/24/26 #F8F9FA<br>lorem_placeholder `670:2852` 1410/192 400/18/24 #343A40<br>lorem_placeholder `670:2853` 1627/168 400/18/24 #343A40 | — · ไม่มีป้าย Caption | 6× `670:2855…2860` [921,1110 359×112 step 122] FILL r0 |

**Page 2.1 (141 frame · ยังไม่อยู่ในขอบเขต D7 — สรุปจำนวน):** 119 frame เป็นแบบมาตรฐาน (hero `1440×~810` @y150, แผง y=960: 104 pad ล่าง 60 · 7 hero สูง 811 · 7 pad ล่าง 80 · 1 hero 809) · 22 frame ต่างรายเฟรม (panel y ระหว่าง 857–1117, hero 1120×730…1440×967; บางอัน hero y=64/89/90 และเฟรม GIF หลายชั้น) — ค่ารายเฟรมครบใน seed `figma[].style` · layout ตัวอย่าง: `layout/detail-p21-*.md` · โครง layout ที่พบ: A) `114484+114481` 79 frame · B) `Frame GIF + Frame 12685` · C) `Frame 114491/114494/114493` 15 · D) `Frame 12756` + แกลเลอรี/หลายรูป

**ลำดับวัตถุในกริด trail ช้าง (ตรง tile Ele 2,3,4,5,6 / 7,01,8,9,10 / 11–15 และการ์ด timeline):** 1 Elephant Leg Bone · 2 Singhasari Ganesha · 3 Ganesha · 4 Elephant Molar Coconut Grater · 5 Embossed Brass Basin · 6 Ivory Fire Piston · 7 Elephant and Mahout Kendi · 8 Patola Cloth (Cindai) · 9 Elephant-Shaped Relic Containers · 10 Pod Duang · 11 Hand-Carved Ivory and Metal Opium Pipe · 12 Tin Ingot in the Shape of Elephant · 13 Wooden Cold-Press Cake Molds… · 14 Siwaih · 15 Procession of a King or Noble (Bas-relief…)

### 4.8.2 Detail — **อัปเดต 2026-11-11** (BACKLOG #9 #10 #11 #12 · Figma ไฟล์ `lastModified 2026-11-11T08:23:07Z`) — เฉพาะ 15 frame Final trail ช้าง · ทับ §4.8/§4.8.1 เฉพาะที่ระบุ
**ที่มา:** ลูกค้าแก้ใน Figma แล้ว (หน้า `670:860` "Master Page Version #Final" และสำเนา `3117:3314` — **ข้อความในสอง page ตรงกัน** (เทียบแล้ว: ต่างเฉพาะข้อความเมนู Header) · id ฝั่ง `3117:3314` อยู่ในตาราง) · ภาพ render `design/frames/detail-3117-1511.png` (opium pipe) · `detail-3117-1633.png` (brass basin)
**node ข้อความถูกแทนใหม่ทุกตัว** (เช่น title Ganesha `670:2797` → `3083:813`) → ~~node id ใน `figma[].style.*` ของ seed เก่าแล้ว~~ **แก้แล้ว (P-245 ปิด · รัน design-detail-style v2 กับไฟล์ใหม่ — ดู §4.8.3)**

**1) โครงใต้หัวข้อ (#12)** — ใต้ชื่อวัตถุ (36/45 Heavy · สีตาม `detail_style.title_color` เดิมต่อวัตถุ) มี **บล็อก metadata ก้อนเดียว** (text node เดียว) บรรทัดละฟิลด์: **label ตัวหนา (Komet-Bold 700)** + ค่าตัวปกติ (500) · ขนาด 20/25 (บาง frame 20/26) · Patola 24/30 · Leg Bone (`1510`) = บรรทัดเดียว `Collection:` 24/26 w600 (สไตล์เดิม) · **ลำดับบรรทัดตายตัว:** `Period:` → `Place of production / Findspot:` → `Material:` → `Dimensions:` → `Collection:` → `Other information / Object biography:` (ข้ามบรรทัดที่ไม่มีค่า — ไม่มีบรรทัดว่าง) · ค่าไหนไม่มี = ไม่แสดง label · บรรทัด **`Section : …` เดิม หายไปจากทุก frame** (ไม่แสดงใต้หัวข้ออีก — ฟิลด์ `sh_thematic_section` เก็บไว้ แต่ไม่แสดงในหน้า detail) · "Other information / Object biography" ยาวหลายบรรทัดได้ (ตัดคำตามกล่อง 739/549 กว้าง) · label ในไฟล์สะกดไม่สม่ำเสมอ ("Other Information / Object Biography" ใน 1506) → **แสดง label มาตรฐาน "Other information / Object biography:"**
**2) Caption (#9 #11):** `textAlignHorizontal = JUSTIFIED` ✔ (ครบ 15/15 — #9 มีใน Figma แล้ว → CSS `text-align: justify` + `hyphens: auto`) · ข้อความขึ้นต้น `Caption:` (Komet-Bold SC 700) + **บรรทัดว่าง 1 บรรทัดระหว่างย่อหน้า** (`\n\n` ในข้อความ — #11 มีใน Figma แล้ว → render `<p>` แยก + margin ≈ 1 บรรทัด = **24px**) · **ยกเว้น 6 จุดใน 5 frame** ที่ขึ้นย่อหน้าด้วย `\n` ตัวเดียว (ไม่เว้นบรรทัด): 1503 (ก่อน "Elephant molars are among…" และ "The idea of miniature elephant…") · 1468 (ก่อน "Beneath this Ganesha statue…") · 1476 (ก่อน "The elephant's form also carr…") · 1506 (ก่อน "Pod duang served as…") · 1508 (ก่อน "This Acehnese siwaih…") → seed แปลงเป็น `\n\n` ให้เท่ากัน (ตีความเจตนา #11 · P-246) · 18/24 · ข้อความ caption ใหม่ **ต่างจาก seed `caption` 14/15** → อยู่ใน seed `objects[].figma_2026_11.caption` (P-244)
**3) ขอบบนภาพ (#10 "align กับ text ตามความเหมาะสม")** — Figma ไม่มีกฎเดียว: คอลัมน์รูปเริ่มที่ y เท่ากับ **บนสุดของบล็อก metadata** (ส่วนต่าง `+0`) ใน 1510 · 1476 · 1468 · 1503 · 1483 · เท่ากับ **บนสุดของหัวข้อ** ใน 1493 · 1512 · 1508 (ส่วนต่างเทียบ meta = −60/−60/−67) · ที่เหลือใกล้ meta (+3…+13, −12) · Bas-relief 1484 รูปเริ่มสูงกว่าหัวข้อ 30 → **ใช้ค่า y ต่อ frame ตามตารางข้างล่างเป๊ะ (เก็บใน `sh_layout`) — ไม่ต้องหากฎกลาง** · ภาพตัวอย่างคอมเมนต์ (`3117:4794` ref `a7bf0939`, `3117:4796` ref `85f70aee`): เส้นแดงจากขอบบนของภาพไปที่บรรทัดแรกของ metadata (หรือชื่อเรื่อง 2 บรรทัด)
**ตาราง geometry ใหม่ (เทียบ frame 1440 · bbox `[x,y,w,h]` · P=Period Pl=Place M=Material D=Dimensions C=Collection O=Other info/Object biography · ⚠ = หัวข้อใน Figma ต่างจาก seed `title`)**
| # | frame (Final / 3117) | หัวข้อ (Figma) | title node [bbox] | metadata node [bbox] ฟอนต์ | caption node [bbox] | ภาพในแผง [bbox] | ภาพบน − meta บน | แถวที่มี |
|---|---|---|---|---|---|---|---|---|
| 1 | `1041:1290` / `3117:4755` (1510) · h1764 | Elephant Leg Bone ⚠ | `1041:1379` [160,982,739,45] | `1041:1391` [160,1052,739,26] 24.0/26.0 | `1041:1403` [160,1103,739,504] | `1041:1452` [921,1052,360,589] | +0 | C |
| 2 | `670:2802` / `3117:4391` (1476) · h2302 | Singhasari Ganesha | `3083:758` [160,1090,739,45] | `3083:770` [160,1160,739,260] 20.0/26.0 | `3083:782` [160,1445,739,720] | `3083:791` [921,1160,359,421] | +0 | P Pl M D C O |
| 3 | `670:2786` / `3117:4367` (1468) · h2090 | Ganesha | `3083:813` [160,1090,739,45] | `3083:825` [160,1160,739,130] 20.0/26.0 | `3083:837` [160,1315,739,600] | `3083:846` [921,1160,359,421] | +0 | P Pl M D C |
| 4 | `956:975` / `3117:4486` (1503) · h2577 | Elephant molar coconut grater ⚠ | `3083:868` [160,1101,684,45] | `3083:880` [160,1171,684,234] 20.0/26.0 | `3083:892` [160,1430,684,984] | `3083:901` [866,1171,414,283] | +0 | M D C O |
| 5 | `3190:4671` / `3190:5587` (1633) · h2434 | Embossed brass basin with mythical animal motifs ⚠ | `3190:4949` [160,1020,843,45] | `3190:4961` [160,1087,739,150] 20.0/25.0 | `3190:4973` [160,1259,739,1032] | `3190:4984` [921,1100,359,269]<br>`3190:4993` [921,1399,359,271] | +13 | M D C O |
| 6 | `1006:922` / `3117:4726` (1507) · h2217 | Fire Piston ⚠ | `1006:1198` [160,1020,176,45] | `1006:1210` [160,1087,739,200] 20.0/25.0 | `1006:1222` [160,1309,739,792] | `1006:1237` [941,1100,232,243]<br>`1006:1249` [1039,1110,221,244] | +13 | M D C O |
| 7 | `670:2872` / `3117:4507` (1493) · h2150 | Elephant and Mahout Kendi | `670:2875` [160,1050,549,45] | `670:2876` [160,1110,549,125] 20.0/25.0 | `670:2878` [160,1250,549,744] | `670:2881` [731,1050,549,284]<br>`670:2883` [731,1354,549,284] | -60 | P Pl C |
| 8 | `3004:804` / `3117:4534` (1512) · h2046 | Patola cloth | `3004:807` [160,940,739,45] | `3004:808` [160,1000,739,180] 24.0/30.0 | `3004:809` [160,1195,739,720] | `3004:812` [921,940,359,284]<br>`3004:814` [921,1244,359,544] | -60 | P Pl M D C |
| 9 | `961:1024` / `3117:4563` (1504) · h2262 | Elephant-shaped containers ⚠ | `961:1219` [161,1020,474,45] | `961:1220` [161,1087,549,200] 20.0/25.0 | `961:1221` [161,1309,549,840] | `961:1231` [732,1090,549,302] | +3 | P Pl M D C O |
| 10 | `964:2090` / `3117:4623` (1506) · h2043 | Pod duang stamped with the chakra and great crown marks ⚠ | `964:2111` [160,1020,994,45] | `964:2112` [160,1087,739,175] 20.0/25.0 | `964:2113` [160,1284,739,600] | `964:2117` [921,1100,359,365] | +13 | P Pl M D C O |
| 11 | `1042:1719` / `3117:4775` (1511) · h2478 | Hand-carved opium pipe ⚠ | `1042:1944` [160,1020,418,45] | `1042:1956` [160,1087,739,225] 20.0/25.0 | `1042:1968` [160,1334,739,984] | `1042:1982` [921,1090,360,589] | +3 | M D C O |
| 12 | `670:2828` / `3117:4441` (1483) · h1836 | Tin ingot in the shape of an elephant | `670:2841` [160,1075,739,45] | `670:2842` [160,1145,739,104] 20.0/26.0 | `670:2843` [160,1274,739,408] | `1041:1458` [921,1145,359,245] | +0 | P Pl M C |
| 13 | `1041:866` / `3117:4682` (1509) · h2445 | Wooden cold-press cake moulds with elephant, horse and fowl motifs ⚠ | `1041:885` [160,1020,812,90] | `1041:886` [160,1132,739,275] 20.0/25.0 | `1041:887` [160,1429,739,912] | `1041:890` [921,1120,360,589] | -12 | Pl M D C O |
| 14 | `1006:561` / `3117:4655` (1508) · h2065 | Siwaih | `1006:837` [160,1020,112,45] | `1006:849` [160,1087,739,100] 20.0/25.0 | `1006:861` [160,1209,739,696] | `1038:579` [921,1020,360,589] | -67 | P Pl M C |
| 15 | `670:2847` / `3117:4461` (1484) · h2125 | Bas-relief of a procession, illustrating the life of the Buddha ⚠ | `670:2850` [160,1140,739,90] | `670:2851` [160,1255,739,200] 20.0/25.0 | `670:2852` [160,1480,739,480] | `670:2855` [921,1110,359,192]<br>`670:2856` [921,1332,359,171]<br>`670:2857` [921,1533,359,112] | -145 | P Pl M D C |

**เทียบกับ CONTENT-MODEL (`sh_*` ที่มีอยู่แล้วครบ — ไม่ต้องเพิ่มฟิลด์เพื่อ #12):** `sh_period` ✔ · `sh_place_of_production` ✔ · `sh_material` ✔ · `sh_dimensions` ✔ · `sh_collection` ✔ · `sh_object_biography` ✔ (ป้ายแสดง "Other information / Object biography") · `sh_local_name` ✔ (ไม่มีใน 15 ชิ้นนี้) · **เสนอให้ core (ไม่บังคับ):** (ก) `sh_scientific_name` (text · "Scientific name / Taxonomy:" — มีใน Page 2.1 ของ trail อื่น ไม่มีใน 15 ชิ้นช้าง) · (ข) template แสดงบล็อกตามลำดับตายตัวข้างบน ข้ามค่าว่าง (ไม่ใช้ฟิลด์ใหม่) · (ค) `sh_thematic_section` เลิกแสดงในหน้า detail
**seed:** `objects[15 ชิ้น].{period,place_of_production,material,dimensions,collection,object_biography}` = ค่าใหม่ตาม Figma · **`info_rows[]`** = ข้อความดิบตามลำดับ Figma `{label,key,value}` · `figma_2026_11.{title,caption,frame,*_node}` = ข้อความใหม่ที่ยังไม่ใช้ · `title`/`caption` ฟิลด์หลัก **ไม่ถูกแก้** (9/15 หัวข้อต่าง · 14/15 caption ต่าง — P-244)

### 4.8.3 Detail — **geometry ภายในแผงตาม Figma 2026-11-11** (P-245 ปิดแล้ว · `design/detail-style.py` v2 รันกับ `figma-file.json` lastModified `2026-11-11T08:23:07Z` · 15 frame Final · ทับตาราง §4.8 และ §4.8.1 เดิม)
**ค่าจริงทั้งหมดอยู่ใน seed `objects[].figma[].style`** (importer ส่งเข้า `sh_layout`) — ตารางนี้สรุปจากสคริปต์ (รันซ้ำได้ผลเดิม byte-for-byte: `/usr/bin/python3 design/detail-style.py --seed <objects.json> [--no-write]`) · พิกัด = เทียบ frame 1440 · ⛔ **ตาราง §4.8 (ความสูง frame/แผง/ตำแหน่ง Back) และ §4.8.1 (ตารางต่อ frame · สี) เป็นค่าก่อน 2026-11 — ใช้ตารางนี้/seed แทน**
**สิ่งที่เปลี่ยนจากเดิม**
- **Brass Basin ถูกแทนด้วย frame ใหม่ `3190:4671` "Desktop - 1633"** (เดิม `964:1522` "1505") → seed `figma[].frame_id` = `3190:4671` · `detail_style.source_frame` = `3190:4671` · (`figma[].page` ยังเป็นข้อความ "Page 2 Version #Final 01092026" ตามเดิมเพื่อไม่ให้ตัวกรองใน `build-image-map.py` / `refs.php` พัง — ชื่อ page จริงตอนนี้ "Master Page Version #Final 01092026" `670:860` เก็บไว้ที่ `figma_2026_11.style_rebuilt.page`)
- **ทุก node ข้อความ/คอลัมน์/ภาพในแผงเป็น id ใหม่** (แผง 1468 `670:2796`→`3083:800` · 1476 `670:2810`→`3083:745` · 1503 `956:976`→`3083:855` ฯลฯ) → `panel_node` `panel_children` `text_col` `text_blocks[].node` `image_col` `images[].node` `title_node` `caption_node` `meta_node` อัปเดตหมด · **ข้อความคอลัมน์ซ้ายเป็นลูกของ frame คอลัมน์ทุก frame** (1468/1476 เดิมเป็นลูกตรงของแผง — ไม่มี `direct_in_panel` แล้ว)
- **สี:** พื้นแผง `#DEDCC3` และหัวข้อ `#495057` **เหมือนกันทั้ง 15 frame** (1512 เดิมไม่มี fill → ตอนนี้มี · เดิมต่อวัตถุ เช่น `#B2BBAA`/`#D75F47`/`#FFFFFF`) · **สี info block / caption ยังต่างรายเฟรม** (ค่าใน `text_blocks[].color`): info `#343A40` ใน 1509·1633·1484·1511·1507·1512·1508 · `#495057` ใน 1493·1510(Collection)·1503·1504·1468·1506·1476·1483 · caption `#343A40` (ส่วนใหญ่) · **`#212529` ใน 1504 · `#495057` ใน 1493** → seed `detail_style.{panel_bg,title_color,collection_color,text_color}` และ `style.{panel_bg,title_color,caption_color,meta_color}` = ค่าใหม่ (`collection_color` = สีของ info block) ⚠ **ถ้า prod ตั้งสีเดิมไว้ — core ตัดสินตาม D40 (แทนเฉพาะที่ยังเท่าค่า import เดิม)**
- ความสูง frame/แผง/Back ใหม่ทุก frame (เช่น 1510: 2283→1764 · 1468: 2005→2090 · 1503: 2094→2577) · hero **ไม่เปลี่ยน** ใน 14 frame (id/ภาพ/ตำแหน่งเดิม) · 1633 hero = `Frame GIF` `3190:4674` 15 ภาพ (id ใหม่ · ภาพเดิม)
- **info block (#12)**: text_blocks = `title` → `meta` (บล็อกเดียว บรรทัดละฟิลด์) → `caption` (1510 = `collection` 1 บรรทัด 24/26) · ไม่มี `plain_meta`/`lorem_placeholder` แล้ว (1484 ตัดออกจาก Figma) · `rows[]` = `{label (มาตรฐาน), field, value, label_weight 700, value_weight 500}` · เพิ่ม `label_in_figma` เมื่อการสะกดใน Figma ต่างจาก label มาตรฐาน (เช่น 1506 "Other Information / Object Biography:") · `meta_labels[]` ตามลำดับ Figma
**คีย์ใหม่ใน `style` (ย้ำ #9–#12 เป็นตัวเลขอ่านง่าย — ตัวเลขทั้งหมดมีในคีย์เดิมด้วย)**
| คีย์ | ความหมาย |
|---|---|
| `info_layout.caption_align` | `JUSTIFIED` ทั้ง 15 (#9) → `text-align: justify; hyphens: auto` |
| `info_layout.caption_paragraph_gap_px` + `caption.paragraph_gap` | #11 ช่องว่างระหว่างย่อหน้า = **1 บรรทัดว่าง = 24px** (line-height caption) · `{mode:"blank_line", px:24, blank_line_points, single_break_points}` · `single_break_points` = จุดที่ Figma ใช้ `\n` เดียว (ไม่เว้นบรรทัด): **รวม 6 จุดใน 5 frame** (1503×2 · 1468 · 1476 · 1506 · 1508) → render เว้นบรรทัดเท่ากัน (P-246) · บรรทัดที่มีแต่ช่องว่าง (`"\n \n"` ใน 1508) นับเป็นบรรทัดว่าง · `caption.body_break_runs` = นับจาก Figma ดิบ (`{"2":n,"1":m}`) · `label_breaks`=2 ทั้ง 15 |
| `info_layout.info_node` · `info_role` · `title_bottom_to_info_top` · `info_font` · `info_label_weight`/`info_value_weight` | #12 บล็อกใต้หัวข้อ: node · role · ระยะจากล่างหัวข้อถึงบนบล็อก (px) · ฟอนต์ (20/25 · 20/26 · Patola 24/30 · 1510 24/26 w600) · ป้าย 700 / ค่า 500 |
| `image_col.top_y` · `top_vs_info` · `top_vs_title` · `top_rule` · `info_layout.image_top_*` | #10 y ขอบบนของภาพบนสุดในคอลัมน์ขวา (เทียบ frame) และส่วนต่างจากบน info block / บนหัวข้อ · `top_rule` = `info_top` (ส่วนต่าง 0) · `title_top` (เท่าหัวข้อ) · `custom` (ค่าเฉพาะ frame) · **ใช้ `images[].bbox` / `image_col.bbox` ต่อ frame เป๊ะ ไม่หากฎกลาง** |
**ตารางต่อ frame (15 · เรียงตาม trail ช้าง)**
| # | frame (Final) | hero (node · bbox · ภาพ) | แผง (node · y · h · pad บน/ล่าง · พื้น `#DEDCC3` ทุก frame) | คอลัมน์ข้อความ (node · bbox · pad-top · gap) | คอลัมน์ภาพ | ภาพ `images[]` (bbox) | ภาพบนสุด y (ส่วนต่างจาก info · หัวข้อ · กฎ) | Back [x,y] (115×50) |
|---|---|---|---|---|---|---|---|---|
| 1 | `1041:1290` Desktop - 1510 1440×1764 | `1041:1293` Frame Gif [60,150 1320×742] (6 ภาพ) | `1041:1366` y=892 h=872 pad 60/60 | `1041:1367` [160,952 739×655] pad-top 30 gap 25 | `1041:1451` [921,952 360×689] pad-top 100 | `1041:1452`[921,1052,360,589] | 1052 (info +0 · หัวข้อ +70 · info_top) | [160,1650] |
| 2 | `670:2802` Desktop - 1476 1440×2302 | `1057:968` Frame GIF [0,160 1440×810] (10 ภาพ) | `3083:745` y=1000 h=1302 pad 60/60 | `3083:746` [160,1060 739×1105] pad-top 30 gap 25 | `3083:783` [921,1060 359×520.57] pad-top 100 | `3083:791`[921,1160,359,420.57] | 1160 (info +0 · หัวข้อ +70 · info_top) | [160,2188] |
| 3 | `670:2786` Desktop - 1468 1440×2090 | `1051:8181` Frame GIF [0,150 1440×810] (10 ภาพ) | `3083:800` y=1000 h=1090 pad 60/60 | `3083:801` [160,1060 739×855] pad-top 30 gap 25 | `3083:838` [921,1060 359×520.57] pad-top 100 | `3083:846`[921,1160,359,420.57] | 1160 (info +0 · หัวข้อ +70 · info_top) | [160,1976] |
| 4 | `956:975` Desktop - 1503 1440×2577 | `958:1226` Rectangle 26 [160,150 1120×831] (1 ภาพ) | `3083:855` y=1011 h=1566 pad 60/60 | `3083:856` [160,1071 684×1343] pad-top 30 gap 25 | `3083:893` [866,1071 414×382.68] pad-top 100 | `3083:901`[866,1171,414,282.68] | 1171 (info +0 · หัวข้อ +70 · info_top) | [160,2463] |
| 5 | `3190:4671` Desktop - 1633 1440×2434 | `3190:4674` Frame GIF [0,150 1440×810] (15 ภาพ) | `3190:4936` y=960 h=1474 pad 60/80 | `3190:4937` [160,1020 739×1271] pad-top 0 gap 22 | `3190:4975` [921,1020 359×650] pad-top 80 | `3190:4984`[921,1100,359,269] · `3190:4993`[921,1399,359,271] | 1100 (info +13 · หัวข้อ +80 · custom) | [160,2318] |
| 6 | `1006:922` Desktop - 1507 1440×2217 | `1006:923` Frame GIF [0,150 1440×810] (15 ภาพ) | `1006:1185` y=960 h=1257 pad 60/80 | `1006:1186` [160,1020 739×1081] pad-top 0 gap 22 | `1006:1224` [921,1020 359×334] pad-top 80 | `1006:1237`[941,1100,232,243] · `1006:1249`[1039,1110,221,244] | 1100 (info +13 · หัวข้อ +80 · custom) | [160,2116] |
| 7 | `670:2872` Desktop - 1493 1440×2150 | `670:2884` Frame 12779 [0,150 1440×840] (7 ภาพ) | `670:2873` y=990 h=1160 pad 60/100 | `670:2874` [160,1050 549×944] pad-top 0 gap 15 | `670:2879` [731,1050 549×588] pad-top 0 | `670:2881`[731,1050,549,284] · `670:2883`[731,1354,549,284] | 1050 (info -60 · หัวข้อ +0 · title_top) | [160,2040] |
| 8 | `3004:804` Desktop - 1512 1440×2046 | `3004:815` Frame 12706 [160,150 1120×730] (5 ภาพ) | `3004:805` y=880 h=1166 pad 60/60 | `3004:806` [160,940 739×975] pad-top 0 gap 15 | `3004:810` [921,940 359×848] pad-top 0 | `3004:812`[921,940,359,284] · `3004:814`[921,1244,359,544] | 940 (info -60 · หัวข้อ +0 · title_top) | [160,1944] |
| 9 | `961:1024` Desktop - 1504 1440×2262 | `961:1027` Frame GIF [0,150 1440×810] (14 ภาพ) | `961:1217` y=960 h=1302 pad 60/80 | `961:1218` [161,1020 549×1129] pad-top 0 gap 22 | `961:1230` [732,1020 549×372.24] pad-top 70 | `961:1231`[732,1090,549,302.24] | 1090 (info +3 · หัวข้อ +70 · custom) | [161,2173] |
| 10 | `964:2090` Desktop - 1506 1440×2043 | `964:2093` Frame GIF [0,150 1440×810] (15 ภาพ) | `964:2109` y=960 h=1083 pad 60/80 | `964:2110` [160,1020 739×864] pad-top 0 gap 22 | `964:2115` [921,1020 359×445] pad-top 80 | `964:2117`[921,1100,359,365] | 1100 (info +13 · หัวข้อ +80 · custom) | [160,1928] |
| 11 | `1042:1719` Desktop - 1511 1440×2478 | `1042:1720` Frame GIF [0,150 1440×810] (6 ภาพ) | `1042:1931` y=960 h=1518 pad 60/80 | `1042:1932` [160,1020 739×1298] pad-top 0 gap 22 | `1042:1981` [921,1020 360×659] pad-top 70 | `1042:1982`[921,1090,360,589] | 1090 (info +3 · หัวข้อ +70 · custom) | [160,2368] |
| 12 | `670:2828` Desktop - 1483 1440×1836 | `670:2831` Frame Gif [0,150 1440×810] (6 ภาพ) | `670:2839` y=985 h=851 pad 60/60 | `670:2840` [160,1045 739×637] pad-top 30 gap 25 | `1041:1460` [921,1045 359×345.13] pad-top 100 | `1041:1458`[921,1145,359,245.13] | 1145 (info +0 · หัวข้อ +70 · info_top) | [160,1722] |
| 13 | `1041:866` Desktop - 1509 1440×2445 | `1041:867` Frame GIF [0,150 1440×810] (12 ภาพ) | `1041:883` y=960 h=1485 pad 60/80 | `1041:884` [160,1020 739×1321] pad-top 0 gap 22 | `1041:889` [921,1020 360×689] pad-top 100 | `1041:890`[921,1120,360,589] | 1120 (info -12 · หัวข้อ +100 · custom) | [160,2375] |
| 14 | `1006:561` Desktop - 1508 1440×2065 | `1006:562` Frame GIF [0,150 1440×810] (15 ภาพ) | `1006:824` y=960 h=1105 pad 60/80 | `1006:825` [160,1020 739×885] pad-top 0 gap 22 | `1038:578` [921,1020 360×589] pad-top 0 | `1038:579`[921,1020,360,589] | 1020 (info -67 · หัวข้อ +0 · title_top) | [160,1956] |
| 15 | `670:2847` Desktop - 1484 1440×2125 | `670:2861` Frame 12756 [60,150 1320×870] (8 ภาพ) | `670:2848` y=1050 h=1075 pad 60/60 | `670:2849` [160,1110 739×850] pad-top 30 gap 25 | `670:2854` [921,1110 359×535] pad-top 0 | `670:2855`[921,1110,359,192] · `670:2856`[921,1332,359,171] · `670:2857`[921,1533,359,112] | 1110 (info -145 · หัวข้อ -30 · custom) | [160,2010] |

**ภาพครอป (`images[].crop_ref`)**: ค่าที่สร้างใหม่ **11 ไฟล์** ใน `design/assets/` (ชื่อไม่ชนของเดิม · 22 ไฟล์เดิมที่ prod ใช้อยู่ไม่ถูกแตะ — ไม่ลบ/ไม่เขียนทับ) — ภาพ/node เดิมที่พิกเซลเท่าเดิม ใช้ชื่อไฟล์เดิมซ้ำ · `1041:1458` (Tin Ingot 1483) ขนาดกรอบเปลี่ยน 414×283→414×282.68 → ไฟล์ใหม่ `266b59cd….crop-1041-1458.r2611.png` · ที่เหลือเป็นภาพของ node ใหม่ (Brass Basin ×2 · Bas-relief ×3 · Ganesha · Singhasari Ganesha · Molar grater · Patola ×2) · ไฟล์ crop เขียนผ่านโฟลเดอร์ชั่วคราว `design/.crop-tmp-*` แล้ว `os.replace` ทีละไฟล์ (ไม่มีไฟล์ค้างครึ่งทาง) · `--no-write` ไม่เขียนอะไร
**Bas-relief 1484 (เปลี่ยน):** รูปในแผง = **3 รูป** (`670:2855` ลายเส้น 359×192 · `670:2856` ลายเส้น 359×171 · `670:2857` ภาพถ่ายภาพนูน 359×112) แทน 6 thumbnails เดิม · ไม่มี lorem/หัวเรื่องซ้ำ (ตัดออกจาก Figma แล้ว) · รูปเริ่มสูงกว่าหัวข้อ 30px (`top_vs_title`=−30)
**ไม่แตะ:** `title` · `title_raw` · `caption` (ค่าหลัก = ค่า import เดิม ใช้เป็นฐานเทียบตาม D40 — ข้อความใหม่อยู่ที่ `figma_2026_11.{title,caption}`) · `info_rows[]` · ฟิลด์ metadata (ปรับไปแล้วโดย design-batch-hzd)

### 4.9 Nav / ปุ่ม / component เสริมที่อยู่ใน Figma แต่ไม่ได้ใช้ในเฟรม Final
- แถบ zone-nav 5 ช่อง `Frame 12723` (`670:1107`): Barlow Condensed 20/24 (active Bold 700 + พื้น `#D65F47`@20%, ไม่ active Medium 500) padding 12/12/10/10 — ตัวอย่างอยู่ใน style guide; ไม่ปรากฏในหน้า (อาจใช้เป็น sub-nav) → ใช้ถ้าต้องการ ไม่บังคับ
- ปุ่ม `Button 1` (TH "นัดปรึกษาฟรี"), ไอคอน mobile/mail, `lucide:arrow-up` = ชิ้นส่วนแม่แบบเดิม ไม่เกี่ยวกับเว็บนี้

### 4.10 Zone 4 chooser — หน้าคั่น "เลือกสัตว์" (D37 · BACKLOG #13 · Figma `3117:3946` **Desktop - 1513** · page `3117:3314` section "Zone 4" `3140:1357`) · ภาพ render `design/frames/zone4-chooser-1513.png` · `zone4-section54.png`
**ที่มา/สถานะ:** คลิกการ์ด Zone 4 (Home) → หน้านี้ (D37) · Figma หน้า 1513 วาง **เฉพาะพื้นหลังเส้นประ + แถบ ZONE** — **ภาพสัตว์ยังไม่ได้วางลงหน้า** (อยู่เป็นไฟล์แยก 17 ตัวใน Section 54 `3140:1362` › "รูปภาพสัตว์แบบแยก แต่ละตัว" `3140:1398` + ภาพรวมการจัดวาง `3140:1359/3140:1360` ที่ข้อความ `3140:1401` สั่ง "***Zone 4 - ให้อิงตามรูปแบบที่เขาจัดวาง") → **ตำแหน่งต่อตัวด้านล่างวัดจากภาพรวม** (จับคู่ silhouette ไฟล์แยก ↔ ภาพรวม · IoU 0.89–0.99 · ±1–2 px) · ข้อมูลเต็ม: `content/seed/zone4-chooser.json`
**โครง (frame 1440×1065 · ตำแหน่งเทียบ frame)**
| section | พิกัด | รายละเอียด |
|---|---|---|
| Header #1/#02 | `[0,0 1440×86]` · `[0,86 1440×64]` | เหมือนทุกหน้า (§3) |
| Cover = พื้นหลังเส้น `3117:3949` › `3117:3950` | `[160,150 1120×540]` | ภาพ `278fb0ba…` (4096×2304 ครอปหน้าต่าง y 7.9%–93.4% ตาม imageTransform แล้ว → ไฟล์ **`design/assets/zone4-animals/_chooser-bg-cover-1120x540.png`** 4096×1971 = 3.66×) · **เส้นทึบแนวนอน 2 เส้น** (บน y≈264 · ล่าง y≈658 เทียบ frame 1440 · สีเทาเข้ม ~1px · วัดจากภาพ render) + **เส้นประทแยง 8 เส้น** เอียงขวา (ล่างซ้าย → บนขวา · ขีดสั้นสีดำ ~1px) ตัดขอบซ้าย/ขวา · เส้นที่ 3 (จากซ้าย) ยื่นเหนือเส้นบน (ตามภาพ) |
| **สัตว์ 17 ตัว** | ตามตาราง (กล่อง PNG รวมขอบโปร่งใส) | วางทับ Cover · ภาพพื้นโปร่ง · สเกล **0.14 × px จริง** ทุกตัว · เรียงเป็น 3 แถวคร่าว ๆ สลับตามเส้นประ (ดูภาพ `design/assets/zone4-animals/_composite-reference.png` = ตำแหน่งอ้างอิง ซึ่งมีเส้น+สัตว์ครบ) |
| แถบ ZONE 1–5 `Frame 12684` `3117:3951` | `[160,710 1120×166]` r22 + shadow `0 1 4 #0C0C0D@5%` ×2 | ภาพ `56172917…` ครอปแล้ว → **`_zone-bar-zone4-active-1120x166.png`** (3886×576) · **Zone 4 active** (พื้น `#EC7966` ตัวหนังสือขาว) · 4 แถบอื่นจาง: Z1 `#D0E2DF` · Z2 `#EFDAE3` · Z3 `#F5BBB1` · Z5 `#B7E3DA` · แถบ = 5 ช่อง 224 กว้างที่ x = 160/384/608/832/1056 · y 833–878 · มีไอคอนกวาง (silhouette สีน้ำตาลเข้ม) + เส้นแนวนอน 2 เส้นเหนือแถบ Zone 4 (ฝังในภาพ) · แถบเป็นภาพทั้งก้อน — **ไม่ระบุว่ากดได้** (P-248) |
| ปุ่ม Back `3117:3947` | `[160,940 115×50]` | §3.3 · ย้อนหน้า |
**ตารางสัตว์ ↔ trail (17 ตัว = 17 trail · พิกัดเทียบ frame 1440 · `png` = กล่องไฟล์เต็ม · `ตัวจริง` = กล่องส่วนที่ไม่โปร่งใส)**
| trail slug | Figma node | ไฟล์ (`design/assets/zone4-animals/`) | px จริง | png `[x,y w×h]` | ตัวจริง `[x,y w×h]` | คลิก |
|---|---|---|---|---|---|---|
| garuda | `3140:1390` (`Zone4 header-03`) | `garuda.png` | 939x1010 | `[214.6,172.5 131.5×141.7]` | `[224.8,193.6 107.7×109.9]` | ไม่ลิงก์ |
| buffalo | `3140:1396` (`Zone4 header-09`) | `buffalo.png` | 989x862 | `[250.0,293.9 138.5×120.9]` | `[261.2,306.0 119.2×100.0]` | ไม่ลิงก์ |
| naga | `3140:1391` (`Zone4 header-04`) | `naga.png` | 1038x1398 | `[446.3,157.7 145.3×196.1]` | `[470.9,172.5 95.4×168.5]` | ไม่ลิงก์ |
| seashell | `3140:1392` (`Zone4 header-05`) | `seashell.png` | 953x883 | `[637.0,193.2 133.4×123.8]` | `[644.8,206.5 117.6×99.2]` | ไม่ลิงก์ |
| phoenix | `3140:1393` (`Zone4 header-06`) | `phoenix.png` | 876x826 | `[892.2,203.8 122.6×115.9]` | `[902.1,212.5 105.3×98.9]` | ไม่ลิงก์ |
| hornbill | `3140:1394` (`Zone4 header-07`) | `hornbill.png` | 798x791 | `[1051.9,208.7 111.7×110.9]` | `[1058.5,228.2 94.9×78.1]` | ไม่ลิงก์ |
| dragon | `3140:1385` (`Zone4 header-15`) | `dragon.png` | 897x1440 | `[179.4,439.5 125.6×202.0]` | `[192.8,445.9 102.3×187.7]` | ไม่ลิงก์ |
| lion | `3140:1380` (`Zone4 header-10`) | `lion.png` | 1271x911 | `[333.3,397.9 177.9×127.8]` | `[354.7,407.0 149.3×107.9]` | ไม่ลิงก์ |
| fish | `3140:1381` (`Zone4 header-11`) | `fish.png` | 1186x918 | `[537.4,332.3 166.0×128.8]` | `[551.6,348.7 144.9×97.5]` | ไม่ลิงก์ |
| water-bird | `3140:1387` (`Zone4 header-17`) | `water-bird.png` | 918x953 | `[691.7,433.5 128.5×133.7]` | `[697.0,455.3 120.6×100.0]` | ไม่ลิงก์ |
| deer | `3140:1382` (`Zone4 header-12`) | `deer.png` | 1144x932 | `[714.9,307.5 160.2×130.7]` | `[721.1,324.3 150.4×94.8]` | ไม่ลิงก์ |
| makara | `3140:1384` (`Zone4 header-14`) | `makara.png` | 1327x579 | `[904.0,336.5 185.8×81.2]` | `[910.9,360.2 168.4×45.5]` | ไม่ลิงก์ |
| bird | `3140:1388` (`Zone4 header-18`) | `bird.png` | 699x636 | `[917.5,441.8 97.9×89.2]` | `[921.0,461.0 91.3×59.4]` | ไม่ลิงก์ |
| rooster | `3140:1383` (`Zone4 header-13`) | `rooster.png` | 855x770 | `[1114.2,303.2 119.7×108.0]` | `[1130.4,320.2 85.3×76.7]` | ไม่ลิงก์ |
| horse | `3140:1386` (`Zone4 header-16`) | `horse.png` | 1215x1095 | `[466.1,498.5 170.1×153.6]` | `[476.6,507.6 146.3×139.2]` | ไม่ลิงก์ |
| turtle | `3140:1395` (`Zone4 header-08`) | `turtle.png` | 777x572 | `[803.5,556.2 108.8×80.2]` | `[809.7,578.8 90.5×51.2]` | ไม่ลิงก์ |
| elephant | `3140:1389` (`Zone4 header-19`) | `elephant.png` | 1504x1052 | `[1023.8,481.7 210.6×147.6]` | `[1030.9,499.4 198.5×124.1]` | **`/timeline/`** |

**พฤติกรรม**
- **hover บนสัตว์ = ขยายใหญ่ขึ้น** (เจ้าของสั่ง) · Figma ไม่มี prototype interaction หรือ variant hover ของสัตว์ใน Section 54 → ขนาด/เวลา **`[P]`**: เสนอ ×**1.14** รอบกึ่งกลางกล่อง PNG (ยืมจาก tile trail 184→210 §3.6) · **0.3s ease-in-out** · ตัวที่ขยายอยู่ชั้นบนสุด (z-index) · `prefers-reduced-motion` = ไม่ขยาย (P-249)
- **คลิก:** **ช้าง → `/timeline/`** · **สัตว์อีก 16 ตัวไม่ลิงก์** `[P]` ตาม D37 (ไม่มี timeline · D7) — แสดง cursor ปกติ ไม่ใช่ลิงก์ · ไม่ใส่ `href`/`role=link`
- เส้นประ/เส้นทึบ = พื้นหลังคงที่ ไม่ขยับ ไม่ animate
- ไอคอนแถบ trail 35² ×17 (`Frame trails` `3137:715` ใน Section 54 · ตัวหนึ่ง 45²) มีใน Figma แต่ **ไม่ได้วางในหน้า 1513** → ไม่ทำ (P-250)
- มือถือ/แท็บเล็ต: ไม่มีใน Figma (D5) → ยุบเป็นกริด 2–3 คอลัมน์ตามดุลยพินิจ wp-dev (ไม่ใช่สเปก)

### 4.11 Zone 5 — **หน้าหลัก 1514 = ใช้ได้ (สเปกเต็ม §4.11a · P-172) · หน้าย่อย 1515/1516 = ไม่ทำ (D40 · Lorem) — ตารางด้านล่างเป็นบัญชีโครง** · Figma section "Zone 5" `3140:1356` (7368×3792) ใน page `3117:3314`
ภาพ render: `design/frames/zone5-1514.png` · `zone5-detail1-1515.png` · `zone5-detail2-1516.png` · `zone5-action-zone.png` · เดิมใน seed: zone 5 `has_page:false` ("ไม่มี frame หน้า zone 5 ใน Figma" P-204) → **ตอนนี้มีแล้ว 3 หน้า + 1 แผ่น action**
| # | frame | ชื่อ/หน้าที่ | ขนาด | ส่วนประกอบหลัก (เทียบ frame) | โต้ตอบ | ข้อความ/ข้อมูล |
|---|---|---|---|---|---|---|
| 1 | `3117:3955` **Desktop - 1514** | **หน้าหลัก Zone 5 "The Curious Minds’ Play Zone"** | 1440×2090 | Header · Cover [160,170 551×384] (ภาพการ์ด `4361cf0c`) + ข้อความ [731,170 549×347] H1 36/45 + 2 ย่อหน้า · แถบ ZONE [160,579 1120×166] (Zone 5 active · ภาพ `82bf391e`) · **การ์ด 4 คอลัมน์** [160,770 280×459 ×4] (`Cover`/`Cover2`/`Cover3`/`Cover4` = Passport / Card game / "Good day to you, Cast Brass Tabletop Horse" / Shared Histories folder) · แถวภาพ 4 ใบ [160,1254 280×123 ×4] · **History** [160,1402 1120×453] ("Shared Histories") · Back [160,1965] | hover การ์ด/History → สลับภาพ (smart-animate 0.3s) · **ลูกศรเชื่อม (annotation `3117:4047/4053`): การ์ด 1 → 1515 · การ์ด 2 → 1516** (ไม่มี click ที่ตั้งค่าใน prototype จริง) · การ์ด 3/4/History ไม่ระบุปลายทาง | ข้อความจริง (ไม่ใช่ lorem): "This zone invites you to carry the journey beyond the screen. Visiting one of our ten partner museums across five countries? Pick up a Shared Heritage Passport…" (2 ย่อหน้า) |
| 2 | `3117:3983` **Desktop - 1515** | **Museum Passport** (หน้าย่อย) | 1440×1783 | hero สีชมพู `#D3AEC2` [160,150 1120×500] (2 ภาพ `b842bc29`) · หัวข้อ "Museum Passport" + ข้อความ [160,700 549×325] · ภาพปก "PASSPORT" 551×384 ("`.gif animate of 10 color cover.`" = หมายเหตุออกแบบ: ให้ทำ GIF วนสี 10 สี) · ตารางสี่เหลี่ยม 2 แถว × 5 (184² พื้น `#D96C4D` = **placeholder**) · Back | — | **เนื้อหาเป็น Lorem Ipsum** (ยังไม่มีจริง) |
| 3 | `3117:4012` **Desktop - 1516** | **Card-and-Art Game** (หน้าย่อย) | 1440×3302 | hero [160,150 1120×499] (ภาพ `49118c0d`) · หัวข้อ + ข้อความ [160,699 739×241] · ปกเกม 361×384 (GIF วนสี) · ไพ่ 3 แถว (หลังไพ่ 172×250 ภาพ `1ff49559`) · ปุ่ม **"download template"** [592,2062 256×52 · 24px] (ลิงก์ไฟล์ไม่ระบุ) · ภาพ "(A5) draw your artefact" 600×852 (`e0269b3c`) · **"#sharedheritage"** 28px · Back | — | หัวข้อจริง · ข้อความ **Lorem Ipsum** |
| 4 | `3117:4039` **Action Zone** (แผ่นอธิบาย) | สถานะ non-active/active ของ Cover×4 + History | 1379×847 | ภาพ `36bc17e8` 1255×524 + หมายเหตุ "Mouse Hover จะเกิดการเปลี่ยนของรูปที่เปลี่ยนไป" | hover | — (ไม่ใช่หน้า) |
| 5 | `3117:4044` Section 52 · `3117:4050` Section 53 | ตัวอย่างการ์ดที่ลิงก์ไป 1515 / 1516 | — | `Cover` · `Cover2` + ลูกศร | — | — |
component: `Cover` `3117:5082` · `Cover2` `3117:5097` · `Cover3` `3117:5087` · `Cover4` `3117:5092` (ตัวละ 2 variant = default / hover · ON_HOVER smart-animate 0.3s) · `History` `3117:5102` (History1 → History2 hover) · ข้อความ H1 `3117:3962` · ย่อหน้า `3117:3963`
**เทียบ CONTENT-MODEL (zone 5):** term `zone-5` มี `sh_has_page = false` · ยัง**ไม่มี**: template หน้า zone 5 · เนื้อหา 2 ย่อหน้าของหน้าหลัก (zone term description ได้) · ภาพการ์ด 4 ใบ + hover 4 ใบ + แถวภาพ 4 + History 2 สถานะ (ฟิลด์ภาพ 14 ช่อง หรือเป็น page/Gutenberg ของหน้านี้) · หน้าย่อย 1515/1516 = 2 หน้าใหม่ (page post) · ปุ่ม "download template" (file) · ข้อความ Passport/Card game = Lorem (ไม่ควร import) · อาจต้องใหม่: GIF วนสี 10 สี (ภาพ/วิดีโอ)
**(ตอบแล้วด้วย D40 — เก็บไว้เป็นประวัติ) คำถามให้เจ้าของ (P-251):** (ก) ทำ Zone 5 ในรอบนี้ไหม หรือแสดงการ์ด "เร็ว ๆ นี้" ไว้ก่อน · (ข) ทำเฉพาะหน้าหลัก 1514 (ข้อความจริงครบ) แล้วเว้น 1515/1516 ที่ยังเป็น Lorem · (ค) การ์ด 1/2 ลิงก์ไป 1515/1516 จริงไหม (Figma เป็นแค่ลูกศร) · (ง) GIF 10 สี ใครทำ · (จ) ปุ่ม "download template" ไฟล์อะไร

**ตัดสินแล้ว (D40):** ทำเฉพาะหน้าหลัก 1514 · การ์ด 1/2 (ไปหน้าย่อย 1515/1516) = ป้าย "เร็ว ๆ นี้" ไม่ลิงก์ (`soon:true`) · การ์ด 3/4/History ไม่มีปลายทางใน Figma → ไม่ลิงก์ (admin เลือกเอง) · GIF 10 สี/ปุ่ม download template = ไม่อยู่ในรอบนี้ (หน้าย่อยไม่ทำ)

#### 4.11a หน้าหลัก Zone 5 `3117:3955` Desktop - 1514 (1440×2090 · พื้น `#FFFFFF`) — สเปกเต็ม
ที่มาค่า: `[N]` = node Figma (`design/figma-file.json` lastModified 2026-11-11T08:23:07Z) · `[P]` = prototype/Action Zone (`3117:4039` · ไม่มีใน REST dump — ดูภาพ `design/frames/zone5-action-zone.png`) · พิกัดเป็นของ frame 1440 (content กว้าง 1120 ที่ x=160..1280) · ภาพทุกชิ้นครอปตาม `imageTransform` แล้วที่ `design/assets/zone5/` (ดูหัวข้อ "ไฟล์ภาพ" ด้านล่าง)

**Auto-layout `[N]`:** `Frame 114484` `3117:3957` [160,150 1120×1705] เรียงแนวตั้ง gap **25** · ลูก: hero (114483) → แถบ ZONE (12684) → การ์ด 4 (114485) → แถวภาพ 4 (114492) → History · Header #1 [0,0 1440×86] + Header #02 [0,86 1440×64] เหมือนหน้า zone อื่น (§3.4) · ปุ่ม Back `3117:3956` [160,1965 115×50]
| # | ส่วน | node · ตำแหน่ง/ขนาด `[N]` | รายละเอียด |
|---|---|---|---|
| 1 | แถว hero `Frame 114483` | `3117:3958` [160,150 1120×404] · แนวนอน gap **20** · padding-top **20** | ซ้าย Cover · ขวา คอลัมน์ข้อความ |
| 1a | **Cover** | `3117:3959` [160,170 551×384] · ภาพ `Rectangle 1` `3117:3960` เต็มกรอบ · ไม่มีมุมโค้ง/เงา/clip | ภาพ `4361cf0c` (ไพ่ 3 ใบ The Body of Vessel · The Power of Literature,Mythology & Folklore · The Spirit of Garuda) **พื้นโปร่งใส** วางบนพื้นขาว · ภาพต้นฉบับ 4096×2102 ครอปตาม imageTransform (เกินขอบบน/ล่างเล็กน้อย = โปร่งใส) → `zone5/cover.png` 1102×768 (2×) · fill ที่สอง `92884b9a` `visible:false` ไม่ใช้ |
| 1b | คอลัมน์ข้อความ `Frame 12683` | `3117:3961` [731,170 549×347] · แนวตั้ง gap **20** · padding-top **40** | ชิด x=731 (หลัง Cover 551 + gap 20) ถึง 1280 |
| 1c | **H1** | `3117:3962` [731,210 549×45] | "The Curious Minds’ Play Zone" · Komet Heavy 800 · **36/45** · left · `#212529` · letterSpacing 0 · (เครื่องหมาย ’ = U+2019 · **ไม่ใช่** "Minds’s") |
| 1d | **ข้อความ 2 ย่อหน้า** | `3117:3963` [731,275 549×242] | Komet Medium 500 · **18/22** · **justified** · `#000000` · ย่อหน้าคั่นด้วยบรรทัดว่าง 1 บรรทัด (22px · ต้นฉบับ `\n\n` · seed เก็บเป็น `\n` ต่อ 1 ย่อหน้า) · ย่อหน้า 1 = 6 บรรทัด · ย่อหน้า 2 = 4 บรรทัด ที่กว้าง 549 · ข้อความใน seed `zones[5].intro_short` (คัดลอกตรง · ตัดช่องว่างท้ายบรรทัด) · ไม่มี style override ในตัวอักษร (ไม่มีตัวหนา/ลิงก์) |
| 2 | **แถบ ZONE** `Frame 12684` | `3117:3964` [160,579 1120×166] · ห่างล่าง hero 25 · r22 + เงา `0 1 4 #0C0C0D@5%` + `0 1 4 #0C0C0D@10%` (ไม่ clip) · ภาพ `3117:3965` เต็มกรอบ | ภาพ raster `82bf391e` **Zone 5 active** (พื้น `#4BBAA3` (pixel) ตัวหนังสือขาว · Z1–Z4 จาง) + ไพ่ 3 ใบ/จระเข้เหนือแถบ (พื้นโปร่งใส) · ข้อความ "ZONE n" ฝังในภาพ · ครอปแล้ว `zone5/zone-bar-zone5-active.png` 2240×332 · เหมือน `_zone-bar-zone4-active-1120x166.png` ของ §4.10 แต่ active = Zone 5 · **การกดแถบ**: ตาม P-248 (§4.10) Z1–3 → หน้า zone · Z4 → หน้าคั่น zone 4 · Z5 = หน้านี้ (Figma ไม่มี interaction ของแถบในหน้านี้ — ใช้กฎเดียวกับ §4.10) |
| 3 | **การ์ด 4 คอลัมน์** `Frame 114485` | `3117:3966` [160,770 1120×459] · แนวนอน ไม่มี gap · ห่างจากแถบ 25 | การ์ดละ **280×459.28** (ไม่มี gap · ชิดกัน) ที่ x **160 · 440 · 720 · 1000** · ไม่มีมุมโค้ง/เงา/ขอบ · ภาพเต็มกรอบ (ครอปตาม imageTransform → `zone5/card<n>-*.png` 560×918) |
| 3.1 | การ์ด 1 "Museum Passport" | instance `Cover` `3117:3967` (component set `3117:5082`) · default `3117:5084` `caeb1ae0` · hover `3117:5086` `b842bc29` | พื้น `#D3AEC2` (pixel) · "MUSEUM PASSPORT · ALONG ANIMAL TRAILS · Creations and Trails that Bind Southeast Asia" + รูปสมุด PASSPORT (ภาพทั้งใบเป็น raster) · hover = องค์ประกอบเลื่อน/ขยับ (โลโก้+ชื่อขยับขึ้น · สมุดเปิดมีตราประทับ) พื้นสีเดิม · **`soon:true`** → ป้าย "เร็ว ๆ นี้" ไม่ลิงก์ (D40) · Figma: ลูกศร annotation → 1515 (ไม่มี click จริง) |
| 3.2 | การ์ด 2 "Card-and-Art Game" | `Cover2` `3117:3968` (set `3117:5097`) · default `3117:5099` `e6c1c610` · hover `3117:5101` `71d361cd` | พื้น `#F0AC9F` · หลังไพ่สีแดง 2 แถว×3 (hover = ไพ่พลิกเปิดหน้า The Body of / The Spirit of แถวละ 1 ใบ) · **`soon:true`** · Figma: ลูกศร → 1516 |
| 3.3 | การ์ด 3 "Good day to you, Cast Brass Tabletop Horse" | `Cover3` `3117:3969` (set `3117:5087`) · default `3117:5089` `0faaa979` · hover `3117:5091` `f730fc5f` | พื้น `#DDDBC2` · ข้อความฝังในภาพ (hover = ภาพควายขยายใหญ่ · เอาข้อความออก · ม้า/ตะเกียงขยับ) · Figma ไม่ระบุปลายทาง → `soon:false` ไม่มีลิงก์ (admin ใส่ `sh_page_card_3_link` ได้) |
| 3.4 | การ์ด 4 "Shared Histories folder" | `Cover4` `3117:3970` (set `3117:5092`) · default `3117:5094` `81340943` · hover `3117:5096` `ed2e9ab8` | พื้น `#6BB8A4` · ปกสมุด 2 เล่มซ้อน (hover = เล่มกางออกเป็นสองคู่) · ไม่ระบุปลายทาง → `soon:false` ไม่ลิงก์ |
| 4 | **แถวภาพ 4** `Frame 114492` | `3117:3971` [160,1254 1120×123] · แนวนอน ไม่มี gap · ห่างจากการ์ด 25 | ช่องละ **280×123** ที่ x **160 · 440 · 720 · 1000** (`Frame 114488–114491` `3117:3972/3974/3976/3978` → ภาพ `Rectangle 1` `3117:3973/3975/3977/3979`) · ภาพ `5dbfb6bf` (ปก passport 3 สี) · `db315b99` (การ์ด 3 ใบ) · `3c590da0` (ตะเกียงดิน ขาว/แดง/น้ำเงิน) · `d4b50c78` (คน 3 + เรือ · **พื้นขาวทึบ** · scaleMode FILL) · แต่ละช่องครอปตาม imageTransform (ช่องที่ 4 = FILL cover กึ่งกลาง) → `zone5/strip1..4.png` 560×246 · เป็นของประดับ (ไม่มี hover/ลิงก์) |
| 5 | **History** | instance `3117:3980` [160,1402 1120×453] (component set `3117:5102` · ขนาดเดิม 1140×453 → override กว้าง 1120) · ห่างจากแถวภาพ 25 · ไม่มีมุมโค้ง · default `3117:5104` `3656bc03` · hover `3117:5106` `d8bec706` | พื้น `#8E775A` (pixel) · สมุดเปิด "Shared Histories" + ตัวละคร 7 คน · hover = ภาพเปลี่ยน ("Histories" เด่นขึ้น ดินสอย้ายไปขวา ตัวละครขยับ) · ครอป → `zone5/history.png` · `history-hover.png` 2240×906 · ไม่ระบุปลายทาง → ไม่ลิงก์ |
| 6 | Back | `3117:3956` [160,1965 115×50] · ห่างล่าง History 110 | ปุ่มเดียวกับหน้า zone อื่น (§3.5) · frame สูง 2090 (Back ล่าง 2015 + 75) |

**พฤติกรรม:** hover การ์ด 1–4 และ History → สลับเป็นภาพ hover (smart-animate **0.3s** `[P]` ON_HOVER · องค์ประกอบในภาพ hover อยู่คนละตำแหน่งกับ default — ใช้ cross-fade ไม่ใช่ morph · Action Zone `3117:4039` ภาพ `36bc17e8`) · ภาพ hover ขนาด/ตำแหน่งกรอบเดียวกับ default (ครอปด้วย transform เดียวกัน) → ซ้อนทับกัน · ไม่มี scale/เงา/ขอบ · **การ์ดที่ `soon`**: ยังสลับ hover ได้ แต่ไม่ใช่ลิงก์ (ไม่มี cursor pointer) + ป้าย "เร็ว ๆ นี้" (ข้อความ/ตำแหน่งป้าย = ดุลยพินิจ wp-dev ตาม `sh_label_coming_soon` · Figma ไม่มี) · มือถือ/แท็บเล็ต: ไม่มีใน Figma (D5)

**ฟิลด์ที่หน้าใช้ (CONTENT-MODEL §12 · seed `content/seed/zones.json` → `zones[5]`):**
| ฟิลด์ (sh_…) | seed key | node | ตัวอย่าง |
|---|---|---|---|
| ชื่อ term + H1 | `name` · `page_title` | `3117:3962` | "The Curious Minds’ Play Zone" (`name_previous` = ตัวพิมพ์ผิด "Minds’s") |
| `sh_intro` (2 ย่อหน้า) | `intro_short` | `3117:3963` | ย่อหน้า 1 "This zone invites you to carry the journey beyond the screen. …leave a message of peace." · ย่อหน้า 2 "For teachers and museum educators, … lower secondary classrooms." |
| `sh_cover_image` | `page.cover` | `3117:3960` | `design/assets/zone5/cover.png` |
| `sh_banner_image` (แถบ ZONE) | `page.zone_bar` | `3117:3965` | `zone5/zone-bar-zone5-active.png` |
| `sh_page_card_<1..4>_image` · `_image_hover` · `_soon` | `page.cards[n].image` · `image_hover` · `soon` | `3117:5084/5086 · 5099/5101 · 5089/5091 · 5094/5096` | การ์ด 1–2 `soon:true` · 3–4 `false` · `alt`/`figma_target` เป็นหมายเหตุ (importer ไม่ต้องอ่าน) |
| `sh_page_strip_image_<1..4>` | `page.strip[0..3]` | `3117:3973/3975/3977/3979` | `zone5/strip1..4.png` |
| `sh_page_history_image` · `_hover` | `page.history.image` · `image_hover` | `3117:5104` · `3117:5106` | `zone5/history.png` · `history-hover.png` |

**ไฟล์ภาพ (`design/assets/zone5/` · 16 ไฟล์ · สร้างด้วย `design/tools/crop-zone5.py` — อ่านไฟล์ที่ดึงแล้ว ไม่เรียก API · `--no-write` ได้):** `cover` · `zone-bar-zone5-active` · `card1-passport` (+`-hover`) · `card2-card-art-game` (+`-hover`) · `card3-brass-horse` (+`-hover`) · `card4-shared-histories` (+`-hover`) · `strip1..4` · `history` (+`-hover`) · เป็นภาพ**ที่ครอปตรงกับกรอบที่แสดง** (ไม่ใช่ภาพต้นฉบับ) ที่ **2× ของขนาดแสดง** (ไม่ขยายเกินต้นฉบับ) · เพราะภาพต้นฉบับ 4096 กว้างถูกครอปด้วย imageTransform ใน Figma — ใช้ไฟล์ดิบที่ราก `design/assets/<ref>.png` ตรงๆ แล้ว `object-fit` จะได้สัดส่วนผิด · sub-folder ใหม่ `zone5` ต้องเพิ่มใน `sh_import_asset_subdirs()` + deploy.sh

---
## 5. ฟิลด์ที่หน้าใช้ (ชื่อ · ชนิด · ที่มา node-id · ตัวอย่างจาก seed) — ให้ core-contract ทำ CONTENT-MODEL
ชื่อเป็นข้อเสนอ snake_case (core ปรับเป็น `sh_…`) · "seed key" = ตำแหน่งใน `content/seed/*.json` · ภาพทุกชิ้นอ้าง `imageRef` (ไฟล์ `design/assets/<ref>.png|jpg` — ref ที่ใช้ = fill ที่ `visible:true` ชั้นบนสุด) · **hero_images (เฟรม GIF) ยังไม่ดาวน์โหลด** (P-209)

### 5.1 `object` (CPT) — ใช้ใน: trail page · detail · timeline card  (seed: `objects.json` → `objects[]`; ตัวอย่างหลัก = Ganesha `670:2786`)
| ฟิลด์ | ชนิด | บังคับ | ที่มา (node) | ตัวอย่าง (จาก seed) | seed key |
|---|---|---|---|---|---|
| `title` | text (ตัวแบ่งบรรทัดยุบเป็นช่องว่าง; ดิบใน `title_raw`) | ✔ | title 36/800 `670:2797` | "Ganesha" · ยาว: "Pod Duang (Bullet Money) stamped with the Chakra and Great Crown Marks, Reign of King Rama IV" | `title` |
| `trails` | taxonomy `trail` (หลายค่า) | ✔ | section ใน Page 2.1 / กริดช้าง | `["elephant"]` · หลาย trail: Embossed Brass Basin = `["elephant","garuda","lion","naga"]` | `trails[]` |
| `trail_order` | int ต่อ trail | — | ลำดับ tile ช้าง (§4.8) | Ganesha `{"elephant":3}` | `trail_order` |
| `collection` | text | ✔ (119/120 มี) | `Collection: …` เช่น `1220:3064` | "National Museum Bangkok" | `collection` |
| `thematic_section` | text | — | `Section : …` `670:2798` (หรือการ์ด timeline `1232:1723`) | "Cosmology Religion & Spirituality" · Common Kingfisher = "Sky" | `thematic_section` |
| `period` | text | — | `Period:` | "10th–11th century" | `period` |
| `place_of_production` | text | — | `Place of Production / Findspot:` | "Candi Singhasari, East Java, Indonesia" | `place_of_production` |
| `material` · `dimensions` · `local_name` | text | — | บรรทัดใน metadata block | `material`: "Andesite, ****Volcanic rock" (คัดลอกตรง รวม `****`) · `dimensions`: "Height: 172 cm" · `local_name` (Liturgical Pulpit Covers): "Pha Hum Thammasan / ผ้าหุ้มธรรมมาสน (ผ้าทอเกาะยอ)" | `material` `dimensions` `local_name` |
| `object_biography` | textarea | — | `Specimen/Object biography:` / `Other Information / Object Biography:` | "The Dutch government in Java presented this to King Chulalongkorn (Rama V) of Thailand during his visit in 1896." | `object_biography` |
| `caption` | long text (หลายย่อหน้า) | ✔ (ยกเว้น Bas-relief ที่มีแต่ lorem) | ข้อความหลัง "Caption:" `670:2799` | "Carved from volcanic stone, this monumental statue depicts Ganesha…" | `caption` |
| `hero_images` (GIF = ลำดับเฟรม) | image[] 1440×810 (เฟรมแรก opacity 0) | ✔ | เฟรมใต้ `Frame GIF` `1051:8181` | Ganesha = 10 เฟรม: `1051:8204` ref `88f0278d…`, `1051:8221` ref `31ff0ee0…` … · Leg Bone = 6 เฟรม (มี `1041:1305` 1320×742) · จำนวนเฟรมต่อวัตถุ 1–24 | `figma[0].hero_images[]` `hero_image_count` |
| `hero_still` (ภาพพื้นหลัง hero) | image 1440×790 | **ไม่ใช้** (ซ่อน) | `Rectangle 1` `670:2788` ใน `Frame 12682` ที่ `visible:false` | ref `0652a86e6bd0…` — **ไม่แสดงในดีไซน์** · เก็บไว้ใช้เป็น poster ได้ถ้าตกลง (P-209) | `figma[0].hidden_images_not_rendered[]` |
| `extra_images` | image[] (0–2 ปกติ ~359 กว้าง; Bas-relief = แกลเลอรี 14) | — | ภาพในแผงเนื้อหา เช่น `Rectangle 27` `1041:1452` | Leg Bone: `1041:1452` 360×589 ref `4953122e17ae…` · Tin Ingot: `1041:1458` 414×283 ref `266b59cd…` · Bas-relief: `670:2855…` thumbnails 359×112 ×6 | `figma[0].extra_images[]` |
| `panel_bg` | color hex | ✔ | fill แผง `670:2796` | `#B2BBAA` (1512 Patola ไม่มี fill = ขาว → null) | `detail_style.panel_bg` |
| `title_color` · `text_color` · `collection_color` | color hex | ✔ | fill ของ text node | Ganesha `#D75F47` · `#343A40` · (ไม่มี collection บรรทัดแยก) | `detail_style.*` |
| `layout` (ต่อวัตถุ) | struct: `panel_y` `panel_h` `panel_padding_top_bottom` `panel_gap` `panel_children[]` `hero[]` `back_button` `frame_size` | ✔ (ถ้าจะให้ตรง 100%) | ตาราง §4.8 · เก็บใน seed ทุก frame | Ganesha: panel_y=1000 h=1005 · pad 60/80 · hero `[0,150 1440×810]` · back `[160,1887 115×50]` | `figma[].style.*` |
| `layout.inner` (ต่อวัตถุ · 15 frame Final) | struct: `text_col` `text_blocks[]` (role · bbox · font · color · runs · rows) `meta_labels[]` `caption{label,label_breaks,body_break_runs…}` `image_col` `images[]{bbox,radius,crop_ref…}` `hero_bg` (1493) | ✔ (ถ้าจะให้ตรง 100%) | node ใน panel ของแต่ละ frame (§4.8.1) | 1510: `text_col.padding_top`=30 · `meta_labels`=["Collection:"] · `caption.label_breaks`=2 · `images[0].crop_ref`=`4953122e…crop-1041-1452.png` | `figma[].style.{text_col,text_blocks,meta_labels,caption,image_col,images,hero_bg}` |
| `tile_image` / `tile_image_hover` | image 184² / 210² | ✔ | component set `Ele 2` `939:774` (default `939:775`, hover `939:776`) · trail อื่น `Bird 1` `1018:1372` | Leg Bone: `939:775` ref `70cf8ed70724…` / `939:776` ref `e978e4de5e9b…` · Bird: `1018:1373` ref `4c493fdc4a29…` / `1018:1374` ref `f8cdca6ed212…` | (ยังไม่อยู่ใน seed — อ้างด้วย node นี้ · เติมรอบ D7 ถัดไปถ้าต้อง) |
| `timeline_card_image` | image (cutout ~180–300 กว้าง) | trail ช้าง | `Frame 114464` ลูก | Leg Bone: `1232:1714` 300×202 ref `4953122e17ae…` · Singhasari Ganesha `1232:1720` 300×279 ref `b0afe5baa2b4…` · Ganesha `1232:1726` 180×187 | `timeline.card_frame` + `timeline-elephant.json items[].thumbnail_nodes` |
| `status[]` | internal | — | ข้อสังเกต (placeholder/lorem/stale) | `["title_placeholder"]`, `["lorem_ipsum_in_frame","caption_missing_lorem_ipsum_only"]` | `status[]` |

### 5.2 `trail` (taxonomy) — ใช้ใน: home icon grid · zone grid · trail page · timeline nav  (seed: `trails.json`; ตัวอย่าง = elephant)
| ฟิลด์ | ชนิด | ที่มา (node) | ตัวอย่าง | seed key |
|---|---|---|---|---|
| `name` / `slug` | text / slug | component set ใน Action Trails (`670:9643` Elephan) | "Elephan" → slug `elephant` (ชื่อแสดงผลยังไม่กำหนด — P-2xx) | `name` `slug` |
| `order_home_icons` | int 1–17 | ลำดับใน `Frame 12796` (`670:9724`) | elephant = 17 | `order_home_icons` |
| `icon_home` + `icon_home_hover` | image 165² / 185² | `670:9575` (ref `5f216ed1368d…` Rooster) / `670:9574` (ref `17bbb16a79e3…`) | Rooster ใช้ 165→185 | `figma.icon_tile_component_set` |
| `tile_zone_grid` + hover | image 184² / 210² | `1018:1372` Bird 1… (`zone_grid_tile_component_set`) | Bird: 184 ref `4c493fdc…` → 210 ref `f8cdca6e…` · elephant/water-bird ไม่มี (P-212) | `figma.zone_grid_tile_component_set` · `in_zone_grid_frame_1472_1474` |
| `tile_bg` | group + hex | pixel ของกริด | `{"group":"beige","hex":"#E0DEC9"}` | `tile_bg` |
| `timeline_icon_group_color` | hex | pixel `Frame trails` (`797:2110`) | mustard `#C3BC85` (elephant) | (§2.1) |
| `description` | long text | `797:1881` | "Elephants have lived alongside the peoples of South-East Asia for millennia…" | `description` (เฉพาะ elephant) |
| `title_on_trail_page` | text | `797:1880` | "Elephant’s Trail" | `title_on_trail_page` |
| `cover_image` | image 1120×497 | `796:1799` ref `ed0e5ee93dd2…` | Elephant_Zone2-1 | `trail_cover_node` |
| `has_timeline` | bool | frame `670:1551` | elephant เท่านั้น | `timeline_page_frame` |
| `object_count` / `object_slugs` | derived | — | elephant = 16 (15 Final + Gringsing) | `object_count` `object_slugs` |

### 5.3 `zone` — ใช้ใน: home · zone pages · zone bar  (seed: `zones.json`)
| ฟิลด์ | ชนิด | ที่มา (node) | ตัวอย่าง | seed key |
|---|---|---|---|---|
| `number` | int 1–5 | — | 1 | `number` |
| `title` | text (2 บรรทัดด้วย `\n`) | `I670:9480;670:9416` | "Animals in Nature \n& Ecological Wisdom" | `name` `name_raw` |
| `color_hex` | hex | pixel แถบ zone | `#14705E` | `color_hex` |
| `card_image` + `card_image_hover` | image 373×487 / 560×294 | Zone 1: `670:9415` ref `342629afe7c3…` / `670:9419` ref `35c9b660c3b2…` · Zone 4: `670:9463`/`670:9466` ref `0c5c09f6ab7c…`/`1feab554459a…` · Zone 5: `670:9519` ref `728a2a8d19b4…` | — | `card_node` `card_component_set` |
| `cover_image` | image 1120×410 | `1013:1289` ref `10247f33df7e…` (Zone 1) | — | `cover_node` |
| `banner_image` | image 1120×166 (แถบ ZONE 1–5) | `1013:1292` ref `413791d606d5…` | — | (frame ใน `page_frame`) |
| `has_page` | bool | — | zone 5 = false | `has_page` |
| `page_title` (ใหม่ 2026-11-11 · zone 1–3) | text | `3117:5053` / `5067` / `5063` | "Animals in Nature and Mythology" | `page_title` (= `name`) · เดิมเก็บใน `name_previous` |
| `name_banner` (ใหม่) | text (ตรงตัวอักษรในภาพ banner · ใช้เป็น alt) | ภาพ banner | "Animals in Nature & Mythology" | `name_banner` |
| `intro_short` + `body_full` (ใหม่) | textarea (ย่อหน้า = `\n`) | `3117:5054` (ย่อ) · `3117:5060` / `5068` / `5064` (เต็ม) | zone 1 ย่อ = 2 ย่อหน้า · zone 2/3 ย่อ = 1 ย่อหน้า | `intro_short` `body_full` `intro_nodes` |
| `read_more` labels (ใหม่) | UI string | `3117:4321` / Hide variant | "Read more >>" ↔ "Hide >>" | `read_more.collapsed/expanded` |
| `banner_image` (ใหม่ · แทน cover เดิม 1120×410) | image 4096×1503 | `3117:3716` (zone 1 ref `57fe08a9…`) · `3117:3686` (zone 2 ref `262bafe2…`) · `3117:3745` (zone 3 ref `1fda5191…`) | ไฟล์ใน `design/assets/zone-banners-2026-11/` | `banner_image.file/ref/figma_node` |

### 5.4 `timeline_item` (trail ช้าง) — ใช้ใน: timeline  (seed: `timeline-elephant.json`)
| ฟิลด์ | ชนิด | ที่มา (node) | ตัวอย่าง | seed key |
|---|---|---|---|---|
| `object` | ref → `object` | การ์ด `Frame 12683` | Elephant Leg Bone | `items[].key` / `title` |
| `card_order` | int 1–15 | x ของการ์ด (ซ้าย→ขวา) | Leg Bone = 1 (x=500) | `card_order` |
| `illustration_order` | int | ลำดับจุดบนภาพ (อ่านจาก PNG) | Leg Bone = 14 | `illustration_order` |
| `era_label` | text | ฝังในภาพ `1232:1711` (อ่านได้ ~5/15) | "20th–21st century" | `illustration_era_label_raster` |
| `card_title` | text | `1232:1716` | "Elephant Leg Bone (Elephas maximus indicus)" | `title` `title_node` |
| `card_meta` | text 14/24 (หลายบรรทัด) | `1232:1717` | "SECTION : Land\nPeriod: 20th-21st century\n…" | `meta_raw` + ฟิลด์แยก `thematic_section` `period` … |
| `thumbnail` | image | `1232:1714` ref `4953122e17ae…` (300×202) | — | `thumbnail_nodes` |
| **(D39) `thumbnail` ชิ้น 5 · 7 เปลี่ยน node** | image | ชิ้น 5 `3160:1508` ref `cba74a7d…` (เดิม `1232:1738`) · ชิ้น 7 `3103:1181` ref `621646b5…` (เดิม `1232:1752`) | — | `thumbnail_nodes` (+ `thumbnail_refs` ชื่อ imageRef เต็ม ใช้ patch image-map) |
| `sh_line_art` (D32 → **D35/D36: "ภาพก่อน (ภาพใต้เส้น)"**) | image · **ไม่บังคับ** (ว่างได้ · ไม่จำเป็นต้องเป็นลายเส้น) | `3103:1040` (ชิ้น 2) · `3103:1056` (3) · `3246:3914` (4) · `3160:1510` (5) · ชิ้น 1 (`3103:1037`) และ 6–15 ไม่มี | `02-singhasari-ganesha.png` · `03-ganesha.png` · `04-elephant-molar.png` (ไอคอนช้างทั่วไป) · `05-embossed-brass-basin.png` (ช้างทองเหลืองตัดชิ้นเล็ก สี 567×382) | `line_art_ref` (ชื่อไฟล์ใน `design/assets/lineart/` — **ชิ้นที่ไม่มี ไม่ใส่ key** · ชิ้น 1 ไม่มีแล้ว) |
| **`sh_below_image` (D39)** | image · **ไม่บังคับ** (ว่าง = ใช้ `sh_thumbnail` เป็นภาพใต้เส้น) | ใต้เส้น: ชิ้น 1 `3103:1037` · 7 `3103:1187` · 9 `3103:1220` (ชิ้นอื่นไม่ต้องมี) | `bbc1e161…png` (ชิ้น 1) · `555d365b…png` (7) · `1c0fc05b…png` (9) | `items[].below_ref` (ชื่อไฟล์ใน `design/assets/` ระดับบน · `below_node` เป็นข้อมูลอ้างอิง) |
| `reflection_title` / `reflection_body` | text | **(D34)** `3103:1377` / `3103:1378` (เดิม `1232:1816` / `1232:1817` — ข้อความเก่าที่ซ่อน · ดู §4.5c.7 ข้อ 7) | "Reflection" / "The objects in this trail reveal the elephant's enduring presence…" | `reflection.*` |
| `timeline_illustration` | image 11000×791 | `1232:1711` ref `791ae91b792b…` | — | `timeline_illustration_node` |

### 5.5 `article` / หน้าข้อความ — ใช้ใน: Home intro · หน้า 1471 · map slider  (seed: `articles.json`)
| ฟิลด์ | ชนิด | ที่มา (node) | ตัวอย่าง | seed key |
|---|---|---|---|---|
| `title` | text | `670:1223` / slide `670:1479` | "Introduction" · ": People, Places, and Shared Histories" | `title` |
| `summary_paragraphs[]` | text[] | `670:1224`, `670:1225` (Home: `670:9373`, `670:9374`) | "What comes to mind when you look at the title of this digital exhibition?…" | `summary_paragraphs[]` |
| `body` | long text | `670:1227` | "The Southeast Asian region has long been shaped by the movement of ideas…" | `body` |
| `image` | image 549×284 (r22) | Intro `670:1221` ref `bec4f0860329…` · Knowing SEA `670:1236` ref `74e1d6fd0e84…` · Home `670:9370` (551×284) ref `3f4cfd0076c9…` | — | (node ใน §4.2) |
| `read_more_label` | text | `670:1226` | "Read more >>" | `read_more_label` |
| `map_slide` + `paragraphs[]` | int 1–4 + text[] | variant `670:1461…` (`670:1480`, `670:1481`) | slide 1: 2 ย่อหน้า | `articles[].map_slide` `paragraphs[]` |
| `map_image` | image 1120×620 | `670:1462` ref `39907d2c90ea…` | — | — |
| `pins[]` | struct x,y,popup | หมุด 34×34 + popup `670:1613…` | popup ใช้ข้อความเดียวกับ slide | — (ยังไม่แยก) |

### 5.6 `site` / options — ใช้ใน: ทุกหน้า  (seed: `site.json`)
| ฟิลด์ | ชนิด | ที่มา (node) | ตัวอย่าง | seed key |
|---|---|---|---|---|
| `nav_primary[5]` | menu items (label + url ว่าง) | Header #02 `I958:1432;670:1146…1150` | "Main Page", "Shared Heritage Framework", … | `nav_primary` |
| `nav_utility[6]` | menu items | Header #1 `I958:1433;670:1094…1099` | "Visit", "Exhibition and Events", … | `nav_utility` |
| `logo_header_image` | image (พื้น Header #1) | `670:9386` ref `709572bf7eca…` (.jpg) | โลโก้ TEMASEK + SHARED HERITAGE ฝังในภาพ | `brand.note` |
| `logo_unesco` | SVG | `670:1088` | `design/assets/svg/unesco-logo.svg` 171.57×36 | `brand.logos[0]` |
| `labels` | text map | `I670:9375;670:1786` | `read_more`="Read more >>", `back`="Back", `zone_prefix`="ZONE" | `labels` |
| `home.hero` | image 1120×466 + (ข้อความฝังในภาพ) | `670:9367` ref `2e3fa9c46cbd…` | text_in_image: "ALONG ANIMAL TRAILS" / "Creations that Bind South-East Asia" | `home.hero` |
| `home.introduction` | title + paragraphs + read_more | `670:9372`, `670:9373`, `670:9374` | "Introduction" | `home.introduction` |
| `home.zone_cards[3]` · `zone_cards_lower[2]` | text | `I670:9480;670:9416`… `I670:9534;670:9464` | "Animals in Nature \n& Ecological Wisdom" | `home.zone_cards` `zone_cards_lower` |
| `home.trails_section` | title + tagline + 2 paragraphs + read_more | `I886:1787;886:1761`… | "Trails" · "Step beyond traditional gallery walls and dive into the animal trails!" | `home.trails_section` |
| `home.side_navigator` | 4 dots → scroll target | `871:2240/2241/2242/2247` → `670:9368/670:9479/886:1795/670:9541` | order 1 = Introduction | `home.side_navigator.dots[]` |
| `home.trails_animation_frames[12]` | image 1120×630 ×12 | `AAMM -01` `680:10513…10948` | state 1 ref `5ebfca80f961…` | (node ใน §6.2) |
**เพิ่มสำหรับ Timeline v2/v3 (D23/D26 · เสนอให้ core ยก — ⛔ ผมไม่แก้ CONTENT-MODEL · เฉพาะที่จำเป็นจริง)** · D26: `strip_dot_x` ❌ ไม่ต้อง (ไม่ใช้ raster) · `sh_thumbnail`+`sh_thumbnail_2` ใช้ทุกภาพ (บนเส้น/mini-map/ใต้เส้น) · `sh_line_art` ✔ **D32 กลับมา (ไม่บังคับ)** — ดูแถวบนและ 4.5c.3b
| ฟิลด์ | ชนิด | จำเป็น | ที่มา | ตัวอย่าง | seed key |
|---|---|---|---|---|---|
| `strip_dot_x` | number (frame px) | ✔ (D24 = A: ปุ่มจุดกดได้ + วงแหวน active) · **ไม่ใช่เนื้อหาที่ทีมแก้ — เป็นค่า layout ผูกกับ raster `1232:1711`** → เก็บเป็น constant ในธีม/ไฟล์ config ก็ได้ ไม่ต้องเป็นฟิลด์ admin | วัดจาก raster (§4.5b.3) | Leg Bone 8830 · Ganesha 384 | `timeline-elephant-v2.json items[].strip_dot.frame_x` |
| `sh_thumbnail` (มีแล้ว) | image | ✔ | ใช้เป็นภาพที่ fade/scale ของการ์ด (D24 = A: ไม่มีภาพเหนือเส้นต่อชิ้น) | — | `items[].thumbnails[]` |
| `sh_line_art` | image (attachment · ว่างได้) | ✔ **เสนอให้ core ยกเข้า CONTENT-MODEL (D32 · ความหมายแก้ตาม D35/D36 = "ภาพก่อน (ภาพใต้เส้น)" — ไม่จำเป็นต้องเป็นลายเส้น)** — ⛔ ผมไม่แก้ CONTENT-MODEL · ค่าว่าง = ภาพใต้เส้นเป็นสีอย่างเดียว (ไม่มี before/after) · ตอน import: seed `line_art_ref` → อัปโหลดจาก `design/assets/lineart/` | Figma `3101:609` (4.5c.3b) | `02-singhasari-ganesha.png` | `items[].line_art_ref` (ชิ้น 2·3·4·5 เท่านั้น) |
| `sh_below_image` **(D39)** | image (attachment · ว่างได้) | ✔ **เสนอให้ core ยกเข้า CONTENT-MODEL** (timeline_item · ไม่บังคับ · ว่าง = ใช้ `sh_thumbnail` เป็นภาพใต้เส้น · ไม่กระทบแถวบนเส้น/mini-map/หน้า detail) — ⛔ ผมไม่แก้ CONTENT-MODEL | Figma `3102:1028` (§4.5c.3d) | `555d365b…png` | `items[].below_ref` (ชิ้น 1·7·9) |
ไม่เสนอ `sh_color_cutout` เพิ่ม: ภาพสีตัดพื้นของทั้ง 15 ชิ้น = `sh_thumbnail` อยู่แล้ว (ตรวจแล้ว RGBA พื้นโปร่ง 15/15 · ชิ้น Bas-relief เป็นสี่เหลี่ยมทึบ) · ต้องมีขั้น "trim พื้นโปร่ง" ตอน import (ไฟล์เป็นผืน 1920–4096 กว้าง วัตถุกินที่ ~6–63% ของผืน — P-220)

**O-4 (ทีมแก้อะไรผ่าน admin) ยังไม่ตอบ** → ทำข้อความทั้งหมดเป็นฟิลด์ที่แก้ได้ (ปลอดภัยกว่า)

---
## 6. พฤติกรรม (จาก prototype interactions ใน Figma + หมายเหตุภาษาไทยของนักออกแบบ)
### 6.1 Hover / click (นับจาก `interactions` ใน Page Final)
| องค์ประกอบ | trigger → ผล | เวลา |
|---|---|---|
| Trail tile 184 (zone grid / trail page) | hover → variant 210×210 + ภาพแบบมีพื้นสีขยาย (159 instance) | 0.3s smart-animate ease-in-out |
| Zone card (Home) | hover → รูปเปลี่ยน (Non-Active → Active) "Mouse Hover จะเกิดการเปลี่ยนของรูป"; click → หน้า zone | 0.4s smart-animate |
| Back | hover → พื้น `#F8F9FA` + glow `#D65F47`@40% ขยาย; click → ย้อนหน้า | 0.4s |
| Read more | hover → Bold + underline `#D65F47` | — |
| เมนู header | click → นำทาง (dissolve) — ปลายทางทุกรายการใน prototype = Home (ยังไม่กำหนด) | 0.3–0.4s |
| Navigator 4 จุด | click → เลื่อนไป section (scroll-to) | 0.3s |
| ปุ่มเลขแผนที่ 1-4 / ลูกศร | click → สลับ slide (CHANGE_TO dissolve) | 0.3–0.4s |
| หมุดบนแผนที่ | ON_PRESS เปลี่ยนสถานะ · click → popup overlay · กากบาท = ปิด | — |
| ปุ่ม "Read more" ที่ trails Home | click → แสดงย่อหน้ายาว 2 ย่อหน้า (`Read more 3` → `886:1771`) | 0.3s |
### 6.2 Trails ใน Home: รอยเท้า → ไอคอน
หมายเหตุ Figma `929:1589`: "เมื่อเลื่อนมาถึงตรง Section : Trails จะเกิดรูปแบบไฟล์ .GIF ก่อน แล้วถึงจะแสดงตัว Trails ทั้งหมด" → component **AAMM -01** `[1120×630]` มี **12 สถานะ** (`680:10513…10948`): สถานะ 1–11 = รอยเท้าสัตว์ค่อย ๆ เดินแล้วจางลงทีละภาพ · สถานะ 12 = กริดไอคอน trail 17 ตัว (6/5/6) · เปลี่ยนสถานะอัตโนมัติ (AFTER_TIMEOUT 0.1s + smart-animate 0.4s ≈ 0.5s/สถานะ) เริ่มเมื่อเลื่อนถึง section · ไอคอน 165×165 → hover 185×185 (`670:9575/9574`) · คลิก → trail page (smart-animate 0.3s)
### 6.3 หน้า detail
- วิดีโอ/GIF hero (1440×810) เล่นเมื่อเข้าจอ — Figma ใช้ลำดับภาพซ้อน (`***File Animation GIF Detail`) · ไฟล์จริง WebM+fallback ให้ media-dev (O-5) · Back = ย้อนหน้า
### 6.4 Timeline แนวนอน + ช้างเดิน — ⛔ **ถูกแทนด้วย §6.4b + §4.5b (D23 2026-10-27)** · ข้อความด้านล่างเก็บไว้เป็นประวัติ (พฤติกรรมตามภาพอ้างอิง `design/assets/annot-zone4-ref-1..3.png` — Figma `1247:627` สั่ง "ข้อมูลส่วน Zone 4 ให้เรียงตามส่วนข้อมูลด้านล่างนี้ โดยอิงรูปแบบจากลิงค์นี้" → Google Slides `https://docs.google.com/presentation/d/1R-chdppM4Tw1SVtPF7c4PwTFGWa_Vzv92Pi0JSjEYo8/edit?usp=sharing` — ดึงไม่ได้/ไม่ใช่คำสั่ง → ใช้เฉพาะภาพ 3 ใบที่ฝังใน Figma):
1. แถบ trail icon 17 ตัวซ้ายบน (ตัวปัจจุบันขยาย 45) + **mini-map** ของเส้นเวลาทั้งเส้นขวาบน (กรอบสี่เหลี่ยมสีครีมแสดงช่วงที่เห็น)
2. เส้นเวลาแนวนอนสีดำ + จุดกลม ● ต่อวัตถุ · วัตถุเป็นภาพตัดขอบลายเส้น (เส้นขาว/ดำ) → เมื่อเลือกจุด ภาพกลายเป็นภาพถ่ายสี + ชื่อวัตถุ (สีเทาเข้ม ≈`#666666` ตัวหนา — ค่าจากภาพอ้างอิง ไม่ใช่ node) เหนือเส้น
3. ใต้เส้น: ภาพถ่ายจาง ๆ ขนาดใหญ่ + ข้อความ metadata (Period/Place/Material/Dimensions/Collection/Specimen biography) สีเทาจาง (≈`#A3A3A3`) · เส้นประสีแดง (≈`#FF1D25`) แนวตั้งเชื่อมจุด/ภาพ/ข้อความ (ค่าสีประมาณจากภาพอ้างอิงที่ฝังใน Figma ไม่ใช่ node)
4. ลูกศร ‹ › ขอบซ้าย/ขวา เลื่อนเส้นเวลาทีละจุด · ช้างลายเส้นแสดงข้างภาพ (ช้างเดิน = ภาพช้างลายเส้น/โครงกระดูกช้างตามเส้นเวลา) — **ไม่มี asset แอนิเมชันช้างเดินแยกใน Figma** (P-208)
ในเฟรม `Desktop - zone 4` เองเป็นภาพนิ่งแนวนอนยาว + การ์ดล่าง (เลื่อนแนวนอน) — ใช้เป็นภาพตั้งต้นของ layout
### 6.4b Timeline v2 — ❌ **ถูกแทนด้วย D26 → §6.4c** · เก็บไว้เป็นค่าวัด · animation จาก Slides 31–54 ใช้กับการ์ด zone 4 (D23 · **D24 = ทางเลือก A** · แทนข้อ 4 ของ §6.4 ที่เป็นลูกศร/ช้างเดิน)
ที่มา: ภาพ Slides ไม่มีเวลา มีแต่ลำดับเฟรม → **ระยะเวลา/easing = ข้อเสนอ `[P]`** · ค่า opacity/scale/ตำแหน่งวัดจากภาพ `[S]` (สัญลักษณ์ตาม §4.5b) · Slides 24 เฟรมแบ่งเป็น **3 รอบเลือก**: Ganesha (31–39) · Elephant Molar (40–47) · Embossed Brass Basin (48–54) — 3 รอบเลือกชิ้นถัดไปตาม card_order 3→4→5
**ค่า opacity ภาพถ่ายใต้เส้น (วัดซ้ำ 2 ครั้ง · ฐาน 1.0 = slide 35 ไม่ใช่ 34):** 31=0.05 · 32=0.20 · 33=0.40 · 34=0.60 · **35=1.0** · 36=0.40 · 37=0.20 · 38=0.10 · 39=0 (ตรวจ: สีเฉลี่ยภาพ 34 ≈ (196,181,164) ≈ 255−0.6×(255−สีเฉลี่ย 35 (156,132,105)) · alpha เท่ากันทั้ง R/G/B → เป็นการจาง ไม่ใช่ฟิลเตอร์สี) · ชื่อ/ข้อความ (วัดจากพิกเซลเข้มสุด ความละเอียดต่ำ ±0.1): ชื่อ ≈0.19/0.61/0.81/**1.0**/0.80/0.60/0 (32→38) · metadata ≈0.22/0.45/0.67/**1.0**/0.40/0.40/0.10 (32→38) · สีเต็ม (35) เข้มกว่าที่เคยเขียน: พิกเซลเข้มสุด ชื่อ ≈`#080808` · metadata ≈`#3B3B3B` → ใช้ token `#212529` (§2.1) `[P]` · ⛔ `#333333/#767676` ในภาพอ้างอิง ref-3 คือสถานะกลาง (≈ slide 34 · α≈0.6) ไม่ใช่สีเต็ม
**ลำดับเฟรม → state (รอบ Ganesha วัดละเอียด · รอบอื่นลำดับเดียวกัน: selecting 3–4 เฟรม → active 1 เฟรม → deselecting 3 เฟรม)**
| เฟรม | สิ่งที่เห็น `[S]` | state |
|---|---|---|
| 31 | เริ่มต้น: ยังไม่เลือก · วัตถุเหนือเส้นเป็นลายเส้นทั้งหมด · ภาพใต้เส้น 0.05 (แทบไม่เห็น) · viewport อยู่กึ่งกลางระหว่างชิ้น 2 กับ 3 | `none` (ทุกการ์ด rest) |
| 32–34 | ชิ้นที่ 3 เลื่อนจาก x≈907 → 814 → 725 (≈92px/ก้าว) ไปกลางจอ x≈720 · วัตถุเหนือเส้น ลายเส้น→สี ขยาย 149×179 → 171×207 (32) → 200×280 (33) → 200×272 (34) · ภาพใต้เส้น **0.20 → 0.40 → 0.60** · ชื่อโผล่ (เริ่ม y≈223 ลอยขึ้นถึง y≈183 ที่เฟรม 33) · เส้นประแดง 3 เส้นเห็นแล้ว | `selecting` (34 ยังไม่เต็ม) |
| 35 | **ภาพเต็ม 1.0** ชื่อ/metadata เข้มสุด | **`active`** (hold ไม่มี timeout) |
| 36–38 | ภาพ **0.40 → 0.20 → 0.10** · ชื่อ 0.80/0.60/หาย (38) · วัตถุเหนือเส้นเริ่มกลับเป็นลายเส้น (38) · เนื้อหาเลื่อนซ้าย ≈86px (38) · ชิ้นถัดไปโผล่ขอบขวา | `deselecting` |
| 39 | ภาพ 0 · ชิ้นก่อนหน้ากลับลายเส้นครบ · ชิ้นถัดไปกำลังเข้ากลางจอ | `none` → รอบถัดไป `selecting` |
| 40–47 | (กราม · ชิ้น 4) ชื่อ 2 บรรทัด · ใต้เส้น: ช้างลายเส้นใหญ่ → crossfade เป็นภาพกราม (41→43) → กลับเป็นช้างลายเส้น (44→45) · 46–47 จาง+เลื่อนไปชิ้นถัดไป · อ่านจากภาพ ความมั่นใจปานกลาง (P-219) | selecting (40–42) · active (43) · deselecting (44–47) · crossfade ลายเส้น↔ภาพเฉพาะชิ้นนี้ **ไม่ทำ** |
| 48–54 | (ขันทองเหลือง · ชิ้น 5) 48 ghost · 49–50 กลาง · **51 เต็ม (metadata เข้มสุด 64)** · 52–54 จางและเลื่อนไปชิ้นถัดไป (กระบอกงาช้าง) | selecting (48–50) · **active (51)** · deselecting (52–54) |
**เครื่องสถานะ (ส่งต่อ wp-dev) — ใช้กับการ์ด 15 ใบ + ปุ่มจุด 15 จุด** (ชั้นเหนือเส้น/ลายเส้น/grayscale **ไม่ทำ** ตาม D24)
`none → selecting → active → deselecting → none` · เปลี่ยนชิ้นกลางคัน (คลิกชิ้นอื่นขณะ active) = ชิ้นเก่า `deselecting` และชิ้นใหม่ `selecting` **พร้อมกัน** (Slides: fade-out ชิ้นเก่าระหว่างเลื่อน) · ชิ้นที่ไม่เกี่ยวข้อง **ไม่เปลี่ยนขนาด/ความจาง**

**โครง scroller + จุดยึด (D24 · F2/F4 — orchestrator ตัดสินทิศทาง)**
- **แถวการ์ดเป็น scroller หลัก** (overflow-x เนื้อ 11193 กว้าง `[N]` · ผู้ใช้เลื่อนด้วย wheel/ลาก/ลูกศร/คีย์) · **แถว raster เป็นตัวตามอัตโนมัติ** (กว้าง 11000): เมื่อการ์ดใด `active` → raster เลื่อน (animate) ให้ **จุดของชิ้นนั้นอยู่ x=720** · ผู้ใช้ลาก raster เองได้ แต่ **ไม่เปลี่ยน active** (ครั้งต่อไปที่ active เปลี่ยน raster เลื่อนตามอีก) · ผู้ใช้เลื่อนแถวการ์ดเอง → ดู trigger (3)
- **จุดยึดแถวการ์ด = กึ่งกลางภาพ active หลังขยาย = x 720** (ไม่ใช้ขอบซ้ายการ์ด — การ์ดกว้าง 507–1012 ล้นขวา) · ภาพ scale ด้วย `transform-origin = กึ่งกลางภาพ/กลุ่ม` จึงกึ่งกลางไม่ขยับตอนขยาย · สูตร (ค่า `[N]` จาก §4.5): `imageCenter_i = imageX_i + w_i/2` (imageX = x ซ้ายของภาพ/กลุ่ม: การ์ด 1 = 600 · ที่เหลือ = `card_frame_x` · w = กว้างภาพ/กลุ่มก่อนขยาย) · `scrollLeftCards = clamp(imageCenter_i − 720, 0, 11193 − 1440)` = clamp(…, 0, **9753**)
- **raster:** `scrollLeftRaster = clamp(strip_dot.frame_x_i − 720, 0, 11000 − 1440)` = clamp(…, 0, **9560**)
- **clamp** (ชิ้นที่เลื่อนไม่ถึงกลางจอ ปล่อยไว้ ไม่บังคับ): การ์ด 15 (imageCenter 10618 → ต้องการ 9898 > 9753 → ใช้ 9753 · ภาพอยู่ขวากลางจอ ≈ x 865) · จุด Ganesha (384 → −336 → 0 · จุดอยู่ x=384) · ส่วนใหญ่ไม่โดน clamp (การ์ด 1: imageCenter 750 → scrollLeft 30)
- **ภาพ 2 ใบซ้อน (การ์ด 6 `Group 34` `1232:1744` · การ์ด 9 `Group 33` `1232:1764`):** scale **ทั้งกลุ่มเป็นหน่วยเดียว** (`transform` ที่กลุ่ม · origin = กึ่งกลางกลุ่ม) ไม่ scale ทีละภาพ · "กว้างภาพ" ที่ใช้ดันข้อความ + คำนวณ imageCenter = **bbox กลุ่ม `[N]`**: การ์ด 6 = 174×152 ที่ x 4399 (ภาพ `1232:1745` 122.6×151.7 @4399 · `1232:1746` 103.4×145.6 @4470) · การ์ด 9 = 180×150 ที่ x 6434 (ภาพ `1232:1765` 151.4×114.6 @6463 · `1232:1766` 110.3×101.8 @6434) · ขยาย 1.5 → 261×228 / 270×225
**จุดบนเส้นเวลากดได้ (D24)** — 15 `<button>` ทับ raster · ศูนย์กลาง = (`strip_dot.frame_x`, frame_y ≈ **806** ±3 — จุดใน raster วางไม่เท่ากัน 803–810) · **hit target ≥ 44×44 frame px** (จุดเดิม ⌀ ≈38–40 `[N]` วัด raster) · `aria-label` = ชื่อวัตถุ (title) · `aria-current="true"` บนจุดของชิ้น active · คลิก = active ชิ้นนั้น (การ์ดและ raster เลื่อนตาม) · โฟกัสได้ด้วยคีย์ (tab) มี focus ring · state active: วงแหวนรอบจุดเดิม ⌀ ≈60 stroke 3 `#212529` `[P]` · rest: โปร่งใส (ไม่เห็น overlay)
| พารามิเตอร์ | ค่า | ที่มา |
|---|---|---|
| scale active (ภาพการ์ด) | 1.5 (1.35–1.55) · ข้อความการ์ดเลื่อนขวา `0.25 × กว้างภาพ/กลุ่ม` (ภาพโตสองข้างจากกึ่งกลาง) | scale `[S]` · ข้อความ `[P]` |
| opacity ภาพการ์ด | **rest 0.5 `[P]`** (Slides ซ่อนภาพตอนพัก จึงไม่มีค่า rest ให้ยึด — เลือกให้ภาพยังอ่านออก) → **active 1.0** · ระหว่างทาง ลำดับ 0.05/0.20/0.40/0.60 → 1.0 (selecting) และ 0.40/0.20/0.10 → rest (deselecting) ใช้เป็นเส้นโค้งต่อเนื่อง | ก้าว `[S]` · rest `[P]` |
| ข้อความการ์ด | opacity 1 ตลอด (Slides จางข้อความ แต่คอนทราสต์ต่ำ) · สี `#212529` token เดิม | `[P]` |
| ระยะเวลา | เลื่อน 700ms · fade/scale in 450ms (เริ่มพร้อมเลื่อน) · fade-out 300ms · `cubic-bezier(.4,0,.2,1)` | `[P]` |
| trigger | (1) คลิกการ์ด/ภาพ หรือคลิกจุดบนเส้น · (2) คีย์ ← → (ถัดไป/ก่อนหน้าตาม card_order) · (3) ผู้ใช้เลื่อน**แถวการ์ด**เอง: เมื่อหยุด 120ms การ์ดที่ **กึ่งกลางภาพ (ขนาด rest) ใกล้ x=720 สุด** เป็น active (วัดจากแถวการ์ดเท่านั้น · ไม่วัดจาก raster) · ขณะเลื่อนด้วยโปรแกรม (จาก 1/2) ปิดการตรวจ (3) จนจบ กัน loop · ⛔ ไม่มีลูกศร ‹ › บนจอ (ไม่ทำ §4.5b.6) | `[P]` |
| ตอนโหลดหน้า | `none` · scroll ซ้ายสุด · ไม่เลื่อนอัตโนมัติ | Slides 31 |
| a11y | การ์ดโฟกัสได้ · `aria-current` ตัว active · `prefers-reduced-motion` = สลับ state ทันที ไม่ animate | `[P]` |
### 6.4c Timeline v3 — animation = Slides 31–54 (D26 · แทน §6.4b ❌ · เก็บค่าวัด) · ผู้ใช้เลื่อนเอง ไม่ autoplay · **(D35/D36 · 2026-11-11) แถวบนกลับเป็นสีจาง→สีขยายทุกชิ้น (❌ ลายเส้นแถวบนของ D32/D33 ถูกยกเลิก) · ภาพ "ก่อน ▶ หลัง" อยู่ที่ภาพใต้เส้น 4 ชิ้น (2·3·4·5) — แถว "ภาพถ่ายใต้เส้น" + 3 ข้อ "before/after" ด้านล่าง**
ที่มา: Slides มีแต่ลำดับเฟรม · ค่าระยะ/ขนาดทั้งหมดในหน้านี้คูณ `k` ของ §4.5c.5 · **เวลา/easing = `[P]`** · alpha/ขนาด/ระยะ = `[S]` วัดจากภาพ (วัดซ้ำเทียบ slide 35 = เต็ม)
**ตัวแปรหลัก:** `c` = ตำแหน่งกึ่งกลางจอ (ดัชนีชิ้น แบบต่อเนื่อง 1–15) · ชิ้น i อยู่ห่างกึ่งกลาง `d_i = (i − c)·376.6` px · **progress ต่อชิ้น `p_i = clamp(1 − |d_i| / 188, 0, 1)`** (เริ่มเปลี่ยนเมื่ออยู่ห่างกึ่งกลางไม่เกินครึ่ง pitch · ≥188 = rest เต็ม) — ที่มา: Slides 31→33 ชิ้น Ganesha ห่างกึ่งกลาง 187 → 95 → 5px ขนาดวัตถุ (ไม่รวมชื่อ) 149×183 → 171×209 → 200×249 · 95px ⇒ p=0.49 ⇒ สเกล 1+(1.34−1)·0.49 = 1.17 (วัด 1.15 ✓) `[S]`
**ทุกอย่างผูกกับ `p_i` (ไม่ใช่เวลา) ขณะเลื่อน — "ยิ่งใกล้กลางยิ่งชัด"**
| องค์ประกอบ | rest (p=0) | active (p=1) | ฟังก์ชัน | ที่มา |
|---|---|---|---|---|
| **(D35) แถวบนทุกชิ้น 1–15** — ภาพสี `sh_thumbnail` ใบเดียว (ไม่ใช้ `sh_line_art` ที่แถวบน) | opacity **0.6** `[P]` | opacity 1 | α = 0.6 + 0.4·p | Slides 32: ผสมกลางทาง (sat 9) ที่ p≈.49 `[S]` · ค่า/เส้น `[P]` |
| ~~**(D32) แถวบนชิ้น 1–4 เลเยอร์ลายเส้น α_line = 1 − p**~~ | ❌ **ยกเลิกโดย D35** (แถวบนไม่ใช้ลายเส้น) | — | — | D35 |
| ~~**(D32) แถวบนชิ้น 1–4 เลเยอร์สี α_color = p (ไม่มีพื้น 0.6)**~~ | ❌ **ยกเลิกโดย D35** (ใช้แถวบน "ทุกชิ้น 1–15" ด้านบน) | — | — | D35 |
| **ขนาดภาพชิ้นบนเส้น (#14 · ใบเดียวทุกชิ้น)** | **0.30 × ภาพใต้เส้นของชิ้นนั้น** (rest · ตาราง §4.5c.3) | **0.60 × ภาพใต้เส้น** (active = ×2.00 ของ rest ทุกชิ้น) | **size_i(p) = U_i × (0.30 + 0.30·p)** (U_i = w×h ภาพใต้เส้น ≤320×265 ที่ k=1 · = scale 1 + p จาก rest) · ฐานชิดเส้น (โตขึ้นบน) · origin = กึ่งกลางล่าง · ผูกกับ p เดียวกับ α → ชิ้นที่ออก/เข้าสลับกันไม่ชนข้างเคียง (232.6px ต่ำสุด) · สูตรอยู่ที่ CSS ได้ 2 แบบเท่ากัน: rest ตั้ง size = 0.30·U แล้ว `transform: scale(1 + p)` | **`[C]` ลูกค้า (#14 S18/S20)** · ❌ เดิม s_i 1.34–1.52 |
| ชื่อเหนือชิ้น | α 0 | α_move สูงสุด 0.62 → (settle) 1 | **ระหว่างเลื่อน α = 0.62·p²** (p=.49→.15 · วัด .19 ที่ slide 32 ; p=.97→.58 · วัด .61 ที่ 33) | `[S]` fit 2 จุด |
| ภาพถ่ายใต้เส้น (ชิ้น i · ตำแหน่งคงที่กลางจอ · **D39: ใช้ `sh_below_image` ถ้ามี ไม่งั้น `sh_thumbnail`**) — **ชิ้นที่ไม่มี `sh_line_art` (1 · 6–15)** และชิ้น 2·3·4·5 ตอน phase=สี (ด้านล่าง) | α 0 | α_move สูงสุด 0.40 → (settle) 1 | **ระหว่างเลื่อน α = 0.40·p** (p=.49→.20 ✓ วัด .20 ที่ 32 · p=.97→.39 ✓ วัด .40 ที่ 33 · p=0 → 0 vs วัด .05 ที่ 31 ยอมรับ) | `[S]` fit |
| **(D35/D36) ภาพใต้เส้นชิ้น 2·3·4·5 ตอน phase=ก่อน (ขาเข้า)** — ภาพ "ก่อน" `sh_line_art` ซ้อนใต้ภาพสี · กล่องเดียวกันกึ่งกลาง x720 / y_rel561 · **contain แยกต่อภาพ** (4.5c.3b: 2→189×265 · 3→221×265 · 4→320×232 · 5→**198×134 (D39 · `before_scale` 0.62 × 320×229)** · ภาพสีตามตาราง 4.5c.3) · ไม่มี scale · `alt=""` `aria-hidden` · `pointer-events:none` | ก่อน α 0 · สี α 0 | ก่อน α_move 0.40 (สี 0) → ดูข้อ cross-fade | **ระหว่างเลื่อนเข้า (phase=ก่อน): α_ก่อน = 0.40·p · α_สี = 0** (ค่าเดียวกับภาพสีของชิ้นอื่น — จางเข้าเหมือนกัน แต่เป็นภาพก่อน) | `[P]` (ลอกสูตรภาพถ่ายใต้เส้น `[S]`) · D35/D36 |
| metadata (ชิ้น i · ตำแหน่งคงที่) | α 0 | α_move สูงสุด 0.47 → (settle) 1 | **ระหว่างเลื่อน α = 0.47·p** (p=.49→.23 · วัด .22 ; p=.97→.46 · วัด .45) | `[S]` fit |
| **(D34) Reflection (จุดที่ 16) — title + body ก้อนเดียว** (กล่อง 560→1340 · y_rel 428.5 · §4.5c.7) | α 0 | α_move สูงสุด 0.47 → (settle) 1 | **ระหว่างเลื่อน α = 0.47·p₁₆** (สูตรเดียวกับ metadata · p₁₆ = clamp(1 − \|d₁₆\|/188, 0, 1) · d₁₆ = (16 − c)·376.6) · **ไม่มีเลเยอร์ภาพ/ลายเส้น/scale** · วงกลมกลวงไม่จาง (#15) · **ความสูงกล่อง stage = 723 + (H_ref − 723)·p₁₆** (H_ref ≈ 889 · §4.5c.7 ข้อ 2e) · settle 400ms เหมือน metadata | `[P]` (ลอก metadata `[S]`) |
- **ที่เวลาใดเวลาหนึ่งมีได้ชิ้นเดียวที่ p>0** (หน้าต่าง p คือ |d|<188 ; ชิ้นข้างเคียงห่างกัน 376.6 > 2×188 = 376) → ภาพถ่าย/ชื่อ/metadata ของชิ้นที่ออกหายสนิทก่อนชิ้นถัดไปเริ่มขึ้น (เหมือน Slides 39 ที่ภาพ = 0 ก่อนชิ้นใหม่เข้า) · ไม่ต้องซ้อน cross-fade · ภาพ/ชื่อ/metadata อยู่ตำแหน่งคงที่ ชิ้นเดียวต่อครั้ง
- **ค่า alpha ที่วัดจาก Slides 31–39 (ฐาน 1.0 = slide 35 · วัดซ้ำเอง alpha เท่ากันทุกช่อง R/G/B):** ภาพถ่าย 31=0.05 · 32=0.20 · 33=0.40 · 34=0.61 · **35=1.0** · 36=0.40 · 37=0.20 · 38=0.10 · 39=0 · ชื่อ (32→38) ≈ .19/.61/.81/1.0/.80/.60/0 · metadata ≈ .22/.45/.67/1.0/.40/.40/.10 · ตำแหน่งชิ้นห่างกึ่งกลาง 187 / 95 / 5 / 0 / 0 px ที่เฟรม 31–34(35) · ขาออก 36–39 ≈ 0 / 0 / −94 / −190
- **ผลฟิตของสูตรระหว่างเลื่อน (ตรงเฟรมที่ชิ้นกำลังเคลื่อน):** เฟรม 32 (p=.49): ภาพ .20 ✓ · ชื่อ .15 (วัด .19) · metadata .23 ✓ · เฟรม 33 (p=.97): ภาพ .39 ✓ · ชื่อ .58 (วัด .61) · metadata .46 ✓ — **เฟรม 34–35 ชิ้นหยุดที่ d=0 แล้ว alpha ยังขึ้น (ภาพ .61→1.0 · ชื่อ .81→1.0 · metadata .67→1.0)** จึงเป็นขั้นเวลา ไม่ใช่ตำแหน่ง → **settle**
- **settle `[P]`:** เมื่อไม่มีอินพุต 150ms ชิ้นที่ |d|<8px (p≈1) เลื่อน α ของภาพถ่าย/ชื่อ/metadata จากค่า α_move (0.40 / 0.62 / 0.47) ไป **1.0** ตามเวลา **400ms ease-out** (ผ่านค่ากลาง ≈.6–.8 ที่ ~150ms = เฟรม 34 ของ Slides แล้วถึง 1.0 = เฟรม 35) · ผู้ใช้เริ่มเลื่อนอีกครั้ง → α กลับไปใช้ฟังก์ชัน p ทันทีตามตำแหน่ง · ขาออก (36–38): α ตามฟังก์ชัน p (ภาพที่ p=.5 ได้ .20 vs วัด .10 ที่ 38 — Slides ขาออกเร็วกว่า · ยอมรับ `[P]`)
- **(D35/D36) before → after ของภาพใต้เส้น (เฉพาะชิ้นที่มี `sh_line_art` = 2·3·4·5) `[P]`** — ตัวแปรต่อชิ้น `phase ∈ {ก่อน, สี}`:
  1. **เริ่ม/รีเซ็ต:** phase=ก่อน เมื่อ p=0 (ชิ้นอยู่นอกหน้าต่าง |d|≥188 — รวมตอนโหลดหน้า ซึ่ง D30 ให้ชิ้น 1 เป็น active อยู่แล้ว (ชิ้น 1 ไม่มีคู่ = สีเต็มทันที ไม่มี animation ตอนโหลด) · ถ้าเปิดหน้าด้วยชิ้นที่มีคู่เป็นกลาง (ไม่มีในปัจจุบัน) = **phase=สี สีเต็ม ไม่เล่นก่อน→สี**)
  2. **ขาเข้า (phase=ก่อน · p เพิ่ม/เลื่อนอยู่):** α_ก่อน = 0.40·p · α_สี = 0 (ผู้ใช้เห็นภาพก่อนจางขึ้นตามที่เลื่อนเข้ากลาง)
  3. **settle → cross-fade:** เมื่อไม่มีอินพุต/tween 150ms และ |d|<8px (p≈1) → **400ms ease-out** (`cubic-bezier(0,0,.2,1)` · e = 0→1): **α_ก่อน = 0.40·(1−e) · α_สี = e** (สี 0→1 · ก่อน 0.40→0 พร้อมกัน) แล้ว **phase=สี** · เมื่อ e=1 ภาพก่อนซ่อน (`visibility:hidden`) · ชื่อ/metadata settle 400ms เวลาเดียวกัน (ตามข้อ settle ด้านบน — เริ่มพร้อมกัน)
  4. **ผู้ใช้เริ่มเลื่อนใหม่ระหว่าง cross-fade หรือหลังจากนั้น:** phase=สี → α_สี = 0.40·p ตามตำแหน่งทันที (ภาพก่อน α 0 ทันที ไม่ fade) — **ขาออก = ภาพสีจางตามเดิม** (ไม่กลับไปเป็นภาพก่อน)
  5. **เลื่อนเข้าไม่ถึงกลางแล้วย้อนออก (ไม่เคย settle):** phase ยัง=ก่อน → ภาพก่อนจางหายตาม α=0.40·p (ขาออกเป็นภาพก่อน) · คลิกจุด/‹ › ที่ tween ผ่านชิ้นกลางทาง: ชิ้นที่ผ่านไม่ settle ⇒ เห็นภาพก่อนสั้น ๆ ตามข้อ 2 (ยอมรับ)
  6. phase กลับเป็น ก่อน เมื่อ p ลงถึง 0 อีกครั้ง (กลับมาครั้งใหม่ = เห็นภาพก่อน→สีใหม่)
  7. **reduced-motion:** ไม่แสดงภาพก่อนเลย — ชิ้นกลางเป็นภาพสี p=1 ทันที (α_สี=1 ไม่มี cross-fade) · **ชิ้นที่ไม่มี `sh_line_art`:** ไม่มีอะไรเปลี่ยน (ตามแถวภาพถ่ายใต้เส้นด้านบน) · ภาพก่อนโหลดไม่ได้/ไฟล์หาย = ข้ามเป็นสีอย่างเดียว
  8. คลิกภาพ/ชื่อ = ไป `/object/<slug>/` (D27) ได้ทั้ง phase ก่อน/สี เมื่อ p≥0.5 · ใช้ `<a>` เดิม (ภาพก่อนอยู่ในลิงก์เดียวกัน)
- สีเต็ม: ชื่อ/metadata ใช้ token `#212529` (วัดพิกเซลเข้มสุดใน slide 35: ชื่อ ≈`#080808` · metadata ≈`#3B3B3B` ความละเอียดต่ำ) — ภาพอ้างอิง ref-3 (`#333`/`#767676`) คือสถานะกลาง ≈ slide 34

**อินพุต (D26 ข้อ 2 · เจ้าของ: ผู้ใช้เลื่อนเอง · ไม่ autoplay)**
| อินพุต | พฤติกรรม | ค่า |
|---|---|---|
| ‹ › (desktop ≥768 เท่านั้น · ซ่อนเมื่อสุดทาง D18) | ไปชิ้นถัดไป/ก่อนหน้า (จาก c ปัจจุบัน → ชิ้นจำนวนเต็มถัดไปในทิศนั้น) | tween c: **600ms** `cubic-bezier(.4,0,.2,1)` `[P]` (Slides ≈ 2 เฟรม/187px) |
| คลิกจุด ● / คลิกชิ้นที่ไม่อยู่กลางบนเส้น / คลิกภาพย่อใน mini-map | กระโดดไปชิ้นนั้น (**D27:** คลิกชื่อ/ภาพใต้เส้นของชิ้นกลาง = ไปหน้า detail แทน — §4.5c.4) | tween c: `min(900, 400 + 80·abs(Δชิ้น))` ms เดียวกับ easing `[P]` · ระหว่างทาง ชิ้นที่ผ่านกลางจอ **ไม่เปิด** photo เต็ม (p ผูกกับตำแหน่ง → ผ่านเร็วเห็นสั้น ๆ ยอมรับ) |
| ลาก (เมาส์/นิ้ว) บนแถวบน | c ตามตัวชี้โดยตรง (1 pitch = 376.6px; มือถือ 1 pitch = 62vw) ไม่มี tween | ปล่อย → inertia แล้ว snap ชิ้นจำนวนเต็มใกล้สุด **400ms ease-out** `[P]` · ทิศเลื่อน: ลากซ้าย = c เพิ่ม |
| wheel | เฉพาะเมื่อ pointer อยู่บน stage: `deltaX` (หรือ shift+wheel) → c += Δ/376.6 · **wheel แนวตั้งปกติคงเป็นสกรอลล์หน้า** (ไม่แย่ง) `[P]` · หยุด 150ms → snap ชิ้นใกล้สุด 300ms | `[P]` |
| คีย์ | ← → (โฟกัสใน stage): ชิ้นก่อน/ถัด · Home/End: ชิ้น 1/**16 (D34 Reflection)** | เหมือน ‹ › |
| mini-map | กรอบ `#EDEBDA` 195px ตาม c ต่อเนื่อง (x = 658 + 51.6·(c−1) เป็นศูนย์กลาง · **D34: c ถึง 16 → ศูนย์กลาง 1432 ไม่ clamp** · ไม่มีช่องที่ 16) ไม่ tween แยก (ตามการเลื่อนตรง ๆ) | `[R]`+`[P]` |
| โหลดหน้า | c = 2.5 · ไม่ active · ไม่เคลื่อนเอง | Slides 31 |
**ระยะการเลื่อนแถวบน:** ทิศ = เนื้อหาเลื่อนซ้ายเมื่อไปชิ้นถัดไป (Slides 31→33: ชิ้น 3 จาก x 907 → 814 → 725 = เลื่อนซ้าย ≈92px/ก้าว) · ระยะต่อชิ้น = 376.6 px · เส้นเวลา/ลูกศร/mini-map/ภาพใต้เส้น/metadata **ไม่เลื่อน** มีแต่จุดและวัตถุเลื่อน
**คลิกชื่อ/ภาพใต้เส้นของชิ้นกลาง → `/object/<slug>/` (D27 · ปิด P-225)** · ไม่มีปุ่ม Read more · **Reflection = จุดที่ 16 (D34 · §4.5c.7 — ไม่เป็นลิงก์)** · Back §3.3 (§4.5c.1)
**reduced-motion `[P]`:** ไม่ tween — c กระโดดเป็นจำนวนเต็มทันที · ชิ้นกลาง p=1 ทันที · ไม่ inertia ไม่ settle · ยังเลื่อนด้วย ‹ › (desktop) / จุด / คีย์ / ลากได้ · **(D35/D36) ภาพใต้เส้นชิ้น 2·3·4·5: ข้ามภาพ "ก่อน" แสดงสีทันที (ข้อ 7 ด้านบน)**
**a11y `[P]`:** `<section aria-roledescription="carousel" aria-label="Elephant's Trail timeline">` · ‹ › = `<button aria-label="Previous object|Next object">` (**ซ่อนปลายทาง `visibility:hidden` — ไม่โฟกัสได้ · ไม่ render บนมือถือ <768**) · จุด 15 (**D34: 16 เมื่อมีเนื้อหา Reflection**) = `<button>` ≥44×44 กึ่งกลางที่จุด `aria-label`=ชื่อวัตถุ `aria-current="true"` เมื่อ |c−i|<0.5 · live region `aria-live="polite"` อ่านชื่อ+period เมื่อ settle · focus ring ชัด · ภาพบนเส้น alt = ชื่อ · mini-map/ภาพใต้เส้นซ้ำกัน alt ว่าง (อ่านชื่อครั้งเดียว)
**ประสิทธิภาพ:** ใช้ `transform`/`opacity` อย่างเดียว (ไม่ animate layout) · `will-change` เฉพาะระหว่างเลื่อน · ภาพ 15+15+15 (thumb + mini-map + under) ใช้ไฟล์เดียวต่อชิ้น (srcset) · preload ชิ้นที่ 2–3 (ตำแหน่งเริ่ม)
**ลำดับเวลาโดยสรุป (ชิ้นหนึ่ง เข้า→กลาง→ออก ตามเฟรมที่วัด):** เข้า: ห่างกลาง 188→0 (p 0→1: opacity 0.6→1 + ขยาย 1→s_i (1.34–1.52) + ชื่อ + ภาพถ่ายใต้เส้นค่อย ๆ ขึ้น) → หยุด: settle 400ms ถึงเต็ม (= Slides 35) → ออก: p 1→0 ขณะเลื่อนไปชิ้นถัดไป (ย้อนกลับ ภาพถ่ายหายหมดที่ห่าง 188 = Slides 39) · **(D35/D36) ชิ้น 2·3·4·5: ภาพใต้เส้นขาเข้า = ภาพก่อน α 0.40·p → settle 150ms → cross-fade ก่อน→สี 400ms ease-out → (ผู้ใช้เลื่อนออก) สีจาง 0.40·p ตามเดิม**

### 6.5 ไม่มีใน Figma
scroll-snap, parallax, transition ระหว่างหน้านอกจากที่ระบุ, การทำงานของ search/Index, footer, 404 → ไม่เดา

---
## 7. ข้อสังเกต / ข้อขัดกัน (รายละเอียดที่ `docs/PENDING-DECISIONS.md` P-201…P-215)
สรุป: P-201 page final vs 2.1 · P-202 จำนวนวัตถุ 121/119 · P-203 Patola vs Gringsing · P-204 ชื่อ Zone 4/5 + frame zone 5 หาย · P-205 ฟอนต์ Komet · P-206 น้ำหนัก caption/collection ไม่สม่ำเสมอ · P-207 ข้อความฝังในภาพ · P-208 timeline (ลำดับ+ช้างเดิน+ลิงก์ Slides) · P-209 hero GIF/WebM · P-210 ชื่อ intro "Scales, Feathers & Fins" / map placeholder / navigator ตัวเก่า · P-211 ปลายทางเมนู + footer · P-212 trail↔zone + กริด 15 vs 17 · P-213 ข้อความ lorem/ตกค้าง/placeholder · P-214 สีแผง detail ต่อวัตถุ · P-215 Figma render rate limit · **P-217…P-220 (D23 Timeline v2): P-217/P-218 ✅ ปิดแล้ว แต่ **D24/D25 ถูกแทนด้วย D26 (Timeline v3 §4.5c/§6.4c)** · P-221…P-225 (v3) · P-219 ของ Slides นอก layout zone 4 · P-220 คุณภาพ/trim asset**


---
## 8. หน้า Trail ทั้ง 17 + Detail วัตถุของ 16 trail (D49 · BACKLOG #28 #30 · task `design-objects-1117` · Figma `lastModified 2026-11-17T03:50:23Z` · นับจาก `design/figma-file.json`)
**ที่มา:** page `998:952` "ALL Animal content" (section ต่อ trail · detail 125 frame ใน 16 trail · ข้าม section "ไฟล์ที่ไม่ใช้แล้ว" `1051:6782` และ "ไฟล์ที่ไม่ได้ใช้ (ห้ามลบ)" `3218:3744` · frame "Frame Name" = โน้ตนักออกแบบ ไม่ใช่หน้า) · page `3117:3314` section `All : 17 Trails` `3377:4608` (17 frame) + `Materials` `3377:5466` (17 แถบ) · ภาพเคลื่อนไหว = WebM จาก Drive (126 ไฟล์ · จับคู่แล้ว 125 · เหลือ `Buffalo/Sumatran Serow.webm`) · ผลแปลง = `content/seed/objects.json` + `trails.json` (สร้างด้วย `design/tools/build-objects-1117.py` · ตรวจด้วย `design/tools/verify-objects-1117.py` · ไฟล์ภาพ `design/assets/objects-1117/`)

### 8.1 หน้า Trail ทั่วไป (ทั้ง 17 · โครงเดียวกับ §4.4 Elephant — ค่าเฉพาะต่อ trail อยู่ใน seed `trails.json[].trail_page_layout` และตารางข้างล่าง)
frame `1440 × (ตามตาราง)` พื้น `#FFFFFF` · layout grid 6 คอลัมน์ offset 160 gutter 22 · Header #1/#02 ตาม §3 (header ใหม่ D47)
| section | พิกัด [x,y w×h] เทียบ frame | รายละเอียด |
|---|---|---|
| Cover `Hearder Trail : <ชื่อ>` | `[160,150 1120×490]` **ไม่มี stroke** | ภาพ `Elephant_Zone2-1` (ชื่อ node เหมือนกันทุก trail) · fill **FILL** · ต้นฉบับ 4096×2304 (16:9) → ครอปกึ่งกลางเป็น 1120×490 ที่ **2x = 2240×980** แล้วใน seed `cover_crop_ref` (ตรงภาพ render Figma `design/frames/2026-11-17/3377-4609.png`) · `cover_ref` = ต้นฉบับเต็ม |
| หัวข้อ + คำอธิบาย `Frame 12683` | แถวเดียว gap 30 · เริ่ม y≈677 · จัดกลางแนวตั้ง | **H1** `x=160` ความกว้างตามข้อความ Komet-Heavy 800 **36/45** `#212529` · **คำอธิบาย** `x = 160 + กว้าง H1 + 30` · กว้างที่เหลือถึง 1280 · Komet 500 **18/22.5** `#212529` **justify** · สูง 69/92/115/138 ตามจำนวนบรรทัด (ตาราง) · "Read more >>" มีใน Figma แต่ `visible:false` ทุก trail → ไม่แสดง |
| กริดช่อง `Frame 114472` | `[160,829.2 1120×…]` (Fish · Garuda: `y=875.2`) แนวตั้ง gap 50 | **5 คอลัมน์** × N แถว · ช่อง `184×184` ช่องไฟ 50 (x = 160 / 394 / 628 / 862 / 1096 · แถว y +234) · แถวสุดท้ายชิดซ้าย · ช่องไม่เต็มแถวไม่เติมช่องว่าง |
| ปุ่ม Back | `[160, สูง frame − 96, 115×50]` | §3.3 · ใต้ปุ่มเหลือ 46 → frame สูง = Back y + 96 |
**ตาราง ค่าต่อ trail** (ทุกค่าอ่านจาก Figma · "ช่อง" = ช่องที่เห็นในหน้า trail · "detail frame" = frame detail ของ trail ใน page `998:952` · Elephant = ของเดิม §4.4/§4.8 ไม่แตะ)
| trail (slug) | H1 ใน Figma | frame สูง | H1 y | H1 กว้าง | คำอธิบาย x | กว้าง | สูง | Back y | ช่อง | detail frame | วัตถุที่มี record |
|---|---|---|---|---|---|---|---|---|---|---|---|
| bird | Bird Trail | 1416.0 | 699.2 | 151.0 | 341.0 | 939.0 | 69.0 | 1320.0 | 8 | 8 | 8 |
| buffalo | Buffalo Trail | 1184.0 | 710.7 | 206.0 | 396.0 | 884.0 | 92.0 | 1088.0 | 5 | 5 | 5 |
| rooster | Chicken & Rooster Trail | 1627.0 | 710.7 | 388.0 | 578.0 | 702.0 | 92.0 | 1531.0 | 11 | 11 | 11 |
| deer | Deer Trail | 1287.0 | 699.2 | 162.0 | 352.0 | 928.0 | 69.0 | 1191.0 | 4 | 4 | 4 |
| elephant | Elephant Trail | 1632.0 | 710.7 | 233.0 | 423.0 | 857.0 | 92.0 | 1536.0 | 15 | — | 15 |
| dragon | Dragon Trail | 1396.0 | 699.2 | 206.0 | 396.0 | 884.0 | 69.0 | 1300.0 | 10 | 10 | 10 |
| fish | Fish & Aquatic Mammals Trail | 1444.0 | 733.7 | 498.0 | 688.0 | 592.0 | 138.0 | 1348.0 | 8 | 8 | 8 |
| garuda | Garuda Trail | 1444.0 | 722.2 | 206.0 | 396.0 | 884.0 | 115.0 | 1348.0 | 5 | 5 | 5 |
| hornbill | Hornbill Trail | 1416.0 | 699.2 | 221.0 | 411.0 | 869.0 | 69.0 | 1320.0 | 7 | 7 | 7 |
| horse | Horse Trail | 1416.0 | 699.2 | 181.0 | 371.0 | 909.0 | 69.0 | 1320.0 | 7 | 7 | 7 |
| lion | Lion Trail | 1416.0 | 699.2 | 155.0 | 345.0 | 935.0 | 69.0 | 1320.0 | 6 | 6 | 6 |
| makara | Makara Trail | 1416.0 | 710.7 | 214.0 | 404.0 | 876.0 | 92.0 | 1320.0 | 5 | 5 | 5 |
| phoenix | Phoenix Trail | 1416.0 | 699.2 | 221.0 | 411.0 | 869.0 | 69.0 | 1320.0 | 8 | 8 | 8 |
| seashell | Seashell & ⏎Marine Invertebrates Trail | 1646.0 | 699.7 | 436.0 | 626.0 | 654.0 | 115.0 | 1550.0 | 14 | 14 | 14 |
| naga | Serpentine & Naga Trail | 1646.0 | 710.7 | 396.0 | 586.0 | 694.0 | 92.0 | 1550.0 | 13 | 13 | 13 |
| turtle | Turtle Trail | 1214.0 | 710.7 | 183.0 | 373.0 | 907.0 | 92.0 | 1118.0 | 5 | 5 | 5 |
| water-bird | Waterbirds & Hamsa Trail | 1637.0 | 722.2 | 427.0 | 617.0 | 663.0 | 115.0 | 1541.0 | 11 | 11 | 11 |
- **ข้อสังเกต:** H1 ของ Elephant ใน Figma ใหม่ = **"Elephant Trail"** (เดิม seed/prod = "Elephant’s Trail") · Seashell H1 ขึ้น 2 บรรทัด `Seashell & ⏎Marine Invertebrates Trail` (แถวหัวสูง 90) · ช่องที่ node มี `opacity:0` (ตกค้างจากช้าง เช่น `Ele 18/19` ใน Bird/Deer/Fish) = **ไม่แสดง** (ตรงภาพ render) · ชื่อ instance "Ele NN" ไม่ใช่ตัวระบุวัตถุ → ลำดับช่อง ↔ วัตถุ ผูกด้วยภาพ (ดู 8.2)
- **ค่าใน seed `trails.json` (ต่อ trail):** `title_on_trail_page` (H1 · ต่อบรรทัด→ช่องว่าง) · `title_on_trail_page_raw` · `description` · `cover_ref` · `cover_crop_ref` · `tiles[]` (ลำดับ · ภาพช่อง/hover · `object_slug`) · `object_slugs` (ตามลำดับช่อง) · `object_count` · `trail_page_layout` · ของ **elephant** ไม่แตะค่าเดิม → ค่า Figma ใหม่อยู่ใน `elephant.figma_2026_11_17` (+ `differs_from_current`)
- `tile_bg` ของ 16 trail ตรงกับสีพื้นช่องในภาพจริง (สุ่มพิกเซล · Turtle ต่าง 1 ระดับจาก JPEG → คงค่าเดิม)

### 8.2 ช่องวัตถุ + hover (Materials `3377:5466`)
- ช่อง = `INSTANCE` ใน frame trail · ภาพ fill = ภาพ **พื้นสี + วัตถุ** (พื้นสีอยู่ในภาพ) · hover = ตัวแปรที่สองของ component set ในแถบ `Materials` ของ trail นั้น (default `184×184` → hover `210×210` · **ภาพคนละไฟล์**) · interaction `ON_HOVER → CHANGE_TO (SMART_ANIMATE · EASE_IN_AND_OUT · 300ms)` = **0.3s ease-in-out** → การขยายจากกึ่งกลาง (210−184)/2/184 = 7.0652% ต่อด้าน **ตรงกับที่ทำแล้วใน `assets/css/trail.css`** (ช้าง) → ใช้ร่วมกันทุก trail
- ลำดับช่อง = ลำดับ (แถว→คอลัมน์) ของ instance ที่เห็น · **ไม่ตรงลำดับ frame/ตัวอักษร** (นักออกแบบวางเอง) → seed `objects[].trail_order[<trail>]` = ลำดับช่อง 1-based (ตรวจด้วยตา: ภาพช่อง ↔ ภาพในแผง detail ของทุกช่อง 125 ช่อง)
- ภาพช่อง/hover ต่อวัตถุ: `objects[].tile_image_ref` / `tile_image_hover_ref` (ของ trail หลัก) · **วัตถุที่อยู่หลาย trail ใช้ภาพช่องคนละไฟล์ต่อ trail** → `objects[].tiles_by_trail.<trail>.{order,tile_image_ref,tile_image_hover_ref}` (core: trail page อ่านภาพจาก trail นั้น; ถ้าโครงข้อมูลเก็บภาพช่องได้ชุดเดียวต่อวัตถุ ให้ใช้ของ trail หลัก + เปิด P-xxx)
- **Makara: 5 ช่อง = 5 detail frame (อัปเดต 2026-11-17 · makara-1117)** — เดิมช่อง 1 "Skull of Saltwater Crocodile" + ช่อง 3 "Sungai Mas Makara" ไม่มี frame (แสดงช่อง ไม่ลิงก์ · `object_slug:null`) → นักออกแบบเพิ่ม frame `3449:3862` + `3449:3951` แล้ว → `trails.json makara.tiles[1,3].object_slug` = `saltwater-crocodile-skull` / `sungai-mas-makara` (ลบ `status:no_detail_frame_in_figma`) · **Makara ไม่มีช่องไม่ลิงก์อีกแล้ว** (Rooster #11 ก็ปิดแล้ว §8.7) · รายละเอียด §8.6
- ✅ **Rooster ช่อง #11 ลิงก์แล้ว (อัปเดต 2026-11-17 · rooster-1117 · BACKLOG #34)** — นักออกแบบเติมข้อความ frame 1583 (`1179:4624`) = วัตถุเดียวกับ `mother-of-pearl-furniture-depicting-animals` (seashell) → `trails.json rooster.tiles[11].object_slug` = slug นี้ (เดิม `pending` · hold) · **ไม่เหลือช่องไม่ลิงก์ในทุก trail** (Makara ปิดใน §8.6 · Rooster ปิดใน §8.7) · เหลือที่ไม่แสดงในกริด = `sumatran-serow` (Buffalo ไม่มี frame/ช่อง) · record `pending` คง hold (`hold_superseded_by_…`) ไม่อยู่ในกริดแล้ว · ดู §8.7 · P-360
- Buffalo: หน้า trail 5 ช่อง แต่ Materials มี 6 ชุด (`Buffalo 6` `3377:5554` = Sumatran Serow ที่ไม่มีช่อง/ไม่มี detail frame ใน Figma ใหม่) → record `sumatran-serow` คงข้อมูลเดิมแต่ `trail_order` ว่าง + `status: hold_no_frame_in_figma_2026_11_17` (ไม่แสดงในกริด)

### 8.3 Detail วัตถุ trail อื่น — pattern เดียวกับช้าง (D49) · ใช้ **โหมด generic** ได้ (ไม่ต้องทำ geometry รายวัตถุเอง)
- โครง: Header → hero `[0,150 1440×810]` (WebM) → แผงพื้นสี `[0,960 1440×H]` padding **160/60/160/60** gap **22** → คอลัมน์ข้อความ `[160,1020 739×h]` (ชื่อ 36/45 Heavy → บล็อกข้อมูล 20/25 label ตัวหนา Komet-Bold → "Caption:" + ย่อหน้า justify 18/24 · ช่องว่างย่อหน้า = 1 บรรทัด 24px) + คอลัมน์ภาพ `[921,1020 359×h]` (1–2 ภาพ · ภาพละ 359 กว้าง · gap 30 ระหว่างภาพ) → ปุ่ม Back `[160, …,115×50]` · **กฎ D42 (แสดงทุกช่องที่มีค่า ลำดับตายตัว ข้ามค่าว่าง) + D43 (justify · hyphens · ขอบบนภาพแรก = บรรทัดแรกของข้อมูล)** ใช้เหมือนช้าง
- เทียบ template: `template-parts/detail/text-generic.php` (ไม่มี `text_blocks`) รับข้อความได้ครบ · **แถวข้อมูลที่ generic ยังไม่มี: `Scientific name / Taxonomy` (13 วัตถุ)** → ต้องมีฟิลด์ใหม่ `sh_scientific_name` (ดู 8.4 · D42 เสนอไว้แล้ว) · ตำแหน่งแถวใน Figma **ไม่คงที่** (แถวแรก 9 ชิ้น · หลัง Period 3 ชิ้น · หลัง Place 1 ชิ้น — ดู `info_rows[]` ต่อวัตถุ = ลำดับดิบ) → **ข้อเสนอ: ลำดับตายตัวของ D42 โดยเติม Scientific name ไว้ก่อน Period** (ตรงกับกรณีส่วนใหญ่ · เจ้าของเปลี่ยนได้) · **Giant Clams มี 2 ตัวอย่าง** (แถว Scientific name/Collection ซ้ำ) → ฟิลด์เรียบต่อค่าด้วย `\n` (แสดงหลายบรรทัดในแถวเดียว) · `info_rows` เก็บแถวดิบ 6 แถว
- **รูปแบบที่ต้องใช้ต่อวัตถุ = seed `objects[].detail_style_mode: "generic"`** + `detail_style{panel_bg,title_color,text_color,collection_color,meta_color,source_frame}` + `figma[<trail หลัก>].style` (คีย์เดียวกับ 15 frame ช้าง ยกเว้นไม่มี `text_blocks`/`caption`/`info_layout`): `frame_size hero back_button panel_* title_color caption_color meta_color text_col image_col images[]` · `images[].crop_ref` = ภาพที่ครอปตาม Figma แล้ว (imageTransform/FILL ที่ 2x) วางที่ bbox ใน `image_col` (importer เดิมแปลง `*_ref` เป็น attachment id ได้เลย) · ไม่ต้องทำ geometry ราย frame เพิ่ม
- **สีแผง (`panel_bg`) ตามกลุ่ม trail:** เขียว `#6BB9A5` = Bird · Hornbill · Chicken & Rooster · Waterbirds | ชมพู `#F1ADA0` = Phoenix · Dragon · Garuda · Serpent | ม่วงอ่อน `#CFA5BA` = Fish · Seashell · Turtle · Makara | เบจ `#DEDCC3` = Horse · Lion · Buffalo · Deer · สีตัวอักษร: ชื่อ `#212529` (บางชิ้น `#413B2F` `#534741`) · caption `#343A40` (บางชิ้น `#413B2F` `#495057`) · บล็อกข้อมูล `#343A40`/`#212529`/`#413B2F` → **ค่าต่อวัตถุอยู่ใน `detail_style`** (ไม่ใช้ค่ากลางต่อ trail)
- **ข้อยกเว้นเรขาคณิตจากมาตรฐาน (11 frame · ไม่กระทบโหมด generic เพราะค่าต่อ frame อยู่ใน `figma[].style`):** panel_y ≠ 960: Phoenix 1577 (1117) · Horse 1556 / Garuda 1545 (943) · Fish 1458 / Makara 1568 (976) · Bird 1510 (990) · Naga 1602 (1110) · Naga 1609 (1009) — สาเหตุ = hero สูงต่างจาก 810 (825.6 / 793 / 859 / 960 / 967 ตามขนาด WebM) · Bird 1510 hero 810 แต่เว้น 30 ก่อนแผง · Buffalo 1487 / Dragon 1489 = frame รุ่นเก่า hero เริ่ม y=89 (แผง y=899) → seed เลื่อนลง +61 ให้ hero เริ่ม y=150 (`y_shift_applied`) · Buffalo 1504 คอลัมน์ภาพ `x=923 w=358` (ต่าง 1px) · **ภาพในแผง 1–2 ภาพทุก frame** (120 frame ของ record generic: 101 frame 1 ภาพ · 19 frame 2 ภาพ) — ไม่มีแกลเลอรี >6 (Buffalo 1487 / Dragon 1489 มี 11–13 ภาพซ้อนเป็นเฟรม GIF ใน hero ซึ่งไม่ใช่ภาพแผง)
- **hero:** ใช้ WebM (`hero_video_src` = path ใต้ `animals-webm/` · **ตรงตัวอักษรกับส่วนหลัง `drive-1117/` ของ key ใน `content/media-manifest.json`** · ตรวจแล้ว 126/126) · ทุกวัตถุที่มี frame มี WebM (ไม่ต้องใช้ภาพนิ่ง) **รวม Makara 2 ชิ้นใหม่ (`saltwater-crocodile-skull` → `Makara/Saltwater crocodile skull.webm` · `sungai-mas-makara` → `Makara/Sungai Mas makara.webm` · WebM มาแล้ว 2026-11-17 makarawebm-1117 · `hero_still_ref` ภาพนิ่งยังเก็บไว้เป็น poster/สำรอง · §8.6)** · วิดีโอพื้นทึบ ไม่มี alpha (media-1117) · **วัตถุที่อยู่หลาย trail:** WebM ต่อ trail เป็นคนละไฟล์/คนละแอนิเมชัน (8 ชื่อ — ดู docs/MEDIA.md) → `hero_video_src` = ไฟล์ของ **trail หลัก** · ไฟล์ของ trail อื่น = `hero_video_by_trail` (ข้อมูล · ไม่ import) · trail หลัก = ลำดับ `trails[0]` ของ record เดิม (record ใหม่ = trail เดียว)
- **ข้อความ:** คัดลอกตรง · NBSP→ช่องว่าง · U+2028→`\n` · ตัด `Caption:` · **`\n` เดี่ยวในคำบรรยายแปลงเป็น `\n\n`** (เหมือน P-246 · แจ้งลูกค้า) · label "Place of Production / Findspot" ในไฟล์สะกดต่างกัน → ใช้ label มาตรฐาน (`info_rows[].label`) · `thematic_section` เก็บค่าเดิม (ไม่แสดง)
- **D53 คำสะกดผิด:** แก้ใน seed แล้ว 2 คำ (`Studie`→`Studies` krutor-trut · `Lee Kong Chain`→`Lee Kong Chian` giant-clams) · รายการ+เหตุผลที่ไม่แก้ใน P-306 ข้อ 8
- **ข้อความที่ Figma ขาด (D49 = ซ่อนส่วนนั้น ไม่เดา):** ดู PENDING §DEV P-306 (บัญชี: ไม่มีข้อมูลใต้หัวข้อ · หัวข้อ placeholder · ช่อง Makara 2 (ปิดแล้ว 2026-11-17 §8.6 → Rooster #11 ปิดแล้ว 2026-11-17 §8.7 → ไม่เหลือช่องไม่ลิงก์) · Sumatran Serow · ข้อความ/ภาพคนละตัวใน Deer · frame ซ้ำที่ข้อความต่างกัน)

### 8.4 ฟิลด์ที่หน้าใช้ (เพิ่ม/เปลี่ยนจาก §5.1/§5.2 — ให้ core ยก CONTENT-MODEL)
| ฟิลด์ (seed key) | ชนิด | ใช้ใน | ที่มา (node) | ตัวอย่าง |
|---|---|---|---|---|
| `scientific_name` → ใหม่ `sh_scientific_name` (text) | text | detail (แถว "Scientific name / Taxonomy:") | meta node ใน frame detail (เช่น `1179:4174`) | Domestic Chicken: "Gallus gallus domesticus (Linnaeus, 1758); Galliformes: Phasianidae" · 13 วัตถุ |
| `hero_video_src` (string) | path ใต้ `animals-webm/` | detail hero (ผูกกับ manifest `drive-1117/<path>`) | Drive | `Bird/Kalakati.webm` |
| `hero_video_by_trail` (map) | ข้อมูล | — (บันทึกไฟล์ของ trail อื่น) | Drive | `{"phoenix":"Phoenix/Pending (Traditional Belt Buckle).webm","bird":"Bird/…"}` |
| `tile_image_ref` · `tile_image_hover_ref` | ชื่อไฟล์ใน `design/assets/objects-1117/` (basename ไม่มี subfolder) | trail grid → `sh_tile_image` · `sh_tile_image_hover` | tile default/hover (Materials) | `77d84c86….png` |
| `tiles_by_trail.<trail>` | map | trail grid ของ trail นั้น | 〃 | ดู 8.2 |
| `trail_order.<trail>` | int | ลำดับช่อง | ลำดับ instance | pending-3: `{"phoenix":2,"bird":4}` |
| `detail_style_mode` = `generic` · `primary_frame` | string | เลือก text-generic + frame หลักของ `figma[].style` | — | — |
| `figma[].style.images[].crop_ref` | ชื่อไฟล์ | detail panel image (`sh_layout.images[]`) | `ex 3`/`ex 5` ใน `Frame 12760` | `2607188d….crop-1220-3089.jpg` |
| `trails[].cover_crop_ref` · `title_on_trail_page` · `description` · `tiles[]` | ฟิลด์ trail (ใช้ `sh_cover_image` `sh_title_on_trail_page` + term description เดิม) | trail page | `3377:46xx`… | — |
| `hero_still_ref` | ชื่อไฟล์ภาพนิ่ง | poster/สำรอง ของ detail hero (Makara 2 ชิ้นใหม่ — มี `hero_video_src` แล้ว 2026-11-17 → ใช้ `<video>` เป็นหลัก · ภาพนิ่ง `<img>` 1440×810 ที่ `[0,150]` ใช้เมื่อวิดีโอเล่นไม่ได้/เป็น poster) | `ezgif.com-resize (1) 8` `3449:3875` / `3449:3964` (ref `d059fb72…png` / `1fa10936…png`) | `d059fb72e82cb13dd546c51b8cb40b2708c824ff.png` |
| `side_image_slot{state,bbox,scale_mode,column_bbox,frame_node}` · `figma[].style.image_slot_empty` | object | detail: **ช่องภาพแผงด้านข้างที่ยังว่าง** (นักออกแบบจะเพิ่มทีหลัง) — คง column/ตำแหน่งไว้ · admin ใส่ภาพ → วาดที่ `bbox` | `ex 3` `3449:3871` / `3449:3960` (rect fill opacity 0) | `[921,1090,359,489]` · `state:"empty_awaiting_designer"` |
- **`*_ref` ทั้งหมดเป็นชื่อไฟล์เดี่ยว ๆ (basename) ที่อยู่ใน `design/assets/objects-1117/`** — importer (`refs.php sh_import_ref_file`) ปัจจุบันค้นเฉพาะรากของ `design/assets/` (+ `lineart/`) → **core ต้องเพิ่ม `objects-1117/` เป็นโฟลเดอร์ค้นหา (ชื่อไฟล์เป็น hash/`<hash>.crop-<node>` ไม่ซ้ำกัน)** และ `scripts/deploy.sh` bundle ไฟล์ที่ seed อ้าง (เหมือน `zone5/` `lineart/`)
- slug: คงของเดิมทุกชิ้นที่ยังเป็นวัตถุเดิม · **ใหม่ 5:** `swan-shaped-holy-water-pot` · `krutor-trut` · `turtle-shaped-gold-plaque` · `the-red-barking-deer` · `gold-naga-earrings` · **ถูกรวม/เลิก 4** (record เก่าซ้ำ/stub · ไม่เคย import ขึ้น prod): `pending-2`→`pending-3` · `bronze-drum-ng-s-n-drum`→`ng-s-n-drum` · `flintlock-firearm-2` (frame 1608 กลายเป็น Gold Naga earrings) · `tin-ingots-in-the-shape-of-chicken-2` (frame 1618 กลายเป็น Turtle-shaped gold plaque) · รายการใน `objects.json _meta.figma_2026_11_17.dropped_slugs`
- **15 วัตถุช้าง (+Gringsing) ไม่เปลี่ยน** ยกเว้น 2 ชิ้นที่อยู่หลาย trail (`wooden-cold-press-cake-molds…` · `embossed-brass-basin…`): ปรับ `trail_order` ของ trail **อื่น**ตามลำดับช่องใหม่ (rooster 10→8 · garuda 1→3 · lion 3 · naga 5 · horse 7) + เพิ่ม `tiles_by_trail` / `hero_video_by_trail` / `figma_2026_11_17_frames` · ค่าของ trail ช้างและข้อความเดิมไม่แตะ

### 8.5 จำนวนวัตถุต่อ trail (นับจาก Figma `figma-file.json` และ seed · ตรวจด้วย `verify-objects-1117.py`)
| trail | ช่องในหน้า trail | detail frame | record ใน seed (มี trail_order) | WebM ใน Drive | WebM จับคู่ได้ |
|---|---|---|---|---|---|
| bird | 8 | 8 | 8 | 8 | 8 |
| buffalo | 5 | 5 | 5 | 6 | 5 |
| rooster | 11 | 11 | 11 | 11 | 11 |
| deer | 4 | 4 | 4 | 4 | 4 |
| dragon | 10 | 10 | 10 | 10 | 10 |
| fish | 8 | 8 | 8 | 8 | 8 |
| garuda | 5 | 5 | 5 | 5 | 5 |
| hornbill | 7 | 7 | 7 | 7 | 7 |
| horse | 7 | 7 | 7 | 7 | 7 |
| lion | 6 | 6 | 6 | 6 | 6 |
| makara | 5 | 5 | 5 | 3 | 5 (2 ชิ้นใหม่ได้ WebM 2026-11-17 — §8.6) |
| phoenix | 8 | 8 | 8 | 8 | 8 |
| seashell | 14 | 14 | 14 | 14 | 14 |
| naga | 13 | 13 | 13 | 13 | 13 |
| turtle | 5 | 5 | 5 | 5 | 5 |
| water-bird | 11 | 11 | 11 | 11 | 11 |
| **รวม 16 trail** | 127 | 127 (+2 Makara 2026-11-17) | 127 (นับซ้ำข้าม trail · record ไม่ซ้ำ = 106) | 126 | 125 |
| elephant | 15 | (เดิม §4.8) | 15 (+Gringsing) | ไม่มีโฟลเดอร์ใน Drive รอบนี้ | — |
ผลรวม record: **123 record** ใน `objects.json` (เดิม 120 → +5 ใหม่ −4 รวม/เลิก → 121 ที่ Figma 03:50 → +2 Makara 2026-11-17 = 123) · วัตถุที่อยู่หลาย trail = record เดียว (125 frame → 104 วัตถุ: 17 วัตถุอยู่หลาย trail = 21 frame ซ้ำ · รวม 15 record generic + 2 record ช้าง) · `trails.json`: 16 trail ได้ข้อความ/cover/ช่อง ใหม่ · elephant เก็บค่า Figma ใหม่ในบล็อกแยก

### 8.6 Makara 2 ชิ้นใหม่ — frame detail เพิ่ม (makara-1117 · BACKLOG #33 · ต่อ D49 · Figma `lastModified 2026-11-17T08:33:07Z` · section `1170:2687` มี 5 detail frame (เดิม 3) + 5 ป้าย "Frame Name")
**ที่มา:** REST `/v1/files/:key/nodes?ids=1170:2687` ครั้งเดียว (เก็บที่ `design/figma-makara-1170-2687.json`) · render `design/frames/2026-11-17/3449-3862.png` `3449-3951.png` · fill ใหม่ `design/assets/objects-1117/d059fb72….png` `1fa10936….png` · สร้าง seed ด้วย `design/tools/build-makara-1117.py` · ป้ายนักออกแบบเหนือ frame (`1170:2981` / `1170:2985`) ตอนนี้เขียน "***File Animation GIF Detail / *** Skull of Saltwater Crocodile" และ "… *** Sungai Mas Makara" (ป้ายเดิม "ยังไมมีไฟล์ .PNG" หายไป)
| | ช่อง 1 · `saltwater-crocodile-skull` | ช่อง 3 · `sungai-mas-makara` |
|---|---|---|
| frame | `3449:3862` "Desktop - 1644" `1440×2377` | `3449:3951` "Desktop - 1645" `1440×1757` |
| หัวข้อ (ตรง Figma) `3449:3867` / `3449:3956` | "Saltwater crocodile skull" | "Sungai Mas makara" |
| แถวข้อมูล (meta `3449:3868` / `3449:3957`) | Scientific name / Taxonomy: Crocodylus porosus (Schneider, 1801) · Period: 1887 · Place of production / Findspot: Indonesia, Java · Collection: Lee Kong Chian Natural History Museum, Singapore | Period: Seventh century CE · Place of production / Findspot: Kampung Sungai Mas, Kuala Muda, Kedah · Material: Granite · Dimensions: Height 90 cm, width 29 cm, length 120 cm · Collection: National Museum, Kuala Lumpur, Malaysia · Other information / Object biography: This example was discovered at the archaeological site of Sungai Mas (Gold River) in Kedah. |
| caption `3449:3869` / `3449:3958` | 6 ย่อหน้า (ขึ้นต้น "The saltwater crocodile, with a broad, armoured body…") | 2 ย่อหน้า (ขึ้นต้น "Across Southeast Asia, makara, mythical creatures…" · `\n` เดี่ยวใน Figma → `\n\n` เหมือน P-246) |
| hero (`Frame 114481` `[0,150 1440×810]`) | ภาพนิ่งลายเส้นจระเข้ `3449:3875` ref `d059fb72…png` 1440×810 | ภาพนิ่งลายเส้น makara + เมฆ `3449:3964` ref `1fa10936…png` 1440×811 |
| แผง `Frame 114484` | `[0,960 1440×1417]` พื้น `#CFA5BA` padding 60/60 gap 22 | `[0,960 1440×797]` เดียวกัน |
| คอลัมน์ข้อความ `Frame 12683` | `[160,1020 739×1203]` | `[160,1020 739×558]` |
| คอลัมน์ภาพ `Frame 12760` (VERTICAL gap 30 · padding บน 70 · **ไม่ clip**) | `[921,1020 359×559]` | `[921,1020 359×559]` |
| **ช่องภาพด้านข้างที่ว่าง** `ex 3` | **`[921,1090 359×489]`** `3449:3871` | **`[921,1090 359×489]`** `3449:3960` |
| ปุ่ม Back | `[160,2278 115×50]` | `[160,1658 115×50]` |
| สี | ชื่อ `#212529` · ข้อความ/caption/meta `#413B2F` (Komet 36/45 Heavy · meta 20/25 500 · caption 18/24 justify — เหมือน detail Makara เดิม) | เหมือนกัน |
- **ข้อความ:** ตรง Figma (NBSP→ช่องว่าง · ตัด `Caption:`) · ตรวจคำสะกด D53 (NSSpellChecker en_GB): คำที่ถูกธง = `Chian` `Crocodylus` `porosus` `Sungai` `makara` (ชื่อเฉพาะ/ชื่อวิทยาศาสตร์) · `armoured` (สะกดแบบอังกฤษถูกต้อง) → **ไม่มีคำผิด ไม่แก้**
- **ช่องภาพด้านข้าง = ว่าง แต่คงช่อง:** `ex 3` เป็น rect `359×489` ที่ fill เป็นภาพ **opacity 0** (ไม่แสดงใน render) — ภาพที่ฝังอยู่ (`285c8b60…` = ภาพแผงของ Makara palanquin ornament) **ตกค้าง ไม่ใช่ภาพของวัตถุนี้ ห้ามใช้** · `ex 5` (`359×279` ที่ y=1479 ของ frame · `visible:false`) = ซ่อน ไม่ใช้ · seed: `figma[].style.images = []` + `figma[].style.image_slot_empty{node,bbox,scale_mode:FILL,state}` + `side_image_slot` ระดับ record · **wp-dev:** คงคอลัมน์ภาพ `[921,1020 359×559]` ไว้ (ไม่ยุบแผงให้ข้อความเต็มกว้าง) และเมื่อ admin ใส่ภาพ → วาดที่ `[921,1090 359×489]` (กฎ crop เหมือนภาพแผงอื่น · scale FILL) · ยังไม่มีภาพ = ไม่วาดอะไรในช่อง (ไม่มี placeholder ที่คนเห็น)
- **hero = WebM (อัปเดต 2026-11-17 · makarawebm-1117)** — เดิมไม่มี WebM (Drive มีแค่ 3 ไฟล์) → เจ้าของอัปโหลดใหม่ 15:21/15:26 · `hero_video_src` = `Makara/Saltwater crocodile skull.webm` / `Makara/Sungai Mas makara.webm` · `hero_video_by_trail:{makara:<เดียวกัน>}` · status `hero_still_only_no_webm` ลบแล้ว · คง `hero_still_ref` เป็น poster/สำรอง · (บันทึกเดิม: fill ใน Figma เป็น `gifRef` (GIF จริงมีใน Figma แต่ REST ให้เฉพาะเฟรมแรก PNG) — ได้ WebM แล้ว → template เลือก `<video>` ก่อนภาพนิ่ง)
- **ช่อง (tile):** ภาพปกติ/hover เดิมของช่อง 1 (`77d84c86…` / `9a4ee322…` Materials `Makara 1` `3377:5740`) และช่อง 3 (`e7f33e87…` / `a7610fef…` `Makara 3` `3377:5746`) · ภาพ hover มีชื่อฝังในภาพ "Skull of Saltwater Crocodile" / "Sungai Mas Makara" ซึ่ง**ต่างจากหัวข้อ detail เล็กน้อย** (P-350)
- **seed:** `objects.json` +2 record (`detail_style_mode:"generic"` · `trail_order.makara` = 1 / 3 · `status:["side_image_empty_slot_awaiting_designer"]` (ลบ `hero_still_only_no_webm` ตอน makarawebm-1117) · core: `status` ชุดนี้**ไม่ใช่ `hold_*` → แสดง/ลิงก์ตามปกติ**) · `trails.json` makara.tiles[1,3] = `object_slug` + `frame_id` · `object_slugs` 5 · `object_count` 5 · ป้ายนักออกแบบเก็บเป็น `title_note_figma_label`

### 8.7 Rooster ช่อง #11 — frame 1583 มีข้อความครบ = วัตถุเดียวกับ Seashell "Mother-of-pearl furniture" (rooster-1117 · BACKLOG #34 · ต่อ D49 · Figma `lastModified 2026-11-17T08:55:13Z`)
**ที่มา:** REST `/v1/files/:key/nodes?ids=1179:4624` ครั้งเดียว (เก็บที่ `design/figma-rooster-1179-4624.json`) · render `design/frames/2026-11-17/1179-4624.png` (1440×2192) · fill ที่ใช้ = ของเดิมใน `design/assets/objects-1117/` (ไม่มี fill ใหม่) · สร้าง seed ด้วย `design/tools/build-rooster-1117.py` (มี `--help` `--no-write`)
**frame:** `1179:4624` "Desktop - 1583" `1440×2192` · หัวข้อ `3451:4147` "Mother-of-pearl furniture" · meta `3451:4159` (Period · Place of production / Findspot · Material · Dimensions · Collection) · caption `3451:4171` 6 ย่อหน้า · hero `Frame 114493` `1179:4636` `[0,150 1440×810]` (ภาพไก่ 3 ตัว fill `17e037ea…`) · แผง `Frame 114494` `1179:4627` `[0,960 1440×1283]` พื้น **`#6BB9A5`** padding 60/60 gap 22 · คอลัมน์ข้อความ `Frame 12761` `3451:4135` `[160,1020 739×1085]` · คอลัมน์ภาพ `Frame 12760` `1179:4632` `[921,1020 359×678]` · ภาพแผงข้าง `ex (3) 3` `1179:4633` **`[921,1170 359×528]`** FILL ref `7c2cbedd…` (ภาพไก่บนฉากกั้น mother-of-pearl) ครอป `7c2cbedd….crop-1179-4633.jpg` 628×924 (1.75×) · `ex 5` `1179:4634` `visible:false` (ไม่แสดง) · Back `[160,2144 115×50]`
**ตัดสิน "วัตถุเดียวกัน" จากหลักฐาน (P-360):** เทียบสตริงดิบ frame 1583 ↔ frame 1596 (seashell `1200:3112`): หัวข้อ 25 ตัวอักษร · meta 296 · caption 2183 **เท่ากันทุกตัวอักษร (รวม NBSP)** · ขนาด/สีตัวอักษรเท่ากัน (36 Heavy `#212529` · meta 20 `#343A40` · caption 18 `#343A40`) · ภาพช่อง hover rooster #11 มีชื่อ "Mother of Pearl Furniture Depicting Animals" (P-306 ข้อ 9④) · WebM `Chicken&Rooster/Mother of Pearl.webm` ชื่อเดียวกับ `Seashell/Mother of Pearl.webm` · ต่างกันเฉพาะส่วนที่ต่อ trail: สีแผง (เขียว `#6BB9A5` ↔ ม่วงอ่อน `#CFA5BA`) · hero (ไก่ ↔ ของ seashell) · ภาพแผงข้าง (ไก่ `7c2cbedd…` 359×528 ↔ `0ba79979…` 359×247) → เหมือน 8 ชื่อใน P-306 ข้อ 6
**seed ที่เปลี่ยน:** (1) `mother-of-pearl-furniture-depicting-animals`: `trails` = `["seashell","rooster"]` · `trail_order.rooster`=11 · `hero_video_by_trail.rooster`=`Chicken&Rooster/Mother of Pearl.webm` · `tiles_by_trail.rooster` (ช่อง `3377:4680` · hover `3377:5590` · ref `7a2d1a27…` / `77358795…`) · `figma[]` + frame 1583 (`is_primary:false` · `text_differs_from_primary:[]` · `style` ครบตามค่าข้างบน) · primary ยัง seashell · (2) `trails.json rooster.tiles[11].object_slug` = slug นี้ · `object_slugs` แทน `pending` · (3) record `pending`: `status:["hold_superseded_by_mother_of_pearl_furniture_depicting_animals"]` · `trail_order:{}` · ถอด `tiles_by_trail`/`tile_image_*`/`hero_video_by_trail` · เพิ่ม `superseded_by` · **ไม่ลบ record** (ไม่ทำลายข้อมูล · core ซ่อน `hold_*` อยู่แล้ว)
**ข้อยกเว้นเรขาคณิต frame 1583 (อยู่ใน `figma[rooster].style` · P-362):** (ก) `frame_size` สูง 2192 แต่เนื้อหายาวกว่า — Back ล่างสุด y=2194 (เกิน 2px) · แผงยาวถึง y=2243 (เกินขอบ frame · `clipsContent` ไม่ได้ตัด) → นักออกแบบไม่ได้ย่อ/ขยาย frame หลังเติมข้อความ · ให้จัดตามคอลัมน์ข้อความ+ระยะมาตรฐาน ไม่ยึดความสูง frame (ภาพ render ตัดแผงที่ y=2192) (ข) ภาพแผงข้างเริ่ม y=1170 (ไม่ใช่ 1090 มาตรฐาน — คอลัมน์ภาพสูง 678 มีที่ว่างด้านบน 150) · **ภาพแผงข้างของ trail นี้ ≠ ภาพของ record หลัก (seashell)** → ดู P-361
**ฟิลด์ที่หน้าใช้:** ไม่เพิ่มฟิลด์ใหม่ (ใช้ `trails`/`trail_order`/`tiles_by_trail`/`hero_video_by_trail`/`figma[].style` ที่มีใน §8.4) · hero `Chicken&Rooster/Mother of Pearl.webm` อยู่ใน manifest (`drive-1117/…` · verify ผ่าน)
**พฤติกรรม:** กดช่อง #11 ใน `/trail/rooster/` → detail ของ `mother-of-pearl-furniture-depicting-animals` (URL เดียวกับที่มาจาก `/trail/seashell/`) · ข้อความเหมือนกัน · ภาพ/สีแผง/hero = **ของ trail ที่กดมา ตาม D59 → §8.7.1** (เดิม: ตาม primary จนกว่า core รองรับ · P-361 ปิดแล้ว)

#### 8.7.1 Variant ต่อ trail — หน้า detail แสดงตาม trail ที่กดมา (multitrail-1117 · D59 · ปิด P-361 · P-306 ข้อ 6 · BACKLOG #41 · Figma `lastModified 2026-11-17T13:20:38Z`)
**ที่มา:** ผลตรวจ `multitrail-check-1117` (REST `GET /v1/files/:key/nodes` ids=45 · 1 คำขอ · เก็บที่ `design/figma-multitrail-1117-nodes.json` · ส่วน A นี้ **0 คำขอ Figma เพิ่ม**) · 45 frame ใน `figma[]` ของ 19 record ที่ `trails` > 1 → ยังอยู่ 43 · หาย 2 (`1096:9698` cake-molds/elephant · `1096:8731` basin/elephant — หายตั้งแต่ก่อน 11-17 เช้า · ช้างใช้ `1041:866` / `3190:4671` อยู่แล้ว · flag `figma[].gone_from_figma_2026_11_17`) · สร้างด้วย `design/tools/build-multitrail-1117.py` (`--help` · `--no-write` · เขียนครอปลงโฟลเดอร์ชั่วคราวใต้ `design/` แล้วย้ายทีละไฟล์) · ตรวจ `design/tools/verify-objects-1117.py` = PROBLEMS none
**ผลตรวจ (D59):** ข้อความเหมือนกันทุกตัวอักษร 15 record · ต่างจริง 3 record / 4 frame (ด้านล่าง) · ต่างตาม trail ทุก record = **สีแผง · hero (WebM ของ trail) · ภาพ/ตำแหน่งแผงข้าง** · live เดิมใช้ของ trail หลักทุกที่ (D55 ถูกแทนด้วย D59)
**โครง `objects.json[].variants_by_trail.<trail>`** (ทุก record ที่ `trails` > 1 · ทุก trail ที่ยังมี frame ใน Figma · `etc` ไม่มี frame = ไม่มี variant · **43 variant / 19 record**) — เก็บฟิลด์เดิม (`hero_video_by_trail` · `tiles_by_trail` · `figma[]` · `figma_2026_11_17_frames`) ไว้ไม่ลบ · variant = ข้อมูลที่หน้าอ่านตรง ๆ ไม่ต้องคุ้ย `figma[]`:
| ฟิลด์ | ชนิด | ความหมาย |
|---|---|---|
| `figma_frame` · `figma_frame_name` | string | frame ของ trail นั้น (`figma[].frame_id`) — เรขาคณิตเต็มอยู่ที่ `figma[frame_id].style` (แผง/คอลัมน์/Back) |
| `is_primary` | bool | `true` = trail หลักของ record (= `detail_style.source_frame`) · เข้าตรง/ไม่รู้ trail = variant นี้ |
| `hero_video_src` | string/null | path ใต้ `animals-webm/` (ตรง key manifest `drive-1117/<path>`) ของ trail นั้น · **`null` + `hero_source:"elephant_record"`** = variant ช้าง (cake-molds · basin) ใช้ hero เดิมของ record ช้าง (ไม่มี WebM ใน Drive) |
| `panel_bg` · `title_color` · `collection_color` · `caption_color` · `meta_color` | hex | สีแผงล่าง + สีตัวอักษรของ frame นั้น (ค่าที่แสดงจริง · ข้าม `visible:false`) |
| `side_image_ref` | string | `crop_ref` ของภาพแผงข้างภาพแรก (`design/assets/objects-1117/`) — ย่อของ `side_images[0]` |
| `side_images[]` | array | `{order,node,crop_ref,crop_size,bbox,scale_mode,radius}` ตามลำดับบน→ล่าง · `bbox` = ตำแหน่งจริงใน frame (**ต่างตาม trail ได้** เช่น y=1090 / 1170 / 1106) · ภาพเดียวกันอาจ `crop_ref` คนละไฟล์ (คนละ node · คนละกรอบ) |
| `image_col_bbox` · `frame_size` | array | คอลัมน์ภาพขวา · ขนาด frame (ความสูงหน้าต่างกันตามความยาวข้อความ/ภาพ) |
| `text_override`? | object | **เฉพาะ frame ที่ Figma ต่างจากข้อความหลัก** (D59) · คีย์ที่มีได้: `title` (+`title_raw`) · `caption` · `info_rows[]` (รูปเดียวกับ `info_rows` ของ record) · ไม่มี = ใช้ข้อความหลักของ record · ฟิลด์ใน override ทับทีละฟิลด์ (ฟิลด์ที่ไม่มีใน override = ใช้ของ record) · `info_rows` ใน override = **แทนทั้งชุด** (dragon-tray seashell มีแถวเดียว) |
ตัวอย่าง (`sulang`): `variants_by_trail.hornbill` = `{figma_frame:"1161:2808", is_primary:true, hero_video_src:"Hornbill/Sulang.webm", panel_bg:"#6BB9A5", …, side_image_ref:"91bad596….crop-1161-2817.png", side_images:[{order:1,bbox:[921,1090,359,327],…}]}` · `.rooster` = `{figma_frame:"1179:4264", is_primary:false, hero_video_src:"Chicken&Rooster/Sulang.webm", …}`
**ตาราง 43 variant** (★ = trail หลัก · `y=` = ขอบบนภาพแผงข้างใน frame · ข้อความ = ฟิลด์ใน `text_override`):
| slug | trail | frame | panel_bg | hero_video_src | ภาพข้าง | text_override |
|---|---|---|---|---|---|---|
| `ayam-ayam-bernaga` | rooster ★ | `1179:3861` D1579 | `#6BB9A5` | `Chicken&Rooster/Ayam-Ayam Bernaga.webm` | `f4af80acd1…` y=1170 | — |
| `ayam-ayam-bernaga` | dragon | `1111:1430` D1527 | `#F1ADA0` | `Dragon/Ayam-Ayam Bernaga.webm` | `f4af80acd1…` y=1170 | — |
| `rooster-headed-brass-charcoal-iron-adorn…` | phoenix ★ | `1170:3571` D1576 | `#F1ADA0` | `Phoenix/Rooster-headed brass charcoal iron.webm` | `73fc1f09f8…` y=1170 | — |
| `rooster-headed-brass-charcoal-iron-adorn…` | rooster | `1179:4723` D1584 | `#6BB9A5` | `Chicken&Rooster/Rooster-headed brass charcoal iron.webm` | `73fc1f09f8…` y=1090 | — |
| `rooster-headed-brass-charcoal-iron-adorn…` | dragon | `3212:4589` D1642 | `#F1ADA0` | `Dragon/Rooster-headed brass charcoal iron.webm` | `73fc1f09f8…` y=1090 | — |
| `sulang` | hornbill ★ | `1161:2808` D1553 | `#6BB9A5` | `Hornbill/Sulang.webm` | `91bad596cd…` y=1090 | — |
| `sulang` | rooster | `1179:4264` D1581 | `#6BB9A5` | `Chicken&Rooster/Sulang.webm` | `91bad596cd…` y=1090 | — |
| `wooden-cold-press-cake-molds-with-elepha…` | elephant ★ | `1041:866` D1509 | `#DEDCC3` | ช้างเดิมของ record | `60b979fd2c…` y=1120 | — |
| `wooden-cold-press-cake-molds-with-elepha…` | horse | `1161:3543` D1560 | `#DEDCC3` | `Horse/พิมพ์เค้กม้า.webm` | `cc4e001f80…` y=1090 | title, caption |
| `wooden-cold-press-cake-molds-with-elepha…` | rooster | `1179:5042` D1587 | `#6BB9A5` | `Chicken&Rooster/Wooden Cold-Press Cake.webm` | `81ff45de2f…` y=1170 | title |
| `the-dragon-head-tortoise-and-crane-ornam…` | water-bird ★ | `1223:3867` D1629 | `#6BB9A5` | `Waterbird/The Dragon Head Tortoise and Crane Ornament.webm` | `d8e8089d34…` y=1090 | — |
| `the-dragon-head-tortoise-and-crane-ornam…` | turtle | `1220:2663` D1616 | `#CFA5BA` | `Turtle/The Dragon Head Tortoise and Crane Ornament.webm` | `d8e8089d34…` y=1170 | — |
| `the-dragon-head-tortoise-and-crane-ornam…` | dragon | `1131:1329` D1534 | `#F1ADA0` | `Dragon/Dragon-head tortoise and crane ornaments.webm` | `d8e8089d34…` y=1170 | — |
| `blackened-brass-ritual-holy-water-vessel` | dragon ★ | `1115:1466` D1529 | `#F1ADA0` | `Dragon/Holy water vessel.webm` | `946674fbcc…` y=1170 | — |
| `blackened-brass-ritual-holy-water-vessel` | naga | `1203:3994` D1605 | `#F1ADA0` | `Serpent/Blackened Brass Ritual Holy Water Vessel.webm` | `946674fbcc…` y=1170 | — |
| `dragon-shaped-mother-of-pearl-inlaid-inc…` | dragon ★ | `1120:2138` D1489 | `#F1ADA0` | `Dragon/Dragon-shaped incense tray.webm` | `e77030efaa…` y=1090 | — |
| `dragon-shaped-mother-of-pearl-inlaid-inc…` | seashell | `1200:2747` D1592 | `#CFA5BA` | `Seashell/Dragon-shaped.webm` | `e77030efaa…` y=1090 | title, caption, info_rows |
| `flintlock-firearm` | dragon ★ | `1120:1918` D1531 | `#F1ADA0` | `Dragon/Flintlock Firearm.webm` | `c5643e230e…` y=1170 | — |
| `flintlock-firearm` | naga | `1205:4177` D1607 | `#F1ADA0` | `Serpent/Flintlock Firearm.webm` | `c5643e230e…` y=1170 | — |
| `jar-decorated-with-fish-and-a-beast` | fish ★ | `1051:3381` D1458 | `#CFA5BA` | `Fish/Jar decorated with fish and a beast␣.webm` | `1566c27872…` y=1106 | — |
| `jar-decorated-with-fish-and-a-beast` | makara | `1170:2791` D1568 | `#CFA5BA` | `Makara/Jar decorated with fish and a beast␣.webm` | `1566c27872…` y=1106 | — |
| `wooden-chalk-line-reels` | fish ★ | `1145:1368` D1541 | `#CFA5BA` | `Fish/Wooden chalk-line reels.webm` | `61b5533e60…` y=1090 | — |
| `wooden-chalk-line-reels` | naga | `1218:2469` D1614 | `#F1ADA0` | `Serpent/Wooden Chalk-Line Reels (Marking Gauges).webm` | `889555dfc8…` y=1170 | — |
| `dastar-head-cloth-with-horse-motif` | horse ★ | `1161:3056` D1555 | `#DEDCC3` | `Horse/Dastar with horse motif.webm` | `c34dee3e94…` y=1090 | — |
| `liturgical-pulpit-covers` | horse ★ | `1161:3157` D1556 | `#DEDCC3` | `Horse/Liturgical pulpit covers.webm` | `c6bb8717a4…` y=1073/1303 | — |
| `liturgical-pulpit-covers` | garuda | `1157:1961` D1545 | `#F1ADA0` | `Garuda/Liturgical pulpit covers.webm` | `c6bb8717a4…` y=1073/1303 | — |
| `oracle-bone` | horse ★ | `1161:3352` D1558 | `#DEDCC3` | `Horse/Oracle Bone.webm` | `4c7db83dd4…` y=1090 | — |
| `oracle-bone` | buffalo | `3218:3649` D1643 | `#DEDCC3` | `Buffalo/Oracle bone.webm` | `4c7db83dd4…` y=1090 | — |
| `embossed-brass-basin-with-mythical-anima…` | elephant ★ | `3190:4671` D1633 | `#DEDCC3` | ช้างเดิมของ record | `d12242d441…` y=1100/1399 | — |
| `embossed-brass-basin-with-mythical-anima…` | garuda | `1153:1051` D1542 | `#F1ADA0` | `Garuda/Embossed brass basin with mythical animal motifs (Garuda).webm` | `3cdcf5ba1c…` y=1170 | — |
| `embossed-brass-basin-with-mythical-anima…` | lion | `1168:2307` D1563 | `#DEDCC3` | `Lion/Embossed brass basin with mythical animal motifs.webm` | `087a6be925…` y=1170 | — |
| `embossed-brass-basin-with-mythical-anima…` | naga | `1203:4085` D1606 | `#F1ADA0` | `Serpent/Embossed brass basin with mythical animal motifs.webm` | `8e4037fc5f…` y=1170 | — |
| `ng-s-n-drum` | water-bird ★ | `1223:3409` D1624 | `#6BB9A5` | `Waterbird/Đông Sơn Drum.webm` | `39d5c14128…` y=1090 | — |
| `ng-s-n-drum` | seashell | `1194:2376` D1588 | `#CFA5BA` | `Seashell/Đông Sơn drum.webm` | `eed819fa95…` y=1090 | caption, info_rows |
| `monk-s-alms-bowl` | buffalo ★ | `1075:1164` D1487 | `#DEDCC3` | `Buffalo/Lacquered ceremonial vessel.webm` | `c4c27743de…` y=1050/1294.5 | — |
| `monk-s-alms-bowl` | lion | `1170:2497` D1565 | `#DEDCC3` | `Lion/Lacquered ceremonial vessel.webm` | `630cb20137…` y=1090/1421 | — |
| `monk-s-alms-bowl` | seashell | `1200:3021` D1595 | `#CFA5BA` | `Seashell/Lacquered ceremonial vessel.webm` | `630cb20137…` y=1090/1376 | — |
| `mother-of-pearl-furniture-depicting-anim…` | seashell ★ | `1200:3112` D1596 | `#CFA5BA` | `Seashell/Mother of Pearl.webm` | `0ba79979c8…` y=1090 | — |
| `mother-of-pearl-furniture-depicting-anim…` | rooster | `1179:4624` D1583 | `#6BB9A5` | `Chicken&Rooster/Mother of Pearl.webm` | `7c2cbeddb7…` y=1170 | — |
| `garment-for-buddhist-prostration-featuri…` | bird ★ | `1091:3326` D1510 | `#6BB9A5` | `Bird/Cloth for Buddhist prostration.webm` | `74d1002259…` y=1120 | — |
| `garment-for-buddhist-prostration-featuri…` | garuda | `1153:1146` D1543 | `#F1ADA0` | `Garuda/Cloth for Buddhist prostration, featuring Garuda and peacock motifs.webm` | `74d1002259…` y=1090 | — |
| `pending-3` | bird ★ | `1090:2840` D1509 | `#6BB9A5` | `Bird/Pending (Traditional Belt Buckle).webm` | `27791d4583…` y=1090/1377.7 | — |
| `pending-3` | phoenix | `1170:3381` D1574 | `#F1ADA0` | `Phoenix/Pending (Traditional Belt Buckle).webm` | `27791d4583…` y=1170/1405 | — |
**`text_override` 4 ตัว (4 frame · คัดลอกตรงจาก node · NBSP→ช่องว่าง · U+2028→`\n` · ตัด "Caption:" · `\n` เดี่ยว→`\n\n`):** (1) `wooden-cold-press-cake-molds…` **horse** `1161:3543` = `title` + `title_raw` + `caption` ("This phim yen (literally "cold mold") is a wooden pastry mold…" · 5 ย่อหน้า) · (2) cake-molds **rooster** `1179:5042` = `title` + `title_raw` ("Wooden Cold-Press Cake Molds with Elephant, Horse, and Fowl Motifs" · ตัวพิมพ์ใหญ่/`Molds`/จุลภาคก่อน `and`) · caption rooster = ข้อความ "These are wooden moulds…" ซึ่งเท่ากับ frame ช้าง → ใช้ข้อความหลัก ดู P-396 · (3) `dragon-shaped-mother-of-pearl-inlaid-incense-tray` **seashell** `1200:2747` = `title` "Dragon-Shaped Mother-of-Pearl Inlaid Incense Tray" + `caption` (ถ้อยคำต่าง 3 ช่วง) + `info_rows` = **แถวเดียว** `Collection: National Museum Bangkok` (Period · Place · Material · Dimensions · Other information **ไม่มีใน Figma frame นี้** → ซ่อน ไม่เดา · D49) · (4) `ng-s-n-drum` **seashell** `1194:2376` = `caption` (Heger Type I drum · "Thung Yang") + `info_rows` 5 แถว (Period Prehistoric · Place Thung Yang District, Uttaradit Province · Dimensions Height 53 cm, diameter 65 cm · Collection National Museum Bangkok, Thailand · Other information / Object biography ได้รับบริจาค 1927) — **คนละชิ้นกับ water-bird** (ชื่อ "Đông Sơn drum" เหมือนกัน · หัวข้อเหมือน จึงไม่มี `title` ใน override) · record หลัก = water-bird (Nakhon Si Thammarat · Folklore Museum) — ดู P-398
**แก้ seed ที่ค้าง (Figma ปัจจุบัน `13:20:38Z` ↔ seed):** `figma[].style` ของ 5 frame สร้างใหม่ (เป็นค่าก่อนนักออกแบบแก้สี/ภาพ — ไม่ได้ re-extract ตอน design-objects-1117 เพราะเป็น record ช้าง) · **panel_bg ผิด 4 frame:** cake-molds horse `1161:3543` `#705F4D`→`#DEDCC3` (สีตัวอักษร `#FFFFFF`/`#F8F9FA`→`#212529`/`#343A40`) · cake-molds rooster `1179:5042` `#EEDFB4`→`#6BB9A5` · basin garuda `1153:1051` `#F0ADA0`→`#F1ADA0` · basin naga `1203:4085` `#E2B3A7`→`#F1ADA0` · basin lion `1168:2307` (สีแผงถูกอยู่แล้ว · ไม่มี `images` มาก่อน) · **ภาพแผงข้างของ 5 frame นี้ไม่เคยอยู่ใน `style.images`** → เพิ่มแล้ว + ครอปใหม่ 5 ไฟล์ใน `design/assets/objects-1117/` (fill ต้นฉบับมีอยู่แล้ว ไม่ดึง Figma): `cc4e001f….crop-1161-3552.png` (horse `[921,1090 359×405]`) · `81ff45de….crop-1179-5051.png` (rooster `[921,1170 359×372]`) · `3cdcf5ba….crop-1153-1104.jpg` (garuda `[921,1170 359×270]`) · `087a6be9….crop-1168-2316.jpg` (lion `[922,1170 358×269]`) · `8e4037fc….crop-3191-3184.jpg` (naga `[921,1170 359×265]`) · **สีตัวอักษรเก่า** `pending-3` phoenix `1170:3381`: title `#534741`→`#212529` · collection/meta `#413B2F`→`#343A40` (แก้เฉพาะคีย์สี) · frame อื่น 38 frame: สีแผง/ภาพข้างใน seed ตรง Figma ปัจจุบันทุกตัว (ต่างแค่การปัดเศษ)
**ฟิลด์ที่หน้าใช้ (ให้ core ยก):** `variants_by_trail.<trail>.{hero_video_src, panel_bg, title_color, collection_color, caption_color, meta_color, side_image_ref, side_images[], text_override}` · เลือก variant: (ก) มาจากหน้า trail → trail ของหน้านั้น (ข) มาจากกริด Zone 1–3 → `zones-2026-11-17.json …tiles[].trail` (ค) เข้าตรง/ไม่รู้ trail/trail ไม่มี variant → variant ที่ `is_primary:true` · ทุกตัวต้อง fallback ที่ค่าเดิมของ record (`hero_video_src` · `detail_style` · `figma[primary].style`) ถ้าไม่มี variant
**zone grid → trail (`zones-2026-11-17.json`):** ทุกช่องที่มี `object_slug` (**139 ช่อง** = zone 1 → 14 · zone 2 → 42 · zone 3 → 83 · zone 1 ช่อง 8 ไม่มี slug) ใส่ `trail` · วัตถุ trail เดียว = trail นั้น (`trail_basis` ไม่มี) · วัตถุหลาย trail (**41 ช่อง** · zone 2 = 19 · zone 3 = 22 · zone 1 ไม่มี) จับคู่ `tile_ref`+`hover_ref` กับ `objects.json tiles_by_trail.<trail>.{tile_image_ref,tile_image_hover_ref}` → `trail_basis:"tile+hover"` (38) · `"tile|hover"` (1 · zone3 #77 dragon-tray/seashell) · **ช่องช้าง 2 ช่อง** = `elephant` (`trail_basis:"…+no_ref_of_other_trails"`): zone 2 #38 `embossed-brass-basin` (`match_basis` เดิม `elephant_trail_tile5_live`) · zone 3 #47 `wooden-cold-press-cake-molds` (`match_basis` เดิม `click_chain`) — ไม่ตรง ref trail อื่นเลย + หลักฐานเดิมของช้าง · รายการจับคู่ทั้ง 41 ช่องอยู่ใน seed (`tiles[].trail`)
**พฤติกรรม (D59):** กดวัตถุหลาย trail → หน้า detail ใช้ **WebM · สีแผง · สีตัวอักษร · ภาพแผงข้าง ของ trail ที่กดมา** · ข้อความ = ของ record (ชุดเดียว) ยกเว้น `text_override` ของ 4 frame ข้างบน · URL เดียวกันทุก trail (ไม่เพิ่มหน้าใหม่) — ต้องส่ง trail ไปกับลิงก์ (query/ตัวแปร ตามที่ core ออกแบบ) · Back กลับ trail ที่มา (D55 เดิม) · เข้าตรง = trail หลัก

### 8.8 ภาพ hover ช่อง trail — ตรวจใหม่ 127 ช่อง · เปลี่ยน 2 (hover-1117 · BACKLOG #35 · ต่อ P-306 ข้อ 9 · Figma `lastModified 2026-11-17T11:28:55Z`)
**ที่มา:** `GET /v1/files/:key/nodes?depth=1` 1 คำขอ (376 id = hover 127 + tile 127 + materials_set 122 ไม่ซ้ำ · `design/tools/fetch-hover-1117.py` · มี `--help`/`--no-write`) + `GET /files/:key/images` 1 คำขอ (เอา URL fill ใหม่) · hover = COMPONENT 210×210 มี image fill เดียว (`children:[]`) → เทียบ `fills[IMAGE].imageRef` กับ seed
**ผล:** id ทุกตัวยังอยู่ · tile ปกติ 127 ช่อง ref ไม่เปลี่ยน · hover เปลี่ยน **2 ช่อง (Seashell)** — seed เก็บ ref เดิมใน `tile_image_hover_ref_prev`:
| trail #ช่อง | slug | hover node | ข้อความบนภาพ เดิม → ใหม่ | ref ใหม่ (`design/assets/objects-1117/`) |
|---|---|---|---|---|
| seashell #12 | `dragon-shaped-mother-of-pearl-inlaid-incense-tray` | `3377:5816` | "Kalung" (รูปมังกร) → "Dragon-Shaped Mother-of-Pearl Inlaid Incense Tray" (รูปมังกร + ไอคอนแปรง/ตรามังกร) | `dc3709eae792995bc8a3d3fabb0bffb5bfc1d81b.png` |
| seashell #13 | `kalung` | `3377:5819` | "Dragon-Shaped Mother-of-Pearl Inlaid Incense Tray" (รูปสร้อย) → "Wangpar" (สร้อยเปลือกหอย + ไอคอนสาน/แจกัน) | `5e9b806b1fad49f0a6068899156debd15ff602b8.png` |
**ฟิลด์ที่หน้าใช้:** ไม่เพิ่มฟิลด์ที่หน้าใช้ · `tile_image_hover_ref_prev` = ฟิลด์ตรวจสอบสำหรับ importer/wp-dev เท่านั้น (บอกว่า prod เดิมเป็นค่าที่ importer ใส่ → ปลอดภัยที่จะอัปเดต ไม่ทับที่ admin แก้) · ตำแหน่ง: `objects.json[].tiles_by_trail.seashell` ของ 2 record + `objects.json[kalung].tile_image_hover_ref(_prev)` (ระดับ record · primary trail) + `trails.json seashell.tiles[12,13]`
**ยังผิดใน Figma (ไม่แก้):** P-306 (9)③⑤⑥⑦ → P-370 · **พฤติกรรม:** hover แสดงภาพใหม่ในช่อง seashell #12/#13 (เดิม 2 ช่องนี้ชื่อสลับกัน) · ช่อง Dragon trail #9 (`3377:5631` `384a62be…`) ชื่อ/ภาพตรงอยู่แล้ว ไม่เปลี่ยน

---

## 9. รอบ 2026-11-17 — header/footer ใหม่ · Home ใหม่ + การ์ดสัตว์ · Intro 4 ตอน · Zone 4 (D47 D48 D50 D51 · BACKLOG #25–#27 #29 #31 #32 · task `design-pages-1117`)
**ที่มา:** `design/figma-file.json` lastModified **`2026-11-17T03:50:23Z`** (ไม่ดึงทั้งไฟล์ซ้ำ) · page `3117:3314` · Home ใหม่ `3339:5227` (Desktop - 1634 · 1440×3786) · ตัวอย่างการสุ่ม `3339:6016` · การ์ด 17 ใบ `3339:5488` · Intro ใหม่ `3339:6117` · render 0.5x: `design/frames/2026-11-17/` · ทับ §3/§4.1/§4.2 เฉพาะที่ระบุในหัวข้อนี้ · ค่ามาจาก node จริง (ข้าม `visible:false`) · ป้ายที่มา: `[N]` node Figma · `[C]` คำขอลูกค้า/เจ้าของ (D-xx) · `[P]` ข้อเสนอ (Figma/ลูกค้าไม่ระบุ — ตัดสินเองตามรูปแบบเดิม) · P-27x = PENDING §DEV ของรอบนี้
**ตารางค่าที่ใช้:** ฟอนต์ Komet (P-205 ยังรอไฟล์) · สี neutral/accent ตาม §2.1 · ฟิลด์ที่หน้าใช้ → §9.6 · seed: `content/seed/site.json` (header/footer/ป้าย) · `guides.json` · `intro-2026-11.json`

### 9.1 Header ใหม่ + Footer (ใช้ทุกหน้า · D47 · BACKLOG #25 #27)
**Header ใหม่ — Figma `3339:5445` "Header" [0,0 1440×150] (ใน Home `3339:5227` · ตัวเดียวกันใน Zone 1–3 ใหม่ `3339:5450/5463/5476` · Intro ใหม่ `3339:6342/…`)** = component เดิม `Header #1` (`3220:3970`) + `Header #02` (`670:1143`) แต่เนื้อใน **เปลี่ยน**:
| ชิ้น | พิกัด/ค่า (px ที่ 1440) | ที่มา |
|---|---|---|
| Header #1 พื้น | `[0,0 1440×86]` fill `#FFFFFF` · Drop Shadow/100 (`0 1px 4px #0C0C0D@5%`) · auto-layout แนวนอน padding-left/right **160** · กึ่งกลางแนวตั้ง · **ไม่มีเส้นล่าง** (stroke `#AACFD8` 1.5 bottom = `visible:false` ในทุก frame ของ page `3339:*` — Home · Zone 1–3 ใหม่ · Intro ใหม่) | `[N]` `3339:5285` |
| **โลโก้ Shared Heritage** (แทนภาพพื้นเดิม + UNESCO vector + เมนู utility) | **`[160,4.5 220×77]`** ชิดซ้ายที่ x=160 · กึ่งกลางแนวตั้ง (4.5+38.5 = 43 = 86/2) · ภาพ `877fd074` (1802×612 PNG · แสดงจริงแบบ crop x 0.0738 / y 0.1013 / w 0.8768 / h 0.8987 — ใช้ไฟล์ `design/assets/pages-1117/fills/877fd074….png` · `site.json` `brand.header_logo`) · ลิงก์ → หน้าแรก `[P]` · `alt` "Shared Heritage — Museums in South-East Asia" | `[N]` `I3339:5285;3307:1972` |
| ❌ ที่ **หายไป** จาก Header #1 | โลโก้ UNESCO vector `[160,14 171×36]` · เมนู utility (Visit · Exhibition and Events · Art · Research · About · Contact) · ภาพพื้น `709572bf` (มี Temasek) — **ไม่มีใน header ใหม่** (Header #1 มี child เดียว = โลโก้) · UNESCO + Temasek ย้ายไป footer (ด้านล่าง) | `[N]` D47 |
| Header #02 พื้น | `[0,86 1440×64]` fill `#FFFFFF` · Drop Shadow/100 · ไม่มี stroke · padding 160/160 · ชิดขวา · แถว `Frame 12653` gap **30** | `[N]` `3339:5284` — **ค่าเดิม §3.2 ทั้งหมด** |
| เมนู 6 รายการ | Komet-Medium **14px / w600 / lh 17.5** สี `#343A40` · ข้อความสูง 18 ที่ y=109 (กึ่งกลางแถว 86+32) · x (กว้าง): **Main Page** 437 (68) · **Shared Heritage Framework** 535 (177) · **Museums and Partnership** 742 (167) · **Index** 939 (36) · **Events and Activities** 1005 (132) · **About the Project** 1167 (113) · ปลายขวา = 1280 (= container 160→1280) · ช่องไฟ 30 · ผลรวมกว้าง 843 | `[N]` `I3339:5284;670:1146…1150` + **`3220:3745` (ใหม่)** |
| About the Project | รายการที่ 6 (ใหม่) = **ลิงก์ภายนอก** `https://www.unesco.org/en/articles/shared-heritage-museums-south-east-asia` · **`target="_blank" rel="noopener noreferrer"`** · ตัวอักษร/สี/ระยะเหมือนรายการอื่น · ไม่มีไอคอน "ลิงก์ออก" ใน Figma → ไม่ใส่ (`aria-label` "About the Project (opens in a new tab)" `[P]`) · admin แก้ URL ได้ (option) | `[C]` D47 · seed `site.json` `nav_primary_links` |
| ปลายทางเมนูอื่น | Figma ON_CLICK ทุกรายการ → Main Page (prototype ยังไม่กำหนดจริง) · ค่าเดิม P-211 (ใช้หน้าของเว็บเมื่อมี) | เดิม |
| เส้นล่าง Header #1 | **หน้าใน page `3339:*` (Home · Zone 1–3 · Intro) = ไม่มีเส้น** · หน้า trail/detail/timeline/zone4 chooser/zone 5 (frame เก่าใน Figma ยังใช้ instance เดิม `visible:true`) = เส้น `#AACFD8` 1.5 bottom ตาม §2.4 · **ทำตามค่าต่อหน้าเหมือนเดิม** `[P]` → P-276 | `[N]` นับจาก instance ของ `3220:3970`: ใน page `3339:*` ทั้ง 12 ไม่มีเส้น · instance เก่าอีก 22 (zone เดิม 3 · zone4/5 4 · trail/detail/timeline 15) มีเส้น |
**Responsive `[P]`:** โครงสองแถว (โลโก้ / เมนู) เหมือนเดิม → ใช้ responsive เดิมของธีมได้ · เปลี่ยนเฉพาะ (1) โลโก้ 220×77 → ย่อตามสัดส่วน (≥ 120 กว้างบนมือถือ) (2) เมนู 6 รายการกว้างรวม 843 → ที่ ≥1024 ยังพอดีแถวเดียว (container ≥ 843+2·24) · < 1024 ใช้พฤติกรรมพับ/แฮมเบอร์เกอร์เดิมของธีม (รายการที่ 6 เข้าเมนูพับ ลำดับท้าย) · ตรวจที่ 1024/768/390
**ความต่างจาก §3.1/§3.2 เดิม (ตาราง):** Header #1: โลโก้ UNESCO 171×36 + utility 6 รายการ + ภาพพื้น Temasek/SH → **โลโก้ SH 220×77 ตัวเดียว** · Header #02: 5 รายการ → **6 รายการ** (+About the Project) · สูง/สี/เงา/พิกัด y **ไม่เปลี่ยน** (150 รวม) · เส้นล่าง: ตามต่อหน้า (ข้างบน)

**Footer (D47 · โลโก้ UNESCO + Temasek ล่างสุดทุกหน้า) — Figma `3339:5248` `Frame 114511` ใน Home**
| ชิ้น | ค่า | ที่มา |
|---|---|---|
| กรอบ | `[160,3556 1120×95]` (Home) · auto-layout แนวนอน **padding-left/right 267 · padding-top/bottom 5 · gap 20 · จัดชิดล่าง** · ห่างบน = **40** จาก Zone 4/5 (3516→3556) · ใต้กรอบ (3651) ถึงท้ายหน้า (3786) = **135** | `[N]` |
| โลโก้ UNESCO | `3339:5249` group `[427,3586 283.1×60]` (= x กรอบ+267 · y ชิดล่าง: ขอบล่าง 3646) · **SVG** `design/assets/pages-1117/svg/unesco-footer-logo-3339-5249.svg` (284×60 · สี `#0069B4` + `#FFFFFF`) — **คนละตัวกับโลโก้ UNESCO 171×36 เดิม (`unesco-logo.svg`)** | `[N]` |
| Temasek | `Frame 114512` `[730.1,3561 208×85]` (x = 427+283.1+20) · แนวตั้ง กึ่งกลาง gap **7**: ข้อความ **"With the Support of"** Komet-Regular 14/17.5 w500 สี **`#49176D`** (127×18 · กึ่งกลาง) + ภาพ `2. Temasek Foundation original` `[730.1,3586 208×60]` (FILL · `8bffcba6` 3886×1124 — ใช้ไฟล์ `…/fills/8bffcba6….png`) | `[N]` `3339:5254/5255/5256` |
| ความสูงโลโก้ | UNESCO **60** · Temasek **60** (+ ข้อความ 18 + 7 = 85) · ขอบล่างของสองตัว **ตรงกัน (3646)** | `[N]` |
| การจัดแนวนอน | **ตาม Figma = ชิดซ้ายที่ x=427 (padding 267) ไม่ใช่กึ่งกลางหน้า** — กลุ่มโลโก้กว้างรวม 511.1 กึ่งกลาง x=682.5 (ซ้ายของกึ่งกลาง 720 อยู่ 37.5px · เห็นได้ใน render 0.5x) → **ทำตาม Figma** แล้วถามเจ้าของว่าต้องการกึ่งกลางไหม (P-274) · ตัวแปร CSS `--footer-pad-left: 267px` · responsive: < 1280 → `padding-left = 267/1120 · container` · < 768 → เรียงแนวตั้ง กึ่งกลาง gap 20 `[P]` | `[N]` / `[P]` |
| ใช้ทุกหน้า | Figma วาด footer เฉพาะ Home → ทุกหน้าใช้ component เดียวกัน (partial `footer.php`) `[P]` · ระยะบน/ล่าง: บน 40 (ขั้นต่ำ · หน้าที่มีพื้นสีเต็มกว้าง เช่น detail/intro "Living heritage" ต่อด้วยพื้นขาวใหม่) · ล่าง 135 ตาม Home `[P]` | `[P]` |

### 9.2 Home ใหม่ — `3339:5227` Desktop - 1634 (1440×3786) · ภาพ 1x `design/frames/2026-11-17/1x-home-3339-5227.png`
ทับ §4.1 เฉพาะที่ระบุ · ลำดับ section ใหม่ (บน → ล่าง) · พิกัดเทียบ frame · ทุก section กว้าง 1120 ที่ x=160 · หมายเหตุ: ช่วง 616→2289 อยู่ใน auto-layout แนวตั้ง `Frame 114482`/`Frame 12685` (gap 50) — ค่าด้านล่างคือผลลัพธ์จริง
| # | section | พิกัด [x,y w×h] | รายละเอียด (ต่างจาก §4.1) |
|---|---|---|---|
| — | Header (§9.1) | `[0,0 1440×150]` | ใหม่ — โลโก้ SH + เมนู 6 รายการ · ไม่มีเส้นล่าง |
| 1 | Cover | `[160,150 1120×466]` | ภาพ `88d09cb8` (Figma 11-11+ · ใน seed/§4.1 ยังเป็น `2e3fa9c4` → ตรวจว่า live ใช้ตัวไหน · `site.json` `home.refs_2026_11_17`) |
| 2 | Introduction `Frame 12685` | `[160,616 1120×364]` padding บน/ล่าง **40** gap 22 | **เหลือแค่แถว intro** (เดิม 552 รวมแถบ Zone): แถว `[160,656 1120×284]` — การ์ดรูป `[160,656 551×284]` r22 + shadow200 (`3f4cfd00` STRETCH crop 1.723×0.891) · ข้อความ `[731,656 549×281]` ตามเดิม §4.1 แถว 2 ทุกค่า · **แถบ Zone ถูกย้ายออก** |
| 3 | Zone bar `Frame 12684` | `[160,1030 1120×166]` r22 + shadow200 (2 ชั้น) | `30d9d301` · **ลง 68px** จากเดิม 962 (ห่างจากพื้นล่าง Introduction 980 = **50**) · ⚠ มี frame ซ้ำ `3339:5239` ที่ y=962 **ซ่อน (`visible:false`)** — ไม่ใช้ |
| 4 | Zone 1-2-3 `Zone 123` | `[160,1246 1120×553]` (ห่างแถบ **50**) | 3 การ์ด x = 160 / 533.3 / 906.7 กว้าง 373.3 · รูป 373.3×487 · gap 16 · ชื่อ Komet-Heavy 20/25 กึ่งกลาง `#212529` ที่ y=1749 · **ข้อความ (ตัวอักษรตรง Figma): "Animals in Nature and Mythology" · "Beyond the Visible: \nAnimals and the Cosmic Order" · "Material Cultures: \nAnimals in Rituals and Daily Life"** (ตรง `zones.json` `name_raw` ของ zone 1–3 ที่มีอยู่แล้ว (ตรวจแล้ว) — ต่างจาก §4.1 แถว 4 ที่คัดจาก frame เก่า) · hover ตามเดิม §3.7 |
| 5 | **Your animal guide (ใหม่)** | `[160,1849 1120×440]` (ห่าง Zone **50**) พื้น `#FFFFFF` · padding 40/40 · gap 50 · **clip** | สถานะเริ่ม = การ์ด ? · สเปกเต็ม **§9.3** · ความสูงเปลี่ยนตามสถานะ: 440 → 585 → 1156–1409 |
| 6 | Trails `Frame 114476` | `[160,2339 1120×1312]` (ห่างกลุ่มบน **50**) gap 40 | ข้อความ `trails Detail text` `[160,2339 1120×127]` + AAMM `[160,2486 1120×630]` ตามเดิม §4.1 แถว 5 / §3.4 (Read more 3 ยุบ→ขยาย) · **ระยะ Zone→Trails = ผ่านการ์ดสัตว์: Zone จบ 1799 → การ์ด 1849–2289 → Trails 2339 (50 + 50 · ภาพเห็นจริง 90 + 90 เพราะการ์ดมี padding 40)** → แทนค่า **120 ของ D46** (D46 ใช้กับ layout เดิมที่ไม่มีการ์ดสัตว์) — ตาม Figma ใหม่ (`[P]` ว่าไม่ขัดเจตนา D46: ช่องว่างระหว่าง Zone กับ Trails ยังโล่งกว่าเดิม) |
| 7 | Zone 4 + Zone 5 `Frame 12792` | `[160,3156 1120×360]` (ห่าง AAMM 3116 = **40**) | เดิม §4.1 แถว 6 (560×360 สองใบ · ชื่อ 20/25) |
| 8 | **Footer (โลโก้)** | `[160,3556 1120×95]` (ห่าง **40**) | §9.1 |
| — | ท้ายหน้า | 3651 → 3786 | ว่าง 135 (§9.1) |
| — | Navigator 4 จุด | `[176,587 30×150]` | ไม่เปลี่ยน §3.5 · Figma ใหม่: จุด 1 → Cover · จุด 3 → Trails `3339:5242` · จุด 4 → Zone 4–5 `3339:5245` · **จุด 2 ไม่มี interaction ใน Figma** → ใช้ Zone 1–3 ตามเดิม `[P]` (ไม่มีจุดให้การ์ดสัตว์) |
**ส่วนที่ไม่เปลี่ยน → อ้าง §4.1/§3:** Hero · การ์ดรูป+ข้อความ Introduction · Zone bar ภาพ · Zone cards hover · Trails animation (§6.2) · Zone 4/5 hover · Navigator · Read more · seed `site.json` `home.*`


### 9.3 การ์ดสัตว์สุ่ม "Your animal guide is..." (D48 · BACKLOG #26) — Figma `3339:5265` (ในหน้า) · ตัวอย่างสถานะ `3339:6016` (`Desktop - 1636` ? · `1637` ยุบ · `1635` ขยาย) · การ์ด 17 ใบ `3339:5488` · seed `content/seed/guides.json` (17 ใบ · ตรวจแล้วตรง Figma ทุกใบ) · ภาพ 1x `1x-guide-initial-3339-6039` · `1x-guide-collapsed-3339-6059` · `1x-guide-expanded-3339-6017` · `1x-card-chicken-3339-5489` (+ bird · dragon · seashell)
**⚠ อัปเดต fb-1117 (2026-11-17 · BACKLOG #38):** เจ้าของขอ **หัวข้อ "Your animal guide is..." และชื่อการ์ด ("The Chicken" / "The Horse" …) = 26px** (ปัจจุบัน 30px) · Figma ณ `lastModified 2026-11-17T11:28:55Z` **ยังเป็น 30px / lh 28px ทั้งคู่** (`3339:5267` หัวข้อ · `3339:5277` ชื่อการ์ดสถานะเริ่ม-ตัวอย่าง · ชื่อในสถานะ 2/3 ใช้สไตล์เดียวกัน) → ค่า 26 มาจากสไลด์ ไม่ใช่ Figma · **line-height ไม่ได้ระบุ → คงเดิม 28px** (ดู §10.4 · P-380) · ข้อความอื่นในส่วนนี้ไม่เปลี่ยน
**ภาพรวมโครง (เทียบขอบบน section = 0 · กว้าง 1120 · พื้น `#FFFFFF` · padding บน/ล่าง 40 · gap ระหว่างก้อน 50 · `clipsContent:true` ⇒ section **ต้องยืดสูงตามสถานะ ไม่ปล่อยเนื้อล้น**)** — ก้อนบน (หัวข้อ + แถว intro + การ์ด) **อยู่ตลอดทุกสถานะ** · ก้อนล่าง (ผลสุ่ม) ปรากฏที่ y=450 หลังสุ่ม
**สถานะ 1 — เริ่ม (การ์ด ?)** `Desktop - 1636` = 1440×440 → section สูง **440**
| ชิ้น | พิกัด [x,y w×h] | ค่า | node |
|---|---|---|---|
| หัวข้อ | `[0,40 1120×28]` | "Your animal guide is..." Komet-Heavy **30px / w800 / lh 28** (lh < size ตาม Figma) กึ่งกลาง `#212529` | `3339:5267` |
| แถว intro | `[0,113 1120×287]` | auto-layout แนวนอน **padding 190/190 · gap 30 · กึ่งกลางแนวตั้ง** (หัวข้อ→แถว ห่าง **45**) | `3339:5268` |
| คอลัมน์ข้อความ | `[190,113 570×287]` | แนวตั้ง gap **15.5** · ทุกย่อหน้า Komet **20px / w500 / lh 25** ชิดซ้าย สี **`#494538`** (ยกเว้น CTA) | `3339:5269` |
| Opening | `[190,113 570×50]` | "An animal has crossed your path... Can you figure out where it went?" | `3339:5270` |
| Heritage | `[190,178.5 570×75]` | "For centuries, animals have inspired artists, craftspeople, families and spiritual seekers, who brought them to life in art you can still see today." | `3339:5271` |
| Question | `[190,269 570×25]` | "Which animal will guide you?" | `3339:5272` |
| Instructions | `[190,309.5 570×50]` | "Pick a card to meet your animal guide, then follow its tracks through the heritage Southeast Asia shares." | `3339:5273` |
| CTA | `[190,375 570×25]` | **"CHOOSE A CARD"** Komet **20/700** lh25 สี **`#343A40`** ชิดซ้าย · ปุ่ม/ลิงก์ = สุ่ม (D48) | `3339:5274` |
| **การ์ด "?"** | **`[790,161.5 120×190]`** (กึ่งกลางแนวตั้งของแถว · x = 190+570+30) | ภาพ `c632e52f` (1716×2710 · STRETCH crop x .016 y .017 w .968 h .971 · ไฟล์ `design/assets/pages-1117/guides/_card-back.png`) · ภาพมีขอบ/มุมมน/ตัว "?" ฝังในภาพ (ไม่มี text node) · **เคอร์เซอร์เปลี่ยนเป็นมือ** (ปุ่มสุ่ม) | `3339:5275` |
| ข้อความ ย่อหน้าใน Figma มี `\n` ตัดบรรทัดเอง (ตัด ~58–60 ตัวอักษร/บรรทัด ให้พอดี 570) | | **ตัดสินเอง:** seed เก็บ 2 ฟิลด์ — `*_raw` ตรงตัวอักษร Figma · ฟิลด์ปกติยุบ `\n`→ช่องว่าง (ให้ธีม reflow ที่กว้าง 570 ได้ผลเท่าเดิมแต่ไม่ตัดแข็งเมื่อจอแคบ) | seed `guides.json` `section` |
**สถานะ 2 — หลังสุ่ม · ยุบ (ชื่อ + คำ 3 คำ + Read more)** `Desktop - 1637` = 1440×585 → section สูง **585** (= 40 + 360 + 50 + 95 + 40)
| ชิ้น | พิกัด | ค่า |
|---|---|---|
| การ์ดบนแถว intro | `[790,161.5 120×190]` | **เปลี่ยนจากการ์ด ? เป็นภาพการ์ดของสัตว์ที่สุ่มได้** (ภาพเดียวกับ `card_image` ใน guides.json · เปลี่ยนมาจาก `c632e52f` เป็นเช่น `f8162319`) ตำแหน่ง/ขนาดเดิม · กดได้ = **สุ่มใหม่** (D48) |
| ก้อนผล `Frame 114515` | `[0,450 1120×95]` | แนวตั้ง · gap **10** |
| ชื่อการ์ด | `[0,450 1120×28]` | เช่น "The Chicken & Rooster" Komet-Heavy **30/800/lh 28** กึ่งกลาง `#212529` (ชื่อตรง `guides.json` `cards[].name`) |
| คำ 3 คำ | `[0,488 1120×25]` | เช่น "Renewal · Homeliness · Abundance" Komet **20/700/lh 25** กึ่งกลาง `#212529` |
| Read more | `[0,523 1120×22]` | "Read more >>" Komet **16/500/lh 22** `#212529` · **กึ่งกลาง** (ข้อความกว้าง 102 ที่ x 509–611) · ใช้ component Read more §3.4 ได้ (พฤติกรรม/hover เดียวกัน: ตัวหนา 700 + underline `#D65F47`) |
**สถานะ 3 — ขยาย (Read more → Hide)** `Desktop - 1635`/การ์ด 17 ใบ → section สูง = **40 + 360 + 50 + H + 40** เมื่อ **H = R + 160** (R = ความสูงข้อความ = บรรทัด × 23 · แต่ละการ์ดต่างกัน: seed `cards[].expanded_block_h` = H · 666 (ไก่) · 597 (ฮอร์นบิล · การ์ด 3) · … · 919 (มังกร) · section 1087–1409)
| ชิ้น | พิกัด (ก้อนผลเริ่ม y=450) | ค่า |
|---|---|---|
| ก้อนผล `Frame 114514` | `[0,450 1120×H]` | แนวตั้ง gap **15** (ต่างจากสถานะยุบ 10) |
| ชื่อ | `[0,450 1120×28]` | เหมือนสถานะ 2 |
| คำ 3 คำ | `[0,493 1120×25]` | เหมือนสถานะ 2 (ห่างชื่อ 15) |
| แถวเนื้อหา `Frame 114515` | `[0,533 1120×R]` | แนวนอน gap **20** · **การ์ดใหญ่ซ้าย + ข้อความขวา** |
| ช่องการ์ด `Frame 114517` | `[0,533 360×R]` | ภาพการ์ด **`[65, 533+(R−363)/2, 230×363]`** (กึ่งกลางช่อง 360 · กึ่งกลางแนวตั้งของแถว) · **ภาพเดียวกับการ์ดเล็ก** (FILL cover ที่ 230×363) · มุมมน/ขอบอยู่ในภาพ · **คลิกการ์ดใหญ่ = ไปหน้า trail ของสัตว์** (D48) `[P]` ดู P-275 |
| ข้อความเนื้อหา | `[380,533 740×R]` | Komet **20/500/lh 23** **justified** สี `#212529` · `\n\n` = ย่อหน้า (เว้น 1 บรรทัดว่าง) · `\n` เดี่ยว = ขึ้นบรรทัดที่ตั้งใจ (`<br>` · เช่น ไก่/นก: บรรทัดนำ "…thousands of years." แยก) · ตัวเอียง `<em>` เฉพาะ **dragon** ("long" 2 ที่) · ข้อความอยู่ใน `guides.json` `cards[].body` / `body_html` · ⚠ lh 23 (frame การ์ด) ≠ 25 (ตัวอย่าง `3339:6035`) → ใช้ **23** (frame การ์ด = ต้นฉบับของเนื้อหา) |
| FOLLOW | `[380,533+R+15 740×25]` | **"→ FOLLOW THE <NAME> TRAIL"** Komet **20/700/lh 25** ชิดซ้าย `#212529` · ลิงก์ → หน้า trail (`trail_slug`) · hover ตัวหนา+underline `#D65F47` `[P]` ตาม §3.4 · ข้อความต่อสัตว์ตรง `cards[].follow_text` (⚠ seashell ต้นฉบับสะกด "INVERTABRATES" → คัดลอกตรง · P-272) |
| Hide | `[0,533+R+55 1120×22]` | "Hide >>" Komet **16/500/lh 22** **ชิดขวา** (ข้อความ x 1062–1120) `#212529` · คลิก = กลับสถานะ 2 |
**สีการ์ด/ไอคอน:** ภาพการ์ด = raster ใบเดียว (สี + ไอคอนสัตว์ฝังในภาพ · 1469×2392) ไม่มี text/vector แยก · สีพื้นการ์ด 4 กลุ่ม (ตัวอย่างพิกเซลจากภาพ · `cards[].card_color`): **green `#428B75`** (ไก่ · นก · ฮอร์นบิล · ฮัมสา/นกน้ำ) · **red `#E6553D`** (ครุฑ · ฟีนิกซ์ · มังกร · นาค) · **mustard `#C2BB83`** (ควาย · ช้าง · ม้า · กวาง · สิงโต) · **purple `#B37092`** (มกร · เต่า · ปลา · หอย) = กลุ่มสีไอคอน trail §2.1 · ไฟล์ `design/assets/pages-1117/guides/<slug>.png` + `_card-back.png`
**17 การ์ด ↔ trail (slug ตาม `trails.json`):** rooster ("The Chicken & Rooster") · bird · hornbill · water-bird ("The Hamsa & Water Birds") · garuda · phoenix · dragon · naga ("The Naga & Snake") · buffalo · elephant · horse · deer · lion · makara · turtle · fish ("The Fish & Aquatic Mammals") · seashell ("The Seashell & Marine Invertebrates") — **ครบ 17 · ทุกใบมี trail จริง · ไม่มี trail ที่ไม่มีการ์ด** (Etc. ไม่ใช่ trail) · ชื่อการ์ด ≠ `trails.json` `name` ("Rooster", "Water bird" …) → ใช้ **ชื่อการ์ดจาก Figma** ในส่วนนี้เท่านั้น
**พฤติกรรม (Figma ไม่มี prototype ของการสุ่ม — มีแค่ hover ของ Read more `886:1824` · ทั้งหมดนี้ `[C]` D48 หรือ `[P]`):**
1. **โหลดหน้า = สถานะ 1 เสมอ** (ไม่จำผลเดิม · ไม่เก็บ localStorage) `[C]` D48
2. **คลิกการ์ด ? หรือ "CHOOSE A CARD"** → สุ่ม 1 จาก 17 · ไปสถานะ 2 (ยุบ) `[C]`
3. **คลิกการ์ดเล็กบนแถว intro (ตอนมีผล) หรือ CHOOSE A CARD ซ้ำ → สุ่มใหม่** · ⛔ ไม่ซ้ำตัวที่แสดงอยู่ (สุ่มจาก 16 ที่เหลือ) · กลับสถานะ 2 ทุกครั้ง (ยุบ) `[C]` + ยุบ `[P]` · ใช้ `crypto.getRandomValues` (ไม่ใช่ Math.random ที่ซ้ำง่าย) `[P]`
4. **Read more >>** → ขยาย (สถานะ 3) · **Hide >>** → ยุบ (สถานะ 2) · ข้อความปุ่มสลับ · ใช้ common read-more (§3.4: toggle label · hover 700 + underline `#D65F47`) **ได้** — ต่างแค่ตำแหน่ง (ยุบ=กึ่งกลาง · ขยาย=ชิดขวา) → ตัวแปร CSS · ไม่ต้องมี component ใหม่
5. **FOLLOW หรือการ์ดใหญ่ → หน้า trail** ของสัตว์นั้น (`/trail/<slug>/` ตามที่ธีมมี · D49 ลิงก์ได้ทุก 17) `[C]`
6. **animation `[P]` (Figma ไม่กำหนด):** (ก) สุ่ม: การ์ดเล็กพลิก `rotateY` 0→90° (0.2s ease-in) สลับภาพ แล้ว 90→0° (0.2s ease-out) · ก้อนผลเลื่อนเปิด `max-height`/`opacity` 0→1 ใน **0.3s ease-in-out** (เท่าเวลา Read more §3.4 ใน Figma) (ข) ขยาย/ยุบ: ความสูง section 0.3s ease-in-out · การ์ดใหญ่ fade-in 0.3s (ค) `prefers-reduced-motion: reduce` = ไม่พลิก ไม่เลื่อน สลับทันที (ง) hover การ์ด (เล็ก/ใหญ่): ยก `translateY(-4px)` + Drop Shadow/200 0.2s `[P]` · focus ring ตามปกติ
7. **a11y `[P]`:** การ์ด ?/เล็ก = `<button>` (`aria-label` "Pick a card" / "Pick another card") · CTA เป็น `<button>` · บริเวณผล `aria-live="polite"` · Read more/Hide = `<button aria-expanded>` · FOLLOW เป็น `<a>`
8. **ไฟล์ภาพ:** โหลดเฉพาะการ์ด ? ตอนแรก · ภาพ 17 ใบ preload ตอน idle หรือเมื่อ hover CTA (ใบละ ≈ 55–90 KB ที่ 1469×2392 → ให้ media-dev ทำ 2 ขนาด 240×380 / 460×726 @2x) `[P]`
**ความสูง section ต่อสถานะ (ใช้ตรวจ):** 1 = **440** · 2 = **585** · 3 = **490 + H** (H = `expanded_block_h` ต่อการ์ด 597–919) → ไก่ H=666 = **1156** ✓ (ตรง Figma `3339:5493`) · ฮอร์นบิล 597 = **1087** ✓ · มังกร 919 = **1409** ✓

### 9.4 Introduction ใหม่ 4 ตอน (D50 · BACKLOG #29) — Figma section `3339:6117` "Introduction (แก้ไขใหม่)" · 4 frame 1440 กว้าง: ตอน 1 `3339:6118` (1440×4356) · ตอน 2 `3339:6155` (×5317) · ตอน 3 `3339:6192` (×5183) · ตอน 4 `3339:6228` (×3813) · ภาพ 1x `1x-intro1…4-3339-6118/6155/6192/6228.png` · seed `content/seed/intro-2026-11.json` (ข้อความ/ภาพ/พิกัดครบ)
**หลักการ D50:** หน้าเดียว · ส่วนที่คงเดิมทุกตอน (ตรวจตัวอักษรครบทุกตัวแล้ว: header ส้ม + หัวข้อ "Southeast Asians and the Environment") = ข้อ A–B · ส่วนที่เปลี่ยนตามตอน = **แผนที่ + ส่วนเนื้อหาด้านล่าง** (ข้อ C–D) · URL `#1`–`#4` · ทับ §4.2 ทั้งหมด (โครง "Introduction block + Knowing SEA + Map slider + popup" เดิม **เลิกใช้** — หน้าใหม่ไม่มี cover/ย่อหน้า Introduction/Knowing SEA/popup แผนที่)
**A. Header (§9.1) `[0,0 1440×150]`** · ไม่มีเส้นล่าง · Navigator ไม่มี
**B. ส่วนหัวสีส้ม `Heritage introduction` `[0,150 1440×1098]` fill `#DD7F6A` (คงเดิมทุกตอน)**
| ชิ้น | node | พิกัด [x,y w×h] | ค่า |
|---|---|---|---|
| emblem (ลายมังกรวงกลม) | `3339:6123` | `[160,216 325.2×320.5]` | ภาพ `dc039836` (2617×2486) STRETCH crop x .109 y .108 w .756 h .784 · = Drive `1.1 People and Place/1.1People and Place-01.png` (ไฟล์เดียวกับ `Intro logo.png` ของตอน 2/3) |
| หัวข้อใหญ่ | `3339:6124` | `[541,334 739×104]` | "Across Southeast Asia, creatures travel\nwhere borders cannot…" (ตัดบรรทัดตามต้นฉบับ 2 บรรทัด) Komet-Heavy **40/800/lh 51.85** สี `#F4F1E6` ชิดซ้าย |
| ย่อหน้า 1–4 | `3339:6126/6127/6128/6129` | `[160,572.1 1120×78]` · `[160,690.1 ×130]` · `[160,860.1 ×156]` · `[160,1056.1 ×52]` (ห่างกัน 40) | Komet-Medium **20/600/lh 26** สี `#FFF9EF` **justified** · ข้อความตรง seed `header.paragraphs[]` |
| เครดิต | `3339:6130` | `[160,1148.1 1120×29]` | "Presented by the UNESCO Regional Office in Bangkok with the generous support of Temasek Foundation." Komet-Regular **20/500/lh 28.28** `#FFF9EF` ชิดซ้าย |
| ตัวชี้ (3 ชิ้น) | `6131/6132/6133` | markers `[40.1,503.5 28.3×82.5]` · scroll `[1388.2,331.5 25.9×82.5]` · continue-down `[700,1236.5 42.4×30.6]` | **ภาพครอปจาก screenshot ทั้งหน้า** (fill `0e54c12c` 4000×13102) ไม่ใช่ element จริง · ไม่มี interaction · ส่งออก PNG 2x: `design/assets/pages-1117/intro-icons/` (พื้นส้มฝังในภาพ) → **P-273** (ค่าเริ่มต้น `[P]`: แสดง "continue down" เป็นปุ่มเลื่อนลงไปที่แผนที่ · markers/scroll indicator ไม่แสดง จนกว่าเจ้าของยืนยัน) |
**C. หัวข้อ + แผนที่ (โครงคงเดิม · เนื้อเปลี่ยนตามตอน)**
| ชิ้น | พิกัด | ค่า |
|---|---|---|
| หัวข้อ `Environment heading` | `[0,1248 1440×229]` · ข้อความ `[160,1309 1120×107]` | "Southeast Asians and the Environment:\nRelationships Between People, Places, and Landscapes" Komet-Heavy **40/800/lh 53.03** สี `#403B2F` ซ้าย (2 บรรทัดตั้งใจ) · ตรงกันทั้ง 4 ตอน |
| แผนที่ | `[0,1477 1440×808.4]` (ตอน 1) / `1440×809` (ตอน 2–4) เต็มความกว้างจอ | ตอน 1 = **GIF เคลื่อนไหว** (fill `6d949ac7` 8000×4500 = ภาพนิ่ง/poster) FILL · ตอน 2–4 ภาพนิ่ง FIT 8000×4500 (16:9 พอดี — ไม่มีขอบเว้น) · ไฟล์ตามตารางตอน |
| ปุ่มเลขตอน 1–4 | `Map pages` `[1188,1507 212×44]` → **44×44 ×4 · gap 12** x = 1188 / 1244 / 1300 / 1356 · y = แผนที่ + 30 | วงกลม radius 23 · stroke **`#403B2F` 1.5** · พื้น active **`#D1CC5C`** / inactive **`#DEDCC3`** · ตัวเลข Komet-Regular **22/500/lh 44.8** `#403B2F` กึ่งกลาง · ปุ่ม active = ตอนปัจจุบัน (ตอน n → ปุ่ม n) · **คลิก = สลับตอน** · ⚠ ไม่ใช่ปุ่ม 34×34 coral ใน §2.4/§4.2 เดิม (แทนที่) |
| ลูกศร ‹ › | `Previous map` `[35.4,1839.9 28.3×56.6]` · `Next map` `[1381.1,1839.9 28.3×56.6]` | ไอคอน chevron 17.4×30 ดำ (`svg/map-next-3339-6140.svg` · ซ้าย = กลับด้าน) · **ตอน 1 ซ่อน ‹ (`visible:false`) · ตอน 4 ซ่อน ›** → `visibility:hidden` (ไม่โฟกัส/ไม่คลิก) · คลิก = ตอนก่อน/ถัดไป |
**D. ส่วนเนื้อหาของตอน (เริ่ม y ≈ 2285–2286 · พื้นขาว ยกเว้นที่ระบุ)** — พิกัดทุกชิ้น = ค่าจาก Figma ที่ frame 1440 (คัดจากสคริปต์ `design/tools/build-intro-pages1117.py` อ่าน `figma-file.json` ไม่พิมพ์มือ) · "crop" = สัดส่วนของภาพต้นฉบับที่ Figma แสดงจริง {x,y,w,h เป็นเศษ 0..1 · ค่าติดลบ/>1 = ภาพเล็กกว่ากล่อง มีขอบว่าง} · วิธีทำใน CSS: กล่อง `overflow:hidden; position:relative` + `<img>` `position:absolute; width:calc(100%/w); left:calc(-100%·x/w); top:calc(-100%·y/h)` (ภาพ/วิดีโอสัดส่วนเดิม) · ภาพ FILL = `object-fit: cover` กึ่งกลาง · FIT = `contain`

**ตอน 1 `people-and-places` (#1) — Figma `3339:6118` (1440×4356)** · ชื่อหัวข้อ (ตัวอักษรตรง): **People and Places** · สีหัวข้อ `#D85A40` · แผนที่: `3339:6137` → ไฟล์ `1.1 People and Place/map1.1 people place.gif` (GIF เคลื่อนไหว) · ปุ่มที่ active = **1** · ลูกศร ‹ **ซ่อน** · › แสดง
| ชิ้น | node | พิกัด [x,y w×h] (เทียบ frame) | รายละเอียด |
|---|---|---|---|
| แผนที่ | `3339:6137` | [0,1477.0 1440×808.4] | FILL · box 1440.0×808.4 · crop {'x': 0, 'y': 0.001, 'w': 1, 'h': 0.998} |
| **section `People and Places`** | `3339:6141` | [0,2285 1440×1368] | พื้น `#FFFFFF` |
| ข้อความ หัวข้อ | `3339:6143` | [160.0,2386.1 548.0×57.0] | Komet 38.0/800 lh56.56 left `#D85A40` · "People and Places…" |
| ภาพ | `3339:6145` | [747.0,2386.5 532.6×351.2] | FILL · `1.1 People and Place/1.1People and Place-02.png` · crop {'x': 0, 'y': 0.0548, 'w': 1, 'h': 0.8904} · fill `f08c8c90` |
| ตัวชี้เลื่อน (decor) | `3339:6148` | [1418.5,2382.0 25.9×84.8] | ภาพครอปจาก screenshot · `intro-icons/section-scroll-indicator-3339-6148.png` · P-273 |
| ข้อความ เนื้อหา | `3339:6144` | [160.0,2475.1 548.0×198.0] | Komet 20.0/500 lh33.0 justified `#343A40` · "Southeast Asia’s history is defined by…" |
| ภาพ (GIF) | `3339:6146` | [160.0,2839.5 1120.0×448.0] | STRETCH · `1.1 People and Place/Hae Nang Maew.gif` · crop {'x': 0.0, 'y': 0.1442, 'w': 1.0, 'h': 0.7111} · fill `64338c81` |
| ข้อความ เนื้อหา | `3339:6147` | [160.0,3357.5 1120.0×132.0] | Komet 20.0/500 lh33.0 justified `#343A40` · "Rather than trying to dominate nature,…" |
| **section `Living heritage`** | `3339:6149` | [0,3653 1440×703] | พื้น `#7EB7A6` |
| ภาพ (GIF) | `3339:6150` | [160.0,3557.5 1120.0×156.0] | STRETCH · `1.1 People and Place/Boat.gif` · crop {'x': 0.0, 'y': 0.3849, 'w': 1.0, 'h': 0.2302} · fill `74a3cb7f` |
| ภาพ | `3339:6151` | [132.0,3816.0 434.0×369.0] | STRETCH · `1.1 People and Place/1.1People and Place-03.png` · crop {'x': 0.0718, 'y': 0.1218, 'w': 0.9002, 'h': 0.8057} · fill `39c9ec27` |
| ข้อความ เนื้อหา | `3339:6153` | [599.0,3816.0 681.0×196.0] | Komet 20.0/500 lh32.52 justified `#343A40` · "Museum collections preserve the materi…" |
| ข้อความ เนื้อหา | `3339:6154` | [599.0,4087.0 681.0×131.0] | Komet 20.0/500 lh32.52 justified `#343A40` · "From the forests of mainland Southeast…" |

**ตอน 2 `early-centres-of-power` (#2) — Figma `3339:6155` (1440×5317)** · ชื่อหัวข้อ (ตัวอักษรตรง): **Early Centres of Power** · สีหัวข้อ `#43695D` · แผนที่: `3339:6174` → ไฟล์ `1.2 Early Centres of Power/1.2 power.png` (ภาพนิ่ง) · ปุ่มที่ active = **2** · ลูกศร ‹ แสดง · › แสดง
| ชิ้น | node | พิกัด [x,y w×h] (เทียบ frame) | รายละเอียด |
|---|---|---|---|
| แผนที่ | `3339:6174` | [0,1477.0 1440×809.0] | FIT · box 1440.0×809.0 · crop None |
| **section `Early centres of power`** | `3339:6178` | [0,2286 1440×1083] | พื้น `#FFFFFF` |
| ภาพ | `3339:6179` | [160.0,2386.0 1120.0×302.0] | STRETCH · `1.2 Early Centres of Power/1.1People and Place-05.png` · crop {'x': 0.0153, 'y': 0.199, 'w': 0.9691, 'h': 0.6746} · fill `1cb4caf4` |
| ข้อความ หัวข้อ | `3339:6181` | [160.0,2771.1 1120.0×54.0] | Komet 38.0/800 lh54.0 center `#43695D` · "Early Centres of Power…" |
| ข้อความ เนื้อหา | `3339:6182` | [160.0,2875.1 1120.0×96.0] | Komet 20.0/400 lh32.0 justified `#343A40` · "Over time, small river and coastal set…" |
| ข้อความ เนื้อหา | `3339:6183` | [160.0,3021.1 1120.0×248.0] | Komet 20.0/400 lh31.0 justified `#343A40` · "Natural transit routes made this growt…" |
| **section `Temple connections`** | `3339:6184` | [0,3369 1440×824] | พื้น `#FFFFFF` |
| ภาพ | `3339:6186` | [160.0,3469.0 1120.0×344.0] | STRETCH · `1.2 Early Centres of Power/1.1People and Place-06.png` · crop {'x': -0.0794, 'y': 0.183, 'w': 1.1564, 'h': 0.7685} · fill `a62481c8` |
| ข้อความ เนื้อหา | `3339:6187` | [160.0,3933.0 1120.0×160.0] | Komet 20.0/400 lh32.0 justified `#343A40` · "The monumental architecture of Southea…" |
| **section `Sacred landscapes`** | `3339:6188` | [0,4193 1440×1124] | พื้น `#FFFFFF` |
| ภาพ | `3339:6190` | [160.0,4293.0 1120.0×580.0] | STRETCH · `1.2 Early Centres of Power/1.1People and Place-07.png` · crop {'x': -0.6263, 'y': 0.025, 'w': 2.2525, 'h': 0.9609} · fill `648a787a` |
| ข้อความ เนื้อหา | `3339:6191` | [160.0,4993.0 1120.0×224.0] | Komet 20.0/500 lh32.0 justified `#343A40` · "The regional networks connecting South…" |

**ตอน 3 `rice-and-spice` (#3) — Figma `3339:6192` (1440×5183)** · ชื่อหัวข้อ (ตัวอักษรตรง): **Rice and Spice: Platforms of Interconnection** · สีหัวข้อ `#887A61` · แผนที่: `3339:6211` → ไฟล์ `1.3 Rice and Spice_ Platforms of Interconnection/Map 1.3 Environment.jpg` (ภาพนิ่ง) · ปุ่มที่ active = **3** · ลูกศร ‹ แสดง · › แสดง
| ชิ้น | node | พิกัด [x,y w×h] (เทียบ frame) | รายละเอียด |
|---|---|---|---|
| แผนที่ | `3339:6211` | [0,1477.0 1440×809.0] | FIT · box 1440.0×809.0 · crop None |
| **section `Rice and spice`** | `3339:6215` | [0,2286 1440×1237] | พื้น `#FFFFFF` |
| ข้อความ หัวข้อ | `3339:6216` | [160.0,2386.0 1120.0×55.0] | Komet 38.0/800 lh55.0 center `#887A61` · "Rice and Spice: Platforms of Interconn…" |
| ภาพ (GIF) | `3339:6217` | [160.0,2541.0 1120.0×489.0] | STRETCH · `1.3 Rice and Spice_ Platforms of Interconnection/1.3 Rice and Spice-farmer.gif` · crop {'x': -0.043, 'y': 0.0762, 'w': 1.085, 'h': 0.8422} · fill `832a2e1a` |
| ข้อความ เนื้อหา | `3339:6219` | [160.0,3097.0 1120.0×64.0] | Komet 20.0/500 lh32.0 justified `#343A40` · "Rice is a sacred source of food for pe…" |
| ข้อความ เนื้อหา | `3339:6220` | [160.0,3231.0 1120.0×192.0] | Komet 20.0/500 lh32.0 justified `#343A40` · "Rice cultivation depended on fertile l…" |
| **section `Pawangs and rice rituals`** | `3339:6221` | [0,3523 1440×1060] | พื้น `#FFFFFF` |
| ภาพ | `3339:6222` | [731.0,3563.0 548.0×501.0] | STRETCH · `1.3 Rice and Spice_ Platforms of Interconnection/PAWANG.png` · crop {'x': -0.1979, 'y': 0.0763, 'w': 1.1669, 'h': 0.8985} · fill `dad6bba0` |
| ข้อความ เนื้อหา | `3339:6223` | [160.0,3767.0 549.0×256.0] | Komet 20.0/400 lh32.0 justified `#343A40` · "Pawangs spent their lives clearing for…" |
| ข้อความ เนื้อหา | `3339:6224` | [160.0,4157.0 1119.0×96.0] | Komet 20.0/500 lh32.0 justified `#343A40` · "In Brunei Darussalam, the durung, a tr…" |
| ข้อความ เนื้อหา | `3339:6225` | [160.0,4313.0 1119.0×160.0] | Komet 20.0/500 lh32.0 justified `#343A40` · "Across Southeast Asia, animals serve a…" |
| **section `Harvest illustration`** | `3339:6226` | [0,4583 1440×600] | พื้น `#FFFFFF` |
| ภาพ | `3339:6227` | [0.0,4627.0 1440.0×556.0] | FIT · `1.3 Rice and Spice_ Platforms of Interconnection/Chicken1.3.png` · crop None · fill `b5179331` |

**ตอน 4 `animals-folklore-and-ecological-stewardship` (#4) — Figma `3339:6228` (1440×3813)** · ชื่อหัวข้อ (ตัวอักษรตรง): **Animals, Folklore, and Ecological Stewardship** · สีหัวข้อ `#3F4036` · แผนที่: `3339:6247` → ไฟล์ `1.4 Animals, Folklore, and Ecological Stewardship/map1.4.jpg` (ภาพนิ่ง) · ปุ่มที่ active = **4** · ลูกศร ‹ แสดง · › **ซ่อน**
| ชิ้น | node | พิกัด [x,y w×h] (เทียบ frame) | รายละเอียด |
|---|---|---|---|
| แผนที่ | `3339:6247` | [0,1477.0 1440×809.0] | FIT · box 1440.0×809.0 · crop None |
| **section `Animals, folklore and ecological stewardship`** | `3339:6251` | [0,2286 1440×1527] | พื้น `#FFFFFF` |
| ข้อความ หัวข้อ | `3339:6252` | [160.0,2386.0 1120.0×55.0] | Komet 38.0/800 lh55.0 left `#3F4036` · "Animals, Folklore, and Ecological Stew…" |
| ข้อความ เนื้อหา | `3339:6253` | [160.0,2487.0 1120.0×160.0] | Komet 20.0/500 lh32.0 justified `#343A40` · "Across Southeast Asia, human culture a…" |
| ข้อความ เนื้อหา | `3339:6254` | [160.0,2737.0 1120.0×192.0] | Komet 20.0/500 lh32.0 justified `#343A40` · "In oral literature and cosmological tr…" |
| ข้อความ เนื้อหา | `3339:6255` | [160.0,3019.0 1120.0×224.0] | Komet 20.0/500 lh32.0 justified `#343A40` · "Beyond actual species, mythical beings…" |
| ภาพ | `3339:6256` | [160.0,3333.0 1120.0×380.0] | STRETCH · `1.4 Animals, Folklore, and Ecological Stewardship/1.1Animals.png` · crop {'x': 0.0664, 'y': 0.0837, 'w': 0.8673, 'h': 0.8411} · fill `33c0fb7a` |

**E. พฤติกรรมสลับตอน (D50 · Figma ไม่มี prototype ของ Intro ใหม่ — มีแค่ "Main Page → Home" ในเมนู · ที่เหลือ `[C]` D50 หรือ `[P]`)**
1. หน้าเดียว `/introduction/` · ตอนปัจจุบัน = ปุ่มที่ active · **โหลดหน้าด้วย `#1`–`#4` → แสดงตอนนั้นตรง ๆ** · ไม่มี hash / hash ไม่ถูกต้อง → ตอน 1 `[C]`
2. **คลิกปุ่ม 1–4 หรือ ‹ ›** → เปลี่ยน **แผนที่ + เนื้อหาด้านล่าง (ข้อ D)** ในที่ · header สีส้ม/หัวข้อ/ปุ่ม/ลูกศรไม่โหลดใหม่ · อัปเดต hash ด้วย `history.replaceState` (ไม่เพิ่มรายการ history `[P]`) · ฟัง `hashchange` (ย้อน/แชร์ลิงก์) `[C]`
3. animation `[P]`: cross-fade 0.4s ease-in-out (เท่า dissolve ของ page nav §2.4) ทั้งแผนที่และเนื้อหา · ความสูงหน้าเปลี่ยนตามตอน (4356 / 5317 / 5183 / 3813) · **ไม่เลื่อนหน้ากระโดด** — ถ้าผู้ใช้อยู่ล่างกว่าแผนที่ เลื่อนกลับให้ขอบบนแผนที่อยู่ใน viewport (`scrollIntoView({block:'start'})` + `scroll-margin-top` = ความสูง header)
4. GIF/วิดีโอ (ตอน 1: แผนที่ · Hae Nang Maew · Boat · ตอน 3: farmer) = `<video autoplay muted loop playsinline>` จาก media-1117 + `poster` = ภาพนิ่ง Figma (`figma_fill_file`) · `prefers-reduced-motion` = แสดง poster เฉย ๆ · โหลดเฉพาะตอนที่แสดง (lazy) `[P]`
5. ลูกศร/ปุ่มกด a11y: `role="tablist"` ปุ่ม 1–4 `role="tab" aria-selected` · เนื้อหา `role="tabpanel"` · ‹ › = `<button aria-label="Previous chapter|Next chapter">`
6. **Responsive `[P]`:** ≥1280 ตามพิกัด ×(W/1440) สำหรับแผนที่เต็มกว้าง · 768–1279 คอลัมน์คู่ (ตอน 1 "People and Places"/ตอน 3 "Pawangs") เรียงซ้อน · ภาพ crop คงสัดส่วน กว้าง 100% · ข้อความ 20px lh 32 คงเดิม (justified → left ที่ <768) · <768 ฟอนต์หัวข้อ 38→28 · ปุ่มเลข 44→36 · แผนที่สูง auto (16:9)
**ข้อมูลที่ขาด/ไม่ตรง → P:** ตัวชี้เลื่อน (P-273) · "ภาพ GIF ไม่มีใน Figma นอกจากเฟรมแรก" (ไม่ใช่ปัญหา — Drive มีครบ 18 ไฟล์ จับคู่ได้ทั้งหมด: PNG/JPG ตรง sha256 กับ fill 14 ไฟล์ · GIF 4 ไฟล์ = เวอร์ชันเคลื่อนไหวของ fill ภาพนิ่ง 4 ใบ · `Intro logo.png` ×2 + `…-01.png` เป็นไฟล์เดียวกัน = emblem) · ไม่มีภาพ Intro ที่ Drive ไม่มี

### 9.5 Zone 4 (หน้า timeline) — feedback ลูกค้า D51 (BACKLOG #31 #32) · เขียนก่อนให้ `tl-1117` อ่าน
**ขอบเขต:** แก้เฉพาะ (ก) ข้อความ metadata ใต้เส้น (ข) แถบช่อง trail 17 ช่องซ้ายบน · ที่เหลือใน §4.5c/§6.4c **ไม่เปลี่ยน** (stage 1440×723 · เส้นเวลา · mini-map (เดิม 639→1413 สูง 65 — **ย่อตาม D52 ดู (ข)**) · ลูกศร · Back · ภาพใต้เส้น) · Figma `3101:609` **ไม่มีค่าใหม่สำหรับสองข้อนี้** (ตรวจ: `Frame trails` `3101:738` ยังเป็นไอคอน 35×35 pitch 40 active 45 `[N]` — ดีไซน์ Figma ไม่ได้ทำตาม D51) → ค่าด้านล่างมาจาก D51 + ค่าเดิมของสเปก `[C]`/`[P]` · **ไม่มีภาพเจ้าของ 10:49–10:52 อยู่ในไฟล์** (อยู่ในแชท) → ค่าที่ไม่ใช่ข้อความ D51 ตรง ๆ ทำเป็น CSS variable ให้ปรับทีเดียวได้ · ~~P-270~~ ปิด → D52

**(ก) metadata ใต้เส้น (D51 ข้อ 1 · แทนค่า 14/18.6 กว้างสูงสุด 442 ใน §4.5c.1 แถว "metadata")**
| รายการ | ค่า | ที่มา |
|---|---|---|
| ตัวอักษร | Komet **18px** · **line-height 24** (= token Caption 18/24 §2.2) · letter-spacing 0 · สี `#212529` · ชิดซ้าย (ไม่ justify) | 18px `[C]` D51 · lh 24 `[P]` (ตาม token) |
| หัวข้อแถว | ตัวหนา **Komet-Bold 700** ตั้งแต่ต้นแถวถึง **":" ตัวแรก** (รวม ":") เช่น **`Period:`** · **`Place of Production / Findspot:`** · **`Dimensions:`** (คำ "Length:"/"Width:" ที่อยู่ในค่า = ตัวปกติ) · **`Specimen / Object Biography:`** · ส่วนหลัง ":" = Komet-Regular 500 | `[C]` D51 "หัวข้อแต่ละแถวตัวหนาถึง ':'" (ตามภาพเจ้าของ Elephant Leg Bone) |
| การไหลของข้อความ | **1 ฟิลด์ = 1 แถว (block) · ภายในแถวไหลเต็มความกว้างแล้วค่อยตัดบรรทัดเอง** — ⛔ ไม่ใส่ `<br>` กลางแถว · ⛔ ไม่ใช้ `max-width` แคบ/`text-wrap: balance`/`pretty` · ⛔ ไม่แยกหัวข้อกับค่าเป็นคนละบรรทัด/คอลัมน์ · ⛔ ไม่ hyphenate · `white-space: normal` · `overflow-wrap: normal` (ใช้ `anywhere` เฉพาะกันคำเดี่ยวยาวเกินกล่อง) · ขึ้นแถวใหม่เฉพาะระหว่างฟิลด์ · ระยะระหว่างแถว 0 (ติดกันที่ lh 24) | `[C]` D51 · ระยะ `[P]` |
| ความกว้างคอลัมน์ | เริ่ม **x = 907** (เดิม) · **ขวาสุด x = 1412** (= ปลายเส้นเวลา §4.5c.1 · กว้าง **505** — เดิม 442 ถึง 1349) | `[P]` — ลูกศร › อยู่ y_rel 238–286 ไม่ทับ metadata (y ≥ 496) · ขยาย 442→505 ให้ตัดบรรทัดน้อยลงที่ 18px · ทุกค่า ×k (§4.5c.5) |
| ตำแหน่งแนวตั้ง | line-box บรรทัดแรกบน **496** (เท่าเดิม) · สูง = n × 24 | `[P]` |
| ชุดฟิลด์/ลำดับ/แถวว่าง | ตามเดิมทุกอย่าง (D42(2): ทุกฟิลด์ที่มีค่า · ข้ามค่าว่าง · ไม่แสดง "SECTION") | เดิม |
| ความสูงเมื่อข้อความยาว | ประมาณจาก seed `timeline-elephant.json` ที่ 505 กว้าง (Komet ≈ 0.51em/ตัว ≈ 9.2px): ชิ้น 1 ≈ 6 บรรทัด (144px → ล่างสุด 640 ≤ 723 ✓) · ชิ้น 13 Wooden cold-press cake mould (12 แถว) ≈ **20 บรรทัด = 480px → ล่างสุด ≈ 1000 > 723** · ชิ้น 11/15 ≈ 16 บรรทัด → ≈ 904 · **ใช้กฎเดียวกับ Reflection (§4.5c.7 ข้อ 2e): H_meta = max(723, 496 + h_meta + 24)** ความสูงกล่อง stage = 723 + (H_meta − 723)·p (p ตาม §6.4c) · เส้น/ไอคอน/mini-map/Back ไม่ขยับ · เลื่อนหน้าแนวตั้งอ่านต่อ (ยอมรับแบบเดียวกับ Reflection) · **วัดจาก DOM จริง ไม่ใช้ตัวเลขประมาณนี้** | `[P]` → P-271 |
| Responsive | ≥1280: ตามตารางนี้ ×k · **ขนาดตัวอักษร = max(18·k, 14px)** (เดิมพื้น 12px ของ 14px) · 768–1279: metadata ใต้ภาพ กึ่งกลาง กว้าง ≤ 560 ใช้ **18/24** · <768: **16/22** padding 20 (`[P]` ลดเพื่ออ่านบนจอ 390 — D51 พูดถึงเฉพาะเดสก์ท็อป) · ตัวหนาถึง ":" ทุกขนาด | `[P]` |
| ตัวอย่างชิ้น 1 | `Period: 20th-21st century` / `Place of Production / Findspot: Thailand` / `Dimensions: Length: 107 cm, Width: 29 cm` / `Collection: National Museum Bangkok` / `Specimen / Object Biography: Received from the Ministry of Finance, Thailand.` — 4 แถวแรกบรรทัดเดียว (≤ 505) · แถวสุดท้าย 77 ตัวอักษร ≈ 700px → ตัดเองเป็น 2 บรรทัด | คัดลอกตรง seed |

**(ข) แถบช่อง trail 17 ช่อง ซ้ายบน (D51 ข้อ 2 + **D52** · แทนแถว "แถบไอคอน trail ×17" ใน §4.5c.1 · ✅ แทนค่าเสนอเดิม 65 / ×1.286 ที่ P-270 — ปิดแล้ว → D52)**
| รายการ | ค่า | ที่มา |
|---|---|---|
| ความสูง (desktop) | **ทั้งสองฝั่งสูงเท่ากันจริง = 40px** (× k) · ช่องซ้ายสูง **40** · **mini-map (กรอบครีม `#EDEBDA` + รูปวัตถุ) ย่อความสูงเหลือ 40 ตามสัดส่วน (≈ ×0.8 ของ 50 เดิม — กรอบครีมเดิมสูง 50 · กล่อง mini-map 65 ไม่ใช่ตัวเทียบ)** และ**แคบลงเท่าที่จำเป็นให้ 17 ช่องพอดี** (ไม่ทับ/ไม่ชน mini-map) · ถ้า mini-map แน่นจนรูปวัตถุทับกัน → **ลด h ลงทีละ 2px** จนไม่ทับ (ขั้นต่ำ ≈ ขนาดใหญ่สุดที่ใส่ได้โดยไม่แคบลง ~35px) · ผูกด้วย CSS variable เดียว `--tl-strip-h` (ใช้ทั้งแถบซ้ายและกรอบ mini-map) | `[C]` D51(2) + **D52** |
| tablet / mobile | **คงตามที่ `tl-1117` ทำ (เท่ากันแล้ว)**: tablet **50** · mobile **37** | D52 |
| ช่อง | 17 ช่อง แถวเดียว · ลำดับเดิม (Birds · Hornbill · Water Bird · Chicken · Garuda · Phoenix · Dragon · Naga · Elephant · Buffalo · Horse · Deer · Lion · Makara · Turtle · Fish · Seashell — §4.5c.1) · สีพื้นกลุ่ม (green `#178D74` · red `#E7553D` · mustard `#C3BC85` · purple `#B47093`) · ภาพไอคอนเดิมกึ่งกลาง (`contain`) · คลิก → หน้า trail นั้น · ความกว้าง/ช่องไฟตามที่ `tl-1117` ทำให้ 17 ช่องพอดีกับ mini-map ที่ย่อ (ตัวแปร `--tl-strip-cell-w` `--tl-strip-gap`) | `[P]` ความกว้าง |
| hover / focus ช่อง | ช่องขยาย **×1.5** (D52 "คงไว้" — ไม่ใช่ ×1.286) `transform: scale()` origin กึ่งกลาง · z เหนือช่องข้างเคียงและ mini-map · Drop Shadow/200 · 0.3s ease-in-out · ไม่ดัน/ไม่ขยับช่องอื่น · overflow visible · `:focus-visible` = เหมือน hover + ring · `aria-label` + `title` = ชื่อ trail | ×1.5 **D52** · อื่น `[P]` |
| ไม่เปลี่ยน | ไม่มีช่อง "active ใหญ่" (D26) · ลำดับ z (§4.5c.1) | เดิม |
| Responsive อื่น | <768: เลื่อนแนวนอนได้ 1 แถว (ตาม §4.5c.6 เดิม) · hover เฉพาะ `@media (hover:hover)` | `[P]` |

### 9.6 ฟิลด์ที่หน้าใช้ (รอบ 2026-11-17) — ⛔ ผมไม่แก้ CONTENT-MODEL · เสนอให้ core-1117 ยก
| ฟิลด์ (ชื่อเสนอ) | ชนิด | ที่ | node | seed ตัวอย่าง |
|---|---|---|---|---|
| `sh_about_url` + เมนู About the Project (เป็นรายการเมนู ลิงก์ภายนอก · new tab) | url / menu item | options/เมนู (ทุกหน้า) | `3220:3745` | `site.json` `nav_primary_links["About the Project"].url` |
| `sh_logo_header` | image (PNG · crop ตามค่า) | options (ทุกหน้า) | `I3339:5285;3307:1972` | `brand.header_logo` (ไฟล์ `877fd074…png`) |
| `sh_logo_unesco_footer` | image/SVG | options (footer ทุกหน้า) | `3339:5249` | `brand.footer.unesco` |
| `sh_logo_temasek` + `sh_footer_support_text` ("With the Support of") | image + text | options (footer) | `3339:5256` · `3339:5255` | `brand.footer.temasek` |
| `sh_label_hide` ("Hide >>") | text | options (labels) | `I3339:5283;886:1820` | `labels.hide` |
| **การ์ดสัตว์ (ฟิลด์บน trail term · 17 ตัว)** `sh_guide_name` · `sh_guide_words` (3 คำ คั่น " · ") · `sh_guide_body` (ข้อความยาว · `\n\n` ย่อหน้า · `\n` ขึ้นบรรทัด · ตัวเอียงได้) · `sh_guide_follow` (ข้อความลิงก์เต็ม) · `sh_guide_card` (image) | text/textarea/image | trail term (D48 · admin แก้ได้ D1/D10) | `3339:5488` frames | `guides.json` `cards[]` (key = `trail_slug`) |
| **ข้อความหัว section การ์ด** `sh_guide_title` · `_opening` · `_heritage` · `_question` · `_instructions` · `_cta` · `sh_guide_card_back` (image ?) | text/image | options หน้าแรก | `3339:5267…5275` | `guides.json` `section` |
| **Intro 4 ตอน** — ส่วนหัว: `sh_intro_title` · `_p1.._p4` · `_credit` · `_emblem` · `sh_intro_env_heading` · ตอนละชุด (n=1..4): `sh_intro_n_title` · `_accent` (สี) · `_map` (image/วิดีโอ + poster) · `_sections` (repeater: ชนิด text/image/video · พื้นสี · ภาพ+crop · ข้อความ) | text/image/video/repeater | options หน้า Introduction (D50 · admin แก้ได้) | `3339:6117` | `intro-2026-11.json` |
| ตัวแปร layout ที่ไม่ใช่เนื้อหา (ไม่เป็นฟิลด์ admin) | CSS/config | ธีม | — | `--tl-strip-h`/`--tl-strip-cell-w` (§9.5) · `--footer-pad-left` (§9.1) · crop {x,y,w,h} ต่อภาพ Intro |

---
## 10. รอบ 2026-11-17 (fb-1117) — feedback สไลด์ "UNESCO - (Comment Dev)" หน้า 4–8 (BACKLOG #36–#40 · ส่วน A design-dev)
**ที่มา:** Figma `lastModified` **`2026-11-17T11:28:55Z`** · REST 4 คำขอ: files depth=3 · files/nodes ×11 id · images ×8 id · files/images (ดู `design/figma-calls.log` แถว `fb-1117`) · ข้อมูลดิบ `design/figma-fb1117-nodes.json` (11 node เต็มความลึก) · render 1x `design/frames/2026-11-17-fb/` (belt-bird-1509 · belt-phoenix-1574 · makara-1644 · makara-1645 · zone1-1472 · zone2-1473 · zone3-1474 · home-1634) · สคริปต์ `design/tools/fetch-fb1117.py` · `build-fb1117.py` · seed: `objects.json` (3 record) · **ใหม่ `zones-2026-11-17.json`** (ยังไม่มีใครใช้) · ไม่เดา: ที่ไม่ชัด = P-380..387

### 10.1 Belt buckle `pending-3` (สไลด์ข้อ 1 · BACKLOG #36)
| | Bird 1509 `1090:2840` (1440×1865) | Phoenix 1574 `1170:3381` (1440×1923) |
|---|---|---|
| หัวข้อ | `1090:2856` **"Pending (traditional belt buckle)"** | `1170:3386` **เหมือนกัน** (เดิม 03:50Z = "Pending (รอไฟล์)") |
| meta | `1090:2857` 2 แถว: Place of production / Findspot: Malaysia · Collection: Department of Museums Malaysia | `1170:3387` **เหมือนกัน** (เดิม "Collection: The Perak Museum, …") |
| caption | `1090:2858` 5 ย่อหน้า | `1170:3388` **เท่ากันทุกตัวอักษร** (เดิมเป็นข้อความรุ่นเก่า) |
| hero | Frame GIF 16 ภาพ `1096:3682` (ภาพจริงของหัวเข็มขัด · พื้นดำเทา) | `1170:3393` ภาพ GIF ลายเส้นคนใส่เสื้อ+เข็มขัด (ref `505b89e9` · พื้นขาว) · พื้นแผง Bird `#6BB9A5` (เขียว) · Phoenix `#F1ADA0` (ชมพู) |
| ภาพแผงข้าง | `ex 3` `[921,1090 359×257.7]` ref `27791d45` · `ex 5` `[921,1377.7 359×202.4]` ref `45d300e6` | `ex 3` `[921,1170 359×205]` · `ex 5` `[921,1405 359×205]` (ref เดียวกัน) |
| geometry (ใหม่) | แผง `[0,960 1440×949]` · คอลัมน์ข้อความ `[160,1020 739×745]` (เดิม 721) · Back `[160,1810]` (เดิม 1766) · **ขอบ frame ยัง 1865 (< ล่างแผง 1909)** | คอลัมน์ข้อความ 745 (เดิม 768) · Back `[160,1824]` · frame 1923 |
**สรุป:** 2 frame = **ชิ้นเดียวกัน ข้อความตรงกันแล้ว** → record `pending-3` ใช้ข้อความนี้ · **เปลี่ยนเฉพาะหัวข้อ** `Pending (Traditional Belt Buckle)` → **`Pending (traditional belt buckle)`** (ตรง Figma · ตัวพิมพ์เล็ก) · caption/meta ไม่เปลี่ยน (ตรวจเท่ากับ seed ทุกตัวอักษรหลังตัด prefix "Caption:" + nbsp) · **ไม่มี Period/Material/Dimensions/Other information ใน Figma** (ไม่เพิ่มเอง) · **slug `pending-3` คงเดิม** (URL live) · เสนอ `pending-traditional-belt-buckle` → P-381 · seed `objects.json` record `pending-3` `figma_2026_11_17_fb` + ปรับ `figma[].style` (geometry) + ลบ `text_differs_from_primary` ของ Phoenix

### 10.2 Makara 2 หน้า (สไลด์ข้อ 2 · BACKLOG #37)
**พบ:** นักออกแบบ **ใส่ภาพแผงด้านข้างแล้ว** (เปลี่ยนจาก fill ว่าง `285c8b60`) · ข้อความ/hero **ไม่เปลี่ยน** (เทียบ node ทุกตัวกับ 08:33Z: ต่างเฉพาะ `ex 3` + ความสูงคอลัมน์)
| frame | `ex 3` | ref ต้นฉบับ | ภาพ | `ex 5` |
|---|---|---|---|---|
| `3449:3862` Desktop - 1644 (Saltwater crocodile skull) | `3449:3871` `[921,1090 359×489]` FILL · คอลัมน์ `3449:3870` `[921,1020 359×559]` | `91de53b6…` | JPEG 4000×3000 **EXIF orientation 6** (แนวตั้งหลังหมุน · ถ่ายพิพิธภัณฑ์ · หัวกะโหลกจระเข้ในตู้กระจก) | `3449:3872` `visible:false` ref `ff21ec51…` |
| `3449:3951` Desktop - 1645 (Sungai Mas makara) | `3449:3960` `[921,1090 359×291]` FILL · คอลัมน์ `3449:3959` `[921,1020 359×361]` (เดิม 559) | `ea68fcba…` | JPEG 2048×1360 (หินแกรนิตรูปมกร) | `3449:3961` `visible:false` ref `ff21ec51…` (เดียวกัน) |
ทำใน seed: `style.images[0]` = `{bbox, scale_mode FILL, source_image, crop_ref, crop_size, crop_scale 2.0}` · **crop_ref** = ภาพครอปแบบ cover กึ่งกลางที่ 2× (718×978 · 718×582 · หมุน EXIF แล้ว) → `design/assets/objects-1117/<ref>.crop-3449-3871.png` · `….crop-3449-3960.png` (+ ต้นฉบับ `<ref>.jpg`) · ลบ `style.image_slot_empty` + `side_image_slot` + status `side_image_empty_slot_awaiting_designer` (เหลือ `hero_still_only_no_webm`) · `ex 5` ซ่อน → ไม่แสดง (เก็บใน `style.images_hidden`) · hero ลายเส้นจระเข้ `3449:3875` ref `d059fb72` · มกร `3449:3964` ref `1fa10936` **ไม่เปลี่ยน** · ปิด P-306 ข้อ (10) ส่วน "ภาพแผงด้านข้างว่าง" (P-382)

### 10.3 Zone 1/2/3 (สไลด์ข้อ 4 · BACKLOG #39) — ช่องทุกช่องอยู่ที่ `content/seed/zones-2026-11-17.json`
**frame ล่าสุด:** page Final `670:860` · Section 3 `670:1151` · `670:1241` (1472 · 1440×1930) · `670:1296` (1473 · 1440×**3503**) · `670:1349` (1474 · 1440×**5224**) · มีสำเนาใน page `3117:3314` section "Zone 1,2,3" `3140:1403` (`3117:3684` · `3117:3713` ×3535 · `3117:3742` ×5184) — **ลำดับ+ภาพช่องเท่ากันทุกช่อง** ต่างแค่ตำแหน่งปุ่ม Back (ดู P-385) · header bar `Frame 12718/12719/12720` `670:1206/1209/1212` (1502×86 · ไม่ได้ดึงเนื้อใน · ไม่เกี่ยวกับกริด)
**จำนวนช่อง (นับจาก node):** zone 1 = **15** (3 แถว × 5) · zone 2 = **46** (10 แถว · แถวสุดท้าย 1 ช่อง) · zone 3 = **83** (17 แถว · แถวสุดท้าย 3 ช่อง) — สไลด์นับคร่าว ~15/~45/~85 ตรง
**โครง (เทียบ §4.3b · ไม่เปลี่ยน):** Cover `[160,150 1120×410]` · แถบ ZONE `Frame 12684` `[160,580 1120×166]` ภาพ raster (`413791d6` · `e5dbe735` · `172ff28b`) · บล็อกข้อความ `Detail text Zone 1` `[160,826 1120×193/149/193]` **หัวข้อ+ข้อความย่อ+เต็มเท่า seed `zones.json` ทุกตัวอักษร** · กริด `Frame zone1` `3377:4473` `[0,1079 1440×652]` · `Frame zone2` `3454:5615` `[0,1035 1440×2290]` · `Frame zone3` `3454:5354` `[0,1079 1440×3928]` · แถวละ 5 ช่อง 184×184 ที่ x = 160/394/628/862/1096 (pitch **234** = 184+50 ทั้งแนวนอน/แนวตั้ง) · Back B1 `[160,1805]` · `[160,3378]` · `[160,5111]` (กริดล่าง +74 / +53 / +104 — ไม่สม่ำเสมอ)
**ช่อง = วัตถุ (ไม่ใช่ trail):** ตัวอย่าง zone 1 = green-peafowl · common-kingfisher · oriental-pied-hornbill… · greater-mousedeer · domestic-chicken · sunda-pangolin · … · saltwater-crocodile-skull · giant-clams (ตัวอย่างธรรมชาติ) · zone 2/3 = วัตถุมรดก (perminaken · oracle-bone · sungai-mas-makara · pending-3 ×2 · …) — ช่อง **ไม่ใช่ช่อง trail** (กริด trail 5×3 เดิมของ zone 1–3 ถูกแทนด้วยกริดวัตถุ)
**prototype:** ช่อง = `ON_HOVER → CHANGE_TO` (variant hover 210² · SMART_ANIMATE 0.3s ease-in-out) **เท่านั้น · ไม่มี `ON_CLICK`/navigate ไปหน้าวัตถุ หรือหน้าไหนเลย** (นับ 144 ช่อง = 144 `ON_HOVER` · 0 `ON_CLICK` บนช่อง) · "เชื่อมไปหน้าข้อมูลแต่ละตัว" = คำสั่งเจ้าของจากสไลด์ ไม่ใช่ prototype → P-386 · Back B1 = `ON_HOVER` เท่านั้น (→ variant `670:1815`) · ลิงก์ `ON_CLICK` navigate มีแต่ 'Main Page' ใน header · Read more = CLICK `CHANGE_TO` (เหมือน §4.3b)
**จับคู่ช่อง → object slug (เทียบ imageRef ช่อง + ภาพ hover กับ `objects.json` · ยืนยัน ON_CLICK ในหน้า trail):** zone 1 **14/15** · zone 2 **45/46** · zone 3 **83/83** = **142/144** ช่อง (126 = ref ช่อง+hover ตรงกัน · 2 = ref ช่อง · 14 = ช้างผ่าน ON_CLICK ของหน้า trail ช้าง เพราะ seed ไม่เก็บ ref ช่องช้าง) · **จับคู่ไม่ได้ 2 ช่อง:** zone 1 ช่อง 8 `3377:4483` (Buffalo 6 · ผู้สมัคร `sumatran-serow`) · zone 2 ช่อง 42 `3454:5666` (Elephant tail 5 · ผู้สมัคร `embossed-brass-basin-…`) → P-383 (ไม่ใส่ slug) · ref ช่องซ้ำใน zone 2 = 4 คู่ (P-384) · วัตถุ 117 จาก 123 อยู่ในกริดอย่างน้อย 1 ช่อง (ไม่อยู่: `pending`(hold) · `bangau-and-okok` · `sumatran-serow` · `gringsing` · `kain-hinggi-kombu` · `mother-of-pearl-furniture` — P-384)

### 10.4 Home ฟอนต์ 26px (สไลด์หน้า 7 · BACKLOG #38) — ดู §9.3
Figma `3339:5227` (Desktop - 1634): `3339:5267` "Your animal guide is..." **30px / w800 / lh 28px (PIXELS)** · `3339:5277` ชื่อการ์ด (เช่น "The Chicken") **30px / w800 / lh 28px** · ไม่มี 26 ใน Figma ตอนนี้ (ยืนยันจากฟิลด์ `style.fontSize`) · เจ้าของสั่ง 26 → ใช้ 26 ทั้ง 2 จุด (หัวข้อ + ชื่อการ์ดทุกสถานะ) · lh ไม่ระบุ → คง 28 (P-380)

### 10.5 แถบ ZONE 1–5 กดได้ (สไลด์หน้า 8 · BACKLOG #40)
**หลักฐาน prototype: ไม่มี** — แถบ `Frame 12684` ทุกหน้า (zone 1–3 `1013:1291/1342/1368` · zone 5 `3117:3964`) = กรอบ + `Rectangle 1` ภาพ raster ใบเดียว (ref `413791d6` / `e5dbe735` / `172ff28b` / `82bf391e`) · **ไม่มี `interactions` ใน node กรอบ/ภาพ/พี่น้อง** · ทั้ง section Zone 5 `3140:1356` มี interaction เฉพาะ `ON_HOVER` ปุ่ม Back + การ์ด Cover/History · Home `3339:5227` มี `ON_HOVER` ของการ์ด Zone 1–5 (ไม่ใช่ CLICK) → Figma **ไม่ได้กำหนดว่าแถบต้องกดได้ ทั้ง zone 5 และ zone 1–3** · ที่ต้องกดได้ = คำสั่งเจ้าของจากสไลด์ (P-387) · ไม่มี hotspot ใน Figma → ใช้เรขาคณิตที่ธีมใช้แล้ว (`template-parts/zone4/zone-bar.php`: hotspot โปร่งใส 5 ช่อง x = 0/20/40/60/80 % · แถวแท็บเริ่ม y ≥ 74.1 % ของภาพแถบ) · live: แถบ Zone 5 กดได้แล้ว · Zone 1–3 ยัง
