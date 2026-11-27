# Wayfolio

> **Launch, brand and run your travel business website.** A self-installing Laravel product: a polished landing
> site (three designs) plus a role-based admin console. Each installation carries the customer's own brand —
> Wayfolio is only the name of the software, shown on the installer and as a small "Powered by" credit.

Laravel 12 (PHP ^8.2) + Vite. Two ways to run it: **Docker** or **Laragon**.

## Option A — Docker (no PHP/Node needed on your PC)

Requires only Docker Desktop. `vendor/` and `node_modules/` are installed
**inside** the containers, so you do not need them on your machine.

```bash
git clone <repo-url> upokul && cd upokul

# 1. Create the Docker env file (it is git-ignored)
cp .env.docker.example .env.docker          # Windows cmd: copy .env.docker.example .env.docker

# 2. Generate an APP_KEY and paste it into .env.docker (APP_KEY=base64:...)
docker run --rm php:8.2-cli php -r "echo 'base64:'.base64_encode(random_bytes(32)).PHP_EOL;"

# 3. Fill in OWNER_*, OFFICE_*, MAIL_* values in .env.docker

# 4a. Development (live code reload + Vite hot reload)
docker compose -f docker-compose.dev.yml up -d --build

# 4b. Or production-like (assets built into the image)
docker compose up -d --build
```

| Service | URL |
|---|---|
| App | http://localhost:8080 |
| phpMyAdmin | http://localhost:8081 (root / secret) |
| Vite (dev only) | http://localhost:5173 |
| MySQL from host | `127.0.0.1:3307` |

The first dev start runs `composer install` and `npm install` automatically, so
give it a minute or two; follow progress with `docker compose -f docker-compose.dev.yml logs -f app`.
Migrations run on every start.

Useful commands (add `-f docker-compose.dev.yml` for the dev stack):

```bash
docker compose exec app php artisan queue:work      # process the contact-mail queue
docker compose exec app php artisan schedule:work  # runs the 5-minute safety net that re-queues failed / stuck contact mails
docker compose down                                 # stop (add -v to also wipe DB/vendor volumes)
```

Development-only commands (the production-like image installs with `--no-dev`, so it has no PHPUnit or Composer dev tools; use the dev stack):

```bash
docker compose -f docker-compose.dev.yml exec app php artisan test        # run tests
docker compose -f docker-compose.dev.yml exec app composer require <pkg>  # add a PHP package
```

Contact-form emails are queued (`QUEUE_CONNECTION=database`), so keep a
`queue:work` process running or mails will never send.

## Option B — Laragon (Windows)

Requires Laragon with PHP 8.2+, Composer and Node `^20.19.0` or `>=22.12.0` (required by Vite 7; Node 22 LTS recommended).

```bash
# Put the project in D:\laragon\www\upokul (or any folder) and open Laragon's terminal
composer install                 # creates vendor/
npm install && npm run build     # creates node_modules/ and public/build
cp .env.example .env
php artisan key:generate
```

Pick a database in `.env`:

- **SQLite (simplest):** keep `DB_CONNECTION=sqlite` and run
  `type nul > database\database.sqlite`
- **MySQL (Laragon):** create a database `upokul_travels` in HeidiSQL, then set
  `DB_CONNECTION=mysql`, `DB_HOST=127.0.0.1`, `DB_PORT=3306`,
  `DB_DATABASE=upokul_travels`, `DB_USERNAME=root`, `DB_PASSWORD=`

Then:

```bash
php artisan migrate --force
php artisan queue:work           # separate terminal, needed for contact emails
```

Open `http://upokul.test` (Laragon auto virtual host: Menu → Apache → Reload),
or run `composer dev` and use `http://localhost:8000`.
Fill in `OWNER_*`, `OFFICE_*` and `MAIL_*` in `.env` (use Mailtrap or `MAIL_MAILER=log` locally).

## First-time installation & admin panel

The site is a self-installing, fully admin-managed product. Nothing in `.env` has to be edited for
branding any more — the owner details, contact info, texts, content, images and design all live in the
database and are edited from the admin panel.

1. **Install** — after `composer install`, `npm run build` and a working database, open the site. Every
   page redirects to **`/install`** until the wizard has run. It checks the server, runs the migrations,
   shows the Wayfolio brand and asks for **your business name**, an optional **logo upload** (also written to `APP_NAME` in `.env`), **owner name / e-mail / phone**,
   office contact info, the home-page design, whether to add the starter content, and creates the
   **master administrator**. The wizard disappears (404) once it has finished, and you land on the finished website
   (the standard landing page) with a **one-time popup** linking to the admin panel. The popup is tied to a one-time
   ticket stored in the database, so it never shows again — not on refresh, not for other visitors.
   *Complete it straight after deploying: until then anyone who finds the URL could install.*
