# DEPLOY — Hostinger `sharedheritage.net`

## ข้อเท็จจริง (ตรวจ 2026-10-27)
Business plan · **live สาธารณะ** · WP เปล่า (0 page · theme `hostinger-ai-theme`) · Wordfence · LiteSpeed · PHP 8.5 บน server · DNS Squarespace → 156.67.222.233 · owner it.bgk@unesco.org แชร์สิทธิ์มา
- **PHP CLI `disable_functions`** (orchestrator อ่านผ่าน SSH 2026-10-27): `system, exec, shell_exec, passthru, mysql_list_dbs, ini_alter, dl, symlink, link, chgrp, leak, popen, apache_child_terminate, virtual, mb_send_mail, proc_open` → **wp-cli ที่ spawn process พังทุกคำสั่ง**: `wp db export|import|query|cli` (เรียก mysqldump/mysql) · `wp rewrite structure` (เรียก `wp rewrite flush` ผ่าน launch) · `wp rewrite flush --hard` · `--launch` (deploy staging ครั้งแรกหยุดที่ตรงนี้: `Cannot do 'launch option': … proc_open() … disabled`) · ใช้ได้: option / plugin / theme / post list / config get / eval / eval-file / cache / litespeed-purge
- `/usr/bin/mysqldump` และ `/usr/bin/mysql` มีบน server (ตรวจแล้ว) — script เรียกตรงจาก shell แทน `wp db …`

## การเข้าถึง (2026-10-27)
- hPanel: บัญชี it.bgk@unesco.org (เราเป็นผู้ร่วมงาน) · staging = `staging.sharedheritage.net` (สร้าง 2026-10-27 · คัดลอกจาก prod · WP + DB แยก)
- SSH: port 65002 · user u972755030 · host 156.67.222.233 · คีย์ `~/.ssh/sharedheritage_hostinger` (ed25519 เฉพาะงานนี้ · ไม่มี passphrase · อยู่นอก repo) · เจ้าของต้องเปิด SSH + เพิ่ม public key ใน hPanel ก่อน
- ครั้งแรกที่ต่อ SSH: script ใช้ `StrictHostKeyChecking=accept-new` (จำ host key ครั้งแรก) → เทียบ fingerprint ก่อนรันจริง: `ssh-keygen -l -F '[156.67.222.233]:65002'` ต้องได้ `ED25519 SHA256:NOwtpIZ2NzL3jgjGNSu9vzFc/RXp1gbQycagapZHaEs` (ไม่ตรง = หยุด · ลบด้วย `ssh-keygen -R '[156.67.222.233]:65002'` แล้วถามเจ้าของ)

## กติกา (hook บังคับ)
1. **staging ก่อนเสมอ** (`scripts/deploy.sh staging`) — ต้องมี staging ใน hPanel + SSH/key ก่อน (O-6, เจ้าของทำ/อนุญาต)
2. **production ต้องมี `orchestrator/deploy-approved`** — orchestrator เขียนหลังเจ้าของตอบ "ใช่" ในแชทเท่านั้น (ไม่มีไฟล์ = guard-bash บล็อก `deploy.sh` ที่ไม่ใช่ staging และ rsync/scp/sftp ไปโฮสต์ปลายทาง · `deploy.sh` ตรวจซ้ำเอง: ไม่มีไฟล์/ไฟล์ว่าง = exit 1 แม้ `--dry-run`) · ลบไฟล์ทันทีหลัง deploy เสร็จ — การอนุมัติใช้ครั้งเดียว
3. dev ⛔ ไม่ deploy เอง · `deploy.sh` เขียนโดย wp-dev แต่รันโดย orchestrator

