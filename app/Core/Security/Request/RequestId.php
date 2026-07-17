<?php

declare(strict_types=1);

namespace Wachplaner\Core\Security\Request;

final class RequestId
{
    private const SESSION_KEY = '_request_id';

    public static function generate(): string
    {
        return sprintf(
            'WP-%s-%s',
            gmdate('Ymd-His'),
            strtoupper(bin2hex(random_bytes(3)))
        );
    }

    public static function current(): string
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $existing = $_SESSION[self::SESSION_KEY] ?? null;

            if (is_string($existing) && $existing !== '') {
                return $existing;
            }

            $requestId = self::generate();
            $_SESSION[self::SESSION_KEY] = $requestId;

            return $requestId;
        }

        return self::generate();
    }

    public static function renew(): string
    {
        $requestId = self::generate();

        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION[self::SESSION_KEY] = $requestId;
        }

        return $requestId;
    }
}