2. **Sign in** at **`/admin`** (the popup links to `/admin/login`) with the master account.
3. **Edit safely — drafts and Publish.** Everything you change in the admin panel is saved as a **draft**.
   Visitors keep seeing the previous version (right after installing: the standard landing page with your name and
   contact details) until you press **Publish changes** in the top bar and confirm. Use **Preview draft** (top bar)
   to look at your unpublished changes first, at desktop / tablet / phone width. Mail (SMTP) settings are the one
   exception: they apply immediately. Publishing is its own permission (`publish.update`).
4. **Manage everything** from the sidebar:

| Area | What you can do |
|---|---|
| Dashboard | Message statistics, 14-day chart, latest messages, content counts, system status |
| Messages | Read, search, mark read/unread, re-send the notification mail, delete contact-form messages |
| Page sections | Headings and wording of every section (hero, about, services, …), switch sections on/off |
| Services · Journeys · How it works · Trust numbers · Gallery · Reviews · FAQs · About highlights | Add / edit / hide / delete / re-order the cards and photos (image upload included) |
| Appearance & preview | Pick the home-page design (Aurora / Noir / Glass / chooser) and preview any design at desktop / tablet / phone width — the **Live preview** button is on every page |
| Site settings | Project name, owner, contact + social links, Google Maps, logo / favicon / banner / owner photo, SEO, **SMTP mail** (password stored encrypted, test-mail button) |
| Admin users · Roles & permissions · Activity log | See below |

### Roles and permissions

* The **master admin** (created by the wizard) can do everything, needs no role, and cannot be edited,
  limited, deactivated or deleted by anyone else.
* Every other admin gets a **role** — a named set of `module.action` permissions
  (**View / Add / Edit / Delete** per module, e.g. `services.create`, `settings.update`). The role editor is a
  permission matrix; Add, Edit and Delete automatically include View. A user can only do — and only *sees in the
  sidebar* — what their role allows; everything else answers 403.
* **No privilege escalation:** you can only grant permissions you hold yourself, only assign/edit roles you fully
  hold, and only manage users who have no more power than you. Nobody can edit themselves through the Users screen
  (use *My profile*) or delete the master admin.
* Two starter roles are created by the wizard (**Content Manager**, **Support Agent**) as examples.
* New modules: add them to `config/permissions.php` and they show up in the role editor automatically;
  new content blocks: add a type to `config/content_types.php`; new settings: `config/site_settings.php`.

**Locked out?** Run `php artisan admin:reset-password` (master) or `php artisan admin:reset-password someone@example.com`
on the server — it sets a new password and re-activates the account.

**Pages unstyled?** `public/hot` is written by `npm run dev` and makes Laravel load all CSS/JS from the dev server; if the
server stops, the file stays behind. The app now detects a dead dev server on every request and silently uses the built
assets (`public/build`, created by `npm run build`) instead. The installer, login and admin panel additionally load plain static
CSS (`public/css/admin-auth.css`, `public/css/admin.css`, `bootstrap.min.css`, `vendor/fontawesome`), so they keep their design
even with no build at all. Only the behaviour scripts (dropdowns, confirm dialogs) need the Vite bundle: run `npm run build`
once after pulling.

**Branding.** Two separate things: the *product* (Wayfolio — `config/product.php`: name, tagline, logo, also
overridable with `PRODUCT_NAME` in `.env`) and the *customer's site* (name, logo, colours of the content — chosen in the
installer, edited under Site settings). Without an uploaded logo the site uses a neutral placeholder
(`public/images/default-logo.svg`); the website never shows another business's logo.
The built-in sample copy (services, FAQs, reviews) is generic travel-agency text — edit or replace it in the admin panel.

**Re-running the wizard** (fresh start): delete `storage/app/installed.lock` and the `installed_at` row in the
`settings` table. **Docker:** `public/uploads` (admin-uploaded images) and `storage/` (lock file, logs) are volumes.

> **"SaaS"** here means a white-label product: one installation = one customer site, installed through a wizard and
> run entirely from its admin panel. It is *not* multi-tenant (many customers sharing one install, subdomains,
> billing/plans) — that would be a separate architectural step.

## Security & anti-spam

The contact form is the only public endpoint that writes data, so it is layered:

