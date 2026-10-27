# Definition of Done — ชุดเดียว ⛔ ไม่โต

reviewer ตรวจตามนี้**เท่านั้น** · ข้อที่ไม่อยู่ในนี้ไม่ใช่เหตุส่งกลับ → DEFERRED.md
เจ้าของแก้ไฟล์นี้ได้คนเดียว · แก้แล้วบันทึกเหตุผลใน DECISION-LOG.md

ขั้นของงาน: **A contract** (core) → **B page** (หน้า) · **C media** (ขนาน) · orchestrator ระบุขั้นตอนส่ง review · ⛔ ห้ามใช้ DOD ขั้นหนึ่งตรวจงานอีกขั้น

## A · contract (core — `core-contract`)
1. ทุกฟิลด์ที่หน้าใช้อยู่ใน `docs/CONTENT-MODEL.md` (ชนิด · ที่มาใน Figma · ตัวอย่าง)
2. CPT/taxonomy/meta ที่ register ตรงเอกสาร 1:1 · `show_in_rest` ตามที่เอกสารระบุ
3. import วัตถุ trail ช้างครบ **15 ชิ้น** ตาม page Final (D7) — นับเทียบ `content/seed/objects.json` (trail ช้าง) กับ Figma · ไม่ใช่ 3
4. ทุกฟิลด์แก้ได้จากหน้า edit มาตรฐานของ WP admin
5. gate ผ่าน

## B · page (home · grid · timeline · detail)
1. **desktop 1440 ตรง Figma 100%** (D5) — screenshot หน้าจริงที่ viewport 1440 เทียบ `design/frames/*.png` ทีละ section · ตำแหน่ง/ระยะ/ขนาด/สี/ฟอนต์ (family·size·weight·line-height·letter-spacing)/ข้อความ/ภาพ ต่างที่มองเห็นได้ = F · ค่าต้องมาจาก DESIGN-SPEC (ไม่ประมาณ) · ภาพเทียบตัดขอบเหลือ 1440 (D9) · glyph ต่างเพราะยังไม่มีไฟล์ Komet ไม่ใช่ F (D6)
1b. **tablet (768–1024) · mobile (375–430) responsive ตามเหมาะสม** (D5 · ไม่มี frame) — F เฉพาะเมื่อ: เลื่อนแนวนอนได้ · เนื้อหาซ้อน/ล้น/ถูกตัด · ตัวอักษร < 14px หรืออ่านไม่ได้ · nav ใช้ไม่ได้ · ภาพ/วิดีโอบิดสัดส่วน · การเลือกจัดวางที่สมเหตุสมผลไม่ใช่ F
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
