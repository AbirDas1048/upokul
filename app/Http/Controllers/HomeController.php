<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        return view('welcome');
        // return view('home');
    }

    public function contact(Request $request)
    {
        dd('papape');
    }
}
