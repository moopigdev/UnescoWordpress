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
- **(D39) ชิ้นที่มี `sh_below_image` (ภาพใต้เส้นต่างจากบนเส้น · ชิ้น 1·7·9):** ฐานของ rest/active = **ภาพบนเส้น** contain ≤320×265 (ไม่ใช่ภาพใต้เส้น) → rest = 0.30 × ฐาน · active = 0.60 × ฐาน · ภาพใต้เส้น = contain ≤320×265 ของ `sh_below_image` แยกต่างหาก · ชิ้นที่ภาพสองตำแหน่งอัตราส่วนเดียวกัน (ทุกชิ้นอื่น) ผลเท่าสูตร #14 เดิมทุกค่า · `[P]` (Figma ไม่ระบุสัดส่วนบน:ล่าง · ตัวเลขต่อชิ้น §4.5c.3d)
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
| 1 | elephant leg bone | 1360×720 (1.89:1) | 320×169 (บนเส้น) · **(D39) ใต้เส้น `sh_below_image` = `bbc1e161` 1295×701 → 320×173** | **96×51** | **192×101** | ×2.00 | `[C]` | 258.2 | 48×25 |
| 2 | singhasari ganesha | 1340×1885 (0.71:1) | 188×265 | **56×80** | **113×159** | ×2.00 | `[C]` | 200.2 | 27×38 |
| 3 | ganesha | 1563×1878 (0.83:1) | 221×265 | **66×80** | **133×159** | ×2.00 | `[C]` | 200.2 | 32×38 |
| 4 | elephant molar | 767×383 (2.0:1) | 320×160 | **96×48** | **192×96** | ×2.00 | `[C]` | 263.2 | 48×24 |
| 5 | embossed brass | 1343×960 (1.4:1) **(D39: `cba74a7d` ขนาด/crop เท่า `5d9931b4` ทุกค่า → ค่าในแถวไม่เปลี่ยน)** | 320×229 | **96×69** | **192×137** | ×2.00 | `[C]` | 222.2 | 48×34 |
| 6 | ivory fire piston | group 107:163 (Slides 49/51 · box ซ้าย/ขวา ดูข้อ "ชิ้นที่มี 2 ภาพ") (0.656:1) | 174×265 | **52×80** | **104×159** | ×2.00 | `[C]` | 200.2 | 25×38 |
| 7 | elephant and mahout | **(D39) บนเส้น `621646b5` trim 2842×3844 (0.74:1)** · ❌ เดิม `867b2833` 1401×2203 | **(D39) ใต้เส้น `sh_below_image` = `555d365b` trim 1150×898 → 320×250** · ฐานบนเส้น (contain 320×265) **196×265** | **59×80** | **118×159** | ×2.00 | `[C]` + D39 `[P]` | 200.2 | **28×38** |
| 8 | patola | 611×580 (1.05:1) | 279×265 | **84×80** | **167×159** | ×2.00 | `[C]` | 200.2 | 40×38 |
| 9 | elephant shaped relic | group 180×150 (1.2:1) | 318×265 (บนเส้น กลุ่ม) · **(D39) ใต้เส้น `sh_below_image` = `1c0fc05b` 3405×1940 → 320×182** | **95×80** | **191×159** | ×2.00 | `[C]` | 200.2 | 46×38 |
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
**ที่มา:** Figma `3101:609` › `Section 51` `3102:1028` › `Frame 114443` `3101:819` › `Frame 114464` `3101:820` (17 frame ลูก = การ์ด 15 + Reflection + ซ่อน 1) · ใช้ `design/figma-file.json` lastModified **2026-11-11T08:23:07Z** (ตรงกับ `figma-3101-609.json` 03:55:12Z ทุกอย่าง ยกเว้นคู่ ▶ ชิ้น 5 ถูกย่อจาก 500×281 เป็น 320×180 + เพิ่ม ▶ `3274:1053`) · **0 คำขอ Figma API** · ⚠ ภาพเจ้าของ 15:47 (= 08:47Z) `owner-basin-new-image.png` แสดงขันเล็กบนเส้นเป็น `cba74a7d` แต่ไฟล์ที่ดึง 08:23Z ยังเป็น `5d9931b4` (`3103:1107`) → นักออกแบบแก้ Figma หลังดึง → ชิ้นอื่นอาจเปลี่ยนอีก → P-261 (ขอดึง node `3102:1028` ใหม่ 1 คำขอ)
**นิยาม:** บนเส้น = กล่องเล็ก 180–200 กว้างในแถว "ภาพ+ข้อความ" (`Frame 1146xx`) · ใต้เส้น = กล่อง 500×281 บนสุดของการ์ด (ชิ้น 2–5 = ภาพ "หลัง" ขวาของ ▶ ในคู่ 320×180) · "ปัจจุบัน" = `scripts/import/image-map.json` timeline_items (sh_thumbnail/_2 · ใช้ทั้งบนเส้นและใต้เส้น) + `line_art_ref` (ภาพก่อน ชิ้น 2–5)
| # | บนเส้น Figma (node · ref · กล่อง) | บนเส้น ปัจจุบัน | ใต้เส้น Figma (node · ref · กล่อง) | ใต้เส้น ปัจจุบัน | ผล |
|---|---|---|---|---|---|
| 1 | `3102:1031` `4953122e` 200×112 | `4953122e` | `3103:1037` **`bbc1e161`** 500×281 (โครงกระดูกช้างลายเส้น + คนยืน + วงกลมกระดูก 1421×1080 trim 1295×701) | `4953122e` (กระดูกสี) | บน ✅ ตรง · **ใต้ ❌ ต่าง** → `below_ref` `bbc1e161` ⚠ ขัด P-224/D27 (default ของ orchestrator ไม่ใช่เจ้าของ · D39(3) ใหม่กว่า) → P-260 |
| 2 | `3103:1048` `3fba9728` 180×168 | `b0afe5ba` | หลัง `3103:1054` `3fba9728` 320×180 (ก่อน `3103:1040` `7e07d19a`) | `b0afe5ba` + ก่อน `02-…png` | ✅ ตรง (**ภาพเดียวกัน คนละไฟล์**: trim 628×884 vs 1340×1885 · ssim 0.96 · คงไฟล์เดิมที่ละเอียดกว่า) |
| 3 | `3103:1075` `bc3f275a` 180×187 | `bc3f275a` | หลัง `3103:1057` `a0d982dc` 320×180 (ก่อน `3103:1056` `14cb9029`) | `bc3f275a` + ก่อน `03-…png` | ✅ ตรง (a0d982dc = ภาพเดียวกับ bc3f275a · ssim 0.87 · trim 691×827 vs 1564×1878) |
| 4 | `3246:3904` `378ea9bd` 180×95 | `378ea9bd` | หลัง `3246:3916` `378ea9bd` (ก่อน `3246:3914` `2511e717`) | `378ea9bd` + ก่อน `04-…png` | ✅ ตรง |
| 5 | `3103:1107` `5d9931b4` 180×129 ในไฟล์ 08:23Z · **ภาพเจ้าของ 15:47 = `cba74a7d`** | `5d9931b4` | หลัง `3160:1508` **`cba74a7d`** 320×180 (ก่อน `3160:1510` `87965132`) | `5d9931b4` + ก่อน `05-…png` | **❌ ต่าง (D39)** → `thumbnail` = `cba74a7d` (บน+ใต้เส้น) · ไม่ใส่ `below_ref` · ก่อนเล็กลง `before_scale` |
| 6 | `3103:1126/1127` กลุ่ม `f8e2d79d` + `8dcebc79` | เหมือนกัน | `3103:1134/1135` กลุ่มเดียวกัน (Figma วางซ้อนกัน 369×322) | เหมือนกัน (กลุ่มไม่ซ้อน 174×265 ตาม Slides) | ✅ ตรง ref · layout ต่างโดยตั้งใจ (Slides 49/51 `[S]`) → P-262 |
| 7 | `3103:1181` **`621646b5`** 180×168 (kendi เล็ก + น้ำ) | `867b2833` | `3103:1187` **`555d365b`** 500×281 (kendi + คนลายเส้น 2 คน) | `867b2833` | **❌ ต่างทั้งคู่ (D39)** → `thumbnail` = `621646b5` · `below_ref` = `555d365b` |
| 8 | `3103:1213` `07e22a68` 200×188 | `07e22a68` | `3103:1201` `a3d77ee3` 500×281 | `07e22a68` | ✅ ตรง (ผ้าผืนเดียวกัน ตัดขอบต่างเล็กน้อย 588×554 vs 612×582 · คงเดิม) |
| 9 | `3103:1233/1234` กลุ่ม `b1ba8a8a` + `1c3aa3a1` | เหมือนกัน | `3103:1220` **`1c0fc05b`** 500×281 (ภาชนะ 2 ใบมีเส้นขอบดำ รวมในภาพเดียว 4096×2305 trim 3405×1940) | กลุ่ม 2 ภาพ | บน ✅ ตรง · **ใต้ ❌ ต่าง** → `below_ref` `1c0fc05b` |
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
| 1 | `4953122e` 1360×720 (1.89) | 320×169 | 96×51 | 192×101 | 48×25 | `bbc1e161` 1295×701 → **320×173** | ใต้เส้นเปลี่ยน · บนเส้นเท่าเดิม |
| 5 | `cba74a7d` 1343×960 (1.40) · crop [1,1,1344,961] | 320×229 | 96×69 | 192×137 | 48×34 | = บนเส้น **320×229** | เปลี่ยนแค่ไฟล์ (ขนาด/crop เท่า `5d9931b4`) · ภาพก่อน → `before_scale` |
| 7 | `621646b5` **2842×3844 (0.739)** · crop [145,76,2987,3920] · src 3403×4096 | **196×265** (เดิม 169×265) | **59×80** (เดิม 51×80) | **118×159** (เดิม 101×159) | **28×38** (เดิม 24×38) | `555d365b` 1150×898 (1.281) · crop [101,111,1251,1009] · src 1421×1080 → **320×250** | บน+ใต้เปลี่ยนทั้งคู่ · ความสูง active 159 เท่าเดิม → ตำแหน่งชื่อ (ล่างสุด 200.2) ไม่เปลี่ยน |
| 9 | กลุ่ม 180×150 (1.2) | 318×265 | 95×80 | 191×159 | 46×38 | `1c0fc05b` 3405×1940 (1.755) · crop [485,250,3890,2190] · src 4096×2305 → **320×182** | ใต้เส้นเปลี่ยน · บนเส้นเท่าเดิม |
| 2–4, 6, 8, 10–15 | ไม่เปลี่ยน — ใช้ตารางด้านบน | | | | | = บนเส้น | — |
- **เช็ก ≥2× (DPR2) ของภาพใต้เส้นใหม่:** ชิ้น 1 640×346 ≤ 1295×701 ✓ · ชิ้น 7 640×500 ≤ 1150×898 ✓ · ชิ้น 9 640×364 ≤ 3405×1940 ✓ · ชิ้น 7 บนเส้น active 118×159 ×2 = 236×318 ≤ 2842×3844 ✓ · ชิ้น 5 ภาพก่อน ดูด้านล่าง
- **ตรวจไม่ชน:** active กว้างสุดยังเป็น 192 (ชิ้น 1) · ชิ้น 7 active 118 < 192 → ช่องว่างต่ำสุด 232.6px (§4.5c.3 #14) ไม่เปลี่ยน
**`before_scale` ชิ้น 5 `[P]` (ค่าคงที่ต่อชิ้นในธีม — ไม่ใช่ฟิลด์ admin · ค่า = 0.62):**
- วัดจาก Figma คู่ ▶ ชิ้น 5 (กล่อง 320×180 ทั้งสอง · ภาพกึ่งกลางทั้งสองแกน · aspect คงที่) · `imageTransform` ของ STRETCH = node→image (ส่วนที่เห็นของไฟล์ = [tx, tx+a] × [ty, ty+d]) → ภาพก่อน `3160:1510` `87965132` (567×382) วาด **137.2×92.5** (a 2.3319 · tx −0.6673 · d 1.9452 · ty −0.4730 → ภาพกินกลางกล่อง 42.9% × 51.4%) · ภาพหลัง `3160:1508` `cba74a7d` (1344×961) วาด **222.7×159.4** (a 1.4368 · tx −0.2189 · d 1.1293 · ty −0.0669) → ก่อน÷หลัง = **0.616 (กว้าง) · 0.580 (สูง)** · ตรงกับภาพเจ้าของ (`owner-basin-before-smaller.png`: ช้าง ≈80px ต่อขัน ≈135px ในกรอบ 190px = 0.59×)
- **กฎ:** ภาพก่อน = contain (สัดส่วนไฟล์ก่อน 1.484) ในกล่อง **(0.62 × W_U) × (0.62 × H_U)** · W_U×H_U = ขนาดภาพหลังใต้เส้น = 320×229 → กล่อง 198.4×141.8 → **ภาพก่อน 198×134 px** (จำกัดด้านกว้าง) · กึ่งกลางทั้งสองแกนในกล่องเดียวกับภาพหลัง (x720 / y_rel561) · ตรง Figma ±1px (Figma 0.616 → 197×133)
- ชิ้น 2·3: Figma ก่อน/หลัง canvas เดียวกัน ซ้อนตรงกัน → `before_scale` = 1 (ค่าเดิม) · ชิ้น 4: Figma ก่อน:หลัง กว้าง 186.4:177.0 (1.05) ≈ ค่าเดิม contain แยกต่อภาพ → ไม่ต้องมี · มีเฉพาะชิ้น 5 · `before_scale` ใช้เฉพาะ phase=ก่อน (§6.4c) ไม่กระทบ cross-fade/α
**ภาพใต้เส้นชิ้น 7 (`555d365b` kendi + คนลายเส้น 2 คน) `[P]`:** กล่อง **≤320×265 ใช้ได้ ไม่ต้องกว้างกว่า** — trim 1150×898 (1.281) กว้างกว่าอัตราส่วนกล่อง (1.208) → จำกัดด้านกว้าง → **320×250** · kendi เองในภาพ (ส้ม 773×873 px = 67% ของความกว้าง trim) = **215×243** (เดิม `867b2833` 169×265 → กว้างขึ้น 27% · สูงลด 8%) · คนซ้าย/ขวาอยู่ในกรอบ 320 ไม่ล้น · **ไม่แนะนำขยาย**: ถ้าให้ kendi สูง 265 เท่าเดิมต้องกว้าง 349 → ขอบขวาภาพ 894.5 ห่าง metadata (x907) เหลือ 12.5px (ปกติ 27) · ภาพบนเส้น active (kendi ส้มสูง ≈125) = ≈51% ของ kendi ใต้เส้น (243) — ต่ำกว่า 60% เล็กน้อยเพราะภาพบนเส้นมีน้ำ/ขอบโปร่ง (ภาพบนเส้น 0.74 ต่างอัตราส่วนกับใต้เส้น 1.28) · ยอมรับ `[P]` (Figma ไม่ระบุสัดส่วนระหว่างสองภาพ)
**ธีม (wp-dev) ต้องทำ:** (1) `geometry.php` ชิ้น 5 เปลี่ยนไฟล์เป็น `cba74a7d` (src [1344,961] crop [1,1,1344,961] เท่าเดิม) · ชิ้น 7 `photo` [196,265] · `mini` [28,38] · `ratio` 2842/3844 · images = `621646b5` src [3403,4096] crop [145,76,2987,3920] · เพิ่มคีย์ `below` [w,h] + `below_images` (src/crop ของ `sh_below_image`) เมื่อมี `below_ref`: ชิ้น 1 [320,173] · 7 [320,250] · 9 [320,182] · (2) `before_scale` 0.62 ชิ้น 5 (3) importer: seed `below_ref` → `sh_below_image` (ค้นไฟล์ที่ `design/assets/<ref>` ระดับบน · ไฟล์ไม่มี/ว่าง = ไม่แตะฟิลด์) + `bundle-files.php` รวมไฟล์ `below_ref` + patch `image-map.json` `timeline_items.5/.7` (seed `thumbnail_refs` — `build-image-map.py` ใช้กับ `figma-file.json` ใหม่ไม่ได้ · ดู DEFERRED)

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
