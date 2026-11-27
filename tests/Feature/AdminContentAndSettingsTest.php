<?php

namespace Tests\Feature;

use App\Jobs\SendContactMailJob;
use App\Models\ContactMail;
use App\Models\ContentItem;
use App\Services\ContactService;
use App\Support\EnvWriter;
use App\Support\Installer;
use App\Support\Settings;
use App\Support\Site;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Queue;
use Tests\Concerns\InteractsWithAdmin;
use Tests\TestCase;

class AdminContentAndSettingsTest extends TestCase
{
    use InteractsWithAdmin;
    use RefreshDatabase;

    private string $envFile;

    protected function setUp(): void
    {
        parent::setUp();
        $this->installSite();

        $this->envFile = sys_get_temp_dir().'/upokul-settings-'.uniqid().'.env';
        file_put_contents($this->envFile, "APP_NAME=\"Old\"\n");
        config(['installer.env_file' => $this->envFile]);
    }

    protected function tearDown(): void
    {
        @unlink($this->envFile);
        File::deleteDirectory((string) config('installer.upload_path'));
        parent::tearDown();
    }

    private function png(string $name = 'photo.png'): UploadedFile
    {
        // 1×1 transparent PNG — a real image without needing the GD extension.
        return UploadedFile::fake()->createWithContent($name, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII='));
    }

    // ---------------------------------------------------------------- the website reads the database

    public function test_a_fresh_checkout_still_shows_the_shipped_content(): void
    {
        // (no installSite(): the wizard has not run, so the built-in defaults are used)
        DB::table('settings')->delete();
        ContentItem::query()->delete();
        app(Settings::class)->flush();
        app(Installer::class)->forget();
        config(['installer.lock' => sys_get_temp_dir().'/nonexistent-'.uniqid()]);
        app(Site::class)->forget();

        $this->get('/demo1')->assertOk()->assertSee('Tour Planning')->assertSee('How early should I plan my trip?')->assertSee('Rahim Ahmed');
    }

    public function test_content_added_in_the_admin_panel_appears_on_every_design(): void
    {
        $this->actingAs($this->master())->post('/admin/content/services', [
            'title' => 'Cruise Packages', 'icon' => 'fa-ship', 'text' => 'Sail the seven seas.', 'subject' => 'Cruise', 'sort_order' => 1, 'is_active' => 1,
        ])->assertRedirect(route('admin.content.index', 'services'));
        $this->publish();

        foreach (['/demo1', '/demo2', '/demo3'] as $url) {
            $this->get($url)->assertOk()->assertSee('Cruise Packages')->assertSee('fa-ship', false)->assertSee('Sail the seven seas.');
        }
    }

    public function test_hidden_and_deleted_content_disappears_from_the_website(): void
    {
        $item = ContentItem::ofType('faqs')->firstOrFail();
        $admin = $this->actingAs($this->master());
        $this->publish();
        $this->get('/demo1')->assertSee($item->title);

        $admin->put("/admin/content/faqs/{$item->id}", ['title' => $item->title, 'answer' => 'x', 'sort_order' => 1, 'is_active' => 0])->assertRedirect();
        $this->publish();
        $this->get('/demo1')->assertDontSee($item->title);

        $admin->delete("/admin/content/faqs/{$item->id}");
        $this->publish();
        $this->get('/demo1')->assertDontSee($item->title);
    }

    public function test_display_order_is_respected(): void
    {
        ContentItem::ofType('services')->update(['sort_order' => 50]);
        $this->actingAs($this->master())->post('/admin/content/services', ['title' => 'Zzz First Service', 'icon' => 'fa-star', 'text' => 'x', 'sort_order' => 1, 'is_active' => 1]);

        $html = $this->get('/demo1')->getContent();
        $this->assertLessThan(strpos($html, 'Tour Planning'), strpos($html, 'Zzz First Service'));
    }

    public function test_content_text_is_escaped_on_the_public_site(): void
    {
        $this->actingAs($this->master())->post('/admin/content/reviews', [
            'title' => '<script>alert(1)</script>', 'source' => 'google', 'quote' => '<img src=x onerror=alert(2)>', 'sort_order' => 1, 'is_active' => 1,
        ])->assertRedirect();

        $this->publish();

        $this->get('/demo1')->assertDontSee('<script>alert(1)</script>', false)->assertDontSee('<img src=x onerror', false)->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false);
    }

