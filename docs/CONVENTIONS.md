# CONVENTIONS — กฎเขียนโค้ด WordPress ของ repo นี้

## โครง
- theme `wp-content/themes/sharedheritage/` (classic PHP) · mu-plugin `wp-content/mu-plugins/sharedheritage-core/` (ข้อมูล: CPT · meta · REST) · **เนื้อหา/โครงข้อมูลอยู่ใน mu-plugin ไม่ใช่ theme**
- ไฟล์กลาง (header · footer · functions.php · tokens · mu-plugin) = งาน `core-*` ถือคนเดียว · หน้าเพิ่มเฉพาะ template + `template-parts/<หน้า>/*` + `assets/{css,js}/<หน้า>.*`
- **enqueue อัตโนมัติตาม slug** (core ทำใน `inc/enqueue.php`): มี `assets/css/<slug>.css` / `assets/js/<slug>.js` ของ template ปัจจุบัน → enqueue เอง (version = filemtime · js ใน footer แบบ defer) · หน้าไม่ต้องแก้ functions.php
  - slug = ชื่อไฟล์ template ที่ WP เลือก (ไม่มี `.php`) ผ่านตาราง `sh_template_slug_map()` (= ตาราง route ใน PLAN): `front-page`→`home` · `page-intro`→`intro` · `taxonomy-zone`→`zone` · `taxonomy-trail`→`trail` · `page-timeline`→`timeline` · `single-object`→`detail` · template อื่นใช้ชื่อไฟล์ตรงๆ (เช่น `index`→`index.css`)
  - ทุกหน้าได้ `tokens.css` → `base.css` (+ `assets/js/base.js` ถ้ามี) ก่อนเสมอ · css หน้า depend `sh-base` · handle = `sh-page-<slug>`
- `header.php` เปิด `<main id="main" class="sh-main">` · `footer.php` ปิด — template หน้าเรียก `get_header()` / `get_footer()` แล้ววางเนื้อหาข้างในเลย · ใช้ได้เฉพาะตัวแปร `--sh-*` จาก `tokens.css`

## PHP / WP
- ข้อมูล: อ่านตาม `docs/CONTENT-MODEL.md` ผ่าน API ของ mu-plugin (`sh_get()` `sh_trail_objects()` …) หรือ `get_post_meta`/`get_term_meta` — ห้ามใช้ `get_field()` ของ ACF ใน template (ACF อาจไม่ active) · เพิ่ม/แก้ฟิลด์ = `inc/fields.php` + CONTENT-MODEL พร้อมกัน แล้วรัน `python3 scripts/import/check-contract.py`
- prefix `sh_` (ฟังก์ชัน · meta · option) · textdomain `sharedheritage` · PHP 8.3 ที่เครื่อง / 8.5 บน server → เขียนให้ไม่มี deprecated
- **escape ทุก output** (`esc_html` `esc_attr` `esc_url` `wp_kses_post`) · sanitize ทุก input · nonce + `current_user_can` ทุก action ที่เขียน
- ไม่ hardcode เนื้อหาในเทมเพลต (label UI ได้) · ไม่มี CDN lib โดยไม่มี D · ไม่ฝัง secret
- ต้องรัน `WP_DEBUG` ได้โดยไม่มี warning/notice

## วิดีโอ
`<video muted playsinline loop preload="metadata" poster="…">` · เล่นเมื่อเข้าจอ (IntersectionObserver) · ไม่โหลดไฟล์ใหญ่ตอน first paint · alpha → `<source>` HEVC-alpha ก่อน WebM สำหรับ Safari หรือ poster

## gate (`bash .claude/hooks/run-gate.sh [worktree]`)
`php -l` ทุก .php ใต้ wp-content → `npm run build` ถ้ามี → `scripts/smoke.sh` ถ้ามี
- **smoke.sh สัญญา:** cwd = root ของ repo/worktree · อ่านรายการหน้าจาก `docs/smoke-urls` (บรรทัดละ path · `#`/ว่าง ข้าม) · ต้องมี server (`scripts/local.sh`) · curl แต่ละหน้า → 200 (ไม่ตาม redirect) และไม่มี output error ของ PHP `Fatal error|Parse error|Warning|Notice|Deprecated` (รูป `<b>Warning</b>: … on line` / `Warning: … on line`) หรือหน้า "critical error" ของ WP · **บรรทัดสุดท้ายของ stdout = `PASS` | `FAIL(…)` | `SKIPPED(เหตุ เช่น ไม่มี server)`**
  - base URL: env `SMOKE_BASE` → ไม่มี = `.playground/port` ของ repo/worktree นี้ (local.sh เขียน) → ไม่มี = `http://127.0.0.1:9400` · probe `/` ต่อไม่ได้ = SKIPPED
  - gate ต้องการ `PASS` เต็ม → เปิด server ของ worktree ตัวเองก่อน (`scripts/local.sh --bg`) แล้วค่อยรัน gate

## local
`scripts/local.sh` → `npx @wp-playground/cli@3.1.60` (SQLite · ไม่ต้องมี MySQL/Docker/wp-cli · 3.1.61 ต้อง node ≥ 24.18) · mount theme + แต่ละรายการใน `wp-content/mu-plugins/` (ถ้ามี) · activate theme · ติดตั้ง+activate ACF ฟรี (wordpress.org · ต้องมีเน็ต) · permalink `/%postname%/` · `WP_DEBUG` + `WP_DEBUG_DISPLAY` · **import seed อัตโนมัติ** (`scripts/import/import.php` · รายงาน `.playground/import-report.json` · uploads ที่ `.playground/uploads/`) · ใช้เวลาบูต ~1–3 นาที (`--bg` รอจน Playground ขึ้น "Ready!")
- env: `SH_ACF=0` ไม่ติดตั้ง ACF · `SH_IMPORT=0` ไม่ import · `SH_IMPORT_RUNS=2` import ซ้ำ 2 รอบ (ตรวจ idempotent) · `SH_DESIGN_ASSETS` / `SH_MEDIA_OUT` path ภาพ/วิดีโอนอก repo (default `Unesco/design/assets` · `Unesco/media/out`)
- `scripts/local.sh [port]` foreground · `--bg [port]` background (รอจนตอบ 200) · `--stop` · `--status`
- port: arg → `SH_PORT` → `PORT` → port ว่างแรกใน 9400–9499 (หลาย worktree รันพร้อมกันได้) · `SH_PHP` (default 8.5 = production) · `SH_WP` (default latest)
- site ใหม่ทุกครั้งที่เริ่ม (ไม่เก็บข้อมูล) · admin `/wp-admin` user `admin` / `password` (local เท่านั้น) · state ใน `.playground/` (gitignored)

## git / task
- commit: `<type>(<scope>): <สั้น>` · 1 task-id = 1+ commit ใน worktree ของ task · add ทีละไฟล์
- task-id = `<พื้นที่>-<ขั้น>` เช่น `core-contract` `home-page` `media-encode`
