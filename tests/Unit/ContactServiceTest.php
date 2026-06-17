<?php

namespace Tests\Unit;

use App\Jobs\SendContactMailJob;
use App\Services\ContactService;
use App\Services\ResponseCodeAndMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ContactServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_service_stores_valid_contact_request(): void
    {
        Queue::fake();

        $request = Request::create('/contact', 'POST', [
            'name' => 'Demo User',
            'email' => 'demo@example.com',
            'phone' => '01700000000',
            'subject' => 'Test Subject',
            'mail_message' => 'This is a test message.',
        ]);

        [$status, $message, $data] = (new ContactService())->store($request);

        $this->assertSame(ResponseCodeAndMessage::SUCCESS, $status);
        $this->assertSame('Success', $message);

        $this->assertSame('Demo User', $data['name']);
        $this->assertSame('demo@example.com', $data['email']);
        $this->assertSame('01700000000', $data['phone']);
        $this->assertSame('Test Subject', $data['subject']);
        $this->assertSame('This is a test message.', $data['mail_message']);

        $this->assertDatabaseHas('contact_mails', [
            'name' => 'Demo User',
            'email' => 'demo@example.com',
            'phone' => '01700000000',
            'subject' => 'Test Subject',
            'mail_message' => 'This is a test message.',
            'status' => ContactService::MAIL_SEND_STATUS_PENDING,
        ]);

        Queue::assertPushed(SendContactMailJob::class);
    }

}
