<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Support\Activity;
use App\Support\AdminGuard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:users.view', only: ['index']),
            new Middleware('can:users.create', only: ['create', 'store']),
            new Middleware('can:users.update', only: ['edit', 'update']),
            new Middleware('can:users.delete', only: ['destroy']),
        ];
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));

        return view('admin.users.index', [
            'users' => User::query()->with('role')
                ->when($search !== '', function ($q) use ($search) {
                    $like = '%'.str_replace(['%', '_'], ['\\%', '\\_'], $search).'%';
                    $q->where(fn ($q) => $q->where('name', 'like', $like)->orWhere('email', 'like', $like));
                })
                ->orderByDesc('is_master')->orderBy('name')->paginate(15)->withQueryString(),
            'search' => $search,
        ]);
    }

    public function create(Request $request): View
    {
        return view('admin.users.form', ['user' => new User(['is_active' => true]), 'roles' => AdminGuard::assignableRoles($request->user())]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email:rfc', 'max:150', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(10)->letters()->mixedCase()->numbers()],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $role = $this->assignableRole($request, (int) $data['role_id']);

        $user = new User(['name' => $data['name'], 'email' => $data['email'], 'password' => $data['password']]);
        $user->role_id = $role->id;
        $user->is_master = false;
        $user->is_active = $request->boolean('is_active');
        $user->email_verified_at = now();
        $user->save();

        Activity::log('users.create', "Added admin user {$user->email} with role \"{$role->name}\"");

        return redirect()->route('admin.users.index')->with('status', 'Admin user added.');
    }

    public function edit(Request $request, User $user): View
    {
        $this->authorizeTarget($request, $user);

        return view('admin.users.form', ['user' => $user, 'roles' => AdminGuard::assignableRoles($request->user())]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorizeTarget($request, $user);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email:rfc', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'confirmed', Password::min(10)->letters()->mixedCase()->numbers()],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $role = $this->assignableRole($request, (int) $data['role_id']);

        $user->name = $data['name'];
        $user->email = $data['email'];

        if (! empty($data['password'])) {
            $user->password = $data['password'];
        }

        $user->role_id = $role->id;
        $user->is_active = $request->boolean('is_active');
        $user->save();

        Activity::log('users.update', "Edited admin user {$user->email} (role \"{$role->name}\", ".($user->is_active ? 'active' : 'inactive').')');

        return redirect()->route('admin.users.index')->with('status', 'Admin user updated.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $this->authorizeTarget($request, $user);

        $email = $user->email;
        $user->delete();
        Activity::log('users.delete', "Deleted admin user {$email}");

        return redirect()->route('admin.users.index')->with('status', 'Admin user deleted.');
    }

    private function authorizeTarget(Request $request, User $user): void
    {
        abort_unless(AdminGuard::canManageUser($request->user(), $user), 403, 'You cannot manage this account.');
    }

    private function assignableRole(Request $request, int $roleId): Role
    {
        $role = Role::query()->findOrFail($roleId);
        abort_unless(AdminGuard::canManageRole($request->user(), $role), 403, 'You cannot assign a role with more permissions than you have.');

        return $role;
    }
}
