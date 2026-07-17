<?php

declare(strict_types=1);

namespace Wachplaner\Core\System;

final class MaintenanceResponder
{
    public static function render(
        MaintenanceState $state,
        bool $loginAvailable = false,
        int $retrySeconds = 30
    ): never {
        http_response_code(503);
        header('Retry-After: ' . max(1, $retrySeconds));
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('X-Robots-Tag: noindex, nofollow');

        $message = htmlspecialchars($state->message, ENT_QUOTES, 'UTF-8');
        $reason = match ($state->reason) {
            'database_unavailable' => 'Die Datenbankverbindung ist aktuell nicht verfügbar.',
            'environment_override' => 'Der Notfall-Wartungsmodus wurde aktiviert.',
            'administrator' => 'Es werden geplante Wartungsarbeiten durchgeführt.',
            default => 'Das System ist vorübergehend nicht verfügbar.',
        };
        $reason = htmlspecialchars($reason, ENT_QUOTES, 'UTF-8');
        $lastCheck = htmlspecialchars(
            $state->lastCheckAt ?? date(DATE_ATOM),
            ENT_QUOTES,
            'UTF-8'
        );
        $refresh = $state->isAutomatic()
            ? '<meta http-equiv="refresh" content="' . max(10, $retrySeconds) . '">'
            : '';
        $login = $loginAvailable
            ? '<a class="button" href="/login">Administrator anmelden</a>'
            : '';

        echo <<<HTML
        <!doctype html>
        <html lang="de">
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            {$refresh}
            <title>Wachplaner · Wartungsmodus</title>
            <style>
                :root { color-scheme: light dark; }
                * { box-sizing: border-box; }
                body {
                    margin: 0;
                    min-height: 100vh;
                    display: grid;
                    place-items: center;
                    padding: 24px;
                    font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
                    background: #eef2f6;
                    color: #1f2937;
                }
                .card {
                    width: min(680px, 100%);
                    background: #fff;
                    border: 1px solid #dbe3ec;
                    border-radius: 18px;
                    box-shadow: 0 20px 60px rgba(31, 41, 55, .12);
                    padding: 34px;
                }
                .brand { display: flex; align-items: center; gap: 12px; font-weight: 750; }
                .mark {
                    width: 46px; height: 46px; border-radius: 12px;
                    display: grid; place-items: center;
                    background: #0d6efd; color: white; font-size: 24px;
                }
                h1 { margin: 28px 0 10px; font-size: clamp(28px, 5vw, 42px); }
                p { line-height: 1.65; }
                .status {
                    margin-top: 24px; padding: 16px; border-radius: 12px;
                    background: #fff4d6; border: 1px solid #f1d38a;
                }
                .meta { margin-top: 22px; color: #64748b; font-size: 14px; }
                .button {
                    display: inline-block; margin-top: 22px; padding: 11px 16px;
                    border-radius: 10px; background: #0d6efd; color: #fff;
                    text-decoration: none; font-weight: 650;
                }
                @media (prefers-color-scheme: dark) {
                    body { background: #111827; color: #e5e7eb; }
                    .card { background: #1f2937; border-color: #374151; }
                    .status { background: #3f3218; border-color: #705d2b; }
                    .meta { color: #aab4c2; }
                }
            </style>
        </head>
        <body>
            <main class="card">
                <div class="brand"><span class="mark">W</span><span>Wachplaner</span></div>
                <h1>Wartungsmodus</h1>
                <p>{$message}</p>
                <div class="status"><strong>Systemstatus</strong><br>{$reason}</div>
                {$login}
                <div class="meta">
                    Fehlerreferenz: <strong>SYS-503</strong><br>
                    Letzte Prüfung: {$lastCheck}
                </div>
            </main>
        </body>
        </html>
        HTML;

        exit;
    }

    public static function json(MaintenanceState $state, int $retrySeconds = 30): never
    {
        http_response_code(503);
        header('Content-Type: application/json; charset=utf-8');
        header('Retry-After: ' . max(1, $retrySeconds));
        header('Cache-Control: no-store');

        echo json_encode([
            'status' => 'maintenance',
            'mode' => $state->mode,
            'reason' => $state->reason,
            'message' => $state->message,
            'retry_after' => $retrySeconds,
            'last_check_at' => $state->lastCheckAt,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        exit;
    }
}
