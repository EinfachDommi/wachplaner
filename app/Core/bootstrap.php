<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

define('WACHPLANER_ROOT', dirname(__DIR__, 2));

require_once __DIR__ . '/EnvLoader.php';
require_once __DIR__ . '/Config.php';
require_once __DIR__ . '/ErrorHandler.php';
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/helpers.php';

ErrorHandler::register(WACHPLANER_ROOT);
Config::load(WACHPLANER_ROOT);

spl_autoload_register(static function (string $class): void {
    $prefix = 'Wachplaner\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $file = __DIR__ . '/../' . str_replace('\\', '/', $relative) . '.php';
    if (is_file($file)) {
        require_once $file;
    }
});
