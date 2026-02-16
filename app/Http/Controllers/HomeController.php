<?php

namespace App\Http\Controllers;

use App\Services\ContactService;
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
        [$status, $response] = (new ContactService())->store($request);

        return response()->json([
            'status'  => true,
            'message' => 'Thank you! We will contact you shortly.'
        ]);
    }
}
