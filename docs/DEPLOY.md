# DEPLOY — Hostinger `sharedheritage.net`

## ข้อเท็จจริง (ตรวจ 2026-10-27)
Business plan · **live สาธารณะ** · WP เปล่า (0 page · theme `hostinger-ai-theme`) · Wordfence · LiteSpeed · PHP 8.5 บน server · DNS Squarespace → 156.67.222.233 · owner it.bgk@unesco.org แชร์สิทธิ์มา

## การเข้าถึง (2026-10-27)
- hPanel: บัญชี it.bgk@unesco.org (เราเป็นผู้ร่วมงาน) · staging = `staging.sharedheritage.net` (สร้าง 2026-10-27 · คัดลอกจาก prod · WP + DB แยก)
- SSH: port 65002 · user u972755030 · host 156.67.222.233 · คีย์ `~/.ssh/sharedheritage_hostinger` (ed25519 เฉพาะงานนี้ · ไม่มี passphrase · อยู่นอก repo) · เจ้าของต้องเปิด SSH + เพิ่ม public key ใน hPanel ก่อน
- ครั้งแรกที่ต่อ SSH: script ใช้ `StrictHostKeyChecking=accept-new` (จำ host key ครั้งแรก) → เทียบ fingerprint กับที่ hPanel แสดงก่อนรันจริง (`ssh-keygen -F '[156.67.222.233]:65002'`)

## กติกา (hook บังคับ)
1. **staging ก่อนเสมอ** (`scripts/deploy.sh staging`) — ต้องมี staging ใน hPanel + SSH/key ก่อน (O-6, เจ้าของทำ/อนุญาต)
2. **production ต้องมี `orchestrator/deploy-approved`** — orchestrator เขียนหลังเจ้าของตอบ "ใช่" ในแชทเท่านั้น (ไม่มีไฟล์ = guard-bash บล็อก `deploy.sh` ที่ไม่ใช่ staging และ rsync/scp/sftp ไปโฮสต์ปลายทาง · `deploy.sh` ตรวจซ้ำเอง: ไม่มีไฟล์/ไฟล์ว่าง = exit 1 แม้ `--dry-run`) · ลบไฟล์ทันทีหลัง deploy เสร็จ — การอนุมัติใช้ครั้งเดียว
3. dev ⛔ ไม่ deploy เอง · `deploy.sh` เขียนโดย wp-dev แต่รันโดย orchestrator

## `scripts/deploy.sh`
```
scripts/deploy.sh [staging|production] [--dry-run] [--discover] [--no-import]     # ไม่ระบุ = staging
```
| flag | ทำอะไร |
|---|---|
| `--dry-run` | พิมพ์ทุกคำสั่ง (ssh · rsync · wp-cli ฝั่ง server แบบ copy-paste ได้) **ไม่เชื่อมต่อ server** · check ฝั่ง server ถือว่าผล "ยังไม่มี" เพื่อให้เห็นคำสั่งที่จะตามมา |
| `--discover` | อ่านอย่างเดียว: SSH ไปหา `wp-config.php` ใต้ `~/domains` (ลึก 4) → พิมพ์ path · siteurl · home · theme ที่ active ไม่เขียนอะไร |
| `--no-import` | ส่งแค่โค้ด + ACF/theme/permalink/purge · ไม่ import seed (deploy ซ้ำเพื่อแก้โค้ด · หรือ prod ที่มีเนื้อหาแล้ว) |

**config** `scripts/deploy/hosts.env` (คัดลอกจาก `hosts.env.example` · gitignored · ไม่มีความลับ) · ลำดับ: env ของ shell > `hosts.env` > `hosts.env.example` · อ่านแบบ KEY=VALUE (ไม่ source) เฉพาะ key ที่รู้จัก
- `SH_DEPLOY_SSH_{HOST,PORT,USER,KEY}` · `SH_DEPLOY_REMOTE_DIR` (default `sh-deploy` — relative กับ home บน server = นอก webroot: `tmp-*` · `backups/` · `logs/`)
- `SH_STAGING_URL` / `SH_STAGING_WP_PATH` · `SH_PRODUCTION_URL` / `SH_PRODUCTION_WP_PATH` (WP path = absolute · ยังไม่รู้ → `--discover`) · `SH_DESIGN_ASSETS` / `SH_MEDIA_OUT` (ว่าง = `Unesco/design/assets` · `Unesco/media/out`)

