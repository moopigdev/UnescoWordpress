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
