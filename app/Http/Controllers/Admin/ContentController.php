<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContentItem;
use App\Support\Activity;
use App\Support\Uploads;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * One controller for every repeatable content type in config/content_types.php. The permission
 * checked is "<type>.<action>", so each type can be granted to roles independently.
 */
class ContentController extends Controller
{
    public function index(string $type): View
    {
        $def = $this->definition($type);
        Gate::authorize("{$type}.view");

        return view('admin.content.index', [
            'type' => $type,
            'def' => $def,
            'items' => ContentItem::ofType($type)->ordered()->get(),
        ]);
    }

    public function create(string $type): View
    {
        $def = $this->definition($type);
        Gate::authorize("{$type}.create");

        $next = (int) ContentItem::ofType($type)->max('sort_order') + 10;

        return view('admin.content.form', [
            'type' => $type, 'def' => $def, 'item' => new ContentItem(['sort_order' => $next, 'is_active' => true, 'data' => []]),
        ]);
    }

    public function store(Request $request, string $type): RedirectResponse
    {
        $def = $this->definition($type);
        Gate::authorize("{$type}.create");

        $item = new ContentItem(['type' => $type]);
        $this->fill($request, $item, $def);
        $item->save();

        Activity::log("{$type}.create", "Added {$def['singular']} \"{$item->title}\"");

        return redirect()->route('admin.content.index', $type)->with('status', "{$def['singular']} added.");
    }

    public function edit(string $type, int $id): View
    {
        $def = $this->definition($type);
        Gate::authorize("{$type}.update");

        return view('admin.content.form', ['type' => $type, 'def' => $def, 'item' => ContentItem::ofType($type)->findOrFail($id)]);
    }

    public function update(Request $request, string $type, int $id): RedirectResponse
    {
        $def = $this->definition($type);
        Gate::authorize("{$type}.update");

        $item = ContentItem::ofType($type)->findOrFail($id);
        $this->fill($request, $item, $def);
        $item->save();

        Activity::log("{$type}.update", "Edited {$def['singular']} \"{$item->title}\"");

        return redirect()->route('admin.content.index', $type)->with('status', "{$def['singular']} updated.");
    }

    public function destroy(string $type, int $id): RedirectResponse
    {
        $def = $this->definition($type);
        Gate::authorize("{$type}.delete");

        $item = ContentItem::ofType($type)->findOrFail($id);

        foreach ($def['fields'] as $field) {
            if ($field['type'] === 'image') {
                Uploads::delete($item->data[$field['name']] ?? null);
            }
        }

        $item->delete();
        Activity::log("{$type}.delete", "Deleted {$def['singular']} \"{$item->title}\"");

        return redirect()->route('admin.content.index', $type)->with('status', "{$def['singular']} deleted.");
    }

    /** @return array<string, mixed> */
    private function definition(string $type): array
    {
        $def = config("content_types.{$type}");
        abort_if($def === null, 404);

        return $def;
    }

    /** Validate the request against the type's field definitions and copy it onto the item. */
    private function fill(Request $request, ContentItem $item, array $def): void
    {
        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'is_active' => ['nullable', 'boolean'],
        ];

        foreach ($def['fields'] as $field) {
            $rules[$field['name']] = $field['type'] === 'toggle' ? ['nullable', 'boolean'] : $field['rules'];
        }

        $validated = $request->validate($rules);
        $data = $item->data ?? [];

        foreach ($def['fields'] as $field) {
            $name = $field['name'];

            if ($field['type'] === 'image') {
                if ($request->hasFile($name)) {
                    Uploads::delete($data[$name] ?? null);
                    $data[$name] = Uploads::store($request->file($name));
                }

                continue;
            }

            $value = $validated[$name] ?? null;
            $data[$name] = match ($field['type']) {
                'number' => $value === null ? null : (int) $value,
                'toggle' => $request->boolean($name),
                'lines', 'textarea' => $value === null ? null : trim(str_replace("\r\n", "\n", (string) $value)),
                default => is_string($value) ? trim($value) : $value,
            };
        }

        $item->title = trim($validated['title']);
        $item->sort_order = (int) ($validated['sort_order'] ?? 0);
        $item->is_active = $request->boolean('is_active');
        $item->data = $data;
    }
}