**ลำดับที่ script ทำ** (ไฟล์ฝั่ง server = `scripts/deploy/remote.sh` ส่งทาง `ssh … bash -s -- <phase>` · ไม่ต้องติดตั้งอะไรบน server นอกจาก wp-cli ของ Hostinger)
0. local: config ครบ · key มีจริง · production: `deploy-approved` + **tree ต้องสะอาด** (wp-content · scripts/import · content) · import: ภาพทุกไฟล์ที่ `image-map.json` "files" อ้างต้องมีใน design/assets
1. **preflight** (อ่านอย่างเดียว): `wp core is-installed` · `siteurl` ของ WP path **ต้องตรง URL ที่คาด** (ไม่ตรง = หยุด · กันส่งผิด site) · พิมพ์ theme + จำนวน object/timeline_item · **production + import + มี object/timeline_item อยู่แล้ว → หยุด exit 3** (ห้าม import ทับ prod · ถามเจ้าของ · ส่งแค่โค้ดใช้ `--no-import`)
2. **production เท่านั้น — สำรองก่อนเขียนอะไรทั้งหมด:** `wp db export` + `tar wp-content` + theme เดิม → `~/sh-deploy/backups/production-<ts>/` (chmod 700/600) + `ROLLBACK.txt` (พิมพ์ออกจอด้วย)
3. prepare: `mkdir -p wp-content/mu-plugins` · โฟลเดอร์ tmp import
4. rsync (`-rlptz` · exclude `.git` `.playground` `node_modules` `.DS_Store`): mu-plugin ทีละรายการ (โฟลเดอร์ `sharedheritage-core/` ใช้ `--delete` ภายในโฟลเดอร์ตัวเอง · ไฟล์ loader `sharedheritage-core.php` ไม่มี `--delete`) → theme `sharedheritage/` (`--delete` ภายในโฟลเดอร์ theme) · **ไม่แตะ wp-content อื่น** (mu-plugin ของ Hostinger · theme อื่น · uploads) · import: bundle (`scripts/import/` · `content/` · ภาพ 159 ไฟล์ที่ image-map อ้าง · `media/out` ยกเว้น `*.html`) → `~/sh-deploy/tmp-<env>-<ts>/`
5. post: ACF ฟรี (ข้ามถ้ามี ACF Pro active · ติดตั้ง/activate ถ้ายังไม่มี) · `wp theme activate sharedheritage` (ถ้ายังไม่ active) · `wp rewrite structure '/%postname%/'` + `wp rewrite flush --hard` · import: `SH_IMPORT_ASSETS_DIR=<tmp>/assets SH_IMPORT_MEDIA_DIR=<tmp>/media SH_IMPORT_SUBSIZES=1 SH_IMPORT_REPORT=~/sh-deploy/logs/import-<env>-<ts>.json wp eval-file <tmp>/scripts/import/import.php` (mode `missing` = ไม่ทับของที่ admin แก้) · **ลบ tmp เสมอ** (trap แม้ import พัง) · `wp litespeed-purge all` ถ้ามีคำสั่ง
- idempotent: รันซ้ำได้ — rsync ส่งเฉพาะที่ต่าง · ACF/theme ข้ามถ้ามีแล้ว · import หาเดิมด้วย `_sh_import_key` (รอบ 2 สร้างใหม่ 0)

## ขั้นตอน staging (orchestrator)
1. เจ้าของเปิด SSH + เพิ่ม public key (O-6) → `scripts/deploy.sh staging --discover` → ยืนยัน path ที่ siteurl = `https://staging.sharedheritage.net` → `cp scripts/deploy/hosts.env.example scripts/deploy/hosts.env` แล้วใส่ `SH_STAGING_WP_PATH` (+ `SH_PRODUCTION_WP_PATH` ถ้าเห็นในผล discover)
2. `scripts/deploy.sh staging --dry-run` → อ่านแผน → `scripts/deploy.sh staging`
3. เปิด `https://staging.sharedheritage.net/` + ทุก path ใน `docs/smoke-urls` (`SMOKE_BASE=https://staging.sharedheritage.net bash scripts/smoke.sh`) · ดูรายงาน import (`~/sh-deploy/logs/import-staging-*.json` · คำเตือน 10 = P-209) · ทดสอบ HEVC บน iPhone/Safari (O-5)
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
wp --path="$WP" db import "$BK/db.sql"              # คืน DB (theme ที่ active เดิม · permalink · ACF · เนื้อหา)
wp --path="$WP" cache flush
wp --path="$WP" litespeed-purge all || true
# ตรวจหน้าเว็บแล้วค่อยลบ: rm -rf "$WP/wp-content.failed-<ts>"
```
- ถอยแค่ theme (ไม่คืน DB): `wp --path="$WP" theme activate "$(cat "$BK/stylesheet.txt")"` แล้ว purge — mu-plugin ยังอยู่แต่ไม่มีผลกับ theme เดิม
- ถอดแค่โค้ดของเรา: `wp theme activate <เดิม>` → `rm -rf "$WP/wp-content/mu-plugins/sharedheritage-core" "$WP/wp-content/mu-plugins/sharedheritage-core.php"` (เนื้อหาที่ import ยังอยู่ใน DB · CPT ซ่อนเพราะไม่มี mu-plugin)
- ไม่มี SSH: hPanel › Files › File Manager (คืน wp-content จาก tar) + hPanel › Databases › phpMyAdmin (import `db.sql`) · หรือ hPanel › Backups (สำรองรายวันของ Hostinger)
- สำรองเก็บบน server (ใช้ quota) — ลบชุดเก่าเมื่อ prod นิ่งแล้ว (`ls ~/sh-deploy/backups`)

### staging
รัน `scripts/deploy.sh staging` จาก commit ก่อนหน้า (idempotent) · หรือ hPanel › WordPress › Staging สร้างใหม่จาก prod
