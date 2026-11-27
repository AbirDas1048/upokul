<?php

return [
    /*
    | Force the "installed" state (true / false). Null (default) = decide from the lock file / database.
    | The test suite sets this so existing tests are not redirected to the installation wizard.
    */
    'installed' => env('INSTALLER_INSTALLED') === null ? null : filter_var(env('INSTALLER_INSTALLED'), FILTER_VALIDATE_BOOLEAN),

    // Written when the wizard finishes. Delete it (and the `installed_at` setting) to run the wizard again.
    'lock' => env('INSTALLER_LOCK', storage_path('app/installed.lock')),

    // The .env file the wizard / settings page keeps in sync (APP_NAME).
    'env_file' => env('INSTALLER_ENV_FILE', base_path('.env')),

    // Where admin uploads (logo, gallery photos...) are stored. Must be inside /public to be served.
    'upload_path' => env('UPLOAD_PATH', public_path('uploads')),

    'requirements' => [
        'php' => '8.2.0',
        'extensions' => ['pdo', 'mbstring', 'openssl', 'tokenizer', 'xml', 'ctype', 'json', 'fileinfo'],
        'writable' => ['storage', 'storage/app', 'storage/logs', 'storage/framework', 'bootstrap/cache', 'public/uploads'],
    ],
];
