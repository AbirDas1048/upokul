<?php

namespace Tests\Feature;

use App\Http\Middleware\SecurityHeaders;
use App\Jobs\SendContactMailJob;
use App\Mail\ContactMailMailable;
use App\Models\ContactMail;
use App\Providers\AppServiceProvider;
use App\Services\ContactSpamGuard;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Abuse / injection / hardening checks for the public contact endpoint and the page headers.
 */
class ContactSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // CSRF itself is covered separately below; these tests focus on the other layers.
        $this->withoutMiddleware(ValidateCsrfToken::class);
        Queue::fake();
    }

    /** A submission shaped like the one a real browser sends (token issued 10s ago, honeypot empty). */
    private function payload(array $overrides = []): array
    {
        return $overrides + [
            'name' => 'Demo User',
            'email' => 'demo@example.com',
            'phone' => '+880 1700-000000',
            'subject' => 'Tour enquiry',
            'mail_message' => 'I would like a quote for Cox\'s Bazar.',
            ContactSpamGuard::TOKEN_FIELD => ContactSpamGuard::issueToken(now()->subSeconds(10)),
            ContactSpamGuard::HONEYPOT_FIELD => '',
        ];
    }

    private function submit(array $payload)
    {
        return $this->postJson(route('contact.submit'), $payload, ['X-Requested-With' => 'XMLHttpRequest']);
    }

    // --------------------------------------------------------------- bots

    public function test_honeypot_filled_looks_successful_but_stores_and_sends_nothing(): void
    {
        $this->submit($this->payload([ContactSpamGuard::HONEYPOT_FIELD => 'http://spam.example']))
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseCount('contact_mails', 0);
        Queue::assertNothingPushed();
    }

    public function test_missing_token_is_rejected(): void
    {
        $payload = $this->payload();
        unset($payload[ContactSpamGuard::TOKEN_FIELD]);

        $this->submit($payload)->assertOk()->assertJson(['success' => false]);

        $this->assertDatabaseCount('contact_mails', 0);
        Queue::assertNothingPushed();
    }

    public function test_forged_or_tampered_token_is_rejected(): void
    {
        $valid = ContactSpamGuard::issueToken(now()->subSeconds(10));
        $flipped = substr_replace($valid, $valid[40] === 'A' ? 'B' : 'A', 40, 1); // breaks the MAC

        foreach (['not-a-real-token', $flipped, base64_encode('1700000000')] as $bad) {
            $this->submit($this->payload([ContactSpamGuard::TOKEN_FIELD => $bad]))
                ->assertOk()
                ->assertJson(['success' => false]);
        }

        $this->assertDatabaseCount('contact_mails', 0);
    }

    public function test_form_submitted_faster_than_a_human_can_type_is_rejected(): void
    {
        $this->submit($this->payload([ContactSpamGuard::TOKEN_FIELD => ContactSpamGuard::issueToken(now())]))
            ->assertOk()
            ->assertJson(['success' => false])
            ->assertJsonPath('data.form.0', 'Please take a moment to complete the form, then try again.');

        $this->assertDatabaseCount('contact_mails', 0);
    }

    public function test_expired_token_asks_the_visitor_to_reload(): void
    {
        $old = ContactSpamGuard::issueToken(now()->subHours(7));

        $this->submit($this->payload([ContactSpamGuard::TOKEN_FIELD => $old]))
            ->assertOk()
            ->assertJson(['success' => false])
            ->assertJsonPath('data.form.0', 'Your session has expired. Please refresh the page and try again.');
    }

    public function test_same_message_twice_is_accepted_once(): void
    {
        $first = $this->submit($this->payload());
        $second = $this->submit($this->payload());

        $first->assertOk()->assertJson(['success' => true]);
        $second->assertOk()->assertJson(['success' => true]); // double-click looks fine to the person

        $this->assertDatabaseCount('contact_mails', 1);
        Queue::assertPushed(SendContactMailJob::class, 1);
    }

    public function test_duplicate_detection_ignores_case_and_whitespace_tricks(): void
    {
        $this->submit($this->payload(['email' => 'Demo@Example.com', 'mail_message' => "Quote   please\n"]))->assertJson(['success' => true]);
        $this->submit($this->payload(['email' => 'demo@example.com', 'mail_message' => 'quote please']))->assertJson(['success' => true]);
        $this->submit($this->payload(['email' => 'demo@example.com', 'mail_message' => 'Quote please']))->assertJson(['success' => true]);

        $this->assertDatabaseCount('contact_mails', 2); // "Quote please" differs from "quote please" only by case
    }

    public function test_per_minute_ip_limit_returns_429_with_retry_after(): void
    {
        config(['custom.contact.per_email_hour' => 100]);

        foreach (range(1, 3) as $i) {
            $this->submit($this->payload(['email' => "user{$i}@example.com", 'mail_message' => "Enquiry number {$i}"]))
                ->assertOk();
        }

        $this->submit($this->payload(['email' => 'user4@example.com', 'mail_message' => 'Enquiry number 4']))
            ->assertStatus(429)
            ->assertJson(['success' => false])
            ->assertJsonPath('data.form.0', 'You are sending messages too quickly. Please wait a few minutes and try again.')
            ->assertHeader('Retry-After');

        $this->assertDatabaseCount('contact_mails', 3);
    }

    public function test_one_address_cannot_be_mail_bombed_from_many_requests(): void
    {
        config(['custom.contact.per_minute' => 100, 'custom.contact.per_hour' => 100]);

        foreach (range(1, 3) as $i) {
            $this->submit($this->payload(['email' => 'victim@example.com', 'mail_message' => "Different text {$i}"]))->assertOk();
        }

        $this->submit($this->payload(['email' => 'victim@example.com', 'mail_message' => 'Different text 4']))
            ->assertStatus(429);

        $this->assertDatabaseCount('contact_mails', 3);
    }

    public function test_site_wide_flood_cap_blocks_even_new_visitors(): void
    {
        config([
            'custom.contact.per_minute' => 100,
            'custom.contact.per_hour' => 100,
            'custom.contact.per_email_hour' => 100,
            'custom.contact.global_per_hour' => 2,
        ]);

        $this->submit($this->payload(['email' => 'a@example.com', 'mail_message' => 'one']))->assertOk();
        $this->submit($this->payload(['email' => 'b@example.com', 'mail_message' => 'two']))->assertOk();
        $this->submit($this->payload(['email' => 'c@example.com', 'mail_message' => 'three']))->assertStatus(429);
    }

    // --------------------------------------------------------------- optional Turnstile

    public function test_turnstile_when_enabled_requires_and_verifies_the_challenge(): void
    {
        config(['services.turnstile.site_key' => 'site', 'services.turnstile.secret' => 'secret']);

        // no challenge response at all
        $this->submit($this->payload())->assertJson(['success' => false]);

        Http::fakeSequence('challenges.cloudflare.com/*')
            ->push(['success' => false])   // 1st verification: Cloudflare says "not human"
            ->push(['success' => true]);   // 2nd verification: passes

        $this->submit($this->payload([ContactSpamGuard::TURNSTILE_FIELD => 'bad']))->assertJson(['success' => false]);
        $this->submit($this->payload([ContactSpamGuard::TURNSTILE_FIELD => 'good']))->assertJson(['success' => true]);

        $this->assertDatabaseCount('contact_mails', 1);
    }

    public function test_turnstile_fails_closed_when_cloudflare_is_unreachable(): void
    {
        config(['services.turnstile.site_key' => 'site', 'services.turnstile.secret' => 'secret']);
        Http::fake(fn () => throw new ConnectionException('timeout'));

        $this->submit($this->payload([ContactSpamGuard::TURNSTILE_FIELD => 'whatever']))->assertJson(['success' => false]);

        $this->assertDatabaseCount('contact_mails', 0);
    }

    // --------------------------------------------------------------- injection

    public function test_sql_injection_payloads_are_stored_as_plain_text_and_harm_nothing(): void
    {
        $evil = "Robert'); DROP TABLE contact_mails;--";

        $this->submit($this->payload(['name' => $evil, 'mail_message' => "' OR 1=1; DELETE FROM contact_mails; --"]))
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertTrue(Schema::hasTable('contact_mails'));
        $this->assertDatabaseHas('contact_mails', ['name' => $evil]); // bound as a value, never executed
        $this->assertDatabaseCount('contact_mails', 1);

        // ...and as an e-mail it is simply not a valid address
        $this->submit($this->payload(['email' => "x@example.com' OR '1'='1"]))->assertJsonPath('data.email.0', 'Please provide a valid email address.');
    }

    public function test_header_injection_attempts_lose_their_line_breaks(): void
    {
        $this->submit($this->payload([
            'name' => "Evil\r\nBcc: victim@example.com",
            'subject' => "Hi\r\nSubject: spoofed\nX-Header: 1",
            'email' => "demo@example.com\r\nBcc: victim@example.com",
        ]))->assertOk();

        // The address with CR/LF is not a valid e-mail at all -> nothing saved
        $this->assertDatabaseCount('contact_mails', 0);

        $this->submit($this->payload([
            'name' => "Evil\r\nBcc: victim@example.com",
            'subject' => "Hi\r\nSubject: spoofed\nX-Header: 1",
        ]))->assertJson(['success' => true]);

        $row = ContactMail::firstOrFail();
        foreach (['name', 'subject', 'email'] as $field) {
            $this->assertStringNotContainsString("\r", $row->{$field});
            $this->assertStringNotContainsString("\n", $row->{$field});
        }
    }

    public function test_html_in_a_message_is_escaped_in_the_e_mail_that_the_owner_opens(): void
    {
        $this->submit($this->payload(['mail_message' => '<script>alert(document.cookie)</script><img src=x onerror=alert(1)>']))
            ->assertJson(['success' => true]);

        $html = (new ContactMailMailable(ContactMail::firstOrFail()))->render();

        $this->assertStringNotContainsString('<script>alert(document.cookie)</script>', $html);
        $this->assertStringNotContainsString('<img src=x onerror', $html);
        $this->assertStringContainsString('&lt;script&gt;', $html);
    }

    public function test_links_are_limited_because_spam_is_mostly_links(): void
    {
        $this->submit($this->payload(['mail_message' => 'see http://a.example and https://b.example']))
            ->assertJsonPath('data.mail_message.0', 'Please remove the extra links from your message.');

        $this->submit($this->payload(['name' => 'Visit www.spam.example']))
            ->assertJsonPath('data.name.0', 'Links are not allowed in this field.');

        $this->submit($this->payload(['mail_message' => 'hotel idea: https://example.com/hotel']))
            ->assertJson(['success' => true]); // one link is fine

        $this->assertDatabaseCount('contact_mails', 1);
    }

    public function test_phone_must_look_like_a_phone_number(): void
    {
        $this->submit($this->payload(['phone' => 'call me <b>now</b>']))->assertJsonPath('data.phone.0', fn ($m) => str_contains($m, 'valid phone number'));
        $this->submit($this->payload(['phone' => '+880 (1700) 000-000']))->assertJson(['success' => true]);
    }

    public function test_only_validated_fields_are_used_and_echoed(): void
    {
        $response = $this->submit($this->payload(['is_admin' => true, 'status' => 1, 'sent_at' => '2020-01-01', 'id' => 999]))
            ->assertOk();

        $response->assertJsonMissingPath('data.is_admin')
            ->assertJsonMissingPath('data.status')
            ->assertJsonMissingPath('data.form_token')
            ->assertJsonMissingPath('data.id');

        $row = ContactMail::firstOrFail();
        $this->assertSame(0, (int) $row->status);       // not the attacker's 1
        $this->assertNull($row->sent_at);
        $this->assertNotSame(999, $row->id);
    }

    public function test_array_and_oversized_inputs_are_rejected_not_crashing(): void
    {
        $this->submit($this->payload(['name' => ['a', 'b'], 'mail_message' => str_repeat('x', 5000)]))
            ->assertOk()
            ->assertJson(['success' => false]);

        $this->assertDatabaseCount('contact_mails', 0);
    }

    // --------------------------------------------------------------- CSRF

    public function test_contact_route_is_protected_by_csrf_security_headers_and_the_rate_limiter(): void
    {
        // (Laravel deliberately skips the CSRF check while tests run, so verify it is in the stack.)
        $this->get('/demo1'); // the kernel registers the middleware groups on the router when it handles a request

        $router = app('router');
        $route = $router->getRoutes()->getByName('contact.submit');
        $stack = $router->gatherRouteMiddleware($route); // groups are expanded once the kernel has run

        // setUp() disables CSRF for the behaviour tests, so read the *default* web group from the kernel.
        $webGroup = app(Kernel::class)->getMiddlewareGroups()['web'];

        $this->assertContains(ValidateCsrfToken::class, $webGroup); // CSRF token is mandatory for POST
        $this->assertContains(SecurityHeaders::class, $stack);      // CSP & friends on every response
        $this->assertContains('throttle:contact', $route->middleware()); // request-rate limiting (declared on the route)
        $this->assertSame(['POST'], $route->methods());
    }

    // --------------------------------------------------------------- headers

    public function test_pages_send_security_headers_and_a_nonce_based_csp(): void
    {
        $response = $this->get('/demo1')->assertOk();

        $csp = $response->headers->get('Content-Security-Policy');

        $this->assertStringContainsString("default-src 'self'", $csp);
        $this->assertStringContainsString("object-src 'none'", $csp);
        $this->assertStringContainsString("frame-ancestors 'self'", $csp);
        $this->assertStringContainsString("form-action 'self'", $csp);

        // script-src must NOT allow arbitrary inline code
        preg_match('/script-src ([^;]+)/', $csp, $m);
        $this->assertStringNotContainsString("'unsafe-inline'", $m[1]);
        $this->assertStringNotContainsString("'unsafe-eval'", $m[1]);

        // the one inline script on the page carries the same nonce the header announces
        preg_match("/'nonce-([^']+)'/", $csp, $n);
        $this->assertNotEmpty($n[1]);
        $response->assertSee('nonce="'.$n[1].'"', false);

        $this->assertSame('nosniff', $response->headers->get('X-Content-Type-Options'));
        $this->assertSame('SAMEORIGIN', $response->headers->get('X-Frame-Options'));
        $this->assertSame('strict-origin-when-cross-origin', $response->headers->get('Referrer-Policy'));
        $this->assertStringContainsString('camera=()', $response->headers->get('Permissions-Policy'));
        $this->assertNull($response->headers->get('Strict-Transport-Security')); // plain http
    }

    public function test_each_response_gets_a_fresh_nonce_and_https_gets_hsts(): void
    {
        $a = $this->get('/demo2')->headers->get('Content-Security-Policy');
        $b = $this->get('/demo3')->headers->get('Content-Security-Policy');
        $this->assertNotSame($a, $b);

        $https = $this->get('https://localhost/demo1');
        $this->assertStringContainsString('max-age=31536000', $https->headers->get('Strict-Transport-Security'));
        $this->assertStringContainsString('upgrade-insecure-requests', $https->headers->get('Content-Security-Policy'));
    }

    public function test_form_renders_the_signed_token_and_an_empty_honeypot(): void
    {
        $this->get('/demo1')
            ->assertOk()
            ->assertSee('name="form_token"', false)
            ->assertSee('name="contact_extra"', false)
            ->assertDontSee('cf-turnstile'); // only when keys are configured
    }

    // --------------------------------------------------------------- review follow-ups

    public function test_array_valued_email_is_rejected_by_validation_not_a_server_error(): void
    {
        // email[]=... used to blow up inside the rate limiter (array-to-string) before validation ran.
        $this->submit($this->payload(['email' => ['victim@example.com']]))
            ->assertOk()
            ->assertJson(['success' => false])
            ->assertJsonStructure(['data' => ['email']]);

        $this->assertDatabaseCount('contact_mails', 0);
    }

    public function test_control_character_padding_cannot_dodge_the_per_email_limit(): void
    {
        config(['custom.contact.per_minute' => 100, 'custom.contact.per_hour' => 100]);

        // 300 control characters in front: the old limiter skipped the e-mail bucket (too long),
        // while the service cleaned the value back to victim@example.com and accepted it.
        $padded = str_repeat("\x01", 300).'victim@example.com';

        foreach (range(1, 3) as $i) {
            $this->submit($this->payload(['email' => $padded, 'mail_message' => "padded message {$i}"]))->assertOk();
        }

        $this->submit($this->payload(['email' => 'Victim@Example.com', 'mail_message' => 'plain message 4']))
            ->assertStatus(429);

        $this->assertDatabaseCount('contact_mails', 3);
    }

    public function test_duplicate_reservation_is_released_when_saving_fails(): void
    {
        $failInsert = true;
        ContactMail::creating(function () use (&$failInsert) {
            if ($failInsert) {
                throw new \RuntimeException('database unavailable');
            }
        });

        $this->submit($this->payload())->assertStatus(500);
        $this->assertDatabaseCount('contact_mails', 0);

        // The identical, honest retry must NOT be mistaken for a duplicate of something never stored.
        $failInsert = false;
        $this->submit($this->payload())->assertOk()->assertJson(['success' => true]);

        $this->assertDatabaseCount('contact_mails', 1);
        Queue::assertPushed(SendContactMailJob::class, 1);
    }

    public function test_trusted_proxies_come_from_config_so_they_survive_config_cache(): void
    {
        Route::get('/__client-ip', fn (Request $request) => $request->ip());

        try {
            config(['custom.trusted_proxies' => '10.0.0.0/8']);
            (new AppServiceProvider($this->app))->boot();

            // From a trusted proxy the forwarded client address is believed...
            $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.5'])
                ->withHeaders(['X-Forwarded-For' => '203.0.113.7'])
                ->get('/__client-ip')
                ->assertSee('203.0.113.7');

            // ...but anyone else cannot spoof their address with the same header.
            $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.9'])
                ->withHeaders(['X-Forwarded-For' => '203.0.113.7'])
                ->get('/__client-ip')
                ->assertSee('198.51.100.9');
        } finally {
            TrustProxies::flushState();
        }
    }

    public function test_failure_to_queue_rolls_the_row_back_so_the_visitors_retry_is_processed(): void
    {
        $realDispatcher = $this->app->make(Dispatcher::class);
        $this->instance(Dispatcher::class, \Mockery::mock(Dispatcher::class, function ($mock) {
            $mock->shouldReceive('dispatch')->andThrow(new \RuntimeException('queue unavailable'));
        }));

        $this->submit($this->payload())->assertStatus(500);
        $this->assertDatabaseCount('contact_mails', 0); // nothing left Pending without a job

        // The queue is back. The identical retry must go through, not be answered as a "duplicate".
        $this->instance(Dispatcher::class, $realDispatcher);
        $this->submit($this->payload())->assertOk()->assertJson(['success' => true]);

        $this->assertDatabaseCount('contact_mails', 1);
        Queue::assertPushed(SendContactMailJob::class, 1);
    }

    public function test_database_queue_dispatches_inside_the_transaction_and_other_queues_only_after_commit(): void
    {
        $base = DB::transactionLevel(); // RefreshDatabase already wraps every test in a transaction

        $levels = [];
        $realDispatcher = $this->app->make(Dispatcher::class);
        $this->instance(Dispatcher::class, \Mockery::mock(Dispatcher::class, function ($mock) use (&$levels) {
            $mock->shouldReceive('dispatch')->andReturnUsing(function () use (&$levels) {
                $levels[] = DB::transactionLevel();

                return 0;
            });
        }));

        // `database` queue: the job row shares the database, so it is written in the same transaction.
        config(['queue.default' => 'database']);
        $this->submit($this->payload(['mail_message' => 'via the database queue']))->assertOk();

        // `redis` (any external queue): a worker must never see the job before the contact has committed.
        config(['queue.default' => 'redis']);
        $this->submit($this->payload(['mail_message' => 'via an external queue']))->assertOk();

        $this->assertSame([$base + 1, $base], $levels);
        $this->assertDatabaseCount('contact_mails', 2);
        $this->instance(Dispatcher::class, $realDispatcher);
    }

    public function test_external_queue_failure_removes_the_unqueued_contact_so_the_retry_works(): void
    {
        config(['queue.default' => 'redis']);

        $realDispatcher = $this->app->make(Dispatcher::class);
        $this->instance(Dispatcher::class, \Mockery::mock(Dispatcher::class, function ($mock) {
            $mock->shouldReceive('dispatch')->andThrow(new \RuntimeException('redis down'));
        }));

        $this->submit($this->payload())->assertStatus(500);
        $this->assertDatabaseCount('contact_mails', 0);

        $this->instance(Dispatcher::class, $realDispatcher);
        $this->submit($this->payload())->assertOk()->assertJson(['success' => true]);
        $this->assertDatabaseCount('contact_mails', 1);
    }

    public function test_recovery_command_is_scheduled_automatically(): void
    {
        $this->artisan('schedule:list')->expectsOutputToContain('contact:resend-failed')->assertSuccessful();
    }
}
