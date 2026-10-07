<?php

return [
    'brand' => 'Failed Jobs Manager',
    'brand_subtitle' => 'Laravel queue failure control',
    'topbar_meta' => 'Standard failed_jobs table',

    'pages' => [
        'index_title' => 'Failed jobs',
        'index_subtitle' => 'Standard failed_jobs table with retry and cleanup actions',
        'show_title' => 'Failed job #:id',
        'show_subtitle' => 'Payload and exception details',
        'default_subtitle' => 'Review, retry, and clean failed queue jobs',
    ],

    'metrics' => [
        'found' => 'Found',
        'filtered' => 'With current filters',
    ],

    'filters' => [
        'queue' => 'Queue',
        'not_selected' => 'Not selected',
        'search' => 'Search',
        'search_placeholder' => 'payload or exception',
        'failed_from' => 'Date from',
        'failed_to' => 'Date to',
        'submit' => 'Search',
        'reset' => 'Reset',
    ],

    'table' => [
        'payload' => 'Payload',
        'exception' => 'Exception',
        'failed_at' => 'Failed at',
        'actions' => 'Actions',
        'empty' => 'No jobs',
        'no_uuid' => 'No uuid',
        'shown' => 'Shown',
        'of' => 'of',
        'previous' => 'Previous',
        'next' => 'Next',
    ],

    'actions' => [
        'back' => 'Back',
        'details' => 'Details',
        'retry' => 'Retry',
        'retry_selected' => 'Retry selected',
        'selected_count' => 'Selected: :count',
        'delete' => 'Delete',
        'delete_confirm' => 'Delete record?',
    ],

    'details' => [
        'payload_summary' => 'Payload summary',
        'exception_summary' => 'Exception summary',
        'serialized_command' => 'Serialized command',
        'raw_payload' => 'Raw payload',
        'raw_exception' => 'Raw exception',
        'failed_at' => 'Failed at',
        'default_queue' => 'default',
        'empty_value' => '-',
    ],

    'payload' => [
        'job' => 'Job',
        'handler' => 'Handler',
        'uuid' => 'UUID',
        'attempts' => 'Attempts',
        'max_tries' => 'Max tries',
        'timeout' => 'Timeout',
        'unknown_job' => 'Unknown job',
    ],

    'exception' => [
        'class' => 'Class',
        'message' => 'Message',
        'file' => 'File',
        'line' => 'Line',
        'unknown_exception' => 'Unknown exception',
    ],

    'flash' => [
        'deleted' => 'Record deleted',
        'delete_failed' => 'Unable to delete record',
        'retry_sent' => 'Job sent to retry',
        'bulk_retry_sent' => 'Jobs sent to retry',
        'bulk_retry_partially_sent' => 'Some jobs were sent to retry before an error occurred',
        'retry_failed' => 'Unable to send job to retry',
    ],
];
