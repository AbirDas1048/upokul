<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\SavesSettingsForm;
use App\Http\Controllers\Controller;
use App\Support\Activity;
use App\Support\Settings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Pick the public home-page design and preview every design at desktop / tablet / phone width. */
class AppearanceController extends Controller
{
    use SavesSettingsForm;

    public function index(Settings $settings): View
    {
        $def = config('site_settings.groups.appearance');

        return view('admin.appearance', [
            'field' => $def['fields'][0],
            'current' => $settings->get('appearance.home_theme', 'chooser'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $this->saveFields($request, config('site_settings.groups.appearance.fields'), 'appearance.');
        Activity::log('appearance.update', 'Changed the home page design to "'.$request->input('home_theme').'"');

        return back()->with('status', 'Home page design saved.');
    }
}
