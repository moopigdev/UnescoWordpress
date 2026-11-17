# MEDIA — WebM + poster + fallback Safari/iOS

เจ้าของ: media-dev · task `media-encode` + `media-hero-frames` · ตัวเลขทุกตัวมาจาก ffprobe/ffmpeg จริง (สร้างอัตโนมัติใน `content/media-manifest.json`)

## วิธีรัน
```bash
bash scripts/media/encode.sh            # encode + poster + fallback + เขียน content/media-manifest.json
FORCE=1 bash scripts/media/encode.sh    # ทำใหม่ทุกไฟล์
```
- ต้นฉบับ: `Unesco/media/src/File Animated/**.webm` (ไม่แตะ · ไม่ commit) → ผล: `Unesco/media/out/` (ไม่ commit)
- ปรับได้ด้วย env: `MEDIA_SRC` `MEDIA_OUT` `LIMIT_BYTES` (default 3000000) `MAXW` (1440) `MAXFPS` (30) `POSTER_AT` (0.35 = ตำแหน่งเฟรม poster) `FALLBACK_FPS` (10)
- สคริปต์: `encode.sh` (WebM เจ้าของ) → เรียก `hero_frames.py` (hero ต่อเฟรม · อ่าน `hero-frames.spec.json`) → `manifest.py` (เขียน manifest รวม) · `SKIP_HERO_FRAMES=1` ข้ามแค่การ encode ต่อเฟรม (manifest ยังรวมครบ 16 รายการจาก spec + ไฟล์ที่มีใน out · ไม่มี sidecar = error) · `HERO_CRF` (24) · `DESIGN_ASSETS` (default `Unesco/design/assets`)
- สร้าง spec ใหม่ (เฉพาะเมื่อ seed/figma เปลี่ยน · ไม่เรียก Figma — อ่าน `design/figma-file.json` ที่ดาวน์โหลดไว้): `python3 -I scripts/media/hero_spec.py > scripts/media/hero-frames.spec.json`
- ต้องมี: ffmpeg (libvpx-vp9 + hevc_videotoolbox = macOS) · cwebp · python3
- รันซ้ำได้ผลเดิม: ข้ามไฟล์ที่ out ใหม่กว่า src · HEVC ไม่ถูกสร้างใหม่ถ้ามีอยู่แล้ว (videotoolbox ไม่ deterministic ระดับไบต์) · manifest ไม่มี timestamp (ทดสอบรัน 2 ครั้ง `diff` ว่าง)
- ชื่อ out = slug ของชื่อไฟล์ (ตัวเล็ก · `-` · ตัด `.webm.webm` ซ้อน) เช่น `walking.webm.webm` → `walking.webm` · `Bas-relief of a procession.webm` → `bas-relief-of-a-procession.webm`

## ขั้นตอนที่สคริปต์ทำ
1. **ตรวจ alpha จริง** — ffprobe บอกแค่ `pix_fmt=yuv420p` + tag `alpha_mode=1` (decoder native ของ ffmpeg ไม่อ่าน alpha plane) → สคริปต์ decode ด้วย `-c:v libvpx-vp9` แล้ววัด alpha ต่ำสุดทุก 0.5 วินาที · **`has_alpha` ใน manifest = มีพิกเซลโปร่งใสจริง (alpha min < 250)** · `alpha_tag` = ติด tag แต่ plane อาจทึบ
2. **encode** — src ≤ 3MB → copy ไม่ re-encode · เกิน → VP9 2-pass, ≤1440 กว้าง, ≤30fps, `-an`, ปรับ bitrate ลง 15% วนจนไม่เกิน 3MB · ถ้าทึบทั้งไฟล์ ทิ้ง alpha plane (`yuv420p`) · ถ้าโปร่งจริง `yuva420p`
3. **poster** — เฟรมที่ 35% ของความยาว · กว้าง ≤640 · WebP q80 (เก็บ alpha)
4. **fallback Safari/iOS** — เฉพาะ `has_alpha`: `<ชื่อ>.hevc.mov` (HEVC-alpha, `hevc_videotoolbox -alpha_quality 0.75 -tag:v hvc1`, 10fps คงความยาวเท่า src) · ไฟล์ทึบ: fallback = poster

