# MEDIA — WebM + poster + fallback Safari/iOS

เจ้าของ: media-dev · task `media-encode` · ตัวเลขทุกตัวมาจาก ffprobe/ffmpeg จริง (สร้างอัตโนมัติใน `content/media-manifest.json`)

## วิธีรัน
```bash
bash scripts/media/encode.sh            # encode + poster + fallback + เขียน content/media-manifest.json
FORCE=1 bash scripts/media/encode.sh    # ทำใหม่ทุกไฟล์
```
- ต้นฉบับ: `Unesco/media/src/File Animated/**.webm` (ไม่แตะ · ไม่ commit) → ผล: `Unesco/media/out/` (ไม่ commit)
- ปรับได้ด้วย env: `MEDIA_SRC` `MEDIA_OUT` `LIMIT_BYTES` (default 3000000) `MAXW` (1440) `MAXFPS` (30) `POSTER_AT` (0.35 = ตำแหน่งเฟรม poster) `FALLBACK_FPS` (10)
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
| walking | 0.02 | 0.02 (copy) | 1120×630 · 8.8s · 11 เฟรม | ❌ ทึบ (alpha_min 255) | poster | 3 |

คุณภาพไฟล์ที่ถูกบีบ (เทียบ src ย่อเป็น 1440×810 @10fps): ganesha SSIM-Y 0.983 / PSNR-Y 40.0 dB · singhasari SSIM-Y 0.974 / 37.7 dB · tin SSIM-Y 0.9997 / 58.5 dB → ไม่เสียคุณภาพชัดเจน จึงไม่เปิด O-xx

## ข้อจำกัด / ข้อสังเกต
- **ทุกไฟล์ติด tag `alpha_mode=1` แต่มี 4 ไฟล์ที่โปร่งใสจริง** (หมวด 4 ไฟล์แรก) · อีก 4 (ganesha, singhasari, tin, walking) พื้นหลังขาวทึบ ไม่โปร่ง → หน้าเว็บต้องวางบนพื้นขาว/ใกล้ขาว ไม่ใช่พื้นสี (ถ้าต้องการโปร่งใส ต้องขอไฟล์ใหม่จากผู้ทำแอนิเมชัน)
- ไฟล์ src ส่วนใหญ่เป็น **stepped animation**: เฟรมน้อย (6–30 เฟรม) ค้างเฟรมละ 0.7–2.6s · `fps` ใน manifest = `avg_frame_rate` ที่ container ประกาศ (หลอกได้) · ใช้ `frames` + `effective_fps` ดูความจริง
- HEVC-alpha ทดสอบด้วย ffprobe (codec hevc · tag hvc1 · ความยาวตรง src) และ decode ด้วย ffmpeg 8.1 แล้วพบ alpha (composite บนสีม่วงเห็นพื้นหลังโปร่ง ตรงกับ WebM) · **ยังไม่ได้ทดสอบเล่นบน Safari/iOS จริง** (ไม่มีอุปกรณ์) · ffprobe แสดง `pix_fmt=yuv420p` สำหรับ HEVC-alpha ซึ่งปกติ (alpha เป็น auxiliary layer)
- การใช้งานในหน้า: `<video>` ใส่ `<source src=hevc.mov type='video/mp4; codecs="hvc1"'>` **ก่อน** `<source src=.webm type="video/webm">` (Safari เลือกตัวแรกที่เล่นได้) · ใส่ `poster=` เสมอ · ไฟล์ทึบไม่ต้องมี hevc
- iOS ต่ำกว่า 17.4 ไม่เล่น WebM เลย → ไฟล์ทึบจะเห็นแค่ poster (ยอมรับได้ ตามเกณฑ์ DOD C4 ที่เน้นไฟล์ alpha)
- poster เลือกเฟรมอัตโนมัติ 35% — บางไฟล์ (ganesha, singhasari) อยู่กลาง transition เห็นตัวละครซ้อน/จาง · ปรับต่อไฟล์ได้ถ้าจะให้ design เลือกเฟรมเอง (เพิ่ม map ใน `encode.sh` ภายหลัง → DEFERRED)
- ยังไม่ทราบว่าวัตถุไหนใน seed ไม่มี WebM — เทียบกับ seed หลัง design-extract (คำถาม O-5 ข้อ "ภาพนิ่ง" ยังรอเจ้าของ)
