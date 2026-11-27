<?php

namespace App\Models;

/** Read-only helper around config/permissions.php. */
final class PermissionRegistry
{
    /** @return list<string> every valid "module.action" key */
    public static function all(): array
    {
        $keys = [];

        foreach (config('permissions.modules') as $module => $def) {
            foreach (array_keys($def['actions']) as $action) {
                $keys[] = "{$module}.{$action}";
            }
        }

        return $keys;
    }

    /**
     * @param  iterable<string>  $keys
     * @return list<string> unique, valid keys; create/update/delete imply view
     */
    public static function normalize(iterable $keys): array
    {
        $valid = array_flip(self::all());
        $out = [];

        foreach ($keys as $key) {
            if (! is_string($key) || ! isset($valid[$key])) {
                continue;
            }

            $out[$key] = true;
            [$module, $action] = explode('.', $key, 2);

            if ($action !== 'view' && isset($valid["{$module}.view"])) {
                $out["{$module}.view"] = true;
            }
        }

        return array_keys($out);
    }
}
