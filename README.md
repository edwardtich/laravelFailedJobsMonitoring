# Laravel Failed Jobs Manager

Small UI for Laravel `failed_jobs` table with single and bulk retry actions.

## Installation from local packages directory

Add path repository to the host project:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "packages/laravel-failed-jobs-manager",
            "options": {
                "symlink": true
            }
        }
    ]
}
```

Require package:

```bash
composer require edwardtich/laravel-failed-jobs-manager:*
```

Publish config or views when customization is needed:

```bash
php artisan vendor:publish --provider="EdwardTich\\FailedJobsManager\\Providers\\FailedJobsManagerProvider" --tag=config
php artisan vendor:publish --provider="EdwardTich\\FailedJobsManager\\Providers\\FailedJobsManagerProvider" --tag=views
```

Default UI route:

```text
/failed-jobs-manager
```

Default page size is 20 records. Change it in published config:

```php
'ui' => [
    'per_page' => 20,
]
```

## Retry

Bulk retry uses Laravel native command with a list of failed job IDs:

```bash
php artisan queue:retry 42694 42695 42696
```

## Delete

Single failed job rows can be deleted from the UI. Bulk delete is intentionally not included.