## ตารางผล (ขนาดวัดจาก `ls`/ffprobe)
| ไฟล์ out | src (MB) | out (MB) | วิดีโอ out | alpha จริง | fallback | poster (KB) |
|---|---|---|---|---|---|---|
| bas-relief-of-a-procession | 0.71 | 0.71 (copy) | 1440×810 · 8.4s · 12 เฟรม | ✅ | hevc.mov 2.26MB | 76 |
| elephant-and-mahout-kendi | 0.30 | 0.30 (copy) | 1440×1440 · 4.8s · 6 เฟรม | ✅ | hevc.mov 0.55MB | 27 |
| elephant-molar-coconut-grater | 0.13 | 0.13 (copy) | 942×912 · 11.0s · 30 เฟรม | ✅ | hevc.mov 0.39MB | 20 |
| elephant-shaped-containers | 0.25 | 0.25 (copy) | 1440×810 · 9.8s · 14 เฟรม | ✅ | hevc.mov 0.51MB | 8 |
| ganesha | 52.73 | 2.52 | 1440×810 · 30fps · 12.27s (src 2560×1440@60) | ❌ ทึบ (alpha_min 254) | poster | 34 |
| singhasari-ganesha | 10.10 | 2.72 | 1440×810 · 30fps · 10.03s | ❌ ทึบ | poster | 31 |
| tin-ingot-in-the-shape-of-an-elephant | 7.00 | 2.38 | 1440×810 · 30fps (src 60) · 10.03s | ❌ ทึบ | poster | 7 |
| walking | 0.02 | 0.02 (copy) | 1120×630 · 3.85s · 11 เฟรม (20fps container) | ❌ ทึบ (alpha_min 255) | poster | 3 |

คุณภาพไฟล์ที่ถูกบีบ (เทียบ src ย่อเป็น 1440×810 @10fps): ganesha SSIM-Y 0.983 / PSNR-Y 40.0 dB · singhasari SSIM-Y 0.974 / 37.7 dB · tin SSIM-Y 0.9997 / 58.5 dB → ไม่เสียคุณภาพชัดเจน จึงไม่เปิด O-xx

## ข้อจำกัด / ข้อสังเกต
- **ทุกไฟล์ติด tag `alpha_mode=1` แต่มี 4 ไฟล์ที่โปร่งใสจริง** (หมวด 4 ไฟล์แรก) · อีก 4 (ganesha, singhasari, tin, walking) พื้นหลังขาวทึบ ไม่โปร่ง → หน้าเว็บต้องวางบนพื้นขาว/ใกล้ขาว ไม่ใช่พื้นสี (ถ้าต้องการโปร่งใส ต้องขอไฟล์ใหม่จากผู้ทำแอนิเมชัน)
- ไฟล์ src ส่วนใหญ่เป็น **stepped animation**: เฟรมน้อย (6–30 เฟรม) ค้างเฟรมละ 0.7–2.6s · `fps` ใน manifest = `avg_frame_rate` ที่ container ประกาศ (หลอกได้) · ใช้ `frames` + `effective_fps` ดูความจริง
- HEVC-alpha ทดสอบด้วย ffprobe (codec hevc · tag hvc1 · ความยาวตรง src) และ decode ด้วย ffmpeg 8.1 แล้วพบ alpha (composite บนสีม่วงเห็นพื้นหลังโปร่ง ตรงกับ WebM) · **ยังไม่ได้ทดสอบเล่นบน Safari/iOS จริง** (ไม่มีอุปกรณ์) · ffprobe แสดง `pix_fmt=yuv420p` สำหรับ HEVC-alpha ซึ่งปกติ (alpha เป็น auxiliary layer)
- การใช้งานในหน้า: `<video>` ใส่ `<source src=hevc.mov type='video/mp4; codecs="hvc1"'>` **ก่อน** `<source src=.webm type="video/webm">` (Safari เลือกตัวแรกที่เล่นได้) · ใส่ `poster=` เสมอ · ไฟล์ทึบไม่ต้องมี hevc
- iOS ต่ำกว่า 17.4 ไม่เล่น WebM เลย → ไฟล์ทึบจะเห็นแค่ poster (ยอมรับได้ ตามเกณฑ์ DOD C4 ที่เน้นไฟล์ alpha)
- poster เลือกเฟรมอัตโนมัติ 35% — บางไฟล์ (ganesha, singhasari) อยู่กลาง transition เห็นตัวละครซ้อน/จาง · ปรับต่อไฟล์ได้ถ้าจะให้ design เลือกเฟรมเอง (เพิ่ม map ใน `encode.sh` ภายหลัง → DEFERRED)
- (ล้าสมัยแล้ว) วัตถุที่ไม่มี WebM นับแล้ว = 8 ชิ้น และตอบ O-5 ด้วย D13 แล้ว — ดูหัวข้อ media-hero-frames ด้านล่าง


---
# media-hero-frames — hero ต่อเฟรมจาก Figma (D13) สำหรับวัตถุที่ไม่มี WebM จากเจ้าของ