## `scripts/deploy.sh`
```
scripts/deploy.sh [staging|production] [--dry-run] [--discover] [--no-import | --refresh-only]     # ไม่ระบุ = staging
```
| flag | ทำอะไร |
|---|---|
| `--dry-run` | พิมพ์ทุกคำสั่ง (ssh · rsync · wp-cli ฝั่ง server แบบ copy-paste ได้) **ไม่เชื่อมต่อ server** · check ฝั่ง server ถือว่าผล "ยังไม่มี" เพื่อให้เห็นคำสั่งที่จะตามมา |
| `--discover` | อ่านอย่างเดียว: SSH ไปหา `wp-config.php` ใต้ `~/domains` (ลึก 4) → พิมพ์ path · siteurl · home · theme ที่ active ไม่เขียนอะไร |
| `--no-import` | ส่งแค่โค้ด + ACF/theme/permalink/purge · ไม่ import seed (deploy ซ้ำเพื่อแก้โค้ด · หรือ prod ที่มีเนื้อหาแล้ว) |
| `--refresh-only` | site ที่มีเนื้อหาแล้ว (**ใช้กับ production ได้** · preflight ไม่หยุด exit 3): **ไม่ส่งโค้ด** · ไม่แตะ ACF/theme/permalink · ส่ง bundle import แล้วรัน `import.php` ด้วย `SH_IMPORT_ONLY=refresh` = เฉพาะ refresh step ที่ site ยังไม่เคยรันจนเสร็จ (`scripts/import/refresh.php`) · **ไม่สร้าง** post/term/page/option/menu (หาของเดิมด้วย `_sh_import_key`) · แก้เฉพาะค่าที่ยังเป็นของ import (ค่าที่ admin แก้ไม่ถูกทับ) · production ยังสำรองก่อน (ขั้น 2) · ใช้คู่กับ `--no-import` ไม่ได้ |

**config** `scripts/deploy/hosts.env` (คัดลอกจาก `hosts.env.example` · gitignored · ไม่มีความลับ) · ลำดับ: env ของ shell > `hosts.env` > `hosts.env.example` · อ่านแบบ KEY=VALUE (ไม่ source) เฉพาะ key ที่รู้จัก
- `SH_DEPLOY_SSH_{HOST,PORT,USER,KEY}` · `SH_DEPLOY_REMOTE_DIR` (default `sh-deploy` — relative กับ home บน server = นอก webroot: `tmp-*` · `backups/` · `logs/`)
- `SH_STAGING_URL` / `SH_STAGING_WP_PATH` · `SH_PRODUCTION_URL` / `SH_PRODUCTION_WP_PATH` (WP path = absolute · ค่าจริงอยู่ใน example แล้ว · ยืนยันด้วย `--discover`) · **production ต้องตั้งเสมอแม้ deploy staging** — `staging` หยุดก่อนเชื่อมต่อถ้า host ของ staging URL = host production (ไม่สน scheme · `www.` · ตัวพิมพ์ · slash ท้าย) · prod URL/path ว่าง · staging path = prod path หรือครอบ prod path (staging เป็นโฟลเดอร์ลูกของ prod `public_html/staging` = ปกติของ Hostinger) · และ preflight บน server หยุดถ้า siteurl จริงของ staging เป็น host production · `SH_DESIGN_ASSETS` / `SH_MEDIA_OUT` (ว่าง = `Unesco/design/assets` · `Unesco/media/out`)

