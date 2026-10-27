# CONTENT-MODEL — สัญญาข้อมูล (CPT · taxonomy · meta)

**เจ้าของไฟล์:** wp-dev (งาน `core-contract`) · หน้าทุกหน้าอ่านจากที่นี่ ⛔ ห้ามหน้าใดตั้งฟิลด์เอง · mu-plugin `sharedheritage-core` register ตรงเอกสารนี้ 1:1 (DOD A)
**สถานะ:** โครง — `core-contract` เติมหลัง `design-extract` ส่ง `DESIGN-SPEC.md` ("ฟิลด์ที่หน้าใช้") · ยังไม่ผ่าน O-7 (ACF vs meta เอง) จึงยังไม่ระบุวิธี register

## หลักการ (D1)
- ข้อมูลอยู่ใน **mu-plugin** ไม่ใช่ theme → เปลี่ยน theme ไม่ทำข้อมูลหาย
- ทุกฟิลด์แก้ได้จากหน้า edit มาตรฐาน · ชื่อ meta ขึ้นต้น `sh_` · ข้อความ UI (label) ไม่อยู่ที่นี่

## รูปแบบรายการ (เติมต่อทีละ entity)
| ฟิลด์ (meta key) | ชนิด | บังคับ | ที่มาใน Figma (frame/node-id) | ตัวอย่าง | ใช้ในหน้า | REST |
|---|---|---|---|---|---|---|

## Entity ที่คาดว่าต้องมี (ยืนยันตาม Figma หลัง design-extract)
| entity | ชนิด WP | มาจาก Figma | หมายเหตุ |
|---|---|---|---|
| `object` | CPT | grid วัตถุ 3 ธีมสี · detail ~19 วัตถุ | ธีมสี → taxonomy `theme` (ยืนยัน) · วิดีโอ WebM + poster + fallback ต่อวัตถุ |
| timeline | ฟิลด์ใน `object` (ปี/ลำดับ) หรือ entity แยก | timeline แนวนอน + ช้างเดิน | ลำดับต้องแก้ได้ใน admin |
| zone | CPT/ตัวเลือก (ยืนยัน) | zone card เช่น "Zone 4" 560×360 `670:9534` | |
| article | post มาตรฐานหรือ CPT (ยืนยัน) | article | O-4 กำหนดว่าทีมแก้ได้ไหม |
| ข้อความหน้าแรก/hero | options หรือ page meta (ยืนยัน) | home/hero | O-4 |

## Seed
`content/seed/*.json` (design-dev) → `scripts/import` (core) · ต้องครบ ~19 วัตถุ
