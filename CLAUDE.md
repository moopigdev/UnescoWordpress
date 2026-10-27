# CLAUDE.md — UnescoWordpress (กฎเขียนโค้ดของ repo)

เว็บ "Shared Heritage" ของ UNESCO บน WordPress (classic PHP theme + mu-plugin) · host Hostinger `sharedheritage.net` (**live สาธารณะ**) · repo นี้ใช้เดี่ยวได้ · workflow หลายคนอยู่ที่ `../CLAUDE.md` (orchestrator)

## เริ่มจากตรงไหน
1. `docs/README.md` (แผนที่เอกสาร) → `docs/CONVENTIONS.md` (กฎ + สัญญา gate) → `docs/DOD.md`
2. ข้อมูล: `docs/CONTENT-MODEL.md` · ดีไซน์: `docs/DESIGN-SPEC.md` + `../design/frames/*.png`
3. รัน local: `scripts/local.sh` (playground SQLite — เครื่องไม่มี MySQL/Docker/wp-cli) · gate: `bash ../.claude/hooks/run-gate.sh`

## ไม่ต่อรอง
- โครงข้อมูล (CPT/ฟิลด์) อยู่ใน mu-plugin ตาม CONTENT-MODEL — หน้าอ่านเท่านั้น ไม่ตั้งฟิลด์เอง
- escape ทุก output · sanitize ทุก input · ไม่ hardcode เนื้อหา · ไม่ฝัง secret · ไม่เพิ่ม CDN lib โดยไม่มี D
- ห้ามเดา — ไม่มีคำตอบใน docs → `docs/PENDING-DECISIONS.md`
- ห้าม deploy production ด้วยตัวเอง (ดู `docs/DEPLOY.md`)
- git: add ทีละไฟล์ (ไม่ `-A`/`.`/โฟลเดอร์) · ไม่ push จนเจ้าของสั่ง

## ใครแก้อะไรได้ (เมื่อทำงานเป็นทีม — hook บังคับ)
`docs/DOD.md` `DECISION-LOG.md` = เจ้าของ/orchestrator · `DESIGN-SPEC.md` `content/seed/**` = design-dev · `content/media-manifest.json` `scripts/media/**` `docs/MEDIA.md` = media-dev · ที่เหลือ = wp-dev
