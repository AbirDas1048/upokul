# Upokul Travels & Holidays

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

## Tests & formatting

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