## วัตถุที่ไม่มี WebM (นับจริง: 15 วัตถุ page Final − 7 ที่เจ้าของส่ง = **8**)
จับคู่ WebM เจ้าของ → slug ใน seed (เก็บใน `hero-frames.spec.json` `owner_webm`): bas-relief-of-a-procession → `bas-relief-of-a-royal-or-noble-procession-…` · elephant-shaped-containers → `elephant-shaped-relic-containers` · ที่เหลือชื่อตรง · `walking` → `object_slug: null` + `usage: "home-trails"` (D12)
ไม่มี WebM 8 ชิ้น: wooden-cold-press-cake-molds… · embossed-brass-basin… · elephant-leg-bone · hand-carved-ivory-and-metal-opium-pipe · ivory-fire-piston-ivory-fire-syringe · patola-cloth · pod-duang-stamped… · siwaih

## วิธีต่อเฟรม (`hero_frames.py`)
1. เฟรมต่อวัตถุ = layer ใน hero ที่ **แสดงจริง** (ไม่ `visible:false`) เรียงตามเลขในชื่อ layer · layer opacity 0 (เฟรม 01) นับ · ขนาด = กรอบ hero ใน Figma (§4.8: 1440×810 · patola 1120×730 · leg-bone 1320×742) · patola ครอปตาม `imageTransform` (STRETCH)
2. ภาพต้นฉบับ 4096×2304 RGBA → lanczos ลงขนาด hero → VP9 **10fps CFR · เฟรมละ 0.7s (7 เฟรมวิดีโอ/ภาพ)** · `-an` · 2-pass CRF 24 (วนเพิ่ม CRF ทีละ 3 ถ้าเกิน 3MB) · `yuva420p` ถ้า alpha_min < 250 ไม่งั้น `yuv420p` · `-fflags +bitexact` (webm ไม่มี UID สุ่ม → รัน `FORCE=1` ซ้ำได้ไบต์เดิม ตรวจแล้ว) · loop ได้ (ใช้ `<video loop>`) ความยาว = จำนวนเฟรม × 0.7 เป๊ะ
3. poster = **เฟรมแรก** (≤640 กว้าง WebP q80) · มีเฟรมเดียว = `<slug>.still.webp` (ขนาด hero เต็ม) + poster · fallback Safari/iOS เหมือนเดิม (alpha → `<slug>.hevc.mov` · ทึบ → poster)
4. ตรวจคุณภาพ: ถอดเฟรมกลางแต่ละช่วง 0.7s เทียบภาพต้นฉบับที่ย่อแล้ว (วางบนพื้นขาว) SSIM-Y ต่ำสุดต่อวัตถุ: embossed-brass-basin 0.989 · opium-pipe 0.998 · ivory-fire-piston 0.997 · patola 0.991 · pod-duang 0.9997 · siwaih 0.9995 · cake-molds 0.998 (พิสูจน์ลำดับ/จังหวะเฟรมด้วย)

## ตารางผล (วัดจาก ffprobe/`ls`)
| slug (= ชื่อไฟล์ out) | เฟรม | ต้นฉบับรวม (MB) | out (MB) | วิดีโอ | alpha จริง | fallback | poster (KB) |
|---|---|---|---|---|---|---|---|
| wooden-cold-press-cake-molds-with-elephant-horse-and-fowl-motifs | 12 | 10.95 | 0.43 | 1440×810 · 8.4s · 10fps | ❌ ทึบ | poster | 9.7 |
| embossed-brass-basin-with-mythical-animal-motifs | 15 | 61.37 | 0.22 | 1440×810 · 10.5s | ❌ ทึบ | poster | 7.8 |
| hand-carved-ivory-and-metal-opium-pipe | 6 | 9.63 | 0.17 | 1440×810 · 4.2s | ❌ ทึบ | poster | 7.0 |
| ivory-fire-piston-ivory-fire-syringe | 15 | 20.39 | 0.23 | 1440×810 · 10.5s | ✅ | hevc.mov 0.75MB (10.5s) | 15.9 |
| patola-cloth | 9 (seed นับ 5 — P-301) | 0.39 | 0.21 | 1120×730 · 6.3s | ✅ | hevc.mov 0.77MB (6.3s) | 24.7 |
| pod-duang-stamped-with-the-chakra-and-great-crown-marks-reign-of-king | 15 | 12.93 | 0.47 | 1440×810 · 10.5s | ❌ ทึบ | poster | 7.5 |
| siwaih | 15 | 10.14 | 0.19 | 1440×810 · 10.5s | ❌ ทึบ | poster | 4.6 |
| elephant-leg-bone | 1 (ซ่อน 5 — P-301) | 1.65 | 0.14 (`.still.webp` 1320×742) | **ภาพนิ่ง ไม่มีวิดีโอ** | ✅ (โปร่งใส) | — | 43.3 |

ทุก WebM ≤ 0.48MB (เกณฑ์ 3MB) · ไม่มี O-xx เรื่องขนาด

