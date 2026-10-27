# DEFERRED — ของใหม่ที่เจอระหว่างทำ ยังไม่ทำ

dev/reviewer จดที่นี่แทนการทำเพิ่มหรือส่งกลับ · orchestrator คัดเข้า PLAN ขั้น 3 หรือทิ้ง
รูปแบบ: `- [task-id] [วันที่] <สิ่งที่เจอ> — <ทำไมไม่ทำตอนนี้>`

- [core-env] [2026-10-27] `scripts/local.sh` ยังไม่ import seed (`content/seed/*.json`) — เป็นงาน `core-contract` (เพิ่ม step ใน `.playground/blueprint.json` ที่ local.sh สร้าง หรือเรียก `scripts/import`)
- [core-env] [2026-10-27] `assets/css/tokens.css` เป็น placeholder ทั้งไฟล์ (ป้าย TODO(design-extract)) — เติมค่าจริงเมื่อ DESIGN-SPEC มี Tokens · ชื่อตัวแปรอาจเปลี่ยน
- [core-env] [2026-10-27] WP version บน production ไม่รู้ — local ใช้ `latest` (ตอนนี้ 7.1.3) · ถ้ารู้แล้วตั้ง `SH_WP=<ver>` ให้ตรง
- [core-env] [2026-10-27] smoke ทดสอบเฉพาะ `/` — งานหน้าเพิ่ม path ของตัวเองใน `docs/smoke-urls` (core ถือไฟล์ → แจ้ง core/orchestrator)
- [core-env] [2026-10-27] scripts/local.sh:111-113 `--stop` kill ทุก process ที่ listen บน port ใน .playground/port — ถ้า server ของ worktree นี้ตายเองแล้ว worktree อื่นได้ port เดิมไป `--stop` จะปิดของอีกตัว · และ auto-port (local.sh:135) ไม่กันกรณีสอง worktree เริ่มพร้อมกัน — edge case นอก DOD
- [media-encode/reviewer] [2026-10-27] scripts/media/encode.sh `alpha_min` คืนค่าว่าง (decode libvpx-vp9 ล้ม) → `${amin:-255}` ถือว่าทึบ · manifest.py `alpha_min` ไม่มีค่า → 255 → has_alpha=false ไม่บังคับ fallback — ไฟล์ใหม่ที่ decode ไม่ได้จะถูกทิ้ง alpha เงียบๆ · ไฟล์ชุดปัจจุบันวัดซ้ำแล้วถูกต้อง จึงไม่ใช่ F
- [media-encode/reviewer] [2026-10-27] ไฟล์ทึบที่ re-encode (ganesha/singhasari/tin) ยังติด SimpleTag `ALPHA_MODE=1` ที่คัดจาก src (pix_fmt จริง yuv420p) — ไม่กระทบการเล่น แต่ probe ด้วย tag จะเข้าใจผิดว่ามี alpha · ใส่ `-metadata:s:v ALPHA_MODE=` ตอน encode ถ้าจะเก็บกวาด
- [media-encode/reviewer] [2026-10-27] poster กว้าง 640 ขณะวิดีโอกว้างถึง 1440 — ตอน first paint บน desktop poster จะถูกขยายเห็นเบลอ · ให้เจ้าของ/งานหน้า (B4) ตัดสินว่าต้องการ poster ใหญ่ขึ้นหรือไม่
- [media-encode] [2026-10-27] poster เลือกเฟรมอัตโนมัติ 35% — ganesha/singhasari เห็นตัวซ้อนกลาง transition; เลือกเฟรมต่อไฟล์ (map ใน encode.sh) — ยังไม่มีผู้ใช้ poster ให้ตัดสิน
- [media-encode] [2026-10-27] ทดสอบ HEVC-alpha บน Safari/iOS จริง — ไม่มีอุปกรณ์ในเครื่องนี้ (ทดสอบได้แค่ ffprobe/ffmpeg)
- [media-encode] [2026-10-27] ไฟล์ทึบ 4 ตัวไม่มี fallback วิดีโอ (iOS<17.4 เห็น poster) — ถ้าต้องการ ทำ H.264 mp4 เพิ่มได้ ไม่อยู่ใน DOD C4
- [design-extract] [2026-10-27] **#1 re-export frame PNG ด้วย `use_absolute_bounds=true` (ให้กว้าง 1440 พอดี) + export detail frame ที่เหลือ (Page 2.1 ~138 ใบ ตาม `design/detail-frames.list`) + frame ใหม่ที่ต้องใช้ screenshot diff** — ติด Figma render rate limit (429 Retry-After 398,727 s ≈ 2026-11-01) · รัน `bash design/export-detail-frames.sh` และ `bash design/figma-fetch.sh images 1 <name>=<id>…` หลังพ้นเวลา
- [design-extract] [2026-10-27] ไอคอน/ภาพ vector ที่ export 2x: ทำเป็น SVG แล้ว 4 ชิ้น (`design/assets/svg/`: unesco-logo, back-arrow, map-arrow-right, close-cross) · ภาพ raster ใช้ต้นฉบับจาก Figma (`design/assets/<imageRef>.png|jpg` 307 ไฟล์ ≥ 2x ส่วนใหญ่) ไม่ render ซ้ำ
- [design-extract] [2026-10-27] ภาพเฟรม GIF ของ hero วัตถุ (427 ref) ยังไม่ดาวน์โหลด (ขนาดใหญ่) — media-dev (O-5) ดึงด้วย `figma-fetch.sh dl <ref>` หรือใช้ไฟล์ Drive
- [design-extract] [2026-10-27] เมนูโลโก้ TEMASEK/SHARED HERITAGE ฝังในภาพพื้น Header #1 (`709572bf`) — ถ้าต้องแยกโลโก้ ต้องขอไฟล์ต้นฉบับ
- [design-extract] [2026-10-27] hover ของ "Read more >>" แบบ Barlow Condensed (intro/trail/timeline) ไม่มี variant ใน Figma — ใช้พฤติกรรมเดียวกับ Home (Bold + underline `#D65F47`)
- [design-extract/reviewer] [2026-10-27] seed `tin-ingots-in-the-shape-of-chicken-2` (turtle) caption เป็นเรื่องแผ่นทอง · `flintlock-firearm-2` (naga) caption เป็นเรื่องต่างหู — ชื่อ/ข้อความน่าจะค้างจาก frame อื่น แต่ `status` ไม่ติดป้าย stale → ให้เจ้าของยืนยันก่อนเผยแพร่ (นอก D7 trail ช้าง)
- [design-extract/reviewer] [2026-10-27] figma-fetch.sh ส่ง token ใน argv ของ curl (`-H "X-Figma-Token: …"`) เห็นใน `ps` ชั่วขณะ · ไม่ลงไฟล์ · ควรใช้ `-H @file`/stdin
- [design-extract/reviewer] [2026-10-27] caption rooster-headed charcoal iron frame 1179:4723 ขาดจุดท้ายเทียบ 1170:3571 · ไม่บันทึกใน caption_variants
- [design-extract] [2026-10-27] **ภาพที่ดาวน์โหลดไว้ใน design/assets/ ก่อนหน้านี้ปนชั้น fill ที่ซ่อน (`visible:false`)** — seed ถูกแก้ให้ใช้ชั้นที่แสดงจริงแล้ว (รอบ review F2/F3) แต่ยังมี **59 imageRef ที่แสดงจริงและยังไม่ได้ดาวน์โหลด** (รายการ `design/assets-missing-refs.txt`; ภาพเฟรม GIF 1440×810 ของ hero ไม่นับ) → ดึงด้วย `bash design/figma-fetch.sh fills` แล้ว `dl <ref>…` เมื่อได้รับอนุญาตให้เรียก Figma ได้อีก (รอบนี้ห้ามเรียก)
- [design-extract] [2026-10-27] seed `tile_image`/`tile_image_hover` ต่อวัตถุ (component `Ele NN` และ trail อื่น) ยังไม่ใส่ใน objects.json — ระบุ node/ref ตัวอย่างใน DESIGN-SPEC §5.1 แล้ว · เติมเมื่อขยายนอก D7
- [design-extract/reviewer] [2026-10-27] §3.1 + §5.6 `logo_unesco` (670:1088): ใน Header #1 instance ที่ใช้จริง กลุ่มโลโก้ vector และเมนู utility `Frame 12653` (Visit…Contact) ถูกซ่อน · ที่แสดงคือภาพพื้น `709572bf` (โลโก้ UNESCO raster #0072BB · ไม่มีเมนู utility) แต่ spec เขียนว่าแสดงทั้งสอง
- [design-extract/reviewer] [2026-10-27] §5.1 ตัวอย่าง text_color `#343A40` แต่ seed Ganesha `detail_style.text_color` = `#212529`
- [design-extract/reviewer] [2026-10-27] §5.5 `read_more_label` อ้าง node `670:1226` ซึ่งถูกซ่อนในหน้า 1471
- [design-extract/reviewer] [2026-10-27] seed: `extra_images` 104 ชิ้นอยู่ใน node ที่ถูกซ่อน (Final มี 1: Pod Duang `964:2119`) · `hero_images` ใน node ซ่อน 10 ชิ้น (Final: 1510 ชั้น 2–6 → §4.8 นับ "6 ภาพ")
- [design-extract/reviewer] [2026-10-27] §5.2–5.6 ไม่มีคอลัมน์ "บังคับ"
