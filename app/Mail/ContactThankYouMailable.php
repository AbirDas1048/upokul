<?php

namespace App\Mail;

use App\Models\ContactMail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactThankYouMailable extends Mailable
{
    use Queueable, SerializesModels;

    public ContactMail $contactMail;

    public function __construct(ContactMail $contactMail)
    {
        $this->contactMail = $contactMail;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Thank you for contacting '.config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-thank-you',
            with: [
                'contactMail' => $this->contactMail,
            ],
        );
    }
}
