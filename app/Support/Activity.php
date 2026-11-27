<?php

namespace App\Support;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

/** Audit trail shown under Administration → Activity log. */
class Activity
{
    public static function log(string $action, string $description): void
    {
        try {
            $user = Auth::user();

            ActivityLog::create([
                'user_id' => $user?->getAuthIdentifier(),
                'user_name' => $user?->name,
                'action' => $action,
                'description' => mb_substr($description, 0, 500),
                'ip_address' => request()?->ip(),
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            report($e); // an audit failure must never break the action itself
        }
    }
}