## manifest (`content/media-manifest.json` — 16 รายการ)
ทุกรายการมี `object_slug` (slug ใน seed · walking = `null`) · `source` (`owner-webm` | `figma-frames`) · `kind` (`video` | `still`) · `usage` (`object-hero` | `home-trails`) · รายการ `figma-frames` มีเพิ่ม `figma_frame_id` `figma_frame_name` `frame_refs[]` `steps` `frame_duration_s` `crf` · `kind: still` → `out: null`, `fallback: null`, ใช้ `still` · รายการ owner-webm 8 รายการเดิมค่าเท่าเดิมทุกฟิลด์ (เทียบก่อน/หลัง · เพิ่มแค่ 4 ฟิลด์ข้างต้น)
การจับคู่วัตถุ → ไฟล์: ใช้ `object_slug` (ไม่ใช่ `id`/ชื่อไฟล์ของ owner-webm 2 ตัวที่ชื่อต่างจาก seed)

## ข้อจำกัด (เพิ่ม)
- ภาพ hero 3 ชุดมี alpha จริง (ivory-fire-piston · patola · leg-bone) — Figma มี SOLID ขาวรองใต้ภาพ → ใช้พื้น `#FFF` หลังวิดีโอ (P-302)
- patola: เฟรม 01 เกือบว่าง (แอนิเมชันทอผ้า) → poster (เฟรมแรกตามสั่ง) แทบไม่เห็นลาย (DEFERRED)
- ตัวเลข `fps`=10 ของ figma-frames เป็น CFR จริง (ต่างจาก owner-webm ที่เป็น VFR stepped: ใช้ `steps` + `frame_duration_s` ดูจำนวนภาพจริง) · เฟรมซ้ำกินไบต์ต่ำมาก
- ไฟล์ out ที่สร้างเพิ่มข้าง WebM: `<slug>.frames.json` (sidecar: alpha ต่อเฟรม · crf — manifest.py อ่าน) · ทั้งหมดอยู่ใน `Unesco/media/out/` ไม่ commit
- ต้องมี `Unesco/design/assets/` (ภาพเฟรม 144 ref · ไม่ commit) ถึงรัน hero_frames.py ได้ · ไม่มี → ใช้ `SKIP_HERO_FRAMES=1` ได้ถ้า `media/out/` มีไฟล์ hero + `<slug>.frames.json` ครบแล้ว (manifest ยังได้ 16 รายการ) · ถ้าไม่มี sidecar manifest.py จะ exit ≠ 0 ไม่ตัดรายการ

## walking (home-trails) — เปลี่ยนเป็นไฟล์ "รอยเท้าเร็วขึ้น" (BACKLOG #5 · task `media-walking-v2` · 2026-11-11)
- ต้นฉบับใหม่ `media/src/drive-2026-11-11/walking-edit-1.webm` (sha256 `a973c118…1559e3`) วางทับ `media/src/File Animated/Homepage/walking.webm.webm` (ตัวเดิม 8.8s เก็บไว้ที่ `media/src/walking-v1-8.8s.webm.bak`) → ชื่อ out/manifest id คงเดิม `walking` · `encode.sh` จึงรันซ้ำได้โดยไม่ต้องแก้สคริปต์ (`SKIP_HERO_FRAMES=1 bash scripts/media/encode.sh`)
- ffprobe/libvpx decode: VP9 · 1120×630 · 3.85s · 11 เฟรมจริง (เฟรมละ 0.35s) · 24,373 B · tag `alpha_mode=1` แต่ alpha plane **ทึบทุกเฟรม (alpha_min 255)** เหมือนตัวเดิม → ตามมาตรฐาน: copy WebM (≤3MB ไม่ re-encode) · **ไม่สร้าง HEVC-alpha** (ไม่มี alpha ให้เก็บ · fallback = poster) · ต้องวางบนพื้นขาว
- poster = เฟรมที่ 35% (1.35s · เห็นรอยเท้าครบหลายแบบ) · 2,764 B · ไบต์เหมือนตัวเดิม (เฟรมนี้เหมือนกันทั้งสองไฟล์)
- ภาพจบไฟล์: รอยเท้าจางลงเกือบหมด (ออกแบบให้ loop ต่อเนื่อง)


---
# media-1117 — วัตถุ 16 trail + ภาพ/GIF Intro (2026-11-17 · D49 · D50 · BACKLOG #29 #30)

ต้นฉบับ (เจ้าของอนุมัติให้ดาวน์โหลด · อ่านอย่างเดียว): `Unesco/media/src/drive-2026-11-17/{animals-webm,introduction}` → ผล `Unesco/media/out/{objects-1117/<trail-slug>/,intro-1117/<chapter-slug>/}` (ไม่ commit) · manifest: เพิ่ม entry ท้าย `content/media-manifest.json` (16 entry เดิมไม่เปลี่ยนไบต์)

