# AGENTS.md - Wayfolio (Laravel) Project

Wayfolio = the product (`config/product.php`). Each installation's own brand lives in the `settings` table; never hard-code a customer name, logo or location in code, views or defaults.

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
- **Custom config**: Values read from `.env` via `config('custom.*')` — see `config/custom.php`. Env vars: `OWNER_NAME`, `OWNER_EMAIL`, `OFFICE_EMAIL`, etc. These are only the **fallback**: once the site is installed, `App\Support\Settings::applyToConfig()` (called in `AppServiceProvider::boot`) overwrites `app.name`, `custom.*` and the SMTP mailer with whatever the admin saved in the `settings` table, so views/mail/jobs keep using `config()` unchanged.
- **Code style**: Run `vendor/bin/pint` (Laravel Pint) to format PHP. Lint via `composer lint` if available.
- **Lint/Formatter**: `vendor/bin/pint` formats PHP code. Check composer.json for scripts.

## Admin panel, installer and RBAC

- **Installer**: `/install` (`Install\InstallController`, `App\Support\Installer`). `InstallationGuard` (prepended to the `web` group, so it runs before `StartSession`) redirects every page to the wizard until installed and 404s the wizard afterwards. "Installed" = lock file `storage/app/installed.lock` **or** `settings.installed_at`; `config('installer.installed')` can force it (tests do, via `INSTALLER_INSTALLED` in `tests/bootstrap.php`, together with temp `INSTALLER_LOCK`, `INSTALLER_ENV_FILE`, `UPLOAD_PATH` so a test can never touch the real `.env`/uploads). `Installer::hasRun()` is the "real" state and decides whether the public site reads the DB or the built-in defaults.
- **Settings** (`App\Support\Settings`, table `settings`, schema in `config/site_settings.php`): keys are `group.name` / `section.<id>.<name>`; blank values fall back to the schema `default` unless the field has `allow_blank`. Password-type keys listed in `site_settings.encrypted` are stored encrypted. Always write through `Settings::set()` (it flushes the cache).
- **Draft → published** (`App\Support\Publisher`): admin edits are drafts; the public site reads only the JSON snapshot stored in `settings.published.snapshot` (settings + active content items; mail settings and secrets excluded). The wizard publishes the first snapshot; with no snapshot the site shows the built-in defaults. `Site` is in published mode unless the `DraftPreview` middleware (`?preview=draft`, signed-in active admin only) switches it to draft for that request and restores it afterwards. In tests call `$this->publish()` after admin changes before asserting on the public site. `Settings::applyToConfig()` applies the published values (pass `draft: true` for preview).
- **Content** (`content_items` table + `ContentItem`, types in `config/content_types.php`, CRUD in `Admin\ContentController`): `title` + JSON `data`. Public views read it through the shared `$site` (`App\Support\Site`): `$site->items('services')`, `$site->section('services', 'title')`, `$site->image('logo')`, `$site->enabled('faq')`. Don't hard-code copy in the section partials — add a field to the schema instead.
- **Permissions** (`config/permissions.php`): `module.action` keys → Gates (see `AppServiceProvider::configureAdmin`), `User::hasPermission()`, `Role::syncPermissions()`. The master admin (`users.is_master`, no role) passes `Gate::before`. Protect routes with `->middleware('can:module.action')` (or `HasMiddleware` in the controller; dynamic content routes call `Gate::authorize("{$type}.{action}")`). A guard test (`AdminHardeningTest`) fails if an admin route has neither. Escalation rules live in `App\Support\AdminGuard` — use them in any new user/role screen.
- **Admin UI**: Bootstrap 5.3. The admin CSS is a static file (`public/css/admin.css`, not built by Vite, so the panel is never unstyled); only `resources/js/admin.js` goes through Vite. `ViteDevServer` ignores a stale `public/hot`. The post-install popup (`partials/install-welcome`) is driven by a one-time DB ticket (`Installer::consumeWelcome`)., layouts `layouts/admin` and `layouts/auth`, field partial `admin/partials/field`. No inline scripts or `on*=` handlers (CSP): use `data-*` hooks handled in `admin.js` (`data-confirm`, `data-matrix`, `data-preview-root`, ...). The live preview iframes the public site, which is why CSP has `frame-src 'self'`.
- **Tests**: `Tests\Concerns\InteractsWithAdmin` (`installSite()`, `master()`, `adminWith([...permissions])`). Files: `InstallerTest`, `AdminRbacTest`, `AdminContentAndSettingsTest`, `AdminHardeningTest`.
- **Recovery**: `php artisan admin:reset-password [email]`.

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