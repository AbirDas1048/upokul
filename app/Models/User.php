<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Deliberately NOT mass assignable: role_id, is_master, is_active. They are set explicitly by the
     * controllers that have already checked the acting user may do so.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /** @var list<string>|null permission keys of the role, loaded once per request */
    private ?array $permissionCache = null;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'is_master' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /** The master admin: every permission, no role needed, cannot be locked out by anyone else. */
    public function isMaster(): bool
    {
        return (bool) $this->is_master;
    }

    /** @return list<string> */
    public function permissionKeys(): array
    {
        if ($this->isMaster()) {
            return PermissionRegistry::all();
        }

        return $this->permissionCache ??= $this->role_id
            ? \DB::table('role_permissions')->where('role_id', $this->role_id)->pluck('permission')->all()
            : [];
    }

    public function hasPermission(string $key): bool
    {
        return $this->isMaster() || ($this->is_active && in_array($key, $this->permissionKeys(), true));
    }

    public function initials(): string
    {
        $parts = preg_split('/\s+/', trim($this->name)) ?: [];

        return mb_strtoupper(mb_substr($parts[0] ?? '?', 0, 1).(count($parts) > 1 ? mb_substr(end($parts), 0, 1) : ''));
    }
}
