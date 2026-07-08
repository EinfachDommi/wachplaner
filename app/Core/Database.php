<?php

final class Database
{
    private static ?PDO $pdo = null;

    public static function pdo(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        Config::assert(['db.host', 'db.name', 'db.user']);

        $host = Config::require('db.host');
        $port = Config::get('db.port', '3306');
        $name = Config::require('db.name');
        $user = Config::require('db.user');
        $pass = Config::get('db.pass', '');
        $charset = Config::get('db.charset', 'utf8mb4');

        $dsn = "mysql:host={$host};port={$port};dbname={$name};charset={$charset}";

        try {
            self::$pdo = new PDO($dsn, $user, (string)$pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            throw new RuntimeException(
                'Datenbankverbindung fehlgeschlagen. Bitte DB_HOST, DB_PORT, DB_NAME, DB_USER und DB_PASS prüfen.',
                1002,
                $e
            );
        }

        return self::$pdo;
    }
}
