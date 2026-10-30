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
        // Bulk retry runs synchronously in the HTTP request; keep the limit small to stay within timeouts.
        'retry_max_ids' => 50,
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

        // view: null leaves page access to the gate (standalone) or filament.roles.
        // retry / delete: null denies the operation. Users who can retry or delete also see
        // unmasked exception messages and can search the full exception and payload text.
        'permissions' => [
            'view' => null,
            'retry' => null,
            'delete' => null,
        ],
    ],

    'filament' => [
        'cluster' => null,
        'register_navigation' => true,
        // Filament leaves clustered pages out of the main navigation. Set to true when the panel
        // hides the cluster sub-navigation and shows clustered items under navigation_parent_item.
        'register_in_main_navigation' => false,
        'navigation_group' => null,
        'navigation_parent_item' => null,
        'navigation_sort' => null,
        'permission' => null,
        'roles' => [
            'super_admin',
        ],
    ],
];
