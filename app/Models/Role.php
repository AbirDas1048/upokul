<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = ['name', 'description'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /** @return list<string> */
    public function permissionKeys(): array
    {
        return \DB::table('role_permissions')->where('role_id', $this->id)->pluck('permission')->all();
    }

    /**
     * Replace this role's permissions. Only keys that exist in config/permissions.php are kept, and
     * create / update / delete always bring "view" of the same module with them.
     *
     * @param  iterable<string>  $keys
     */
    public function syncPermissions(iterable $keys): void
    {
        $keys = PermissionRegistry::normalize($keys);

        \DB::transaction(function () use ($keys) {
            \DB::table('role_permissions')->where('role_id', $this->id)->delete();
            \DB::table('role_permissions')->insert(array_map(fn ($key) => ['role_id' => $this->id, 'permission' => $key], $keys));
        });
    }
}
