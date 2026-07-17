<?php

declare(strict_types=1);

return [
    'app_name' => getenv('APP_NAME') ?: 'Wachplaner',
    'app_env' => getenv('APP_ENV') ?: 'production',
    'app_debug' => filter_var(getenv('APP_DEBUG') ?: false, FILTER_VALIDATE_BOOL),
    'app_url' => getenv('APP_URL') ?: 'https://wachplaner.sh-com.de',
    'version' => [
        'number' => '0.2.2-dev',
        'codename' => 'Guardian',
        'build' => '20260717.003',
    ],
    'db' => [
        'host' => getenv('DB_HOST') ?: '',
        'port' => getenv('DB_PORT') ?: '3306',
        'name' => getenv('DB_NAME') ?: '',
        'user' => getenv('DB_USER') ?: '',
        'pass' => getenv('DB_PASS') ?: '',
        'charset' => getenv('DB_CHARSET') ?: 'utf8mb4',
    ],
    'maintenance' => [
        'force' => filter_var(getenv('MAINTENANCE_FORCE') ?: false, FILTER_VALIDATE_BOOL),
        'message' => getenv('MAINTENANCE_MESSAGE') ?: 'Der Wachplaner wird aktuell gewartet.',
        'retry_seconds' => max(10, (int) (getenv('MAINTENANCE_RETRY_SECONDS') ?: 30)),
    ],
    'registration' => [
        'enabled' => filter_var(
            getenv('REGISTRATION_ENABLED') === false ? true : getenv('REGISTRATION_ENABLED'),
            FILTER_VALIDATE_BOOL
        ),
    ],
    'session' => [
        'secure' => filter_var(
            getenv('SESSION_SECURE') === false ? true : getenv('SESSION_SECURE'),
            FILTER_VALIDATE_BOOL
        ),
        'same_site' => getenv('SESSION_SAME_SITE') ?: 'Lax',
    ],
    'paths' => [
        'storage' => dirname(__DIR__) . '/storage',
        'logs' => dirname(__DIR__) . '/storage/logs',
        'cache' => dirname(__DIR__) . '/storage/cache',
    ],
];
