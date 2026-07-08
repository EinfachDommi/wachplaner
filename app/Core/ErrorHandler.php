<?php

final class ErrorHandler
{
    public static function register(string $rootPath): void
    {
        set_exception_handler(static function (Throwable $e) use ($rootPath): void {
            self::log($rootPath, $e);
            self::render($e);
        });
    }

    public static function log(string $rootPath, Throwable $e): void
    {
        $logDir = $rootPath . '/storage/logs';
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0775, true);
        }
        $line = sprintf(
            "[%s] %s: %s in %s:%d\n%s\n\n",
            date('Y-m-d H:i:s'),
            get_class($e),
            $e->getMessage(),
            $e->getFile(),
            $e->getLine(),
            $e->getTraceAsString()
        );
        @file_put_contents($logDir . '/app.log', $line, FILE_APPEND | LOCK_EX);
    }

    public static function render(Throwable $e): void
    {
        http_response_code(500);
        $isConfig = $e->getCode() === 1001;
        $code = $isConfig ? 'CFG-001' : 'APP-001';
        $message = $isConfig
            ? 'Die Konfiguration ist unvollständig. Bitte .env und .env.example abgleichen.'
            : 'Ein unerwarteter Fehler ist aufgetreten. Details wurden im Log gespeichert.';

        if (PHP_SAPI === 'cli') {
            fwrite(STDERR, $code . ' ' . $message . PHP_EOL . $e->getMessage() . PHP_EOL);
            return;
        }

        echo '<!doctype html><html lang="de"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">';
        echo '<title>Wachplaner Fehler</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head>';
        echo '<body class="bg-light"><main class="container py-5"><div class="card shadow-sm"><div class="card-body">';
        echo '<h1 class="h3">Wachplaner Fehler</h1><div class="alert alert-danger mt-3"><strong>' . htmlspecialchars($code) . '</strong><br>' . htmlspecialchars($message) . '</div>';
        echo '<p class="text-muted">Bitte prüfe die Konfiguration oder das Log unter <code>storage/logs/app.log</code>.</p>';
        echo '</div></div></main></body></html>';
    }
}
