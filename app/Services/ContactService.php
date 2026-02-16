<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactService
{
    public function store(Request $request){
        dd($request->all());

        $validated = $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email|max:150',
            'phone'   => 'required|string|max:20',
            'subject' => 'required|string|max:150',
            'mail_message' => 'required|string|max:2000',
        ]);

        Mail::send('emails.contact', $validated, function ($mail) use ($validated) {
            $mail->to(config('custom.office_email'))
                ->subject('New Contact Message: ' . $validated['subject']);
        });
    }
}
