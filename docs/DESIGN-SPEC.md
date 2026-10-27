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
### 3.5 Navigator (จุดข้างหน้า Home)
**ตัวจริง (ตามหมายเหตุ Figma `929:1597`/ภาพ `annot-navigator.png`):** วงกลม 4 ดวง ไม่มี icon/ข้อความ — `Navigator [176,587 30×150]` แนวตั้ง gap 10 · แต่ละดวง 30×30 พื้น `#FFFFFF` stroke `#ADB5BD` 2 · คลิกเลื่อนไป: 1) Introduction (`670:9368`) 2) Zone 1-3 (`670:9479`) 3) Trails (`886:1795`) 4) Zone 4-5 (`670:9541`) · scroll-animate 0.3s
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

### 4.4 Trail page — Elephant's Trail `796:1795` (1440×1632, clip) · `trail-elephant-1501.png`
| section | พิกัด | รายละเอียด |
|---|---|---|
| Header #1/#02 | `[0,0]`,`[0,86]` | |
| Cover `Frame 12685` | `[0,150 1440×497.228]` padding 160/160 · stroke `#DEE2E6` 1 inside | ภาพ `Elephant_Zone2-1 1` `[160,150 1120×497.228]` (IMG `ed0e5ee9`) |
| หัวข้อ `Frame 12683` | `[160,677.2 1120×112]` แนวนอน gap 30 padding-bottom/top 10 กลางแนวตั้ง | "Elephant’s Trail" 36/45 Heavy `[160,710.7 257×45]` · คำอธิบาย `[~447,687 833×92]` Komet 18/24 · Read more (Barlow 20/22) |
| กริดวัตถุ `Frame 114472` | `[160,829.2 1120×652]` แนวตั้ง gap 50 | 3 แถว × 5 tile 184×184 gap 50 ที่ y = 829.2 / 1063.2 / 1297.2 · **15 วัตถุ** (ลำดับ §4.8) |
| ปุ่ม Back | `[160,1536 115×50]` | |
tile ช้างพื้น `#E1DEC6` (render `#E0DEC9`) ภาพ FILL · hover 210 (`Ele NN` variants)
trail อื่นอีก 16 trail **ไม่มีหน้า trail ใน Figma** → ใช้แม่แบบนี้ (P-201)

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

### 4.5b Timeline v2 (D23) — layout = §4.5 เดิม (frame `670:1551`) · เพิ่มเฉพาะสิ่งที่ animation ต้องใช้
**ขอบเขต (เจ้าของสั่ง D23 · ชี้แจงเพิ่ม 2026-10-27):** การเรียงข้อมูล/layout = frame `Desktop - zone 4` ตาม §4.5 ทุกค่า (ไม่เปลี่ยน) · พฤติกรรม/animation = Google Slides 31–54 (§6.4b) · **ไม่ใช้ layout ของ Slides** (mini-map · ลูกศร ‹ › · เส้นประแดง · ภาพถ่ายใต้เส้นแบบ Slides) — ค่าเหล่านั้นวัดไว้ใน §4.5b.6 เผื่อเจ้าของให้เพิ่ม (P-219)
**ที่มาค่า:** `[N]` = node Figma (figma-file.json) · `[R]` = วัดจากภาพอ้างอิงที่ฝังใน Figma Section 50 (`annot-zone4-ref-1..3.png` 1424–1431 กว้าง → ×1440/กว้าง) · `[S]` = วัดจากภาพ Slides `design/timeline-ref/slide-31..54.png` (ขนาดภาพ 755/647/486/432 กว้าง → ×1440/กว้าง · ความละเอียดต่ำ ±3px) · `[P]` = ข้อเสนอ (ไม่มีใน Figma/Slides)