**ลำดับที่ script ทำ** (ไฟล์ฝั่ง server = `scripts/deploy/remote.sh` ส่งทาง `ssh … bash -s -- <phase>` · ไม่ต้องติดตั้งอะไรบน server นอกจาก wp-cli ของ Hostinger)
0. local: config ครบ · key มีจริง · production: `deploy-approved` + **tree ต้องสะอาด** (wp-content · scripts/import · content) · import: **ทุกไฟล์ที่ import อ่าน** ต้องมีใน design/assets — รายการจาก `php scripts/import/bundle-files.php <assets>` (โค้ดเดียวกับ import: `scripts/import/refs.php`) = image-map "files" + ไฟล์ของ style `*_ref` ทุกตัวของวัตถุที่ import (ภาพครอป `<ref>.crop-<node>.png` ของ design-dev) + `line_art_ref` ของ timeline เป็น `lineart/<file>` · ชื่อไฟล์ต้องอยู่ที่ราก assets ยกเว้น `lineart/<file>` ชั้นเดียว (P-164 · `lineart/x/y` · `lineart/.x` · `lineart/../x` · `<โฟลเดอร์อื่น>/x` = ไม่ปลอดภัย) · ขาด/ชื่อไม่ปลอดภัย = หยุดก่อนเชื่อมต่อ (ต้องมี php ในเครื่อง · Herd ก็ได้) · `--dry-run` พิมพ์จำนวน + ชื่อไฟล์ `*_ref` ทุกตัว
1. **preflight** (อ่านอย่างเดียว): `wp core is-installed` · `siteurl` ของ WP path **ต้องตรง URL ที่คาด** (ไม่ตรง = หยุด · กันส่งผิด site) · พิมพ์ theme + จำนวน object/timeline_item · **production + import + มี object/timeline_item อยู่แล้ว → หยุด exit 3** (ห้าม import ทับ prod · ถามเจ้าของ · ส่งแค่โค้ดใช้ `--no-import`)
2. **production เท่านั้น — สำรองก่อนเขียนอะไรทั้งหมด:** `mysqldump` ตรง (ไม่ใช่ `wp db export`) + `tar wp-content` + theme เดิม → `~/sh-deploy/backups/production-<ts>/` (chmod 700/600) + `ROLLBACK.txt` (พิมพ์ออกจอด้วย)
   - ค่า DB จาก `wp config get DB_NAME|DB_HOST|DB_CHARSET|DB_USER|DB_PASSWORD` (อ่าน wp-config · ไม่ spawn) · `DB_HOST` แยก host/port/socket → `db-conn.cnf` (ไม่มีรหัส) · user/รหัส → option file ชั่วคราว `mktemp ~/sh-deploy/.db-cnf.*` (600 ในโฟลเดอร์ 700 · ค่าไม่ขึ้นจอ/argv/log) → `mysqldump --defaults-extra-file=<ไฟล์> --single-transaction --quick --skip-lock-tables --no-tablespaces --default-character-set=<DB_CHARSET|utf8mb4> --result-file=db.sql <DB_NAME>` → ลบไฟล์ทันที (trap แม้ dump พัง)
   - ไม่มี `mysqldump` · dump ล้ม · บรรทัดสุดท้ายของ `db.sql` ไม่มี `Dump completed` = **หยุดก่อนส่งไฟล์ใดๆ**
