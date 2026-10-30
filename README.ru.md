# Laravel Failed Jobs Manager

[![Latest Version on Packagist](https://img.shields.io/packagist/v/edwardtich/laravel-failed-jobs-manager.svg?style=flat-square)](https://packagist.org/packages/edwardtich/laravel-failed-jobs-manager)
[![PHP Version Require](https://img.shields.io/packagist/php-v/edwardtich/laravel-failed-jobs-manager.svg?style=flat-square)](https://packagist.org/packages/edwardtich/laravel-failed-jobs-manager)
[![Laravel Version](https://img.shields.io/badge/Laravel-12%2F13-orange?style=flat-square)](https://laravel.com)

Пакет для Laravel, который помогает работать со стандартной таблицей `failed_jobs`.

Возможности:

- отдельный Laravel UI;
- опциональная страница Filament;
- повтор одного упавшего задания;
- массовый повтор выбранных заданий;
- удаление одной записи из `failed_jobs`;
- читаемая сводка `payload` и `exception`;
- русская и английская локализация.

Массовый повтор использует стандартный формат Laravel:

```bash
php artisan queue:retry 42694 42695 42696
```

## Оглавление

- [Требования](#требования)
- [Установка](#установка)
- [Публикация файлов](#публикация-файлов)
- [Конфигурация](#конфигурация)
- [Отдельный Laravel UI](#отдельный-laravel-ui)
- [Интеграция с Filament](#интеграция-с-filament)
- [Навигация Filament](#навигация-filament)
- [Меню Filament Cluster](#меню-filament-cluster)
- [Filament Shield и права доступа](#filament-shield-и-права-доступа)
- [Локализация](#локализация)
- [Пагинация](#пагинация)
- [Ключ повтора](#ключ-повтора)
- [Действия](#действия)
- [Безопасность](#безопасность)
- [История изменений](#история-изменений)

## Требования

- PHP 8.3+
- Laravel 12 или 13
- Filament 4 или 5 (только для интеграции с Filament)
- настроенная таблица `failed_jobs`
- настроенный Laravel failed job provider для очередей
- Filament нужен только для Filament-интеграции

## Установка

Установите пакет:

```bash
composer require edwardtich/laravel-failed-jobs-manager
```

Для локальной разработки через `packages/` добавьте path-репозиторий в `composer.json` основного проекта:

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

Затем подключите пакет:

```bash
composer require edwardtich/laravel-failed-jobs-manager:*
```

## Публикация файлов

Пакет работает без публикации config-файла, потому что service provider рекурсивно подмешивает настройки по умолчанию при регистрации.

Публикуйте config только если нужно изменить route, middleware, пагинацию или настройки Filament:

```bash
php artisan vendor:publish --provider="EdwardTich\\FailedJobsManager\\Providers\\FailedJobsManagerProvider" --tag=config
```

Публикация Blade-шаблонов:

```bash
php artisan vendor:publish --provider="EdwardTich\\FailedJobsManager\\Providers\\FailedJobsManagerProvider" --tag=views
```

Публикация переводов:

```bash
php artisan vendor:publish --provider="EdwardTich\\FailedJobsManager\\Providers\\FailedJobsManagerProvider" --tag=translations
```

## Конфигурация

После публикации config будет доступен по пути:

```text
config/failed-jobs-manager.php
```

Пакет не должен автоматически создавать этот файл в host-приложении при обычной загрузке. Это нормальное поведение Laravel packages.

Конфигурация по умолчанию:

```php
return [
    // null — берётся из queue.failed.table / queue.failed.database
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

## Отдельный Laravel UI

Отдельный UI включен по умолчанию.

URL по умолчанию:

```text
/failed-jobs-manager
```

По умолчанию маршрут использует middleware:

```php
'middleware' => [
    'web',
    'auth',
],
```

`auth` означает только то, что пользователь залогинен. Поэтому вне окружения `local` standalone UI по умолчанию закрыт, пока вы не определите gate `viewFailedJobsManager` (как в Horizon и Telescope) или не настроите `authorization.permissions.view`:

```php
use Illuminate\Support\Facades\Gate;

// AppServiceProvider::boot()
Gate::define('viewFailedJobsManager', fn ($user) => $user->isAdmin());
```

Middleware можно использовать как дополнительный слой, например с ролями:

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

Если используется только Filament-страница, отдельный UI можно отключить:

```php
'ui' => [
    'enabled' => false,
],
```

## Интеграция с Filament

Filament является опциональной зависимостью. Пакет не требует `filament/filament` в `composer.json`, поэтому проекты без Filament могут использовать обычный Laravel UI.

Зарегистрируйте страницу в вашем Filament panel provider:

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

## Права на действия

Route middleware и доступ к странице Filament определяют, кто может открыть менеджер. Следующие необязательные permissions определяют, какие действия этот пользователь может выполнять:

```php
'authorization' => [
    'permissions' => [
        'view' => 'failed-jobs.view',
        'retry' => 'failed-jobs.retry',
        'delete' => 'failed-jobs.delete',
    ],
],
```

`retry` и `delete` дополнительно требуют `view`. Если permission действия равен `null`, действие запрещено: retry и delete недоступны, пока permission не настроен и не выдан. Если `null` у `view`, доступ к странице решает gate (standalone UI) или `filament.roles` (страница Filament).

Так оператор может смотреть failed jobs, но не сможет их перезапускать или удалять.

Пользователи без `retry` и `delete` видят замаскированный текст ошибки: кроме секретов скрываются SQL с подставленными значениями (`(Connection: ..., SQL: ...)`) и значения из `Duplicate entry`, а файл и строка не показываются. Их поиск работает только по `uuid` задачи и классу исключения (по началу строки, например `Illuminate\Database\QueryException`), поэтому скрытые значения нельзя подобрать перебором подстрок. Пользователи с `retry` или `delete` видят текст без маскирования и ищут по полному тексту exception и payload.

Если в проекте стоит spatie/laravel-permission, настроенные permissions можно создать командой:

```bash
php artisan failed-jobs-manager:permissions --guard=web
```

Команда только создает permissions (включая `filament.permission`, если он задан); назначать их ролям и пользователям нужно в приложении.

Если панель доступна по пути `/admin`, страница будет доступна по адресу:

```text
/admin/failed-jobs-manager
```

## Навигация Filament

Настройка пункта меню находится в `config/failed-jobs-manager.php`:

```php
'filament' => [
    'cluster' => null,
    'register_navigation' => true,
    'navigation_group' => 'Справочник',
    'navigation_parent_item' => null,
    'navigation_sort' => 40,
    'permission' => null,
    'roles' => [
        'admin',
    ],
],
```

Чтобы скрыть страницу из бокового меню:

```php
'register_navigation' => false,
```

Страница останется доступной по URL, если текущий пользователь Filament проходит `canAccess()`.

## Меню Filament Cluster

Если в проекте используются Filament clusters и ручная структура бокового меню, настройте страницу как clustered page.

Пример:

```php
'filament' => [
    'cluster' => \App\Filament\Clusters\AdministrationCluster::class,
    'register_navigation' => true,
    'navigation_group' => 'Справочник',
    'navigation_parent_item' => 'Администрирование',
    'navigation_sort' => 40,
    'permission' => null,
    'roles' => [
        'super_admin',
    ],
],
```

Страницу все равно нужно зарегистрировать в panel provider:

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

Если боковое меню собирается вручную через массив родительского пункта, добавьте страницу и туда:

```php
protected array $administrationNavigation = [
    UserResource::class,
    RoleResource::class,
    FailedJobsManagerPage::class,
];
```

## Filament Shield и права доступа

Если в проекте используется Filament Shield или Spatie Permission, лучше использовать permission-based доступ вместо жесткой проверки роли.

Пакет сам не создает permissions в базе. Это намеренно: права должны генерироваться и назначаться в приложении, где уже настроены Shield, роли и guard.

Пример:

```php
'filament' => [
    'permission' => null,
    'roles' => [
        'admin',
    ],
],
```

Если `permission` равен `null` и Filament Shield установлен, пакет попробует автоматически получить permission, который Shield сгенерировал для страницы.

Для production лучше задавать permission явно. Так поведение не будет зависеть от настроек генерации Shield в конкретном проекте:

```php
'filament' => [
    'permission' => 'View:FailedJobsManagerPage',
    'roles' => [
        'admin',
    ],
],
```

Если нужно задать permission вручную, используйте точный ключ из Shield. Например, при настройках Shield `separator => ':'`, `case => 'pascal'` и `prefix => 'view'` для page будет сгенерирован ключ:

```text
View:FailedJobsManagerPage
```

После этого пакет проверяет:

```php
$user->can($permission);
```

`roles` используется только как fallback, когда не настроены ни `filament.permission` (или permission страницы из Shield), ни `authorization.permissions.view`; если permission настроен, а у пользователя его нет, доступ запрещен даже при подходящей роли. Это удобно, например, для `super_admin` до генерации или назначения Shield permissions, но основную проверку доступа в production лучше держать через permission.

Permission страницы Filament контролирует только вход на страницу. Дополнительно настройте permissions действий из предыдущего раздела: одного `View:*` недостаточно для оператора, который должен делать retry или delete.

После регистрации страницы можно сгенерировать права Shield:

```bash
php artisan shield:generate
```

Затем откройте страницу ролей Filament и выдайте нужное право:

```text
/admin/shield/roles
```

Например:

```text
View:FailedJobsManagerPage
```

Если Shield в проекте настроен с другим `separator`, `case` или `prefix`, название permission будет другим. В таком случае смотрите фактическое имя права на странице ролей Shield и укажите его в `failed-jobs-manager.filament.permission`.

## Локализация

Пакет содержит русские и английские переводы.

Laravel использует текущую локаль приложения:

```php
'locale' => 'ru',
```

Namespace переводов:

```text
failed-jobs-manager::failed-jobs-manager
```

## Пагинация

По умолчанию на странице отображается 20 записей:

```php
'ui' => [
    'per_page' => 20,
],
```

Пакет всегда использует пагинацию, чтобы не загружать всю таблицу `failed_jobs`.

Поиск ограничивается перед запросом к базе:

```php
'ui' => [
    'search_max_length' => 120,
],
```

Пользователи с `retry` или `delete` ищут по точному `uuid` и по полному тексту `exception` и `payload`. Остальные ищут только по точному `uuid` и по началу класса исключения: иначе поиск позволял бы восстановить замаскированное содержимое подбором подстрок. `%` и `_` ищутся буквально. Поиск работает через `LIKE '%...%'`, так что это инструмент операционного просмотра, а не аналитики на больших объёмах.

## Ключ повтора

Провайдеры failed jobs в Laravel ищут задачи по разным идентификаторам: драйвер `database` — по числовому `id`, драйвер `database-uuids` — по `uuid`.

Настройка ключа повтора:

```php
'ui' => [
    'retry_key' => 'auto',
],
```

Доступные значения:

- `auto` - `uuid` для драйвера `database-uuids`, иначе `id` (драйвер `database` или драйвер не задан);
- `id` - всегда вызывать команды очередей с ID записи;
- `uuid` - вызывать команды очередей с UUID, fallback на ID, если UUID пустой.

Интерфейс все равно выбирает строки по внутреннему `id`. Настройка `retry_key` применяется при вызове Laravel-команд очередей для повтора и удаления: `queue:retry` и `queue:forget`.

## Действия

### Повтор одного задания

Пакет вызывает Laravel-команду `queue:retry` с настроенным ключом повтора.

### Повтор выбранных заданий

Пакет собирает выбранные ID, преобразует их в настроенные retry keys и вызывает стандартную команду Laravel:

```bash
php artisan queue:retry 42694 42695 42696
```

Если `retry_key` равен `auto` или `uuid`, команда может выглядеть так:

```bash
php artisan queue:retry 550e8400-e29b-41d4-a716-446655440000
```

### Удаление одного задания

Из UI можно удалить одну failed job через стандартную команду Laravel:

```bash
php artisan queue:forget 91401d2c-0784-4f43-824c-34f94a33c24d
```

Пакет рассчитан на стандартное хранилище failed jobs Laravel и не реализует отдельные адаптеры удаления для нестандартных failed job providers.

Массовое удаление намеренно не реализовано. Failed jobs являются операционными данными, и массовое удаление слишком легко использовать неправильно.

## Безопасность

Пакет открывает доступ к операционным действиям:

- повтор failed jobs;
- удаление записей из `failed_jobs`;
- просмотр `payload`;
- просмотр сообщений исключений.

Не открывайте этот функционал каждому авторизованному пользователю. `auth` означает только то, что пользователь залогинен; это не проверка администратора.

Рекомендуемая production-настройка:

- standalone UI: `web`, `auth` и gate `viewFailedJobsManager` или permission `view`;
- Filament UI: Filament auth плюс Shield/permission доступ;
- если используется только Filament, отключите standalone UI.

Пример permissions для standalone UI:

```php
'authorization' => [
    'permissions' => [
        'view' => 'failed-jobs.view',
        'retry' => 'failed-jobs.retry',
        'delete' => 'failed-jobs.delete',
    ],
],
```

Пример, если в проекте есть свой middleware администратора:

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

Если страница используется только внутри Filament, standalone UI лучше выключить:

```php
'ui' => [
    'enabled' => false,
],
```

Raw payload не показывается. В тексте ошибки всегда маскируются распространенные названия секретов: `token`, `secret`, `password`, `api_key`, а для пользователей без `retry` и `delete` текст маскируется дополнительно. Это дополнительная защита, а не замена корректных прав доступа.

Retry загружает задачи чанками по `ui.retry_chunk_size`, один запрос принимает не больше `ui.retry_max_ids` задач (по умолчанию 50; если выбрано больше, запрос отклоняется целиком, а не обрезается). Массовый retry выполняется синхронно внутри HTTP-запроса, поэтому лимит должен укладываться в таймаут запроса. Каждая задача перезапускается под cache lock (`ui.retry_lock_seconds`) с повторной проверкой внутри блокировки, поэтому двойной клик или два оператора не отправят одну задачу в очередь дважды. Ошибки очереди уходят в exception handler и показываются как неудачный retry, а не HTTP 500.

Список очередей в фильтре кешируется на `ui.queues_cache_ttl` секунд (`0` отключает кеш), потому что `SELECT DISTINCT queue` сканирует всю таблицу.

После успешных операций пакет отправляет события `FailedJobsRetried` и `FailedJobDeleted`. В них есть идентификаторы jobs и контекст пользователя, но нет raw payload. Хост-приложение должно само подключить listener для audit log.

## История изменений

Смотрите [CHANGELOG.ru.md](CHANGELOG.ru.md).

## Лицензия

MIT © Edward Tich
