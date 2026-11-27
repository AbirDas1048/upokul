<?php

namespace Tests;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * The installer remembers "installed" in a lock file (tests/bootstrap.php points it at a temp
     * folder). It must never leak from one test into the next, or later tests would stop seeing the
     * shipped default content.
     */
    protected function setUp(): void
    {
        parent::setUp();

        @unlink((string) config('installer.lock'));
    }

    protected function tearDown(): void
    {
        @unlink((string) config('installer.lock'));

        parent::tearDown();
    }

    /**
     * Tripwire. tests/bootstrap.php pins the test environment, but environments leak in creative ways
     * (a stray DB_URL, a new config key...). RefreshDatabase is destructive, so before ANY test can
     * touch a database or send mail, refuse to run unless the application really is isolated.
     */
    public function createApplication(): Application
    {
        $app = parent::createApplication();
        $config = $app['config'];

        $problem = self::isolationProblem(
            (array) $config['database.connections.'.$config['database.default']],
            (string) $config['mail.default'],
        );

        if ($problem !== null) {
            throw new \RuntimeException(
                'Refusing to run the tests: '.$problem.'. Tests must use in-memory SQLite and the array mailer so '
                .'they can never wipe development data or send real e-mail. Check tests/bootstrap.php and your '
                .'environment variables (e.g. DB_URL).'
            );
        }

        return $app;
    }

    /**
     * @param  array<string, mixed>  $database  the default connection's configuration
     * @return string|null why the environment is NOT safe for destructive tests, or null when it is
     */
    public static function isolationProblem(array $database, string $mailer): ?string
    {
        return match (true) {
            ($database['driver'] ?? null) !== 'sqlite' => 'the database driver is "'.($database['driver'] ?? '?').'", not sqlite',
            ($database['database'] ?? null) !== ':memory:' => 'the database is "'.($database['database'] ?? '?').'", not :memory:',
            // Laravel merges a DB_URL over driver/database when it connects, so a set URL defeats the two checks above.
            ! empty($database['url']) => 'a database URL (DB_URL) is set and would override the connection',
            $mailer !== 'array' => 'the mailer is "'.$mailer.'", not array',
            default => null,
        };
    }
}