3. prepare: `mkdir -p wp-content/mu-plugins` · โฟลเดอร์ tmp import
4. rsync (`-rlptz` · exclude `.git` `.playground` `node_modules` `.DS_Store`): mu-plugin ทีละรายการ (โฟลเดอร์ `sharedheritage-core/` ใช้ `--delete` ภายในโฟลเดอร์ตัวเอง · ไฟล์ loader `sharedheritage-core.php` ไม่มี `--delete`) → theme `sharedheritage/` (`--delete` ภายในโฟลเดอร์ theme) · **ไม่แตะ wp-content อื่น** (mu-plugin ของ Hostinger · theme อื่น · uploads) · import: bundle (`scripts/import/` · `content/` · ภาพ 191 ไฟล์ = image-map 169 + style `*_ref` 22 (นับ 2026-10-28) · `media/out` ยกเว้น `*.html`) → `~/sh-deploy/tmp-<env>-<ts>/` · ⚠ ก่อน 2026-10-28 bundle มีแค่ image-map → ภาพครอป 22 ไฟล์ไม่ถูกส่ง → prod import (2026-10-27 23:40) เก็บชื่อไฟล์เป็นข้อความ → หน้า detail ไม่เข้าโหมด placed (แก้: §แก้ prod หลัง go-live) · `--refresh-only`: ไม่มี rsync โค้ด มีแต่ bundle
5. post: ACF ฟรี (ข้ามถ้ามี ACF Pro active · ติดตั้ง/activate ถ้ายังไม่มี) · `wp theme activate sharedheritage` (ถ้ายังไม่ active) · permalink ด้วย `wp eval` 2 process (ไม่ใช้ `rewrite structure` / `flush --hard` — proc_open): (1) `$wp_rewrite->set_permalink_structure('/%postname%/')` ถ้ายังไม่ใช่ (2) process ใหม่ `flush_rewrite_rules(false)` (rules ว่าง = หยุด) แล้ว `.htaccess`: มี `# BEGIN WordPress` แล้ว = **ไม่แตะ** (staging มีอยู่แล้ว) · ไม่มี = เขียนบล็อกด้วย `save_mod_rewrite_rules()` (บังคับ filter `got_rewrite` = true เพราะ CLI ไม่รู้ว่า server เป็น LiteSpeed) · เขียนไม่ได้ = หยุด · import: `SH_IMPORT_ASSETS_DIR=<tmp>/assets SH_IMPORT_MEDIA_DIR=<tmp>/media SH_IMPORT_SUBSIZES=1 SH_IMPORT_REPORT=~/sh-deploy/logs/import-<env>-<ts>.json wp eval-file <tmp>/scripts/import/import.php` (mode `missing` = ไม่ทับของที่ admin แก้ · `--refresh-only` เพิ่ม `SH_IMPORT_ONLY=refresh` และข้าม ACF/theme/permalink) · **คำเตือน import = `runs[-1].warnings` ของรายงาน** (ไม่ใช่ key ระดับบนสุด) — script อ่านด้วย `wp eval` แล้วพิมพ์ทุกบรรทัด + `SH_IMPORT_WARNINGS=<n>` · **> 0 = `⛔` แล้ว `deploy.sh` exit 4** หลังทุกขั้นเสร็จ (ไฟล์ส่งแล้ว · tmp ลบแล้ว · purge แล้ว — exit 4 = "ห้ามรายงานว่าผ่าน" ไม่ใช่ rollback อัตโนมัติ) · อ่านรายงานไม่ได้ / ไม่มี `runs[-1].warnings` = หยุด (ไม่ถือว่า 0) · **ลบ tmp เสมอ** (trap แม้ import พัง) · **cache (ล้ม = warning ไม่ fatal · deploy ถือว่าสำเร็จ)**: `wp litespeed-purge all` ยิง HTTP ไปที่เว็บ → staging ไม่มี DNS ได้ `Could not notify the web server to purge.` (เจอจริง 2026-10-27 รอบ 2) · ลำดับ: (1) `wp cache flush` (2) `wp litespeed-purge all` ถ้ามีคำสั่ง · ถ้าอะไรล้ม พิมพ์ ⚠ ให้ **purge เองใน wp-admin › LiteSpeed Cache › Toolbox › Purge All หรือ hPanel** · ⛔ script ไม่ลบไฟล์ใต้ `wp-content/litespeed/` เอง (page cache ที่ยังค้างอ้าง CSS/JS ที่ minify ไว้ → 404)
- idempotent: รันซ้ำได้ — rsync ส่งเฉพาะที่ต่าง · ACF/theme ข้ามถ้ามีแล้ว · permalink ข้ามถ้าตั้งแล้ว · import หาเดิมด้วย `_sh_import_key` (รอบ 2 สร้างใหม่ 0)
- **resume หลัง staging ครั้งแรกหยุด (2026-10-27 · หยุดที่ permalink ก่อน import):** รัน `scripts/deploy.sh staging` ซ้ำได้เลย — ACF (ติดตั้ง+active แล้ว) / theme (active แล้ว) / permalink (option ถูกตั้งก่อน wp-cli พัง) ถูกข้าม → flush → import (bundle ส่งใหม่ไป tmp ใหม่ · tmp เดิมถูกลบแล้ว) · จำลอง offline 2 รอบติดกันถึง import ทั้งคู่

