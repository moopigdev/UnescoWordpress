# DEFERRED — ของใหม่ที่เจอระหว่างทำ ยังไม่ทำ

dev/reviewer จดที่นี่แทนการทำเพิ่มหรือส่งกลับ · orchestrator คัดเข้า PLAN ขั้น 3 หรือทิ้ง
รูปแบบ: `- [task-id] [วันที่] <สิ่งที่เจอ> — <ทำไมไม่ทำตอนนี้>`

- [media-encode] [2026-10-27] poster เลือกเฟรมอัตโนมัติ 35% — ganesha/singhasari เห็นตัวซ้อนกลาง transition; เลือกเฟรมต่อไฟล์ (map ใน encode.sh) — ยังไม่มีผู้ใช้ poster ให้ตัดสิน
- [media-encode] [2026-10-27] ทดสอบ HEVC-alpha บน Safari/iOS จริง — ไม่มีอุปกรณ์ในเครื่องนี้ (ทดสอบได้แค่ ffprobe/ffmpeg)
- [media-encode] [2026-10-27] ไฟล์ทึบ 4 ตัวไม่มี fallback วิดีโอ (iOS<17.4 เห็น poster) — ถ้าต้องการ ทำ H.264 mp4 เพิ่มได้ ไม่อยู่ใน DOD C4
