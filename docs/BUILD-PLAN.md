# BUILD-PLAN — สถานะงานของ repo (ฉบับย่อ · แผนเต็มที่ `Unesco/orchestrator/PLAN.md`)

| task-id | ขั้น | ผู้ทำ | สถานะ | บล็อกด้วย |
|---|---|---|---|---|
| core-env | 0 | wp-dev (core) | ยังไม่เริ่ม | — |
| design-extract | 0 | design-dev | ยังไม่เริ่ม | O-1 🔴 |
| media-encode | 0 | media-dev | ยังไม่เริ่ม | O-5 🔴 |
| core-contract | 1 | wp-dev (core) | ยังไม่เริ่ม | design-extract · O-7 |
| home-page · grid-page · timeline-page · detail-page | 1 | wp-dev ×4 | ยังไม่เริ่ม | core-contract |
| integrate → staging → production | 2 | orchestrator | — | O-6 · deploy-approved |
| admin UX | 3 | wp-dev | — | ถ้ามีเวลา |

orchestrator อัปเดตตารางนี้หลัง task ผ่าน review และ merge