## วิธีรัน
```bash
bash scripts/media/encode-1117.sh          # ทำทั้งชุด (วัตถุ 126 + Intro 18) แล้วเพิ่ม/แทน entry 1117 ใน manifest
FORCE=1 bash scripts/media/encode-1117.sh  # ทำใหม่ทุกไฟล์ (HEVC-alpha ไม่ deterministic ระดับไบต์ — ชุดนี้ไม่มีไฟล์ alpha จึงไม่เกิด)
```
- `scripts/media/encode-1117.sh` → `objects_1117.py` · env: `LIMIT_BYTES` (3000000) `MAXW` (1440) `MAXFPS` (30) `POSTER_AT` (0.35) `INTRO_MAXW` (2880) `JOBS` (4) `MEDIA_SRC_1117`
- รันซ้ำได้ผลเดิม: ข้ามไฟล์ที่ out ใหม่กว่า src · manifest ไม่มี timestamp (ทดสอบรัน 2 ครั้ง `cmp` ตรงกัน) · `encode.sh` เดิมรันซ้ำจะคง entry `drive-1117/` `intro-1117/` ไว้ (`manifest.py` เก็บให้ · ทดสอบแล้ว)
- มาตรฐาน = เดียวกับชุดช้าง: src ≤ 3MB และกว้าง ≤ 1440 → copy ไม่ re-encode · ไม่งั้น VP9 2-pass `-an` ≤ 1440 กว้าง ≤ 30fps ≤ 3MB · poster เฟรม 35% WebP q80 กว้าง ≤ 640 · alpha จริง (decode libvpx-vp9 วัด alpha min < 250) → `.hevc.mov` (hevc_videotoolbox) · ทึบ → fallback = poster

## key + ชื่อไฟล์
- manifest `id` = `key` = `drive-1117/<โฟลเดอร์ trail>/<ชื่อไฟล์ต้นฉบับ>` ตรงตัวอักษร (รวม NBSP · ช่องว่างคู่ · Đ) = path ใต้ `animals-webm/` · เพิ่ม `key_normalized` (NFKC · NBSP/ช่องว่างซ้ำ → 1 ช่อง · ตัดช่องว่างก่อน `.webm`) สำหรับจับคู่แบบทน · Intro: `intro-1117/<โฟลเดอร์>/<ชื่อไฟล์>`
- ชื่อ out = ASCII kebab-case · `Đ→d` · ตัด `'`/`’` (`Iban’s` → `ibans`) · อักษรไทยตัดทิ้ง (`tin ingots ไก่` → `tin-ingots`) · `พิมพ์เค้กม้า` ไม่มี ASCII → ตั้งเป็น `horse-cake-mold` (ตัดสินเอง)
- **basename ไม่ซ้ำทั้ง media/out** (importer เดิมหาไฟล์จาก `media/out/<basename>` และใช้ `media:<basename>` เป็น import key): ชื่อที่ซ้ำข้าม trail หรือซ้ำไฟล์เดิม → ต่อท้าย `-<trail-slug>` ทุกตัวที่ซ้ำ (เช่น `lacquered-ceremonial-vessel-lion`) · Intro ชื่อซ้ำข้ามบท → ต่อท้าย `-1-2`/`-1-3`
- trail-slug ตาม `content/seed/trails.json`: Chicken&Rooster→`rooster` · Serpent→`naga` · Waterbird→`water-bird` · ที่เหลือชื่อโฟลเดอร์ตัวเล็ก
- entry วัตถุ: รูปแบบเดิม (`src out poster fallback bytes src_bytes poster_bytes duration_s width height fps frames effective_fps has_alpha alpha_tag alpha_min_src alpha_min_out src_* within_limit` — import ใช้อยู่) + `key key_normalized trail trail_slug source:"drive-2026-11-17" src_has_audio encode src_content_md5 src_sha256 dup_of` · `object_slug: null` (design-dev/core จับคู่ด้วย `key`) · `usage: "object-hero"`

