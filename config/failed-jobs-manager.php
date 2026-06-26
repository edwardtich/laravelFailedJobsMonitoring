<?php

return [
    'table' => 'failed_jobs',
    'connection' => null,

    'ui' => [
        'enabled' => true,

        'route' => [
            'prefix' => 'failed-jobs-manager',
            'middleware' => [
                'web',
                'auth',
            ],
        ],

        'per_page' => 20,
        'allow_retry' => true,
        'allow_delete' => true,
        'retry_key' => 'auto',
        'search_max_length' => 120,
        'payload_preview_length' => 300,
        'exception_preview_length' => 500,
    ],

    'filament' => [
        'cluster' => null,
        'register_navigation' => true,
        'navigation_group' => null,
        'navigation_parent_item' => null,
        'navigation_sort' => null,
        'permission' => null,
        'roles' => [
            'super_admin',
        ],
    ],
];
