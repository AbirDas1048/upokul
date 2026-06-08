<?php

namespace App\Mail;

use App\Models\ContactMail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactMailMailable extends Mailable
{
    use Queueable, SerializesModels;

    public ContactMail $contactMail;

    /**
     * Create a new message instance.
     */
    public function __construct(ContactMail $contactMail)
    {
        $this->contactMail = $contactMail;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Contact message send from web: '.$this->contactMail->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.contact',
            with: [
                'contactMail' => $this->contactMail,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