## ผล: วัตถุ 126 ไฟล์ (ตัวเลขจาก ffprobe/`ls`)
- ต้นฉบับทั้งหมด: VP9 `yuv420p` · **ไม่มี tag `alpha_mode` และ alpha plane ทึบทั้ง 126 ไฟล์ (alpha_min = 255 ทุกไฟล์ วัดด้วย libvpx-vp9 ทุก 0.5s)** → ไม่มี HEVC-alpha · fallback ทุกไฟล์ = poster (type `poster-only`) · ต้องวางบนพื้นขาว/ใกล้ขาวเหมือนชุดช้างที่ทึบ · ไม่มีเสียง (src_has_audio = false ทุกไฟล์)
- ขนาดเฟรม: 1440×810 เป็นส่วนใหญ่ · แปลก: 1440×808 ×2 · 1410×808 ×2 · 1440×792 ×2 · 1440×858 · 1440×958 · 1440×960 · 1440×966 · **456×810 ×1** · 1920×1080 ×3 (ย่อเป็น 1440×810) · fps container 1.25–20 (stepped animation เหมือนเดิม — ดู `frames`/`effective_fps`) · ความยาว 2.4–20.4s
- ไฟล์ใหญ่สุด 1.14MB (Turtle/Koffo Cloth) → **ทุกไฟล์ ≤ 3MB ไม่มีเรื่องขนาด ไม่เปิด O-xx**
- re-encode เฉพาะ 3 ไฟล์ที่ src กว้าง 1920: `Chicken&Rooster/Ayam-Ayam Bernaga` · `Dragon/Ayam-Ayam Bernaga` (0.280→0.241MB) · `Dragon/Cannon with dragon motif` (0.085→0.062MB) · เพดานบิตเรต = บิตเรตของ src (ไม่ให้ใหญ่กว่า src) · SSIM(All) vs src ย่อ 1440×810: 0.996 / 0.998 · ที่เหลือ 123 ไฟล์ copy
- ถอดรหัสครบทุกไฟล์ out ด้วย libvpx-vp9 ไม่มี error · ไฟล์ซ้ำ (decode แล้วเฟรมเหมือนกัน · ต่างแค่ UID ของ mux): 6 คู่ — ดู `dup_of` ใน manifest (ไม่ตัดทิ้ง: แต่ละ trail ถือไฟล์ของตัวเอง) · ชื่อเดียวกันแต่เนื้อหาต่าง (คนละแอนิเมชัน): Turtle/Waterbird Dragon-head-tortoise · Chicken/Seashell Mother of Pearl · Phoenix/Bird Pending belt buckle · Lion/Serpent Embossed brass basin · Buffalo/Lion/Seashell Lacquered vessel · Garuda/Horse Liturgical pulpit covers · Phoenix vs Chicken/Dragon Rooster-headed brass · Đông Sơn drum (Waterbird/Seashell)

| trail (โฟลเดอร์ → slug) | ไฟล์ | src (MB) | out (MB) | alpha จริง | fallback | poster (KB รวม) |
|---|---|---|---|---|---|---|
| Bird → `bird` | 8 | 2.42 | 2.42 | 0 | poster ×8 | 238 |
| Buffalo → `buffalo` | 6 | 1.09 | 1.09 | 0 | poster ×6 | 60 |
| Chicken&Rooster → `rooster` | 11 | 1.66 | 1.62 | 0 | poster ×11 | 94 |
| Deer → `deer` | 4 | 0.20 | 0.20 | 0 | poster ×4 | 21 |
| Dragon → `dragon` | 10 | 1.84 | 1.78 | 0 | poster ×10 | 109 |
| Fish → `fish` | 8 | 1.06 | 1.06 | 0 | poster ×8 | 53 |
| Garuda → `garuda` | 5 | 2.27 | 2.27 | 0 | poster ×5 | 183 |
| Hornbill → `hornbill` | 7 | 0.87 | 0.87 | 0 | poster ×7 | 56 |
| Horse → `horse` | 7 | 1.70 | 1.70 | 0 | poster ×7 | 180 |
| Lion → `lion` | 6 | 1.56 | 1.56 | 0 | poster ×6 | 62 |
| Makara → `makara` | 3 | 0.53 | 0.53 | 0 | poster ×3 | 31 |
| Phoenix → `phoenix` | 8 | 1.67 | 1.67 | 0 | poster ×8 | 176 |
| Seashell → `seashell` | 14 | 2.41 | 2.41 | 0 | poster ×14 | 204 |
| Serpent → `naga` | 13 | 2.54 | 2.54 | 0 | poster ×13 | 187 |
| Turtle → `turtle` | 5 | 1.47 | 1.47 | 0 | poster ×5 | 130 |
| Waterbird → `water-bird` | 11 | 1.78 | 1.78 | 0 | poster ×11 | 123 |
| **รวม** | **126** | **25.07** | **24.97** | **0** | poster ×126 | 1914 |

รายไฟล์ทั้ง 126 (key · ขนาด · ความยาว · เฟรม · out) อ่านจาก `content/media-manifest.json` (`jq '.files[]|select(.id|startswith("drive-1117/"))'`) · ตัวอย่าง: `drive-1117/Turtle/Koffo Cloth.webm` → `media/out/objects-1117/turtle/koffo-cloth.webm` + `.poster.webp`

