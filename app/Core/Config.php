<?php

final class Config
{
    private static array $config = [];

    public static function load(string $rootPath): void
    {
        EnvLoader::load($rootPath);
        self::$config = require $rootPath . '/config/config.php';
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $segments = explode('.', $key);
        $value = self::$config;
        foreach ($segments as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }
        return $value;
    }

    public static function require(string $key): mixed
    {
        $value = self::get($key);
        if ($value === null || $value === '') {
            throw new RuntimeException('Konfigurationswert fehlt: ' . $key, 1001);
        }
        return $value;
    }

    public static function all(): array
    {
        return self::$config;
    }
}
