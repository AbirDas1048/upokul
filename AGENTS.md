# AGENTS.md - Upokul Travels Laravel Project

## Quickstart

| Command | Description |
|---|---|
| `composer install` | Install PHP dependencies |
| `npm install && npm run build` | Install JS deps and build Vite assets |
| `cp .env.example .env && php artisan key:generate` | Setup environment |
| `php artisan migrate --force` | Run migrations |
| `composer test` | Run full test suite |
| `composer dev` | Start dev server + queue + Vite |

## Test Commands

- `composer test` — runs `php artisan test --ansi` (clears config first)
- `php artisan test` — runs all tests (Unit + Feature)
- `php artisan test --filter=ContactPostTest` — run a specific test class
- Tests use `RefreshDatabase` for Feature tests; `Queue::fake()` is used to assert job dispatching
- Feature tests live in `tests/Feature/`, Unit tests in `tests/Unit/`

## Key Conventions

- **Contact form**: POST `/contact` (route named `contact.submit`). Must be an AJAX request (`X-Requested-With: XMLHttpRequest`). Validates name, email, phone, subject, mail_message. On success, dispatches `SendContactMailJob`.
- **Queue**: Jobs are dispatched but tested via `Queue::fake()`. The `SendContactMailJob` sends mail to `config('custom.office_email')` and updates the `ContactMail` model status.
- **DB**: SQLite in testing (`DB_DATABASE: :memory:`). Migrations define the `contact_mails` table.
- **Custom config**: Values read from `.env` via `config('custom.*')` — see `config/custom.php`. Env vars: `OWNER_NAME`, `OWNER_EMAIL`, `OFFICE_EMAIL`, etc.
- **Code style**: Run `vendor/bin/pint` (Laravel Pint) to format PHP. Lint via `composer lint` if available.
- **Lint/Formatter**: `vendor/bin/pint` formats PHP code. Check composer.json for scripts.

## CI Pipeline (github/workflows/laravel.yml)

Order matters: `composer install` → `npm ci` → `npm run build` → `php artisan key:generate` → `chmod -R 777 storage bootstrap/cache` → `php artisan test --coverage-clover=coverage.xml`

Also runs: Semgrep security scan (`semgrep ci`) and optional SonarQube scan.

## Project Structure

- `app/Http/Controllers/HomeController.php` — main controller with contact form handling
- `app/Services/ContactService.php` — business logic for contact form validation + DB save + job dispatch
- `app/Models/ContactMail.php` — Eloquent model for `contact_mails` table
- `app/Jobs/SendContactMailJob.php` — queued job that sends email via `Mail::to(config('custom.office_email'))`
- `routes/web.php` — defines `/`, `/demo1-3`, and `/contact` routes
- `tests/Feature/ContactPostTest.php` — key integration test for contact form (uses `RefreshDatabase` + `Queue::fake()`)