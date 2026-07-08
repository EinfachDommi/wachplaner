<?php

final class ErrorHandler
{
    public static function register(string $rootPath): void
    {
        set_exception_handler(static function (Throwable $e) use ($rootPath): void {
            $errorId = self::errorId();
            self::log($rootPath, $e, $errorId);
            self::render($e, $errorId);
        });
    }

    public static function log(string $rootPath, Throwable $e, string $errorId): void
    {
        $logDir = $rootPath . '/storage/logs';
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0775, true);
        }

        $route = PHP_SAPI === 'cli' ? 'cli' : ($_SERVER['REQUEST_METHOD'] ?? 'GET') . ' ' . ($_SERVER['REQUEST_URI'] ?? '/');
        $line = sprintf(
            "[%s] %s %s: %s in %s:%d route=%s
%s

",
            date('Y-m-d H:i:s'),
            $errorId,
            get_class($e),
            $e->getMessage(),
            $e->getFile(),
            $e->getLine(),
            $route,
            $e->getTraceAsString()
        );
        @file_put_contents($logDir . '/app.log', $line, FILE_APPEND | LOCK_EX);
    }

    public static function render(Throwable $e, string $errorId): void
    {
        http_response_code(500);
        [$code, $message] = self::publicMessage($e);

        if (PHP_SAPI === 'cli') {
            fwrite(STDERR, $code . ' ' . $message . PHP_EOL . 'Fehler-ID: ' . $errorId . PHP_EOL . $e->getMessage() . PHP_EOL);
            return;
        }

        echo '<!doctype html><html lang="de"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">';
        echo '<title>Wachplaner Fehler</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head>';
        echo '<body class="bg-light"><main class="container py-5"><div class="card shadow-sm"><div class="card-body">';
        echo '<h1 class="h3">Wachplaner Fehler</h1><div class="alert alert-danger mt-3"><strong>' . htmlspecialchars($code) . '</strong><br>' . htmlspecialchars($message) . '</div>';
        echo '<p class="mb-1"><strong>Fehler-ID:</strong> <code>' . htmlspecialchars($errorId) . '</code></p>';
        echo '<p class="text-muted">Details wurden im Log unter <code>storage/logs/app.log</code> gespeichert.</p>';
        echo '</div></div></main></body></html>';
    }

    private static function publicMessage(Throwable $e): array
    {
        return match ((int)$e->getCode()) {
            1001 => ['CFG-001', 'Die Konfiguration ist unvollständig. Bitte .env und .env.example abgleichen.'],
            1002 => ['DB-001', 'Die Datenbankverbindung konnte nicht hergestellt werden. Bitte Datenbankzugangsdaten prüfen.'],
            default => ['APP-001', 'Ein unerwarteter Fehler ist aufgetreten.'],
        };
    }

    private static function errorId(): string
    {
        return 'WP-' . date('Ymd-His') . '-' . strtoupper(bin2hex(random_bytes(3)));
    }
}
