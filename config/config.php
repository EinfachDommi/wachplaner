<?php

return [
    'app_name' => getenv('APP_NAME') ?: 'Wachplaner',
    'app_env' => getenv('APP_ENV') ?: 'production',
    'app_debug' => filter_var(getenv('APP_DEBUG') ?: false, FILTER_VALIDATE_BOOL),
    'app_url' => getenv('APP_URL') ?: 'http://wachplaner.sh-com.de',
    'version' => [
        'number' => '0.2.1',
        'codename' => 'Atlas Hotfix',
        'build' => '20260708.001',
    ],
    'db' => [
        'host' => getenv('DB_HOST') ?: '',
        'name' => getenv('DB_NAME') ?: '',
        'user' => getenv('DB_USER') ?: '',
        'pass' => getenv('DB_PASS') ?: '',
        'charset' => getenv('DB_CHARSET') ?: 'utf8mb4',
    ],
];
