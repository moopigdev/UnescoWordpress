# CONVENTIONS — กฎเขียนโค้ด WordPress ของ repo นี้

## โครง
- theme `wp-content/themes/sharedheritage/` (classic PHP) · mu-plugin `wp-content/mu-plugins/sharedheritage-core/` (ข้อมูล: CPT · meta · REST) · **เนื้อหา/โครงข้อมูลอยู่ใน mu-plugin ไม่ใช่ theme**
- ไฟล์กลาง (header · footer · functions.php · tokens · mu-plugin) = งาน `core-*` ถือคนเดียว · หน้าเพิ่มเฉพาะ template + `template-parts/<หน้า>/*` + `assets/{css,js}/<หน้า>.*`
- **enqueue อัตโนมัติตาม slug** (core ทำใน functions.php): มี `assets/css/<slug>.css` / `assets/js/<slug>.js` ของ template ปัจจุบัน → enqueue เอง · หน้าไม่ต้องแก้ functions.php

## PHP / WP
- prefix `sh_` (ฟังก์ชัน · meta · option) · textdomain `sharedheritage` · PHP 8.3 ที่เครื่อง / 8.5 บน server → เขียนให้ไม่มี deprecated
- **escape ทุก output** (`esc_html` `esc_attr` `esc_url` `wp_kses_post`) · sanitize ทุก input · nonce + `current_user_can` ทุก action ที่เขียน
- ไม่ hardcode เนื้อหาในเทมเพลต (label UI ได้) · ไม่มี CDN lib โดยไม่มี D · ไม่ฝัง secret
- ต้องรัน `WP_DEBUG` ได้โดยไม่มี warning/notice

## วิดีโอ
`<video muted playsinline loop preload="metadata" poster="…">` · เล่นเมื่อเข้าจอ (IntersectionObserver) · ไม่โหลดไฟล์ใหญ่ตอน first paint · alpha → `<source>` HEVC-alpha ก่อน WebM สำหรับ Safari หรือ poster

## gate (`bash .claude/hooks/run-gate.sh [worktree]`)
`php -l` ทุก .php ใต้ wp-content → `npm run build` ถ้ามี → `scripts/smoke.sh` ถ้ามี
- **smoke.sh สัญญา:** cwd = root ของ repo/worktree · อ่านรายการหน้าจาก `docs/smoke-urls` (บรรทัดละ path) · ต้องมี server (`scripts/local.sh`) · curl แต่ละหน้า → 200 และไม่มี `Fatal|Warning|Notice|Deprecated` · **บรรทัดสุดท้ายของ stdout = `PASS` | `FAIL(…)` | `SKIPPED(เหตุ เช่น ไม่มี server)`** · base URL จาก env `SMOKE_BASE`

## local
`scripts/local.sh` → `npx @wp-playground/cli` (SQLite · ไม่ต้องมี MySQL/Docker/wp-cli) · mount theme + mu-plugin + seed import

## git / task
- commit: `<type>(<scope>): <สั้น>` · 1 task-id = 1+ commit ใน worktree ของ task · add ทีละไฟล์
- task-id = `<พื้นที่>-<ขั้น>` เช่น `core-contract` `home-page` `media-encode`
