<?php

namespace App\Http\Controllers;

use App\Services\ContactService;
use App\Services\ResponseCodeAndMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class HomeController extends Controller
{
    public function index()
    {
        return view('welcome');
    }

    public function demo1(){
        return view('home');
    }

    public function demo2(){
        return view('home_new');
    }

    public function demo3(){
        return view('new');
    }

    public function contact(Request $request)
    {
        if ($request->ajax()) {
            [$status, $messages, $data] = (new ContactService())->store($request);

            return response()->json([
                'success' => $status === ResponseCodeAndMessage::SUCCESS,
                'status'  => $status,
                'message' => $messages,
                'data'    => $data
            ]);
        }
        return response()->json(['success' => false, 'message' => 'Request must be ajax']);
    }
}
