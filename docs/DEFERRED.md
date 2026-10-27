# DEFERRED — ของใหม่ที่เจอระหว่างทำ ยังไม่ทำ

dev/reviewer จดที่นี่แทนการทำเพิ่มหรือส่งกลับ · orchestrator คัดเข้า PLAN ขั้น 3 หรือทิ้ง
รูปแบบ: `- [task-id] [วันที่] <สิ่งที่เจอ> — <ทำไมไม่ทำตอนนี้>`

- [design-extract] [2026-10-27] **#1 re-export frame PNG ด้วย `use_absolute_bounds=true` (ให้กว้าง 1440 พอดี) + export detail frame ที่เหลือ (Page 2.1 ~138 ใบ ตาม `design/detail-frames.list`) + frame ใหม่ที่ต้องใช้ screenshot diff** — ติด Figma render rate limit (429 Retry-After 398,727 s ≈ 2026-11-01) · รัน `bash design/export-detail-frames.sh` และ `bash design/figma-fetch.sh images 1 <name>=<id>…` หลังพ้นเวลา
- [design-extract] [2026-10-27] ไอคอน/ภาพ vector ที่ export 2x: ทำเป็น SVG แล้ว 4 ชิ้น (`design/assets/svg/`: unesco-logo, back-arrow, map-arrow-right, close-cross) · ภาพ raster ใช้ต้นฉบับจาก Figma (`design/assets/<imageRef>.png|jpg` 307 ไฟล์ ≥ 2x ส่วนใหญ่) ไม่ render ซ้ำ
- [design-extract] [2026-10-27] ภาพเฟรม GIF ของ hero วัตถุ (427 ref) ยังไม่ดาวน์โหลด (ขนาดใหญ่) — media-dev (O-5) ดึงด้วย `figma-fetch.sh dl <ref>` หรือใช้ไฟล์ Drive
- [design-extract] [2026-10-27] เมนูโลโก้ TEMASEK/SHARED HERITAGE ฝังในภาพพื้น Header #1 (`709572bf`) — ถ้าต้องแยกโลโก้ ต้องขอไฟล์ต้นฉบับ
- [design-extract] [2026-10-27] hover ของ "Read more >>" แบบ Barlow Condensed (intro/trail/timeline) ไม่มี variant ใน Figma — ใช้พฤติกรรมเดียวกับ Home (Bold + underline `#D65F47`)
