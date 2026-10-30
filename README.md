# Laravel Failed Jobs Manager

[![Latest Version on Packagist](https://img.shields.io/packagist/v/edwardtich/laravel-failed-jobs-manager.svg?style=flat-square)](https://packagist.org/packages/edwardtich/laravel-failed-jobs-manager)
[![PHP Version Require](https://img.shields.io/packagist/php-v/edwardtich/laravel-failed-jobs-manager.svg?style=flat-square)](https://packagist.org/packages/edwardtich/laravel-failed-jobs-manager)
[![Laravel Version](https://img.shields.io/badge/Laravel-12%2F13-orange?style=flat-square)](https://laravel.com)

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

- PHP 8.3+
- Laravel 12 or 13
- Filament 4 or 5 (only for the Filament integration)
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
    // null uses queue.failed.table / queue.failed.database
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
        'retry_key' => 'auto',
        'retry_chunk_size' => 100,
        'retry_max_ids' => 50,
        'retry_lock_seconds' => 60,
        'queues_cache_ttl' => 60,
        'search_max_length' => 120,
        'payload_preview_length' => 300,
        'exception_preview_length' => 500,
    ],

    'authorization' => [
        'gate' => 'viewFailedJobsManager',

        'permissions' => [
            'view' => null,
            'retry' => null,
            'delete' => null,
        ],
    ],

    'filament' => [
        'cluster' => null,
        'register_navigation' => true,
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

`auth` only proves that the user is logged in. Access to the standalone UI is therefore closed by default outside the `local` environment until you either define the `viewFailedJobsManager` gate (the same approach as Horizon and Telescope) or configure `authorization.permissions.view`:

```php
use Illuminate\Support\Facades\Gate;

// AppServiceProvider::boot()
Gate::define('viewFailedJobsManager', fn ($user) => $user->isAdmin());
```

Middleware can still be used as an additional layer, for example with roles:

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

## Action Permissions

Route middleware and Filament page access determine who can open the manager. The optional permissions below determine which operations that user may perform:

```php
'authorization' => [
    'permissions' => [
        'view' => 'failed-jobs.view',
        'retry' => 'failed-jobs.retry',
        'delete' => 'failed-jobs.delete',
    ],
],
```

`retry` and `delete` also require `view`. A `null` operation permission denies the operation: retry and delete are never available until a permission is configured and assigned. A `null` `view` permission leaves page access to the gate (standalone UI) or `filament.roles` (Filament page).

This lets an operator inspect failed jobs without being allowed to retry or delete them.

Users without `retry` or `delete` see masked exception messages: besides credentials, the SQL with bindings (`(Connection: ..., SQL: ...)`) and duplicate key values are hidden, and the file and line are not shown. Their search matches only the job `uuid` and the exception class (as a prefix, e.g. `Illuminate\Database\QueryException`), so hidden values cannot be guessed by trying substrings. Users with `retry` or `delete` see the unmasked message and search the full exception and payload text.

If the project uses spatie/laravel-permission, create the configured permissions with:

```bash
php artisan failed-jobs-manager:permissions --guard=web
```

The command only creates permissions (including `filament.permission`, when set); assign them to roles or users in your application.

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
    'register_in_main_navigation' => true,
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

Filament does not add clustered pages to the main navigation: they are expected in the cluster sub-navigation. If the cluster hides its sub-navigation (`$shouldRegisterSubNavigation = false`) and clustered items are shown under a parent item of the main sidebar, set `register_in_main_navigation` to `true`. Without it the page is reachable only by URL. With a regular cluster sub-navigation keep it `false`, otherwise the page appears in both menus.

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

`roles` is only used as a fallback when neither `filament.permission` (or the Shield page permission) nor `authorization.permissions.view` is configured; a user who lacks a configured permission is denied even if they have a listed role. This is useful for roles like `super_admin` before Shield permissions are generated or assigned, but production access should preferably be permission-based.

The Filament page permission controls access to the page itself. Configure the operation permissions from the previous section as well; a `View:*` permission must not be the only permission assigned to an operator who should retry or delete jobs.

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

Users with `retry` or `delete` search the exact `uuid` and the full `exception` and `payload` text. Other users search only the exact `uuid` and the exception class prefix, otherwise the search would let anyone reveal masked content by guessing substrings. `%` and `_` are matched literally. The search uses a `LIKE '%...%'` query, so keep it for operational inspection, not for high-volume analytics.

## Retry Key

Laravel's failed job providers look jobs up by different identifiers: the `database` driver uses the numeric `id`, the `database-uuids` driver uses `uuid`.

Retry key configuration:

```php
'ui' => [
    'retry_key' => 'auto',
],
```

Available values:

- `auto` - `uuid` for the `database-uuids` failed job driver, `id` otherwise (`database` driver or no driver set);
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

- standalone UI: `web`, `auth`, and the `viewFailedJobsManager` gate or the `view` permission;
- Filament UI: Filament auth plus Shield/permission access;
- if you use only Filament, disable the standalone UI.

Standalone permission example:

```php
'authorization' => [
    'permissions' => [
        'view' => 'failed-jobs.view',
        'retry' => 'failed-jobs.retry',
        'delete' => 'failed-jobs.delete',
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

The raw payload is never shown. Exception messages always redact common credential names such as `token`, `secret`, `password`, and `api_key`, and are additionally masked for users without `retry` or `delete`; this is a safety measure, not a replacement for access control.

Retry requests are loaded in `ui.retry_chunk_size` chunks, and one request accepts at most `ui.retry_max_ids` jobs (50 by default; a larger selection is refused, not truncated). Bulk retry runs synchronously inside the HTTP request, so keep the limit small enough to finish within your request timeout. Every job is retried under a cache lock (`ui.retry_lock_seconds`) and is re-checked inside the lock, so a double click or two operators retrying the same job do not push it onto the queue twice. Queue errors are reported through the exception handler and shown as a failed retry instead of an HTTP 500.

The list of queues in the filter is cached for `ui.queues_cache_ttl` seconds (`0` disables caching), because `SELECT DISTINCT queue` scans the whole table.

The package dispatches `FailedJobsRetried` and `FailedJobDeleted` events after successful operations. Events contain job identifiers and actor context, never raw payloads; the host application should attach its own audit-log listener.

## Changelog

See [CHANGELOG.md](CHANGELOG.md).

## License

MIT © Edward Tich