**4.5b.1 องค์ประกอบของ zone 4 ที่ animation แตะ (ทุกค่า geometry อื่นตาม §4.5)**
| ชิ้น (ตาม §4.5) | ภาพที่ animate | state พัก (rest) | state เลือก (active) | asset ที่ต้องใช้ | มีแล้วไหม |
|---|---|---|---|---|---|
| การ์ดวัตถุ ×15 `Frame 1146xx` (ภาพ + `Frame 12683` ข้อความ) | **ภาพการ์ด** (cutout สี พื้นโปร่ง · ref ใน `content/seed/timeline-elephant-v2.json` → `items[].thumbnails[]`) | scale 1.0 · opacity **0.35** `[P]` (=ก้าวแรกของ Slides 0.34 `[S]`) | scale **1.5** `[S]` (ขอบเขต 1.35–1.55) · opacity 1.0 | ภาพการ์ดเดิม `sh_thumbnail` (+ `_2` การ์ด 6, 9) — **ไม่ต้องมีภาพใหม่** | ✔ 15/15 (ต้อง trim พื้นโปร่ง — P-220) |
| ข้อความการ์ด (title 20/25 Heavy · meta 14 · Read more) | ข้อความ | opacity 1.0 `[P]` (Slides จางข้อความตามภาพ แต่ #D3D3D3 บนขาวคอนทราสต์ <2:1 → ไม่ทำตาม; เลือกได้ — P-219) | opacity 1.0 (title `#212529` ตาม token เดิม; Slides = `#333333` ตัวหนา `[S][R]`) | — | ✔ |
| จุด ● บนเส้นเวลา ×15 (ฝังใน raster `1232:1711`) | **ทำเครื่องหมายจุดที่เลือก** (overlay วงแหวน/จุดใหญ่) | ไม่มี | วงกลมดำ ⌀ **≈ 2× จุดเดิม** `[P]` ที่ `strip_dot.frame_x` (ตาราง §4.5b.3) | พิกัดจุด — เพิ่มใน seed v2 แล้ว | ✔ (วัดแล้ว 15/15) |
| เส้นเวลา+ภาพ `ROOM4 ele 2` `1232:1711` (raster ใบเดียว 4096×285 แสดง 11000×791.481 · `[0,200]`) | **ไม่ animate ต่อชิ้นได้** (ทุกวัตถุเป็นพิกเซลเดียวกัน) | static ตาม §4.5 | static + เลื่อนตามชิ้นที่เลือก | ดู §4.5b.4 ตัวเลือก A/B/C | A = ✔ · B = ✔ (ใช้ภาพการ์ด) · C = ✘ |
| `Frame trails` (ไอคอน trail 17) | — | ตาม §4.5 (active 45×45 ที่ x≈260) | ไม่เปลี่ยน | — | ✔ |

**4.5b.2 การจัดวาง state (เพิ่มจาก §4.5)**
- การ์ดคงกรอบ `Frame 1146xx` เดิม (x/y/w/h ตาม §4.5 · ข้อความไม่ขยับในสถานะพัก) · ภาพ active ขยายด้วย `transform: scale(1.5)` โดย `transform-origin: left center` + ข้อความของการ์ดเลื่อนขวา `0.5 × กว้างภาพ` พร้อมกัน `[P]` · กว้างภาพ = node `[N]` เช่น Leg Bone 300 → 450, Ganesha 180 → 270
- ถ้าข้อความที่เลื่อนแล้วชนการ์ดถัดไป (การ์ด 2, 6 ที่ข้อความเต็มกรอบ) → ให้ขยายกว้างการ์ด active ด้วย (animate width +0.5×กว้างภาพ) แล้วชดเชย scroll ให้ชิ้นที่เลือกอยู่กลางจอ `[P]` — wp-dev วัดจริงตอนทำ (ผมไม่ได้เรนเดอร์ฟอนต์ Komet)
- ภาพ **ไม่ขยายเกินความละเอียดไฟล์**: ปัญหา = Pod Duang ไฟล์ 227×227 (ขยาย 270px ที่ 1.5× = 1.19× ของไฟล์ บน DPR1; DPR2 ไม่พอ) · Patola 625×589 พอ · ที่เหลือ ≥ 400px (ตาราง `trimmed_px` ใน seed v2) → P-220
- state พัก/เลือกเป็น class ต่อการ์ด (`is-active`) ตัวเดียวที่ active (ไม่มีหลายใบพร้อมกัน) · ไม่เลือกอะไร = ทุกใบ rest (state เริ่มต้น เหมือน Slides 31 ที่ยังไม่เลือก)
- `prefers-reduced-motion`: สลับ state ทันทีไม่ animate `[P]`

**4.5b.3 พิกัดจุดบนเส้นเวลา (raster `1232:1711`) ↔ การ์ด — ต้องใช้ทำ marker · วัดจาก raster ด้วยสคริปต์ (ไม่เรียก API)**
แปลง raster→frame: x×11000/4096 = **×2.68555** · y = 200 + y×791.481/285 = **200 + ×2.77712** · เส้นเวลาสูง 5px raster ≈ 14 frame px ที่ y≈797–808 (จุด ⌀ 11–12 raster ≈ 30–32 frame px ที่ y≈803)
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
(ครบใน `content/seed/timeline-elephant-v2.json` พร้อม bbox วัตถุต่อชิ้นบน raster) · **ลำดับบนเส้น ≠ ลำดับการ์ด** (illustration_order ≠ card_order · P-208ก ยังจริงสำหรับ raster) แต่ใน Slides + mini-map 15 ชิ้นเรียงตาม **card_order** (อ่านจากภาพ — P-218) → ถ้าไม่จัดเส้นเวลาใหม่ การเลือกชิ้นหนึ่งต้องเลื่อน 2 แถวไปคนละตำแหน่ง (แถว raster ไป `strip_dot.frame_x` · แถวการ์ดไป `card_frame_x`) — ไม่ตรงกันเหมือน Slides ที่ภาพ/จุด/ข้อความอยู่แนวเดียวกัน

**4.5b.4 ตัวเลือกสำหรับ "ภาพวัตถุเหนือเส้น" (ใน Slides = ลายเส้น→สี + ขยาย 1.5×) — ต้องให้เจ้าของเลือก (P-217)**
| | ทำอะไร | asset | สถานะ |
|---|---|---|---|
| **A (baseline · แนะนำให้ปล่อยก่อน)** | raster เหนือเส้นคงเดิม (static) · มี marker จุดที่เลือก + เลื่อนให้ชิ้นที่เลือกอยู่กลางจอ · animation fade+scale ที่ **การ์ด** | ไม่ต้องมีใหม่ | ทำได้ทันที |
| **B** | แทน raster ด้วยเลเยอร์ต่อชิ้น: ภาพ cutout สี (= ภาพการ์ดความละเอียดสูง 15/15) วางที่ `strip_dot.frame_x` (ฐานวัตถุบนเส้น y≈800) · rest = scale 1 opacity 0.35 (+ grayscale 1 `[P]` ให้ดูเป็น "ลายเส้น") · active = scale 1.5 opacity 1 grayscale 0 · ชื่อวัตถุเป็นข้อความจริงเหนือวัตถุ (Komet-Heavy ≈18/22 `#333333` `[R]`; token ใกล้สุด Card title 20/25) → แก้ P-207 (ชื่อ/ป้ายยุคไม่ฝังภาพ) · คนยืน/โครงกระดูกต้องตัดจาก raster (ละเอียดต่ำ) | ภาพการ์ด (มี) + คนยืน 4 ตัว + โครงกระดูก (ต้องขอความละเอียดสูง) | ทำได้ถ้าเจ้าของยอมเปลี่ยนหน้าตา strip จาก raster เดิม |
| **C (ตรง Slides)** | rest = ลายเส้น · active = สี (crossfade) | ลายเส้นแยกชิ้น 15 ภาพ — **มีเพียง 4/15 ใน raster** (ความละเอียดต่ำ ~0.37× · ตัวอย่างตัดแล้ว `design/assets/timeline-v2/` 2 ชิ้น) · ลายเส้นช้างการ์ตูน (ที่ Slides ใช้แทนกรามช้าง) **ไม่มีใน Figma** | ✘ ต้องขอนักออกแบบ |
**หลักฐานว่าเลเยอร์ต่อชิ้นไม่ได้จาก raster:** IMG `791ae91b` เก็บที่ 4096×285 แต่แสดง 11000×791 (×2.69) — วัตถุสูงแค่ ~93 px ใน raster (≈250 px ที่ใช้แสดง ขยาย 1.5× → blur ≈4×) · ตัวอย่างที่ตัดได้: `ganesha-fourarmed-lineart.png` 97×90 · `singhasari-ganesha-lineart.png` 62×87 (พื้นโปร่ง · ตัดด้วย `design/tools/crop-timeline-lineart.py` · ขอบล่างถูกตัดที่เส้นเวลา) → **พิสูจน์ว่าตัดได้ แต่ใช้จริงไม่ได้**

**4.5b.5 สิ่งที่ Slides แสดงแล้ววิเคราะห์ว่า "ภาพไหน" fade/ขยาย (สรุป)**
1. วัตถุเหนือเส้น (ลายเส้น→สี ×≈1.5 ฐานติดเส้น · เพื่อนบ้านไม่เปลี่ยนขนาด: ลายเส้น Singhasari 127×178 คงเดิมทุกเฟรม 31–36 `[S]` · ช้างการ์ตูน 181×129 คงเดิม `[S]`) → zone 4: ทางเลือก A/B/C ข้างบน
2. ภาพถ่ายใต้เส้น (opacity 0.10→0.34→0.67→1.0 ใน 31→34 · ค้าง 35 · 0.66→0.33→0.18→0 ใน 36→39 `[S]` วัดจากค่าสว่างเทียบเฟรมชัด) → zone 4 = **ภาพการ์ด** (ภาพถ่าย cutout สีชนิดเดียวกัน ไม่ต้องมี asset ใหม่) — สีใน Slides ใต้เส้นจางกว่าเหนือเส้น (ทราย vs น้ำตาลเข้ม) น่าจะเป็นฟิลเตอร์ของภาพเดียวกัน ไม่ใช่ไฟล์ต่าง (ไม่แน่ใจ)
3. ชื่อวัตถุ: `#CECECE`→`#696969`→`#383838` (≈0.24→0.74→0.98 ของ `#333333`) ตัวหนา ≈18px ที่ 1440 (กว้าง "Ganesha" 72px) `[S][R]`
4. metadata: `#D3D3D3`→`#A7A7A7`→`#767676` (คงที่ `#767676` ในภาพอ้างอิงละเอียด `[R]`) · ระยะบรรทัด ≈18.6px · ขนาด ≈13–14px (token Komet 14/17.5 ใกล้สุด) · 7 บรรทัดสำหรับ Ganesha
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

**ขาด/ต้องขอ:** (1) ลายเส้นแยกชิ้น **11/15 ไม่มี** (ที่มี 4: ชิ้น 1 โครงกระดูก+กระดูก · 2 · 3 · 4 กราม — ใน raster `791ae91b` ความละเอียดต่ำ) และลายเส้น **ช้างการ์ตูน** ที่ Slides ใช้แทนชิ้น 4 ไม่มีใน Figma → ต้องขอก็ต่อเมื่อเลือกทางเลือก C (P-217) · (2) raster ความละเอียดสูง/ชิ้นคนยืน 4 + โครงกระดูก (ทางเลือก B) · (3) Pod Duang `bf841c7f` 227×227 ต่ำเกินสำหรับ 1.5×/DPR2 → ขอไฟล์ใหญ่ (P-220) · (4) ภาพทุกใบ **ไม่มีไฟล์ใหม่ใน Figma/ดาวน์โหลดเพิ่ม** — ใช้ของที่มี 100% (ไม่เรียก API)
**ตัวอย่างตัดลายเส้นจาก raster (พิสูจน์ ไม่ใช่ของใช้จริง):** `design/assets/timeline-v2/ganesha-fourarmed-lineart.png` 97×90 · `singhasari-ganesha-lineart.png` 62×87 + `manifest.json` · สคริปต์ `design/tools/crop-timeline-lineart.py` (`--help` · `--no-write` ไม่เขียนอะไร · เขียนผ่าน temp dir แล้ว `os.replace` ต่อไฟล์) · `design/` ไม่อยู่ใน repo (ไม่ commit)

### 4.6 รายการ trail 17 + "Etc."
ดู `content/seed/trails.json` — rooster · turtle · deer · dragon · fish · hornbill · horse · naga (=Serpent ใน 2.1) · water-bird · makara · lion · seashell · buffalo · phoenix · bird · garuda · elephant (17) + `etc` (4 วัตถุที่ยังไม่จัด trail) · จำนวนวัตถุต่อ trail (นับ record ใน objects.json): elephant 16 (15 final + Gringsing) · seashell 14 · naga 13 · rooster 10 · water-bird 10 · dragon 9 · phoenix 8 · fish 8 · bird 8 · hornbill 7 · horse 7 · lion 6 · turtle 5 · garuda 5 · buffalo 5 · deer 3 · makara 3 · etc 4

### 4.7 แผนที่ + popup (Section 11 / Frame 12718 `670:1596`)
แผนที่ `1120×620` พื้น `#F2F2F2` + ภาพ (IMG `cd63a05d`) · ปุ่ม ‹ › 60×80 กลางแนวตั้ง · เลข 1–4 มุมขวาบน `[932,22 166×34]` · หมุด 34×34 (instance `Frame 12681`, ON_PRESS เปลี่ยนสถานะ, ON_CLICK เปิด overlay popup) · popup `336×h` พื้น `#D65F47` r4 padding 12 gap 10 · ปุ่มปิด `basil:cross-solid` 24×24 (`close-cross.svg` สี `#000000`) ที่มุมขวาบน (404,66 เทียบ section) · ชื่อ Barlow SemiBold 16/19.2 · เนื้อ Barlow Regular 14/16.8 (กว้างข้อความ 312) · popup 4 ใบสูง 369/284/464/352 ตามเนื้อ

### 4.8 Detail วัตถุ (หน้า "Desktop - NNNN") — โครง + ตาราง geometry รายเฟรม (1440 กว้าง · 15 frame Final = ที่ต้องตรง 100%)
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

### 4.9 Nav / ปุ่ม / component เสริมที่อยู่ใน Figma แต่ไม่ได้ใช้ในเฟรม Final
- แถบ zone-nav 5 ช่อง `Frame 12723` (`670:1107`): Barlow Condensed 20/24 (active Bold 700 + พื้น `#D65F47`@20%, ไม่ active Medium 500) padding 12/12/10/10 — ตัวอย่างอยู่ใน style guide; ไม่ปรากฏในหน้า (อาจใช้เป็น sub-nav) → ใช้ถ้าต้องการ ไม่บังคับ
- ปุ่ม `Button 1` (TH "นัดปรึกษาฟรี"), ไอคอน mobile/mail, `lucide:arrow-up` = ชิ้นส่วนแม่แบบเดิม ไม่เกี่ยวกับเว็บนี้

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
| `reflection_title` / `reflection_body` | text | `1232:1816` / `1232:1817` | "Reflection" / "The objects in this trail reveal the elephant's enduring presence…" | `reflection.*` |
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
**เพิ่มสำหรับ Timeline v2 (D23 · เสนอให้ core ยก — ⛔ ผมไม่แก้ CONTENT-MODEL · เฉพาะที่จำเป็นจริง)**
| ฟิลด์ | ชนิด | จำเป็น | ที่มา | ตัวอย่าง | seed key |
|---|---|---|---|---|---|
| `strip_dot_x` | number (frame px) | ✔ ถ้าทำ marker จุด (ทางเลือก A/B §4.5b.4) · **ไม่ใช่เนื้อหาที่ทีมแก้ — เป็นค่า layout ผูกกับ raster `1232:1711`** → เก็บเป็น constant ในธีม/ไฟล์ config ก็ได้ ไม่ต้องเป็นฟิลด์ admin | วัดจาก raster (§4.5b.3) | Leg Bone 8830 · Ganesha 384 | `timeline-elephant-v2.json items[].strip_dot.frame_x` |
| `sh_thumbnail` (มีแล้ว) | image | ✔ | ใช้เป็นภาพที่ fade/scale ทั้งการ์ดและ (ทางเลือก B) ภาพเหนือเส้น | — | `items[].thumbnails[]` |
| `sh_line_art` | image (PNG พื้นโปร่ง) | ✘ **เฉพาะทางเลือก C** — ยังไม่ต้องยก จนกว่าเจ้าของเลือก C และได้ไฟล์จากนักออกแบบ (P-217) | ลายเส้นแยกชิ้น 15 (ไม่มีใน Figma ครบ) | — | — |
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
### 6.4b Timeline v2 — animation จาก Slides 31–54 ใช้กับการ์ด zone 4 (D23 · แทนข้อ 4 ของ §6.4 ที่เป็นลูกศร/ช้างเดิน)
ที่มา: ภาพ Slides ไม่มีเวลา มีแต่ลำดับเฟรม → **ระยะเวลา/easing = ข้อเสนอ `[P]`** · ค่า opacity/scale/ตำแหน่งวัดจากภาพ `[S]` (ดูสัญลักษณ์ §4.5b) · Slides 24 เฟรมแบ่งเป็น **3 รอบเลือก**: Ganesha (31–38) · Elephant Molar (39–47) · Embossed Brass Basin (48–54) — 2 รอบหลังเริ่มที่ชิ้นถัดไปบนเส้น (ลำดับ = card_order 3→4→5)
**ลำดับเฟรม → state (รอบ Ganesha 31–38 วัดละเอียด · รอบอื่นลำดับเดียวกัน)**
| เฟรม | สิ่งที่เห็น `[S]` | สถานะที่ใช้เขียนโค้ด |
|---|---|---|
| 31 | เริ่มต้น: ยังไม่เลือก · วัตถุทั้งหมดเป็นลายเส้น · ภาพถ่ายใต้เส้นจางมาก (0.10) · viewport อยู่กึ่งกลางระหว่างชิ้น 2 กับ 3 | `none` (ทุกการ์ด rest) |
| 31→33 | ชิ้นที่ 3 เลื่อนจาก x≈907 ไปกลางจอ x≈720 (2 ก้าวละ ≈92px) พร้อมกัน: ลายเส้น→สี · ขยาย (149×179 → 171×207 → 200×280) · ภาพใต้เส้น 0.10→0.34→0.67 · ชื่อโผล่เหนือวัตถุ `#CECECE`→`#696969` · metadata `#D3D3D3`→`#A7A7A7` | `selecting` — เลื่อน + fade/scale-in พร้อมกัน |
| 34–35 | ค้าง: ภาพชัด 1.0 · ชื่อ `#333333` ตัวหนา · metadata `#767676` · เส้นประแดง 3 เส้น | `active` (hold ไม่มี timeout — รอผู้ใช้) |
| 36–37 | ภาพใต้เส้น 0.66→0.33 · ชื่อยังชัด (36) · metadata `#B0B0B0` · เส้นไกด์เพิ่ม (ส้ม/ชมพู) | `deselecting` เริ่ม (fade-out ภาพ+ข้อความ) |
| 38 | ภาพ 0.18 · ชื่อหาย (`#F9F9F9`) · วัตถุเริ่มกลับเป็นลายเส้น · เนื้อหาเลื่อนซ้าย ≈86px · ชิ้นถัดไป (สี) โผล่ขอบขวา | fade-out จบ + `scroll-to-next` เริ่ม |
| 39–40 | เลื่อนต่อ · ชิ้นก่อนหน้ากลับลายเส้นครบ · ชิ้นถัดไปเข้ากลางจอ ชื่อโผล่จาง | เริ่มรอบถัดไป = `selecting` ของชิ้นใหม่ |
| 41–45 | (กราม) ชื่อ 2 บรรทัด `#333333` · ใต้เส้น: ช้างลายเส้นใหญ่ (41) → crossfade เป็นภาพกราม (42→43) → กลับเป็นช้างลายเส้น (44→45) ก่อนจาง (46) · อ่านจากภาพ ความมั่นใจปานกลาง (P-219) | `active` + ชิ้นเดียวมี crossfade ลายเส้น↔ภาพ (ไม่ทำ) |
| 46–47 | ชื่อจาง · ภาพ→จาง · เลื่อนซ้ายไปชิ้นถัดไป (ขันทองเหลืองเข้าจากขวา) | `deselecting` → `scroll-to-next` |
| 48–51 | (ขันทองเหลือง) ชื่อจาง→ชัด · ภาพ ghost→กลาง→ชัด (ไม่ได้วัดค่า) · metadata `#D9D9D9`→`#B2B2B2`→`#404040` (วัดเฟรม 48/49/51) | `selecting` → `active` |
| 52–54 | ย้อน: ภาพจาง · ชื่อจาง (53) · เลื่อนไปชิ้นถัดไป (กระบอกงาช้างเข้ากลางจอ) (54) | `deselecting` → `scroll-to-next` |
**เครื่องสถานะ (ส่งต่อ wp-dev) — ใช้กับการ์ด 15 ใบ + marker จุด (+ ชั้นเหนือเส้นถ้าเลือก B)**
`none → selecting → active → deselecting → (selecting ของชิ้นใหม่)` · เปลี่ยนชิ้นกลางคัน (คลิกชิ้นอื่นขณะ active) = ชิ้นเก่า `deselecting` และชิ้นใหม่ `selecting` **พร้อมกัน** (Slides ทำแบบนี้: fade-out ชิ้นเก่าระหว่างเลื่อน) · ชิ้นที่ไม่เกี่ยวข้อง **ไม่เปลี่ยนขนาด/ความจาง**
| พารามิเตอร์ | ค่า | ที่มา |
|---|---|---|
| scale active | 1.5 (1.35–1.55) | `[S]` |
| opacity ภาพ | 0.35 (rest) → 1.0 (active) · เส้นทาง 3 ก้าว 0.10/0.34/0.67/1.0 → ใช้เส้นต่อเนื่อง | `[S]` ก้าว · rest `[P]` |
| เลื่อนให้ชิ้นอยู่กลางจอ | ชิ้นที่เลือกอยู่ x=720 (กึ่งกลาง 1440) · **แถวการ์ดและแถว raster เลื่อนแยกกันเพื่อให้ `card_frame_x`/`strip_dot.frame_x` ของชิ้นนั้นอยู่กลางจอ** (ลำดับสองแถวต่างกัน P-218) | `[S]` กึ่งกลาง · วิธีแยก `[P]` |
| ระยะเวลา | เลื่อน 700ms · fade/scale in 450ms (เริ่มพร้อมเลื่อน) · fade-out 300ms · `cubic-bezier(.4,0,.2,1)` | `[P]` |
| trigger | (1) คลิกการ์ด/ภาพ หรือคลิกจุดบนเส้น · (2) คีย์ ← → (ถัดไป/ก่อนหน้า ตาม card_order · a11y) · (3) เลื่อนด้วยมือ: เมื่อหยุด 120ms การ์ดที่ใกล้กลางจอสุดเป็น active · (4) ลูกศร ‹ › บนจอ **ไม่ทำ** เว้นเจ้าของเพิ่ม (§4.5b.6) | `[P]` (brief: "คลิกการ์ด/จุด/ลูกศร หรือเลื่อนถึง") |
| ตอนโหลดหน้า | state `none` · ไม่เลื่อนอัตโนมัติ · ไม่มี autoplay | Slides 31 |
| a11y | การ์ด = `button`/ลิงก์ มี `aria-current` ตัวที่ active · `prefers-reduced-motion` = สลับทันที | `[P]` |
### 6.5 ไม่มีใน Figma
scroll-snap, parallax, transition ระหว่างหน้านอกจากที่ระบุ, การทำงานของ search/Index, footer, 404 → ไม่เดา

---
## 7. ข้อสังเกต / ข้อขัดกัน (รายละเอียดที่ `docs/PENDING-DECISIONS.md` P-201…P-215)
สรุป: P-201 page final vs 2.1 · P-202 จำนวนวัตถุ 121/119 · P-203 Patola vs Gringsing · P-204 ชื่อ Zone 4/5 + frame zone 5 หาย · P-205 ฟอนต์ Komet · P-206 น้ำหนัก caption/collection ไม่สม่ำเสมอ · P-207 ข้อความฝังในภาพ · P-208 timeline (ลำดับ+ช้างเดิน+ลิงก์ Slides) · P-209 hero GIF/WebM · P-210 ชื่อ intro "Scales, Feathers & Fins" / map placeholder / navigator ตัวเก่า · P-211 ปลายทางเมนู + footer · P-212 trail↔zone + กริด 15 vs 17 · P-213 ข้อความ lorem/ตกค้าง/placeholder · P-214 สีแผง detail ต่อวัตถุ · P-215 Figma render rate limit · **P-217…P-220 (D23 Timeline v2): P-217 ทางเลือกภาพเหนือเส้น A/B/C · P-218 ลำดับ raster ≠ การ์ด/Slides · P-219 ของ Slides นอก layout zone 4 · P-220 คุณภาพ/trim asset**