    public function test_content_fields_are_validated(): void
    {
        $this->actingAs($this->master());

        $this->post('/admin/content/services', ['title' => '', 'icon' => 'fa-x', 'text' => 'x'])->assertSessionHasErrors('title');
        $this->post('/admin/content/services', ['title' => 'T', 'icon' => 'fa-solid fa-x" onclick="evil()', 'text' => 'x'])->assertSessionHasErrors('icon');
        $this->post('/admin/content/stats', ['title' => 'T', 'icon' => 'fa-x', 'count' => 'abc'])->assertSessionHasErrors('count');
        $this->post('/admin/content/reviews', ['title' => 'T', 'source' => 'myspace', 'quote' => 'x'])->assertSessionHasErrors('source');
        $this->assertSame(0, ContentItem::where('title', 'T')->count());
    }

    public function test_images_are_uploaded_replaced_and_cleaned_up(): void
    {
        $admin = $this->actingAs($this->master());

        $admin->post('/admin/content/gallery', [
            'title' => 'Sunset', 'category' => 'Beach', 'alt' => 'A sunset', 'image' => $this->png(), 'sort_order' => 1, 'is_active' => 1,
        ])->assertRedirect();

        $item = ContentItem::where('title', 'Sunset')->firstOrFail();
        $first = $item->data['image'];
        $this->assertStringStartsWith('uploads/', $first);
        $this->assertMatchesRegularExpression('~^uploads/[a-z0-9]{32}\.png$~', $first, 'random server-side name, never the client file name');
        $this->assertFileExists(config('installer.upload_path').'/'.basename($first));

        $admin->put("/admin/content/gallery/{$item->id}", ['title' => 'Sunset', 'category' => 'Beach', 'alt' => 'A sunset', 'image' => $this->png('new.png'), 'sort_order' => 1, 'is_active' => 1])->assertRedirect();
        $second = $item->fresh()->data['image'];
        $this->assertNotSame($first, $second);
        $this->assertFileDoesNotExist(config('installer.upload_path').'/'.basename($first));
        $this->assertFileExists(config('installer.upload_path').'/'.basename($second));

        $admin->delete("/admin/content/gallery/{$item->id}");
        $this->assertFileDoesNotExist(config('installer.upload_path').'/'.basename($second));
    }

    public function test_only_images_can_be_uploaded(): void
    {
        $admin = $this->actingAs($this->master());

        $admin->post('/admin/content/gallery', [
            'title' => 'Evil', 'category' => 'x', 'alt' => 'x', 'sort_order' => 1,
            'image' => UploadedFile::fake()->createWithContent('shell.php', '<?php echo "pwned";'),
        ])->assertSessionHasErrors('image');

        $this->assertSame(0, ContentItem::where('title', 'Evil')->count());
    }

    // ---------------------------------------------------------------- page sections

    public function test_section_text_and_switch_are_applied_to_the_website(): void
    {
        $this->actingAs($this->master())->put('/admin/sections/services', [
            'eyebrow' => 'What we do', 'title' => 'Services for', 'highlight' => 'Every Traveller', 'subtitle' => 'Welcome to {app_name}.', 'enabled' => 1,
        ])->assertRedirect();

        $this->publish();

        $this->get('/demo2')->assertOk()->assertSee('What we do')->assertSee('Services for')->assertSee('Every Traveller')
            ->assertSee('Welcome to '.str_replace('&', '&amp;', config('app.name')).'.', false);
    }

