<?php

namespace App\Http\Controllers;

use App\Services\ContactService;
use App\Services\ResponseCodeAndMessage;
use App\Support\Installer;
use App\Support\Site;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // The admin picks the public home design (Appearance); until then visitors see the design chooser.
        $view = ['aurora' => 'demo1', 'noir' => 'demo2', 'glass' => 'demo3'][app(Site::class)->theme()] ?? 'welcome';

        return view($view, ['installWelcome' => app(Installer::class)->consumeWelcome($request->query('welcome'))]);
    }

    public function demo1()
    {
        return view('demo1');
    }

    public function demo2()
    {
        return view('demo2');
    }

    public function demo3()
    {
        return view('demo3');
    }

    public function contact(Request $request)
    {
        if ($request->ajax()) {
            [$status, $messages, $data] = (new ContactService)->store($request);

            return response()->json([
                'success' => $status === ResponseCodeAndMessage::SUCCESS,
                'status' => $status,
                'message' => $messages,
                'data' => $data,
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Request must be ajax']);
    }
}
