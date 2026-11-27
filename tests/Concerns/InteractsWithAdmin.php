<?php

namespace Tests\Concerns;

use App\Models\ContentItem;
use App\Models\Role;
use App\Models\User;
use App\Support\Publisher;
use App\Support\Settings;

trait InteractsWithAdmin
{
    /** Behave like a site that has been through the installation wizard (content lives in the database). */
    protected function installSite(): void
    {
        app(Settings::class)->set(['installed_at' => now()->toIso8601String()]);

        foreach (array_keys(config('content_types')) as $type) {
            ContentItem::seedDefaults($type);
        }

        $this->publish();
    }

    /** Confirm the draft, as an admin pressing "Publish changes" does. */
    protected function publish(): void
    {
        app(Publisher::class)->publish();
    }

    protected function master(array $attributes = []): User
    {
        $email = $attributes['email'] ?? 'master@example.com';

        if ($existing = User::query()->where('email', $email)->first()) {
            return $existing;
        }

        $user = new User(['name' => 'Master Admin', 'email' => $email, 'password' => 'Secret-pass-123']);
        $user->is_master = true;
        $user->is_active = true;
        $user->save();

        return $user;
    }

    /** An active admin whose role holds exactly these "module.action" permissions. */
    protected function adminWith(array $permissions, string $email = 'staff@example.com', string $roleName = 'Test role'): User
    {
        $role = Role::query()->firstOrCreate(['name' => $roleName]);
        $role->syncPermissions($permissions);

        $user = new User(['name' => 'Staff Member', 'email' => $email, 'password' => 'Secret-pass-123']);
        $user->role_id = $role->id;
        $user->is_active = true;
        $user->save();

        return $user;
    }
}
