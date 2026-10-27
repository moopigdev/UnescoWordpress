# DEPLOY — Hostinger `sharedheritage.net`

## ข้อเท็จจริง (ตรวจ 2026-10-27)
Business plan · **live สาธารณะ** · WP เปล่า (0 page · theme `hostinger-ai-theme`) · Wordfence · LiteSpeed · PHP 8.5 บน server · DNS Squarespace → 156.67.222.233 · owner it.bgk@unesco.org แชร์สิทธิ์มา

## กติกา (hook บังคับ)
1. **staging ก่อนเสมอ** (`scripts/deploy.sh staging`) — ต้องมี staging ใน hPanel + SSH/key ก่อน (O-6, เจ้าของทำ/อนุญาต)
2. **production ต้องมี `orchestrator/deploy-approved`** — orchestrator เขียนหลังเจ้าของตอบ "ใช่" ในแชทเท่านั้น (ไม่มีไฟล์ = guard-bash บล็อก `deploy.sh` ที่ไม่ใช่ staging และ rsync/scp/sftp ไปโฮสต์ปลายทาง) · ลบไฟล์ทันทีหลัง deploy เสร็จ — การอนุมัติใช้ครั้งเดียว
3. dev ⛔ ไม่ deploy เอง · `deploy.sh` เขียนโดย wp-dev แต่รันโดย orchestrator

## ก่อนขึ้น production (checklist ให้ orchestrator)
- [ ] เจ้าของดู staging แล้วตอบ "ใช่" (ชื่อสิ่งที่อนุมัติ + เวลา บันทึกใน LOG)
- [ ] สำรอง `wp-content` + export DB ของ prod ก่อน (เก็บนอก repo) — รู้วิธี rollback
- [ ] เปลี่ยน theme จาก `hostinger-ai-theme` เป็น `sharedheritage` (O-6) และตรวจ Wordfence / LiteSpeed cache ไม่ทับ (purge หลัง deploy)
- [ ] seed import ทำบน staging ก่อน — ห้าม import ทับเนื้อหาจริงบน prod โดยไม่ถามเจ้าของ
- [ ] ชั้นพิเศษใน DOD (deploy · role · upload · ข้อมูลส่วนบุคคล) ผ่าน review เต็ม

## Rollback
ย้อน theme เป็นตัวเดิม + คืน `wp-content`/DB จากสำรอง · รายละเอียดคำสั่งเติมเมื่อรู้วิธีเข้าถึง (SSH หรือ hPanel file manager)
