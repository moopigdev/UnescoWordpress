# Definition of Done — ชุดเดียว ⛔ ไม่โต

reviewer ตรวจตามนี้**เท่านั้น** · ข้อที่ไม่อยู่ในนี้ไม่ใช่เหตุส่งกลับ → DEFERRED.md
เจ้าของแก้ไฟล์นี้ได้คนเดียว · แก้แล้วบันทึกเหตุผลใน DECISION-LOG.md

ขั้นของงาน: **A contract** (core) → **B page** (หน้า) · **C media** (ขนาน) · orchestrator ระบุขั้นตอนส่ง review · ⛔ ห้ามใช้ DOD ขั้นหนึ่งตรวจงานอีกขั้น

## A · contract (core — `core-contract`)
1. ทุกฟิลด์ที่หน้าใช้อยู่ใน `docs/CONTENT-MODEL.md` (ชนิด · ที่มาใน Figma · ตัวอย่าง)
2. CPT/taxonomy/meta ที่ register ตรงเอกสาร 1:1 · `show_in_rest` ตามที่เอกสารระบุ
3. seed ครบ ~19 วัตถุ ไม่ใช่ 3 (นับเทียบ `content/seed` กับ Figma)
4. ทุกฟิลด์แก้ได้จากหน้า edit มาตรฐานของ WP admin
5. gate ผ่าน

## B · page (home · grid · timeline · detail)
1. ตรง Figma frame — เทียบ `design/frames/*.png` ที่ 1440 (และ mobile ถ้ามี design) · layout/สี/ฟอนต์/ข้อความผิดชัด = F
2. ข้อมูลมาจาก CPT/ฟิลด์ ไม่ hardcode เนื้อหา (ยกเว้น label UI)
3. escape ทุก output · ไม่มี PHP warning/notice (WP_DEBUG)
4. วิดีโอ `muted playsinline loop preload="metadata" poster` · เล่นเมื่อเข้าจอ · ไม่โหลดวิดีโอใหญ่ตอน first paint
5. enqueue เฉพาะหน้าที่ใช้ · ไม่มี CDN lib โดยไม่มี D ใน DECISION-LOG
6. ไม่มีวิดีโอ/ภาพ → layout ไม่พัง
7. แตะเฉพาะไฟล์ที่ถือตาม PLAN (ownership map)
8. gate ผ่าน

## C · media (media-dev — `media-encode`)
1. ขนาดต่อไฟล์ ≤ เป้า (เสนอ ≤ 3MB · เกินต้องมี D)
2. poster ทุกไฟล์
3. manifest: src → out · ขนาด · ความยาว · has_alpha
4. alpha → มี fallback Safari/iOS (VP9 alpha ไม่รองรับ)

## ชั้นพิเศษ — ตรวจเต็มทุกครั้ง ไม่ว่าขั้นไหน
diff ที่แตะ **deploy/production · role/capability · upload/รับไฟล์ · ข้อมูลส่วนบุคคล** = reviewer เปิดทุกไฟล์ที่ diff แตะ · เจอรั่ว = **ESCALATE** หยุดงาน ส่ง orchestrator → เจ้าของ (ไม่มีทางแก้เองรอบถัดไป)
