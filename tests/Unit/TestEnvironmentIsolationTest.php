<?php

namespace Tests\Unit;

use Illuminate\Database\ConfigurationUrlParser;
use Tests\TestCase;

/**
 * The suite uses RefreshDatabase, which is destructive. These checks make sure a test run can never be
 * pointed at the development database or a real mail server, wherever it is started.
 */
class TestEnvironmentIsolationTest extends TestCase
{
    public function test_the_suite_runs_on_in_memory_sqlite_with_the_array_mailer_and_sync_queue(): void
    {
        $this->assertSame('testing', $this->app->environment());
        $this->assertSame('sqlite', config('database.default'));
        $this->assertSame(':memory:', config('database.connections.sqlite.database'));
        $this->assertSame('array', config('mail.default'));
        $this->assertSame('sync', config('queue.default'));
        $this->assertSame('array', config('cache.default'));
    }

    public function test_a_leaked_database_url_cannot_redirect_the_connection(): void
    {
        // Laravel merges the driver/database from DB_URL OVER the configured connection...
        $leaked = (new ConfigurationUrlParser)->parseConfiguration([
            'driver' => 'sqlite',
            'database' => ':memory:',
            'url' => 'mysql://root:secret@mysql:3306/upokul_travels',
        ]);
        $this->assertSame('mysql', $leaked['driver']);
        $this->assertSame('upokul_travels', $leaked['database']);

        // ...so the bootstrap must have emptied it for every connection the suite could use.
        $this->assertEmpty(config('database.connections.sqlite.url'));
        $this->assertEmpty(config('database.connections.mysql.url'));
    }

    public function test_the_tripwire_recognises_every_unsafe_environment(): void
    {
        $safe = ['driver' => 'sqlite', 'database' => ':memory:', 'url' => null];

        $this->assertNull(TestCase::isolationProblem($safe, 'array'));

        $this->assertStringContainsString('not sqlite', TestCase::isolationProblem(['driver' => 'mysql', 'database' => 'upokul_travels'] + $safe, 'array'));
        $this->assertStringContainsString('not :memory:', TestCase::isolationProblem(['database' => 'database/dev.sqlite'] + $safe, 'array'));
        $this->assertStringContainsString('DB_URL', TestCase::isolationProblem(['url' => 'mysql://root:secret@mysql/upokul_travels'] + $safe, 'array'));
        $this->assertStringContainsString('not array', TestCase::isolationProblem($safe, 'smtp'));
    }

    public function test_every_contact_setting_read_from_the_environment_is_pinned_for_the_tests(): void
    {
        $config = file_get_contents(base_path('config/custom.php'));
        $bootstrap = file_get_contents(base_path('tests/bootstrap.php'));

        preg_match_all("/env\\('((?:CONTACT_|TURNSTILE_|TRUSTED_)[A-Z_]+)'/", $config.file_get_contents(base_path('config/services.php')), $matches);

        $this->assertNotEmpty($matches[1]);

        foreach (array_unique($matches[1]) as $key) {
            $this->assertStringContainsString("'{$key}'", $bootstrap, "{$key} is read from the environment but not pinned in tests/bootstrap.php: a developer's .env could change it and break the tests.");
        }
    }
}
