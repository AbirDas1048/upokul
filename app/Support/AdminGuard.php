<?php

namespace App\Support;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Privilege-escalation rules for user and role management. Having "users.update" or "roles.update"
 * must never be a way to gain more power than you already have:
 *
 *  - you can only give a role permissions you hold yourself;
 *  - you can only assign / edit a role whose permissions you fully hold;
 *  - you can only edit or delete a user whose power you fully hold, never yourself (use Profile)
 *    and never the master admin.
 *
 * The master admin holds everything, so only the last rule (the master account itself is untouchable
 * here) applies to them.
 */
class AdminGuard
{
    public static function canManageRole(User $actor, Role $role): bool
    {
        return $actor->isMaster() || self::covers($actor, $role->permissionKeys());
    }

    public static function canManageUser(User $actor, User $target): bool
    {
        if ($target->isMaster() || $target->is($actor)) {
            return false;
        }

        if ($actor->isMaster()) {
            return true;
        }

        return $target->role_id === null || self::covers($actor, $target->role?->permissionKeys() ?? []);
    }

    /** @param  iterable<string>  $keys */
    public static function covers(User $actor, iterable $keys): bool
    {
        $held = array_flip($actor->permissionKeys());

        foreach ($keys as $key) {
            if (! isset($held[$key])) {
                return false;
            }
        }

        return true;
    }

    /** @return Collection<int, Role> roles this admin is allowed to hand out */
    public static function assignableRoles(User $actor): Collection
    {
        return Role::query()->orderBy('name')->get()->filter(fn (Role $role) => self::canManageRole($actor, $role))->values();
    }
}
