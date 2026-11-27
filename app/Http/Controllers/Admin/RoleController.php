<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PermissionRegistry;
use App\Models\Role;
use App\Support\Activity;
use App\Support\AdminGuard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RoleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:roles.view', only: ['index']),
            new Middleware('can:roles.create', only: ['create', 'store']),
            new Middleware('can:roles.update', only: ['edit', 'update']),
            new Middleware('can:roles.delete', only: ['destroy']),
        ];
    }

    public function index(Request $request): View
    {
        return view('admin.roles.index', [
            'roles' => Role::query()->withCount('users')->orderBy('name')->get(),
            'permissionCounts' => \DB::table('role_permissions')->selectRaw('role_id, count(*) as total')->groupBy('role_id')->pluck('total', 'role_id'),
            'totalPermissions' => count(PermissionRegistry::all()),
            'actor' => $request->user(),
        ]);
    }

    public function create(Request $request): View
    {
        return view('admin.roles.form', ['role' => new Role, 'granted' => [], 'actorKeys' => $request->user()->permissionKeys()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $keys = $this->grantableKeys($request, $data['permissions'] ?? []);

        $role = Role::create(['name' => $data['name'], 'description' => $data['description'] ?? null]);
        $role->syncPermissions($keys);

        Activity::log('roles.create', "Created role \"{$role->name}\" with ".count($role->permissionKeys()).' permissions');

        return redirect()->route('admin.roles.index')->with('status', 'Role created.');
    }

    public function edit(Request $request, Role $role): View
    {
        $this->authorizeRole($request, $role);

        return view('admin.roles.form', ['role' => $role, 'granted' => $role->permissionKeys(), 'actorKeys' => $request->user()->permissionKeys()]);
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $this->authorizeRole($request, $role);

        $data = $this->validated($request, $role);
        $role->update(['name' => $data['name'], 'description' => $data['description'] ?? null]);
        $role->syncPermissions($this->grantableKeys($request, $data['permissions'] ?? []));

        Activity::log('roles.update', "Updated role \"{$role->name}\" (".count($role->permissionKeys()).' permissions)');

        return redirect()->route('admin.roles.index')->with('status', 'Role updated.');
    }

    public function destroy(Request $request, Role $role): RedirectResponse
    {
        $this->authorizeRole($request, $role);

        if ($role->users()->exists()) {
            return back()->with('error', 'This role is still assigned to '.$role->users()->count().' user(s). Move them to another role first.');
        }

        $name = $role->name;
        $role->delete();
        Activity::log('roles.delete', "Deleted role \"{$name}\"");

        return redirect()->route('admin.roles.index')->with('status', 'Role deleted.');
    }

    /** @return array{name: string, description: ?string, permissions?: array<int, string>} */
    private function validated(Request $request, ?Role $role = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:80', Rule::unique('roles', 'name')->ignore($role?->id)],
            'description' => ['nullable', 'string', 'max:255'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string'],
        ]);
    }

    private function authorizeRole(Request $request, Role $role): void
    {
        abort_unless(AdminGuard::canManageRole($request->user(), $role), 403, 'This role has permissions you do not have, so you cannot change it.');
    }

    /**
     * Unknown keys are dropped; keys the acting admin does not hold themselves are refused
     * outright (the form never offers them, so this only trips on a hand-made request).
     *
     * @param  array<int, string>  $submitted
     * @return list<string>
     */
    private function grantableKeys(Request $request, array $submitted): array
    {
        $keys = PermissionRegistry::normalize($submitted);

        abort_unless(AdminGuard::covers($request->user(), $keys) || $request->user()->isMaster(), 403, 'You cannot grant permissions you do not have.');

        return $keys;
    }
}