    public function test_a_section_can_be_switched_off_and_its_menu_link_goes_with_it(): void
    {
        $this->publish();
        $this->get('/demo1')->assertSee('id="faq"', false)->assertSee('href="#faq"', false);

        $this->actingAs($this->master())->put('/admin/sections/faq', ['eyebrow' => 'FAQ', 'title' => 'Q', 'highlight' => 'A', 'subtitle' => 's', 'enabled' => 0])->assertRedirect();

        $this->publish();

        $this->get('/demo1')->assertOk()->assertDontSee('id="faq"', false)->assertDontSee('href="#faq"', false)->assertSee('id="services"', false);
    }

    public function test_always_on_sections_ignore_the_switch(): void
    {
        $this->actingAs($this->master())->put('/admin/sections/hero', ['title' => 'Hello World', 'highlight' => 'Again'])->assertRedirect();

        $this->publish();

        $this->get('/demo3')->assertSee('Hello World')->assertSee('Again')->assertSee('id="hero"', false);
        $this->assertTrue(app(Settings::class)->bool('section.hero.enabled'));
    }

    public function test_blank_section_fields_fall_back_to_the_default_wording(): void
    {
        $this->actingAs($this->master())->put('/admin/sections/about', ['eyebrow' => '', 'title' => '', 'highlight' => '', 'body' => '', 'tag' => '', 'enabled' => 1])->assertRedirect();

        $this->publish();

        $this->get('/demo1')->assertSee('Your Trusted Partner for')->assertSee('About Us');
    }

    public function test_users_who_may_only_view_sections_cannot_save_them(): void
    {
        $this->actingAs($this->adminWith(['sections.view']));

        $this->get('/admin/sections/services')->assertOk();
        $this->put('/admin/sections/services', ['title' => 'Hacked', 'enabled' => 1])->assertForbidden();
        $this->assertNotSame('Hacked', app(Settings::class)->get('section.services.title'));
    }

    // ---------------------------------------------------------------- appearance

    public function test_the_admin_chooses_which_design_the_home_page_uses(): void
    {
        $this->publish();
        $this->get('/')->assertSee('Choose a design');

        $this->actingAs($this->master());

        foreach (['aurora', 'noir', 'glass'] as $theme) {
            $this->put('/admin/appearance', ['home_theme' => $theme])->assertRedirect();
            $this->publish();
            $this->get('/')->assertOk()->assertSee("theme-{$theme}", false);
        }

        $this->put('/admin/appearance', ['home_theme' => 'chooser'])->assertRedirect();
        $this->publish();
        $this->get('/')->assertSee('Choose a design');
    }

    public function test_an_unknown_design_is_rejected(): void
    {
        $this->actingAs($this->master())->put('/admin/appearance', ['home_theme' => '../../etc/passwd'])->assertSessionHasErrors('home_theme');
    }

    public function test_the_appearance_page_embeds_a_same_origin_preview_that_the_csp_allows(): void
    {
        $response = $this->actingAs($this->master())->get('/admin/appearance')->assertOk()->assertSee('data-preview-root', false)->assertSee(route('demo1'), false);

        $this->assertStringContainsString("frame-src 'self'", $response->headers->get('Content-Security-Policy'));
        $this->assertStringContainsString("frame-ancestors 'self'", $response->headers->get('Content-Security-Policy'));
    }

    // ---------------------------------------------------------------- settings

    public function test_general_settings_rename_the_project_everywhere_and_in_env(): void
    {
        $this->actingAs($this->master())->put('/admin/settings/general', [
            'app_name' => 'Blue Lagoon Trips', 'tagline' => 'Dive in', 'owner_name' => 'Sam Owner', 'owner_designation' => 'CEO', 'owner_email' => 'sam@lagoon.test', 'owner_phone' => '+100',
        ])->assertRedirect();

        // Settings are applied to config when the app boots (a fresh request in production).
        $this->publish();
        app(Settings::class)->applyToConfig();

        $this->assertSame('Blue Lagoon Trips', config('app.name'));
        $this->assertSame('Sam Owner', config('custom.owner_name'));
        $this->assertSame('sam@lagoon.test', config('custom.owner_email'));
        $this->publish();
        $this->get('/demo1')->assertSee('Blue Lagoon Trips')->assertSee('Sam Owner')->assertDontSee('Upokul Travels');
        $this->assertStringContainsString('APP_NAME="Blue Lagoon Trips"', (string) file_get_contents($this->envFile));
    }

