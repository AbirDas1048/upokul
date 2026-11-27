<?php

namespace Tests\Feature;

use App\Models\ContentItem;
use App\Support\Publisher;
use App\Support\Settings;
use App\Support\ViteDevServer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithAdmin;
use Tests\TestCase;

class AdminPublishTest extends TestCase
{
    use InteractsWithAdmin;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->installSite();
    }

    private function edit(): void
    {
        $admin = $this->actingAs($this->master());
        $admin->post('/admin/content/services', ['title' => 'Cruise Packages', 'icon' => 'fa-ship', 'text' => 'Sail away.', 'sort_order' => 1, 'is_active' => 1]);
        $admin->put('/admin/sections/services', ['eyebrow' => 'Draft eyebrow', 'title' => 'Draft title', 'highlight' => 'Draft highlight', 'subtitle' => 's', 'enabled' => 1]);
        $admin->put('/admin/appearance', ['home_theme' => 'glass']);
        $admin->put('/admin/sections/faq', ['eyebrow' => 'FAQ', 'title' => 'Q', 'highlight' => 'A', 'subtitle' => 's', 'enabled' => 0]);
        auth()->logout();
    }

    public function test_the_live_site_keeps_the_previous_version_until_the_changes_are_published(): void
    {
        $this->edit();

        $page = $this->get('/demo1')->assertOk();
        $page->assertDontSee('Cruise Packages')->assertDontSee('Draft title')->assertSee('Tour Planning')->assertSee('Everything You Need for a')->assertSee('id="faq"', false);
        $this->get('/')->assertSee('Choose a design'); // the design change is still a draft too
    }

    public function test_publishing_makes_every_pending_change_live_at_once(): void
    {
        $this->edit();
        $this->actingAs($this->master())->post('/admin/publish')->assertRedirect()->assertSessionHas('status');
        auth()->logout();

        $this->get('/demo1')->assertSee('Cruise Packages')->assertSee('Draft title')->assertSee('Draft eyebrow')->assertDontSee('id="faq"', false);
        $this->get('/')->assertSee('theme-glass', false);
    }

    public function test_a_signed_in_admin_can_preview_the_draft_before_publishing(): void
    {
        $this->edit();
        $this->actingAs($this->master());

        $draft = $this->get('/demo1?preview=draft')->assertOk();
        $draft->assertSee('Cruise Packages')->assertSee('Draft title')->assertDontSee('id="faq"', false)->assertSee('Draft preview — not published yet');
        $draft->assertHeader('Cache-Control', 'no-store, private');

        $this->get('/?preview=draft')->assertSee('theme-glass', false);
        $this->get('/demo1')->assertDontSee('Cruise Packages')->assertDontSee('Draft preview', false); // same session, no ?preview => live
    }

    public function test_visitors_can_never_see_the_draft_whatever_the_query_string_says(): void
    {
        $this->edit();

        $this->get('/demo1?preview=draft')->assertOk()->assertDontSee('Cruise Packages')->assertDontSee('Draft preview', false);
    }

    public function test_a_deactivated_admin_gets_no_draft_preview(): void
    {
        $this->edit();
        $user = $this->adminWith(['dashboard.view']);
        $user->forceFill(['is_active' => false])->save();

        $this->actingAs($user)->get('/demo1?preview=draft')->assertDontSee('Cruise Packages');
    }

    public function test_the_publish_button_only_appears_when_there_is_something_to_publish(): void
    {
        $master = $this->master();
        $this->actingAs($master);
        $this->assertFalse(app(Publisher::class)->isPending());
        $this->get('/admin')->assertDontSee('Publish changes')->assertSee('Live site up to date');

        $this->post('/admin/content/services', ['title' => 'Draft only', 'icon' => 'fa-ship', 'text' => 'x', 'sort_order' => 1, 'is_active' => 1]);

        $this->assertTrue(app(Publisher::class)->isPending());
        $this->get('/admin')->assertSee('Publish changes')->assertSee('You have unpublished changes');

        $this->post('/admin/publish');
        $this->assertFalse(app(Publisher::class)->isPending());
    }

    public function test_saved_messages_tell_the_admin_it_is_only_a_draft(): void
    {
        $this->actingAs($this->master())
            ->followingRedirects()
            ->post('/admin/content/services', ['title' => 'Draft only', 'icon' => 'fa-ship', 'text' => 'x', 'sort_order' => 1, 'is_active' => 1])
            ->assertSee('saved as a draft');
    }

    public function test_publishing_needs_its_own_permission(): void
    {
        $editor = $this->adminWith(['services.view', 'services.create', 'dashboard.view']);
        $this->actingAs($editor);
        $this->post('/admin/content/services', ['title' => 'Draft only', 'icon' => 'fa-ship', 'text' => 'x', 'sort_order' => 1, 'is_active' => 1])->assertRedirect();

        $this->post('/admin/publish')->assertForbidden();
        $this->get('/admin')->assertDontSee('Publish changes');
        $this->get('/demo1')->assertDontSee('Draft only');

        $publisher = $this->adminWith(['publish.update'], 'publisher@example.com', 'Publisher');
        $this->actingAs($publisher)->post('/admin/publish')->assertRedirect();
        $this->get('/demo1')->assertSee('Draft only');
    }

    public function test_hidden_items_and_mail_settings_are_handled_correctly(): void
    {
        $item = ContentItem::ofType('faqs')->firstOrFail();
        $admin = $this->actingAs($this->master());

        $admin->put("/admin/content/faqs/{$item->id}", ['title' => $item->title, 'answer' => 'x', 'sort_order' => 1, 'is_active' => 0]);
        $this->get('/demo1')->assertSee($item->title); // still live until published

        $snapshot = app(Settings::class)->snapshot();
        $this->assertArrayNotHasKey('mail.password', $snapshot['settings'], 'secrets are never copied into the snapshot');
        $this->assertSame([], array_filter(array_keys($snapshot['settings']), fn ($k) => str_starts_with($k, 'mail.')));
    }

    public function test_the_installer_and_login_pages_style_themselves_without_the_vite_build(): void
    {
        foreach (['css/bootstrap.min.css', 'css/admin-auth.css', 'vendor/fontawesome/all.min.css', 'vendor/fontawesome/webfonts/fa-solid-900.woff2'] as $file) {
            $this->assertFileExists(public_path($file));
        }

        // Even when public/hot points at a dev server that is not running, the page keeps its styling.
        $hot = public_path('hot');
        $hadHot = is_file($hot);
        $previous = $hadHot ? file_get_contents($hot) : null;
        file_put_contents($hot, 'http://127.0.0.1:59999');

        try {
            $this->get('/admin/login')->assertOk()
                ->assertSee(asset('css/bootstrap.min.css'), false)
                ->assertSee(asset('css/admin-auth.css'), false);
        } finally {
            $hadHot ? file_put_contents($hot, $previous) : unlink($hot);
        }
    }

    public function test_the_admin_panel_keeps_its_design_when_public_hot_is_stale(): void
    {
        foreach (['css/bootstrap.min.css', 'css/admin.css', 'vendor/fontawesome/all.min.css'] as $file) {
            $this->assertFileExists(public_path($file));
        }

        $hot = public_path('hot');
        $hadHot = is_file($hot);
        $previous = $hadHot ? file_get_contents($hot) : null;
        file_put_contents($hot, 'http://127.0.0.1:59999'); // nothing listens there
        ViteDevServer::reset();

        try {
            $this->assertTrue(ViteDevServer::isStale());

            $page = $this->actingAs($this->master())->get('/admin')->assertOk();
            $page->assertSee(asset('css/admin.css'), false)->assertSee(asset('css/bootstrap.min.css'), false);
            $this->assertStringNotContainsString('127.0.0.1:59999', $page->getContent(), 'no asset may point at the dead dev server');
            $this->assertStringNotContainsString('59999', $page->headers->get('Content-Security-Policy'), 'a dead dev server must not widen the CSP');

            // The public designs fall back to the built assets as well.
            $this->get('/demo1')->assertOk();
            $this->assertStringNotContainsString('59999', $this->get('/demo2')->getContent());
        } finally {
            $hadHot ? file_put_contents($hot, $previous) : @unlink($hot);
            ViteDevServer::reset();
        }
    }
}
