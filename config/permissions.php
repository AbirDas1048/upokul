<?php

/*
|--------------------------------------------------------------------------
| Permission registry
|--------------------------------------------------------------------------
| Every permission is "module.action". The master admin passes every check; every other
| admin user only gets what their role lists. Add a module here and it automatically
| appears in the role editor, the sidebar checks (@can) and the route middleware (can:).
|
| "create", "update" and "delete" always imply "view" (enforced when a role is saved).
*/

$crud = ['view' => 'View', 'create' => 'Add', 'update' => 'Edit', 'delete' => 'Delete'];

return [
    'groups' => [
        'overview' => 'Overview',
        'inbox' => 'Inbox',
        'content' => 'Website content',
        'config' => 'Configuration',
        'admin' => 'Administration',
    ],

    'modules' => [
        'dashboard' => ['group' => 'overview', 'label' => 'Dashboard', 'icon' => 'fa-gauge-high', 'actions' => ['view' => 'View']],

        'contacts' => ['group' => 'inbox', 'label' => 'Contact messages', 'icon' => 'fa-envelope-open-text', 'actions' => ['view' => 'View', 'update' => 'Mark read / resend', 'delete' => 'Delete']],

        'sections' => ['group' => 'content', 'label' => 'Page sections & text', 'icon' => 'fa-table-columns', 'actions' => ['view' => 'View', 'update' => 'Edit']],
        'services' => ['group' => 'content', 'label' => 'Services', 'icon' => 'fa-concierge-bell', 'actions' => $crud],
        'journeys' => ['group' => 'content', 'label' => 'Journeys / packages', 'icon' => 'fa-route', 'actions' => $crud],
        'process_steps' => ['group' => 'content', 'label' => 'How it works steps', 'icon' => 'fa-list-ol', 'actions' => $crud],
        'stats' => ['group' => 'content', 'label' => 'Trust numbers', 'icon' => 'fa-chart-simple', 'actions' => $crud],
        'gallery' => ['group' => 'content', 'label' => 'Gallery photos', 'icon' => 'fa-images', 'actions' => $crud],
        'reviews' => ['group' => 'content', 'label' => 'Reviews', 'icon' => 'fa-star', 'actions' => $crud],
        'faqs' => ['group' => 'content', 'label' => 'FAQs', 'icon' => 'fa-circle-question', 'actions' => $crud],
        'about_points' => ['group' => 'content', 'label' => 'About highlights', 'icon' => 'fa-circle-info', 'actions' => $crud],

        'appearance' => ['group' => 'config', 'label' => 'Appearance & preview', 'icon' => 'fa-palette', 'actions' => ['view' => 'View / preview', 'update' => 'Edit']],
        'publish' => ['group' => 'config', 'label' => 'Publish website changes', 'icon' => 'fa-rocket', 'actions' => ['update' => 'Publish']],
        'settings' => ['group' => 'config', 'label' => 'Site settings', 'icon' => 'fa-sliders', 'actions' => ['view' => 'View', 'update' => 'Edit']],
        'mail' => ['group' => 'config', 'label' => 'Mail (SMTP) settings', 'icon' => 'fa-paper-plane', 'actions' => ['view' => 'View', 'update' => 'Edit']],

        'users' => ['group' => 'admin', 'label' => 'Admin users', 'icon' => 'fa-users', 'actions' => $crud],
        'roles' => ['group' => 'admin', 'label' => 'Roles & permissions', 'icon' => 'fa-user-shield', 'actions' => $crud],
        'activity' => ['group' => 'admin', 'label' => 'Activity log', 'icon' => 'fa-clock-rotate-left', 'actions' => ['view' => 'View', 'delete' => 'Clear']],
    ],

    // Roles created by the installation wizard so the permission system is usable straight away.
    'starter_roles' => [
        'Content Manager' => [
            'description' => 'Edits page text, services, journeys, gallery, reviews and FAQs. No settings or users.',
            'permissions' => [
                'dashboard.view', 'sections.view', 'sections.update', 'appearance.view',
                ...array_merge(...array_map(
                    fn (string $m) => ["$m.view", "$m.create", "$m.update", "$m.delete"],
                    ['services', 'journeys', 'process_steps', 'stats', 'gallery', 'reviews', 'faqs', 'about_points'],
                )),
            ],
        ],
        'Support Agent' => [
            'description' => 'Reads and answers contact messages. Read-only everywhere else.',
            'permissions' => ['dashboard.view', 'contacts.view', 'contacts.update'],
        ],
    ],
];