    public function test_contact_settings_drive_the_site_links_and_where_enquiries_are_mailed(): void
    {
        $this->actingAs($this->master())->put('/admin/settings/contact', [
            'office_address' => '9 Harbour Road', 'office_phone' => '+880 1 2345', 'office_email' => 'desk@lagoon.test', 'whatsapp_number' => '+880 (1700) 123-456',
            'facebook_page_name' => 'Lagoon FB', 'facebook_page_link' => 'https://facebook.com/lagoon',
            'google_review_url' => 'https://g.page/lagoon', 'facebook_review_url' => 'https://facebook.com/lagoon/reviews', 'map_embed_url' => '',
        ])->assertRedirect();
        $this->publish();
        app(Settings::class)->applyToConfig();

        $this->assertSame('desk@lagoon.test', config('custom.office_email'));
        $this->assertSame('8801700123456', config('custom.whatsapp_digits'));

        $this->publish();

        $this->get('/demo1')->assertSee('9 Harbour Road')->assertSee('https://wa.me/8801700123456', false)->assertSee('https://g.page/lagoon', false)
            ->assertDontSee('google.com/maps/embed', false);
    }

    public function test_the_map_url_must_be_a_google_maps_embed_address(): void
    {
        $base = ['office_email' => 'a@b.test', 'google_review_url' => '', 'facebook_review_url' => ''];

        $this->actingAs($this->master())->put('/admin/settings/contact', $base + ['map_embed_url' => 'https://evil.example/phish'])->assertSessionHasErrors('map_embed_url');
        $this->put('/admin/settings/contact', $base + ['map_embed_url' => 'javascript:alert(1)'])->assertSessionHasErrors('map_embed_url');
        $this->put('/admin/settings/contact', $base + ['map_embed_url' => 'https://www.google.com/maps/embed?pb=abc'])->assertSessionHasNoErrors();
    }

    public function test_the_mail_password_is_stored_encrypted_never_shown_and_kept_when_left_blank(): void
    {
        $admin = $this->actingAs($this->master());
        $form = ['host' => 'smtp.lagoon.test', 'port' => 587, 'username' => 'mailer', 'password' => 'S3cret!pw', 'encryption' => 'tls', 'from_address' => 'no-reply@lagoon.test', 'from_name' => 'Lagoon'];

        Queue::fake(); // saving mail settings restarts queue workers
        $admin->put('/admin/settings/mail', $form)->assertRedirect();

        $raw = DB::table('settings')->where('key', 'mail.password')->value('value');
        $this->assertNotSame('S3cret!pw', $raw);
        $this->assertSame('S3cret!pw', Crypt::decryptString($raw));

        $admin->get('/admin/settings/mail')->assertOk()->assertDontSee('S3cret!pw');

        $admin->put('/admin/settings/mail', ['password' => ''] + $form)->assertRedirect();
        $this->assertSame('S3cret!pw', app(Settings::class)->get('mail.password'));

        app(Settings::class)->applyToConfig();
        $this->assertSame('smtp.lagoon.test', config('mail.mailers.smtp.host'));
        $this->assertSame('S3cret!pw', config('mail.mailers.smtp.password'));
        $this->assertSame('no-reply@lagoon.test', config('mail.from.address'));
    }

