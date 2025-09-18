<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Основные настройки пакета
    |--------------------------------------------------------------------------
    |
    | Здесь вы можете настроить основные параметры пакета Laravel от RostPack
    |
    */

    'name' => env('ROSTPACK_NAME', 'RostPack Package'),
    
    'version' => '1.0.0',
    
    'debug' => env('ROSTPACK_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Настройки API
    |--------------------------------------------------------------------------
    |
    | Конфигурация для работы с внешними API
    |
    */

    'api' => [
        'timeout' => env('ROSTPACK_API_TIMEOUT', 30),
        'retries' => env('ROSTPACK_API_RETRIES', 3),
        'base_url' => env('ROSTPACK_API_BASE_URL', 'https://api.example.com'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Настройки кэширования
    |--------------------------------------------------------------------------
    |
    | Параметры кэширования для пакета
    |
    */

    'cache' => [
        'enabled' => env('ROSTPACK_CACHE_ENABLED', true),
        'ttl' => env('ROSTPACK_CACHE_TTL', 3600), // 1 час
        'prefix' => env('ROSTPACK_CACHE_PREFIX', 'rostpack'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Настройки логирования
    |--------------------------------------------------------------------------
    |
    | Конфигурация логирования для пакета
    |
    */

    'logging' => [
        'enabled' => env('ROSTPACK_LOGGING_ENABLED', true),
        'channel' => env('ROSTPACK_LOG_CHANNEL', 'daily'),
        'level' => env('ROSTPACK_LOG_LEVEL', 'info'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Настройки безопасности
    |--------------------------------------------------------------------------
    |
    | Параметры безопасности пакета
    |
    */

    'security' => [
        'encryption_key' => env('ROSTPACK_ENCRYPTION_KEY'),
        'rate_limit' => env('ROSTPACK_RATE_LIMIT', 100),
        'allowed_ips' => env('ROSTPACK_ALLOWED_IPS', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Настройки ресурсов
    |--------------------------------------------------------------------------
    |
    | Конфигурация для CSS и JavaScript файлов
    |
    */

    'assets' => [
        'published' => env('ROSTPACK_ASSETS_PUBLISHED', false),
        'css_path' => env('ROSTPACK_CSS_PATH', 'vendor/rostpack/css'),
        'js_path' => env('ROSTPACK_JS_PATH', 'vendor/rostpack/js'),
        'minify' => env('ROSTPACK_MINIFY_ASSETS', false),
        'version' => env('ROSTPACK_ASSETS_VERSION', '1.0.0'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Настройки Telegram бота
    |--------------------------------------------------------------------------
    |
    | Конфигурация для работы с Telegram Bot API
    |
    */

    'telegram' => [
        'bot_token' => env('TELEGRAM_BOT_TOKEN'),
        'bot_username' => env('TELEGRAM_BOT_USERNAME'),
        'webhook_url' => env('TELEGRAM_WEBHOOK_URL'),
        'webhook_secret' => env('TELEGRAM_WEBHOOK_SECRET'),
        'timeout' => env('TELEGRAM_TIMEOUT', 30),
        'retries' => env('TELEGRAM_RETRIES', 3),
        'parse_mode' => env('TELEGRAM_PARSE_MODE', 'HTML'),
        'disable_web_page_preview' => env('TELEGRAM_DISABLE_WEB_PAGE_PREVIEW', true),
        'disable_notification' => env('TELEGRAM_DISABLE_NOTIFICATION', false),
    ],
];
