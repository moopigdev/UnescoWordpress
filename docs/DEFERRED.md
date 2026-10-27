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