    public function test_branding_images_can_be_replaced_and_reset(): void
    {
        $admin = $this->actingAs($this->master());
        $this->assertStringEndsWith('images/default-logo.svg', $this->get('/demo1')->assertOk()->baseResponse->getContent() ? app(Site::class)->image('logo') : '');

        $admin->put('/admin/settings/branding', ['logo' => $this->png('logo.png')])->assertRedirect();
        $custom = app(Settings::class)->get('branding.logo');
        $this->assertStringStartsWith('uploads/', $custom);
        $this->publish();
        $this->get('/demo1')->assertSee($custom, false);

        $admin->put('/admin/settings/branding', ['reset_logo' => 1])->assertRedirect();
        $this->assertSame('images/default-logo.svg', app(Settings::class)->get('branding.logo'));
        $this->assertFileDoesNotExist(config('installer.upload_path').'/'.basename($custom), 'the old upload is removed; shipped images are never deleted');
    }

    public function test_each_settings_group_has_its_own_permission(): void
    {
        $this->actingAs($this->adminWith(['mail.view']));
        $this->get('/admin/settings/mail')->assertOk();
        $this->get('/admin/settings/general')->assertForbidden();
        $this->put('/admin/settings/mail', ['host' => 'x.test'])->assertForbidden();
        $this->post('/admin/settings/mail/test')->assertForbidden();

        $this->actingAs($this->adminWith(['settings.view', 'settings.update'], 'settings@example.com', 'Settings'));
        $this->get('/admin/settings')->assertRedirect(route('admin.settings.edit', 'general'));
        $this->get('/admin/settings/mail')->assertForbidden();
        $this->put('/admin/settings/general', ['app_name' => 'Allowed', 'owner_name' => 'O', 'owner_email' => 'o@x.test'])->assertRedirect();
    }

    public function test_view_only_users_see_settings_read_only_and_cannot_save(): void
    {
        $this->actingAs($this->adminWith(['settings.view']));

        $this->get('/admin/settings/general')->assertOk()->assertSee('Read only')->assertSee('disabled', false);
        $this->put('/admin/settings/general', ['app_name' => 'Hacked', 'owner_name' => 'O', 'owner_email' => 'o@x.test'])->assertForbidden();
    }

    public function test_a_test_email_goes_to_the_signed_in_admin(): void
    {
        $this->actingAs($this->master());

        $this->post('/admin/settings/mail/test')->assertRedirect()->assertSessionHas('status');
        $this->assertCount(1, app('mailer')->getSymfonyTransport()->messages());
    }

    // ---------------------------------------------------------------- env writer

    public function test_env_writer_updates_in_place_adds_missing_keys_and_quotes_values(): void
    {
        file_put_contents($this->envFile, "# comment\nAPP_NAME=Old\nAPP_DEBUG=true\n");

        (new EnvWriter($this->envFile))->set(['APP_NAME' => 'My "Great" Site $1', 'NEW_KEY' => 'plain']);

        $env = (string) file_get_contents($this->envFile);
        $this->assertStringContainsString('# comment', $env);
        $this->assertStringContainsString('APP_DEBUG=true', $env);
        $this->assertStringContainsString('APP_NAME="My \\"Great\\" Site \\$1"', $env);
        $this->assertStringContainsString('NEW_KEY=plain', $env);
        $this->assertSame(1, substr_count($env, 'APP_NAME='));
    }

