<?php

namespace App\Support;

/** Minimal, safe .env editor: updates existing keys in place, appends missing ones, keeps comments. */
class EnvWriter
{
    public function __construct(private readonly string $file) {}

    /** @param  array<string, string>  $values */
    public function set(array $values): void
    {
        $lines = is_file($this->file) ? preg_split('/\R/', (string) file_get_contents($this->file)) : [];

        foreach ($values as $key => $value) {
            if (! preg_match('/^[A-Z][A-Z0-9_]*$/', $key)) {
                throw new \InvalidArgumentException("Invalid environment key [{$key}].");
            }

            $line = $key.'='.$this->format($value);
            $found = false;

            foreach ($lines as $i => $existing) {
                if (preg_match('/^\s*'.preg_quote($key, '/').'\s*=/', $existing)) {
                    $lines[$i] = $line;
                    $found = true;
                    break;
                }
            }

            if (! $found) {
                $lines[] = $line;
            }
        }

        file_put_contents($this->file, rtrim(implode("\n", $lines))."\n", LOCK_EX);
    }

    /** Quote when needed; strip anything that could start a new line or an interpolation. */
    private function format(string $value): string
    {
        $value = preg_replace('/[\r\n\x00]+/', ' ', $value);

        if ($value === '' || ! preg_match('/^[A-Za-z0-9_.\-\/:@]+$/', $value)) {
            $value = '"'.str_replace(['\\', '"', '$'], ['\\\\', '\\"', '\\$'], $value).'"';
        }

        return $value;
    }
}
