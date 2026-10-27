# PENDING-DECISIONS — เรื่องที่ค้าง

dev เขียน P-xxx ที่ §DEV เมื่อ "ทำต่อไม่ได้" · orchestrator grep DECISION-LOG ก่อน — มีคำตอบ → ชี้ให้ dev · ไม่มี → ยกเป็น O-xx ที่ §OWNER (ไม่มี architect — เจ้าของตัดสินตรง)
🔴 = บล็อกงาน · ⛔ ห้าม dev "ทำไปก่อน" ในเรื่องโครงข้อมูล/สิทธิ์/deploy

## §OWNER — รอเจ้าของ
| O | เรื่อง | บล็อก | สถานะ |
|---|---|---|---|
| O-1 | `FIGMA_TOKEN` (scope `file_content:read`) ใส่ที่ `Unesco/.env` เอง (copy จาก `.env.example`) | 🔴 `design-extract` | รอ |
| O-2 | scope คืนนี้: หน้าไหนบ้าง · page "Page 2 Version #Final 01092026" คือ final จริงไหม · ~~ต้องมี mobile ไหม~~ | 🔴 ขอบเขตทุกหน้า | ✅ บางส่วน 2026-10-27: mobile/tablet = responsive ตามเหมาะสม · desktop ตรง 100% (D5) · ยังรอ: หน้าไหนบ้าง · page final |
| O-3 | ภาษา: อังกฤษอย่างเดียว / มีไทยด้วย | — | รอ |
| O-4 | ทีม UNESCO แก้อะไรผ่าน admin: วัตถุอย่างเดียว / ข้อความหน้าแรก + บทความด้วย | — (กระทบ CONTENT-MODEL) | รอ |
| O-5 | media: อนุญาตให้ดาวน์โหลดโฟลเดอร์ Drive (~68MB) หรือเจ้าของวางที่ `Unesco/media/src/` · วัตถุที่ไม่มี WebM ใช้ภาพนิ่งไหม · บีบ Ganesha 50MB ได้ไหม | 🔴 `media-encode` | รอ |
| O-6 | deploy: สร้าง staging ใน hPanel + เปิด SSH/เพิ่ม key (เจ้าของทำ/อนุญาต) · เปลี่ยน theme จาก hostinger-ai-theme บน prod | ขั้น 2 | รอ |
| O-7 | ฟิลด์: ACF ฟรี vs `register_post_meta` + meta box เอง — **แนะนำ ACF ฟรี** (เร็ว · admin UX ดีกว่า · มี dependency 1 ตัว) | `core-contract` | ✅ ตอบแล้ว 2026-10-27: **ACF ฟรี** (เจ้าของเลือกตามคำแนะนำ) |

## §DEV — dev เปิด (P-xxx)
ช่วงเลข: wp-dev P-100–199 · design-dev P-200–299 · media-dev P-300–399 · reviewer ไม่เปิด (ใช้ DEFERRED)
(ยังไม่มี)
