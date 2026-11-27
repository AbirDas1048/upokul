<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMail extends Model
{
    protected $table = 'contact_mails';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'mail_message',
        'request_source',
        'status',
        'queued_at',
        'sent_at',
        'read_at',
        'error_message',
    ];

    protected $casts = [
        'request_source' => 'array',
        'queued_at' => 'datetime',
        'sent_at' => 'datetime',
        'read_at' => 'datetime',
    ];

    public static function saveContactMail(array $data): self
    {
        return self::create($data);
    }
}
