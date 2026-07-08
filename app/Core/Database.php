<?php

final class Database
{
    private static ?PDO $pdo = null;

    public static function pdo(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        $host = Config::require('db.host');
        $name = Config::require('db.name');
        $user = Config::require('db.user');
        $pass = Config::get('db.pass', '');
        $charset = Config::get('db.charset', 'utf8mb4');

        $dsn = "mysql:host={$host};dbname={$name};charset={$charset}";

        try {
            self::$pdo = new PDO($dsn, $user, (string)$pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            throw new RuntimeException('Datenbankverbindung fehlgeschlagen. Bitte DB_HOST, DB_NAME, DB_USER und DB_PASS prüfen.', 1001, $e);
        }

        return self::$pdo;
    }
}