## เพิ่มไฟล์ Makara 2 ไฟล์ (makarawebm-1117 · BACKLOG #33 · อัปโหลด Drive 15:21/15:26 · ดาวน์โหลด 16:13)
ต้นฉบับ: `media/src/drive-2026-11-17/animals-webm/Makara/{Saltwater crocodile skull,Sungai Mas makara}.webm` (อ่านอย่างเดียว) → `media/out/objects-1117/makara/` · รัน: `ONLY="Makara/Saltwater crocodile skull.webm|Makara/Sungai Mas makara.webm" bash scripts/media/encode-1117.sh` (env `ONLY` ใหม่ — ทำเฉพาะไฟล์ที่ระบุ · ไม่แตะ Intro · entry อื่นคงไบต์เดิม · คำนวณ `dup_of` ใหม่ทั้งชุดด้วยกติกาเดิม · รันซ้ำได้ผลเดิม)
- **สองไฟล์นี้ต่างจาก 126 ไฟล์ก่อนหน้า: มี alpha จริง** — ffprobe: VP9 `yuva420p` · tag `alpha_mode=1` · วัด alpha min = 0 ทั้งต้นฉบับและ out (libvpx-vp9 ทุก 0.5s) → เป็นไฟล์ alpha ชุดแรกของรอบ 2026-11-17 · ต้องมี fallback Safari/iOS (DOD C4) → `.hevc.mov` (HEVC-alpha `hevc_videotoolbox -alpha_quality 0.75 -tag:v hvc1` 10fps · คงความยาว 3.6s) + poster
- ประวัติ: 1440×810 · 5/3 fps (6 เฟรม · เฟรมละ 0.6s = stepped เหมือนชุดอื่น) · 3.6s · ไม่มีเสียง · ≤ 3MB → copy ไม่ re-encode (ตามมาตรฐาน) · ไม่ซ้ำกับไฟล์อื่น (`dup_of: null`)
- poster = เฟรม 35% WebP q80 กว้าง 640 (alpha_q 90 · เก็บพื้นโปร่ง) · ดูด้วยตาแล้ว ตัวละครชัด
- HEVC-alpha: ffprobe เห็น codec `hevc` tag `hvc1` `yuv420p` 1440×810 10fps 3.6s (ffprobe ไม่แสดง alpha layer ของ HEVC — ตรวจทางอ้อมแทน: encode ตัวเดียวกันแบบไม่มี alpha ได้ 139,479 B เทียบกับ 218,571 B ที่ใส่ alpha → มี alpha layer จริง · ยังไม่ได้เล่นใน Safari จริง)

| key (หลัง `drive-1117/Makara/`) | ก่อน (MB) | หลัง (MB) | alpha | fallback | poster (KB) | out |
|---|---|---|---|---|---|---|
| Saltwater crocodile skull.webm | 0.126 (125,722 B) | 0.126 (copy) | จริง (min 0) | `.hevc.mov` 192,120 B | 16.3 | `objects-1117/makara/saltwater-crocodile-skull.{webm,poster.webp,hevc.mov}` |
| Sungai Mas makara.webm | 0.164 (164,397 B) | 0.164 (copy) | จริง (min 0) | `.hevc.mov` 218,571 B | 11.9 | `objects-1117/makara/sungai-mas-makara.{webm,poster.webp,hevc.mov}` |

ตารางสรุป trail ด้านบนนับเฉพาะ 126 ไฟล์รอบแรก — รวมหลังเพิ่ม: 128 ไฟล์ · Makara 5 ไฟล์ (alpha จริง 2 · fallback HEVC 2 · poster ×3 + ×2)

## ผล: Intro (`media/out/intro-1117/<chapter>/`) — 18 ไฟล์ ต้นฉบับ 30.68 MB → 4.40 MB
- **ภาพนิ่ง (PNG/JPG 11 ไฟล์ → WebP)**: กว้าง ≤ 2880 (= 2× 1440 · คงสัดส่วน · ไม่ขยาย) · lossy q82 `alpha_q` 95 (PNG มี alpha ทุกไฟล์ · JPG ไม่มี) · ทดลองแล้ว lossless 0.5–1.1MB/ไฟล์ vs lossy 0.28–0.46MB → lossy · แหล่งที่เป็น sRGB/ไม่ติด profile จึงไม่ต้องฝัง ICC · entry `kind:"still"` `out:null` `still:<path>` (รูปแบบเดียวกับ still ของ elephant-leg-bone)
- **GIF 4 ไฟล์ → animated WebP** (`gif2webp -mixed -q 80 -m 6`) · `kind:"animation"` · `out` = `.webp` (ใช้ใน `<img>` เหมือน GIF ไม่ต้อง JS) · `poster` = เฟรมแรก (WebP กว้าง ≤ 1440) · เหตุผลที่ไม่ใช้วิดีโอ: GIF มีพื้นโปร่งใส (GCE transparency) → WebM alpha เล่น Safari ไม่ได้ · animated WebP รองรับ alpha และ Safari 14+/iOS 14+ · เล็กกว่า GIF 2.7–3.9× · จังหวะเฟรมคงเดิม (330ms/เฟรม · farmer 250ms) · วนไม่จบ (loop 0) · จำนวนเฟรมเท่าต้นฉบับ
- ข้อจำกัด: Safari < 14 / iOS < 14 ไม่เล่น animated WebP → เห็น poster (ยอมรับ ระดับเดียวกับ WebM iOS < 17.4 ของชุดช้าง) · `Intro logo.png` อยู่ 2 บท + `1.1People and Place-01.png` เป็นไฟล์เดียวกัน (sha256 เท่ากัน) → out แยกต่อบท (93KB ต่อไฟล์)

