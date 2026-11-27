<?php

namespace Tests\Feature;

use App\Models\ContentItem;
use App\Models\Role;
use App\Models\User;
use App\Support\Installer;
use App\Support\Publisher;
use App\Support\Settings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class InstallerTest extends TestCase
{
    use RefreshDatabase;

    private string $lock;

    private string $envFile;

    protected function setUp(): void
    {
        parent::setUp();

        $this->lock = sys_get_temp_dir().'/upokul-install-'.uniqid().'.lock';
        $this->envFile = sys_get_temp_dir().'/upokul-install-'.uniqid().'.env';
        file_put_contents($this->envFile, "APP_NAME=\"Old Name\"\nAPP_ENV=local\n");

        // Behave like a brand-new site: not forced to "installed", no lock file, no marker in the database.
        config(['installer.installed' => null, 'installer.lock' => $this->lock, 'installer.env_file' => $this->envFile]);
        app(Installer::class)->forget();
        app(Settings::class)->flush();
    }

    protected function tearDown(): void
    {
        @unlink($this->lock);
        @unlink($this->envFile);
        parent::tearDown();
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'app_name' => 'Sunrise Tours',
            'tagline' => 'Travel better',
            'owner_name' => 'Jane Owner',
            'owner_designation' => 'Founder',
            'owner_email' => 'jane@sunrise.test',
            'owner_phone' => '+8801700000000',
            'office_address' => '1 Beach Road',
            'office_phone' => '+880255555',
            'office_email' => 'office@sunrise.test',
            'whatsapp_number' => '+8801700000000',
            'home_theme' => 'noir',
            'admin_name' => 'Jane Admin',
            'admin_email' => 'admin@sunrise.test',
            'admin_password' => 'Str0ng-Passphrase',
            'admin_password_confirmation' => 'Str0ng-Passphrase',
        ], $overrides);
    }

    public function test_every_page_leads_to_the_wizard_until_the_site_is_installed(): void
    {
        $this->get('/')->assertRedirect(route('install.index'));
        $this->get('/demo1')->assertRedirect(route('install.index'));
        $this->get('/admin')->assertRedirect(route('install.index'));
        $this->get('/install')->assertOk()->assertSee('Master administrator')->assertSee('Server check');
    }

    public function test_the_wizard_creates_the_master_admin_settings_roles_and_content(): void
    {
        $this->post('/install', $this->payload())->assertRedirect(route('home', ['welcome' => app(Settings::class)->get(Installer::WELCOME_KEY)]));

        $admin = User::where('email', 'admin@sunrise.test')->firstOrFail();
        $this->assertTrue($admin->is_master);
        $this->assertNull($admin->role_id, 'the master admin needs no role');
        $this->assertTrue(Hash::check('Str0ng-Passphrase', $admin->password));

        $settings = app(Settings::class);
        $this->assertSame('Sunrise Tours', $settings->get('general.app_name'));
        $this->assertSame('Jane Owner', $settings->get('general.owner_name'));
        $this->assertSame('office@sunrise.test', $settings->get('contact.office_email'));
        $this->assertSame('noir', $settings->get('appearance.home_theme'));
        $this->assertTrue($settings->has('installed_at'));

        $this->assertGreaterThan(0, ContentItem::where('type', 'services')->count());
        $this->assertGreaterThan(0, ContentItem::where('type', 'faqs')->count());
        $this->assertTrue(Role::where('name', 'Content Manager')->exists());
        $this->assertTrue(Role::where('name', 'Support Agent')->exists());

        $this->assertStringContainsString('APP_NAME="Sunrise Tours"', (string) file_get_contents($this->envFile));
        $this->assertStringContainsString('APP_ENV=local', (string) file_get_contents($this->envFile), 'other .env lines are kept');
        $this->assertFileExists($this->lock);
    }

    public function test_the_wizard_disappears_once_installed_so_a_second_master_cannot_be_created(): void
    {
        $this->post('/install', $this->payload())->assertRedirect();

        $this->get('/install')->assertNotFound();
        $this->post('/install', $this->payload(['admin_email' => 'evil@example.com']))->assertNotFound();

        $this->assertSame(1, User::where('is_master', true)->count());
        $this->assertFalse(User::where('email', 'evil@example.com')->exists());
    }

    public function test_the_lock_file_is_recreated_from_the_database_marker(): void
    {
        $this->post('/install', $this->payload())->assertRedirect();
        unlink($this->lock);
        app(Installer::class)->forget();

        $this->get('/install')->assertNotFound();
        $this->assertFileExists($this->lock);
    }

    public function test_the_site_is_never_blank_right_after_installing(): void
    {
        $this->post('/install', $this->payload())->assertRedirect();
        app(Settings::class)->applyToConfig();

        $this->assertNotNull(app(Settings::class)->snapshot(), 'the wizard publishes the first version');

        foreach (['/', '/demo1', '/demo2', '/demo3'] as $url) {
            $this->get($url)->assertOk()->assertSee('Tour Planning')->assertSee('Visa Processing')->assertSee('How early should I plan my trip?')
                ->assertSee('Rahim Ahmed')->assertSee('Sunrise Tours')->assertSee('id="contact"', false);
        }
    }

    public function test_the_installed_site_serves_the_chosen_design_on_the_home_page(): void
    {
        $this->post('/install', $this->payload(['home_theme' => 'glass']))->assertRedirect();

        // Settings are applied to config when the application boots; the redirect after installing
        // is a fresh request in real life, so repeat that boot step here.
        app(Settings::class)->applyToConfig();

        $this->get('/')->assertOk()->assertSee('theme-glass', false)->assertSee('Sunrise Tours');
    }

    /** @return array<string, array{0: array<string, string>, 1: string}> */
    public static function invalid(): array
    {
        return [
            'missing owner' => [['owner_name' => ''], 'owner_name'],
            'bad office e-mail' => [['office_email' => 'nope'], 'office_email'],
            'weak password' => [['admin_password' => 'password', 'admin_password_confirmation' => 'password'], 'admin_password'],
            'mismatched password' => [['admin_password_confirmation' => 'Different-Pass-1'], 'admin_password'],
            'unknown theme' => [['home_theme' => 'neon'], 'home_theme'],
        ];
    }

    /** @dataProvider invalid */
    #[DataProvider('invalid')]
    public function test_the_wizard_validates_its_input(array $override, string $field): void
    {
        $this->post('/install', $this->payload($override))->assertSessionHasErrors($field);

        $this->assertSame(0, User::count());
        $this->assertFileDoesNotExist($this->lock);
    }

    public function test_a_hostile_site_name_cannot_inject_into_the_env_file(): void
    {
        $this->post('/install', $this->payload(['app_name' => "Evil\nAPP_KEY=base64:stolen\"; \$(rm -rf /)"]))->assertRedirect();

        $env = (string) file_get_contents($this->envFile);
        $this->assertStringNotContainsString("\nAPP_KEY=", $env);
        $this->assertSame(1, substr_count($env, 'APP_NAME='));
    }

    public function test_the_installer_shows_the_products_own_brand_not_a_customers(): void
    {
        $html = $this->get('/install')->assertOk()
            ->assertSee(config('product.name'))
            ->assertSee(config('product.tagline'))
            ->assertSee(asset(config('product.logo')), false)
            ->getContent();

        $this->assertSame('Wayfolio', config('product.name'));
        $this->assertFileExists(public_path(config('product.logo')));
        $this->assertStringNotContainsString('Upokul', $html);
        $this->assertStringNotContainsString('images/Logo.png', $html);
        $this->assertStringContainsString('<title>Install · Wayfolio</title>', $html);
        $this->assertStringContainsString('type="file"', $html, 'a logo can be uploaded while installing');
        $this->assertStringContainsString('multipart/form-data', $html);
    }

    public function test_the_wizard_starts_from_a_blank_form_whatever_app_name_is_in_env(): void
    {
        config(['app.name' => 'Leftover Name From .env', 'custom.office_email' => 'leftover@example.com']);

        $this->get('/install')->assertOk()->assertDontSee('Leftover Name From .env')->assertDontSee('leftover@example.com');
    }

    public function test_the_logo_chosen_while_installing_becomes_the_sites_logo(): void
    {
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=');
        $upload = UploadedFile::fake()->createWithContent('my-brand.png', $png);

        $this->post('/install', $this->payload(['logo' => $upload]))->assertRedirect();

        $logo = app(Settings::class)->get('branding.logo');
        $this->assertMatchesRegularExpression('~^uploads/[a-z0-9]{32}\.png$~', $logo);
        $this->assertFileExists(config('installer.upload_path').'/'.basename($logo));

        app(Settings::class)->applyToConfig();
        $this->get('/demo1')->assertSee($logo, false)->assertDontSee('images/Logo.png', false);

        File::deleteDirectory(config('installer.upload_path'));
    }

    public function test_without_an_uploaded_logo_a_neutral_placeholder_is_used_never_another_brands_logo(): void
    {
        $this->post('/install', $this->payload())->assertRedirect();
        app(Settings::class)->applyToConfig();

        $this->get('/demo1')->assertSee('images/default-logo.svg', false)->assertDontSee('images/Logo.png', false)->assertDontSee('Upokul');
        $this->assertFileExists(public_path('images/default-logo.svg'));
    }

    public function test_a_non_image_cannot_be_used_as_the_logo(): void
    {
        $this->post('/install', $this->payload(['logo' => UploadedFile::fake()->createWithContent('logo.php', '<?php echo 1;')]))
            ->assertSessionHasErrors('logo');

        $this->assertSame(0, User::count());
    }

    public function test_a_failed_installation_leaves_no_orphan_logo_upload(): void
    {
        $dir = config('installer.upload_path');
        $files = fn () => is_dir($dir) ? array_values(array_diff(scandir($dir), ['.', '..'])) : [];
        $before = $files(); // other tests may have left files behind (Windows can refuse to delete them)
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=');

        // A duplicate e-mail makes the user insert fail inside the installer, after the logo was stored.
        $existing = new User(['name' => 'Existing', 'email' => 'admin@sunrise.test', 'password' => 'Str0ng-Passphrase']);
        $existing->save();

        // A real file on disk (Laravel's fake upload keeps its handle open, which Windows will not let us delete).
        $real = tempnam(sys_get_temp_dir(), 'logo');
        file_put_contents($real, $png);
        $upload = new UploadedFile($real, 'b.png', 'image/png', null, true);

        $this->post('/install', $this->payload(['logo' => $upload]))->assertSessionHasErrors('install');

        $this->assertSame($before, $files(), 'the logo uploaded by the failed installation was removed');
        $this->assertFileDoesNotExist($this->lock);
    }

    public function test_after_installing_you_land_on_the_website_not_the_login_page(): void
    {
        $response = $this->post('/install', $this->payload());

        $ticket = app(Installer::class)->welcomeTicket();
        $this->assertNotNull($ticket);
        $response->assertRedirect('/?welcome='.$ticket);
    }

    public function test_the_welcome_popup_with_the_admin_link_shows_once_and_never_again(): void
    {
        $this->post('/install', $this->payload())->assertRedirect();
        app(Settings::class)->applyToConfig();
        $ticket = app(Installer::class)->welcomeTicket();

        // First visit after installing: the website (previous standard version) + the popup with the admin link.
        $first = $this->get('/?welcome='.$ticket)->assertOk();
        $first->assertSee('Installation complete!')->assertSee(route('admin.login'), false)->assertSee('id="installWelcome"', false);
        $first->assertSee('Tour Planning')->assertSee('id="services"', false);

        // The ticket is gone: reload, bookmark, copied URL, or the plain home page — no popup.
        $this->assertNull(app(Installer::class)->welcomeTicket());
        $this->get('/?welcome='.$ticket)->assertOk()->assertDontSee('Installation complete!')->assertDontSee('installWelcome', false);
        $this->get('/')->assertOk()->assertDontSee('Installation complete!');
    }

    public function test_a_wrong_or_missing_ticket_never_shows_the_popup_and_does_not_burn_the_real_one(): void
    {
        $this->post('/install', $this->payload())->assertRedirect();
        app(Settings::class)->applyToConfig();
        $ticket = app(Installer::class)->welcomeTicket();

        $this->get('/')->assertDontSee('Installation complete!');
        $this->get('/?welcome=guess')->assertDontSee('Installation complete!');
        $this->get('/?welcome[]=x')->assertOk()->assertDontSee('Installation complete!');
        $this->get('/demo1?welcome='.$ticket)->assertDontSee('Installation complete!'); // only the home page honours it

        $this->assertSame($ticket, app(Installer::class)->welcomeTicket(), 'still available for the real first visit');
    }

    public function test_the_welcome_ticket_does_not_count_as_an_unpublished_change(): void
    {
        $this->post('/install', $this->payload())->assertRedirect();

        $this->assertFalse(app(Publisher::class)->isPending());
        app(Installer::class)->consumeWelcome(app(Installer::class)->welcomeTicket());
        $this->assertFalse(app(Publisher::class)->isPending());
    }
}
