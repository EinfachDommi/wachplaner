<?php

declare(strict_types=1);

namespace Wachplaner\Core\Security\RateLimit;

final class RateLimitScope
{
    public const LOGIN = 'login';
    public const REGISTER = 'register';
    public const PASSWORD_RESET = 'password_reset';
    public const IMPORT = 'import';
    public const API = 'api';

    private const ALLOWED = [
        self::LOGIN,
        self::REGISTER,
        self::PASSWORD_RESET,
        self::IMPORT,
        self::API,
    ];

    public static function isValid(string $scope): bool
    {
        return in_array($scope, self::ALLOWED, true);
    }

    /**
     * @return list<string>
     */
    public static function all(): array
    {
        return self::ALLOWED;
    }
}
