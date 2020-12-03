<?php
return [
    // this resources has been auto load to layout
    'dist' => [
        'js/main.js',
        'js/main.legacy.js',
        'css/main.css',
    ],
    'routes' => [
        // all routes is active
        'active' => true,
        // section installations
        'installation' => [
            'active' => true,
            'prefix' => '/installation/packaginator',
            'name_prefix' => 'packaginator.installation.',
            // this routes has beed except for installation module
            'expect' => [
                'module-assets.assets',
                'packaginator.installation.index',
                'packaginator.installation.store',
            ]
        ],
        'creator' => [
            'active' => true,
            'prefix' => '/packaginator/creator',
            'name_prefix' => 'packaginator.creator.',
            'middleware' => [
                'web',
                //'auth',
               // 'verified'
            ]
        ],
        'example' => [
            'active' => true,
            'prefix' => '/packaginator/example',
            'name_prefix' => 'packaginator.example.',
            'middleware' => [
                'web',
                'auth',
                'verified'
            ]
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Use permissions in application.
    |--------------------------------------------------------------------------
    |
    | This permission has been insert to database with migrations
    | of module permission.
    |
    */
    'permissions' =>[
        'install_packages'
    ],

    /*
    |--------------------------------------------------------------------------
    | Can merge permissions to module permission
    |--------------------------------------------------------------------------
    */
    'merge_permissions' => true,

    'installation' => [
        'auto_redirect' => [
            // user with this permission has been automation redirect to
            // installation package
            'permission' => 'install_packages'
        ]
    ],

    'database' => [
        'tables' => [
            'users' =>'users',
            'packaginator_histories' =>'packaginator_histories',
        ]
    ]
];
