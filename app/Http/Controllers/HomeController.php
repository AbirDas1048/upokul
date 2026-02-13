<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class HomeController extends Controller
{
    public function index()
    {
        // return view('welcome');
        return view('home');
    }

    public function contact(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email|max:150',
            'phone'   => 'required|string|max:20',
            'subject' => 'required|string|max:150',
            'message' => 'required|string|max:2000',
        ]);

        Mail::send('emails.contact', $validated, function ($mail) use ($validated) {
            $mail->to(config('custom.office_email'))
                ->subject('New Contact Message: ' . $validated['subject']);
        });

        return response()->json([
            'status'  => true,
            'message' => 'Thank you! We will contact you shortly.'
        ]);
    }
}
