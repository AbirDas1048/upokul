<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Support\Settings;
use App\Support\Uploads;
use Illuminate\Http\Request;

/**
 * Validates and stores a form that is described by a field list in config/site_settings.php.
 * Used for the Settings groups, the page sections and the appearance form.
 */
trait SavesSettingsForm
{
    /**
     * @param  list<array<string, mixed>>  $fields
     * @param  string  $prefix  settings key prefix, e.g. "general." or "section.services."
     * @param  bool  $withToggle  also store the section's "enabled" switch
     * @return list<string> the setting keys that were written
     */
    protected function saveFields(Request $request, array $fields, string $prefix, bool $withToggle = false): array
    {
        $settings = app(Settings::class);
        $rules = [];

        foreach ($fields as $field) {
            $rules[$field['name']] = $field['type'] === 'toggle' ? ['nullable', 'boolean'] : $field['rules'];
            $rules['reset_'.$field['name']] = ['nullable', 'boolean'];
        }

        $validated = $request->validate($rules);
        $pairs = [];

        foreach ($fields as $field) {
            $name = $field['name'];
            $key = $prefix.$name;

            switch ($field['type']) {
                case 'image':
                    if ($request->hasFile($name)) {
                        Uploads::delete($settings->get($key)); // only uploads/... files are ever removed
                        $pairs[$key] = Uploads::store($request->file($name));
                    } elseif ($request->boolean('reset_'.$name)) {
                        Uploads::delete($settings->get($key));
                        $pairs[$key] = null; // back to the shipped default
                    }

                    break;

                case 'password':
                    // Blank means "keep the current password".
                    if (($validated[$name] ?? '') !== '') {
                        $pairs[$key] = $validated[$name];
                    }

                    break;

                case 'toggle':
                    $pairs[$key] = $request->boolean($name) ? '1' : '0';

                    break;

                default:
                    $value = $validated[$name] ?? null;
                    $value = is_string($value) ? trim(str_replace("\r\n", "\n", $value)) : $value;

                    // Laravel turns an empty input into null; for allow_blank fields a submitted-but-empty
                    // value is a deliberate "nothing" (e.g. hide the map) and must not revert to the default.
                    if (($value === null || $value === '') && ! empty($field['allow_blank']) && $request->exists($name)) {
                        $value = '';
                    }

                    $pairs[$key] = $value === '' && empty($field['allow_blank']) ? null : $value;
            }
        }

        if ($withToggle) {
            $pairs[$prefix.'enabled'] = $request->boolean('enabled') ? '1' : '0';
        }

        $settings->set($pairs);

        return array_keys($pairs);
    }
}
