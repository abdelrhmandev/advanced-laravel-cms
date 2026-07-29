<?php
return [
    'app' => [
        'html_allowed_tags' => '<p><strong><b><i><br><span><u><em><ul><li><ol><h1><h2><h3><h4><div>',
        'title' => env('APP_NAME', 'New Project'),
        'description' => env('APP_NAME', 'New Project').' Description',
        'logo' => 'assets/logo.svg',
        'favicon' => 'assets/logo.svg',
        'page_templates' => [
            'homepage' => 'HomePage Template',
            'contact-us' => 'Contact Us Template',
            'about-us' => 'About Us Template',
            'services' => 'Services Template Page',
            'projects' => 'Projects Template Page',
            'single-page' => 'Single Page Template',
        ],
    ],

    'locale_flags' => [
        'en' => 'https://flagcdn.com/24x18/gb.png',
        'ar' => 'https://flagcdn.com/24x18/eg.png',
    ],

    'admin' => [
        'name_length_limit' => 10,
        'prefixRoute' => env('ADMIN_PREFIX', 'admin'),
        'roles' => [
            [
                'name' => 'Editor',
                'arabic' => 'محرر',
            ],
            [
                'name' => 'Writer',
                'arabic' => 'كاتب',
            ],
        ],
        // 'permissions_moudles' => ['pages', 'roles', 'permissions', 'settings', 'menus', 'logs', 'users'],
    ],
];
