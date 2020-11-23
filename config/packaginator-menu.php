<?php

return [
    'merge_to_navigation' => true,

    'navs' => [
        'sidebar' =>[
            [
                'name' => 'Packaginator',
                'link' => '/packaginator/creator',
                'icon' => 'speed',
                'key' => 'packaginator::menus.packaginator',
                'children_top' => [
                    [
                        'name' => 'Creator',
                        'link' => '/packaginator/creator',
                        'key' => 'packaginator::menus.creator',
                    ],
                    [
                        'name' => 'Example',
                        'link' => '/packaginator/example',
                        'key' => 'packaginator::menus.example',
                    ]
                ],
                'children' => [
                    [
                        'name' => 'Creator',
                        'link' => '/packaginator/creator',
                        'key' => 'packaginator::menus.creator',
                    ],
                    [
                        'name' => 'Example',
                        'link' => '/packaginator/example',
                        'key' => 'packaginator::menus.example',
                    ]
                ],
            ]
        ],
        'guestSidebar' =>[
            [
                'name' => 'Packaginator',
                'link' => '/packaginator/creator',
                'icon' => 'speed',
                'key' => 'packaginator::menus.packaginator',
                'children_top' => [
                    [
                        'name' => 'Creator',
                        'link' => '/packaginator/creator',
                        'key' => 'packaginator::menus.creator',
                    ],
                ],
                'children' => [
                    [
                        'name' => 'Creator',
                        'link' => '/packaginator/creator',
                        'key' => 'packaginator::menus.creator',
                    ],
                ],
            ]
        ]
    ]
];
