<?php

return [
    'brand' => 'Менеджер неудачных задач',
    'brand_subtitle' => 'Контроль ошибок очередей Laravel',
    'topbar_meta' => 'Стандартная таблица failed_jobs',

    'pages' => [
        'index_title' => 'Неудачные задачи',
        'index_subtitle' => 'Таблица failed_jobs с перезапуском и очисткой записей',
        'show_title' => 'Неудачная задача #:id',
        'show_subtitle' => 'Детали payload и exception',
        'default_subtitle' => 'Просмотр, перезапуск и очистка неудачных задач',
    ],

    'metrics' => [
        'found' => 'Найдено',
        'filtered' => 'С учетом текущих фильтров',
    ],

    'filters' => [
        'queue' => 'Очередь',
        'not_selected' => 'Не выбрано',
        'search' => 'Поиск',
        'search_placeholder' => 'payload или exception',
        'failed_from' => 'Дата с',
        'failed_to' => 'Дата по',
        'submit' => 'Найти',
        'reset' => 'Сброс',
    ],

    'table' => [
        'payload' => 'Payload',
        'exception' => 'Exception',
        'failed_at' => 'Дата падения',
        'actions' => 'Действия',
        'empty' => 'Нет задач',
        'no_uuid' => 'Нет uuid',
        'shown' => 'Показано',
        'of' => 'из',
        'previous' => 'Предыдущая',
        'next' => 'Следующая',
    ],

    'actions' => [
        'back' => 'Назад',
        'details' => 'Подробно',
        'retry' => 'Рестарт',
        'retry_selected' => 'Перезапустить выбранные',
        'selected_count' => 'Выбрано: :count',
        'delete' => 'Удалить',
        'delete_confirm' => 'Удалить запись?',
    ],

    'details' => [
        'payload_summary' => 'Сводка payload',
        'exception_summary' => 'Сводка exception',
        'serialized_command' => 'Сериализованная команда',
        'raw_payload' => 'Исходный payload',
        'raw_exception' => 'Исходный exception',
        'failed_at' => 'Дата падения',
        'default_queue' => 'default',
        'empty_value' => '-',
    ],

    'payload' => [
        'job' => 'Job',
        'handler' => 'Handler',
        'uuid' => 'UUID',
        'attempts' => 'Попытки',
        'max_tries' => 'Максимум попыток',
        'timeout' => 'Timeout',
        'unknown_job' => 'Неизвестная задача',
    ],

    'exception' => [
        'class' => 'Класс',
        'message' => 'Сообщение',
        'file' => 'Файл',
        'line' => 'Строка',
        'unknown_exception' => 'Неизвестная ошибка',
    ],

    'flash' => [
        'deleted' => 'Запись удалена',
        'delete_failed' => 'Не удалось удалить запись',
        'retry_sent' => 'Задача отправлена на перезапуск',
        'bulk_retry_sent' => 'Задачи отправлены на перезапуск',
        'bulk_retry_partially_sent' => 'Часть задач отправлена на перезапуск до возникновения ошибки',
        'retry_failed' => 'Не удалось отправить задачу на перезапуск',
    ],
];
