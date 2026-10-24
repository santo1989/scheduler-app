# scheduler-app

Auto-generated overview (2026-10-24); refine as the app is worked on.

## Stack
- plain PHP
- Remote: https://github.com/santo1989/scheduler-app.git

## Layout
- Top level: .github/, assets/, includes/, .env.example, .gitignore, bootstrap.php, config.php, create_task.php, delete_task.php, index.php, init.sql, README.md, reschedule_task.php
- SQL files: init.sql

## Work log
- 2026-10-24: pre-work zip in `D:\NTG_APP\_backups\pre-work-2026-10-24`. Branch `chore/claude-hardening-2026-10-24`: added LICENSE (docs only; no code changed), CLAUDE.md, system_info.md.

## Open items (need owner approval)
- Database backup, licence check and security headers are NOT added here: not a standard Laravel app, or its database settings are not available in this folder; needs a per-app decision.
- No dependency/vulnerability audit done for this app.

## DB auto-backup (added 2026-10-24)
- `php tools/db_backup.php [--keep=14]` (CLI only; `tools/.htaccess` denies web access). Uses the app's own DB settings, writes `mysqldump` files to `D:\NTG_APP\_db_auto_backups\<db>\` (outside the web root; override with `DB_BACKUP_DIR`), keeps the newest 14. Set `MYSQLDUMP_PATH` if `mysqldump` is not on PATH.
- Schedule it daily with Windows Task Scheduler (action: `php.exe D:\NTG_APP\www\scheduler-app	ools\db_backup.php`). NOT scheduled yet; needs owner approval.
