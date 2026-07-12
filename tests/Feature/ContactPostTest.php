<?php

namespace Tests\Feature;

use App\Jobs\SendContactMailJob;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ContactPostTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(ValidateCsrfToken::class);
    }

    /**
     * A basic feature test example.
     */
    public function test_contact_form_can_be_submitted_with_ajax_request(): void
    {
        Queue::fake();

        $payload = [
            'name' => 'Demo User',
            'email' => 'demo@example.com',
            'phone' => '01700000000',
            'subject' => 'Test Subject',
            'mail_message' => 'This is a test contact message.',
        ];

        $response = $this->postJson(route('contact.submit'), $payload, [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
                'data' => $payload,
            ]);

        $this->assertDatabaseHas('contact_mails', [
            'name' => 'Demo User',
            'email' => 'demo@example.com',
            'phone' => '01700000000',
            'subject' => 'Test Subject',
            'mail_message' => 'This is a test contact message.',
            'status' => 0,
        ]);

        Queue::assertPushed(SendContactMailJob::class);
    }

    public function test_contact_form_returns_validation_errors_for_missing_fields(): void
    {
        Queue::fake();

        $response = $this->postJson(route('contact.submit'), [], [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response
            ->assertOk()
            ->assertJson([
                'success' => false,
            ])
            ->assertJsonPath('data.name.0', 'Please enter your name.')
            ->assertJsonPath('data.email.0', 'Please enter your email address.')
            ->assertJsonPath('data.phone.0', 'Please enter your phone number.')
            ->assertJsonPath('data.subject.0', 'Please enter a subject.')
            ->assertJsonPath('data.mail_message.0', 'Please enter your message.');

        $this->assertDatabaseCount('contact_mails', 0);

        Queue::assertNothingPushed();
    }

    public function test_contact_form_rejects_non_ajax_request(): void
    {
        $payload = [
            'name' => 'Demo User',
            'email' => 'demo@example.com',
            'phone' => '01700000000',
            'subject' => 'Test Subject',
            'mail_message' => 'This is a test contact message.',
        ];

        $response = $this->post(route('contact.submit'), $payload);

        $response
            ->assertOk()
            ->assertJson([
                'success' => false,
                'message' => 'Request must be ajax',
            ]);

        $this->assertDatabaseCount('contact_mails', 0);
    }
}