## แก้ prod หลัง go-live: ภาพครอปหน้า detail (deploy-crops · 2026-10-28)
อาการ: หน้า detail ที่มีภาพครอปไม่เข้าโหมด placed (`sh-detail__media--placed`) · รายงาน `~/sh-deploy/logs/import-production-20261027-234048.json` → `runs[-1].warnings` = 22 บรรทัด `style *_ref without file (kept as text): <sha>.crop-<x>-<y>.png (…)`
แก้ = refresh step `2026-10-28-layout-refs` (`scripts/import/refresh.php`): ทุกวัตถุที่ import · ทุก `*_ref` ใน `sh_layout` ที่ **ยังเป็นข้อความเท่ากับค่า seed ที่ตำแหน่ง (key path) เดียวกันเป๊ะ** → attachment id (คัดลอกไฟล์ครอปเข้า media library ครั้งเดียวต่อไฟล์) · ค่าอื่น (admin พิมพ์ชื่ออื่น · เลือก attachment เอง · ลบ/ย้าย key · JSON เสีย) **ไม่แตะ** · key อื่นใน `sh_layout` คงตามที่เก็บ · บันทึกว่าเสร็จ (`sh_import_refresh_done`) เมื่อไม่เหลือค่าที่หาไฟล์ไม่เจอ (ขาดไฟล์ = ลองใหม่รอบหน้า) · รันซ้ำ = แก้ 0
ลำดับ (orchestrator · หลัง review ผ่าน + merge + เจ้าของตอบ "ใช่" + เขียน `orchestrator/deploy-approved`):
1. `scripts/deploy.sh production --refresh-only --dry-run` — อ่านแผน: bundle 191 ไฟล์ (`*_ref` 22 ชื่อ) · ขั้น 2 backup · ไม่มี rsync โค้ด · ไม่มี ACF/theme/permalink · import มี `SH_IMPORT_ONLY=refresh`
2. `scripts/deploy.sh production --refresh-only` — preflight → backup (จด `ROLLBACK.txt`) → bundle → import refresh-only → อ่านคำเตือน → purge
3. คาด: `SH_IMPORT_WARNINGS=0` · exit 0 · รายงาน `runs[-1]`: `"only":"refresh"` · `"refresh":["2026-10-28-layout-refs"]` · `created.attachment` ≤ 22 · `updated.layout_ref` ≤ 22 (น้อยกว่าเมื่อ admin แก้ค่าไปแล้ว) · `warnings` [] · หน้า detail placed = 12 ชิ้น (เท่า local fresh import) · smoke `SMOKE_BASE=https://sharedheritage.net bash scripts/smoke.sh`
4. ลบ `orchestrator/deploy-approved` · (ถ้ารันซ้ำ: `refresh` [] · updated {} · ไม่มีอะไรเปลี่ยน)
- exit 4 = ยังมีคำเตือน → อ่านรายการที่พิมพ์ · step ยังไม่ถูกบันทึกว่าเสร็จ → แก้ไฟล์แล้วรัน `--refresh-only` ซ้ำได้
- ⛔ ห้ามใช้ import เต็มบน prod (preflight หยุด exit 3 อยู่แล้ว) · ห้าม `SH_IMPORT_MODE=overwrite` บน prod (ทับงานของทีม UNESCO)
- rollback: ทั้งก้อน = `ROLLBACK.txt` ของชุดสำรองขั้น 2 (§Rollback) ⚠ คืน DB = สิ่งที่ทีม UNESCO แก้หลังเวลาสำรองหายด้วย → คืนเฉพาะเมื่อหน้าเว็บพัง · ค่าเดิมของแต่ละ `*_ref` = ชื่อไฟล์ใน seed `content/seed/objects.json` (`figma[Final].style`) ใส่กลับทางกล่อง `sh_layout` ใน wp-admin ได้ · attachment ครอปที่สร้างแล้วไม่กระทบหน้าอื่น

