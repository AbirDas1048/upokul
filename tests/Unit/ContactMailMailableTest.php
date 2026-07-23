<?php

namespace Tests\Unit;

use App\Mail\ContactMailMailable;
use App\Models\ContactMail;
use Tests\TestCase;

class ContactMailMailableTest extends TestCase
{
    public function test_contact_mail_mailable_uses_contact_subject_and_view_data(): void
    {
        $contactMail = new ContactMail([
            'name' => 'Demo User',
            'email' => 'demo@example.com',
            'phone' => '01700000000',
            'subject' => 'Booking Question',
            'mail_message' => 'I want to know more about your service.',
        ]);

        $mailable = new ContactMailMailable($contactMail);

        $this->assertSame('Contact message send from web: Booking Question', $mailable->envelope()->subject);
        $this->assertSame('emails.contact', $mailable->content()->view);
        $this->assertSame(['contactMail' => $contactMail], $mailable->content()->with);
        $this->assertSame([], $mailable->attachments());
    }
}
