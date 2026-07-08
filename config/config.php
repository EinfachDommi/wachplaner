<?php

return [
    'app_name' => getenv('APP_NAME') ?: 'Wachplaner',
    'app_env' => getenv('APP_ENV') ?: 'production',
    'app_debug' => filter_var(getenv('APP_DEBUG') ?: false, FILTER_VALIDATE_BOOL),
    'app_url' => getenv('APP_URL') ?: 'http://wachplaner.sh-com.de',
    'version' => [
        'number' => '0.2.1',
        'codename' => 'Atlas Hotfix',
        'build' => '20260708.002',
    ],
    'db' => [
        'host' => getenv('DB_HOST') ?: '',
        'port' => getenv('DB_PORT') ?: '3306',
        'name' => getenv('DB_NAME') ?: '',
        'user' => getenv('DB_USER') ?: '',
        'pass' => getenv('DB_PASS') ?: '',
        'charset' => getenv('DB_CHARSET') ?: 'utf8mb4',
    ],
    'paths' => [
        'storage' => dirname(__DIR__) . '/storage',
        'logs' => dirname(__DIR__) . '/storage/logs',
        'cache' => dirname(__DIR__) . '/storage/cache',
    ],
];
