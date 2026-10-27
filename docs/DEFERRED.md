# DEFERRED — ของใหม่ที่เจอระหว่างทำ ยังไม่ทำ

dev/reviewer จดที่นี่แทนการทำเพิ่มหรือส่งกลับ · orchestrator คัดเข้า PLAN ขั้น 3 หรือทิ้ง
รูปแบบ: `- [task-id] [วันที่] <สิ่งที่เจอ> — <ทำไมไม่ทำตอนนี้>`

- [core-env] [2026-10-27] `scripts/local.sh` ยังไม่ import seed (`content/seed/*.json`) — เป็นงาน `core-contract` (เพิ่ม step ใน `.playground/blueprint.json` ที่ local.sh สร้าง หรือเรียก `scripts/import`)
- [core-env] [2026-10-27] `assets/css/tokens.css` เป็น placeholder ทั้งไฟล์ (ป้าย TODO(design-extract)) — เติมค่าจริงเมื่อ DESIGN-SPEC มี Tokens · ชื่อตัวแปรอาจเปลี่ยน
- [core-env] [2026-10-27] WP version บน production ไม่รู้ — local ใช้ `latest` (ตอนนี้ 7.1.3) · ถ้ารู้แล้วตั้ง `SH_WP=<ver>` ให้ตรง
- [core-env] [2026-10-27] smoke ทดสอบเฉพาะ `/` — งานหน้าเพิ่ม path ของตัวเองใน `docs/smoke-urls` (core ถือไฟล์ → แจ้ง core/orchestrator)
- [core-env] [2026-10-27] scripts/local.sh:111-113 `--stop` kill ทุก process ที่ listen บน port ใน .playground/port — ถ้า server ของ worktree นี้ตายเองแล้ว worktree อื่นได้ port เดิมไป `--stop` จะปิดของอีกตัว · และ auto-port (local.sh:135) ไม่กันกรณีสอง worktree เริ่มพร้อมกัน — edge case นอก DOD
