<?php
return [
    [
        'title' => 'Dashboard',
        'icon' => 'home',
        'url' => 'admin.dashboard',
    ],

    [
        'title' => 'Users',
        'icon' => 'user',
        'children' => [
            [
                'title' => 'Add User',
                'icon' => 'plus',
                'url' => 'admin.users.create',
                'can' => 'users.create',
            ],

            [
                'title' => 'All Users',
                'icon' => 'bars-4',
                'url' => 'admin.users.index',
                'can' => 'users.index',
            ],
        ],
    ],

    [
        'title' => 'Roles',
        'icon' => 'lock-closed',
        'url' => 'admin.roles',
        'can' => 'roles.index',
    ],
    [
        'title' => 'Permissions',
        'icon' => 'shield-check',
        'url' => 'admin.permissions',
        'can' => 'permissions.index',
    ],
    [
        'title' => 'Settings',
        'icon' => 'cog-8-tooth',
        'url' => 'admin.settings.index',
        'can' => 'settings.edit',
    ],

    [
        'title' => 'Pages',
        'icon' => 'document-text',
        'children' => [
            [
                'title' => 'Add Page',
                'icon' => 'plus',
                'url' => 'admin.pages.create',
                'can' => 'pages.create',
            ],

            [
                'title' => 'All Pages',
                'icon' => 'bars-4',
                'url' => 'admin.pages.index',
                'can' => 'pages.index',
            ],
        ],
    ],

    [
        'title' => 'Blocks',
        'icon' => 'cube',
        'url' => 'admin.blocks.index',
        'can' => 'blocks.index',
    ],

    [
        'title' => 'Contacts',
        'icon' => 'identification',
        'url' => 'admin.contacts.index',
        'can' => 'contacts.index',
    ],

    [
        'title' => 'Menus',
        'icon' => 'list-bullet',
        'url' => 'admin.menus.index',
        'can' => 'menus.index',
    ],
    [
        'title' => 'Activitylogs',
        'icon' => 'clock',
        'url' => 'admin.activitylogs.index',
        'can' => 'activity_logs.index',
    ],
];