| key (หลัง `intro-1117/`) | ประเภท | src (KB) | out (KB) | ขนาด out | เฟรม |
|---|---|---|---|---|---|
| 1.1 People and Place/1.1People and Place-01.png | ภาพนิ่ง WebP | 218 | 93 | 2617×2486 | 1 |
| 1.1 People and Place/1.1People and Place-02.png | ภาพนิ่ง WebP | 520 | 327 | 2880×2132 | 1 |
| 1.1 People and Place/1.1People and Place-03.png | ภาพนิ่ง WebP | 264 | 110 | 2617×2486 | 1 |
| 1.1 People and Place/Boat.gif | animated WebP | 154 | 56 | 1920×1080 | 6 |
| 1.1 People and Place/Hae Nang Maew.gif | animated WebP | 856 | 232 | 1920×1080 | 6 |
| 1.1 People and Place/map1.1 people place.gif | animated WebP | 2639 | 681 | 1920×1080 | 13 |
| 1.2 Early Centres of Power/1.1People and Place-05.png | ภาพนิ่ง WebP | 886 | 249 | 2880×1116 | 1 |
| 1.2 Early Centres of Power/1.1People and Place-06.png | ภาพนิ่ง WebP | 409 | 162 | 2880×1332 | 1 |
| 1.2 Early Centres of Power/1.1People and Place-07.png | ภาพนิ่ง WebP | 88 | 69 | 2777×3372 | 1 |
| 1.2 Early Centres of Power/1.2 power.png | ภาพนิ่ง WebP | 3372 | 279 | 2880×1620 | 1 |
| 1.2 Early Centres of Power/Intro logo.png | ภาพนิ่ง WebP | 218 | 93 | 2617×2486 | 1 |
| 1.3 Rice and Spice_ Platforms of Interconnection/1.3 Rice and Spice-farmer.gif | animated WebP | 1291 | 364 | 1920×1080 | 7 |
| 1.3 Rice and Spice_ Platforms of Interconnection/Chicken1.3.png | ภาพนิ่ง WebP | 937 | 461 | 2880×1112 | 1 |
| 1.3 Rice and Spice_ Platforms of Interconnection/Intro logo.png | ภาพนิ่ง WebP | 218 | 93 | 2617×2486 | 1 |
| 1.3 Rice and Spice_ Platforms of Interconnection/Map 1.3 Environment.jpg | ภาพนิ่ง WebP | 7847 | 281 | 2880×1620 | 1 |
| 1.3 Rice and Spice_ Platforms of Interconnection/PAWANG.png | ภาพนิ่ง WebP | 234 | 302 | 2617×3108 | 1 |
| 1.4 Animals, Folklore, and Ecological Stewardship/1.1Animals.png | ภาพนิ่ง WebP | 3413 | 288 | 2880×1008 | 1 |
| 1.4 Animals, Folklore, and Ecological Stewardship/map1.4.jpg | ภาพนิ่ง WebP | 7106 | 245 | 2880×1620 | 1 |

## ข้อควรระวังสำหรับผู้ใช้ manifest (core-1117)
- **importer เดิมหาไฟล์จาก `media/out/<basename ของ out/poster/still>` (แบนโฟลเดอร์)** แต่ชุดนี้อยู่ใต้ `objects-1117/<trail>/` · `intro-1117/<บท>/` ตามที่สั่ง → ต้องให้ importer ใช้ path จาก manifest (`media/out/...` ต่อจาก `Unesco/`) หรือเปิด P-303 ในไฟล์ PENDING · basename ทุกไฟล์ไม่ซ้ำกันทั้ง `media/out` แล้ว (ตรวจแล้ว) จึงแบนโฟลเดอร์ได้ถ้าเลือกทางนั้น
- `kind:"animation"` เป็นค่าใหม่ (importer เดิมรู้แค่ `video`/`still`) · ใช้ `out` + `poster` ได้เหมือน video แต่ไม่มี HEVC · แสดงด้วย `<img src=out>` ไม่ใช่ `<video>`
- ยังไม่ได้ทดสอบเล่นในเบราว์เซอร์จริง (Chrome/Safari) — ตรวจด้วย ffmpeg decode ทั้ง 126 ไฟล์ + webpmux (เฟรม/ดีเลย์/loop) + ดู poster/ภาพ Intro ด้วยตา