## รอบ Figma 2026-11-11 (core-hzd · D37 D38 D40 · BACKLOG #5 #7 #8 #13 #16 #19) — prod ที่มีเนื้อหาแล้ว
**ไฟล์ใน bundle เพิ่ม:** ภาพจาก seed ใหม่ (`refs.php sh_import_seed_asset_values()`): banner zone 1–3 `zone-banners-2026-11/<file>` · หน้าคั่น Zone 4 `zone4-animals/<file>` (สัตว์ 17 + พื้นเส้นประ + แถบ ZONE) · Zone 5 (`zones.json` zone 5 `page` เมื่อ design-dev ใส่ — P-172) · **sub-folder ที่ยอม = ชั้นเดียว รายชื่อตายตัว** `lineart/` `zone-banners-2026-11/` `zone4-animals/` (deploy.sh `case` + `refs.php sh_import_asset_subdirs()`) · `<sub>/<sub>/x` · `<sub>/.x` · `<sub>/../x` · โฟลเดอร์อื่น = หยุดก่อนเชื่อมต่อ · `--dry-run` พิมพ์ทุกชื่อในบรรทัด `*_ref:`
**refresh step (`scripts/import/refresh-2026-11.php` · รันใน `--refresh-only` · ครั้งเดียวต่อ site):**
- `2026-11-11-zones` ชื่อ zone 1–3 (term name) · `sh_card_title` · `sh_cover_image` (banner ใหม่) → แทน **เฉพาะเมื่อค่าบน site ยังเท่าค่าที่ import รุ่นก่อนเขียน** (`scripts/import/legacy-values.json` = ค่าจากประวัติ git ของ seed · สร้างด้วย `build-legacy-values.py` · ภาพ = attachment ที่ key เป็น cover เดิม) · `sh_intro` `sh_intro_full` เติมเมื่อว่าง
- `2026-11-11-objects` วัตถุ 15: `post_title` `sh_title_raw` `sh_caption` + แถวข้อมูล (`sh_period` … `sh_object_biography`) → แทนเฉพาะค่าที่ยังเท่าค่า import เดิม · **slug ไม่เปลี่ยน** (`/object/<slug>/` เดิมใช้ได้) · Pod Duang `sh_title_raw` ที่ทีมแก้บน prod 2026-10-28 (ขึ้นบรรทัด → ช่องว่าง) นับเป็นค่า import (`EXTRA` ใน build script)
- `2026-11-11-chooser` zone 4: `sh_link_page` = หน้า Timeline (ค่า import เดิม) → ว่าง (การ์ดหน้าแรกไป `/zone/zone-4/`) · `sh_cover_image` `sh_banner_image` + trail `sh_chooser_image` เติมเมื่อว่าง · ⚠ ต้อง deploy theme ที่มีหน้าคั่น (`taxonomy-zone-zone-4.php` หรือเทียบเท่า) **พร้อมกัน** — ไม่งั้น `/zone/zone-4/` แสดงหน้า zone แบบกริด
- `2026-11-11-zone5` zone 5: `sh_has_page` → true + ภาพ/ข้อความ/ป้าย "Coming soon" ของหน้าเติมเมื่อว่าง · **รอ seed** (zone 5 `has_page:true` + `page` — P-172): ยังไม่มี = step ไม่ถูกบันทึกว่าเสร็จ (รันอีกครั้งเมื่อ seed มา · ไม่มีคำเตือน)
- `2026-11-11-media-replace` ไฟล์ใน `media/out` ที่เปลี่ยนแต่ชื่อเดิม (`walking.webm` v2 · BACKLOG #5): attachment ที่ import สร้าง (key `media:walking.webm`) ถ้า sha256 ของไฟล์บน site = ไฟล์รุ่นเก่า (`legacy-values.json` `media`) → **เขียนทับไฟล์เดิม** (id/URL เดิม) + อัปเดต `_wp_attachment_metadata` (filesize · length · width/height) · admin เปลี่ยนไฟล์แล้ว = ไม่ทับ · poster ไม่เปลี่ยน · ⚠ URL เดิม → หลัง deploy ต้อง purge LiteSpeed (ขั้น 5 ทำอยู่แล้ว) · browser ที่ cache ไฟล์เก่าอาจเห็นของเก่าจนหมดอายุ (DEFERRED)
ลำดับ (orchestrator · หลังหน้า theme ของรอบนี้ merge แล้ว): `deploy.sh staging` (หรือ `production --no-import` ส่งโค้ด) → `deploy.sh production --refresh-only --dry-run` → `--refresh-only` · คาด `runs[-1].refresh` = 5 step (zone5 ซ้ำได้จนมี seed) · `warnings` [] · รันซ้ำ = แก้ 0

## ขั้นตอน staging (orchestrator)
1. เจ้าของเปิด SSH + เพิ่ม public key (O-6) → `scripts/deploy.sh staging --discover` → ยืนยัน path ที่ siteurl = `https://staging.sharedheritage.net` → `cp scripts/deploy/hosts.env.example scripts/deploy/hosts.env` แล้วใส่ `SH_STAGING_WP_PATH` (+ `SH_PRODUCTION_WP_PATH` ถ้าเห็นในผล discover)
2. `scripts/deploy.sh staging --dry-run` → อ่านแผน → `scripts/deploy.sh staging`
3. เปิด `https://staging.sharedheritage.net/` + ทุก path ใน `docs/smoke-urls` (`SMOKE_BASE=https://staging.sharedheritage.net bash scripts/smoke.sh`) · ดูรายงาน import (`~/sh-deploy/logs/import-staging-*.json` → `runs[-1].warnings` · local fresh import 2026-10-28 = 0 · script พิมพ์ `SH_IMPORT_WARNINGS=<n>` และ exit 4 ถ้า > 0) · ทดสอบ HEVC บน iPhone/Safari (O-5)
4. ส่งลิงก์ให้เจ้าของดู
- staging พัง: รันซ้ำ (idempotent) หรือสร้าง staging ใหม่จาก prod ใน hPanel (staging ไม่มีสำรองอัตโนมัติจาก script)

## ก่อนขึ้น production (checklist ให้ orchestrator)
- [ ] เจ้าของดู staging แล้วตอบ "ใช่" (ชื่อสิ่งที่อนุมัติ + เวลา บันทึกใน LOG) → เขียน `orchestrator/deploy-approved` (ไม่ว่าง)
- [ ] deploy จาก commit ที่ merge แล้ว (tree สะอาด — script บังคับ) · `scripts/deploy.sh production --dry-run` อ่านแผน
- [ ] สำรอง `wp-content` + export DB ของ prod ก่อน — **script ทำเองเป็นขั้นที่ 2** · จด path `ROLLBACK.txt` ที่พิมพ์ออกมา
- [ ] เปลี่ยน theme จาก `hostinger-ai-theme` เป็น `sharedheritage` (O-6 · script ทำในขั้น post) และตรวจ Wordfence / LiteSpeed cache ไม่ทับ (script purge หลัง deploy · ตรวจหน้าใน incognito)
- [ ] ACF ฟรีติดตั้ง+active (script ทำ · ไม่มี ACF เว็บไม่พังแต่แก้ฟิลด์ในกล่องไม่ได้)
- [ ] seed import ทำบน staging ก่อน — prod ที่มีเนื้อหาแล้ว script หยุดเอง (exit 3) · ห้าม import ทับเนื้อหาจริงบน prod โดยไม่ถามเจ้าของ
- [ ] ชั้นพิเศษใน DOD (deploy · role · upload · ข้อมูลส่วนบุคคล) ผ่าน review เต็ม
- [ ] หลังเสร็จ: smoke กับ `SMOKE_BASE=https://sharedheritage.net` · **ลบ `orchestrator/deploy-approved` ทันที**

## Rollback
### production (จากสำรองของ script)
สำรองอยู่ที่ `~/sh-deploy/backups/production-<ts>/` บน server: `db.sql` · `wp-content.tar.gz` · `stylesheet.txt` (theme เดิม) · `ROLLBACK.txt` (คำสั่งชุดนี้พร้อม path จริง)
```bash
ssh -p 65002 -i ~/.ssh/sharedheritage_hostinger u972755030@156.67.222.233
BK=~/sh-deploy/backups/production-<ts> ; WP=<SH_PRODUCTION_WP_PATH>
cd "$WP"
mv wp-content "wp-content.failed-<ts>"              # เก็บของที่ deploy ไว้ตรวจ
tar -xzf "$BK/wp-content.tar.gz" -C "$WP"           # คืน wp-content ทั้งก้อน (theme · mu-plugin · plugins · uploads)
# คืน DB (theme ที่ active เดิม · permalink · ACF · เนื้อหา) — ⛔ ห้าม `wp db import` (proc_open ถูกปิด) → mysql ตรง
DB_NAME=<ดู ROLLBACK.txt> ; DB_CS=<ดู ROLLBACK.txt>
q() { sed 's/[\\"]/\\&/g'; }
CNF="$(mktemp "$BK/.db-cnf.XXXXXX")"               # 600 · รหัสอ่านจาก wp-config ไม่พิมพ์ขึ้นจอ
{ echo '[client]'
  printf 'user="%s"\n' "$(wp --path="$WP" config get DB_USER | q)"
  printf 'password="%s"\n' "$(wp --path="$WP" config get DB_PASSWORD | q)"
  cat "$BK/db-conn.cnf"; } > "$CNF"
mysql --defaults-extra-file="$CNF" --default-character-set="$DB_CS" "$DB_NAME" < "$BK/db.sql"; echo "mysql exit=$?"
rm -f "$CNF"
wp --path="$WP" cache flush
wp --path="$WP" litespeed-purge all || true
# ตรวจหน้าเว็บแล้วค่อยลบ: rm -rf "$WP/wp-content.failed-<ts>"
```
- ถอยแค่ theme (ไม่คืน DB): `wp --path="$WP" theme activate "$(cat "$BK/stylesheet.txt")"` แล้ว purge — mu-plugin ยังอยู่แต่ไม่มีผลกับ theme เดิม
- ถอดแค่โค้ดของเรา: `wp theme activate <เดิม>` → `rm -rf "$WP/wp-content/mu-plugins/sharedheritage-core" "$WP/wp-content/mu-plugins/sharedheritage-core.php"` (เนื้อหาที่ import ยังอยู่ใน DB · CPT ซ่อนเพราะไม่มี mu-plugin)
- `ROLLBACK.txt` ของแต่ละชุดมีคำสั่งชุดนี้พร้อมค่าจริง (WP · BK · TS · DB_NAME · DB_CS) วางทั้งก้อนใน bash ได้
- ไม่มี SSH: hPanel › Files › File Manager (คืน wp-content จาก tar) + hPanel › Databases › phpMyAdmin (import `db.sql`) · หรือ hPanel › Backups (สำรองรายวันของ Hostinger)
- สำรองเก็บบน server (ใช้ quota) — ลบชุดเก่าเมื่อ prod นิ่งแล้ว (`ls ~/sh-deploy/backups`)

### staging
รัน `scripts/deploy.sh staging` จาก commit ก่อนหน้า (idempotent) · หรือ hPanel › WordPress › Staging สร้างใหม่จาก prod
