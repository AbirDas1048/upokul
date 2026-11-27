<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Activity;
use App\Support\Publisher;
use Illuminate\Http\RedirectResponse;

/** Confirms the draft: from this moment visitors see the admin's changes. */
class PublishController extends Controller
{
    public function store(Publisher $publisher): RedirectResponse
    {
        $publisher->publish();
        Activity::log('publish', 'Published the website changes');

        return back()->with('status', 'Your changes are now live on the website.');
    }
}
