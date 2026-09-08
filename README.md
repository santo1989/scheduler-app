# Dynamic Task Scheduler

A small PHP/MySQL app for scheduling tasks on a drag-to-reschedule daily calendar.

## Setup

1. Copy `.env.example` to `.env` and adjust the DB credentials.
2. Import the schema: `mysql -u root -p < init.sql`
3. Point your webserver's document root at this folder (PHP 8.1+, PDO MySQL extension).

## Structure

- `bootstrap.php` — shared entry point (config, DB connection, CSRF, helpers).
- `config.php` — environment loading, error reporting, security headers.
- `includes/` — `Database` (PDO singleton), CSRF helpers, misc helpers.
- `index.php` — calendar view + add-task form.
- `create_task.php`, `reschedule_task.php`, `delete_task.php` — task CRUD endpoints.
- `assets/` — front-end JS/CSS.

## Security notes

- All state-changing requests require a CSRF token (form field for POST forms, `X-CSRF-Token` header for the AJAX endpoints).
- DB credentials are read from `.env` (not committed) rather than hardcoded.
- Errors are logged server-side and never leak details to the client in production (`APP_ENV=production`, the default).
