# AGENTS.md

Laravel 12 (PHP 8.2+, timezone `Asia/Dhaka`) travel-agency marketing site. Three demo landing pages (`demo1`–`demo3`, HTML mockups) plus an AJAX contact form that persists to MySQL and sends email via a queued job. Frontend: Vite 7 + Tailwind 4, Bootstrap 5, FontAwesome, SweetAlert2, Swiper.

## Two dev environments (don't mix them)

- **Local (no Docker):** uses `.env` → MySQL at `127.0.0.1:3306`. Run `composer run dev` (starts artisan serve, `queue:listen`, pail, vite together). App at `127.0.0.1:8000`.
- **Docker:** uses `.env.docker` → the compose `mysql` service. `docker-compose up -d --build` (prod-like Dockerfile) or `docker-compose -f docker-compose.dev.yml up -d --build` (bind-mounts + separate vite container). App `localhost:8080`, phpMyAdmin `localhost:8081`, **MySQL exposed on host port 3307, not 3306** (avoids Laragon conflict). Never commit real `.env`/`.env.docker`; edit the committed `.env.example` / `.env.docker.example` instead.

Both envs use `QUEUE_CONNECTION=database`. **A queue worker must be running or contact emails never send** (contact form flow: `ContactService::store` → `ContactMail` row → `SendContactMailJob`, 3 tries / 10s backoff; status 0=pending, 1=sent, 2=failed, failure detail in `error_message`).

## Commands

- Tests: `composer test` (runs `artisan config:clear` then `artisan test`). PHPUnit uses in-memory sqlite via `phpunit.xml` — no DB setup needed.
- Feature tests disable CSRF via `withoutMiddleware(ValidateCsrfToken::class)`; follow that pattern for new POST endpoint tests.
- Formatter: Laravel Pint is installed but not wired into scripts — run `./vendor/bin/pint` manually. No config file (default preset). No lint/typecheck step exists.
- CI (`laravel.yml`) runs `php artisan test --log-junit=test-results.xml --coverage-clover=coverage.xml`; SonarQube consumes those two XML files, which are not gitignored. Don't delete them.
- Fresh local setup: `composer run setup` (copy env, key:generate, migrate, npm install + build).

## Architecture conventions

- `app/Http/Controllers` stay thin; business logic goes in `app/Services`. `ResponseCodeAndMessage` holds HTTP status constants + messages, `ContactService` holds validation, `GlobalFunction` has static helpers.
- Models are thin with static helpers (e.g. `ContactMail::saveContactMail`). `ContactMail.request_source` is a JSON-cast array.
- Site/contact details come from `config/custom.php` (`OWNER_*`, `OFFICE_*`, `FACEBOOK_*`, `WHATSAPP_*` env vars). Views must use `config('custom.xxx')`, never `env()` directly.
- Routes are only in `routes/web.php`; `POST /contact` (`contact.submit`) is AJAX-only JSON and rejects non-AJAX requests.

## Frontend

- Each demo page gets its own CSS entry (`resources/css/demoN.css`) plus shared `resources/js/global.js`; a page's blade uses `@vite([...])`. New entries must be registered in `vite.config.js` or assets silently won't load in builds.
- Unbundled static assets live in `public/` (`css/`, `images/`, `videos/`, `fonts/`) and are referenced with `asset('...')` — check there before assuming an asset is missing from Vite.
- `resources/views/partials/sections/*` are shared page sections (nav, hero, about, services, contact, footer, whatsapp...). `public/build` is gitignored; rebuild with `npm run build` after adding entries.
