<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/** Admin image uploads: random file names, whitelisted extensions, stored under public/uploads. */
class Uploads
{
    private const EXTENSIONS = ['png', 'jpg', 'jpeg', 'webp', 'ico'];

    /** @return string path relative to /public, e.g. "uploads/ab12....jpg" */
    public static function store(UploadedFile $file): string
    {
        $extension = strtolower((string) ($file->guessExtension() ?: $file->getClientOriginalExtension()));
        $extension = $extension === 'jpeg' ? 'jpg' : $extension;

        if (! in_array($extension, self::EXTENSIONS, true)) {
            throw new \InvalidArgumentException('Unsupported image type.');
        }

        $directory = (string) config('installer.upload_path');
        File::ensureDirectoryExists($directory);

        $name = Str::lower(Str::random(32)).'.'.$extension;
        $file->move($directory, $name);

        return 'uploads/'.$name;
    }

    /** Removes a previously uploaded file. Shipped images (images/...) are never touched. */
    public static function delete(?string $path): void
    {
        if (! $path || ! str_starts_with($path, 'uploads/') || str_contains($path, '..')) {
            return;
        }

        $full = rtrim((string) config('installer.upload_path'), '/\\').DIRECTORY_SEPARATOR.basename($path);

        // A freshly written file can be locked for a moment on Windows (antivirus / indexer), so retry briefly.
        for ($attempt = 0; $attempt < 5 && is_file($full); $attempt++) {
            if (@unlink($full)) {
                break;
            }

            usleep(100_000);
        }
    }
}
