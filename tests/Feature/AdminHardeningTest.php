<?php

namespace Tests\Feature;

use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\MiddlewareNameResolver;
use Illuminate\Support\Facades\Hash;
use Tests\Concerns\InteractsWithAdmin;
use Tests\TestCase;

class AdminHardeningTest extends TestCase
{
    use InteractsWithAdmin;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->installSite();
    }

    public function test_repeated_wrong_passwords_are_throttled(): void
    {
        $user = $this->master();

        for ($i = 0; $i < 5; $i++) {
            $this->post('/admin/login', ['email' => $user->email, 'password' => 'wrong-'.$i])->assertSessionHasErrors('email');
        }

        // The sixth attempt is refused even with the right password.
        $this->post('/admin/login', ['email' => $user->email, 'password' => 'Secret-pass-123'])->assertStatus(429);
        $this->assertGuest();
    }

    public function test_the_login_page_is_not_a_user_enumeration_oracle(): void
    {
        $this->master();

        $unknown = $this->post('/admin/login', ['email' => 'nobody@example.com', 'password' => 'whatever'])->assertSessionHasErrors('email');
        $wrong = $this->post('/admin/login', ['email' => 'master@example.com', 'password' => 'whatever'])->assertSessionHasErrors('email');

        $this->assertSame(session('errors')->first('email'), $unknown->baseResponse->getSession()->get('errors')->first('email'));
        $this->assertSame('These credentials do not match our records.', $wrong->baseResponse->getSession()->get('errors')->first('email'));
    }

    public function test_the_session_id_changes_on_login(): void
    {
        $user = $this->master();
        $this->get('/admin/login');
        $before = session()->getId();

        $this->post('/admin/login', ['email' => $user->email, 'password' => 'Secret-pass-123']);

        $this->assertNotSame($before, session()->getId());
    }

    public function test_every_state_changing_admin_route_is_csrf_protected_and_authenticated(): void
    {
        // Laravel skips CSRF verification while testing, so check the middleware stack of each route instead.
        $this->get('/admin/login'); // boots the HTTP kernel, which registers the middleware groups
        $router = app('router');
        $checked = 0;

        foreach ($router->getRoutes() as $route) {
            if (! str_starts_with($route->getName() ?? '', 'admin.') || array_intersect($route->methods(), ['POST', 'PUT', 'PATCH', 'DELETE']) === []) {
                continue;
            }

            // Expand middleware groups ("web" => session, CSRF, ...) the way the router does at dispatch time.
            $stack = collect($route->gatherMiddleware())
                ->flatMap(fn ($name) => (array) MiddlewareNameResolver::resolve($name, $router->getMiddleware(), $router->getMiddlewareGroups()))
                ->all();
            $this->assertContains(ValidateCsrfToken::class, $stack, $route->getName().' lacks CSRF protection');

            if (! in_array($route->getName(), ['admin.login.submit'], true)) {
                $this->assertContains('auth', $route->gatherMiddleware(), $route->getName().' is reachable without signing in');
            }

            $checked++;
        }

        $this->assertGreaterThanOrEqual(20, $checked);
    }

    public function test_every_authenticated_admin_page_needs_a_permission_or_checks_one_itself(): void
    {
        // Pages guarded in the controller (dynamic content types, profile) are listed explicitly.
        $selfChecked = ['admin.logout', 'admin.profile.edit', 'admin.profile.update', 'admin.settings.edit', 'admin.settings.update'];
        $dynamic = fn (string $name) => str_starts_with($name, 'admin.content.');

        foreach (app('router')->getRoutes() as $route) {
            $name = $route->getName() ?? '';

            if (! str_starts_with($name, 'admin.') || in_array($name, ['admin.login', 'admin.login.submit'], true) || in_array($name, $selfChecked, true) || $dynamic($name)) {
                continue;
            }

            $guards = array_filter($route->gatherMiddleware(), fn ($m) => is_string($m) && str_starts_with($m, 'can:'));
            $controllerGuards = $route->getController() instanceof HasMiddleware;

            $this->assertTrue($guards !== [] || $controllerGuards, "{$name} has no permission check");
        }
    }

    public function test_the_reset_command_recovers_the_master_account(): void
    {
        $master = $this->master();
        $master->forceFill(['is_active' => false])->save();

        $this->artisan('admin:reset-password', ['--password' => 'Recovered-Pass-42'])
            ->expectsOutputToContain('Password updated for master@example.com')
            ->assertSuccessful();

        $fresh = $master->fresh();
        $this->assertTrue(Hash::check('Recovered-Pass-42', $fresh->password));
        $this->assertTrue($fresh->is_active);
    }

    public function test_the_reset_command_refuses_weak_passwords_and_unknown_users(): void
    {
        $this->master();

        $this->artisan('admin:reset-password', ['--password' => 'weak'])->assertFailed();
        $this->artisan('admin:reset-password', ['email' => 'ghost@example.com', '--password' => 'Recovered-Pass-42'])
            ->expectsOutputToContain('No admin user')->assertFailed();
    }
}
