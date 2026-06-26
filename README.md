# Laravel Failed Jobs Manager

[![Latest Version on Packagist](https://img.shields.io/packagist/v/edwardtich/laravel-failed-jobs-manager.svg?style=flat-square)](https://packagist.org/packages/edwardtich/laravel-failed-jobs-manager)
[![PHP Version Require](https://img.shields.io/packagist/php-v/edwardtich/laravel-failed-jobs-manager.svg?style=flat-square)](https://packagist.org/packages/edwardtich/laravel-failed-jobs-manager)
[![Laravel Version](https://img.shields.io/badge/Laravel-11%2F12-orange?style=flat-square)](https://laravel.com)

Laravel package for working with the standard `failed_jobs` table.

Features:

- standalone Laravel UI;
- optional Filament page;
- single failed job retry;
- bulk retry by selected IDs;
- single failed job delete;
- readable `payload` and `exception` summaries;
- English and Russian translations.

Bulk retry uses Laravel's native command format:

```bash
php artisan queue:retry 42694 42695 42696
```

## Table Of Contents

- [Requirements](#requirements)
- [Installation](#installation)
- [Publishing Files](#publishing-files)
- [Configuration](#configuration)
- [Standalone Laravel UI](#standalone-laravel-ui)
- [Filament Integration](#filament-integration)
- [Filament Navigation](#filament-navigation)
- [Filament Cluster Menu](#filament-cluster-menu)
- [Filament Shield And Permissions](#filament-shield-and-permissions)
- [Localization](#localization)
- [Pagination](#pagination)
- [Retry Key](#retry-key)
- [Actions](#actions)
- [Security](#security)
- [Changelog](#changelog)

## Requirements

- PHP 8.2+
- Laravel 11 or 12
- configured `failed_jobs` table
- configured Laravel queue failed job provider
- Filament is required only for Filament integration

## Installation

Install the package:

```bash
composer require edwardtich/laravel-failed-jobs-manager
```

For local package development through `packages/`, add a path repository to the host project's `composer.json`:

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

Then require the package:

```bash
composer require edwardtich/laravel-failed-jobs-manager:*
```

## Publishing Files

The package works without publishing the config file because the service provider recursively merges default settings during registration.

Publish the config only when you need to override routes, middleware, pagination, or Filament settings:

```bash
php artisan vendor:publish --provider="EdwardTich\\FailedJobsManager\\Providers\\FailedJobsManagerProvider" --tag=config
```

Publish Blade views:

```bash
php artisan vendor:publish --provider="EdwardTich\\FailedJobsManager\\Providers\\FailedJobsManagerProvider" --tag=views
```

Publish translations:

```bash
php artisan vendor:publish --provider="EdwardTich\\FailedJobsManager\\Providers\\FailedJobsManagerProvider" --tag=translations
```

## Configuration

After publishing, the config file will be available at:

```text
config/failed-jobs-manager.php
```

The package should not automatically create this file in the host application during normal boot. This is standard Laravel package behavior.

Default configuration:

```php
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
```

## Standalone Laravel UI

The standalone UI is enabled by default.

Default URL:

```text
/failed-jobs-manager
```

By default, the route uses these middleware:

```php
'middleware' => [
    'web',
    'auth',
],
```

In production, do not leave access protected only by `auth`. This page can retry jobs, delete records, and show `payload` / `exception`, so access should be restricted to admins or a dedicated permission.

Example with role middleware:

```php
'route' => [
    'prefix' => 'failed-jobs-manager',
    'middleware' => [
        'web',
        'auth',
        'role:admin',
    ],
],
```

If you use only the Filament page, disable the standalone UI:

```php
'ui' => [
    'enabled' => false,
],
```

## Filament Integration

Filament is optional. The package does not require `filament/filament` in `composer.json`, so projects without Filament can still use the standalone Laravel UI.

Register the package page in your Filament panel provider:

```php
use EdwardTich\FailedJobsManager\Filament\Pages\FailedJobsManagerPage;
use Filament\Pages\Dashboard;
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        ->pages([
            Dashboard::class,
            FailedJobsManagerPage::class,
        ]);
}
```

If your panel path is `/admin`, the page will be available at:

```text
/admin/failed-jobs-manager
```

## Filament Navigation

Menu settings are configured in `config/failed-jobs-manager.php`:

```php
'filament' => [
    'cluster' => null,
    'register_navigation' => true,
    'navigation_group' => 'System',
    'navigation_parent_item' => null,
    'navigation_sort' => 40,
    'permission' => null,
    'roles' => [
        'admin',
    ],
],
```

Hide the page from the sidebar:

```php
'register_navigation' => false,
```

The page will still be accessible by URL if the current Filament user passes `canAccess()`.

## Filament Cluster Menu

If the project uses Filament clusters and a manually composed sidebar, configure the page as a clustered page.

Example:

```php
'filament' => [
    'cluster' => \App\Filament\Clusters\AdministrationCluster::class,
    'register_navigation' => true,
    'navigation_group' => 'System',
    'navigation_parent_item' => 'Administration',
    'navigation_sort' => 40,
    'permission' => null,
    'roles' => [
        'super_admin',
    ],
],
```

The page must still be registered in the panel provider:

```php
use EdwardTich\FailedJobsManager\Filament\Pages\FailedJobsManagerPage;

public function panel(Panel $panel): Panel
{
    return $panel
        ->pages([
            Dashboard::class,
            FailedJobsManagerPage::class,
        ]);
}
```

If the sidebar is composed manually through a parent item array, add the page there too:

```php
protected array $administrationNavigation = [
    UserResource::class,
    RoleResource::class,
    FailedJobsManagerPage::class,
];
```

## Filament Shield And Permissions

If the project uses Filament Shield or Spatie Permission, prefer permission-based access instead of hardcoded role checks.

The package does not create permissions in the database by itself. This is intentional: permissions should be generated and assigned inside the application where Shield, roles, and guards are already configured.

Example:

```php
'filament' => [
    'permission' => null,
    'roles' => [
        'admin',
    ],
],
```

If `permission` is `null` and Filament Shield is installed, the package will try to read the page permission generated by Shield automatically.

For production, it is better to define the permission explicitly. This keeps package behavior independent from Shield generation settings in each project:

```php
'filament' => [
    'permission' => 'View:FailedJobsManagerPage',
    'roles' => [
        'admin',
    ],
],
```

If you want to set a permission manually, use the exact key generated by Shield. For example, with Shield settings `separator => ':'`, `case => 'pascal'`, and `prefix => 'view'`, the generated page key is:

```text
View:FailedJobsManagerPage
```

The package then checks:

```php
$user->can($permission);
```

`roles` is only used as a fallback. This is useful for roles like `super_admin` before Shield permissions are generated or assigned, but production access should preferably be permission-based.

After registering the page, generate Shield permissions:

```bash
php artisan shield:generate
```

Then open the Filament roles page and grant the required permission:

```text
/admin/shield/roles
```

For example:

```text
View:FailedJobsManagerPage
```

If Shield uses another `separator`, `case`, or `prefix`, the permission name will be different. In that case, check the actual permission name on the Shield roles page and set it in `failed-jobs-manager.filament.permission`.

## Localization

The package contains English and Russian translations.

Laravel uses the current application locale:

```php
'locale' => 'en',
```

Translation namespace:

```text
failed-jobs-manager::failed-jobs-manager
```

## Pagination

By default, the page displays 20 records:

```php
'ui' => [
    'per_page' => 20,
],
```

The package always uses pagination to avoid loading the full `failed_jobs` table.

Search is limited before querying the database:

```php
'ui' => [
    'search_max_length' => 120,
],
```

The search filter checks `payload` and `exception` with a wildcard `LIKE` query. Keep it for operational inspection, not for high-volume analytics.

## Retry Key

Different Laravel versions can retry failed jobs by different identifiers. Older projects usually use numeric `id`, while newer projects may use `uuid`.

Retry key configuration:

```php
'ui' => [
    'retry_key' => 'auto',
],
```

Available values:

- `auto` - use `uuid` when it exists, otherwise use `id`;
- `id` - always call queue commands with the failed job ID;
- `uuid` - call queue commands with UUID, fallback to ID when UUID is empty.

The UI still selects rows by internal `id`. The `retry_key` setting is applied when calling Laravel queue commands for retry and delete actions: `queue:retry` and `queue:forget`.

## Actions

### Retry One Job

The package calls Laravel's `queue:retry` command with the configured retry key.

### Retry Selected Jobs

The package collects selected IDs, resolves them to configured retry keys, and calls Laravel's native command:

```bash
php artisan queue:retry 42694 42695 42696
```

When `retry_key` is `auto` or `uuid`, the command can look like this:

```bash
php artisan queue:retry 550e8400-e29b-41d4-a716-446655440000
```

### Delete One Job

The UI deletes one failed job through Laravel's native command:

```bash
php artisan queue:forget 91401d2c-0784-4f43-824c-34f94a33c24d
```

The package is intended for Laravel's standard failed job storage and does not implement custom deletion adapters for non-standard failed job providers.

Bulk delete is intentionally not implemented. Failed jobs are operational data, and bulk deletion is too easy to misuse.

## Security

The package exposes operational actions:

- retry failed jobs;
- delete records from `failed_jobs`;
- inspect `payload`;
- inspect exception messages.

Do not expose this functionality to every authenticated user. `auth` only means that the user is logged in; it is not an administrator check.

Recommended production setup:

- standalone UI: `web`, `auth`, and admin/permission middleware;
- Filament UI: Filament auth plus Shield/permission access;
- if you use only Filament, disable the standalone UI.

Standalone middleware example with permission:

```php
'ui' => [
    'route' => [
        'prefix' => 'failed-jobs-manager',
        'middleware' => [
            'web',
            'auth',
            'can:View:FailedJobsManagerPage',
        ],
    ],
],
```

Example with a project-specific admin middleware:

```php
'ui' => [
    'route' => [
        'prefix' => 'failed-jobs-manager',
        'middleware' => [
            'web',
            'auth',
            'admin',
        ],
    ],
],
```

If the page is used only inside Filament, disable the standalone UI:

```php
'ui' => [
    'enabled' => false,
],
```

## Changelog

See [CHANGELOG.md](CHANGELOG.md).

## License

MIT © Edward Tich