    public function test_env_writer_rejects_invalid_keys(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new EnvWriter($this->envFile))->set(["BAD KEY\nINJECT" => 'x']);
    }

    // ---------------------------------------------------------------- contact inbox

    private function message(array $attributes = []): ContactMail
    {
        return ContactMail::create($attributes + [
            'name' => 'Visitor One', 'email' => 'visitor@example.com', 'phone' => '+8801700000000', 'subject' => 'Honeymoon in Bali',
            'mail_message' => "Hello,\nWe want to travel in May.", 'status' => ContactService::MAIL_SEND_STATUS_SUCCESS,
        ]);
    }

    public function test_the_inbox_lists_searches_and_filters_messages(): void
    {
        $this->message();
        $this->message(['name' => 'Another Person', 'email' => 'other@example.com', 'subject' => 'Visa help', 'status' => ContactService::MAIL_SEND_STATUS_FAILED]);
        $this->actingAs($this->master());

        $this->get('/admin/contacts')->assertOk()->assertSee('Visitor One')->assertSee('Another Person');
        $this->get('/admin/contacts?q=bali')->assertSee('Visitor One')->assertDontSee('Another Person');
        $this->get('/admin/contacts?filter=failed')->assertSee('Another Person')->assertDontSee('Visitor One');
    }

    public function test_opening_a_message_marks_it_read_and_it_can_be_toggled(): void
    {
        $m = $this->message();
        $this->actingAs($this->master());
        $this->assertNull($m->read_at);

        $this->get("/admin/contacts/{$m->id}")->assertOk()->assertSee('We want to travel in May.');
        $this->assertNotNull($m->fresh()->read_at);

        $this->post("/admin/contacts/{$m->id}/toggle-read");
        $this->assertNull($m->fresh()->read_at);
    }

    public function test_a_view_only_user_can_read_without_marking_it_read_or_deleting(): void
    {
        $m = $this->message();
        $this->actingAs($this->adminWith(['contacts.view']));

        $this->get("/admin/contacts/{$m->id}")->assertOk();
        $this->assertNull($m->fresh()->read_at);
        $this->post("/admin/contacts/{$m->id}/toggle-read")->assertForbidden();
        $this->post("/admin/contacts/{$m->id}/resend")->assertForbidden();
        $this->delete("/admin/contacts/{$m->id}")->assertForbidden();
        $this->assertModelExists($m);
    }

    public function test_a_failed_message_can_be_queued_again(): void
    {
        Queue::fake();
        $m = $this->message(['status' => ContactService::MAIL_SEND_STATUS_FAILED, 'error_message' => 'SMTP down']);

        $this->actingAs($this->master())->post("/admin/contacts/{$m->id}/resend")->assertRedirect();

        Queue::assertPushed(SendContactMailJob::class, fn ($job) => $job->contactMail->is($m));
        $this->assertSame(ContactService::MAIL_SEND_STATUS_PENDING, (int) $m->fresh()->status);
        $this->assertNull($m->fresh()->error_message);
    }

    public function test_a_message_that_is_being_sent_is_not_queued_twice(): void
    {
        Queue::fake();
        $m = $this->message(['status' => ContactService::MAIL_SEND_STATUS_PROCESSING]);

        $this->actingAs($this->master())->post("/admin/contacts/{$m->id}/resend")->assertSessionHas('error');

        Queue::assertNothingPushed();
    }

    public function test_messages_can_be_deleted_with_the_delete_permission(): void
    {
        $m = $this->message();

        $this->actingAs($this->adminWith(['contacts.view', 'contacts.delete']))->delete("/admin/contacts/{$m->id}")->assertRedirect(route('admin.contacts.index'));
        $this->assertModelMissing($m);
    }

    public function test_message_text_is_escaped_in_the_inbox(): void
    {
        $m = $this->message(['mail_message' => '<script>alert("xss")</script>', 'subject' => '<b>bold</b>']);

        $this->actingAs($this->master())->get("/admin/contacts/{$m->id}")->assertDontSee('<script>alert("xss")</script>', false)->assertSee('&lt;script&gt;', false);
        $this->get('/admin/contacts')->assertDontSee('<b>bold</b>', false);
    }

    public function test_the_dashboard_counts_messages_and_respects_permissions(): void
    {
        $this->message();
        $this->message(['email' => 'two@example.com']);

        $this->actingAs($this->master())->get('/admin')->assertOk()->assertSee('Total messages')->assertSee('Visitor One');

        $this->actingAs($this->adminWith(['dashboard.view'], 'plain@example.com', 'Plain'))->get('/admin')->assertOk()->assertDontSee('Total messages')->assertDontSee('Visitor One');
    }
}
