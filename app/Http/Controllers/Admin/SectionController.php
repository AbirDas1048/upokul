<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\SavesSettingsForm;
use App\Http\Controllers\Controller;
use App\Support\Activity;
use App\Support\Settings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Texts and on/off switch for each section of the landing pages. */
class SectionController extends Controller
{
    use SavesSettingsForm;

    public function index(Settings $settings): View
    {
        return view('admin.sections.index', [
            'sections' => config('site_settings.sections'),
            'enabled' => fn (string $id) => $settings->bool("section.{$id}.enabled", true),
        ]);
    }

    public function edit(string $section, Settings $settings): View
    {
        $def = $this->definition($section);
        $values = [];

        foreach ($def['fields'] as $field) {
            $values[$field['name']] = $settings->get("section.{$section}.{$field['name']}");
        }

        return view('admin.sections.edit', [
            'id' => $section,
            'def' => $def,
            'values' => $values,
            'enabled' => $settings->bool("section.{$section}.enabled", true),
            'hasToggle' => ($def['toggle'] ?? true) !== false,
        ]);
    }

    public function update(Request $request, string $section): RedirectResponse
    {
        $def = $this->definition($section);

        $this->saveFields($request, $def['fields'], "section.{$section}.", ($def['toggle'] ?? true) !== false);
        Activity::log('sections.update', "Edited the {$def['label']} section");

        return redirect()->route('admin.sections.edit', $section)->with('status', "{$def['label']} section saved.");
    }

    /** @return array<string, mixed> */
    private function definition(string $section): array
    {
        $def = config("site_settings.sections.{$section}");
        abort_if($def === null, 404);

        return $def;
    }
}
