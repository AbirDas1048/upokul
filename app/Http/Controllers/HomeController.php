<?php

namespace App\Http\Controllers;

use App\Services\ContactService;
use App\Services\ResponseCodeAndMessage;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // return view('welcome');
        return view('home');
    }

    public function contact(Request $request)
    {
        [$status, $messages, $data] = (new ContactService())->store($request);

        $isSuccess = $status === ResponseCodeAndMessage::SUCCESS;

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => $isSuccess,
                'status'  => $status,
                'message' => $messages,
                'data'    => $data,
            ]);
        }

        return back()->with($isSuccess ? 'success' : 'error', $messages);
    }
}
