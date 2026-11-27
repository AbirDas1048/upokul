<?php

/*
 |--------------------------------------------------------------------------
 | Test bootstrap: pin the test environment before Laravel boots
 |--------------------------------------------------------------------------
 |
 | Laravel reads configuration from $_SERVER / $_ENV / getenv(). Inside the Docker containers those
 | already hold the DEVELOPMENT values from the env_file (DB_CONNECTION=mysql, MAIL_MAILER=smtp,
 | QUEUE_CONNECTION=database...), and PHPUnit's <env> entries do not replace values that already
 | exist (even with force="true", that only reaches getenv()). Without this file,
 | `docker compose exec app php artisan test` would run RefreshDatabase against the real
 | development database and send real mail.
 |
 | The values below are written to all three places, so every test run, wherever it is started
 | (Laragon, Docker, CI), uses an in-memory SQLite database, the array mailer / cache / session
 | and the sync queue, and sees the anti-abuse settings the tests were written for.
 |
 */

$testScratch = sys_get_temp_dir().'/upokul-tests-'.getmypid();

$testEnvironment = [
    'APP_ENV' => 'testing',
    'DB_CONNECTION' => 'sqlite',
    'DB_DATABASE' => ':memory:',
    // Laravel merges a DB_URL (e.g. mysql://...) OVER the connection's driver/database, so it must be empty.
    'DB_URL' => '',
    'MAIL_MAILER' => 'array',
    'QUEUE_CONNECTION' => 'sync',
    'CACHE_STORE' => 'array',
    'SESSION_DRIVER' => 'array',
    'BROADCAST_CONNECTION' => 'null',

    // Admin panel / installer: existing tests run as an "installed" site, and nothing a test does may
    // touch the real .env, the real install lock or the real public/uploads folder.
    'INSTALLER_INSTALLED' => 'true',
    'INSTALLER_LOCK' => $testScratch.'/installed.lock',
    'INSTALLER_ENV_FILE' => $testScratch.'/.env',
    'UPLOAD_PATH' => $testScratch.'/uploads',

    // Anti-abuse settings the contact-form tests assume; a developer's .env must not change them.
    'TRUSTED_PROXIES' => '',
    'TURNSTILE_SITE_KEY' => '',
    'TURNSTILE_SECRET_KEY' => '',
    'CONTACT_LIMIT_PER_MINUTE' => '3',
    'CONTACT_LIMIT_PER_HOUR' => '10',
    'CONTACT_LIMIT_PER_EMAIL_HOUR' => '3',
    'CONTACT_LIMIT_GLOBAL_PER_HOUR' => '200',
    'CONTACT_MIN_FILL_SECONDS' => '3',
    'CONTACT_TOKEN_MAX_HOURS' => '6',
    'CONTACT_DUPLICATE_MINUTES' => '10',
    'CONTACT_MAX_LINKS' => '1',
];

foreach ($testEnvironment as $name => $value) {
    putenv("{$name}={$value}");
    $_ENV[$name] = $value;
    $_SERVER[$name] = $value;
}

require __DIR__.'/../vendor/autoload.php';