| Threat | Protection |
|---|---|
| Floods / repeated submits by a bot | Rate limiter `contact` on `POST /contact`: per IP (3/min, 10/hour), per e-mail (3/hour) and whole site (200/hour). Over the limit → HTTP 429 + `Retry-After`. Tunable with `CONTACT_LIMIT_*` in `.env`. |
| Bots that fill every field | Invisible honeypot field. A filled honeypot is answered with a fake "success" and nothing is stored or sent. |
| Scripts posting without loading the page | Signed (encrypted) form token that proves the page was loaded, cannot be forged, expires after 6 h and must be at least 3 s old. |
| Double-click / replay | The same message from the same address is accepted once per 10 minutes. |
| Real bots (optional) | Cloudflare Turnstile: set `TURNSTILE_SITE_KEY` and `TURNSTILE_SECRET_KEY` and the widget appears and is verified server-side (fails closed). |
| Using the site to mail-bomb someone | The "thank you" mail goes to the typed address, so there is a per-address limit, and links are limited (name/subject: none, message: 1). |
| SQL injection | Only Eloquent / bound parameters are used; there is no raw SQL. |
| Header injection | Control characters (CR/LF) are stripped from single-line fields and the address must be RFC-valid. |
| XSS | All output is escaped (Blade `{{ }}`, escaped popups, e-mail templates) and a strict, nonce-based Content-Security-Policy blocks injected scripts. |
| CSRF | `POST /contact` is inside the `web` group (token required). |
| Browser hardening | `Content-Security-Policy`, `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`, `Permissions-Policy`, HSTS (HTTPS only). |
| Known-vulnerable packages | Run `composer audit` and `npm audit` regularly (both are clean today). |

**Production checklist**

- `APP_ENV=production`, `APP_DEBUG=false`, `SESSION_SECURE_COOKIE=true`, serve over HTTPS.
- Behind a proxy / CDN set `TRUSTED_PROXIES` (otherwise every visitor shares the proxy's IP and IP limits are useless).
- Change the database password (`secret` is a development default) and do not expose MySQL or phpMyAdmin publicly; the compose files already bind them to `127.0.0.1`.
- If HTML pages are cached by a CDN, exclude the contact section or the form token will go stale (re-load fixes it).

## Tests & formatting

The test suite always runs against an **in-memory SQLite database** with the array mailer, array cache and sync queue (pinned in `tests/bootstrap.php`), so it never touches your development MySQL data or sends real e-mail, including when run inside the Docker containers.

```bash
composer test          # php artisan test
vendor/bin/pint        # format PHP
```

## Troubleshooting

- **`vendor/autoload.php` not found** → run `composer install` (Laragon) or restart the dev container.
- **"No application encryption key"** → `APP_KEY` empty in `.env` / `.env.docker`.
- **Port 8080/8081/3307 in use** → change the left side of the port mapping in the compose file.
- **Laragon MySQL conflict** → Docker MySQL is on 3307 on purpose, so both can run together.
- **Vite assets 404 in Docker prod build** → rebuild with `docker compose up -d --build`.

## Assets (images, video, PDFs, fonts)

All site assets are served from this app — no hot-linked third-party images (keeps the CSP strict, avoids broken links and third-party tracking, and lets the browser cache everything).

| Type | Location | Linked with |
|---|---|---|
| Logo, banner, favicon | `public/images/` | `asset('images/…')` |
| Destination photos | `public/images/destinations/{slug}-800.jpg` (cards) and `{slug}-1400.jpg` (full-screen) | metadata in `config/destinations.php` |
| Video + captions | `public/videos/` | `asset('videos/…')` |
| Downloadable PDFs/brochures | `public/docs/` | `asset('docs/…')` |
| Fonts | `public/fonts/` | `@font-face` in `resources/css/*_fonts.css` |
| User/admin uploads (future) | `storage/app/public/` via `Storage::disk('public')` after `php artisan storage:link` | store only the relative path in the DB; show with `Storage::disk('public')->url($path)` (not `Storage::url()`, which follows `FILESYSTEM_DISK` and may point at a different disk than the one that received the upload) |

Rules of thumb:
- **Static, code-reviewed assets → committed to Git** under `public/`. **Uploaded or large/changing assets → storage disk, path in the DB, not in Git** (swap the disk to S3/R2 later without changing the upload/URL code). **If the new disk returns cross-origin URLs, also add the bucket/CDN origin to `img-src` (and `media-src` for video) in `app/Http/Middleware/SecurityHeaders.php`, or serve files through the app origin; otherwise the CSP blocks them.**
- Never store image bytes (base64/BLOB) in the database.
- Name files in lowercase kebab-case; optimise before committing (JPEG/WebP ≈ 100–400 KB for photos, MP4 H.264 for video).
- Keep `width`/`height`, `alt` and `loading="lazy"` on `<img>`; use `fetchpriority="high"` only for the hero image.
- To change a destination photo: overwrite the two files with the same slug (or add a row in `config/destinations.php` plus both sizes).
