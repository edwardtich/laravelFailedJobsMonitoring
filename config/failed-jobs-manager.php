<?php

return [
    // null uses queue.failed.table / queue.failed.database, the same storage the queue failer writes to.
    'table' => null,
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
        // auto: uuid for the "database-uuids" failer driver, id otherwise.
        'retry_key' => 'auto',
        'retry_chunk_size' => 100,
        'retry_max_ids' => 1000,
        'retry_lock_seconds' => 60,
        'queues_cache_ttl' => 60,
        'search_max_length' => 120,
        'payload_preview_length' => 300,
        'exception_preview_length' => 500,
    ],

    'authorization' => [
        // Standalone UI access gate. When the gate is not defined and no view permission is configured,
        // the standalone UI is only available in the local environment.
        'gate' => 'viewFailedJobsManager',

        'permissions' => [
            'view' => null,
            'retry' => null,
            'delete' => null,
            'view_sensitive_payload' => null,
        ],
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
