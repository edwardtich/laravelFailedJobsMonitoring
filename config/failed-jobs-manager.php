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
        'payload_preview_length' => 300,
        'exception_preview_length' => 500,
    ],
];
